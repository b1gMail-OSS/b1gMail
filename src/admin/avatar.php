<?php
/*
 * b1gMail – admin profile avatar (custom upload)
 */

if(!defined('B1GMAIL_INIT'))
	include '../serverlib/admin.inc.php';

if(!RequestPrivileges(PRIVILEGES_ADMIN, true))
{
	header('HTTP/1.1 403 Forbidden');
	exit();
}

$size = isset($_REQUEST['size']) ? (int)$_REQUEST['size'] : 32;
AvatarOutputImage('admin', (int)$adminRow['adminid'], $size);
