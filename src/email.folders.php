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

if(!defined('B1GMAIL_INIT'))
	require './serverlib/init.inc.php';
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
	$sharedFolderGroups = array();
	foreach($sharedFolderList as $folderID=>$folder)
	{
		$ownerEmail = !empty($folder['owner_email']) ? DecodeEMail($folder['owner_email']) : $lang_user['sharedfolders'];
		$sharedFolderGroups[$ownerEmail][$folderID] = $folder;
	}

	// page output
	$tpl->assign('pageTitle', $lang_user['folderadmin']);
	$tpl->assign('sysFolderList', $sysFolderList);
	$tpl->assign('theFolderList', $theFolderList);
	$tpl->assign('sortColumn', $sortColumn);
	$tpl->assign('sortOrder', $sortOrderFA);
	$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
	$tpl->assign('sharedFolderList', $sharedFolderList);
	$tpl->assign('sharedFolderGroups', $sharedFolderGroups);
	$tpl->assign('folderFavoriteMap', array_flip($mailbox->GetFolderFavoriteIDs()));
	// F4: expose the sharing capability so the folder-admin template can
	// render the "share full mailbox" button. Same gate as the shareMailbox
	// action itself uses (see below).
	$tpl->assign('canShareMailbox', bmOrganizerGroupCanShare('mail'));
	$tpl->assign('pageContent', 'li/email.folders.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * action
 */
else if($_REQUEST['action'] == 'action'
		&& isset($_REQUEST['do']))
{
	// F2: bulk-delete requires CSRF token.
	CsrfEnforceOnStateChange();
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
	SessionRedirect('email.folders.php');
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

	SessionRedirect('email.folders.php');
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
		SessionRedirect('email.folders.php?action=editFolder&id=' . $id);
	else
		SessionRedirect('email.folders.php');
}

/**
 * delete folder — F2: CSRF-token required.
 */
else if($_REQUEST['action'] == 'deleteFolder'
		&& isset($_REQUEST['id']))
{
	CsrfEnforceOnStateChange();
	$mailbox->DeleteFolder((int)$_REQUEST['id']);
	SessionRedirect('email.folders.php');
}

/**
 * toggle favorite
 */
else if($_REQUEST['action'] == 'toggleFavorite'
		&& isset($_REQUEST['id']))
{
	$nowFavorite = $mailbox->ToggleFolderFavorite((int)$_REQUEST['id']);
	if(isset($_REQUEST['rpc']))
		die($nowFavorite ? '1' : '0');
	SessionRedirect('email.folders.php');
}

/**
 * share folder
 */
else if($_REQUEST['action'] == 'share' && isset($_REQUEST['id']))
{
	if(!bmOrganizerGroupCanShare('mail'))
	{
		SessionRedirect('email.folders.php');
		exit();
	}
	$folderID = (int)$_REQUEST['id'];
	$shareType = BM_ORGANIZER_SHARE_MAIL;
	$collectionId = $folderID;

	if($mailbox->IsSystemFolder($folderID))
	{
		$folder = $mailbox->GetSystemFolderShareItem($folderID);
		if($folder === false)
		{
			SessionRedirect('email.folders.php');
			exit();
		}
		$shareType = BM_ORGANIZER_SHARE_MAILSYS;
		$collectionId = $mailbox->EncodeSharedSysFolder($userRow['id'], $folderID);
	}
	else
	{
		$folder = $mailbox->GetFolder($folderID);
		if($folder === false || (int)$folder['userid'] !== (int)$userRow['id'] || !empty($folder['intelligent']))
		{
			SessionRedirect('email.folders.php');
			exit();
		}
		$folder['title'] = $folder['titel'];
	}

	$notifyItem = $folder;
	$notifyItem['id'] = $collectionId;

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add' && isset($_REQUEST['email']) && IsPOSTRequest())
	{
		$targetId = bmOrganizerShareTargetUserId($_REQUEST['email']);
		$access = isset($_REQUEST['access']) ? $_REQUEST['access'] : BM_ORGANIZER_ACCESS_READ;
		$result = bmOrganizerAddShare($shareType, $collectionId, $userRow['id'], $targetId, $access);
		if(is_string($result) && isset($lang_user[$result]))
			$tpl->assign('shareError', $lang_user[$result]);
		else if((int)$result > 0)
		{
			bmOrganizerNotifyShareInvite($shareType, $notifyItem, $userRow['id'], $targetId, $access);
			$tpl->assign('shareSuccess', $lang_user['shareinvited']);
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'remove'
		&& isset($_REQUEST['share'])
		&& IsPOSTRequest())
	{
		// F2: revoking a folder share was GET-reachable.
		bmOrganizerRemoveShare($shareType, (int)$_REQUEST['share'], $userRow['id']);
	}

	$tpl->assign('shareItem', $folder);
	$tpl->assign('shareList', bmOrganizerListShares($shareType, $collectionId, $userRow['id']));
	$tpl->assign('shareType', 'mailfolder');
	$tpl->assign('shareAction', 'email.folders.php');
	$tpl->display('li/organizer.share.dialog.tpl');
	exit();
}

/**
 * share whole mailbox
 */
else if($_REQUEST['action'] == 'shareMailbox')
{
	if(!bmOrganizerGroupCanShare('mail'))
	{
		SessionRedirect('email.folders.php');
		exit();
	}
	$shareItem = array(
		'id'	=> (int)$userRow['id'],
		'title'	=> $lang_user['mailbox']
	);

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add' && isset($_REQUEST['email']) && IsPOSTRequest())
	{
		$targetId = bmOrganizerShareTargetUserId($_REQUEST['email']);
		$access = isset($_REQUEST['access']) ? $_REQUEST['access'] : BM_ORGANIZER_ACCESS_READ;
		$result = bmOrganizerAddShare(BM_ORGANIZER_SHARE_MAILBOX, (int)$userRow['id'], $userRow['id'], $targetId, $access);
		if(is_string($result) && isset($lang_user[$result]))
			$tpl->assign('shareError', $lang_user[$result]);
		else if((int)$result > 0)
		{
			bmOrganizerNotifyShareInvite(BM_ORGANIZER_SHARE_MAILBOX, $shareItem, $userRow['id'], $targetId, $access);
			$tpl->assign('shareSuccess', $lang_user['shareinvited']);
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'remove'
		&& isset($_REQUEST['share'])
		&& IsPOSTRequest())
	{
		// F2: revoking a full-mailbox share was GET-reachable.
		bmOrganizerRemoveShare(BM_ORGANIZER_SHARE_MAILBOX, (int)$_REQUEST['share'], $userRow['id']);
	}

	$tpl->assign('shareItem', $shareItem);
	$tpl->assign('shareList', bmOrganizerListShares(BM_ORGANIZER_SHARE_MAILBOX, (int)$userRow['id'], $userRow['id']));
	$tpl->assign('shareType', 'mailbox');
	$tpl->assign('shareAction', 'email.folders.php');
	$tpl->assign('shareRequestAction', 'shareMailbox');
	$tpl->display('li/organizer.share.dialog.tpl');
	exit();
}

/**
 * leave a folder shared with this user
 */
else if($_REQUEST['action'] == 'leaveshare' && isset($_REQUEST['id']))
{
	$shared = $mailbox->GetAccessibleSharedFolders();
	$folderID = (int)$_REQUEST['id'];
	if(!isset($shared[$folderID]) || empty($shared[$folderID]['can_leave']))
	{
		SessionRedirect('email.folders.php');
		exit();
	}

	$leaveItem = $shared[$folderID];
	$leaveItem['title'] = $leaveItem['titel'];
	if(!empty($leaveItem['owner_email']))
		$leaveItem['owner_email'] = DecodeEMail($leaveItem['owner_email']);
	if(!empty($leaveItem['mailbox_share']))
		$leaveItem['title'] = $lang_user['mailbox'];

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'leave' && IsPOSTRequest())
	{
		if(!empty($leaveItem['mailbox_share']))
			bmOrganizerLeaveShare(BM_ORGANIZER_SHARE_MAILBOX, (int)$leaveItem['userid'], $userRow['id']);
		if(!empty($leaveItem['mailsys_share']))
			bmOrganizerLeaveShare(BM_ORGANIZER_SHARE_MAILSYS, $folderID, $userRow['id']);
		if(empty($leaveItem['mailbox_share']) && empty($leaveItem['mailsys_share']))
			bmOrganizerLeaveShare(BM_ORGANIZER_SHARE_MAIL, $folderID, $userRow['id']);
		foreach($shared as $fid=>$folderRow)
		{
			if((int)$folderRow['userid'] === (int)$leaveItem['userid']
				&& (empty($leaveItem['mailbox_share']) ? (int)$fid === $folderID : true))
				$mailbox->RemoveFolderFavorite($fid);
		}
		if(empty($leaveItem['mailbox_share']))
			$mailbox->RemoveFolderFavorite($folderID);
		SessionRedirect('email.folders.php');
		exit();
	}

	$tpl->assign('leaveItem', $leaveItem);
	$tpl->assign('leaveAction', 'email.folders.php');
	$tpl->display('li/organizer.share.leave.tpl');
	exit();
}
?>