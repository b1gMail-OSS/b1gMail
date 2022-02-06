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
$tpl->addJSFile('li', 'clientlib/selectable.js');
$tpl->addJSFile('li', $tpl->tplDir . 'js/organizer.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'start';
$tpl->assign('activeTab', 'organizer');
$tpl->assign('pageTitle', $lang_user['todolist']);

/**
 * todo interface
 */
$todo = _new('BMTodo', array($userRow['id']));

/**
 * page menu
 */
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
	if(isset($_REQUEST['taskListID']))
		$taskListID = (int)$_REQUEST['taskListID'];
	else
		$taskListID = 0;

	/**
	 * set task status RPC
	 */
	if(isset($_REQUEST['do'])
			&& $_REQUEST['do'] == 'setTaskDone'
			&& isset($_REQUEST['id'])
			&& isset($_REQUEST['done']))
	{
		$result = $todo->SetStatus((int)$_REQUEST['id'], $_REQUEST['done'] == 'true' ? TASKS_DONE : TASKS_PROCESSING);
		if(!isset($_REQUEST['listOnly']))
			die($result);
	}

	/**
	 * add task RPC
	 */
	if(isset($_REQUEST['do'])
			&& $_REQUEST['do'] == 'addTask'
			&& isset($_REQUEST['title']))
	{
		$result = $todo->Add(time(),
					time()+TIME_ONE_DAY,
					TASKS_NOTBEGUN,
					$_REQUEST['title'],
					0,
					0,
					'',
					$taskListID);
		if(!isset($_REQUEST['listOnly']))
			die($result);
	}

	/**
	 * move tasks RPC
	 */
	if(isset($_REQUEST['do'])
			&& $_REQUEST['do'] == 'moveTasks'
			&& isset($_REQUEST['tasks'])
			&& isset($_REQUEST['destID']))
	{
		$tasks = explode(',', $_REQUEST['tasks']);
		$result = $todo->MoveTasks($tasks, (int)$_REQUEST['destID']);
		if(!isset($_REQUEST['listOnly']))
			die($result);
	}

	// note list
	$todoList = $todo->GetTodoList('(akt_status=64) ASC,priority DESC,faellig', 'ASC', -1, $taskListID);

	// page output
	$tpl->assign('taskListID', $taskListID);
	$tpl->assign('taskLists', $todo->GetTaskLists());
	$tpl->assign('todoList', $todoList);

	if(isset($_REQUEST['listOnly']))
	{
		$tpl->display('li/organizer.todo.list.tpl');
	}
	else
	{
		$tpl->assign('pageContent', 'li/organizer.todo.tpl');
		$tpl->display('li/index.tpl');
	}
}

/**
 * add task list RPC
 */
else if($_REQUEST['action'] == 'addList'
		&& !empty($_REQUEST['title']))
{
	$todo->AddTaskList($_REQUEST['title']);
	NormalArray2XML($todo->GetTaskLists(), 'taskLists');
	exit();
}

/**
 * delete task list RPC
 */
else if($_REQUEST['action'] == 'deleteList'
		&& !empty($_REQUEST['tasklistid']))
{
	$todo->DeleteTaskList($_REQUEST['tasklistid']);
	NormalArray2XML($todo->GetTaskLists(), 'taskLists');
	exit();
}

/**
 * action
 */
else if($_REQUEST['action'] == 'action'
		&& isset($_REQUEST['do'])
		&& IsPOSTRequest())
{
	$taskListID = isset($_POST['taskListID']) ? (int)$_POST['taskListID'] : 0;
	$taskIDs = explode(';', $_POST['taskIDs']);

	if($_REQUEST['do'] == 'delete')
	{
		foreach($taskIDs as $id)
		{
			$id = (int)$id;
			$todo->Delete($id);
		}
	}
	else if($_REQUEST['do'] == 'markasdone')
	{
		foreach($taskIDs as $id)
		{
			$id = (int)$id;
			$todo->SetStatus($id, TASKS_DONE);
		}
	}

	header('Location: organizer.todo.php?taskListID='.$taskListID.'&sid=' . session_id());
}

/**
 * delete task
 */
else if($_REQUEST['action'] == 'deleteTask'
		&& isset($_REQUEST['id']))
{
	$taskListID = isset($_REQUEST['taskListID']) ? (int)$_REQUEST['taskListID'] : 0;
	$todo->Delete((int)$_REQUEST['id']);
	header('Location: organizer.todo.php?taskListID='.$taskListID.'&sid=' . session_id());
}

/**
 * add task
 */
else if($_REQUEST['action'] == 'addTask')
{
	$taskListID = isset($_REQUEST['taskListID']) ? (int)$_REQUEST['taskListID'] : 0;
	$tpl->assign('taskLists', $todo->GetTaskLists());
	$tpl->assign('taskListID', $taskListID);
	$tpl->assign('pageTitle', $lang_user['addtask']);
	$tpl->assign('pageContent', 'li/organizer.todo.edit.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * create task
 */
else if($_REQUEST['action'] == 'createTask'
		&& isset($_REQUEST['beginnDay'])
		&& isset($_REQUEST['faelligDay'])
		&& isset($_REQUEST['erledigt'])
		&& isset($_REQUEST['comments'])
		&& isset($_REQUEST['titel'])
		&& isset($_REQUEST['priority'])
		&& IsPOSTRequest())
{
	$taskListID = isset($_REQUEST['taskListID']) ? (int)$_REQUEST['taskListID'] : 0;
	$todo->Add(SmartyDateTime('beginn'),
				SmartyDateTime('faellig'),
				(int)$_REQUEST['akt_status'],
				$_REQUEST['titel'],
				(int)$_REQUEST['priority'],
				(int)$_REQUEST['erledigt'],
				$_REQUEST['comments'],
				$taskListID);
	header('Location: organizer.todo.php?taskListID='.$taskListID.'&sid=' . session_id());
}

/**
 * edit task
 */
else if($_REQUEST['action'] == 'editTask'
		&& isset($_REQUEST['id']))
{
	$taskInfo = $todo->GetTask((int)$_REQUEST['id']);
	if($taskInfo !== false)
	{
		$tpl->assign('taskLists', $todo->GetTaskLists());
		$tpl->assign('pageTitle', $lang_user['edittask']);
		$tpl->assign('pageContent', 'li/organizer.todo.edit.tpl');
		$tpl->assign('task', $taskInfo);
		$tpl->display('li/index.tpl');
	}
}

/**
 * save task
 */
else if($_REQUEST['action'] == 'saveTask'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['beginnDay'])
		&& isset($_REQUEST['faelligDay'])
		&& isset($_REQUEST['erledigt'])
		&& isset($_REQUEST['comments'])
		&& isset($_REQUEST['titel'])
		&& isset($_REQUEST['priority'])
		&& IsPOSTRequest())
{
		$taskListID = isset($_REQUEST['taskListID']) ? (int)$_REQUEST['taskListID'] : 0;
	$todo->Change((int)$_REQUEST['id'],
				SmartyDateTime('beginn'),
				SmartyDateTime('faellig'),
				(int)$_REQUEST['akt_status'],
				$_REQUEST['titel'],
				(int)$_REQUEST['priority'],
				(int)$_REQUEST['erledigt'],
				$_REQUEST['comments'],
				$taskListID);
	header('Location: organizer.todo.php?taskListID='.$taskListID.'&sid=' . session_id());
}
?>