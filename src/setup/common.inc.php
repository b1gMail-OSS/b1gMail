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

// error reporting
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// example data
include('./data/example.data.php');

// files and folders that should have write permissions
$writeableFiles = array(
	'serverlib/config.inc.php',
	'serverlib/version.inc.php',
	'admin/templates/cache/',
	'languages/',
	'languages/deutsch.lang.php',
	'languages/english.lang.php',
	'logs/',
	'plugins/',
	'plugins/templates/',
	'plugins/templates/images/',
	'plugins/js/',
	'plugins/css/',
	'temp/',
	'temp/session/',
	'temp/cache/',
	'templates/modern/cache/',
	'data/'
);

// constants
define('VERSION_IS_OLDER',		-1);
define('VERSION_IS_EQUAL',		0);
define('VERSION_IS_NEWER',		1);

/**
 * Encode a (possible non-ASCII) domain to IDN form.
 *
 * @param string $domain
 * @return string
 */
function EncodeDomain($domain)
{
	if(function_exists('idn_to_ascii'))
	{
		$domain = CharsetDecode($domain, false, 'utf8');
		return idn_to_ascii($domain);
	}
	return $domain;
}

/**
 * escape string for use in sql query
 *
 * @param string $str
 * @return string
 */
function SQLEscape($str, $handle)
{
	return(mysqli_real_escape_string($handle, $str));
}

/**
 * compare versions
 *
 * @param string $ver1
 * @param string $ver2
 * @return int
 */
function CompareVersions($ver1, $ver2)
{
	$version1Parts = explode('.', $ver1);
	$version2Parts = explode('.', $ver2);

	$count = max(count($version1Parts), count($version2Parts));

	if(count($version1Parts) < $count)
		$version1Parts = array_pad($version1Parts, $count, 0);
	if(count($version2Parts) < $count)
		$version2Parts = array_pad($version2Parts, $count, 0);

	for($i=0; $i<$count; $i++)
	{
		if($version1Parts[$i] == $version2Parts[$i])
			continue;
		else if($version1Parts[$i] > $version2Parts[$i])
			return(VERSION_IS_NEWER);
		else if($version1Parts[$i] < $version2Parts[$i])
			return(VERSION_IS_OLDER);
	}

	return(VERSION_IS_EQUAL);
}

/**
 * setup page header
 *
 */
function pageHeader($update = false, $convert = false)
{
	global $lang_setup, $lang, $step;

	header('Content-Type: text/html; charset=ISO-8859-1');
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>b1gMail - <?php echo($lang_setup['setup']); ?></title>
	<link type="text/css" href="res/style.css" rel="stylesheet" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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
				<div id="gradient"><i><?php echo($update ? $lang_setup['update'] : ($convert ? $lang_setup['utf8convert'] : $lang_setup['setup'])); ?></i>&nbsp;&nbsp;</div>

				<form action="<?php echo($update ? 'update' : ($convert ? 'utf8convert' : 'index')); ?>.php" method="get"><?php if($step != STEP_SELECT_LANGUAGE) { ?><input type="hidden" name="lng" value="<?php echo($lang); ?>" /><?php } ?><div id="content">
	<?php
}

/**
 * setup page footer
 *
 */
function pageFooter($update = false, $convert = false)
{
	global $lang_setup, $nextStep, $step;

	if(isset($nextStep))
	{
		?>
		<hr size="1" color="#DDDDDD" noshade="noshade" width="100%" />
		<div align="right">
			<?php if($nextStep != -1) { ?><input type="hidden" name="step" value="<?php echo($nextStep); ?>" /><?php } ?>
			<input<?php if((!$update && !$convert && ($step == STEP_UPDATE_UPDATE)) || ($update && $step == STEP_UPDATE) || ($convert && $step == STEP_CONVERT)) echo ' disabled="disabled"';?> id="next_button" type="submit" class="button" value=" <?php echo($lang_setup['next']); ?> &raquo; " />
		</div>
		<?php
	}
	?>
				</div></form>
			</td>
			<td id="rightshade"></td>
		</tr>

	</table>
	<img src="res/shade_bottom.png" border="0" alt="" />

</center>
</body>
</html>
	<?php
}

/**
 * read setup language file
 *
 */
function ReadLanguage()
{
	global $lang, $lang_setup, $step, $exampleData;

	// language?
	if(!isset($_GET['lng']) && !isset($_POST['lng']))
	{
		// try auto detection
		$acceptLanguages = explode(';', str_replace(array(' ', ','), ';', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])));
		$lang = 'english';
		foreach($acceptLanguages as $acceptLanguage)
			if($acceptLanguage == 'de')
			{
				$lang = 'deutsch';
				break;
			}
			else if($acceptLanguage == 'en')
			{
				$lang = 'english';
				break;
			}
	}
	else
		$lang = isset($_GET['lng']) ? $_GET['lng'] : $_POST['lng'];

	// load language
	$lang_setup = array();
	$lang = preg_replace('/[^a-z]/', '', $lang);
	$langFile = './' . $lang . '.lang.php';
	if(!file_exists($langFile))
	{
		$step = STEP_SELECT_LANGUAGE;
		$lang = 'deutsch';
		$langFile = './' . $lang . '.lang.php';
	}
	include($langFile);
}

/**
 * check mysql login
 *
 * @param string $host
 * @param string $user
 * @param string $pass
 * @param string $db
 * @return bool
 */
function CheckMySQLLogin($host, $user, $pass, $db)
{
	$result = false;

	$connection = @mysqli_connect($host, $user, $pass);
	if($connection)
	{
		if(@mysqli_select_db($connection, $db))
			$result = $connection;
	}

	return($result);
}

/**
 * encode a single (possibly international) email address to IDN form
 *
 * @param string $email Email
 * @return string
 */
function EncodeSingleEMail($email)
{
	if(strpos($email, '@') !== false)
	{
		list($localPart, $domainPart) = explode('@', $email);
		$email = $localPart . '@' . EncodeDomain($domainPart);
	}
	return $email;
}

/**
 * check pop3 login
 *
 * @param string $host
 * @param string $user
 * @param string $pass
 * @return bool
 */
function CheckPOP3Login($host, $user, $pass)
{
	$result = false;

	$sock = @fsockopen($host, 110);
	if($sock)
	{
		if(($response = @fgets($sock, 255))
			&& substr($response, 0, 3) == '+OK')
		{
			@fwrite($sock, 'USER ' . EncodeSingleEMail($user) . "\r\n");

			if(($response = @fgets($sock, 255))
				&& substr($response, 0, 3) == '+OK')
			{
				@fwrite($sock, 'PASS ' . $pass . "\r\n");

				if(($response = @fgets($sock, 255))
					&& substr($response, 0, 3) == '+OK')
				{
					$result = true;
				}
			}
		}

		@fwrite($sock, 'QUIT' . "\r\n");
		@fclose($sock);
	}

	return($result);
}

/**
 * password generator
 *
 * @return string
 */
function GeneratePW()
{
	$result = '';
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.,;-_&';
	for($i=0; $i<8; $i++)
		$result .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
	return($result);
}


/**
 * synchronize DB structure against an DB structure array
 *
 * @param resource $connection
 * @param array $databaseStructure (New/correct) DB structure
 * @param bool $return Return queries?
 * @param bool $return Return queries?
 * @return array
 */
function SyncDBStruct($connection, $databaseStructure, $return = true, $utf8Mode = false)
{
	// queries to execute
	$syncQueries = array();

	// get tables
	$defaultTables = array();
	$res = mysqli_query($connection, 'SHOW TABLES');
	while($row = mysqli_fetch_array($res, MYSQLI_NUM))
		$myTables[] = $row[0];
	mysqli_free_result($res);

	// compare tables
	foreach($databaseStructure as $tableName=>$tableInfo)
	{
		$tableFields = $tableInfo['fields'];
		$tableIndexes = $tableInfo['indexes'];

		//
		// table exists => compare fields and indexes
		//
		if(in_array($tableName, $myTables))
		{
			// get my fields
			$myFields = array();
			$res = mysqli_query($connection, 'SHOW FIELDS FROM ' . $tableName);
			while($row = mysqli_fetch_array($res, MYSQLI_ASSOC))
			{
				if($row['Null'] == '') $row['Null'] = 'NO';
				$myFields[$row['Field']] = array($row['Field'], stripslashes($row['Type']), $row['Null'], $row['Key'], $row['Default'], $row['Extra']);
			}
			mysqli_free_result($res);

			// get my indexes
			$myIndexes = array();
			$res = mysqli_query($connection, 'SHOW INDEX FROM ' . $tableName);
			while($row = mysqli_fetch_array($res, MYSQLI_ASSOC))
				if(isset($myIndexes[$row['Key_name']]))
					$myIndexes[$row['Key_name']][] = $row['Column_name'];
				else
					$myIndexes[$row['Key_name']] = array($row['Column_name']);
			mysqli_free_result($res);

			// compare fields
			foreach($tableFields as $field)
			{
				$op = false;

				if(!isset($myFields[$field[0]]))
				{
					$op = 'ADD';
				}
				else
				{
					$myField = $myFields[$field[0]];
					if($myField[1] != $field[1]
						|| $myField[2] != $field[2]
						|| ($myField[4] != $field[4] && !(($myField[4]==0 && $field[4]=='') || ($myField[4]=='' && $field[4]==0)))
						|| $myField[5] != $field[5])
					{
						$op = 'MODIFY';
					}
				}

				if($op !== false)
				{
					$syncQueries[] = sprintf('ALTER TABLE %s %s `%s` %s%s%s%s%s',
						$tableName,
						$op,
						$field[0],
						$field[1],
						$utf8Mode ? (strpos($field[1], 'char') !== false || strpos($field[2], 'text') !== false
										? ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
										: '') : '',
						$field[2] == 'NO' ? ' NOT NULL' : '',
						$field[4] == 'NULL' ? ' DEFAULT NULL' : ($field[4] != ''
							? (is_numeric($field[4])
									? ' DEFAULT ' . $field[4]
									: ' DEFAULT \'' . SQLEscape($field[4], $connection) . '\'')
							: ''),
						$field[5] != '' ? ' ' . $field[5] : '');
				}
			}

			// compare indexes
			foreach($tableIndexes as $indexName=>$indexFields)
			{
				// keys
				if($indexName != 'PRIMARY')
				{
					$op = false;

					if(!isset($myIndexes[$indexName]))
					{
						$op = true;
					}
					else if($myIndexes[$indexName] != $indexFields)
					{
						$op = true;
						$syncQueries[] = sprintf('ALTER TABLE %s DROP KEY `%s`',
							$tableName,
							$indexName);
					}

					if($op)
					{
						$syncQueries[] = sprintf('ALTER TABLE %s ADD KEY `%s`(%s)',
							$tableName,
							$indexName,
							'`' . implode('`,`', $indexFields) . '`');
					}
				}

				// primary keys
				else
				{
					if(!isset($myIndexes[$indexName]))
					{
						// add
						$syncQueries[] = sprintf('ALTER TABLE %s ADD PRIMARY KEY(%s)',
							$tableName,
							'`' . implode('`,`', $indexFields) . '`');
					}
					else if($myIndexes[$indexName] != $indexFields)
					{
						// drop, add
						$syncQueries[] = sprintf('ALTER TABLE %s DROP PRIMARY KEY, ADD PRIMARY KEY(%s)',
							$tableName,
							'`' . implode('`,`', $indexFields) . '`');
					}
				}
			}
		}

		//
		// table does not exist => create
		//
		else
		{
			$stmt = sprintf('CREATE TABLE %s(' . "\n",
				$tableName);

			// fields
			foreach($tableFields as $field)
			{
				$stmt .= sprintf(' `%s` %s%s%s%s%s,' . "\n",
					$field[0],
					$field[1],
					$utf8Mode ? (strpos($field[1], 'char') !== false || strpos($field[2], 'text') !== false
									? ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
									: '') : '',
					$field[2] == 'NO' ? ' NOT NULL' : '',
					$field[4] == 'NULL' ? ' DEFAULT NULL' : ($field[4] != ''
						? (is_numeric($field[4])
								? ' DEFAULT ' . $field[4]
								: ' DEFAULT \'' . SQLEscape($field[4], $connection) . '\'')
						: ''),
					$field[5] != '' ? ' ' . $field[5] : '');
			}

			// indexes
			foreach($tableIndexes as $indexName=>$indexFields)
			{
				if($indexName == 'PRIMARY')
					$stmt .= sprintf(' PRIMARY KEY (%s),' . "\n",
						'`' . implode('`,`', $indexFields) . '`');
				else
					$stmt .= sprintf(' KEY `%s`(%s),' . "\n",
						$indexName,
						'`' . implode('`,`', $indexFields) . '`');
			}

			$stmt = substr($stmt, 0, -2) . "\n" . ')';

			$syncQueries[] = $stmt;
		}
	}

	// return
	if($return)
		return($syncQueries);

	// execute queries
	$result = array();
	foreach($syncQueries as $query)
		if(mysqli_query($connection, $query))
			$result[$query] = true;
		else
			$result[$query] = false;

	// return
	return($result);
}

/**
 * create datbase structure
 *
 * @param resource $connection
 * @param array $databaseStructure
 * @param bool $utf8Mode
 * @return array
 */
function CreateDatabaseStructure($connection, $databaseStructure, $utf8Mode = false, $dbName = '')
{
	// queries to execute
	$syncQueries = array();

	if($utf8Mode && $dbName != '')
		$syncQueries[] = 'ALTER DATABASE `'  . $dbName . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';

	// create tables
	foreach($databaseStructure as $tableName=>$tableInfo)
	{
		$tableFields = $tableInfo['fields'];
		$tableIndexes = $tableInfo['indexes'];

		$stmt = sprintf('CREATE TABLE %s(' . "\n",
			$tableName);

		// fields
		foreach($tableFields as $field)
		{
			$stmt .= sprintf(' `%s` %s%s%s%s%s,' . "\n",
				$field[0],
				$field[1],
				$utf8Mode ? (strpos($field[1], 'char') !== false || strpos($field[2], 'text') !== false
							? ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
							: '') : '',
				$field[2] == 'NO' ? ' NOT NULL' : '',
				$field[4] == 'NULL' ? ' DEFAULT NULL' : ($field[4] != ''
					? (is_numeric($field[4])
							? ' DEFAULT ' . $field[4]
							: ' DEFAULT \'' . SQLEscape($field[4], $connection) . '\'')
					: ''),
				$field[5] != '' ? ' ' . $field[5] : '');
		}

		// indexes
		foreach($tableIndexes as $indexName=>$indexFields)
		{
			if($indexName == 'PRIMARY')
				$stmt .= sprintf(' PRIMARY KEY (%s),' . "\n",
					'`' . implode('`,`', $indexFields) . '`');
			else
				$stmt .= sprintf(' KEY `%s`(%s),' . "\n",
					$indexName,
					'`' . implode('`,`', $indexFields) . '`');
		}

		$stmt = substr($stmt, 0, -2) . "\n" . ')';

		$syncQueries[] = $stmt;
	}

	// execute queries
	$result = array();
	foreach($syncQueries as $query)
		if(mysqli_query($connection, $query))
			$result[$query] = true;
		else
			$result[$query] = mysqli_error($connection);

	// return
	return($result);
}

/**
 * convert string encoding
 *
 * @param string $str String
 * @param string $from In encoding
 * @param string $to Out encoding
 * @return string
 */
function ConvertEncoding($str, $from, $to)
{
	if(function_exists('mb_convert_encoding'))
		return(mb_convert_encoding($str, $to, $from));
	else if(function_exists('iconv'))
		return(iconv($from, $to, $str));
	else if(function_exists('utf8_encode') && strtolower($to) == 'utf-8' && strpos(strtolower($from), 'iso-8859-1') !== false)
		return(utf8_encode($str));
	return($str);
}

/**
 * get language file info
 *
 */
function GetLanguageInfo($fileName)
{
	$result = array();
	$fp = @fopen($fileName, 'r');
	if(is_resource($fp))
	{
		while($line = fgets($fp))
		{
			if(substr($line, 0, strlen('// b1gMailLang::')) == '// b1gMailLang::')
			{
				list(, $langTitle,
						$langAuthor,
						$langAuthorMail,
						$langAuthorWeb,
						$langCharset,
						$langLocale) = explode('::', trim($line));
				$result['ctime'] = filectime($fileName);
				$result['title'] = $langTitle;
				$result['author'] = $langAuthor;
				$result['authorMail'] = $langAuthorMail;
				$result['authorWeb'] = $langAuthorWeb;
				$result['charset'] = $langCharset;
				$result['locale'] = $langLocale;
				$result['writeable'] = is_writeable($fileName);
				$result['langDefLine'] = trim($line);
				break;
			}
		}

		fclose($fp);
	}
	return($result);
}
