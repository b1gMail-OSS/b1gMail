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

require '../serverlib/admin.inc.php';
RequestPrivileges(PRIVILEGES_ADMIN);
AdminRequirePrivilege('prefs.webdisk');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'common';

$tabs = [
	0 => [
		'title'		=> $lang_admin['common'],
		'relIcon'	=> 'ico_disk.png',
		'link'		=> 'prefs.webdisk.php?',
		'active'	=> $_REQUEST['action'] == 'common'
	],
	1 => [
		'title'		=> $lang_admin['limits'],
		'relIcon'	=> 'filetype.png',
		'link'		=> 'prefs.webdisk.php?action=limits&',
		'active'	=> $_REQUEST['action'] == 'limits'
	],
];
if(LEGACY_WEBDISCICONS===true) {
	$tabs[2] = [
			'title'		=> $lang_admin['webdiskicons'],
			'relIcon'	=> 'extension.png',
			'link'		=> 'prefs.webdisk.php?action=extensions&',
			'active'	=> $_REQUEST['action'] == 'extensions'
	];
}
/**
 * common
 */
if($_REQUEST['action'] == 'common')
{
	if(isset($_REQUEST['save']))
	{
		$db->Query('UPDATE {pre}prefs SET blobstorage_provider_webdisk=?, blobstorage_webdisk_compress=?',
			$_REQUEST['blobstorage_provider_webdisk'],
			isset($_REQUEST['blobstorage_webdisk_compress']) ? 'yes' : 'no');
		ReadConfig();
	}

	// assign
	$tpl->assign('bsUserDBAvailable', BMBlobStorage::createProvider(BMBLOBSTORAGE_USERDB)->isAvailable());
	$tpl->assign('page', 'prefs.webdisk.common.tpl');
}

/**
 * webdisk
 */
else if($_REQUEST['action'] == 'limits')
{
	if(isset($_REQUEST['save']))
	{
		$forbiddenExtensionsArray = explode("\n", $_REQUEST['forbidden_extensions']);
		foreach($forbiddenExtensionsArray as $key=>$val)
			if(($val = trim($val)) != '')
				$forbiddenExtensionsArray[$key] = ($val[0]!='.'?'.':'') . $val;
			else
				unset($forbiddenExtensionsArray[$key]);
		$forbiddenExtensions = implode(':', $forbiddenExtensionsArray);

		$forbiddenMIMETypesArray = explode("\n", $_REQUEST['forbidden_mimetypes']);
		foreach($forbiddenMIMETypesArray as $key=>$val)
			if(($val = trim($val)) != '')
				$forbiddenMIMETypesArray[$key] = $val;
			else
				unset($forbiddenMIMETypesArray[$key]);
		$forbiddenMIMETypes = implode(':', $forbiddenMIMETypesArray);

		$db->Query('UPDATE {pre}prefs SET forbidden_extensions=?,forbidden_mimetypes=?',
			$forbiddenExtensions,
			$forbiddenMIMETypes);
		ReadConfig();
	}

	$bm_prefs['forbidden_extensions'] = str_replace(':', "\n", $bm_prefs['forbidden_extensions']);
	$bm_prefs['forbidden_mimetypes'] = str_replace(':', "\n", $bm_prefs['forbidden_mimetypes']);
	$tpl->assign('page', 'prefs.webdisk.limits.tpl');
}

/**
 * extensions
 */
else if($_REQUEST['action'] == 'extensions' AND LEGACY_WEBDISCICONS===true)
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
			if(isset($_FILES['icon']) && $_FILES['icon']['error'] == 0 && $_FILES['icon']['size'] > 5)
			{
				$tempFileID = RequestTempFile(0);
				$tempFileName = TempFileName($tempFileID);
				if(move_uploaded_file($_FILES['icon']['tmp_name'], $tempFileName))
				{
					$iconData = base64_encode(getFileContents($tempFileName));

					$db->Query('INSERT INTO {pre}extensions(ext,ctype,bild) VALUES(?,?,?)',
						str_replace(array(' ', '.'), '', $_REQUEST['ext']),
						$_FILES['icon']['type'],
						$iconData);
				}
				ReleaseTempFile(0, $tempFileID);
			}
		}

		// delete
		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}extensions WHERE id=?',
				(int)$_REQUEST['delete']);
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get extesion IDs
			$extIDs = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 4) == 'ext_')
					$extIDs[] = (int)substr($key, 4);

			if(count($extIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					// delete row
					$db->Query('DELETE FROM {pre}extensions WHERE id IN(' . implode(',', $extIDs) . ')');
				}
			}
		}

		// fetch
		$extensions = array();
		$res = $db->Query('SELECT id,ext,ctype FROM {pre}extensions ORDER BY ext ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$extensions[$row['id']] = array(
				'id'		=> $row['id'],
				'ext'		=> $row['ext'],
				'ctype'		=> $row['ctype']
			);
		$res->Free();

		// assign
		$tpl->assign('extensions', $extensions);
		$tpl->assign('page', 'prefs.webdisk.extensions.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit')
	{
		// save?
		if(isset($_REQUEST['save']))
		{
			// modified ext?
			if(isset($_REQUEST['ext']))
				$db->Query('UPDATE {pre}extensions SET ext=? WHERE id=?',
					str_replace(array(' ', '.'), '', $_REQUEST['ext']),
					$_REQUEST['id']);

			// new icon?
			if(isset($_FILES['icon']) && $_FILES['icon']['error'] == 0 && $_FILES['icon']['size'] > 5)
			{
				$tempFileID = RequestTempFile(0);
				$tempFileName = TempFileName($tempFileID);
				if(move_uploaded_file($_FILES['icon']['tmp_name'], $tempFileName))
				{
					$iconData = base64_encode(getFileContents($tempFileName));

					$db->Query('UPDATE {pre}extensions SET ctype=?,bild=? WHERE id=?',
						$_FILES['icon']['type'],
						$iconData,
						$_REQUEST['id']);
				}
				ReleaseTempFile(0, $tempFileID);
			}

			header('Location: prefs.webdisk.php?action=extensions&sid=' . session_id());
			exit();
		}

		// fetch
		$res = $db->Query('SELECT id,ext FROM {pre}extensions WHERE id=?',
			(int)$_REQUEST['id']);
		assert('$res->RowCount() != 0');
		$extension = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// assign
		$tpl->assign('extension', $extension);
		$tpl->assign('page', 'prefs.webdisk.extensions.edit.tpl');
	}
}

/**
 * display extension
 */
else if($_REQUEST['action'] == 'displayExt'
		&& isset($_REQUEST['id']))
{
	$res = $db->Query('SELECT bild,ctype FROM {pre}extensions WHERE id=?',
		(int)$_REQUEST['id']);
	list($img, $ctype) = $res->FetchArray(MYSQLI_NUM);
	$img = base64_decode($img);

	header('Content-Type: ' . $ctype);
	header('Content-Length: ' . strlen($img));

	echo $img;
	exit();
}

$tpl->assign('bm_prefs', $bm_prefs);
$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['webdisk']);
$tpl->display('page.tpl');
?>