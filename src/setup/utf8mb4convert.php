<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
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

// steps
define('STEP_SELECT_LANGUAGE', 0);
define('STEP_WELCOME', 1);
define('STEP_SYSTEMCHECK', 2);
define('STEP_CONVERT', 3);
define('STEP_CONVERT_STEP', 4);
// other
define('DB_INSTALL_PREFIX', 'bm60_');

// connect to mysql db
if (!($connection = CheckMySQLLogin($mysql['host'], $mysql['user'], $mysql['pass'],
                    $mysql['db']))) {
    die('ERROR:MySQL connection failed');
}

// read prefs
$result = mysqli_query($connection, 'SELECT * FROM '.DB_INSTALL_PREFIX.'prefs LIMIT 1');
$bm_prefs = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);

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
if ($_GET['lng'] == 'deutsch') {
    $lang_setup['convert_welcome_text'] = 'Herzlich Willkommen! Dieser Assistent konvertiert Ihre b1gMail-Datenbank von den MySQL-Zeichensatz <b>UTF-8(mb3)</b>-Zeichensatz nach <b>UTF8MB4</b>. <font color="red">Nutzen Sie diesen Konverter nur, wenn Ihre Datenbank in der MySQL UTF8(mb3)-Kodierung vorliegt!</font> Klicken Sie auf &quot;Weiter &raquo;&quot;, um fortzufahren.';
    $lang_setup['convert_alreadyutf8'] = 'Ihre Datenbank ist noch in latin1. Sie müssen zuerst utf8convert.php aufrufen, bevor Sie die Konvertierung nach UTF8MB4 abschließen können.';
} else {
    $lang_setup['convert_welcome_text'] = 'Welcome! This wizard will convert your b1gMail installation from the MySQL <b>UTF8(mb3)</b> encoding to <b>UTF8MB4</b>. <font color="red">Use this converter only if your database is in MySQL UTF8(mb3) encoding!</font> Click at &quot;Next &raquo;&quot; to continue.';
    $lang_setup['convert_alreadyutf8'] = 'You have to first to execute the script utf8convert.php, before execute this script.';
}

function pageHeader2($update = false, $convert = false)
{
    global $lang_setup, $lang, $step;

    header('Content-Type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>b1gMail - <?php echo $lang_setup['setup']; ?></title>
	<link type="text/css" href="res/style.css" rel="stylesheet" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="../clientlib/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="../clientlib/fontawesome/css/font-awesome-animation.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
<center>

	<br />
	<img src="res/shade_top.png" border="0" alt="" />
	<table cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
		<tr>
			<td id="leftshade"></td>
			<td id="main">
				<div id="header">&nbsp;</div>
				<div id="gradient"><i>MySQL UTF8MB4</i>&nbsp;&nbsp;</div>

				<form action="utf8mb4convert.php" method="get"><?php if ($step != STEP_SELECT_LANGUAGE) { ?><input type="hidden" name="lng" value="<?php echo $lang; ?>" /><?php } ?><div id="content">
	<?php
}

// header
if ($step != STEP_CONVERT_STEP) {
    pageHeader2(false, true);
}

/*
 * already utf-8?
 */
if (isset($bm_prefs['db_is_utf8']) && $bm_prefs['db_is_utf8'] == 0) {
    ?>
	<h1><?php echo $lang_setup['error']; ?></h1>

	<?php echo $lang_setup['convert_alreadyutf8']; ?>
	<?php
}

/*
 * welcome
 */
elseif ($step == STEP_WELCOME) {
    $nextStep = STEP_SYSTEMCHECK; ?>
	<h1><?php echo $lang_setup['welcome']; ?></h1>

	<p>
		<?php echo $lang_setup['convert_welcome_text']; ?>
	</p>

	<div style="border:1px solid red;background-color:#efefef;padding:1em;">
		<?php echo $lang_setup['update_note3']; ?>
	</div>
	<?php
}

/*
 * system check
 */
elseif ($step == STEP_SYSTEMCHECK) {
    $nextStep = STEP_CONVERT;

    $result = mysqli_query($connection, 'SELECT VERSION()');
    list($mysqlVersion) = mysqli_fetch_array($result, MYSQLI_NUM);
    mysqli_free_result($result);

    $mysqlVersionOK = in_array(CompareVersions($mysqlVersion, '5.5.3'), [VERSION_IS_NEWER, VERSION_IS_EQUAL]); ?>
	<h1><?php echo $lang_setup['syscheck']; ?></h1>

	<?php echo $lang_setup['convert_syscheck_text']; ?>

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
			<th><?php echo $lang_setup['mysqlversion']; ?></th>
			<td>5.5.3</td>
			<td><?php echo $mysqlVersion; ?></td>
			<td><img src="../admin/templates/images/<?php if ($mysqlVersionOK) {
        echo 'ok';
    } else {
        echo 'error';
        $nextStep = STEP_SYSTEMCHECK;
    } ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
	</table>

	<br />
	<?php echo $nextStep == STEP_CONVERT ? $lang_setup['checkok_text'] : $lang_setup['checkfail_text']; ?>
	<?php
}

/*
 * convert
 */
elseif ($step == STEP_CONVERT) {
    ?>
	<h1><?php echo $lang_setup['converting']; ?></h1>

	<?php echo $lang_setup['converting_text']; ?>

	<br /><br />
	<table class="list">
		<tr>
			<th width="40"></th>
			<th><?php echo $lang_setup['step']; ?></th>
			<th width="180"><?php echo $lang_setup['progress']; ?></th>
		</tr>
		<tr>
			<td id="step_prepare_status">&nbsp;</td>
			<th id="step_prepare_text" style="font-weight:normal;">1. <?php echo $lang_setup['convert_prepare']; ?></th>
			<td id="step_prepare_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_analyzedb_status">&nbsp;</td>
			<th id="step_analyzedb_text" style="font-weight:normal;">2. <?php echo $lang_setup['convert_analyzedb']; ?></th>
			<td id="step_analyzedb_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_preptables_status">&nbsp;</td>
			<th id="step_preptables_text" style="font-weight:normal;">3. <?php echo $lang_setup['convert_prepare_tables']; ?></th>
			<td id="step_preptables_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_collations_status">&nbsp;</td>
			<th id="step_collations_text" style="font-weight:normal;">4. <?php echo $lang_setup['convert_collations']; ?></th>
			<td id="step_collations_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_resetcache_status">&nbsp;</td>
			<th id="step_resetcache_text" style="font-weight:normal;">5. <?php echo $lang_setup['convert_resetcache']; ?></th>
			<td id="step_resetcache_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_complete_status">&nbsp;</td>
			<th id="step_complete_text" style="font-weight:normal;">6. <?php echo $lang_setup['convert_complete']; ?></th>
			<td id="step_complete_progress">&nbsp;</td>
		</tr>
	</table>

	<br />
	<?php echo $lang_setup['converting_text2']; ?>

	<textarea readonly="readonly" class="installLog" id="log" style="display:none;height:150px;"></textarea>
	<br /><br />

	<div align="center" id="done" style="display:none;">
		<b><?php echo $lang_setup['convertdonefinal']; ?></b>
	</div>

	<script>
    var steps = [
        'prepare',
        'analyzedb',
        'preptables',
        'collations',
        'resetcache',
        'complete'
    ];
    var step = -1,
        args = '',
        pos = 0,
        allQ = -1;

    function EBID(f)
    {
        return(document.getElementById(f));
    }

    function Log(txt)
    {
        var log = EBID('log');

        if(log.style.display == 'none')
            log.style.display = '';

        log.value = txt + "\n" + log.value;
    }

    function MakeXMLRequest(url, callback, param)
    {
        var xmlHTTP = false;

        if(typeof(XMLHttpRequest) != "undefined")
        {
            xmlHTTP = new XMLHttpRequest();
        }
        if(!xmlHTTP)
        {
            return(false);
        }
        else
        {
            xmlHTTP.open("GET", url, true);
            if(typeof(callback) == "string")
            {
                xmlHTTP.onreadystatechange = function xh_readyChange()
                    {
                        eval(callback + "(xmlHTTP)");
                    }
            }
            else if(callback != null)
            {
                xmlHTTP.onreadystatechange = function xh_readyChangeCallback()
                    {
                        callback(xmlHTTP, param);
                    }
            }
            xmlHTTP.send(null);
            return(true);
        }
    }

    function _stepStep(e)
    {
        if(e.readyState == 4)
        {
            var response = e.responseText;

            if(response.substr(0, 3) == 'OK:')
            {
                response = response.substr(3);

                if(response == 'DONE')
                {
                    stepInit(step+1);
                }
                else
                {
                    var numbers = response.split('/');
                    if(numbers.length == 2)
                    {
                        if(steps[step] == 'struct2' && allQ == -1)
                            allQ = parseInt(numbers[1]);

                        if(steps[step] == 'struct2')
                            numbers[1] = '' + allQ;

                        pos = parseInt(numbers[0]);
                        EBID('step_' + steps[step] + '_progress').innerHTML = '<b>' + Math.round(pos / parseInt(numbers[1]) * 100) + '%</b> <small>('
                            + pos + ' / ' + parseInt(numbers[1]) + ')</small>';
                        stepStep();
                    }
                    else
                    {
                        Log('Unexpected response - skipping position ' + pos);
                        pos++;
                        stepStep();
                    }
                }
            }
            else
            {
                Log('Unexpected response - skipping position ' + pos);
                pos++;
                stepStep();
            }
        }
        else if(e.readyState < 0 || e.readyState > 4)
        {
            Log('Error in HTTP-Request: ' + e.readyState + ' - Trying again in 10s');
            window.setTimeout('stepStep()', 10000);
        }
    }

    function stepStep()
    {
        MakeXMLRequest('utf8mb4convert.php?' + args + '&step=4&do=' + steps[step] + '&pos=' + pos,
                        _stepStep);
    }

    function stepInit(theStep)
    {
        if(step != -1)
        {
            EBID('step_' + steps[step] + '_status').innerHTML = '<i class="fa fa-check-circle" aria-hidden="true"></i>';
            EBID('step_' + steps[step] + '_progress').innerHTML = '<b>100%</b>';
        }

        if(theStep < steps.length)
        {
            step = theStep;
            EBID('step_' + steps[step] + '_text').innerHTML = '<b>' + EBID('step_' + steps[step] + '_text').innerHTML + '</b>';
            EBID('step_' + steps[step] + '_status').innerHTML = '<i class="fa fa-spinner fa-spin fa-fw"></i>';

            pos = 0;
            stepStep();
        }
        else
        {
            EBID('done').style.display = '';
        }
    }

    function beginConversion()
    {
        stepInit(0);
    }

		window.onload = beginConversion;
	</script>

	<?php
}

/*
 * convert step
 */
elseif ($step == STEP_CONVERT_STEP) {
    $do = $_REQUEST['do'];
    $pos = isset($_REQUEST['pos']) ? (int) $_REQUEST['pos'] : 0;

    //
    // preparation
    //
    if ($do == 'prepare') {
        mysqli_query($connection, 'UPDATE '.DB_INSTALL_PREFIX.'prefs SET `wartung`=\'yes\'');
        mysqli_query($connection, 'DROP TABLE IF EXISTS '.DB_INSTALL_PREFIX.'utf8convert');
        mysqli_query($connection, 'CREATE TABLE '.DB_INSTALL_PREFIX.'utf8convert('
            .'`tabletoconvert` varchar(64) NOT NULL,'
            .'`analyzed` tinyint(4) NOT NULL DEFAULT 0,'
            .'`rows` int(11) NOT NULL DEFAULT 0,'
            .'`done_rows` int(11) NOT NULL DEFAULT 0,'
            .'`convert_data` tinyint(4) NOT NULL DEFAULT 0,'
            .'`fields_to_convert` text NOT NULL DEFAULT 0,'
            .'`primary_keys` text NOT NULL DEFAULT 0,'
            .'`collation_converted` tinyint(4) NOT NULL DEFAULT 0,'
            .'`column_added` tinyint(4) NOT NULL DEFAULT 0,'
            .'`data_converted` tinyint(4) NOT NULL DEFAULT 0,'
            .'`column_removed` tinyint(4) NOT NULL DEFAULT 0,'
            .'PRIMARY KEY(`tabletoconvert`))');

        // get tables
        $tables = [];
        $result = mysqli_query($connection, 'SHOW TABLES');
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
            $table = $row[0];
            if (substr($table, 0, strlen($mysql['prefix'])) == $mysql['prefix']
                && $table != ''.DB_INSTALL_PREFIX.'utf8convert'
                && $table != ''.DB_INSTALL_PREFIX.'spamindex') {
                $tables[] = $table;
            }
        }
        mysqli_free_result($result);

        // write tables to status table
        foreach ($tables as $table) {
            mysqli_query($connection, 'INSERT INTO '.DB_INSTALL_PREFIX.'utf8convert(`tabletoconvert`) VALUES(\''.SQLEscape($table, $connection).'\')');
            //echo 'INSERT INTO '.DB_INSTALL_PREFIX.'utf8convert(`tabletoconvert`) VALUES(\''.SQLEscape($table, $connection).'\')'."\n";
        }

        echo 'OK:DONE';
    }

    //
    // analyze db
    //
    elseif ($do == 'analyzedb') {
        // get table count
        $result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert');
        list($allCount) = mysqli_fetch_array($result, MYSQLI_NUM);
        mysqli_free_result($result);

        // analyze table
        $result = mysqli_query($connection, 'SELECT `tabletoconvert` FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `analyzed`=0 LIMIT 1');
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
            // get row count
            $result2 = mysqli_query($connection, 'SELECT COUNT(*) FROM '.$row[0]);
            list($rowCount) = mysqli_fetch_array($result2, MYSQLI_NUM);
            mysqli_free_result($result2);

            // get fields
            $fieldsToConvert = [];
            $primaryKeys = [];
            $result2 = mysqli_query($connection, 'SHOW FIELDS FROM '.$row[0]);
            while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
                if ((strpos(strtolower($row2['Type']), 'char') !== false
                    || strpos(strtolower($row2['Type']), 'text') !== false)
                    && !($row[0] == $mysql['prefix'].'mails' && $row2['Field'] == 'body')) {
                    $fieldsToConvert[] = $row2['Field'];
                }

                if (strpos($row2['Key'], 'PRI') !== false) {
                    $primaryKeys[] = $row2['Field'];
                }
            }
            mysqli_free_result($result2);

            // convert data?
            $convertData = count($fieldsToConvert) > 0 && (count($primaryKeys) > 0 || $rowCount == 1) && $rowCount > 0;

            // save
            mysqli_query($connection, sprintf('UPDATE '.DB_INSTALL_PREFIX.'utf8convert SET `rows`=%d,`convert_data`=%d,`fields_to_convert`=\'%s\',`primary_keys`=\'%s\',`analyzed`=1 WHERE `tabletoconvert`=\'%s\'',
                            $rowCount,
                            $convertData ? 1 : 0,
                            SQLEscape(implode(';', $fieldsToConvert), $connection),
                            SQLEscape(implode(';', $primaryKeys), $connection),
                            SQLEscape($row[0], $connection)));
        }
        mysqli_free_result($result);

        // get status info
        $result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `analyzed`=1');
        list($doneCount) = mysqli_fetch_array($result, MYSQLI_NUM);
        mysqli_free_result($result);

        if ($allCount == $doneCount) {
            echo 'OK:DONE';
        } else {
            echo 'OK:'.$doneCount.'/'.$allCount;
        }
    }

    //
    // prepare tables
    //
    elseif ($do == 'preptables') {
        // get table count
        $result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `convert_data`=1');
        list($allCount) = mysqli_fetch_array($result, MYSQLI_NUM);
        mysqli_free_result($result);

        // get table to alter
        $result = mysqli_query($connection, 'SELECT `tabletoconvert` FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `column_added`=0 AND `convert_data`=1 LIMIT 1');
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
            // add status column
            mysqli_query($connection, sprintf('ALTER TABLE %s ADD `__converted` tinyint(4) NOT NULL DEFAULT 0',
                $row[0]));

            // update status
            mysqli_query($connection, sprintf('UPDATE '.DB_INSTALL_PREFIX.'utf8convert SET `column_added`=1 WHERE `tabletoconvert`=\'%s\'',
                            SQLEscape($row[0], $connection)));
        }
        mysqli_free_result($result);

        // get status info
        $result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `convert_data`=1 AND `column_added`=1');
        list($doneCount) = mysqli_fetch_array($result, MYSQLI_NUM);
        mysqli_free_result($result);

        if ($allCount == $doneCount) {
            echo 'OK:DONE';
        } else {
            echo 'OK:'.$doneCount.'/'.$allCount;
        }
    }

    //
    // update collations
    //
    elseif ($do == 'collations') {
        // get table count
        $result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert');
        list($allCount) = mysqli_fetch_array($result, MYSQLI_NUM);
        mysqli_free_result($result);

        if ($pos == 0) {
            mysqli_query($connection, 'ALTER DATABASE `'.$mysql['db'].'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            echo 'OK:1/'.($allCount + 1);
        } else {
            $binConvertFields = [
                'char' => 'binary',
                'varchar' => 'varbinary',
                'text' => 'blob',
                'tinytext' => 'tinyblob',
                'mediumtext' => 'mediumblob',
                'longtext' => 'longblob',
            ];

            // get table to alter
            $result = mysqli_query($connection, 'SELECT `tabletoconvert`,`fields_to_convert` FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `collation_converted`=0 LIMIT 1');
            while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                if (trim($row[1]) != '') {
                    $fieldsToConvert = explode(';', $row[1]);

                    // get fields
                    $result2 = mysqli_query($connection, 'SHOW FIELDS FROM '.$row[0]);
                    while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
                        if (!in_array($row2[0], $fieldsToConvert)) {
                            continue;
                        }

                        $fieldType = $row2[1];
                        if (($pos = strpos($fieldType, '(')) !== false) {
                            $fieldType = substr($fieldType, 0, $pos);
                        }

                        if (isset($binConvertFields[strtolower($fieldType)])) {
                            $newFieldType = str_replace($fieldType, $binConvertFields[strtolower($fieldType)], $row2[1]);

                            $query = sprintf('ALTER TABLE %s %s `%s` %s%s%s%s',
                                $row[0],
                                'MODIFY',
                                $row2[0],
                                $newFieldType,
                                $row2[2] == 'NO' ? ' NOT NULL' : '',
                                $row2[4] == 'NULL' ? ' DEFAULT NULL' : ($row2[4] != ''
                                    ? (is_numeric($row2[4])
                                            ? ' DEFAULT '.$row2[4]
                                            : ' DEFAULT \''.SQLEscape($row2[4], $connection).'\'')
                                    : ''),
                                $row2[5] != '' ? ' '.$row2[5] : '');
                            mysqli_query($connection, $query);
                        }

                        $query = sprintf('ALTER TABLE %s %s `%s` %s CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci%s%s%s',
                            $row[0],
                            'MODIFY',
                            $row2[0],
                            $row2[1],
                            $row2[2] == 'NO' ? ' NOT NULL' : '',
                            $row2[4] == 'NULL' ? ' DEFAULT NULL' : ($row2[4] != ''
                                ? (is_numeric($row2[4])
                                        ? ' DEFAULT '.$row2[4]
                                        : ' DEFAULT \''.SQLEscape($row2[4], $connection).'\'')
                                : ''),
                            $row2[5] != '' ? ' '.$row2[5] : '');
                        mysqli_query($connection, $query);
                    }
                    mysqli_free_result($result2);
                }

                // update status
                mysqli_query($connection, sprintf('UPDATE '.DB_INSTALL_PREFIX.'utf8convert SET `collation_converted`=1 WHERE `tabletoconvert`=\'%s\'',
                                SQLEscape($row[0], $connection)));
            }
            mysqli_free_result($result);

            // get status info
            $result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `collation_converted`=1');
            list($doneCount) = mysqli_fetch_array($result, MYSQLI_NUM);
            mysqli_free_result($result);

            if ($allCount == $doneCount) {
                echo 'OK:DONE';
            } else {
                echo 'OK:'.($doneCount + 1).'/'.($allCount + 1);
            }
        }
    }

    //
    // convert lang files
    //
    elseif ($do == 'langfiles') {
        die('OK:DONE');
    }

    //
    // reset cache
    //
    elseif ($do == 'resetcache') {
        $deleteIDs = [];

        $res = mysqli_query($connection, 'SELECT size,`key` FROM '.DB_INSTALL_PREFIX.'file_cache', $connection);
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
            mysqli_query($connection, 'DELETE FROM '.DB_INSTALL_PREFIX.'file_cache WHERE `key` IN(\''.implode('\',\'', $deleteIDs).'\')');
        }

        echo 'OK:DONE';
    }

    //
    // complete
    //
    elseif ($do == 'complete') {
        // get table count
        $result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `column_added`=1');
        list($allCount) = mysqli_fetch_array($result, MYSQLI_NUM);
        mysqli_free_result($result);

        // get table to alter
        $result = mysqli_query($connection, 'SELECT `tabletoconvert` FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `column_added`=1 AND `column_removed`=0 LIMIT 1');
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
            // remove status column
            mysqli_query($connection, sprintf('ALTER TABLE %s DROP `__converted`',
                $row[0]));

            // update status
            mysqli_query($connection, sprintf('UPDATE '.DB_INSTALL_PREFIX.'utf8convert SET `column_removed`=1 WHERE `tabletoconvert`=\'%s\'',
                            SQLEscape($row[0], $connection)));
        }
        mysqli_free_result($result);

        // get status info
        $result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `column_added`=1 AND `column_removed`=1');
        list($doneCount) = mysqli_fetch_array($result, MYSQLI_NUM);
        mysqli_free_result($result);

        if ($allCount == $doneCount) {
            mysqli_query($connection, 'UPDATE '.DB_INSTALL_PREFIX.'prefs SET `wartung`=\'no\'');
            mysqli_query($connection, 'DROP TABLE IF EXISTS '.DB_INSTALL_PREFIX.'utf8convert');

            echo 'OK:DONE';
        } else {
            echo 'OK:'.$doneCount.'/'.$allCount;
        }
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
pageFooter(false, true);

// disconnect
mysqli_close($connection);
?>