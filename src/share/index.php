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

// Public shares must never leak the share URL (which may contain a
// ?password= parameter) via the Referer header when a user clicks a
// link inside a shared folder, and browsers must not cache/store the
// response — both would otherwise expose the passphrase or preview
// content to third parties, browser history and shared caches.
header('Referrer-Policy: no-referrer');
header('Cache-Control: no-store, no-cache, must-revalidate, private');
header('Pragma: no-cache');

// If a client sends the share password in the URL (?password=...) it
// ends up in web-server access logs, browser history and any Referer
// header sent from pages the visitor navigates to afterwards. Log a
// warning so operators notice bookmark-based leaks; the request is
// still processed to preserve backward compatibility with existing
// links. The passwordSubmit(File) endpoints send via POST and are the
// preferred path — they don't hit this branch.
if(isset($_GET['password']) && isset($_REQUEST['action'])
	&& in_array($_REQUEST['action'], array('getFolder', 'getFile'), true))
{
	PutLog(sprintf('Public share request carries password in URL (action=%s, ip=%s) — '
		. 'prefer POST via passwordSubmit(File)',
		(string)$_REQUEST['action'],
		isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '?'),
		PRIO_NOTE, __FILE__, __LINE__);
}

/**
 * Pretty path-URL fallback: /share/<email>[/<file-token>]
 *
 * Preferred delivery is via the .htaccess RewriteRule which turns
 * /share/<email> into share/index.php?user=<email>. This fallback lets
 * the same URL work when mod_rewrite is unavailable (nginx w/o custom
 * config, deployments that dropped the rewrite rule, PATH_INFO-only
 * setups), *without* breaking the legacy /share/?user=<email>
 * bookmark/e-mail links — which are still routed through $_REQUEST.
 */
if(!isset($_REQUEST['user']))
{
	$prettyTail = '';
	if(isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '')
	{
		$prettyTail = ltrim((string)$_SERVER['PATH_INFO'], '/');
	}
	else if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] !== '')
	{
		$path = parse_url((string)$_SERVER['REQUEST_URI'], PHP_URL_PATH);
		if(is_string($path) && $path !== '')
		{
			$pos = strrpos($path, '/share/');
			if($pos !== false)
				$prettyTail = trim(substr($path, $pos + 7), '/');
			// direct pretty URL served by rewrite/alias
			else if(preg_match('~^/?share/(.+)$~', $path, $m))
				$prettyTail = trim($m[1], '/');
		}
	}
	if($prettyTail !== '' && strpos($prettyTail, '.') !== false)
	{
		$candidate = rawurldecode($prettyTail);
		// must look like an e-mail address, not e.g. index.php
		if(strpos($candidate, '@') !== false)
			$_REQUEST['user'] = $candidate;
	}
}

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

	// Custom avatar for the public share landing page.
	// avatar.php requires an authenticated session, so for the public
	// share view we inline the user's uploaded avatar as a data URL.
	// Only uploaded images are exposed – Gravatar/Libravatar/initials
	// are intentionally not resolved here to avoid leaking e-mail hashes
	// to third parties from a public page.
	if(function_exists('AvatarHasCustomImage') && AvatarHasCustomImage('user', $userID))
	{
		$avatarPath = AvatarImagePath('user', $userID, 128);
		if(is_readable($avatarPath))
		{
			$avatarData = @file_get_contents($avatarPath);
			if($avatarData !== false && $avatarData !== '')
				$tpl->assign('avatarImage', 'data:image/jpeg;base64,' . base64_encode($avatarData));
		}
	}

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

		if(!$isShared)
			die('Permission denied');

		// F3: constant-time password compare + rate limit for
		// bruteforce protection. Bucket is per (folder, IP) so an
		// attacker with a valid token can still be blocked after a
		// handful of wrong tries without affecting other visitors.
		if(trim($sharePW) != '')
		{
			$provided = isset($_REQUEST['password']) ? _unescape($_REQUEST['password']) : '';
			$bucket = 'pwfolder:' . (int)$id . ':'
				. (isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '0');

			if(!ShareRateLimitAllow($bucket))
			{
				PutLog(sprintf('Public share password bruteforce blocked (folder=%d, ip=%s)',
					(int)$id,
					isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '?'),
					PRIO_WARNING, __FILE__, __LINE__);
				die('Permission denied');
			}

			if(!ShareComparePassword($sharePW, $provided))
			{
				ShareRateLimitFail($bucket);
				die('Permission denied');
			}

			ShareRateLimitClear($bucket);
		}
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

	// F3: rate-limit password submission per (folder, IP) — a wrong
	// password does NOT continue the JS auth flow, so the visitor
	// cannot brute-force through this endpoint any faster than
	// ShareRateLimitAllow() permits.
	$bucket = 'pwfolder:' . (int)$_REQUEST['folder'] . ':'
		. (isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '0');

	if(!ShareRateLimitAllow($bucket))
	{
		PutLog(sprintf('Public share password bruteforce blocked (folder=%d, ip=%s)',
			(int)$_REQUEST['folder'],
			isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '?'),
			PRIO_WARNING, __FILE__, __LINE__);
		echo 'alert(\'' . addslashes($lang_user['folder_wrongpw']) . '\');' . "\n";
	}
	else if($isShared && ShareComparePassword($sharePW, (string)$_REQUEST['pw']))
	{
		// ok
		ShareRateLimitClear($bucket);
		echo 'parent.share_currentPWfor = ' . (int)$_REQUEST['folder'] . ';' . "\n";
		echo 'parent.share_currentPW = \'' . addslashes($_REQUEST['pw']) . '\';' . "\n";
		echo 'parent.shareEnterProtectedDir();' . "\n";
	}
	else
	{
		// wrong
		ShareRateLimitFail($bucket);
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

	// F3: rate-limit per (share-token, IP) so an attacker cannot
	// brute-force the password by scripting POSTs to this endpoint.
	$bucket = 'pwfile:' . hash('sha256', (string)$fileShareToken) . ':'
		. (isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '0');

	if(!ShareRateLimitAllow($bucket))
	{
		PutLog(sprintf('Public file-share password bruteforce blocked (ip=%s)',
			isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '?'),
			PRIO_WARNING, __FILE__, __LINE__);
		echo 'alert(\'' . addslashes($lang_user['folder_wrongpw']) . '\');' . "\n";
	}
	else if($fileShare !== false
		&& (int)$fileShare['user'] === (int)$userID
		&& ShareComparePassword($fileShare['share_pw'], (string)$_REQUEST['pw']))
	{
		ShareRateLimitClear($bucket);
		echo 'parent.share_currentPWfor = \'f' . (int)$_REQUEST['id'] . '\';' . "\n";
		echo 'parent.share_currentPW = \'' . addslashes($_REQUEST['pw']) . '\';' . "\n";
		echo 'parent.shareEnterProtectedFile();' . "\n";
	}
	else
	{
		ShareRateLimitFail($bucket);
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
		// F3: track whether we're in the "one-shot" flow so we can
		// atomically claim the token instead of just marking it used
		// afterwards. `$singleUseToken` holds the token to claim; NULL
		// means either non-single-use or non-token-based access.
		$singleUseToken = null;

		if($fileShareToken != '')
		{
			$fileShare = $webdisk->GetFileShareByToken($fileShareToken);
			if($fileShare === false
				|| (int)$fileShare['user'] !== (int)$userID
				|| (int)$fileShare['file_id'] !== (int)$fileInfo['id'])
				die('Permission denied');

			// F3: constant-time password compare + rate limit per
			// (token, IP). Rate-limit fires only when password is
			// required so unprotected shares stay unaffected.
			if(trim($fileShare['share_pw']) != '')
			{
				$provided = isset($_REQUEST['password']) ? _unescape($_REQUEST['password']) : '';
				$bucket = 'dlfile:' . hash('sha256', (string)$fileShareToken) . ':'
					. (isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '0');

				if(!ShareRateLimitAllow($bucket))
				{
					PutLog(sprintf('Public file-share download bruteforce blocked (ip=%s)',
						isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '?'),
						PRIO_WARNING, __FILE__, __LINE__);
					die('Permission denied');
				}

				if(!ShareComparePassword($fileShare['share_pw'], $provided))
				{
					ShareRateLimitFail($bucket);
					die('Permission denied');
				}

				ShareRateLimitClear($bucket);
			}

			if(!empty($fileShare['single_use']))
				$singleUseToken = $fileShareToken;
		}
		else
		{
			$fileFolder = $fileInfo['ordner'];
			list($isShared, $sharePW) = $webdisk->IsShared($fileFolder);
			if(!$isShared)
				die('Permission denied');

			if(trim($sharePW) != '')
			{
				$provided = isset($_REQUEST['password']) ? _unescape($_REQUEST['password']) : '';
				$bucket = 'dlfolder:' . (int)$fileFolder . ':'
					. (isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '0');

				if(!ShareRateLimitAllow($bucket))
				{
					PutLog(sprintf('Public folder-share download bruteforce blocked (folder=%d, ip=%s)',
						(int)$fileFolder,
						isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '?'),
						PRIO_WARNING, __FILE__, __LINE__);
					die('Permission denied');
				}

				if(!ShareComparePassword($sharePW, $provided))
				{
					ShareRateLimitFail($bucket);
					die('Permission denied');
				}

				ShareRateLimitClear($bucket);
			}
		}

		// F3: Anti-DoS download cap per share and IP. Independent from
		// the password check because open shares (no password) can
		// otherwise be script-downloaded in bulk to burn the owner's
		// traffic budget. 60 downloads / hour / IP is generous for
		// legitimate users (retry, refresh, multi-tab) but crushes
		// automation.
		if($fileShareToken != '')
			$dlBucket = 'dllimit:file:' . hash('sha256', (string)$fileShareToken) . ':'
				. (isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '0');
		else
			$dlBucket = 'dllimit:folder:' . (int)$fileInfo['ordner'] . ':'
				. (isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '0');
		if(!ShareRateLimitConsume($dlBucket, 60, 3600))
		{
			PutLog(sprintf('Public share download rate limit hit (bucket=%s)',
				$dlBucket), PRIO_WARNING, __FILE__, __LINE__);
			die('Permission denied');
		}

		// F3: Race-free single-use enforcement.
		//
		// The historical flow was:
		//   1) GetFileShareByToken() → used=0 → allow
		//   2) stream file, count traffic
		//   3) MarkFileShareUsed() → used=1
		// Two parallel requests both pass step 1 and both consume the
		// share. We now flip used=0→1 atomically BEFORE step 2 so the
		// second request loses cleanly.
		if($singleUseToken !== null && !$webdisk->ClaimSingleUseFileShare($singleUseToken))
			die('Permission denied');

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

			// Non-single-use shares still hit the legacy path (updates
			// last_used and toggles used=1 non-atomically).
			if($fileShareToken != '' && $singleUseToken === null)
				$webdisk->MarkFileShareUsed($fileShareToken);

			// send file
			header('Content-Type: ' . $fileInfo['contenttype']);
			header('Content-Length: ' . $fileInfo['size']);
			SendContentDispositionHeader('attachment', $fileInfo['dateiname']);
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
