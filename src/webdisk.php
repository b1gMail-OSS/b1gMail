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

require './serverlib/init.inc.php';
include('./serverlib/webdisk.class.php');
include('./serverlib/webdisk.thumbnail.inc.php');
WebdiskThumbnailsEnsureSchema();
include('./serverlib/zip.class.php');
include('./serverlib/unzip.class.php');
RequestPrivileges(PRIVILEGES_USER);

function WebdiskGenerateSharePassword($minLength = 12)
{
	$alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%*-_';
	$alphaLen = strlen($alphabet);
	$pw = '';

	if(function_exists('random_int'))
	{
		for($i = 0; $i < $minLength; $i++)
			$pw .= $alphabet[random_int(0, $alphaLen - 1)];
	}
	else
	{
		$seed = md5(uniqid((string)mt_rand(), true));
		while(strlen($pw) < $minLength)
		{
			$idx = hexdec($seed[strlen($pw) % strlen($seed)]) % $alphaLen;
			$pw .= $alphabet[$idx];
		}
	}

	return($pw);
}

function WebdiskStoreShareFeedback($folderID, $fileName, $shareURL)
{
	$_SESSION['webdiskShareFeedback'] = array(
		'folderID' => (int)$folderID,
		'fileName' => (string)$fileName,
		'shareURL' => (string)$shareURL
	);
}

function WebdiskApplyShareFeedback($folderID, $tpl)
{
	if(empty($_SESSION['webdiskShareFeedback']))
		return;

	$fb = $_SESSION['webdiskShareFeedback'];
	unset($_SESSION['webdiskShareFeedback']);

	if((int)$fb['folderID'] !== (int)$folderID)
		return;

	$tpl->assign('fileShareNoticeName', isset($fb['fileName']) ? $fb['fileName'] : '');
	$tpl->assign('fileShareNoticeURL', isset($fb['shareURL']) ? $fb['shareURL'] : '');
}

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
$tpl->addJSFile('li', 'clientlib/pdfjs/pdf.min.js');
$tpl->addJSFile('li', $tpl->tplDir . 'js/webdisk.preview.js');
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
			$zipName = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '_', $zipName);
			if(empty($zipName))
				$zipName = 'files';
			if(preg_match('/\.zip$/i', $zipName))
				$zipName = substr($zipName, 0, -4);

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

	WebdiskApplyUploadFeedback($folderID, $tpl);
	WebdiskApplyShareFeedback($folderID, $tpl);

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
	$webdiskMaxUploadBytes = WebdiskGetMaxUploadFileSize($usedSpace, $spaceLimit, $groupRow, $userRow);
	$tpl->assign('webdiskMaxUploadBytes', $webdiskMaxUploadBytes);
	$tpl->assign('webdiskMaxUploadSize', WebdiskFormatBytes($webdiskMaxUploadBytes));
	$webdiskUploadRules = WebdiskGetForbiddenUploadRules();
	$tpl->assign('webdiskUploadRulesJSON', json_encode($webdiskUploadRules, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE));
	$tpl->assign('webdiskForbiddenExtensions', $webdiskUploadRules['extensions']);
	$tpl->assign('webdiskForbiddenMimetypes', $webdiskUploadRules['mimetypes']);
	$tpl->assign('webdiskForbiddenExtensionsList', implode(', ', $webdiskUploadRules['extensions']));
	$tpl->assign('webdiskForbiddenMimetypesList', implode(', ', $webdiskUploadRules['mimetypes']));
	$tpl->assign('folderContent', $folderContent);
	$tpl->assign('webdiskThumbnails', isset($groupRow['wd_thumbnails']) && $groupRow['wd_thumbnails'] == 'yes');
	$webdiskPreviewData = WebdiskBuildPreviewData($folderContent);
	$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
	$tpl->assign('webdiskPreviewFilesJSON', json_encode($webdiskPreviewData['gallery'], $jsonFlags));
	$tpl->assign('webdiskPreviewItemsJSON', json_encode($webdiskPreviewData['items'], $jsonFlags));
	$tpl->assign('webdiskicons', 'li/webdisk.icons.tpl');
	if(LEGACY_WEBDISCICONS===true) {
		$tpl->assign('use_fa_icons', 0);
	}
	else {
		$tpl->assign('use_fa_icons', 1);
	}

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
		'size'			=> $type == 'folder'
							? TemplateSize(array('bytes' => $webdisk->GetFolderTreeSize((int)$_info['id'])), $tpl)
							: TemplateSize(array('bytes' => $_info['size']), $tpl),
		'ext'			=> $ext,
		'created'		=> TemplateDate(array('timestamp' => $_info['created'], 'nice' => true), $tpl),
		'id'			=> $_info['id'],
		'share'			=> $type == 'folder'
							? ($_info['share'] == 'yes')
							: $webdisk->IsFileShared((int)$_info['id']),
		'viewable'		=> $type == 'folder'
							|| in_array(strtolower($_info['contenttype']), $VIEWABLE_TYPES)
							|| ($type == 'file' && WebdiskIsTextPreviewFile($_info['dateiname'], $_info['contenttype']))
							|| ($type == 'file' && function_exists('WebdiskIsMediaPreviewFile') && WebdiskIsMediaPreviewFile($_info['dateiname'], $_info['contenttype']))
	);

	NormalArray2XML($info, $type);
	exit();
}

/**
 * get info for multiple selected items
 */
else if($_REQUEST['action'] == 'selectionInfo'
		&& isset($_REQUEST['items']))
{
	$folderIDs = $fileIDs = array();

	$_items = explode(';', $_REQUEST['items']);
	foreach($_items as $_item)
	{
		if(trim($_item) == '')
			continue;

		list($_itemType, $_itemID) = explode(',', $_item);

		if((int)$_itemType == WEBDISK_ITEM_FOLDER)
			$folderIDs[] = (int)$_itemID;
		else if((int)$_itemType == WEBDISK_ITEM_FILE)
			$fileIDs[] = (int)$_itemID;
	}

	$stats = $webdisk->GetSelectionStats($folderIDs, $fileIDs);

	$info = array(
		'count'			=> $stats['count'],
		'fileCount'		=> $stats['fileCount'],
		'folderCount'	=> $stats['folderCount'],
		'totalSize'		=> $stats['totalSize'],
		'sizeFormatted'	=> TemplateSize(array('bytes' => $stats['totalSize']), $tpl)
	);

	NormalArray2XML($info, 'selection');
	exit();
}

/**
 * thumbnail image (group option wd_thumbnails)
 */
else if($_REQUEST['action'] == 'thumbnail'
		&& isset($_REQUEST['id']))
{
	if(!isset($groupRow['wd_thumbnails']) || $groupRow['wd_thumbnails'] != 'yes' || !function_exists('imagecreatetruecolor'))
	{
		header('HTTP/1.1 404 Not Found');
		exit();
	}

	$fileInfo = $webdisk->GetFileInfo((int)$_REQUEST['id']);
	if($fileInfo === false || !WebdiskThumbnailIsSupportedType($fileInfo))
	{
		header('HTTP/1.1 404 Not Found');
		exit();
	}

	$cachePath = WebdiskEnsureThumbnail($fileInfo, $userRow['id']);
	if($cachePath === false)
	{
		header('HTTP/1.1 404 Not Found');
		exit();
	}

	header('Content-Type: image/jpeg');
	header('Cache-Control: private, max-age=86400');
	readfile($cachePath);
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
			$isInlineView = isset($_REQUEST['view']);
			$contentType = strtolower($fileInfo['contenttype']);
			$isMediaFile = function_exists('WebdiskIsMediaPreviewFile')
				&& WebdiskIsMediaPreviewFile($fileInfo['dateiname'], $contentType);
			$isPdfView = $isInlineView && $contentType === 'application/pdf';
			$isMediaRangeView = $isInlineView && ($isMediaFile || strpos($contentType, 'video/') === 0 || strpos($contentType, 'audio/') === 0);

			$effectiveContentType = $fileInfo['contenttype'];
			if($isInlineView && $isMediaFile)
			{
				$guessedType = GuessMIMEType($fileInfo['dateiname']);
				if(is_string($guessedType) && trim($guessedType) != '' && strtolower($guessedType) != 'application/octet-stream')
					$effectiveContentType = $guessedType;
				else if(function_exists('WebdiskGetMediaMimeTypeByFileName'))
				{
					$fallbackType = WebdiskGetMediaMimeTypeByFileName($fileInfo['dateiname']);
					if($fallbackType !== '')
						$effectiveContentType = $fallbackType;
				}
			}

			// Vorschau: kein KB/s-Limit; PDF.js nutzt HTTP-Range (nur benötigte Bytes)
			if($isPdfView || $isMediaRangeView)
				$speedLimit = -1;
			else
				$speedLimit = $groupRow['wd_member_kbs'] <= 0 ? -1 : $groupRow['wd_member_kbs'];

			header('Pragma: public');
			header('Content-Type: ' . $effectiveContentType);
			header('Content-Disposition: ' . ($isInlineView ? 'inline' : 'attachment') . '; filename="' . addslashes($fileInfo['dateiname']) . '"');

			$fp = BMBlobStorage::CreateProvider($fileInfo['blobstorage'], $userRow['id'])->loadBlob(BMBLOB_TYPE_WEBDISK, $fileInfo['id']);
			if($isPdfView || $isMediaRangeView)
				$sentBytes = SendFileFPWithRange($fp, $fileInfo['size'], $speedLimit);
			else
			{
				header('Content-Length: ' . $fileInfo['size']);
				$sentBytes = SendFileFP($fp, $speedLimit);
			}

			if($sentBytes > 0)
			{
				$db->Query('UPDATE {pre}users SET traffic_down=traffic_down+? WHERE id=?',
					$sentBytes,
					$userRow['id']);
				Add2Stat('wd_down', ceil($sentBytes / 1024));
			}

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
 * load text file for preview/editor
 */
else if($_REQUEST['action'] == 'getFileText'
		&& isset($_REQUEST['id']))
{
	$fileInfo = $webdisk->GetFileInfo((int)$_REQUEST['id']);

	header('Content-Type: application/json; charset=' . $currentCharset);

	if($fileInfo === false
		|| !WebdiskIsTextPreviewFile($fileInfo['dateiname'], $fileInfo['contenttype']))
	{
		echo json_encode(array('ok' => false, 'error' => 'forbidden'));
		exit();
	}

	if($fileInfo['size'] > WebdiskGetTextEditMaxBytes())
	{
		echo json_encode(array(
			'ok'		=> false,
			'error'		=> 'toolarge',
			'maxBytes'	=> WebdiskGetTextEditMaxBytes(),
			'size'		=> (int)$fileInfo['size']
		));
		exit();
	}

	$fp = BMBlobStorage::createProvider($fileInfo['blobstorage'], $userRow['id'])->loadBlob(BMBLOB_TYPE_WEBDISK, $fileInfo['id']);
	if(!$fp)
	{
		echo json_encode(array('ok' => false, 'error' => 'internal'));
		exit();
	}

	$content = stream_get_contents($fp);
	fclose($fp);

	if($content === false || !WebdiskIsLikelyTextContent($content))
	{
		echo json_encode(array('ok' => false, 'error' => 'binary'));
		exit();
	}

	if($groupRow['traffic'] > 0)
	{
		$db->Query('UPDATE {pre}users SET traffic_down=traffic_down+? WHERE id=?',
			$fileInfo['size'],
			$userRow['id']);
		Add2Stat('wd_down', ceil($fileInfo['size'] / 1024));
	}

	echo json_encode(array(
		'ok'		=> true,
		'content'	=> $content,
		'size'		=> (int)$fileInfo['size'],
		'editable'	=> true
	), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
	exit();
}

/**
 * save text file from preview editor
 */
else if($_REQUEST['action'] == 'saveFileText'
		&& IsPOSTRequest()
		&& isset($_REQUEST['id']))
{
	$fileID = (int)$_REQUEST['id'];
	$content = isset($_POST['content']) ? $_POST['content'] : '';

	header('Content-Type: application/json; charset=' . $currentCharset);

	$result = $webdisk->UpdateFileContent($fileID, $content);

	if($result === true)
	{
		$fileInfo = $webdisk->GetFileInfo($fileID);
		echo json_encode(array(
			'ok'	=> true,
			'size'	=> $fileInfo ? (int)$fileInfo['size'] : strlen($content)
		));
		exit();
	}

	$errorMessages = array(
		'notfound'	=> $lang_user['wd_text_notfound'],
		'forbidden'	=> $lang_user['wd_fileforbidden'],
		'binary'	=> $lang_user['wd_text_binary'],
		'toolarge'	=> sprintf($lang_user['wd_text_toolarge'], TemplateSize(array('bytes' => WebdiskGetTextEditMaxBytes()), $tpl)),
		'nospace'	=> $lang_user['nospace'],
		'notraffic'	=> $lang_user['notraffic'],
		'internal'	=> $lang_user['internalerror']
	);

	echo json_encode(array(
		'ok'		=> false,
		'error'		=> $result,
		'message'	=> isset($errorMessages[$result]) ? $errorMessages[$result] : $lang_user['internalerror']
	));
	exit();
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
 * file share settings
 */
else if($_REQUEST['action'] == 'shareFile' && isset($_REQUEST['id']) && $groupRow['share'] == 'yes')
{
	$fileInfo = $webdisk->GetFileInfo((int)$_REQUEST['id']);
	if($fileInfo !== false)
	{
		$existingShare = $webdisk->GetFileShareInfo((int)$fileInfo['id']);
		$fileShared = $existingShare !== false && $webdisk->IsFileShared((int)$fileInfo['id']);
		$sharePasswordRequired = isset($bm_prefs['wd_share_pw_required']) && $bm_prefs['wd_share_pw_required'] == 'yes';
		$maxShareDays = isset($bm_prefs['wd_share_max_days']) ? max(0, (int)$bm_prefs['wd_share_max_days']) : 0;
		$shareExpiryRequired = isset($bm_prefs['wd_share_expiry_required']) && $bm_prefs['wd_share_expiry_required'] == 'yes';
		$shareExpiryMinDate = date('Y-m-d');
		$shareExpiryMaxDate = $maxShareDays > 0 ? date('Y-m-d', strtotime('+' . $maxShareDays . ' day')) : '';
		$shareUntilDate = '';
		if($existingShare && (int)$existingShare['share_until'] > 0)
			$shareUntilDate = date('Y-m-d', (int)$existingShare['share_until']);
		$filePW = $existingShare ? $existingShare['share_pw'] : '';
		if($sharePasswordRequired && trim($filePW) == '')
			$filePW = WebdiskGenerateSharePassword(12);
		$fileToken = $existingShare ? $existingShare['token'] : '';
		$fileShareURL = $fileToken != ''
			? sprintf('%sshare/?user=%s&file=%s', $bm_prefs['selfurl'], urlencode($userRow['email']), urlencode($fileToken))
			: '';
		$shareSingleUse = $existingShare ? ((int)$existingShare['single_use'] === 1 || $existingShare['single_use'] === true) : false;

		$tpl->assign('pageTitle',		$lang_user['sharing']);
		$tpl->assign('id', 				$fileInfo['id']);
		$tpl->assign('folderID', 		(int)$fileInfo['ordner']);
		$tpl->assign('fileName', 		$fileInfo['dateiname']);
		$tpl->assign('fileShared', 		$fileShared);
		$tpl->assign('filePW', 			$filePW);
		$tpl->assign('shareSingleUse',	$shareSingleUse);
		$tpl->assign('fileShareURL',	$fileShareURL);
		$tpl->assign('sharePasswordRequired', $sharePasswordRequired);
		$tpl->assign('shareUntilDate',	$shareUntilDate);
		$tpl->assign('shareExpiryRequired', $shareExpiryRequired);
		$tpl->assign('shareExpiryMaxDays', $maxShareDays);
		$tpl->assign('shareExpiryMinDate', $shareExpiryMinDate);
		$tpl->assign('shareExpiryMaxDate', $shareExpiryMaxDate);
		$tpl->assign('pageContent', 	'li/webdisk.file.share.tpl');
		$tpl->display('li/index.tpl');
	}
}

/**
 * save file share settings
 */
else if($_REQUEST['action'] == 'saveFileShareSettings' && isset($_REQUEST['id']) && $groupRow['share'] == 'yes'
		&& IsPOSTRequest())
{
	$fileID = (int)$_REQUEST['id'];
	$fileInfo = $webdisk->GetFileInfo($fileID);
	if($fileInfo === false)
		die('File not found');

	$shareFile = isset($_REQUEST['shareFile']);
	$sharePW = isset($_REQUEST['sharePW']) ? $_REQUEST['sharePW'] : '';
	$shareSingleUse = isset($_REQUEST['shareSingleUse']);
	$sharePasswordRequired = isset($bm_prefs['wd_share_pw_required']) && $bm_prefs['wd_share_pw_required'] == 'yes';
	$shareUntil = 0;
	$maxShareDays = isset($bm_prefs['wd_share_max_days']) ? max(0, (int)$bm_prefs['wd_share_max_days']) : 0;
	$shareExpiryRequired = isset($bm_prefs['wd_share_expiry_required']) && $bm_prefs['wd_share_expiry_required'] == 'yes';
	$shareExpiryMinDate = date('Y-m-d');
	$shareExpiryMaxDate = $maxShareDays > 0 ? date('Y-m-d', strtotime('+' . $maxShareDays . ' day')) : '';
	$shareUntilRaw = isset($_REQUEST['shareUntil']) ? trim($_REQUEST['shareUntil']) : '';
	$shareError = '';

	if($shareFile)
	{
		if($sharePasswordRequired && trim($sharePW) == '')
			$shareError = $lang_user['wd_share_pw_required_err'];
		else if($sharePasswordRequired && strlen($sharePW) < 12)
			$shareError = $lang_user['wd_share_pw_minlength_err'];

		if($shareUntilRaw == '' && $shareExpiryRequired && $shareError == '')
			$shareError = $lang_user['wd_share_expiry_required'];
		else if($shareUntilRaw != '' && $shareError == '')
		{
			$shareUntil = strtotime($shareUntilRaw . ' 23:59:59');
			if($shareUntil === false)
				$shareError = $lang_user['wd_share_expiry_invalid'];
			else
			{
				$todayStart = strtotime(date('Y-m-d') . ' 00:00:00');
				if($shareUntil < $todayStart)
					$shareError = $lang_user['wd_share_expiry_invalid'];
				else if($maxShareDays > 0)
				{
					$maxAllowed = strtotime('+' . $maxShareDays . ' day', $todayStart) + 86399;
					if($shareUntil > $maxAllowed)
						$shareError = sprintf($lang_user['wd_share_expiry_maxdays_err'], $maxShareDays);
				}
			}
		}
	}

	if($shareError != '')
	{
		$existingShare = $webdisk->GetFileShareInfo($fileID);
		$fileToken = $existingShare ? $existingShare['token'] : '';
		$fileShareURL = $fileToken != ''
			? sprintf('%sshare/?user=%s&file=%s', $bm_prefs['selfurl'], urlencode($userRow['email']), urlencode($fileToken))
			: '';

		$tpl->assign('pageTitle',		$lang_user['sharing']);
		$tpl->assign('id', 				$fileInfo['id']);
		$tpl->assign('folderID', 		(int)$fileInfo['ordner']);
		$tpl->assign('fileName', 		$fileInfo['dateiname']);
		$tpl->assign('fileShared', 		$shareFile);
		$tpl->assign('filePW', 			(trim($sharePW) == '' && $sharePasswordRequired) ? WebdiskGenerateSharePassword(12) : $sharePW);
		$tpl->assign('shareSingleUse',	$shareSingleUse);
		$tpl->assign('fileShareURL',	$fileShareURL);
		$tpl->assign('sharePasswordRequired', $sharePasswordRequired);
		$tpl->assign('shareUntilDate',	$shareUntilRaw);
		$tpl->assign('shareExpiryRequired', $shareExpiryRequired);
		$tpl->assign('shareExpiryMaxDays', $maxShareDays);
		$tpl->assign('shareExpiryMinDate', $shareExpiryMinDate);
		$tpl->assign('shareExpiryMaxDate', $shareExpiryMaxDate);
		$tpl->assign('shareError',		$shareError);
		$tpl->assign('pageContent', 	'li/webdisk.file.share.tpl');
		$tpl->display('li/index.tpl');
		exit();
	}

	if($shareFile)
	{
		$fileToken = $webdisk->SetFileShareSettings($fileID, true, $sharePW, $shareUntil, $shareSingleUse);
		$fileShareURL = sprintf('%sshare/?user=%s&file=%s', $bm_prefs['selfurl'], urlencode($userRow['email']), urlencode($fileToken));
		WebdiskStoreShareFeedback((int)$fileInfo['ordner'], $fileInfo['dateiname'], $fileShareURL);
	}
	else
		$webdisk->StopFileShare($fileID);

	header('Location: webdisk.php?folder=' . (int)$fileInfo['ordner'] . '&sid=' . session_id());
}

/**
 * folder share settings
 */
else if($_REQUEST['action'] == 'shareFolder' && isset($_REQUEST['id']) && $groupRow['share'] == 'yes')
{
	$folderInfo = $webdisk->GetFolderInfo((int)$_REQUEST['id']);
	if($folderInfo !== false)
	{
		$sharePasswordRequired = isset($bm_prefs['wd_share_pw_required']) && $bm_prefs['wd_share_pw_required'] == 'yes';
		$maxShareDays = isset($bm_prefs['wd_share_max_days']) ? max(0, (int)$bm_prefs['wd_share_max_days']) : 0;
		$shareExpiryRequired = isset($bm_prefs['wd_share_expiry_required']) && $bm_prefs['wd_share_expiry_required'] == 'yes';
		$shareExpiryMinDate = date('Y-m-d');
		$shareExpiryMaxDate = $maxShareDays > 0 ? date('Y-m-d', strtotime('+' . $maxShareDays . ' day')) : '';
		$shareUntilDate = '';
		if(isset($folderInfo['share_until']) && (int)$folderInfo['share_until'] > 0)
			$shareUntilDate = date('Y-m-d', (int)$folderInfo['share_until']);
		$folderPW = $folderInfo['share_pw'];
		if($sharePasswordRequired && trim($folderPW) == '')
			$folderPW = WebdiskGenerateSharePassword(12);

		$tpl->assign('pageTitle',		$lang_user['sharing']);
		$tpl->assign('id', 				$folderInfo['id']);
		$tpl->assign('folderName', 		$folderInfo['titel']);
		$tpl->assign('folderShared', 	$folderInfo['share'] == 'yes');
		$tpl->assign('folderPW', 		$folderPW);
		$tpl->assign('sharePasswordRequired', $sharePasswordRequired);
		$tpl->assign('shareUntilDate',	$shareUntilDate);
		$tpl->assign('shareExpiryRequired', $shareExpiryRequired);
		$tpl->assign('shareExpiryMaxDays', $maxShareDays);
		$tpl->assign('shareExpiryMinDate', $shareExpiryMinDate);
		$tpl->assign('shareExpiryMaxDate', $shareExpiryMaxDate);
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
	$folderID = (int)$_REQUEST['id'];
	$shareFolder = isset($_REQUEST['shareFolder']);
	$sharePW = isset($_REQUEST['sharePW']) ? $_REQUEST['sharePW'] : '';
	$sharePasswordRequired = isset($bm_prefs['wd_share_pw_required']) && $bm_prefs['wd_share_pw_required'] == 'yes';
	$shareUntil = 0;
	$maxShareDays = isset($bm_prefs['wd_share_max_days']) ? max(0, (int)$bm_prefs['wd_share_max_days']) : 0;
	$shareExpiryRequired = isset($bm_prefs['wd_share_expiry_required']) && $bm_prefs['wd_share_expiry_required'] == 'yes';
	$shareExpiryMinDate = date('Y-m-d');
	$shareExpiryMaxDate = $maxShareDays > 0 ? date('Y-m-d', strtotime('+' . $maxShareDays . ' day')) : '';
	$shareUntilRaw = isset($_REQUEST['shareUntil']) ? trim($_REQUEST['shareUntil']) : '';
	$shareError = '';

	if($shareFolder)
	{
		if($sharePasswordRequired && trim($sharePW) == '')
			$shareError = $lang_user['wd_share_pw_required_err'];
		else if($sharePasswordRequired && strlen($sharePW) < 12)
			$shareError = $lang_user['wd_share_pw_minlength_err'];

		if($shareUntilRaw == '' && $shareExpiryRequired && $shareError == '')
			$shareError = $lang_user['wd_share_expiry_required'];
		else if($shareUntilRaw != '' && $shareError == '')
		{
			$shareUntil = strtotime($shareUntilRaw . ' 23:59:59');
			if($shareUntil === false)
				$shareError = $lang_user['wd_share_expiry_invalid'];
			else
			{
				$todayStart = strtotime(date('Y-m-d') . ' 00:00:00');
				if($shareUntil < $todayStart)
					$shareError = $lang_user['wd_share_expiry_invalid'];
				else if($maxShareDays > 0)
				{
					$maxAllowed = strtotime('+' . $maxShareDays . ' day', $todayStart) + 86399;
					if($shareUntil > $maxAllowed)
						$shareError = sprintf($lang_user['wd_share_expiry_maxdays_err'], $maxShareDays);
				}
			}
		}
	}

	if($shareError != '')
	{
		$folderInfo = $webdisk->GetFolderInfo($folderID);
		if($folderInfo !== false)
		{
			$tpl->assign('pageTitle',		$lang_user['sharing']);
			$tpl->assign('id', 				$folderInfo['id']);
			$tpl->assign('folderName', 		$folderInfo['titel']);
			$tpl->assign('folderShared', 	$shareFolder);
			$tpl->assign('folderPW', 		(trim($sharePW) == '' && $sharePasswordRequired) ? WebdiskGenerateSharePassword(12) : $sharePW);
			$tpl->assign('sharePasswordRequired', $sharePasswordRequired);
			$tpl->assign('shareUntilDate',	$shareUntilRaw);
			$tpl->assign('shareExpiryRequired', $shareExpiryRequired);
			$tpl->assign('shareExpiryMaxDays', $maxShareDays);
			$tpl->assign('shareExpiryMinDate', $shareExpiryMinDate);
			$tpl->assign('shareExpiryMaxDate', $shareExpiryMaxDate);
			$tpl->assign('shareError',		$shareError);
			$tpl->assign('pageContent', 	'li/webdisk.share.tpl');
			$tpl->display('li/index.tpl');
			exit();
		}
	}

	$webdisk->SetShareSettings($folderID, $shareFolder, $sharePW, $shareUntil);
	header('Location: webdisk.php?folder=' . (int)$_REQUEST['id'] . '&sid=' . session_id());
}

/**
 * stop folder share (one click from sidebar)
 */
else if($_REQUEST['action'] == 'stopShare' && isset($_REQUEST['id']) && $groupRow['share'] == 'yes')
{
	$folderID = (int)$_REQUEST['id'];
	$folderInfo = $webdisk->GetFolderInfo($folderID);

	if($folderInfo !== false && $folderInfo['share'] == 'yes')
		$webdisk->SetShareSettings($folderID, false, '');

	$redirectFolder = isset($_REQUEST['folder']) ? (int)$_REQUEST['folder'] : $folderID;
	header('Location: webdisk.php?folder=' . $redirectFolder . '&sid=' . session_id());
}

/**
 * stop file share (one click from sidebar)
 */
else if($_REQUEST['action'] == 'stopFileShare' && isset($_REQUEST['id']) && $groupRow['share'] == 'yes')
{
	$fileID = (int)$_REQUEST['id'];
	$fileInfo = $webdisk->GetFileInfo($fileID);
	if($fileInfo !== false)
		$webdisk->StopFileShare($fileID);

	$redirectFolder = $fileInfo !== false ? (int)$fileInfo['ordner'] : (isset($_REQUEST['folder']) ? (int)$_REQUEST['folder'] : 0);
	header('Location: webdisk.php?folder=' . $redirectFolder . '&sid=' . session_id());
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
	$maxUpload = WebdiskGetMaxUploadFileSize($usedSpace, $spaceLimit, $groupRow, $userRow);

	if($mimeType == '' || $mimeType == 'application/octet-stream')
		$mimeType = GuessMIMEType($fileName);

	if($fileSize <= 0)
	{
		$msg = $lang_user['wd_upload_nofile'];
	}
	else if($maxUpload <= 0)
	{
		$msg = $lang_user['nospace'];
	}
	else if($fileSize > $maxUpload)
	{
		$msg = sprintf($lang_user['wd_filetoolarge'],
			$fileName,
			WebdiskFormatBytes($maxUpload));
	}
	else if($webdisk->Forbidden($fileName, $mimeType))
	{
		$msg = $lang_user['wd_fileforbidden'];
	}
	else if($groupRow['traffic'] <= 0 || ($userRow['traffic_down']+$userRow['traffic_up']+$fileSize) <= $groupRow['traffic']+$userRow['traffic_add'])
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

	if($msg === '1')
		http_response_code(200);
	else
		http_response_code(400);
	echo $msg;
}

/**
 * upload files
 */
else if($_REQUEST['action'] == 'uploadFiles'
		&& IsPOSTRequest())
{
	$error = $success = array();
	$maxUpload = WebdiskGetMaxUploadFileSize($usedSpace, $spaceLimit, $groupRow, $userRow);

	if(WebdiskIsUploadPostTooLarge())
	{
		$error[''] = sprintf($lang_user['wd_upload_posttoolarge'],
			WebdiskFormatBytes(ParsePHPSize(ini_get('post_max_size'))));
	}
	else
	{
		foreach(WebdiskCollectUploadFiles() as $entry)
		{
			$fileName = $entry['name'];
			$errMsg = WebdiskProcessUploadedFileEntry($webdisk, $folderID, $entry, $maxUpload, $usedSpace, $spaceLimit, $groupRow, $userRow);

			if($errMsg !== null)
				$error[$fileName] = $errMsg;
			else
				$success[$fileName] = $lang_user['success'];
		}
	}

	if(count($error) > 0 || count($success) > 0)
		WebdiskStoreUploadFeedback($folderID, $error, $success);

	header('Location: webdisk.php?folder=' . $folderID . '&sid=' . session_id());
	exit();
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