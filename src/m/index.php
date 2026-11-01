<?php
/*
 * b1gMail — legacy mobile UI (/m) removed.
 * Keep a redirect for bookmarks and old links.
 */

$uri = isset($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : '/m/';
$path = parse_url($uri, PHP_URL_PATH);
if(!is_string($path) || $path === '')
	$path = '/m/';

// Strip /m or /m/... → install prefix ( "/" or "/subdir/" ).
$target = preg_replace('#/m(?:/.*)?$#', '/', $path);
if(!is_string($target) || $target === '' || $target[0] !== '/')
	$target = '/';

// Never emit a filesystem path as Location.
if(strpos($target, '/var/') === 0 || preg_match('#^/[a-z]+/www/#', $target))
	$target = '/';

header('Location: ' . $target, true, 301);
exit;
