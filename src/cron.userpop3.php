<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

include('./serverlib/init.inc.php');
include('./serverlib/userpop3gateway.class.php');

// try to prevent abortion
header('Connection: close');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('Expires: Wed, 04 Aug 2004 14:46:00 GMT');
@set_time_limit(0);

// output status
if(!isset($_REQUEST['out']) || $_REQUEST['out'] == 'text')
{
	$str = microtime() . ' - OK';
	header('Content-Length: ' . strlen($str));
	echo($str);
}
else if(isset($_REQUEST['out']) && $_REQUEST['out'] == 'img')
{
	header('Content-Type: image/gif');
	header('Content-Length: ' . filesize('res/dummy.gif'));
	readfile('res/dummy.gif');
}
flush();

// set up lock
function ReleaseUserPOP3Lock()
{
	global $lockFP;

	flock($lockFP, LOCK_UN);
	fclose($lockFP);
}
$lockFileName = B1GMAIL_DIR . 'temp/cron.userpop3.lock';
$lockFP = fopen($lockFileName, 'w+');
if(!flock($lockFP, LOCK_EX | LOCK_NB))
	exit();
register_shutdown_function('ReleaseUserPOP3Lock');

// check if interval time passed
if($bm_prefs['last_userpop3_cron'] < time()-$bm_prefs['cron_interval'])
{
	// get pop3 accounts
	$startTime = time();
	$res = $db->Query('SELECT DISTINCT({pre}pop3.user) FROM {pre}pop3,{pre}users,{pre}gruppen WHERE {pre}gruppen.id={pre}users.gruppe AND {pre}users.id={pre}pop3.user AND {pre}users.gesperrt=\'no\' AND {pre}pop3.last_fetch+{pre}gruppen.ownpop3_interval<? AND {pre}pop3.paused=\'no\'',
		time());
	while($row = $res->FetchArray(MYSQLI_NUM))
	{
		$user = _new('BMUser', array($row[0]));
		$userPOP3Gateway = _new('BMUserPOP3Gateway', array($row[0], &$user));
		$userPOP3Gateway->Run();

		// clean up
		unset($userPOP3Gateway);
		unset($user);
	}
	$res->Free();

	// update last cron run time
	$db->Query('UPDATE {pre}prefs SET last_userpop3_cron=?',
		time());
}
