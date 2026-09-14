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

include_once B1GMAIL_DIR . 'serverlib/organizer.shares.inc.php';

/**
 * notes interface class
 */
class BMNotes
{
	var $_userID;

	/**
	 * constructor
	 *
	 * @param int $userID User ID
	 * @return BMNotes
	 */
	public function __construct($userID)
	{
		$this->_userID = $userID;
	}

	/**
	 * get list of notes
	 *
	 * @param string $sortColumn Sort column
	 * @param string $sortOrder Sort order
	 * @param int $limit Entry limit
	 * @return array
	 */
	public function GetNoteList($sortColumn = 'date', $sortOrder = 'ASC', $limit = -1)
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT id,priority,date,text FROM {pre}notes WHERE user=? ORDER BY ' . $sortColumn . ' ' . $sortOrder
							. ($limit != -1 ? ' LIMIT ' . (int)$limit : ''),
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$row['shared'] = 0;
			$row['readonly'] = 0;
			$result[$row['id']] = $row;
		}
		$res->Free();

		foreach(bmOrganizerListSharedNotes($this->_userID) as $noteID=>$row)
		{
			$row['shared'] = 1;
			$row['readonly'] = ($row['share_access'] !== BM_ORGANIZER_ACCESS_WRITE) ? 1 : 0;
			$result[$noteID] = $row;
		}

		return($result);
	}

	/**
	 * get note details
	 *
	 * @param int $id Note ID
	 * @return array
	 */
	public function GetNote($id)
	{
		global $db;

		$access = $this->GetNoteAccess($id);
		if($access === false)
			return(false);

		$res = $db->Query('SELECT id,priority,date,text FROM {pre}notes WHERE user=? AND id=?',
			(int)$access['ownerId'],
			(int)$id);
		if($res->RowCount() == 0)
			return(false);
		$result = $res->FetchArray();
		$res->Free();

		return($result);
	}

	/**
	 * delete a note
	 *
	 * @param int $id Note ID
	 * @return bool
	 */
	public function Delete($id)
	{
		global $db;

		$access = $this->GetNoteWriteAccess($id);
		if($access === false)
			return(false);

		// F9: audit cross-owner deletes performed via a write-shared note stack.
		bmShareAuditLog($this->_userID, (int)$access['ownerId'],
			'note_delete', (int)$id, '');

		$db->Query('DELETE FROM {pre}notes WHERE user=? AND id=?',
			(int)$access['ownerId'],
			$id);
		return($db->AffectedRows() == 1);
	}

	/**
	 * add a note
	 *
	 * @param int $priority Priority
	 * @param string $text Text
	 * @return int
	 */
	public function Add($priority, $text)
	{
		global $db;

		$db->Query('INSERT INTO {pre}notes(user,date,priority,text) VALUES(?,?,?,?)',
			$this->_userID,
			time(),
			(int)$priority,
			$text);
		return($db->InsertID());
	}

	/**
	 * change a note
	 *
	 * @param int $id Note ID
	 * @param int $priority New priority
	 * @param string $text New test
	 * @return bool
	 */
	public function Change($id, $priority, $text)
	{
		global $db;

		$access = $this->GetNoteWriteAccess($id);
		if($access === false)
			return(false);

		$db->Query('UPDATE {pre}notes SET priority=?,text=? WHERE id=? AND user=?',
			(int)$priority,
			$text,
			(int)$id,
			(int)$access['ownerId']);
		return($db->AffectedRows() == 1);
	}

	/**
	 * @param int $id
	 * @return array|false
	 */
	function GetNoteAccess($id)
	{
		global $db;

		$id = (int)$id;
		$res = $db->Query('SELECT `user` FROM {pre}notes WHERE `id`=?',
			$id);
		if($res->RowCount() !== 1)
		{
			$res->Free();
			return(false);
		}
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();
		$ownerId = (int)$row['user'];
		if($ownerId === (int)$this->_userID)
			return(array('ownerId' => $ownerId, 'write' => true, 'shared' => false));

		$access = bmOrganizerShareAccess(BM_ORGANIZER_SHARE_NOTE, $id, $this->_userID);
		if($access === false)
			return(false);

		return(array(
			'ownerId' => $ownerId,
			'write' => $access === BM_ORGANIZER_ACCESS_WRITE,
			'shared' => true
		));
	}

	/**
	 * @param int $id
	 * @return array|false
	 */
	function GetNoteWriteAccess($id)
	{
		$access = $this->GetNoteAccess($id);
		if($access === false || empty($access['write']))
			return(false);
		return($access);
	}
}
