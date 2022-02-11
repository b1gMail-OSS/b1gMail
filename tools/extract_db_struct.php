<?php
error_reporting(E_ALL);
require '../src/serverlib/database.struct.php';
echo json_encode(unserialize(base64_decode($databaseStructure)), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);