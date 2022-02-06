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

define('BMBS_USERDB_FLAG_GZCOMPRESSED',			1);

class BMBlobStorage_UserDB extends BMAbstractBlobStorage
{
	/**
	 * SQLite db object
	 */
	private $sdb = false;

	/**
	 * Active transactions counter
	 */
	private $txCounter = 0;

	/**
	 * Compression level for compressed blobs
	 */
	private $compressLevel = 8;

	public function open($userID)
	{
		parent::open($userID);

		$dbFileName = $this->getDBFileName();
		if(!file_exists($dbFileName))
			@touch($dbFileName);
		@chmod($dbFileName, 0666);

		$this->sdb = new SQLite3($dbFileName);
		$this->initDB();
	}

	public function __destruct()
	{
		if($this->sdb !== false)
			$this->sdb->close();
	}

	private function getDBFileName()
	{
		return(DataFilename($this->userID, 'blobdb'));
	}

	private function initDB()
	{
		$this->sdb->busyTimeout(15000);

		$this->sdb->query('CREATE TABLE IF NOT EXISTS [blobs] ('
			. '	[type] INTEGER,'
			. '	[id] INTEGER,'
			. '	[flags] INTEGER,'
			. ' [size] INTEGER,'
			. '	[data] BLOB,'
			. '	PRIMARY KEY([type],[id])'
			. ')');
	}

	public function storeBlob($type, $id, $data, $limit = -1)
	{
		global $bm_prefs;

		if(is_resource($data))
		{
			$fp = $data;
			$data = '';
			while(!feof($fp))
				$data .= fread($fp, 4096);
		}

		if($limit > -1 && strlen($data) > $limit)
			$data = substr($data, 0, $limit);

		$dataSize = strlen($data);

		$flags = 0;
		if((($type == BMBLOB_TYPE_WEBDISK && $bm_prefs['blobstorage_webdisk_compress'] == 'yes')
			|| ($type == BMBLOB_TYPE_MAIL && $bm_prefs['blobstorage_compress'] == 'yes'))
			&& function_exists('gzcompress'))
		{
			$data = gzcompress($data, $this->compressLevel);
			$flags |= BMBS_USERDB_FLAG_GZCOMPRESSED;
		}

		$stmt = $this->sdb->prepare('REPLACE INTO [blobs]([type],[id],[data],[flags],[size]) VALUES(:type,:id,:data,:flags,:size)');
		$stmt->bindValue(':type', 	$type, 		SQLITE3_INTEGER);
		$stmt->bindValue(':id', 	$id, 		SQLITE3_INTEGER);
		$stmt->bindValue(':data', 	$data, 		SQLITE3_BLOB);
		$stmt->bindValue(':flags', 	$flags, 	SQLITE3_INTEGER);
		$stmt->bindValue(':size',	$dataSize,	SQLITE3_INTEGER);
		$stmt->execute();

		$stmt = $this->sdb->prepare('SELECT COUNT(*) FROM [blobs] WHERE [type]=:type AND [id]=:id');
		$stmt->bindValue(':type',	$type, 	SQLITE3_INTEGER);
		$stmt->bindValue(':id',		$id,	SQLITE3_INTEGER);
		$res = $stmt->execute();

		$result = false;
		while($row = $res->fetchArray(SQLITE3_NUM))
		{
			if($row[0] > 0)
				$result = true;
		}
		$res->finalize();

		return($result);
	}

	public function loadBlob($type, $id)
	{
		$stmt = $this->sdb->prepare('SELECT [data],[flags] FROM [blobs] WHERE [type]=:type AND [id]=:id');
		$stmt->bindValue(':type',	$type, 	SQLITE3_INTEGER);
		$stmt->bindValue(':id',		$id,	SQLITE3_INTEGER);
		$res = $stmt->execute();

		$result = false;
		if($row = $res->fetchArray(SQLITE3_ASSOC))
		{
			if(($row['flags'] & BMBS_USERDB_FLAG_GZCOMPRESSED) != 0)
				$row['data'] = gzuncompress($row['data']);

			$result = fopen('php://temp', 'wb+');
			fwrite($result, $row['data']);
			fseek($result, 0, SEEK_SET);
		}
		$res->finalize();

		return($result);
	}

	public function deleteBlob($type, $id)
	{
		$stmt = $this->sdb->prepare('DELETE FROM [blobs] WHERE [type]=:type AND [id]=:id');
		$stmt->bindValue(':type', 	$type, 	SQLITE3_INTEGER);
		$stmt->bindValue(':id', 	$id, 	SQLITE3_INTEGER);
		$stmt->execute();

		return(true);
	}

	public function getBlobSize($type, $id)
	{
		$stmt = $this->sdb->prepare('SELECT [size] FROM [blobs] WHERE [type]=:type AND [id]=:id');
		$stmt->bindValue(':type', 	$type, 	SQLITE3_INTEGER);
		$stmt->bindValue(':id', 	$id, 	SQLITE3_INTEGER);
		$res = $stmt->execute();

		$result = 0;
		if($row = $res->fetchArray(SQLITE3_NUM))
		{
			$result = $row[0];
		}
		$res->finalize();

		return($result);
	}

	public function deleteUser()
	{
		$this->sdb->close();
		$this->sdb = false;

		@unlink($this->getDBFileName());
	}

	public function beginTx()
	{
		if($this->txCounter == 0)
		{
			$this->sdb->query('BEGIN TRANSACTION');
		}
		++$this->txCounter;
	}

	public function endTx()
	{
		if(--$this->txCounter == 0)
		{
			$this->sdb->Query('COMMIT');
		}
		if($this->txCounter < 0)
			$this->txCounter = 0;
	}

	public function isAvailable()
	{
		return(class_exists('SQLite3'));
	}
}
