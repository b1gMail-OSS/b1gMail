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
AdminRequirePrivilege('backup');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'backup';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['backup'],
		'link'		=> 'backup.php?',
		'active'	=> $_REQUEST['action'] == 'backup'
	)
);

/**
 * backup
 */
if($_REQUEST['action'] == 'backup')
{
	if(!isset($_REQUEST['do']))
		$_REQUEST['do'] = 'page';

	//
	// default page
	//
	if($_REQUEST['do'] == 'page')
	{
		// assign
		$tpl->assign('sizes', GetCategorizedSpaceUsage());
		$tpl->assign('page', 'backup.tpl');
	}

	//
	// create backup page
	//
	else if($_REQUEST['do'] == 'createBackup')
	{
		// backup sequence
		$sequence = $fileSequence = array();
		if(isset($_REQUEST['backup_prefs']))
			$sequence = array_merge($sequence, $backupTables['prefs']);
		if(isset($_REQUEST['backup_stats']))
			$sequence = array_merge($sequence, $backupTables['stats']);
		if(isset($_REQUEST['backup_users']))
			$sequence = array_merge($sequence, $backupTables['users']);
		if(isset($_REQUEST['backup_organizer']))
			$sequence = array_merge($sequence, $backupTables['organizer']);
		if(isset($_REQUEST['backup_mails']))
			$sequence = array_merge($sequence, $backupTables['mails']);
		if(isset($_REQUEST['backup_webdisk']))
			$sequence = array_merge($sequence, $backupTables['webdisk']);

		// assign
		$tpl->assign('sequence', $sequence);
		$tpl->assign('page', 'backup.create.tpl');
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['tools'] . ' &raquo; ' . $lang_admin['backup']);
$tpl->display('page.tpl');
?>