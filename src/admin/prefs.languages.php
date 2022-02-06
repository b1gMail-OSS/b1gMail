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
AdminRequirePrivilege('prefs.languages');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'languages';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['languages'],
		'relIcon'	=> 'lang32.png',
		'link'		=> 'prefs.languages.php?',
		'active'	=> $_REQUEST['action'] == 'languages'
	),
	1 => array(
		'title'		=> $lang_admin['customtexts'],
		'relIcon'	=> 'phrases32.png',
		'link'		=> 'prefs.languages.php?action=texts&',
		'active'	=> $_REQUEST['action'] == 'texts'
	)
);

/**
 * fields
 */
if($_REQUEST['action'] == 'languages')
{
	// add
	if(isset($_REQUEST['add']))
	{
		if(isset($_FILES['langfile'])
			&& $_FILES['langfile']['error'] == 0
			&& $_FILES['langfile']['size'] > 5
			&& strtolower(substr($_FILES['langfile']['name'], -9)) == '.lang.php')
		{
			$fileName = $_FILES['langfile']['name'];
			$newFileName = B1GMAIL_DIR . 'languages/' . $fileName;
			move_uploaded_file($_FILES['langfile']['tmp_name'], $newFileName);
			@chmod($newFileName, 0777);
		}
	}

	// delete
	if(isset($_REQUEST['delete']))
	{
		$langID = str_replace(array('/', '\\'), '', $_REQUEST['delete']);
		if(is_writeable(B1GMAIL_DIR . 'languages/' . $langID . '.lang.php'))
			unlink(B1GMAIL_DIR . 'languages/' . $langID . '.lang.php');
	}

	// mass action
	if(isset($_REQUEST['executeMassAction']))
	{
		// get country IDs
		$langIDs = array();
		foreach($_POST as $key=>$val)
			if(substr($key, 0, 5) == 'lang_')
				$langIDs[] = str_replace(array('/', '\\'), '', substr($key, 5));

		if(count($langIDs) > 0)
		{
			if($_REQUEST['massAction'] == 'delete')
			{
				// delete lang files
				foreach($langIDs as $langID)
				{
					if(is_writeable(B1GMAIL_DIR . 'languages/' . $langID . '.lang.php'))
						unlink(B1GMAIL_DIR . 'languages/' . $langID . '.lang.php');
				}
			}
		}
	}

	// get available languages
	$languages = GetAvailableLanguages();

	// assign
	$tpl->assign('languages', $languages);
	$tpl->assign('page', 'prefs.languages.tpl');
}

/**
 * texts
 */
else if($_REQUEST['action'] == 'texts')
{
	// language given?
	$selectedLang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : $currentLanguage;

	// get custom lang of lang file
	function GetCustomLang($langfile)
	{
		$lang_client = $lang_user = $lang_admin = $lang_custom = array();
		include(B1GMAIL_DIR . 'languages/' . $langfile . '.lang.php');
		ModuleFunction('OnReadLang', array(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $langfile));
		return($lang_custom);
	}
	if($selectedLang)
		$lang_custom = GetCustomLang($selectedLang);

	// db texts
	$dbTexts = array();
	$res = $db->Query('SELECT `key`,`text` FROM {pre}texts WHERE language=?',
		$selectedLang);
	while($row = $res->FetchArray(MYSQLI_ASSOC))
		$lang_custom[$row['key']] = $row['text'];
	$res->Free();

	// save?
	if($selectedLang && isset($_REQUEST['save']))
	{
		foreach($_POST as $key=>$val)
		{
			if(substr($key, 0, 5) == 'text-' && trim($lang_custom[substr($key, 5)]) != trim($val))
			{
				$db->Query('REPLACE INTO {pre}texts(language,`key`,`text`) VALUES(?,?,?)',
					$selectedLang,
					substr($key, 5),
					$val);
				$lang_custom[substr($key, 5)] = $val;
			}
		}

		$cacheManager->Delete('langCustom:' . $selectedLang);
	}

	// get available languages
	$languages = GetAvailableLanguages();

	// get texts
	$texts = array();
	if($selectedLang)
	{
		// lang texts
		foreach($lang_custom as $key=>$val)
		{
			$texts[$key] = array(
				'key'		=> $key,
				'title'		=> $lang_admin['text_' . $key],
				'text'		=> $val
			);
		}
	}

	// assign
	$tpl->assign('usertpldir', B1GMAIL_REL . 'templates/' . $bm_prefs['template'] . '/');
	$tpl->assign('customTextsHTML', $customTextsHTML);
	$tpl->assign('languages', $languages);
	$tpl->assign('selectedLang', $selectedLang);
	$tpl->assign('texts', $texts);
	$tpl->assign('page', 'prefs.languages.texts.tpl');
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['languages']);
$tpl->display('page.tpl');
?>