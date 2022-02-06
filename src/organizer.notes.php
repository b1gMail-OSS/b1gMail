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
include('./serverlib/notes.class.php');
include('./serverlib/todo.class.php');
RequestPrivileges(PRIVILEGES_USER);

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));

/**
 * default action = start
 */
$tpl->addJSFile('li', $tpl->tplDir . 'js/organizer.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'start';
$tpl->assign('activeTab', 'organizer');
$tpl->assign('pageTitle', $lang_user['notes']);

/**
 * notes interface
 */
$notes = _new('BMNotes', array($userRow['id']));

/**
 * page menu
 */
$todo = _new('BMTodo', array($userRow['id']));
$sideTasks = $todo->GetTodoList('faellig', 'asc', 6, 0, true);
$tpl->assign('tasks_haveMore', count($sideTasks) > 5);
if(count($sideTasks) > 5)
	$sideTasks = array_slice($sideTasks, 0, 5);
$tpl->assign('tasks', $sideTasks);
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

	// note list
	$noteList = $notes->GetNoteList($sortColumn, $sortOrder);

	// page output
	if(isset($_REQUEST['show']))
		$tpl->assign('showID', (int)$_REQUEST['show']);
	$tpl->assign('noteList', $noteList);
	$tpl->assign('sortColumn', $sortColumn);
	$tpl->assign('sortOrder', $sortOrder);
	$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
	$tpl->assign('pageContent', 'li/organizer.notes.tpl');
	$tpl->display('li/index.tpl');
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
		foreach($_POST as $key=>$val)
		{
			if(substr($key, 0, 5) == 'note_')
			{
				$id = substr($key, 5);
				$notes->Delete($id);
			}
		}
	}
	header('Location: organizer.notes.php?sid=' . session_id());
}

/**
 * delete note
 */
else if($_REQUEST['action'] == 'deleteNote'
		&& isset($_REQUEST['id']))
{
	$notes->Delete((int)$_REQUEST['id']);
	header('Location: organizer.notes.php?sid=' . session_id());
}

/**
 * add note
 */
else if($_REQUEST['action'] == 'addNote')
{
	$tpl->assign('pageTitle', $lang_user['addnote']);
	$tpl->assign('pageContent', 'li/organizer.notes.edit.tpl');
	$tpl->display('li/index.tpl');
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
	header('Location: organizer.notes.php?sid=' . session_id());
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
	header('Location: organizer.notes.php?sid=' . session_id());
}
?>