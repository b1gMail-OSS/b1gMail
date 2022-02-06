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
AdminRequirePrivilege('abuse');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'overview';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['overview'],
		'relIcon'	=> 'abuse32.png',
		'link'		=> 'abuse.php?',
		'active'	=> $_REQUEST['action'] == 'overview'
	)
);

/**
 * activity
 */
if($_REQUEST['action'] == 'overview')
{
	if(!isset($_REQUEST['do']))
		$_REQUEST['do'] = 'list';

	//
	// list
	//
	if($_REQUEST['do'] == 'list')
	{
		// single action?
		if(isset($_REQUEST['singleAction']))
		{
			if($_REQUEST['singleAction'] == 'lock')
			{
				$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id=?',
					'yes',
					$_REQUEST['singleID']);
			}
			else if($_REQUEST['singleAction'] == 'unlock'
						|| $_REQUEST['singleAction'] == 'activate'
						|| $_REQUEST['singleAction'] == 'recover')
			{
				if($_REQUEST['singleAction'] == 'unlock')
				{
					$db->Query('UPDATE {pre}abuse_points SET `expired`=1 WHERE `userid`=?',
						$_REQUEST['singleID']);
				}

				$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id=?',
					'no',
					$_REQUEST['singleID']);
			}
			else if($_REQUEST['singleAction'] == 'delete')
			{
				$res = $db->Query('SELECT gesperrt FROM {pre}users WHERE id=?',
					$_REQUEST['singleID']);
				list($userStatus) = $res->FetchArray(MYSQLI_NUM);
				$res->Free();

				if($userStatus != 'delete')
				{
					$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id=?',
						'delete',
						$_REQUEST['singleID']);
				}
				else
				{
					DeleteUser((int)$_REQUEST['singleID']);
				}
			}
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get user IDs
			$userIDs = array();
			if(isset($_POST['users']) && count($_POST['users']) > 0)
				$userIDs = array_map('intval', $_POST['users']);

			if(count($userIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					// get states
					$markIDs = $deleteIDs = array();
					$res = $db->Query('SELECT id,gesperrt FROM {pre}users WHERE id IN(' . implode(',', $userIDs) . ')');
					while($row = $res->FetchArray(MYSQLI_ASSOC))
						if($row['gesperrt'] == 'delete')
							$deleteIDs[] = $row['id'];
						else
							$markIDs[] = $row['id'];

					// mark users
					if(count($markIDs) > 0)
						$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id IN(' . implode(',', $markIDs) . ')',
							'delete');

					// delete users
					foreach($deleteIDs as $userID)
						DeleteUser($userID);
				}
				else if($_REQUEST['massAction'] == 'restore'
						|| $_REQUEST['massAction'] == 'unlock')
				{
					if($_REQUEST['massAction'] == 'unlock')
					{
						$db->Query('UPDATE {pre}abuse_points SET `expired`=1 WHERE `userid` IN ?',
							$userIDs);
					}

					$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id IN(' . implode(',', $userIDs) . ')',
						'no');
				}
				else if($_REQUEST['massAction'] == 'lock')
				{
					$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id IN(' . implode(',', $userIDs) . ')',
						'yes');
				}
				else if(substr($_REQUEST['massAction'], 0, 7) == 'moveto_')
				{
					$groupID = (int)substr($_REQUEST['massAction'], 7);
					$db->Query('UPDATE {pre}users SET gruppe=? WHERE id IN(' . implode(',', $userIDs) . ')',
						$groupID);
				}
			}
		}

		// sort options
		$sortBy = isset($_REQUEST['sortBy'])
					? $_REQUEST['sortBy']
					: 'pointsum';
		$sortOrder = isset($_REQUEST['sortOrder'])
						? strtolower($_REQUEST['sortOrder'])
						: 'desc';
		$perPage = 50;

		// page calculation
		$res = $db->Query('SELECT COUNT(DISTINCT(`userid`)) FROM {pre}abuse_points');
		list($entryCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		$pageCount = ceil($entryCount / $perPage);
		$pageNo = isset($_REQUEST['page'])
					? max(1, min($pageCount, (int)$_REQUEST['page']))
					: 1;
		$startPos = max(0, min($perPage*($pageNo-1), $entryCount));

		// users
		$users = array();
		$res = $db->Query('SELECT id,gruppe,email,gesperrt,SUM(points) AS pointsum FROM {pre}users INNER JOIN {pre}abuse_points ON {pre}abuse_points.`userid`={pre}users.`id` WHERE {pre}abuse_points.`expired`=0 GROUP BY `id` ORDER BY ' . $sortBy . ' ' . $sortOrder . ' ' . 'LIMIT ' . $startPos . ',' . $perPage);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($row['pointsum'] >= $bm_prefs['ap_hard_limit'])
				$row['indicator'] = 'red';
			else if($row['pointsum'] >= $bm_prefs['ap_medium_limit'])
				$row['indicator'] = 'yellow';
			else
				$row['indicator'] = 'green';
			$row['statusImg'] 		= $statusImgTable[$row['gesperrt']];
			$users[$row['id']] 		= $row;
		}
		$res->Free();

		// assign
		$tpl->assign('users', $users);
		$tpl->assign('pageNo', $pageNo);
		$tpl->assign('pageCount', $pageCount);
		$tpl->assign('sortBy', $sortBy);
		$tpl->assign('sortOrder', $sortOrder);
		$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
		$tpl->assign('page', 'abuse.list.tpl');
	}

	//
	// show send stats details for a day
	//
	else if($_REQUEST['do']=='statsDetails' && isset($_REQUEST['userid']) && isset($_REQUEST['day']) && isset($_REQUEST['type']))
	{
		$type = in_array($_REQUEST['type'], array('send', 'recv')) ? $_REQUEST['type'] : 'send';
		if($type == 'send')
		{
			$field = 'recipients';
			$table = '{pre}sendstats';
		}
		else
		{
			$field = 'size';
			$table = '{pre}recvstats';
		}

		$dayBegin = mktime(0, 0, 0, date('n', $_REQUEST['day']), date('j', $_REQUEST['day']), date('Y', $_REQUEST['day']));
		$dayEnd = mktime(23, 59, 59, date('n', $_REQUEST['day']), date('j', $_REQUEST['day']), date('Y', $_REQUEST['day']));

		$stats = array();
		$res = $db->Query('SELECT `'.$field.'`,`time` FROM '.$table.' WHERE `time`>=? AND `time`<=? AND `userid`=? ORDER BY `time` ASC',
			$dayBegin, $dayEnd, $_REQUEST['userid']);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$hour = date('H', $row['time']);
			if(!isset($stats[$hour]))
				$stats[$hour] = array('mails' => 0, $field => 0, 'timeStamp' => $row['time']);
			++$stats[$hour]['mails'];
			$stats[$hour][$field] += $row[$field];
		}
		$res->Free();

		header('Content-Type: application/json; charset="' . $currentCharset . '"');
		echo '[';
		foreach($stats as $hour=>$item)
		{
			if($type == 'recv')
				$item['size'] = TemplateSize(array('bytes' => $item['size']), $tpl);
			printf('{ "mails": %d, "%s": "%s", "timeStamp": %d, "hour": "%d:00 - %d:59" },',
				$item['mails'], $field, $item[$field], $item['timeStamp'], $hour, $hour);
		}
		echo ']';
		exit;
	}

	//
	// show points
	//
	else if($_REQUEST['do']=='show' && isset($_REQUEST['userid']))
	{
		$userID = (int)$_REQUEST['userid'];
		$types = GetAbuseTypes();

		// single action?
		if(isset($_REQUEST['singleAction']))
		{
			if($_REQUEST['singleAction'] == 'delete')
			{
				$db->Query('DELETE FROM {pre}abuse_points WHERE `userid`=? AND `entryid`=?',
					$userID,
					$_REQUEST['singleID']);
			}
			else if($_REQUEST['singleAction'] == 'lockUser')
			{
				$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id=?',
					'yes',
					$_REQUEST['singleID']);
			}
			else if($_REQUEST['singleAction'] == 'unlockUser'
						|| $_REQUEST['singleAction'] == 'activateUser'
						|| $_REQUEST['singleAction'] == 'recoverUser')
			{
				if($_REQUEST['singleAction'] == 'unlockUser')
				{
					$db->Query('UPDATE {pre}abuse_points SET `expired`=1 WHERE `userid`=?',
						$_REQUEST['singleID']);
				}

				$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id=?',
					'no',
					$_REQUEST['singleID']);
			}
			else if($_REQUEST['singleAction'] == 'deleteUser')
			{
				$res = $db->Query('SELECT gesperrt FROM {pre}users WHERE id=?',
					$_REQUEST['singleID']);
				list($userStatus) = $res->FetchArray(MYSQLI_NUM);
				$res->Free();

				if($userStatus != 'delete')
				{
					$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id=?',
						'delete',
						$_REQUEST['singleID']);
				}
				else
				{
					DeleteUser((int)$_REQUEST['singleID']);
				}
			}
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			if(isset($_POST['entries']) && is_array($_POST['entries']) && count($_POST['entries']) > 0)
			{
				$_POST['entries'] = array_map('intval', $_POST['entries']);

				if($_REQUEST['massAction'] == 'delete')
				{
					$db->Query('DELETE FROM {pre}abuse_points WHERE `userid`=? AND `entryid` IN ?',
						$userID,
						$_POST['entries']);
				}
			}
		}

		// save notes
		if(isset($_POST['save']) && isset($_POST['notes']))
		{
			$db->Query('UPDATE {pre}users SET `notes`=? WHERE `id`=?',
				$_POST['notes'],
				$userID);
		}

		// user info
		$user = _new('BMUser', array($userID));
		$group = $user->GetGroup();

		// get usage stuff
		$res = $db->Query('SELECT COUNT(*) FROM {pre}mails WHERE userid=?',
			$userID);
		list($emailMails) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		$res = $db->Query('SELECT COUNT(*) FROM {pre}folders WHERE userid=?',
			$userID);
		list($emailFolders) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		$res = $db->Query('SELECT COUNT(*) FROM {pre}diskfiles WHERE user=?',
			$userID);
		list($diskFiles) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		$res = $db->Query('SELECT COUNT(*) FROM {pre}diskfolders WHERE user=?',
			$userID);
		list($diskFolders) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		$usedMonthSMS = $user->GetUsedMonthSMS();

		// fetch points from db
		$sum = 0;
		$points = array();
		$res = $db->Query('SELECT `entryid`,`date`,`type`,`points`,`comment`,`expired` FROM {pre}abuse_points '
			. 'WHERE `userid`=? ORDER BY `expired` DESC,`entryid` ASC', $userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($row['expired'])
			{
				$row['indicator'] = 'grey';
				$row['sum'] = 0;
			}
			else
			{
				$sum += $row['points'];
				$row['sum'] = $sum;

				if($sum >= $bm_prefs['ap_hard_limit'])
					$row['indicator'] = 'red';
				else if($sum >= $bm_prefs['ap_medium_limit'])
					$row['indicator'] = 'yellow';
				else
					$row['indicator'] = 'green';
			}

			$row['typeText'] = $types[$row['type']]['title'];

			$points[$row['entryid']] = $row;
		}
		$res->Free();

		// page calculation
		$perPage = 50;
		$entryCount = count($points);
		$pageCount = ceil($entryCount / $perPage);
		$pageNo = isset($_REQUEST['page'])
					? max(1, min($pageCount, (int)$_REQUEST['page']))
					: 1;
		$startPos = max(0, min($perPage*($pageNo-1), $entryCount));
		$points = array_slice(array_reverse($points, true), $startPos, $perPage);

		// send stats
		$sendStats = array();
		$res = $db->Query('SELECT `recipients`,`time` FROM {pre}sendstats WHERE `time`>=? AND `userid`=? ORDER BY `time` DESC',
			time()-TIME_ONE_WEEK, $userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$day = date('d.m.Y', $row['time']);
			if(!isset($sendStats[$day]))
				$sendStats[$day] = array('mails' => 0, 'recipients' => 0, 'timeStamp' => $row['time']);
			++$sendStats[$day]['mails'];
			$sendStats[$day]['recipients'] += $row['recipients'];
		}
		$res->Free();

		// receive stats
		$recvStats = array();
		$res = $db->Query('SELECT `size`,`time` FROM {pre}recvstats WHERE `time`>=? AND `userid`=? ORDER BY `time` DESC',
			time()-TIME_ONE_WEEK, $userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$day = date('d.m.Y', $row['time']);
			if(!isset($recvStats[$day]))
				$recvStats[$day] = array('mails' => 0, 'size' => 0, 'timeStamp' => $row['time']);
			++$recvStats[$day]['mails'];
			$recvStats[$day]['size'] += $row['size'];
		}
		$res->Free();

		// assign
		$tpl->assign('sendStats',		$sendStats);
		$tpl->assign('recvStats',		$recvStats);
		$tpl->assign('usedMonthSMS', 	(int)$usedMonthSMS);
		$tpl->assign('emailMails', 		$emailMails);
		$tpl->assign('emailFolders', 	$emailFolders);
		$tpl->assign('diskFiles',		$diskFiles);
		$tpl->assign('diskFolders', 	$diskFolders);
		$tpl->assign('userStatusImg', 	$statusImgTable[$user->_row['gesperrt']]);
		$tpl->assign('userRow', 		$user->_row);
		$tpl->assign('groupRow', 		$group->_row);
		$tpl->assign('userID', 			$userID);
		$tpl->assign('pageNo', 			$pageNo);
		$tpl->assign('pageCount', 		$pageCount);
		$tpl->assign('points', 			$points);
		$tpl->assign('page', 'abuse.show.tpl');
	}
}

/**
 * prefs
 */
else if($_REQUEST['action'] == 'prefs')
{
	$tpl->assign('page', 'abuse.prefs.tpl');
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['usersgroups'] . ' &raquo; ' . $lang_admin['abuseprotect']);
$tpl->display('page.tpl');
?>