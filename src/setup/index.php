<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al, 2022 b1gMail.eu
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

require './common.inc.php';

define('STEP_SELECT_LANGUAGE', 0);
define('STEP_WELCOME', 1);
define('STEP_SYSTEMCHECK', 3);
define('STEP_MYSQL', 5);
define('STEP_CHECK_MYSQL', 6);
define('STEP_CHECK_EMAIL', 7);
define('STEP_INSTALL', 8);

$defaultInvoice = file_get_contents('./rgtemplate.tpl');

if (!isset($_REQUEST['step'])) {
    $step = STEP_SELECT_LANGUAGE;
} else {
    $step = (int) $_REQUEST['step'];
}

ReadLanguage();

if (!SetupCsrfOk()) {
    $step = STEP_SELECT_LANGUAGE;
    $setupError = $lang_setup['csrf_fail'];
}

if (!defined('DB_INSTALL_PREFIX')) {
    define('DB_INSTALL_PREFIX', SetupNormalizePrefix(SetupInput('mysql_prefix', SETUP_DEFAULT_PREFIX)));
}

SetupAbortIfLocked('lock');
SetupAbortIfAlreadyInstalled();

pageHeader();

if (!empty($setupError)) {
    echo SetupAlert('danger', $setupError);
}

/*
 * select language
 */
if ($step == STEP_SELECT_LANGUAGE) {
    $nextStep = STEP_WELCOME; ?>
	<h1><?php echo SetupH($lang_setup['selectlanguage']); ?></h1>
	<p class="text-secondary"><?php echo $lang_setup['selectlanguage_text']; ?></p>
	<div class="form-selectgroup form-selectgroup-boxes setup-langs">
		<label class="form-selectgroup-item">
			<input class="form-selectgroup-input" type="radio" id="lang_deutsch" name="lng" value="deutsch"<?php if ($lang == 'deutsch') {
        echo ' checked="checked"';
    } ?> />
			<span class="form-selectgroup-label setup-lang-card">
				<span class="setup-lang-badge setup-lang-badge-de" aria-hidden="true">DE</span>
				<span class="setup-lang-meta">
					<span class="form-selectgroup-title">Deutsch</span>
					<span class="text-secondary small">German</span>
				</span>
				<span class="form-selectgroup-check"></span>
			</span>
		</label>
		<label class="form-selectgroup-item">
			<input class="form-selectgroup-input" type="radio" id="lang_english" name="lng" value="english"<?php if ($lang == 'english') {
        echo ' checked="checked"';
    } ?> />
			<span class="form-selectgroup-label setup-lang-card">
				<span class="setup-lang-badge setup-lang-badge-en" aria-hidden="true">EN</span>
				<span class="setup-lang-meta">
					<span class="form-selectgroup-title">English</span>
					<span class="text-secondary small">Englisch</span>
				</span>
				<span class="form-selectgroup-check"></span>
			</span>
		</label>
	</div>
	<?php
}

/*
 * welcome / license
 */
elseif ($step == STEP_WELCOME) {
    $nextStep = STEP_SYSTEMCHECK;
    $backStep = STEP_SELECT_LANGUAGE; ?>
	<h1><?php echo SetupH($lang_setup['welcome']); ?></h1>
	<p><?php echo $lang_setup['welcome_text']; ?></p>
	<?php
}

/*
 * system check
 */
elseif ($step == STEP_SYSTEMCHECK) {
    $nextStep = STEP_MYSQL;
    $backStep = STEP_WELCOME;

    $phpOk = version_compare(PHP_VERSION, SETUP_PHP_MIN, '>=');
    $rows = [
        [
            'label' => $lang_setup['phpversion'],
            'required' => SETUP_PHP_MIN,
            'available' => PHP_VERSION,
            'ok' => $phpOk,
        ],
    ];
    foreach (SetupRequiredExtensions() as $ext => $ok) {
        $labelKey = 'ext_'.$ext;
        $rows[] = [
            'label' => $lang_setup[$labelKey] ?? $ext,
            'required' => $lang_setup['yes'],
            'available' => $ok ? $lang_setup['yes'] : $lang_setup['no'],
            'ok' => $ok,
        ];
        if (!$ok) {
            $phpOk = false;
        }
    }
    list($fileRows, $chmodCommands, $filesOk) = SetupCollectWritableRows($writeableFiles);
    $rows = array_merge($rows, $fileRows);
    if (!$phpOk || !$filesOk) {
        $nextStep = STEP_SYSTEMCHECK;
    } ?>
	<h1><?php echo SetupH($lang_setup['syscheck']); ?></h1>
	<p><?php echo $lang_setup['syscheck_text']; ?></p>
	<?php SetupRenderCheckTable($rows); ?>
	<?php echo SetupRenderChmod($chmodCommands); ?>
	<?php echo SetupAlert($nextStep == STEP_MYSQL ? 'success' : 'danger', $nextStep == STEP_MYSQL ? $lang_setup['checkok_text'] : $lang_setup['checkfail_text'], '', array('class' => 'mt-3')); ?>
	<?php
}

/*
 * mysql login
 */
elseif ($step == STEP_MYSQL) {
    $nextStep = STEP_CHECK_MYSQL;
    $backStep = STEP_SYSTEMCHECK; ?>
	<h1><?php echo SetupH($lang_setup['db']); ?></h1>
	<p><?php echo $lang_setup['dbfresh_text']; ?></p>
	<div class="mb-3">
		<label class="form-label" for="mysql_host"><?php echo SetupH($lang_setup['mysql_host']); ?></label>
		<input class="form-control" id="mysql_host" name="mysql_host" type="text" value="<?php echo SetupH(SetupInput('mysql_host', 'localhost')); ?>" required="required" />
	</div>
	<div class="mb-3">
		<label class="form-label" for="mysql_user"><?php echo SetupH($lang_setup['mysql_user']); ?></label>
		<input class="form-control" id="mysql_user" name="mysql_user" type="text" value="<?php echo SetupH(SetupInput('mysql_user')); ?>" required="required" autocomplete="off" />
	</div>
	<div class="mb-3">
		<label class="form-label" for="mysql_pass"><?php echo SetupH($lang_setup['mysql_pass']); ?></label>
		<?php echo SetupPasswordField('mysql_pass', 'mysql_pass', SetupInput('mysql_pass')); ?>
	</div>
	<div class="mb-3">
		<label class="form-label" for="mysql_db"><?php echo SetupH($lang_setup['mysql_db']); ?></label>
		<input class="form-control" id="mysql_db" name="mysql_db" type="text" value="<?php echo SetupH(SetupInput('mysql_db')); ?>" required="required" />
	</div>
	<div class="mb-3">
		<label class="form-label" for="mysql_prefix"><?php echo SetupH($lang_setup['mysql_prefix']); ?></label>
		<input class="form-control" id="mysql_prefix" name="mysql_prefix" type="text" value="<?php echo SetupH(SetupInput('mysql_prefix', SETUP_DEFAULT_PREFIX)); ?>" />
	</div>
	<?php
}

/*
 * check mysql login
 */
elseif ($step == STEP_CHECK_MYSQL) {
    $connection = CheckMySQLLogin(
        SetupInput('mysql_host'),
        SetupInput('mysql_user'),
        SetupInput('mysql_pass'),
        SetupInput('mysql_db')
    );
    if ($connection) {
        $b1gMailInDB = false;
        $res = mysqli_query($connection, 'SHOW TABLES');
        while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
            if ($row[0] == DB_INSTALL_PREFIX.'prefs') {
                $b1gMailInDB = true;
                break;
            }
        }
        mysqli_free_result($res);
        mysqli_close($connection);

        if ($b1gMailInDB) {
            $nextStep = STEP_MYSQL;
            $backStep = STEP_SYSTEMCHECK; ?>
			<h1><?php echo SetupH($lang_setup['db']); ?></h1>
			<?php echo SetupAlert('danger', $lang_setup['dbexists_text']); ?>
			<?php
        } else {
            $nextStep = STEP_CHECK_EMAIL;
            $backStep = STEP_MYSQL;
            $receiveMethod = SetupInput('receive_method', 'pipe');
            $sendMethod = SetupInput('send_method', 'php'); ?>
			<h1><?php echo SetupH($lang_setup['emailcfg']); ?></h1>
			<p><?php echo $lang_setup['emailcfg_text']; ?></p>

			<h2 class="h3"><?php echo SetupH($lang_setup['setupmode']); ?></h2>
			<p class="text-secondary"><?php echo $lang_setup['mode_note']; ?></p>
			<div class="setup-option">
				<label class="form-check">
					<input class="form-check-input" type="radio" id="setup_mode_public" name="setup_mode" value="public"<?php if (SetupInput('setup_mode', 'public') !== 'private') {
                echo ' checked="checked"';
            } ?> />
					<span class="form-check-label"><strong><?php echo $lang_setup['mode_public']; ?></strong></span>
				</label>
				<div class="text-secondary small mt-1"><?php echo $lang_setup['mode_public_desc']; ?></div>
			</div>
			<div class="setup-option">
				<label class="form-check">
					<input class="form-check-input" type="radio" id="setup_mode_private" name="setup_mode" value="private"<?php if (SetupInput('setup_mode') === 'private') {
                echo ' checked="checked"';
            } ?> />
					<span class="form-check-label"><strong><?php echo $lang_setup['mode_private']; ?></strong></span>
				</label>
				<div class="text-secondary small mt-1"><?php echo $lang_setup['mode_private_desc']; ?></div>
			</div>

			<h2 class="h3 mt-4"><?php echo SetupH($lang_setup['receiving']); ?></h2>
			<div class="setup-option">
				<label class="form-check">
					<input class="form-check-input" type="radio" id="receive_method_pipe" name="receive_method" value="pipe"<?php if ($receiveMethod !== 'pop3') {
                echo ' checked="checked"';
            } ?> />
					<span class="form-check-label"><?php echo $lang_setup['pipe']; ?></span>
				</label>
			</div>
			<div class="setup-option">
				<label class="form-check">
					<input class="form-check-input" type="radio" id="receive_method_pop3" name="receive_method" value="pop3"<?php if ($receiveMethod === 'pop3') {
                echo ' checked="checked"';
            } ?> />
					<span class="form-check-label"><?php echo $lang_setup['pop3gateway']; ?></span>
				</label>
				<div class="setup-option-fields" data-setup-pop3>
					<div class="mb-3">
						<label class="form-label" for="pop3_host"><?php echo SetupH($lang_setup['pop3_host']); ?></label>
						<input class="form-control" id="pop3_host" name="pop3_host" type="text" value="<?php echo SetupH(SetupInput('pop3_host')); ?>" />
					</div>
					<div class="mb-3">
						<label class="form-label" for="pop3_port"><?php echo SetupH($lang_setup['pop3_port']); ?></label>
						<input class="form-control" id="pop3_port" name="pop3_port" type="number" value="<?php echo SetupH(SetupInput('pop3_port', '110')); ?>" />
					</div>
					<label class="form-check mb-3">
						<input class="form-check-input" type="checkbox" id="pop3_tls" name="pop3_tls" value="1"<?php if (SetupInput('pop3_tls')) {
                echo ' checked="checked"';
            } ?> />
						<span class="form-check-label"><?php echo $lang_setup['pop3_tls']; ?></span>
					</label>
					<div class="mb-3">
						<label class="form-label" for="pop3_user"><?php echo SetupH($lang_setup['pop3_user']); ?></label>
						<input class="form-control" id="pop3_user" name="pop3_user" type="text" value="<?php echo SetupH(SetupInput('pop3_user')); ?>" autocomplete="off" />
					</div>
					<div class="mb-3">
						<label class="form-label" for="pop3_pass"><?php echo SetupH($lang_setup['pop3_pass']); ?></label>
						<?php echo SetupPasswordField('pop3_pass', 'pop3_pass', SetupInput('pop3_pass')); ?>
					</div>
				</div>
			</div>

			<h2 class="h3 mt-4"><?php echo SetupH($lang_setup['sending']); ?></h2>
			<div class="setup-option">
				<label class="form-check">
					<input class="form-check-input" type="radio" id="send_method_phpmail" name="send_method" value="php"<?php if ($sendMethod === 'php') {
                echo ' checked="checked"';
            } ?> />
					<span class="form-check-label"><?php echo $lang_setup['phpmail']; ?></span>
				</label>
			</div>
			<div class="setup-option">
				<label class="form-check">
					<input class="form-check-input" type="radio" id="send_method_smtp" name="send_method" value="smtp"<?php if ($sendMethod === 'smtp') {
                echo ' checked="checked"';
            } ?> />
					<span class="form-check-label"><?php echo $lang_setup['smtp']; ?></span>
				</label>
				<div class="setup-option-fields" data-setup-smtp>
					<div class="mb-3">
						<label class="form-label" for="smtp_host"><?php echo SetupH($lang_setup['smtp_host']); ?></label>
						<input class="form-control" id="smtp_host" name="smtp_host" type="text" value="<?php echo SetupH(SetupInput('smtp_host', 'localhost')); ?>" />
					</div>
					<div class="mb-3">
						<label class="form-label" for="smtp_port"><?php echo SetupH($lang_setup['smtp_port']); ?></label>
						<input class="form-control" id="smtp_port" name="smtp_port" type="number" value="<?php echo SetupH(SetupInput('smtp_port', '25')); ?>" />
					</div>
					<label class="form-check mb-3">
						<input class="form-check-input" type="checkbox" id="smtp_auth" name="smtp_auth" value="yes"<?php if (SetupInput('smtp_auth') === 'yes') {
                echo ' checked="checked"';
            } ?> />
						<span class="form-check-label"><?php echo $lang_setup['smtp_auth']; ?></span>
					</label>
					<div data-setup-smtp-auth>
						<div class="mb-3">
							<label class="form-label" for="smtp_user"><?php echo SetupH($lang_setup['smtp_user']); ?></label>
							<input class="form-control" id="smtp_user" name="smtp_user" type="text" value="<?php echo SetupH(SetupInput('smtp_user')); ?>" autocomplete="off" />
						</div>
						<div class="mb-3">
							<label class="form-label" for="smtp_pass"><?php echo SetupH($lang_setup['smtp_pass']); ?></label>
							<?php echo SetupPasswordField('smtp_pass', 'smtp_pass', SetupInput('smtp_pass')); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="setup-option">
				<label class="form-check">
					<input class="form-check-input" type="radio" id="send_method_sendmail" name="send_method" value="sendmail"<?php if ($sendMethod === 'sendmail') {
                echo ' checked="checked"';
            } ?> />
					<span class="form-check-label"><?php echo $lang_setup['sendmail']; ?></span>
				</label>
				<div class="setup-option-fields" data-setup-sendmail>
					<label class="form-label" for="sendmail_path"><?php echo SetupH($lang_setup['sendmail_path']); ?></label>
					<input class="form-control" id="sendmail_path" name="sendmail_path" type="text" value="<?php echo SetupH(SetupInput('sendmail_path', '/usr/sbin/sendmail')); ?>" />
				</div>
			</div>
			<?php
        }
    } else {
        $nextStep = STEP_MYSQL;
        $backStep = STEP_SYSTEMCHECK; ?>
		<h1><?php echo SetupH($lang_setup['db']); ?></h1>
		<?php echo SetupAlert('danger', $lang_setup['dbfail_text']); ?>
		<?php
    }
}

/*
 * check email config + access data
 */
elseif ($step == STEP_CHECK_EMAIL) {
    $receiveMethod = SetupInput('receive_method', 'pipe');
    $sendMethod = SetupInput('send_method', 'php');
    $pop3Ok = $receiveMethod != 'pop3'
        || CheckPOP3Login(
            SetupInput('pop3_host'),
            SetupInput('pop3_user'),
            SetupInput('pop3_pass'),
            (int) SetupInput('pop3_port', '110'),
            SetupInput('pop3_tls') !== ''
        );
    $sendmailOk = $sendMethod != 'sendmail'
        || (file_exists(SetupInput('sendmail_path')) && is_executable(SetupInput('sendmail_path')));

    if (!$pop3Ok) {
        $nextStep = STEP_CHECK_MYSQL;
        $backStep = STEP_MYSQL; ?>
		<h1><?php echo SetupH($lang_setup['emailcfg']); ?></h1>
		<?php echo SetupAlert('danger', $lang_setup['emailcfgpop3fail_text']); ?>
		<?php
    } elseif (!$sendmailOk) {
        $nextStep = STEP_CHECK_MYSQL;
        $backStep = STEP_MYSQL; ?>
		<h1><?php echo SetupH($lang_setup['emailcfg']); ?></h1>
		<?php echo SetupAlert('danger', $lang_setup['emailcfgsmfail_text']); ?>
		<?php
    } else {
        $nextStep = STEP_INSTALL;
        $backStep = STEP_CHECK_MYSQL;
        if (SetupInput('adminpw') === '') {
            $generatedAdminPw = GeneratePW();
            $_POST['adminpw'] = $generatedAdminPw;
            $_POST['adminpw2'] = $generatedAdminPw;
        }
        if (SetupInput('adminuser') === '') {
            $_SESSION['setup_data']['adminuser'] = 'admin';
        }
        if (SetupInput('url') === '') {
            $_SESSION['setup_data']['url'] = SetupDetectSelfUrl();
        } ?>
		<h1><?php echo SetupH($lang_setup['misc']); ?></h1>
		<p><?php echo $lang_setup['misc_text']; ?></p>
		<div class="mb-3">
			<label class="form-label" for="adminuser"><?php echo SetupH($lang_setup['adminuser']); ?></label>
			<input class="form-control" id="adminuser" name="adminuser" type="text" value="<?php echo SetupH(SetupInput('adminuser', 'admin')); ?>" required="required" autocomplete="username" />
		</div>
		<div class="mb-3">
			<label class="form-label" for="adminpw"><?php echo SetupH($lang_setup['adminpw']); ?></label>
			<?php echo SetupPasswordField('adminpw', 'adminpw', SetupInput('adminpw'), true); ?>
		</div>
		<div class="mb-3">
			<label class="form-label" for="adminpw2"><?php echo SetupH($lang_setup['adminpw_confirm']); ?></label>
			<?php echo SetupPasswordField('adminpw2', 'adminpw2', SetupInput('adminpw2'), true); ?>
		</div>
		<div class="mb-3">
			<label class="form-label" for="domains"><?php echo SetupH($lang_setup['domains']); ?></label>
			<textarea class="form-control setup-domains" id="domains" name="domains" rows="4"><?php echo SetupH(SetupInput('domains', "example.com\nexample.net\nexample.org")); ?></textarea>
		</div>
		<div class="mb-3">
			<label class="form-label" for="url"><?php echo SetupH($lang_setup['url']); ?></label>
			<input class="form-control" id="url" name="url" type="url" value="<?php echo SetupH(SetupInput('url', SetupDetectSelfUrl())); ?>" required="required" />
		</div>
		<?php
    }
}

/*
 * install
 */
elseif ($step == STEP_INSTALL) {
    $adminPlain = SetupInput('adminpw');
    $adminPlain2 = SetupInput('adminpw2');
    $adminUser = SetupNormalizeAdminUser(SetupInput('adminuser', 'admin'));

    if (strlen($adminPlain) < 8) {
        $nextStep = STEP_CHECK_EMAIL;
        $backStep = STEP_CHECK_MYSQL; ?>
		<h1><?php echo SetupH($lang_setup['misc']); ?></h1>
		<?php echo SetupAlert('danger', $lang_setup['pw_too_short']); ?>
		<?php
    } elseif ($adminPlain !== $adminPlain2) {
        $nextStep = STEP_CHECK_EMAIL;
        $backStep = STEP_CHECK_MYSQL; ?>
		<h1><?php echo SetupH($lang_setup['misc']); ?></h1>
		<?php echo SetupAlert('danger', $lang_setup['password_mismatch']); ?>
		<?php
    } else {
        include '../serverlib/database.struct.php';
        include './data/rootcerts.data.php';

        $databaseStructure = SetupRewriteStructurePrefix(json_decode($databaseStructure, JSON_OBJECT_AS_ARRAY), DB_INSTALL_PREFIX);

        $url = SetupInput('url', SetupDetectSelfUrl());
        if (substr($url, -1) != '/') {
            $url .= '/';
        }
        $domains = explode("\n", str_replace(["\r", '@', ',', ';', ':', ' '], '', SetupInput('domains')));
        foreach ($domains as $key => $val) {
            $val = trim($val);
            if (strlen($val) < 2) {
                unset($domains[$key]);
            } else {
                $domains[$key] = $val;
            }
        }
        $domains = count($domains) > 0 ? $domains : ['example.com'];
        list($firstDomain) = $domains;

        ob_start();

        $dbStructResult = 'error';
        $defaultConfigResult = 'error';
        $adminAccountResult = 'error';
        $defaultGroupResult = 'error';
        $exampleDataResult = 'error';
        $postmasterResult = 'error';
        $configResult = 'error';

        $connection = SetupHostAllowed(SetupInput('mysql_host'))
            ? mysqli_connect(SetupInput('mysql_host'), SetupInput('mysql_user'), SetupInput('mysql_pass'), SetupInput('mysql_db'))
            : false;
        if ($connection) {
            if (mysqli_select_db($connection, SetupInput('mysql_db'))) {
                mysqli_set_charset($connection, 'utf8mb4');
                @mysqli_query($connection, 'SET SESSION sql_mode=\'\'');

                $setupMode = SetupInput('setup_mode', 'public');
                if (!in_array($setupMode, ['public', 'private'], true)) {
                    $setupMode = 'public';
                }

                $utf8Mode = true;
                $dbStructResult = 'ok';
                $result = CreateDatabaseStructure($connection, $databaseStructure, $utf8Mode, SetupInput('mysql_db'));
                foreach ($result as $query => $queryResult) {
                    if ($queryResult !== true) {
                        $dbStructResult = 'warning';
                        echo 'Failed to execute structure query: '.$queryResult."\n";
                    }
                }

                $blobDBSupport = class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', PDO::getAvailableDrivers()));
                $gzSupport = function_exists('gzcompress') && function_exists('gzuncompress');
                $hostName = @gethostbyname($_SERVER['SERVER_ADDR'] ?? '');
                if (!$hostName || trim($hostName) == '' || $hostName == ($_SERVER['SERVER_ADDR'] ?? '')) {
                    $hostName = $_SERVER['HTTP_HOST'] ?? 'localhost';
                }
                $dataFolder = preg_replace('/\/setup\/index\.php(.*)/', '/data/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']));
                $selfFolder = preg_replace('/\/setup\/index\.php(.*)/', '/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']));
                $smtpAuth = SetupInput('smtp_auth') === 'yes' ? 'yes' : 'no';
                $prefsQuery = sprintf('INSERT INTO '.DB_INSTALL_PREFIX.'prefs(template,language,selfurl,mobile_url,send_method,smtp_host,smtp_port,smtp_auth,smtp_user,smtp_pass,sendmail_path,receive_method,pop3_host,pop3_port,pop3_user,pop3_pass,passmail_abs,titel,datafolder,selffolder,b1gmta_host,dnsbl,signup_dnsbl,smsreply_abs,widget_order_start,widget_order_organizer,structstorage,search_in,db_is_utf8,rgtemplate,pay_emailfrom,pay_emailfromemail,regenabled,contactform_to,ap_autolock_notify_to,blobstorage_provider,blobstorage_provider_webdisk,blobstorage_compress,blobstorage_webdisk_compress) '
                            .'VALUES(\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',%d,\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',%d,\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',%d,\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',%d,%d,\'%s\',\'%s\')',
                            SETUP_DEFAULT_TEMPLATE,
                            SetupInput('lng') == 'deutsch' ? 'deutsch' : 'english',
                            SQLEscape($url, $connection),
                            SQLEscape($url.'m/', $connection),
                            SQLEscape(SetupInput('send_method', 'php'), $connection),
                            SQLEscape(SetupInput('smtp_host', 'localhost'), $connection),
                            (int) SetupInput('smtp_port', '25'),
                            $smtpAuth,
                            SQLEscape(SetupInput('smtp_user'), $connection),
                            SQLEscape(SetupInput('smtp_pass'), $connection),
                            SQLEscape(SetupInput('sendmail_path', '/usr/sbin/sendmail'), $connection),
                            SQLEscape(SetupInput('receive_method', 'pipe'), $connection),
                            SQLEscape(SetupInput('pop3_host'), $connection),
                            (int) SetupInput('pop3_port', '110'),
                            SQLEscape(SetupInput('pop3_user'), $connection),
                            SQLEscape(SetupInput('pop3_pass'), $connection),
                            SQLEscape('"Postmaster '.$firstDomain.'" <postmaster@'.EncodeDomain($firstDomain).'>', $connection),
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
                            SQLEscape('a:8:{s:5:"mails";s:2:"on";s:11:"attachments";s:2:"on";s:3:"sms";s:2:"on";s:8:"calendar";s:2:"on";s:5:"tasks";s:2:"on";s:11:"addressbook";s:2:"on";s:5:"notes";s:2:"on";s:7:"webdisk";s:2:"on";}', $connection),
                            $utf8Mode ? 1 : 0,
                            SQLEscape($defaultInvoice, $connection),
                            SQLEscape($lang_setup['accounting'], $connection),
                            SQLEscape('postmaster@'.EncodeDomain($firstDomain), $connection),
                            $setupMode == 'public' ? 'yes' : 'no',
                            SQLEscape('postmaster@'.EncodeDomain($firstDomain), $connection),
                            SQLEscape('postmaster@'.EncodeDomain($firstDomain), $connection),
                            $blobDBSupport ? 1 : 0,
                            $blobDBSupport ? 1 : 0,
                            $gzSupport ? 'yes' : 'no',
                            $gzSupport ? 'yes' : 'no');
                if (mysqli_query($connection, $prefsQuery)) {
                    $defaultConfigResult = 'ok';
                } else {
                    echo 'Failed to create default config: '.mysqli_error($connection)."\n";
                }

                $adminPW = SetupHashPassword($adminPlain, 'admin');
                if (mysqli_query($connection, sprintf('REPLACE INTO '.DB_INSTALL_PREFIX.'admins(`adminid`,`username`,`firstname`,`lastname`,`password`,`password_salt`,`type`,`notes`) VALUES '
                            .'(1,\'%s\',\'%s\',\'%s\',\'%s\',\'\',0,\'\')',
                            SQLEscape($adminUser, $connection),
                            'Super',
                            'Administrator',
                            SQLEscape($adminPW, $connection)))) {
                    $adminAccountResult = 'ok';
                } else {
                    echo 'Failed to create admin account: '.mysqli_error($connection)."\n";
                }

                $groupQuery = sprintf('INSERT INTO '.DB_INSTALL_PREFIX.'gruppen(id,titel,ftsearch) VALUES(1,\'%s\',\'%s\')',
                    SQLEscape($lang_setup['defaultgroup'], $connection),
                    $blobDBSupport ? 'yes' : 'no');
                if (mysqli_query($connection, $groupQuery)) {
                    $defaultGroupResult = 'ok';
                } else {
                    echo 'Failed to create default group: '.mysqli_error($connection)."\n";
                }

                $domainPos = 0;
                foreach ($domains as $domain) {
                    $domainQuery = sprintf('INSERT INTO '.DB_INSTALL_PREFIX.'domains(`domain`,`pos`) VALUES(\'%s\',%d)',
                        SQLEscape(EncodeDomain($domain), $connection),
                        $domainPos += 10);
                    mysqli_query($connection, $domainQuery);
                }

                $userPW = SetupHashPassword($adminPlain, 'li');
                $postmasterQuery = sprintf('INSERT INTO '.DB_INSTALL_PREFIX.'users(id,email,vorname,nachname,passwort,passwort_salt,gruppe,preview,plaintext_courier,soforthtml) '
                                    .'VALUES(1,\'%s\',\'%s\',\'%s\',\'%s\',\'\',1,\'yes\',\'yes\',\'yes\')',
                                    SQLEscape('postmaster@'.EncodeDomain($firstDomain), $connection),
                                    'Postmaster',
                                    SQLEscape($firstDomain, $connection),
                                    SQLEscape($userPW, $connection));
                if (mysqli_query($connection, $postmasterQuery)) {
                    $postmasterResult = 'ok';
                } else {
                    echo 'Failed to create postmaster: '.mysqli_error($connection)."\n";
                }

                foreach ($domains as $domain) {
                    if ($domain != $firstDomain) {
                        $aliasQuery = sprintf('INSERT INTO '.DB_INSTALL_PREFIX.'aliase(email,user) VALUES(\'%s\',1)',
                            SQLEscape('postmaster@'.EncodeDomain($domain), $connection));
                        mysqli_query($connection, $aliasQuery);
                    }
                }

                $exampleDataResult = 'ok';
                foreach ($exampleData as $query) {
                    if (DB_INSTALL_PREFIX != SETUP_DEFAULT_PREFIX) {
                        $query = str_replace(SETUP_DEFAULT_PREFIX, DB_INSTALL_PREFIX, $query);
                    }
                    if (!mysqli_query($connection, $query)) {
                        echo 'Failed to execute example data query: '.mysqli_error($connection)."\n";
                        $exampleDataResult = 'warning';
                    }
                }

                foreach ($rootCertsData as $query) {
                    if (DB_INSTALL_PREFIX != SETUP_DEFAULT_PREFIX) {
                        $query = str_replace(SETUP_DEFAULT_PREFIX, DB_INSTALL_PREFIX, $query);
                    }
                    if (!mysqli_query($connection, $query)) {
                        echo 'Failed to execute root cert insert query: '.mysqli_error($connection)."\n";
                        $exampleDataResult = 'warning';
                    }
                }

                mysqli_query($connection, 'DELETE FROM '.DB_INSTALL_PREFIX.'certificates WHERE `type`=0 AND `userid`=0 AND `validto`<'.time());

                if ($setupMode == 'private') {
                    mysqli_query($connection, 'INSERT INTO '.DB_INSTALL_PREFIX.'templateprefs(`template`,`key`,`value`) VALUES(\''.SETUP_DEFAULT_TEMPLATE.'\',\'hideSignup\',\'1\')');
                }

                $signKey = SetupCreateSignKey();
                $fp = fopen('../serverlib/version.inc.php', 'w');
                fwrite($fp, sprintf('<?php define(\'B1GMAIL_VERSION\', $b1gmail_version = \'%s\'); ?>', $target_version));
                fclose($fp);

                $configFile = "<?php\n// Generated ".date('r')."\n"
                    ."\$mysql = array(\n"
                    ."\t'host'\t\t=> ".var_export(SetupInput('mysql_host'), true).",\n"
                    ."\t'user'\t\t=> ".var_export(SetupInput('mysql_user'), true).",\n"
                    ."\t'pass'\t\t=> ".var_export(SetupInput('mysql_pass'), true).",\n"
                    ."\t'db'\t\t=> ".var_export(SetupInput('mysql_db'), true).",\n"
                    ."\t'prefix'\t=> ".var_export(DB_INSTALL_PREFIX, true)."\n"
                    .");\ndefine('B1GMAIL_SIGNKEY', ".var_export($signKey, true).");\ndefine('DB_CHARSET', 'utf8mb4');\n";
                $fp = fopen('../serverlib/config.inc.php', 'w');
                if ($fp) {
                    fwrite($fp, $configFile);
                    fclose($fp);
                    $configResult = 'ok';
                    SetupClearFileCache();
                } else {
                    echo 'Failed to open config.inc.php for writing'."\n";
                }
            } else {
                echo 'MySQL database selection failed'."\n";
            }
        } else {
            echo 'MySQL connection failed'."\n";
        }

        $installLog = trim(strip_tags(ob_get_contents()));
        ob_end_clean();

        $lockOk = SetupWriteLock('lock'); ?>
	<h1><?php echo SetupH($lang_setup['installing']); ?></h1>
	<p><?php echo $lang_setup['installing_text']; ?></p>
	<?php SetupCloseCardBody(); ?>
	<div class="table-responsive">
	<table class="table table-vcenter card-table">
		<tr>
			<th><?php echo sprintf($lang_setup['inst_dbstruct'], $databaseStructureVersion); ?></th>
			<td><?php echo SetupResultIcon($dbStructResult); ?></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['inst_defaultcfg']; ?></th>
			<td><?php echo SetupResultIcon($defaultConfigResult); ?></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['inst_admin']; ?></th>
			<td><?php echo SetupResultIcon($adminAccountResult); ?></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['inst_defaultgroup']; ?></th>
			<td><?php echo SetupResultIcon($defaultGroupResult); ?></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['inst_postmaster']; ?></th>
			<td><?php echo SetupResultIcon($postmasterResult); ?></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['inst_exdata']; ?></th>
			<td><?php echo SetupResultIcon($exampleDataResult); ?></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['inst_config']; ?></th>
			<td><?php echo SetupResultIcon($configResult); ?></td>
		</tr>
	</table>
	</div>
	<?php
        SetupOpenCardBody();
        if ($installLog != '') {
            echo '<p>'.$lang_setup['log_text'].'</p>';
            echo '<textarea readonly="readonly" class="form-control setup-log" rows="8">'.SetupH($installLog).'</textarea>';
        } ?>
	<?php echo SetupAlert('warning', $lang_setup['remove_setup'], '', array('class' => 'mt-3')); ?>
	<?php if (!$lockOk) {
        echo SetupAlert('danger', $lang_setup['lock_failed']);
    } ?>
	<p><?php echo $lang_setup['finished_text']; ?></p>
	<dl class="setup-creds">
		<dt><?php echo SetupH($lang_setup['userlogin']); ?></dt>
		<dd><a target="_blank" href="<?php echo SetupH($url); ?>"><?php echo SetupH($url); ?></a></dd>
		<dt><?php echo SetupH($lang_setup['adminlogin']); ?></dt>
		<dd><a target="_blank" href="<?php echo SetupH($url); ?>admin/"><?php echo SetupH($url); ?>admin/</a></dd>
		<dt><?php echo SetupH($lang_setup['adminuser']); ?></dt>
		<dd><?php echo SetupH($adminUser); ?></dd>
		<dt><?php echo SetupH($lang_setup['adminpw']); ?></dt>
		<dd><?php echo SetupH($adminPlain); ?></dd>
	</dl>
	<?php
        unset($_SESSION['setup_data']);
    }
}

pageFooter();
