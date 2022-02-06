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

define('BMBLOB_TYPE_MAIL',					0);
define('BMBLOB_TYPE_WEBDISK',				1);

define('BMBLOBSTORAGE_SEPARATEFILES',		0);
define('BMBLOBSTORAGE_USERDB',				1);

/**
 * blob storage provider interface
 */
interface BMBlobStorageInterface
{
	/**
	 * store a blob
	 *
	 * @param int $type
	 * @param int $id
	 * @param mixed $data Either string or resource (stream)
	 * @param int $limit Max no. of bytes to copy from stream
	 * @return bool
	 */
	public function storeBlob($type, $id, $data, $limit = -1);

	/**
	 * load a blob
	 *
	 * @param int $type
	 * @param int $id
	 * @return resource stream
	 */
	public function loadBlob($type, $id);

	/**
	 * delete a blob
	 *
	 * @param int $type
	 * @param int $id
	 * @return bool
	 */
	public function deleteBlob($type, $id);

	/**
	 * get size of a blob in bytes
	 *
	 * @param int $type
	 * @param int $id
	 * @return int
	 */
	public function getBlobSize($type, $id);

	/**
	 * delete all the user's blobs
	 *
	 * @return void
	 */
	public function deleteUser();

	/**
	 * check if blob provider is available, i.e. system requirements are met
	 *
	 * @return bool
	 */
	public function isAvailable();

	/**
	 * set provider ID (called by factory)
	 *
	 * @param int $id
	 */
	public function setProviderID($id);

	/**
	 * open provider for a certain user (called by factory)
	 *
	 * @param int $userID
	 */
	public function open($userID);

	/**
	 * hint that a transaction of many loads/stores/deletes might follow
	 *
	 * @return void
	 */
	public function beginTx();

	/**
	 * hint that a transaction of many loads/stores/deletes is finished
	 *
	 * @return void
	 */
	public function endTx();
}

abstract class BMAbstractBlobStorage implements BMBlobStorageInterface
{
	/**
	 * user ID
	 */
	protected $userID;

	/**
	 * provider ID as set by setProviderID
	 */
	public $providerID;

	/**
	 * set provider ID
	 *
	 * @param int $id
	 */
	public function setProviderID($id)
	{
		$this->providerID = $id;
	}

	/**
	 * open provider for a certain user (called by factory)
	 *
	 * @param int $userID
	 */
	public function open($userID)
	{
		$this->userID = $userID;
	}

	/**
	 * hint that a transaction of many loads/stores/deletes might follow
	 *
	 * @return void
	 */
	public function beginTx()
	{
	}

	/**
	 * hint that a transaction of many loads/stores/deletes is finished
	 *
	 * @return void
	 */
	public function endTx()
	{
	}
}

/**
 * blob storage provider factory
 */
class BMBlobStorage
{
	/**
	 * list of available providers
	 *	id => array(file, class name)
	 */
	public static $providers = array(
		BMBLOBSTORAGE_SEPARATEFILES		=> array('separatefiles.php', 	'BMBlobStorage_SeparateFiles'),
		BMBLOBSTORAGE_USERDB			=> array('userdb.php', 			'BMBlobStorage_UserDB')
	);

	/**
	 * returns ID of default provider
	 *
	 * @return int
	 */
	public static function getDefaultProvider()
	{
		global $bm_prefs;
		return($bm_prefs['blobstorage_provider']);
	}

	/**
	 * returns ID of default webdisk provider
	 *
	 * @return int
	 */
	public static function getDefaultWebdiskProvider()
	{
		global $bm_prefs;
		return($bm_prefs['blobstorage_provider_webdisk']);
	}

	/**
	 * returns an instance of the default provider for a specific user
	 *
	 * @param int $userID
	 * @return BMBlobStorageInterface
	 */
	public static function createDefaultProvider($userID)
	{
		return(BMBlobStorage::createProvider(BMBlobStorage::getDefaultProvider(), $userID));
	}

	/**
	 * returns an instance of the default webdisk provider for a specific user
	 *
	 * @param int $userID
	 * @return BMBlobStorageInterface
	 */
	public static function createDefaultWebdiskProvider($userID)
	{
		return(BMBlobStorage::createProvider(BMBlobStorage::getDefaultWebdiskProvider(), $userID));
	}

	/**
	 * create an instance of a specific provider
	 *
	 * @param int $id provider ID
	 * @param int $userID
	 * @return BMBlobStorageInterface
	 */
	public static function createProvider($id, $userID = 0)
	{
		if(!isset(BMBlobStorage::$providers[$id]))
			return(false);

		include_once(BMBlobStorage::getBlobStorageDir() . BMBlobStorage::$providers[$id][0]);
		$p = new BMBlobStorage::$providers[$id][1];
		$p->setProviderID($id);
		if($userID > 0)
			$p->open($userID);
		return($p);
	}

	/**
	 * get blob storage provider directory
	 *
	 * @return string
	 */
	private static function getBlobStorageDir()
	{
		return(B1GMAIL_DIR . 'serverlib/blobstorage/');
	}
}
