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
AdminRequirePrivilege('workgroups');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'workgroups';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['workgroups'],
		'relIcon'	=> 'workgroup32.png',
		'link'		=> 'workgroups.php?',
		'active'	=> $_REQUEST['action'] == 'workgroups'
	)
);

if(EXTENDED_WORKGROUPS)
{
	$tabs[] = array(
		'title'		=> $lang_admin['sharedfolders'],
		'relIcon'	=> 'workgroup_mail32.png',
		'link'		=> 'workgroups.php?action=folders&',
		'active'	=> $_REQUEST['action'] == 'folders'
	);
}

/**
 * workgroups
 */
if($_REQUEST['action'] == 'workgroups')
{
	if(!isset($_REQUEST['do']))
		$_REQUEST['do'] = 'list';

	//
	// list
	//
	if($_REQUEST['do'] == 'list')
	{
		// create group
		if(isset($_REQUEST['create']))
		{
			$db->Query('INSERT INTO {pre}workgroups(title,email,addressbook,calendar,webdisk,todo,notes) VALUES(?,?,?,?,?,?,?)',
				$_REQUEST['title'],
				EncodeEMail($_REQUEST['email']),
				'no',
				'no',
				0,
				'no',
				'no');
			$wgID = $db->InsertId();
			header('Location: workgroups.php?do=edit&id=' . $wgID . '&sid=' . session_id());
			exit();
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get group IDs
			$groupIDs = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 6) == 'group_')
					$groupIDs[] = (int)substr($key, 6);

			if(count($groupIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					$db->Query('DELETE FROM {pre}workgroups_member WHERE workgroup IN(' . implode(',', $groupIDs) . ')');
					$db->Query('DELETE FROM {pre}workgroups WHERE id IN(' . implode(',', $groupIDs) . ')');
				}
			}
		}

		// delete?
		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}workgroups_member WHERE workgroup=?',
				(int)$_REQUEST['delete']);
			$db->Query('DELETE FROM {pre}workgroups WHERE id=?',
				(int)$_REQUEST['delete']);
		}

		// groups
		$groups = array();
		$res = $db->Query('SELECT id,title,email FROM {pre}workgroups ORDER BY title ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$res2 = $db->Query('SELECT COUNT(*) FROM {pre}workgroups_member WHERE workgroup=?',
				$row['id']);
			list($memberCount) = $res2->FetchArray(MYSQLI_NUM);
			$res2->Free();

			$row['members'] = $memberCount;
			$groups[$row['id']] = $row;
		}
		$res->Free();

		// assign
		$tpl->assign('groups',		$groups);
		$tpl->assign('page', 		'workgroups.list.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit'
			&& isset($_REQUEST['id']))
	{
		// save?
		if(isset($_REQUEST['save']))
		{
			$db->Query('UPDATE {pre}workgroups SET title=?,email=? WHERE id=?',
				$_REQUEST['title'],
				EncodeEMail($_REQUEST['email']),
				(int)$_REQUEST['id']);
		}

		// delete member?
		else if(isset($_REQUEST['deleteMember']))
		{
			$db->Query('DELETE FROM {pre}workgroups_member WHERE user=? AND workgroup=?',
				(int)$_REQUEST['deleteMember'],
				(int)$_REQUEST['id']);
		}

		// add member?
		if(isset($_REQUEST['userMail'])
			&& trim($_REQUEST['userMail']) != ''
			&& ($userID = BMUser::GetID(EncodeEMail($_REQUEST['userMail']))) > 0)
		{
			$db->Query('REPLACE INTO {pre}workgroups_member(workgroup,user) VALUES(?,?)',
				(int)$_REQUEST['id'],
				$userID);
		}

		// fetch from DB
		$res = $db->Query('SELECT * FROM {pre}workgroups WHERE id=?',
			(int)$_REQUEST['id']);
		$group = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// members
		$members = array();
		$res = $db->Query('SELECT {pre}users.email AS email, CONCAT({pre}users.nachname,\', \',{pre}users.vorname) AS name, {pre}workgroups_member.user AS id FROM {pre}workgroups_member,{pre}users WHERE {pre}workgroups_member.workgroup=? AND {pre}users.id={pre}workgroups_member.user ORDER BY name ASC',
			(int)$_REQUEST['id']);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$members[$row['id']] = $row;
		$res->Free();

		// assign
		$tpl->assign('group',			$group);
		$tpl->assign('members',			$members);
		$tpl->assign('page', 			'workgroups.edit.tpl');
	}
}

/**
 * email folders
 */
else if(EXTENDED_WORKGROUPS && $_REQUEST['action'] == 'folders')
{
	if(!isset($_REQUEST['do']))
		$_REQUEST['do'] = 'list';

	//
	// list
	//
	if($_REQUEST['do'] == 'list')
	{
		// create?
		if(isset($_REQUEST['create']) && !empty($_POST['titel']))
		{
			$db->Query('INSERT INTO {pre}folders(`userid`,`titel`) VALUES(?,?)',
				-1,
				$_POST['titel']);
			$folderID = $db->InsertId();

			header('Location: workgroups.php?action=folders&do=edit&id='.$folderID.'&sid='.session_id());
			exit();
		}

		// delete?
		if(isset($_REQUEST['delete']) && (int)$_REQUEST['delete'] > 0)
		{
			// delete mails
			$res = $db->Query('SELECT `id`,`size`,`blobstorage`,`userid` FROM {pre}mails WHERE `folder`=?',
				(int)$_REQUEST['delete']);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				BMBlobStorage::createProvider($row['blobstorage'], $row['userid'])->deleteBlob(BMBLOB_TYPE_MAIL, $row['id']);

				$db->Query('DELETE FROM {pre}mails WHERE `id`=?',
						   $row['id']);
				$db->Query('DELETE FROM {pre}attachments WHERE `mailid`=?',
						   $row['id']);
			}
			$res->Free();

			// delete associations
			$db->Query('DELETE FROM {pre}workgroups_shares WHERE `sharetype`=? AND `shareid`=?',
				WORKGROUP_TYPE_MAILFOLDER,
				(int)$_REQUEST['delete']);

			// delete folder
			$db->Query('DELETE FROM {pre}folders WHERE `id`=? AND `userid`=?',
				(int)$_REQUEST['delete'],
				-1);
		}

		// get folders
		$folders = array();
		$res = $db->Query('SELECT `titel`,`id` FROM {pre}folders WHERE `userid`=? ORDER BY `titel` ASC',
			-1);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$folders[$row['id']] = $row;
		}
		$res->Free();

		// assign
		$tpl->assign('folders',		$folders);
		$tpl->assign('page', 		'workgroups.folders.list.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit'
			&& isset($_REQUEST['id']))
	{
		// fetch folder
		$res = $db->Query('SELECT * FROM {pre}folders WHERE `id`=? AND `userid`=?',
			(int)$_REQUEST['id'],
			-1);
		if($res->RowCount() == 0)
			die('Folder not found.');
		$folder = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// save?
		if(isset($_REQUEST['save']) && isset($_POST['titel']))
		{
			// save folder
			$db->Query('UPDATE {pre}folders SET `titel`=?,`perpage`=? WHERE `userid`=? AND `id`=?',
				$_POST['titel'],
				max(1, min(500, $_POST['perpage'])),
				-1,
				(int)$_REQUEST['id']);

			// save group<->folder assocs
			$assoc = isset($_POST['groups']) && is_array($_POST['groups']) ? $_POST['groups'] : array();
			foreach($assoc as $groupID=>$value)
			{
				if($value == 'no')
				{
					$db->Query('DELETE FROM {pre}workgroups_shares WHERE `workgroupid`=? AND `sharetype`=? AND `shareid`=?',
						$groupID,
						WORKGROUP_TYPE_MAILFOLDER,
						(int)$_REQUEST['id']);
				}
				else
				{
					$db->Query('REPLACE INTO {pre}workgroups_shares(`workgroupid`,`sharetype`,`shareid`,`writeaccess`) VALUES(?,?,?,?)',
						$groupID,
						WORKGROUP_TYPE_MAILFOLDER,
						(int)$_REQUEST['id'],
						$value == 'rw' ? 1 : 0);
				}
			}

			header('Location: workgroups.php?action=folders&sid=' . session_id());
			exit();
		}

		// fetch workgroups
		$groups = array();
		$res = $db->Query('SELECT `id`,`title` FROM {pre}workgroups ORDER BY `title` ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$groups[$row['id']] = $row;
		}
		$res->Free();

		// fetch associations
		$folder['groups'] = array();
		$res = $db->Query('SELECT `workgroupid`,`writeaccess` FROM {pre}workgroups_shares WHERE `sharetype`=? AND `shareid`=?',
			WORKGROUP_TYPE_MAILFOLDER,
			(int)$_REQUEST['id']);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if(!isset($groups[$row['workgroupid']]))
				continue;

			if($row['writeaccess'] == 1)
				$folder['groups'][$row['workgroupid']] = 'rw';
			else
				$folder['groups'][$row['workgroupid']] = 'ro';
		}
		$res->Free();

		// assign
		$tpl->assign('folder',			$folder);
		$tpl->assign('groups',			$groups);
		$tpl->assign('page', 			'workgroups.folders.edit.tpl');
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['usersgroups'] . ' &raquo; ' . $lang_admin['workgroups']);
$tpl->display('page.tpl');
?>