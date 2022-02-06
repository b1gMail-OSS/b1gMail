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

/**
 * constants
 */
define('TASKS_NOTBEGUN', 		16);
define('TASKS_PROCESSING', 		32);
define('TASKS_DONE', 			64);
define('TASKS_POSTPONED',		128);

/**
 * todo interface class
 */
class BMTodo
{
	var $_userID;
	var $_prioTrans = array(
		'low'		=> -1,
		'normal'	=> 0,
		'high'		=> 1,
		-1			=> 'low',
		0			=> 'normal',
		1			=> 'high'
	);

	/**
	 * constructor
	 *
	 * @param int $userID User ID
	 * @return BMTodo
	 */
	function __construct($userID)
	{
		$this->_userID = $userID;
	}

	/**
	 * get list of tasks
	 *
	 * @param string $sortColumn Sort column
	 * @param string $sortOrder Sort order
	 * @param int $limit Entry limit
	 * @param int $taskListID Task list ID
	 * @return array
	 */
	function GetTodoList($sortColumn = 'faellig,beginn', $sortOrder = 'ASC', $limit = -1, $taskListID = 0, $undoneOnly = false)
	{
		global $db;

		$queryAdd = '';
		if($undoneOnly)
			$queryAdd .= ' AND akt_status!=' . TASKS_DONE;

		$result = array();
		$res = $db->Query('SELECT id,beginn,faellig,akt_status,titel,priority,erledigt,comments,dav_uri,dav_uid FROM {pre}tasks WHERE user=? AND tasklistid=?' . $queryAdd . ' ORDER BY ' . $sortColumn . ' '  . $sortOrder
							. ($limit != -1  ? ' LIMIT ' . $limit : ''),
							$this->_userID,
							$taskListID);
		while($row = $res->FetchArray())
		{
			$result[$row['id']] = array(
				'id'			=> $row['id'],
				'beginn'		=> $row['beginn'],
				'faellig'		=> $row['faellig'],
				'akt_status'	=> $row['akt_status'],
				'titel'			=> $row['titel'],
				'priority'		=> $this->_prioTrans[$row['priority']],
				'erledigt'		=> $row['erledigt'],
				'comments'		=> $row['comments'],
				'dav_uri'		=> $row['dav_uri'],
				'dav_uid'		=> $row['dav_uid']
			);
		}

		return($result);
	}

	/**
	 * get undone task count
	 *
	 * @return int
	 */
	function GetUndoneTaskCount()
	{
		global $db;

		$res = $db->Query('SELECT COUNT(*) FROM {pre}tasks WHERE user=? AND akt_status!=?',
			$this->_userID,
			TASKS_DONE);
		list($taskCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($taskCount);
	}

	/**
	 * add a task
	 *
	 * @param int $beginn Begin
	 * @param int $faellig Due
	 * @param int $akt_status Status
	 * @param string $titel Titel
	 * @param int $priority Priority
	 * @param int $erledigt Done
	 * @param string $comments Comments
	 * @return int
	 */
	function Add($beginn, $faellig, $akt_status, $titel, $priority, $erledigt, $comments, $taskListID = 0, $davURI = '', $davUID = '')
	{
		global $db;

		// translate $priority, if neccessary
		if(is_numeric($priority))
			$priority = $this->_prioTrans[$priority];

		$db->Query('INSERT INTO {pre}tasks(user,beginn,faellig,akt_status,titel,priority,erledigt,comments,tasklistid,dav_uri,dav_uid) VALUES(?,?,?,?,?,?,?,?,?,?,?)',
			$this->_userID,
			(int)$beginn,
			(int)$faellig,
			(int)$akt_status,
			$titel,
			$priority,
			(int)$erledigt,
			$comments,
			(int)$taskListID,
			$davURI,
			$davUID);
		$id = $db->InsertID();

		ChangelogAdded(BMCL_TYPE_TODO, $id, time());

		return($id);
	}

	/**
	 * change a task
	 *
	 * @param int $id Task ID
	 * @param int $beginn Begin
	 * @param int $faellig Due
	 * @param int $akt_status Status
	 * @param string $titel Titel
	 * @param int $priority Priority
	 * @param int $erledigt Done
	 * @param string $comments Comments
	 * @return bool
	 */
	function Change($id, $beginn, $faellig, $akt_status, $titel, $priority, $erledigt, $comments, $taskListID = 0)
	{
		global $db;

		// translate $priority, if neccessary
		if(is_numeric($priority))
			$priority = $this->_prioTrans[$priority];

		$db->Query('UPDATE {pre}tasks SET beginn=?,faellig=?,akt_status=?,titel=?,priority=?,erledigt=?,comments=?,tasklistid=? WHERE id=? AND user=?',
			(int)$beginn,
			(int)$faellig,
			(int)$akt_status,
			$titel,
			$priority,
			(int)$erledigt,
			$comments,
			(int)$taskListID,
			(int)$id,
			$this->_userID);

		if($db->AffectedRows() == 1)
		{
			ChangelogUpdated(BMCL_TYPE_TODO, $id, time());
			return(true);
		}
		return(false);
	}

	/**
	 * update task status
	 *
	 * @param int $id Task ID
	 * @param int $status New status
	 * @return bool
	 */
	function SetStatus($id, $status)
	{
		global $db;

		$db->Query('UPDATE {pre}tasks SET akt_status=? WHERE id=? AND user=?',
			(int)$status,
			(int)$id,
			$this->_userID);
		if($db->AffectedRows() == 1)
		{
			ChangelogUpdated(BMCL_TYPE_TODO, $id, time());
			return(true);
		}
		return(false);
	}

	/**
	 * delete a task
	 *
	 * @param int $id Task ID
	 * @return bool
	 */
	function Delete($id)
	{
		global $db;

		$db->Query('DELETE FROM {pre}tasks WHERE id=? AND user=?',
			(int)$id,
			$this->_userID);
		if($db->AffectedRows() == 1)
		{
			ChangelogDeleted(BMCL_TYPE_TODO, $id, time());
			return(true);
		}
		return(false);
	}

	/**
	 * get task info
	 *
	 * @param int $id Task ID
	 * @return array
	 */
	function GetTask($id)
	{
		global $db;

		$res = $db->Query('SELECT id,beginn,faellig,akt_status,titel,priority,erledigt,comments,tasklistid,dav_uri,dav_uid FROM {pre}tasks WHERE id=? AND user=?',
			(int)$id,
			$this->_userID);
		if($res->RowCount() == 0)
			return(false);
		$row = $res->FetchArray();
		$res->Free();

		return(array(
			'id'			=> $row['id'],
			'beginn'		=> $row['beginn'],
			'faellig'		=> $row['faellig'],
			'akt_status'	=> $row['akt_status'],
			'titel'			=> $row['titel'],
			'priority'		=> $this->_prioTrans[$row['priority']],
			'erledigt'		=> $row['erledigt'],
			'comments'		=> $row['comments'],
			'tasklistid'	=> $row['tasklistid'],
			'dav_uri'		=> $row['dav_uri'],
			'dav_uid'		=> $row['dav_uid']
		));
	}

	/**
	 * get task lists
	 *
	 * @return array
	 */
	function GetTaskLists()
	{
		global $db, $lang_user;

		$result = array();
		$result[0] = array('tasklistid' => 0, 'title' => $lang_user['tasks']);
		$res = $db->Query('SELECT `tasklistid`,`title`,`dav_uri` FROM {pre}tasklists WHERE `userid`=? ORDER BY `tasklistid` ASC',
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$result[$row['tasklistid']] = $row;
		}
		$res->Free();

		return($result);
	}

	/**
	 * add a task list
	 *
	 * @param string $title Title
	 * @return int ID of new list
	 */
	function AddTaskList($title, $davURI = '')
	{
		global $db;

		$db->Query('INSERT INTO {pre}tasklists(`userid`,`title`,`dav_uri`) VALUES(?,?,?)',
			$this->_userID,
			$title,
			$davURI);
		return($db->InsertId());
	}

	/**
	 * change a task list
	 *
	 * @param string $title New title
	 * @return bool
	 */
	function ChangeTaskList($taskListID, $title)
	{
		global $db;

		$db->Query('UPDATE {pre}tasklists SET `title`=? WHERE `userid`=? AND `tasklistid`=?',
			$title,
			$this->_userID,
			$taskListID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * delete a task list
	 *
	 * @param int $taskListID ID of task list to delete
	 * @param bool $deleteTasks Delete tasks in list? ('false' moves them to default list)
	 * @return bool Success
	 */
	function DeleteTaskList($taskListID, $deleteTasks = true)
	{
		global $db;

		if($taskListID < 0)
			return(false);

		if($deleteTasks)
		{
			$db->Query('DELETE FROM {pre}tasks WHERE `user`=? AND `tasklistid`=?',
				$this->_userID,
				$taskListID);
		}
		else
		{
			$db->Query('UPDATE {pre}tasks SET `tasklistid`=0 WHERE `user`=? AND `tasklistid`=?',
				$this->_userID,
				$taskListID);
		}

		$db->Query('DELETE FROM {pre}tasklists WHERE `tasklistid`=? AND `userid`=?',
			$taskListID,
			$this->_userID);
		return($db->AffectedRows() > 0);
	}

	/**
	 * move task(s) to different task list
	 *
	 * @param array/int $tasks Task ID(s)
	 * @param int $taskListID Destination task list ID
	 * @return bool Success
	 */
	function MoveTasks($tasks, $taskListID)
	{
		global $db;

		if(!is_array($tasks))
		 	$tasks = array($tasks);
		if(count($tasks) == 0)
			return(false);

		$db->Query('UPDATE {pre}tasks SET `tasklistid`=? WHERE `id` IN ? AND `user`=?',
			$taskListID,
			$tasks,
			$this->_userID);

		if($db->AffectedRows() > 0)
		{
			foreach($tasks as $taskID)
				ChangelogUpdated(BMCL_TYPE_TODO, $taskID, time());
			return(true);
		}

		return(false);
	}
}
