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
include('./serverlib/notes.class.php');
RequestPrivileges(PRIVILEGES_USER);

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));
/**
 * organizer enabled?
 */
if($groupRow['organizer']=='no')
{
	SessionRedirect('start.php');
	exit();
}
/**
 * default action = start
 */
$tpl->addJSFile('li', $tpl->tplDir . 'js/organizer.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'start';
$tpl->assign('activeTab', 'notes');
$tpl->assign('pageTitle', $lang_user['notes']);

/**
 * notes interface
 */
$notes = _new('BMNotes', array($userRow['id']));

/**
 * page menu
 */
$tpl->assign('pageMenuFile', 'li/organizer.sidebar.tpl');

/**
 * start page
 */
if($_REQUEST['action'] == 'start')
{
	$sortColumns = array('date', 'priority', 'text');

	// get sort info
	$sortColumn = (isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], $sortColumns))
					? $_REQUEST['sort']
					: 'date';
	$sortOrder = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')))
					? $_REQUEST['order']
					: 'desc';
	$sortOrderFA = ($sortOrder=="desc")?'fa-arrow-down': 'fa-arrow-up';

	// note list
	$noteList = $notes->GetNoteList($sortColumn, $sortOrder);
	foreach($noteList as $noteID => $note)
	{
		$raw = (string)$note['text'];
		$parts = preg_split("/\r\n|\n|\r/", $raw, 2);
		$firstLine = isset($parts[0]) ? trim($parts[0]) : '';
		if($firstLine === '')
			$firstLine = trim(preg_replace('/\s+/u', ' ', $raw));
		// List view only shows a short teaser; full text via AJAX preview
		$noteList[$noteID]['text'] = $firstLine;
	}

	// page output
	if(isset($_REQUEST['show']))
		$tpl->assign('showID', (int)$_REQUEST['show']);
	$tpl->assign('noteList', $noteList);
	$tpl->assign('canShareNotes', bmOrganizerGroupCanShare('notes'));
	$tpl->assign('sortColumn', $sortColumn);
	$tpl->assign('sortOrder', $sortOrderFA);
	$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
	$tpl->assign('pageContent', 'li/organizer.notes.tpl');
	$tpl->display('li/index.tpl');
	exit();
}

/**
 * get note text for AJAX preview
 */
else if($_REQUEST['action'] == 'getNoteText'
		&& isset($_REQUEST['id']))
{
	$noteInfo = $notes->GetNote((int)$_REQUEST['id']);
	if($noteInfo !== false)
	{
		die($noteInfo['text']);
	}
	die('Unknown note');
}

/**
 * action
 */
else if($_REQUEST['action'] == 'action'
		&& isset($_REQUEST['do']))
{
	if($_REQUEST['do'] == 'delete')
	{
		// F2: bulk-delete requires CSRF token so <a href>-based click
		// tricks can't bulk-wipe notes.
		CsrfEnforceOnStateChange();
		foreach($_POST as $key=>$val)
		{
			if(substr($key, 0, 5) == 'note_')
			{
				$id = substr($key, 5);
				$notes->Delete($id);
			}
		}
	}
	SessionRedirect('organizer.notes.php');
}

/**
 * delete note — F2: CSRF-token required.
 */
else if($_REQUEST['action'] == 'deleteNote'
		&& isset($_REQUEST['id']))
{
	CsrfEnforceOnStateChange();
	$notes->Delete((int)$_REQUEST['id']);
	SessionRedirect('organizer.notes.php');
}

/**
 * add note
 */
else if($_REQUEST['action'] == 'addNote')
{
	$tpl->assign('pageTitle', $lang_user['addnote']);
	$tpl->assign('pageContent', 'li/organizer.notes.edit.tpl');
	$tpl->display('li/index.tpl');
	exit();
}

/**
 * create note
 */
else if($_REQUEST['action'] == 'createNote'
		&& isset($_REQUEST['priority'])
		&& isset($_REQUEST['text'])
		&& IsPOSTRequest())
{
	$notes->Add((int)$_REQUEST['priority'], $_REQUEST['text']);
	SessionRedirect('organizer.notes.php');
}

/**
 * edit note
 */
else if($_REQUEST['action'] == 'editNote'
		&& isset($_REQUEST['id']))
{
	$noteInfo = $notes->GetNote((int)$_REQUEST['id']);
	if($noteInfo !== false)
	{
		$tpl->assign('pageTitle', $lang_user['editnote']);
		$tpl->assign('pageContent', 'li/organizer.notes.edit.tpl');
		$tpl->assign('note', $noteInfo);
		$tpl->display('li/index.tpl');
		exit();
	}
}

/**
 * save note
 */
else if($_REQUEST['action'] == 'saveNote'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['priority'])
		&& isset($_REQUEST['text'])
		&& IsPOSTRequest())
{
	$notes->Change((int)$_REQUEST['id'], (int)$_REQUEST['priority'], $_REQUEST['text']);
	SessionRedirect('organizer.notes.php');
}

/**
 * share note
 */
else if($_REQUEST['action'] == 'share' && isset($_REQUEST['id']))
{
	if(!bmOrganizerGroupCanShare('notes'))
	{
		SessionRedirect('organizer.notes.php');
		exit();
	}
	$note = $notes->GetNote((int)$_REQUEST['id']);
	$access = $notes->GetNoteAccess((int)$_REQUEST['id']);
	if($note === false || $access === false || !empty($access['shared']))
	{
		SessionRedirect('organizer.notes.php');
		exit();
	}
	$shareItem = array(
		'id' => (int)$note['id'],
		'title' => trim(preg_split("/\r\n|\n|\r/", (string)$note['text'], 2)[0])
	);
	if($shareItem['title'] === '')
		$shareItem['title'] = $lang_user['note'];

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add' && isset($_REQUEST['email']) && IsPOSTRequest())
	{
		$targetId = bmOrganizerShareTargetUserId($_REQUEST['email']);
		$accessVal = isset($_REQUEST['access']) ? $_REQUEST['access'] : BM_ORGANIZER_ACCESS_READ;
		$result = bmOrganizerAddShare(BM_ORGANIZER_SHARE_NOTE, (int)$note['id'], $userRow['id'], $targetId, $accessVal);
		if(is_string($result) && isset($lang_user[$result]))
			$tpl->assign('shareError', $lang_user[$result]);
		else if((int)$result > 0)
		{
			bmOrganizerNotifyShareInvite(BM_ORGANIZER_SHARE_NOTE, $shareItem, $userRow['id'], $targetId, $accessVal);
			$tpl->assign('shareSuccess', $lang_user['shareinvited']);
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'remove'
		&& isset($_REQUEST['share'])
		&& IsPOSTRequest())
	{
		// F2: revoking a note share was GET-reachable.
		bmOrganizerRemoveShare(BM_ORGANIZER_SHARE_NOTE, (int)$_REQUEST['share'], $userRow['id']);
	}

	$tpl->assign('shareItem', $shareItem);
	$tpl->assign('shareList', bmOrganizerListShares(BM_ORGANIZER_SHARE_NOTE, (int)$note['id'], $userRow['id']));
	$tpl->assign('shareType', 'note');
	$tpl->assign('shareAction', 'organizer.notes.php');
	$tpl->display('li/organizer.share.dialog.tpl');
	exit();
}

/**
 * leave shared note
 */
else if($_REQUEST['action'] == 'leaveshare' && isset($_REQUEST['id']))
{
	$access = $notes->GetNoteAccess((int)$_REQUEST['id']);
	if($access === false || empty($access['shared']))
	{
		SessionRedirect('organizer.notes.php');
		exit();
	}
	$note = $notes->GetNote((int)$_REQUEST['id']);
	$title = $note ? trim(preg_split("/\r\n|\n|\r/", (string)$note['text'], 2)[0]) : $lang_user['note'];
	$leaveItem = array('id' => (int)$_REQUEST['id'], 'title' => $title);

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'leave' && IsPOSTRequest())
	{
		bmOrganizerLeaveShare(BM_ORGANIZER_SHARE_NOTE, (int)$_REQUEST['id'], $userRow['id']);
		SessionRedirect('organizer.notes.php');
		exit();
	}

	$tpl->assign('leaveItem', $leaveItem);
	$tpl->assign('leaveAction', 'organizer.notes.php');
	$tpl->display('li/organizer.share.leave.tpl');
	exit();
}

SessionRedirect('organizer.notes.php');
