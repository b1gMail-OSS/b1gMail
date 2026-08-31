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
	require '../serverlib/init.inc.php';
include('../serverlib/webdisk.class.php');
include('../serverlib/webdisk.thumbnail.inc.php');

/**
 * determine username
 */
if(isset($_REQUEST['user']))
{
	$userMail = $_REQUEST['user'];
}
else
{
	$mySubdomain = strtolower($_SERVER['HTTP_HOST']);
	$myDomains = MyDomains();

	foreach($myDomains as $domain)
		if(strlen($domain) > 1 && substr($mySubdomain, strlen($domain)*-1) == $domain
			&& substr($mySubdomain, strlen($domain)*-1-1, 1) == '.')
		{
			$userMail = substr_replace($mySubdomain, '@', strlen($domain)*-1-1, 1);
			break;
		}
}

/**
 * user exists?
 */
if(!isset($userMail) || ($userID = BMUser::GetID($userMail)) == 0)
{
	$tpl->assign('title', $lang_user['error']);
	$tpl->assign('msg', $lang_user['badshare']);
	$tpl->assign('error', true);
	$tpl->display('share/index.tpl');
	exit();
}

/**
 * open webdisk
 */
else
{
	$thisUser = _new('BMUser', array($userID));
	$userRow = $thisUser->Fetch();

	if($userRow['gesperrt'] != 'no')
	{
		$tpl->assign('title', $lang_user['error']);
		$tpl->assign('msg', $lang_user['badshare']);
		$tpl->assign('error', true);
		$tpl->display('share/index.tpl');
		exit();
	}

	$thisGroup = $thisUser->GetGroup();
	$groupRow = $thisGroup->Fetch();
	$webdisk = _new('BMWebdisk', array($userID));
}

/**
 * default action = folder
 */
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'start';

$fileShareToken = isset($_REQUEST['file']) ? trim($_REQUEST['file']) : '';
if($fileShareToken != '')
{
	$fileShare = $webdisk->GetFileShareByToken($fileShareToken);
	if($fileShare === false || (int)$fileShare['user'] !== (int)$userID)
	{
		$tpl->assign('title', $lang_user['error']);
		$tpl->assign('msg', $lang_user['badshare']);
		$tpl->assign('error', true);
		$tpl->display('share/index.tpl');
		exit();
	}
}

/**
 * show folder
 */
if($_REQUEST['action'] == 'start')
{
	$tpl->assign('user', $userMail);
	$tpl->assign('userMail', $userMail);
	$tpl->assign('fileToken', $fileShareToken);
	$tpl->display('share/index.tpl');
}

/**
 * get folder contents
 */
else if($_REQUEST['action'] == 'getFolder'
		&& isset($_REQUEST['id']))
{
	$id = (int)$_REQUEST['id'];
	$path = array(array('id' => 0, 'title' => '/'));

	if($id == 0)
	{
		if($fileShareToken != '')
		{
			$fileShare = $webdisk->GetFileShareByToken($fileShareToken);
			if($fileShare === false || (int)$fileShare['user'] !== (int)$userID)
				die('Permission denied');

			$fileInfo = $webdisk->GetFileInfo((int)$fileShare['file_id']);
			if($fileInfo === false)
				die('Permission denied');

			$dotPos = strrchr($fileInfo['dateiname'], '.');
			$ext = $dotPos !== false ? substr($dotPos, 1) : '?';
			$item = array(
				'id' => (int)$fileInfo['id'],
				'type' => WEBDISK_ITEM_FILE,
				'title' => $fileInfo['dateiname'],
				'size' => (int)$fileInfo['size'],
				'created' => (int)$fileInfo['created'],
				'accessed' => (int)$fileInfo['accessed'],
				'modified' => (int)$fileInfo['modified'],
				'ctype' => $fileInfo['contenttype'],
				'ext' => $ext,
				'pw' => trim($fileShare['share_pw']) != '',
				'share' => true
			);
			if(function_exists('WebdiskGetItemIcon'))
				$item['icon'] = WebdiskGetItemIcon($item);
			$contents = array($item);
		}
		else
			$contents = $webdisk->GetShares();
	}
	else
	{
		list($isShared, $sharePW) = $webdisk->IsShared($id);

		if(!$isShared
			|| (trim($sharePW) != '' && (!isset($_REQUEST['password']) || ($sharePW != _unescape($_REQUEST['password'])))))
			die('Permission denied');
		$path = array_merge($path, $webdisk->GetFolderPath($id));
		$contents = $webdisk->GetFolderContent($id);
	}

	foreach($path as $key=>$val)
	{
		if(isset($val['share_pw']))
		{
			$path[$key]['pw'] = trim($val['share_pw']) != '';
			unset($path[$key]['share_pw']);
		}
		else
			$path[$key]['pw'] = false;

		$path[$key]['type'] = WEBDISK_ITEM_FOLDER;

		if((int)$val['id'] === 0)
		{
			$path[$key]['title'] = '/';
			$path[$key]['ext'] = '.ROOT';
		}
		else
		{
			$path[$key]['ext'] = (isset($val['share']) && $val['share'] == 'yes')
				? '.SHAREDFOLDER'
				: '.FOLDER';
			if(function_exists('WebdiskGetItemIcon'))
				$path[$key]['icon'] = WebdiskGetItemIcon($path[$key]);
		}
	}

	NormalArray2XML(array('path' => $path, 'contents' => $contents));
}

/**
 * password dialog
 */
else if($_REQUEST['action'] == 'passwordInput'
		&& isset($_REQUEST['folder']))
{
	$tpl->assign('user', $userMail);
	$tpl->assign('folder', (int)$_REQUEST['folder']);
	$tpl->display('share/dialog.password.tpl');
}

/**
 * password dialog for file share
 */
else if($_REQUEST['action'] == 'passwordInputFile'
		&& isset($_REQUEST['id'])
		&& $fileShareToken != '')
{
	$tpl->assign('user', $userMail);
	$tpl->assign('folder', 0);
	$tpl->assign('fileShareToken', $fileShareToken);
	$tpl->assign('fileShareID', (int)$_REQUEST['id']);
	$tpl->display('share/dialog.password.file.tpl');
}

/**
 * password dialog submit
 */
else if($_REQUEST['action'] == 'passwordSubmit'
		&& isset($_REQUEST['folder'])
		&& isset($_REQUEST['pw']))
{
	echo '<script type="text/javascript">' . "\n";
	echo '<!--' . "\n";

	$folderInfo = $webdisk->GetFolderInfo((int)$_REQUEST['folder']);
	list($isShared, $sharePW) = $webdisk->IsShared((int)$_REQUEST['folder']);
	if($isShared && $_REQUEST['pw'] == $sharePW)
	{
		// ok
		echo 'parent.share_currentPWfor = ' . (int)$_REQUEST['folder'] . ';' . "\n";
		echo 'parent.share_currentPW = \'' . addslashes($_REQUEST['pw']) . '\';' . "\n";
		echo 'parent.shareEnterProtectedDir();' . "\n";
	}
	else
	{
		// wrong
		echo 'alert(\'' . addslashes($lang_user['folder_wrongpw']) . '\');' . "\n";
	}

	echo 'parent.hideOverlay();' . "\n";
	echo '//-->' . "\n";
	echo '</script>' . "\n";
}

/**
 * file password dialog submit
 */
else if($_REQUEST['action'] == 'passwordSubmitFile'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['pw'])
		&& $fileShareToken != '')
{
	echo '<script type="text/javascript">' . "\n";
	echo '<!--' . "\n";

	$fileShare = $webdisk->GetFileShareByToken($fileShareToken);
	if($fileShare !== false && (int)$fileShare['user'] === (int)$userID && $_REQUEST['pw'] == $fileShare['share_pw'])
	{
		echo 'parent.share_currentPWfor = \'f' . (int)$_REQUEST['id'] . '\';' . "\n";
		echo 'parent.share_currentPW = \'' . addslashes($_REQUEST['pw']) . '\';' . "\n";
		echo 'parent.shareEnterProtectedFile();' . "\n";
	}
	else
	{
		echo 'alert(\'' . addslashes($lang_user['folder_wrongpw']) . '\');' . "\n";
	}

	echo 'parent.hideOverlay();' . "\n";
	echo '//-->' . "\n";
	echo '</script>' . "\n";
}

/**
 * download file
 */
else if($_REQUEST['action'] == 'getFile'
		&& isset($_REQUEST['id']))
{
	$fileInfo = $webdisk->GetFileInfo((int)$_REQUEST['id']);
	if($fileInfo !== false)
	{
		if($fileShareToken != '')
		{
			$fileShare = $webdisk->GetFileShareByToken($fileShareToken);
			if($fileShare === false
				|| (int)$fileShare['user'] !== (int)$userID
				|| (int)$fileShare['file_id'] !== (int)$fileInfo['id']
				|| (trim($fileShare['share_pw']) != '' && (!isset($_REQUEST['password']) || ($fileShare['share_pw'] != _unescape($_REQUEST['password'])))))
				die('Permission denied');
		}
		else
		{
			$fileFolder = $fileInfo['ordner'];
			list($isShared, $sharePW) = $webdisk->IsShared($fileFolder);
			if(!$isShared
				|| (trim($sharePW) != '' && (!isset($_REQUEST['password']) || ($sharePW != _unescape($_REQUEST['password'])))))
				die('Permission denied');
		}

		if($groupRow['traffic'] <= 0 || ($userRow['traffic_down']+$userRow['traffic_up']+$fileInfo['size']) <= $groupRow['traffic']+$userRow['traffic_add'])
		{
			// ok
			$speedLimit = $groupRow['wd_open_kbs'] <= 0 ? -1 : $groupRow['wd_open_kbs'];
			$db->Query('UPDATE {pre}users SET traffic_down=traffic_down+? WHERE id=?',
				$fileInfo['size'],
				$userID);

			$ownerFolderInfo = $webdisk->GetFolderInfo((int)$fileInfo['ordner']);
			$thisUser->PostNotification('notify_wd_share_download',
				array(
					HTMLFormat($fileInfo['dateiname']),
					HTMLFormat($ownerFolderInfo !== false ? $ownerFolderInfo['titel'] : '/')
				),
				'webdisk.php?folder=' . (int)$fileInfo['ordner'] . '&',
				'%%tpldir%%images/li/notify_webdisk_download.png',
				0,
				0,
				NOTIFICATION_FLAG_USELANG,
				'::webdiskShareDownload');

			if($fileShareToken != '')
				$webdisk->MarkFileShareUsed($fileShareToken);

			// send file
			header('Content-Type: ' . $fileInfo['contenttype']);
			header('Content-Length: ' . $fileInfo['size']);
			header('Content-Disposition: attachment; filename="' . addslashes($fileInfo['dateiname']) . '"');
			Add2Stat('wd_down', ceil($fileInfo['size']/1024));
			SendFileFP(BMBlobStorage::CreateProvider($fileInfo['blobstorage'], $userRow['id'])->loadBlob(BMBLOB_TYPE_WEBDISK, $fileInfo['id']),
				$speedLimit);
			exit();
		}
		else
		{
			// not enough traffic
			$tpl->assign('title', $lang_user['error']);
			$tpl->assign('msg', $lang_user['notraffic'] . '.');
			$tpl->assign('error', true);
			$tpl->display('share/index.tpl');
			exit();
		}
	}
}
