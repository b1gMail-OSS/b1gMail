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
include('../serverlib/mailbox.class.php');
RequestPrivileges(PRIVILEGES_ADMIN);
AdminRequirePrivilege('maintenance');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'inactive';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['inactiveusers'],
		'relIcon'	=> 'user_inactive32.png',
		'link'		=> 'maintenance.php?',
		'active'	=> $_REQUEST['action'] == 'inactive'
	),
	1 => array(
		'title'		=> $lang_admin['trash'],
		'relIcon'	=> 'trash32.png',
		'link'		=> 'maintenance.php?action=trash&',
		'active'	=> $_REQUEST['action'] == 'trash'
	),
	2 => array(
		'title'		=> $lang_admin['orphans'],
		'relIcon'	=> 'orphans32.png',
		'link'		=> 'maintenance.php?action=orphans&',
		'active'	=> $_REQUEST['action'] == 'orphans'
	)
);

if(FTS_SUPPORT)
{
	$tabs[] = array(
		'title'		=> $lang_admin['ftsindex'],
		'relIcon'	=> 'search32.png',
		'link'		=> 'maintenance.php?action=fts&',
		'active'	=> $_REQUEST['action'] == 'fts'
	);
}

if($bm_prefs['receive_method'] == 'pop3')
{
	$tabs[] = array(
		'title'		=> $lang_admin['pop3gateway'],
		'relIcon'	=> 'fetch.png',
		'link'		=> 'maintenance.php?action=pop3gateway&',
		'active'	=> $_REQUEST['action'] == 'pop3gateway'
	);
}

/**
 * inactive users
 */
if($_REQUEST['action'] == 'inactive')
{
	//
	// form
	//
	if(!isset($_REQUEST['do']))
	{
		// assign
		$tpl->assign('groups', BMGroup::GetSimpleGroupList());
		$tpl->assign('page', 'maintenance.inactive.tpl');
	}

	//
	// exec
	//
	else if($_REQUEST['do'] == 'exec')
	{
		// conditions
		$condition = array();
		if(isset($_REQUEST['queryTypeLogin']))
		{
			$timeDiff = time() - max(1, $_REQUEST['loginDays']) * TIME_ONE_DAY;
			$condition[] = sprintf('((lastlogin!=0 AND lastlogin<%d AND last_pop3<%d AND last_imap<%d AND last_smtp<%d) OR (lastlogin=0 AND last_pop3=0 AND last_imap=0 AND last_smtp=0 AND reg_date!=0 AND reg_date<%d))',
				$timeDiff, $timeDiff, $timeDiff, $timeDiff, $timeDiff);
		}
		if(isset($_REQUEST['queryTypeGroups'])
			&& isset($_REQUEST['groups'])
			&& is_array($_REQUEST['groups'])
			&& count($_REQUEST['groups']) > 0)
		{
			$condition[] = '(gruppe IN (' . implode(',', array_keys($_REQUEST['groups'])) . '))';
		}

		// conditions given?
		if(count($condition) == 0)
		{
			header('Location: maintenance.php?sid=' . session_id());
			exit();
		}
		$condition = 'WHERE `id`!=1 AND (' . implode(' AND ', $condition) . ')';

		// update
		$affectedUsers = 0;
		$action = $_REQUEST['queryAction'];

		if($action == 'show')
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
						: 'lastactivity';
			$sortOrder = isset($_REQUEST['sortOrder'])
							? strtolower($_REQUEST['sortOrder'])
							: 'asc';
			$perPage = max(1, isset($_REQUEST['perPage'])
							? (int)$_REQUEST['perPage']
							: 50);
			if($sortBy == 'lastactivity')
				$qSortBy = 'GREATEST(`lastlogin`,`last_pop3`,`last_imap`,`last_smtp`,`reg_date`)';
			else
				$qSortBy = $sortBy;

			// page calculation
			$res = $db->Query('SELECT COUNT(*) FROM {pre}users ' . $condition);
			list($userCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
			$pageCount = ceil($userCount / $perPage);
			$pageNo = isset($_REQUEST['page'])
						? max(1, min($pageCount, (int)$_REQUEST['page']))
						: 1;
			$startPos = max(0, min($perPage*($pageNo-1), $userCount));

			// query
			$groups = BMGroup::GetSimpleGroupList();
			$users = array();
			$res = $db->Query('SELECT * FROM {pre}users ' . $condition . ' ORDER BY ' . $qSortBy . ' '  . $sortOrder . ' LIMIT ' . $startPos . ',' . $perPage);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$aliases = array();
				$aliasRes = $db->Query('SELECT email FROM {pre}aliase WHERE type=? AND user=? ORDER BY email ASC',
					ALIAS_RECIPIENT|ALIAS_SENDER,
					$row['id']);
				while($aliasRow = $aliasRes->FetchArray())
					$aliases[] = $aliasRow['email'];
				$aliasRes->Free();

				$row['groupName'] 	= isset($groups[$row['gruppe']])
										? $groups[$row['gruppe']]['title']
										: $lang_admin['missing'];
				$row['aliases'] 	= count($aliases) > 0
										? implode(', ', $aliases)
										: '';
				if($row['lastlogin'] == 0 && $row['gesperrt'] == 'no')
				{
					$row['status'] 		= $statusTable['registered'];
					$row['statusImg'] 	= $statusImgTable['registered'];
				}
				else
				{
					$row['status'] 		= $statusTable[$row['gesperrt']];
					$row['statusImg'] 	= $statusImgTable[$row['gesperrt']];
				}
				$row['lastActivity']= max($row['lastlogin'], $row['last_pop3'], $row['last_imap'], $row['last_smtp'], $row['reg_date']);
				$users[$row['id']] 	= $row;
			}
			$res->Free();

			$tpl->assign('pageNo', $pageNo);
			$tpl->assign('pageCount', $pageCount);
			$tpl->assign('sortBy', $sortBy);
			$tpl->assign('sortOrder', $sortOrder);
			$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
			$tpl->assign('users', $users);
			$tpl->assign('groups', $groups);
			$tpl->assign('perPage', $perPage);
			$tpl->assign('page', 'maintenance.inactive.list.tpl');
		}
		else
		{
			if($action == 'lock')
			{
				$db->Query('UPDATE {pre}users SET gesperrt=? ' . $condition,
					'yes');
				$affectedUsers = $db->AffectedRows();
			}
			else if($action == 'move')
			{
				$db->Query('UPDATE {pre}users SET gruppe=? ' . $condition,
					$_REQUEST['moveGroup']);
				$affectedUsers = $db->AffectedRows();
			}
			else if($action == 'delete')
			{
				$db->Query('UPDATE {pre}users SET gesperrt=? ' . $condition,
					'delete');
				$affectedUsers = $db->AffectedRows();
			}

			// assign
			$tpl->assign('msgTitle', $lang_admin['inactiveusers']);
			$tpl->assign('msgText', sprintf($lang_admin['activity_done'], $affectedUsers));
			$tpl->assign('msgIcon', 'info32');
			$tpl->assign('backLink', 'maintenance.php?');
			$tpl->assign('page', 'msg.tpl');
		}
	}
}

/**
 * trash
 */
else if($_REQUEST['action'] == 'trash')
{
	//
	// form
	//
	if(!isset($_REQUEST['do']))
	{
		// assign
		$tpl->assign('groups', BMGroup::GetSimpleGroupList());
		$tpl->assign('page', 'maintenance.trash.tpl');
	}

	//
	// exec
	//
	else if($_REQUEST['do'] == 'exec')
	{
		if(!isset($_REQUEST['groups']) || !is_array($_REQUEST['groups'])
			|| count($_REQUEST['groups']) < 1)
		{
			die('DONE');
		}

		$perPage = max(isset($_REQUEST['perpage']) ? (int)$_REQUEST['perpage'] : 50, 1);
		$pos = (int)$_REQUEST['pos'];

		$res = $db->Query('SELECT COUNT(*) FROM {pre}users WHERE gruppe IN(' . implode(',', array_keys($_REQUEST['groups'])) . ')');
		list($count) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		if($pos >= $count)
		{
			die('DONE');
		}
		else
		{
			$mails = $mailSizes = 0;
			$res = $db->Query('SELECT id,email FROM {pre}users WHERE gruppe IN(' . implode(',', array_keys($_REQUEST['groups'])) . ') ORDER BY id ASC LIMIT '
				. (int)$pos . ',' . (int)$perPage);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$user = _new('BMUser', array($row['id']));
				$mailbox = _new('BMMailbox', array($row['id'], $row['email'], $user));

				$trashMails = $mailbox->GetMailList(FOLDER_TRASH);
				foreach($trashMails as $mailID=>$mail)
				{
					if((!isset($_REQUEST['daysOnly'])
						|| $mail['timestamp'] < time()-max(1, $_REQUEST['days'])*TIME_ONE_DAY)
						&& (!isset($_REQUEST['sizesOnly'])
							|| $mail['size'] > max(1, $_REQUEST['size']) * 1024))
					{
						// delete
						$mailbox->DeleteMail($mailID);

						// stats
						$mails++;
						$mailSizes += $mail['size'];
					}
				}

				unset($mailbox);
				unset($user);

				$pos++;
			}
			$res->Free();

			if($pos >= $count)
				die('DONE');
			else
				die($pos . '/' . $count);
		}

		// assign
		$tpl->assign('msgTitle', $lang_admin['trash']);
		$tpl->assign('msgText', sprintf($lang_admin['trash_done'], $mails, round($mailSizes/1024/1024, 2)));
		$tpl->assign('msgIcon', 'info32');
		$tpl->assign('backLink', 'maintenance.php?action=trash&');
		$tpl->assign('page', 'msg.tpl');
	}
}

/**
 * full-text search index
 */
else if($_REQUEST['action'] == 'fts' && FTS_SUPPORT)
{
	if(!class_exists('BMSearchIndex'))
		include(B1GMAIL_DIR . 'serverlib/searchindex.class.php');

	//
	// form
	//
	if(!isset($_REQUEST['do']))
	{
		$tpl->assign('page', 'maintenance.fts.tpl');
	}

	//
	// build index
	//
	else if($_REQUEST['do'] == 'buildIndex')
	{
		$perPage = max(1, $_REQUEST['perpage']);
		$qPart = ' FROM {pre}mails '
			. 'INNER JOIN {pre}users ON {pre}mails.`userid`={pre}users.`id` '
			. 'INNER JOIN {pre}gruppen ON {pre}gruppen.`id`={pre}users.`gruppe` '
			. 'WHERE ({pre}mails.`flags`&'.(FLAG_INDEXED|FLAG_DECEPTIVE).')=0 AND {pre}gruppen.`ftsearch`=\'yes\'';

		if(!isset($_REQUEST['all']))
		{
			$res = $db->Query('SELECT COUNT(*)' . $qPart);
			while($row = $res->FetchArray(MYSQLI_NUM))
			{
				$all = $row[0];
			}
			$res->Free();
		}
		else
			$all = max(0, (int)$_REQUEST['all']);

		if(!isset($all) || $all == 0)
			die('DONE');

		$processedMails = 0;
		$currentUserID = 0;
		$currentMailbox = false;
		$currentIndex = false;

		$res = $db->Query('SELECT {pre}mails.`id` AS `id`,{pre}mails.`userid` AS `userid`' . $qPart . ' ORDER BY {pre}mails.`userid` ASC LIMIT ' . (int)$perPage);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$success = false;

			if($currentUserID != $row['userid'])
			{
				$currentUserID = $row['userid'];
				$userObject = _new('BMUser', array($currentUserID));
				$userRow = $userObject->Fetch();

				if(is_array($userRow))
				{
					if(is_object($currentIndex))
						$currentIndex->endTx();

					$currentMailbox = _new('BMMailbox', array($currentUserID, $userRow['email'], $userObject));
					$currentIndex = _new('BMSearchIndex', array($currentUserID));
					$currentIndex->beginTx();
				}
			}

			if(is_object($currentMailbox) && is_object($currentIndex))
			{
				$mail = $currentMailbox->GetMail($row['id']);
				if(is_object($mail))
				{
					$mail->AddToIndex($currentIndex);
					$currentMailbox->FlagMail(FLAG_INDEXED, true, $mail->id);
					unset($mail);
					$success = true;
				}
			}

			if(!$success)
			{
				// flag mail as indexed even if we failed to index it
				// it is probably corrupt and we do not want to process it again in the next iteration
				$db->Query('UPDATE {pre}mails SET `flags`=`flags`|'.FLAG_INDEXED.' WHERE `id`=?',
					$row['id']);
			}

			++$processedMails;
		}
		$res->Free();

		if(is_object($currentIndex))
		{
			$currentIndex->endTx();
			unset($currentIndex);
		}

		if($processedMails == 0)
			echo 'DONE';
		else
			printf('%d/%d', $processedMails, $all);
		exit;
	}

	//
	// optimize index
	//
	else if($_REQUEST['do'] == 'optimizeIndex')
	{
		$perPage = max(1, $_REQUEST['perpage']);
		$pos = !isset($_REQUEST['pos']) ? 0 : max(0, $_REQUEST['pos']);
		$qPart = ' FROM {pre}users '
			. 'INNER JOIN {pre}gruppen ON {pre}gruppen.`id`={pre}users.`gruppe` '
			. 'WHERE {pre}gruppen.`ftsearch`=\'yes\'';

		if(!isset($_REQUEST['all']))
		{
			$res = $db->Query('SELECT COUNT(*)' . $qPart);
			while($row = $res->FetchArray(MYSQLI_NUM))
			{
				$all = $row[0];
			}
			$res->Free();
		}
		else
			$all = max(0, (int)$_REQUEST['all']);

		if(!isset($all) || $all == 0)
			die('DONE');

		$res = $db->Query('SELECT {pre}users.`id` AS `userid`' . $qPart . ' ORDER BY {pre}users.`id` ASC LIMIT ' . (int)$pos . ',' . (int)$perPage);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$idx = _new('BMSearchIndex', array($row['userid']));
			if(is_object($idx))
				$idx->optimize();
			++$pos;
		}
		$res->Free();

		if($pos >= $all)
			echo 'DONE';
		else
			printf('%d/%d', $pos, $all);
		exit;
	}
}

/**
 * orphaned emails
 */
else if($_REQUEST['action'] == 'orphans')
{
	//
	// form
	//
	if(!isset($_REQUEST['do']))
	{
		$tpl->assign('page', 'maintenance.orphans.tpl');
	}

	//
	// exec (mail)
	//
	else if($_REQUEST['do'] == 'exec')
	{
		$deletedCount = $deletedSize = 0;

		$res = $db->Query('SELECT `id`,`size`,`blobstorage`,`userid` FROM {pre}mails WHERE `userid`!=-1 AND `userid` NOT IN(SELECT `id` FROM {pre}users)');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			BMBlobStorage::createProvider($row['blobstorage'], $row['userid'])->deleteBlob(BMBLOB_TYPE_MAIL, $row['id']);

			$db->Query('DELETE FROM {pre}mails WHERE `id`=?',
					   $row['id']);
			$db->Query('DELETE FROM {pre}attachments WHERE `mailid`=?',
					   $row['id']);

			$deletedCount++;
			$deletedSize += $row['size'];
		}
		$res->Free();

		// assign
		$tpl->assign('msgTitle', $lang_admin['mailorphans']);
		$tpl->assign('msgText', sprintf($lang_admin['orphans_done'], $deletedCount, $deletedSize/1024));
		$tpl->assign('msgIcon', 'info32');
		$tpl->assign('backLink', 'maintenance.php?action=orphans&');
		$tpl->assign('page', 'msg.tpl');
	}

	//
	// exec (disk)
	//
	else if($_REQUEST['do'] == 'diskExec')
	{
		$deletedCount = $deletedSize = 0;

		$res = $db->Query('SELECT `id`,`size`,`blobstorage`,`user` FROM {pre}diskfiles WHERE `user`!=-1 AND `user` NOT IN(SELECT `id` FROM {pre}users)');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			BMBlobStorage::createProvider($row['blobstorage'], $row['user'])->deleteBlob(BMBLOB_TYPE_WEBDISK, $row['id']);

			$db->Query('DELETE FROM {pre}diskfiles WHERE `id`=?',
					   $row['id']);

			$deletedCount++;
			$deletedSize += $row['size'];
		}
		$res->Free();

		// assign
		$tpl->assign('msgTitle', $lang_admin['diskorphans']);
		$tpl->assign('msgText', sprintf($lang_admin['orphans_done'], $deletedCount, $deletedSize/1024));
		$tpl->assign('msgIcon', 'info32');
		$tpl->assign('backLink', 'maintenance.php?action=orphans&');
		$tpl->assign('page', 'msg.tpl');
	}
}

/**
 * pop3 gateway
 */
else if($_REQUEST['action'] == 'pop3gateway')
{
	// fetch
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'fetch')
	{
		$perPage = max(isset($_REQUEST['perpage']) ? (int)$_REQUEST['perpage'] : 50, 1);
		if(!class_exists('BMPOP3Gateway'))
			include(B1GMAIL_REL . 'serverlib/pop3gateway.class.php');

		$pop3Gateway = _new('BMPOP3Gateway');
		list($mailCount, $processedMails) = $pop3Gateway->Run($perPage);

		if($mailCount == 0 || $mailCount == 0)
		{
			die('DONE');
		}
		else
		{
			die($processedMails . '/' . $mailCount);
		}
	}

	// assign
	$tpl->assign('page', 'maintenance.pop3gateway.tpl');
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['maintenance']);
$tpl->display('page.tpl');
