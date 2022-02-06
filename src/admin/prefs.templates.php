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
AdminRequirePrivilege('prefs.templates');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'templates';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['templates'],
		'relIcon'	=> 'template32.png',
		'link'		=> 'prefs.templates.php?',
		'active'	=> $_REQUEST['action'] == 'templates'
	)
);

/**
 * templates
 */
if($_REQUEST['action'] == 'templates')
{
	if(!isset($_REQUEST['do']))
		$_REQUEST['do'] = 'list';

	$templates = GetAvailableTemplates();

	//
	// list
	//
	if($_REQUEST['do'] == 'list')
	{
		if(isset($_REQUEST['save']) && isset($_POST['template'])
			&& isset($templates[$_POST['template']]))
		{
			$db->Query('UPDATE {pre}prefs SET `template`=?',
				$_POST['template']);
			ReadConfig();
		}

		// assign
		$tpl->assign('defaultTemplate', $bm_prefs['template']);
		$tpl->assign('templates', $templates);
		$tpl->assign('page', 'prefs.templates.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'prefs' && isset($_REQUEST['template'])
		&& isset($templates[$_REQUEST['template']]))
	{
		$prefsMeta = $templates[$_REQUEST['template']]['prefs'];

		if(isset($_POST['save']))
		{
			foreach($prefsMeta as $key=>$info)
			{
				switch($info['type'])
				{
				case FIELD_CHECKBOX:
					$value = isset($_POST['prefs'][$key]) ? 1 : 0;
					break;

				default:
					$value = $_POST['prefs'][$key];
					break;
				}

				$db->Query('REPLACE INTO {pre}templateprefs(`template`,`key`,`value`) VALUES(?,?,?)',
					$_REQUEST['template'],
					$key,
					$value);
			}
		}

		$prefsValues = GetTemplatePrefs($_REQUEST['template']);
		foreach($prefsValues as $key=>$val)
			if(isset($prefsMeta[$key]))
				$prefsMeta[$key]['value'] = $val;

		$tpl->assign('template', $_REQUEST['template']);
		$tpl->assign('templateInfo', $templates[$_REQUEST['template']]);
		$tpl->assign('meta', $prefsMeta);
		$tpl->assign('page', 'prefs.templates.prefs.tpl');		// yo dawg
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['templates']);
$tpl->display('page.tpl');
?>