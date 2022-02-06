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

include('../serverlib/admin.inc.php');
RequestPrivileges(PRIVILEGES_ADMIN);
AdminRequirePrivilege('prefs.faq');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'faq';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['faq'],
		'relIcon'	=> 'faq32.png',
		'link'		=> 'prefs.faq.php?',
		'active'	=> $_REQUEST['action'] == 'faq'
	)
);

/**
 * faq
 */
if($_REQUEST['action'] == 'faq')
{
	if(!isset($_REQUEST['do']))
		$_REQUEST['do'] = 'list';

	//
	// list
	//
	if($_REQUEST['do'] == 'list')
	{
		// add
		if(isset($_REQUEST['add']))
		{
			$db->Query('INSERT INTO {pre}faq(frage,typ,lang,required,antwort) VALUES(?,?,?,?,?)',
				HTMLFormat($_REQUEST['frage']),
				$_REQUEST['typ'],
				$_REQUEST['lang'],
				$_REQUEST['required'],
				$_REQUEST['antwort']);
		}

		// delete
		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}faq WHERE id=?',
				(int)$_REQUEST['delete']);
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get faq IDs
			$faqIDs = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 4) == 'faq_')
					$faqIDs[] = (int)substr($key, 4);

			if(count($faqIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					// delete row
					$db->Query('DELETE FROM {pre}faq WHERE id IN(' . implode(',', $faqIDs) . ')');
				}
			}
		}

		// fetch
		$languages = GetAvailableLanguages();
		$faqs = array();
		$res = $db->Query('SELECT id,typ,lang,frage,antwort,required FROM {pre}faq ORDER BY typ,lang,frage ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$faqs[$row['id']] = array(
				'id'		=> $row['id'],
				'typ'		=> $lang_admin[$row['typ']],
				'lang'		=> $row['lang'] == ':all:' ? $lang_admin['all'] : $languages[$row['lang']]['title'],
				'frage'		=> $row['frage'],
				'antwort'	=> $row['antwort'],
				'required'	=> $row['required']
			);
		$res->Free();

		// assign
		$tpl->assign('faqs', $faqs);
		$tpl->assign('requirements', $faqRequirementTable);
		$tpl->assign('languages', $languages);
		$tpl->assign('page', 'prefs.faq.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit')
	{
		// save?
		if(isset($_REQUEST['save']))
		{
			$db->Query('UPDATE {pre}faq SET frage=?,typ=?,lang=?,required=?,antwort=? WHERE id=?',
				HTMLFormat($_REQUEST['frage']),
				$_REQUEST['typ'],
				$_REQUEST['lang'],
				$_REQUEST['required'],
				$_REQUEST['antwort'],
				(int)$_REQUEST['id']);
			header('Location: prefs.faq.php?sid=' . session_id());
			exit();
		}

		// fetch
		$res = $db->Query('SELECT id,typ,lang,frage,antwort,required FROM {pre}faq WHERE id=?',
			(int)$_REQUEST['id']);
		assert('$res->RowCount() != 0');
		$faq = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// assign
		$languages = GetAvailableLanguages();
		$tpl->assign('faq', $faq);
		$tpl->assign('requirements', $faqRequirementTable);
		$tpl->assign('languages', $languages);
		$tpl->assign('page', 'prefs.faq.edit.tpl');
	}
}

$tpl->assign('usertpldir', B1GMAIL_REL . 'templates/' . $bm_prefs['template'] . '/');
$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['faq']);
$tpl->display('page.tpl');
