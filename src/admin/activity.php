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
AdminRequirePrivilege('activity');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'activity';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['activity'],
		'relIcon'	=> 'activity32.png',
		'link'		=> 'activity.php?',
		'active'	=> $_REQUEST['action'] == 'activity'
	)
);

/**
 * activity
 */
if($_REQUEST['action'] == 'activity')
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
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 5) == 'user_')
					$userIDs[] = (int)substr($key, 5);

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
					: 'mailspace_used';
		$sortOrder = isset($_REQUEST['sortOrder'])
						? strtolower($_REQUEST['sortOrder'])
						: 'desc';
		$perPage = 50;

		// groups
		$groups = array();
		$res = $db->Query('SELECT id,storage,webdisk,traffic,titel AS title FROM {pre}gruppen');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$groups[$row['id']] = $row;
		}
		$res->Free();

		// page calculation
		$res = $db->Query('SELECT COUNT(*) FROM {pre}users');
		list($userCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		$pageCount = ceil($userCount / $perPage);
		$pageNo = isset($_REQUEST['page'])
					? max(1, min($pageCount, (int)$_REQUEST['page']))
					: 1;
		$startPos = max(0, min($perPage*($pageNo-1), $userCount));

		// users
		$users = array();
		$res = $db->Query('SELECT id,gruppe,email,gesperrt,mailspace_used,diskspace_used,(traffic_down+traffic_up) AS traffic,mailspace_add,diskspace_add,traffic_add,received_mails,sent_mails,traffic_status FROM {pre}users ORDER BY ' . $sortBy . ' ' . $sortOrder . ' ' . 'LIMIT ' . $startPos . ',' . $perPage);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$row['mailspace_max'] 	= $groups[$row['gruppe']]['storage'] + $row['mailspace_add'];
			$row['diskspace_max'] 	= $groups[$row['gruppe']]['webdisk'] + $row['diskspace_add'];
			$row['traffic_max'] 	= $groups[$row['gruppe']]['traffic'] + $row['traffic_add'];
			$row['statusImg'] 		= $statusImgTable[$row['gesperrt']];

			if($row['traffic_status'] != (int)date('m'))
				$row['traffic'] = 0;

			$users[$row['id']] 		= $row;
		}
		$res->Free();

		// assign
		$tpl->assign('groups', $groups);
		$tpl->assign('users', $users);
		$tpl->assign('pageNo', $pageNo);
		$tpl->assign('pageCount', $pageCount);
		$tpl->assign('sortBy', $sortBy);
		$tpl->assign('sortOrder', $sortOrder);
		$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
		$tpl->assign('page', 'activity.list.tpl');
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['usersgroups'] . ' &raquo; ' . $lang_admin['activity']);
$tpl->display('page.tpl');
?>