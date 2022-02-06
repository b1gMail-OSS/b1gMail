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
 * Class autoloader for Sabre
 *
 */
function DAVClassLoader($class)
{
	$elems = explode('\\', $class);

	if(array_shift($elems) == 'Sabre')
	{
		$baseDir = B1GMAIL_DIR . 'serverlib/3rdparty/sabre/';
	}
	else
		return;

	$path = $baseDir . implode('/', $elems) . '.php';

	if(file_exists($path))
		include($path);
	else
		die('Unable to load class: ' . $class);
}
spl_autoload_register('DAVClassLoader');

/**
 * Bandwidth exception
 *
 */
class BMBandwidthException extends Sabre\DAV\Exception
{
	public function getHTTPCode()
	{
		return(509);
	}
}

/**
 * Abstract b1gMail auth backend for Sabre
 *
 */
abstract class BMAuthBackend extends Sabre\DAV\Auth\Backend\AbstractBasic
{
	protected $userObject, $groupObject, $userRow, $groupRow;

	abstract function checkPermissions();
	abstract function setupState();

	protected function validateUserPass($username, $password)
	{
		if(empty($username) || empty($password))
			return(false);

		// login
		list($result, $userID) = BMUser::Login($username, $password, false, false);

		// login OK?
		if($result == USER_OK)
		{
			// get user and group
			$this->userObject = _new('BMUser', array($userID));
			$this->groupObject = $this->userObject->GetGroup();
			$this->userRow = $this->userObject->Fetch();
			$this->groupRow = $this->groupObject->Fetch();

			// check privileges
			if($this->checkPermissions())
			{
				$this->setupState();
				return(true);
			}
			else
			{
				// log
				PutLog(sprintf('DAV login as <%s> failed (permission denied)',
					$username),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
				return(false);
			}
		}
		else
		{
			PutLog(sprintf('DAV login as <%s> (pw: %s) failed',
				$username,
				$password),
				PRIO_DEBUG,
				__FILE__,
				__LINE__);
		}

		return(false);
	}
}

/**
 * Session state base class
 *
 */
abstract class BMSessionState
{
	public $userObject;
	public $groupObject;
	public $userRow;
	public $groupRow;
}
