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

include('./serverlib/init.inc.php');
include('./serverlib/webdisk.class.php');
include('./serverlib/zip.class.php');
include('./serverlib/unzip.class.php');
RequestPrivileges(PRIVILEGES_USER);

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));

/**
 * default action = start
 */
$tpl->addJSFile('li', 'clientlib/selectable.js');
$tpl->addJSFile('li', $tpl->tplDir . 'js/webdisk.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'folder';
$tpl->assign('activeTab', 'webdisk');
$tpl->assign('pageTitle', $lang_user['webdisk']);
$tpl->assign('hasRightSidebar', true);

/**
 * webdisk interface
 */
$webdisk 		= _new('BMWebdisk', array($userRow['id']));
$folderID 		= !isset($_REQUEST['folder']) ? 0 : (int)$_REQUEST['folder'];
$folderPath 	= $webdisk->GetFolderPath($folderID);
$spaceLimit 	= $webdisk->GetSpaceLimit();
$usedSpace 		= $webdisk->GetUsedSpace();
$tpl->assign('pageMenuFile', 	'li/webdisk.folderbar.tpl');
$tpl->assign('pageToolbarFile', 'li/webdisk.toolbar.tpl');
$tpl->assign('folderList',		$webdisk->GetPageFolderList());
$tpl->assign('viewMode', 		($viewMode = $thisUser->GetPref('webdiskViewMode')) === false ? 'icons' : $viewMode);
$tpl->assign('spaceUsed', 		$usedSpace);
$tpl->assign('trafficUsed', 	$userRow['traffic_down'] + $userRow['traffic_up']);
$tpl->assign('clipboard', 		isset($_SESSION['clipboard']) && is_array($_SESSION['clipboard']) && count($_SESSION['clipboard']) > 0);
$tpl->assign('spaceLimit', 		$spaceLimit);
$tpl->assign('trafficLimit', 	$groupRow['traffic'] > 0 ? $groupRow['traffic'] + $userRow['traffic_add'] : 0);
$tpl->assign('folderID', 		$folderID);
$tpl->assign('currentPath', 	$folderPath);
$tpl->assign('userAgent',		$_SERVER['HTTP_USER_AGENT']);
$tpl->assign('dndKey',			isset($_COOKIE['sessionSecret_' . substr(session_id(), 0, 16)]) ? $_COOKIE['sessionSecret_' . substr(session_id(), 0, 16)] : '');
$tpl->assign('allowShare',		$groupRow['share'] == 'yes');
$tpl->assign('hotkeys',			$thisUser->GetPref('hotkeys'));

/**
 * folder view
 */
if($_REQUEST['action'] == 'folder')
{
	if(isset($_REQUEST['massAction']))
	{
		if(isset($_POST['selectedWebdiskItems']) && trim($_POST['selectedWebdiskItems'])!='')
		{
			$folderIDs = $fileIDs = array();

			$_items = explode(';', $_POST['selectedWebdiskItems']);
			foreach($_items as $_item)
			{
				list($_itemType, $_itemID) = explode(',', $_item);

				if($_itemType == WEBDISK_ITEM_FOLDER)
					$folderIDs[] = (int)$_itemID;
				else if($_itemType == WEBDISK_ITEM_FILE)
					$fileIDs[] = (int)$_itemID;
			}
		}
		else
		{
			$folderIDs 	= isset($_REQUEST['folders']) && is_array($_REQUEST['folders']) ? $_REQUEST['folders'] : array();
			$fileIDs	= isset($_REQUEST['files']) && is_array($_REQUEST['files']) ? $_REQUEST['files'] : array();
		}

		if($_REQUEST['massAction'] == 'delete')
		{
			foreach($folderIDs as $theFolderID)
				$webdisk->DeleteFolder((int)$theFolderID);
			foreach($fileIDs as $theFileID)
				$webdisk->DeleteFile((int)$theFileID);
			$tpl->assign('folderList',		$webdisk->GetPageFolderList());
		}

		else if($_REQUEST['massAction'] == 'download'
			&& (count($folderIDs) > 0 || count($fileIDs) > 0))
		{
			$tempFileID = RequestTempFile($userRow['id'], time()+TIME_ONE_HOUR);
			$tempFileName = TempFileName($tempFileID);

			// determine zip filename
			$zipName = '';
			if(count($folderIDs) == 1 && count($fileIDs) == 0)
			{
				$folderInfo 	= $webdisk->GetFolderInfo(end($folderIDs));
				if($folderInfo)
					$zipName 	= $folderInfo['titel'];
			}
			else if(count($folderIDs) == 0 && count($fileIDs) == 1)
			{
				$fileInfo		= $webdisk->GetFileInfo(end($fileIDs));
				if($fileInfo)
					$zipName	= $fileInfo['dateiname'];
			}
			else
			{
				$folderInfo = false;

				if(count($folderIDs) > 0)
				{
					$folderInfo		= $webdisk->GetFolderInfo(end($folderIDs));

					if($folderInfo && $folderInfo['parent'] > 0)
						$folderInfo = $webdisk->GetFolderInfo($folderInfo['parent']);
					else
						$folderInfo = false;
				}
				else if(count($fileIDs) > 0)
				{
					$fileInfo		= $webdisk->GetFileInfo(end($fileIDs));

					if($fileInfo && $fileInfo['ordner'])
						$folderInfo = $webdisk->GetFolderInfo($fileInfo['ordner']);
					else
						$folderInfo = false;
				}

				if($folderInfo)
					$zipName 	= $folderInfo['titel'];
			}
			$zipName = preg_replace('/[^a-zA-Z0-9\-\_]/', '_', $zipName);
			if(empty($zipName)) $zipName = 'files.zip';

			// create ZIP file
			$fp = fopen($tempFileName, 'wb+');
			$zip = _new('BMZIP', array($fp));
			foreach($folderIDs as $theFolderID)
				$webdisk->ZipFolder((int)$theFolderID, $zip);
			foreach($fileIDs as $theFileID)
				$webdisk->ZipFile((int)$theFileID, $zip);
			$size = $zip->Finish();

			// check traffic
			if($groupRow['traffic'] <= 0 || ($userRow['traffic_down']+$userRow['traffic_up']+$size) <= $groupRow['traffic']+$userRow['traffic_add'])
			{
				// ok
				$speedLimit = $groupRow['wd_member_kbs'] <= 0 ? -1 : $groupRow['wd_member_kbs'];
				$db->Query('UPDATE {pre}users SET traffic_down=traffic_down+? WHERE id=?',
					$size,
					$userRow['id']);

				// send file
				header('Pragma: public');
				header(sprintf('Content-Disposition: attachment; filename="%s.zip"', $zipName));
				header('Content-Type: application/zip');
				header(sprintf('Content-Length: %d',
					$size));
				Add2Stat('wd_down', ceil($size/1024));
				SendFileFP($fp, $speedLimit);

				// clean up
				fclose($fp);
				ReleaseTempFile($userRow['id'], $tempFileID);
				exit();
			}
			else
			{
				// not enough traffic
				$tpl->assign('msg', $lang_user['notraffic'] . '.');
			}

			$tpl->assign('pageContent', 'li/error.tpl');
			$tpl->display('li/index.tpl');
			exit();
		}
	}

	// upload mode?
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'uploadFilesForm')
	{
		$count = min(max(isset($_REQUEST['fileCount']) ? (int)$_REQUEST['fileCount'] : 5, 0), 50);
		$tpl->assign('upload', $count);
	}

	// change view mode?
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'changeViewMode'
		&& isset($_REQUEST['viewmode']))
	{
		$newMode = in_array($_REQUEST['viewmode'], array('icons', 'list'))
					? $_REQUEST['viewmode']
					: 'icons';
		$thisUser->SetPref('webdiskViewMode', $newMode);
		$tpl->assign('viewMode', $newMode);
	}

	$titlePath = '/';
	foreach($folderPath as $folderBit)
		$titlePath .= $folderBit['title'] . '/';

	$folderInfo 	= $webdisk->GetFolderInfo($folderID);
	$folderContent 	= $webdisk->GetFolderContent($folderID);
	$shareURL		= sprintf('%sshare/?user=%s', $bm_prefs['selfurl'], $userRow['email']);

	if($folderInfo !== false && $folderInfo['share'] == 'yes')
	{
		$shareMail = $lang_custom['share_text'];
		$shareMail = str_replace('%%url%%', $shareURL, $shareMail);
		$shareMail = str_replace('%%firstname%%', $thisUser->_row['vorname'], $shareMail);
		$shareMail = str_replace('%%lastname%%', $thisUser->_row['nachname'], $shareMail);
		$tpl->assign('shareMail', $shareMail);
		$tpl->assign('shareMailSubject', $lang_custom['share_sub']);
	}

	$tpl->assign('shareURL', $shareURL);
	$tpl->assign('isShared', $folderInfo !== false && $folderInfo['share'] == 'yes');
	$tpl->assign('folderContent', $folderContent);

	if(isset($_REQUEST['inline']))
	{
		$tpl->display('li/webdisk.folder.tpl');
	}
	else
	{
		$tpl->assign('pageContent', 'li/webdisk.folder.tpl');
		$tpl->display('li/index.tpl');
	}
}

/**
 * get file info
 */
else if($_REQUEST['action'] == 'itemInfo'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['type']))
{
	$type = (int)$_REQUEST['type'];
	$_info = false;

	if($type == WEBDISK_ITEM_FOLDER)
	{
		$_info = $webdisk->GetFolderInfo((int)$_REQUEST['id']);
		$type = 'folder';
		$ext = ($_info['share'] == 'yes') ? '.SHAREDFOLDER' : '.FOLDER';
	}
	else if($type == WEBDISK_ITEM_FILE)
	{
		$_info = $webdisk->GetFileInfo((int)$_REQUEST['id']);
		$type = 'file';
		$_info['titel'] = $_info['dateiname'];

		$dotPos = strrchr($_info['dateiname'], '.');
		if($dotPos !== false)
			$ext = substr($dotPos, 1);
		else
			$ext = '?';
	}

	if(!$_info) die('Item not found');

	$info = array(
		'type'			=> (int)$_REQUEST['type'],
		'title'			=> $_info['titel'],
		'shortTitle'	=> TemplateText(array('cut' => 20, 'value' => $_info['titel']), $tpl),
		'size'			=> $type == 'folder' ? '-' : TemplateSize(array('bytes' => $_info['size']), $tpl),
		'ext'			=> $ext,
		'created'		=> TemplateDate(array('timestamp' => $_info['created'], 'nice' => true), $tpl),
		'id'			=> $_info['id'],
		'share'			=> $type == 'folder' && $_info['share'] == 'yes',
		'viewable'		=> $type == 'folder' || in_array(strtolower($_info['contenttype']), $VIEWABLE_TYPES)
	);

	NormalArray2XML($info, $type);
	exit();
}

/**
 * download file
 */
else if($_REQUEST['action'] == 'downloadFile'
		&& isset($_REQUEST['id']))
{
	$fileInfo = $webdisk->GetFileInfo((int)$_REQUEST['id']);
	if($fileInfo !== false)
	{
		if($groupRow['traffic'] <= 0 || ($userRow['traffic_down']+$userRow['traffic_up']+$fileInfo['size']) <= $groupRow['traffic']+$userRow['traffic_add'])
		{
			// ok
			$speedLimit = $groupRow['wd_member_kbs'] <= 0 ? -1 : $groupRow['wd_member_kbs'];
			$db->Query('UPDATE {pre}users SET traffic_down=traffic_down+? WHERE id=?',
				$fileInfo['size'],
				$userRow['id']);

			// send file
			header('Pragma: public');
			header('Content-Type: ' . $fileInfo['contenttype']);
			header('Content-Length: ' . $fileInfo['size']);
			header('Content-Disposition: ' . (isset($_REQUEST['view']) ? 'inline' : 'attachment') . '; filename="' . addslashes($fileInfo['dateiname']) . '"');
			Add2Stat('wd_down', ceil($fileInfo['size']/1024));
			SendFileFP(BMBlobStorage::CreateProvider($fileInfo['blobstorage'], $userRow['id'])->loadBlob(BMBLOB_TYPE_WEBDISK, $fileInfo['id']),
				$speedLimit);
			exit();
		}
		else
		{
			// not enough traffic
			$tpl->assign('msg', $lang_user['notraffic'] . '.');
		}
	}

	$tpl->assign('pageContent', 'li/error.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * create folder
 */
else if($_REQUEST['action'] == 'createFolder' && isset($_REQUEST['folderName']))
{
	$folderName = trim($_REQUEST['folderName']);

	if($webdisk->FolderExists($folderID, $folderName) || strlen($folderName) == 0)
	{
		$tpl->assign('msg', $lang_user['foldererror']);
		$tpl->assign('pageContent', 'li/error.tpl');
		$tpl->display('li/index.tpl');
	}
	else
	{
		$webdisk->CreateFolder($folderID, $folderName);

		if(isset($_REQUEST['rpc']))
			die('1');
		else
			header('Location: webdisk.php?folder=' . $folderID . '&sid=' . session_id());
	}
}

/**
 * folder share settings
 */
else if($_REQUEST['action'] == 'shareFolder' && isset($_REQUEST['id']) && $groupRow['share'] == 'yes')
{
	$folderInfo = $webdisk->GetFolderInfo((int)$_REQUEST['id']);
	if($folderInfo !== false)
	{
		$tpl->assign('pageTitle',		$lang_user['sharing']);
		$tpl->assign('id', 				$folderInfo['id']);
		$tpl->assign('folderName', 		$folderInfo['titel']);
		$tpl->assign('folderShared', 	$folderInfo['share'] == 'yes');
		$tpl->assign('folderPW', 		$folderInfo['share_pw']);
		$tpl->assign('pageContent', 	'li/webdisk.share.tpl');
		$tpl->display('li/index.tpl');
	}
}

/**
 * save share settings
 */
else if($_REQUEST['action'] == 'saveShareSettings' && isset($_REQUEST['id']) && $groupRow['share'] == 'yes'
		&& IsPOSTRequest())
{
	$webdisk->SetShareSettings((int)$_REQUEST['id'], isset($_REQUEST['shareFolder']), $_REQUEST['sharePW']);
	header('Location: webdisk.php?folder=' . (int)$_REQUEST['id'] . '&sid=' . session_id());
}

/**
 * extract
 */
else if($_REQUEST['action'] == 'extractFile' && isset($_REQUEST['id']))
{
	$folder 	= isset($_REQUEST['folder']) ? (int)$_REQUEST['folder'] : 0;
	$file 		= $webdisk->GetFileInfo((int)$_REQUEST['id']);

	if($folder == 0)
		$folderPathStr = '/';
	else
	{
		$folderPathStr = '/';
		foreach($folderPath as $folderBit)
			$folderPathStr .= $folderBit['title'] . '/';
	}

	if(!$file)
		die('File not found');

	$tpl->assign('folder',			$folder);
	$tpl->assign('folderName',		$folderPathStr);
	$tpl->assign('id', 				(int)$_REQUEST['id']);
	$tpl->assign('fileName', 		$file['dateiname']);
	$tpl->assign('pageContent',		'li/webdisk.extract.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * extract action
 */
else if($_REQUEST['action'] == 'doExtractFile' && isset($_REQUEST['id'])
		&& isset($_REQUEST['folder']))
{
	$folderID	= (int)$_REQUEST['folder'];
	$fileID		= $zipFileID = (int)$_REQUEST['id'];
	$deleteZIP	= isset($_REQUEST['deleteAfterExtraction']);
	$overwrite 	= $_REQUEST['existingFiles'] == 'overwrite';
	$folderInfo = $webdisk->GetFolderInfo($folderID);
	$fileInfo 	= $webdisk->GetFileInfo($fileID);
	$success	= false;

	if((!$folderInfo && $folderID != 0) || !$fileInfo)
		die('Folder/file not found');

	// open ZIP
	$fp 		= BMBlobStorage::CreateProvider($fileInfo['blobstorage'], $userRow['id'])->loadBlob(BMBLOB_TYPE_WEBDISK, $fileInfo['id']);
	if(!$fp)
		die('File not found');
	$zip 		= _new('BMUnZIP', array(&$fp));
	$fileList	= $zip->GetFileList();

	// calc required space
	$requiredSpace = 0;
	foreach($fileList as $file)
		$requiredSpace += $file['uncompressedSize'];

	// check space
	if($spaceLimit == -1 || $usedSpace+$requiredSpace <= $spaceLimit)
	{
		foreach($fileList as $fileNo=>$file)
		{
			$folderName 	= dirname($file['fileName']);
			$fileName 		= basename($file['fileName']);
			$destFolderID	= $folderID;

			if($folderName != '.')
			{
				$folderParts = explode('/', $folderName);
				foreach($folderParts as $folderPart)
				{
					$folderPartID = $webdisk->FolderExists($destFolderID, $folderPart);

					if($folderPartID == 0)
						$folderPartID = $webdisk->CreateFolder($destFolderID, $folderPart);

					$destFolderID = $folderPartID;
				}
			}

			if($exFileID = $webdisk->FileExists($destFolderID, $fileName))
			{
				if($overwrite)
					$webdisk->DeleteFile($exFileID);
				else
					continue;
			}

			$fileID = $webdisk->CreateFile($destFolderID, $fileName, GuessMIMEType($fileName), $file['uncompressedSize']);
			if($fileID)
			{
				$fpDest = fopen('php://temp', 'wb+');
				$zip->ExtractFile($fileNo, $fpDest, $file['uncompressedSize']);
				fseek($fpDest, 0, SEEK_SET);
				if(!BMBlobStorage::createDefaultWebdiskProvider($userRow['id'])->storeBlob(BMBLOB_TYPE_WEBDISK, $fileID, $fpDest))
					$webdisk->DeleteFile($fileID);
				fclose($fpDest);
			}
		}

		$success = true;
	}
	else
	{
		// not enough space
		$tpl->assign('msg', $lang_user['nospace'] . '.');
		$tpl->assign('pageContent', 'li/error.tpl');
		$tpl->display('li/index.tpl');
	}

	// close ZIP
	fclose($fp);

	if($success)
	{
		if($deleteZIP)
			$webdisk->DeleteFile($zipFileID);

		header('Location: webdisk.php?folder='.$folderID.'&sid='.session_id());
		exit();
	}
}

/**
 * rename file/folder
 */
else if($_REQUEST['action'] == 'renameItem'
		&& isset($_REQUEST['type'])
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['name']))
{
	$newName = trim($_REQUEST['name']);

	if($_REQUEST['type'] == WEBDISK_ITEM_FILE)
	{
		$fileInfo = $webdisk->GetFileInfo((int)$_REQUEST['id']);
		if($fileInfo !== false)
		{
			if($newName == $fileInfo['dateiname']
				|| strlen($newName) < 1
				|| $webdisk->FileExists($folderID, $newName))
				die($fileInfo['dateiname']);
			die($webdisk->RenameFile((int)$_REQUEST['id'], $newName) ? $newName : $fileInfo['dateiname']);
		}
	}
	else if($_REQUEST['type'] == WEBDISK_ITEM_FOLDER)
	{
		$folderInfo = $webdisk->GetFolderInfo((int)$_REQUEST['id']);
		if($folderInfo !== false)
		{
			if($newName == $folderInfo['titel']
				|| strlen($newName) < 1
				|| $webdisk->FolderExists($folderID, $newName))
				die($folderInfo['titel']);
			die($webdisk->RenameFolder((int)$_REQUEST['id'], $newName) ? $newName : $folderInfo['titel']);
		}
	}
}

/**
 * delete file
 */
else if($_REQUEST['action'] == 'deleteItem'
		&& isset($_REQUEST['type'])
		&& isset($_REQUEST['id']))
{
	if($_REQUEST['type'] == WEBDISK_ITEM_FILE)
	{
		$webdisk->DeleteFile((int)$_REQUEST['id']);
	}
	else
	{
		$webdisk->DeleteFolder((int)$_REQUEST['id']);
	}
	header('Location: webdisk.php?folder=' . $folderID . '&sid=' . session_id());
}

/**
 * clipboard copy/cut
 */
else if($_REQUEST['action'] == 'clipboardAction'
		&& isset($_REQUEST['do'])
		&& in_array($_REQUEST['do'], array('cut', 'copy'))
		&& isset($_REQUEST['items']))
{
	$items = explode(';', $_REQUEST['items']);
	$clipboard = array();

	foreach($items as $item)
	{
		$parts = explode(',', $item);
		if(count($parts) != 2)
			continue;
		list($itemType, $itemID) = $parts;

		$clipboard[] = array(
			'do'		=> $_REQUEST['do'],
			'type'		=> (int)$itemType,
			'id'		=> (int)$itemID
		);
	}

	$_SESSION['clipboard'] = $clipboard;

	die('Ok');
}

/**
 * DnD move
 */
else if($_REQUEST['action'] == 'moveItems'
	&& isset($_REQUEST['items'])
	&& isset($_REQUEST['destFolderID']))
{
	$folderInvolved = false;
	$destFolderID = (int)$_REQUEST['destFolderID'];

	if(!empty($_REQUEST['items']))
	{
		$items = explode(';', $_REQUEST['items']);
		foreach($items as $item)
		{
			$split = explode(',', $item);
			if(count($split) != 2) continue;
			list($type, $itemID) = $split;

			if($type == WEBDISK_ITEM_FILE)
			{
				$webdisk->MoveFile($destFolderID, $itemID);
			}
			else if($type == WEBDISK_ITEM_FOLDER)
			{
				$folderInvolved = true;
				$webdisk->MoveFolder($destFolderID, $itemID);
			}
		}
	}

	echo('Ok');
	if($folderInvolved)
		echo(',ReloadFolderList');
	exit();
}

/**
 * clipboard paste
 */
else if($_REQUEST['action'] == 'pasteHere')
{
	$ok = false;

	foreach($_SESSION['clipboard'] as $key=>$clipboardItem)
	{
		// cut
		if($clipboardItem['do'] == 'cut')
		{
			// file
			if($clipboardItem['type'] == WEBDISK_ITEM_FILE)
			{
				$fileInfo = $webdisk->GetFileInfo($clipboardItem['id']);
				if($webdisk->FileExists($folderID, $fileInfo['dateiname']))
				{
					// exists
					$tpl->assign('msg', $lang_user['fileexists'] . '.');
				}
				else
				{
					// ok!
					$webdisk->MoveFile($folderID, $clipboardItem['id']);
					unset($_SESSION['clipboard'][$key]);
					$ok = true;
				}
			}

			// folder
			else if($clipboardItem['type'] == WEBDISK_ITEM_FOLDER)
			{
				$folderInfo = $webdisk->GetFolderInfo($clipboardItem['id']);
				if($webdisk->FolderExists($folderID, $folderInfo['titel']))
				{
					// exists
					$tpl->assign('msg', $lang_user['foldererror']);
				}
				else
				{
					// ok!
					$webdisk->MoveFolder($folderID, $clipboardItem['id']);
					unset($_SESSION['clipboard'][$key]);
					$ok = true;
				}
			}
		}

		// copy
		else if($clipboardItem['do'] == 'copy')
		{
			// file
			if($clipboardItem['type'] == WEBDISK_ITEM_FILE)
			{
				$fileInfo = $webdisk->GetFileInfo($clipboardItem['id']);
				if($fileInfo !== false && $webdisk->FileExists($folderID, $fileInfo['dateiname']))
				{
					// exists
					$tpl->assign('msg', $lang_user['fileexists'] . '.');
				}
				else if($fileInfo !== false)
				{
					if($spaceLimit == -1 || ($usedSpace+$fileInfo['size']) <= $spaceLimit)
					{
						// ok!
						$webdisk->CopyFile($folderID, $clipboardItem['id']);
						$ok = true;
					}
					else
					{
						// not enough space
						$tpl->assign('msg', $lang_user['nospace'] . '.');
					}
				}
				else
				{
					$tpl->assign('msg', $lang_user['sourcenex']);
				}
			}

			// folder
			else if($clipboardItem['type'] == WEBDISK_ITEM_FOLDER)
			{
				$folderInfo = $webdisk->GetFolderInfo($clipboardItem['id']);
				if($folderInfo !== false && $webdisk->FolderExists($folderID, $folderInfo['titel']))
				{
					// exists
					$tpl->assign('msg', $lang_user['foldererror']);
				}
				else if($folderInfo !== false)
				{
					// copy folder
					$maxSpace = $spaceLimit == -1 ? -1 : $spaceLimit - $usedSpace;
					if(!$webdisk->CopyFolder($folderID, $clipboardItem['id'], $maxSpace))
					{
						// not enough space
						$tpl->assign('msg', $lang_user['nospace2'] . '.');
					}
					else
					{
						$ok = true;
					}
				}
				else
				{
					$tpl->assign('msg', $lang_user['sourcenex']);
				}
			}
		}
	}

	if($ok)
	{
		header('Location: webdisk.php?folder=' . $folderID . '&sid=' . session_id());
	}
	else
	{
		$tpl->assign('pageContent', 'li/error.tpl');
		$tpl->display('li/index.tpl');
	}
}

/**
 * dnd upload from new JS uploader
 */
else if($_REQUEST['action'] == 'dndUpload'
		&& IsPOSTRequest()
		&& isset($_REQUEST['filename'])
		&& isset($_REQUEST['type']))
{
	$msg = '0';
	$fileName = $_REQUEST['filename'];
	$fileSize = (int)$_REQUEST['size'];
	$mimeType = $_REQUEST['type'];

	if($mimeType == '' || $mimeType == 'application/octet-stream')
		$mimeType = GuessMIMEType($fileName);

	if($groupRow['traffic'] <= 0 || ($userRow['traffic_down']+$userRow['traffic_up']+$fileSize) <= $groupRow['traffic']+$userRow['traffic_add'])
	{
		if($spaceLimit == -1 || $usedSpace+$fileSize <= $spaceLimit)
		{
			if(($fileID = $webdisk->CreateFile($folderID, $fileName, $mimeType, $fileSize)) !== false)
			{
				$success = false;

				$fp = @fopen('php://input', 'rb');
				$fpOut = @fopen('php://temp', 'wb+');
				if($fpOut)
				{
					if($fp)
					{
						$readBytes = 0;
						while(!feof($fp))
						{
							$chunkSize = 4*1024;

							$chunk = base64_decode(fread($fp, $chunkSize));
							fwrite($fpOut, $chunk);

							$readBytes += strlen($chunk);

							if($readBytes >= $fileSize)
								break;
						}
						fclose($fp);

						fseek($fpOut, 0, SEEK_SET);
						$success = BMBlobStorage::createDefaultWebdiskProvider($userRow['id'])->storeBlob(BMBLOB_TYPE_WEBDISK, $fileID, $fpOut, $fileSize);
					}

					fclose($fpOut);
				}

				if(!$success || ($readBytes != $fileSize))
				{
					$webdisk->DeleteFile($fileID);
					$msg = $lang_user['internalerror'];

					// log
					if(!$success)
					{
						PutLog(sprintf('Failed to save DnD-uploaded file (readBytes: %d, fileSize: %d), deleting webdisk file',
								$readBytes,
								$fileSize),
								PRIO_ERROR,
								__FILE__,
								__LINE__);
					}
				}
				else
				{
					if($fileSize < $readBytes)
					{
						$db->Query('UPDATE {pre}diskfiles SET `size`=? WHERE `id`=?',
							$readBytes,
							$fileSize);
						$fileSize = $readBytes;
					}

					$usedSpace += $fileSize;
					$db->Query('UPDATE {pre}users SET traffic_up=traffic_up+? WHERE id=?',
						$fileSize,
						$userRow['id']);
					Add2Stat('wd_up', ceil($fileSize/1024));
					$msg = '1';
				}
			}
			else
			{
				$msg = $lang_user['fileexists'];
			}
		}
		else
		{
			$msg = $lang_user['nospace'];
		}
	}
	else
	{
		$msg = $lang_user['notraffic'];
	}

	echo $msg;
}

/**
 * upload files
 */
else if($_REQUEST['action'] == 'uploadFiles'
		&& IsPOSTRequest())
{
	$error = $success = array();

	foreach($_FILES as $key=>$value)
	{
		if(is_array($value) && substr($key, 0, 4) == 'file' && isset($value['name']) && trim($value['name']) != '')
		{
			$fileName = isset($value['name']) ? $value['name'] : 'unknown';
			$fileSize = (int)$value['size'];
			$mimeType = $value['type'];

			if($mimeType == '' || $mimeType == 'application/octet-stream')
				$mimeType = GuessMIMEType($fileName);

			if($groupRow['traffic'] <= 0 || ($userRow['traffic_down']+$userRow['traffic_up']+$fileSize) <= $groupRow['traffic']+$userRow['traffic_add'])
			{
				if($spaceLimit == -1 || $usedSpace+$fileSize <= $spaceLimit)
				{
					if(($fileID = $webdisk->CreateFile($folderID, $fileName, $mimeType, $fileSize)) !== false)
					{
						$tempFileID = RequestTempFile($userRow['id'], time()+TIME_ONE_HOUR);
						$tempFileName = TempFileName($tempFileID);

						if(!@move_uploaded_file($value['tmp_name'], $tempFileName))
						{
							$webdisk->DeleteFile($fileID);
							$error[$fileName] = $lang_user['internalerror'];

							// log
							PutLog(sprintf('Failed to move uploaded file <%s> to <%s>, deleting webdisk file',
								$value['tmp_name'],
								$tempFileName),
								PRIO_ERROR,
								__FILE__,
								__LINE__);
						}
						else
						{
							$sourceFP = fopen($tempFileName, 'rb');
							if($sourceFP
								&& BMBlobStorage::createDefaultWebdiskProvider($userRow['id'])->storeBlob(BMBLOB_TYPE_WEBDISK, $fileID, $sourceFP))
							{
								fclose($sourceFP);

								$usedSpace += $fileSize;
								$db->Query('UPDATE {pre}users SET traffic_up=traffic_up+? WHERE id=?',
									$fileSize,
									$userRow['id']);
								Add2Stat('wd_up', ceil($fileSize/1024));
								$success[$fileName] = $lang_user['success'];
							}
							else
							{
								$webdisk->DeleteFile($fileID);
								$error[$fileName] = $lang_user['internalerror'];
							}
						}

						ReleaseTempFile($userRow['id'], $tempFileID);
					}
					else
					{
						$error[$fileName] = $lang_user['fileexists'];
					}
				}
				else
				{
					$error[$fileName] = $lang_user['nospace'];
				}
			}
			else
			{
				$error[$fileName] = $lang_user['notraffic'];
			}
		}
	}

	if(count($error) > 0 || count($success) > 0)
	{
		$tpl->assign('error', $error);
		$tpl->assign('success', $success);
		$tpl->assign('pageContent', 'li/webdisk.uploadresult.tpl');
		$tpl->display('li/index.tpl');
	}
	else
	{
		header('Location: webdisk.php?folder=' . $folderID . '&sid=' . session_id());
	}
}

/**
 * extension image
 */
else if($_REQUEST['action'] == 'displayExtension')
{
	if(isset($_REQUEST['ext']))
		$ext = preg_replace('/[^a-zA-Z\.0-9]/', '', $_REQUEST['ext']);
	else
		$ext = '.?';
	$webdisk->DisplayExtension($ext);
}

/**
 * dialog
 */
else if($_REQUEST['action'] == 'webdiskDialog')
{
	// type
	if(isset($_REQUEST['type']) && $_REQUEST['type']=='save')
		$type = 'save';
	else
		$type = 'open';

	$tpl->assign('type', $type);
	$tpl->display('li/webdisk.dialog.tpl');
}

/**
 * dialog content
 */
else if($_REQUEST['action'] == 'webdiskDialogContent')
{
	// path
	if(!isset($_REQUEST['path']))
		$path = 0;
	else
		$path = (int)$_REQUEST['path'];

	// get path
	$pathArray = array_merge(array(0 => array('id' => '0', 'title' => '/')), $webdisk->GetFolderPath($path));
	$pathIDs = array();
	foreach($pathArray as $item)
		$pathIDs[] = $item['id'];

	// process path
	$contentColumns = array();
	foreach($pathArray as $pathFolder)
	{
		$content = $webdisk->GetFolderContent($pathFolder['id'], 'dateiname', 'ASC');
		foreach($content as $key=>$val)
		{
			$content[$key]['folderID'] = $pathFolder['id'];
			if($val['type'] == WEBDISK_ITEM_FOLDER && in_array($val['id'], $pathIDs))
				$content[$key]['inPath'] = true;
		}
		$contentColumns[] = $content;
	}

	// assign & display
	$tpl->assign('height', (int)$_REQUEST['height']);
	$tpl->assign('columns', $contentColumns);
	$tpl->assign('pathID', $path);
	$tpl->assign('history', array_reverse($pathArray));
	$tpl->display('li/webdisk.dialog.content.tpl');
}

/**
 * import from mail dialog
 */
else if($_REQUEST['action'] == 'importFromMail')
{
	$tpl->assign('params', 'webdisk.php?action=doImportFromMail&sid=' . session_id() . '&id=' . (int)$_REQUEST['id'] . '&attachment=' . preg_replace('/[^\.0-9]/', '', $_REQUEST['attachment']));
	$tpl->assign('filename', _unescape($_REQUEST['filename']));
	$tpl->assign('type', 'save');
	$tpl->display('li/webdisk.dialog.tpl');
}

/**
 * import attachment
 */
else if($_REQUEST['action'] == 'doImportFromMail'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['attachment'])
		&& isset($_REQUEST['filename'])
		&& isset($_REQUEST['path']))
{
	$mailID = (int)$_REQUEST['id'];
	$attachment = $_REQUEST['attachment'];
	$fileName = trim(_unescape($_REQUEST['filename']));
	$folderID = (int)$_REQUEST['path'];

	echo '<script>' . "\n";
	echo '<!--' . "\n";

	// load class, if needed
	if(!class_exists('BMMailbox'))
		include(B1GMAIL_DIR . 'serverlib/mailbox.class.php');

	// open mailbox
	$mailbox = _new('BMMailbox', array($userRow['id'], $userRow['email'], $thisUser));

	// get mail
	$mail = $mailbox->GetMail($mailID);
	if($mail !== false)
	{
		$parts = $mail->GetPartList();
		if(isset($parts[$attachment]))
		{
			$part = $parts[$attachment];

			// attachment => temp file
			$fp = fopen('php://temp', 'wb+');
			$attData = &$part['body'];
			$attData->Init();
			while($block = $attData->DecodeBlock(PART_CHUNK_SIZE))
			{
				fwrite($fp, $block);
			}
			$attData->Finish();
			$fileSize = ftell($fp);
			fseek($fp, 0, SEEK_SET);

			// limit?
			if($spaceLimit == -1 || $usedSpace+$fileSize <= $spaceLimit)
			{
				// try to create file
				if(!($fileID = $webdisk->CreateFile($folderID, $fileName, $part['content-type'], $fileSize))
					|| !BMBlobStorage::createDefaultWebdiskProvider($userRow['id'])->storeBlob(BMBLOB_TYPE_WEBDISK, $fileID, $fp))
				{
					echo 'alert(\'' . addslashes($lang_user['fileexists']) . '\');' . "\n";
				}
			}
			else
			{
				// too less space
				echo 'alert(\'' . addslashes($lang_user['nospace']) . '\');' . "\n";
			}

			// release temp file
			fclose($fp);
		}

	}

	echo 'parent.hideOverlay();' . "\n";
	echo '//-->' . "\n";
	echo '</script>' . "\n";
}

/**
 * create folder RPC
 */
else if($_REQUEST['action'] == 'webdiskDialogCreateFolder' && isset($_REQUEST['title']))
{
	$folderName = trim(_unescape($_REQUEST['title']));
	$folderID = (int)$_REQUEST['path'];

	if(!$webdisk->FolderExists($folderID, $folderName) && strlen($folderName) > 0)
	{
		$newFolderID = $webdisk->CreateFolder($folderID, $folderName);
		echo($newFolderID);
		die();
	}

	die('0');
}

/**
 * rpc get folder list
 */
else if($_REQUEST['action'] == 'getFolderList')
{
	$tpl->display('li/webdisk.folderlist.tpl');
}
?>