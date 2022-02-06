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

include('../serverlib/init.inc.php');
if(!class_exists('BMAddressbook'))
	include('../serverlib/addressbook.class.php');
RequestPrivileges(PRIVILEGES_USER | PRIVILEGES_MOBILE);

/**
 * addressbook interface
 */
$book = _new('BMAddressbook', array($userRow['id']));

/**
 * assign
 */
$tpl->assign('activeTab', 	'contacts');

/**
 * default action
 */
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'list';

/**
 * contact list
 */
if($_REQUEST['action'] == 'list')
{
	$addressList = $book->GetAddressBook('*',
						-1,
						'nachname',
						'ASC',
						true);

	$tpl->assign('list', $addressList);
	$tpl->assign('pageTitle', $lang_user['contacts']);
	$tpl->assign('page', 'm/contacts.list.tpl');
	$tpl->display('m/index.tpl');
}

/**
 * contact details
 */
else if($_REQUEST['action'] == 'show' && isset($_REQUEST['id']))
{
	$contact = $book->GetContact((int)$_REQUEST['id']);
	if($contact !== false)
	{
		$groupList = $book->GetGroupList((int)$_REQUEST['id']);

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

		if(empty($contact['vorname']) && empty($contact['nachname']))
			$contactName = $contact['firma'];
		else
			$contactName = $contact['vorname'] . ' ' . $contact['nachname'];

		$tpl->assign('groups', $groupList);
		$tpl->assign('contact', $contact);
		$tpl->assign('pageTitle', HTMLFormat($contactName));
		$tpl->assign('page', 'm/contacts.show.tpl');
		$tpl->display('m/index.tpl');
	}
}

/**
 * contact picture
 */
else if($_REQUEST['action'] == 'addressbookPicture' && isset($_REQUEST['id']))
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
?>