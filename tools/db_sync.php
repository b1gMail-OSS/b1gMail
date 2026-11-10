<?php
define('SRC_DIR', dirname(dirname(dirname(__FILE__))) . '/src/');

error_reporting(E_ALL);
require SRC_DIR.'serverlib/init.inc.php';
require SRC_DIR.'serverlib/database.struct.php';
echo 'Syncing DB...';
$databaseStructure = json_decode($databaseStructure, JSON_OBJECT_AS_ARRAY);
SyncDBStruct($databaseStructure);
echo 'DB synced...';
