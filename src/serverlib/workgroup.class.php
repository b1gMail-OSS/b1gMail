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

define('WORKGROUP_TYPE_MAILFOLDER',			1);

/**
 * workgroup class
 */
class BMWorkgroup
{
	var $_id;
	var $_row;

	/**
	 * constructor
	 *
	 * @param int $id
	 * @return BMWorkgroup
	 */
	function __construct($id)
	{
		$this->_id = $id;
		$this->_row = $this->Fetch();
	}

	/**
	 * fetch a group row (assoc)
	 *
	 * @param int $id
	 * @return $array
	 */
	function Fetch($id = -1)
	{
		global $db;

		if($id == -1)
		{
			$id = $this->_id;
			if(is_array($this->_row))
				return($this->_row);
		}

		$res = $db->Query('SELECT * FROM {pre}workgroups WHERE id=?',
			$id);
		if($res->RowCount() == 0)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return($row);
	}

	/**
	 * retrieve a simple id/title workgroup list for user
	 *
	 * @param int $userID
	 * @param bool $withMembers Include members?
	 * @param bool $excludeDeleted Exclude deleted members?
	 * @return array
	 */
	static function GetSimpleWorkgroupList($userID, $withMembers = false, $excludeDeleted = true)
	{
		global $db;

		$groups = array();
		$res = $db->Query('SELECT {pre}workgroups.id AS id,{pre}workgroups.title AS title,{pre}workgroups.email AS email,{pre}workgroups.webdisk AS webdisk FROM {pre}workgroups,{pre}workgroups_member WHERE {pre}workgroups.id={pre}workgroups_member.workgroup AND {pre}workgroups_member.user=? ORDER BY title ASC',
			(int)$userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($withMembers)
			{
				$members = array();
				$res2 = $db->Query('SELECT {pre}users.vorname AS vorname,{pre}users.nachname AS nachname,{pre}users.email AS email,{pre}users.id AS id FROM {pre}users,{pre}workgroups_member WHERE {pre}users.id={pre}workgroups_member.user AND {pre}workgroups_member.workgroup=? ' . ($excludeDeleted ? 'AND {pre}users.gesperrt!=\'delete\' ' : '') . 'ORDER BY nachname ASC',
					$row['id']);
				while($memberRow = $res2->FetchArray(MYSQLI_ASSOC))
					$members[$memberRow['id']] = $memberRow;
				$res2->Free();

				$row['members'] = $members;
				$row['memberCount'] = count($members);
			}

			$groups[$row['id']] = $row;
		}
		$res->Free();

		return($groups);
	}

	/**
	 * get workgroup id by mail address
	 *
	 * @param string $email Mail address
	 * @return int
	 */
	function GetIDbyMail($email)
	{
		global $db;

		list(, $domainPart) = explode('@', $email);
		$res = $db->Query('SELECT id FROM {pre}workgroups WHERE email=? OR email=? LIMIT 1',
			$email,
			'*@' . $domainPart);
		if($res->RowCount() > 0)
		{
			list($id) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
			return($id);
		}

		return(0);
	}

	/**
	 * get group members (id, email)
	 *
	 * @param int $id Workgroup ID
	 * @param bool $excludeDeleted Exclude deleted users?
	 * @return array
	 */
	function GetMembers($id = -1, $excludeDeleted = true)
	{
		global $db;

		if($id == -1)
			$id = $this->_id;

		$members = array();
		$res = $db->Query('SELECT {pre}users.id AS id, {pre}users.email AS email FROM {pre}users,{pre}workgroups_member WHERE {pre}users.id={pre}workgroups_member.user AND {pre}workgroups_member.workgroup=?'
			. ($excludeDeleted ? ' AND {pre}users.gesperrt!=\'delete\'' : ''),
			$id);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$members[$row['id']] = $row;
		$res->Free();

		return($members);
	}

	/**
	 * check if user is in a workgroup
	 *
	 * @param int $userID
	 * @param int $groupID
	 * @return bool
	 */
	function UserInGroup($userID, $groupID)
	{
		global $db, $__inGroupCache;

		// init cache
		if(!isset($__inGroupCache) || !is_array($__inGroupCache))
			$__inGroupCache = array();

		// cached?
		if(isset($__inGroupCache[$userID.'_'.$groupID]))
			return($__inGroupCache[$userID.'_'.$groupID]);

		// get from db
		$res = $db->Query('SELECT COUNT(*) FROM {pre}workgroups_member WHERE workgroup=? AND user=?',
			(int)$groupID,
			(int)$userID);
		list($rowCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// cache, return
		$__inGroupCache[$userID.'_'.$groupID] = $rowCount == 1;
		return($__inGroupCache[$userID.'_'.$groupID]);
	}

	/**
	 * get title by id
	 *
	 * @param int $id
	 * @return string
	 */
	function GetTitle($id)
	{
		global $db;

		$res = $db->Query('SELECT title FROM {pre}workgroups WHERE id=?',
			$id);
		list($title) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($title);
	}

	/**
	 * check if access to a certain shared element is allowed
	 *
	 * @param int $userID User ID
	 * @param int $shareType Share type (see constants at top of file)
	 * @param bool $writeAccess Also check for write access?
	 * @return bool
	 */
	static function AccessAllowed($userID, $shareType, $shareID, $writeAccess)
	{
		global $db, $wgAccessCache;

		if(!EXTENDED_WORKGROUPS)
			return(false);

		if(!isset($wgAccessCache) || !is_array($wgAccessCache))
			$wgAccessCache = array();

		if($shareID <= 0)
			return(false);

		// this function gets called quite frequently, so we want to cache all shared objects
		// this user may access to avoid unnecessary db queries
		if(!isset($wgAccessCache[$userID]))
		{
			$wgAccessCache[$userID] = array();

			$res = $db->Query('SELECT sharetype,shareid,writeaccess FROM {pre}workgroups_shares '
				. 'INNER JOIN {pre}workgroups_member ON {pre}workgroups_shares.workgroupid={pre}workgroups_member.workgroup '
				. 'WHERE {pre}workgroups_member.user=?',
				$userID);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				if(!isset($wgAccessCache[$userID][$row['sharetype']]))
					$wgAccessCache[$userID][$row['sharetype']] = array();
				$wgAccessCache[$userID][$row['sharetype']][$row['shareid']] = ($row['writeaccess']==1?'rw':'ro');
			}
			$res->Free();
		}

		$result = false;

		if(!isset($wgAccessCache[$userID][$shareType][$shareID]))
			$result = false;
		else if($writeAccess)
			$result = ($wgAccessCache[$userID][$shareType][$shareID] == 'rw');
		else
			$result = true;

		return($result);
	}
}
