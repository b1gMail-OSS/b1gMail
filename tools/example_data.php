<?php
/**
 * db_struct.php
 * Exports the current database structure to an array
 */

error_reporting(E_ALL);
include('../src/serverlib/config.inc.php');

// tables
$tables = array('bm60_faq', 'bm60_extensions', 'bm60_staaten', 'bm60_mods');

// connect to db
$conn = mysqli_connect($mysql['host'], $mysql['user'], $mysql['pass']);
if(!$conn) die('Cannot connect to database');
mysqli_select_db($conn, $mysql['db'])
	|| die('Cannot select database');

// tables
foreach($tables as $table)
{
	$result = mysqli_query($conn, 'SELECT * FROM ' . $table);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$fields = $values = array();
		foreach($row as $field=>$value)
		{
			$value = mysqli_escape_string($conn, $value);
			$value = str_replace("\n", "\\n", $value);
			$value = str_replace("\r", "\\r", $value);
			$fields[] = '`' . $field . '`';
			$values[] = '\\\'' . $value . '\\\'';
		}
		
		printf('$exampleData[] = \'INSERT INTO %s(%s) VALUES(%s)\';' . "\n",
			$table,
			implode(',', $fields),
			implode(',', $values));
	}
	mysqli_free_result($result);
}

// clean up
mysqli_close($conn);
