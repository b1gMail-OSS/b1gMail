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
define('ADDRESS_PRIVATE',	1);
define('ADDRESS_WORK',		2);

/**
 * addressbook interface class
 */
class BMAddressbook
{
	var $_userID;
	var $_groupCache = array();
	var $_exportFields = 'vorname AS Firstname, nachname AS Lastname, anrede AS Salutation, position AS Position, firma AS Company, strassenr AS Street, plz AS ZIP, ort as City, land AS Country, email AS EMail, tel AS Phone, handy AS Mobile, fax AS Fax, work_strassenr AS workStreet, work_plz AS workZIP, work_ort AS workCity, work_land AS workCountry, work_email AS workEMail, work_tel AS workPhone, work_handy AS workMobile, work_fax AS workFax, web AS Homepage, kommentar AS Comment, CASE geburtsdatum WHEN 0 THEN \'\' ELSE FROM_UNIXTIME(geburtsdatum, \'%Y-%m-%d\') END AS Birthday';

	/**
	 * constructor
	 *
	 * @param int $userID User ID
	 * @return BMAddressbook
	 */
	function __construct($userID)
	{
		$this->_userID = (int)$userID;
	}

	/**
	 * lookup address book entry by email
	 *
	 * @param string $email Email address to look up
	 * @return int
	 */
	function LookupEmail($email)
	{
		global $db;

		if(is_array($email))
		{
			$res = $db->Query('SELECT COUNT(*) FROM {pre}adressen WHERE user=? AND (email IN ? OR work_email IN ?)',
				$this->_userID,
				$email,
				$email);
			list($count) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			return($count > 0);
		}
		else
		{
			$res = $db->Query('SELECT id FROM {pre}adressen WHERE user=? AND (email=? OR work_email=?) LIMIT 1',
				$this->_userID,
				$email,
				$email);
			if($res->RowCount() == 0)
				return(0);
			list($result) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			return($result);
		}
	}

	/**
	 * look up text in addressbook (e.g. for auto completion)
	 *
	 * @param string $text Text to look up
	 * @return array
	 */
	function Lookup($text)
	{
		global $db;

		$addresses = array();
		$addr = array();

		$res = $db->Query('SELECT id,vorname,nachname,email,work_email,default_address FROM {pre}adressen WHERE '
							. 'user=? AND ('
							. sprintf('email LIKE \'%s%%\' OR work_email LIKE \'%s%%\' OR CONCAT(vorname,\' \',nachname) LIKE \'%s%%\' '
									. 'OR CONCAT(nachname,\', \',vorname) LIKE \'%s%%\'',
									$db->Escape($text), $db->Escape($text), $db->Escape($text), $db->Escape($text))
							. ')',
							$this->_userID);
		while($row = $res->FetchArray())
		{
			if(trim($row['email']) != '')
				$addr[] = array('email' => $row['email'], 'vorname' => $row['vorname'], 'nachname' => $row['nachname']);
			if(trim($row['work_email']) != '')
				$addr[] = array('email' => $row['work_email'], 'vorname' => $row['vorname'], 'nachname' => $row['nachname']);
		}
		$res->Free();

		$groups = $this->GetGroupList();
		foreach($groups as $group)
		{
			if($group['members'] == 0)
				continue;

			$addr[] = array('email' => sprintf('%d@contact.groups', $group['id']), 'vorname' => '', 'nachname' => $group['title']);
		}

		foreach($addr as $email)
		{
			if(strtolower(substr($email['email'], 0, strlen($text))) == strtolower($text)
				|| strtolower(substr($email['vorname'] . ' ' . $email['nachname'], 0, strlen($text))) == strtolower($text)
				|| strtolower(substr($email['nachname'] . ', ' . $email['vorname'], 0, strlen($text))) == strtolower($text))
			{
				$match = $email['email'];
				$name = trim($email['vorname'] . ' ' . $email['nachname']);

				if(strtolower(substr($email['nachname'] . ', ' . $email['vorname'], 0, strlen($text))) == strtolower($text)
					&& $email['vorname'] != '')
					$name = trim($email['nachname'] . ', ' . $email['vorname']);

				if(trim($match) != '')
					$addresses[] = str_replace(';', ',', sprintf('"%s" <%s>', $name, $match));
			}
		}

		return($addresses);
	}

	/**
	 * get group title
	 *
	 * @param int $groupID
	 * @return string
	 */
	function GetGroupTitle($groupID)
	{
		global $db;

		// lookup in cache first
		if(isset($this->_groupCache[$groupID]))
			return($this->_groupCache[$groupID]);

		// get title
		$groupTitle = '-';
		$res = $db->Query('SELECT title FROM {pre}adressen_gruppen WHERE user=? AND id=?',
			$this->_userID,
			$groupID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$groupTitle = $row['title'];
		}
		$res->Free();

		// cache & return
		$this->_groupCache[$groupID] = $groupTitle;
		return($groupTitle);
	}

	/**
	 * find address by first / last name and returns ID (> 0) on success
	 *
	 * @param string $vorname First name
	 * @param string $nachname Last name
	 * @return int
	 */
	function FindAddress($vorname, $nachname)
	{
		global $db;

		$result = 0;
		$res = $db->Query('SELECT id FROM {pre}adressen WHERE vorname=? AND nachname=? AND user=? LIMIT 1',
			$vorname,
			$nachname,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_NUM))
			$result = $row[0];
		$res->Free();

		return($result);
	}

	/**
	 * get addressbook entries
	 *
	 * @param string $letter Letter (or *)
	 * @param string $sortColumn Sort column
	 * @param string $sortOrder Sort order
	 * @param bool $groupByLetter Create grouped output array?
	 * @return array
	 */
	function GetAddressbook($letter, $groupID = -1, $sortColumn = 'nachname', $sortOrder = 'ASC', $groupByLetter = false)
	{
		global $db;

		// letter => where clause
		if($letter == '9')
		{
			$whereClause = ' AND (';
			for($i=0; $i<=9; $i++)
				$whereClause .= sprintf('{pre}adressen.nachname LIKE \'%s%%\' OR ', $i);
			$whereClause = substr($whereClause, 0, -4) . ') ';
		}
		else if(preg_match('/^[a-zA-Z]$/', $letter))
		{
			$whereClause = sprintf(' AND {pre}adressen.nachname LIKE \'%s%%\' ', $letter);
		}
		else
		{
			$whereClause = '';
		}

		// query
		$result = array();

		// group => where clause
		if($groupID > 0)
		{
			$res = $db->Query('SELECT {pre}adressen.* FROM {pre}adressen,{pre}adressen_gruppen_member WHERE {pre}adressen.user=?' . $whereClause . ' AND {pre}adressen.id={pre}adressen_gruppen_member.adresse AND {pre}adressen_gruppen_member.gruppe=? ORDER BY '
								. '{pre}adressen.' . $sortColumn . ' ' . $sortOrder,
								$this->_userID,
								$groupID);
		}
		else
		{
			$res = $db->Query('SELECT * FROM {pre}adressen WHERE user=?' . $whereClause . ' ORDER BY '
								. $sortColumn . ' ' . $sortOrder,
								$this->_userID);
		}
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($groupByLetter)
			{
				if(strlen($row['nachname']) > 0)
					$letter = strtoupper(substr($row['nachname'], 0, 1));
				else if(strlen($row['firma']) > 0)
					$letter = strtoupper(substr($row['firma'], 0, 1));
				else
					$letter = '#';

				if(!(ord($letter) >= ord('A') && ord($letter) <= ord('Z')))
					$letter = '#';

				if(!isset($result[$letter]))
					$result[$letter] = array($row['id'] => $row);
				else
					$result[$letter][$row['id']] = $row;
			}
			else
			{
				$result[$row['id']] = $row;
			}
		}
		$res->Free();

		if($groupByLetter)
			ksort($result);

		// return
		return($result);
	}

	/**
	 * get group list
	 *
	 * @param int $contactID Fetch list for contact
	 * @return array
	 */
	function GetGroupList($contactID = 0)
	{
		global $db;

		// query
		$result = array();
		$res = $db->Query('SELECT id,title,`dav_uri`,`dav_uid` FROM {pre}adressen_gruppen WHERE user=? ORDER BY title ASC',
			$this->_userID);
		while($row = $res->FetchArray())
			$result[$row['id']] = $row;
		$res->Free();

		// for contact?
		if($contactID > 0)
		{
			$res = $db->Query('SELECT gruppe FROM {pre}adressen_gruppen_member WHERE adresse=?',
				$contactID);
			while($row = $res->FetchArray())
				if(isset($result[$row['gruppe']]))
					$result[$row['gruppe']]['member'] = true;
			$res->Free();
		}
		else
		{
			// get member count
			foreach($result as $id=>$val)
			{
				$res = $db->Query('SELECT COUNT(*) FROM {pre}adressen_gruppen_member WHERE gruppe=?',
					$id);
				list($result[$id]['members']) = $res->FetchArray();
				$res->Free();
			}
		}

		// return
		return($result);
	}

	/**
	 * check if a group with this title exists
	 *
	 * @param string $title Title
	 * @return bool
	 */
	function GroupExists($title)
	{
		global $db;

		$res = $db->Query('SELECT COUNT(*) FROM {pre}adressen_gruppen WHERE title=? AND user=?',
			$title,
			$this->_userID);
		list($count) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($count > 0);
	}

	/**
	 * add a group
	 *
	 * @param string $title Title
	 * @return int
	 */
	function GroupAdd($title, $davURI = '', $davUID = '')
	{
		global $db;

		$db->Query('INSERT INTO {pre}adressen_gruppen(title,user,`dav_uri`,`dav_uid`) VALUES(?,?,?,?)',
			$title,
			$this->_userID,
			$davURI,
			$davUID);

		$groupID = $db->InsertID();
		ChangelogAdded(BMCL_TYPE_CONTACTGROUP, $groupID, time());
		return($groupID);
	}

	/**
	 * change user picture
	 *
	 * @param int $contactID
	 * @param string $pictureFile
	 * @param string $mimeType
	 * @return bool
	 */
	function ChangePicture($contactID, $pictureFile, $mimeType, $isFile = true)
	{
		global $db;

		$pictureData = serialize(array(
			'mimeType'	=> $mimeType,
			'data'		=> $isFile ? getFileContents($pictureFile) : $pictureFile
		));
		$db->Query('UPDATE {pre}adressen SET picture=? WHERE id=? AND user=?',
			$pictureData,
			(int)$contactID,
			$this->_userID);

		if($db->AffectedRows())
		{
			ChangelogUpdated(BMCL_TYPE_CONTACT, $contactID, time());
			return(true);
		}
		return(false);
	}

	/**
	 * change a contact
	 *
	 * @param int $id Contact ID
	 * @param string $firma Company
	 * @param string $vorname First name
	 * @param string $nachname Last name
	 * @param string $strassenr Street / No
	 * @param string $plz ZIP
	 * @param string $ort City
	 * @param string $land Country
	 * @param string $tel Phone
	 * @param string $fax Fax
	 * @param string $handy Cellphone
	 * @param string $email E-Mail
	 * @param string $work_strassenr Street / No
	 * @param string $work_plz ZIP
	 * @param string $work_ort City
	 * @param string $work_land Country
	 * @param string $work_tel Phone
	 * @param string $work_fax Fax
	 * @param string $work_handy Cellphone
	 * @param string $work_email E-Mail
	 * @param string $anrede Salutation
	 * @param string $position Position
	 * @param string $web WWW address
	 * @param string $kommentar Comment
	 * @param int $geburtsdatum Birthday
	 * @param int $default Default address
	 * @param array $groups Groups
	 * @return bool
	 */
	function Change($contactID, $firma, $vorname, $nachname, $strassenr,
					$plz, $ort, $land, $tel, $fax, $handy,
					$email, $work_strassenr, $work_plz, $work_ort,
					$work_land, $work_tel, $work_fax, $work_handy,
					$work_email, $anrede, $position, $web, $kommentar,
					$geburtsdatum, $default = ADDRESS_PRIVATE,	$groups = array())
	{
		global $db;

		// change contact
		$db->Query('UPDATE {pre}adressen SET '
					. 'firma=?, vorname=?, nachname=?, strassenr=?, plz=?, ort=?, land=?, tel=?, fax=?, handy=?, email=?, '
					. 'work_strassenr=?, work_plz=?, work_ort=?, work_land=?, work_tel=?, work_fax=?, work_handy=?, work_email=?, web=?, kommentar=?, '
					. 'default_address=?, anrede=?, position=?, geburtsdatum=? '
					. 'WHERE id=? AND user=?',
					$firma,
					$vorname,
					$nachname,
					$strassenr,
					$plz,
					$ort,
					$land,
					$tel,
					$fax,
					$handy,
					$email,
					$work_strassenr,
					$work_plz,
					$work_ort,
					$work_land,
					$work_tel,
					$work_fax,
					$work_handy,
					$work_email,
					$web,
					$kommentar,
					$default,
					$anrede,
					$position,
					$geburtsdatum,
					(int)$contactID,
					$this->_userID);
		$affRows = $db->AffectedRows();

		// groups
		if($groups !== false)
		{
			$this->DeContactGroup($contactID);
			if(count($groups) > 0)
				foreach($groups as $group)
					$this->ContactGroup($contactID, (int)$group);
		}

		if($affRows)
		{
			ChangelogUpdated(BMCL_TYPE_CONTACT, $contactID, time());
			return(true);
		}
		return(false);
	}

	/**
	 * invalidate self complete invitation
	 *
	 * @param int $contactID Contact ID
	 * @param string $key Key
	 * @return bool
	 */
	function InvalidateSelfCompleteInvitation($contactID, $key)
	{
		global $db;

		$db->Query('UPDATE {pre}adressen SET invitationCode=? WHERE id=? AND invitationCode=? AND LENGTH(invitationCode)=32',
			'',
			$contactID,
			$key);
		return($db->AffectedRows() == 1);
	}

	/**
	 * send address book self completion invitation
	 *
	 * @param int $contactID Contact ID
	 * @param int $addressType Address type (mail recipient)
	 * @return bool
	 */
	function SendSelfCompleteInvitation($contactID, $addressType)
	{
		global $db, $userRow, $thisUser, $bm_prefs, $lang_custom;

		// fetch mail address
		$res = $db->Query('SELECT email,work_email,invitationCode FROM {pre}adressen WHERE id=? AND user=?',
			$contactID,
			$this->_userID);
		if($res->RowCount() != 1)
			return(false);
		list($eMail, $work_eMail, $invitationCode) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// already sent?
		//if(trim($invitationCode) != '')
		//	return(false);

		// generate invitation code
		$invitationCode = GenerateRandomKey('addressBookInvitationCode');

		// set code
		$db->Query('UPDATE {pre}adressen SET invitationCode=? WHERE id=? AND user=?',
			$invitationCode,
			$contactID,
			$this->_userID);

		// send mail
		$vars = array(
			'vorname'	=> $userRow['vorname'],
			'nachname'	=> $userRow['nachname'],
			'link'		=> sprintf('%sindex.php?action=completeAddressBookEntry&contact=%d&key=%s',
				$bm_prefs['selfurl'],
				$contactID,
				$invitationCode)
		);
		if(SystemMail($thisUser->GetDefaultSender(),
			$eMailTo = ExtractMailAddress($addressType == ADDRESS_PRIVATE ? $eMail : $work_eMail),
			$lang_custom['selfcomp_sub'],
			'selfcomp_text',
			$vars))
		{
			// log
			PutLog(sprintf('User <%s> (%d) invited <%s> to complete his/her address book entry (contact id: %d, IP: %s)',
				$userRow['email'],
				$this->_userID,
				$eMailTo,
				$contactID,
				$_SERVER['REMOTE_ADDR']),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			return(true);
		}
		else
			return(false);
	}

	/**
	 * add a contact
	 *
	 * @param string $firma Company
	 * @param string $vorname First name
	 * @param string $nachname Last name
	 * @param string $strassenr Street / No
	 * @param string $plz ZIP
	 * @param string $ort City
	 * @param string $land Country
	 * @param string $tel Phone
	 * @param string $fax Fax
	 * @param string $handy Cellphone
	 * @param string $email E-Mail
	 * @param string $work_strassenr Street / No
	 * @param string $work_plz ZIP
	 * @param string $work_ort City
	 * @param string $work_land Country
	 * @param string $work_tel Phone
	 * @param string $work_fax Fax
	 * @param string $work_handy Cellphone
	 * @param string $work_email E-Mail
	 * @param string $anrede Salutation
	 * @param string $position Position
	 * @param string $web WWW address
	 * @param string $kommentar Comment
	 * @param int $geburtsdatum Birthday
	 * @param int $default Default address
	 * @param array $groups Groups
	 * @param string $pictureFile Picture file
	 * @param string $pictureMime Picture mime type
	 * @return int
	 */
	function AddContact($firma, $vorname, $nachname, $strassenr,
						$plz, $ort, $land, $tel, $fax, $handy,
						$email, $work_strassenr, $work_plz, $work_ort,
						$work_land, $work_tel, $work_fax, $work_handy,
						$work_email, $anrede, $position, $web, $kommentar,
						$geburtsdatum, $default = ADDRESS_PRIVATE,	$groups = array(),
						$pictureFile = false, $pictureMime = false, $davURI = '', $davUID = '')
	{
		global $db;

		// add contact
		$db->Query('INSERT INTO {pre}adressen(user,firma,vorname,nachname,strassenr,plz,ort,land,tel,fax,handy,email,work_strassenr,work_plz,work_ort,work_land,work_tel,work_fax,work_handy,work_email,web,kommentar,default_address,anrede,position,geburtsdatum,dav_uri,dav_uid) VALUES '
							. '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
							$this->_userID,
							$firma,
							$vorname,
							$nachname,
							$strassenr,
							$plz,
							$ort,
							$land,
							$tel,
							$fax,
							$handy,
							$email,
							$work_strassenr,
							$work_plz,
							$work_ort,
							$work_land,
							$work_tel,
							$work_fax,
							$work_handy,
							$work_email,
							$web,
							$kommentar,
							$default,
							$anrede,
							$position,
							$geburtsdatum,
							$davURI,
							$davUID);
		$contactID = $db->InsertID();

		// groups
		if($contactID > 0 && count($groups) > 0)
			foreach($groups as $group)
				$this->ContactGroup($contactID, (int)$group);

		// picture
		if($pictureFile !== false)
			$this->ChangePicture($contactID, $pictureFile, $pictureMime);

		ChangelogAdded(BMCL_TYPE_CONTACT, $contactID, time());

		// return
		return($contactID);
	}

	/**
	 * associate contact with group
	 *
	 * @param int $contactID Contact ID
	 * @param int $groupID Group ID
	 * @return bool
	 */
	function ContactGroup($contactID, $groupID)
	{
		global $db;
		$db->Query('REPLACE INTO {pre}adressen_gruppen_member(adresse,gruppe) VALUES(?,?)',
			$contactID,
			$groupID);
		if($db->AffectedRows() == 1)
		{
			ChangelogUpdated(BMCL_TYPE_CONTACTGROUP, $groupID, time());
			return true;
		}
		return false;
	}

	/**
	 * de-associate contact with group
	 *
	 * @param int $contactID Contact ID
	 * @param int $groupID Group ID
	 * @return bool
	 */
	function DeContactGroup2($contactID, $groupID)
	{
		global $db;
		$groups = $this->GetGroupList();
		if(!isset($groups[$groupID]))
			return false;
		$db->Query('DELETE FROM {pre}adressen_gruppen_member WHERE `adresse`=? AND `gruppe`=?',
			$contactID,
			$groupID);
		if($db->AffectedRows() == 1)
		{
			ChangelogUpdated(BMCL_TYPE_CONTACTGROUP, $groupID, time());
			return true;
		}
		return false;
	}

	/**
	 * remove contact from all groups
	 *
	 * @param int $contactID Contact ID
	 * @return bool
	 */
	function DeContactGroup($contactID)
	{
		global $db;

		$res = $db->Query('SELECT `gruppe` FROM {pre}adressen_gruppen_member WHERE `adresse`=?',
			$contactID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			ChangelogUpdated(BMCL_TYPE_CONTACTGROUP, $row['gruppe'], time());
		}
		$res->Free();

		$db->Query('DELETE FROM {pre}adressen_gruppen_member WHERE adresse=?',
			$contactID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * delete a contact
	 *
	 * @param int $contactID Contact ID
	 * @return bool
	 */
	function Delete($contactID)
	{
		global $db;

		// delete contact
		$db->Query('DELETE FROM {pre}adressen WHERE id=? AND user=?',
			(int)$contactID,
			$this->_userID);

		// remove from groups
		if($db->AffectedRows() == 1)
		{
			$this->DeContactGroup($contactID);

			ChangelogDeleted(BMCL_TYPE_CONTACT, $contactID, time());

			return(true);
		}

		return(false);
	}

	/**
	 * delete a group
	 *
	 * @param int $groupID Group ID
	 * @return bool
	 */
	function DeleteGroup($groupID)
	{
		global $db;

		$db->Query('DELETE FROM {pre}adressen_gruppen WHERE user=? AND id=?',
			$this->_userID,
			(int)$groupID);

		if($db->AffectedRows() == 1)
		{
			$db->Query('DELETE FROM {pre}adressen_gruppen_member WHERE gruppe=?',
				(int)$groupID);

			ChangelogDeleted(BMCL_TYPE_CONTACTGROUP, $groupID, time());

			return(true);
		}

		return(false);
	}

	/**
	 * fetch contact from database
	 *
	 * @param int $contactID Contact ID
	 * @return array
	 */
	function GetContact($contactID)
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}adressen WHERE id=? AND user=?',
			$contactID,
			$this->_userID);
		if($res->RowCount() == 0)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return($row);
	}

	/**
	 * get contact data for self complete invitation
	 *
	 * @param int $contactID Contact ID
	 * @param string $key Invitation code
	 * @return array
	 */
	function GetContactForSelfCompleteInvitation($contactID, $key)
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}adressen WHERE id=? AND LENGTH(invitationCode)=32 AND invitationCode=?',
			$contactID,
			$key);
		if($res->RowCount() == 0)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return($row);
	}

	/**
	 * fetch group from database
	 *
	 * @param int $groupID Group ID
	 * @return array
	 */
	function GetGroup($groupID)
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}adressen_gruppen WHERE id=? AND user=?',
			$groupID,
			$this->_userID);
		if($res->RowCount() == 0)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return($row);
	}

	/**
	 * change group
	 *
	 * @param int $groupID Group ID
	 * @param string $title Title
	 * @return bool
	 */
	function ChangeGroup($groupID, $title)
	{
		global $db;

		$db->Query('UPDATE {pre}adressen_gruppen SET title=? WHERE id=? AND user=?',
			$title,
			(int)$groupID,
			$this->_userID);

		if($db->AffectedRows() == 1)
		{
			ChangelogUpdated(BMCL_TYPE_CONTACTGROUP, $groupID, time());
			return true;
		}

		return false;
	}

	/**
	 * export contast as VCF
	 *
	 * @param int $contactID Contact ID
	 * @return string
	 */
	function ExportContact($contactID)
	{
		$contact = $this->GetContact($contactID);
		if($contact)
		{
			if(!class_exists('VCardBuilder'))
				include(B1GMAIL_DIR . 'serverlib/vcard.class.php');
			$vcardBuilder = _new('VCardBuilder', array($contact));
			return($vcardBuilder->Build());
		}

		return(false);
	}

	/**
	 * export certain contacts as CSV
	 *
	 * @param array $contactIDs List of contacts (identified by ID)
	 */
	function ExportContacts($contactIDs)
	{
		global $db;

		$res = $db->Query('SELECT ' . $this->_exportFields . ' FROM {pre}adressen WHERE user=? AND id IN ? ORDER BY id ASC',
			$this->_userID,
			$contactIDs);
		$res->ExportCSV();
		$res->Free();
	}

	/**
	 * export all contacts of certain groups as CSV
	 *
	 * @param array $groupIDs List of groups (identified by ID)
	 */
	function ExportGroupContacts($groupIDs)
	{
		global $db;

		$contactIDs = array();
		$res = $db->Query('SELECT adresse FROM {pre}adressen_gruppen_member WHERE gruppe IN ?',
			$groupIDs);
		while($row = $res->FetchArray(MYSQLI_NUM))
			$contactIDs[] = $row[0];
		$res->Free();

		$this->ExportContacts($contactIDs);
	}

	/**
	 * get name/mail for certain groups
	 *
	 * @param array $groupIDs List of groups (identified by ID)
	 * @return array
	 */
	function GetGroupContactMails($groupIDs)
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT {pre}adressen.vorname AS vorname,{pre}adressen.nachname AS nachname,{pre}adressen.email AS email,{pre}adressen.work_email AS work_email,{pre}adressen.default_address AS default_address FROM {pre}adressen,{pre}adressen_gruppen_member WHERE {pre}adressen_gruppen_member.gruppe IN ? AND {pre}adressen.user=? AND {pre}adressen.id={pre}adressen_gruppen_member.adresse',
			$groupIDs,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$result[] = $row;
		$res->Free();

		return($result);
	}

	/**
	 * get list of group members
	 *
	 * @param int $groupID ID of group
	 * @return array
	 */
	function GetGroupMembers($groupID)
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT {pre}adressen.`id` AS `id`, {pre}adressen.`dav_uri` AS `dav_uri`, {pre}adressen.`dav_uid` AS `dav_uid` '
			. 'FROM {pre}adressen '
			. 'INNER JOIN {pre}adressen_gruppen_member '
			. 'ON {pre}adressen_gruppen_member.`adresse`={pre}adressen.`id` '
			. 'WHERE {pre}adressen_gruppen_member.`gruppe`=? '
			. 'AND {pre}adressen.`user`=?',
			$groupID,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$result[$row['id']] = $row;
		$res->Free();

		return $result;
	}

	/**
	 * export whole addressbook
	 *
	 * @param string $lineBreakChar Line break char
	 * @param string $quoteChar Quote char
	 * @param string $sepChar Seperator char
	 */
	function ExportAddressbook($lineBreakChar, $quoteChar, $sepChar)
	{
		global $db;

		$res = $db->Query('SELECT ' . $this->_exportFields . ' FROM {pre}adressen WHERE user=? ORDER BY id ASC',
			$this->_userID);
		$res->ExportCSV($lineBreakChar, $quoteChar, $sepChar);
		$res->Free();
	}
}
