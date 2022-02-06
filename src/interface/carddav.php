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

define('INTERFACE_MODE', 		true);

include('../serverlib/init.inc.php');
include('../serverlib/dav.inc.php');
include('../serverlib/organizerdav.inc.php');
include('../serverlib/addressbook.class.php');

use Sabre\DAV;

class BMCardDAVBackend extends Sabre\CardDAV\Backend\AbstractBackend
{
	public function getAddressBooksForUser($principalUri)
	{
		global $os;

		$result = array();

		$result[] = array(
			'id'				=> 0,
			'uri'				=> 'main',
			'principaluri'		=> $os->getPrincipalURI(),
			'{DAV:}displayname'	=> 'Addressbook',
			'{' . Sabre\CardDAV\Plugin::NS_CARDDAV . '}supported-address-data' => new Sabre\CardDAV\Property\SupportedAddressData()
		);

		return($result);
	}

	public function updateAddressBook($addressBookId, \Sabre\DAV\PropPatch $propPatch)
	{
		return false;
	}

	public function createAddressBook($principalUri, $url, array $properties)
	{
		return false;
	}

	public function deleteAddressBook($addressBookId)
	{
		return false;
	}

	public function getCards($addressbookId)
	{
		global $os, $db;

		$result = array();

		if($addressbookId == 0)
			$addressbookId = -1;

		$groups = $os->addressbook->GetGroupList();

		if(count($groups) > 0)
		{
			foreach($os->getLastModified(array_keys($groups), BMCL_TYPE_CONTACTGROUP) as $itemID=>$lastModified)
				$groups[$itemID]['lastmodified'] = $lastModified;
		}

		foreach($groups as $item)
		{
			$data = $this->groupToVObject($item)->serialize();

			$result[] = array(
				'id'			=> $item['id'],
				'uri'			=> !empty($item['dav_uri']) ? $item['dav_uri'] : 'group-' . $item['id'],
				'lastmodified'	=> $item['lastmodified'],
				'carddata'		=> $data,
				'size'			=> strlen($data)
			);
		}

		$book = $os->addressbook->GetAddressbook('*', $addressbookId);

		if(count($book) > 0)
		{
			foreach($os->getLastModified(array_keys($book), BMCL_TYPE_CONTACT) as $itemID=>$lastModified)
				$book[$itemID]['lastmodified'] = $lastModified;
		}

		foreach($book as $item)
		{
			$data = $this->rowToVObject($item)->serialize();

			$result[] = array(
				'id'			=> $item['id'],
				'uri'			=> !empty($item['dav_uri']) ? $item['dav_uri'] : 'item-' . $item['id'],
				'lastmodified'	=> $item['lastmodified'],
				'carddata'		=> $data,
				'size'			=> strlen($data)
			);
		}

		return($result);
	}

	public function getCard($addressBookId, $cardUri)
	{
		global $os;

		$contactID = $this->cardURItoID($cardUri);
		if($contactID === false)
			return(false);

		if($contactID[0] == BMCL_TYPE_CONTACT)
		{
			$contact = $os->addressbook->GetContact($contactID[1]);
			if($contact === false)
				return(false);

			return(array(
				'id'			=> $contact['id'],
				'uri'			=> $cardUri,
				'lastmodified'	=> $os->getLastModified($contactID[1], BMCL_TYPE_CONTACT),
				'carddata'		=> $this->rowToVObject($contact)->serialize()
			));
		}
		else if($contactID[0] == BMCL_TYPE_CONTACTGROUP)
		{
			$group = $os->addressbook->GetGroup($contactID[1]);
			if($group === false)
				return(false);

			return(array(
				'id'			=> $group['id'],
				'uri'			=> $cardUri,
				'lastmodified'	=> $os->getLastModified($contactID[1], BMCL_TYPE_CONTACTGROUP),
				'carddata'		=> $this->groupToVObject($group)->serialize()
			));
		}
	}

	private function createGroupCard($cardData, $cardUri)
	{
		global $os;

		$davUID = '';
		if($cardData->UID)
			$davUID = $cardData->UID;

		$title = '';
		if($cardData->FN)
			$title = $cardData->FN;
		else if($cardData->N)
			$title = $cardData->N;

		return ($os->addressbook->GroupAdd($title, $cardUri, $davUID) > 0);
	}

	public function createCard($addressBookId, $cardUri, $cardData)
	{
		global $os;

		if($addressBookId > 0)
			$groups = array($addressBookId);
		else
			$groups = array();

		$parsedCard = Sabre\VObject\Reader::read($cardData, Sabre\VObject\Reader::OPTION_FORGIVING);

		if(isset($parsedCard->{'X-ADDRESSBOOKSERVER-KIND'}) && strtoupper($parsedCard->{'X-ADDRESSBOOKSERVER-KIND'}->getValue()) == 'GROUP')
		{
			$this->createGroupCard($parsedCard, $cardUri);
			return(null);
		}

		$contact = $this->vObjectToRow($parsedCard);

		$os->addressbook->AddContact(
			$contact['firma'], $contact['vorname'], $contact['nachname'], $contact['strassenr'], $contact['plz'], $contact['ort'],
			$contact['land'], $contact['tel'], $contact['fax'], $contact['handy'], $contact['email'],
			$contact['work_strassenr'], $contact['work_plz'], $contact['work_ort'], $contact['work_land'], $contact['work_tel'],
			$contact['work_fax'], $contact['work_handy'], $contact['work_email'], '', $contact['position'],
			$contact['web'], $contact['kommentar'], $contact['geburtsdatum'], ADDRESS_PRIVATE, $groups,
			isset($contact['pictureFile']) ? $contact['pictureFile'] : false,
			isset($contact['pictureMIME']) ? $contact['pictureMIME'] : false,
			$cardUri, $contact['dav_uid']);

		return(null);
	}

	public function updateCard($addressBookId, $cardUri, $cardData)
	{
		global $os;

		$contactID = $this->cardURItoID($cardUri);
		if($contactID === false)
			return(false);

		$origCardData = $cardData;
		$cardData = Sabre\VObject\Reader::read($cardData, Sabre\VObject\Reader::OPTION_FORGIVING);

		if($contactID[0] == BMCL_TYPE_CONTACT)
		{
			$oldContact = $os->addressbook->GetContact($contactID[1]);
			$contact = $this->vObjectToRow($cardData);

			$os->addressbook->Change($contactID[1],
				$contact['firma'], $contact['vorname'], $contact['nachname'], $contact['strassenr'], $contact['plz'], $contact['ort'],
				$contact['land'], $contact['tel'], $contact['fax'], $contact['handy'], $contact['email'],
				$contact['work_strassenr'], $contact['work_plz'], $contact['work_ort'], $contact['work_land'], $contact['work_tel'],
				$contact['work_fax'], $contact['work_handy'], $contact['work_email'], $oldContact['anrede'], $contact['position'],
				$contact['web'], $contact['kommentar'], $contact['geburtsdatum'], $oldContact['default_address'], false);

			if(isset($contact['pictureFile']))
				$os->addressbook->ChangePicture($contactID[1], $contact['pictureFile'], $contact['pictureMIME'], false);
		}
		else if($contactID[0] == BMCL_TYPE_CONTACTGROUP)
		{
			$title = '';
			if($cardData->FN)
				$title = $cardData->FN;
			else if($cardData->N)
				$title = $cardData->N;

			$os->addressbook->ChangeGroup($contactID[1], $title);
			$this->updateGroupMembers($contactID[1], $cardData);
		}

		return(null);
	}

	private function updateGroupMembers($groupID, $cardData)
	{
		global $os;

		$oldUIDs = array();
		$oldMembers = $os->addressbook->GetGroupMembers($groupID);
		foreach($oldMembers as $member)
		{
			$uid = $os->genUID($member['dav_uid'], 'contact-' . $member['id'] . '@' . $os->userRow['id']);
			$oldUIDs[$uid] = $member['id'];
		}

		$newMembers = array();
		$members = $cardData->{'X-ADDRESSBOOKSERVER-MEMBER'};
		foreach($members as $member)
		{
			$uid = str_replace('urn:uuid:', '', $member->getValue());
			$id = $this->contactIdByUID($uid);

			if(!$id)
				continue;

			$newMembers[$uid] = $id;
		}

		foreach($newMembers as $newUID=>$newID)
		{
			if(!isset($oldUIDs[$newUID]))
			{
				$os->addressbook->ContactGroup($newID, $groupID);
			}
		}

		foreach($oldUIDs as $oldUID=>$oldID)
		{
			if(!isset($newMembers[$oldUID]))
			{
				$os->addressbook->DeContactGroup2($oldID, $groupID);
			}
		}
	}

	public function deleteCard($addressBookId, $cardUri)
	{
		global $os;

		$contactID = $this->cardURItoID($cardUri);
		if($contactID === false)
			return(false);

		if($contactID[0] == BMCL_TYPE_CONTACT)
		{
			$os->addressbook->Delete($contactID[1]);
		}
		else if($contactID[0] == BMCL_TYPE_CONTACTGROUP)
		{
			$os->addressbook->DeleteGroup($contactID[1]);
		}
	}

	private function contactIdByUID($uid)
	{
		global $os;

		if(empty($uid))
			return 0;

		$contacts = $os->addressbook->GetAddressbook('*');
		foreach($contacts as $contact)
		{
			if(!empty($contact['dav_uid']) && $contact['dav_uid'] == $uid
				|| (empty($contact['dav_uid']) && $os->genUID('', 'contact-' . $contact['id'] . '@' . $os->userRow['id']) == $uid))
				return $contact['id'];
		}

		return 0;
	}

	private function cardURItoID($cardUri)
	{
		global $db, $os;

		if(substr($cardUri, 0, 6) == 'group-')
		{
			$groupID = (int)substr($cardUri, 6);
			return(array(BMCL_TYPE_CONTACTGROUP, $groupID));
		}
		else if(substr($cardUri, 0, 5) == 'item-')
		{
			$contactID = (int)substr($cardUri, 5);
			return(array(BMCL_TYPE_CONTACT, $contactID));
		}
		else if(!empty($cardUri))
		{
			$result = false;

			$res = $db->Query('SELECT `id` FROM {pre}adressen WHERE `user`=? AND `dav_uri`=?',
				$os->userRow['id'],
				$cardUri);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$result = $row['id'];
				break;
			}
			$res->Free();

			if($result)
				return(array(BMCL_TYPE_CONTACT, $result));

			$res = $db->Query('SELECT `id` FROM {pre}adressen_gruppen WHERE `user`=? AND `dav_uri`=?',
				$os->userRow['id'],
				$cardUri);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$result = $row['id'];
				break;
			}
			$res->Free();

			if($result)
				return(array(BMCL_TYPE_CONTACTGROUP, $result));
		}

		return false;
	}

	private function vObjectToRow($obj, $baseRow = null)
	{
		if($baseRow !== null)
			$row = $baseRow;
		else
			$row = array();

		if($obj->UID)
			$row['dav_uid'] = $obj->UID;
		else
			$row['dav_uid'] = '';

		if($obj->N)
			list($row['nachname'], $row['vorname']) = $obj->N->getParts();

		if($obj->ORG)
			list($row['firma']) = $obj->ORG->getParts();

		if($obj->ADR)
		{
			foreach($obj->ADR as $item)
			{
				$prefix = '';

				foreach($item['TYPE'] as $type)
				{
					switch(strtolower((string)$type))
					{
					case 'work':
						$prefix = 'work_';
						break;

					case 'home':
						$prefix = '';
						break;
					}
				}

				list(, , $row[$prefix.'strassenr'], $row[$prefix.'ort'], , $row[$prefix.'plz'], $row[$prefix.'land']) = $item->getParts();
			}
		}

		if($obj->TEL)
		{
			foreach($obj->TEL as $item)
			{
				$prefix = '';
				$field = 'tel';

				foreach($item['TYPE'] as $type)
				{
					switch(strtolower((string)$type))
					{
					case 'work':
						$prefix = 'work_';
						break;

					case 'home':
						$prefix = '';
						break;

					case 'fax':
						$field = 'fax';
						break;

					case 'cell':
						$field = 'handy';
						break;
					}
				}

				$row[$prefix.$field] = (string)$item;
			}
		}

		if($obj->EMAIL)
		{
			foreach($obj->EMAIL as $item)
			{
				$prefix = '';

				foreach($item['TYPE'] as $type)
				{
					switch(strtolower((string)$type))
					{
					case 'work':
						$prefix = 'work_';
						break;

					case 'home':
						$prefix = '';
						break;
					}
				}

				$row[$prefix.'email'] = (string)$item;
			}
		}

		if($obj->TITLE)
			$row['position'] = (string)$obj->TITLE;

		if($obj->URL)
			$row['web'] = (string)$obj->URL;

		if($obj->NOTE)
			$row['kommentar'] = (string)$obj->NOTE;

		if($obj->PHOTO)
		{
			$photoData = (string)$obj->PHOTO;
			$row['pictureFile'] = $photoData;

			if(substr($photoData, 0, 3) == 'GIF')
				$row['pictureMIME'] = 'image/gif';
			else if(substr($photoData, 1, 3) == 'PNG')
				$row['pictureMIME'] = 'image/png';
			else if(strpos(substr($photoData, 0, 32), 'JFIF') !== false)
				$row['pictureMIME'] = 'image/jpeg';
			else
				$row['pictureMIME'] = 'application/octet-stream';
		}

		if($obj->BDAY)
			$row['geburtsdatum'] = $obj->BDAY->getDateTime()->getTimestamp();

		return($row);
	}

	private function groupToVObject($row)
	{
		global $os;

		$obj = new Sabre\VObject\Component\VCard(array('PRODID' => $os->getProdID()));

		$obj->add('FN', $row['title']);
		$obj->add('N', $row['title']);
		$obj->add('X-ADDRESSBOOKSERVER-KIND', 'group');
		$obj->add('UID', $os->genUID($row['dav_uid'], 'group-' . $row['id'] . '@' . $os->userRow['id']));

		$members = $os->addressbook->GetGroupMembers($row['id']);
		foreach($members as $member)
		{
			$uid = $os->genUID($member['dav_uid'], 'contact-' . $member['id'] . '@' . $os->userRow['id']);
			$obj->add('X-ADDRESSBOOKSERVER-MEMBER', 'urn:uuid:' . $uid);
		}

		return $obj;
	}

	private function rowToVObject($row)
	{
		global $os;

		$obj = new Sabre\VObject\Component\VCard(array('PRODID' => $os->getProdID()));

		$obj->add('UID', $os->genUID($row['dav_uid'], 'contact-' . $row['id'] . '@' . $os->userRow['id']));
		$obj->add('N', array($row['nachname'], $row['vorname'], '', '', ''));
		$obj->add('FN', $row['vorname'] . ' ' . $row['nachname']);

		if(!empty($row['firma']))
			$obj->add('ORG', $row['firma']);

		if(!empty($row['tel']))
			$obj->add('TEL', $row['tel'], array('type' => 'HOME'));
		if(!empty($row['work_tel']))
			$obj->add('TEL', $row['work_tel'], array('type' => 'WORK'));

		if(!empty($row['fax']))
			$obj->add('TEL', $row['fax'], array('type' => 'FAX'));
		if(!empty($row['work_fax']))
			$obj->add('TEL', $row['work_fax'], array('type' => array('FAX', 'WORK')));

		if(!empty($row['handy']))
			$obj->add('TEL', $row['handy'], array('type' => 'CELL'));
		if(!empty($row['work_handy']))
			$obj->add('TEL', $row['work_handy'], array('type' => array('CELL', 'WORK')));

		if(!empty($row['email']))
			$obj->add('EMAIL', $row['email'], array('type' => 'HOME'));
		if(!empty($row['work_email']))
			$obj->add('EMAIL', $row['work_email'], array('type' => 'WORK'));

		if(!empty($row['position']))
			$obj->add('TITLE', $row['position']);

		$obj->add('ADR', array('', '', $row['strassenr'], $row['ort'], '', $row['plz'], $row['land']), array('type' => 'HOME'));
		$obj->add('ADR', array('', '', $row['work_strassenr'], $row['work_ort'], '', $row['work_plz'], $row['work_land']), array('type' => 'WORK'));

		if(!empty($row['web']))
			$obj->add('URL', $row['web']);

		if(!empty($row['kommentar']))
			$obj->add('NOTE', $row['kommentar']);

		if(!empty($row['picture']))
		{
			$picture = @unserialize($row['picture']);
			if(is_array($picture))
				$obj->add('PHOTO', $picture['data'], array('BASE64' => null));
		}

		if(!empty($row['geburtsdatum']))
			$obj->add('BDAY', date('Y-m-d', $row['geburtsdatum']));

		return($obj);
	}
}

class BMCardDAVAuthBackend extends BMAuthBackend
{
	function checkPermissions()
	{
		return($this->groupRow['organizerdav'] == 'yes');
	}

	function setupState()
	{
		global $os;

		$os->userObject 	= $this->userObject;
		$os->groupObject 	= $this->groupObject;
		$os->userRow 		= $this->userRow;
		$os->groupRow 		= $this->groupRow;
		$os->addressbook 	= _new('BMAddressbook', array($os->userRow['id']));
	}
}

$os = new BMOrganizerState;

$principalBackend 	= new BMPrincipalBackend;
$carddavBackend		= new BMCardDAVBackend;

$nodes = array(
	new \Sabre\CalDAV\Principal\Collection($principalBackend),
	new \Sabre\CardDAV\AddressBookRoot($principalBackend, $carddavBackend)
);

$server = new DAV\Server($nodes);
$server->setBaseUri($_SERVER['SCRIPT_NAME']);

$authBackend = new BMCardDAVAuthBackend;
$authPlugin = new DAV\Auth\Plugin($authBackend, $bm_prefs['titel'] . ' ' . $lang_user['addressbook']);
$server->addPlugin($authPlugin);

$server->addPlugin(new \Sabre\CardDAV\Plugin());
$server->addPlugin(new \Sabre\DAVACL\Plugin());
$server->addPlugin(new \Sabre\DAV\Sync\Plugin());

$server->exec();
