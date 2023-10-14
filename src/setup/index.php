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

// init
include './common.inc.php';

//  operations per call
define('CALL_OPS', 50);

// message flags (needed while updating)
define('FLAG_UNREAD', 1);
define('FLAG_ANSWERED', 2);
define('FLAG_FORWARDED', 4);
define('FLAG_DELETED', 8);
define('FLAG_FLAGGED', 16);
define('FLAG_SEEN', 32);
define('FLAG_ATTACHMENT', 64);
define('FLAG_INFECTED', 128);
define('FLAG_SPAM', 256);
define('FLAG_CERTMAIL', 512);

// folders (needed while updating)
define('FOLDER_INBOX', 0);
define('FOLDER_OUTBOX', -2);
define('FOLDER_DRAFTS', -3);
define('FOLDER_SPAM', -4);
define('FOLDER_TRASH', -5);
define('FOLDER_ROOT', -128);

// filter stuff (needed while updating)
define('BMOP_EQUAL', 1);
define('BMOP_NOTEQUAL', 2);
define('BMOP_CONTAINS', 3);
define('BMOP_NOTCONTAINS', 4);
define('BMOP_STARTSWITH', 5);
define('BMOP_ENDSWITH', 6);
define('MAILFIELD_SUBJECT', 1);
define('MAILFIELD_FROM', 2);
define('MAILFIELD_TO', 3);
define('MAILFIELD_CC', 4);
define('MAILFIELD_BCC', 5);
define('MAILFIELD_READ', 6);
define('MAILFIELD_ANSWERED', 7);
define('MAILFIELD_FORWARDED', 8);
define('MAILFIELD_PRIORITY', 9);
define('MAILFIELD_ATTACHMENT', 10);
define('MAILFIELD_FLAGGED', 11);
define('MAILFIELD_FOLDER', 12);
define('MAILFIELD_ATTACHLIST', 13);
define('FILTER_ACTION_MOVETO', 1);
define('FILTER_ACTION_BLOCK', 2);
define('FILTER_ACTION_DELETE', 3);
define('FILTER_ACTION_MARKREAD', 4);
define('FILTER_ACTION_MARKSPAM', 5);
define('FILTER_ACTION_MARK', 6);
define('FILTER_ACTION_STOP', 7);

// calendar stuff (needed while updating)
define('CLNDR_WHOLE_DAY', 1);
define('CLNDR_REMIND_EMAIL', 2);
define('CLNDR_REMIND_SMS', 4);
define('CLNDR_REPEATING_UNTIL_ENDLESS', 1);
define('CLNDR_REPEATING_UNTIL_COUNT', 2);
define('CLNDR_REPEATING_UNTIL_DATE', 4);
define('CLNDR_REPEATING_DAILY', 8);
define('CLNDR_REPEATING_WEEKLY', 16);
define('CLNDR_REPEATING_MONTHLY_MDAY', 32);
define('CLNDR_REPEATING_MONTHLY_WDAY', 64);

// steps
define('STEP_SELECT_LANGUAGE', 0);
define('STEP_WELCOME', 1);
define('STEP_SYSTEMCHECK', 3);
define('STEP_INSTALLTYPE', 4);

// fresh install
define('STEP_MYSQL', 5);
define('STEP_CHECK_MYSQL', 6);
define('STEP_CHECK_EMAIL', 7);
define('STEP_INSTALL', 8);

// update
define('STEP_UPDATE_MYSQL', 105);
define('STEP_UPDATE_CHECK_MYSQL', 106);
define('STEP_UPDATE_UPDATE', 107);
define('STEP_UPDATE_STEP', 108);
define('STEP_UPDATE_DONE', 109);

// other
define('DB_INSTALL_PREFIX', 'bm60_');

// target version
$target_version = '7.4.1-RC1';

// invoice
$defaultInvoice = file_get_contents("./rgtemplate.tpl");

// step?
if (!isset($_REQUEST['step'])) {
    $step = STEP_SELECT_LANGUAGE;
} else {
    $step = (int) $_REQUEST['step'];
}

// read language file
ReadLanguage();

// header
if ($step != STEP_UPDATE_STEP) {
    pageHeader();
}

if(file_exists(__DIR__."/lock")) {
    die("Lockfile detected. Please remove the file lock if you want rerun.");
}

/*
 * select language
 */
if ($step == STEP_SELECT_LANGUAGE) {
    $nextStep = STEP_WELCOME; ?>
	<h1><?php echo $lang_setup['selectlanguage']; ?></h1>

	<?php echo $lang_setup['selectlanguage_text']; ?>

	<blockquote>
		<input type="radio" id="lang_deutsch" name="lng" value="deutsch"<?php if ($lang == 'deutsch') {
        echo ' checked="checked"';
    } ?> />
			<label for="lang_deutsch">Deutsch</label>
		<br />
		<input type="radio" id="lang_english" name="lng" value="english"<?php if ($lang == 'english') {
        echo ' checked="checked"';
    } ?> />
			<label for="lang_english">English</label>
	</blockquote>
	<?php
}

/*
 * welcome / license
 */
elseif ($step == STEP_WELCOME) {
    $nextStep = STEP_SYSTEMCHECK; ?>
	<h1><?php echo $lang_setup['welcome']; ?></h1>

	<?php echo $lang_setup['welcome_text']; ?>
	<?php
}

/*
 * system check
 */
elseif ($step == STEP_SYSTEMCHECK) {
    $nextStep = STEP_MYSQL; ?>
	<h1><?php echo $lang_setup['syscheck']; ?></h1>

	<?php echo $lang_setup['syscheck_text']; ?>

	<br /><br />
	<table class="list">
		<tr>
			<th width="180">&nbsp;</th>
			<th><?php echo $lang_setup['required']; ?></th>
			<th><?php echo $lang_setup['available']; ?></th>
			<th width="60">&nbsp;</th>
		</tr>
		<tr>
			<th><?php echo $lang_setup['phpversion']; ?></th>
			<td>5.4.0</td>
			<td><?php echo phpversion(); ?></td>
			<td><img src="../admin/templates/images/<?php if ((int) str_replace('.', '', phpversion()) >= 540) {
        echo 'ok';
    } else {
        echo 'error';
        $nextStep = STEP_SYSTEMCHECK;
    } ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['mysqlext']; ?></th>
			<td><?php echo $lang_setup['yes']; ?></td>
			<td><?php echo function_exists('mysqli_connect') ? $lang_setup['yes'] : $lang_setup['no']; ?></td>
			<td><img src="../admin/templates/images/<?php if (function_exists('mysqli_connect')) {
        echo 'ok';
    } else {
        echo 'error';
        $nextStep = STEP_SYSTEMCHECK;
    } ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
		<?php
        $chmodCommands = [];
    foreach ($writeableFiles as $file) {
        if (!is_writeable('../'.$file)) {
            $chmodMode = is_dir('../'.$file) ? '0777' : '0666';
            if (!isset($chmodCommands[$chmodMode])) {
                $chmodCommands[$chmodMode] = [];
            }
            $chmodCommands[$chmodMode][] = $file;
        } ?>
		<tr>
			<th><?php echo $file; ?></th>
			<td><?php echo $lang_setup['writeable']; ?></td>
			<td><?php echo is_writeable('../'.$file) ? $lang_setup['writeable'] : $lang_setup['notwriteable']; ?></td>
			<td><img src="../admin/templates/images/<?php if (is_writeable('../'.$file)) {
            echo 'ok';
        } else {
            echo 'error';
            $nextStep = STEP_SYSTEMCHECK;
        } ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
			<?php
    } ?>
	</table>

	<?php
    if ($nextStep != STEP_MYSQL) {
        ?>
	<br />
	<div style="text-align:center;display:;" id="chmodCommandsNote">
		<a href="#" onclick="document.getElementById('chmodCommandsNote').style.display='none';document.getElementById('chmodCommands').style.display='';"><?php echo $lang_setup['showchmod']; ?></a>
	</div>
	<div style="display:none;" id="chmodCommands">
		<textarea readonly="readonly" class="installLog" style="height:120px;"><?php
            foreach ($chmodCommands as $mode => $files) {
                echo 'chmod '.$mode;
                foreach ($files as $file) {
                    echo " \\\n\t".$file;
                }
                echo "\n";
            } ?></textarea>
	</div>
		<?php
    } ?>

	<br />
	<?php echo $nextStep == STEP_MYSQL ? $lang_setup['checkok_text'] : $lang_setup['checkfail_text']; ?>
	<?php
}

//
//
//				FRESH INSTALL
//
//

/*
 * mysql login
 */
elseif ($step == STEP_MYSQL) {
    $nextStep = STEP_CHECK_MYSQL; ?>
	<h1><?php echo $lang_setup['db']; ?></h1>

	<?php echo $lang_setup['dbfresh_text']; ?>

	<blockquote>
		<label for="mysql_host"><?php echo $lang_setup['mysql_host']; ?></label><br />
			<input id="mysql_host" name="mysql_host" type="text" value="" size="32" />
		<br /><br />
		<label for="mysql_user"><?php echo $lang_setup['mysql_user']; ?></label><br />
			<input id="mysql_user" name="mysql_user" type="text" value="" size="32" />
		<br /><br />
		<label for="mysql_pass"><?php echo $lang_setup['mysql_pass']; ?></label><br />
			<input id="mysql_pass" name="mysql_pass" type="text" value="" size="32" />
		<br /><br />
		<label for="mysql_db"><?php echo $lang_setup['mysql_db']; ?></label><br />
			<input id="mysql_db" name="mysql_db" type="text" value="" size="32" />
	</blockquote>
	<?php
}

/*
 * check mysql login
 */
elseif ($step == STEP_CHECK_MYSQL) {
    if ($connection = CheckMySQLLogin($_REQUEST['mysql_host'], $_REQUEST['mysql_user'], $_REQUEST['mysql_pass'],
                        $_REQUEST['mysql_db'])) {
        // b1gMail already installed here?
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
            $nextStep = STEP_MYSQL; ?>
			<h1><?php echo $lang_setup['db']; ?></h1>

			<?php echo $lang_setup['dbexists_text']; ?>
			<?php
        } else {
            $nextStep = STEP_CHECK_EMAIL; ?>
			<h1><?php echo $lang_setup['emailcfg']; ?></h1>

			<?php echo $lang_setup['emailcfg_text']; ?>

			<br /><br />
			<fieldset>
				<legend><?php echo $lang_setup['setupmode']; ?></legend>

				<?php echo $lang_setup['mode_note']; ?><br /><br />

				<input type="radio" id="setup_mode_public" name="setup_mode" value="public" checked="checked" />
				<label for="setup_mode_public"><?php echo $lang_setup['mode_public']; ?></label><br />
				<blockquote>
					<?php echo $lang_setup['mode_public_desc']; ?><br />
				</blockquote>

				<input type="radio" id="setup_mode_private" name="setup_mode" value="private" />
				<label for="setup_mode_private"><?php echo $lang_setup['mode_private']; ?></label>
				<blockquote>
					<?php echo $lang_setup['mode_private_desc']; ?><br />
				</blockquote>
			</fieldset>

			<br />
			<fieldset>
				<legend><?php echo $lang_setup['receiving']; ?></legend>

				<input type="radio" id="receive_method_pop3" name="receive_method" value="pop3" checked="checked" />
				<label for="receive_method_pop3"><?php echo $lang_setup['pop3gateway']; ?></label>
				<blockquote>
					<label for="pop3_host"><?php echo $lang_setup['pop3_host']; ?></label><br />
						<input id="pop3_host" name="pop3_host" type="text" value="" size="32" />
					<br /><br />
					<label for="pop3_user"><?php echo $lang_setup['pop3_user']; ?></label><br />
						<input id="pop3_user" name="pop3_user" type="text" value="" size="32" />
					<br /><br />
					<label for="pop3_pass"><?php echo $lang_setup['pop3_pass']; ?></label><br />
						<input id="pop3_pass" name="pop3_pass" type="text" value="" size="32" />
				</blockquote>

				<input type="radio" id="receive_method_pipe" name="receive_method" value="pipe" />
				<label for="receive_method_pipe"><?php echo $lang_setup['pipe']; ?></label>
			</fieldset>

			<br />
			<fieldset>
				<legend><?php echo $lang_setup['sending']; ?></legend>

				<input type="radio" id="send_method_phpmail" name="send_method" value="php" checked="checked" />
				<label for="send_method_phpmail"><?php echo $lang_setup['phpmail']; ?></label><br />

				<input type="radio" id="send_method_smtp" name="send_method" value="smtp" />
				<label for="send_method_smtp"><?php echo $lang_setup['smtp']; ?></label>
				<blockquote>
					<label for="smtp_host"><?php echo $lang_setup['smtp_host']; ?></label><br />
						<input id="smtp_host" name="smtp_host" type="text" value="localhost" size="32" />
				</blockquote>

				<input type="radio" id="send_method_sendmail" name="send_method" value="sendmail" />
				<label for="send_method_sendmail"><?php echo $lang_setup['sendmail']; ?></label>
				<blockquote>
					<label for="sendmail_path"><?php echo $lang_setup['sendmail_path']; ?></label><br />
						<input id="sendmail_path" name="sendmail_path" type="text" value="/usr/sbin/sendmail" size="32" />
				</blockquote>
			</fieldset>

			<input type="hidden" name="mysql_host" value="<?php echo htmlentities($_REQUEST['mysql_host']); ?>" />
			<input type="hidden" name="mysql_user" value="<?php echo htmlentities($_REQUEST['mysql_user']); ?>" />
			<input type="hidden" name="mysql_pass" value="<?php echo htmlentities($_REQUEST['mysql_pass']); ?>" />
			<input type="hidden" name="mysql_db" value="<?php echo htmlentities($_REQUEST['mysql_db']); ?>" />
			<?php
        }
    } else {
        $nextStep = STEP_MYSQL; ?>
		<h1><?php echo $lang_setup['db']; ?></h1>

		<?php echo $lang_setup['dbfail_text']; ?>
		<?php
    }
}

/*
 * check email config
 */
elseif ($step == STEP_CHECK_EMAIL) {
    if ($_REQUEST['receive_method'] != 'pop3'
        || CheckPOP3Login($_REQUEST['pop3_host'], $_REQUEST['pop3_user'], $_REQUEST['pop3_pass'])) {
        if ($_REQUEST['send_method'] != 'sendmail'
            || (file_exists($_REQUEST['sendmail_path']) && is_executable($_REQUEST['sendmail_path']))) {
            $nextStep = STEP_INSTALL; ?>
			<h1><?php echo $lang_setup['misc']; ?></h1>

			<?php echo $lang_setup['misc_text']; ?>

			<blockquote>
				<label for="adminpw"><?php echo $lang_setup['adminpw']; ?></label><br />
					<input id="adminpw" name="adminpw" type="text" value="<?php echo GeneratePW(); ?>" size="32" />
				<br /><br />
				<label for="domains"><?php echo $lang_setup['domains']; ?></label><br />
					<textarea id="domains" name="domains" style="width:220px;height:80px;">example.com
example.net
example.org</textarea>
				<br /><br />
				<label for="url"><?php echo $lang_setup['url']; ?></label><br />
					<input id="url" name="url" type="text" value="http://<?php echo $_SERVER['HTTP_HOST'].preg_replace('/\/setup\/index\.php(.*)/', '/', $_SERVER['REQUEST_URI']); ?>" size="32" />
			</blockquote>

			<input type="hidden" name="setup_mode" value="<?php echo htmlentities($_REQUEST['setup_mode']); ?>" />
			<input type="hidden" name="receive_method" value="<?php echo htmlentities($_REQUEST['receive_method']); ?>" />
			<input type="hidden" name="send_method" value="<?php echo htmlentities($_REQUEST['send_method']); ?>" />
			<input type="hidden" name="pop3_host" value="<?php echo htmlentities($_REQUEST['pop3_host']); ?>" />
			<input type="hidden" name="pop3_user" value="<?php echo htmlentities($_REQUEST['pop3_user']); ?>" />
			<input type="hidden" name="pop3_pass" value="<?php echo htmlentities($_REQUEST['pop3_pass']); ?>" />
			<input type="hidden" name="smtp_host" value="<?php echo htmlentities($_REQUEST['smtp_host']); ?>" />
			<input type="hidden" name="sendmail_path" value="<?php echo htmlentities($_REQUEST['sendmail_path']); ?>" />
			<?php
        } else {
            $nextStep = STEP_CHECK_MYSQL; ?>
			<h1><?php echo $lang_setup['emailcfg']; ?></h1>

			<?php echo $lang_setup['emailcfgsmfail_text']; ?>
			<?php
        }
    } else {
        $nextStep = STEP_CHECK_MYSQL; ?>
		<h1><?php echo $lang_setup['emailcfg']; ?></h1>

		<?php echo $lang_setup['emailcfgpop3fail_text']; ?>
		<?php
    } ?>
	<input type="hidden" name="mysql_host" value="<?php echo htmlentities($_REQUEST['mysql_host']); ?>" />
	<input type="hidden" name="mysql_user" value="<?php echo htmlentities($_REQUEST['mysql_user']); ?>" />
	<input type="hidden" name="mysql_pass" value="<?php echo htmlentities($_REQUEST['mysql_pass']); ?>" />
	<input type="hidden" name="mysql_db" value="<?php echo htmlentities($_REQUEST['mysql_db']); ?>" />
	<?php
}

/*
 * install!
 */
elseif ($step == STEP_INSTALL) {
    include '../serverlib/database.struct.php';
    include './data/rootcerts.data.php';

    // prepare structure
    $databaseStructure = json_decode($databaseStructure, JSON_OBJECT_AS_ARRAY);

    // sanitize input
    if (substr($_REQUEST['url'], -1) != '/') {
        $_REQUEST['url'] .= '/';
    }
    $domains = explode("\n", str_replace(["\r", '@', ',', ';', ':', ' '], '', $_REQUEST['domains']));
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

    // start installation log
    ob_start();

    // status
    $dbStructResult = 'error';
    $defaultConfigResut = 'error';
    $adminAccountResult = 'error';
    $defaultGroupResut = 'error';
    $exampleDataResult = 'error';
    $postmasterResut = 'error';
    $configResult = 'error';

    // install
    $connection = mysqli_connect($_REQUEST['mysql_host'], $_REQUEST['mysql_user'], $_REQUEST['mysql_pass'], $_REQUEST['mysql_db']);
    if ($connection) {
        if (mysqli_select_db($connection, $_REQUEST['mysql_db'])) {
            // install routine uses utf8mb4
            mysqli_set_charset($connection, 'utf8mb4');

            // disable strict mode
            @mysqli_query($connection, 'SET SESSION sql_mode=\'\'');

            // get MySQL version
            $result = mysqli_query($connection, 'SELECT VERSION()');
            list($mysqlVersion) = mysqli_fetch_array($result, MYSQLI_NUM);
            mysqli_free_result($result);

            // setup mode?
            $setupMode = $_REQUEST['setup_mode'];
            if (!in_array($setupMode, ['public', 'private'])) {
                $setupMode = 'public';
            }

            // install in utf-8 mode?
            $utf8Mode = true;

            // create db structure
            $dbStructResult = 'ok';
            $result = CreateDatabaseStructure($connection, $databaseStructure, $utf8Mode, $_REQUEST['mysql_db']);
            foreach ($result as $query => $queryResult) {
                if ($queryResult !== true) {
                    $dbStructResult = 'warning';
                    echo 'Failed to execute structure query: '.$queryResult."\n";
                }
            }

            // create default config
            $blobDBSupport = class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', PDO::getAvailableDrivers()));
            $gzSupport = function_exists('gzcompress') && function_exists('gzuncompress');
            $hostName = @gethostbyname($_SERVER['SERVER_ADDR']);
            if (!$hostName || trim($hostName) == '' || $hostName == $_SERVER['SERVER_ADDR']) {
                $hostName = $_SERVER['HTTP_HOST'];
            }
            $dataFolder = preg_replace('/\/setup\/index\.php(.*)/', '/data/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']));
            $selfFolder = preg_replace('/\/setup\/index\.php(.*)/', '/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']));
            $prefsQuery = sprintf('INSERT INTO '.DB_INSTALL_PREFIX.'prefs(template,language,selfurl,mobile_url,send_method,smtp_host,sendmail_path,receive_method,pop3_host,pop3_user,pop3_pass,passmail_abs,titel,datafolder,selffolder,b1gmta_host,dnsbl,signup_dnsbl,smsreply_abs,widget_order_start,widget_order_organizer,structstorage,search_in,db_is_utf8,rgtemplate,pay_emailfrom,pay_emailfromemail,regenabled,contactform_to,ap_autolock_notify_to,blobstorage_provider,blobstorage_provider_webdisk,blobstorage_compress,blobstorage_webdisk_compress) '
                            .'VALUES(\'modern\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',%d,\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',%d,%d,\'%s\',\'%s\')',
                            $_REQUEST['lng'] == 'deutsch' ? 'deutsch' : 'english',
                            SQLEscape($_REQUEST['url'], $connection),
                            SQLEscape($_REQUEST['url'].'m/', $connection),
                            SQLEscape($_REQUEST['send_method'], $connection),
                            SQLEscape($_REQUEST['smtp_host'], $connection),
                            SQLEscape($_REQUEST['sendmail_path'], $connection),
                            SQLEscape($_REQUEST['receive_method'], $connection),
                            SQLEscape($_REQUEST['pop3_host'], $connection),
                            SQLEscape($_REQUEST['pop3_user'], $connection),
                            SQLEscape($_REQUEST['pop3_pass'], $connection),
                            SQLEscape('"Postmaster '.$firstDomain.'" <postmaster@'.EncodeDomain($firstDomain).'>', $connection),
                            SQLEscape($firstDomain.' Mail', $connection),
                            SQLEscape($dataFolder, $connection),
                            SQLEscape($selfFolder, $connection),
                            $hostName,
                            'ix.dnsbl.manitu.net:zen.spamhaus.org',
                            'dnsbl.tornevall.org',
                            SQLEscape('postmaster@'.EncodeDomain($firstDomain), $connection),
                            'BMPlugin_Widget_Welcome,BMPlugin_Widget_EMail,BMPlugin_Widget_Websearch;BMPlugin_Widget_Mailspace,,BMPlugin_Widget_Quicklinks;BMPlugin_Widget_Webdiskspace,,',
                            'BMPlugin_Widget_Websearch,BMPlugin_Widget_Calendar,BMPlugin_Widget_Notes;,BMPlugin_Widget_Tasks,',
                            'yes',
                            SQLEscape('a:8:{s:5:"mails";s:2:"on";s:11:"attachments";s:2:"on";s:3:"sms";s:2:"on";s:8:"calendar";s:2:"on";s:5:"tasks";s:2:"on";s:11:"addressbook";s:2:"on";s:5:"notes";s:2:"on";s:7:"webdisk";s:2:"on";}', $connection),
                            $utf8Mode ? 1 : 0,
                            $defaultInvoice,
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
                $defaultConfigResut = 'ok';
            } else {
                echo 'Failed to create default config: '.mysqli_error($connection)."\n";
            }

            // create admin account
            $adminSalt = GeneratePW();
            $adminPW = md5($_REQUEST['adminpw'].$adminSalt);
            if (mysqli_query($connection, sprintf('REPLACE INTO '.DB_INSTALL_PREFIX.'admins(`adminid`,`username`,`firstname`,`lastname`,`password`,`password_salt`,`type`,`notes`) VALUES '
                            .'(1,\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',0,\'\')',
                            'admin',
                            'Super',
                            'Administrator',
                            $adminPW,
                            SQLEscape($adminSalt, $connection)))) {
                $adminAccountResult = 'ok';
            } else {
                echo 'Failed to create admin account: '.mysqli_error($connection)."\n";
            }

            // create default group
            $groupQuery = sprintf('INSERT INTO '.DB_INSTALL_PREFIX.'gruppen(id,titel,ftsearch) VALUES(1,\'%s\',\'%s\')',
                SQLEscape($lang_setup['defaultgroup'], $connection),
                $blobDBSupport ? 'yes' : 'no');
            if (mysqli_query($connection, $groupQuery)) {
                $defaultGroupResut = 'ok';
            } else {
                echo 'Failed to create default group: '.mysqli_error($connection)."\n";
            }

            // create domains
            $domainPos = 0;
            foreach ($domains as $domain) {
                $domainQuery = sprintf('INSERT INTO '.DB_INSTALL_PREFIX.'domains(`domain`,`pos`) VALUES(\'%s\',%d)',
                    SQLEscape(EncodeDomain($domain), $connection),
                    $domainPos += 10);
                mysqli_query($connection, $domainQuery);
            }

            // create postmaster
            $salt = GeneratePW();
            $postmasterQuery = sprintf('INSERT INTO '.DB_INSTALL_PREFIX.'users(id,email,vorname,nachname,passwort,passwort_salt,gruppe,preview,plaintext_courier,soforthtml) '
                                .'VALUES(1,\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',1,\'yes\',\'yes\',\'yes\')',
                                SQLEscape('postmaster@'.EncodeDomain($firstDomain), $connection),
                                'Postmaster',
                                SQLEscape($firstDomain, $connection),
                                md5(md5($_REQUEST['adminpw']).$salt),
                                SQLEscape($salt, $connection));
            if (mysqli_query($connection, $postmasterQuery)) {
                $postmasterResut = 'ok';
            } else {
                echo 'Failed to create postmaster: '.mysqli_error($connection)."\n";
            }

            // create postmaster aliases
            foreach ($domains as $domain) {
                if ($domain != $firstDomain) {
                    $aliasQuery = sprintf('INSERT INTO '.DB_INSTALL_PREFIX.'aliase(email,user) VALUES(\'%s\',1)',
                        SQLEscape('postmaster@'.EncodeDomain($domain), $connection));
                    mysqli_query($connection, $aliasQuery);
                }
            }

            // install default data
            $exampleDataResult = 'ok';
            foreach ($exampleData as $query) {
                if (defined('DB_INSTALL_PREFIX') && DB_INSTALL_PREFIX != 'bm60_') {
                    $query = str_replace('bm60_', DB_INSTALL_PREFIX, $query);
                }
                if (!mysqli_query($connection, $query)) {
                    echo 'Failed to execute example data query: '.mysqli_error($connection)."\n";
                    $exampleDataResult = 'warning';
                }
            }

            // install default root certs
            foreach ($rootCertsData as $query) {
                if (defined('DB_INSTALL_PREFIX') && DB_INSTALL_PREFIX != 'bm60_') {
                    $query = str_replace('bm60_', DB_INSTALL_PREFIX, $query);
                }
                if (!mysqli_query($connection, $query)) {
                    echo 'Failed to execute root cert insert query: '.mysqli_error($connection)."\n";
                    $exampleDataResult = 'warning';
                }
            }

            // remove outdated root certificates
            mysqli_query($connection, 'DELETE FROM '.DB_INSTALL_PREFIX.'certificates WHERE `type`=0 AND `userid`=0 AND `validto`<'.time());

            // install template prefs
            if ($setupMode == 'private') {
                mysqli_query($connection, 'INSERT INTO '.DB_INSTALL_PREFIX.'templateprefs(`template`,`key`,`value`) VALUES(\'modern\',\'hideSignup\',\'1\')');
            }

            // create sign key
            if (function_exists('random_bytes')) {
                $signKey = str_pad(bin2hex(random_bytes(16)), 32, '0', STR_PAD_LEFT);
            } elseif (function_exists('openssl_random_pseudo_bytes')) {
                $signKey = str_pad(bin2hex(openssl_random_pseudo_bytes(16)), 32, 0, STR_PAD_LEFT);
            } else {
                $signKey = md5(microtime().mt_rand(0, PHP_INT_MAX));
            }
            // Create version file
            $fp = fopen('../serverlib/version.inc.php', 'w');
            fwrite($fp, sprintf('<?php define(\'B1GMAIL_VERSION\', $b1gmail_version = \'%s\'); ?>', $target_version));
            fclose($fp);

            // create config file
            $configFile = sprintf("<?php\n// Generated %s\n\$mysql = array(\n\t'host'\t\t=> '%s',\n\t'user'\t\t=> '%s',\n\t'pass'\t\t=> '%s',\n\t'db'\t\t=> '%s',\n\t'prefix'\t=> '%s'\n);\ndefine('B1GMAIL_SIGNKEY', '%s');\ndefine('DB_CHARSET', '%s');\n?>",
                date('r'),
                addslashes($_REQUEST['mysql_host']),
                addslashes($_REQUEST['mysql_user']),
                addslashes($_REQUEST['mysql_pass']),
                addslashes($_REQUEST['mysql_db']),
                DB_INSTALL_PREFIX,
                $signKey,
                'utf8mb4');
            $fp = fopen('../serverlib/config.inc.php', 'w');
            if ($fp) {
                fwrite($fp, $configFile);
                fclose($fp);

                $configResult = 'ok';
            } else {
                echo 'Failed to open config.inc.php for writing'."\n";
            }
        } else {
            echo 'MySQL database selection failed'."\n";
        }
    } else {
        echo 'MySQL connection failed'."\n";
    }

    // finish installation log
    $installLog = trim(strip_tags(ob_get_contents()));
    ob_end_clean(); ?>
	<h1><?php echo $lang_setup['installing']; ?></h1>

	<?php echo $lang_setup['installing_text']; ?>

	<br /><br />
	<table class="list">
		<tr>
			<th><?php echo sprintf($lang_setup['inst_dbstruct'], $databaseStructureVersion); ?></th>
			<td><img src="../admin/templates/images/<?php echo $dbStructResult; ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['inst_defaultcfg']; ?></th>
			<td><img src="../admin/templates/images/<?php echo $defaultConfigResut; ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['inst_admin']; ?></th>
			<td><img src="../admin/templates/images/<?php echo $adminAccountResult; ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['inst_defaultgroup']; ?></th>
			<td><img src="../admin/templates/images/<?php echo $defaultGroupResut; ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['inst_postmaster']; ?></th>
			<td><img src="../admin/templates/images/<?php echo $postmasterResut; ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['inst_exdata']; ?></th>
			<td><img src="../admin/templates/images/<?php echo $exampleDataResult; ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
		<tr>
			<th><?php echo $lang_setup['inst_config']; ?></th>
			<td><img src="../admin/templates/images/<?php echo $configResult; ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
	</table>
	<br />

	<?php
    if ($installLog != '') {
        echo $lang_setup['log_text']; ?>
		<textarea readonly="readonly" class="installLog"><?php echo htmlentities($installLog); ?></textarea>
		<br /><br />
		<?php
    }

    echo $lang_setup['finished_text']; ?>
	<blockquote>
		<b><?php echo $lang_setup['userlogin']; ?></b><br />
		<a target="_blank" href="<?php echo $_REQUEST['url']; ?>"><?php echo $_REQUEST['url']; ?></a><br /><br />

		<b><?php echo $lang_setup['adminlogin']; ?></b><br />
		<a target="_blank" href="<?php echo $_REQUEST['url']; ?>admin/"><?php echo $_REQUEST['url']; ?>admin/</a><br /><br />

		<b><?php echo $lang_setup['adminuser']; ?></b><br />
		admin<br /><br />

		<b><?php echo $lang_setup['adminpw']; ?></b><br />
		<?php echo htmlentities($_REQUEST['adminpw']); ?>
	</blockquote>
	<?php
    if(is_writable('./'))
	{
		$lock = @fopen('./lock', 'w');
		$written = @fwrite($lock, '1');
		@fclose($lock);
	}
}

// footer
pageFooter();
?>