<?php
error_reporting(E_ALL);
require '../src/serverlib/init.inc.php';
require '../src/serverlib/database.struct.php';
echo 'Syncing DB...';
$databaseStructure = unserialize(base64_decode($databaseStructure));
SyncDBStruct($databaseStructure);
echo 'DB synced...';