<?php
/*
 * b1gMail – user profile avatar (custom upload)
 */

if(!defined('B1GMAIL_INIT'))
	require './serverlib/init.inc.php';

if(!RequestPrivileges(PRIVILEGES_USER, true))
{
	header('HTTP/1.1 403 Forbidden');
	exit();
}

$size = isset($_REQUEST['size']) ? (int)$_REQUEST['size'] : 32;
AvatarOutputImage('user', (int)$userRow['id'], $size);
