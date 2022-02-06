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

include('../serverlib/admin.inc.php');
RequestPrivileges(PRIVILEGES_ADMIN);

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'welcome';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['welcome'],
		'relIcon'	=> 'ico_license.png',
		'link'		=> 'welcome.php?',
		'active'	=> $_REQUEST['action'] == 'welcome'
	),
	1 => array(
		'title'		=> $lang_admin['phpinfo'],
		'relIcon'	=> 'phpinfo32.png',
		'link'		=> 'welcome.php?action=phpinfo&',
		'active'	=> $_REQUEST['action'] == 'phpinfo'
	),
	2 => array(
		'title'		=> $lang_admin['about'],
		'relIcon'	=> 'ico_b1gmail.png',
		'link'		=> 'welcome.php?action=about&',
		'active'	=> $_REQUEST['action'] == 'about'
	)
);

if($adminRow['type'] != 0)
	unset($tabs[1]);

if($_REQUEST['action'] == 'welcome')
{
	//
	// actions
	//
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'saveNotes')
	{
		$db->Query('UPDATE {pre}admins SET notes=? WHERE adminid=?',
			$_REQUEST['notes'],
			$adminRow['adminid']);
		$adminRow['notes'] = $_REQUEST['notes'];
	}

	//
	// license
	//
	$tpl->assign('version', B1GMAIL_VERSION);

	//
	// overview
	//

	// get table status
	$dbSize = $freeableSpace = $tableCount = 0;
	$tables = array();
	$res = $db->Query('SHOW TABLE STATUS');
	while($row = $res->FetchArray(MYSQLI_ASSOC))
	{
		if(substr($row['Name'], 0, strlen($mysql['prefix'])) != $mysql['prefix'])
			continue;

		$dbSize += $row['Data_length'] + $row['Index_length'];

		if($row['Engine'] != 'InnoDB')
			$freeableSpace += $row['Data_free'];

		$tableCount++;

		$tables[$row['Name']] = array(
			'index_length'	=> $row['Index_length'],
			'data_length'	=> $row['Data_length'],
			'rows'			=> $row['Rows']
		);
	}
	$res->Free();

	// count rows
	$userCount = BMUser::GetUserCount();
	$res = $db->Query('SELECT COUNT(*) FROM {pre}users WHERE gesperrt=?',
		'locked');
	list($notActivatedUserCount) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();
	$res = $db->Query('SELECT COUNT(*) FROM {pre}users WHERE gesperrt=?',
		'yes');
	list($lockedUserCount) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();

	$emailCount = $tables[$mysql['prefix'].'mails']['rows'];
	if($emailCount < 20000)
	{
		$res = $db->Query('SELECT COUNT(*),SUM(size) FROM {pre}mails WHERE `userid`!=-1');
		list($emailCount, $emailSize) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
	}
	else
	{
		$res = $db->Query('SELECT SUM(mailspace_used) FROM {pre}users');
		list($emailSize) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
	}

	$folderCount = $tables[$mysql['prefix'].'folders']['rows'];
	$diskFileCount = $tables[$mysql['prefix'].'diskfiles']['rows'];

	if($diskFileCount < 20000)
	{
		$res = $db->Query('SELECT COUNT(*),SUM(size) FROM {pre}diskfiles');
		list($diskFileCount, $diskSize) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
	}
	else
		$diskSize = false;

	$diskFolderCount = $tables[$mysql['prefix'].'diskfolders']['rows'];

	// load avg
	$loadAvg = '<i>(' . $lang_admin['unknown'] . ')</i>';
	if(!SERVER_WINDOWS)
	{
		if(@file_exists('/usr/bin/uptime'))			$uptime = @exec('/usr/bin/uptime');
		else if(@file_exists('/bin/uptime'))		$uptime = @exec('/bin/uptime');
		else if(@file_exists('/usr/sbin/uptime'))	$uptime = @exec('/usr/sbin/uptime');
		else if(@file_exists('/sbin/uptime'))		$uptime = @exec('/sbin/uptime');
		else 										$uptime = @exec('uptime');

		if($uptime)
		{
   		 	if(preg_match("/([0-9\.\,]+),[\s]+([0-9\.\,]+),[\s]+([0-9\.\,]+)/", $uptime, $match))
    				$loadAvg = implode(' ', array_slice($match, 1));
    			else if(preg_match("/([0-9\.\,]+)[\s]+([0-9\.\,]+)[\s]+([0-9\.\,]+)/", $uptime, $match))
    				$loadAvg = implode(' ', array_slice($match, 1));
		}
	}

	// assign values
	$tpl->assign('loadAvg', $loadAvg);
	$tpl->assign('userCount', $userCount);
	$tpl->assign('notActivatedUserCount', $notActivatedUserCount);
	$tpl->assign('lockedUserCount', $lockedUserCount);
	$tpl->assign('emailSize', $emailSize);
	$tpl->assign('emailCount', $emailCount);
	$tpl->assign('folderCount', $folderCount);
	$tpl->assign('diskFileCount', $diskFileCount);
	$tpl->assign('diskFolderCount', $diskFolderCount);
	$tpl->assign('diskSize', $diskSize);
	$tpl->assign('tableCount', $tableCount);
	$tpl->assign('webserver', explode(' ', $_SERVER['SERVER_SOFTWARE'])[0]);
	$tpl->assign('phpVersion', phpversion());
	$tpl->assign('mysqlVersion', $db->GetServerVersion());
	$tpl->assign('dbSize', $dbSize);

	//
	// system notices
	//
	$notices = array();

	// self folder?
	if(!file_exists($bm_prefs['selffolder']))
		$notices[] = array('type'	=> 'error',
							'text'	=> sprintf($lang_admin['invalidselffolder'], $bm_prefs['selffolder']),
							'link'	=> 'prefs.common.php?');

	// data folder?
	if(!file_exists(B1GMAIL_DATA_DIR))
		$notices[] = array('type'	=> 'error',
							'text'	=> sprintf($lang_admin['invaliddata'], B1GMAIL_DATA_DIR),
							'link'	=> 'prefs.common.php?');
	else if(!is_writeable(B1GMAIL_DATA_DIR))
		$notices[] = array('type'	=> 'error',
							'text'	=> sprintf($lang_admin['dataperms'], B1GMAIL_DATA_DIR));
	// struct storage?
	if($bm_prefs['structstorage'] == 'yes' && ini_get('safe_mode'))
		$notices[] = array('type'	=> 'error',
							'text'	=> $lang_admin['structsafewarn'],
							'link'	=> 'prefs.common.php?');

	// dynamic recipient detection but no receive rules
	if($bm_prefs['recipient_detection'] == 'dynamic')
	{
		$res = $db->Query('SELECT COUNT(*) FROM {pre}recvrules WHERE type=?',
						  RECVRULE_TYPE_RECEIVERULE);
		list($recvRuleCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		if($recvRuleCount == 0)
		{
			$notices[] = array('type'	=> 'error',
								'text'	=> $lang_admin['dynnorecvrules'],
								'link'	=> 'prefs.email.php?action=receive&');
		}
	}

	// permissions
	$brokenPermissions = array();
	foreach($writeableFiles as $item)
		if(!is_writeable(B1GMAIL_REL . $item))
			$brokenPermissions[] = $item;
	if(count($brokenPermissions) > 0)
		$notices[] = array('type'	=> 'warning',
							'text'	=> sprintf($lang_admin['brokenperms'], implode(', ', $brokenPermissions)));

	// htaccess files
	$brokenHTAccess = array();
	foreach($htaccessFiles as $item)
		if(!file_exists($item))
			$brokenHTAccess[] = $item;
	if(count($brokenHTAccess) > 0)
		$notices[] = array('type'	=> 'warning',
							'text'	=> sprintf($lang_admin['brokenhtaccess'], implode(', ', $brokenHTAccess)));

	// orphaned mails?
 	$orphansFound = false;
	if($emailCount < 20000)
	{
		$res = $db->Query('SELECT COUNT(*),SUM(`size`) FROM {pre}mails WHERE `userid`!=-1 AND `userid` NOT IN(SELECT `id` FROM {pre}users)');
		list($orphanCount, $orphanSize) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		if($orphanCount > 0)
		{
			$notices[] = array('type' 	=> 'warning',
								'text'	=> sprintf($lang_admin['orphansfound'], $orphanCount, $orphanSize/1024),
								'link'	=> 'maintenance.php?action=orphans&');
			$orphansFound = true;
		}
	}

	// orphaned files?
 	$diskOrphansFound = false;
	if($diskFileCount < 20000)
	{
		$res = $db->Query('SELECT COUNT(*),SUM(`size`) FROM {pre}diskfiles WHERE `user`!=-1 AND `user` NOT IN(SELECT `id` FROM {pre}users)');
		list($diskOrphanCount, $diskOrphanSize) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		if($diskOrphanCount > 0)
		{
			$notices[] = array('type' 	=> 'warning',
								'text'	=> sprintf($lang_admin['diskorphansfound'], $diskOrphanCount, $diskOrphanSize/1024),
								'link'	=> 'maintenance.php?action=orphans&');
			$diskOrphansFound = true;
		}
	}

	if(!$orphansFound && !$diskOrphansFound && $emailSize !== false && $diskSize !== false)
	{
		// caches out of sync?
		$res = $db->Query('SELECT SUM(mailspace_used),SUM(diskspace_used) FROM {pre}users');
		list($userMailSpace, $userDiskSpace) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		if((int)$userMailSpace != (int)$emailSize || (int)$userDiskSpace != (int)$diskSize)
			$notices[] = array('type'	=> 'warning',
								'text'	=> $lang_admin['cachesizesdiffer'],
								'link'	=> 'optimize.php?action=cache&');
	}

	// no postmaster?
	if(BMUser::GetID(GetPostmasterMail(), true) == 0)
		$notices[] = array('type'	=> 'warning',
							'text'	=> sprintf($lang_admin['nopostmaster'], DecodeEMail(GetPostmasterMail())),
							'link'	=> 'users.php?action=create&');

	// dom?
	if(!class_exists('DOMDocument'))
		$notices[] = array('type'	=> 'warning',
							'text'	=> $lang_admin['domdocument']);

	// maintenance mode?
	if($bm_prefs['wartung'] == 'yes')
		$notices[] = array('type'	=> 'info',
							'text'	=> $lang_admin['maintmodenote'],
							'link'	=> 'prefs.common.php?');

	// struct storage recommended?
	if($bm_prefs['structstorage'] == 'no' && !ini_get('safe_mode'))
		$notices[] = array('type'	=> 'info',
							'text'	=> $lang_admin['structrec'],
							'link'	=> 'prefs.common.php?');

	// gd?
	if(!function_exists('imagecreate'))
		$notices[] = array('type'	=> 'info',
							'text'	=> $lang_admin['gdlib']);

	// idn?
	if(!IDN_SUPPORT)
		$notices[] = array('type'	=> 'info',
							'text'	=> $lang_admin['idnlib']);

	// iconv/mbstring?
	if(!function_exists('mb_convert_encoding') && !function_exists('iconv'))
		$notices[] = array('type'	=> 'info',
							'text'	=> $lang_admin['mbstring']);

	// debug mode?
	if(DEBUG)
		$notices[] = array('type'	=> 'info',
							'text'	=> $lang_admin['debugmode']);

	// db optimizable?
	if($freeableSpace > 1024*1024*5)
		$notices[] = array('type'	=> 'info',
							'text'	=> sprintf($lang_admin['couldfree'], $freeableSpace/1024/1024),
							'link'	=> 'optimize.php?');

	// many logs?
	$logCount = $tables[$mysql['prefix'].'logs']['rows'];
	if($logCount > 250000)
		$notices[] = array('type'	=> 'info',
							'text'	=> $lang_admin['manylogs'],
							'link'	=> 'logs.php?action=archiving&');

	// orders with custom payment methods waiting for activation
	$res = $db->Query('SELECT COUNT(*) FROM {pre}orders WHERE `paymethod`<0 AND `status`=?',
		ORDER_STATUS_CREATED);
	list($orderCount) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();
	if($orderCount > 0)
		$notices[] = array('type' 	=> 'info',
							'text'	=> sprintf($lang_admin['waitingorders'], $orderCount),
							'link'	=> 'payments.php?');

	// users who are not locked and exceeded the abuse points hard limit
	$apHardCount = $apMediumCount = 0;
	$res = $db->Query('SELECT SUM(`points`) FROM {pre}users '
		. 'INNER JOIN {pre}abuse_points ON {pre}abuse_points.`userid`={pre}users.`id` '
		. 'WHERE `expired`=0 AND `gesperrt`=\'no\' '
		. 'GROUP BY {pre}users.`id`');
	while($row = $res->FetchArray(MYSQLI_NUM))
	{
		if($row[0] >= $bm_prefs['ap_hard_limit'])
			$apHardCount++;
		else if($row[0] >= $bm_prefs['ap_medium_limit'])
			$apMediumCount++;
	}
	$res->Free();
	if($apHardCount > 0)
		$notices[] = array('type' 	=> 'warning',
							'text'	=> sprintf($lang_admin['ap_warn_hard'], $apHardCount),
							'link'	=> 'abuse.php?');
	if($apMediumCount > 0)
		$notices[] = array('type' 	=> 'info',
							'text'	=> sprintf($lang_admin['ap_warn_medium'], $apMediumCount),
							'link'	=> 'abuse.php?');

	// users waiting for activation
	if($notActivatedUserCount > 0)
		$notices[] = array('type'	=> 'info',
							'text'	=> sprintf($lang_admin['notactnotice'], $notActivatedUserCount),
							'link'	=> 'users.php?filter=true&statusNotActivated=true&allGroups=true&');

	// users waiting for delete
	$res = $db->Query('SELECT COUNT(*) FROM {pre}users WHERE gesperrt=?',
		'delete');
	list($deleteCount) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();
	if($deleteCount > 0)
		$notices[] = array('type'	=> 'info',
							'text'	=> sprintf($lang_admin['deletenotice'], $deleteCount),
							'link'	=> 'users.php?filter=true&statusDeleted=true&allGroups=true&');

	//
	// groups with maxsize > hard limit
	//
	$res = $db->Query('SELECT `id`,`titel`,`maxsize` FROM {pre}gruppen WHERE `maxsize`>? ORDER BY `titel` ASC',
		$bm_prefs['mailmax']);
	while($row = $res->FetchArray(MYSQLI_ASSOC))
	{
		$notices[] = array('type'	=> 'warning',
							'text'	=> sprintf($lang_admin['maxsizewarning'], HTMLFormat($row['titel']),
								$row['maxsize']/1024,
								$bm_prefs['mailmax']/1024),
							'link'	=> 'groups.php?do=edit&id='.$row['id'].'&');
	}
	$res->Free();

	//
	// plugin notices
	//
	$pluginNotices = $plugins->callFunction('getNotices', false, true);
	foreach($pluginNotices as $value)
		$notices = array_merge($notices, $value);
	$tpl->assign('notices', $notices);

	//
	// misc
	//
	$tpl->assign('bm_prefs', $bm_prefs);
	$tpl->assign('showActivation', $bm_prefs['enable_vk'] == 'yes'
									&& AdminAllowed('payments'));
	$tpl->assign('lang', $currentLanguage);
	$tpl->assign('serial', $bm_prefs['serial']);
	$tpl->assign('notes', $adminRow['notes']);
	$tpl->assign('page', 'welcome.tpl');
}
else if($_REQUEST['action'] == 'about')
{
	$tpl->assign('version', B1GMAIL_VERSION);
	$tpl->assign('page', 'about.tpl');
}
else if($_REQUEST['action'] == 'phpinfo' && $adminRow['type'] == 0)
{
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'phpinfo')
	{
		if(isset($_REQUEST['download']))
		{
			header('Content-Disposition: attachment; filename="PHPInfo.html"');
			header('Pragma: public');
		}

		phpinfo();
		exit();
	}

	$tpl->assign('page', 'welcome.phpinfo.tpl');
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['welcome']);
$tpl->display('page.tpl');
?>