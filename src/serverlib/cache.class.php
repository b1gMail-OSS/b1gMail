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
 * cache base class
 *
 */
class BMCache
{
	/**
	 * constructor
	 *
	 * @return BMCache
	 */
	function __construct()
	{
	}

	/**
	 * retrieve object from cache
	 *
	 * @param string $key
	 * @return mixed
	 */
	function Get($key)
	{
		global $bm_prefs;

		if($bm_prefs['cache_parseonly'] == 'no' || substr($key, 0, 10) == 'parsedMsg:')
			return($this->_Get($key));
		else
			return(false);
	}
	function _Get($key)
	{
		return(false);
	}

	/**
	 * add object to cache
	 *
	 * @param string $key
	 * @param mixed $obj
	 * @param int $expires
	 * @return bool
	 */
	function Add($key, $obj, $expires = 0)
	{
		global $bm_prefs;

		if($bm_prefs['cache_parseonly'] == 'no' || substr($key, 0, 10) == 'parsedMsg:')
			return($this->_Add($key, $obj, $expires));
		else
			return(false);
	}
	function _Add($key, $obj, $expires = 0)
	{
		return(false);
	}

	/**
	 * set cache object
	 *
	 * @param string $key
	 * @param mixed $obj
	 * @param int $expires
	 * @return bool
	 */
	function Set($key, $obj, $expires = 0)
	{
		global $bm_prefs;

		if($bm_prefs['cache_parseonly'] == 'no' || substr($key, 0, 10) == 'parsedMsg:')
			return($this->_Set($key, $obj, $expires));
		else
			return(false);
	}
	function _Set($key, $obj, $expires = 0)
	{
		return(false);
	}

	/**
	 * delete cache object
	 *
	 * @param string $key
	 * @return bool
	 */
	function Delete($key)
	{
		global $bm_prefs;

		if($bm_prefs['cache_parseonly'] == 'no' || substr($key, 0, 10) == 'parsedMsg:')
			return($this->_Delete($key));
		else
			return(false);
	}
	function _Delete($key)
	{
		return(false);
	}
}

/**
 * disabled cache
 *
 */
class BMCache_None extends BMCache
{

}

/**
 * file based cache implementation
 *
 */
class BMCache_b1gMail extends BMCache
{
	/**
	 * constructor
	 *
	 * @return BMCache_b1gMail
	 */
	function __construct()
	{
	}

	/**
	 * retrieve object from cache
	 *
	 * @param string $key
	 * @return mixed
	 */
	function _Get($key)
	{
		$cacheFile = $this->_cacheFilename($key);

		if(file_exists($cacheFile))
		{
			$cacheFP = @fopen($cacheFile, 'rb');

			if($cacheFP)
			{
				$cacheData = fread($cacheFP, filesize($cacheFile));
				fclose($cacheFP);

				// unserialize
				$cacheObject = @unserialize($cacheData);
				unset($cacheData);
				if($cacheObject !== false)
				{
					return($cacheObject);
				}
			}
		}

		return(false);
	}

	/**
	 * add object to cache
	 *
	 * @param string $key
	 * @param mixed $obj
	 * @param int $expires
	 * @return bool
	 */
	function _Add($key, $obj, $expires = 0)
	{
		$cacheFile = $this->_cacheFilename($key);

		if(!file_exists($cacheFile))
			return($this->Set($key, $obj, $expires));

		return(false);
	}

	/**
	 * set cache object
	 *
	 * @param string $key
	 * @param mixed $obj
	 * @param int $expires
	 * @return bool
	 */
	function _Set($key, $obj, $expires = 0)
	{
		global $db;

		$cacheFile = $this->_cacheFilename($key);

		// store object
		$cacheFP = @fopen($cacheFile, 'wb');
		if($cacheFP)
		{
			fwrite($cacheFP, serialize($obj));
			fclose($cacheFP);

			@chmod($cacheFile, 0666);

			$db->Query('REPLACE INTO {pre}file_cache(`key`,expires,size) VALUES(?,?,?)',
				md5($key),
				time()+$expires,
				filesize($cacheFile));

			return(true);
		}

		return(false);
	}

	/**
	 * delete cache object
	 *
	 * @param string $key
	 * @return bool
	 */
	function _Delete($key)
	{
		global $db;

		$cacheFile = $this->_cacheFilename($key);

		if(file_exists($cacheFile))
		{
			$db->Query('DELETE FROM {pre}file_cache WHERE `key`=?',
				md5($key));
			unlink($cacheFile);
			return(true);
		}

		return(false);
	}

	/**
	 * clean up file cache
	 *
	 * @param bool $hard Delete everything?
	 * @return bool
	 */
	function CleanUp($hard = false)
	{
		global $db, $bm_prefs;

		// get cache size
		$res = $db->Query('SELECT SUM(size) FROM {pre}file_cache');
		list($fileCacheSize) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// delete old files until cache is small enough
		if($hard || (int)$fileCacheSize > $bm_prefs['filecache_size'])
		{
			$deleteIDs = array();

			$res = $db->Query('SELECT size,`key` FROM {pre}file_cache WHERE expires>0 ORDER BY expires ASC');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				@unlink($this->_cacheFilename($row['key'], true));
				$fileCacheSize -= $row['size'];
				$deleteIDs[] = $row['key'];

				if(!$hard && ($fileCacheSize < $bm_prefs['filecache_size']))
					break;
			}
			$res->Free();

			if($hard || ($fileCacheSize > $bm_prefs['filecache_size']))
			{
				$res = $db->Query('SELECT size,`key` FROM {pre}file_cache WHERE expires=0');
				while($row = $res->FetchArray(MYSQLI_ASSOC))
				{
					@unlink($this->_cacheFilename($row['key'], true));
					$fileCacheSize -= $row['size'];
					$deleteIDs[] = $row['key'];

					if(!$hard && ($fileCacheSize < $bm_prefs['filecache_size']))
						break;
				}
				$res->Free();
			}

			if(count($deleteIDs) > 0)
				$db->Query('DELETE FROM {pre}file_cache WHERE `key` IN(\'' . implode('\',\'', $deleteIDs) . '\')');
		}

		return(true);
	}

	/**
	 * generate cache filename
	 *
	 * @param string $key Key
	 * @param bool $isMD5 Is the key already a MD5 hash?
	 * @return string
	 */
	function _cacheFilename($key, $isMD5 = false)
	{
		return(B1GMAIL_DIR . 'temp/cache/' . ($isMD5 ? $key : md5($key)) . '.cache');
	}
}

/**
 * memcache based cache
 *
 */
class BMCache_memcache extends BMCache
{
	/**
	 * memcache instance
	 *
	 * @var Memcache
	 */
	var $_memcache;

	/**
	 * memcached instance
	 *
	 * @var Memcached
	 */
	var $_memcached;

	/**
	 * constructor - initializes memcache extension
	 *
	 * @return BMCache_memcache
	 */
	function __construct()
	{
		global $bm_prefs;

		// init memcache interface
		$memcacheServers = explode(';', $bm_prefs['memcache_servers']);
		if(class_exists('Memcache') || class_exists('Memcached'))
		{
			if(count($memcacheServers) > 0 && trim($memcacheServers[0]) != '')
			{
				if(class_exists('Memcache'))
				{
					$this->_memcache = new Memcache;
					$this->_memcached = false;
				}
				else
				{
					$this->_memcache = false;
					$this->_memcached = new Memcached;
				}

				foreach($memcacheServers as $server)
				{
					if(trim($server) != '')
					{
						$serverWeight = 1;
						$serverPort = 11211;

						// weight?
						$serverInfo = explode(',', $server);
						if(count($serverInfo) == 2)
							$serverWeight = $serverInfo[1];
						$server = $serverInfo[0];

						// port?
						$serverInfo = explode(':', $server);
						if(count($serverInfo) == 2)
							$serverPort = $serverInfo[1];
						$serverHost = $serverInfo[0];

						// add
						if($this->_memcache)
							$this->_memcache->addServer($serverHost, $serverPort, $bm_prefs['memcache_persistent'] == 'yes', $serverWeight);
						else
							$this->_memcached->addServer($serverHost, $serverPort, $serverWeight);
					}
				}
			}
			else
			{
				PutLog('Cache configured for use with memcache, but no servers specified - caching will not work',
					PRIO_WARNING,
					__FILE__,
					__LINE__);
			}
		}
		else
		{
			PutLog('Cache configured for use with memcache, but Memcache(d)-class not defined (missing/outdated memcache(d) extension?) - caching will not work',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
		}
	}

	/**
	 * retrieve object from cache
	 *
	 * @param string $key
	 * @return mixed
	 */
	function _Get($key)
	{
		if($this->_memcache)
			return($this->_memcache->get($key));
		else if($this->_memcached)
			return($this->_memcached->get($key));
		else
			return false;
	}

	/**
	 * add object to cache
	 *
	 * @param string $key
	 * @param mixed $obj
	 * @param int $expires
	 * @return bool
	 */
	function _Add($key, $obj, $expires = 0)
	{
		if($this->_memcache)
			return($this->_memcache->add($key, $obj, 0, $expires));
		else if($this->_memcached)
			return($this->_memcached->add($key, $obj, $expires));
		else
			return false;
	}

	/**
	 * set cache object
	 *
	 * @param string $key
	 * @param mixed $obj
	 * @param int $expires
	 * @return bool
	 */
	function _Set($key, $obj, $expires = 0)
	{
		if($this->_memcache)
			return($this->_memcache->set($key, $obj, 0, $expires));
		else if($this->_memcached)
			return($this->_memcached->set($key, $obj, $expires));
		else
			return false;
	}

	/**
	 * delete cache object
	 *
	 * @param string $key
	 * @return bool
	 */
	function _Delete($key)
	{
		if($this->_memcache)
			return($this->_memcache->delete($key));
		else if($this->_memcached)
			return($this->_memcached->delete($key));
		else
			return false;
	}

	/**
	 * clean up / flush cache
	 *
	 * @param bool $hard Delete everything?
	 * @return bool
	 */
	function CleanUp($hard = false)
	{
		if(!$hard)
			return false;
		if($this->_memcache)
			return($this->_memcache->flush());
		else if($this->_memcached)
			return($this->_memcached->flush());
		else
			return false;
	}
}
