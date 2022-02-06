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

class BMBlobStorage_SeparateFiles extends BMAbstractBlobStorage
{
	private $extMap = array(
		BMBLOB_TYPE_MAIL	=> 'msg',
		BMBLOB_TYPE_WEBDISK	=> 'dsk'
	);

	public function storeBlob($type, $id, $data, $limit = -1)
	{
		$ext = $this->extMap[$type];
		$fileName = DataFilename($id, $ext);

		$fp = fopen($fileName, 'wb');
		if(!is_resource($fp))
		{
			PutLog(sprintf('Failed to open blob file <%s> for writing (type: %d, id: %d)',
					$fileName, $type, $id),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}
		if(is_resource($data))
		{
			$byteCount = 0;
			while(!feof($data))
			{
				$chunk = fread($data, 4096);
				if($limit != -1 && $byteCount+strlen($chunk) > $limit)
					break;
				fwrite($fp, $chunk);
				$byteCount += strlen($chunk);
			}
		}
		else
		{
			if($limit > -1 && strlen($data) > $limit)
				$data = substr($data, 0, $limit);
			fwrite($fp, $data);
		}
		fclose($fp);

		@chmod($fileName, 0666);

		return(true);
	}

	public function loadBlob($type, $id)
	{
		$ext = $this->extMap[$type];
		$fileName = DataFilename($id, $ext, true);

		if(!file_exists($fileName))
		{
			PutLog(sprintf('Blob file <%s> does not exist (type: %d, id: %d)',
					$fileName, $type, $id),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

		$fp = fopen($fileName, 'rb');
		if(!is_resource($fp))
		{
			PutLog(sprintf('Failed to open blob file <%s> for reading (type: %d, id: %d)',
					$fileName, $type, $id),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
		}

		return($fp);
	}

	public function deleteBlob($type, $id)
	{
		$ext = $this->extMap[$type];
		$fileName = DataFilename($id, $ext, true);

		if(file_exists($fileName))
			return(@unlink($fileName));

		return(true);
	}

	public function getBlobSize($type, $id)
	{
		$ext = $this->extMap[$type];
		$fileName = DataFilename($id, $ext, true);

		$result = @filesize($fileName);
		if($result === false)
		{
			PutLog(sprintf('Failed to get file size of blob file <%s> (type: %d, id: %d)',
					$fileName, $type, $id),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
		}

		return($result);
	}

	public function deleteUser()
	{
		global $db;

		$res = $db->Query('SELECT `id` FROM {pre}mails WHERE `userid`=? AND `blobstorage`=?',
			$this->userID,
			BMBLOBSTORAGE_SEPARATEFILES);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$this->deleteBlob(BMBLOB_TYPE_MAIL, $row['id']);
		}
		$res->Free();

		$res = $db->Query('SELECT `id` FROM {pre}diskfiles WHERE `user`=? AND `blobstorage`=?',
			$this->userID,
			BMBLOBSTORAGE_SEPARATEFILES);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$this->deleteBlob(BMBLOB_TYPE_WEBDISK, $row['id']);
		}
		$res->Free();
	}

	public function isAvailable()
	{
		return(true);
	}
}
