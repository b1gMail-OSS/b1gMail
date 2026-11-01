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
include('./serverlib/todo.class.php');
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
$tpl->addJSFile('li', 'clientlib/selectable.js');
$tpl->addJSFile('li', $tpl->tplDir . 'js/organizer.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'start';
$tpl->assign('activeTab', 'todo');
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
	 *
	 * F2: RPC endpoint reachable via GET → CSRF-token required.
	 */
	if(isset($_REQUEST['do'])
			&& $_REQUEST['do'] == 'setTaskDone'
			&& isset($_REQUEST['id'])
			&& isset($_REQUEST['done']))
	{
		CsrfEnforceOnStateChange();
		$result = $todo->SetStatus((int)$_REQUEST['id'], $_REQUEST['done'] == 'true' ? TASKS_DONE : TASKS_PROCESSING);
		if(!isset($_REQUEST['listOnly']))
			die($result);
	}

	/**
	 * add task RPC — F2: CSRF-token required.
	 */
	if(isset($_REQUEST['do'])
			&& $_REQUEST['do'] == 'addTask'
			&& isset($_REQUEST['title']))
	{
		CsrfEnforceOnStateChange();
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
	 * move tasks RPC — F2: CSRF-token required.
	 */
	if(isset($_REQUEST['do'])
			&& $_REQUEST['do'] == 'moveTasks'
			&& isset($_REQUEST['tasks'])
			&& isset($_REQUEST['destID']))
	{
		CsrfEnforceOnStateChange();
		$tasks = explode(',', $_REQUEST['tasks']);
		$result = $todo->MoveTasks($tasks, (int)$_REQUEST['destID']);
		if(!isset($_REQUEST['listOnly']))
			die($result);
	}

	// note list
	$todoList = $todo->GetTodoList('(akt_status=64) ASC,priority DESC,faellig', 'ASC', -1, $taskListID);
	$resolvedList = $todo->ResolveTaskList($taskListID);

	// page output
	$tpl->assign('taskListID', $taskListID);
	$tpl->assign('taskLists', $todo->GetTaskLists());
	$tpl->assign('todoList', $todoList);
	$tpl->assign('taskListWritable', $resolvedList && !empty($resolvedList['write']) && empty($resolvedList['virtual_tasks']));
	$tpl->assign('taskListShared', $resolvedList && !empty($resolvedList['shared']));
	$tpl->assign('canShareTodo', bmOrganizerGroupCanShare('todo'));

	if(isset($_REQUEST['listOnly']))
	{
		$tpl->display('li/organizer.todo.list.tpl');
	}
	else
	{
		$tpl->assign('pageContent', 'li/organizer.todo.tpl');
		$tpl->display('li/index.tpl');
	}
	exit();
}

/**
 * add task list RPC — F2: CSRF-token required.
 */
else if($_REQUEST['action'] == 'addList'
		&& !empty($_REQUEST['title']))
{
	CsrfEnforceOnStateChange();
	$todo->AddTaskList($_REQUEST['title']);
	NormalArray2XML($todo->GetTaskLists(), 'taskLists');
	exit();
}

/**
 * edit task list
 */
else if($_REQUEST['action'] == 'editList' && isset($_REQUEST['id']))
{
	$listId = (int)$_REQUEST['id'];
	$resolved = $todo->ResolveTaskList($listId);
	if($resolved === false || !empty($resolved['shared']) || !empty($resolved['virtual_tasks']) || $listId <= 0)
	{
		SessionRedirect('organizer.todo.php');
		exit();
	}

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'save' && isset($_REQUEST['title']) && IsPOSTRequest())
	{
		$todo->ChangeTaskList($listId, $_REQUEST['title']);
		SessionRedirect('organizer.todo.php?taskListID='.$listId);
		exit();
	}

	$lists = $todo->GetTaskLists();
	if(!isset($lists[$listId]))
	{
		SessionRedirect('organizer.todo.php');
		exit();
	}
	$tpl->assign('taskListItem', $lists[$listId]);
	$tpl->display('li/organizer.todo.list.dialog.tpl');
	exit();
}

/**
 * task list XML
 */
else if($_REQUEST['action'] == 'getLists')
{
	NormalArray2XML($todo->GetTaskLists(), 'taskLists');
	exit();
}

/**
 * delete task list RPC — F2: CSRF-token required. Deleting a list drops
 * all tasks inside, so the CSRF surface was significant.
 */
else if($_REQUEST['action'] == 'deleteList'
		&& !empty($_REQUEST['tasklistid']))
{
	CsrfEnforceOnStateChange();
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

	SessionRedirect('organizer.todo.php?taskListID='.$taskListID);
}

/**
 * delete task — F2: CSRF-token required.
 */
else if($_REQUEST['action'] == 'deleteTask'
		&& isset($_REQUEST['id']))
{
	CsrfEnforceOnStateChange();
	$taskListID = isset($_REQUEST['taskListID']) ? (int)$_REQUEST['taskListID'] : 0;
	$todo->Delete((int)$_REQUEST['id']);
	SessionRedirect('organizer.todo.php?taskListID='.$taskListID);
}

/**
 * add task
 */
else if($_REQUEST['action'] == 'addTask')
{
	$taskListID = isset($_REQUEST['taskListID']) ? (int)$_REQUEST['taskListID'] : 0;
	if($todo->GetTaskListWriteAccess($taskListID) === false)
	{
		SessionRedirect('organizer.todo.php?taskListID='.$taskListID);
		exit();
	}
	$tpl->assign('taskLists', $todo->GetTaskLists());
	$tpl->assign('taskListID', $taskListID);
	$tpl->assign('pageTitle', $lang_user['addtask']);
	$tpl->assign('pageContent', 'li/organizer.todo.edit.tpl');
	$tpl->display('li/index.tpl');
	exit();
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
	if($todo->GetTaskListWriteAccess($taskListID) === false)
	{
		SessionRedirect('organizer.todo.php?taskListID='.$taskListID);
		exit();
	}
	$todo->Add(SmartyDateTime('beginn'),
				SmartyDateTime('faellig'),
				(int)$_REQUEST['akt_status'],
				$_REQUEST['titel'],
				(int)$_REQUEST['priority'],
				(int)$_REQUEST['erledigt'],
				$_REQUEST['comments'],
				$taskListID);
	SessionRedirect('organizer.todo.php?taskListID='.$taskListID);
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
		exit();
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
	SessionRedirect('organizer.todo.php?taskListID='.$taskListID);
}

/**
 * share task list or task
 */
else if($_REQUEST['action'] == 'share' && isset($_REQUEST['id']))
{
	if(!bmOrganizerGroupCanShare('todo'))
	{
		SessionRedirect('organizer.todo.php');
		exit();
	}

	$kind = isset($_REQUEST['kind']) ? $_REQUEST['kind'] : 'list';
	if($kind === 'task')
	{
		$task = $todo->GetTask((int)$_REQUEST['id']);
		if($task === false)
		{
			SessionRedirect('organizer.todo.php');
			exit();
		}
		$accessCheck = $todo->GetTaskAccess((int)$task['id']);
		if($accessCheck === false || !empty($accessCheck['shared']))
		{
			SessionRedirect('organizer.todo.php');
			exit();
		}
		$shareType = BM_ORGANIZER_SHARE_TASK;
		$collectionId = (int)$task['id'];
		$shareItem = array('id' => (int)$task['id'], 'title' => $task['titel']);
		$dialogType = 'task';
	}
	else
	{
		$listId = (int)$_REQUEST['id'];
		$resolved = $todo->ResolveTaskList($listId);
		if($resolved === false || !empty($resolved['shared']) || !empty($resolved['virtual_tasks']))
		{
			SessionRedirect('organizer.todo.php');
			exit();
		}
		$lists = $todo->GetTaskLists();
		$title = isset($lists[$listId]['title']) ? $lists[$listId]['title'] : $lang_user['tasks'];
		$shareType = $resolved['type'];
		$collectionId = (int)$resolved['collectionId'];
		$shareItem = array('id' => $listId, 'title' => $title);
		$dialogType = 'tasklist';
	}

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add' && isset($_REQUEST['email']) && IsPOSTRequest())
	{
		$targetId = bmOrganizerShareTargetUserId($_REQUEST['email']);
		$access = isset($_REQUEST['access']) ? $_REQUEST['access'] : BM_ORGANIZER_ACCESS_READ;
		$result = bmOrganizerAddShare($shareType, $collectionId, $userRow['id'], $targetId, $access);
		if(is_string($result) && isset($lang_user[$result]))
			$tpl->assign('shareError', $lang_user[$result]);
		else if((int)$result > 0)
		{
			bmOrganizerNotifyShareInvite($shareType, array('id' => $collectionId, 'title' => $shareItem['title']), $userRow['id'], $targetId, $access);
			$tpl->assign('shareSuccess', $lang_user['shareinvited']);
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'remove'
		&& isset($_REQUEST['share'])
		&& IsPOSTRequest())
	{
		// F2: revoking a task-list / task share was GET-reachable.
		bmOrganizerRemoveShare($shareType, (int)$_REQUEST['share'], $userRow['id']);
	}

	$tpl->assign('shareItem', $shareItem);
	$tpl->assign('shareList', bmOrganizerListShares($shareType, $collectionId, $userRow['id']));
	$tpl->assign('shareType', $dialogType);
	$tpl->assign('shareAction', 'organizer.todo.php');
	$tpl->assign('shareKind', $kind === 'task' ? 'task' : '');
	$tpl->display('li/organizer.share.dialog.tpl');
	exit();
}

/**
 * leave shared list or task
 */
else if($_REQUEST['action'] == 'leaveshare' && isset($_REQUEST['id']))
{
	$kind = isset($_REQUEST['kind']) ? $_REQUEST['kind'] : 'list';
	if($kind === 'task')
	{
		$access = $todo->GetTaskAccess((int)$_REQUEST['id']);
		if($access === false || empty($access['shared']))
		{
			SessionRedirect('organizer.todo.php');
			exit();
		}
		$task = $todo->GetTask((int)$_REQUEST['id']);
		$leaveItem = array('id' => (int)$_REQUEST['id'], 'title' => $task ? $task['titel'] : '', 'titel' => $task ? $task['titel'] : '');
		$shareType = BM_ORGANIZER_SHARE_TASK;
		$collectionId = (int)$_REQUEST['id'];
	}
	else
	{
		$resolved = $todo->ResolveTaskList((int)$_REQUEST['id']);
		if($resolved === false || empty($resolved['shared']) || !empty($resolved['virtual_tasks']))
		{
			SessionRedirect('organizer.todo.php');
			exit();
		}
		$lists = $todo->GetTaskLists();
		$title = isset($lists[(int)$_REQUEST['id']]['title']) ? $lists[(int)$_REQUEST['id']]['title'] : $lang_user['tasks'];
		$leaveItem = array('id' => (int)$_REQUEST['id'], 'title' => $title, 'titel' => $title);
		$shareType = $resolved['type'];
		$collectionId = (int)$resolved['collectionId'];
	}

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'leave' && IsPOSTRequest())
	{
		bmOrganizerLeaveShare($shareType, $collectionId, $userRow['id']);
		SessionRedirect('organizer.todo.php');
		exit();
	}

	$tpl->assign('leaveItem', $leaveItem);
	$tpl->assign('leaveAction', 'organizer.todo.php');
	if($kind === 'task')
		$tpl->assign('leaveKind', 'task');
	$tpl->display('li/organizer.share.leave.tpl');
	exit();
}

SessionRedirect('organizer.todo.php');
