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
require('./common.inc.php');
require('../serverlib/config.inc.php');

// steps
define('STEP_SELECT_LANGUAGE',		0);
define('STEP_WELCOME',				1);
define('STEP_SYSTEMCHECK',			2);
define('STEP_CONVERT',				3);
define('STEP_CONVERT_STEP',			4);
// other
define('DB_INSTALL_PREFIX', 'bm60_');

// connect to mysql db
if(!($connection = CheckMySQLLogin($mysql['host'], $mysql['user'], $mysql['pass'],
					$mysql['db'])))
{
	die('ERROR:MySQL connection failed');
}

// read prefs
$result = mysqli_query($connection, 'SELECT * FROM '.DB_INSTALL_PREFIX.'prefs LIMIT 1');
$bm_prefs = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);

// step?
if(!isset($_REQUEST['step']))
	$step = STEP_WELCOME;
else
	$step = (int)$_REQUEST['step'];

// read language file
if(!isset($_GET['lng']))
	$_GET['lng'] = strpos($bm_prefs['language'], 'deutsch') !== false ? 'deutsch' : 'english';
ReadLanguage();

// header
if($step != STEP_CONVERT_STEP)
	pageHeader(false, true);

/**
 * already utf-8?
 */
if(isset($bm_prefs['db_is_utf8']) && $bm_prefs['db_is_utf8']==1)
{
	?>
	<h1><?php echo($lang_setup['error']); ?></h1>

	<?php echo($lang_setup['convert_alreadyutf8']); ?>
	<?php
}

/**
 * welcome
 */
else if($step == STEP_WELCOME)
{
	$nextStep = STEP_SYSTEMCHECK;
	?>
	<h1><?php echo($lang_setup['welcome']); ?></h1>

	<p>
		<?php echo($lang_setup['convert_welcome_text']); ?>
	</p>

	<div style="border:1px solid red;background-color:#efefef;padding:1em;">
		<?php echo($lang_setup['update_note3']); ?>
	</div>
	<?php
}

/**
 * system check
 */
else if($step == STEP_SYSTEMCHECK)
{
	$nextStep = STEP_CONVERT;

	$result = mysqli_query($connection, 'SELECT VERSION()');
	list($mysqlVersion) = mysqli_fetch_array($result, MYSQLI_NUM);
	mysqli_free_result($result);

	$mysqlVersionOK = in_array(CompareVersions($mysqlVersion, '4.1.1'), array(VERSION_IS_NEWER, VERSION_IS_EQUAL));
	?>
	<h1><?php echo($lang_setup['syscheck']); ?></h1>

	<?php echo($lang_setup['convert_syscheck_text']); ?>

	<br /><br />
	<table class="list">
		<tr>
			<th width="180">&nbsp;</th>
			<th><?php echo($lang_setup['required']); ?></th>
			<th><?php echo($lang_setup['available']); ?></th>
			<th width="60">&nbsp;</th>
		</tr>
		<tr>
			<th><?php echo($lang_setup['phpversion']); ?></th>
			<td>5.4.0</td>
			<td><?php echo(phpversion()); ?></td>
			<td><img src="../admin/templates/images/<?php if((int)str_replace('.', '', phpversion()) >= 540) echo 'ok'; else { echo 'error'; $nextStep = STEP_SYSTEMCHECK; } ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
		<tr>
			<th><?php echo($lang_setup['mbiconvext']); ?></th>
			<td>mb_string / iconv</td>
			<td><?php echo(function_exists('mb_convert_encoding') ? 'mb_string' : (function_exists('iconv') ? 'iconv' : $lang_setup['no'])); ?></td>
			<td><img src="../admin/templates/images/<?php if(function_exists('mb_convert_encoding') || function_exists('iconv')) echo 'ok'; else { echo 'error'; $nextStep = STEP_SYSTEMCHECK; } ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
		<tr>
			<th><?php echo($lang_setup['mysqlversion']); ?></th>
			<td>4.1.1</td>
			<td><?php echo($mysqlVersion); ?></td>
			<td><img src="../admin/templates/images/<?php if($mysqlVersionOK) echo 'ok'; else { echo 'error'; $nextStep = STEP_SYSTEMCHECK; } ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
		<?php
		$langFiles = array('data/');
		$d = dir('../languages/');
		while($entry = $d->read())
		{
			if($entry == '.' || $entry == '..')
				continue;

			if(substr($entry, -9) == '.lang.php')
				$langFiles[] = 'languages/' . $entry;
		}

		foreach($langFiles as $file)
		{
			?>
		<tr>
			<th><?php echo($file); ?></th>
			<td><?php echo($lang_setup['writeable']); ?></td>
			<td><?php echo(is_writeable('../' . $file) ? $lang_setup['writeable'] : $lang_setup['notwriteable']); ?></td>
			<td><img src="../admin/templates/images/<?php if(is_writeable('../' . $file)) echo 'ok'; else { echo 'error'; $nextStep = STEP_SYSTEMCHECK; } ?>.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
			<?php
		}
		?>
	</table>

	<br />
	<?php echo($nextStep == STEP_CONVERT ? $lang_setup['checkok_text'] : $lang_setup['checkfail_text']);?>
	<?php
}

/**
 * convert
 */
else if($step == STEP_CONVERT)
{
	?>
	<h1><?php echo($lang_setup['converting']); ?></h1>

	<?php echo($lang_setup['converting_text']); ?>

	<br /><br />
	<table class="list">
		<tr>
			<th width="40"></th>
			<th><?php echo($lang_setup['step']); ?></th>
			<th width="180"><?php echo($lang_setup['progress']); ?></th>
		</tr>
		<tr>
			<td id="step_prepare_status">&nbsp;</td>
			<th id="step_prepare_text" style="font-weight:normal;">1. <?php echo($lang_setup['convert_prepare']); ?></th>
			<td id="step_prepare_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_analyzedb_status">&nbsp;</td>
			<th id="step_analyzedb_text" style="font-weight:normal;">2. <?php echo($lang_setup['convert_analyzedb']); ?></th>
			<td id="step_analyzedb_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_preptables_status">&nbsp;</td>
			<th id="step_preptables_text" style="font-weight:normal;">3. <?php echo($lang_setup['convert_prepare_tables']); ?></th>
			<td id="step_preptables_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_convert_status">&nbsp;</td>
			<th id="step_convert_text" style="font-weight:normal;">4. <?php echo($lang_setup['convert_convertdata']); ?></th>
			<td id="step_convert_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_collations_status">&nbsp;</td>
			<th id="step_collations_text" style="font-weight:normal;">5. <?php echo($lang_setup['convert_collations']); ?></th>
			<td id="step_collations_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_langfiles_status">&nbsp;</td>
			<th id="step_langfiles_text" style="font-weight:normal;">6. <?php echo($lang_setup['convert_langfiles']); ?></th>
			<td id="step_langfiles_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_resetcache_status">&nbsp;</td>
			<th id="step_resetcache_text" style="font-weight:normal;">7. <?php echo($lang_setup['convert_resetcache']); ?></th>
			<td id="step_resetcache_progress">&nbsp;</td>
		</tr>
		<tr>
			<td id="step_complete_status">&nbsp;</td>
			<th id="step_complete_text" style="font-weight:normal;">8. <?php echo($lang_setup['convert_complete']); ?></th>
			<td id="step_complete_progress">&nbsp;</td>
		</tr>
	</table>

	<br />
	<?php echo($lang_setup['converting_text2']); ?>

	<textarea readonly="readonly" class="installLog" id="log" style="display:none;height:150px;"></textarea>
	<br /><br />

	<div align="center" id="done" style="display:none;">
		<b><?php echo($lang_setup['convertdonefinal']); ?></b>
	</div>

	<script src="./res/convert.js"></script>
	<script>
	<!--
		window.onload = beginConversion;
	//-->
	</script>

	<?php
}

/**
 * convert step
 */
else if($step == STEP_CONVERT_STEP)
{
	$do = $_REQUEST['do'];
	$pos = isset($_REQUEST['pos']) ? (int)$_REQUEST['pos'] : 0;

	//
	// preparation
	//
	if($do == 'prepare')
	{
		mysqli_query($connection, 'UPDATE '.DB_INSTALL_PREFIX.'prefs SET `wartung`=\'yes\'');
		mysqli_query($connection, 'DROP TABLE IF EXISTS '.DB_INSTALL_PREFIX.'utf8convert');
		mysqli_query($connection, 'CREATE TABLE '.DB_INSTALL_PREFIX.'utf8convert('
			. '`table` varchar(64) NOT NULL,'
			. '`analyzed` tinyint(4) NOT NULL DEFAULT 0,'
			. '`rows` int(11) NOT NULL DEFAULT 0,'
			. '`done_rows` int(11) NOT NULL DEFAULT 0,'
			. '`convert_data` tinyint(4) NOT NULL DEFAULT 0,'
			. '`fields_to_convert` text NOT NULL,'
			. '`primary_keys` text NOT NULL,'
			. '`collation_converted` tinyint(4) NOT NULL DEFAULT 0,'
			. '`column_added` tinyint(4) NOT NULL DEFAULT 0,'
			. '`data_converted` tinyint(4) NOT NULL DEFAULT 0,'
			. '`column_removed` tinyint(4) NOT NULL DEFAULT 0,'
			. 'PRIMARY KEY(`table`))');

		// get tables
		$tables = array();
		$result = mysqli_query($connection, 'SHOW TABLES');
		while($row = mysqli_fetch_array($result, MYSQLI_NUM))
		{
			$table = $row[0];
			if(substr($table, 0, strlen($mysql['prefix'])) == $mysql['prefix']
				&& $table != ''.DB_INSTALL_PREFIX.'utf8convert'
				&& $table != ''.DB_INSTALL_PREFIX.'spamindex')
				$tables[] = $table;
		}
		mysqli_free_result($result);

		// write tables to status table
		foreach($tables as $table)
			mysqli_query($connection, 'INSERT INTO '.DB_INSTALL_PREFIX.'utf8convert(`table`) VALUES(\'' . SQLEscape($table, $connection) . '\')');

		echo 'OK:DONE';
	}

	//
	// analyze db
	//
	else if($do == 'analyzedb')
	{
		// get table count
		$result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert');
		list($allCount) = mysqli_fetch_array($result, MYSQLI_NUM);
		mysqli_free_result($result);

		// analyze table
		$result = mysqli_query($connection, 'SELECT `table` FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `analyzed`=0 LIMIT 1');
		while($row = mysqli_fetch_array($result, MYSQLI_NUM))
		{
			// get row count
			$result2 = mysqli_query($connection, 'SELECT COUNT(*) FROM ' . $row[0]);
			list($rowCount) = mysqli_fetch_array($result2, MYSQLI_NUM);
			mysqli_free_result($result2);

			// get fields
			$fieldsToConvert = array();
			$primaryKeys = array();
			$result2 = mysqli_query($connection, 'SHOW FIELDS FROM ' . $row[0]);
			while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
			{
				if((strpos(strtolower($row2['Type']), 'char') !== false
					|| strpos(strtolower($row2['Type']), 'text') !== false)
					&& !($row[0] == $mysql['prefix'].'mails' && $row2['Field'] == 'body'))
					$fieldsToConvert[] = $row2['Field'];

				if(strpos($row2['Key'], 'PRI') !== false)
					$primaryKeys[] = $row2['Field'];
			}
			mysqli_free_result($result2);

			// convert data?
			$convertData = count($fieldsToConvert) > 0 && (count($primaryKeys) > 0 || $rowCount == 1) && $rowCount > 0;

			// save
			mysqli_query($connection, sprintf('UPDATE '.DB_INSTALL_PREFIX.'utf8convert SET `rows`=%d,`convert_data`=%d,`fields_to_convert`=\'%s\',`primary_keys`=\'%s\',`analyzed`=1 WHERE `table`=\'%s\'',
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

		if($allCount == $doneCount)
			echo 'OK:DONE';
		else
			echo 'OK:' . $doneCount . '/' . $allCount;
	}

	//
	// prepare tables
	//
	else if($do == 'preptables')
	{
		// get table count
		$result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `convert_data`=1');
		list($allCount) = mysqli_fetch_array($result, MYSQLI_NUM);
		mysqli_free_result($result);

		// get table to alter
		$result = mysqli_query($connection, 'SELECT `table` FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `column_added`=0 AND `convert_data`=1 LIMIT 1');
		while($row = mysqli_fetch_array($result, MYSQLI_NUM))
		{
			// add status column
			mysqli_query($connection, sprintf('ALTER TABLE %s ADD `__converted` tinyint(4) NOT NULL DEFAULT 0',
				$row[0]));

			// update status
			mysqli_query($connection, sprintf('UPDATE '.DB_INSTALL_PREFIX.'utf8convert SET `column_added`=1 WHERE `table`=\'%s\'',
							SQLEscape($row[0], $connection)));
		}
		mysqli_free_result($result);

		// get status info
		$result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `convert_data`=1 AND `column_added`=1');
		list($doneCount) = mysqli_fetch_array($result, MYSQLI_NUM);
		mysqli_free_result($result);

		if($allCount == $doneCount)
			echo 'OK:DONE';
		else
			echo 'OK:' . $doneCount . '/' . $allCount;
	}

	//
	// convert data
	//
	else if($do == 'convert')
	{
		// get overall row count
		$result = mysqli_query($connection, 'SELECT SUM(`rows`) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `convert_data`=1');
		list($allCount) = mysqli_fetch_array($result, MYSQLI_NUM);
		mysqli_free_result($result);

		// get table to convert
		$addCount = 0;
		$result = mysqli_query($connection, 'SELECT `table`,`rows`,`done_rows`,`fields_to_convert`,`primary_keys` FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `data_converted`=0 AND `convert_data`=1 AND `table`!=\'tcbms_plugin_search_word\' ORDER BY `table` ASC LIMIT 1');
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			// get done row count
			$addCount = $row['done_rows'];

			// select fields?
			$fieldsToConvert = explode(';', $row['fields_to_convert']);
			$primaryKeys = explode(';', $row['primary_keys']);
			$selectFields = array();
			foreach($fieldsToConvert as $field)
				if($field != '' && !in_array('`'.$field.'`', $selectFields))
					$selectFields[] = '`'.$field.'`';
			foreach($primaryKeys as $field)
				if($field != '' && !in_array('`'.$field.'`', $selectFields))
					$selectFields[] = '`'.$field.'`';

			// get rows
			$result2 = mysqli_query($connection, sprintf('SELECT %s FROM %s WHERE `__converted`=0 LIMIT 500',
									implode(',', $selectFields),
									$row['table']));
			while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
			{
				$query = 'UPDATE ' . $row['table'] . ' SET ';

				foreach($row2 as $fieldName=>$fieldValue)
					if(in_array($fieldName, $fieldsToConvert))
					{
						$convertedValue = ConvertEncoding($fieldValue, 'ISO-8859-15', 'UTF-8');

						if($convertedValue != $fieldValue)
							$query .= sprintf('`%s`=\'%s\', ', $fieldName, SQLEscape($convertedValue, $connection));
					}

				$query .= '`__converted`=1';

				if(count($primaryKeys) > 0)
				{
					$query .= ' WHERE ';

					foreach($primaryKeys as $fieldName)
						$query .= sprintf('`%s`=\'%s\' AND ', $fieldName, SQLEscape($row2[$fieldName], $connection));
				}

				$query = substr($query, 0, -5);
				mysqli_query($connection, $query);

				$addCount++;
			}
			mysqli_free_result($result2);

			// update status?
			if($addCount >= $row['rows'])
			{
				mysqli_query($connection, sprintf('UPDATE '.DB_INSTALL_PREFIX.'utf8convert SET `data_converted`=1,`done_rows`=%d WHERE `table`=\'%s\'',
								$addCount,
								SQLEscape($row['table'], $connection)));
				$addCount = 0;
			}
			else
			{
				mysqli_query($connection, sprintf('UPDATE '.DB_INSTALL_PREFIX.'utf8convert SET `done_rows`=%d WHERE `table`=\'%s\'',
								$addCount,
								SQLEscape($row['table'], $connection)));
			}
		}
		mysqli_free_result($result);

		// get status info
		$result = mysqli_query($connection, 'SELECT SUM(`rows`) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `data_converted`=1 AND `convert_data`=1');
		list($doneCount) = mysqli_fetch_array($result, MYSQLI_NUM);
		mysqli_free_result($result);

		if($allCount == $doneCount)
			echo 'OK:DONE';
		else
			echo 'OK:' . ($doneCount+$addCount) . '/' . $allCount;
	}

	//
	// update collations
	//
	else if($do == 'collations')
	{
		// get table count
		$result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert');
		list($allCount) = mysqli_fetch_array($result, MYSQLI_NUM);
		mysqli_free_result($result);

		if($pos == 0)
		{
			mysqli_query($connection, 'ALTER DATABASE `' . $mysql['db'] . '` CHARACTER SET utf8 COLLATE utf8_general_ci');
			echo 'OK:1/' . ($allCount+1);
		}
		else
		{
			$binConvertFields = array(
				'char'			=> 'binary',
				'varchar'		=> 'varbinary',
				'text'			=> 'blob',
				'tinytext'		=> 'tinyblob',
				'mediumtext'	=> 'mediumblob',
				'longtext'		=> 'longblob'
			);

			// get table to alter
			$result = mysqli_query($connection, 'SELECT `table`,`fields_to_convert` FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `collation_converted`=0 LIMIT 1');
			while($row = mysqli_fetch_array($result, MYSQLI_NUM))
			{
				if(trim($row[1]) != '')
				{
					$fieldsToConvert = explode(';', $row[1]);

					// get fields
					$result2 = mysqli_query($connection, 'SHOW FIELDS FROM ' . $row[0]);
					while($row2 = mysqli_fetch_array($result2, MYSQLI_NUM))
					{
						if(!in_array($row2[0], $fieldsToConvert))
							continue;

						$fieldType = $row2[1];
						if(($pos = strpos($fieldType, '(')) !== false)
							$fieldType = substr($fieldType, 0, $pos);

						if(isset($binConvertFields[strtolower($fieldType)]))
						{
							$newFieldType = str_replace($fieldType, $binConvertFields[strtolower($fieldType)], $row2[1]);

							$query = sprintf('ALTER TABLE %s %s `%s` %s%s%s%s',
								$row[0],
								'MODIFY',
								$row2[0],
								$newFieldType,
								$row2[2] == 'NO' ? ' NOT NULL' : '',
								$row2[4] == 'NULL' ? ' DEFAULT NULL' : ($row2[4] != ''
									? (is_numeric($row2[4])
											? ' DEFAULT ' . $row2[4]
											: ' DEFAULT \'' . SQLEscape($row2[4], $connection) . '\'')
									: ''),
								$row2[5] != '' ? ' ' . $row2[5] : '');
							mysqli_query($connection, $query);
						}

						$query = sprintf('ALTER TABLE %s %s `%s` %s CHARACTER SET utf8 COLLATE utf8_general_ci%s%s%s',
							$row[0],
							'MODIFY',
							$row2[0],
							$row2[1],
							$row2[2] == 'NO' ? ' NOT NULL' : '',
							$row2[4] == 'NULL' ? ' DEFAULT NULL' : ($row2[4] != ''
								? (is_numeric($row2[4])
										? ' DEFAULT ' . $row2[4]
										: ' DEFAULT \'' . SQLEscape($row2[4], $connection) . '\'')
								: ''),
							$row2[5] != '' ? ' ' . $row2[5] : '');
						mysqli_query($connection, $query);
					}
					mysqli_free_result($result2);
				}

				// update status
				mysqli_query($connection, sprintf('UPDATE '.DB_INSTALL_PREFIX.'utf8convert SET `collation_converted`=1 WHERE `table`=\'%s\'',
								SQLEscape($row[0], $connection)));
			}
			mysqli_free_result($result);

			// get status info
			$result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `collation_converted`=1');
			list($doneCount) = mysqli_fetch_array($result, MYSQLI_NUM);
			mysqli_free_result($result);

			if($allCount == $doneCount)
				echo 'OK:DONE';
			else
				echo 'OK:' . ($doneCount+1) . '/' . ($allCount+1);
		}
	}

	//
	// convert lang files
	//
	else if($do == 'langfiles')
	{
		$langFiles = array();
		$d = dir('../languages/');
		while($entry = $d->read())
		{
			if($entry == '.' || $entry == '..')
				continue;

			if(substr($entry, -9) == '.lang.php')
				$langFiles[] = '../languages/' . $entry;
		}

		foreach($langFiles as $file)
		{
			$info = GetLanguageInfo($file);
			if(!isset($info['charset']))
				continue;

			$charset = strtolower($info['charset']);
			if($charset == 'utf8' || $charset == 'utf-8')
				continue;

			// back up
			$backupFileName = '../data/' . preg_replace('/^(.*)\//', '', $file) . '.utf8-convert-backup.php';
			if(!@copy($file, $backupFileName))
				continue;

			// read file contents
			$fp = fopen($file, 'rb+');
			if(!$fp || !is_resource($fp))
				continue;
			$contents = fread($fp, filesize($file));

			// convert contents to utf-8
			$contents = ConvertEncoding($contents, $charset, 'UTF-8');

			// manipulate locales
			$locales = array();
			$oldLocales = explode('|', $info['locale']);
			foreach($oldLocales as $locale)
			{
				$locale = preg_replace('/\..*/i', '.UTF-8', $locale);
				if(!in_array($locale, $locales))
					$locales[] = $locale;
			}

			// manipulate lang def line
			$newLangDef = sprintf('// b1gMailLang::%s::%s::%s::%s::UTF-8::%s',
				$info['title'],
				$info['author'],
				$info['authorMail'],
				$info['authorWeb'],
				implode('|', $locales));
			$contents = str_replace($info['langDefLine'], $newLangDef . "\n" . '// Converted to UTF-8 by setup/utf8convert.php at ' . date('r'), $contents);

			// save
			fseek($fp, 0, SEEK_SET);
			ftruncate($fp, 0);
			fwrite($fp, $contents);
			fclose($fp);
		}

		die('OK:DONE');
	}

	//
	// reset cache
	//
	else if($do == 'resetcache')
	{
		$deleteIDs = array();

		$res = mysqli_query($connection, 'SELECT size,`key` FROM '.DB_INSTALL_PREFIX.'file_cache', $connection);
		while($row = mysqli_fetch_array($res, MYSQLI_ASSOC))
		{
			$fileName = '../temp/cache/' . $row['key'] . '.cache';
			if(file_exists($fileName))
				@unlink($fileName);
			$fileName = '../temp/' . $row['key'] . '.cache';
			if(file_exists($fileName))
				@unlink($fileName);
			$deleteIDs[] = $row['key'];
		}
		mysqli_free_result($res);

		if(count($deleteIDs) > 0)
			mysqli_query($connection, 'DELETE FROM '.DB_INSTALL_PREFIX.'file_cache WHERE `key` IN(\'' . implode('\',\'', $deleteIDs) . '\')');

		echo 'OK:DONE';
	}

	//
	// complete
	//
	else if($do == 'complete')
	{
		// get table count
		$result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `column_added`=1');
		list($allCount) = mysqli_fetch_array($result, MYSQLI_NUM);
		mysqli_free_result($result);

		// get table to alter
		$result = mysqli_query($connection, 'SELECT `table` FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `column_added`=1 AND `column_removed`=0 LIMIT 1');
		while($row = mysqli_fetch_array($result, MYSQLI_NUM))
		{
			// remove status column
			mysqli_query($connection, sprintf('ALTER TABLE %s DROP `__converted`',
				$row[0]));

			// update status
			mysqli_query($connection, sprintf('UPDATE '.DB_INSTALL_PREFIX.'utf8convert SET `column_removed`=1 WHERE `table`=\'%s\'',
							SQLEscape($row[0], $connection)));
		}
		mysqli_free_result($result);

		// get status info
		$result = mysqli_query($connection, 'SELECT COUNT(*) FROM '.DB_INSTALL_PREFIX.'utf8convert WHERE `column_added`=1 AND `column_removed`=1');
		list($doneCount) = mysqli_fetch_array($result, MYSQLI_NUM);
		mysqli_free_result($result);

		if($allCount == $doneCount)
		{
			mysqli_query($connection, 'ALTER TABLE '.DB_INSTALL_PREFIX.'prefs ADD `db_is_utf8` tinyint(4) NOT NULL DEFAULT 0');
			mysqli_query($connection, 'UPDATE '.DB_INSTALL_PREFIX.'prefs SET `wartung`=\'no\',`db_is_utf8`=1');
			mysqli_query($connection, 'DROP TABLE IF EXISTS '.DB_INSTALL_PREFIX.'utf8convert');

			echo 'OK:DONE';
		}
		else
			echo 'OK:' . $doneCount . '/' . $allCount;
	}

	//
	// unknown action
	//
	else
	{
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