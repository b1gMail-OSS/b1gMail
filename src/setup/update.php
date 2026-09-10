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

// lock is checked after language load

// known versions
$knownVersions = [
                        '7.4.0-Beta1', '7.4.0-Beta2', '7.4.0-Beta3', '7.4.0-Beta4', '7.4.0', 
                        '7.4.1-Beta1', '7.4.1-Beta2', '7.4.1-Beta3', '7.4.1-Beta4', '7.4.1-RC1', '7.4.1-RC2',
                        '7.4.2-RC1', '7.4.2-RC2',
                        '7.5.0-RC1', '7.5.0-RC2', '7.5.0-RC3'];


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
}elseif (strpos($b1gmail_version, '-RC') !== false) {
    $numVersion = str_replace('-RC', '.', $b1gmail_version);
}
 else {
    $numVersion = $b1gmail_version.'.9';
}
$numVersion = (int) str_replace('.', '', $numVersion);
// Prevent upgrade from b1gMail older than 7.4.0
if ($numVersion <= 7309) {
    die ('b1gMail Version too old. Please upgrade first to b1gMail 7.4');
}

// step?
if (!isset($_REQUEST['step'])) {
    $step = STEP_WELCOME;
} else {
    $step = (int) $_REQUEST['step'];
}

// read language file
if (!isset($_GET['lng']) && !isset($_POST['lng'])) {
    $_GET['lng'] = strpos($bm_prefs['language'], 'deutsch') !== false ? 'deutsch' : 'english';
}
ReadLanguage();

if ($step == STEP_UPDATE_STEP) {
    if (!SetupCsrfOk(true)) {
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'ERROR:CSRF';
        exit;
    }
} elseif (!SetupCsrfOk()) {
    $step = STEP_WELCOME;
    $setupError = $lang_setup['csrf_fail'];
}

SetupAbortIfLocked('lock_update', true);

if ($step != STEP_UPDATE_STEP) {
    pageHeader(true);
    if (!empty($setupError)) {
        echo SetupAlert('danger', $setupError);
    }
}

/*
 * welcome
 */
if ($step == STEP_WELCOME
    || ($b1gmail_version == $target_version)
    || !in_array($b1gmail_version, $knownVersions)) {
    if ($b1gmail_version == $target_version) {
        ?>
		<h1><?php echo SetupH($lang_setup['error']); ?></h1>
		<?php echo SetupAlert('info', sprintf($lang_setup['uptodate'], SetupH($target_version))); ?>
		<?php
    } elseif (!in_array($b1gmail_version, $knownVersions)) {
        ?>
		<h1><?php echo SetupH($lang_setup['error']); ?></h1>
		<?php echo SetupAlert('danger', sprintf($lang_setup['unknownversion'], SetupH($b1gmail_version), SetupH($target_version))); ?>
		<?php
    } else {
        $nextStep = STEP_SYSTEMCHECK; ?>
		<h1><?php echo SetupH($lang_setup['welcome']); ?></h1>
		<p><?php echo sprintf($lang_setup['update_welcome_text'], SetupH($b1gmail_version), SetupH($target_version)); ?></p>
		<?php echo SetupAlert('warning', $lang_setup['update_note3']); ?>
		<?php
    }
}

/*
 * system check
 */
elseif ($step == STEP_SYSTEMCHECK) {
    $nextStep = STEP_UPDATE;
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

    $showDbMailsNote = false;
    if ($numVersion <= 7309) {
        $res = mysqli_query($connection, 'SELECT COUNT(*) FROM '.$mysql['prefix'].'mails WHERE LENGTH(`body`)!=4');
        list($dbMails) = mysqli_fetch_array($res, MYSQLI_NUM);
        mysqli_free_result($res);
        $showDbMailsNote = ($dbMails != 0);
        $rows[] = [
            'label' => $lang_setup['dbmails'],
            'required' => '0',
            'available' => (string) $dbMails,
            'ok' => !$showDbMailsNote,
        ];
    }

    list($fileRows, $chmodCommands, $filesOk) = SetupCollectWritableRows($writeableFiles);
    $rows = array_merge($rows, $fileRows);
    if (!$phpOk || !$filesOk || $showDbMailsNote) {
        $nextStep = STEP_SYSTEMCHECK;
    } ?>
	<h1><?php echo SetupH($lang_setup['syscheck']); ?></h1>
	<p><?php echo $lang_setup['syscheck_text']; ?></p>
	<?php SetupRenderCheckTable($rows); ?>
	<?php echo SetupRenderChmod($chmodCommands); ?>
	<?php if ($showDbMailsNote) { ?>
	<?php echo SetupAlert('danger', $lang_setup['dbmails_note'], '', array('class' => 'mt-3')); ?>
	<?php } ?>
	<?php echo SetupAlert($nextStep == STEP_UPDATE ? 'success' : 'danger', $nextStep == STEP_UPDATE ? $lang_setup['checkok_text'] : $lang_setup['checkfail_text'], '', array('class' => 'mt-3')); ?>
	<?php
}

/*
 * update
 */
elseif ($step == STEP_UPDATE) {
    $updateSteps = ['prepare', 'struct2', 'config', 'struct3', 'resetcache', 'optimize', 'complete'];
    $updateLabels = [
        'prepare' => $lang_setup['update_prepare'],
        'struct2' => $lang_setup['update_struct2'],
        'config' => $lang_setup['update_config'],
        'struct3' => $lang_setup['update_struct3'],
        'resetcache' => $lang_setup['update_resetcache'],
        'optimize' => $lang_setup['update_optimize'],
        'complete' => $lang_setup['update_complete'],
    ]; ?>
	<h1><?php echo SetupH($lang_setup['updating']); ?></h1>
	<p><?php echo $lang_setup['updating_text']; ?></p>
	<?php SetupCloseCardBody(); ?>
	<div id="setup-progress" data-script="update.php" data-ajax-step="4" data-csrf="<?php echo SetupH(SetupCsrfToken()); ?>" data-lng="<?php echo SetupH($lang); ?>" data-steps="<?php echo SetupH(json_encode($updateSteps)); ?>">
	<div class="table-responsive">
	<table class="table table-vcenter card-table">
		<thead>
			<tr>
				<th class="w-1"></th>
				<th><?php echo SetupH($lang_setup['step']); ?></th>
				<th class="setup-progress-col"><?php echo SetupH($lang_setup['progress']); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($updateSteps as $i => $key) { ?>
		<tr class="setup-progress-row">
			<td id="step_<?php echo SetupH($key); ?>_status"></td>
			<th id="step_<?php echo SetupH($key); ?>_text"><?php echo ($i + 1).'. '.$updateLabels[$key]; ?></th>
			<td class="setup-progress-col" id="step_<?php echo SetupH($key); ?>_progress"></td>
		</tr>
		<?php } ?>
		</tbody>
	</table>
	</div>
	</div>
	<?php SetupOpenCardBody(); ?>
	<?php echo SetupAlert('warning', $lang_setup['updating_text2'], '', array('dismissible' => false)); ?>
	<textarea readonly="readonly" class="form-control setup-log d-none" id="log" rows="6"></textarea>
	<?php
    $doneText = $lang_setup['updatedonefinal'];
    if (empty($bm_prefs['db_is_utf8'])) {
        $doneText .= '<br /><br />'.$lang_setup['dbnotconverted'];
    }
    echo SetupAlert('success', $doneText, '', array('id' => 'done', 'class' => 'd-none mt-3', 'dismissible' => false));

    // CleverCron still active at start of update → show relocation notice when finished.
    $showCleverCronNotice = false;
    $res = mysqli_query($connection,
        'SELECT installed, paused FROM '.$mysql['prefix'].'mods'
        .' WHERE modname=\'TCCronPlugin\' OR modname=\'PluginTCCron\''
        .' OR packageName LIKE \'%tccrn%\' OR packageName LIKE \'%clevercron%\''
        .' LIMIT 1');
    if ($res && ($row = mysqli_fetch_array($res, MYSQLI_ASSOC))) {
        $showCleverCronNotice = ((int) $row['installed'] === 1 && (int) $row['paused'] === 0);
    }
    if ($res) {
        mysqli_free_result($res);
    }
    if ($showCleverCronNotice && !empty($lang_setup['update_clevercron_notice'])) {
        echo SetupAlert(
            'info',
            $lang_setup['update_clevercron_notice'],
            '',
            array('id' => 'update-clevercron-notice', 'class' => 'd-none mt-3', 'dismissible' => false)
        );
    }
	?>
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

        $databaseStructure = json_decode($databaseStructure, JSON_OBJECT_AS_ARRAY);
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


        if ($numVersion <= 7412) {
            $old_defaultInvoice = '<table width=\"100%\">\n    <tbody>\n        <tr>\n            <td style=\"font-family: Arial;\" align=\"left\">\n            	<h2>{$service_title}</h2>\n            </td>\n            <td style=\"font-family: Arial;\" align=\"right\">\n		{$service_title}<br>Bitte passen<br>Sie die Absender-Adresse<br>in der Rechnungsvorlage an.<br>\n	   </td>\n        </tr>\n        <tr style=\"font-family: Arial;\">\n            <td colspan=\"2\"><hr style=\"height: 1px;\" color=\"#666666\" noshade=\"noshade\" width=\"100%\"><br></td>\n        </tr>\n        <tr>\n            <td style=\"font-family: Arial;\" align=\"left\">\n            <table style=\"border: 1px solid rgb(0, 0, 0);\" bgcolor=\"#666666\" cellpadding=\"10\" cellspacing=\"0\" width=\"100%\">\n                <tbody>\n                    <tr>\n\n                        <td bgcolor=\"#ffffff\">{$vorname} {$nachname}<br>{$strasse} {$nr}<br>{$plz} {$ort}<br>{$land}</td>\n                    </tr>\n                </tbody>\n            </table>\n            </td>\n            <td style=\"font-family: Arial;\" align=\"right\">\n 			<b style=\"font-family: Arial;\">{lng p=\"date\"}: </b><span style=\"font-family: Arial;\">{$datum}</span><br style=\"font-family: Arial;\">\n 			<b style=\"font-family: Arial;\">{lng p=\"invoiceno\"}: </b><span style=\"font-family: Arial;\">{$rgnr}</span><br style=\"font-family: Arial;\">\n			<b style=\"font-family: Arial;\">{lng p=\"customerno\"}: </b><span style=\"font-family: Arial;\">{$kdnr}</span><br>\n	   </td>\n        </tr>\n        <tr style=\"font-family: Arial;\">\n            <td colspan=\"2\">\n            <p>&nbsp;</p>\n            <b><br>{lng p=\"yourinvoice\"}</b>\n            <p>{lng p=\"dearsirormadam\"},</p>\n            <p>{lng p=\"invtext\"}:</p>\n\n            <p>\n            <table cellpadding=\"4\" cellspacing=\"0\" width=\"100%\">\n                <tbody>\n                    <tr>\n                        <td width=\"10%\">{lng p=\"pos\"}</td>\n                        <td width=\"10%\">{lng p=\"count\"}</td>\n                        <td width=\"50%\">{lng p=\"descr\"}</td>\n                        <td width=\"15%\">{lng p=\"ep\"} ({$currency})</td>\n                        <td width=\"15%\">{lng p=\"gp\"} ({$currency})</td>\n                    </tr>\n                    <tr>\n                        <td colspan=\"5\"><hr style=\"height: 1px;\" color=\"#666666\" noshade=\"noshade\" width=\"100%\"></td>\n                    </tr>\n{foreach from=$cart item=pos}\n                    <tr>\n                        <td>{$pos.pos}</td>\n                        <td>{$pos.count}</td>\n                        <td>{text value=$pos.text}</td>\n                        <td>{$pos.amount}</td>\n                        <td>{$pos.total}</td>\n                    </tr>\n{/foreach}\n                    <tr>\n                        <td colspan=\"5\"><hr style=\"height: 1px;\" color=\"#666666\" noshade=\"noshade\" width=\"100%\"></td>\n\n                    </tr>\n                    <tr>\n                        <td colspan=\"4\" align=\"right\">{lng p=\"gb\"} ({lng p=\"net\"}):</td>\n                        <td>{$netto}</td>\n                    </tr>\n                    <tr>\n                        <td colspan=\"4\" align=\"right\">{lng p=\"vat\"} {$mwstsatz}%:</td>\n\n                        <td>{$mwst}</td>\n                    </tr>\n                    <tr>\n                        <td colspan=\"4\" align=\"right\">{lng p=\"gb\"} ({lng p=\"gross\"}):</td>\n                        <td>{$brutto}</td>\n                    </tr>\n                </tbody>\n\n            </table>\n            </p>\n            <p>{$zahlungshinweis}<br></p>\n            <p>{lng p=\"kindregards\"}</p>\n            <p>{$service_title}</p>\n            <p>&nbsp;</p>\n            </td>\n\n        </tr>\n        <tr style=\"font-family: Arial;\">\n            <td colspan=\"2\"><hr style=\"height: 1px;\" color=\"#666666\" noshade=\"noshade\" width=\"100%\"></td>\n        </tr>\n        <tr style=\"font-family: Arial;\">\n            <td colspan=\"2\"><small>{lng p=\"invfooter\"}<br><br>{if $ktonr}<b>{lng p=\"bankacc\"}: </b>{lng p=\"kto_nr\"} {$ktonr} ({lng p=\"kto_inh\"} {$ktoinhaber}), {lng p=\"kto_blz\"} {$ktoblz} ({$ktoinstitut}){if $ktoiban}, {lng p=\"kto_iban\"} {$ktoiban}, {lng p=\"kto_bic\"} {$ktobic}{/if}{/if}<br></small></td>\n        </tr>\n\n    </tbody>\n</table>\n\n';
            $res = mysqli_query($connection, 'SELECT rgtemplate FROM '.$mysql['prefix'].'prefs');
            $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
            if($row['rgtemplate']==$old_defaultInvoice) {
                mysqli_query($connection, sprintf('UPDATE '.$mysql['prefix'].'prefs SET rgtemplate=\'%s\'',
                                SQLEscape($defaultInvoice, $connection)));
            }
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'staaten SET is_eu = \'no\' WHERE id = 37'); // GB is not in EU anymore
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'staaten SET land = \'Eswatini\' WHERE id = 117'); // Rename Swasiland to Eswatini
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'gruppen SET organizer = \'yes\''); // Allow organizer to all groups (default)
            mysqli_query($connection, 'UPDATE '.$mysql['prefix'].'aliase SET login = \'no\''); // No login with alias (default)
        }

        // 7.5: one-time data migrations (schema itself comes from database.struct.json / struct2)
        if ($numVersion < 7503) {
            $p = $mysql['prefix'];

            // Pre-7.5 installs: keep Toolbox RPC working after column default "no".
            if ($numVersion < 7501) {
                mysqli_query($connection, 'UPDATE '.$p.'prefs SET clientapi_enable=\'yes\'');
            }

            // Fill cron_secret once if empty (column added by struct sync).
            $res = mysqli_query($connection, 'SELECT cron_secret FROM '.$p.'prefs LIMIT 1');
            if ($res) {
                $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
                mysqli_free_result($res);
                if (is_array($row) && trim((string) $row['cron_secret']) === '') {
                    $secret = bin2hex(random_bytes(16));
                    mysqli_query($connection, sprintf(
                        'UPDATE '.$p.'prefs SET cron_secret=\'%s\'',
                        SQLEscape($secret, $connection)
                    ));
                }
            }

            // MFA: backfill enabled_at from created.
            $res = mysqli_query($connection, 'SHOW TABLES LIKE \''.$p.'mfa_accounts\'');
            if ($res && mysqli_num_rows($res) > 0) {
                mysqli_free_result($res);
                mysqli_query($connection,
                    'UPDATE '.$p.'mfa_accounts SET enabled_at=created'
                    .' WHERE enabled=\'yes\' AND (enabled_at=0 OR enabled_at IS NULL)');
            } elseif ($res) {
                mysqli_free_result($res);
            }

            // known_logins: legacy unique key included ua_hash; new key is per account+IP.
            $res = mysqli_query($connection, 'SHOW TABLES LIKE \''.$p.'known_logins\'');
            if ($res && mysqli_num_rows($res) > 0) {
                mysqli_free_result($res);
                $idx = mysqli_query($connection, 'SHOW INDEX FROM '.$p.'known_logins WHERE Key_name=\'login_key\'');
                $hasLegacy = $idx && mysqli_num_rows($idx) > 0;
                if ($idx) {
                    mysqli_free_result($idx);
                }
                if ($hasLegacy) {
                    mysqli_query($connection,
                        'DELETE t1 FROM '.$p.'known_logins t1'
                        .' INNER JOIN '.$p.'known_logins t2'
                        .' ON t1.account_type=t2.account_type AND t1.account_id=t2.account_id'
                        .' AND t1.ip=t2.ip AND t1.id < t2.id');
                    mysqli_query($connection, 'ALTER TABLE '.$p.'known_logins DROP INDEX `login_key`');
                }
                $idx = mysqli_query($connection, 'SHOW INDEX FROM '.$p.'known_logins WHERE Key_name=\'login_ip\'');
                $hasNew = $idx && mysqli_num_rows($idx) > 0;
                if ($idx) {
                    mysqli_free_result($idx);
                }
                if (!$hasNew) {
                    mysqli_query($connection,
                        'ALTER TABLE '.$p.'known_logins'
                        .' ADD UNIQUE KEY `login_ip` (`account_type`,`account_id`,`ip`)');
                }
            } elseif ($res) {
                mysqli_free_result($res);
            }

            // CleverCron → core scheduled tasks: import jobs first, then deactivate plugin.
            $cleverCronActive = false;
            $res = mysqli_query($connection,
                'SELECT installed, paused FROM '.$p.'mods'
                .' WHERE modname=\'TCCronPlugin\' OR modname=\'PluginTCCron\''
                .' OR packageName LIKE \'%tccrn%\' OR packageName LIKE \'%clevercron%\''
                .' LIMIT 1');
            if ($res && ($row = mysqli_fetch_array($res, MYSQLI_ASSOC))) {
                $cleverCronActive = ((int) $row['installed'] === 1 && (int) $row['paused'] === 0);
            }
            if ($res) {
                mysqli_free_result($res);
            }

            $oldCron = $p.'tccrn_plugin_cron';
            $oldSettings = $p.'tccrn_plugin_settings';
            $res = mysqli_query($connection, 'SHOW TABLES LIKE \''.$oldCron.'\'');
            $hasOldCron = ($res && mysqli_num_rows($res) > 0);
            if ($res) {
                mysqli_free_result($res);
            }

            $res = mysqli_query($connection, 'SHOW TABLES LIKE \''.$p.'scheduled_tasks\'');
            $hasSched = ($res && mysqli_num_rows($res) > 0);
            if ($res) {
                mysqli_free_result($res);
            }

            if ($hasSched) {
                $res = mysqli_query($connection, 'SELECT COUNT(*) FROM '.$p.'scheduled_tasks_config');
                if ($res) {
                    list($cfgCount) = mysqli_fetch_array($res, MYSQLI_NUM);
                    mysqli_free_result($res);
                    if ((int) $cfgCount < 1) {
                        mysqli_query($connection, 'INSERT INTO '.$p.'scheduled_tasks_config (`id`, `loglevel`) VALUES (1, 6)');
                    }
                }

                if ($hasOldCron) {
                    mysqli_query($connection,
                        'INSERT INTO '.$p.'scheduled_tasks'
                        .' (`active`, `task`, `status`, `lastcall`, `nextcall`, `crondata`, `taskdata`, `log`)'
                        .' SELECT `active`, `task`, `status`, `lastcall`, `nextcall`, `crondata`, `taskdata`, `log`'
                        .' FROM `'.$oldCron.'`');

                    $res = mysqli_query($connection, 'SHOW TABLES LIKE \''.$oldSettings.'\'');
                    if ($res && mysqli_num_rows($res) > 0) {
                        mysqli_free_result($res);
                        $res2 = mysqli_query($connection, 'SELECT `loglevel` FROM `'.$oldSettings.'` LIMIT 1');
                        if ($res2 && ($cfg = mysqli_fetch_array($res2, MYSQLI_ASSOC))) {
                            mysqli_query($connection, sprintf(
                                'UPDATE '.$p.'scheduled_tasks_config SET `loglevel`=%d WHERE `id`=1',
                                (int) $cfg['loglevel']
                            ));
                        }
                        if ($res2) {
                            mysqli_free_result($res2);
                        }
                        mysqli_query($connection, 'DROP TABLE IF EXISTS `'.$oldSettings.'`');
                    } elseif ($res) {
                        mysqli_free_result($res);
                    }

                    mysqli_query($connection, 'DROP TABLE IF EXISTS `'.$oldCron.'`');
                }

                mysqli_query($connection,
                    'UPDATE '.$p.'scheduled_tasks SET `task`=REPLACE(`task`,\'tccrn.\',\'sched.\')'
                    .' WHERE `task` LIKE \'tccrn.%\'');
            }

            mysqli_query($connection,
                'UPDATE '.$p.'mods SET installed=0, paused=1'
                .' WHERE modname=\'TCCronPlugin\' OR modname=\'PluginTCCron\''
                .' OR packageName LIKE \'%tccrn%\' OR packageName LIKE \'%clevercron%\'');

            if ($cleverCronActive) {
                @file_put_contents(
                    dirname(__DIR__).'/temp/update_clevercron_notice.flag',
                    '1'
                );
            }
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

        if ($numVersion < 7401) {
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'gruppen DROP httpmail';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'gruppen DROP send_limit';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP steuersatz';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP mail_send_code';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP sms_send_code';
            $queries[] = 'ALTER TABLE '.$mysql['prefix'].'mails DROP body';
        }

        // Legacy mobile interface (/m) removed
        $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP mobile_url';
        $queries[] = 'ALTER TABLE '.$mysql['prefix'].'prefs DROP redirect_mobile';
        $queries[] = 'ALTER TABLE '.$mysql['prefix'].'gruppen DROP wap';

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

        ConfigEnsureSignKeyInFile('../serverlib/config.inc.php');

        $fp = fopen('../serverlib/version.inc.php', 'w');
        fwrite($fp, sprintf('<?php define(\'B1GMAIL_VERSION\', $b1gmail_version = \'%s\'); ?>', $target_version));
        fclose($fp);

        SetupWriteLock('lock_update');

        @unlink(dirname(__DIR__).'/temp/update_clevercron_notice.flag');

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