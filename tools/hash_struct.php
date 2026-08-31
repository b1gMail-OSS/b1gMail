<?php

$databaseStructure = file_get_contents(__DIR__.'/../src/serverlib/database.struct.json');
echo hash('sha256', $databaseStructure);