<?php
/*
 * b1gMail admin front controller (pretty URLs)
 */

if(!defined('ADMIN_MODE'))
	define('ADMIN_MODE', true);
define('BM_ROUTE_FRONT', true);

require dirname(__DIR__) . '/serverlib/route-bootstrap.inc.php';
