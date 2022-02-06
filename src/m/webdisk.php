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
if(!class_exists('BMWebdisk'))
	include('../serverlib/webdisk.class.php');
if(!class_exists('BMZIP'))
	include('../serverlib/zip.class.php');
RequestPrivileges(PRIVILEGES_USER | PRIVILEGES_MOBILE);

/**
 * open webdisk
 */
$webdisk 		= _new('BMWebdisk', array($userRow['id']));
$folderID 		= !isset($_REQUEST['folder']) ? 0 : (int)$_REQUEST['folder'];
$folderPath 	= $webdisk->GetFolderPath($folderID);
$spaceLimit 	= $webdisk->GetSpaceLimit();
$usedSpace 		= $webdisk->GetUsedSpace();

/**
 * assign
 */
$tpl->assign('activeTab', 	'webdisk');
$tpl->assign('folderID', 	$folderID);
if(count($folderPath) > 1)
{
	$parentFolder = end(array_slice($folderPath, -2, 1));
	$tpl->assign('parentFolderID', 		$parentFolder['id']);
	$tpl->assign('parentFolderName', 	$parentFolder['title']);
}
else if(count($folderPath) == 1)
{
	$tpl->assign('parentFolderID', 0);
	$tpl->assign('parentFolderName', $lang_user['webdisk']);
}
else
{
	$tpl->assign('parentFolderID', -1);
}

/**
 * default action = inbox
 */
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'folder';

/**
 * folder
 */
if($_REQUEST['action'] == 'folder')
{
	/**
	 * create folder
	 */
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'createFolder' && isset($_REQUEST['title']))
	{
		$folderName = trim($_REQUEST['title']);

		if($webdisk->FolderExists($folderID, $folderName) || strlen($folderName) == 0)
		{
			$tpl->assign('msg', $lang_user['foldererror']);
			$tpl->assign('page', 'm/message.tpl');
			$tpl->assign('pageTitle', $lang_user['createfolder']);
			$tpl->assign('backLink', 'webdisk.php?folder='.$folderID.'&sid='.session_id());
			$tpl->display('m/index.tpl');
		}
		else
		{
			$webdisk->CreateFolder($folderID, $folderName);
		}
	}

	/**
	 * delete item
	 */
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'deleteItem' && isset($_REQUEST['type']) && isset($_REQUEST['id']))
	{
		if($_REQUEST['type'] == WEBDISK_ITEM_FOLDER)
			$webdisk->DeleteFolder($_REQUEST['id']);
		else if($_REQUEST['type'] == WEBDISK_ITEM_FILE)
			$webdisk->DeleteFile($_REQUEST['id']);
	}

	/**
	 * upload files
	 */
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'uploadFiles' && IsPOSTRequest())
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

		if(count($error) > 0)
		{
			$tpl->assign('msg', implode(', ', $error) . '.');
			$tpl->assign('page', 'm/message.tpl');
			$tpl->assign('pageTitle', $lang_user['uploadfiles']);
			$tpl->assign('backLink', 'webdisk.php?folder='.$folderID.'&sid='.session_id());
			$tpl->display('m/index.tpl');
			exit;
		}
	}

	if($folderID <= 0)
	{
		$folderName = $lang_user['webdisk'];
	}
	else
	{
		$folder = $webdisk->GetFolderInfo($folderID);
		if($folder) $folderName = $folder['titel'];
	}

	$folderContent 	= $webdisk->GetFolderContent($folderID);
	$tpl->assign('folderContent', $folderContent);
	$tpl->assign('pageTitle', HTMLFormat($folderName));
	$tpl->assign('page', 'm/webdisk.folder.tpl');
	$tpl->display('m/index.tpl');
}

/**
 * download folder
 */
else if($_REQUEST['action'] == 'downloadFolder'
	&& isset($_REQUEST['id']))
{
	$folderID = (int)$_REQUEST['id'];
	$folderInfo = $webdisk->GetFolderInfo($folderID);
	if(!$folderInfo) die('Folder not found.');

	$tempFileID = RequestTempFile($userRow['id'], time()+TIME_ONE_HOUR);
	$tempFileName = TempFileName($tempFileID);

	// determine zip filename
	$zipName = $folderInfo['titel'];
	$zipName = preg_replace('/[^a-zA-Z0-9\-\_]/', '_', $zipName);
	if(empty($zipName)) $zipName = 'files.zip';

	// create ZIP file
	$fp = fopen($tempFileName, 'wb+');
	$zip = _new('BMZIP', array($fp));
	$webdisk->ZipFolder($folderID, $zip);
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
		$tpl->assign('page', 'm/message.tpl');
		$tpl->assign('pageTitle', $lang_user['createfolder']);
		$tpl->assign('backLink', 'webdisk.php?folder='.$folderID.'&sid='.session_id());
		$tpl->display('m/index.tpl');
	}
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
			$tpl->assign('page', 'm/message.tpl');
			$tpl->assign('pageTitle', $lang_user['createfolder']);
			$tpl->assign('backLink', 'webdisk.php?folder='.$folderID.'&sid='.session_id());
			$tpl->display('m/index.tpl');
		}
	}
}

/**
 * item details
 */
else if($_REQUEST['action'] == 'itemDetails'
	&& isset($_REQUEST['type'])
	&& isset($_REQUEST['id']))
{
	if($_REQUEST['type'] == WEBDISK_ITEM_FOLDER)
	{
		$item = $webdisk->GetFolderInfo($_REQUEST['id']);

		if($item)
		{
			$item['ext'] = $item['share'] == 'yes' ? '.SHAREDFOLDER' : '.FOLDER';
			$folderID = $item['parent'];
			$tpl->assign('pageTitle', $item['titel']);
		}
	}
	else if($_REQUEST['type'] == WEBDISK_ITEM_FILE)
	{
		$item = $webdisk->GetFileInfo($_REQUEST['id']);

		if($item)
		{
			$dotPos = strrchr($item['dateiname'], '.');
			if($dotPos !== false)
				$ext = substr($dotPos, 1);
			else
				$ext = '?';
			$item['ext'] = $ext;

			$folderID = $item['ordner'];
			$tpl->assign('pageTitle', HTMLFormat($item['dateiname']));
		}
	}

	if($item)
	{
		if($folderID <= 0)
		{
			$tpl->assign('folderName', HTMLFormat($lang_user['webdisk']));
		}
		else
		{
			$folder = $webdisk->GetFolderInfo($folderID);
			$tpl->assign('folderName', $folder['titel']);
		}

		$tpl->assign('folderID', $folderID);
		$tpl->assign('itemType', (int)$_REQUEST['type']);
		$tpl->assign('item', $item);
		$tpl->assign('page', 'm/webdisk.details.tpl');
		$tpl->display('m/index.tpl');
	}
}

/**
 * create folder
 */
else if($_REQUEST['action'] == 'createFolder')
{
	$tpl->assign('isDialog', true);
	$tpl->assign('page', 'm/webdisk.createfolder.tpl');
	$tpl->display('m/index.tpl');
}

/**
 * upload files
 */
else if($_REQUEST['action'] == 'uploadFiles')
{
	$tpl->assign('isDialog', true);
	$tpl->assign('page', 'm/webdisk.uploadfiles.tpl');
	$tpl->display('m/index.tpl');
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
?>