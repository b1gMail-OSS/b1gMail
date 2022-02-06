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
 * open mailbox
 */
$mailbox = _new('BMMailbox', array($userRow['id'], $userRow['email'], $thisUser));
if(EXTENDED_WORKGROUPS)
	$avoidFrameTasks = false;
else
	$avoidFrameTasks = strpos($_SERVER['PHP_SELF'], 'email.read.php') !== false && isset($_REQUEST['preview']);

/**
 * template stuff
 */
$tpl->assign('activeTab', 'email');
if(!$avoidFrameTasks)
{
	$tpl->assign('spaceUsed', $mailbox->GetUsedSpace());
	$tpl->assign('spaceLimit', $mailbox->GetSpaceLimit());
	$tpl->assign('dropdownFolderList', $mailbox->GetDropdownFolderList(-1, $null));
	$tpl->assign('narrow', $thisUser->GetPref('previewPosition') == 'right');
}
$tpl->assign('pageToolbarFile', 'li/email.toolbar.tpl');
$tpl->assign('pageTitle', $lang_user['email']);
$tpl->assign('hotkeys', $thisUser->GetPref('hotkeys'));
$null = null;

/**
 * page menu (folders)
 */
list($folderList, $pageMenu) = $mailbox->GetPageFolderList();
$tpl->assign('folderList', $pageMenu);
$tpl->assign('pageMenuFile', 'li/email.sidebar.tpl');
