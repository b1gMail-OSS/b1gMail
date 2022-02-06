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
if(!class_exists('BMMailbox'))
	include('./serverlib/mailbox.class.php');
RequestPrivileges(PRIVILEGES_USER);

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));

/**
 * shared email code
 */
include('./serverlib/email.top.php');

/**
 * default action = start
 */
$tpl->addJSFile('li', $tpl->tplDir . 'js/email.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'folders';

/**
 * folder list
 */
if($_REQUEST['action'] == 'folders')
{
	$sortColumns = array('titel', 'parent', 'subscribed', 'storetime', 'intelligent');

	// get sort info
	$sortColumn = (isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], $sortColumns))
					? $_REQUEST['sort']
					: 'titel';
	$sortOrder = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')))
					? $_REQUEST['order']
					: 'asc';
	$sortOrderFA = ($sortOrder=="desc")?'fa-arrow-down': 'fa-arrow-up';

	// note list
	$sysFolderList = $mailbox->GetSysFolderList();
	$theFolderList = $mailbox->GetUserFolderList($sortColumn, $sortOrder, true, true);
	$sharedFolderList = $mailbox->GetSharedFolderList($sortColumn, $sortOrder, true, true);

	// page output
	$tpl->assign('pageTitle', $lang_user['folderadmin']);
	$tpl->assign('sysFolderList', $sysFolderList);
	$tpl->assign('theFolderList', $theFolderList);
	$tpl->assign('sortColumn', $sortColumn);
	$tpl->assign('sortOrder', $sortOrderFA);
	$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
	$tpl->assign('sharedFolderList', $sharedFolderList);
	$tpl->assign('pageContent', 'li/email.folders.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * action
 */
else if($_REQUEST['action'] == 'action'
		&& isset($_REQUEST['do']))
{
	foreach($_POST as $key=>$val)
	{
		if(substr($key, 0, 7) == 'folder_')
		{
			if($_REQUEST['do'] == 'delete')
			{
				$id = substr($key, 7);
				$mailbox->DeleteFolder((int)$id);
			}
		}
	}
	header('Location: email.folders.php?sid=' . session_id());
}

/**
 * set folder subscription
 */
else if($_REQUEST['action'] == 'setFolderSubscription'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['subscribe']))
{
	$subscribe = $_REQUEST['subscribe'] == 1;
	$id = (int)$_REQUEST['id'];
	die($mailbox->SubscribeFolder($id, $subscribe) ? 'OK' : 'FAILED');
}

/**
 * edit folder
 */
else if($_REQUEST['action'] == 'editFolder'
		&& isset($_REQUEST['id']))
{
	$folderID = (int)$_REQUEST['id'];

	if($folderID <= 0 && isset($folderList[$folderID]))
	{
		$storeTime = $thisUser->GetPref('storeTime_' . $folderID);
		if($storeTime === false)
			$storeTime = -1;
		$tpl->assign('pageTitle', $lang_user['editfolder']);
		$tpl->assign('folderTitle', $folderList[$folderID]['title']);
		$tpl->assign('folderID', $folderID);
		$tpl->assign('storeTime', $storeTime);
		$tpl->assign('pageContent', 'li/email.folders.editsys.tpl');
		$tpl->display('li/index.tpl');
	}
	else
	{
		$folder = $mailbox->GetFolder($folderID);
		if($folder !== false)
		{
			$tpl->assign('realFolderList', $mailbox->GetUserFolderList('titel', 'ASC', false));
			$tpl->assign('folder', $folder);
			$tpl->assign('dropdownFolderList', $mailbox->GetDropdownFolderList(-1, $null, 0, 0, false));
			$tpl->assign('pageContent', 'li/email.folders.edit.tpl');
			$tpl->display('li/index.tpl');
		}
	}
}

/**
 * edit conditions
 */
else if($_REQUEST['action'] == 'editConditions'
		&& isset($_REQUEST['id']))
{
	$folder = $mailbox->GetFolder((int)$_REQUEST['id']);
	if($folder !== false)
	{
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'save')
		{
			$conditions = $mailbox->GetConditions((int)$_REQUEST['id']);

			// save
			foreach($_POST as $key=>$val)
			{
				if(substr($key, 0, 6) == 'field_')
				{
					$id = substr($key, 6);
					if(isset($conditions[$id]))
					{
						$field = $val;
						$op = 1;

						if(in_array($field, array(6, 7, 8, 10, 11, 15)))
							$val = $_POST['bool_val_'.$id];
						else if($field == 9)
							$val = $_POST['priority_val_'.$id];
						else if($field == 14)
						{
							$op = $_POST['color_op_'.$id];
							$val = $_POST['color_val_'.$id];
						}
						else if($field == 12)
						{
							$op = $_POST['folder_op_'.$id];
							$val = $_POST['folder_val_'.$id];
						}
						else
						{
							$op = $_POST['op_'.$id];
							$val = $_POST['text_val_'.$id];
						}

						$mailbox->UpdateCondition($id, (int)$_REQUEST['id'], $field, $op, $val);
					}
				}
			}

			// delete a condition?
			if(count($conditions) > 1)
				foreach($_POST as $key=>$val)
				{
					if(substr($key, 0, 7) == 'remove_')
					{
						$id = substr($key, 7);
						if(isset($conditions[$id]) && count($conditions) > 1)
							$mailbox->DeleteCondition($id, (int)$_REQUEST['id']);
					}
				}

			// add a condition?
			if(isset($_POST['add']))
				$mailbox->AddCondition((int)$_REQUEST['id']);
		}

		$conditions = $mailbox->GetConditions((int)$_REQUEST['id']);
		$tpl->assign('id', (int)$_REQUEST['id']);
		$tpl->assign('realFolderList', $mailbox->GetUserFolderList('titel', 'ASC', false));
		$tpl->assign('conditions', $conditions);
		$tpl->assign('conditionCount', count($conditions));
		$tpl->display('li/email.folders.conditions.tpl');
	}
}

/**
 * save folder
 */
else if($_REQUEST['action'] == 'saveFolder'
		&& isset($_REQUEST['id'])
		&& IsPOSTRequest())
{
	$id = (int)$_REQUEST['id'];

	if($id <= 0 && isset($folderList[$id]))
	{
		$thisUser->SetPref('storeTime_' . $id,
			max(-1, min((int)$_REQUEST['storetime'], 4838400)));
	}
	else
	{
		$mailbox->UpdateFolder($id,
			$_REQUEST['titel'],
			(int)$_REQUEST['parentfolder'],
			isset($_REQUEST['subscribed']),
			!isset($_REQUEST['storetime']) ? -1 : max(-1, min((int)$_REQUEST['storetime'], 4838400)),
			isset($_REQUEST['intelligent_link'])
				? max(BMLINK_AND, min(BMLINK_OR, $_REQUEST['intelligent_link']))
				: BMLINK_AND);
	}

	header('Location: email.folders.php?sid=' . session_id());
}

/**
 * add folder
 */
else if($_REQUEST['action'] == 'addFolder')
{
	$tpl->assign('pageTitle', $lang_user['addfolder']);
	$tpl->assign('realFolderList', $mailbox->GetUserFolderList('titel', 'ASC', false));
	$tpl->assign('dropdownFolderList', $mailbox->GetDropdownFolderList(-1, $null, 0, 0, false));
	$tpl->assign('pageContent', 'li/email.folders.edit.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * create folder
 */
else if($_REQUEST['action'] == 'createFolder'
		&& IsPOSTRequest())
{
	$id = $mailbox->AddFolder($_REQUEST['titel'],
		(int)$_REQUEST['parentfolder'],
		isset($_REQUEST['subscribed']),
		max(-1, min((int)$_REQUEST['storetime'], 4838400)),
		isset($_REQUEST['intelligent']));

	if(isset($_REQUEST['intelligent']))
		header('Location: email.folders.php?action=editFolder&id=' . $id . '&sid=' . session_id());
	else
		header('Location: email.folders.php?sid=' . session_id());
}

/**
 * delete folder
 */
else if($_REQUEST['action'] == 'deleteFolder'
		&& isset($_REQUEST['id']))
{
	$mailbox->DeleteFolder((int)$_REQUEST['id']);
	header('Location: email.folders.php?sid=' . session_id());
}
?>