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
require './common.inc.php';
require '../serverlib/config.inc.php';
require '../serverlib/version.inc.php';

if(file_exists(__DIR__."/lock_update")) {
    die("Lockfile detected. Please remove the file lock_update if you want rerun.");
}

// target version
$target_version = '7.4.1-RC1';

// known versions
$knownVersions = ['7.0.0-Beta1', '7.0.0-Beta2', '7.0.0-Beta3', '7.0.0-RC1', '7.0.0',
                        '7.1.0',
                        '7.2.0-Beta1', '7.2.0-Beta2', '7.2.0-Beta3', '7.2.0',
                        '7.3.0-Beta1', '7.3.0-Beta2', '7.3.0-Beta3', '7.3.0-Beta4',
                        '7.3.0-Beta5', '7.3.0-Beta6', '7.3.0',
                        '7.4.0-Beta1', '7.4.0-Beta2', '7.4.0-Beta3', '7.4.0-Beta4', '7.4.0', 
                        '7.4.1-Beta1', '7.4.1-Beta2', '7.4.1-Beta3', '7.4.1-Beta4'];

// steps
define('STEP_SELECT_LANGUAGE', 0);
define('STEP_WELCOME', 1);
define('STEP_SYSTEMCHECK', 2);
define('STEP_UPDATE', 3);
define('STEP_UPDATE_STEP', 4);

// invoice
$defaultInvoice = file_get_contents("./rgtemplate.tpl");

// connect to mysql db
if (!($connection = CheckMySQLLogin($mysql['host'], $mysql['user'], $mysql['pass'],
                    $mysql['db']))) {
    die('ERROR:MySQL connection failed');
}

// read prefs
$result = mysqli_query($connection, 'SELECT * FROM '.$mysql['prefix'].'prefs LIMIT 1');
$bm_prefs = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);

// convert current version to int
if (strpos($b1gmail_version, '-Beta') !== false) {
    $numVersion = str_replace('-Beta', '.', $b1gmail_version);
} else {
    $numVersion = $b1gmail_version.'.9';
}
$numVersion = (int) str_replace('.', '', $numVersion);

// step?
if (!isset($_REQUEST['step'])) {
    $step = STEP_WELCOME;
} else {
    $step = (int) $_REQUEST['step'];
}

// read language file
if (!isset($_GET['lng'])) {
    $_GET['lng'] = strpos($bm_prefs['language'], 'deutsch') !== false ? 'deutsch' : 'english';
}
ReadLanguage();

// header
if ($step != STEP_UPDATE_STEP) {
    pageHeader(true);
}

/*
 * welcome
 */
if ($step == STEP_WELCOME
    || ($b1gmail_version == $target_version)
    || !in_array($b1gmail_version, $knownVersions)) {
    if ($b1gmail_version == $target_version) {
        ?>
		<h1><?php echo $lang_setup['error']; ?></h1>

		<?php echo sprintf($lang_setup['uptodate'], $target_version); ?>
		<?php
    } elseif (!in_array($b1gmail_version, $knownVersions)) {
        ?>
		<h1><?php echo $lang_setup['error']; ?></h1>

		<?php echo sprintf($lang_setup['unknownversion'], $b1gmail_version, $target_version); ?>
		<?php
    } else {
        $nextStep = STEP_SYSTEMCHECK; ?>
		<h1><?php echo $lang_setup['welcome']; ?></h1>

		<?php echo sprintf($lang_setup['update_welcome_text'], $b1gmail_version, $target_version); ?>
		<?php
    }
}

/*
 * system check
 */
elseif ($step == STEP_SYSTEMCHECK) {
    $nextStep = STEP_UPDATE; ?>
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
        if ($numVersion <= 7309) {
            $res = mysqli_query($connection, 'SELECT COUNT(*) FROM '.$mysql['prefix'].'mails WHERE LENGTH(`body`)!=4');
            list($dbMails) = mysqli_fetch_array($res, MYSQLI_NUM);
            mysqli_free_result($res); ?>
		<tr>
			<th><?php echo $lang_setup['dbmails']; ?></th>
			<td>0</td>
			<td><?php echo $dbMails; ?></td>
			<td><img src="../admin/templates/images/<?php if ($dbMails == 0) {
                echo 'ok';
            } else {
                echo 'error';
                $nextStep = STEP_SYSTEMCHECK;
            } ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
			<?php

            $showDbMailsNote = ($dbMails != 0);
        }

    foreach ($writeableFiles as $file) {
        ?>
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
    if (!empty($showDbMailsNote)) {
        ?>
		<br />
		<p>
			<font color="red"><?php echo $lang_setup['dbmails_note']; ?></font>
		</p>
		<?php
    } ?>

	<br />
	<?php echo $nextStep == STEP_UPDATE ? $lang_setup['checkok_text'] : $lang_setup['checkfail_text']; ?>
	<?php
}

/*
 * update
 */
elseif ($step == STEP_UPDATE) {
    ?>
	<h1><?php echo $lang_setup['updating']; ?></h1>

	<?php echo $lang_setup['updating_text']; ?>

	<br /><br />
	<table class="list">
		<tr>
			<th width="40"></th>
			<th><?php echo $lang_setup['step']; ?></th>
			<th width="180"><?php echo $lang_setup['progress']; ?></th>
		</tr>
		<tr>
			<td id="step_prepare_status">&nbsp;</td>
			<th id="step_prepare_text" style="font-weight:normal;">1. <?php echo $lang_setup['update_prepare']; ?></th>
			<td id="step_prepare_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_struct2_status">&nbsp;</td>
			<th id="step_struct2_text" style="font-weight:normal;">1. <?php echo $lang_setup['update_struct2']; ?></th>
			<td id="step_struct2_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_config_status">&nbsp;</td>
			<th id="step_config_text" style="font-weight:normal;">2. <?php echo $lang_setup['update_config']; ?></th>
			<td id="step_config_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_struct3_status">&nbsp;</td>
			<th id="step_struct3_text" style="font-weight:normal;">3. <?php echo $lang_setup['update_struct3']; ?></th>
			<td id="step_struct3_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_resetcache_status">&nbsp;</td>
			<th id="step_resetcache_text" style="font-weight:normal;">4. <?php echo $lang_setup['update_resetcache']; ?></th>
			<td id="step_resetcache_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_optimize_status">&nbsp;</td>
			<th id="step_optimize_text" style="font-weight:normal;">5. <?php echo $lang_setup['update_optimize']; ?></th>
			<td id="step_optimize_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_complete_status">&nbsp;</td>
			<th id="step_complete_text" style="font-weight:normal;">6. <?php echo $lang_setup['update_complete']; ?></th>
			<td id="step_complete_progress">&nbsp;</td>
		</tr>
	</table>

	<br />
	<?php echo $lang_setup['updating_text2']; ?>

	<textarea readonly="readonly" class="installLog" id="log" style="display:none;height:150px;"></textarea>
	<br /><br />

	<div align="center" id="done" style="display:none;">
		<b><?php echo $lang_setup['updatedonefinal']; ?></b>
        <?php if (empty($bm_prefs['db_is_utf8'])) { 
            echo '<br /><br />'.$lang_setup['dbnotconverted'];
        }?>
	</div>

	<script src="./res/update2.js"></script>
	<script>
	<!--
		window.onload = beginUpdate;
	//-->
	</script>

	<?php
}

/*
 * update step
 */
elseif ($step == STEP_UPDATE_STEP) {
    $do = $_REQUEST['do'];
    $pos = isset($_REQUEST['pos']) ? (int) $_REQUEST['pos'] : 0;

    //
    // preparation
    //
    if ($do == 'prepare') {
        mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'prefs SET wartung=\'yes\'');
        echo 'OK:DONE';
    }

    //
    // db structure sync
    //
    elseif ($do == 'struct2') {
        include '../serverlib/database.struct.php';

        $databaseStructure = json_decode($databaseStructure);
        $queries = SyncDBStruct($connection, $databaseStructure, true, isset($bm_prefs['db_is_utf8']) && $bm_prefs['db_is_utf8'] == 1);

        if (count($queries) == 0) {
            echo 'OK:DONE';
        } else {
            $done = true;
            foreach ($queries as $query) {
                if (mysqli_query($connection, $query)) {
                    $done = false;
                    break;
                }
            }

            if ($done) {
                echo 'OK:DONE';
            } else {
                echo 'OK:'.(++$pos).'/'.count($queries);
            }
        }
    }

    //
    // config
    //
    elseif ($do == 'config') {
        if ($numVersion <= 7001) {
            if (isset($bm_prefs['sms_user']) && isset($bm_prefs['sms_pass'])) {
                mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'smsgateways SET `user`=\'%s\',`pass`=\'%s\'',
                    SQLEscape($bm_prefs['sms_user'], $connection),
                    SQLEscape($bm_prefs['sms_pass'], $connection)));
            }
        }

        if ($numVersion <= 7100) {
            $res = mysqli_query($connection, 'SELECT COUNT(*) FROM '.$mysql['prefix'].'certificates');
            list($certCount) = mysqli_fetch_array($res, MYSQLI_NUM);
            mysqli_free_result($res);

            if ($certCount == 0) {
                include './data/rootcerts.data.php';

                foreach ($rootCertsData as $query) {
                    mysqli_query($connection, $query);
                }
            }
        }

        if ($numVersion <= 7201
           && isset($bm_prefs['reg_smsvalidation'])) {
            mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'prefs SET `reg_validation`=\'%s\'',
                        $bm_prefs['reg_smsvalidation'] == 'yes' ? 'sms' : 'off'));
        }

        if ($numVersion < 7202) {
            mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'prefs SET `selffolder`=\'%s\',`enable_paypal`=\'%s\',`paypal_mail`=\'%s\',`sms_enable_charge`=\'%s\',`rgtemplate`=\'%s\'',
                                SQLEscape(str_replace('/data/', '/', $bm_prefs['datafolder']), $connection),
                                $bm_prefs['sms_enable_paypal'],
                                SQLEscape($bm_prefs['sms_paypal_mail'], $connection),
                                $bm_prefs['sms_enable_paypal'] == 'yes' || $bm_prefs['sms_enable_su'] == 'yes' ? 'yes' : 'no',
                                $defaultInvoice));
            mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'gruppen SET `max_recps`=%d',
                                $bm_prefs['max_bcc']));

            if (isset($bm_prefs['sms_enable_su'])) {
                mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'prefs SET `enable_su`=\'%s\',`su_kdnr`=\'%s\',`su_prjnr`=\'%s\',`su_prjpass`=\'%s\'',
                                    $bm_prefs['sms_enable_su'],
                                    SQLEscape($bm_prefs['sms_su_kdnr'], $connection),
                                    SQLEscape($bm_prefs['sms_su_prjnr'], $connection),
                                    SQLEscape($bm_prefs['sms_su_prjpass'], $connection)));
            }

            // check for plugins
            $havePAcc = $haveTabOrder = false;
            $res = mysqli_query($connection, 'SHOW TABLES');
            while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
                if ($row[0] == $mysql['prefix'].'mod_premium_prefs') {
                    $havePAcc = true;
                } elseif ($row[0] == $mysql['prefix'].'mod_taborder') {
                    $haveTabOrder = true;
                }
            }
            mysqli_free_result($res);

            // transfer tab order
            if ($haveTabOrder) {
                $tabOrder = [];

                $res = mysqli_query($connection, 'SELECT * FROM '.$mysql['prefix'].'mod_taborder');
                while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                    $tabOrder[$row['key']] = $row['order'];
                }
                mysqli_free_result($res);

                mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'prefs SET `taborder`=\'%s\'',
                                    SQLEscape(serialize($tabOrder), $connection)));
            }

            // transfer PAcc settings
            $pacc_prefs = [];
            if ($havePAcc) {
                $res = mysqli_query($connection, 'SELECT * FROM '.$mysql['prefix'].'mod_premium_prefs');
                $pacc_prefs = mysqli_fetch_array($res, MYSQLI_ASSOC);
                mysqli_free_result($res);

                mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'prefs SET `send_pay_notification`=\'%s\',`pay_notification_to`=\'%s\',`pay_emailfrom`=\'%s\',`pay_emailfromemail`=\'%s\',`mwst`=\'%s\',`enable_vk`=\'%s\',`vk_kto_inh`=\'%s\',`vk_kto_nr`=\'%s\',`vk_kto_blz`=\'%s\',`vk_kto_inst`=\'%s\',`vk_kto_iban`=\'%s\',`vk_kto_bic`=\'%s\',`sendrg`=\'%s\',`rgnrfmt`=\'%s\',`kdnrfmt`=\'%s\'',
                                    SQLEscape($pacc_prefs['send_pay_notification'], $connection),
                                    SQLEscape($pacc_prefs['pay_notification_to'], $connection),
                                    SQLEscape($pacc_prefs['emailfrom'], $connection),
                                    SQLEscape($pacc_prefs['emailfromemail'], $connection),
                                    SQLEscape($pacc_prefs['mwst'], $connection),
                                    SQLEscape($pacc_prefs['zahlung_vorkasse'], $connection),
                                    SQLEscape($pacc_prefs['vk_kto_inh'], $connection),
                                    SQLEscape($pacc_prefs['vk_kto_nr'], $connection),
                                    SQLEscape($pacc_prefs['vk_kto_blz'], $connection),
                                    SQLEscape($pacc_prefs['vk_kto_inst'], $connection),
                                    SQLEscape($pacc_prefs['vk_kto_iban'], $connection),
                                    SQLEscape($pacc_prefs['vk_kto_bic'], $connection),
                                    SQLEscape($pacc_prefs['sendrg'], $connection),
                                    SQLEscape($pacc_prefs['rgnrfmt'], $connection),
                                    SQLEscape($pacc_prefs['kdnrfmt'], $connection)));
            } else {
                $emailFromEMail = 'postmaster@'.array_shift(explode(':', $bm_prefs['domains']));
                mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'prefs SET `pay_emailfrom`=\'%s\',`pay_emailfromemail`=\'%s\'',
                                    SQLEscape($lang_setup['accounting'], $connection),
                                    SQLEscape($emailFromEMail, $connection)));
            }

            // transfer PAcc orders
            if ($havePAcc) {
                $paymentMethods = [
                    'paypal' => 1,
                    'banktransfer' => 0,
                    'sofortueberweisung' => 2,
                    '' => 1,
                ];

                $res = mysqli_query($connection, 'SELECT * FROM '.$mysql['prefix'].'mod_premium_payments');
                while ($row = mysqli_fetch_array($res)) {
                    $txnID = $packageName = '';

                    if ((int) $row['payment_id'] > 0) {
                        $res2 = mysqli_query($connection, sprintf('SELECT `txn_id` FROM '.$mysql['prefix'].'payments WHERE `id`=%d',
                                                    $row['payment_id']));
                        list($txnID) = mysqli_fetch_array($res2, MYSQLI_NUM);
                        mysqli_free_result($res2);
                    }

                    $res2 = mysqli_query($connection, sprintf('SELECT `titel` FROM '.$mysql['prefix'].'mod_premium_packages WHERE `id`=%d',
                                                $row['paket']));
                    list($packageName) = mysqli_fetch_array($res2, MYSQLI_NUM);
                    mysqli_free_result($res2);

                    $cart = [];
                    $cart[] = [
                        'key' => 'PAcc.order.'.$row['paket'],
                        'count' => $row['fuer_einheiten'],
                        'amount' => round($row['betrag'] / $row['fuer_einheiten'], 0),
                        'total' => $row['betrag'],
                        'text' => $packageName,
                    ];

                    mysqli_query($connection, sprintf('INSERT INTO '.$mysql['prefix'].'orders(`orderid`,`userid`,`vkcode`,`txnid`,`cart`,`paymethod`,`amount`,`tax`,`inv_firstname`,`inv_lastname`,`inv_street`,`inv_no`,`inv_zip`,`inv_city`,`inv_country`,`created`,`activated`,`status`) '
                                .'VALUES(%d,%d,\'%s\',\'%s\',\'%s\',%d,%d,%d,\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',%d,%d,%d)',
                                $row['id'],
                                $row['benutzer'],
                                SQLEscape($row['vk-code'], $connection),
                                SQLEscape($txnID, $connection),
                                SQLEscape(serialize($cart), $connection),
                                $paymentMethods[$row['payment']],
                                $row['betrag'],
                                $pacc_prefs['mwst'] != 'nomwst' ? (float) $pacc_prefs['steuersatz'] : 0,
                                SQLEscape($row['inv_firstname'], $connection),
                                SQLEscape($row['inv_lastname'], $connection),
                                SQLEscape($row['inv_street'], $connection),
                                SQLEscape($row['inv_no'], $connection),
                                SQLEscape($row['inv_zip'], $connection),
                                SQLEscape($row['inv_city'], $connection),
                                SQLEscape($row['inv_country'], $connection),
                                $row['datum'],
                                $row['fertig'] == 1 ? $row['datum'] : 0,
                                $row['fertig'] == 1 ? 1 : 0));

                    if ($row['rechnung'] != '') {
                        mysqli_query($connection, sprintf('INSERT INTO '.$mysql['prefix'].'invoices(`orderid`,`invoice`) VALUES(%d,\'%s\')',
                                            $row['id'],
                                            SQLEscape($row['rechnung'], $connection)));
                    }
                }
                mysqli_free_result($res);
            }

            // payments table
            $res = mysqli_query($connection, 'SELECT * FROM '.$mysql['prefix'].'payments');
            while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                if ($row['typ'] == 'credits') {
                    $cart = [];
                    $cart[] = [
                        'key' => 'b1gMail.credits',
                        'count' => $row['items'],
                        'amount' => round(($row['amount'] * 100) / $row['items'], 0),
                        'total' => $row['amount'],
                        'text' => $lang_setup['credit_text'],
                    ];

                    mysqli_query($connection, sprintf('INSERT INTO '.$mysql['prefix'].'orders(`userid`,`txnid`,`cart`,`paymethod`,`amount`,`created`,`activated`,`status`) '
                                .'VALUES(%d,\'%s\',\'%s\',%d,%d,%d,%d,%d)',
                                $row['user'],
                                SQLEscape($row['txn_id'], $connection),
                                SQLEscape(serialize($cart), $connection),
                                1,
                                round($row['amount'] * 100, 2),
                                $row['datum'],
                                $row['txn_id'] != '' ? $row['datum'] : 0,
                                $row['txn_id'] != '' ? 1 : 0));
                }
            }
            mysqli_free_result($res);

            // delete old certificates
            mysqli_query($connection, 'DELETE FROM '.$mysql['prefix'].'certificates WHERE `hash` IN(\'e268a4c5\',\'4166ec0c\',\'c80493cb\',\'f73e89fd\',\'f64d9715\')');

            // disable obsolete / outdated plugins
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'mods SET `installed`=0 WHERE `modname` IN (\'TabOrderPlugin\',\'WidgetOrderPlugin\',\'PremiumAccountPlugin\')');
        }

        if ($numVersion <= 7203) {
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'users SET `last_smtp`=-1 WHERE `last_smtp`=0');

            $searchIn = @unserialize($bm_prefs['search_in']);
            if (is_array($searchIn)) {
                $searchIn['attachments'] = true;
                mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'prefs SET `search_in`=\'%s\'',
                    SQLEscape(serialize($searchIn), $connection)));
            }
        }

        if ($numVersion < 7301) {
            // salt PWs
            $res = mysqli_query($connection, 'SELECT `id`,`passwort` FROM '.$mysql['prefix'].'users WHERE `passwort_salt`=\'\'');
            while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                $newSalt = GeneratePW();
                $newPassword = md5($row['passwort'].$newSalt);

                mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'users SET `passwort`=\'%s\',`passwort_salt`=\'%s\' WHERE `id`=%d',
                    $newPassword,
                    SQLEscape($newSalt, $connection),
                    $row['id']));
            }
            mysqli_free_result($res);

            // transfer domains
            $domainPos = 0;
            $domains = explode(':', $bm_prefs['domains']);
            foreach ($domains as $domain) {
                $domain = trim($domain);
                if (strlen($domain) == 0) {
                    continue;
                }

                mysqli_query($connection, sprintf('REPLACE INTO '.$mysql['prefix'].'domains(`domain`,`in_login`,`in_signup`,`in_aliases`,`pos`) VALUES(\'%s\',1,1,1,%d)',
                    SQLEscape($domain, $connection),
                    $domainPos += 10));
            }

            // transfer admin account
            $adminSalt = GeneratePW();
            $adminPW = md5($bm_prefs['adminpw'].$adminSalt);
            mysqli_query($connection, sprintf('REPLACE INTO '.$mysql['prefix'].'admins(`adminid`,`username`,`firstname`,`lastname`,`password`,`password_salt`,`type`,`notes`) VALUES '
                .'(1,\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',0,\'%s\')',
                'admin',
                'Super',
                'Administrator',
                $adminPW,
                SQLEscape($adminSalt, $connection),
                SQLEscape($bm_prefs['notes'], $connection)));

            // set new prefs
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'prefs SET `template`=\'modern\',`adminpw`=\'\'');

            // enable preview
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'users SET `preview`=\'yes\'');
        }

        if ($numVersion < 7302) {
            mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'gruppen SET `allow_newsletter_optout`=\'%s\'',
                $bm_prefs['allow_newsletter_optout']));
            mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'prefs SET `mobile_url`=\'%s\'',
                SQLEscape($bm_prefs['selfurl'].'m/', $connection)));
        }

        if ($numVersion <= 7306) {
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'prefs SET `signup_dnsbl`=\'dnsbl.tornevall.org\' WHERE `signup_dnsbl`=\'\'');
        }

        if ($numVersion <= 7309) {
            // install abuse protect default config
            $apQueries = [];
            $apQueries[] = 'REPLACE INTO '.$mysql['prefix'].'abuse_points_config(`type`,`points`,`prefs`) VALUES(1,5,\'\')';
            $apQueries[] = 'REPLACE INTO '.$mysql['prefix'].'abuse_points_config(`type`,`points`,`prefs`) VALUES(2,25,\'\')';
            $apQueries[] = 'REPLACE INTO '.$mysql['prefix'].'abuse_points_config(`type`,`points`,`prefs`) VALUES(3,15,\'\')';
            $apQueries[] = 'REPLACE INTO '.$mysql['prefix'].'abuse_points_config(`type`,`points`,`prefs`) VALUES(4,10,\'\')';
            $apQueries[] = 'REPLACE INTO '.$mysql['prefix'].'abuse_points_config(`type`,`points`,`prefs`) VALUES(5,10,\'\')';
            $apQueries[] = 'REPLACE INTO '.$mysql['prefix'].'abuse_points_config(`type`,`points`,`prefs`) VALUES(6,20,\'interval=60\')';
            $apQueries[] = 'REPLACE INTO '.$mysql['prefix'].'abuse_points_config(`type`,`points`,`prefs`) VALUES(7,20,\'interval=5\')';
            $apQueries[] = 'REPLACE INTO '.$mysql['prefix'].'abuse_points_config(`type`,`points`,`prefs`) VALUES(21,5,\'amount=50;interval=5\')';
            $apQueries[] = 'REPLACE INTO '.$mysql['prefix'].'abuse_points_config(`type`,`points`,`prefs`) VALUES(22,5,\'amount=100;interval=5\')';

            foreach ($apQueries as $apQuery) {
                mysqli_query($connection, $apQuery);
            }

            // default values for new prefs
            mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'prefs SET `contactform_to`=\'%s\',`ap_autolock_notify_to`=\'%s\',`fts_bg_indexing`=\'no\'',
                SQLEscape($bm_prefs['passmail_abs'], $connection),
                SQLEscape($bm_prefs['passmail_abs'], $connection)));

            // default values for new group settings
            $res = mysqli_query($connection, 'SELECT `id`,`send_limit` FROM '.$mysql['prefix'].'gruppen');
            while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                $ftsSupport = class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', PDO::getAvailableDrivers()));
                mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'gruppen SET `send_limit_count`=1,`send_limit_time`=%d,`mail_send_code`=\'%s\',`sms_send_code`=\'%s\',`ftsearch`=\'%s\' WHERE `id`=%d',
                    $row['send_limit'],
                    $bm_prefs['mail_send_code'],
                    $bm_prefs['sms_send_code'],
                    $ftsSupport ? 'yes' : 'no',
                    $row['id']));
            }
            mysqli_free_result($res);

            // add new notification system alert for all existing dates
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'dates SET `flags`=`flags`|8');

            // set VAT rates and is_eu column for EU countries
            $vat = [
                14 => 21, 19 => 20, 24 => 25, 25 => 19, 29 => 20, 31 => 24, 32 => 20, 36 => 23, 37 => 20,
                44 => 23, 47 => 22, 60 => 25, 64 => 21, 68 => 21, 69 => 17, 75 => 18, 85 => 21, 89 => 20,
                95 => 23, 96 => 23, 100 => 24, 104 => 25, 107 => 25, 110 => 20, 111 => 22, 112 => 21,
                125 => 21, 131 => 27, 144 => 19,
            ];
            foreach ($vat as $countryID => $vatRate) {
                mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'staaten SET `is_eu`=\'yes\',`vat`=%d WHERE `id`=%d',
                    $vatRate,
                    $countryID));
            }

            // convert static balances
            mysqli_query($connection, 'BEGIN') || die('Failed to start transaction');
            $res = mysqli_query($connection, 'SELECT `id`,`sms_kontigent` FROM '.$mysql['prefix'].'users WHERE `sms_kontigent`!=0');
            while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                mysqli_query($connection, sprintf('INSERT INTO '.$mysql['prefix'].'transactions(`userid`,`description`,`amount`,`date`,`status`) VALUES(%d,\'lang:startingbalance\',%d,%d,%d)',
                    $row['id'],
                    $row['sms_kontigent'],
                    time(),
                    1)) || die('Failed to insert transaction');
                mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'users SET `sms_kontigent`=0 WHERE `id`=%d',
                    $row['id'])) || die('Failed to reset sms_kontigent');
            }
            mysqli_free_result($res);
            mysqli_query($connection, 'COMMIT') || die('Failed to commit transaction');
            mysqli_query($connection, 'ALTER TABLE '.$mysql['prefix'].'users DROP sms_kontigent');
        }

        if ($numVersion <= 7412) {
            $old_defaultInvoice = '<table width=\"100%\">\n    <tbody>\n        <tr>\n            <td style=\"font-family: Arial;\" align=\"left\">\n            	<h2>{$service_title}</h2>\n            </td>\n            <td style=\"font-family: Arial;\" align=\"right\">\n		{$service_title}<br>Bitte passen<br>Sie die Absender-Adresse<br>in der Rechnungsvorlage an.<br>\n	   </td>\n        </tr>\n        <tr style=\"font-family: Arial;\">\n            <td colspan=\"2\"><hr style=\"height: 1px;\" color=\"#666666\" noshade=\"noshade\" width=\"100%\"><br></td>\n        </tr>\n        <tr>\n            <td style=\"font-family: Arial;\" align=\"left\">\n            <table style=\"border: 1px solid rgb(0, 0, 0);\" bgcolor=\"#666666\" cellpadding=\"10\" cellspacing=\"0\" width=\"100%\">\n                <tbody>\n                    <tr>\n\n                        <td bgcolor=\"#ffffff\">{$vorname} {$nachname}<br>{$strasse} {$nr}<br>{$plz} {$ort}<br>{$land}</td>\n                    </tr>\n                </tbody>\n            </table>\n            </td>\n            <td style=\"font-family: Arial;\" align=\"right\">\n 			<b style=\"font-family: Arial;\">{lng p=\"date\"}: </b><span style=\"font-family: Arial;\">{$datum}</span><br style=\"font-family: Arial;\">\n 			<b style=\"font-family: Arial;\">{lng p=\"invoiceno\"}: </b><span style=\"font-family: Arial;\">{$rgnr}</span><br style=\"font-family: Arial;\">\n			<b style=\"font-family: Arial;\">{lng p=\"customerno\"}: </b><span style=\"font-family: Arial;\">{$kdnr}</span><br>\n	   </td>\n        </tr>\n        <tr style=\"font-family: Arial;\">\n            <td colspan=\"2\">\n            <p>&nbsp;</p>\n            <b><br>{lng p=\"yourinvoice\"}</b>\n            <p>{lng p=\"dearsirormadam\"},</p>\n            <p>{lng p=\"invtext\"}:</p>\n\n            <p>\n            <table cellpadding=\"4\" cellspacing=\"0\" width=\"100%\">\n                <tbody>\n                    <tr>\n                        <td width=\"10%\">{lng p=\"pos\"}</td>\n                        <td width=\"10%\">{lng p=\"count\"}</td>\n                        <td width=\"50%\">{lng p=\"descr\"}</td>\n                        <td width=\"15%\">{lng p=\"ep\"} ({$currency})</td>\n                        <td width=\"15%\">{lng p=\"gp\"} ({$currency})</td>\n                    </tr>\n                    <tr>\n                        <td colspan=\"5\"><hr style=\"height: 1px;\" color=\"#666666\" noshade=\"noshade\" width=\"100%\"></td>\n                    </tr>\n{foreach from=$cart item=pos}\n                    <tr>\n                        <td>{$pos.pos}</td>\n                        <td>{$pos.count}</td>\n                        <td>{text value=$pos.text}</td>\n                        <td>{$pos.amount}</td>\n                        <td>{$pos.total}</td>\n                    </tr>\n{/foreach}\n                    <tr>\n                        <td colspan=\"5\"><hr style=\"height: 1px;\" color=\"#666666\" noshade=\"noshade\" width=\"100%\"></td>\n\n                    </tr>\n                    <tr>\n                        <td colspan=\"4\" align=\"right\">{lng p=\"gb\"} ({lng p=\"net\"}):</td>\n                        <td>{$netto}</td>\n                    </tr>\n                    <tr>\n                        <td colspan=\"4\" align=\"right\">{lng p=\"vat\"} {$mwstsatz}%:</td>\n\n                        <td>{$mwst}</td>\n                    </tr>\n                    <tr>\n                        <td colspan=\"4\" align=\"right\">{lng p=\"gb\"} ({lng p=\"gross\"}):</td>\n                        <td>{$brutto}</td>\n                    </tr>\n                </tbody>\n\n            </table>\n            </p>\n            <p>{$zahlungshinweis}<br></p>\n            <p>{lng p=\"kindregards\"}</p>\n            <p>{$service_title}</p>\n            <p>&nbsp;</p>\n            </td>\n\n        </tr>\n        <tr style=\"font-family: Arial;\">\n            <td colspan=\"2\"><hr style=\"height: 1px;\" color=\"#666666\" noshade=\"noshade\" width=\"100%\"></td>\n        </tr>\n        <tr style=\"font-family: Arial;\">\n            <td colspan=\"2\"><small>{lng p=\"invfooter\"}<br><br>{if $ktonr}<b>{lng p=\"bankacc\"}: </b>{lng p=\"kto_nr\"} {$ktonr} ({lng p=\"kto_inh\"} {$ktoinhaber}), {lng p=\"kto_blz\"} {$ktoblz} ({$ktoinstitut}){if $ktoiban}, {lng p=\"kto_iban\"} {$ktoiban}, {lng p=\"kto_bic\"} {$ktobic}{/if}{/if}<br></small></td>\n        </tr>\n\n    </tbody>\n</table>\n\n';
            $res = mysqli_query($connection, 'SELECT rgtemplate FROM '.$mysql['prefix'].'prefs');
            $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
            if($row['rgtemplate']==$old_defaultInvoice) {
                mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'prefs SET rgtemplate=\'%s\'',
                                $defaultInvoice));
            }
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'staaten SET is_eu = \'no\' WHERE id = 37'); // GB is not in EU anymore
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'staaten SET land = \'Eswatini\' WHERE id = 117'); // Rename Swasiland to Eswatini
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'gruppen SET organizer = \'yes\''); // Allow organizer to all groups (default)
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'aliase SET login = \'no\''); // No login with alias (default)
        }

        // add new root certificates
        if (!isset($rootCertsData)) {
            include './data/rootcerts.data.php';
        }
        foreach ($rootCertsData as $hash => $query) {
            $res = mysqli_query($connection, sprintf('SELECT COUNT(*) FROM '.$mysql['prefix'].'certificates WHERE `type`=0 AND `userid`=0 AND `hash`=\'%s\'',
                SQLEscape($hash, $connection)));
            list($hashCount) = mysqli_fetch_array($res, MYSQLI_NUM);
            mysqli_free_result($res);

            if ((int) $hashCount == 0) {
                mysqli_query($connection, $query);
            }
        }

        // remove outdated root certificates
        mysqli_query($connection, 'DELETE FROM '.$mysql['prefix'].'certificates WHERE `type`=0 AND `userid`=0 AND `validto`<'.time());

        echo 'OK:DONE';
    }

    //
    // optimize and clean up
    //
    elseif ($do == 'struct3') {
        $queries = [];

        if ($numVersion <= 7001) {
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP sms_user';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP sms_pass';
        }

        if ($numVersion <= 7201
            && isset($bm_prefs['reg_smsvalidation'])) {
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP reg_smsvalidation';
        }

        if ($numVersion < 7202) {
            // keep compatibility with older b1gMailServer releases -- $queries[] = 'ALTER TABLE bm60_prefs DROP max_bcc';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP sms_enable_paypal';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP sms_paypal_mail';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP sms_enable_su';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP sms_su_kdnr';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP sms_su_prjnr';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP sms_su_prjpass';
            $queries[] = 'DROP TABLE '.$mysql['prefix'].'payments';
            $queries[] = 'DROP TABLE IF EXISTS '.$mysql['prefix'].'mod_taborder';
        }

        if ($numVersion < 7302) {
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP allow_newsletter_optout';
        }

        if ($numVersion < 7401) {
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'gruppen DROP httpmail';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'gruppen DROP send_limit';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP steuersatz';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP mail_send_code';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP sms_send_code';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'mails DROP body';
        }

        $count = count($queries);

        // done?
        if ($pos >= $count) {
            echo 'OK:DONE';
        } else {
            $query = $queries[$pos++];

            mysqli_query($connection, $query);

            if ($pos >= $count) {
                echo 'OK:DONE';
            } else {
                echo 'OK:'.$pos.'/'.$count;
            }
        }
    }

    //
    // reset cache
    //
    elseif ($do == 'resetcache') {
        $deleteIDs = [];

        $res = mysqli_query($connection, 'SELECT size,`key` FROM '.$mysql['prefix'].'file_cache');
        while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
            $fileName = '../temp/cache/'.$row['key'].'.cache';
            if (file_exists($fileName)) {
                @unlink($fileName);
            }
            $fileName = '../temp/'.$row['key'].'.cache';
            if (file_exists($fileName)) {
                @unlink($fileName);
            }
            $deleteIDs[] = $row['key'];
        }
        mysqli_free_result($res);

        if (count($deleteIDs) > 0) {
            mysqli_query($connection, 'DELETE FROM '.$mysql['prefix'].'file_cache WHERE `key` IN(\''.implode('\',\'', $deleteIDs).'\')');
        }

        echo 'OK:DONE';
    }

    //
    // optimize tables
    //
    elseif ($do == 'optimize') {
        // get tables
        $tables = [];
        $res = mysqli_query($connection, 'SHOW TABLES');
        while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
            if (substr($row[0], 0, 5) == $mysql['prefix']) {
                $tables[] = $row[0];
            }
        }
        mysqli_free_result($res);
        $count = count($tables);

        // done?
        if ($pos >= $count) {
            echo 'OK:DONE';
        } else {
            $table = $tables[$pos++];
            mysqli_query($connection, 'OPTIMIZE TABLE '.$table);

            if ($pos >= $count) {
                echo 'OK:DONE';
            } else {
                echo 'OK:'.$pos.'/'.$count;
            }
        }
    }

    //
    // complete
    //
    elseif ($do == 'complete') {
        mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'prefs SET wartung=\'no\',patchlevel=0');

        $fp = fopen('../serverlib/version.inc.php', 'w');
        fwrite($fp, sprintf('<?php define(\'B1GMAIL_VERSION\', $b1gmail_version = \'%s\'); ?>', $target_version));
        fclose($fp);

        if(is_writable('./'))
        {
            $lock = @fopen('./lock_update', 'w');
            $written = @fwrite($lock, '1');
            @fclose($lock);
        }

        echo 'OK:DONE';
    }

    //
    // unknown action
    //
    else {
        echo 'ERROR:Unknown action.';
    }

    mysqli_close($connection);

    exit();
}

// footer
pageFooter(true);

// disconnect
mysqli_close($connection);