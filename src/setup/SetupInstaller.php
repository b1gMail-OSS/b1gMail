<?php
/*
 * Shared installer logic for the web setup and CLI installer.
 */

class SetupInstaller
{
    private array $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function install(): array
    {
        $host = trim((string)($this->options['mysql_host'] ?? 'localhost'));
        $user = (string)($this->options['mysql_user'] ?? '');
        $pass = (string)($this->options['mysql_pass'] ?? '');
        $db = (string)($this->options['mysql_db'] ?? '');

        $prefix = SetupNormalizePrefix((string)($this->options['mysql_prefix'] ?? SETUP_DEFAULT_PREFIX));
        if (!defined('DB_INSTALL_PREFIX')) {
            define('DB_INSTALL_PREFIX', $prefix);
        }

        $adminPlain = (string)($this->options['adminpw'] ?? '');
        $adminPlain2 = (string)($this->options['adminpw2'] ?? $adminPlain);
        $adminUser = SetupNormalizeAdminUser((string)($this->options['adminuser'] ?? 'admin'));

        if (strlen($adminPlain) < 8) {
            throw new RuntimeException('Admin password is too short');
        }

        if ($adminPlain !== $adminPlain2 && $adminPlain2 !== '') {
            throw new RuntimeException('Admin passwords do not match');
        }

        $url = rtrim((string)($this->options['url'] ?? SetupDetectSelfUrl()), '/').'/';
        $domains = $this->normalizeDomains((string)($this->options['domains'] ?? "example.com\nexample.net\nexample.org"));
        $firstDomain = $domains[0];

        require dirname(__DIR__).'/serverlib/database.struct.php';
        require __DIR__.'/data/rootcerts.data.php';

        $databaseStructure = SetupRewriteStructurePrefix(
            json_decode($databaseStructure, true, 512, JSON_THROW_ON_ERROR),
            DB_INSTALL_PREFIX
        );

        $connection = SetupHostAllowed($host)
            ? mysqli_connect($host, $user, $pass, $db)
            : false;

        if (!$connection) {
            throw new RuntimeException('MySQL connection failed');
        }

        if (!mysqli_select_db($connection, $db)) {
            mysqli_close($connection);
            throw new RuntimeException('MySQL database selection failed');
        }

        mysqli_set_charset($connection, 'utf8mb4');
        @mysqli_query($connection, "SET SESSION sql_mode=''");
        $utf8Mode = true;

        $result = CreateDatabaseStructure($connection, $databaseStructure, $utf8Mode, $db);
        foreach ($result as $query => $queryResult) {
            if ($queryResult !== true) {
                // keep as warning and continue
            }
        }

        $setupMode = (string)($this->options['setup_mode'] ?? 'public');
        if (!in_array($setupMode, ['public', 'private'], true)) {
            $setupMode = 'public';
        }

        $blobDBSupport = class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', PDO::getAvailableDrivers()));
        $gzSupport = function_exists('gzcompress') && function_exists('gzuncompress');

        $hostName = @gethostbyname($_SERVER['SERVER_ADDR'] ?? '');
        if (!$hostName || trim($hostName) === '' || $hostName === ($_SERVER['SERVER_ADDR'] ?? '')) {
            $hostName = $_SERVER['HTTP_HOST'] ?? 'localhost';
        }

        $dataFolder = preg_replace('/\/setup\/index\.php(.*)/', '/data/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']));
        $selfFolder = preg_replace('/\/setup\/index\.php(.*)/', '/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']));

        $smtpAuth = ($this->options['smtp_auth'] ?? 'no') === 'yes' ? 'yes' : 'no';
        $sendMethod = (string)($this->options['send_method'] ?? 'php');
        $receiveMethod = (string)($this->options['receive_method'] ?? 'pipe');
        $lng = (string)($this->options['lng'] ?? 'english');
        if ($lng !== 'deutsch') {
            $lng = 'english';
        }

        $defaultInvoice = @file_get_contents(__DIR__.'/rgtemplate.tpl');
        if ($defaultInvoice === false) {
            $defaultInvoice = '';
        }

        $prefsQuery = sprintf(
            'INSERT INTO '.DB_INSTALL_PREFIX.'prefs('
            .'template,language,selfurl,send_method,smtp_host,smtp_port,smtp_auth,smtp_user,smtp_pass,sendmail_path,'
            .'receive_method,pop3_host,pop3_port,pop3_user,pop3_pass,postmaster,mailbox_name,datafolder,selfurlfolder,hostname,'
            .'dnsbl,spamassassin,systememail,widgets_l,widgets_r,enablewebmail,mailbox_defaults,utf8,invoice_template,invoice_name,'
            .'layout,email_sender,setup_mode,first_email,autoemail,blobdb_support,blobdb_support_readonly,gzcompress,gzcompress_readonly) '
            .'VALUES('
            .'\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',%d,\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',%d,\'%s\',\'%s\','
            .'\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',%d,\'%s\',\'%s\','
            .'\'%s\',\'%s\',\'%s\',%d,\'%s\',\'%s\',\'%s\',%d,%d,\'%s\',\'%s\')',
            SETUP_DEFAULT_TEMPLATE,
            $lng,
            SQLEscape($url, $connection),
            SQLEscape($sendMethod, $connection),
            SQLEscape((string)($this->options['smtp_host'] ?? 'localhost'), $connection),
            (int)($this->options['smtp_port'] ?? 25),
            $smtpAuth,
            SQLEscape((string)($this->options['smtp_user'] ?? ''), $connection),
            SQLEscape((string)($this->options['smtp_pass'] ?? ''), $connection),
            SQLEscape((string)($this->options['sendmail_path'] ?? '/usr/sbin/sendmail'), $connection),
            SQLEscape($receiveMethod, $connection),
            SQLEscape((string)($this->options['pop3_host'] ?? ''), $connection),
            (int)($this->options['pop3_port'] ?? 110),
            SQLEscape((string)($this->options['pop3_user'] ?? ''), $connection),
            SQLEscape((string)($this->options['pop3_pass'] ?? ''), $connection),
            SQLEscape('Postmaster '.$firstDomain.' <postmaster@'.EncodeDomain($firstDomain).'>', $connection),
            SQLEscape($firstDomain.' Mail', $connection),
            SQLEscape($dataFolder, $connection),
            SQLEscape($selfFolder, $connection),
            SQLEscape($hostName, $connection),
            'ix.dnsbl.manitu.net:zen.spamhaus.org',
            'dnsbl.tornevall.org',
            SQLEscape('postmaster@'.EncodeDomain($firstDomain), $connection),
            'BMPlugin_Widget_Welcome,BMPlugin_Widget_EMail,BMPlugin_Widget_Websearch;BMPlugin_Widget_Mailspace,,BMPlugin_Widget_Quicklinks;BMPlugin_Widget_Webdiskspace,,',
            'BMPlugin_Widget_Websearch,BMPlugin_Widget_Calendar,BMPlugin_Widget_Notes;,BMPlugin_Widget_Tasks,',
            'yes',
            'a:8:{s:5:"mails";s:2:"on";s:11:"attachments";s:2:"on";s:3:"sms";s:2:"on";s:8:"calendar";s:2:"on";s:5:"tasks";s:2:"on";s:11:"addressbook";s:2:"on";s:5:"notes";s:2:"on";s:5:"files";s:2:"on";}',
            $utf8Mode ? 1 : 0,
            SQLEscape($defaultInvoice, $connection),
            SQLEscape('Accounting', $connection),
            SQLEscape('postmaster@'.EncodeDomain($firstDomain), $connection),
            $setupMode == 'public' ? 'yes' : 'no',
            SQLEscape('postmaster@'.EncodeDomain($firstDomain), $connection),
            SQLEscape('postmaster@'.EncodeDomain($firstDomain), $connection),
            $blobDBSupport ? 1 : 0,
            $blobDBSupport ? 1 : 0,
            $gzSupport ? 'yes' : 'no',
            $gzSupport ? 'yes' : 'no'
        );

        if (!mysqli_query($connection, $prefsQuery)) {
            mysqli_close($connection);
            throw new RuntimeException('Failed to create default config: '.mysqli_error($connection));
        }

        mysqli_query($connection, 'UPDATE '.DB_INSTALL_PREFIX.'prefs SET app_password_dav_mode=\'strict\'');

        $adminHash = SetupHashPassword($adminPlain, 'admin');
        if (!mysqli_query($connection, sprintf(
            'REPLACE INTO '.DB_INSTALL_PREFIX.'admins(`adminid`,`username`,`firstname`,`lastname`,`password`,`password_salt`,`type`,`notes`) VALUES '
            .'(1,\'%s\',\'%s\',\'%s\',\'%s\',\'\',0,\'\')',
            SQLEscape($adminUser, $connection),
            'Super',
            'Administrator',
            SQLEscape($adminHash, $connection)
        ))) {
            mysqli_close($connection);
            throw new RuntimeException('Failed to create admin account: '.mysqli_error($connection));
        }

        $groupQuery = sprintf(
            'INSERT INTO '.DB_INSTALL_PREFIX.'gruppen(id,titel,ftsearch) VALUES(1,\'%s\',\'%s\')',
            SQLEscape('Default group', $connection),
            $blobDBSupport ? 'yes' : 'no'
        );
        if (!mysqli_query($connection, $groupQuery)) {
            mysqli_close($connection);
            throw new RuntimeException('Failed to create default group: '.mysqli_error($connection));
        }

        $domainPos = 0;
        foreach ($domains as $domain) {
            $domainQuery = sprintf(
                'INSERT INTO '.DB_INSTALL_PREFIX.'domains(`domain`,`pos`) VALUES(\'%s\',%d)',
                SQLEscape(EncodeDomain($domain), $connection),
                $domainPos += 10
            );
            mysqli_query($connection, $domainQuery);
        }

        $userPW = SetupHashPassword($adminPlain, 'li');
        $postmasterQuery = sprintf(
            'INSERT INTO '.DB_INSTALL_PREFIX.'users(id,email,vorname,nachname,passwort,passwort_salt,gruppe,preview,plaintext_courier,soforthtml) '
            .'VALUES(1,\'%s\',\'%s\',\'%s\',\'%s\',\'\',1,\'yes\',\'yes\',\'yes\')',
            SQLEscape('postmaster@'.EncodeDomain($firstDomain), $connection),
            'Postmaster',
            SQLEscape($firstDomain, $connection),
            SQLEscape($userPW, $connection)
        );
        if (!mysqli_query($connection, $postmasterQuery)) {
            mysqli_close($connection);
            throw new RuntimeException('Failed to create postmaster: '.mysqli_error($connection));
        }

        foreach ($domains as $domain) {
            if ($domain !== $firstDomain) {
                $aliasQuery = sprintf(
                    'INSERT INTO '.DB_INSTALL_PREFIX.'aliase(email,user) VALUES(\'%s\',1)',
                    SQLEscape('postmaster@'.EncodeDomain($domain), $connection)
                );
                mysqli_query($connection, $aliasQuery);
            }
        }

        foreach ($GLOBALS['exampleData'] ?? [] as $query) {
            if (DB_INSTALL_PREFIX != SETUP_DEFAULT_PREFIX) {
                $query = str_replace(SETUP_DEFAULT_PREFIX, DB_INSTALL_PREFIX, $query);
            }
            if (!mysqli_query($connection, $query)) {
                echo 'Failed to execute example data query: '.mysqli_error($connection)."\n";
            }
        }

        foreach ($rootCertsData ?? [] as $query) {
            if (DB_INSTALL_PREFIX != SETUP_DEFAULT_PREFIX) {
                $query = str_replace(SETUP_DEFAULT_PREFIX, DB_INSTALL_PREFIX, $query);
            }
            if (!mysqli_query($connection, $query)) {
                echo 'Failed to execute root cert insert query: '.mysqli_error($connection)."\n";
            }
        }

        mysqli_query($connection, 'DELETE FROM '.DB_INSTALL_PREFIX.'certificates WHERE `type`=0 AND `userid`=0 AND `validto`<'.time());

        if ($setupMode === 'private') {
            mysqli_query($connection, 'INSERT INTO '.DB_INSTALL_PREFIX.'templateprefs(`template`,`key`,`value`) VALUES(\''.SETUP_DEFAULT_TEMPLATE.'\',\'hideSignup\',\'1\')');
        }

        $signKey = SetupCreateSignKey();

        $versionPath = dirname(__DIR__).'/serverlib/version.inc.php';
        $versionFp = fopen($versionPath, 'w');
        if ($versionFp) {
            fwrite($versionFp, sprintf('<?php define(\'B1GMAIL_VERSION\', $b1gmail_version = \'%s\'); ?>', $GLOBALS['target_version'] ?? '7.5.0-RC4'));
            fclose($versionFp);
        } else {
            throw new RuntimeException('Failed to open version.inc.php for writing');
        }

        $configPath = dirname(__DIR__).'/serverlib/config.inc.php';
        $configFile = "<?php\n// Generated ".date('r')."\n"
            ."\$mysql = array(\n"
            ."\t'host'\t\t=> ".var_export($host, true).",\n"
            ."\t'user'\t\t=> ".var_export($user, true).",\n"
            ."\t'pass'\t\t=> ".var_export($pass, true).",\n"
            ."\t'db'\t\t=> ".var_export($db, true).",\n"
            ."\t'prefix'\t=> ".var_export(DB_INSTALL_PREFIX, true)."\n"
            .");\ndefine('B1GMAIL_SIGNKEY', ".var_export($signKey, true).");\ndefine('DB_CHARSET', 'utf8mb4');\n";

        $configFp = fopen($configPath, 'w');
        if ($configFp) {
            fwrite($configFp, $configFile);
            fclose($configFp);
            SetupClearFileCache();
        } else {
            throw new RuntimeException('Failed to open config.inc.php for writing');
        }

        mysqli_close($connection);

        return [
            'url' => $url,
            'adminuser' => $adminUser,
            'adminpw' => $adminPlain,
            'domains' => $domains,
        ];
    }

    private function normalizeDomains(string $value): array
    {
        $domains = preg_split('/[\r\n,;:\s]+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);
        $clean = [];

        foreach ($domains as $domain) {
            $d = trim((string)$domain, " \t\n\r\0\x0B@");
            if (strlen($d) >= 2) {
                $clean[] = $d;
            }
        }

        return $clean ?: ['example.com'];
    }
}
