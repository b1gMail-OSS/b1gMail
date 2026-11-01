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

if($_REQUEST['action'] == 'about')
{
	header('Location: ' . SessionUrl('welcome.php?open=about'));
	exit;
}

if($_REQUEST['action'] == 'phpinfo')
{
	if($adminRow['type'] != 0)
	{
		header('Location: ' . SessionUrl('welcome.php'));
		exit;
	}

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'phpinfo')
	{
		if(isset($_REQUEST['download']))
		{
			header('Content-Disposition: attachment; filename="PHPInfo.html"');
			header('Pragma: public');
			phpinfo();
			exit();
		}

		$theme = (isset($_REQUEST['theme']) && $_REQUEST['theme'] === 'dark') ? 'dark' : 'light';
		ob_start();
		phpinfo();
		$html = ob_get_clean();
		echo AdminStylePhpinfo($html, $theme);
		exit();
	}

	header('Location: ' . SessionUrl('welcome.php?open=phpinfo'));
	exit;
}

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

	$mailReceiveSeries = array();
	$mailSendSeries = array();
	$mailDayIndex = array();
	$mailHistoryDays = 180;
	for($i = $mailHistoryDays - 1; $i >= 0; $i--)
	{
		$ts = mktime(0, 0, 0, (int)date('n'), (int)date('j') - $i, (int)date('Y'));
		$key = date('Y-n-j', $ts);
		$mailDayIndex[$key] = count($mailReceiveSeries);
		$mailReceiveSeries[] = 0;
		$mailSendSeries[] = 0;
	}
	$firstDay = mktime(0, 0, 0, (int)date('n'), (int)date('j') - ($mailHistoryDays - 1), (int)date('Y'));
	$res = $db->Query('SELECT y,m,d,typ,SUM(anzahl) AS anzahlSum FROM {pre}stats WHERE typ IN (\'receive\',\'send\') AND ((y=? AND m=? AND d>=?) OR (y=? AND m>?) OR y>?) GROUP BY y,m,d,typ',
		(int)date('Y', $firstDay),
		(int)date('n', $firstDay),
		(int)date('j', $firstDay),
		(int)date('Y', $firstDay),
		(int)date('n', $firstDay),
		(int)date('Y', $firstDay));
	while($row = $res->FetchArray(MYSQLI_ASSOC))
	{
		$key = (int)$row['y'] . '-' . (int)$row['m'] . '-' . (int)$row['d'];
		if(!isset($mailDayIndex[$key]))
			continue;
		$idx = $mailDayIndex[$key];
		if($row['typ'] == 'receive')
			$mailReceiveSeries[$idx] = (int)$row['anzahlSum'];
		else if($row['typ'] == 'send')
			$mailSendSeries[$idx] = (int)$row['anzahlSum'];
	}
	$res->Free();

	$logTodayStart = mktime(0, 0, 0, (int)date('n'), (int)date('j'), (int)date('Y'));
	$logTodayEnd = mktime(23, 59, 59, (int)date('n'), (int)date('j'), (int)date('Y'));
	$logToday = array(
		PRIO_NOTE => 0,
		PRIO_WARNING => 0,
		PRIO_ERROR => 0
	);
	$res = $db->Query('SELECT prio, COUNT(*) AS c FROM {pre}logs WHERE zeitstempel>=? AND zeitstempel<=? AND prio IN (?,?,?) GROUP BY prio',
		$logTodayStart,
		$logTodayEnd,
		PRIO_WARNING,
		PRIO_NOTE,
		PRIO_ERROR);
	while($row = $res->FetchArray(MYSQLI_ASSOC))
		$logToday[(int)$row['prio']] = (int)$row['c'];
	$res->Free();
	$logTodayTotal = $logToday[PRIO_NOTE] + $logToday[PRIO_WARNING] + $logToday[PRIO_ERROR];
	$logBarBase = max(1, $logTodayTotal);

	$mailReceiveRanges = bmWelcomeMailRanges($mailReceiveSeries);
	$mailSendRanges = bmWelcomeMailRanges($mailSendSeries);
	$tpl->assign('mailReceiveRanges', json_encode($mailReceiveRanges));
	$tpl->assign('mailSendRanges', json_encode($mailSendRanges));
	$tpl->assign('mailReceiveSum7', $mailReceiveRanges['7']['sum']);
	$tpl->assign('mailSendSum7', $mailSendRanges['7']['sum']);
	$tpl->assign('mailReceivePct7', $mailReceiveRanges['7']['pct']);
	$tpl->assign('mailSendPct7', $mailSendRanges['7']['pct']);
	$tpl->assign('mailReceiveValues7', implode(',', $mailReceiveRanges['7']['values']));
	$tpl->assign('mailSendValues7', implode(',', $mailSendRanges['7']['values']));
	$tpl->assign('logTodayInfo', $logToday[PRIO_NOTE]);
	$tpl->assign('logTodayWarning', $logToday[PRIO_WARNING]);
	$tpl->assign('logTodayError', $logToday[PRIO_ERROR]);
	$tpl->assign('logTodayTotal', $logTodayTotal);
	$tpl->assign('logInfoPct', $logTodayTotal > 0 ? round($logToday[PRIO_NOTE] / $logBarBase * 100, 2) : 0);
	$tpl->assign('logWarningPct', $logTodayTotal > 0 ? round($logToday[PRIO_WARNING] / $logBarBase * 100, 2) : 0);
	$tpl->assign('logErrorPct', $logTodayTotal > 0 ? round($logToday[PRIO_ERROR] / $logBarBase * 100, 2) : 0);
	$tpl->assign('logTodayStart', $logTodayStart);
	$tpl->assign('logTodayEnd', $logTodayEnd);
	$tpl->assign('canViewLogs', AdminAllowed('logs'));
	$tpl->assign('canViewStats', AdminAllowed('stats'));

	//
	// system notices
	//
	$notices = array();

	// setup folder still on disk?
	$setupDir = B1GMAIL_DIR . 'setup';
	if(is_dir($setupDir))
	{
		$setupHasLock = is_file($setupDir . '/lock');
		$setupHasLockUpdate = is_file($setupDir . '/lock_update');
		if(!$setupHasLock && !$setupHasLockUpdate)
			$notices[] = array('type'	=> 'error',
								'text'	=> $lang_admin['setupfolderunlocked']);
		else
			$notices[] = array('type'	=> 'warning',
								'text'	=> $lang_admin['setupfolderlocked']);
	}

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
								'link'	=> 'prefs.email.php?action=receive');
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
								'link'	=> 'maintenance.php?action=orphans');
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
								'link'	=> 'maintenance.php?action=orphans');
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
								'link'	=> 'optimize.php?action=cache');
	}

	// no postmaster?
	if(BMUser::GetID(GetPostmasterMail(), true) == 0)
		$notices[] = array('type'	=> 'warning',
							'text'	=> sprintf($lang_admin['nopostmaster'], DecodeEMail(GetPostmasterMail())),
							'link'	=> 'users.php?action=create');

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
	if($bm_prefs['structstorage'] == 'no')
		$notices[] = array('type'	=> 'info',
							'text'	=> $lang_admin['structrec'],
							'link'	=> 'prefs.common.php?');
	// still latin mode?
	if(empty($bm_prefs['db_is_utf8']))
		$notices[] = array('type'	=> 'warning',
							'text'	=> $lang_admin['latinmodenote'],
							'link'	=> '../setup/utf8convert.php?');

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
	// bzip2?
	if(!function_exists('bzopen'))
		$notices[] = array('type'	=> 'info',
							'text'	=> $lang_admin['bzip2']);

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
							'link'	=> 'logs.php?action=archiving');

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
							'link'	=> 'users.php?filter=true&statusNotActivated=true&allGroups=true');

	// users waiting for delete
	$res = $db->Query('SELECT COUNT(*) FROM {pre}users WHERE gesperrt=?',
		'delete');
	list($deleteCount) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();
	if($deleteCount > 0)
		$notices[] = array('type'	=> 'info',
							'text'	=> sprintf($lang_admin['deletenotice'], $deleteCount),
							'link'	=> 'users.php?filter=true&statusDeleted=true&allGroups=true');

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
	include B1GMAIL_DIR . 'serverlib/scheduledtasks.class.php';
	$stNotices = BMScheduledTasks::getNotices();
	if(is_array($stNotices) && count($stNotices) > 0)
		$notices = array_merge($notices, $stNotices);

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
	$tpl->assign('hasNotes', trim((string)$adminRow['notes']) !== '');
	$tpl->assign('page', 'welcome.tpl');
}

$tpl->assign('title', $lang_admin['welcome']);
$tpl->display('page.tpl');

function bmWelcomeMailRanges($series)
{
	$ranges = array();
	foreach(array(7, 30, 90) as $days)
	{
		$n = count($series);
		$current = array_slice($series, $n - $days);
		$previous = array_slice($series, $n - (2 * $days), $days);
		$sum = array_sum($current);
		$previousSum = array_sum($previous);
		$ranges[(string)$days] = array(
			'values' => array_values(array_map('intval', $current)),
			'sum' => (int)$sum,
			'pct' => $previousSum > 0 ? (int)round((($sum - $previousSum) / $previousSum) * 100) : null
		);
	}

	return $ranges;
}

/**
 * Restyle phpinfo() so it follows the ACP instead of PHP's fixed purple layout.
 *
 * @param string $html
 * @param string $theme light|dark
 * @return string
 */
function AdminStylePhpinfo($html, $theme)
{
	$theme = ($theme === 'dark') ? 'dark' : 'light';
	$css = <<<'CSS'
:root {
	color-scheme: light;
	--pi-bg: #fff;
	--pi-text: #1f2937;
	--pi-muted: #667085;
	--pi-border: #e6e7eb;
	--pi-head: #f6f8fb;
	--pi-label: #f8fafc;
	--pi-link: #066fd1;
}
html[data-bs-theme="dark"] {
	color-scheme: dark;
	--pi-bg: #1a2234;
	--pi-text: #e6edf5;
	--pi-muted: #9aa4b2;
	--pi-border: rgba(255, 255, 255, 0.08);
	--pi-head: #243044;
	--pi-label: #1e293b;
	--pi-link: #7eb6ff;
}
html, body {
	margin: 0;
	background: var(--pi-bg);
	color: var(--pi-text);
	font-family: Inter, system-ui, -apple-system, "Segoe UI", sans-serif;
	font-size: 13px;
	line-height: 1.45;
}
body { padding: 1rem 1.25rem 1.5rem; }
pre { margin: 0; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; white-space: pre-wrap; }
a:link, a:visited { color: var(--pi-link); text-decoration: none; background: transparent; }
a:hover { text-decoration: underline; }
.center { text-align: initial; }
.center table { margin: 0 0 1rem; text-align: left; }
.center th { text-align: left !important; }
table {
	width: 100% !important;
	max-width: 100%;
	border-collapse: collapse;
	border: 0;
	box-shadow: none;
	background: var(--pi-bg);
}
td, th {
	border: 1px solid var(--pi-border);
	font-size: 13px;
	vertical-align: top;
	padding: 0.4rem 0.7rem;
}
th { position: sticky; top: 0; background: var(--pi-head); }
h1 { font-size: 1.15rem; font-weight: 600; margin: 0.15rem 0; }
h2 { font-size: 0.95rem; font-weight: 600; margin: 1.15rem 0 0.4rem; text-align: left; }
.p { text-align: left; }
.h { background: var(--pi-head); font-weight: 600; }
.e {
	background: var(--pi-label);
	width: 24%;
	font-weight: 500;
}
.v {
	background: var(--pi-bg);
	max-width: none;
	overflow-wrap: anywhere;
	word-break: break-word;
}
.v i { color: var(--pi-muted); font-style: normal; }
img { float: right; height: 36px; width: auto; border: 0; }
hr {
	width: 100%;
	background: var(--pi-border);
	border: 0;
	height: 1px;
	margin: 0.25rem 0 0.75rem;
}
CSS;

	$style = '<style>' . $css . '</style>';
	$replaced = preg_replace('#<style\b[^>]*>.*?</style>#s', $style, $html, 1, $count);
	if($count === 1)
		$html = $replaced;

	$html = preg_replace('#<html\b#', '<html data-bs-theme="' . $theme . '"', $html, 1);
	return $html;
}
?>