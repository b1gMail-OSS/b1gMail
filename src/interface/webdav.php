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

define('INTERFACE_MODE', 		true);
define('WEBDAV_ALT_TIMESTAMP', 	1411119133);

include('../serverlib/init.inc.php');
include('../serverlib/dav.inc.php');
include('../serverlib/webdisk.class.php');

use Sabre\DAV, Sabre\HTTP\URLUtil;

class BMWebdiskState extends BMSessionState
{
	public $webdisk;
}

abstract class BMWebdiskNode extends Sabre\DAV\FS\Node
{
	protected function getDataSize(&$data)
	{
		$fileSize = 0;

		if($data !== null)
		{
			if(is_resource($data))
			{
				$data2 = fopen('php://temp', 'r+');

				while(!feof($data))
				{
					$chunk = fread($data, 4096);
					fwrite($data2, $chunk);
					$fileSize += strlen($chunk);
				}

				rewind($data2);
				$data = $data2;
			}
			else if(is_string($data))
			{
				$fileSize = strlen($data);
			}
		}

		return($fileSize);
	}
}

class BMWebdiskFile extends BMWebdiskNode implements Sabre\DAV\IFile
{
	private $fileID = -1;

	public function __construct($path, $fileID = -1, $fileRow = false)
	{
		global $wds;

		$this->path = $path;
		$this->fileID = $fileID;
		$this->fileRow = $fileRow;

		if($this->fileRow === false)
			$this->fileRow = $wds->webdisk->GetFileInfo($this->getFileID());
	}

	private function getFileID()
	{
		global $wds;

		if($this->fileID == -1)
		{
			list($fileID, $itemType) = $wds->webdisk->ParsePath($this->path);

			if($itemType == WEBDISK_ITEM_FILE)
			{
				$this->fileID = $fileID;
			}
			else
			{
				throw new Sabre\DAV\Exception\NotFound('File ' . $this->path . ' not found');
			}
		}

		return($this->fileID);
	}

	public function setName($name)
	{
		global $wds;

		list($parentPath, ) = URLUtil::splitPath($this->path);
		list(, $newName) = URLUtil::splitPath($name);

		$wds->webdisk->RenameFile($this->getFileID(), $newName);

		$this->path = $parentPath . '/' . $newName;
	}

	public function put($data)
	{
		global $wds, $db;

		$fileSize = $this->getDataSize($data);

		$sizeDiff = $this->fileRow['size'] - $fileSize;

		$spaceLimit = $wds->groupRow['webdisk'] + $wds->userRow['diskspace_add'];
		$usedSpace = $wds->webdisk->GetUsedSpace();

		if($sizeDiff >= 0 && $spaceLimit != -1 && $usedSpace+$sizeDiff > $spaceLimit)
		{
			throw new Sabre\DAV\Exception\InsufficientStorage;
		}

		if($wds->groupRow['traffic'] > 0 &&
			($wds->userRow['traffic_down']+$wds->userRow['traffic_up']+$fileSize) > $wds->groupRow['traffic'] + $wds->userRow['traffic_add'])
		{
			throw new BMBandwidthException;
		}

		$wds->webdisk->UpdateSpace($sizeDiff);

		$db->Query('UPDATE {pre}users SET `traffic_up`=`traffic_up`+? WHERE `id`=?',
			$fileSize,
			$wds->userRow['id']);
		Add2Stat('wd_up', ceil($fileSize/1024));

		$db->Query('UPDATE {pre}diskfiles SET `size`=? WHERE `id`=? AND `user`=?',
			$fileSize,
			$this->getFileID(),
			$wds->userRow['id']);

		BMBlobStorage::createDefaultWebdiskProvider($wds->userRow['id'])
			->storeBlob(BMBLOB_TYPE_WEBDISK, $this->getFileID(), $data);
	}

	public function get()
	{
		global $wds, $db;

		$file = $wds->webdisk->GetFileInfo($this->getFileID());
		if(!$file)
			return(false);

		$provider = BMBlobStorage::createProvider($file['blobstorage'], $wds->userRow['id']);

		$fileSize = $provider->getBlobSize(BMBLOB_TYPE_WEBDISK, $file['id']);

		if($wds->groupRow['traffic'] > 0 &&
			($wds->userRow['traffic_down']+$wds->userRow['traffic_up']+$fileSize) > $wds->groupRow['traffic'] + $wds->userRow['traffic_add'])
		{
			throw new BMBandwidthException;
		}

		$sourceFP = $provider->loadBlob(BMBLOB_TYPE_WEBDISK, $file['id']);
		if(!$sourceFP)
			return(false);

		$db->Query('UPDATE {pre}users SET `traffic_down`=`traffic_down`+? WHERE `id`=?',
			$fileSize,
			$wds->userRow['id']);
		Add2Stat('wd_down', ceil($fileSize/1024));

		return($sourceFP);
	}

	public function delete()
	{
		global $wds;

		$wds->webdisk->DeleteFile($this->getFileID());
	}

	public function getSize()
	{
		return($this->fileRow['size']);
	}

	public function getETag()
	{
		global $wds;

		$file = $wds->webdisk->GetFileInfo($this->getFileID());
		if(!$file)
			return(false);

		$etag = '';
		$fp = BMBlobStorage::createProvider($file['blobstorage'], $wds->userRow['id'])->loadBlob(BMBLOB_TYPE_WEBDISK, $file['id']);
		if($fp)
		{
			while(!feof($fp))
				$etag = md5($etag . fread($fp, 4096));
			fclose($fp);
		}

		return('"' . $etag . '"');
	}

	public function getContentType()
	{
		return(isset($this->fileRow['ctype'])
			? $this->fileRow['ctype']
			: $this->fileRow['contenttype']);
	}

	public function getLastModified()
	{
		return($this->fileRow['modified']);
	}
}

class BMWebdiskDirectory extends BMWebdiskNode implements Sabre\DAV\ICollection, Sabre\DAV\IQuota
{
	private $folderID = -1;

	public function __construct($path, $folderID = -1)
	{
		$this->path = $path;
		$this->folderID = $folderID;
	}

	private function getFolderID()
	{
		global $wds;

		if($this->folderID == -1)
		{
			list($folderID, $itemType) = $wds->webdisk->ParsePath($this->path == '' ? '/' : $this->path);

			if($itemType == WEBDISK_ITEM_FOLDER)
			{
				$this->folderID = $folderID;
			}
			else
			{
				throw new Sabre\DAV\Exception\NotFound('Folder ' . $this->path . ' not found');
			}
		}

		return($this->folderID);
	}

	public function setName($name)
	{
		global $wds;

		list($parentPath, ) = URLUtil::splitPath($this->path);
		list(, $newName) = URLUtil::splitPath($name);

		$wds->webdisk->RenameFolder($this->getFolderID(), $newName);

		$this->path = $parentPath . '/' . $newName;
	}

	public function createFile($name, $data = null)
	{
		global $wds, $db;

		$fileSize = $this->getDataSize($data);

		if(($oldFileID = $wds->webdisk->FileExists($this->getFolderID(), $name)))
		{
			$wds->webdisk->DeleteFile($oldFileID);
		}

		$spaceLimit = $wds->groupRow['webdisk'] + $wds->userRow['diskspace_add'];
		$usedSpace = $wds->webdisk->GetUsedSpace();

		if($spaceLimit != -1 && $usedSpace+$fileSize > $spaceLimit)
		{
			throw new Sabre\DAV\Exception\InsufficientStorage();
		}

		if($wds->groupRow['traffic'] > 0 &&
			($wds->userRow['traffic_down']+$wds->userRow['traffic_up']+$fileSize) > $wds->groupRow['traffic'] + $wds->userRow['traffic_add'])
		{
			throw new BMBandwidthException;
		}

		$db->Query('UPDATE {pre}users SET `traffic_up`=`traffic_up`+? WHERE `id`=?',
			$fileSize,
			$wds->userRow['id']);
		Add2Stat('wd_up', ceil($fileSize/1024));

		$fileID = $wds->webdisk->CreateFile($this->getFolderID(),
			$name,
			GuessMIMEType($name),
			$fileSize);
		if($fileID === false)
		{
			throw new Sabre\DAV\Exception\Forbidden();
		}

		BMBlobStorage::createDefaultWebdiskProvider($wds->userRow['id'])->storeBlob(BMBLOB_TYPE_WEBDISK, $fileID, $data);
	}

	public function createDirectory($name)
	{
		global $wds;

		$wds->webdisk->CreateFolder($this->getFolderID(), $name);
	}

	public function getChild($name)
	{
		global $wds;

		$path = $this->path . '/' . $name;

		$item = $wds->webdisk->ParsePath($path, true);
		if($item === false || !is_array($item))
			throw new Sabre\DAV\Exception\NotFound('Item ' . $path . ' not found');

		if($item[1] == WEBDISK_ITEM_FOLDER)
		{
			return(new BMWebdiskDirectory($path, $item[0]));
		}
		else if($item[1] == WEBDISK_ITEM_FILE)
		{
			return(new BMWebdiskFile($path, $item[0]));
		}
	}

	public function getChildren()
	{
		global $wds;

		$result = array();

		$contents = $wds->webdisk->GetFolderContent($this->getFolderID());
		foreach($contents as $item)
		{
			if($item['type'] == WEBDISK_ITEM_FOLDER)
			{
				$result[] = new BMWebdiskDirectory($this->path . '/' . $item['title'], $item['id']);
			}
			else if($item['type'] == WEBDISK_ITEM_FILE)
			{
				$result[] = new BMWebdiskFile($this->path . '/' . $item['title'], $item['id'], $item);
			}
		}

		return($result);
	}

	public function childExists($name)
	{
		global $wds;

		return($wds->webdisk->FileExists($this->getFolderID(), $name)
			|| $wds->webdisk->FolderExists($this->getFolderID(), $name));
	}

	public function delete()
	{
		global $wds;

		$wds->webdisk->DeleteFolder($this->getFolderID());
	}

	public function getQuotaInfo()
	{
		global $wds;

		$usedSpace = $wds->webdisk->GetUsedSpace();
		$spaceLimit = $wds->groupRow['webdisk'] + $wds->userRow['diskspace_add'];
		$freeSpace = $spaceLimit - $usedSpace;

		return(array($usedSpace, $freeSpace));
	}

	public function getLastModified()
	{
		global $wds;

		return($wds->webdisk->FolderDate($this->getFolderID(), 'modified', WEBDAV_ALT_TIMESTAMP));
	}
}

class BMWebdiskAuthBackend extends BMAuthBackend
{
	function checkPermissions()
	{
		return($this->groupRow['webdav'] == 'yes');
	}

	function setupState()
	{
		global $wds;

		$wds->userObject 	= $this->userObject;
		$wds->groupObject 	= $this->groupObject;
		$wds->userRow 		= $this->userRow;
		$wds->groupRow 		= $this->groupRow;
		$wds->webdisk 		= _new('BMWebdisk', array($wds->userRow['id']));
	}
}

class BMWebdiskLockBackend extends Sabre\DAV\Locks\Backend\AbstractBackend
{
	public function getLocks($uri, $returnChildLocks)
	{
		global $wds, $db;

		$result = array();

		$res = $db->Query('SELECT * FROM {pre}disklocks WHERE `user`=?',
			$wds->userRow['id']);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($row['path'] == $uri
				|| ($row['type'] != 0 && strpos($uri, $row['path'] . '/') === 0)
				|| ($returnChildLocks && (strpos($row['path'], $uri . '/')===0)))
			{
				$lockInfo = new Sabre\DAV\Locks\LockInfo;
				$lockInfo->owner 		= $row['owner'];
				$lockInfo->token 		= $row['token'];
				$lockInfo->timeout 		= $row['expires'] - $row['modified'];
				$lockInfo->created 		= $row['created'];
				$lockInfo->scope 		= $row['scope'];
				$lockInfo->depth 		= $row['type'];
				$lockInfo->uri 			= $row['path'];
				$result[] = $lockInfo;
			}
		}
		$res->Free();

		return($result);
	}

	public function lock($uri, Sabre\DAV\Locks\LockInfo $lockInfo)
	{
		global $wds, $db;

		$lockInfo->timeout = 1800;
		$lockInfo->uri = $uri;

		$res = $db->Query('SELECT COUNT(*) FROM {pre}disklocks WHERE `user`=? AND `path`=? AND `token`=?',
			$wds->userRow['id'],
			$lockInfo->uri,
			$lockInfo->token);
		list($lockCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		if($lockCount > 0)
		{
			$db->Query('UPDATE {pre}disklocks SET `expires`=?,`modified`=?,`scope`=?,`owner`=?,`type`=? WHERE `user`=? AND `path`=? AND `token`=?',
				time()+$lockInfo->timeout,
				time(),
				$lockInfo->scope,
				$lockInfo->owner,
				$lockInfo->depth,
				$wds->userRow['id'],
				$lockInfo->uri,
				$lockInfo->token);
			return(true);
		}
		else
		{
			$db->Query('REPLACE INTO {pre}disklocks(`user`,`path`,`token`,`type`,`created`,`modified`,`expires`,`scope`,`owner`) '
				. 'VALUES(?,?,?,?,?,?,?,?,?)',
				$wds->userRow['id'],
				$lockInfo->uri,
				$lockInfo->token,
				$lockInfo->depth,
				time(),
				time(),
				time()+$lockInfo->timeout,
				$lockInfo->scope,
				$lockInfo->owner);
		}

		return(true);
	}

	public function unlock($uri, Sabre\DAV\Locks\LockInfo $lockInfo)
	{
		global $wds, $db;

		$lockInfo->uri = $uri;

		$db->Query('DELETE FROM {pre}disklocks WHERE `user`=? AND `token`=?',
			$wds->userRow['id'],
			$lockInfo->token);

		return($db->AffectedRows() > 0);
	}
}

$wds = new BMWebdiskState;

$rootDirectory = new BMWebdiskDirectory('');

$server = new DAV\Server($rootDirectory);
$server->setBaseUri($_SERVER['SCRIPT_NAME']);

$authBackend = new BMWebdiskAuthBackend;
$authPlugin = new DAV\Auth\Plugin($authBackend, $bm_prefs['titel'] . ' ' . $lang_user['webdisk']);
$server->addPlugin($authPlugin);

$lockBackend = new BMWebdiskLockBackend;
$lockPlugin = new DAV\Locks\Plugin($lockBackend);
$server->addPlugin($lockPlugin);

$server->exec();
