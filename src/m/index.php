<?php
/*
 * b1gMail — legacy mobile UI (/m) removed.
 * Keep a redirect for bookmarks and old links.
 */

$script = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', (string)$_SERVER['SCRIPT_NAME']) : '/m/index.php';
$dir = rtrim(dirname($script), '/');
$parent = dirname($dir);
if($parent === '/' || $parent === '.' || $parent === '')
	$target = '/';
else
	$target = $parent . '/';

header('Location: ' . $target, true, 301);
exit;
