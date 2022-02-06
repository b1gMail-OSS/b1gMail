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

		$pageMenu[] = array(
			'i'				=> $i++,
			'icon'			=> 'folder',
			'id'			=> 0,
			'parent'		=> -1,
			'text'			=> $lang_user['webdisk']
		);

		$res = $db->Query('SELECT `id`,`titel`,`parent`,`share` FROM {pre}diskfolders WHERE `user`=? ORDER BY `titel` ASC',
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$idTable[$row['id']] = $i;

			$pageMenu[] = array(
				'id'		=> $row['id'],
				'icon'		=> $row['share'] == 'yes' ? 'folder_shared' : 'folder',
				'i'			=> $i,
				'parent'	=> $row['parent'],
				'text'		=> $row['titel']
			);

			$i++;
		}
		$res->Free();

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

		if(isset($userRow) && $userRow['id'] == $this->_userID)
			return($groupRow['webdisk'] + $userRow['diskspace_add']);

		assert(false);
		return(0);
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
	function SetShareSettings($folderID, $shareFolder, $sharePW)
	{
		global $db;

		$db->Query('UPDATE {pre}diskfolders SET share=?, share_pw=?, modified=? WHERE id=? AND user=?',
			$shareFolder ? 'yes' : 'no',
			$sharePW,
			time(),
			$folderID,
			$this->_userID);
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
			$result[] = array(
				'id'		=> $row['id'],
				'type'		=> WEBDISK_ITEM_FOLDER,
				'pw'		=> trim($row['share_pw']) != '',
				'title'		=> $row['titel'],
				'size'		=> 0,
				'created'	=> $row['created'],
				'accessed'	=> $this->FolderDate($row['id'], 'accessed', $row['accessed']),
				'modified'	=> $this->FolderDate($row['id'], 'modified', $row['modified']),
				'ext'		=> '.SHAREDFOLDER'
			);
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

		$info['viewable'] = in_array(strtolower($info['contenttype']), $VIEWABLE_TYPES);

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
			$userRow = BMUser::Fetch($this->_userID);
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
			BMBlobStorage::createProvider($info['blobstorage'], $this->_userID)->deleteBlob(BMBLOB_TYPE_WEBDISK, $fileID);

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
		global $db;

		if($this->Forbidden($fileName, $mimeType)
			|| $this->FileExists($folderID, $fileName))
			return(false);

		$db->Query('BEGIN');
		$db->Query('INSERT INTO {pre}diskfiles(user,dateiname,ordner,size,contenttype,created,accessed,modified,blobstorage) VALUES(?,?,?,?,?,?,?,?,?)',
			$this->_userID,
			$fileName,
			$folderID,
			$fileSize,
			$mimeType,
			time(),
			time(),
			time(),
			BMBlobStorage::getDefaultWebdiskProvider());
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

			$path[] = array('id' => $thisID, 'title' => $thisTitle, 'share' => $share, 'share_pw' => $share_pw);
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
		global $db, $VIEWABLE_TYPES, $thisUser;

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

			$result[] = array(
				'id'		=> $row['id'],
				'type'		=> WEBDISK_ITEM_FOLDER,
				'title'		=> $row['titel'],
				'share'		=> $row['share']=='yes',
				'size'		=> 0,
				'created'	=> $row['created'],
				'accessed'	=> $this->FolderDate($row['id'], 'accessed', $row['accessed']),
				'modified'	=> $this->FolderDate($row['id'], 'modified', $row['modified']),
				'ext'		=> $row['share']=='yes' ? '.SHAREDFOLDER' : '.FOLDER',
				'viewable'	=> true
			);
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
			$result[] = array(
				'id'		=> $row['id'],
				'type'		=> WEBDISK_ITEM_FILE,
				'title'		=> $row['dateiname'],
				'size'		=> (int)$row['size'],
				'created'	=> (int)$row['created'],
				'accessed'	=> (int)$row['accessed'],
				'modified'	=> (int)$row['modified'],
				'ctype'		=> $row['contenttype'],
				'ext'		=> $ext,
				'viewable'	=> in_array(strtolower($row['contenttype']), $VIEWABLE_TYPES)
			);
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
		global $db;

		$res = $db->Query("SELECT bild,ctype FROM {pre}extensions WHERE (ext='$ext') OR (ext LIKE '$ext,%') OR (ext LIKE '%,$ext,%') OR (ext LIKE '%,$ext') LIMIT 1");
		if($res->RowCount() == 0 && $ext != '.?')
			return(BMWebdisk::DisplayExtension('.?'));
		list($img, $ctype) = $res->FetchArray(MYSQLI_NUM);

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
