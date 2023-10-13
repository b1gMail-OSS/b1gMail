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
	die('Directly calling this file is not supported');

if(!class_exists('BMMail'))
	include(B1GMAIL_DIR . 'serverlib/mail.class.php');
if(!class_exists('BMSMS'))
	include(B1GMAIL_DIR . 'serverlib/sms.class.php');

/**
 * mailbox class
 */
class BMMailbox
{
	var $_userID;
	var $_userMail;
	var $_userObject;
	var $_userGroup;
	var $_intelligentFolders;
	var $_lastInsertId;
	var $_mailboxGeneration;
	var $_mailboxStructureGeneration;

	/**
	 * constructor
	 *
	 * @param int $userID User ID
	 * @param string $userMail User's primary mail address
	 * @return BMMailbox
	 */
	function __construct($userID, $userMail, $userObject)
	{
		$this->_userID = $userID;
		$this->_userMail = $userMail;
		$this->_userObject = $userObject;
		$this->_userGroup = false;
		$this->_mailboxGeneration = $userObject->_row['mailbox_generation'];
		$this->_mailboxStructureGeneration = $userObject->_row['mailbox_structure_generation'];
	}

	/**
	 * increment mailbox generation
	 *
	 */
	function IncGeneration()
	{
		global $db;

		$db->Query('UPDATE {pre}users SET mailbox_generation=mailbox_generation+1 WHERE id=?',
			$this->_userID);
		$this->_mailboxGeneration++;
	}

	/**
	 * increment mailbox structure generation
	 *
	 */
	function IncStructureGeneration()
	{
		global $db;

		$db->Query('UPDATE {pre}users SET mailbox_structure_generation=mailbox_structure_generation+1 WHERE id=?',
			$this->_userID);
		$this->_mailboxStructureGeneration++;
	}

	/**
	 * check if folder is an intelligent folder
	 *
	 * @param int $folderID Folder ID
	 * @return bool
	 */
	function IsIntelligentFolder($folderID)
	{
		global $db;

		if(!is_array($this->_intelligentFolders))
		{
			$this->_intelligentFolders = array();
			$res = $db->Query('SELECT id FROM {pre}folders WHERE intelligent=1 AND userid=?',
				$this->_userID);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$this->_intelligentFolders[$row['id']] = true;
			$res->Free();
		}

		return(isset($this->_intelligentFolders[$folderID]));
	}

	/**
	 * group a mail list
	 *
	 * @param array $mailList Mail list
	 * @param string $groupMode Group mode
	 * @return array
	 */
	function GroupMailList($mailList, $groupMode)
	{
		global $lang_user;

		$newList = array();

		$lastGroupValue = -1;
		$sepID = 0;
		foreach($mailList as $key=>$value)
		{
			if($groupMode == 'gelesen')
				$groupValue = ($value['row']['flags'] & FLAG_UNREAD) == 0 ? 1 : 0;
			else if($groupMode == 'beantwortet')
				$groupValue = ($value['row']['flags'] & FLAG_ANSWERED) != 0 ? 1 : 0;
			else if($groupMode == 'weitergeleitet')
				$groupValue = ($value['row']['flags'] & FLAG_FORWARDED) != 0 ? 1 : 0;
			else if($groupMode == 'attach')
				$groupValue = ($value['row']['flags'] & FLAG_ATTACHMENT) != 0 ? 1 : 0;
			else if($groupMode == 'flagged')
				$groupValue = ($value['row']['flags'] & FLAG_FLAGGED) != 0 ? 1 : 0;
			else if($groupMode == 'done')
				$groupValue = ($value['row']['flags'] & FLAG_DONE) != 0 ? 1 : 0;
			else if($groupMode == 'fetched')
				$groupValue = $value['row']['datum'];
			else if($groupMode == 'color')
				$groupValue = $value['row']['color'];
			else
				$groupValue = $value['row'][$groupMode];

			if($groupMode != 'fetched')
			{
				if($groupValue != $lastGroupValue)
				{
					$text = '?';

					if($groupMode == 'gelesen')
					{
						$text = $lang_user['read'] . ': '
									. $lang_user[$groupValue ? 'yes' : 'no'];
					}
					else if($groupMode == 'beantwortet')
					{
						$text = $lang_user['answered'] . ': '
									. $lang_user[$groupValue ? 'yes' : 'no'];
					}
					else if($groupMode == 'weitergeleitet')
					{
						$text = $lang_user['forwarded'] . ': '
									. $lang_user[$groupValue ? 'yes' : 'no'];
					}
					else if($groupMode == 'attach')
					{
						$text = $lang_user['attachment'] . ': '
									. $lang_user[$groupValue ? 'yes' : 'no'];
					}
					else if($groupMode == 'flagged')
					{
						$text = $lang_user['flagged'] . ': '
									. $lang_user[$groupValue ? 'yes' : 'no'];
					}
					else if($groupMode == 'done')
					{
						$text = $lang_user['done'] . ': '
									. $lang_user[$groupValue ? 'yes' : 'no'];
					}
					else if($groupMode == 'von')
					{
						$text = HTMLFormat($groupValue);
					}
					else if($groupMode == 'color')
					{
						$text = $lang_user['color'] . ': ' . $lang_user['color_' . $groupValue];
					}

					$newList[--$sepID] = array(
						'text'	=> $text
					);
				}
			}
			else
			{
				list($cat1) = categorizeDate($lastGroupValue);
				list($cat2, $cat2Array) = categorizeDate($groupValue);

				if($lastGroupValue == -1 || $cat1!=$cat2)
				{
					$newList[--$sepID] = array(
						'text'		=> $cat2Array['text'],
						'date'		=> $cat2Array['date'],
						'groupID' 	=> substr(md5($cat2Array['text'].$cat2Array['date']), 0, 8)
					);
				}
			}

			$newList[$key] = $value;

			$lastGroupValue = $groupValue;
		}

		return($newList);
	}

	/**
	 * get mails per page for folder
	 *
	 * @param int $id Folder ID
	 */
	function GetMailsPerPage($id)
	{
		global $db, $bm_prefs;

		if($id <= 0)
		{
			$prefKey = 'perpage_' . $id;
			$value = $this->_userObject->GetPref($prefKey);
			if($value !== false && !empty($value))
				return($value);
			else
				return((int)$bm_prefs['ordner_proseite']);
		}
		else
		{
			if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $id, false))
			{
				$res = $db->Query('SELECT perpage FROM {pre}folders WHERE id=?',
					$id);
			}
			else
			{
				$res = $db->Query('SELECT perpage FROM {pre}folders WHERE userid=? AND id=?',
					$this->_userID,
					$id);
			}
			$row = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
			return(isset($row[0]) ? $row[0] : (int)$bm_prefs['ordner_proseite']);
		}
	}

	/**
	 * set mails per page
	 *
	 * @param int $id Folder ID
 	 * @param string $num New Number
	 * @return bool
	 */
	function SetMailsPerPage($id, $num)
	{
		global $db;

		$num = min(150, max(5, $num));

		if($id <= 0)
		{
			$prefKey = 'perpage_' . $id;
			return($this->_userObject->SetPref($prefKey, $num));
		}
		else
		{
			if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $id, true))
			{
				$db->Query('UPDATE {pre}folders SET perpage=? WHERE id=?',
					$num,
					$id);
			}
			else
			{
				$db->Query('UPDATE {pre}folders SET perpage=? WHERE userid=? AND id=?',
					$num,
					$this->_userID,
					$id);
			}
			return($db->AffectedRows() == 1);
		}
	}

	/**
	 * get group mode for folder
	 *
	 * @param int $id Folder ID
	 */
	function GetGroupMode($id)
	{
		global $db, $bm_prefs;

		if($id <= 0)
		{
			$prefKey = 'groupmode_' . $id;
			$value = $this->_userObject->GetPref($prefKey);
			if($value !== false && !empty($value))
				return($value);
			else
				return($bm_prefs['mail_groupmode']);
		}
		else
		{
			if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $id, false))
			{
				$res = $db->Query('SELECT group_mode FROM {pre}folders WHERE id=?',
					$id);
			}
			else
			{
				$res = $db->Query('SELECT group_mode FROM {pre}folders WHERE userid=? AND id=?',
					$this->_userID,
					$id);
			}
			$row = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
			return(isset($row[0]) ? $row[0] : $bm_prefs['mail_groupmode']);
		}
	}

	/**
	 * set group mode
	 *
	 * @param int $id Folder ID
 	 * @param string $mode New mode
	 * @return bool
	 */
	function SetGroupMode($id, $mode)
	{
		global $db;

		if($id <= 0)
		{
			$prefKey = 'groupmode_' . $id;
			return($this->_userObject->SetPref($prefKey, $mode));
		}
		else
		{
			if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $id, true))
			{
				$db->Query('UPDATE {pre}folders SET group_mode=? WHERE id=?',
					$mode,
					$id);
			}
			else
			{
				$db->Query('UPDATE {pre}folders SET group_mode=? WHERE userid=? AND id=?',
					$mode,
					$this->_userID,
					$id);
			}
			return($db->AffectedRows() == 1);
		}
	}

	/**
	 * get folder title
	 *
	 * @param int $folderID Folder ID
	 * @return string
	 */
	function GetFolderTitle($folderID)
	{
		global $db;

		if($folderID < 1)
			return('');

		if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folderID, false))
		{
			$res = $db->Query('SELECT titel FROM {pre}folders WHERE id=?',
				(int)$folderID);
		}
		else
		{
			$res = $db->Query('SELECT titel FROM {pre}folders WHERE id=? AND userid=?',
				(int)$folderID,
				$this->_userID);
		}
		if($res->RowCount() == 0)
			return('');
		list($title) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($title);
	}

	/**
	 * delete a folder
	 *
	 * @param int $id Folder ID
	 * @return bool
	 */
	function DeleteFolder($id)
	{
		global $db, $cacheManager;

		// do not allow removal of shared folders (must be done in ACP or un-shared first)
		if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $id, false))
			return(false);

		$cacheManager->Delete('folderList:0:' . $this->_userID . ':' . $this->_mailboxStructureGeneration);
		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration);
		$cacheManager->Delete('folderList:0:' . $this->_userID . ':' . $this->_mailboxStructureGeneration . ':1');
		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration . ':1');
		$this->IncStructureGeneration();

		$db->Query('UPDATE {pre}folders SET parent=? WHERE parent=? AND userid=?',
			-1,
			(int)$id,
			$this->_userID);

		$db->Query('DELETE FROM {pre}folders WHERE id=? AND userid=?',
			(int)$id,
			$this->_userID);
		if($db->AffectedRows() == 1)
		{
			$db->Query('DELETE FROM {pre}folder_conditions WHERE folder=?',
				(int)$id);

			$db->Query('UPDATE {pre}mails SET folder=?,trashstamp=? WHERE userid=? AND folder=?',
				FOLDER_TRASH,
				time(),
				$this->_userID,
				$id);

			return(true);
		}

		return(false);
	}

	/**
	 * add a folder
	 *
	 * @param string $title Title
	 * @param int $parent Parent
	 * @param bool $subscribed Subscribed?
	 * @param int $storetime Storetime
	 * @param bool $intelligent Intelligent folder?
	 * @param bool $noDefaultCondition Do not create default condition?
	 * @return int
	 */
	function AddFolder($title, $parent, $subscribed, $storetime, $intelligent, $noDefaultCondition = false)
	{
		global $db, $cacheManager, $bm_prefs;

		$db->Query('INSERT INTO {pre}folders(userid,titel,parent,subscribed,storetime,intelligent,group_mode) VALUES(?,?,?,?,?,?,?)',
			$this->_userID,
			$title,
			(int)$parent,
			$subscribed ? 1 : 0,
			$intelligent ? -1 : (int)$storetime,
			$intelligent ? 1 : 0,
			$bm_prefs['mail_groupmode']);
		$id = $db->InsertID();

		if($id && $intelligent && !$noDefaultCondition)
			$this->AddCondition($id);

		$cacheManager->Delete('folderList:0:' . $this->_userID . ':' . $this->_mailboxStructureGeneration);
		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration);
		$cacheManager->Delete('folderList:0:' . $this->_userID . ':' . $this->_mailboxStructureGeneration . ':1');
		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration . ':1');
		$this->IncStructureGeneration();

		return($id);
	}

	/**
	 * update folder
	 *
	 * @param int $folderID Folder ID
	 * @param string $title Title
	 * @param int $parent Parent
	 * @param bool $subscribed Subscribed?
	 * @param int $storetime Storetime
	 * @param int $intelligent_link Intelligent link type
	 * @return bool
	 */
	function UpdateFolder($folderID, $title, $parent, $subscribed, $storetime, $intelligent_link)
	{
		global $db, $cacheManager;

		// shared folders must be updated from ACP
		if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folderID, false))
			return(false);

		$db->Query('UPDATE {pre}folders SET titel=?, parent=?, subscribed=?, storetime=?, intelligent_link=? WHERE id=? AND userid=?',
			$title,
			(int)$parent,
			$subscribed ? 1 : 0,
			(int)$storetime,
			(int)$intelligent_link,
			(int)$folderID,
			$this->_userID);

		$cacheManager->Delete('folderList:0:' . $this->_userID . ':' . $this->_mailboxStructureGeneration);
		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration);
		$cacheManager->Delete('folderList:0:' . $this->_userID . ':' . $this->_mailboxStructureGeneration . ':1');
		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration . ':1');
		$this->IncStructureGeneration();

		return($db->AffectedRows() == 1);
	}

	/**
	 * get folder dataset
	 *
	 * @param int $folderID Folder ID
	 * @return array
	 */
	function GetFolder($folderID)
	{
		global $db;

		$res = $db->Query('SELECT id,titel,parent,subscribed,perpage,storetime,group_mode,intelligent,intelligent_link,userid FROM {pre}folders WHERE id=?',
			(int)$folderID,
			$this->_userID);
		if($res->RowCount() == 0)
			return(false);
		$result = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		if($result['userid'] != $this->_userID
			&& !BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folderID, false))
		{
			return(false);
		}

		return($result);
	}

	/**
	 * get intelligent folder conditions
	 *
	 * @param int $folderID Folder ID
	 * @return array
	 */
	function GetConditions($folderID)
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT id,field,op,val FROM {pre}folder_conditions WHERE folder=? ORDER BY id ASC',
			(int)$folderID);
		while($row = $res->FetchArray())
			$result[$row['id']] = $row;
		$res->Free();

		return($result);
	}

	/**
	 * delete intelligent folder condition
	 *
	 * @param int $conditionID Condition ID
	 * @param int $folderID Folder ID
	 * @return bool
	 */
	function DeleteCondition($conditionID, $folderID)
	{
		global $db;

		$db->Query('DELETE FROM {pre}folder_conditions WHERE id=? AND folder=?',
			(int)$conditionID,
			(int)$folderID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * add intelligent folder condition
	 *
	 * @param int $folderID Folder ID
	 * @return int
	 */
	function AddCondition($folderID)
	{
		global $db;

		$db->Query('INSERT INTO {pre}folder_conditions(folder,field,op,val) VALUES(?,?,?,?)',
			(int)$folderID,
			1,
			1,
			'');
		return($db->InsertID());
	}

	/**
	 * update intelligent folder condition
	 *
	 * @param int $conditionID Condition ID
	 * @param int $folderID Folder ID
	 * @param int $field Field constant
	 * @param int $op Op constant
	 * @param string $val Value
	 * @return bool
	 */
	function UpdateCondition($conditionID, $folderID, $field, $op, $val)
	{
		global $db;

		$db->Query('UPDATE {pre}folder_conditions SET field=?,op=?,val=? WHERE id=? AND folder=?',
			(int)$field,
			(int)$op,
			$val,
			(int)$conditionID,
			(int)$folderID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * get user folder list
	 *
	 * @param string $sortColumn Sort column
	 * @param string $sortOrder Sort order
	 * @param bool $withParentTitle Get title of parent folder?
	 * @param bool $withStats With stats?
	 * @return array
	 */
	function GetUserFolderList($sortColumn = 'titel', $sortOrder = 'ASC', $withParentTitle = false, $withStats = false)
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT id,titel,parent,subscribed,perpage,storetime,group_mode,intelligent,intelligent_link FROM {pre}folders WHERE userid=? ORDER BY ' . $sortColumn . ' ' . $sortOrder,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($withParentTitle)
				$row['parent'] = $this->GetFolderTitle($row['parent']);
			if($withStats)
			{
				$row['allMails'] = $this->GetMailCount($row['id']);
				$row['unreadMails'] = $this->GetMailCount($row['id'], true);
				$row['flaggedMails'] = $this->GetMailCount($row['id'], false, true);
				$row['size'] = $this->GetFolderSize($row['id']);
			}
			$result[$row['id']] = $row;
		}
		$res->Free();

		return($result);
	}

	function GetSharedFolderList($sortColumn = 'titel', $sortOrder = 'ASC', $withParentTitle = false, $withStats = false)
	{
		global $db;

		if(!EXTENDED_WORKGROUPS)
			return(array());

		$result = array();
		$res = $db->Query('SELECT id,titel,parent,subscribed,perpage,storetime,group_mode,intelligent,intelligent_link,writeaccess FROM {pre}folders '
			. 'INNER JOIN {pre}workgroups_shares ON {pre}folders.id={pre}workgroups_shares.shareid '
			. 'INNER JOIN {pre}workgroups_member ON {pre}workgroups_shares.workgroupid={pre}workgroups_member.workgroup '
			. 'WHERE {pre}workgroups_shares.sharetype=' . WORKGROUP_TYPE_MAILFOLDER . ' AND {pre}workgroups_member.user=? '
			. 'ORDER BY ' . $sortColumn . ' '  . $sortOrder,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($withParentTitle)
				$row['parent'] = $this->GetFolderTitle($row['parent']);
			if($withStats)
			{
				$row['allMails'] = $this->GetMailCount($row['id']);
				$row['unreadMails'] = $this->GetMailCount($row['id'], true);
				$row['flaggedMails'] = $this->GetMailCount($row['id'], false, true);
				$row['size'] = $this->GetFolderSize($row['id']);
			}
			$row['readonly'] = $row['writeaccess'] == 0;
			$result[$row['id']] = $row;
		}
		$res->Free();

		return($result);
	}

	/**
	 * get system folder list
	 *
	 * @return array
	 */
	function GetSysFolderList()
	{
		global $lang_user;

		$result = array();

		// default folders
		$result[FOLDER_INBOX] = array(
			'titel'			=> $lang_user['inbox'],
			'type'			=> 'inbox',
			'size'			=> $this->GetFolderSize(FOLDER_INBOX),
			'allMails'		=> $this->GetMailCount(FOLDER_INBOX),
			'unreadMails'	=> $this->GetMailCount(FOLDER_INBOX, true),
			'flaggedMails'	=> $this->GetMailCount(FOLDER_INBOX, false, true),
		);
		$result[FOLDER_OUTBOX] = array(
			'titel'			=> $lang_user['outbox'],
			'type'			=> 'outbox',
			'size'			=> $this->GetFolderSize(FOLDER_OUTBOX),
			'allMails'		=> $this->GetMailCount(FOLDER_OUTBOX),
			'unreadMails'	=> $this->GetMailCount(FOLDER_OUTBOX, true),
			'flaggedMails'	=> $this->GetMailCount(FOLDER_OUTBOX, false, true),
		);
		$result[FOLDER_DRAFTS] = array(
			'titel'			=> $lang_user['drafts'],
			'type'			=> 'drafts',
			'size'			=> $this->GetFolderSize(FOLDER_DRAFTS),
			'allMails'		=> $this->GetMailCount(FOLDER_DRAFTS),
			'unreadMails'	=> $this->GetMailCount(FOLDER_DRAFTS, true),
			'flaggedMails'	=> $this->GetMailCount(FOLDER_DRAFTS, false, true),
		);
		$result[FOLDER_SPAM] = array(
			'titel'			=> $lang_user['spam'],
			'type'			=> 'spam',
			'size'			=> $this->GetFolderSize(FOLDER_SPAM),
			'allMails'		=> $this->GetMailCount(FOLDER_SPAM),
			'unreadMails'	=> $this->GetMailCount(FOLDER_SPAM, true),
			'flaggedMails'	=> $this->GetMailCount(FOLDER_SPAM, false, true),
		);
		$result[FOLDER_TRASH] = array(
			'titel'			=> $lang_user['trash'],
			'type'			=> 'trash',
			'size'			=> $this->GetFolderSize(FOLDER_TRASH),
			'allMails'		=> $this->GetMailCount(FOLDER_TRASH),
			'unreadMails'	=> $this->GetMailCount(FOLDER_TRASH, true),
			'flaggedMails'	=> $this->GetMailCount(FOLDER_TRASH, false, true),
		);

		return($result);
	}

	/**
	 * get folder size
	 *
	 * @param int $folderID Folder ID
	 * @return int
	 */
	function GetFolderSize($folderID)
	{
		global $db;

		if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folderID, false))
		{
			$res = $db->Query('SELECT SUM(size) FROM {pre}mails WHERE folder=?',
				$folderID);
		}
		else
		{
			$res = $db->Query('SELECT SUM(size) FROM {pre}mails WHERE folder=? AND userid=?',
				$folderID,
				$this->_userID);
		}
		list($folderSize) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($folderSize);
	}

	/**
	 * change subscribed-status of a folder
	 *
	 * @param int $folderID Folder ID
	 * @param bool $subscribe Subscribe?
	 * @return bool
	 */
	function SubscribeFolder($folderID, $subscribe = true)
	{
		global $db, $cacheManager;

		$db->Query('UPDATE {pre}folders SET subscribed=? WHERE id=? AND userid=?',
			$subscribe ? 1 : 0,
			(int)$folderID,
			$this->_userID);

		$cacheManager->Delete('folderList:0:' . $this->_userID . ':' . $this->_mailboxStructureGeneration);
		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration);
		$cacheManager->Delete('folderList:0:' . $this->_userID . ':' . $this->_mailboxStructureGeneration . ':1');
		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration . ':1');
		$this->IncStructureGeneration();

		return($db->AffectedRows() == 1);
	}

	/**
	 * fetch structured folder list for use in dropdown
	 * (parameters only used in internal recursion)
	 *
	 * @return array
	 */
	function GetDropdownFolderList($parent = -1, &$result = null, $level = 0, $more = 0, $includeShared = true)
	{
		global $db, $lang_user;

		$returnArray = $result == null;
		if($result == null)
			$result = array();

		if($parent == -1)
		{
			$result[FOLDER_INBOX]	= $lang_user['inbox'];
			$result[FOLDER_OUTBOX]	= $lang_user['outbox'];
			$result[FOLDER_SPAM]	= $lang_user['spam'];
			$result[FOLDER_TRASH]	= $lang_user['trash'];
		}

		// own folders
		$folderCount = 0;
		$res = $db->Query('SELECT titel,id FROM {pre}folders WHERE userid=? AND intelligent=0 AND parent=? ORDER BY titel ASC',
			$this->_userID,
			$parent);
		$rowCount = $res->RowCount();
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$folderCount++;
			$lastItem = $folderCount == $rowCount;

			if($level > 0)
			{
				$indent = str_repeat('&nbsp;&nbsp;&nbsp;', max(0, $more-1))
							. str_repeat('|&nbsp;', $level-$more);
				if($lastItem)
					$indent .= '\-- ';
				else
					$indent .= '|-- ';
			}
			else
				$indent = '';

			$result[$row['id']] = trim($indent . HTMLFormat($row['titel']));
			if($this->GetDropdownFolderList($row['id'], $result, $level+1, $lastItem || $level == 0 ? $more+1 : $more) > 0)
				$result[$row['id']] = str_replace('|-- ', '|-+ ', $result[$row['id']]);
		}
		$res->Free();

		// workgroup folders
		if(EXTENDED_WORKGROUPS && $parent == -1 && $includeShared)
		{
			$res = $db->Query('SELECT id,titel FROM {pre}folders '
				. 'INNER JOIN {pre}workgroups_shares ON {pre}folders.id={pre}workgroups_shares.shareid '
				. 'INNER JOIN {pre}workgroups_member ON {pre}workgroups_shares.workgroupid={pre}workgroups_member.workgroup '
				. 'WHERE {pre}workgroups_shares.sharetype=' . WORKGROUP_TYPE_MAILFOLDER . ' AND {pre}workgroups_member.user=? '
				. 'AND {pre}workgroups_shares.writeaccess=1',
				$this->_userID);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$result[$row['id']] = HTMLFormat($row['titel']);
			}
			$res->Free();
		}

		if($returnArray)
			return($result);
		else
			return($folderCount);
	}

	/**
	 * fetch folder list for user
	 *
	 * @param bool $withUnreadCount Calculate unread count?
	 * @return array
	 */
	function GetFolderList($withUnreadCount = false, $encode = true, $withAllCount = false)
	{
		global $db, $lang_user, $cacheManager;

		$cacheKey = sprintf('folderList:%d:%d:%d%s',
			$withUnreadCount ? 1 : 0,
			$this->_userID,
			$withUnreadCount ? $this->_mailboxGeneration : $this->_mailboxStructureGeneration,
			$withAllCount ? ':1' : '');

		if(!($result = $cacheManager->Get($cacheKey)))
		{
			$result = array();

			// default folders
			$result[FOLDER_INBOX] = array(
				'parent'		=> -1,
				'title'			=> $lang_user['inbox'],
				'type'			=> 'inbox',
				'intelligent'	=> false
			);
			$result[FOLDER_OUTBOX] = array(
				'parent'		=> -1,
				'title'			=> $lang_user['outbox'],
				'type'			=> 'outbox',
				'intelligent'	=> false
			);
			$result[FOLDER_DRAFTS] = array(
				'parent'		=> -1,
				'title'			=> $lang_user['drafts'],
				'type'			=> 'drafts',
				'intelligent'	=> false
			);
			$result[FOLDER_SPAM] = array(
				'parent'		=> -1,
				'title'			=> $lang_user['spam'],
				'type'			=> 'spam',
				'intelligent'	=> false
			);
			$result[FOLDER_TRASH] = array(
				'parent'		=> -1,
				'title'			=> $lang_user['trash'],
				'type'			=> 'trash',
				'intelligent'	=> false
			);

			// user folders
			$res = $db->Query('SELECT parent,titel,intelligent,id FROM {pre}folders WHERE userid=? AND subscribed=1 ORDER BY titel ASC',
				$this->_userID);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$result[$row['id']] = array(
					'parent'		=> $row['parent'],
					'title'			=> $row['titel'],
					'type'			=> $row['intelligent'] == 1 ? 'intellifolder' : 'folder',
					'intelligent'	=> $row['intelligent'] == 1
				);
			}
			$res->Free();

			// unread count
			if($withUnreadCount)
				foreach($result as $key=>$val)
					$result[$key]['unread'] = $this->GetMailCount($key, true);
			if($withAllCount)
				foreach($result as $key=>$val)
					$result[$key]['all'] = $this->GetMailCount($key);

			$cacheManager->Set($cacheKey, $result, TIME_ONE_DAY);
		}
		else
		{
			// apply language file
			$result[FOLDER_INBOX]['title'] 	= $lang_user['inbox'];
			$result[FOLDER_OUTBOX]['title'] = $lang_user['outbox'];
			$result[FOLDER_DRAFTS]['title'] = $lang_user['drafts'];
			$result[FOLDER_SPAM]['title'] 	= $lang_user['spam'];
			$result[FOLDER_TRASH]['title'] 	= $lang_user['trash'];
		}

		// workgroup folders
		if(EXTENDED_WORKGROUPS)
		{
			$res = $db->Query('SELECT titel,id,writeaccess FROM {pre}folders '
				. 'INNER JOIN {pre}workgroups_shares ON {pre}folders.id={pre}workgroups_shares.shareid '
				. 'INNER JOIN {pre}workgroups_member ON {pre}workgroups_shares.workgroupid={pre}workgroups_member.workgroup '
				. 'WHERE {pre}workgroups_shares.sharetype=' . WORKGROUP_TYPE_MAILFOLDER . ' AND {pre}workgroups_member.user=?',
				$this->_userID);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$result[$row['id']] = array(
					'parent'		=> FOLDER_ROOT,
					'title'			=> $row['titel'],
					'type'			=> 'sharedfolder',
					'intelligent'	=> false,
					'readonly'		=> $row['writeaccess'] == 0
				);

				if($withUnreadCount)
					$result[$row['id']]['unread'] = $this->GetMailCount($row['id'], true, false);
				if($withAllCount)
					$result[$row['id']]['all'] = $this->GetMailCount($row['id'], false, false);
			}
			$res->Free();
		}

		// encode?
		if($encode)
			foreach($result as $key=>$val)
				if($key>0)
					$result[$key]['title'] = HTMLFormat($result[$key]['title']);

		return($result);
	}

	/**
	 * rename folder
	 *
	 * @param int $folderID
	 * @param string $newName
	 * @return int
	 */
	function RenameFolder($folderID, $newName)
	{
		global $db, $cacheManager;

		// not supported for shared folders
		if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folderID, false))
			return(0);

		// long enough?
		$newName = trim($newName);
		if(strlen($newName) < 1)
			return(2);

		// get folder parent
		$res = $db->Query('SELECT parent FROM {pre}folders WHERE id=? AND userid=?',
			$folderID,
			$this->_userID);
		list($parent) = $res->FetchArray();
		$res->Free();

		// check if name is available
		$res = $db->Query('SELECT COUNT(*) FROM {pre}folders WHERE parent=? AND titel=? AND userid=?',
			$parent,
			$newName,
			$this->_userID);
		list($count) = $res->FetchArray();
		$res->Free();

		if($count == 0)
		{
			// okay!
			$db->Query('UPDATE {pre}folders SET titel=? WHERE id=? AND userid=?',
				$newName,
				$folderID,
				$this->_userID);

			$cacheManager->Delete('folderList:0:' . $this->_userID . ':' . $this->_mailboxStructureGeneration);
			$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration);
			$cacheManager->Delete('folderList:0:' . $this->_userID . ':' . $this->_mailboxStructureGeneration . ':1');
			$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration . ':1');
			$this->IncStructureGeneration();

			return(1);
		}
		else
		{
			// exists...
			return(0);
		}
	}

	/**
	 * get count of mails in folder
	 *
	 * @param int $folderID
	 * @param bool $unread Only count unread mails?
	 * @param bool $flagged Only count flagged mails?
	 * @return int
	 */
	function GetMailCount($folderID, $unread = false, $flagged = false)
	{
		global $db;

		$cond = ($folderID == -1)
					? '1'
					: $this->FolderCondition($folderID);
		if($unread)
			$cond .= ' AND (flags&'.FLAG_UNREAD.')!=0';
		if($flagged)
			$cond .= ' AND (flags&'.FLAG_FLAGGED.')!=0';

		if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folderID, false))
		{
			$res = $db->Query('SELECT COUNT(*) FROM {pre}mails WHERE folder=? AND ' . $cond,
				$folderID);
		}
		else if($this->IsIntelligentFolder($folderID))
		{
			$sharedFolders = $this->GetSharedFolderList();

			if(count($sharedFolders) > 0)
			{
				$res = $db->Query('SELECT COUNT(*) FROM {pre}mails WHERE (userid=? OR folder IN ?) AND ' . $cond,
					$this->_userID,
					array_keys($sharedFolders));
			}
			else
			{
				$res = $db->Query('SELECT COUNT(*) FROM {pre}mails WHERE userid=? AND ' . $cond,
					$this->_userID);
			}
		}
		else
		{
			$res = $db->Query('SELECT COUNT(*) FROM {pre}mails WHERE userid=? AND ' . $cond,
				$this->_userID);
		}
		$row = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($row[0]);
	}

	/**
	 * get group order by - statement
	 *
	 * @param string $groupMode Group mode
	 * @return string
	 */
	function GetGroupOrderBy($groupMode)
	{
		if($groupMode == 'fetched')
			return('datum');
		else if($groupMode == 'von')
			return('von');
		else if($groupMode == 'gelesen')
			return('(flags&'.FLAG_UNREAD.')=0');
		else if($groupMode == 'beantwortet')
			return('(flags&'.FLAG_ANSWERED.')=0');
		else if($groupMode == 'weitergeleitet')
			return('(flags&'.FLAG_FORWARDED.')=0');
		else if($groupMode == 'flagged')
			return('(flags&'.FLAG_FLAGGED.')=0');
		else if($groupMode == 'done')
			return('(flags&'.FLAG_DONE.')=0');
		else if($groupMode == 'attach')
			return('(flags&'.FLAG_ATTACHMENT.')=0');
		else if($groupMode == 'color')
			return('color');
		else
			return('datum');
	}

	/**
	 * get mail list (IDs only)
	 *
	 * @param int $folderID
	 * @return array
	 */
	function GetMailIDList($folderID)
	{
		global $db;
		$result = array();

		$condition = $this->FolderCondition($folderID);

		if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folderID, false))
		{
			$res = $db->Query('SELECT id FROM {pre}mails WHERE folder=? AND ' .  $condition,
				$folderID);
		}
		else
		{
			$res = $db->Query('SELECT id FROM {pre}mails WHERE userid=? AND ' .  $condition,
				$this->_userID);
		}
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$result[] = $row['id'];
		$res->Free();

		return($result);
	}

	/**
	 * Find the latest auto-saved draft which is not older than a certain max age.
	 *
	 * @param int $maxAge Maximum age in seconds
	 * @return int Draft ID (or false in case no draft can be found)
	 */
	function GetLatestAutoSavedDraft($maxAge)
	{
		global $db;

		$res = $db->Query('SELECT `id` FROM {pre}mails WHERE `userid`=? AND `folder`=? AND (`flags`&'.FLAG_AUTOSAVEDDRAFT.')!=0 AND (`flags`&'.FLAG_NODRAFTNOTIFY.')=0 AND `fetched`>=? ORDER BY `id` DESC LIMIT 1',
			$this->_userID,
			FOLDER_DRAFTS,
			time()-$maxAge);
		if($res->RowCount() == 0)
			return false;
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return($row['id']);
	}

	/**
	 * Disable compose draft notification for all stored auto-saved drafts.
	 *
	 * @return bool Success
	 */
	function SetNoDraftNotify()
	{
		global $db;

		$db->Query('UPDATE {pre}mails SET `flags`=`flags`|'.FLAG_NODRAFTNOTIFY.' WHERE `userid`=? AND `folder`=? AND (`flags`&'.FLAG_AUTOSAVEDDRAFT.')!=0 AND (`flags`&'.FLAG_NODRAFTNOTIFY.')=0',
			$this->_userID,
			FOLDER_DRAFTS);

		return(true);
	}

	/**
	 * get mail list
	 *
	 * @param int $folderID
	 * @return array
	 */
	function GetMailList($folderID, $page = 1, $mailsPerPage = -1, $sortField = 'fetched', $sortBy = 'DESC', $groupMode = '-')
	{
		global $db;
		$result = array();

		if($groupMode == 'fetched')
			$groupMode = '-';
		if($sortField == 'fetched')
			$sortField = 'datum';

		ModuleFunction('OnStartMailList', array($this->_userID, $folderID == FOLDER_DRAFTS));

		$condition = $this->FolderCondition($folderID);

		if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folderID, false))
		{
			$res = $db->Query('SELECT id,von,an,betreff,datum,flags,priority,size,color FROM {pre}mails WHERE folder=? AND ' . $condition . ' '
						. 'ORDER BY ' . ($groupMode != '-' ? $this->GetGroupOrderBy($groupMode) . ', ' : ''). $sortField . '  ' . $sortBy
						. ($mailsPerPage != -1 ? ' LIMIT ' . (($page-1)*$mailsPerPage) . ','. (int)$mailsPerPage : ''),
						$folderID);
		}
		else if($this->IsIntelligentFolder((int)$folderID))
		{
			$sharedFolders = array_keys($this->GetSharedFolderList());

			if(count($sharedFolders) > 0)
			{
				$res = $db->Query('SELECT id,von,an,betreff,datum,flags,priority,size,color FROM {pre}mails WHERE (userid=? OR folder IN ?) AND ' . $condition . ' '
							. 'ORDER BY ' . ($groupMode != '-' ? $this->GetGroupOrderBy($groupMode) . ', ' : ''). $sortField . '  ' . $sortBy
							. ($mailsPerPage != -1 ? ' LIMIT ' . (($page-1)*$mailsPerPage) . ','. (int)$mailsPerPage : ''),
							$this->_userID,
							$sharedFolders);
			}
			else
			{
				$res = $db->Query('SELECT id,von,an,betreff,datum,flags,priority,size,color FROM {pre}mails WHERE userid=? AND ' . $condition . ' '
							. 'ORDER BY ' . ($groupMode != '-' ? $this->GetGroupOrderBy($groupMode) . ', ' : ''). $sortField . '  ' . $sortBy
							. ($mailsPerPage != -1 ? ' LIMIT ' . (($page-1)*$mailsPerPage) . ','. (int)$mailsPerPage : ''),
							$this->_userID);
			}
		}
		else
		{
			$res = $db->Query('SELECT id,von,an,betreff,datum,flags,priority,size,color FROM {pre}mails WHERE userid=? AND ' . $condition . ' '
						. 'ORDER BY ' . ($groupMode != '-' ? $this->GetGroupOrderBy($groupMode) . ', ' : ''). $sortField . '  ' . $sortBy
						. ($mailsPerPage != -1 ? ' LIMIT ' . (($page-1)*$mailsPerPage) . ','. (int)$mailsPerPage : ''),
						$this->_userID);
		}
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$to = ParseMailList($row['an']);
			if(count($to) == 0)
				$to_name = $to_mail = $row['an'];
			else
			{
				$to_name = (isset($to[0]['name']) && trim($to[0]['name']) != ''
					? $to[0]['name']
					: DecodeEMail($to[0]['mail'])) . (count($to) > 1
					? ', ...'
					: '');
				$to_mail = $to[0]['mail'];
			}

			$result[$row['id']] = array(
				'subject'	=> $row['betreff'],
				'timestamp'	=> $row['datum'],
				'from_name'	=> ExtractMailName($row['von']),
				'from_mail'	=> ExtractMailAddress($row['von']),
				'to_name'	=> $to_name,
				'to_mail'	=> $to_mail,
				'priority'	=> $row['priority'] == 'high' ? ITEMPRIO_HIGH : ($row['priority'] == 'low' ? ITEMPRIO_LOW : ITEMPRIO_NORMAL),
				'flags'		=> $row['flags'],
				'row'		=> $row,
				'from'		=> $row['von'],
				'to'		=> $row['an'],
				'size'		=> $row['size'],
				'color'		=> $row['color']
			);
			ModuleFunction('OnGetMail', array($row['id'], $this->_userID));
		}
		$res->Free();

		ModuleFunction('OnEndMailList', array($this->_userID, $folderID == FOLDER_DRAFTS));

		return($result);
	}

	/**
	 * get previous and next mail
	 *
	 * @param int $folderID
	 * @param int $mailID
	 * @return array
	 */
	function GetPrevNextMail($folderID, $mailID)
	{
		global $db;

		$isShared = BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folderID, false);

		// prev
		if($isShared)
		{
			$res = $db->Query('SELECT id FROM {pre}mails WHERE id<? AND folder=? ORDER BY id DESC LIMIT 1',
								$mailID,
								$folderID);
		}
		else
		{
			$res = $db->Query('SELECT id FROM {pre}mails WHERE id<? AND userid=? AND folder=? ORDER BY id DESC LIMIT 1',
								$mailID,
								$this->_userID,
								$folderID);
		}
		if($res->RowCount() == 1)
			list($prevID) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// next
		if($isShared)
		{
			$res = $db->Query('SELECT id FROM {pre}mails WHERE id>? AND folder=? ORDER BY id ASC LIMIT 1',
								$mailID,
								$folderID);
		}
		else
		{
			$res = $db->Query('SELECT id FROM {pre}mails WHERE id>? AND userid=? AND folder=? ORDER BY id ASC LIMIT 1',
								$mailID,
								$this->_userID,
								$folderID);
		}
		if($res->RowCount() == 1)
			list($nextID) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return(array(isset($prevID) ? $prevID : -1, isset($nextID) ? $nextID : -1));
	}

	/**
	 * Find IDs of mails with specific flags
	 *
	 * @param int $flags
	 * @param bool $set 'true' searches for mails which have $flags set, 'false' for mails which DO NOT have any of $flags set
	 * @param int $limit ID count limit ('-1' = unlimited)
	 * @return array
	 */
	function GetMailsWithFlags($flags, $set = true, $limit = -1)
	{
		global $db;

		$result = array();

		$cond = '(flags&' . (int)$flags . ')';
		if($set)
			$cond .= '=' . (int)$flags;
		else
			$cond .= '=0';

		$res = $db->Query('SELECT id FROM {pre}mails WHERE userid=? AND '.$cond . ($limit != -1 ? ' LIMIT ' . $limit : ''),
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$result[] = $row['id'];
		}
		$res->Free();

		return($result);
	}

	/**
	 * build SQL condition for intelligent folder
	 *
	 * @param int $folderID Intelligent folder ID
	 * @return string
	 */
	function IntelligentFolderCondition($folderID)
	{
		global $db;

		$conditions = array();

		// get link mode
		$res = $db->Query('SELECT intelligent_link FROM {pre}folders WHERE id=? AND userid=?',
			(int)$folderID,
			$this->_userID);
		assert('$res->RowCount() != 0');
		list($linkMode) = $res->FetchArray();
		$res->Free();

		// get condition
		$res = $db->Query('SELECT field,op,val FROM {pre}folder_conditions WHERE folder=?',
			(int)$folderID);
		while($row = $res->FetchArray(MYSQLI_NUM))
		{
			list($field, $op, $val) = $row;

			// field & op valid?
			if(($field > 0 && $field <= MAILFIELD_DONE)
				&& $op != MAILFIELD_ATTACHLIST
				&& ($op > 0 && $op <= BMOP_ENDSWITH))
			{
				if(isset($condition))
					unset($condition);

				// prepare value
				$sqlVal = $db->Escape($val);

				// prepare field
				if($field == MAILFIELD_SUBJECT)
					$sqlField = 'betreff';
				else if($field == MAILFIELD_FROM)
					$sqlField = 'von';
				else if($field == MAILFIELD_TO)
					$sqlField = 'an';
				else if($field == MAILFIELD_CC)
					$sqlField = 'cc';
				else if($field == MAILFIELD_READ)
					$condition = '(flags&'.FLAG_UNREAD.')' . ($sqlVal == 'no' ? '!=0' : '=0');
				else if($field == MAILFIELD_ANSWERED)
					$condition = '(flags&'.FLAG_ANSWERED.')' . ($sqlVal == 'no' ? '=0' : '!=0');
				else if($field == MAILFIELD_FORWARDED)
					$condition = '(flags&'.FLAG_FORWARDED.')' . ($sqlVal == 'no' ? '=0' : '!=0');
				else if($field == MAILFIELD_PRIORITY)
					$sqlField = 'priority';
				else if($field == MAILFIELD_ATTACHMENT)
					$condition = '(flags&'.FLAG_ATTACHMENT.')' . ($sqlVal == 'no' ? '=0' : '!=0');
				else if($field == MAILFIELD_FLAGGED)
					$condition = '(flags&'.FLAG_FLAGGED.')' . ($sqlVal == 'no' ? '=0' : '!=0');
				else if($field == MAILFIELD_DONE)
					$condition = '(flags&'.FLAG_DONE.')' . ($sqlVal == 'no' ? '=0' : '!=0');
				else if($field == MAILFIELD_FOLDER)
					$sqlField = 'folder';
				else if($field == MAILFIELD_COLOR)
					$sqlField = 'color';
				else
					$sqlField = '0';

				if(!isset($condition))
				{
					// prepare op
					if($op == BMOP_EQUAL)
						$sqlOp = '=';
					else if($op == BMOP_NOTEQUAL)
						$sqlOp = '!=';
					else if($op == BMOP_CONTAINS)
					{
						$sqlOp = ' LIKE ';
						$sqlVal = '%' . $sqlVal . '%';
					}
					else if($op == BMOP_NOTCONTAINS)
					{
						$sqlOp = ' NOT LIKE ';
						$sqlVal = '%' . $sqlVal . '%';
					}
					else if($op == BMOP_STARTSWITH)
					{
						$sqlOp =  ' LIKE ';
						$sqlVal .= '%';
					}
					else if($op == BMOP_ENDSWITH)
					{
						$sqlOp = ' LIKE ';
						$sqlVal = '%' . $sqlVal;
					}

					// build condition!
					$condition = sprintf('%s%s\'%s\'',
						$sqlField,
						$sqlOp,
						$sqlVal);
				}

				$conditions[] = $condition;
			}
		}
		$res->Free();

		// return SQL string
		if(count($conditions) > 0)
			return('(' . implode($linkMode == BMLINK_AND ? ' AND ' : ' OR ', $conditions) . ')');
		else
			return('(0)');
	}

	/**
	 * get SQL condition for folder
	 *
	 * @param int $folderID Folder-ID (special or user)
	 * @param bool $moveCondition Move-Condition?
	 * @return string
	 */
	function FolderCondition($folderID, $moveCondition = false)
	{
		$result = 'folder=\'' . (int)$folderID . '\'';

		if($folderID > 0)
		{
			// this may be an intelligent folder
			if(!$moveCondition && $this->IsIntelligentFolder((int)$folderID))
				$result = $this->IntelligentFolderCondition($folderID);
		}

		if($moveCondition && $folderID == FOLDER_TRASH)
			$result .= ',trashstamp=\'' . time() . '\'';

		if($moveCondition)
			return($result);
		else
			return('(' . $result . ')');
	}

	/**
	 * check if folder exists
	 *
	 * @param int $folderID
	 * @return bool
	 */
	function FolderExists($folderID)
	{
		global $db;

		if(in_array($folderID, array(FOLDER_INBOX, FOLDER_OUTBOX, FOLDER_DRAFTS, FOLDER_SPAM, FOLDER_TRASH)))
			return(true);

		$res = $db->Query('SELECT COUNT(*) FROM {pre}folders WHERE userid=? AND id=?',
			$this->_userID,
			$folderID);
		list($count) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		if($count == 0
			&& BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folderID, false))
		{
			$count = 1;
		}

		return($count == 1);
	}

	/**
	 * set a flag
	 *
	 * @param int $flag
	 * @param bool $value
	 * @param int $mail
	 * @return int New message flags
	 */
	function FlagMail($flag, $value, $mail)
	{
		global $db, $tpl;

		// get current flags
		$res = $db->Query('SELECT folder,flags,userid FROM {pre}mails WHERE id=? LIMIT 1',
			$mail);
		if($res->RowCount() == 0)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		if($row['userid'] != $this->_userID
			&& !BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $row['folder'], true))
		{
			return(false);
		}

		$currentFlags = $newFlags = $row['flags'];

		// change flag
		if($value && (($currentFlags & $flag) == 0))
			$newFlags |= $flag;
		else if(!$value && (($currentFlags & $flag) != 0))
			$newFlags &= ~($flag);

		// update needed?
		if($newFlags != $currentFlags
			&& $this->SetMessageFlags($mail, $newFlags))
		{
			if(isset($tpl) && is_object($tpl) && isset($tpl->_tpl_vars) && isset($tpl->_tpl_vars['folderList']))
				$tpl->reassignFolderList = true;

			return($newFlags);
		}
		else
		{
			return($currentFlags);
		}
	}

	/**
	 * set mail color
	 *
	 * @param int $id
	 * @param int $color
	 */
	function ColorMail($id, $color)
	{
		global $db;

		if(!$this->MailAccessAllowed($id, true))
			return(false);

		if(!is_array($id))
		{
			$db->Query('UPDATE {pre}mails SET color=? WHERE id=?',
					   $color,
					   $id);
		}
		else
		{
			$db->Query('UPDATE {pre}mails SET color=? WHERE id IN ?',
					   $color,
					   $id);
		}
	}

	/**
	 * set mail notes
	 *
	 * @param int $id Mail ID
	 * @param string $notes Notes
	 */
	function SetMailNotes($id, $notes)
	{
		global $db;

		if(!$this->MailAccessAllowed($id, true))
			return(false);

		if(trim($notes) == '')
		{
			$db->Query('DELETE FROM {pre}mailnotes WHERE mailid=?',
				$id);
		}
		else
		{
			$db->Query('REPLACE INTO {pre}mailnotes(mailid,notes) VALUES(?,?)',
				$id,
				$notes);
		}
	}

	/**
	 * get mail notes
	 *
	 * @param int $id Mail ID
	 * @return string Notes
	 */
	function GetMailNotes($id)
	{
		global $db;

		if(!$this->MailAccessAllowed($id, false))
			return(false);

		$res = $db->Query('SELECT notes FROM {pre}mailnotes WHERE mailid=?',
			$id);
		if($res->RowCount() == 0)
			return('');
		list($result) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($result);
	}

	/**
	 * set message flags
	 *
	 * @param int $mail
	 * @param int $flags
	 * @return bool
	 */
	function SetMessageFlags($mail, $flags)
	{
		global $db, $cacheManager;

		if(!$this->MailAccessAllowed($mail, true))
			return(false);

		// update row
		$db->Query('UPDATE {pre}mails SET flags=? WHERE id=?',
			$flags,
			$mail);
		$affected = $db->AffectedRows() == 1;

		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration);
		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration . ':1');
		$this->IncGeneration();

		// success
		if($affected)
 		{
 			// plugins
 			ModuleFunction('AfterChangeMailFlags', array($mail, $flags, &$this));

 			return(true);
 		}
 		else
 			return(false);
	}

	/**
	 * move message(s)
	 *
	 * @param mixed $mails
	 * @param int $destFolder
	 * @return int count of moved mails
	 */
	function MoveMail($mails, $destFolder)
	{
		global $db, $cacheManager, $tpl;

		// array required
		if(!is_array($mails))
			$mails = array($mails);
		if(count($mails) == 0)
			return(0);

		// int!
		array_map('intval', $mails);

		// first check for write access
		if(!$this->MailAccessAllowed($mails, true))
			return(0);

		// then check if dest folder is shared
		$destIsShared = BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $destFolder, false);

		// if it is shared, we need write access to it
		if($destIsShared && !BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $destFolder, true))
			return(0);

		// query part
		$queryPart = $this->FolderCondition((int)$destFolder, true);

		// execute query
		$spaceChange = 0;
		$result = 0;

		$db->Query('BEGIN');
		$res = $db->Query('SELECT `id`,`folder`,`size` FROM {pre}mails WHERE `id` IN ?',
			$mails);
		while($row = $res->FetchArray(MYSQLI_NUM))
		{
			list($mailID, $srcFolderID, $srcMailSize) = $row;

			// source folder shared?
			$sourceIsShared = BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $srcFolderID, false);

			// write access?
			if($sourceIsShared && !BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $srcFolderID, true))
				continue;

			// source shared, dest local => add to space
			if($sourceIsShared && !$destIsShared)
			{
				$spaceChange += $srcMailSize;
			}
			// source local, dest shared => subtract from space
			else if(!$sourceIsShared && $destIsShared)
			{
				$spaceChange -= $srcMailSize;
			}
			// shared => shared is space-neutral

			// move
			$db->Query('UPDATE {pre}mails SET `userid`=?,'.$queryPart.' WHERE `id`=?',
				$destIsShared ? -1 : $this->_userID,
				$mailID);
			$result += $db->AffectedRows();
		}
		$db->Query('COMMIT');

		if($spaceChange != 0)
			$this->UpdateSpace($spaceChange);

		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration);
		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration . ':1');
		$this->IncGeneration();

		// folder list should be refreshed
		if(isset($tpl) && is_object($tpl) && isset($tpl->_tpl_vars) && isset($tpl->_tpl_vars['folderList']))
			$tpl->reassignFolderList = true;

 		// plugins
 		ModuleFunction('AfterMoveMails', array($mails, $destFolder, &$this));

 		return($result);
	}

	/**
	 * get BMMail-instance for message $id
	 *
	 * @param int $id ID
	 * @return BMMail
	 */
	function GetMail($id)
	{
		global $db;

		$result = false;

		if(!$this->MailAccessAllowed($id, false))
			return(false);

		$res = $db->Query('SELECT * FROM {pre}mails WHERE id=? LIMIT 1',
			$id);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$result = _new('BMMail', array($this->_userID, $row, false, true, false, &$this->_userObject));
		$res->Free();

		$group = $this->_userObject->GetGroup();

		if($group->_row['smime'] == 'yes' && $result !== false)
		{
			$smimeStatus = SMIME_UNKNOWN;

			$i = 0;
			while(is_object($result) && ($result->IsSigned() || $result->IsEncrypted()))
			{
				if($result->IsSigned())
				{
					if(!class_exists('BMSMIME'))
						include(B1GMAIL_DIR . 'serverlib/smime.class.php');
					$smime = _new('BMSMIME', array($this->_userID, &$this->_userObject));
					$res = $smime->CheckMailSignature($result);
					$smimeStatus |= $res[0];
					if(isset($res[1]) && $res[1] && is_object($res[1]))
						$result = $res[1];
					if(isset($res[2]))
						$result->smimeCertificateHash = $res[2];
				}

				if($result->IsEncrypted())
				{
					if(!class_exists('BMSMIME'))
						include(B1GMAIL_DIR . 'serverlib/smime.class.php');
					$smime = _new('BMSMIME', array($this->_userID, &$this->_userObject));
					$res = $smime->DecryptMail($result);
					$smimeStatus |= $res[0];
					if($res[0] == SMIME_DECRYPTION_FAILED)
						break;
					if(isset($res[1]) && $res[1] && is_object($res[1]))
						$result = $res[1];
				}

				if(++$i == 5)
				{
					PutLog(sprintf('Breaking S/MIME processing loop for message %d (this should not happen)',
						$id),
						PRIO_WARNING,
						__FILE__,
						__LINE__);
					break;
				}
			}

			$result->smimeStatus = $smimeStatus;
		}

		return($result);
	}

	/**
	 * check if mail exists in mailbox
	 *
	 * @param int $id ID
	 * @return bool
	 */
	function MailExists($id)
	{
		return($this->MailAccessAllowed($id, false));
	}

	/**
	 * get count of recent mails and reset it
	 *
	 * @return int
	 */
	function GetRecentMailCount($folder = FOLDER_ROOT)
	{
		global $db;

		// TODO: shared folders

		$userInfo = BMUser::staticFetch($this->_userID);

		// select new mails
		$res = $db->Query('SELECT COUNT(*) FROM {pre}mails WHERE '
			. ($folder == FOLDER_ROOT
				? '(folder=' . FOLDER_INBOX . ' OR folder>0)'
				: '(folder=' . (int)$folder . ')')
			. ' AND (flags&'.FLAG_UNREAD.')!=0 AND userid=? AND fetched>=?',
			$userInfo['id'],
			$userInfo['last_notify']);
		$row = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// update last notify date
		$this->UpdateLastNotifyDate();

		// return
		return($row[0]);
	}

	/**
	 * update last notify date
	 *
	 */
	function UpdateLastNotifyDate()
	{
		global $db;
		$db->Query('UPDATE {pre}users SET last_notify=? WHERE id=?',
			time(),
			$this->_userID);
	}

	/**
	 * update space
	 *
	 * @param int $bytes Bytes (negative or positive)
	 * @return boolean
	 */
	function UpdateSpace($bytes)
	{
		global $db;

		if($bytes == 0)
			return(true);

		if($bytes < 0)
		{
			$db->Query('UPDATE {pre}users SET mailspace_used=mailspace_used-LEAST(mailspace_used,'.abs($bytes).') WHERE id=?',
				$this->_userID);
			$this->_userObject->_row['mailspace_used'] -= abs($bytes);
		}
		else if($bytes > 0)
		{
			$db->Query('UPDATE {pre}users SET mailspace_used=mailspace_used+' . abs($bytes) . ' WHERE id=?',
				$this->_userID);
			$this->_userObject->_row['mailspace_used'] += abs($bytes);
		}

		return(true);
	}

	/**
	 * get user space limit
	 *
	 * @return int
	 */
	function GetSpaceLimit()
	{
		global $userRow, $groupRow;

		if(isset($userRow) && $userRow['id'] == $this->_userID)
		{
			return($groupRow['storage'] + $userRow['mailspace_add']);
		}
		else
		{
			if($this->_userGroup === false)
				$this->_userGroup = $this->_userObject->GetGroup();
			$myUserRow = $this->_userObject->_row;
			$myGroupRow = $this->_userGroup->Fetch();
			return($myGroupRow['storage'] + $myUserRow['mailspace_add']);
		}
	}

	/**
	 * get space limit per mail item
	 *
	 * @return int
	 */
	function GetMaxMailSize()
	{
		global $userRow, $groupRow;

		if(isset($userRow) && $userRow['id'] == $this->_userID)
		{
			return($groupRow['maxsize']);
		}
		else
		{
			if($this->_userGroup === false)
				$this->_userGroup = $this->_userObject->GetGroup();
			$myGroupRow = $this->_userGroup->Fetch();
			return($myGroupRow['maxsize']);
		}
	}

	/**
	 * get used space
	 *
	 * @return int
	 */
	function GetUsedSpace()
	{
		return($this->_userObject->_row['mailspace_used']);
	}

	/**
	 * empty a folder
	 *
	 * @param int $id Folder ID
	 * @return int Deleted mail count
	 */
	function EmptyFolder($id)
	{
		global $db, $tpl;

		$deletedMails = 0;
		$mails = $this->GetMailIDList($id);
		foreach($mails as $mailID)
			if($this->DeleteMail($mailID))
				$deletedMails++;

		if($deletedMails > 0)
		{
			if(isset($tpl) && is_object($tpl) && isset($tpl->_tpl_vars) && isset($tpl->_tpl_vars['folderList']))
				$tpl->reassignFolderList = true;
		}

		return($deletedMails);
	}

	/**
	 * set mail spam status
	 *
	 * @param int $id
	 * @param bool $spam
	 * @return bool
	 */
	function SetSpamStatus($id, $spam)
	{
		global $bm_prefs;

		$mail = $this->GetMail($id);

		if($mail !== false)
		{
			if($bm_prefs['use_bayes'] == 'yes'
				&& !$mail->trained)
			{
				if(!class_exists('BMMailFilter_Bayes'))
					include(B1GMAIL_DIR . 'serverlib/filters.inc.php');

				if($bm_prefs['bayes_mode'] == 'local')
					$filter = _new('BMMailFilter_Bayes', array($mail, $this->_userID));
				else
					$filter = _new('BMMailFilter_Bayes', array($mail));
				$filter->Train($spam);
			}

			$this->FlagMail(FLAG_SPAM, $spam, $id);

			if($spam)
				$this->FlagMail(FLAG_UNREAD, false, $id);

			if(!$spam
				&& $mail->_row['folder'] == $this->_userObject->_row['spamaction'])
				$this->MoveMail($id, FOLDER_INBOX);
			else if($spam
					&& $mail->_row['folder'] == FOLDER_INBOX
					&& $this->_userObject->_row['spamaction'] != -1
					&& $this->FolderExists($this->_userObject->_row['spamaction']))
				$this->MoveMail($id, $this->_userObject->_row['spamaction']);
		}

		return(false);
	}

	/**
	 * delete a mail
	 *
	 * @param int $id Mail ID
	 * @param bool $hard Delete without trashing?
	 * @return bool
	 */
	function DeleteMail($id, $hard = false)
	{
		global $db, $cacheManager;

		// get msg state
		$res = $db->Query('SELECT folder,size,blobstorage,userid FROM {pre}mails WHERE id=?',
			(int)$id);
		if($res->RowCount() != 1)
			return(false);
		list($folder, $messageSize, $blobStorage, $userID) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		if($userID != $this->_userID
			&& !BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folder, true))
		{
			return(false);
		}

		// trashed?
		if($folder == FOLDER_TRASH || $hard)
		{
			// delete mail
			BMBlobStorage::createProvider($blobStorage, $userID)->deleteBlob(BMBLOB_TYPE_MAIL, $id);

			$db->Query('DELETE FROM {pre}certmails WHERE mail=?',
				(int)$id);

			$db->Query('DELETE FROM {pre}mails WHERE id=?',
				(int)$id);

			$db->Query('DELETE FROM {pre}mailnotes WHERE mailid=?',
				(int)$id);

			$db->Query('DELETE FROM {pre}maildeliverystatus WHERE outboxid=?',
				(int)$id);

			$db->Query('DELETE FROM {pre}attachments WHERE mailid=?',
				(int)$id);
			$this->UpdateSpace($messageSize*-1);

			$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration);
			$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration . ':1');
			$this->IncGeneration();

			// delete mail from search index
			$this->DeleteMailFromSearchIndex($id);

			// plugins
 			ModuleFunction('AfterDeleteMail', array($id, &$this));

			return(true);
		}
		else
		{
			// mark mail as trashed
			$this->MoveMail($id, FOLDER_TRASH);

			// flag mail as read
			$this->FlagMail(FLAG_UNREAD, false, $id);

			$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration);
			$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration . ':1');
			$this->IncGeneration();
			return(true);
		}
	}

	/**
	 * deletes a mail from the search index if full text search is used
	 *
	 * @param int $id Mail ID
	 * @return bool Success
	 */
	function DeleteMailFromSearchIndex($id)
	{
		if(!FTS_SUPPORT)
			return false;

		if($this->_userGroup === false)
			$this->_userGroup = $this->_userObject->GetGroup();
		$groupRow = $this->_userGroup->Fetch();

		if($groupRow['ftsearch'] == 'yes')
		{
			if(!class_exists('BMSearchIndex'))
				include(B1GMAIL_DIR . 'serverlib/searchindex.class.php');
			$idx = _new('BMSearchIndex', array($this->_userID));
			$idx->deleteItem($id);
			unset($idx);

			return true;
		}

		return false;
	}

	/**
	 * store mail in mailbox without passing filters etc.
	 *
	 * @param BMMail $mail Mail object
	 * @param int $folder Folder
	 * @return int Store result constant
	 */
	function StoreMail($mail, $folder = FOLDER_INBOX, $trainNoSpam = false)
	{
		global $db, $bm_prefs, $cacheManager;

		if($this->_userGroup === false)
			$this->_userGroup = $this->_userObject->GetGroup();
		$groupRow = $this->_userGroup->Fetch();

		if($groupRow['ftsearch'] == 'yes' && FTS_SUPPORT)
			$mail->flags |= FLAG_INDEXED;

		// check if folder is shared and if write access is available
		$folderIsShared = false;
		if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folder, false))
			$folderIsShared = true;
		if($folderIsShared && !BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folder, true))
		{
			$folderIsShared = false;
			$folder = FOLDER_INBOX;
		}

		// get mail size
		$oldOffset = ftell($mail->_fp);
		fseek($mail->_fp, 0, SEEK_END);
		$mailSize = ftell($mail->_fp);
		fseek($mail->_fp, 0, SEEK_SET);

		// space left?
		if($this->GetUsedSpace()+$mailSize > $this->GetSpaceLimit())
		{
			fseek($mail->_fp, $oldOffset, SEEK_SET);
			return(STORE_RESULT_NOTENOUGHSPACE);
		}
		else if($mailSize > $this->GetMaxMailSize())
		{
			fseek($mail->_fp, $oldOffset, SEEK_SET);
			return(STORE_RESULT_MAILTOOBIG);
		}

		// extract message ID
		$messageIDs = ExtractMessageIDs($mail->GetHeaderValue('message-id'));
		$messageID = count($messageIDs) > 0
			? $messageIDs[0]
			: '<' . GenerateRandomKey('messageID') . '@' . $bm_prefs['b1gmta_host'] . '>';

		// date?
		$date = @strtotime($mail->GetHeaderValue('date'));
		if($date <= 0)
			$date = time();

		// blob storage provider
		$bsProvider = BMBlobStorage::createDefaultProvider($this->_userID);

		// insert into DB
		$db->Query('INSERT INTO {pre}mails(userid,betreff,von,an,cc,blobstorage,folder,datum,trashstamp,priority,fetched,msg_id,virnam,trained,refs,flags,size,color) VALUES '
			. '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
			$folderIsShared ? -1 : $this->_userID,
			Strip4ByteChars($mail->GetHeaderValue('subject')),
			Strip4ByteChars($mail->GetHeaderValue('from')),
			Strip4ByteChars($mail->GetHeaderValue('to')),
			Strip4ByteChars($mail->GetHeaderValue('cc')),
			$bsProvider->providerID,
			$folder,
			$date,
			0,
			$mail->priority == ITEMPRIO_LOW
				? 'low'
				: ($mail->priority == ITEMPRIO_HIGH
					? 'high'
					: 'normal'),
			time(),
			$messageID,
			$mail->infection,
			0,
			implode(';;;', $mail->GetReferences()),
			$mail->flags,
			$mailSize,
			$mail->color);

		// get insert id
		$id = $db->InsertId();

		// clean up cache
		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration);
		$cacheManager->Delete('folderList:1:' . $this->_userID . ':' . $this->_mailboxGeneration . ':1');
		$this->IncGeneration();

		// failed to insert?
		if(!$id)
		{
			fseek($mail->_fp, $oldOffset, SEEK_SET);
			return(STORE_RESULT_INTERNALERROR);
		}

		// set object id
		$mail->id = $id;

		// create file?
		if(!$bsProvider->storeBlob(BMBLOB_TYPE_MAIL, $id, $mail->_fp))
		{
			// log
			PutLog(sprintf('Failed to store blob for message %d using blob provider %d for user <%s> (%d)',
				$id,
				$bsProvider->providerID,
				$this->_userMail,
				$this->_userID),
				PRIO_ERROR,
				__FILE__,
				__LINE__);

			// delete message row
			$db->Query('DELETE FROM {pre}mails WHERE id=? AND userid=?',
				$id,
				$this->_userID);

			// return
			fseek($mail->_fp, $oldOffset, SEEK_SET);
			return(STORE_RESULT_INTERNALERROR);
		}
		$mail->blobStorage = $bsProvider->providerID;
		unset($bsProvider);

		// update space (only if destination is not shared)
		if(!$folderIsShared)
			$this->UpdateSpace($mailSize);

		// add attachments to attachment index
		$attachments = $mail->GetAttachments();
		if(count($attachments) > 0)
		{
			$db->Query('BEGIN');
			foreach($attachments as $partID=>$attachment)
			{
				$attachmentFlags = 0;
				if($attachment['viewable'])
					$attachmentFlags |= ATT_FLAG_VIEWABLE;

				$db->Query('INSERT INTO {pre}attachments(`userid`,`mailid`,`partid`,`filename`,`size`,`contenttype`,`flags`) VALUES(?,?,?,?,?,?,?)',
						   $this->_userID,
						   $id,
						   $partID,
						   $attachment['filename'],
						   $attachment['size'],
						   $attachment['mimetype'],
						   $attachmentFlags);
			}
			$db->Query('COMMIT');
		}

		// add mail to search index
		if($groupRow['ftsearch'] == 'yes' && FTS_SUPPORT)
		{
			if(!class_exists('BMSearchIndex'))
				include(B1GMAIL_DIR . 'serverlib/searchindex.class.php');
			$idx = _new('BMSearchIndex', array($this->_userID));
			$mail->AddToIndex($idx);
			unset($idx);
		}

		// okay!
		fseek($mail->_fp, $oldOffset, SEEK_SET);
		$this->_lastInsertId = $id;

		if($trainNoSpam)
			$this->SetSpamStatus($id, false);

		// plugins
 		ModuleFunction('AfterStoreMail',
 			array($id, &$mail, &$this));

		return(STORE_RESULT_OK);
	}

	/**
	 * store mail in mailbox with passing filters etc.
	 *
	 * @param BMMail $mail Mail object
	 * @return int Store result constant
	 */
	function ReceiveMail($mail, $folder = FOLDER_INBOX, $aliasReceiver = false, $isUserPOP3 = false)
	{
		global $plugins, $bm_prefs, $db, $lang_admin;

		// load needed classes
		if(!class_exists('BMMailBuilder'))
			include(B1GMAIL_DIR . 'serverlib/mailbuilder.class.php');
		if(!class_exists('BMFilterEval'))
			include(B1GMAIL_DIR . 'serverlib/filtereval.class.php');

		// get group row
		if($this->_userGroup === false)
			$this->_userGroup = $this->_userObject->GetGroup();
		$groupRow = $this->_userGroup->Fetch();

		// destination folder and default result
		if($folder != FOLDER_INBOX && !$this->FolderExists($folder))
			$folder = FOLDER_INBOX;
		$storeResult = STORE_RESULT_OK;
		$trainNoSpam = false;

		// trusted?
		$trusted = false;
		if(($trustToken = $mail->GetHeaderValue('x-b1gmail-trusted')) != '')
		{
			$validToken = GenerateTrustToken($mail->GetHeaderValue('message-id'),
				$mail->GetHeaderValue('from'),
				$mail->GetHeaderValue('to'),
				$mail->GetHeaderValue('subject'));
			if($validToken == $trustToken)
				$trusted = true;
		}

		// module functions
		$moduleResults = $plugins->callFunction('OnReceiveMail', false, true, array(&$mail, &$this, &$this->_userObject));
		foreach($moduleResults as $moduleName=>$moduleResult)
		{
			if($moduleResult == BM_BLOCK && !$trusted)
			{
				PutLog(sprintf('Mail to <%s> (%d) from <%s> blocked by plugin <%s>',
					$this->_userMail,
					$this->_userID,
					ExtractMailAddress($mail->GetHeaderValue('from')),
					$moduleName),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
				return(RECEIVE_RESULT_BLOCKED);
			}
			else if($moduleResult == BM_DELETE && !$trusted)
			{
				PutLog(sprintf('Mail to <%s> (%d) from <%s> deleted by plugin <%s>',
					$this->_userMail,
					$this->_userID,
					ExtractMailAddress($mail->GetHeaderValue('from')),
					$moduleName),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
				return(RECEIVE_RESULT_DELETE);
			}
			else if($moduleResult == BM_IS_INFECTED
					&& ($mail->flags&FLAG_INFECTED)==0
					&& !$trusted)
			{
				PutLog(sprintf('Mail to <%s> (%d) from <%s> marked as infected by plugin <%s>',
					$this->_userMail,
					$this->_userID,
					ExtractMailAddress($mail->GetHeaderValue('from')),
					$moduleName),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
				$mail->flags |= FLAG_INFECTED;
			}
			else if($moduleResult == BM_IS_SPAM
					&& ($mail->flags&FLAG_SPAM)==0
					&& !$trusted)
			{
				PutLog(sprintf('Mail to <%s> (%d) from <%s> marked as spam by plugin <%s>',
					$this->_userMail,
					$this->_userID,
					ExtractMailAddress($mail->GetHeaderValue('from')),
					$moduleName),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
				$mail->flags |= FLAG_SPAM;
			}
		}

		// duplicate detection
		if($bm_prefs['detect_duplicates'] == 'yes')
		{
			// extract message IDs
			$messageIDs = ExtractMessageIDs($mail->GetHeaderValue('message-id'));

			if(count($messageIDs) > 0)
			{
				$messageID = $messageIDs[0];

				// check for duplicates
				$res = $db->Query('SELECT COUNT(*) FROM {pre}mails WHERE msg_id=? AND userid=? AND folder!=?',
					$messageID,
					$this->_userID,
					FOLDER_OUTBOX);
				list($duplicateCount) = $res->FetchArray(MYSQLI_NUM);
				$res->Free();

				// duplicate?
				if($duplicateCount > 0)
				{
					PutLog(sprintf('Message duplicate for <%s> (%d) detected (%s), processing stopped',
						$this->_userMail,
						$this->_userID,
						$messageID),
						PRIO_NOTE,
						__FILE__,
						__LINE__);
					return(RECEIVE_RESULT_DELETE);
				}
			}
		}

		// user filter rules
		$filterEval = _new('BMFilterEval', array(&$this->_userObject, &$mail));
		list($filterStoreResult, $filterFolder, $filterActionFlags, $mailColor) = $filterEval->EvalFilters();
		if($folder == FOLDER_INBOX && $filterFolder != -1 && $this->FolderExists($filterFolder))
			$folder = $filterFolder;
		if($filterStoreResult != STORE_RESULT_OK)
		{
			$storeResult = $filterStoreResult;
			if($storeResult == RECEIVE_RESULT_BLOCKED && !$trusted)
			{
				PutLog(sprintf('Mail to <%s> (%d) from <%s> blocked by user filter',
					$this->_userMail,
					$this->_userID,
					ExtractMailAddress($mail->GetHeaderValue('from'))),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
				return($storeResult);
			}
			else if($storeResult == RECEIVE_RESULT_DELETE && !$trusted)
			{
				PutLog(sprintf('Mail to <%s> (%d) from <%s> deleted by user filter',
					$this->_userMail,
					$this->_userID,
					ExtractMailAddress($mail->GetHeaderValue('from'))),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
				return($storeResult);
			}
		}
		$mail->color = $mailColor;

		// bayes, if non-spam, local and spam filter enabled
		if($bm_prefs['use_bayes'] == 'yes'
			&& $bm_prefs['bayes_mode'] == 'local'
			&& $this->_userObject->_row['spamfilter'] == 'yes'
			&& !$trusted)
		{
			if(!class_exists('BMMailFilter_Bayes'))
				include(B1GMAIL_DIR . 'serverlib/filters.inc.php');
			$filter = _new('BMMailFilter_Bayes', array(&$mail, $this->_userID));
			$filter->Filter();
		}

		// virus action
		if(($mail->flags&FLAG_INFECTED) != 0
			&& $this->_userObject->_row['virusfilter'] == 'yes'
			&& !$trusted)
		{
			$virusAction = $this->_userObject->_row['virusaction'];
			if($virusAction == -1)
				$storeResult = RECEIVE_RESULT_BLOCKED;
			else if($virusAction != -256
					&& $folder == FOLDER_INBOX
					&& $this->FolderExists($virusAction))
				$folder = $virusAction;
		}

		// remove infected flag if virus filter is disabled
		if($this->_userObject->_row['virusfilter'] != 'yes')
			$mail->flags &= ~(FLAG_INFECTED);

		// remove spam flag if mail is trusted
		if($trusted)
		{
			$mail->flags &= ~(FLAG_SPAM);
		}

		// mark emails from senders which are in the adressbook as non-spam?
		if($this->_userObject->_row['addressbook_nospam'] == 'yes')
		{
			if(!class_exists('BMAddressbook'))
				include(B1GMAIL_DIR . 'serverlib/addressbook.class.php');

			$senders = ExtractMailAddresses($mail->GetHeaderValue('from'));

			if(is_array($senders) && count($senders) > 0)
			{
				$book = _new('BMAddressbook', array($this->_userID));
				if($book->LookupEmail($senders))
				{
					$mail->flags &= ~(FLAG_SPAM);
					$trainNoSpam = true;
				}
			}
		}

		// spam action
		if(($mail->flags&FLAG_SPAM) != 0
			&& $this->_userObject->_row['spamfilter'] == 'yes')
		{
			$spamAction = $this->_userObject->_row['spamaction'];
			if($spamAction == -1)
				$storeResult = RECEIVE_RESULT_BLOCKED;
			else if(($folder == FOLDER_INBOX || ($filterActionFlags & FILTER_ACTIONFLAG_DO_NOT_OVERRIDE_SPAMFILTER) != 0)
					&& $this->FolderExists($spamAction))
				$folder = $spamAction;
		}

		// forward if storeResult == STORE_RESULT_OK
		if($storeResult == STORE_RESULT_OK
			&& $groupRow['forward'] == 'yes'
			&& (ExtractMailAddress($mail->GetHeaderValue('return-path')) != '' || $bm_prefs['returnpath_check'] == 'no'))
		{
			$forwardAddresses = array();
			if($this->_userObject->_row['forward'] == 'yes'
				&& BMUser::AddressValid(ExtractMailAddress($this->_userObject->_row['forward_to']), false))
			{
				$forwardAddresses[] = ExtractMailAddress($this->_userObject->_row['forward_to']);
			}
			if($filterActionFlags & FILTER_ACTIONFLAG_FORWARD)
			{
				foreach($filterEval->_forwardTo as $forwardAddress)
					if(BMUser::AddressValid($forwardAddress, false))
						$forwardAddresses[] = $forwardAddress;
			}
			foreach($forwardAddresses as $forwardTo)
			{
				if(BMUser::GetID($forwardTo) != $this->_userObject->_id)
				{
					if($fp = fopen('php://temp', 'wb+'))
					{
						$isDKIMSigned = !empty($mail->GetHeaderValue('dkim-signature'));

						// add mail headers
						fprintf($fp, 'X-Forward-To: <%s>' . "\r\n", $forwardTo);
						fprintf($fp, 'Return-Path: <>' . "\r\n");
						if(!$isDKIMSigned) fprintf($fp, 'To: <%s>' . "\r\n", $forwardTo);

						// copy original content
						$forwardInHeader = true;
						$forwardOldPos = ftell($mail->_fp);
						fseek($mail->_fp, 0, SEEK_SET);
						while(is_resource($mail->_fp) && !feof($mail->_fp))
						{
							$line = rtrim(fgets2($mail->_fp), "\r\n") . "\r\n";

							if($forwardInHeader && $line == "\r\n")
								$forwardInHeader = false;

							if(!$forwardInHeader ||
								(strtolower(substr($line, 0, 12)) != 'return-path:'
								&& (strtolower(substr($line, 0, 3)) != 'to:' || $isDKIMSigned)
								&& strtolower(substr($line, 0, 3)) != 'cc:'
								&& strtolower(substr($line, 0, 4)) != 'bcc:'
								&& strtolower(substr($line, 0, 13)) != 'x-forward-to:'
								&& strtolower(substr($line, 0, 14)) != 'x-original-to:'
								&& strtolower(substr($line, 0, 13)) != 'delivered-to:'))
							{
								fwrite($fp, $line);
							}
						}
						fseek($mail->_fp, $forwardOldPos, SEEK_SET);

						// reset stream
						fseek($fp, 0, SEEK_SET);

						// send
						$sendMail = _new('BMSendMail');
						$sendMail->SetUserID($this->_userID);
						$sendMail->SetSender($mail->GetHeaderValue('from'));
						$sendMail->SetMailFrom($this->_userObject->_row['email']);
						$sendMail->SetRecipients($forwardTo);
						$sendMail->SetSubject(($subject = $mail->GetHeaderValue('subject')) != ''
								? $subject
								: '(no subject)');
						$sendMail->SetBodyStream($fp);

						// send, log
						if($sendMail->Send())
						{
							PutLog(sprintf('Forwarded mail for <%s> (%d) to <%s> (forwardDelete: %d)',
								$this->_userMail,
								$this->_userID,
								$forwardTo,
								$this->_userObject->_row['forward_delete'] == 'yes'),
								PRIO_NOTE,
								__FILE__,
								__LINE__);
							if($this->_userObject->_row['forward_delete'] == 'yes')
								return(RECEIVE_RESULT_DELETE);
						}
						else
						{
							PutLog(sprintf('Failed to forward mail for <%s> (%d) to <%s>',
								$this->_userMail,
								$this->_userID,
								$forwardTo),
								PRIO_WARNING,
								__FILE__,
								__LINE__);
						}

						// close file
						fclose($fp);
					}
				}
				else
				{
					PutLog(sprintf('Will not forward mail for <%s> (storeResultCheck: %d, addressValidityCheck: %d, returnPathCheck: %d, idCheck: %d)',
						$this->_userMail,
						$storeResult == STORE_RESULT_OK,
						BMUser::AddressValid($forwardTo, false),
						(ExtractMailAddress($mail->GetHeaderValue('return-path')) != '' || $bm_prefs['returnpath_check'] == 'no'),
						BMUser::GetID($forwardTo) != $this->_userObject->_id),
						PRIO_DEBUG,
						__FILE__,
						__LINE__);
				}
			}
		}

		// draft responder?
		if(($filterActionFlags & FILTER_ACTIONFLAG_RESPOND)
			&& strpos(strtolower($mail->GetHeaderValue('precedence')), 'junk') === false
			&& (trim($mail->GetHeaderValue('auto-submitted')) == '' || trim(strtolower($mail->GetHeaderValue('auto-submitted'))) == 'no')
			&& ($mailFrom = ExtractMailAddress($mail->GetHeaderValue('from'))) != ''
			&& $mail->GetHeaderValue('x-autoresponder') == ''
			&& (ExtractMailAddress($mail->GetHeaderValue('return-path')) != '' || $bm_prefs['returnpath_check'] == 'no')
			&& !$trusted)
		{
			foreach($filterEval->_respondWith as $draftID)
			{
				if(($draftMail = $this->GetMail($draftID))
				   && is_object($draftMail)
				   && $draftMail->_row['folder'] == FOLDER_DRAFTS)
				{
					if($draftMailFP = $draftMail->GetMessageFP())
					{
						if($fp = fopen('php://temp', 'wb+'))
						{
							// add mail headers
							fprintf($fp, 'Return-Path: <>' . "\r\n");
							fprintf($fp, 'Date: %s' . "\r\n", date('r'));
							fprintf($fp, 'To: <%s>' . "\r\n", $mailFrom);
							fprintf($fp, 'Auto-Submitted: auto-replied' . "\r\n");
							fprintf($fp, 'Precedence: junk' . "\r\n");
							fprintf($fp, 'X-Autoresponder: yes' . "\r\n");

							// copy original content
							$respondInHeader = true;
							while(!feof($draftMailFP))
							{
								$line = rtrim(fgets2($draftMailFP), "\r\n") . "\r\n";

								if($respondInHeader && $line == "\r\n")
									$respondInHeader = false;

								if(!$respondInHeader ||
									(strtolower(substr($line, 0, 12)) != 'return-path:'
									&& strtolower(substr($line, 0, 3)) != 'to:'
									&& strtolower(substr($line, 0, 3)) != 'cc:'
									&& strtolower(substr($line, 0, 4)) != 'bcc:'
									&& strtolower(substr($line, 0, 13)) != 'x-forward-to:'
									&& strtolower(substr($line, 0, 5)) != 'date:'))
								{
									fwrite($fp, $line);
								}
							}

							// reset stream
							fseek($fp, 0, SEEK_SET);

							// send
							$sendMail = _new('BMSendMail');
							$sendMail->SetUserID($this->_userID);
							$sendMail->SetSender($draftMail->GetHeaderValue('from'));
							$sendMail->SetRecipients($mailFrom);
							$sendMail->SetSubject(($subject = $draftMail->GetHeaderValue('subject')) != ''
									? $subject
									: '(no subject)');
							$sendMail->SetBodyStream($fp);

							// send, log
							if($sendMail->Send())
							{
								PutLog(sprintf('Sent draft response mail for <%s> (%d) to <%s>',
									$this->_userMail,
									$this->_userID,
									$mailFrom),
									PRIO_NOTE,
									__FILE__,
									__LINE__);
								if($this->_userObject->_row['forward_delete'] == 'yes')
									return(RECEIVE_RESULT_DELETE);
							}
							else
							{
								PutLog(sprintf('Failed to send draft response mail from <%s> (%d) to <%s>',
									$this->_userMail,
									$this->_userID,
									$mailFrom),
									PRIO_WARNING,
									__FILE__,
									__LINE__);
							}

							// close file
							fclose($fp);
						}

						fclose($draftMailFP);
					}
				}
			}
		}

		// autoresponder if nonspam and nonjunk
		if(($mail->flags&FLAG_SPAM) == 0
			&& strpos(strtolower($mail->GetHeaderValue('precedence')), 'junk') === false
			&& (trim($mail->GetHeaderValue('auto-submitted')) == '' || trim(strtolower($mail->GetHeaderValue('auto-submitted'))) == 'no')
			&& ($mailFrom = ExtractMailAddress($mail->GetHeaderValue('from'))) != ''
			&& $mail->GetHeaderValue('x-autoresponder') == ''
			&& (ExtractMailAddress($mail->GetHeaderValue('return-path')) != '' || $bm_prefs['returnpath_check'] == 'no')
			&& !$trusted)
		{
			$autoresponderSettings = $this->_userObject->GetAutoresponder();

			// autoresponder active?
			if($autoresponderSettings[0]
				&& $autoresponderSettings[3] != strtolower($mailFrom))
			{
				$arMail = _new('BMMailBuilder');
				$arMail->SetUserID($this->_userID);
				$arMail->AddHeaderField('From',				$this->_userMail);
				$arMail->AddHeaderField('To',				$mailFrom);
				$arMail->AddHeaderField('Subject',			$autoresponderSettings[1]);
				$arMail->AddHeaderField('X-Autoresponder',	'yes');
				$arMail->AddHeaderField('Precedence',		'junk');
				$arMail->AddHeaderField('Auto-Submitted',	'auto-replied');
				$arMail->AddText($autoresponderSettings[2],
					'plain',
					$this->_userObject->_row['charset']);
				$success = $arMail->Send() !== false;
				$arMail->CleanUp();

				// log
				if($success)
				{
					$this->_userObject->SetAutoresponderLastSend($mailFrom);
					Add2Stat('sysmail');
					PutLog(sprintf('Sent autoresponder from <%s> (%d) to <%s>',
						$this->_userMail,
						$this->_userID,
						$mailFrom),
						PRIO_DEBUG,
						__FILE__,
						__LINE__);
				}
				else
				{
					PutLog(sprintf('Failed to send autoresponder from <%s> (%d) to <%s>',
						$this->_userMail,
						$this->_userID,
						$mailFrom),
						PRIO_WARNING,
						__FILE__,
						__LINE__);
				}
			}
		}

		// store mail
		if($storeResult == STORE_RESULT_OK)
			$storeResult = $this->StoreMail($mail, $folder, $trainNoSpam);

		// spam & infection stats
		if(($mail->flags & FLAG_SPAM) != 0)
			Add2Stat('spam');
		if(($mail->flags & FLAG_INFECTED) != 0)
			Add2Stat('infected');

		// stats, logs
		if($storeResult == STORE_RESULT_OK)
		{
			$mail->id = $this->_lastInsertId;

			// mail2sms if (nonspam and mail2sms enabled) or (filter action flag is set)
			if((($this->_userObject->_row['mail2sms'] == 'yes' && ($mail->flags&FLAG_SPAM) == 0)
				|| ($filterActionFlags & FILTER_ACTIONFLAG_MAIL2SMS) != 0)
				&& strpos(strtolower($mail->GetHeaderValue('precedence')), 'junk') === false
				&& (trim($mail->GetHeaderValue('auto-submitted')) == '' || trim(strtolower($mail->GetHeaderValue('auto-submitted'))) == 'no')
				&& (ExtractMailAddress($mail->GetHeaderValue('return-path')) != '' || $bm_prefs['returnpath_check'] == 'no')
				&& strlen(trim($this->_userObject->_row['mail2sms_nummer'])) > 3)
			{
				$toNo = $this->_userObject->_row['mail2sms_nummer'];
				$smsText = GetPhraseForUser($this->_userID, 'lang_custom', 'mail2sms');
				$smsText = str_replace('%%abs%%', ExtractMailAddress($mail->GetHeaderValue('from')), $smsText);
				$smsText = str_replace('%%betreff%%', $mail->GetHeaderValue('subject'), $smsText);
				if(strlen($smsText) > 160)
					$smsText = substr($smsText, 0, 157) . '...';

				$sms = _new('BMSMS', array($this->_userID, &$this->_userObject));
				$sms->Send($bm_prefs['mail2sms_abs'], $toNo, $smsText, $bm_prefs['mail2sms_type'], true, true);
			}

			// notification
			if(($filterActionFlags & FILTER_ACTIONFLAG_NOTIFY) != 0)
			{
				$this->_userObject->PostNotification('notify_email',
					array(HTMLFormat(DecodeSingleEMail(ExtractMailAddress($mail->GetHeaderValue('from')))), HTMLFormat($mail->GetHeaderValue('subject'))),
					'email.read.php?id='.$mail->id.'&',
					'%%tpldir%%images/li/notify_email.png',
					0,
					0,
					NOTIFICATION_FLAG_USELANG,
					'::notifyEMail');
			}
			else if(($this->_userObject->_row['notify_email'] == 'yes' && ($mail->flags&FLAG_SPAM) == 0))
			{
				$unreadCount = $this->GetMailCount(-1, true);

				if($unreadCount)
				{
					$this->_userObject->PostNotification('notify_newemail',
						array($unreadCount, HTMLFormat($mail->GetHeaderValue('subject')) . ($unreadCount > 1 ? ', ...' : '')),
						'email.php?folder='.$folder.'&',
						'%%tpldir%%images/li/notify_newemail.png',
						0,
						0,
						NOTIFICATION_FLAG_USELANG,
						'::newEMail',
						true);
				}
			}

			// receive stats
			$oldOffset = ftell($mail->_fp);
			fseek($mail->_fp, 0, SEEK_END);
			$mailSize = ftell($mail->_fp);
			fseek($mail->_fp, 0, SEEK_SET);
			if(!$isUserPOP3)
			{
				$this->_userObject->AddRecvStat($mailSize);
			}

			// check abuse protect limits
			if($groupRow['abuseprotect'] == 'yes' && !$isUserPOP3)
			{
				$sendFreqPrefs = GetAbuseTypePrefs(BMAP_RECV_FREQ_LIMIT);
				if(isset($sendFreqPrefs['amount']) && isset($sendFreqPrefs['interval']))
				{
					$receivedMailsCount = $this->_userObject->GetReceivedMailsCount(time() - TIME_ONE_MINUTE * $sendFreqPrefs['interval']);
					if($receivedMailsCount > $sendFreqPrefs['amount'])
					{
						AddAbusePoint($this->_userID, BMAP_RECV_FREQ_LIMIT,
							sprintf($lang_admin['ap_comment_21'], $receivedMailsCount, $sendFreqPrefs['interval']));
					}
				}

				$sendTrafficPrefs = GetAbuseTypePrefs(BMAP_RECV_TRAFFIC_LIMIT);
				if(isset($sendTrafficPrefs['amount']) && isset($sendTrafficPrefs['interval']))
				{
					$receivedMailsSize = $this->_userObject->GetReceivedMailsSize(time() - TIME_ONE_MINUTE * $sendTrafficPrefs['interval']);
					if($receivedMailsSize > $sendTrafficPrefs['amount']*1024*1024)
					{
						AddAbusePoint($this->_userID, BMAP_RECV_TRAFFIC_LIMIT,
							sprintf($lang_admin['ap_comment_22'], $receivedMailsSize/1024/1024, $sendTrafficPrefs['interval']));
					}
				}
			}

			// module functions
			ModuleFunction('AfterReceiveMail', array(&$mail, &$this, &$this->_userObject));
			if(!$isUserPOP3)
			{
				Add2Stat('receive');
				$this->_userObject->UpdateLastReceive();
			}
			PutLog(sprintf('<%s> (%d%s) received mail from <%s>%s',
				$this->_userMail,
				$this->_userID,
				$aliasReceiver ? sprintf(', to alias/group <%s>', $aliasReceiver) : '',
				ExtractMailAddress($mail->GetHeaderValue('from')),
				$isUserPOP3 ? ' (via external user POP3 account)' : ''),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
		}
		else if($storeResult == STORE_RESULT_INTERNALERROR)
		{
			PutLog(sprintf('Failed to store mail for <%s> (%d) from <%s>: Internal error',
				$this->_userMail,
				$this->_userID,
				ExtractMailAddress($mail->GetHeaderValue('from'))),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
		}
		else if($storeResult == STORE_RESULT_MAILTOOBIG)
		{
			PutLog(sprintf('Failed to store mail for <%s> (%d) from <%s>: Mail too big',
				$this->_userMail,
				$this->_userID,
				ExtractMailAddress($mail->GetHeaderValue('from'))),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
		}
		else if($storeResult == STORE_RESULT_NOTENOUGHSPACE)
		{
			PutLog(sprintf('Failed to store mail for <%s> (%d) from <%s>: Not enough space left in user mailbox',
				$this->_userMail,
				$this->_userID,
				ExtractMailAddress($mail->GetHeaderValue('from'))),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
		}

		return($storeResult);
	}

	/**
	 * send cert mail
	 *
	 * @param int $mailID Mail ID
	 * @param BMMail $mailObj Mail object
	 * @return int
	 */
	function SendCertMail($mailID, $mailObj)
	{
		global $db, $bm_prefs;

		$count = 0;
		$recipients = ExtractMailAddresses(implode(' ', array(
				$mailObj->GetHeaderValue('to'),
				$mailObj->GetHeaderValue('cc'),
				$mailObj->GetHeaderValue('bcc')
			)));

		foreach($recipients as $to)
		{
			$db->Query('INSERT INTO {pre}certmails(mail,recipient,user,code,date) VALUES(?,?,?,?,?)',
				$mailID,
				$to,
				$this->_userID,
				$certMailCode = GenerateRandomKey('certMail:' . $to),
				time());

			if($certMailID = $db->InsertId())
			{
				$vars = array(
					'user_name'		=> $this->_userObject->_row['vorname'] . ' ' . $this->_userObject->_row['nachname'],
					'user_mail'		=> DecodeEMail(ExtractMailAddress($mailObj->GetHeaderValue('from'))),
					'date'			=> date('d.m.Y, H:i', time()+$bm_prefs['einsch_life']),
					'url'			=> sprintf('%sindex.php?action=readCertMail&id=%d&key=%s',
						$bm_prefs['selfurl'],
						$certMailID,
						$certMailCode)
				);

				if(SystemMail($mailObj->GetHeaderValue('from'),
					$to,
					$mailObj->GetHeaderValue('subject'),
					'certmail',
					$vars))
				{
					$count++;
				}
				else
				{
					$db->Query('DELETE FROM {pre}certmails WHERE id=?',
						$certMailID);
				}
			}
		}

		if($count)
			$this->FlagMail(FLAG_CERTMAIL, true, $mailID);

		return($count);
	}

	/**
	 * get cert mail
	 *
	 * @param int $id
	 * @param string $key
	 * @return BMMail
	 */
	static function GetCertMail($id, $key)
	{
		global $db, $bm_prefs;

		$id = (int)$id;
		$key = trim($key);

		if(strlen($key) == 32)
		{
			$res = $db->Query('SELECT id,mail,recipient,user,date,code,confirmation_sent FROM {pre}certmails WHERE id=? AND code=?',
				$id,
				$key);
			if($res->RowCount() != 1)
				return(false);
			$row = $res->FetchArray(MYSQLI_ASSOC);

			if($row['code'] == $key)
			{
				$userID = $row['user'];
				$userObject = _new('BMUser', array($userID));

				if($userObject)
				{
					$mailbox = _new('BMMailbox', array($userID, $userObject->_row['email'], $userObject));
					if($mailbox)
					{
						if($mailObject = $mailbox->GetMail($row['mail']))
						{
							if($row['confirmation_sent'] == 0)
							{
								//
								// send confirmation
								//
								$vars = array(
									'an'		=> DecodeEMail($row['recipient']),
									'subject'	=> $mailObject->GetHeaderValue('subject'),
									'date'		=> date($userObject->_row['datumsformat'])
								);
								SystemMail($bm_prefs['passmail_abs'],
									$userObject->_row['email'],
									GetPhraseForUser($row['user'], 'lang_custom', 'cs_subject'),
									'cs_text',
									$vars,
									$row['user']);

								//
								// log
								//
								PutLog(sprintf('Certified mail <%d> read by <%s> (user: %d, mail: %d, key: %s, IP: %s)',
									$id,
									$row['recipient'],
									$row['user'],
									$row['mail'],
									$key,
									$_SERVER['REMOTE_ADDR']),
									PRIO_NOTE,
									__FILE__,
									__LINE__);

								//
								// forward "real" mail to recipient
								//
								if($fp = fopen('php://temp', 'wb+'))
								{
									// add mail headers
									fprintf($fp, 'To: <%s>' . "\r\n", $row['recipient']);

									// copy original content
									$mailFP = $mailObject->GetMessageFP();
									$forwardInHeader = true;
									fseek($mailFP, 0, SEEK_SET);
									while(is_resource($mailFP) && !feof($mailFP))
									{
										$line = rtrim(fgets2($mailFP), "\r\n") . "\r\n";

										if($forwardInHeader && $line == "\r\n")
											$forwardInHeader = false;

										if(!$forwardInHeader ||
											(strtolower(substr($line, 0, 3)) != 'to:'
											&& strtolower(substr($line, 0, 3)) != 'cc:'
											&& strtolower(substr($line, 0, 4)) != 'bcc:'))
										{
											fwrite($fp, $line);
										}
									}

									// reset stream
									fseek($fp, 0, SEEK_SET);

									// send
									$sendMail = _new('BMSendMail');
									$sendMail->SetUserID($row['user']);
									$sendMail->SetSender($mailObject->GetHeaderValue('from'));
									$sendMail->SetRecipients($row['recipient']);
									$sendMail->SetSubject(($subject = $mailObject->GetHeaderValue('subject')) != ''
											? $subject
											: '(no subject)');
									$sendMail->SetBodyStream($fp);

									// send, log
									if($sendMail->Send())
									{
										PutLog(sprintf('Sent certified mail <%d> to <%s>',
											$row['id'],
											$row['recipient']),
											PRIO_NOTE,
											__FILE__,
											__LINE__);
									}
									else
									{
										PutLog(sprintf('Failed to send certified mail <%d> to <%s>',
											$row['id'],
											$row['recipient']),
											PRIO_WARNING,
											__FILE__,
											__LINE__);
									}

									// close file
									fclose($fp);
									fclose($mailFP);
								}

								//
								// update row
								//
								$db->Query('UPDATE {pre}certmails SET confirmation_sent=1 WHERE id=?',
									$id);
							}

							return($mailObject);
						}
					}
				}
			}
		}

		return(false);
	}

	/**
	 * get page list for template use
	 *
	 * @return array ($folderList, $pageMenu)
	 */
	function GetPageFolderList()
	{
		$folderList = $this->GetFolderList(true);
		$pageMenu = $idTable = array();
		$i = 0;
		foreach($folderList as $folderID=>$folder)
		{
			$idTable[$folderID] = $i;
			$pageMenu[] = array(
					'link'			=> 'email.php?folder=' . $folderID . '&sid=' . session_id(),
					'link_noSID'	=> 'email.php?folder=' . $folderID . '&sid=',
					'text'			=> $folder['title'],
					'icon'			=> $folder['type'],
					'intelligent'	=> $folder['intelligent'],
					'unread'		=> $folder['unread'],
					'id'			=> $folderID,
					'i'				=> $i,
					'parent'		=> $folder['parent']
			);
			$i++;
		}
		foreach($pageMenu as $key=>$val)
		{
			if($val['parent'] == 0)
				$pageMenu[$key]['parent'] = -1;
			else if(isset($idTable[$val['parent']]))
				$pageMenu[$key]['parent'] = $idTable[$val['parent']];
			else
				$pageMenu[$key]['parent'] = -1;
		}
		return(array($folderList, $pageMenu));
	}

	/**
	 * check if user is allowed to access an email.
	 *
	 * @param int $mailID Mail ID
	 * @param bool $writeAccess Also check for write access?
	 * @return bool
	 */
	function MailAccessAllowed($mailID, $writeAccess)
	{
		global $db;

		if(is_array($mailID))
		{
			if(count($mailID) == 0)
				return(false);

			$okCount = 0;

			$res = $db->Query('SELECT id,folder,userid FROM {pre}mails WHERE `id` IN ?',
				$mailID);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				if($row['userid'] == $this->_userID)
				{
					$okCount++;
				}
				else
				{
					if(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $row['folder'], $writeAccess))
						$okCount++;
					else
						break;
				}
			}
			$res->Free();

			return($okCount == count($mailID));
		}
		else
		{
			$res = $db->Query('SELECT folder,userid FROM {pre}mails WHERE `id`=?',
				$mailID);
			if($res->RowCount() != 1)
				return(false);
			list($folderID, $userID) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			if($userID == $this->_userID)
				return(true);

			return(BMWorkgroup::AccessAllowed($this->_userID, WORKGROUP_TYPE_MAILFOLDER, $folderID, $writeAccess));
		}
	}
}
