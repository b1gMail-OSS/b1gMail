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
chdir(B1GMAIL_DIR . 'serverlib/3rdparty/SabreDAV/');
require_once 'vendor/autoload.php';
chdir(B1GMAIL_DIR . 'interface/');

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
 * Determine Sabre's baseUri for a DAV endpoint.
 *
 * When the client accesses the DAV endpoint via its natural URL
 * (e.g. /interface/webdav.php/...), SCRIPT_NAME is authoritative.
 *
 * When the client accesses it via the .htaccess short-URL rewrite
 * (e.g. /webdav/...) — which we use because some DAV clients (GNOME
 * GVFS/Nautilus, older Windows Explorer, ...) do not follow 301
 * redirects on the DAV base URL — SCRIPT_NAME still points to the
 * physical script (/interface/webdav.php), but the client expects
 * response Hrefs to stay under the short URL it requested. In that
 * case we derive the baseUri from REQUEST_URI so Sabre generates
 * URLs matching what the client sees.
 *
 * @param string $shortName 'webdav' | 'caldav' | 'carddav'
 * @return string
 */
function bmDavDetermineBaseUri($shortName)
{
	$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	$q = strpos($requestUri, '?');
	if($q !== false)
		$requestUri = substr($requestUri, 0, $q);

	$scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';

	// Natural URL: REQUEST_URI starts with SCRIPT_NAME → use SCRIPT_NAME.
	if($scriptName !== '' && strpos($requestUri, $scriptName) === 0)
		return $scriptName;

	// Short URL rewrite path: look for /.../<shortName>(/|$) in REQUEST_URI
	// and treat everything up to and including <shortName> as the baseUri.
	if(preg_match('#^(.*/)' . preg_quote($shortName, '#') . '(?:/|$)#', $requestUri, $m))
		return $m[1] . $shortName;

	// Fallback: SCRIPT_NAME
	return $scriptName;
}

/**
 * Abstract b1gMail auth backend for Sabre.
 *
 * Supports two credential types:
 *   1) App-specific passwords (recommended, MFA-compatible)
 *   2) Account password (only when MFA is not required OR the admin runs
 *      `app_password_dav_mode` in 'off'/'warn' mode)
 *
 * Order:
 *   1) Validate optional HTTPS requirement.
 *   2) Try app password first (fast path, MFA-safe).
 *   3) If MFA is active AND enforcement mode is 'enforce', reject.
 *   4) Otherwise, fall back to the traditional account-password login.
 */
abstract class BMAuthBackend extends Sabre\DAV\Auth\Backend\AbstractBasic
{
	protected $userObject, $groupObject, $userRow, $groupRow;

	/**
	 * Set to the matched app-password row when auth succeeded via app password,
	 * false otherwise. Available to callers after successful login for audit
	 * / feature gating (e.g. hiding UI-only features from app-password sessions).
	 *
	 * @var array|false
	 */
	protected $appPasswordRow = false;

	abstract function checkPermissions();
	abstract function setupState();

	/**
	 * Scope identifier this DAV endpoint uses when verifying app passwords.
	 * Must return one of BMAppPassword::SCOPE_CALDAV / SCOPE_CARDDAV / SCOPE_WEBDAV.
	 *
	 * @return string
	 */
	abstract protected function davScope();

	/**
	 * @return array|false
	 */
	public function getAppPasswordRow()
	{
		return $this->appPasswordRow;
	}

	protected function validateUserPass($username, $password)
	{
		global $bm_prefs;

		if(empty($username) || empty($password))
			return(false);

		$scope = $this->davScope();

		// Optional: reject non-HTTPS DAV logins if the admin requires TLS.
		if(isset($bm_prefs['dav_require_https']) && $bm_prefs['dav_require_https'] === 'yes'
			&& !self::_requestIsHttps())
		{
			PutLog(sprintf('DAV login <%s> rejected: HTTPS required (scope=%s)',
				$username, $scope), PRIO_WARNING, __FILE__, __LINE__);
			return(false);
		}

		// Resolve the user WITHOUT running the full account login yet, so we
		// can try the app-password path first.
		$isAlias = false;
		$userID = BMUser::GetID($username, true, $isAlias);
		if($userID <= 0)
		{
			PutLog(sprintf('DAV login <%s> unknown user (scope=%s)',
				$username, $scope), PRIO_DEBUG, __FILE__, __LINE__);
			return(false);
		}

		//
		// 1) App-password fast path (MFA-safe)
		//
		if(BMAppPassword::IsScopeEnabled($scope))
		{
			$appRow = BMAppPassword::Verify($userID, $password, $scope);
			if($appRow !== false)
			{
				if(!$this->_bootstrapUser($userID))
					return(false);
				if(!$this->checkPermissions())
				{
					PutLog(sprintf('DAV login <%s> app-password authenticated but permission denied (scope=%s)',
						$username, $scope), PRIO_NOTE, __FILE__, __LINE__);
					return(false);
				}
				$this->appPasswordRow = $appRow;
				BMAppPassword::TouchLastUsed(
					(int)$appRow['id'],
					isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
					$scope
				);
				PutLog(sprintf('DAV login <%s> via app password id=%d (scope=%s)',
					$username, (int)$appRow['id'], $scope), PRIO_DEBUG, __FILE__, __LINE__);
				$this->setupState();
				return(true);
			}
		}

		//
		// 2) Enforcement — decide whether we may fall back to the account
		//    password at all.
		//
		//    MODE_STRICT  — account-password DAV login is always blocked.
		//    MODE_ENFORCE — blocked only when MFA is active for this user
		//                   (classic F1 MFA-bypass mitigation).
		//    MODE_WARN    — allowed, but a warning is logged when MFA is
		//                   active (audit trail for deprecated usage).
		//    MODE_OFF     — allowed silently.
		//
		$mode = BMAppPassword::EnforcementMode($scope);
		$mfaReady = class_exists('BMMfa')
			&& BMMfa::IsLoginReady('user', $userID);

		// Structured decision log (DEBUG): why account-password fallback
		// was allowed or skipped. Not emitted at NOTE — DAV clients
		// re-authenticate on almost every request.
		PutLog(sprintf(
			'DAV auth decision user=%s uid=%d scope=%s mode=%s mfa_ready=%s prefs_mode=%s prefs_enable=%s',
			$username, (int)$userID, $scope, $mode,
			$mfaReady ? 'yes' : 'no',
			isset($bm_prefs['app_password_dav_mode']) ? (string)$bm_prefs['app_password_dav_mode'] : '(unset)',
			isset($bm_prefs['app_password_enable'])    ? (string)$bm_prefs['app_password_enable']    : '(unset)'
		), PRIO_DEBUG, __FILE__, __LINE__);

		if($mode === BMAppPassword::MODE_STRICT)
		{
			PutLog(sprintf('DAV login <%s> rejected: strict mode, app password required (scope=%s)',
				$username, $scope), PRIO_WARNING, __FILE__, __LINE__);
			return(false);
		}

		if($mfaReady)
		{
			if($mode === BMAppPassword::MODE_ENFORCE)
			{
				PutLog(sprintf('DAV login <%s> rejected: MFA is active, app password required (scope=%s)',
					$username, $scope), PRIO_WARNING, __FILE__, __LINE__);
				return(false);
			}
			else if($mode === BMAppPassword::MODE_WARN)
			{
				PutLog(sprintf('DAV login <%s> via account password despite active MFA (deprecated; scope=%s)',
					$username, $scope), PRIO_WARNING, __FILE__, __LINE__);
			}
		}

		//
		// 3) Legacy account-password login. Keeps 100% compatibility for
		//    users without MFA and preserves the existing lockout / plugin
		//    auth pipeline.
		//
		list($result, $loginUserID) = BMUser::Login($username, $password, false, false);

		if($result != USER_OK)
		{
			PutLog(sprintf('DAV login <%s> failed (scope=%s)',
				$username, $scope), PRIO_DEBUG, __FILE__, __LINE__);
			return(false);
		}

		if(!$this->_bootstrapUser($loginUserID))
			return(false);

		if(!$this->checkPermissions())
		{
			PutLog(sprintf('DAV login as <%s> failed (permission denied, scope=%s)',
				$username, $scope),
				PRIO_NOTE, __FILE__, __LINE__);
			return(false);
		}

		$this->setupState();
		return(true);
	}

	/**
	 * @param int $userID
	 * @return bool
	 */
	private function _bootstrapUser($userID)
	{
		$userID = (int)$userID;
		if($userID <= 0)
			return false;

		$this->userObject  = _new('BMUser', array($userID));
		$this->userRow     = $this->userObject->Fetch();

		if(!is_array($this->userRow) || (int)$this->userRow['id'] <= 0
			|| (isset($this->userRow['gesperrt']) && $this->userRow['gesperrt'] !== 'no'))
			return false;

		$this->groupObject = $this->userObject->GetGroup();
		$this->groupRow    = $this->groupObject->Fetch();

		return is_array($this->groupRow);
	}

	/**
	 * Detect whether the current request runs over TLS. Honors common
	 * reverse-proxy headers when the frontend forwards traffic.
	 *
	 * @return bool
	 */
	private static function _requestIsHttps()
	{
		if(!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
			return true;
		if(isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
			return true;
		if(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
			&& strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
			return true;
		if(isset($_SERVER['HTTP_X_FORWARDED_SSL'])
			&& strtolower((string)$_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
			return true;
		return false;
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
