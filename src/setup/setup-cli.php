#!/usr/bin/env php
<?php
/*
 * b1gMail CLI installer.
 * Run from any directory: php src/setup/setup-cli.php --help
 */

$setupDir = __DIR__;
if (!chdir($setupDir)) {
    fwrite(STDERR, "ERROR: Cannot change to setup directory.\n");
    exit(1);
}

require $setupDir.'/common.inc.php';
require_once $setupDir.'/SetupInstaller.php';

function setupCliUsage(): void
{
    echo <<<HELP
Usage:
  php src/setup/setup-cli.php [options]

Required options:
  --mysql-host=HOST       MySQL server (default: localhost)
  --mysql-user=USER       MySQL user
  --mysql-pass=PASSWORD   MySQL password
  --mysql-db=DATABASE     Existing, empty database
  --admin-pass=PASSWORD   Initial admin password (minimum 8 characters)

Optional options:
  --mysql-prefix=PREFIX   Table prefix (default: ".SETUP_DEFAULT_PREFIX.")
  --admin-user=USER       Initial admin username (default: admin)
  --domains=LIST          Domains separated by spaces, commas, or newlines
  --url=URL               Public base URL, ending slash is optional
  --setup-mode=public|private (default: public)
  --lang=english|deutsch  Installer language (default: english)
  --receive-method=pipe|pop3 (default: pipe)
  --pop3-host=HOST --pop3-port=PORT --pop3-user=USER --pop3-pass=PASSWORD
  --pop3-tls              Use TLS for POP3
  --send-method=php|smtp|sendmail (default: php)
  --smtp-host=HOST --smtp-port=PORT --smtp-user=USER --smtp-pass=PASSWORD
  --smtp-auth             Enable SMTP authentication
  --sendmail-path=PATH    (default: /usr/sbin/sendmail)
  --check                 Validate settings only; do not install
  --dry-run               Validate settings only; do not install
  --help                  Show this help

Example:
  php src/setup/setup-cli.php --mysql-host=localhost --mysql-user=b1gmail \\
    --mysql-pass='secret' --mysql-db=b1gmail --admin-pass='change-me-now' \\
    --domains='example.com example.net' --url='https://mail.example.com/'

HELP;
}

function setupCliFail(string $message, int $exitCode = 1): never
{
    fwrite(STDERR, 'ERROR: '.$message."\n");
    exit($exitCode);
}

$options = getopt('', [
    'help',
    'mysql-host:', 'mysql-user:', 'mysql-pass:', 'mysql-db:', 'mysql-prefix:',
    'admin-user:', 'admin-pass:', 'domains:', 'url:', 'setup-mode:', 'lang:',
    'receive-method:', 'pop3-host:', 'pop3-port:', 'pop3-user:', 'pop3-pass:', 'pop3-tls',
    'send-method:', 'smtp-host:', 'smtp-port:', 'smtp-user:', 'smtp-pass:', 'smtp-auth',
    'sendmail-path:', 'check', 'dry-run',
]);

if (isset($options['help'])) {
    setupCliUsage();
    exit(0);
}

$required = ['mysql-user', 'mysql-pass', 'mysql-db', 'admin-pass'];
foreach ($required as $key) {
    if (!isset($options[$key]) || $options[$key] === '') {
        setupCliUsage();
        setupCliFail('Missing required option --'.$key);
    }
}

$mysqlHost = (string)($options['mysql-host'] ?? 'localhost');
$prefix = SetupNormalizePrefix((string)($options['mysql-prefix'] ?? SETUP_DEFAULT_PREFIX));
if (!defined('DB_INSTALL_PREFIX')) {
    define('DB_INSTALL_PREFIX', $prefix);
}

$lang = (string)($options['lang'] ?? 'english');
if (!in_array($lang, ['english', 'deutsch'], true)) {
    setupCliFail('--lang must be english or deutsch');
}
$lang_setup = [];
include $setupDir.'/'.$lang.'.lang.php';

$receiveMethod = (string)($options['receive-method'] ?? 'pipe');
$sendMethod = (string)($options['send-method'] ?? 'php');
if (!in_array($receiveMethod, ['pipe', 'pop3'], true)) {
    setupCliFail('--receive-method must be pipe or pop3');
}
if (!in_array($sendMethod, ['php', 'smtp', 'sendmail'], true)) {
    setupCliFail('--send-method must be php, smtp, or sendmail');
}
if (strlen((string)$options['admin-pass']) < 8) {
    setupCliFail('Admin password must contain at least 8 characters');
}
if (isset($options['setup-mode']) && !in_array($options['setup-mode'], ['public', 'private'], true)) {
    setupCliFail('--setup-mode must be public or private');
}

$checks = [];
$checks['PHP '.SETUP_PHP_MIN.' or newer'] = version_compare(PHP_VERSION, SETUP_PHP_MIN, '>=');
foreach (SetupRequiredExtensions() as $extension => $available) {
    $checks['PHP extension '.$extension] = $available;
}
foreach ($writeableFiles as $path) {
    $checks['Writable: '.$path] = is_writable(dirname(__DIR__).'/'.$path);
}
if (file_exists($setupDir.'/lock')) {
    setupCliFail('Setup lock file exists. Remove src/setup/lock only if you intend to run setup.');
}
if (SetupConfigLooksInstalled()) {
    setupCliFail('An existing installation was detected in serverlib/config.inc.php.');
}
if (!SetupHostAllowed($mysqlHost)) {
    setupCliFail('MySQL host is invalid or not allowed.');
}

foreach ($checks as $label => $ok) {
    echo sprintf("[%s] %s\n", $ok ? 'OK' : 'FAIL', $label);
}
if (in_array(false, $checks, true)) {
    setupCliFail('System checks failed. Correct the reported issues and retry.');
}

$connection = @mysqli_connect($mysqlHost, (string)$options['mysql-user'], (string)$options['mysql-pass']);
if (!$connection || !@mysqli_select_db($connection, (string)$options['mysql-db'])) {
    if ($connection) {
        mysqli_close($connection);
    }
    setupCliFail('Cannot connect to the requested MySQL database. Check host, credentials, database, and grants.');
}

mysqli_set_charset($connection, 'utf8mb4');
$tableExists = false;
$tableResult = mysqli_query($connection, 'SHOW TABLES');
if ($tableResult) {
    while ($table = mysqli_fetch_array($tableResult, MYSQLI_NUM)) {
        if (($table[0] ?? '') === DB_INSTALL_PREFIX.'prefs') {
            $tableExists = true;
            break;
        }
    }
    mysqli_free_result($tableResult);
}
mysqli_close($connection);
if ($tableExists) {
    setupCliFail('The database already contains the b1gMail prefs table for prefix '.DB_INSTALL_PREFIX.'. Use an empty database or a different prefix.');
}
echo "[OK] MySQL connection and database\n";

if ($receiveMethod === 'pop3') {
    $pop3Host = (string)($options['pop3-host'] ?? '');
    $pop3Port = (int)($options['pop3-port'] ?? (isset($options['pop3-tls']) ? 995 : 110));
    if ($pop3Host === '' || !CheckPOP3Login(
        $pop3Host,
        (string)($options['pop3-user'] ?? ''),
        (string)($options['pop3-pass'] ?? ''),
        $pop3Port,
        isset($options['pop3-tls'])
    )) {
        setupCliFail('POP3 login validation failed.');
    }
    echo "[OK] POP3 connection and credentials\n";
}

$sendmailPath = (string)($options['sendmail-path'] ?? '/usr/sbin/sendmail');
if ($sendMethod === 'sendmail' && (!is_file($sendmailPath) || !is_executable($sendmailPath))) {
    setupCliFail('Sendmail path is not an executable file: '.$sendmailPath);
}
if ($sendMethod === 'sendmail') {
    echo "[OK] Sendmail executable\n";
}

if (isset($options['check']) || isset($options['dry-run'])) {
    echo "Checks passed. No installation was performed.\n";
    exit(0);
}

// SetupInstaller derives application paths from this value as the web installer does.
$_SERVER['SCRIPT_FILENAME'] = $setupDir.'/index.php';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/setup/';

$installerOptions = [
    'mysql_host' => $mysqlHost,
    'mysql_user' => (string)$options['mysql-user'],
    'mysql_pass' => (string)$options['mysql-pass'],
    'mysql_db' => (string)$options['mysql-db'],
    'mysql_prefix' => $prefix,
    'adminuser' => (string)($options['admin-user'] ?? 'admin'),
    'adminpw' => (string)$options['admin-pass'],
    'adminpw2' => (string)$options['admin-pass'],
    'domains' => (string)($options['domains'] ?? "example.com\nexample.net\nexample.org"),
    'url' => (string)($options['url'] ?? 'http://localhost/'),
    'setup_mode' => (string)($options['setup-mode'] ?? 'public'),
    'receive_method' => $receiveMethod,
    'send_method' => $sendMethod,
    'smtp_host' => (string)($options['smtp-host'] ?? 'localhost'),
    'smtp_port' => (string)($options['smtp-port'] ?? 25),
    'smtp_user' => (string)($options['smtp-user'] ?? ''),
    'smtp_pass' => (string)($options['smtp-pass'] ?? ''),
    'smtp_auth' => isset($options['smtp-auth']) ? 'yes' : 'no',
    'pop3_host' => (string)($options['pop3-host'] ?? ''),
    'pop3_port' => (string)($options['pop3-port'] ?? (isset($options['pop3-tls']) ? 995 : 110)),
    'pop3_user' => (string)($options['pop3-user'] ?? ''),
    'pop3_pass' => (string)($options['pop3-pass'] ?? ''),
    'lng' => $lang,
];

try {
    $result = (new SetupInstaller($installerOptions))->install();
    if (!SetupWriteLock('lock')) {
        fwrite(STDERR, "WARNING: Installation finished, but setup lock could not be written. Remove/disable the setup directory immediately.\n");
    }
    echo "Installation complete.\n";
    echo 'URL: '.$result['url']."\n";
    echo 'Admin user: '.$result['adminuser']."\n";
    echo 'Admin password: '.$result['adminpw']."\n";
    echo "Remove or disable the setup directory after installation.\n";
} catch (Throwable $e) {
    setupCliFail($e->getMessage());
}
