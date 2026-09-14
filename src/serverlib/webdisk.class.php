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

include_once B1GMAIL_DIR . 'serverlib/organizer.collections.inc.php';

/**
 * constants
 */
define('WEBDISK_ITEM_FOLDER',		1);
define('WEBDISK_ITEM_FILE',			2);

/**
 * webdisk interface class
 */
class BMWebdisk
{
	var $_userID;
	var $_shareUntilByFolder = null;
	var $_fileShareByFile = null;
	var $_sharedWebdiskFolders = null;
	var $_ownerDiskFolderMap = array();
	static $_shareMetaTableReady = false;
	static $_fileShareTableReady = false;
	static $_uploaderColumnReady = false;

	/**
	 * constructor
	 *
	 * @param int $userID User ID
	 * @return BMWebdisk
	 */
	function __construct($userID)
	{
		global $userRow, $db;

		$this->_userID = $userID;

		if($userRow['id'] == $userID && $userRow['traffic_status'] != (int)date('m'))
		{
			$userRow['traffic_down'] = $userRow['traffic_up'] = 0;
			$userRow['traffic_status'] = (int)date('m');

			$db->Query('UPDATE {pre}users SET traffic_down=0,traffic_up=0,traffic_status=? WHERE id=?',
				(int)date('m'),
				$userID);
		}

		$this->EnsureShareMetaTable();
		$this->CleanupExpiredShares();
		$this->EnsureFileShareTable();
		$this->CleanupExpiredFileShares();
		$this->EnsureUploaderColumn();
	}

	function EnsureShareMetaTable()
	{
		global $db;

		if(self::$_shareMetaTableReady)
			return;

		$db->Query('CREATE TABLE IF NOT EXISTS {pre}disksharemeta (
			folder_id INT(11) NOT NULL,
			`user` INT(11) NOT NULL,
			share_until INT(11) NOT NULL DEFAULT 0,
			PRIMARY KEY(folder_id),
			KEY `user` (`user`)
		)');

		self::$_shareMetaTableReady = true;
	}

	function LoadShareMeta()
	{
		global $db;

		if(is_array($this->_shareUntilByFolder))
			return;

		$this->_shareUntilByFolder = array();
		$res = $db->Query('SELECT folder_id,share_until FROM {pre}disksharemeta WHERE user=?',
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$this->_shareUntilByFolder[(int)$row['folder_id']] = max(0, (int)$row['share_until']);
		$res->Free();
	}

	function GetShareUntil($folderID)
	{
		$this->LoadShareMeta();
		return(isset($this->_shareUntilByFolder[(int)$folderID]) ? (int)$this->_shareUntilByFolder[(int)$folderID] : 0);
	}

	function SetShareUntil($folderID, $shareUntil)
	{
		global $db;

		$folderID = (int)$folderID;
		$shareUntil = max(0, (int)$shareUntil);
		$this->LoadShareMeta();

		if($shareUntil > 0)
		{
			$db->Query('REPLACE INTO {pre}disksharemeta(folder_id,user,share_until) VALUES(?,?,?)',
				$folderID,
				$this->_userID,
				$shareUntil);
			$this->_shareUntilByFolder[$folderID] = $shareUntil;
		}
		else
		{
			$db->Query('DELETE FROM {pre}disksharemeta WHERE folder_id=? AND user=?',
				$folderID,
				$this->_userID);
			unset($this->_shareUntilByFolder[$folderID]);
		}
	}

	function IsShareExpired($shareUntil)
	{
		return((int)$shareUntil > 0 && (int)$shareUntil < time());
	}

	function CleanupExpiredShares()
	{
		global $db;

		$this->LoadShareMeta();

		$expiredFolderIDs = array();
		foreach($this->_shareUntilByFolder as $folderID => $shareUntil)
			if($this->IsShareExpired($shareUntil))
				$expiredFolderIDs[] = (int)$folderID;

		if(count($expiredFolderIDs) == 0)
			return;

		$db->Query('UPDATE {pre}diskfolders SET share=?,share_pw=?,modified=? WHERE user=? AND id IN(' . implode(',', $expiredFolderIDs) . ')',
			'no',
			'',
			time(),
			$this->_userID);
		$db->Query('DELETE FROM {pre}disksharemeta WHERE user=? AND folder_id IN(' . implode(',', $expiredFolderIDs) . ')',
			$this->_userID);

		foreach($expiredFolderIDs as $folderID)
			unset($this->_shareUntilByFolder[(int)$folderID]);
	}

	function EnsureFileShareTable()
	{
		global $db;

		if(self::$_fileShareTableReady)
			return;

		$db->Query('CREATE TABLE IF NOT EXISTS {pre}diskfileshares (
			file_id INT(11) NOT NULL,
			`user` INT(11) NOT NULL,
			token VARCHAR(96) NOT NULL,
			share_pw VARCHAR(255) NOT NULL DEFAULT \'\',
			share_until INT(11) NOT NULL DEFAULT 0,
			single_use TINYINT(1) NOT NULL DEFAULT 0,
			used TINYINT(1) NOT NULL DEFAULT 0,
			active TINYINT(1) NOT NULL DEFAULT 1,
			created INT(11) NOT NULL DEFAULT 0,
			last_used INT(11) NOT NULL DEFAULT 0,
			PRIMARY KEY(file_id),
			UNIQUE KEY token (token),
			KEY `user` (`user`)
		)');

		self::$_fileShareTableReady = true;
	}

	function EnsureUploaderColumn()
	{
		global $mysql;

		if(self::$_uploaderColumnReady)
			return;
		self::$_uploaderColumnReady = true;

		if(!function_exists('bmOrganizerEnsureColumn'))
			return;

		$prefix = isset($mysql['prefix']) ? $mysql['prefix'] : '';
		bmOrganizerEnsureColumn($prefix.'diskfiles', 'uploader_id',
			'ALTER TABLE `{table}` ADD `uploader_id` int(11) NOT NULL DEFAULT 0');
	}

	/**
	 * email of the user who uploaded a file
	 *
	 * @param array $fileInfo
	 * @return string
	 */
	function GetFileUploaderEmail($fileInfo)
	{
		global $db;

		$uploaderId = isset($fileInfo['uploader_id']) ? (int)$fileInfo['uploader_id'] : 0;
		if($uploaderId <= 0 && isset($fileInfo['user']))
			$uploaderId = (int)$fileInfo['user'];
		if($uploaderId <= 0)
			$uploaderId = (int)$this->_userID;
		if($uploaderId <= 0)
			return('');

		$res = $db->Query('SELECT email FROM {pre}users WHERE id=?',
			$uploaderId);
		if($res->RowCount() != 1)
		{
			$res->Free();
			return('');
		}
		list($email) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		return(DecodeEMail($email));
	}

	function LoadFileShareMeta()
	{
		global $db;

		if(is_array($this->_fileShareByFile))
			return;

		$this->_fileShareByFile = array();
		$res = $db->Query('SELECT file_id,token,share_pw,share_until,single_use,used,active FROM {pre}diskfileshares WHERE user=?',
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$this->_fileShareByFile[(int)$row['file_id']] = array(
				'file_id' => (int)$row['file_id'],
				'token' => $row['token'],
				'share_pw' => $row['share_pw'],
				'share_until' => (int)$row['share_until'],
				'single_use' => (int)$row['single_use'] === 1,
				'used' => (int)$row['used'] === 1,
				'active' => (int)$row['active'] === 1
			);
		$res->Free();
	}

	function GetFileShareInfo($fileID)
	{
		$this->LoadFileShareMeta();
		return(isset($this->_fileShareByFile[(int)$fileID]) ? $this->_fileShareByFile[(int)$fileID] : false);
	}

	function IsFileShared($fileID)
	{
		$info = $this->GetFileShareInfo((int)$fileID);
		if(!$info || !$info['active'])
			return(false);

		return(!$this->IsShareExpired($info['share_until']));
	}

	function BuildFileShareToken($fileID)
	{
		return(md5(GenerateRandomKey('file-share:' . $this->_userID . ':' . (int)$fileID . ':' . microtime(true)))
			. md5(GenerateRandomKey('file-share-alt:' . $this->_userID . ':' . (int)$fileID . ':' . mt_rand())));
	}

	function SetFileShareSettings($fileID, $enabled, $sharePW, $shareUntil = 0, $singleUse = false)
	{
		global $db;

		$fileID = (int)$fileID;
		$shareUntil = max(0, (int)$shareUntil);
		$enabled = (bool)$enabled;
		$singleUse = (bool)$singleUse;
		$this->LoadFileShareMeta();

		if(!$enabled)
			return($this->StopFileShare($fileID));

		$existing = $this->GetFileShareInfo($fileID);
		$token = $existing && !empty($existing['token']) ? $existing['token'] : $this->BuildFileShareToken($fileID);
		$created = $existing && isset($existing['created']) ? (int)$existing['created'] : time();

		$db->Query('REPLACE INTO {pre}diskfileshares(file_id,user,token,share_pw,share_until,single_use,used,active,created,last_used) VALUES(?,?,?,?,?,?,?,?,?,?)',
			$fileID,
			$this->_userID,
			$token,
			$sharePW,
			$shareUntil,
			$singleUse ? 1 : 0,
			0,
			1,
			$created,
			0);

		$this->_fileShareByFile[$fileID] = array(
			'file_id' => $fileID,
			'token' => $token,
			'share_pw' => $sharePW,
			'share_until' => $shareUntil,
			'single_use' => $singleUse,
			'used' => false,
			'active' => true
		);

		return($token);
	}

	function StopFileShare($fileID)
	{
		global $db;

		$fileID = (int)$fileID;
		$this->LoadFileShareMeta();
		$db->Query('DELETE FROM {pre}diskfileshares WHERE file_id=? AND user=?',
			$fileID,
			$this->_userID);
		unset($this->_fileShareByFile[$fileID]);
		return(true);
	}

	function GetFileShareByToken($token, $includeInactive = false)
	{
		global $db;

		$this->EnsureFileShareTable();
		$res = $db->Query('SELECT file_id,user,token,share_pw,share_until,single_use,used,active FROM {pre}diskfileshares WHERE token=? LIMIT 1',
			$token);
		if($res->RowCount() == 0)
		{
			$res->Free();
			return(false);
		}

		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();
		$row['file_id'] = (int)$row['file_id'];
		$row['user'] = (int)$row['user'];
		$row['share_until'] = (int)$row['share_until'];
		$row['single_use'] = (int)$row['single_use'] === 1;
		$row['used'] = (int)$row['used'] === 1;
		$row['active'] = (int)$row['active'] === 1;

		if(!$includeInactive)
		{
			if(!$row['active'])
				return(false);
			if($this->IsShareExpired($row['share_until']))
				return(false);
			if($row['single_use'] && $row['used'])
				return(false);
		}

		return($row);
	}

	function MarkFileShareUsed($token)
	{
		global $db;

		$now = time();
		$db->Query('UPDATE {pre}diskfileshares SET used=1,active=CASE WHEN single_use=1 THEN 0 ELSE active END,last_used=? WHERE token=?',
			$now,
			$token);
	}

	/**
	 * Atomically claim a share token for exactly one use.
	 *
	 * The naive flow — check via GetFileShareByToken(), then serve the
	 * file, then call MarkFileShareUsed() at the end — races with any
	 * parallel request: two concurrent requests both see used=0, both
	 * proceed to download, and only the second UPDATE takes effect.
	 * The single-use guarantee is then meaningless in practice.
	 *
	 * This method performs the "reserve first" step atomically:
	 * a conditional UPDATE that flips used=0 → used=1 in a single SQL
	 * statement. Only the winning request gets AffectedRows() == 1 and
	 * may proceed with the download. Every other concurrent request
	 * sees 0 and must abort.
	 *
	 * Non-single-use shares don't have this race (multiple downloads
	 * are permitted by design) so the caller should only call this for
	 * single_use=true shares. For non-single-use shares the plain
	 * MarkFileShareUsed() (which updates last_used and used=1 without
	 * gating access) is the right choice.
	 *
	 * @param string $token Public share token.
	 * @return bool True when this caller owns the single-use slot; false
	 *              if another request already claimed it (or the row is
	 *              gone/inactive/expired).
	 */
	function ClaimSingleUseFileShare($token)
	{
		global $db;

		$now = time();
		$db->Query('UPDATE {pre}diskfileshares SET used=1,active=0,last_used=? '
			.'WHERE token=? AND single_use=1 AND used=0 AND active=1',
			$now,
			$token);

		return $db->AffectedRows() === 1;

		$this->LoadFileShareMeta();
		foreach($this->_fileShareByFile as $fileID => $shareInfo)
		{
			if($shareInfo['token'] == $token)
			{
				$this->_fileShareByFile[$fileID]['used'] = true;
				if($this->_fileShareByFile[$fileID]['single_use'])
					$this->_fileShareByFile[$fileID]['active'] = false;
				break;
			}
		}
	}

	function CleanupExpiredFileShares()
	{
		global $db;

		$this->LoadFileShareMeta();
		$expired = array();
		foreach($this->_fileShareByFile as $fileID => $shareInfo)
		{
			if(!$shareInfo['active'])
				continue;
			if($this->IsShareExpired($shareInfo['share_until']))
				$expired[] = (int)$fileID;
		}

		if(count($expired) < 1)
			return;

		$db->Query('DELETE FROM {pre}diskfileshares WHERE user=? AND file_id IN(' . implode(',', $expired) . ')',
			$this->_userID);
		foreach($expired as $fileID)
			unset($this->_fileShareByFile[(int)$fileID]);
	}


	/**
	* get page list for template use
	*
	* @return array
	*/
	function GetPageFolderList()
	{
		global $db, $lang_user;

		$pageMenu = $idTable = array();
		$i = 0;

		$idTable[0] = $i;
		$pageMenu[] = array(
			'i'				=> $i++,
			'icon'			=> 'folder',
			'id'			=> 0,
			'parent'		=> -1,
			'parentFolder'	=> -1,
			'text'			=> $lang_user['webdisk'],
			'shareOwner'	=> 0,
			'owner_email'	=> '',
			'canShareWebdisk'	=> bmOrganizerGroupCanShare('webdisk')
		);

		$res = $db->Query('SELECT `id`,`titel`,`parent`,`share` FROM {pre}diskfolders WHERE `user`=? ORDER BY `titel` ASC',
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$idTable[$row['id']] = $i;

			$pageMenu[] = array(
				'id'			=> $row['id'],
				'icon'			=> $row['share'] == 'yes' ? 'folder_shared' : 'folder',
				'i'				=> $i,
				'parent'		=> $row['parent'],
				'parentFolder'	=> $row['parent'],
				'text'			=> $row['titel'],
				'shareOwner'	=> 0,
				'owner_email'	=> '',
				'canShare'		=> bmOrganizerGroupCanShare('webdisk')
			);

			$i++;
		}
		$res->Free();

		foreach($this->GetAccessibleSharedFolders() as $folder)
		{
			$viewId = (int)$folder['id'];
			$idTable[$viewId] = $i;
			$parent = isset($folder['parent']) ? $folder['parent'] : -1;
			$pageMenu[] = array(
				'id'			=> $viewId,
				'icon'			=> 'folder_shared',
				'i'				=> $i,
				'parent'		=> $parent,
				'parentFolder'	=> $parent,
				'text'			=> $folder['titel'],
				'shareOwner'	=> isset($folder['userid']) ? (int)$folder['userid'] : 0,
				'owner_email'	=> !empty($folder['owner_email']) ? DecodeEMail($folder['owner_email']) : ''
			);
			$i++;
		}

		foreach($pageMenu as $key=>$val)
		{
			if(isset($idTable[$val['parent']]))
				$pageMenu[$key]['parent'] = $idTable[$val['parent']];
			else if($pageMenu[$key]['parent'] != -1)
				$pageMenu[$key]['parent'] = 0;
		}
		return($pageMenu);
	}

	/**
	 * own folders plus shared webdisks grouped by owner email for the sidebar
	 *
	 * @param array $pageMenu
	 * @return array
	 */
	function SplitSidebarFolderMenus($pageMenu)
	{
		$own = array();
		$shared = array();
		foreach($pageMenu as $item)
		{
			if(!empty($item['shareOwner']))
			{
				$ownerId = (int)$item['shareOwner'];
				if(!isset($shared[$ownerId]))
				{
					$shared[$ownerId] = array(
						'email'		=> isset($item['owner_email']) ? $item['owner_email'] : '',
						'folders'	=> array()
					);
				}
				else if($shared[$ownerId]['email'] == '' && !empty($item['owner_email']))
					$shared[$ownerId]['email'] = $item['owner_email'];
				$shared[$ownerId]['folders'][] = $item;
			}
			else
				$own[] = $item;
		}

		$own = $this->_reindexFolderTreeMenu($own);
		foreach($shared as $ownerId=>$group)
			$shared[$ownerId]['folders'] = $this->_reindexFolderTreeMenu($group['folders']);

		if(count($shared) > 1)
		{
			uasort($shared, function($a, $b) {
				return strcasecmp(isset($a['email']) ? $a['email'] : '', isset($b['email']) ? $b['email'] : '');
			});
		}

		return(array($own, $shared));
	}

	/**
	 * @param array $items
	 * @return array
	 */
	function _reindexFolderTreeMenu($items)
	{
		$idTable = array();
		$result = array();
		$i = 0;
		foreach($items as $item)
		{
			$idTable[$item['id']] = $i;
			$item['i'] = $i;
			$result[] = $item;
			$i++;
		}
		foreach($result as $key=>$val)
		{
			$parentFolder = isset($val['parentFolder']) ? (int)$val['parentFolder'] : -1;
			if($parentFolder == -1 || !isset($idTable[$parentFolder]))
				$result[$key]['parent'] = -1;
			else
				$result[$key]['parent'] = $idTable[$parentFolder];
		}
		return($result);
	}

	/**
	 * virtual id for another user's webdisk root
	 *
	 * @param int $ownerId
	 * @return int
	 */
	static function EncodeSharedWebdiskRoot($ownerId)
	{
		return(WEBDISK_SHARE_ROOT_BASE - (int)$ownerId);
	}

	/**
	 * @param int $folderID
	 * @return int|false
	 */
	static function DecodeSharedWebdiskRoot($folderID)
	{
		$folderID = (int)$folderID;
		if($folderID >= WEBDISK_SHARE_ROOT_BASE)
			return(false);
		$ownerId = WEBDISK_SHARE_ROOT_BASE - $folderID;
		return($ownerId > 0 ? $ownerId : false);
	}

	/**
	 * webdisk folders shared with this user
	 *
	 * @return array
	 */
	function GetAccessibleSharedFolders()
	{
		global $db, $lang_user;

		if(is_array($this->_sharedWebdiskFolders))
			return($this->_sharedWebdiskFolders);

		$result = array();
		bmOrganizerEnsureShares();

		$res = $db->Query('SELECT f.id,f.titel,f.parent,f.user,f.share,s.access,u.email AS owner_email '
			. 'FROM {pre}organizer_shares s '
			. 'INNER JOIN {pre}diskfolders f ON f.id=s.collection_id AND f.user=s.owner_id '
			. 'INNER JOIN {pre}users u ON u.id=s.owner_id '
			. 'WHERE s.type=? AND s.target_id=? AND u.gesperrt!=\'delete\' AND s.owner_id!=?',
			BM_ORGANIZER_SHARE_WDFOLDER,
			$this->_userID,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if(!bmOrganizerShareActiveForOwner(BM_ORGANIZER_SHARE_WDFOLDER, $row['user']))
				continue;
			$this->_mergeSharedWebdiskTree($result, $row, $row['access'] === BM_ORGANIZER_ACCESS_WRITE, true, true);
		}
		$res->Free();

		$res = $db->Query('SELECT s.access,s.owner_id,u.email AS owner_email '
			. 'FROM {pre}organizer_shares s '
			. 'INNER JOIN {pre}users u ON u.id=s.owner_id '
			. 'WHERE s.type=? AND s.target_id=? AND u.gesperrt!=\'delete\' AND s.owner_id!=?',
			BM_ORGANIZER_SHARE_WEBDISK,
			$this->_userID,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if(!bmOrganizerShareActiveForOwner(BM_ORGANIZER_SHARE_WEBDISK, $row['owner_id']))
				continue;
			$this->_mergeSharedWebdisk($result, (int)$row['owner_id'], $row['owner_email'], $row['access'] === BM_ORGANIZER_ACCESS_WRITE);
		}
		$res->Free();

		$this->_sharedWebdiskFolders = $result;
		return($result);
	}

	/**
	 * @param array  $result
	 * @param int    $ownerId
	 * @param string $ownerEmail
	 * @param bool   $writeAccess
	 */
	function _mergeSharedWebdisk(&$result, $ownerId, $ownerEmail, $writeAccess)
	{
		global $lang_user;

		$ownerId = (int)$ownerId;
		$vid = self::EncodeSharedWebdiskRoot($ownerId);
		if(!isset($result[$vid]))
		{
			$result[$vid] = array(
				'id'			=> $vid,
				'titel'			=> $lang_user['webdisk'],
				'parent'		=> -1,
				'userid'		=> $ownerId,
				'owner_email'	=> $ownerEmail,
				'readonly'		=> $writeAccess ? 0 : 1,
				'share_root'	=> 1,
				'can_leave'		=> 1,
				'webdisk_share'	=> 1
			);
		}
		else
		{
			if($writeAccess)
				$result[$vid]['readonly'] = 0;
			$result[$vid]['can_leave'] = 1;
			$result[$vid]['webdisk_share'] = 1;
		}

		$this->_ensureOwnerDiskFolderMap($ownerId);
		$all = isset($this->_ownerDiskFolderMap[$ownerId]) ? $this->_ownerDiskFolderMap[$ownerId] : array();
		foreach($all as $fid=>$frow)
		{
			$frow['owner_email'] = $ownerEmail;
			$origParent = (int)$frow['parent'];
			$this->_mergeSharedWebdiskTree($result, $frow, $writeAccess, false, false);
			if(isset($result[$fid]))
			{
				$result[$fid]['webdisk_share'] = 1;
				if($origParent === 0)
					$result[$fid]['parent'] = $vid;
			}
		}
	}

	/**
	 * @param array $result
	 * @param array $root
	 * @param bool  $writeAccess
	 * @param bool  $shareRoot
	 * @param bool  $canLeave
	 */
	function _mergeSharedWebdiskTree(&$result, $root, $writeAccess, $shareRoot, $canLeave)
	{
		$ownerId = (int)$root['user'];
		$rootId = (int)$root['id'];
		$ownerEmail = isset($root['owner_email']) ? $root['owner_email'] : '';
		$this->_ensureOwnerDiskFolderMap($ownerId);
		$all = isset($this->_ownerDiskFolderMap[$ownerId]) ? $this->_ownerDiskFolderMap[$ownerId] : array();
		if(!isset($all[$rootId]) && $rootId > 0)
			$all[$rootId] = $root;

		$stack = array($rootId);
		$seen = array();
		while(count($stack) > 0)
		{
			$id = (int)array_pop($stack);
			if(isset($seen[$id]) || $id <= 0)
				continue;
			$seen[$id] = true;
			if(!isset($all[$id]))
				continue;

			$row = $all[$id];
			$isRoot = ($id == $rootId);
			$parent = $isRoot && $shareRoot ? -1 : (isset($row['parent']) ? $row['parent'] : 0);
			$title = isset($row['titel']) ? $row['titel'] : '';

			if(!isset($result[$id]))
			{
				$result[$id] = array(
					'id'			=> $id,
					'titel'			=> $title,
					'parent'		=> $parent,
					'userid'		=> $ownerId,
					'owner_email'	=> $ownerEmail,
					'readonly'		=> $writeAccess ? 0 : 1,
					'share_root'	=> ($isRoot && $shareRoot) ? 1 : 0,
					'can_leave'		=> ($isRoot && $canLeave) ? 1 : 0,
					'webdisk_share'	=> 0
				);
			}
			else
			{
				if($writeAccess)
					$result[$id]['readonly'] = 0;
				if($isRoot && $canLeave)
					$result[$id]['can_leave'] = 1;
				if($isRoot && $shareRoot)
					$result[$id]['share_root'] = 1;
			}

			foreach($all as $childId=>$child)
			{
				if((int)$child['parent'] === $id)
					$stack[] = (int)$childId;
			}
		}
	}

	/**
	 * @param int $ownerId
	 */
	function _ensureOwnerDiskFolderMap($ownerId)
	{
		global $db;

		$ownerId = (int)$ownerId;
		if(isset($this->_ownerDiskFolderMap[$ownerId]))
			return;

		$this->_ownerDiskFolderMap[$ownerId] = array();
		$res = $db->Query('SELECT id,titel,parent,user,share FROM {pre}diskfolders WHERE user=?',
			$ownerId);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$this->_ownerDiskFolderMap[$ownerId][(int)$row['id']] = $row;
		$res->Free();
	}

	/**
	 * map a folder id to owner disk + real folder
	 *
	 * @param int $folderID
	 * @return array|false
	 */
	function ResolveFolderAccess($folderID)
	{
		$folderID = (int)$folderID;
		if($folderID === 0)
		{
			return(array(
				'ownerId'		=> $this->_userID,
				'realFolder'	=> 0,
				'readonly'		=> 0,
				'can_leave'		=> 0,
				'webdisk_share'	=> 0,
				'share_root_id'	=> 0
			));
		}

		$ownerId = self::DecodeSharedWebdiskRoot($folderID);
		$shared = $this->GetAccessibleSharedFolders();
		if($ownerId)
		{
			if(!isset($shared[$folderID]))
				return(false);
			return(array(
				'ownerId'		=> $ownerId,
				'realFolder'	=> 0,
				'readonly'		=> !empty($shared[$folderID]['readonly']),
				'can_leave'		=> !empty($shared[$folderID]['can_leave']),
				'webdisk_share'	=> 1,
				'share_root_id'	=> $folderID
			));
		}

		global $db;
		$res = $db->Query('SELECT id,user,parent FROM {pre}diskfolders WHERE id=?',
			$folderID);
		if($res->RowCount() !== 1)
		{
			$res->Free();
			return(false);
		}
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		if((int)$row['user'] === (int)$this->_userID)
		{
			return(array(
				'ownerId'		=> $this->_userID,
				'realFolder'	=> $folderID,
				'readonly'		=> 0,
				'can_leave'		=> 0,
				'webdisk_share'	=> 0,
				'share_root_id'	=> 0
			));
		}

		if(!isset($shared[$folderID]))
			return(false);

		$shareRootId = $folderID;
		$guard = 0;
		$cursor = $folderID;
		while(isset($shared[$cursor]) && $guard++ < 200)
		{
			if(!empty($shared[$cursor]['share_root']) || !empty($shared[$cursor]['webdisk_share']))
			{
				$shareRootId = $cursor;
				break;
			}
			$parent = (int)$shared[$cursor]['parent'];
			if($parent == -1 || $parent == $cursor)
			{
				$shareRootId = $cursor;
				break;
			}
			$cursor = $parent;
		}

		return(array(
			'ownerId'		=> (int)$shared[$folderID]['userid'],
			'realFolder'	=> $folderID,
			'readonly'		=> !empty($shared[$folderID]['readonly']),
			'can_leave'		=> !empty($shared[$folderID]['can_leave']),
			'webdisk_share'	=> !empty($shared[$folderID]['webdisk_share']),
			'share_root_id'	=> $shareRootId
		));
	}

	/**
	 * @param int $fileID
	 * @return array|false
	 */
	function ResolveFileAccess($fileID)
	{
		global $db;

		$fileID = (int)$fileID;
		$res = $db->Query('SELECT id,user,ordner FROM {pre}diskfiles WHERE id=?',
			$fileID);
		if($res->RowCount() !== 1)
		{
			$res->Free();
			return(false);
		}
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$ownerId = (int)$row['user'];
		$folderID = (int)$row['ordner'];
		if($ownerId === (int)$this->_userID)
		{
			return(array(
				'ownerId'		=> $ownerId,
				'realFolder'	=> $folderID,
				'readonly'		=> 0,
				'can_leave'		=> 0,
				'webdisk_share'	=> 0,
				'share_root_id'	=> 0
			));
		}

		if($folderID === 0)
		{
			$vid = self::EncodeSharedWebdiskRoot($ownerId);
			$shared = $this->GetAccessibleSharedFolders();
			if(!isset($shared[$vid]))
				return(false);
			return(array(
				'ownerId'		=> $ownerId,
				'realFolder'	=> 0,
				'readonly'		=> !empty($shared[$vid]['readonly']),
				'can_leave'		=> 0,
				'webdisk_share'	=> 1,
				'share_root_id'	=> $vid
			));
		}

		$access = $this->ResolveFolderAccess($folderID);
		if($access === false || (int)$access['ownerId'] !== $ownerId)
			return(false);
		return($access);
	}

	/**
	 * path for the UI folder id (own or shared)
	 *
	 * @param int $viewFolderID
	 * @return array
	 */
	function GetViewFolderPath($viewFolderID)
	{
		$access = $this->ResolveFolderAccess($viewFolderID);
		if($access === false)
			return(array());

		if((int)$access['ownerId'] === (int)$this->_userID)
			return($this->GetFolderPath($access['realFolder']));

		$ownerDisk = _new('BMWebdisk', array($access['ownerId']));
		$path = $ownerDisk->GetFolderPath($access['realFolder']);
		if(!empty($access['webdisk_share']))
			return($path);

		$shared = $this->GetAccessibleSharedFolders();
		$trimmed = array();
		$started = false;
		foreach($path as $bit)
		{
			if(!$started)
			{
				if(isset($shared[$bit['id']]) && !empty($shared[$bit['id']]['share_root']))
					$started = true;
				continue;
			}
			$trimmed[] = $bit;
		}
		return($trimmed);
	}

	/**
	 * check if upload of file is forbidden
	 *
	 * @param string $fileName
	 * @param string $mimeType
	 * @return bool
	 */
	function Forbidden($fileName, $mimeType)
	{
		global $bm_prefs;

		$fileName = trim($fileName);
		$mimeType = trim($mimeType);

		// forbidden extensions
		if($fileName != '')
		{
			$forbiddenExtensions = explode(':', $bm_prefs['forbidden_extensions']);
			foreach($forbiddenExtensions as $val)
				if((substr($val, -1) == '*'
					&& strpos(strtolower($fileName), strtolower(substr($val, 0, -1))) !== false)
					|| strlen($val) > 1 && strtolower(substr($fileName, -strlen($val))) == $val)
					return(true);
		}

		// forbidden MIME types
		if($mimeType != '')
		{
			$forbiddenMIMETypes = explode(':', $bm_prefs['forbidden_mimetypes']);
			foreach($forbiddenMIMETypes as $val)
				if(strtolower(trim($val)) == strtolower($mimeType)
					|| (substr($val, -1) == '*'
						&& strtolower(substr($val, 0, -1)) == strtolower(substr($mimeType, 0, strlen($val)-1))))
					return(true);
		}

		// allowed?
		return(false);
	}

	/**
	 * parse a path, return ID of element or false on error
	 *
	 * @param string $path Path
	 * @param bool $withFiles Search for files and folders?
	 * @return array
	 */
	function ParsePath($path, $withFiles = false)
	{
		global $db;

		if($path == '/')
			return(array(0, WEBDISK_ITEM_FOLDER));

		$layers = explode('/', preg_replace('/^\//', '', preg_replace('/\/$/', '', $path)));
		$parent = 0;

		foreach($layers as $layer)
		{
			$res = $db->Query('SELECT id FROM {pre}diskfolders WHERE parent=? AND titel=? AND user=?',
				$parent,
				$layer,
				$this->_userID);
			if($res->RowCount() == 0)
			{
				// not found. file?
				$res = $db->Query('SELECT id FROM {pre}diskfiles WHERE ordner=? AND user=? AND dateiname=?',
					$parent,
					$this->_userID,
					$layer);
				if($res->RowCount() == 0 || !$withFiles)
				{
					return(false);
				}
				else
				{
					list($file) = $res->FetchArray(MYSQLI_NUM);
					$res->Free();

					if(substr($path, -(strlen($layer)+1)) == '/' . $layer)
						return(array($file, WEBDISK_ITEM_FILE));
					else
						return(false);
				}
			}
			else
			{
				// found, continue
				list($parent) = $res->FetchArray(MYSQLI_NUM);
				$res->Free();
				continue;
			}
		}

		return(array($parent, WEBDISK_ITEM_FOLDER));
	}

	/**
	 * get user space limit
	 *
	 * @return int
	 */
	function GetSpaceLimit()
	{
		global $userRow, $groupRow;

		if(isset($userRow) && (int)$userRow['id'] === (int)$this->_userID)
			return($groupRow['webdisk'] + $userRow['diskspace_add']);

		$row = BMUser::staticFetch($this->_userID);
		if(!$row)
			return(0);
		$group = _new('BMGroup', array($row['gruppe']));
		$gRow = is_object($group) ? $group->_row : false;
		if(!is_array($gRow))
			return((int)$row['diskspace_add']);
		return((int)$gRow['webdisk'] + (int)$row['diskspace_add']);
	}

	/**
	 * get used space
	 *
	 * @return int
	 */
	function GetUsedSpace()
	{
		global $db;

		$res = $db->Query('SELECT diskspace_used FROM {pre}users WHERE id=?',
			$this->_userID);
		assert($res->RowCount() != 0);
		list($usedSpace) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($usedSpace);
	}

	/**
	 * set folder share settings
	 *
	 * @param int $folderID Folder ID
	 * @param bool $shareFolder Share folder?
	 * @param string $sharePW Password
	 * @return bool
	 */
	function SetShareSettings($folderID, $shareFolder, $sharePW, $shareUntil = 0)
	{
		global $db;

		$folderID = (int)$folderID;
		$shareUntil = $shareFolder ? max(0, (int)$shareUntil) : 0;

		$db->Query('UPDATE {pre}diskfolders SET share=?, share_pw=?, modified=? WHERE id=? AND user=?',
			$shareFolder ? 'yes' : 'no',
			$sharePW,
			time(),
			$folderID,
			$this->_userID);
		$this->SetShareUntil($folderID, $shareUntil);
		return($db->AffectedRows() == 1);
	}

	/**
	 * get user's shares
	 *
	 * @return array
	 */
	function GetShares()
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT id,titel,share_pw,created,accessed,modified FROM {pre}diskfolders WHERE share=? AND user=? ORDER BY titel ASC',
			'yes',
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$shareUntil = $this->GetShareUntil($row['id']);
			if($this->IsShareExpired($shareUntil))
				continue;

			$shareFolderItem = array(
				'id'		=> $row['id'],
				'type'		=> WEBDISK_ITEM_FOLDER,
				'pw'		=> trim($row['share_pw']) != '',
				'share_until' => $shareUntil,
				'title'		=> $row['titel'],
				'size'		=> 0,
				'created'	=> $row['created'],
				'accessed'	=> $this->FolderDate($row['id'], 'accessed', $row['accessed']),
				'modified'	=> $this->FolderDate($row['id'], 'modified', $row['modified']),
				'ext'		=> '.SHAREDFOLDER'
			);
			$shareFolderItem['icon'] = function_exists('WebdiskGetItemIcon')
				? WebdiskGetItemIcon($shareFolderItem)
				: 'ti-folder-share';
			$result[] = $shareFolderItem;
		}
		$res->Free();

		return($result);
	}

	/**
	 * rename file
	 *
	 * @param int $fileID File ID
	 * @param string $newName New name
	 * @return bool
	 */
	function RenameFile($fileID, $newName)
	{
		global $db;

		if($this->Forbidden($newName, ''))
			return(false);

		$db->Query('UPDATE {pre}diskfiles SET dateiname=?, modified=? WHERE id=? AND user=?',
			$newName,
			time(),
			$fileID,
			$this->_userID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * rename folder
	 *
	 * @param int $folderID Folder ID
	 * @param string $newName New name
	 * @return bool
	 */
	function RenameFolder($folderID, $newName)
	{
		global $db;

		$db->Query('UPDATE {pre}diskfolders SET titel=?,modified=? WHERE id=? AND user=?',
			$newName,
			time(),
			$folderID,
			$this->_userID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * get file info
	 *
	 * @param int $fileID File ID
	 * @return array
	 */
	function GetFileInfo($fileID)
	{
		global $db, $VIEWABLE_TYPES;

		$res = $db->Query('SELECT * FROM {pre}diskfiles WHERE id=? AND user=?',
			$fileID,
			$this->_userID);
		if($res->RowCount() == 0)
			return(false);
		$info = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$ctypeLower = strtolower($info['contenttype']);
		$info['viewable'] = in_array($ctypeLower, $VIEWABLE_TYPES)
			|| (function_exists('WebdiskIsTextPreviewFile') && WebdiskIsTextPreviewFile($info['dateiname'], $ctypeLower))
			|| (function_exists('WebdiskIsMediaPreviewFile') && WebdiskIsMediaPreviewFile($info['dateiname'], $ctypeLower));

		$this->UpdateFileAccess($fileID);

		return($info);
	}

	/**
	 * get folder info
	 *
	 * @param int $folderID Folder ID
	 * @return array
	 */
	function GetFolderInfo($folderID)
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}diskfolders WHERE id=? AND user=?',
			$folderID,
			$this->_userID);
		if($res->RowCount() == 0)
			return(false);
		$info = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();
		$info['share_until'] = $this->GetShareUntil($folderID);

		$this->UpdateFolderAccess($folderID);

		return($info);
	}

	/**
	 * get "common" file info
	 *
	 * @param int $fileID File ID
	 * @return array
	 */
	function GetStructFileInfo($fileID)
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}diskfiles WHERE id=? AND user=?',
			$fileID,
			$this->_userID);
		if($res->RowCount() == 0)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return(array(
				'id'		=> $row['id'],
				'type'		=> WEBDISK_ITEM_FILE,
				'title'		=> $row['dateiname'],
				'share'		=> false,
				'size'		=> $row['size'],
				'ctype'		=> $row['contenttype'],
				'created'	=> $row['created'],
				'accessed'	=> $row['accessed'],
				'modified'	=> $row['modified']
			));
	}

	/**
	 * get "common" folder info
	 *
	 * @param int $folderID Folder ID
	 * @return array
	 */
	function GetStructFolderInfo($folderID)
	{
		global $db;

		if($folderID != 0)
		{
			$res = $db->Query('SELECT * FROM {pre}diskfolders WHERE id=? AND user=?',
				$folderID,
				$this->_userID);
			if($res->RowCount() == 0)
				return(false);
			$row = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			$info = array(
					'id'		=> $row['id'],
					'type'		=> WEBDISK_ITEM_FOLDER,
					'title'		=> $row['titel'],
					'share'		=> $row['share']=='yes',
					'size'		=> 0,
					'created'	=> $row['created'],
					'accessed'	=> $this->FolderDate($folderID, 'accessed', $row['accessed']),
					'modified'	=> $this->FolderDate($folderID, 'modified', $row['modified']),
					'ext'		=> $row['share']=='yes' ? '.SHAREDFOLDER' : '.FOLDER'
				);
		}
		else
		{
			$userRow = BMUser::staticFetch($this->_userID);
			$info = array(
					'id'		=> 0,
					'type'		=> WEBDISK_ITEM_FOLDER,
					'title'		=> '',
					'share'		=> false,
					'size'		=> 0,
					'created'	=> $userRow['reg_date'],
					'accessed'	=> $this->FolderDate($folderID, 'accessed', 0),
					'modified'	=> $this->FolderDate($folderID, 'modified', 0),
					'ext'		=> '.FOLDER'
				);
		}

		$this->UpdateFolderAccess($folderID);

		return($info);
	}

	/**
	 * get DB props for resource
	 *
	 * @param string $path Path
	 * @param int $userID User ID
	 * @return array
	 */
	function GetDBProps($path, $userID)
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT `name`,`value`,`xmlns` FROM {pre}diskprops WHERE user=? AND path=?',
			$userID,
			$path);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$result[] = array(
				'name'	=> $row['name'],
				'xmlns'	=> $row['xmlns'],
				'value'	=> $row['value']
			);
		$res->Free();

		return($result);
	}

	/**
	 * move file to another folder
	 *
	 * @param int $folderID (New) folder ID
	 * @param int $fileID File ID
	 * @param string $newName New name
	 */
	function MoveFile($folderID, $fileID, $newName = false)
	{
		global $db;

		$fileInfo = $this->GetFileInfo($fileID);
		if($this->FileExists($folderID, $newName ? $newName : $fileInfo['dateiname']))
			return(false);

		if($newName && $this->Forbidden($newName, ''))
			return(false);

		$db->Query('UPDATE {pre}diskfiles SET dateiname=?,ordner=?,modified=? WHERE id=? AND user=?',
			$newName ? $newName : $fileInfo['dateiname'],
			$folderID,
			time(),
			$fileID,
			$this->_userID);

		return($db->AffectedRows() == 1);
	}

	/**
	 * move folder to another folder
	 *
	 * @param int $folderID (New) folder ID
	 * @param int $moveID Folder ID
	 * @param string $newName New name
	 */
	function MoveFolder($folderID, $moveID, $newName = false)
	{
		global $db;

		// $folderID must not be a child of $srcFolderID
		if($moveID == $folderID || $this->IsFolderChildOf($folderID, $moveID))
			return(false);

		$folderInfo = $this->GetFolderInfo($moveID);
		if($this->FolderExists($folderID, $newName ? $newName : $folderInfo['titel']))
			return(false);

		$db->Query('UPDATE {pre}diskfolders SET titel=?,parent=?,modified=? WHERE id=? AND user=?',
			$newName ? $newName : $folderInfo['titel'],
			$folderID,
			time(),
			$moveID,
			$this->_userID);

		return($db->AffectedRows() == 1);
	}

	/**
	 * check if folder is shared or a subfolder of a shared folder
	 *
	 * @param int $folderID Folder ID
	 * @return array
	 */
	function IsShared($folderID)
	{
		$path = array_reverse($this->GetFolderPath($folderID));

		foreach($path as $pathItem)
		{
			if($pathItem['share'] == 'yes')
			{
				$shareUntil = $this->GetShareUntil($pathItem['id']);
				if($this->IsShareExpired($shareUntil))
					continue;

				return(array(true, trim($pathItem['share_pw'])));
			}
		}

		return(array(false, ''));
	}

	/**
	 * delete a folder
	 *
	 * @param int $folderID Folder ID
	 * @return bool
	 */
	function DeleteFolder($folderID)
	{
		global $db;

		// delete content first
		$folderContent = $this->GetFolderContent($folderID);
		foreach($folderContent as $item)
		{
			if($item['type'] == WEBDISK_ITEM_FILE)
			{
				$this->DeleteFile($item['id']);
			}
			else if($item['type'] == WEBDISK_ITEM_FOLDER)
			{
				$this->DeleteFolder($item['id']);
			}
		}

		// delete folder
		$db->Query('DELETE FROM {pre}diskfolders WHERE id=? AND user=?',
			$folderID,
			$this->_userID);
		$this->SetShareUntil($folderID, 0);
		bmOrganizerDeleteCollectionShares(BM_ORGANIZER_SHARE_WDFOLDER, (int)$folderID);
		return(true);
	}

	/**
	 * checks if $folderID is a child of $srcFolderID
	 *
	 * @param int $folerID Folder ID
	 * @param int $srcFolderID (Parent) Folder ID
	 */
	function IsFolderChildOf($folderID, $srcFolderID)
	{
		global $db;

		if($folderID == $srcFolderID)
			return(true);

		$res = $db->Query('SELECT id FROM {pre}diskfolders WHERE parent=? AND user=?',
			$srcFolderID,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($row['id'] == $folderID)
			{
				return(true);
			}
			else
			{
				if($this->IsFolderChildOf($folderID, $row['id']))
					return(true);
			}
		}

		return(false);
	}

	/**
	 * copy a folder
	 *
	 * @param int $folderID (Destination) folder ID
	 * @param int $srcFolderID (Source) folder ID
	 * @param int $maxSpace Max space to use
	 * @param string $newName New folder name
	 * @return bool
	 */
	function CopyFolder($folderID, $srcFolderID, &$maxSpace, $newName = false)
	{
		global $db;

		$folderInfo = $this->GetFolderInfo($srcFolderID);
		if($this->FolderExists($folderID, $newName ? $newName : $folderInfo['titel']))
			return(false);

		// $folderID must not be a child of $srcFolderID
		if($this->IsFolderChildOf($folderID, $srcFolderID))
			return(false);

		// create folder
		if(($newFolderID = $this->CreateFolder($folderID, $newName ? $newName : $folderInfo['titel'])) === false)
			return(false);

		// copy items
		$folderContent = $this->GetFolderContent($srcFolderID);
		foreach($folderContent as $item)
		{
			if($item['type'] == WEBDISK_ITEM_FILE)
			{
				if($maxSpace != -1)
				{
					$fileInfo = $this->GetFileInfo($item['id']);
					if(($maxSpace-$fileInfo['size']) >= 0)
					{
						$maxSpace -= $fileInfo['size'];
						$this->CopyFile($newFolderID, $item['id']);
					}
				}
				else
				{
					$this->CopyFile($newFolderID, $item['id']);
				}
			}
			else if($item['type'] == WEBDISK_ITEM_FOLDER)
			{
				$this->CopyFolder($newFolderID, $item['id'], $maxSpace);
			}
		}

		return(true);
	}

	/**
	 * copy a file
	 *
	 * @param int $folderID (New) folder ID
	 * @param int $fileID File ID
	 * @param string $newName New name
	 * @return int
	 */
	function CopyFile($folderID, $fileID, $newName = false)
	{
		global $db;

		$fileInfo = $this->GetFileInfo($fileID);
		if($fileInfo === false || $this->FileExists($folderID, $newName ? $newName : $fileInfo['dateiname']))
			return(false);

		if($newName && $this->Forbidden($newName, ''))
			return(false);

		$id = $this->CreateFile($folderID, $newName ? $newName : $fileInfo['dateiname'], $fileInfo['contenttype'], $fileInfo['size']);
		if($id === false)
			return(false);

		$sourceFP = BMBlobStorage::createProvider($fileInfo['blobstorage'], $this->_userID)->loadBlob(BMBLOB_TYPE_WEBDISK, $fileID);
		if($sourceFP)
		{
			if(BMBlobStorage::createDefaultWebdiskProvider($this->_userID)->storeBlob(BMBLOB_TYPE_WEBDISK, $id, $sourceFP))
			{
				fclose($sourceFP);
				return($id);
			}

			fclose($sourceFP);
			return(0);
		}
		else
		{
			PutLog(sprintf('Cannot copy webdisk file #%d to #%d',
					$fileID,
					$id),
					PRIO_ERROR,
					__FILE__,
					__LINE__);
			$this->DeleteFile($id);
			return(0);
		}
	}

	/**
	 * delete a file
	 *
	 * @param int $fileID File ID
	 * @return bool
	 */
	function DeleteFile($fileID)
	{
		global $db;

		$success = false;

		$info = $this->GetFileInfo($fileID);

		$db->Query('BEGIN');
		$db->Query('DELETE FROM {pre}diskfiles WHERE id=? AND user=?',
			$fileID,
			$this->_userID);
		if($db->AffectedRows() == 1)
		{
			$success = true;
			$this->UpdateSpace(abs($info['size'])*-1);
		}
		$db->Query('COMMIT');

		if($success)
		{
			$this->StopFileShare((int)$fileID);
			BMBlobStorage::createProvider($info['blobstorage'], $this->_userID)->deleteBlob(BMBLOB_TYPE_WEBDISK, $fileID);
		}

		return($success);
	}

	/**
	 * update space
	 *
	 * @param int $bytes Bytes (negative or positive)
	 * @return boolean
	 */
	function UpdateSpace($bytes)
	{
		global $db;

		if($bytes == 0)
			return(true);

		if($bytes < 0)
		{
			$db->Query('UPDATE {pre}users SET diskspace_used=diskspace_used-LEAST(diskspace_used,'.abs($bytes).') WHERE id=?',
				$this->_userID);
		}
		else if($bytes > 0)
		{
			$db->Query('UPDATE {pre}users SET diskspace_used=diskspace_used+' . abs($bytes) . ' WHERE id=?',
				$this->_userID);
		}

		return(true);
	}

	/**
	 * get file size
	 *
	 * @param int $fileID File ID
	 * @return int
	 */
	function GetFileSize($fileID)
	{
		global $db;

		$res = $db->Query('SELECT size FROM {pre}diskfiles WHERE id=? AND user=?',
			$fileID,
			$this->_userID);
		list($size) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		$this->UpdateFileAccess($fileID);

		return($size);
	}

	/**
	 * replace file contents (text files)
	 *
	 * @param int $fileID File ID
	 * @param string $content New file content
	 * @return bool|string true on success, error code string on failure
	 */
	function UpdateFileContent($fileID, $content)
	{
		global $db, $groupRow, $userRow;

		$fileInfo = $this->GetFileInfo((int)$fileID);
		if($fileInfo === false)
			return('notfound');

		$ctypeLower = strtolower($fileInfo['contenttype']);
		if(!function_exists('WebdiskIsTextPreviewFile')
			|| !WebdiskIsTextPreviewFile($fileInfo['dateiname'], $ctypeLower))
			return('forbidden');

		if(!WebdiskIsLikelyTextContent($content))
			return('binary');

		$newSize = strlen($content);
		if($newSize > WebdiskGetTextEditMaxBytes())
			return('toolarge');

		$oldSize = (int)$fileInfo['size'];
		$sizeDiff = $newSize - $oldSize;

		$spaceLimit = $this->GetSpaceLimit();
		$usedSpace = $this->GetUsedSpace();

		if($sizeDiff > 0 && $spaceLimit != -1 && ($usedSpace + $sizeDiff) > $spaceLimit)
			return('nospace');

		if($groupRow['traffic'] > 0
			&& ($userRow['traffic_down'] + $userRow['traffic_up'] + $newSize) > ($groupRow['traffic'] + $userRow['traffic_add']))
			return('notraffic');

		$fp = fopen('php://temp', 'wb+');
		if(!$fp)
			return('internal');

		fwrite($fp, $content);
		fseek($fp, 0, SEEK_SET);

		$provider = BMBlobStorage::createProvider($fileInfo['blobstorage'], $this->_userID);
		if(!$provider->storeBlob(BMBLOB_TYPE_WEBDISK, (int)$fileID, $fp))
		{
			fclose($fp);
			return('internal');
		}
		fclose($fp);

		if($sizeDiff != 0)
			$this->UpdateSpace($sizeDiff);

		$db->Query('UPDATE {pre}diskfiles SET size=?, modified=? WHERE id=? AND user=?',
			$newSize,
			time(),
			(int)$fileID,
			$this->_userID);

		if($newSize > 0)
		{
			$db->Query('UPDATE {pre}users SET traffic_up=traffic_up+? WHERE id=?',
				$newSize,
				$this->_userID);
			Add2Stat('wd_up', ceil($newSize / 1024));
		}

		$this->UpdateFileAccess((int)$fileID);

		return(true);
	}

	/**
	 * get total size of all files in a folder tree
	 *
	 * @param int $folderID Folder ID
	 * @return int
	 */
	function GetFolderTreeSize($folderID)
	{
		global $db;

		if(!$this->GetFolderInfo($folderID))
			return(0);

		$total = 0;

		$res = $db->Query('SELECT COALESCE(SUM(size), 0) AS total FROM {pre}diskfiles WHERE ordner=? AND user=?',
			$folderID,
			$this->_userID);
		if($row = $res->FetchArray(MYSQLI_ASSOC))
			$total += (int)$row['total'];
		$res->Free();

		$res = $db->Query('SELECT id FROM {pre}diskfolders WHERE parent=? AND user=?',
			$folderID,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$total += $this->GetFolderTreeSize((int)$row['id']);
		$res->Free();

		return($total);
	}

	/**
	 * get stats for a multi-item selection
	 *
	 * @param array $folderIDs Folder IDs
	 * @param array $fileIDs File IDs
	 * @return array
	 */
	function GetSelectionStats($folderIDs, $fileIDs)
	{
		$totalSize = 0;

		foreach($fileIDs as $fileID)
		{
			$fileInfo = $this->GetFileInfo((int)$fileID);
			if($fileInfo)
				$totalSize += (int)$fileInfo['size'];
		}

		foreach($folderIDs as $folderID)
			$totalSize += $this->GetFolderTreeSize((int)$folderID);

		return(array(
			'count'			=> count($fileIDs) + count($folderIDs),
			'fileCount'		=> count($fileIDs),
			'folderCount'	=> count($folderIDs),
			'totalSize'		=> $totalSize
		));
	}

	/**
	 * create a new file, returns path to datafile
	 *
	 * @param int $folderID Parent folder
	 * @param string $fileName File name
	 * @param string $mimeType Mime type
	 * @param int $fileSize File size
	 * @return string
	 */
	function CreateFile($folderID, $fileName, $mimeType, $fileSize)
	{
		global $db, $userRow;

		if($this->Forbidden($fileName, $mimeType)
			|| $this->FileExists($folderID, $fileName))
			return(false);

		$uploaderID = (isset($userRow['id']) && (int)$userRow['id'] > 0)
			? (int)$userRow['id']
			: (int)$this->_userID;
		if($uploaderID <= 0)
			$uploaderID = (int)$this->_userID;

		$db->Query('BEGIN');
		$db->Query('INSERT INTO {pre}diskfiles(user,dateiname,ordner,size,contenttype,created,accessed,modified,blobstorage,uploader_id) VALUES(?,?,?,?,?,?,?,?,?,?)',
			$this->_userID,
			$fileName,
			$folderID,
			$fileSize,
			$mimeType,
			time(),
			time(),
			time(),
			BMBlobStorage::getDefaultWebdiskProvider(),
			$uploaderID);
		$id = $db->InsertId();
		$this->UpdateSpace($fileSize);
		$db->Query('COMMIT');

		assert($id > 0);

		return($id);
	}

	/**
	 * check if file exists, return id
	 *
	 * @param int $folderID Parent folder
	 * @param string $fileName File name
	 * @return int
	 */
	function FileExists($folderID, $fileName)
	{
		global $db;

		$res = $db->Query('SELECT id FROM {pre}diskfiles WHERE dateiname=? AND ordner=? AND user=?',
			$fileName,
			$folderID,
			$this->_userID);
	 	if($res->RowCount() != 0)
	 	{
			list($id) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
			return($id);
	 	}

	 	return(0);
	}

	/**
	 * get path to file
	 *
	 * @param int $fileID File ID
	 */
	function GetFilePath($fileID)
	{
		global $db;

		$res = $db->Query('SELECT dateiname,ordner FROM {pre}diskfiles WHERE id=? AND user=?',
			$fileID,
			$this->_userID);
		assert($res->RowCount() != 0);
		list($thisFilename, $thisFolder) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		$path = $this->GetFolderPath($thisFolder);
		$path[] = array('id' => $fileID, 'title' => $thisFilename);

		return($path);
	}

	/**
	 * check if folder exists
	 *
	 * @param int $folderID Parent folder
	 * @param string $folderName Folder name
	 * @return bool
	 */
	function FolderExists($folderID, $folderName)
	{
		global $db;

		$res = $db->Query('SELECT id FROM {pre}diskfolders WHERE titel=? AND parent=? AND user=?',
			$folderName,
			$folderID,
			$this->_userID);
	 	if($res->RowCount() != 0)
	 	{
			list($id) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
			return($id);
	 	}

		return(0);
	}

	/**
	 * create a new folder
	 *
	 * @param int $folderID Parent folder
	 * @param string $folderName Folder name
	 * @return bool
	 */
	function CreateFolder($folderID, $folderName)
	{
		global $db;

		if($this->FolderExists($folderID, $folderName))
			return(false);

		$db->Query('INSERT INTO {pre}diskfolders(user,parent,titel,share,share_pw,created,accessed,modified) VALUES(?,?,?,?,?,?,?,?)',
			$this->_userID,
			$folderID,
			$folderName,
			'no',
			'',
			time(),
			time(),
			time());

		return($db->InsertId());
	}

	/**
	 * get path to folder
	 *
	 * @param int $folderID Folder ID
	 */
	function GetFolderPath($folderID)
	{
		global $db;

		$path = array();
		$parentID = $folderID;

		while($parentID != 0)
		{
			$res = $db->Query('SELECT id,titel,parent,share,share_pw FROM {pre}diskfolders WHERE id=? AND user=?',
				$parentID,
				$this->_userID);
			if($res->RowCount() == 0)
				break;
			list($thisID, $thisTitle, $parentID, $share, $share_pw) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			$path[] = array(
				'id' => $thisID,
				'title' => $thisTitle,
				'share' => $share,
				'share_pw' => $share_pw,
				'share_until' => $this->GetShareUntil($thisID)
			);
		}

		$path = array_reverse($path);
		return($path);
	}

	/**
	 * get folder parent
	 *
	 * @param int $id Folder ID
	 * @return int
	 */
	function GetFolderParent($id)
	{
		global $db;

		$res = $db->Query('SELECT parent FROM {pre}diskfolders WHERE user=? AND id=?',
			$this->_userID,
			$id);
		if($res->RowCount() > 0)
		{
			list($parent) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
			return($parent);
		}

		return(0);
	}

	/**
	 * get folder contents
	 *
	 * @param integer $folderID Folder ID
	 */
	function GetFolderContent($folderID, $sort = 'dateiname', $order = 'ASC')
	{
		global $db, $VIEWABLE_TYPES, $thisUser, $groupRow;

		if(isset($thisUser) && is_object($thisUser))
			$hideHidden = $thisUser->GetPref('webdisk_hideHidden');
		else
			$hideHidden = false;

		$result = array();
		if(!in_array($sort, array('dateiname', 'size')))
			$sort = 'dateiname';

		// folders
		$res = $db->Query('SELECT id,titel,share,created,accessed,modified FROM {pre}diskfolders WHERE parent=? AND user=? ORDER BY titel ' . $order,
			$folderID,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($row['titel'][0] == '.' && $hideHidden)
				continue;

			$folderItem = array(
				'id'		=> $row['id'],
				'type'		=> WEBDISK_ITEM_FOLDER,
				'title'		=> $row['titel'],
				'share'		=> $row['share']=='yes',
				'size'		=> 0,
				'created'	=> $row['created'],
				'accessed'	=> $this->FolderDate($row['id'], 'accessed', $row['accessed']),
				'modified'	=> $this->FolderDate($row['id'], 'modified', $row['modified']),
				'ext'		=> $row['share']=='yes' ? '.SHAREDFOLDER' : '.FOLDER',
				'viewable'	=> true,
				'thumbnail'	=> false
			);
			$folderItem['icon'] = function_exists('WebdiskGetItemIcon')
				? WebdiskGetItemIcon($folderItem)
				: 'ti-folder';
			$result[] = $folderItem;
		}
		$res->Free();

		// file
		$res = $db->Query('SELECT id,dateiname,size,created,accessed,modified,contenttype FROM {pre}diskfiles WHERE ordner=? AND user=? ORDER BY ' . $sort . ' ' . $order,
			$folderID,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($row['dateiname'][0] == '.' && $hideHidden)
				continue;

			$dotPos = strrchr($row['dateiname'], '.');
			if($dotPos !== false)
				$ext = substr($dotPos, 1);
			else
				$ext = '?';
			$ctypeLower = strtolower($row['contenttype']);
			$thumbsEnabled = isset($groupRow['wd_thumbnails']) && $groupRow['wd_thumbnails'] == 'yes';

			$fileItem = array(
				'id'		=> $row['id'],
				'type'		=> WEBDISK_ITEM_FILE,
				'title'		=> $row['dateiname'],
				'share'		=> $this->IsFileShared((int)$row['id']),
				'size'		=> (int)$row['size'],
				'created'	=> (int)$row['created'],
				'accessed'	=> (int)$row['accessed'],
				'modified'	=> (int)$row['modified'],
				'ctype'		=> $row['contenttype'],
				'ext'		=> $ext,
				'viewable'	=> in_array($ctypeLower, $VIEWABLE_TYPES)
								|| (function_exists('WebdiskIsTextPreviewFile') && WebdiskIsTextPreviewFile($row['dateiname'], $ctypeLower))
								|| (function_exists('WebdiskIsMediaPreviewFile') && WebdiskIsMediaPreviewFile($row['dateiname'], $ctypeLower)),
				'thumbnail'	=> $thumbsEnabled && strpos($ctypeLower, 'image/') === 0 && $ctypeLower !== 'image/svg+xml'
			);
			$fileItem['icon'] = function_exists('WebdiskGetItemIcon')
				? WebdiskGetItemIcon($fileItem)
				: 'ti-file';
			$result[] = $fileItem;
		}
		$res->Free();

		$this->UpdateFolderAccess($folderID);

		return($result);
	}

	/**
	 * display a file extension
	 *
	 * @param string $ext Extension
	 */
	function DisplayExtension($ext)
	{
		if(LEGACY_WEBDISCICONS===true) {
			global $db;

			$res = $db->Query("SELECT bild,ctype FROM {pre}extensions WHERE (ext='".$db->Escape($ext)."') OR (ext LIKE '".$db->Escape($ext).",%') OR (ext LIKE '%,".$db->Escape($ext).",%') OR (ext LIKE '%,".$db->Escape($ext)."') LIMIT 1");
			if($res->RowCount() == 0 && $ext != '.?')
				return(BMWebdisk::DisplayExtension('.?'));
			list($img, $ctype) = $res->FetchArray(MYSQLI_NUM);
			if(empty($img)) {
				return readfile(B1GMAIL_DIR.'res/empty.gif');
			}

			$lastModifiedTime = mktime(0, 0, 0);
			$eTag = md5($img);

			header('Cache-Control: private');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModifiedTime) . ' GMT');
			header('ETag: ' . $eTag);

			if(@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModifiedTime
				|| (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $eTag))
			{
				header('HTTP/1.1 304 Not Modified');
			}
			else
			{
				$img = base64_decode($img);

				header('Content-Type: ' . $ctype);
				header('Content-Length: ' . strlen($img));

				echo $img;
			}
		}
	}

	/**
	 * update folder access time
	 *
	 * @param int $folderID Folder ID
	 */
	function UpdateFolderAccess($folderID)
	{
		global $db;

		$db->Query('UPDATE {pre}diskfolders SET accessed=? WHERE user=? AND id=?',
			time(),
			$this->_userID,
			$folderID);
	}

	/**
	 * update file access time
	 *
	 * @param int $fileID File ID
	 */
	function UpdateFileAccess($fileID)
	{
		global $db;

		$db->Query('UPDATE {pre}diskfiles SET accessed=? WHERE user=? AND id=?',
			time(),
			$this->_userID,
			$fileID);
	}

	/**
	 * get folder date properties
	 *
	 * @param int $id ID
	 * @param string $type Type
	 * @param int $alt Alternative value
	 * @return int
	 */
	function FolderDate($id, $type, $alt)
	{
		global $db;

		if(!in_array($type, array('modified', 'accessed', 'created')))
			return(false);

		$res = $db->Query('SELECT '.$type.' FROM {pre}diskfiles WHERE ordner=? ORDER BY '.$type.' DESC LIMIT 1',
			$id);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			if($row[$type] > $alt)
				$alt = $row[$type];
		$res->Free();

		$res = $db->Query('SELECT '.$type.' FROM {pre}diskfolders WHERE parent=? ORDER BY '.$type.' DESC LIMIT 1',
			$id);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			if($row[$type] > $alt)
				$alt = $row[$type];
		$res->Free();

		return($alt);
	}

	/**
	 * add a folder to a ZIP archive
	 *
	 * @param int $folderID Folder ID
	 * @param BMZIP $zip BMZIP object
	 * @param string $path Path in ZIP file
	 * @return bool
	 */
	function ZipFolder($folderID, &$zip, $path = '')
	{
		$folderInfo = $this->GetFolderInfo($folderID);
		$folderContents = $this->GetFolderContent($folderID);
		if(!$folderContents)
			return(false);

		if($path == '')
			$path = $folderInfo['titel'] . '/';

		foreach($folderContents as $item)
		{
			if($item['type'] == WEBDISK_ITEM_FOLDER)
				$this->ZipFolder($item['id'], $zip, $path . $item['title'] . '/');
			elseif($item['type'] == WEBDISK_ITEM_FILE)
				$this->ZipFile($item['id'], $zip, $path);
		}

		return(true);
	}

	/**
	 * add a file to a ZIP archive
	 *
	 * @param int $fileID File ID
	 * @param BMZIP $zip BMZIP object
	 * @param string $path Path in ZIP file
	 * @return bool
	 */
	function ZipFile($fileID, &$zip, $path = '')
	{
		$fileInfo = $this->GetFileInfo($fileID);
		if(!$fileInfo)
			return(false);

		if($path == '')
			$fileName = $fileInfo['dateiname'];
		else
			$fileName = substr($path, -1) == '/' ? $path . $fileInfo['dateiname'] : $path . '/' . $fileInfo['dateiname'];

		$sourceFP = BMBlobStorage::createProvider($fileInfo['blobstorage'], $this->_userID)->loadBlob(BMBLOB_TYPE_WEBDISK, $fileID);
		if($sourceFP)
		{
			$zip->AddFileByFP($sourceFP, $fileName);
			fclose($sourceFP);

			return(true);
		}

		return(false);
	}
}
