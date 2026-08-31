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
                        '7.5.0-RC1', '7.5.0-RC2'];


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