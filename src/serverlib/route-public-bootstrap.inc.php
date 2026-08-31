<?php
/*
 * Public front controller bootstrap (NLI/LI).
 */

require_once __DIR__ . '/route.inc.php';

if(!defined('B1GMAIL_INIT'))
{
	$root = dirname(__DIR__);
	if(getcwd() !== $root)
		chdir($root);
	require_once $root . '/serverlib/init.inc.php';
}

require RouteResolvePublicScript();
