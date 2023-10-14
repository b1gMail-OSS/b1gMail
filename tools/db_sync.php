<?php
error_reporting(E_ALL);
require '../src/serverlib/init.inc.php';
require '../src/serverlib/database.struct.php';
echo 'Syncing DB...';
$databaseStructure = json_decode($databaseStructure, JSON_OBJECT_AS_ARRAY);
SyncDBStruct($databaseStructure);
echo 'DB synced...';
