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

/**
 * b1gMail base search provider plugin
 *
 */
class B1GMailSearchProvider extends BMPlugin
{
	/**
	 * constructor
	 *
	 * @return B1GMail_SearchProvider
	 */
	function __construct()
	{
		global $lang_admin;

		// plugin info
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'b1gMail Search Provider';
		$this->author				= 'b1gMail Project';
		$this->version				= '1.23';
		$this->designedfor			= '7.5.0';
		$this->website				= 'https://www.b1gmail.org/';
		$this->update_url			= '';

		// admin pages
		$this->admin_pages			= true;
		$this->admin_page_title		= $lang_admin['searchprovider'];
		$this->admin_page_icon		= 'search32.png';
		$this->admin_route_slug		= 'searchprovider';
	}

	/**
	 * handle search mass action
	 *
	 * @param string $category Category name
	 * @param string $action Action name
	 * @param array $items Array with item IDs
	 * @return bool Handled?
	 */
	function HandleSearchMassAction($category, $action, $items)
	{
		global $thisUser, $userRow;

		if(substr($category, 0, 22) != 'B1GMailSearchProvider_')
			return(false);

		if($category == 'B1GMailSearchProvider_mails')
		{
			if(!class_exists('BMMailbox'))
				include(B1GMAIL_DIR . 'serverlib/mailbox.class.php');
			$mailbox = _new('BMMailbox', array($userRow['id'], $userRow['email'], $thisUser));

			if($action == 'delete')
			{
				foreach($items as $mailID)
					$mailbox->DeleteMail((int)$mailID);
			}
			else if($action == 'markread')
			{
				foreach($items as $mailID)
					$mailbox->FlagMail(FLAG_UNREAD, false, (int)$mailID);
			}
			else if($action == 'markunread')
			{
				foreach($items as $mailID)
					$mailbox->FlagMail(FLAG_UNREAD, true, (int)$mailID);
			}
			else if(substr($action, 0, 7) == 'moveto_')
			{
				$destFolderID = (int)substr($action, 7);
				$mailbox->MoveMail($items, $destFolderID);
			}
		}

		else if($category == 'B1GMailSearchProvider_sms')
		{
			if(!class_exists('BMSMS'))
				include(B1GMAIL_DIR . 'serverlib/sms.class.php');
			$sms = _new('BMSMS', array($userRow['id'], &$thisUser));

			if($action == 'delete')
			{
				foreach($items as $itemID)
					$sms->DeleteOutboxEntry((int)$itemID);
			}
		}

		else if($category == 'B1GMailSearchProvider_calendar')
		{
			if(!class_exists('BMCalendar'))
				include(B1GMAIL_DIR . 'serverlib/calendar.class.php');
			$calendar = _new('BMCalendar', array($userRow['id']));

			if($action == 'delete')
			{
				foreach($items as $itemID)
					$calendar->DeleteDate((int)$itemID);
			}
		}

		else if($category == 'B1GMailSearchProvider_tasks')
		{
			if(!class_exists('BMTodo'))
				include(B1GMAIL_DIR . 'serverlib/todo.class.php');
			$calendar = _new('BMTodo', array($userRow['id']));

			if($action == 'delete')
			{
				foreach($items as $itemID)
					$calendar->Delete((int)$itemID);
			}
		}

		else if($category == 'B1GMailSearchProvider_addressbook')
		{
			if(!class_exists('BMAddressbook'))
				include(B1GMAIL_DIR . 'serverlib/addressbook.class.php');
			$book = _new('BMAddressbook', array($userRow['id']));

			if($action == 'delete')
			{
				foreach($items as $itemID)
					$book->Delete((int)$itemID);
			}
			else if($action == 'compose')
			{
				$to = array();

				foreach($items as $itemID)
				{
					$contact = $book->GetContact($itemID);
					$email = $contact['default_address'] == ADDRESS_WORK
						? $contact['work_email']
						: $contact['email'];

					if(trim($email) != '')
						array_push($to, sprintf('"%s, %s" <%s>',
							$contact['nachname'],
							$contact['vorname'],
							$email));
				}

				$toList = urlencode(implode(', ', $to));
				SessionRedirect('email.compose.php?to=' . $toList);
				exit();
			}
		}

		else if($category == 'B1GMailSearchProvider_notes')
		{
			if(!class_exists('BMNotes'))
				include(B1GMAIL_DIR . 'serverlib/notes.class.php');
			$notes = _new('BMNotes', array($userRow['id']));

			if($action == 'delete')
			{
				foreach($items as $itemID)
					$notes->Delete((int)$itemID);
			}
		}

		else if($category == 'B1GMailSearchProvider_webdisk')
		{
			if(!class_exists('BMWebdisk'))
				include(B1GMAIL_DIR . 'serverlib/webdisk.class.php');
			$webdisk = _new('BMWebdisk', array($userRow['id']));

			if($action == 'delete')
			{
				foreach($items as $itemID)
				{
					if(strpos($itemID, '_') === false)
						continue;
					[$itemType, $id] = explode('_', $itemID);

					if($itemType == 'file')
						$webdisk->DeleteFile((int)$id);
					else if($itemType == 'folder')
						$webdisk->DeleteFolder((int)$id);
				}
			}
		}
	}

	/**
	 * get implemented search categories
	 *
	 * @return array
	 */
	function GetSearchCategories()
	{
		global $bm_prefs, $lang_user;

		// prefs
		$searchIn = @unserialize($bm_prefs['search_in']);
		if(!is_array($searchIn))
			$searchIn = array();

		// build result
		$result = array();
		if(isset($searchIn['mails']))
			$result['B1GMailSearchProvider_mails'] = array('title' => $lang_user['mails'], 'icon' => 'fa-envelope-o');
		if(isset($searchIn['attachments']))
			$result['B1GMailSearchProvider_attachments'] = array('title' => $lang_user['attachments'], 'icon' => 'fa-paperclip');
		if(isset($searchIn['sms']))
			$result['B1GMailSearchProvider_sms'] = array('title' => $lang_user['smsoutbox'], 'icon' => 'fa-commenting-o');
		if(isset($searchIn['calendar']))
			$result['B1GMailSearchProvider_calendar'] = array('title' => $lang_user['dates2'], 'icon' => 'fa-calendar');
		if(isset($searchIn['tasks']))
			$result['B1GMailSearchProvider_tasks'] = array('title' => $lang_user['tasks'], 'icon' => 'fa-tasks');
		if(isset($searchIn['addressbook']))
			$result['B1GMailSearchProvider_addressbook'] = array('title' => $lang_user['contacts'], 'icon' => 'fa-address-book-o');
		if(isset($searchIn['notes']))
			$result['B1GMailSearchProvider_notes'] = array('title' => $lang_user['notes'], 'icon' => 'fa-sticky-note-o');
		if(isset($searchIn['webdisk']))
			$result['B1GMailSearchProvider_webdisk'] = array('title' => $lang_user['webdisk'], 'icon' => 'fa-folder-open-o');
		if($bm_prefs['search_engine'] != '')
			$result['B1GMailSearchProvider_websearch'] = array('title' => $lang_user['websearch'], 'icon' => 'fa-globe');

		// return result
		return($result);
	}

	/**
	 * perform search
	 *
	 * @param string $query Query
	 * @return array Results
	 */
	function OnSearch($query, $dateFrom = 0, $dateTo = 0)
	{
		global $bm_prefs, $userRow, $groupRow, $thisUser, $lang_user, $db;

		// prepare
		$results = array();
		$q = '\'%' . $db->Escape($query) . '%\'';

		// prefs
		$searchIn = @unserialize($bm_prefs['search_in']);
		if(!is_array($searchIn))
			$searchIn = array();

		// date
		if($dateTo == 0)
			$dateTo = time()+TIME_ONE_MINUTE;

		//
		// mails
		//
		if(isset($searchIn['mails']) && ($groupRow['ftsearch'] == 'no' || !FTS_SUPPORT))
		{
			if(!class_exists('BMMailbox'))
				include(B1GMAIL_DIR . 'serverlib/mailbox.class.php');
			$mailbox = _new('BMMailbox', array($userRow['id'], $userRow['email'], $thisUser));

			$folderIcons = array(
				FOLDER_INBOX		=> 'fa-inbox',
				FOLDER_OUTBOX		=> 'fa-outbox',
				FOLDER_DRAFTS		=> 'fa-envelope',
				FOLDER_SPAM			=> 'fa-ban',
				FOLDER_TRASH		=> 'fa-trash-o'
			);

			$thisResults = array();
			$res = $db->Query('SELECT id,betreff,fetched,size,folder,flags FROM {pre}mails WHERE fetched>=? AND fetched<=? AND userid=? AND (betreff LIKE ' . $q . ' OR von LIKE ' . $q . ' OR an LIKE ' . $q . ')',
				$dateFrom,
				$dateTo,
				$thisUser->_id);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$thisResults[] = array(
					'title'		=> $row['betreff'],
					'link'		=> SessionUrl(sprintf('email.read.php?id=%d', $row['id'])),
					'date'		=> $row['fetched'],
					'size'		=> $row['size'],
					'id'		=> $row['id'],
					'icon'		=> $folderIcons[$row['folder']] ?? 'fa-folder-o',
					'bold'		=> ($row['flags'] & FLAG_UNREAD) != 0,
					'strike'	=> ($row['flags'] & FLAG_DELETED) != 0 || ($row['folder'] == FOLDER_TRASH)
				);
			$res->Free();

			$massActions = array(
				$lang_user['actions'] 	=> array(
					'markread'		=> $lang_user['markread'],
					'markunread'	=> $lang_user['markunread'],
					'delete'		=> $lang_user['delete']
				),
				$lang_user['move']		=> array()
			);
			$folders = $mailbox->GetFolderList(false);
			foreach($folders as $folderID=>$folder)
				$massActions[$lang_user['move']]['moveto_'.$folderID] = $lang_user['moveto'] . ' &quot;' . HTMLFormat($folder['title']) . '&quot;';

			if(count($thisResults) > 0)
				$results[] = array(
					'icon'			=> 'fa-envelope-o',
					'name'			=> 'B1GMailSearchProvider_mails',
					'title'			=> $lang_user['mails'],
					'results'		=> $thisResults,
					'massActions'	=> $massActions
				);
		}
		else if(isset($searchIn['mails']) && $groupRow['ftsearch'] == 'yes' && FTS_SUPPORT)
		{
			if(!class_exists('BMSearchIndex'))
				include(B1GMAIL_DIR . 'serverlib/searchindex.class.php');
			if(!class_exists('BMMailbox'))
				include(B1GMAIL_DIR . 'serverlib/mailbox.class.php');

			$mailbox = _new('BMMailbox', array($userRow['id'], $userRow['email'], $thisUser));
			$idx = _new('BMSearchIndex', array($thisUser->_id));

			$items = $idx->search($query);

			$mailIDs = array();
			foreach($items as $key=>$item)
				$mailIDs[$item['itemID']] = $key;

			$thisResults = array();
			if(count($mailIDs) > 0)
			{
				$res = $db->Query('SELECT `id`,`betreff`,`fetched`,`size`,`folder`,`flags` FROM {pre}mails WHERE `fetched`>=? AND `fetched`<=? AND `userid`=? AND `id` IN ?',
					$dateFrom,
					$dateTo,
					$thisUser->_id,
					array_keys($mailIDs));
				while($row = $res->FetchArray(MYSQLI_ASSOC))
				{
					$searchText 	= $items[ $mailIDs[$row['id']] ]['text'];
					$excerpt 		= $idx->createExcerpt($query, $searchText);
					$score 			= round($idx->computeScore($query, $searchText)*100, 0);

					$thisResults[] = array(
						'title'		=> $row['betreff'],
						'link'		=> SessionUrl(sprintf('email.read.php?id=%d', $row['id'])),
						'date'		=> $row['fetched'],
						'size'		=> $row['size'],
						'id'		=> $row['id'],
						'icon'		=> $folderIcons[$row['folder']] ?? 'fa-folder-o',
						'bold'		=> ($row['flags'] & FLAG_UNREAD) != 0,
						'strike'	=> ($row['flags'] & FLAG_DELETED) != 0 || ($row['folder'] == FOLDER_TRASH),
						'excerpt'	=> $excerpt,
						'score'		=> $score
					);
				}
				$res->Free();
			}

			if(count($thisResults) > 0)
			{
				$massActions = array(
					$lang_user['actions'] 	=> array(
						'markread'		=> $lang_user['markread'],
						'markunread'	=> $lang_user['markunread'],
						'delete'		=> $lang_user['delete']
					),
					$lang_user['move']		=> array()
				);
				$folders = $mailbox->GetFolderList(false);
				foreach($folders as $folderID=>$folder)
					$massActions[$lang_user['move']]['moveto_'.$folderID] = $lang_user['moveto'] . ' &quot;' . HTMLFormat($folder['title']) . '&quot;';

				$results[] = array(
					'icon'			=> 'fa-envelope-o',
					'name'			=> 'B1GMailSearchProvider_mails',
					'title'			=> $lang_user['mails'],
					'results'		=> $thisResults,
					'massActions'	=> $massActions
				);
			}
		}
		//
		// attachments
		//
		if(isset($searchIn['attachments']))
		{
			$thisResults = array();
			$res = $db->Query('SELECT `filename`,`size`,`mailid` FROM {pre}attachments WHERE `userid`=? AND (`filename` LIKE ' . $q . ') ORDER BY `filename` ASC',
				$thisUser->_id);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$res2 = $db->Query('SELECT fetched FROM {pre}mails WHERE id=? AND userid=?',
					$row['mailid'],
					$thisUser->_id);
				if($res2->RowCount() != 1)
					continue;
				[$fetched] = $res2->FetchArray(MYSQLI_NUM);
				$res2->Free();

				if($fetched < $dateFrom || $fetched > $dateTo)
					continue;

				$thisResults[] = array(
					'title'		=> $row['filename'],
					'link'		=> SessionUrl(sprintf('email.read.php?id=%d', $row['mailid'])),
					'date'		=> $fetched,
					'size'		=> $row['size']
				);
			}
			$res->Free();

			if(count($thisResults) > 0)
				$results[] = array(
					'icon'			=> 'fa-paperclip',
					'name'			=> 'B1GMailSearchProvider_attachments',
					'title'			=> $lang_user['attachments'],
					'results'		=> $thisResults
				);
		}
		//
		// sms outbox
		//
		if(isset($searchIn['sms']))
		{
			$thisResults = array();
			$res = $db->Query('SELECT id,text,`date` FROM {pre}smsend WHERE `date`>=? AND `date`<=? AND isSMS=1 AND user=? AND deleted=0 AND (text LIKE ' . $q . ' OR `from` LIKE ' . $q . ' OR `to` LIKE ' . $q . ') ORDER BY text ASC',
				$dateFrom,
				$dateTo,
				$thisUser->_id);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$thisResults[] = array(
					'title'		=> $row['text'],
					'link'		=> SessionUrl(sprintf('sms.php?action=outbox&show=%d', $row['id'])),
					'date'		=> $row['date'],
					'id'		=> $row['id']
				);
			$res->Free();

			if(count($thisResults) > 0)
				$results[] = array(
					'icon'			=> 'fa-commenting-o',
					'name'			=> 'B1GMailSearchProvider_sms',
					'title'			=> $lang_user['smsoutbox'],
					'results'		=> $thisResults,
					'massActions'	=> array(
						'delete'	=> $lang_user['delete']
					)
				);
		}

		//
		// calendar
		//
		if(isset($searchIn['calendar']))
		{
			$thisResults = array();
			$res = $db->Query('SELECT id,title,`startdate` FROM {pre}dates WHERE `startdate`>=? AND `startdate`<=? AND user=? AND (title LIKE ' . $q . ' OR text LIKE ' . $q . ') ORDER BY title ASC',
				$dateFrom,
				$dateTo,
				$thisUser->_id);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$thisResults[] = array(
					'title'		=> $row['title'],
					'link'		=> SessionUrl(sprintf('organizer.calendar.php?action=editDate&id=%d', $row['id'])),
					'date'		=> $row['startdate'],
					'id'		=> $row['id']
				);
			$res->Free();

			if(count($thisResults) > 0)
				$results[] = array(
					'icon'			=> 'fa-calendar',
					'name'			=> 'B1GMailSearchProvider_calendar',
					'title'			=> $lang_user['dates2'],
					'results'		=> $thisResults,
					'massActions'	=> array(
						'delete'	=> $lang_user['delete']
					)
				);
		}

		//
		// tasks
		//
		if(isset($searchIn['tasks']))
		{
			$thisResults = array();
			$res = $db->Query('SELECT id,titel,faellig FROM {pre}tasks WHERE faellig>=? AND faellig<=? AND user=? AND (titel LIKE ' . $q . ' OR comments LIKE ' . $q . ') ORDER BY titel ASC',
				$dateFrom,
				$dateTo,
				$thisUser->_id);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$thisResults[] = array(
					'title'		=> $row['titel'],
					'link'		=> SessionUrl(sprintf('organizer.todo.php?action=editTask&id=%d', $row['id'])),
					'date'		=> $row['faellig'],
					'id'		=> $row['id']
				);
			$res->Free();

			if(count($thisResults) > 0)
				$results[] = array(
					'icon'			=> 'fa-tasks',
					'name'			=> 'B1GMailSearchProvider_tasks',
					'title'			=> $lang_user['tasks'],
					'results'		=> $thisResults,
					'massActions'	=> array(
						'delete'	=> $lang_user['delete']
					)
				);
		}

		//
		// addressbook
		//
		if(isset($searchIn['addressbook']))
		{
			$thisResults = array();
			$res = $db->Query('SELECT id,vorname,nachname,firma FROM {pre}adressen WHERE user=? AND (CONCAT(vorname,\' \',nachname,\' \',firma) LIKE ' . $q . ' OR CONCAT(nachname,\', \',vorname,\' \',firma) LIKE ' . $q . ') ORDER BY nachname,vorname ASC',
				$thisUser->_id);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$addrTitle = '';

				if(empty($row['vorname']) && empty($row['nachname']))
					$addrTitle = $row['firma'];
				else
					$addrTitle = $row['nachname'] . ', ' . $row['vorname'];

				$thisResults[] = array(
					'title'		=> $addrTitle,
					'link'		=> SessionUrl(sprintf('organizer.addressbook.php?action=editContact&id=%d', $row['id'])),
					'id'		=> $row['id']
				);
			}
			$res->Free();

			if(count($thisResults) > 0)
				$results[] = array(
					'icon'			=> 'fa-address-book-o',
					'name'			=> 'B1GMailSearchProvider_addressbook',
					'title'			=> $lang_user['contacts'],
					'results'		=> $thisResults,
					'massActions'	=> array(
						'compose'	=> $lang_user['sendmail'],
						'delete'	=> $lang_user['delete']
					)
				);
		}

		//
		// notes
		//
		if(isset($searchIn['notes']))
		{
			$thisResults = array();
			$res = $db->Query('SELECT id,text,`date` FROM {pre}notes WHERE `date`>=? AND `date`<=? AND user=? AND text LIKE ' . $q . ' ORDER BY text ASC',
				$dateFrom,
				$dateTo,
				$thisUser->_id);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$thisResults[] = array(
					'title'		=> $row['text'],
					'link'		=> SessionUrl(sprintf('organizer.notes.php?show=%d', $row['id'])),
					'date'		=> $row['date'],
					'id'		=> $row['id']
				);
			$res->Free();

			if(count($thisResults) > 0)
				$results[] = array(
					'icon'			=> 'fa-sticky-note-o',
					'name'			=> 'B1GMailSearchProvider_notes',
					'title'			=> $lang_user['notes'],
					'results'		=> $thisResults,
					'massActions'	=> array(
						'delete'	=> $lang_user['delete']
					)
				);
		}

		//
		// webdisk
		//
		if(isset($searchIn['webdisk']))
		{
			// files
			$thisResults = array();
			$res = $db->Query('SELECT id,ordner,dateiname,modified,`size` FROM {pre}diskfiles WHERE modified>=? AND modified<=? AND user=? AND dateiname LIKE ' . $q . ' ORDER BY dateiname ASC',
				$dateFrom,
				$dateTo,
				$thisUser->_id);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$thisResults[] = array(
					'icon'		=> 'fa-file-o',
					'title'		=> $row['dateiname'],
					'link'		=> SessionUrl(sprintf('webdisk.php?folder=%d', $row['ordner'])),
					'date'		=> $row['modified'],
					'size'		=> $row['size'],
					'id'		=> 'file_' . $row['id']
				);
			$res->Free();

			// folders
			$res = $db->Query('SELECT id,titel,modified FROM {pre}diskfolders WHERE modified>=? AND modified<=? AND user=? AND titel LIKE ' . $q . ' ORDER BY titel ASC',
				$dateFrom,
				$dateTo,
				$thisUser->_id);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$thisResults[] = array(
					'icon'		=> 'fa-folder-open-o',
					'title'		=> $row['titel'],
					'link'		=> SessionUrl(sprintf('webdisk.php?folder=%d', $row['id'])),
					'date'		=> $row['modified'],
					'id'		=> 'folder_' . $row['id']
				);
			$res->Free();

			if(count($thisResults) > 0)
				$results[] = array(
					'icon'			=> 'fa-folder-open-o',
					'name'			=> 'B1GMailSearchProvider_webdisk',
					'title'			=> $lang_user['webdisk'],
					'results'		=> $thisResults,
					'massActions'	=> array(
						'delete'	=> $lang_user['delete']
					)
				);
		}

		//
		// web
		//
		if(!empty($bm_prefs['search_engine']))
		{
			$results[] = array(
				'icon'		=> 'fa-globe',
				'name'		=> 'B1GMailSearchProvider_websearch',
				'title'		=> $lang_user['websearch'],
				'results'	=>  array(
					array(
						'title'		=> $query,
						'extLink'	=> DerefUrl(sprintf($bm_prefs['search_engine'], urlencode($query)))
					)
				)
			);
		}

		// return
		return($results);
	}

	/**
	 * Map pretty URLs and legacy query params to do=prefs.
	 */
	protected function _searchNormalizeRequest()
	{
		$do = $_REQUEST['do'] ?? $_REQUEST['action'] ?? '';

		if($do === '' || $do === 'prefs')
			$do = 'prefs';
		else if($do === 'save')
		{
			$_REQUEST['save'] = true;
			$do = 'prefs';
		}

		$_REQUEST['do'] = $do;
	}

	/**
	 * Pretty admin URL for this plugin.
	 *
	 * @param array<string, mixed> $params
	 * @param bool                 $trailingAmp
	 * @return string
	 */
	protected function _searchAdminUrl(array $params = array(), $trailingAmp = true)
	{
		$params = array_merge(array('plugin' => $this->internal_name, 'do' => 'prefs'), $params);

		if(function_exists('AdminSessionUrl'))
			return AdminSessionUrl('plugin.page.php', $params, $trailingAmp);

		$url = $this->_adminLink();
		unset($params['plugin']);
		foreach($params as $key => $val)
		{
			if((string)$val === '')
				continue;
			$url .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((string)$val);
		}
		if($trailingAmp)
			$url .= (strpos($url, '?') !== false ? '&' : '?');

		return $url;
	}

	/**
	 * admin handler
	 */
	function AdminHandler()
	{
		global $tpl, $lang_admin;

		$this->_searchNormalizeRequest();

		if(($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST')
			$this->_searchHandlePost();

		$do = $_REQUEST['do'] ?? 'prefs';

		$tabs = array(
			0 => array(
				'title'		=> $lang_admin['prefs'],
				'icon'		=> '../plugins/templates/images/search32.png',
				'link'		=> $this->_searchAdminUrl(array(), true),
				'active'	=> $do === 'prefs',
			),
		);

		$tpl->assign('tabs', $tabs);
		$tpl->assign('searchPlugin', $this->internal_name);
		$tpl->assign('pageURL', $this->_searchAdminUrl(array(), true));

		if($do === 'prefs')
			$this->_prefsPage();
	}

	/**
	 * Save prefs (POST + CSRF only).
	 */
	protected function _searchHandlePost()
	{
		global $db;

		if(!isset($_REQUEST['save']))
			return;

		if(isset($_REQUEST['searchIn']) && is_array($_REQUEST['searchIn']))
			$searchIn = $_REQUEST['searchIn'];
		else
			$searchIn = array();

		$db->Query('UPDATE {pre}prefs SET search_in=?',
			serialize($searchIn));
		ReadConfig();

		SessionRedirect($this->_searchAdminUrl(array(), false));
		exit();
	}

	/**
	 * admin prefs page
	 */
	function _prefsPage()
	{
		global $tpl, $bm_prefs;

		$searchIn = @unserialize($bm_prefs['search_in']);
		if(!is_array($searchIn))
			$searchIn = array();

		$tpl->assign('searchIn', $searchIn);
		$tpl->assign('page', $this->_templatePath('search.plugin.prefs.tpl'));
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('B1GMailSearchProvider');