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

class BMOrganizerState extends BMSessionState
{
	public $addressbook;
	public $calendar;
	public $todo;

	public function toUTC($date)
	{
		return $date + $this->userRow['last_timezone'];
	}

	public function fromUTC($date)
	{
		return $date;
	}

	public function getDisplayName()
	{
		return($this->userRow['vorname'] . ' ' . $this->userRow['nachname']);
	}

	public function getPrincipalURI()
	{
		return('principals/' . $this->userRow['email']);
	}

	public function genUID($davUID, $str)
	{
		if(!empty($davUID))
			return $davUID;
		$uid = md5($str);
		return(substr($uid, 0, 8)
			. '-' . substr($uid, 8, 4)
			. '-' . substr($uid, 12, 4)
			. '-' . substr($uid, 16, 4)
			. '-' . substr($uid, 20, 12));
	}

	function getLastModified($itemIDs, $itemType)
	{
		global $db;

		$result = array();
		$noArray = false;

		if(!is_array($itemIDs))
		{
			$noArray = true;
			$itemIDs = array($itemIDs);
		}

		$res = $db->Query('SELECT `itemid`,`created`,`updated` FROM {pre}changelog WHERE `itemtype`=? AND `itemid` IN ?',
			$itemType,
			$itemIDs);
		while($row = $res->FetchArray())
		{
			$result[ $row['itemid'] ] = max($row['updated'], $row['created']);
		}
		$res->Free();

		return($noArray ? array_pop($result) : $result);
	}

	public function getProdID()
	{
		return('-//b1gMail Project//b1gMail ' . B1GMAIL_VERSION . '//EN');
	}
}

class BMPrincipalBackend extends Sabre\DAVACL\PrincipalBackend\AbstractBackend
{
	function getPrincipalsByPrefix($prefixPath)
	{
		global $os;

		$result = array();

		if($prefixPath == 'principals')
		{
			$result[] = array('uri' => $os->getPrincipalURI(),
				'{DAV:}displayname' => $os->getDisplayName());
		}

		return($result);
	}

	function getPrincipalByPath($path)
	{
		global $os;

		if($path != 'principals/' . $os->userRow['email'])
			return;

		return(array('id' => $os->userRow['id'],
			'uri' => $os->getPrincipalURI(),
			'{DAV:}displayname' => $os->getDisplayName()));
	}

	function updatePrincipal($path, \Sabre\DAV\PropPatch $propPatch)
	{
	}

	function searchPrincipals($prefixPath, array $searchProperties, $test = 'allof')
	{
		$result = array();

		if($prefixPath == 'principals')
		{
			foreach($searchProperties as $property=>$value)
			{
				if($property == '{DAV:}displayname')
				{
					if(stripos($os->getDisplayName(), $value) !== false)
						$result[] = 'principals/' .  $os->userRow['email'];
				}
				else
					return($result);
			}
		}

		return($result);
	}

	function getGroupMemberSet($principal)
	{
		return(array());
	}

	function getGroupMembership($principal)
	{
		return(array());
	}

	function setGroupMemberSet($principal, array $members)
	{
	}
}
