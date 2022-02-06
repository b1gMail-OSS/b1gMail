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
 * group class
 */
class BMGroup
{
	var $_id;
	var $_row;

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
		global $db, $cacheManager;

		if($id == -1)
		{
			$id = $this->_id;
			if(is_array($this->_row))
				return($this->_row);
		}

		if(!($row = $cacheManager->Get('group:' . $id)))
		{
			$res = $db->Query('SELECT * FROM {pre}gruppen WHERE id=?',
				$id);
			if($res->RowCount() == 0)
				return(false);
			$row = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			$cacheManager->Set('group:' . $id, $row);
		}

		return($row);
	}

	/**
	 * retrieve a simple id/title group list
	 *
	 * @return array
	 */
	static function GetSimpleGroupList()
	{
		global $db;

		$groups = array();
		$res = $db->Query('SELECT id,titel FROM {pre}gruppen ORDER BY titel ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$groups[$row['id']] = array(
				'id'		=> $row['id'],
				'title'		=> $row['titel']
			);
		$res->Free();

		return($groups);
	}

	/**
	 * get all group domains
	 *
	 * @return array
	 */
	static function GetGroupDomains()
	{
		global $db;

		$domains = array();
		$res = $db->Query('SELECT saliase FROM {pre}gruppen');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$groupDomains = explode(':', strtolower($row['saliase']));
			foreach($groupDomains as $domain)
				if(!in_array($domain, $domains))
					$domains[] = $domain;
		}
		$res->Free();

		return($domains);
	}
}
