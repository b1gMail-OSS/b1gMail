<?php
/**
 * db_struct.php
 * Exports the current database structure to an array
 */
error_reporting(E_ALL);
include('../src/serverlib/config.inc.php');

// result array
$structure = array();

// connect to db
$conn = mysqli_connect($mysql['host'], $mysql['user'], $mysql['pass']);
if(!$conn) die('Cannot connect to database');
mysqli_select_db($conn, $mysql['db'])
	|| die('Cannot select database');

// get tables
$tables = array();
$res = mysqli_query($conn, 'SHOW TABLES');
while($row = mysqli_fetch_array($res, MYSQLI_NUM))
{
	list($tableName) = $row;
	if(substr($tableName, 0, strlen($mysql['prefix'])) == $mysql['prefix']
		&& strpos($tableName, '_mod_taborder') === false
		&& strpos($tableName, '_mod_premium_') === false
		&& strpos($tableName, '_bms_') === false
		&& $tableName !== 'bm60_news'
		&& strpos($tableName, '_tccrn') === false
		&& strpos($tableName, '_tcspc') === false
		&& strpos($tableName, '_tcbrn_plugin_domains') === false
		&& strpos($tableName, '_mod_signatures') === false
		&& strpos($tableName, '_mod_accountmirror') === false
		&& strpos($tableName, '_modfax_') === false)
		$tables[] = $tableName;
}
mysqli_free_result($res);

echo '<div style="float:left;"><h3>Exportierte Tabellen (' . count($tables) . ')</h3>';
echo '<ul>';
foreach($tables as $table)
	echo '<li>' . $table . '</li>';
echo '</ul></div>';

// get field infos 
foreach($tables as $table)
{
	$fields = $indexes = array();
	$res = mysqli_query($conn, 'SHOW FIELDS FROM ' . $table);
	while($row = mysqli_fetch_array($res, MYSQLI_NUM))
	{
		$fields[] = $row;
	}
	mysqli_free_result($res);
	
	$res = mysqli_query($conn, 'SHOW INDEX FROM ' . $table);
	while($row = mysqli_fetch_array($res, MYSQLI_ASSOC))
	{
		if(isset($indexes[$row['Key_name']]))
			$indexes[$row['Key_name']][] = $row['Column_name'];
		else 
			$indexes[$row['Key_name']] = array($row['Column_name']);
	}
	mysqli_free_result($res);
	
	$structure[$table] = array(
		'fields' 	=> $fields,
		'indexes'	=> $indexes
	);
}

// output
$structure = serialize($structure);
$structure = base64_encode($structure);
$structure = wordwrap($structure, 75, "'\n\t. '", true);
echo '<div style="float:right;"><h3>Export-Daten</h3>';
echo '<textarea cols=90 rows=40 readonly=readonly>$databaseStructure = ' . sprintf('% 66s', "// checksum: " . md5($structure)) . "\n\t" . '  \'' . $structure . '\';</textarea>';
echo '</div>';

// clean up
mysqli_close($conn);
