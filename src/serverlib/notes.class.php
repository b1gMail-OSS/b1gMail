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
	function __construct($userID)
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
	function GetNoteList($sortColumn = 'date', $sortOrder = 'ASC', $limit = -1)
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT id,priority,date,text FROM {pre}notes WHERE user=? ORDER BY ' . $sortColumn . ' ' . $sortOrder
							. ($limit != -1 ? ' LIMIT ' . (int)$limit : ''),
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$result[$row['id']] = $row;
		}
		$res->Free();

		return($result);
	}

	/**
	 * get note details
	 *
	 * @param int $id Note ID
	 * @return array
	 */
	function GetNote($id)
	{
		global $db;

		$res = $db->Query('SELECT id,priority,date,text FROM {pre}notes WHERE user=? AND id=?',
			$this->_userID,
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
	function Delete($id)
	{
		global $db;

		$db->Query('DELETE FROM {pre}notes WHERE user=? AND id=?',
			$this->_userID,
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
	function Add($priority, $text)
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
	function Change($id, $priority, $text)
	{
		global $db;

		$db->Query('UPDATE {pre}notes SET priority=?,text=? WHERE id=? AND user=?',
			(int)$priority,
			$text,
			(int)$id,
			$this->_userID);
		return($db->AffectedRows() == 1);
	}
}
