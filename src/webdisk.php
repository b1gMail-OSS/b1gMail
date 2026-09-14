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

/**
 * Build the public share URL for a user (and optional file token).
 * Emits the modern path-style URL /share/<email>[?file=<token>], which
 * is served by share/index.php through the .htaccess rewrite. The
 * legacy /share/?user=<email> URL keeps working (PATH_INFO / REQUEST_URI
 * fallback in share/index.php) — links printed / mailed years ago do
 * not break.
 *
 * @param string $selfurl   Absolute install URL (trailing slash tolerated)
 * @param string $email     User e-mail
 * @param string $fileToken Optional file-share token
 * @return string
 */
function WebdiskGetPublicShareUrl($selfurl, $email, $fileToken = '')
{
	// keep '@' readable in the URL — functionally equivalent, RFC 3986 §3.3
	$prettyEmail = str_replace('%40', '@', rawurlencode((string)$email));
	$url = rtrim((string)$selfurl, '/') . '/share/' . $prettyEmail;
	if($fileToken !== '')
		$url .= '?file=' . rawurlencode((string)$fileToken);
	return $url;
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
 * Validate & persist public-share (read-only link) settings for a folder.
 * Shared between the legacy full-page `saveShareSettings` handler and
 * the new combined `share` overlay dialog.
 *
 * @param BMWebdisk $webdisk
 * @param int       $folderID
 * @param array     $bmPrefs
 * @param array     $langUser
 * @return string   Empty string on success, else localized error text
 */
function WebdiskApplyPublicShareForm($webdisk, $folderID, $bmPrefs, $langUser)
{
	$folderID     = (int)$folderID;
	$shareEnabled = isset($_REQUEST['shareFolder']);
	$sharePW      = isset($_REQUEST['sharePW']) ? (string)$_REQUEST['sharePW'] : '';
	$shareUntilRaw = isset($_REQUEST['shareUntil']) ? trim((string)$_REQUEST['shareUntil']) : '';

	$pwRequired    = isset($bmPrefs['wd_share_pw_required']) && $bmPrefs['wd_share_pw_required'] == 'yes';
	$maxShareDays  = isset($bmPrefs['wd_share_max_days']) ? max(0, (int)$bmPrefs['wd_share_max_days']) : 0;
	$expiryRequired = isset($bmPrefs['wd_share_expiry_required']) && $bmPrefs['wd_share_expiry_required'] == 'yes';

	$shareUntil = 0;

	if($shareEnabled)
	{
		if($pwRequired && trim($sharePW) == '')
			return $langUser['wd_share_pw_required_err'];
		if($pwRequired && strlen($sharePW) < 12)
			return $langUser['wd_share_pw_minlength_err'];

		if($shareUntilRaw == '' && $expiryRequired)
			return $langUser['wd_share_expiry_required'];

		if($shareUntilRaw != '')
		{
			$shareUntil = strtotime($shareUntilRaw . ' 23:59:59');
			if($shareUntil === false)
				return $langUser['wd_share_expiry_invalid'];

			$todayStart = strtotime(date('Y-m-d') . ' 00:00:00');
			if($shareUntil < $todayStart)
				return $langUser['wd_share_expiry_invalid'];

			if($maxShareDays > 0)
			{
				$maxAllowed = strtotime('+' . $maxShareDays . ' day', $todayStart) + 86399;
				if($shareUntil > $maxAllowed)
					return sprintf($langUser['wd_share_expiry_maxdays_err'], $maxShareDays);
			}
		}
	}

	$webdisk->SetShareSettings($folderID, $shareEnabled, $sharePW, $shareUntil);
	return '';
}

/**
 * @param BMWebdisk $viewerDisk
 * @param int        $folderID
 * @param bool       $needWrite
 * @return array|false
 */
function WebdiskOpenFolder($viewerDisk, $folderID, $needWrite = false)
{
	$access = $viewerDisk->ResolveFolderAccess($folderID);
	if($access === false)
		return(false);
	if($needWrite && !empty($access['readonly']))
		return(false);

	$disk = ((int)$access['ownerId'] === (int)$viewerDisk->_userID)
		? $viewerDisk
		: _new('BMWebdisk', array($access['ownerId']));
	return(array(
		'disk'			=> $disk,
		'access'		=> $access,
		'folderID'		=> (int)$access['realFolder'],
		'viewFolderID'	=> (int)$folderID
	));
}

/**
 * @param BMWebdisk $viewerDisk
 * @param int        $fileID
 * @param bool       $needWrite
 * @return array|false
 */
function WebdiskOpenFile($viewerDisk, $fileID, $needWrite = false)
{
	$access = $viewerDisk->ResolveFileAccess($fileID);
	if($access === false)
		return(false);
	if($needWrite && !empty($access['readonly']))
		return(false);

	$disk = ((int)$access['ownerId'] === (int)$viewerDisk->_userID)
		? $viewerDisk
		: _new('BMWebdisk', array($access['ownerId']));
	$info = $disk->GetFileInfo($fileID);
	if($info === false)
		return(false);

	return(array(
		'disk'		=> $disk,
		'access'	=> $access,
		'file'		=> $info
	));
}

function WebdiskDeny()
{
	global $tpl, $lang_user;
	$tpl->assign('msg', $lang_user['internalerror']);
	$tpl->assign('pageContent', 'li/error.tpl');
	$tpl->display('li/index.tpl');
	exit();
}

/**
 * @param BMTemplate $tpl
 * @param BMWebdisk  $webdisk
 */
function WebdiskAssignFolderLists($tpl, $webdisk)
{
	global $userRow;

	$pageMenu = $webdisk->GetPageFolderList();
	list($ownFolderList, $sharedFolderMenus) = $webdisk->SplitSidebarFolderMenus($pageMenu);
	$tpl->assign('folderList', $pageMenu);
	$tpl->assign('ownFolderList', $ownFolderList);
	$tpl->assign('sharedFolderMenus', $sharedFolderMenus);
	$tpl->assign('webdiskEmail', DecodeEMail($userRow['email']));
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
$viewFolderID 	= !isset($_REQUEST['folder']) ? 0 : (int)$_REQUEST['folder'];
$wdFolderCtx 	= WebdiskOpenFolder($webdisk, $viewFolderID, false);
if($wdFolderCtx === false)
{
	$viewFolderID = 0;
	$wdFolderCtx = WebdiskOpenFolder($webdisk, 0, false);
}
$folderID 		= $viewFolderID;
$wdReadonly 	= !empty($wdFolderCtx['access']['readonly']);
$wdForeign 		= ((int)$wdFolderCtx['access']['ownerId'] !== (int)$userRow['id']);
$wdCanLeave 	= !empty($wdFolderCtx['access']['can_leave']);
$wdOwnerDisk 	= $wdFolderCtx['disk'];
$wdRealFolderID = $wdFolderCtx['folderID'];
$folderPath 	= $webdisk->GetViewFolderPath($viewFolderID);
$spaceLimit 	= $webdisk->GetSpaceLimit();
$usedSpace 		= $webdisk->GetUsedSpace();
$wdCrumbTitle 	= $lang_user['webdisk'];
if($wdForeign)
{
	$wdSharedFolders = $webdisk->GetAccessibleSharedFolders();
	$wdRootId = (int)$wdFolderCtx['access']['share_root_id'];
	if(isset($wdSharedFolders[$wdRootId]))
		$wdCrumbTitle = $wdSharedFolders[$wdRootId]['titel'];
}
$tpl->assign('pageMenuFile', 	'li/webdisk.folderbar.tpl');
$tpl->assign('pageToolbarFile', 'li/webdisk.toolbar.tpl');
WebdiskAssignFolderLists($tpl, $webdisk);
$tpl->assign('viewMode', 		($viewMode = $thisUser->GetPref('webdiskViewMode')) === false ? 'icons' : $viewMode);
$tpl->assign('spaceUsed', 		$usedSpace);
$tpl->assign('trafficUsed', 	$userRow['traffic_down'] + $userRow['traffic_up']);
$tpl->assign('clipboard', 		isset($_SESSION['clipboard']) && is_array($_SESSION['clipboard']) && count($_SESSION['clipboard']) > 0);
$tpl->assign('spaceLimit', 		$spaceLimit);
$tpl->assign('trafficLimit', 	$groupRow['traffic'] > 0 ? $groupRow['traffic'] + $userRow['traffic_add'] : 0);
$tpl->assign('folderID', 		$viewFolderID);
$tpl->assign('currentPath', 	$folderPath);
$tpl->assign('userAgent',		$_SERVER['HTTP_USER_AGENT']);
$tpl->assign('dndKey',			isset($_COOKIE['sessionSecret_' . substr(session_id(), 0, 16)]) ? $_COOKIE['sessionSecret_' . substr(session_id(), 0, 16)] : '');
$tpl->assign('allowShare',		$groupRow['share'] == 'yes' && !$wdForeign);
$tpl->assign('webdiskReadonly',	$wdReadonly);
$tpl->assign('webdiskIsOwner',	!$wdForeign);
$tpl->assign('webdiskCanLeave',	$wdCanLeave);
$tpl->assign('webdiskBreadcrumbRootId', $wdForeign ? (int)$wdFolderCtx['access']['share_root_id'] : 0);
$tpl->assign('webdiskBreadcrumbRootTitle', $wdCrumbTitle);
$tpl->assign('hotkeys',			$thisUser->GetPref('hotkeys'));

/**
 * folder view
 */
if($_REQUEST['action'] == 'folder')
{
	// F2: massAction is state-changing (bulk delete, bulk move, bulk
	// download-as-zip). Require a CSRF token so the GET fallback
	// via ?folders[]=&files[]= cannot be abused from a crafted link.
	if(isset($_REQUEST['massAction']))
	{
		CsrfEnforceOnStateChange();
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

		if($_REQUEST['massAction'] == 'delete' && !$wdReadonly)
		{
			// F9: mass-delete in a write-shared context — record actor,
			// owner, item ids for later forensic reconstruction. The
			// $wdOwnerDisk is instantiated in the folder owner's
			// context, so its _userID is the owner.
			foreach($folderIDs as $theFolderID)
			{
				bmShareAuditLog((int)$userRow['id'], (int)$wdOwnerDisk->_userID,
					'webdisk_folder_delete', (int)$theFolderID, '');
				$wdOwnerDisk->DeleteFolder((int)$theFolderID);
			}
			foreach($fileIDs as $theFileID)
			{
				bmShareAuditLog((int)$userRow['id'], (int)$wdOwnerDisk->_userID,
					'webdisk_file_delete', (int)$theFileID, '');
				$wdOwnerDisk->DeleteFile((int)$theFileID);
			}
			WebdiskAssignFolderLists($tpl, $webdisk);
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
				$folderInfo 	= $wdOwnerDisk->GetFolderInfo(end($folderIDs));
				if($folderInfo)
					$zipName 	= $folderInfo['titel'];
			}
			else if(count($folderIDs) == 0 && count($fileIDs) == 1)
			{
				$fileInfo		= $wdOwnerDisk->GetFileInfo(end($fileIDs));
				if($fileInfo)
					$zipName	= $fileInfo['dateiname'];
			}
			else
			{
				$folderInfo = false;

				if(count($folderIDs) > 0)
				{
					$folderInfo		= $wdOwnerDisk->GetFolderInfo(end($folderIDs));

					if($folderInfo && $folderInfo['parent'] > 0)
						$folderInfo = $wdOwnerDisk->GetFolderInfo($folderInfo['parent']);
					else
						$folderInfo = false;
				}
				else if(count($fileIDs) > 0)
				{
					$fileInfo		= $wdOwnerDisk->GetFileInfo(end($fileIDs));

					if($fileInfo && $fileInfo['ordner'])
						$folderInfo = $wdOwnerDisk->GetFolderInfo($fileInfo['ordner']);
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
				$wdOwnerDisk->ZipFolder((int)$theFolderID, $zip);
			foreach($fileIDs as $theFileID)
				$wdOwnerDisk->ZipFile((int)$theFileID, $zip);
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

	WebdiskApplyUploadFeedback($viewFolderID, $tpl);
	WebdiskApplyShareFeedback($viewFolderID, $tpl);

	$folderInfo 	= $wdRealFolderID != 0 ? $wdOwnerDisk->GetFolderInfo($wdRealFolderID) : false;
	$folderContent 	= $wdOwnerDisk->GetFolderContent($wdRealFolderID);
	$shareURL		= WebdiskGetPublicShareUrl($bm_prefs['selfurl'], $userRow['email']);

	if(!$wdForeign && $folderInfo !== false && $folderInfo['share'] == 'yes')
	{
		$shareMail = $lang_custom['share_text'];
		$shareMail = str_replace('%%url%%', $shareURL, $shareMail);
		$shareMail = str_replace('%%firstname%%', $thisUser->_row['vorname'], $shareMail);
		$shareMail = str_replace('%%lastname%%', $thisUser->_row['nachname'], $shareMail);
		$tpl->assign('shareMail', $shareMail);
		$tpl->assign('shareMailSubject', $lang_custom['share_sub']);
	}

	$tpl->assign('shareURL', $shareURL);
	$tpl->assign('isShared', !$wdForeign && $folderInfo !== false && $folderInfo['share'] == 'yes');
	$wdUploadSpaceUsed = $wdForeign ? $wdOwnerDisk->GetUsedSpace() : $usedSpace;
	$wdUploadSpaceLimit = $wdForeign ? $wdOwnerDisk->GetSpaceLimit() : $spaceLimit;
	$webdiskMaxUploadBytes = $wdReadonly ? 0 : WebdiskGetMaxUploadFileSize($wdUploadSpaceUsed, $wdUploadSpaceLimit, $groupRow, $userRow);
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
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');
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
		$ctx = WebdiskOpenFolder($webdisk, (int)$_REQUEST['id'], false);
		$_info = $ctx ? $ctx['disk']->GetFolderInfo($ctx['folderID']) : false;
		$type = 'folder';
		$ext = ($_info && $_info['share'] == 'yes') ? '.SHAREDFOLDER' : '.FOLDER';
	}
	else if($type == WEBDISK_ITEM_FILE)
	{
		$ctx = WebdiskOpenFile($webdisk, (int)$_REQUEST['id'], false);
		$_info = $ctx ? $ctx['file'] : false;
		$type = 'file';
		if($_info)
			$_info['titel'] = $_info['dateiname'];
	}

	if(!$_info) die('Item not found');

	if($type == 'file')
	{
		$dotPos = strrchr($_info['dateiname'], '.');
		if($dotPos !== false)
			$ext = substr($dotPos, 1);
		else
			$ext = '?';
	}

	$infoDisk = $ctx['disk'];
	$info = array(
		'type'			=> (int)$_REQUEST['type'],
		'title'			=> $_info['titel'],
		'shortTitle'	=> TemplateText(array('cut' => 20, 'value' => $_info['titel']), $tpl),
		'size'			=> $type == 'folder'
							? TemplateSize(array('bytes' => $infoDisk->GetFolderTreeSize((int)$_info['id'])), $tpl)
							: TemplateSize(array('bytes' => $_info['size']), $tpl),
		'ext'			=> $ext,
		'created'		=> TemplateDate(array('timestamp' => $_info['created'], 'nice' => true), $tpl),
		'uploader'		=> ($type == 'file') ? $infoDisk->GetFileUploaderEmail($_info) : '',
		'id'			=> $_info['id'],
		'share'			=> $type == 'folder'
							? ($_info['share'] == 'yes')
							: $infoDisk->IsFileShared((int)$_info['id']),
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

	$stats = $wdOwnerDisk->GetSelectionStats($folderIDs, $fileIDs);

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
	$ctx = WebdiskOpenFile($webdisk, (int)$_REQUEST['id'], false);
	if($ctx === false || !isset($groupRow['wd_thumbnails']) || $groupRow['wd_thumbnails'] != 'yes' || !function_exists('imagecreatetruecolor'))
	{
		header('HTTP/1.1 404 Not Found');
		header('Cache-Control: no-store');
		exit();
	}

	$fileInfo = $ctx['file'];
	if(!WebdiskThumbnailIsSupportedType($fileInfo))
	{
		header('HTTP/1.1 404 Not Found');
		header('Cache-Control: no-store');
		exit();
	}

	$cachePath = WebdiskEnsureThumbnail($fileInfo, $ctx['access']['ownerId']);
	if($cachePath === false)
	{
		header('HTTP/1.1 404 Not Found');
		header('Cache-Control: no-store');
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
	$ctx = WebdiskOpenFile($webdisk, (int)$_REQUEST['id'], false);
	$fileInfo = $ctx ? $ctx['file'] : false;
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
			SendContentDispositionHeader($isInlineView ? 'inline' : 'attachment', $fileInfo['dateiname']);

			$fp = BMBlobStorage::CreateProvider($fileInfo['blobstorage'], $ctx['access']['ownerId'])->loadBlob(BMBLOB_TYPE_WEBDISK, $fileInfo['id']);
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
	$ctx = WebdiskOpenFile($webdisk, (int)$_REQUEST['id'], false);
	$fileInfo = $ctx ? $ctx['file'] : false;

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

	$fp = BMBlobStorage::createProvider($fileInfo['blobstorage'], $ctx['access']['ownerId'])->loadBlob(BMBLOB_TYPE_WEBDISK, $fileInfo['id']);
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

	$ctx = WebdiskOpenFile($webdisk, $fileID, true);
	if($ctx === false)
	{
		echo json_encode(array('ok' => false, 'error' => 'forbidden'));
		exit();
	}

	$result = $ctx['disk']->UpdateFileContent($fileID, $content);

	if($result === true)
	{
		$fileInfo = $ctx['disk']->GetFileInfo($fileID);
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
	// F2: creating a folder is state-changing — CSRF-token required.
	CsrfEnforceOnStateChange();
	$folderName = trim($_REQUEST['folderName']);
	$ctx = WebdiskOpenFolder($webdisk, $viewFolderID, true);

	if($ctx === false || $ctx['disk']->FolderExists($ctx['folderID'], $folderName) || strlen($folderName) == 0)
	{
		if(isset($_REQUEST['rpc']))
			die('0');
		$tpl->assign('msg', $lang_user['foldererror']);
		$tpl->assign('pageContent', 'li/error.tpl');
		$tpl->display('li/index.tpl');
	}
	else
	{
		$ctx['disk']->CreateFolder($ctx['folderID'], $folderName);

		if(isset($_REQUEST['rpc']))
			die('1');
		else
			header('Location: ' . SessionUrl('webdisk.php?folder=' . $viewFolderID));
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
			? WebdiskGetPublicShareUrl($bm_prefs['selfurl'], $userRow['email'], $fileToken)
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
			? WebdiskGetPublicShareUrl($bm_prefs['selfurl'], $userRow['email'], $fileToken)
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
		$fileShareURL = WebdiskGetPublicShareUrl($bm_prefs['selfurl'], $userRow['email'], $fileToken);
		WebdiskStoreShareFeedback((int)$fileInfo['ordner'], $fileInfo['dateiname'], $fileShareURL);
	}
	else
		$webdisk->StopFileShare($fileID);

	header('Location: ' . SessionUrl('webdisk.php?folder=' . (int)$fileInfo['ordner']));
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
	header('Location: ' . SessionUrl('webdisk.php?folder=' . (int)$_REQUEST['id']));
}

/**
 * stop folder share (one click from sidebar) — F2: CSRF-token required.
 * Stopping a public share invalidates existing links / bookmarks; a
 * CSRF <img> href could silently kill an active share without the
 * owner noticing.
 */
else if($_REQUEST['action'] == 'stopShare' && isset($_REQUEST['id'])
	&& $groupRow['share'] == 'yes')
{
	CsrfEnforceOnStateChange();
	$folderID = (int)$_REQUEST['id'];
	$folderInfo = $webdisk->GetFolderInfo($folderID);

	if($folderInfo !== false && $folderInfo['share'] == 'yes')
		$webdisk->SetShareSettings($folderID, false, '');

	$redirectFolder = isset($_REQUEST['folder']) ? (int)$_REQUEST['folder'] : $folderID;
	header('Location: ' . SessionUrl('webdisk.php?folder=' . $redirectFolder));
}

/**
 * stop file share (one click from sidebar) — F2: CSRF-token required.
 */
else if($_REQUEST['action'] == 'stopFileShare' && isset($_REQUEST['id'])
	&& $groupRow['share'] == 'yes')
{
	CsrfEnforceOnStateChange();
	$fileID = (int)$_REQUEST['id'];
	$fileInfo = $webdisk->GetFileInfo($fileID);
	if($fileInfo !== false)
		$webdisk->StopFileShare($fileID);

	$redirectFolder = $fileInfo !== false ? (int)$fileInfo['ordner'] : (isset($_REQUEST['folder']) ? (int)$_REQUEST['folder'] : 0);
	header('Location: ' . SessionUrl('webdisk.php?folder=' . $redirectFolder));
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

		header('Location: ' . SessionUrl('webdisk.php?folder='.$folderID));
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
	// F2: rename is state-changing — CSRF-token required.
	CsrfEnforceOnStateChange();
	$newName = trim($_REQUEST['name']);

	if($_REQUEST['type'] == WEBDISK_ITEM_FILE)
	{
		$ctx = WebdiskOpenFile($webdisk, (int)$_REQUEST['id'], true);
		if($ctx !== false)
		{
			$fileInfo = $ctx['file'];
			$parentReal = $ctx['access']['realFolder'];
			if($newName == $fileInfo['dateiname']
				|| strlen($newName) < 1
				|| $ctx['disk']->FileExists($parentReal, $newName))
				die($fileInfo['dateiname']);
			die($ctx['disk']->RenameFile((int)$_REQUEST['id'], $newName) ? $newName : $fileInfo['dateiname']);
		}
	}
	else if($_REQUEST['type'] == WEBDISK_ITEM_FOLDER)
	{
		$ctx = WebdiskOpenFolder($webdisk, (int)$_REQUEST['id'], true);
		if($ctx !== false)
		{
			$folderInfo = $ctx['disk']->GetFolderInfo($ctx['folderID']);
			if($folderInfo)
			{
				if($newName == $folderInfo['titel']
					|| strlen($newName) < 1
					|| $ctx['disk']->FolderExists($folderInfo['parent'], $newName))
					die($folderInfo['titel']);
				die($ctx['disk']->RenameFolder($ctx['folderID'], $newName) ? $newName : $folderInfo['titel']);
			}
		}
	}
}

/**
 * delete file — F2: CSRF-token required. Deletion of a file or folder is
 * destructive; a CSRF <img>/<a> GET could otherwise wipe arbitrary
 * user data.
 */
else if($_REQUEST['action'] == 'deleteItem'
		&& isset($_REQUEST['type'])
		&& isset($_REQUEST['id']))
{
	CsrfEnforceOnStateChange();
	if($_REQUEST['type'] == WEBDISK_ITEM_FILE)
	{
		$ctx = WebdiskOpenFile($webdisk, (int)$_REQUEST['id'], true);
		if($ctx)
		{
			// F9: shared write access → audit cross-owner destruction.
			bmShareAuditLog((int)$userRow['id'], (int)$ctx['access']['ownerId'],
				'webdisk_file_delete', (int)$_REQUEST['id'],
				isset($ctx['file']['dateiname']) ? (string)$ctx['file']['dateiname'] : '');
			$ctx['disk']->DeleteFile((int)$_REQUEST['id']);
		}
	}
	else
	{
		$ctx = WebdiskOpenFolder($webdisk, (int)$_REQUEST['id'], true);
		if($ctx)
		{
			bmShareAuditLog((int)$userRow['id'], (int)$ctx['access']['ownerId'],
				'webdisk_folder_delete', (int)$ctx['folderID'], '');
			$ctx['disk']->DeleteFolder($ctx['folderID']);
		}
	}
	header('Location: ' . SessionUrl('webdisk.php?folder=' . $viewFolderID));
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
	$destCtx = WebdiskOpenFolder($webdisk, (int)$_REQUEST['destFolderID'], true);
	if($destCtx === false)
	{
		echo('0');
		exit();
	}

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
				$fileCtx = WebdiskOpenFile($webdisk, (int)$itemID, true);
				if($fileCtx && (int)$fileCtx['access']['ownerId'] === (int)$destCtx['access']['ownerId'])
					$destCtx['disk']->MoveFile($destCtx['folderID'], (int)$itemID);
			}
			else if($type == WEBDISK_ITEM_FOLDER)
			{
				$folderCtx = WebdiskOpenFolder($webdisk, (int)$itemID, true);
				if($folderCtx && (int)$folderCtx['access']['ownerId'] === (int)$destCtx['access']['ownerId'])
				{
					$folderInvolved = true;
					$destCtx['disk']->MoveFolder($destCtx['folderID'], $folderCtx['folderID']);
				}
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
	$destCtx = WebdiskOpenFolder($webdisk, $viewFolderID, true);
	if($destCtx === false)
		WebdiskDeny();
	$pasteDisk = $destCtx['disk'];
	$pasteFolderID = $destCtx['folderID'];
	$pasteSpaceLimit = $pasteDisk->GetSpaceLimit();
	$pasteUsedSpace = $pasteDisk->GetUsedSpace();

	foreach($_SESSION['clipboard'] as $key=>$clipboardItem)
	{
		// cut
		if($clipboardItem['do'] == 'cut')
		{
			// file
			if($clipboardItem['type'] == WEBDISK_ITEM_FILE)
			{
				$fileInfo = $pasteDisk->GetFileInfo($clipboardItem['id']);
				if($fileInfo && $pasteDisk->FileExists($pasteFolderID, $fileInfo['dateiname']))
				{
					// exists
					$tpl->assign('msg', $lang_user['fileexists'] . '.');
				}
				else if($fileInfo)
				{
					// ok!
					$pasteDisk->MoveFile($pasteFolderID, $clipboardItem['id']);
					unset($_SESSION['clipboard'][$key]);
					$ok = true;
				}
			}

			// folder
			else if($clipboardItem['type'] == WEBDISK_ITEM_FOLDER)
			{
				$folderInfo = $pasteDisk->GetFolderInfo($clipboardItem['id']);
				if($folderInfo && $pasteDisk->FolderExists($pasteFolderID, $folderInfo['titel']))
				{
					// exists
					$tpl->assign('msg', $lang_user['foldererror']);
				}
				else if($folderInfo)
				{
					// ok!
					$pasteDisk->MoveFolder($pasteFolderID, $clipboardItem['id']);
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
				$fileInfo = $pasteDisk->GetFileInfo($clipboardItem['id']);
				if($fileInfo !== false && $pasteDisk->FileExists($pasteFolderID, $fileInfo['dateiname']))
				{
					// exists
					$tpl->assign('msg', $lang_user['fileexists'] . '.');
				}
				else if($fileInfo !== false)
				{
					if($pasteSpaceLimit == -1 || ($pasteUsedSpace+$fileInfo['size']) <= $pasteSpaceLimit)
					{
						// ok!
						$pasteDisk->CopyFile($pasteFolderID, $clipboardItem['id']);
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
				$folderInfo = $pasteDisk->GetFolderInfo($clipboardItem['id']);
				if($folderInfo !== false && $pasteDisk->FolderExists($pasteFolderID, $folderInfo['titel']))
				{
					// exists
					$tpl->assign('msg', $lang_user['foldererror']);
				}
				else if($folderInfo !== false)
				{
					// copy folder
					$maxSpace = $pasteSpaceLimit == -1 ? -1 : $pasteSpaceLimit - $pasteUsedSpace;
					if(!$pasteDisk->CopyFolder($pasteFolderID, $clipboardItem['id'], $maxSpace))
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
		SessionRedirect('webdisk.php?folder=' . $viewFolderID . '&_=' . time());
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
	$upCtx = WebdiskOpenFolder($webdisk, $viewFolderID, true);
	if($upCtx === false)
	{
		http_response_code(400);
		echo $lang_user['internalerror'];
		exit();
	}
	$upDisk = $upCtx['disk'];
	$upFolderID = $upCtx['folderID'];
	$upUsedSpace = $upDisk->GetUsedSpace();
	$upSpaceLimit = $upDisk->GetSpaceLimit();
	$maxUpload = WebdiskGetMaxUploadFileSize($upUsedSpace, $upSpaceLimit, $groupRow, $userRow);

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
	else if($upDisk->Forbidden($fileName, $mimeType))
	{
		$msg = $lang_user['wd_fileforbidden'];
	}
	else if($groupRow['traffic'] <= 0 || ($userRow['traffic_down']+$userRow['traffic_up']+$fileSize) <= $groupRow['traffic']+$userRow['traffic_add'])
	{
		if($upSpaceLimit == -1 || $upUsedSpace+$fileSize <= $upSpaceLimit)
		{
			if(($fileID = $upDisk->CreateFile($upFolderID, $fileName, $mimeType, $fileSize)) !== false)
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
						$success = BMBlobStorage::createDefaultWebdiskProvider($upDisk->_userID)->storeBlob(BMBLOB_TYPE_WEBDISK, $fileID, $fpOut, $fileSize);
					}

					fclose($fpOut);
				}

				if(!$success || ($readBytes != $fileSize))
				{
					$upDisk->DeleteFile($fileID);
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
	$upCtx = WebdiskOpenFolder($webdisk, $viewFolderID, true);
	if($upCtx === false)
		WebdiskDeny();
	$upDisk = $upCtx['disk'];
	$upFolderID = $upCtx['folderID'];
	$upUsedSpace = $upDisk->GetUsedSpace();
	$upSpaceLimit = $upDisk->GetSpaceLimit();
	$maxUpload = WebdiskGetMaxUploadFileSize($upUsedSpace, $upSpaceLimit, $groupRow, $userRow);

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
			$errMsg = WebdiskProcessUploadedFileEntry($upDisk, $upFolderID, $entry, $maxUpload, $upUsedSpace, $upSpaceLimit, $groupRow, $userRow);

			if($errMsg !== null)
				$error[$fileName] = $errMsg;
			else
				$success[$fileName] = $lang_user['success'];
		}
	}

	if(count($error) > 0 || count($success) > 0)
		WebdiskStoreUploadFeedback($viewFolderID, $error, $success);

	header('Location: ' . SessionUrl('webdisk.php?folder=' . $viewFolderID));
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

	$pathFolders = $webdisk->GetFolderPath($path);
	$history = array_merge(
		array(array('id' => '0', 'title' => '/')),
		$pathFolders ? $pathFolders : array()
	);

	$parentID = ($path > 0) ? (int)$webdisk->GetFolderParent($path) : -1;

	$folders = array();
	$files = array();
	$content = $webdisk->GetFolderContent($path, 'dateiname', 'ASC');
	foreach($content as $item)
	{
		if($item['type'] == WEBDISK_ITEM_FOLDER)
			$folders[] = $item;
		else
			$files[] = $item;
	}

	// assign & display
	$tpl->assign('height', (int)$_REQUEST['height']);
	$tpl->assign('pathID', $path);
	$tpl->assign('parentID', $parentID);
	$tpl->assign('history', $history);
	$tpl->assign('folders', $folders);
	$tpl->assign('files', $files);
	$tpl->display('li/webdisk.dialog.content.tpl');
}

/**
 * import from mail dialog
 */
else if($_REQUEST['action'] == 'importFromMail')
{
	$tpl->assign('params', 'webdisk.php?action=doImportFromMail' . '&id=' . (int)$_REQUEST['id'] . '&attachment=' . preg_replace('/[^\.0-9]/', '', $_REQUEST['attachment']));
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
 * share whole webdisk with a local user
 */
else if($_REQUEST['action'] == 'shareWebdisk')
{
	if(!bmOrganizerGroupCanShare('webdisk'))
	{
		SessionRedirect('webdisk.php');
		exit();
	}
	$shareItem = array(
		'id'	=> (int)$userRow['id'],
		'title'	=> $lang_user['webdisk']
	);

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add' && isset($_REQUEST['email']) && IsPOSTRequest())
	{
		$targetId = bmOrganizerShareTargetUserId($_REQUEST['email']);
		$access = isset($_REQUEST['access']) ? $_REQUEST['access'] : BM_ORGANIZER_ACCESS_READ;
		$result = bmOrganizerAddShare(BM_ORGANIZER_SHARE_WEBDISK, (int)$userRow['id'], $userRow['id'], $targetId, $access);
		if(is_string($result) && isset($lang_user[$result]))
			$tpl->assign('shareError', $lang_user[$result]);
		else if((int)$result > 0)
		{
			bmOrganizerNotifyShareInvite(BM_ORGANIZER_SHARE_WEBDISK, $shareItem, $userRow['id'], $targetId, $access);
			$tpl->assign('shareSuccess', $lang_user['shareinvited']);
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'remove'
		&& isset($_REQUEST['share'])
		&& IsPOSTRequest())
	{
		// F2: revoking a webdisk share was GET-reachable.
		bmOrganizerRemoveShare(BM_ORGANIZER_SHARE_WEBDISK, (int)$_REQUEST['share'], $userRow['id']);
	}

	$tpl->assign('shareItem', $shareItem);
	$tpl->assign('shareList', bmOrganizerListShares(BM_ORGANIZER_SHARE_WEBDISK, (int)$userRow['id'], $userRow['id']));
	$tpl->assign('shareType', 'webdisk');
	$tpl->assign('shareAction', 'webdisk.php');
	$tpl->assign('shareRequestAction', 'shareWebdisk');
	$tpl->display('li/organizer.share.dialog.tpl');
	exit();
}

/**
 * share a folder (combined dialog: internal users + public link)
 */
else if($_REQUEST['action'] == 'share' && isset($_REQUEST['id']))
{
	if(!bmOrganizerGroupCanShare('webdisk'))
	{
		SessionRedirect('webdisk.php');
		exit();
	}
	$folder = $webdisk->GetFolderInfo((int)$_REQUEST['id']);
	if($folder === false)
	{
		SessionRedirect('webdisk.php');
		exit();
	}
	$folder['title'] = $folder['titel'];

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add' && isset($_REQUEST['email']) && IsPOSTRequest())
	{
		$targetId = bmOrganizerShareTargetUserId($_REQUEST['email']);
		$access = isset($_REQUEST['access']) ? $_REQUEST['access'] : BM_ORGANIZER_ACCESS_READ;
		$result = bmOrganizerAddShare(BM_ORGANIZER_SHARE_WDFOLDER, (int)$folder['id'], $userRow['id'], $targetId, $access);
		if(is_string($result) && isset($lang_user[$result]))
			$tpl->assign('shareError', $lang_user[$result]);
		else if((int)$result > 0)
		{
			bmOrganizerNotifyShareInvite(BM_ORGANIZER_SHARE_WDFOLDER, $folder, $userRow['id'], $targetId, $access);
			$tpl->assign('shareSuccess', $lang_user['shareinvited']);
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'remove'
		&& isset($_REQUEST['share'])
		&& IsPOSTRequest())
	{
		// F2: revoking a folder share was GET-reachable.
		bmOrganizerRemoveShare(BM_ORGANIZER_SHARE_WDFOLDER, (int)$_REQUEST['share'], $userRow['id']);
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'savePublic'
		&& $groupRow['share'] == 'yes' && IsPOSTRequest())
	{
		$publicShareError = WebdiskApplyPublicShareForm($webdisk, (int)$folder['id'], $bm_prefs, $lang_user);
		if($publicShareError !== '')
			$tpl->assign('publicShareError', $publicShareError);
		else
			$tpl->assign('publicShareSuccess', $lang_user['saved_changes']);
		// re-fetch the folder to reflect the new share state below
		$folder = $webdisk->GetFolderInfo((int)$_REQUEST['id']);
		$folder['title'] = $folder['titel'];
	}

	// Public-link section state
	if($groupRow['share'] == 'yes')
	{
		$sharePasswordRequired = isset($bm_prefs['wd_share_pw_required']) && $bm_prefs['wd_share_pw_required'] == 'yes';
		$maxShareDays = isset($bm_prefs['wd_share_max_days']) ? max(0, (int)$bm_prefs['wd_share_max_days']) : 0;
		$shareExpiryRequired = isset($bm_prefs['wd_share_expiry_required']) && $bm_prefs['wd_share_expiry_required'] == 'yes';
		$shareExpiryMinDate = date('Y-m-d');
		$shareExpiryMaxDate = $maxShareDays > 0 ? date('Y-m-d', strtotime('+' . $maxShareDays . ' day')) : '';
		$shareUntilDate = '';
		if(isset($folder['share_until']) && (int)$folder['share_until'] > 0)
			$shareUntilDate = date('Y-m-d', (int)$folder['share_until']);
		$folderPW = isset($folder['share_pw']) ? $folder['share_pw'] : '';
		if($sharePasswordRequired && trim($folderPW) == '')
			$folderPW = WebdiskGenerateSharePassword(12);

		$tpl->assign('publicShareAvailable',  true);
		$tpl->assign('publicShareEnabled',    $folder['share'] == 'yes');
		$tpl->assign('publicSharePW',         $folderPW);
		$tpl->assign('publicSharePasswordRequired', $sharePasswordRequired);
		$tpl->assign('publicShareUntilDate',  $shareUntilDate);
		$tpl->assign('publicShareExpiryRequired', $shareExpiryRequired);
		$tpl->assign('publicShareExpiryMaxDays', $maxShareDays);
		$tpl->assign('publicShareExpiryMinDate', $shareExpiryMinDate);
		$tpl->assign('publicShareExpiryMaxDate', $shareExpiryMaxDate);
		$tpl->assign('publicShareUrl',        WebdiskGetPublicShareUrl($bm_prefs['selfurl'], $userRow['email']));
	}
	else
	{
		$tpl->assign('publicShareAvailable', false);
	}

	$tpl->assign('shareItem', $folder);
	$tpl->assign('shareList', bmOrganizerListShares(BM_ORGANIZER_SHARE_WDFOLDER, (int)$folder['id'], $userRow['id']));
	$tpl->assign('shareType', 'wdfolder');
	$tpl->assign('shareAction', 'webdisk.php');
	$tpl->display('li/organizer.share.dialog.tpl');
	exit();
}

/**
 * leave a webdisk share
 */
else if($_REQUEST['action'] == 'leaveshare' && isset($_REQUEST['id']))
{
	$leaveId = (int)$_REQUEST['id'];
	$shared = $webdisk->GetAccessibleSharedFolders();
	if(!isset($shared[$leaveId]) || empty($shared[$leaveId]['can_leave']))
	{
		SessionRedirect('webdisk.php');
		exit();
	}

	$leaveItem = $shared[$leaveId];
	$leaveItem['title'] = $leaveItem['titel'];
	if(!empty($leaveItem['owner_email']))
		$leaveItem['owner_email'] = DecodeEMail($leaveItem['owner_email']);

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'leave' && IsPOSTRequest())
	{
		if(!empty($leaveItem['webdisk_share']))
			bmOrganizerLeaveShare(BM_ORGANIZER_SHARE_WEBDISK, (int)$leaveItem['userid'], $userRow['id']);
		else
			bmOrganizerLeaveShare(BM_ORGANIZER_SHARE_WDFOLDER, $leaveId, $userRow['id']);
		SessionRedirect('webdisk.php');
		exit();
	}

	$tpl->assign('leaveItem', $leaveItem);
	$tpl->assign('leaveAction', 'webdisk.php');
	$tpl->display('li/organizer.share.leave.tpl');
	exit();
}

/**
 * rpc get folder list
 */
else if($_REQUEST['action'] == 'getFolderList')
{
	$tpl->display('li/webdisk.folderlist.tpl');
}
?>