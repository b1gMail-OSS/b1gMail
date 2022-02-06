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

include('../serverlib/init.inc.php');
if(!class_exists('BMTodo'))
	include('../serverlib/todo.class.php');
RequestPrivileges(PRIVILEGES_USER | PRIVILEGES_MOBILE);

/**
 * todo interface
 */
$todo = _new('BMTodo', array($userRow['id']));

/**
 * assign
 */
$tpl->assign('activeTab', 	'tasks');

/**
 * default action = inbox
 */
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'list';

/**
 * list
 */
if($_REQUEST['action'] == 'list')
{
	if(!isset($_REQUEST['list']))
		$taskListID = 0;
	else
		$taskListID = (int)$_REQUEST['list'];

	$taskLists = $todo->GetTaskLists();
	if(!isset($taskLists[ $taskListID ]))
	{
		$taskListID 	= 0;
		$taskListTitle 	= $lang_user['todo'];
	}
	else
	{
		$taskListTitle	= $taskLists[ $taskListID ]['title'];
	}

	// add
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add' && isset($_POST['titel']))
	{
		$title = trim($_REQUEST['titel']);

		if(!empty($title))
			$todo->Add(time(),
				time()+TIME_ONE_DAY,
				TASKS_NOTBEGUN,
				$title,
				0,
				0,
				'',
				$taskListID);
	}

	// change
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'save' && isset($_REQUEST['id'])
		&& isset($_POST['titel']))
	{
		$taskID = (int)$_REQUEST['id'];
		$task = $todo->GetTask($taskID);

		$todo->Change($taskID,
			$task['beginn'], $task['faellig'],
			$_POST['akt_status'], $_POST['titel'],
			$_POST['priority'], $task['erledigt'],
			$_POST['comments'], $task['tasklistid']);
	}

	$todoList = $todo->GetTodoList('(akt_status=64) ASC,priority DESC,faellig', 'ASC', -1, $taskListID);

	$tpl->assign('taskListID', $taskListID);
	$tpl->assign('list', $todoList);
	$tpl->assign('pageTitle', $taskListTitle);
	$tpl->assign('page', 'm/tasks.list.tpl');
	$tpl->display('m/index.tpl');
}

/**
 * lists
 */
else if($_REQUEST['action'] == 'lists')
{
	$lists = $todo->GetTaskLists();

	$tpl->assign('lists', $lists);
	$tpl->assign('pageTitle', $lang_user['tasklists']);
	$tpl->assign('page', 'm/tasks.lists.tpl');
	$tpl->display('m/index.tpl');
}

/**
 * edit
 */
else if($_REQUEST['action'] == 'edit' && isset($_REQUEST['id']))
{
	$taskID = (int)$_REQUEST['id'];
	$task = $todo->GetTask($taskID);
	$taskListID = (int)$task['tasklistid'];

	$taskLists = $todo->GetTaskLists();
	if(!isset($taskLists[ $taskListID ]))
	{
		$taskListID 	= 0;
		$taskListTitle 	= $lang_user['todo'];
	}
	else
	{
		$taskListTitle	= $taskLists[ $taskListID ]['title'];
	}

	$tpl->assign('taskListID', $taskListID);
	$tpl->assign('taskListTitle', $taskListTitle);
	$tpl->assign('task', $task);
	$tpl->assign('pageTitle', $lang_user['task'] . ': ' . HTMLFormat($task['titel']));
	$tpl->assign('page', 'm/tasks.edit.tpl');
	$tpl->display('m/index.tpl');
}

/**
 * add
 */
else if($_REQUEST['action'] == 'add' && isset($_REQUEST['list']))
{
	$tpl->assign('taskListID', (int)$_REQUEST['list']);
	$tpl->assign('isDialog', true);
	$tpl->assign('page', 'm/tasks.add.tpl');
	$tpl->display('m/index.tpl');
}

/**
 * set done RPC
 */
else if($_REQUEST['action'] == 'setTaskDone' && isset($_REQUEST['id']))
{
	$result = $todo->SetStatus((int)$_REQUEST['id'],
		$_REQUEST['done'] == 'true' ? TASKS_DONE : TASKS_PROCESSING);
	die($result);
}
?>