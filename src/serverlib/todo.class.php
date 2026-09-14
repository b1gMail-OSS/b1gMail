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

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

include_once B1GMAIL_DIR.'serverlib/organizer.shares.inc.php';

/*
 * constants
 */
define('TASKS_NOTBEGUN', 16);
define('TASKS_PROCESSING', 32);
define('TASKS_DONE', 64);
define('TASKS_POSTPONED', 128);

/**
 * todo interface class.
 */
class BMTodo
{
    private $_userID;
    private $_prioTrans = [
        'low' => -1,
        'normal' => 0,
        'high' => 1,
        -1 => 'low',
        0 => 'normal',
        1 => 'high',
    ];

    /**
     * constructor.
     *
     * @param int $userID User ID
     *
     * @return BMTodo
     */
    public function __construct($userID)
    {
        $this->_userID = $userID;
    }

    /**
     * get list of tasks.
     *
     * @param string $sortColumn Sort column
     * @param string $sortOrder  Sort order
     * @param int    $limit      Entry limit
     * @param int    $taskListID Task list ID
     *
     * @return array
     */
    public function GetTodoList($sortColumn = 'faellig,beginn', $sortOrder = 'ASC', $limit = -1, $taskListID = 0, $undoneOnly = false)
    {
        global $db;

        $resolved = $this->ResolveTaskList($taskListID);
        if ($resolved === false) {
            return [];
        }

        $result = [];
        if (!empty($resolved['virtual_tasks'])) {
            foreach (bmOrganizerListSharedTasks($this->_userID) as $row) {
                $result[(int) $row['id']] = $this->_formatTaskRow($row);
            }

            return $result;
        }

        $queryAdd = '';
        if ($undoneOnly) {
            $queryAdd .= ' AND akt_status!='.TASKS_DONE;
        }

        $res = $db->Query('SELECT id,beginn,faellig,akt_status,titel,priority,erledigt,comments,dav_uri,dav_uid FROM {pre}tasks WHERE user=? AND tasklistid=?'.$queryAdd.' ORDER BY '.$sortColumn.' '.$sortOrder
                            .($limit != -1 ? ' LIMIT '.$limit : ''),
                            (int) $resolved['ownerId'],
                            (int) $resolved['realId']);
        while ($row = $res->FetchArray()) {
            $result[$row['id']] = [
                'id' => $row['id'],
                'beginn' => $row['beginn'],
                'faellig' => $row['faellig'],
                'akt_status' => $row['akt_status'],
                'titel' => $row['titel'],
                'priority' => $this->_prioTrans[$row['priority']],
                'erledigt' => $row['erledigt'],
                'comments' => $row['comments'],
                'dav_uri' => $row['dav_uri'],
                'dav_uid' => $row['dav_uid'],
                'shared' => !empty($resolved['shared']) ? 1 : 0,
                'readonly' => (!empty($resolved['shared']) && empty($resolved['write'])) ? 1 : 0,
                'can_leave' => 0,
            ];
        }

        return $result;
    }

    /**
     * get undone task count.
     *
     * @return int
     */
    public function GetUndoneTaskCount()
    {
        global $db;

        $res = $db->Query('SELECT COUNT(*) FROM {pre}tasks WHERE user=? AND akt_status!=?',
            $this->_userID,
            TASKS_DONE);
        list($taskCount) = $res->FetchArray(MYSQLI_NUM);
        $res->Free();

        return $taskCount;
    }

    /**
     * add a task.
     *
     * @param int    $beginn     Begin
     * @param int    $faellig    Due
     * @param int    $akt_status Status
     * @param string $titel      Titel
     * @param int    $priority   Priority
     * @param int    $erledigt   Done
     * @param string $comments   Comments
     *
     * @return int
     */
    public function Add($beginn, $faellig, $akt_status, $titel, $priority, $erledigt, $comments, $taskListID = 0, $davURI = '', $davUID = '')
    {
        global $db;

        // translate $priority, if neccessary
        if (is_numeric($priority)) {
            $priority = $this->_prioTrans[$priority];
        }

        $access = $this->GetTaskListWriteAccess($taskListID);
        if ($access === false) {
            return 0;
        }

        $db->Query('INSERT INTO {pre}tasks(user,beginn,faellig,akt_status,titel,priority,erledigt,comments,tasklistid,dav_uri,dav_uid) VALUES(?,?,?,?,?,?,?,?,?,?,?)',
            (int) $access['ownerId'],
            (int) $beginn,
            (int) $faellig,
            (int) $akt_status,
            $titel,
            $priority,
            (int) $erledigt,
            $comments,
            (int) $access['realId'],
            $davURI,
            $davUID);
        $id = $db->InsertID();

        ChangelogAdded(BMCL_TYPE_TODO, $id, time());

        return $id;
    }

    /**
     * change a task.
     *
     * @param int    $id         Task ID
     * @param int    $beginn     Begin
     * @param int    $faellig    Due
     * @param int    $akt_status Status
     * @param string $titel      Titel
     * @param int    $priority   Priority
     * @param int    $erledigt   Done
     * @param string $comments   Comments
     *
     * @return bool
     */
    public function Change($id, $beginn, $faellig, $akt_status, $titel, $priority, $erledigt, $comments, $taskListID = 0)
    {
        global $db;

        // translate $priority, if neccessary
        if (is_numeric($priority)) {
            $priority = $this->_prioTrans[$priority];
        }

        $access = $this->GetTaskWriteAccess($id);
        if ($access === false) {
            return false;
        }

        $listId = (int) $taskListID;
        if (!empty($access['shared'])) {
            $res = $db->Query('SELECT `tasklistid` FROM {pre}tasks WHERE `id`=? AND `user`=?',
                (int) $id,
                (int) $access['ownerId']);
            if ($res->RowCount() !== 1) {
                $res->Free();

                return false;
            }
            $row = $res->FetchArray(MYSQLI_ASSOC);
            $res->Free();
            $listId = (int) $row['tasklistid'];
        } else {
            $dest = $this->GetTaskListWriteAccess($taskListID);
            if ($dest === false || (int) $dest['ownerId'] !== (int) $this->_userID) {
                return false;
            }
            $listId = (int) $dest['realId'];
        }

        $db->Query('UPDATE {pre}tasks SET beginn=?,faellig=?,akt_status=?,titel=?,priority=?,erledigt=?,comments=?,tasklistid=? WHERE id=? AND user=?',
            (int) $beginn,
            (int) $faellig,
            (int) $akt_status,
            $titel,
            $priority,
            (int) $erledigt,
            $comments,
            $listId,
            (int) $id,
            (int) $access['ownerId']);

        if ($db->AffectedRows() == 1) {
            ChangelogUpdated(BMCL_TYPE_TODO, $id, time());

            return true;
        }

        return false;
    }

    /**
     * update task status.
     *
     * @param int $id     Task ID
     * @param int $status New status
     *
     * @return bool
     */
    public function SetStatus($id, $status)
    {
        global $db;

        $access = $this->GetTaskWriteAccess($id);
        if ($access === false) {
            return false;
        }

        $db->Query('UPDATE {pre}tasks SET akt_status=? WHERE id=? AND user=?',
            (int) $status,
            (int) $id,
            (int) $access['ownerId']);
        if ($db->AffectedRows() == 1) {
            ChangelogUpdated(BMCL_TYPE_TODO, $id, time());

            return true;
        }

        return false;
    }

    /**
     * delete a task.
     *
     * @param int $id Task ID
     *
     * @return bool
     */
    public function Delete($id)
    {
        global $db;

        $access = $this->GetTaskWriteAccess($id);
        if ($access === false) {
            return false;
        }

        // F9: cross-owner delete via write-shared task list → audit.
        bmShareAuditLog($this->_userID, (int) $access['ownerId'],
            'task_delete', (int) $id, '');

        $db->Query('DELETE FROM {pre}tasks WHERE id=? AND user=?',
            (int) $id,
            (int) $access['ownerId']);
        if ($db->AffectedRows() == 1) {
            ChangelogDeleted(BMCL_TYPE_TODO, $id, time());

            return true;
        }

        return false;
    }

    /**
     * get task info.
     *
     * @param int $id Task ID
     *
     * @return array
     */
    public function GetTask($id)
    {
        global $db;

        $access = $this->GetTaskAccess($id);
        if ($access === false) {
            return false;
        }

        $res = $db->Query('SELECT id,beginn,faellig,akt_status,titel,priority,erledigt,comments,tasklistid,dav_uri,dav_uid FROM {pre}tasks WHERE id=? AND user=?',
            (int) $id,
            (int) $access['ownerId']);
        if ($res->RowCount() == 0) {
            return false;
        }
        $row = $res->FetchArray();
        $res->Free();

        return [
            'id' => $row['id'],
            'beginn' => $row['beginn'],
            'faellig' => $row['faellig'],
            'akt_status' => $row['akt_status'],
            'titel' => $row['titel'],
            'priority' => $this->_prioTrans[$row['priority']],
            'erledigt' => $row['erledigt'],
            'comments' => $row['comments'],
            'tasklistid' => $row['tasklistid'],
            'dav_uri' => $row['dav_uri'],
            'dav_uid' => $row['dav_uid'],
            'shared' => !empty($access['shared']) ? 1 : 0,
            'readonly' => empty($access['write']) ? 1 : 0,
        ];
    }

    /**
     * get task lists.
     *
     * @return array
     */
    public function GetTaskLists()
    {
        global $db, $lang_user;

        $result = [];
        $result[0] = ['tasklistid' => 0, 'title' => $lang_user['tasks'], 'can_share' => 1, 'can_delete' => 0, 'can_leave' => 0, 'can_edit' => 0, 'shared' => 0];
        $res = $db->Query('SELECT `tasklistid`,`title`,`dav_uri` FROM {pre}tasklists WHERE `userid`=? ORDER BY `tasklistid` ASC',
            $this->_userID);
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $row['can_share'] = 1;
            $row['can_delete'] = 1;
            $row['can_leave'] = 0;
            $row['can_edit'] = 1;
            $row['shared'] = 0;
            $result[$row['tasklistid']] = $row;
        }
        $res->Free();

        foreach (bmOrganizerListSharedTaskLists($this->_userID) as $sid => $shared) {
            $title = !empty($shared['is_default_share'])
                ? $lang_user['tasks'].' ('.DecodeEMail($shared['owner_email']).')'
                : $shared['title'];
            $result[$sid] = [
                'tasklistid' => $sid,
                'title' => $title,
                'shared' => 1,
                'can_share' => 0,
                'can_delete' => 0,
                'can_leave' => 1,
                'can_edit' => 0,
                'share_access' => $shared['share_access'],
                'owner_email' => $shared['owner_email'],
            ];
        }

        $sharedTasks = bmOrganizerListSharedTasks($this->_userID);
        if (count($sharedTasks) > 0) {
            $result[TASKLIST_SHARED_ITEMS] = [
                'tasklistid' => TASKLIST_SHARED_ITEMS,
                'title' => $lang_user['sharedtasks'],
                'shared' => 1,
                'can_share' => 0,
                'can_delete' => 0,
                'can_leave' => 0,
                'can_edit' => 0,
                'virtual_tasks' => 1,
            ];
        }

        return $result;
    }

    /**
     * add a task list.
     *
     * @param string $title Title
     *
     * @return int ID of new list
     */
    public function AddTaskList($title, $davURI = '')
    {
        global $db;

        $db->Query('INSERT INTO {pre}tasklists(`userid`,`title`,`dav_uri`) VALUES(?,?,?)',
            $this->_userID,
            $title,
            $davURI);

        return $db->InsertId();
    }

    /**
     * change a task list.
     *
     * @param string $title New title
     *
     * @return bool
     */
    public function ChangeTaskList($taskListID, $title)
    {
        global $db;

        $db->Query('UPDATE {pre}tasklists SET `title`=? WHERE `userid`=? AND `tasklistid`=?',
            $title,
            $this->_userID,
            $taskListID);

        return $db->AffectedRows() == 1;
    }

    /**
     * delete a task list.
     *
     * @param int  $taskListID  ID of task list to delete
     * @param bool $deleteTasks Delete tasks in list? ('false' moves them to default list)
     *
     * @return bool Success
     */
    public function DeleteTaskList($taskListID, $deleteTasks = true)
    {
        global $db;

        if ($taskListID < 0) {
            return false;
        }

        if ($deleteTasks) {
            $db->Query('DELETE FROM {pre}tasks WHERE `user`=? AND `tasklistid`=?',
                $this->_userID,
                $taskListID);
        } else {
            $db->Query('UPDATE {pre}tasks SET `tasklistid`=0 WHERE `user`=? AND `tasklistid`=?',
                $this->_userID,
                $taskListID);
        }

        $db->Query('DELETE FROM {pre}tasklists WHERE `tasklistid`=? AND `userid`=?',
            $taskListID,
            $this->_userID);

        return $db->AffectedRows() > 0;
    }

    /**
     * move task(s) to different task list.
     *
     * @param array/int $tasks      Task ID(s)
     * @param int       $taskListID Destination task list ID
     *
     * @return bool Success
     */
    public function MoveTasks($tasks, $taskListID)
    {
        global $db;

        if (!is_array($tasks)) {
            $tasks = [$tasks];
        }
        if (count($tasks) == 0) {
            return false;
        }

        $dest = $this->ResolveTaskList($taskListID);
        if ($dest === false || !empty($dest['shared']) || !empty($dest['virtual_tasks'])) {
            return false;
        }

        $db->Query('UPDATE {pre}tasks SET `tasklistid`=? WHERE `id` IN ? AND `user`=?',
            (int) $dest['realId'],
            $tasks,
            $this->_userID);

        if ($db->AffectedRows() > 0) {
            foreach ($tasks as $taskID) {
                ChangelogUpdated(BMCL_TYPE_TODO, $taskID, time());
            }

            return true;
        }

        return false;
    }

    /**
     * @param array $row
     * @return array
     */
    function _formatTaskRow($row)
    {
        $prio = $row['priority'];
        if (!isset($this->_prioTrans[$prio])) {
            $prio = 0;
        }

        return [
            'id' => $row['id'],
            'beginn' => $row['beginn'],
            'faellig' => $row['faellig'],
            'akt_status' => $row['akt_status'],
            'titel' => $row['titel'],
            'priority' => $this->_prioTrans[$prio],
            'erledigt' => $row['erledigt'],
            'comments' => isset($row['comments']) ? $row['comments'] : '',
            'dav_uri' => isset($row['dav_uri']) ? $row['dav_uri'] : '',
            'dav_uid' => isset($row['dav_uid']) ? $row['dav_uid'] : '',
            'shared' => !empty($row['shared']) ? 1 : 0,
            'can_leave' => !empty($row['can_leave']) ? 1 : 0,
            'readonly' => !empty($row['shared']) && (isset($row['share_access']) ? $row['share_access'] : '') !== BM_ORGANIZER_ACCESS_WRITE,
        ];
    }

    /**
     * @param int $taskListID
     * @return array|false
     */
    function ResolveTaskList($taskListID)
    {
        $taskListID = (int) $taskListID;
        if ($taskListID === TASKLIST_SHARED_ITEMS) {
            return [
                'virtual_tasks' => true,
                'shared' => true,
                'write' => false,
                'ownerId' => 0,
                'realId' => TASKLIST_SHARED_ITEMS,
            ];
        }

        $ownerDefault = bmDecodeSharedDefaultTaskList($taskListID);
        if ($ownerDefault) {
            $access = bmOrganizerShareAccess(BM_ORGANIZER_SHARE_TASKLISTDEF, $ownerDefault, $this->_userID);
            if ($access === false) {
                return false;
            }

            return [
                'ownerId' => $ownerDefault,
                'realId' => 0,
                'shared' => true,
                'write' => $access === BM_ORGANIZER_ACCESS_WRITE,
                'type' => BM_ORGANIZER_SHARE_TASKLISTDEF,
                'collectionId' => $ownerDefault,
            ];
        }

        if ($taskListID === 0) {
            return [
                'ownerId' => $this->_userID,
                'realId' => 0,
                'shared' => false,
                'write' => true,
                'type' => BM_ORGANIZER_SHARE_TASKLISTDEF,
                'collectionId' => $this->_userID,
            ];
        }

        $res = $GLOBALS['db']->Query('SELECT `userid` FROM {pre}tasklists WHERE `tasklistid`=?',
            $taskListID);
        if ($res->RowCount() === 1) {
            $row = $res->FetchArray(MYSQLI_ASSOC);
            $res->Free();
            if ((int) $row['userid'] === (int) $this->_userID) {
                return [
                    'ownerId' => $this->_userID,
                    'realId' => $taskListID,
                    'shared' => false,
                    'write' => true,
                    'type' => BM_ORGANIZER_SHARE_TASKLIST,
                    'collectionId' => $taskListID,
                ];
            }
        } else {
            $res->Free();
        }

        $access = bmOrganizerShareAccess(BM_ORGANIZER_SHARE_TASKLIST, $taskListID, $this->_userID);
        if ($access === false) {
            return false;
        }
        $res = $GLOBALS['db']->Query('SELECT `userid` FROM {pre}tasklists WHERE `tasklistid`=?',
            $taskListID);
        if ($res->RowCount() !== 1) {
            $res->Free();

            return false;
        }
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();

        return [
            'ownerId' => (int) $row['userid'],
            'realId' => $taskListID,
            'shared' => true,
            'write' => $access === BM_ORGANIZER_ACCESS_WRITE,
            'type' => BM_ORGANIZER_SHARE_TASKLIST,
            'collectionId' => $taskListID,
        ];
    }

    /**
     * @param int $taskListID
     * @return array|false
     */
    function GetTaskListWriteAccess($taskListID)
    {
        $resolved = $this->ResolveTaskList($taskListID);
        if ($resolved === false || empty($resolved['write']) || !empty($resolved['virtual_tasks'])) {
            return false;
        }

        return $resolved;
    }

    /**
     * @param int $taskId
     * @return array|false
     */
    function GetTaskAccess($taskId)
    {
        global $db;

        $taskId = (int) $taskId;
        $res = $db->Query('SELECT `user`,`tasklistid` FROM {pre}tasks WHERE `id`=?',
            $taskId);
        if ($res->RowCount() !== 1) {
            $res->Free();

            return false;
        }
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();
        $ownerId = (int) $row['user'];
        if ($ownerId === (int) $this->_userID) {
            return ['ownerId' => $ownerId, 'write' => true, 'shared' => false];
        }

        $taskAccess = bmOrganizerShareAccess(BM_ORGANIZER_SHARE_TASK, $taskId, $this->_userID);
        if ($taskAccess) {
            return [
                'ownerId' => $ownerId,
                'write' => $taskAccess === BM_ORGANIZER_ACCESS_WRITE,
                'shared' => true,
            ];
        }

        $listId = (int) $row['tasklistid'];
        if ($listId > 0) {
            $listAccess = bmOrganizerShareAccess(BM_ORGANIZER_SHARE_TASKLIST, $listId, $this->_userID);
        } else {
            $listAccess = bmOrganizerShareAccess(BM_ORGANIZER_SHARE_TASKLISTDEF, $ownerId, $this->_userID);
        }
        if ($listAccess) {
            return [
                'ownerId' => $ownerId,
                'write' => $listAccess === BM_ORGANIZER_ACCESS_WRITE,
                'shared' => true,
            ];
        }

        return false;
    }

    /**
     * @param int $taskId
     * @return array|false
     */
    function GetTaskWriteAccess($taskId)
    {
        $access = $this->GetTaskAccess($taskId);
        if ($access === false || empty($access['write'])) {
            return false;
        }

        return $access;
    }
}
