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
include('./serverlib/vcard.class.php');
include('./serverlib/addressbook.class.php');
include('./serverlib/csv.class.php');
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
	header('Location: start.php');
	exit();
}
/**
 * default action = start
 */
$tpl->addJSFile('li', 'clientlib/selectable.js');
$tpl->addJSFile('li', $tpl->tplDir . 'js/organizer.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'start';
$tpl->assign('activeTab', 'addressbook');
$tpl->assign('pageTitle', $lang_user['addressbook']);

/**
 * addressbook interface
 */
$book = _new('BMAddressbook', array($userRow['id']));
$addressbooks = $book->GetAddressbooks();
$sharedAddressbooks = $book->GetSharedAddressbooks();
$defaultAddressbookID = $book->GetDefaultAddressbookID();
$currentAddressbookID = $defaultAddressbookID;
if(isset($_REQUEST['addressbook']))
{
	$currentAddressbookID = $book->ResolveAddressbookID($_REQUEST['addressbook']);
	$thisUser->SetPref('currentAddressbook', $currentAddressbookID);
}
else if(($prefBook = $thisUser->GetPref('currentAddressbook')) !== false)
{
	$currentAddressbookID = $book->ResolveAddressbookID($prefBook);
}
$writableAddressbooks = $addressbooks;
$writableSharedAddressbooksByOwner = array();
foreach($sharedAddressbooks as $bid => $sharedBook)
{
	if($sharedBook['share_access'] === BM_ORGANIZER_ACCESS_WRITE)
	{
		$writableAddressbooks[$bid] = $sharedBook;

		// build an owner label (name if available, e-mail as fallback)
		$ownerName = trim((isset($sharedBook['owner_firstname']) ? $sharedBook['owner_firstname'] : '')
			.' '
			.(isset($sharedBook['owner_lastname']) ? $sharedBook['owner_lastname'] : ''));
		if($ownerName === '')
			$ownerName = isset($sharedBook['owner_email']) ? $sharedBook['owner_email'] : '';
		if($ownerName === '')
			$ownerName = '?';

		if(!isset($writableSharedAddressbooksByOwner[$ownerName]))
			$writableSharedAddressbooksByOwner[$ownerName] = array();
		$writableSharedAddressbooksByOwner[$ownerName][$bid] = $sharedBook;
	}
}
ksort($writableSharedAddressbooksByOwner);
$currentBookWritable = $book->CanWriteAddressbook($currentAddressbookID);
$tpl->assign('addressbooks', $addressbooks);
$tpl->assign('sharedAddressbooks', $sharedAddressbooks);
$tpl->assign('writableAddressbooks', $writableAddressbooks);
$tpl->assign('writableSharedAddressbooksByOwner', $writableSharedAddressbooksByOwner);
$tpl->assign('addressbookCount', count($addressbooks) + count($sharedAddressbooks));
$tpl->assign('currentAddressbookID', $currentAddressbookID);
$tpl->assign('defaultAddressbookID', $defaultAddressbookID);
$tpl->assign('currentBookWritable', $currentBookWritable);
$tpl->assign('currentBookOwned', $book->GetAddressbookByID($currentAddressbookID) !== false);

/**
 * page menu
 */
$tpl->assign('pageMenuFile', 'li/organizer.sidebar.tpl');

/**
 * addressbook fields
 */
$bookFields = array(
	'vorname'			=> $lang_user['firstname'],
	'nachname'			=> $lang_user['surname'],
	'anrede'			=> $lang_user['salutation'],
	'position'			=> $lang_user['position'],
	'firma'				=> $lang_user['company'],
	'strassenr'			=> $lang_user['streetnr'],
	'plz'				=> $lang_user['zip'],
	'ort'				=> $lang_user['city'],
	'land'				=> $lang_user['country'],
	'email'				=> $lang_user['email'],
	'tel'				=> $lang_user['phone'],
	'handy'				=> $lang_user['mobile'],
	'fax'				=> $lang_user['fax'],
	'work_strassenr'	=> $lang_user['work'] . ' ' . $lang_user['streetnr'],
	'work_plz'			=> $lang_user['work'] . ' ' . $lang_user['zip'],
	'work_ort'			=> $lang_user['work'] . ' ' . $lang_user['city'],
	'work_land'			=> $lang_user['work'] . ' ' . $lang_user['country'],
	'work_email'		=> $lang_user['work'] . ' ' . $lang_user['email'],
	'work_tel'			=> $lang_user['work'] . ' ' . $lang_user['phone'],
	'work_handy'		=> $lang_user['work'] . ' ' . $lang_user['mobile'],
	'work_fax'			=> $lang_user['work'] . ' ' . $lang_user['fax'],
	'web'				=> $lang_user['web'],
	'kommentar'			=> $lang_user['comment'],
	'geburtsdatum'		=> $lang_user['birthday']
);

/**
 * csv assoc
 */
$bookFieldsAssoc = array(
	'firstname'							=> 'vorname',
	'prename'							=> 'vorname',
	'vorname'							=> 'vorname',

	'lastname'							=> 'nachname',
	'nachname'							=> 'nachname',
	'surname'							=> 'nachname',
	'name'								=> 'nachname',

	'salutation'						=> 'anrede',
	'anrede'							=> 'anrede',
	'titel'								=> 'anrede',

	'position'							=> 'position',

	'company'							=> 'firma',
	'business'							=> 'firma',
	'firma'								=> 'firma',
	'unternehmen'						=> 'firma',

	'street'							=> 'strassenr',
	'streetno'							=> 'strassenr',
	'streetnr'							=> 'strassenr',
	'strasse'							=> 'strassenr',
	'strassenr'							=> 'strassenr',

	'zip'								=> 'plz',
	'postcode'							=> 'plz',
	'plz'								=> 'plz',
	'postleitzahl privat'				=> 'plz',

	'city'								=> 'ort',
	'town'								=> 'ort',
	'ort'								=> 'ort',
	'ort privat'						=> 'ort',

	'country'							=> 'land',
	'nation'							=> 'land',
	'nationality'						=> 'land',
	'land'								=> 'land',
	'land/region privat'				=> 'land',

	'email'								=> 'email',
	'mail'								=> 'email',
	'e-mail'							=> 'email',
	'e-mail-address'					=> 'email',
	'e-mail-adresse'					=> 'email',
	'mail-address'						=> 'email',
	'mailaddress'						=> 'email',

	'phone'								=> 'tel',
	'telephone'							=> 'tel',
	'telefon'							=> 'tel',
	'telefon privat'					=> 'tel',

	'mobile'							=> 'handy',
	'cellphone'							=> 'handy',
	'cell'								=> 'handy',
	'mobilephone'						=> 'handy',
	'mobil'								=> 'handy',
	'handy'								=> 'handy',
	'mobiltelefon'						=> 'handy',

	'fax'								=> 'fax',
	'facsimile'							=> 'fax',
	'fax privat'						=> 'fax',

	'workstreet'						=> 'work_strassenr',
	'workzip'							=> 'work_plz',
	'workcity'							=> 'work_ort',
	'workcountry'						=> 'work_land',
	'workemail'							=> 'work_email',
	'workphone'							=> 'work_tel',
	'workmobile'						=> 'work_handy',
	'workfax'							=> 'work_fax',

	'homepage'							=> 'web',
	'web'								=> 'web',
	'www'								=> 'web',
	'url'								=> 'web',

	'comment'							=> 'kommentar',
	'kommentar'							=> 'kommentar',
	'note'								=> 'kommentar',
	'notes'								=> 'kommentar',

	'birthday'							=> 'geburtsdatum',
	'birthdate'							=> 'geburtsdatum',
	'geburtstag'						=> 'geburtsdatum',
	'geburtsdatum'						=> 'geburtsdatum'
);

/**
 * start page
 */
if($_REQUEST['action'] == 'start')
{
	$sortColumns = array('vorname', 'nachname', 'firma', 'email', 'gruppe');

	// group id
	$currentGroup = -1;
	if(isset($_REQUEST['group']) && (substr($_REQUEST['group'], 0, 3) == 'wg_' || (int)$_REQUEST['group'] > 0))
		$currentGroup = $_REQUEST['group'];
	$tpl->assign('currentGroup', $currentGroup);

	// get sort info
	$sortColumn = (isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], $sortColumns))
					? $_REQUEST['sort']
					: 'nachname';
	$sortOrder = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')))
					? $_REQUEST['order']
					: 'asc';

	// get addresses & groups
	$addressList = $book->GetAddressBook(isset($_REQUEST['letter']) ? $_REQUEST['letter'] : '*',
						$currentGroup,
						$sortColumn,
						$sortOrder,
						true,
						$currentAddressbookID);
	$groupList = $book->GetGroupList(0, $currentAddressbookID);

	// get page info
	$entriesPerPage = (int)$bm_prefs['ordner_proseite'];
	$pageNo = (isset($_REQUEST['page']))
					? (int)$_REQUEST['page']
					: 1;
	$addressCount = count($addressList);
	$pageCount = max(1, ceil($addressCount / max(1, $entriesPerPage)));
	$pageNo = min($pageCount, max(1, $pageNo));

	// extract page
	$addressList = array_slice($addressList, ($pageNo-1) * $entriesPerPage, $entriesPerPage, true);

	// page output
	$tpl->assign('alpha', array(
		'A'		=> 'A',		'B'		=> 'B',		'C'		=> 'C',		'D'		=> 'D',		'E'		=> 'E',
		'F'		=> 'F',		'G'		=> 'G',		'H'		=> 'H',		'I'		=> 'I',		'J'		=> 'J',
		'K'		=> 'K',		'L'		=> 'L',		'M'		=> 'M',		'N'		=> 'N',		'O'		=> 'O',
		'P'		=> 'P',		'Q'		=> 'Q',		'R'		=> 'R',		'S'		=> 'S',		'T'		=> 'T',
		'U'		=> 'U',		'V'		=> 'V',		'W'		=> 'W',		'X'		=> 'X',		'Y'		=> 'Y',
		'Z'		=> 'Z',		'9'		=> '0-9'));
	$tpl->assign('pageNo', $pageNo);
	$tpl->assign('pageCount', $pageCount);
	$tpl->assign('addressList', $addressList);
	$tpl->assign('groupList', $groupList);
	$tpl->assign('sortColumn', $sortColumn);
	$tpl->assign('sortOrder', $sortOrder);
	$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
	$tpl->assign('pageContent', 'li/organizer.addressbook.tpl');
	$tpl->assign('displayLetter', urlencode(isset($_REQUEST['letter']) ? $_REQUEST['letter'] : '*'));

	// F5: consume the "cross-owner move refused" flash exactly once.
	if(!empty($_SESSION['bm_addrMoveBlocked']))
	{
		$tpl->assign('moveBlockedNotice', true);
		unset($_SESSION['bm_addrMoveBlocked']);
	}

	$tpl->display('li/index.tpl');
}

/**
 * add contact
 */
else if($_REQUEST['action'] == 'addContact')
{
	if(isset($_REQUEST['importFile'])
		&& ValidTempFile($userRow['id'], (int)$_REQUEST['importFile']))
	{
		$tempID = (int)$_REQUEST['importFile'];
		$tempName = TempFileName($tempID);

		$jsCode  = '<script>' . "\n";
		$jsCode .= '<!--' . "\n";

		// parse card
		$vcardReader = _new('VCardReader', array($vcardFP = fopen($tempName, 'r')));
		$cardData = $vcardReader->Parse();
		fclose($vcardFP);

		// output fill-in code
		foreach($cardData as $key=>$val)
		{
			if($key == 'email' || $key == 'work_email')
				$val = DecodeEMail($val);
			if($key != 'geburtsdatum' && $key != 'anrede')
			{
				$jsCode .= sprintf('document.getElementById(\'%s\').value = \'%s\';' . "\n",
					$key,
					addslashes(str_replace(array("\r", "\n"), '', $val)));
			}
		}

		$jsCode .= '//-->' . "\n";
		$jsCode .= '</script>' . "\n";

		ReleaseTempFile($userRow['id'], $tempID);
		$tpl->assign('jsCode', $jsCode);
	}

	$tpl->assign('pageTitle', $lang_user['addcontact']);
	$tpl->assign('pageContent', 'li/organizer.addressbook.edit.tpl');
	$tpl->assign('groups', $book->GetGroupList(0, -1));
	$tpl->display('li/index.tpl');
}

/**
 * quick create
 */
else if($_REQUEST['action'] == 'quickAdd' && IsPOSTRequest())
{
	if(is_array($_POST['addr']))
	{
		foreach($_POST['addr'] as $addr)
		{
			if(!isset($addr['email']))
				continue;
			$email 		= empty($addr['company']) ? $addr['email'] : '';
			$workEmail 	= !empty($addr['company']) ? $addr['email'] : '';
			$groups		= isset($addr['groups']) && is_array($addr['groups']) ? $addr['groups'] : array();
			$book->AddContact($addr['company'], $addr['firstname'], $addr['lastname'], '', '', '', '', '', '', '', $email,
				'', '', '', '', '', '', '', $workEmail, '', '', '', '', '', empty($addr['company']) ? ADDRESS_PRIVATE : ADDRESS_WORK, $groups,
				false, false, '', '', isset($_POST['addressbook']) ? (int)$_POST['addressbook'] : $currentAddressbookID);
		}
	}

	$tpl->assign('icon', 'yes.png');
	$tpl->assign('msg', $lang_user['addradddone']);
	$tpl->display('li/msg.tiny.tpl');
	exit();
}

/**
 * create contact
 */
else if($_REQUEST['action'] == 'createContact'
		&& isset($_REQUEST['vorname']) && isset($_REQUEST['nachname'])
		&& IsPOSTRequest())
{
	// process groups
	$groups = array();
	foreach($_POST as $key=>$val)
		if(substr($key, 0, 6) == 'group_' && $key !== 'group_new')
			$groups[] = (int)substr($key, 6);

	// add new group?
	if(isset($_POST['group_new']))
	{
		$groupName = trim($_POST['group_new_name']);
		if(!empty($groupName))
		{
			$groups[] = $book->GroupAdd($groupName, '', '', isset($_POST['addressbook']) ? (int)$_POST['addressbook'] : $currentAddressbookID);
		}
	}

	// picture
	$pictureFile = false;
	$pictureMime = false;
	if(isset($_REQUEST['pictureFile']) && isset($_REQUEST['pictureMime']) && is_numeric($_REQUEST['pictureFile'])
		&& $_REQUEST['pictureFile'] > 0	&& ValidTempFile($userRow['id'], (int)$_REQUEST['pictureFile']))
	{
		$pictureFile = TempFileName((int)$_REQUEST['pictureFile']);
		$pictureMime = $_REQUEST['pictureMime'];
	}

	// add contact
	$contactID = $book->AddContact($_REQUEST['firma'],
		$_REQUEST['vorname'],
		$_REQUEST['nachname'],
		$_REQUEST['strassenr'],
		$_REQUEST['plz'],
		$_REQUEST['ort'],
		$_REQUEST['land'],
		$_REQUEST['tel'],
		$_REQUEST['fax'],
		$_REQUEST['handy'],
		$_REQUEST['email'],
		$_REQUEST['work_strassenr'],
		$_REQUEST['work_plz'],
		$_REQUEST['work_ort'],
		$_REQUEST['work_land'],
		$_REQUEST['work_tel'],
		$_REQUEST['work_fax'],
		$_REQUEST['work_handy'],
		$_REQUEST['work_email'],
		$_REQUEST['anrede'],
		$_REQUEST['position'],
		$_REQUEST['web'],
		$_REQUEST['kommentar'],
		SmartyDateTime('geburtsdatum_'),
		$_REQUEST['default'] == 'work' ? ADDRESS_WORK : ADDRESS_PRIVATE,
		$groups,
		$pictureFile,
		$pictureMime,
		'',
		'',
		isset($_REQUEST['addressbook']) ? (int)$_REQUEST['addressbook'] : $currentAddressbookID);

	// release temp file
	if($pictureFile !== false)
		ReleaseTempFile($userRow['id'], $_REQUEST['pictureFile']);

	// submit action?
	if(isset($_REQUEST['submitAction']) && $_REQUEST['submitAction'] == 'selfComplete')
	{
		// self complete page
		SessionRedirect('organizer.addressbook.php?action=selfComplete&id=' . (int)$contactID);
	}
	else
	{
		// back to list
		SessionRedirect('organizer.addressbook.php');
	}
}

/**
 * show contact
 */
else if($_REQUEST['action'] == 'showContact'
		&& isset($_REQUEST['id']))
{
	$contact = $book->GetContact((int)$_REQUEST['id']);
	if($contact !== false)
	{
		$groupList = $book->GetGroupList((int)$_REQUEST['id'], isset($contact['addressbook_id']) ? (int)$contact['addressbook_id'] : $currentAddressbookID);

		$contact['kommentar'] = nl2br(HTMLFormat($contact['kommentar']));

		if(!empty($contact['vorname']) || !empty($contact['nachname']))
		{
			$tpl->assign('privEmailTo', urlencode(sprintf('"%s" <%s>',
				trim($contact['nachname'] . ', ' . $contact['vorname']),
				$contact['email'])));
			$tpl->assign('workEmailTo', urlencode(sprintf('"%s" <%s>',
				trim($contact['nachname'] . ', ' . $contact['vorname']),
				$contact['work_email'])));
		}
		else
		{
			$tpl->assign('privEmailTo', urlencode($contact['email']));
			$tpl->assign('workEmailTo', urlencode($contact['work_email']));
		}

		$tpl->assign('groups', $groupList);
		$tpl->assign('contact', $contact);
		$tpl->display('li/organizer.addressbook.show.tpl');
	}
}

/**
 * edit contact
 */
else if($_REQUEST['action'] == 'editContact'
		&& isset($_REQUEST['id']))
{
	$contact = $book->GetContact((int)$_REQUEST['id']);
	if($contact !== false)
	{
		$groupList = $book->GetGroupList((int)$_REQUEST['id'], -1);
		$tpl->assign('pageTitle', $lang_user['editcontact']);
		$tpl->assign('pageContent', 'li/organizer.addressbook.edit.tpl');
		$tpl->assign('groups', $groupList);
		$tpl->assign('contact', $contact);
		$tpl->display('li/index.tpl');
	}
}

/**
 * save contact
 */
else if($_REQUEST['action'] == 'saveContact'
		&& isset($_REQUEST['id']) && isset($_REQUEST['vorname']) && isset($_REQUEST['nachname'])
		&& IsPOSTRequest())
{
	// process groups
	$groups = array();
	foreach($_POST as $key=>$val)
		if(substr($key, 0, 6) == 'group_' && $key !== 'group_new')
			$groups[] = (int)substr($key, 6);

	// add new group?
	if(isset($_POST['group_new']))
	{
		$groupName = trim($_POST['group_new_name']);
		if(!empty($groupName))
		{
			$groups[] = $book->GroupAdd($groupName, '', '', isset($_POST['addressbook']) ? (int)$_POST['addressbook'] : $currentAddressbookID);
		}
	}

	// change contact
	$book->Change((int)$_REQUEST['id'],
		$_REQUEST['firma'],
		$_REQUEST['vorname'],
		$_REQUEST['nachname'],
		$_REQUEST['strassenr'],
		$_REQUEST['plz'],
		$_REQUEST['ort'],
		$_REQUEST['land'],
		$_REQUEST['tel'],
		$_REQUEST['fax'],
		$_REQUEST['handy'],
		$_REQUEST['email'],
		$_REQUEST['work_strassenr'],
		$_REQUEST['work_plz'],
		$_REQUEST['work_ort'],
		$_REQUEST['work_land'],
		$_REQUEST['work_tel'],
		$_REQUEST['work_fax'],
		$_REQUEST['work_handy'],
		$_REQUEST['work_email'],
		$_REQUEST['anrede'],
		$_REQUEST['position'],
		$_REQUEST['web'],
		$_REQUEST['kommentar'],
		SmartyDateTime('geburtsdatum_'),
		$_REQUEST['default'] == 'work' ? ADDRESS_WORK : ADDRESS_PRIVATE,
		$groups,
		isset($_REQUEST['addressbook']) ? (int)$_REQUEST['addressbook'] : $currentAddressbookID);

	// F5: Change() silently keeps the contact in its source book when
	// a cross-owner move was requested. Surface that as a session-flash
	// so the next page render can show a warning banner and the user
	// isn't left wondering why nothing happened.
	if($book->wasLastMoveBlocked())
	{
		$_SESSION['bm_addrMoveBlocked'] = true;
	}

	// submit action?
	if(isset($_REQUEST['submitAction']) && $_REQUEST['submitAction'] == 'selfComplete')
	{
		// self complete page
		SessionRedirect('organizer.addressbook.php?action=selfComplete&id=' . (int)$_REQUEST['id']);
	}

	// send mail?
	else if(isset($_REQUEST['submitAction']) && $_REQUEST['submitAction'] == 'sendMail')
	{
		// compose
		$email = urlencode($_REQUEST['default'] == 'work'
			? $_REQUEST['work_email']
			: $_REQUEST['email']);
		SessionRedirect('email.compose.php?to=' . $email);
	}

	// vcf?
	else if(isset($_REQUEST['submitAction']) && $_REQUEST['submitAction'] == 'exportVCF')
	{
		// send VCF
		$vcfData = $book->ExportContact((int)$_REQUEST['id']);

		// headers
		header('Pragma: public');
		SendContentDispositionHeader('attachment',
			trim((string)$_REQUEST['vorname'] . ' ' . (string)$_REQUEST['nachname']) . '.vcf');
		header('Content-Type: text/x-vcard; charset=' . $currentCharset);
		header(sprintf('Content-Length: %d',
			strlen($vcfData)));

		// send it
		echo $vcfData;
		exit();
	}

	// create conversation folder
	else if(isset($_REQUEST['submitAction']) && $_REQUEST['submitAction'] == 'intelliFolder')
	{
		if(!class_exists('BMMailbox'))
			include('./serverlib/mailbox.class.php');

		$mailbox = _new('BMMailbox', array($userRow['id'], $userRow['email'], $thisUser));

		$folderTitle = $_REQUEST['vorname'] . ' ' . $_REQUEST['nachname'];
		$folderID = $mailbox->AddFolder($folderTitle,
			-1, true, -1, true, true);
		$mailbox->UpdateFolder($folderID, $folderTitle,
			-1, true, -1, BMLINK_OR);

		if(($value = ExtractMailAddress($_REQUEST['email'], true)) != '')
		{
			$conditionID = $mailbox->AddCondition($folderID);
			$mailbox->UpdateCondition($conditionID, $folderID, MAILFIELD_TO, BMOP_CONTAINS, $value);
			$conditionID = $mailbox->AddCondition($folderID);
			$mailbox->UpdateCondition($conditionID, $folderID, MAILFIELD_FROM, BMOP_CONTAINS, $value);
		}
		if(($value = ExtractMailAddress($_REQUEST['work_email'], true)) != '')
		{
			$conditionID = $mailbox->AddCondition($folderID);
			$mailbox->UpdateCondition($conditionID, $folderID, MAILFIELD_TO, BMOP_CONTAINS, $value);
			$conditionID = $mailbox->AddCondition($folderID);
			$mailbox->UpdateCondition($conditionID, $folderID, MAILFIELD_FROM, BMOP_CONTAINS, $value);
		}

		SessionRedirect('email.folders.php?action=editFolder&id=' . $folderID);
	}

	// default
	else
	{
		// back to list
		SessionRedirect('organizer.addressbook.php');
	}
}

/**
 * send self complete link
 */
else if($_REQUEST['action'] == 'sendSelfComplete'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['destMail']))
{
	$tpl->assign('pageTitle', $lang_user['complete']);

	$result = $book->SendSelfCompleteInvitation((int)$_REQUEST['id'], $_REQUEST['destMail'] == 'private'
				? ADDRESS_PRIVATE
				: ADDRESS_WORK);
	if(!$result)
	{
		$tpl->assign('msg', $lang_user['complete_error']);
		$tpl->assign('pageContent', 'li/error.tpl');
	}
	else
	{
		$tpl->assign('title', $lang_user['complete']);
		$tpl->assign('msg', $lang_user['complete_ok']);
		$tpl->assign('pageContent', 'li/msg.tpl');
	}

	$tpl->assign('backLink', 'organizer.addressbook.php?action=editContact&id=' . (int)$_REQUEST['id']);
	$tpl->display('li/index.tpl');
}

/**
 * self complete
 */
else if($_REQUEST['action'] == 'selfComplete'
		&& isset($_REQUEST['id']))
{
	$tpl->assign('pageTitle', $lang_user['complete']);

	$contact = $book->GetContact((int)$_REQUEST['id']);
	if($contact !== false)
	{
		$workMail = trim($contact['work_email']);
		$privateMail = trim($contact['email']);

		if(strpos($workMail, '@') === false && strpos($privateMail, '@') === false)
		{
			$tpl->assign('msg', $lang_user['complete_noemail']);
			$tpl->assign('backLink', SessionUrl('organizer.addressbook.php?action=editContact&id=' . (int)$_REQUEST['id']));
			$tpl->assign('pageContent', 'li/error.tpl');
		}
		else if(trim($contact['invitationCode']) != ''
			&& !isset($_REQUEST['anyhow']))
		{
			$tpl->assign('msg', $lang_user['complete_invited']);
			$tpl->assign('backLink', SessionUrl('organizer.addressbook.php?action=editContact&id=' . (int)$_REQUEST['id']));
			$tpl->assign('otherButton', array(
				'caption' 	=> $lang_user['send_anyhow'],
				'href'		=> SessionUrl('organizer.addressbook.php?action=selfComplete&id=' . (int)$_REQUEST['id'] . '&anyhow=true')
			));
			$tpl->assign('pageContent', 'li/error.tpl');
		}
		else
		{
			$tpl->assign('workMail', strpos($workMail, '@') !== false ? $workMail : false);
			$tpl->assign('privateMail', strpos($privateMail, '@') !== false ? $privateMail : false);
			$tpl->assign('id', (int)$_REQUEST['id']);
			$tpl->assign('pageContent', 'li/organizer.addressbook.selfcomplete.tpl');
		}

		$tpl->display('li/index.tpl');
	}
}

/**
 * picture
 */
else if($_REQUEST['action'] == 'addressbookPicture'
		&& isset($_REQUEST['id']))
{
	if($_REQUEST['id'] == -1
		&& isset($_REQUEST['tempID'])
		&& isset($_REQUEST['contentType']))
	{
		if(ValidTempFile($userRow['id'], $_REQUEST['tempID']))
		{
			$tempName = TempFileName($_REQUEST['tempID']);
			header('Content-Type: ' . $_REQUEST['contentType']);
			header('Content-Length: ' . filesize($tempName));
			readfile($tempName);
		}
	}
	else
	{
		$contact = $book->GetContact((int)$_REQUEST['id']);
		if($contact !== false && $contact['picture'] != '')
		{
			$picture = @unserialize($contact['picture']);
			if(is_array($picture))
			{
				header('Content-Type: ' . $picture['mimeType']);
				header('Content-Length: ' . strlen($picture['data']));
				echo $picture['data'];
			}
		}
	}
}

/**
 * vcf import dialog
 */
else if($_REQUEST['action'] == 'vcfImportDialog')
{
	$tpl->assign('title', $lang_user['importvcf']);
	$tpl->assign('text', $lang_user['importvcftext']);
	$tpl->assign('formAction', SessionUrl('organizer.addressbook.php?action=vcfImportDialogSubmit'));
	$tpl->assign('fieldName', 'vcfFile');
	$tpl->display('li/dialog.openfile.tpl');
}

/**
 * vcf import dialog submit
 */
else if($_REQUEST['action'] == 'vcfImportDialogSubmit'
		&& IsPOSTRequest())
{
	$tempID = RequestTempFile($userRow['id'], time()+TIME_ONE_HOUR);
	$tempName = TempFileName($tempID);
	$vcfFile = getUploadedFile('vcfFile', $tempName);

	echo '<script>' . "\n";
	echo '<!--' . "\n";

	// parse card, if any
	if($vcfFile)
	{
		// parse card
		$vcardReader = _new('VCardReader', array($vcardFP = fopen($tempName, 'r')));
		$cardData = $vcardReader->Parse();
		fclose($vcardFP);

		// output fill-in code
		foreach($cardData as $key=>$val)
		{
			if($key != 'geburtsdatum' && $key != 'anrede')
			{
				printf('parent.document.getElementById(\'%s\').value = \'%s\';' . "\n",
					$key,
					addslashes(str_replace(array("\r", "\n"), '', $val)));
			}
		}
	}

	echo 'parent.hideOverlay();' . "\n";
	echo '//-->' . "\n";
	echo '</script>' . "\n";

	ReleaseTempFile($userRow['id'], $tempID);
}

/**
 * delete a contact
 */
else if($_REQUEST['action'] == 'deleteContact'
		&& isset($_REQUEST['id']))
{
	$book->Delete((int)$_REQUEST['id']);
	header('Location: organizer.addressbook.php');
}

/**
 * contact action
 */
else if($_REQUEST['action'] == 'action')
{
	if(!empty($_POST['addrIDs']))
	{
		$addrIDs = explode(';', $_POST['addrIDs']);
	}
	else
	{
		$addrIDs = array();
		foreach($_POST as $key=>$val)
			if(substr($key, 0, 5) == 'addr_')
			{
				$id = substr($key, 5);
				$addrIDs[] = $id;
			}
	}
	foreach($addrIDs as $key=>$val) $addrIDs[$key] = (int)$val;

	if($_REQUEST['do'] == 'delete')
	{
		// F2: bulk contact deletion needs a CSRF token so a GET link
		// cannot silently wipe all selected contacts.
		CsrfEnforceOnStateChange();
		foreach($addrIDs as $id)
			$book->Delete((int)$id);
	}
	else if($_REQUEST['do'] == 'sendmail')
	{
		$to = array();

		foreach($addrIDs as $id)
		{
			$contact = $book->GetContact($id);
			$email = $contact['default_address'] == ADDRESS_WORK
				? $contact['work_email']
				: $contact['email'];

			if(trim($email) != '')
			{
				if(empty($contact['vorname']) && empty($contact['nachname']))
					array_push($to, sprintf('"%s" <%s>',
						$contact['firma'],
						$email));
				else
					array_push($to, sprintf('"%s, %s" <%s>',
						$contact['nachname'],
						$contact['vorname'],
						$email));
			}
		}

		$toList = urlencode(implode(', ', $to));
		header('Location: email.compose.php' . '&to=' . $toList);
		exit();
	}
	else if($_REQUEST['do'] == 'export')
	{
		// collect IDs
		$contactIDs = array();
		foreach($addrIDs as $id)
		{
			$contactIDs[] = (int)$id;
		}

		// export
		header('Pragma: public');
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="export.csv"');
		$book->ExportContacts($contactIDs);
		exit();
	}
	else if(strlen($_REQUEST['do']) > 11 && substr($_REQUEST['do'], 0, 11) == 'addtogroup_')
	{
		$groupID = (int)substr($_REQUEST['do'], 11);

		// add to groups
		foreach($addrIDs as $id)
			$book->ContactGroup($id, $groupID);
	}
	header('Location: organizer.addressbook.php');
}

/**
 * groups
 */
else if($_REQUEST['action'] == 'groups')
{
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add')
	{
		CsrfEnforceOnStateChange();
		if(!$book->GroupExists($_REQUEST['title'], $currentAddressbookID))
		{
			$book->GroupAdd($_REQUEST['title'], '', '', $currentAddressbookID);
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'delete')
	{
		// F2: single-group deletion via GET was CSRF-able.
		CsrfEnforceOnStateChange();
		$book->DeleteGroup($_REQUEST['id']);
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'export')
	{
		// export
		header('Pragma: public');
		header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename="export.csv"');
		$book->ExportGroupContacts(array((int)$_REQUEST['id']));
		exit();
	}

	$tpl->assign('groupList', $book->GetGroupList(0, $currentAddressbookID));
	$tpl->display('li/organizer.addressbook.groups.tpl');
}

/**
 * address books
 */
else if($_REQUEST['action'] == 'books')
{
	// F2: creating / renaming / deleting an address book is
	// state-changing and requires a CSRF token (POST body/header or
	// URL query parameter — CsrfEnforceOnStateChange accepts all).
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add'
		&& isset($_REQUEST['title']) && trim($_REQUEST['title']) != '')
	{
		CsrfEnforceOnStateChange();
		$book->AddAddressbook($_REQUEST['title']);
		SessionRedirect('organizer.addressbook.php');
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'save'
		&& isset($_REQUEST['id']))
	{
		CsrfEnforceOnStateChange();
		$book->UpdateAddressbook((int)$_REQUEST['id'], $_REQUEST['title']);
		SessionRedirect('organizer.addressbook.php');
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'delete'
		&& isset($_REQUEST['id']))
	{
		CsrfEnforceOnStateChange();
		$book->DeleteAddressbook((int)$_REQUEST['id']);
		SessionRedirect('organizer.addressbook.php');
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'edit'
		&& isset($_REQUEST['id']))
	{
		$bookItem = $book->GetAddressbookByID((int)$_REQUEST['id']);
		if($bookItem !== false)
		{
			$tpl->assign('bookItem', $bookItem);
			$tpl->display('li/organizer.addressbook.books.dialog.tpl');
			exit();
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'addForm')
	{
		$tpl->display('li/organizer.addressbook.books.dialog.tpl');
		exit();
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'deleteForm'
		&& isset($_REQUEST['id']))
	{
		$bookItem = $book->GetAddressbookByID((int)$_REQUEST['id']);
		if($bookItem !== false && empty($bookItem['is_default']))
		{
			$tpl->assign('bookItem', $bookItem);
			$tpl->display('li/organizer.addressbook.books.delete.tpl');
			exit();
		}
	}

	$tpl->assign('addressbooks', $book->GetAddressbooks());
	$tpl->assign('pageContent', 'li/organizer.addressbook.books.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * share address book
 */
else if($_REQUEST['action'] == 'share' && isset($_REQUEST['id']))
{
	if(!bmOrganizerGroupCanShare('addressbook'))
	{
		SessionRedirect('organizer.addressbook.php');
		exit();
	}
	$bookItem = $book->GetAddressbookByID((int)$_REQUEST['id']);
	if($bookItem === false)
	{
		SessionRedirect('organizer.addressbook.php');
		exit();
	}

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add' && isset($_REQUEST['email']) && IsPOSTRequest())
	{
		$targetId = bmOrganizerShareTargetUserId($_REQUEST['email']);
		$access = isset($_REQUEST['access']) ? $_REQUEST['access'] : BM_ORGANIZER_ACCESS_READ;
		$result = bmOrganizerAddShare(BM_ORGANIZER_SHARE_BOOK, (int)$bookItem['id'], $userRow['id'], $targetId, $access);
		if(is_string($result) && isset($lang_user[$result]))
			$tpl->assign('shareError', $lang_user[$result]);
		else if((int)$result > 0)
		{
			bmOrganizerNotifyShareInvite(BM_ORGANIZER_SHARE_BOOK, $bookItem, $userRow['id'], $targetId, $access);
			$tpl->assign('shareSuccess', $lang_user['shareinvited']);
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'remove'
		&& isset($_REQUEST['share'])
		&& IsPOSTRequest())
	{
		// F2: revoking an addressbook share was GET-reachable.
		bmOrganizerRemoveShare(BM_ORGANIZER_SHARE_BOOK, (int)$_REQUEST['share'], $userRow['id']);
	}

	$tpl->assign('shareItem', $bookItem);
	$tpl->assign('shareList', bmOrganizerListShares(BM_ORGANIZER_SHARE_BOOK, (int)$bookItem['id'], $userRow['id']));
	$tpl->assign('shareType', 'addressbook');
	$tpl->assign('shareAction', 'organizer.addressbook.php');
	$tpl->display('li/organizer.share.dialog.tpl');
	exit();
}

/**
 * leave an address book shared with this user
 */
else if($_REQUEST['action'] == 'leaveshare' && isset($_REQUEST['id']))
{
	$bookItem = $book->GetAccessibleAddressbook((int)$_REQUEST['id']);
	if($bookItem === false || empty($bookItem['shared']))
	{
		SessionRedirect('organizer.addressbook.php');
		exit();
	}

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'leave' && IsPOSTRequest())
	{
		bmOrganizerLeaveShare(BM_ORGANIZER_SHARE_BOOK, (int)$bookItem['id'], $userRow['id']);
		$thisUser->SetPref('currentAddressbook', $defaultAddressbookID);
		SessionRedirect('organizer.addressbook.php');
		exit();
	}

	$tpl->assign('leaveItem', $bookItem);
	$tpl->assign('leaveAction', 'organizer.addressbook.php');
	$tpl->display('li/organizer.share.leave.tpl');
	exit();
}

/**
 * export dialog
 */
else if($_REQUEST['action'] == 'exportDialog')
{
	$tpl->display('li/organizer.addressbook.export.tpl');
}

/**
 * import dialog
 */
else if($_REQUEST['action'] == 'importDialogStart')
{
	$tpl->display('li/organizer.addressbook.import.tpl');
}

/**
 * group action
 */
else if($_REQUEST['action'] == 'groupAction')
{
	// add group
	if(isset($_REQUEST['add']))
	{
		if(!$book->GroupExists($_REQUEST['title'], $currentAddressbookID))
		{
			$book->GroupAdd($_REQUEST['title'], '', '', $currentAddressbookID);
			header('Location: organizer.addressbook.php');
		}
		else
		{
			// already exists
			$tpl->assign('msg', $lang_user['groupexists']);
			$tpl->assign('pageContent', 'li/error.tpl');
			$tpl->display('li/index.tpl');
		}
	}

	// delete — F2: CSRF-token required.
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'delete')
	{
		CsrfEnforceOnStateChange();
		foreach($_POST as $key=>$val)
		{
			if(substr($key, 0, 6) == 'group_')
			{
				$id = substr($key, 6);
				$book->DeleteGroup((int)$id);
			}
		}
		header('Location: organizer.addressbook.php');
	}

	// export as CSV
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'export')
	{
		// collect IDs
		$groupIDs = array();
		foreach($_POST as $key=>$val)
			if(substr($key, 0, 6) == 'group_')
			{
				$id = substr($key, 6);
				$groupIDs[] = (int)$id;
			}

		// export
		header('Pragma: public');
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="export.csv"');
		$book->ExportGroupContacts($groupIDs);
		exit();
	}

	// mail
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'sendmail')
	{
		// collect IDs
		$groupIDs = array();
		foreach($_POST as $key=>$val)
			if(substr($key, 0, 6) == 'group_')
			{
				$id = substr($key, 6);
				$groupIDs[] = (int)$id;
			}

		// collect addresses
		$addresses = $book->GetGroupContactMails($groupIDs);
		$to = array();
		foreach($addresses as $contact)
		{
			$email = $contact['default_address'] == ADDRESS_WORK
				? $contact['work_email']
				: $contact['email'];

			if(trim($email) != '')
				array_push($to, sprintf('"%s, %s" <%s>',
					$contact['nachname'],
					$contact['vorname'],
					$email));
		}

		// redirect
		$toList = urlencode(implode(', ', $to));
		header('Location: email.compose.php' . '&to=' . $toList);
		exit();
	}

	// no / invalid action
	else
	{
		header('Location: organizer.addressbook.php');
		exit();
	}
}

/**
 * delete single group
 */
else if($_REQUEST['action'] == 'deleteGroup'
		&& isset($_REQUEST['id']))
{
	$book->DeleteGroup((int)$_REQUEST['id']);
	header('Location: organizer.addressbook.php');
}

/**
 * edit group
 */
else if($_REQUEST['action'] == 'editGroup'
		&& isset($_REQUEST['id']))
{
	$group = $book->GetGroup((int)$_REQUEST['id']);
	if($group !== false)
	{
		$tpl->assign('group', $group);
		$tpl->assign('pageContent', 'li/organizer.addressbook.editgroup.tpl');
		$tpl->display('li/index.tpl');
	}
}

/**
 * save group
 */
else if($_REQUEST['action'] == 'saveGroup'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['title'])
		&& IsPOSTRequest())
{
	$group = $book->GetGroup((int)$_REQUEST['id']);

	if($group['title'] != $_REQUEST['title'] && $book->GroupExists($_REQUEST['title']))
	{
		// already exists
		$tpl->assign('msg', $lang_user['groupexists']);
		$tpl->assign('pageContent', 'li/error.tpl');
		$tpl->display('li/index.tpl');
	}
	else
	{
		$book->ChangeGroup((int)$_REQUEST['id'], $_REQUEST['title']);
		header('Location: organizer.addressbook.php');
	}
}

/**
 * user picture dialog
 */
else if($_REQUEST['action'] == 'userPictureDialog'
		&& isset($_REQUEST['id']))
{
	$tpl->assign('title', $lang_user['userpicture']);
	$tpl->assign('text', $lang_user['userpicturetext']);
	$tpl->assign('formAction', SessionUrl('organizer.addressbook.php?action=userPictureDialogSubmit&id=' . (int)$_REQUEST['id']));
	$tpl->assign('fieldName', 'pictureFile');
	$tpl->display('li/dialog.openfile.tpl');
}

/**
 * user picture dialog submit
 */
else if($_REQUEST['action'] == 'userPictureDialogSubmit'
		&& isset($_REQUEST['id'])
		&& IsPOSTRequest())
{
	$tempID = RequestTempFile($userRow['id'], time()+TIME_ONE_HOUR);
	$tempName = TempFileName($tempID);
	$uploadFile = getUploadedFile('pictureFile', $tempName);
	$allowedMimes = array('image/png', 'image/x-png', 'image/jpg', 'image/pjpeg', 'image/jpeg', 'image/gif');
	$mime = ($uploadFile && isset($uploadFile['type'])) ? strtolower($uploadFile['type']) : '';
	if($uploadFile && !in_array($mime, $allowedMimes, true))
	{
		$info = @getimagesize($tempName);
		if(is_array($info) && !empty($info['mime']))
			$mime = strtolower($info['mime']);
	}
	if($mime === 'image/x-png')
		$mime = 'image/png';
	else if($mime === 'image/jpg' || $mime === 'image/pjpeg')
		$mime = 'image/jpeg';
	$validPicture = $uploadFile
		&& in_array($mime, array('image/png', 'image/jpeg', 'image/gif'), true)
		&& $uploadFile['size'] <= $bm_prefs['max_userpicture_size'];

	echo '<script>' . "\n";
	echo '<!--' . "\n";
	echo '(function(){' . "\n";
	echo 'var doc = (parent.overlayDocument && parent.overlayDocument().document) ? parent.overlayDocument().document : parent.document;' . "\n";

	if($validPicture)
	{
		if($_REQUEST['id'] == -1)
		{
			$pictureURL = SessionUrl('organizer.addressbook.php?action=addressbookPicture&id=-1&contentType=' . urlencode($mime) . '&tempID=' . $tempID);
			echo 'if(doc.getElementById(\'pictureFile\')) doc.getElementById(\'pictureFile\').value = ' . (int)$tempID . ';' . "\n";
			echo 'if(doc.getElementById(\'pictureMime\')) doc.getElementById(\'pictureMime\').value = \'' . addslashes($mime) . '\';' . "\n";
		}
		else
		{
			if($book->ChangePicture((int)$_REQUEST['id'], $tempName, $mime))
				$pictureURL = SessionUrl('organizer.addressbook.php?action=addressbookPicture&id=' . (int)$_REQUEST['id'] . '&t=' . time());
		}

		if(isset($pictureURL))
		{
			echo 'var pic = doc.getElementById(\'pictureDiv\');' . "\n";
			echo 'if(pic) {' . "\n";
			echo 'pic.style.backgroundImage = \'url(' . addslashes($pictureURL) . ')\';' . "\n";
			echo 'pic.className = pic.className.replace(\'bm-organizer-contact-avatar--empty\', \'\');' . "\n";
			echo 'pic.innerHTML = \'\';' . "\n";
			echo '}' . "\n";
		}
	}
	else
	{
		ReleaseTempFile($userRow['id'], $tempID);
		echo 'alert(\'' . addslashes(sprintf($lang_user['invalidpicture'], round($bm_prefs['max_userpicture_size']/1024, 2))) . '\');' . "\n";
	}

	echo 'if(parent.hideOverlay) parent.hideOverlay();' . "\n";
	echo '})();' . "\n";
	echo '//-->' . "\n";
	echo '</script>' . "\n";
}

/**
 * whole addressbook export
 */
else if($_REQUEST['action'] == 'exportAddressbook'
		&& isset($_REQUEST['lineBreakChar'])
		&& isset($_REQUEST['sepChar'])
		&& isset($_REQUEST['quoteChar']))
{
	// line break char
	if($_REQUEST['lineBreakChar'] == 'lf')
		$lineBreakChar = "\n";
	else if($_REQUEST['lineBreakChar'] == 'cr')
		$lineBreakChar =  "\r";
	else
		$lineBreakChar = "\r\n";

	// seperator char
	if($_REQUEST['sepChar'] == 'semicolon')
		$sepChar = ';';
	else if($_REQUEST['sepChar'] == 'comma')
		$sepChar = ',';
	else
		$sepChar = "\t";

	// quote char
	if($_REQUEST['quoteChar'] == 'single')
		$quoteChar = '\'';
	else
		$quoteChar = '"';

	// export
	header('Pragma: public');
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="export.csv"');
	$book->ExportAddressbook($lineBreakChar, $quoteChar, $sepChar, $currentAddressbookID);
	exit();
}

/**
 * import dialog
 */
else if($_REQUEST['action'] == 'importDialog'
		&& isset($_REQUEST['type'])
		&& isset($_REQUEST['encoding']))
{
	$importBook = isset($_REQUEST['importBook']) ? (int)$_REQUEST['importBook'] : $currentAddressbookID;
	$tpl->assign('title', $lang_user['import']);
	$tpl->assign('text', $lang_user['addrimporttext']);
	$tpl->assign('formAction', SessionUrl('organizer.addressbook.php?action=importDialogSubmit&type=' . urlencode($_REQUEST['type']) . '&encoding=' . urlencode($_REQUEST['encoding']) . '&importBook=' . $importBook));
	$tpl->assign('fieldName', 'importFile');
	$tpl->display('li/dialog.openfile.tpl');
}

/**
 * import dialog submit
 */
else if($_REQUEST['action'] == 'importDialogSubmit'
		&& isset($_REQUEST['type'])
		&& IsPOSTRequest())
{
	$tempID = RequestTempFile($userRow['id'], time() + TIME_ONE_HOUR);
	$tempName = TempFileName($tempID);
	$uploadFile = getUploadedFile('importFile', $tempName);
	$encoding = addslashes($_REQUEST['encoding']);
	$fileOK = true;

	if($uploadFile)
	{
		$fp = fopen($tempName, 'rb');
		$firstLine = fgets($fp);
		fclose($fp);

		// check for bom
		if(strlen($firstLine) > 3 && substr($firstLine, 0, 3) == "\xEF\xBB\xBF")
			$encoding = 'UTF-8';
		else if(strlen($firstLine) > 4 && substr($firstLine, 0, 4) == "\x00\x00\xFE\xFF")
			$encoding = 'UTF-32BE';
		else if(strlen($firstLine) > 4 && substr($firstLine, 0, 4) == "\xFF\xFE\x00\x00")
			$encoding = 'UTF-32LE';
		else if(strlen($firstLine) > 2 && substr($firstLine, 0, 2) == "\xFE\xFF")
			$encoding = 'UTF-16BE';
		else if(strlen($firstLine) > 2 && substr($firstLine, 0, 2) == "\xFF\xFE")
			$encoding = 'UTF-16LE';

		if(strpos($firstLine, ',') === false
			&& strpos($firstLine, ';') === false)
			$fileOK = false;
	}
	else
		$fileOK = false;

	echo '<script>' . "\n";
	echo '<!--' . "\n";

	if($fileOK)
	{
		echo 'parent.document.location.href = \'organizer.addressbook.php?action=importFile&type=' . addslashes($_REQUEST['type']) . '&encoding=' . $encoding . '&tempID=' . $tempID . '&importBook=' . (isset($_REQUEST['importBook']) ? (int)$_REQUEST['importBook'] : (int)$currentAddressbookID) . '\';';
	}
	else
	{
		ReleaseTempFile($userRow['id'], $tempID);
		echo 'alert(\'' . addslashes($lang_user['invalidformat']) . '\');' . "\n";
	}

	echo 'parent.hideOverlay();' . "\n";
	echo '//-->' . "\n";
	echo '</script>' . "\n";
}

/**
 * file import
 */
else if($_REQUEST['action'] == 'importFile'
		&& isset($_REQUEST['type'])
		&& isset($_REQUEST['tempID'])
		&& ValidTempFile($userRow['id'], (int)$_REQUEST['tempID']))
{
	$tpl->assign('pageTitle', $lang_user['import']);

	$tempID = (int)$_REQUEST['tempID'];
	$tempName = TempFileName($tempID);

	if($_REQUEST['type'] == 'csv')
	{
		// csv
		$fp = @fopen($tempName, 'r');
		if($fp)
		{
			if(!isset($_REQUEST['encoding']))
				$encoding = FALLBACK_CHARSET;
			else
				$encoding = $_REQUEST['encoding'];

			$csvReader = _new('CSVReader', array($fp, $encoding));
			$fields = $csvReader->Fields();
			$importBookID = $book->ResolveAddressbookID(isset($_REQUEST['importBook']) ? $_REQUEST['importBook'] : $currentAddressbookID);
			$autoDetect = array();
			$groupList = $book->GetGroupList(0, $importBookID);

			foreach($fields as $field)
				if(isset($bookFieldsAssoc[strtolower($field)]))
					$autoDetect[$field] = $bookFieldsAssoc[strtolower($field)];

			$tpl->assign('datasetCount', max(count($csvReader->_data)-1, 0));
			$tpl->assign('fileFields', $fields);
			$tpl->assign('bookFields', $bookFields);
			$tpl->assign('autoDetect', $autoDetect);
			$tpl->assign('groups', $groupList);
			$tpl->assign('tempID', $tempID);
			$tpl->assign('encoding', $encoding);
			$tpl->assign('importBookID', $importBookID);
			$tpl->assign('pageContent', 'li/organizer.addressbook.importcsv.tpl');
			$tpl->display('li/index.tpl');

			fclose($fp);
		}
	}
}

/**
 * csv import
 */
else if($_REQUEST['action'] == 'importCSV'
		&& isset($_REQUEST['tempID'])
		&& isset($_REQUEST['fields'])
		&& is_array($_REQUEST['fields'])
		&& ValidTempFile($userRow['id'], (int)$_REQUEST['tempID']))
{
	$tempID = (int)$_REQUEST['tempID'];
	$tempName = TempFileName($tempID);
	$importedDatasets = 0;
	$fieldsAssoc = $_REQUEST['fields'];
	$ignoreExisting = $_REQUEST['existing'] != 'update';
	$importBookID = $book->ResolveAddressbookID(isset($_POST['importBook']) ? $_POST['importBook'] : (isset($_REQUEST['importBook']) ? $_REQUEST['importBook'] : $currentAddressbookID));

	// process groups
	$groups = array();
	foreach($_POST as $key=>$val)
		if(substr($key, 0, 6) == 'group_' && $key !== 'group_new')
			$groups[] = (int)substr($key, 6);

	// add new group?
	if(isset($_POST['group_new']))
	{
		$groupName = trim($_POST['group_new_name']);
		if(!empty($groupName))
		{
			$groups[] = $book->GroupAdd($groupName, '', '', $importBookID);
		}
	}

	$fp = @fopen($tempName, 'r');
	if($fp)
	{
		if(!isset($_REQUEST['encoding']))
			$encoding = FALLBACK_CHARSET;
		else
			$encoding = $_REQUEST['encoding'];

		$csvReader = _new('CSVReader', array($fp, $encoding));
		$fields = $csvReader->Fields();

		while($row = $csvReader->FetchRow())
		{
			// prepare row
			$addressRow = array();
			foreach($fieldsAssoc as $fileField=>$bookField)
				if(isset($bookFields[$bookField]) && isset($row[$fileField]))
					$addressRow[$bookField] = $row[$fileField];

			// complete missing fields
			foreach($bookFields as $bookField=>$langTitle)
				if(!isset($addressRow[$bookField]))
					$addressRow[$bookField] = '';

			// get ID of existing row, if available
			$existingID = $ignoreExisting ? 0 : $book->FindAddress($addressRow['vorname'], $addressRow['nachname'], $importBookID);

			// add/update dataset
			if($existingID == 0)
			{
				// add
				$book->AddContact($addressRow['firma'],
								$addressRow['vorname'],
								$addressRow['nachname'],
								$addressRow['strassenr'],
								$addressRow['plz'],
								$addressRow['ort'],
								$addressRow['land'],
								$addressRow['tel'],
								$addressRow['fax'],
								$addressRow['handy'],
								$addressRow['email'],
								$addressRow['work_strassenr'],
								$addressRow['work_plz'],
								$addressRow['work_ort'],
								$addressRow['work_land'],
								$addressRow['work_tel'],
								$addressRow['work_fax'],
								$addressRow['work_handy'],
								$addressRow['work_email'],
								$addressRow['anrede'],
								$addressRow['position'],
								$addressRow['web'],
								$addressRow['kommentar'],
								max(@strtotime($addressRow['geburtsdatum']), 0),
								ADDRESS_PRIVATE,
								$groups,
								false, false, '', '', $importBookID);
			}
			else
			{
				// update
				$book->Change($existingID,
								$addressRow['firma'],
								$addressRow['vorname'],
								$addressRow['nachname'],
								$addressRow['strassenr'],
								$addressRow['plz'],
								$addressRow['ort'],
								$addressRow['land'],
								$addressRow['tel'],
								$addressRow['fax'],
								$addressRow['handy'],
								$addressRow['email'],
								$addressRow['work_strassenr'],
								$addressRow['work_plz'],
								$addressRow['work_ort'],
								$addressRow['work_land'],
								$addressRow['work_tel'],
								$addressRow['work_fax'],
								$addressRow['work_handy'],
								$addressRow['work_email'],
								$addressRow['anrede'],
								$addressRow['position'],
								$addressRow['web'],
								$addressRow['kommentar'],
								max(@strtotime($addressRow['geburtsdatum']), 0),
								ADDRESS_PRIVATE,
								false,
								$importBookID);
			}

			// inc
			$importedDatasets++;
		}

		fclose($fp);
	}

	// show result
	$tpl->assign('pageTitle', $lang_user['import']);
	$tpl->assign('title', $lang_user['import']);
	$tpl->assign('msg', sprintf($lang_user['importdone'], $importedDatasets));
	$tpl->assign('backLink', 'organizer.addressbook.php');
	$tpl->assign('pageContent', 'li/msg.tpl');
	$tpl->display('li/index.tpl');

	// release temp file
	ReleaseTempFile($userRow['id'], $tempID);
}

/**
 * address popup
 */
else if($_REQUEST['action'] == 'addressPopup')
{
	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'email';

	$addresses = array();

	// addressbook
	$addressBook = $book->GetAddressBook('*', -1, 'nachname', 'desc', false, -1);
	foreach($addressBook as $id=>$entry)
	{
		$addresses[] = array('name' 			=> $entry['nachname'] . ', ' . $entry['vorname'],
								'email1' 		=> $entry['default_address'] == ADDRESS_WORK ? $entry['work_email'] : $entry['email'],
								'email2'		=> $entry['default_address'] == ADDRESS_WORK ? $entry['email'] : $entry['work_email'],
								'handy' 		=> $entry['handy'],
								'type'			=> '');
	}
	$groups = $book->GetGroupList(0, -1);
	foreach($groups as $group)
	{
		if($group['members'] == 0) continue;

		$addresses[] = array('name'				=> $group['title'],
								'email1'		=> sprintf('%d@contact.groups', $group['id']),
								'email2'		=> '',
								'handy'			=> '',
								'type'			=> '');
	}

	// given addresses
	$givenAddresses = array();
	if($mode != 'handy')
	{
		$givenAddresses['to'] = isset($_REQUEST['to'])
									? ParseMailList(_unescape($_REQUEST['to']))
									: array();
		$givenAddresses['cc'] = isset($_REQUEST['cc'])
									? ParseMailList(_unescape($_REQUEST['cc']))
									: array();
		$givenAddresses['bcc'] = isset($_REQUEST['bcc'])
									? ParseMailList(_unescape($_REQUEST['bcc']))
									: array();
		foreach($givenAddresses as $type=>$addressList)
			foreach($addressList as $address)
				$addresses[] = array('name'			=> $address['name'],
										'email1'	=> $address['mail'],
										'type'		=> $type);
	}

	$tpl->assign('mode', $mode);
	$tpl->assign('addresses', $addresses);
	$tpl->display('li/organizer.addressbook.popup.tpl');
}

/**
 * attendee popup
 */
else if($_REQUEST['action'] == 'attendeePopup')
{
	$addresses = array();

	// addressbook
	$addressBook = $book->GetAddressBook('*', -1, 'nachname', 'asc');
	foreach($addressBook as $id=>$entry)
	{
		$addresses[] = array('firstname' 		=> $entry['vorname'],
								'lastname'		=> $entry['nachname'],
								'type'			=> '',
								'id'			=> $entry['id']);
	}

	// given addresses
	$attendees = array();
	if(trim($_REQUEST['attendeeList']) != '')
	{
		$attendeeList = explode(';', _unescape($_REQUEST['attendeeList']));
		foreach($attendeeList as $attendeeItem)
		{
			if(trim($attendeeItem) != '')
			{
				list($attendeeID, $attendeeLastName, $attendeeFirstName) = explode(',', $attendeeItem);
				$addresses[] = array('firstname' 		=> $attendeeFirstName,
										'lastname'		=> $attendeeLastName,
										'type'			=> 'att',
										'id'			=> $attendeeID);
			}
		}
	}

	$tpl->assign('addresses', $addresses);
	$tpl->display('li/organizer.addressbook.popup.attendees.tpl');
}

/**
 * number popup
 */
else if($_REQUEST['action'] == 'numberPopup')
{
	$addresses = array();

	// addressbook
	$addressBook = $book->GetAddressBook('*', -1, 'nachname', 'asc');
	foreach($addressBook as $id=>$entry)
	{
		if(trim($entry['handy']) != '' || trim($entry['work_handy']) != '')
			$addresses[] = array('firstname' 		=> $entry['vorname'],
									'lastname'		=> $entry['nachname'],
									'handy'			=> $entry['handy'],
									'work_handy'	=> $entry['work_handy'],
									'id'			=> $entry['id']);
	}

	$tpl->assign('addresses', $addresses);
	$tpl->display('li/organizer.addressbook.popup.numbers.tpl');
}

/**
 * address lookup RPC
 */
else if($_REQUEST['action'] == 'lookupAddresses'
		&& isset($_REQUEST['text']))
{
	$text = trim(_unescape($_REQUEST['text']));
	if($text !=  '')
	{
		$addresses = $book->Lookup($text);
		echo implode(';', $addresses);
	}
}
?>