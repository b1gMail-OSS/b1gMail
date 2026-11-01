<?php
/*
 * b1gMail
 * Copyright (c) 2002-2025 Patrick Schlangen et al, All Rights Reserved
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
 * Ensure default prefs exist even when the DB update has not been run yet.
 * Values may be present as empty string after a fresh column ADD.
 */
function AppPasswordApplyPrefDefaults()
{
	global $bm_prefs, $db;

	// Widen mail-mode enum for existing installs (strict was added later, like DAV).
	static $mailModeEnumChecked = false;
	if(!$mailModeEnumChecked && isset($db) && is_object($db))
	{
		$mailModeEnumChecked = true;
		try
		{
			$res = $db->Query("SHOW COLUMNS FROM {pre}prefs LIKE 'app_password_mail_mode'");
			if($res->RowCount() > 0)
			{
				$col = $res->FetchArray(MYSQLI_ASSOC);
				$res->Free();
				$type = isset($col['Type']) ? strtolower($col['Type']) : '';
				if($type !== '' && strpos($type, "'strict'") === false)
				{
					$db->Query("ALTER TABLE {pre}prefs MODIFY COLUMN `app_password_mail_mode` "
						. "enum('off','warn','enforce','strict') NOT NULL DEFAULT 'off'");
				}
			}
			else
			{
				$res->Free();
			}
		}
		catch(Exception $ex)
		{
			// ignore — older installs without the column yet
		}
	}

	$defaults = array(
		'app_password_enable'       => 'yes',
		'app_password_dav_mode'     => 'warn',
		'app_password_mail_enable'  => 'no',
		'app_password_mail_mode'    => 'off',
		'app_password_expiry_days'  => 0,
		'app_password_max_per_user' => 20,
		'dav_require_https'         => 'no',
	);

	foreach($defaults as $key => $val)
	{
		if(!isset($bm_prefs[$key]) || $bm_prefs[$key] === '' || $bm_prefs[$key] === null)
			$bm_prefs[$key] = $val;
	}
}

/**
 * App-specific passwords ("app passwords").
 *
 * Consumers:
 *   - PHP-Frontend: WebDAV/CalDAV/CardDAV backends verify via Verify() with
 *     the corresponding scope (`caldav`, `carddav`, `webdav`).
 *   - Mail daemon (b1gMailServer): shares the same table and the
 *     same verification rules for SMTP/IMAP/POP3 scopes. Enable via
 *     $bm_prefs['app_password_mail_enable'] once a BMS build with
 *     app-password support is deployed.
 *
 * Canonical verification contract (for BMS parity):
 *   1) Resolve user by e-mail.
 *   2) SELECT id,password_hash,scope,revoked_at,expires
 *        FROM {pre}app_passwords
 *        WHERE user=? AND revoked_at=0
 *          AND (expires=0 OR expires>UNIX_TIMESTAMP())
 *          AND FIND_IN_SET(<scope>, scope) > 0
 *   3) Iterate ALL rows and call password_verify() constant-time
 *      (BMS: bcrypt / argon2id via libcrypt / libargon2).
 *   4) On success: UPDATE last_used/last_ip/last_scope, ACCEPT.
 *   5) On failure: DO NOT fall back to the account password automatically -
 *      the caller decides that (see BMAuthBackend for the DAV rules).
 */
class BMAppPassword
{
	const SCOPE_CALDAV  = 'caldav';
	const SCOPE_CARDDAV = 'carddav';
	const SCOPE_WEBDAV  = 'webdav';
	const SCOPE_IMAP    = 'imap';
	const SCOPE_POP3    = 'pop3';
	const SCOPE_SMTP    = 'smtp';

	/**
	 * Enforcement modes for the app-password subsystem.
	 *
 *   MODE_OFF     — the app-password subsystem is disabled for this scope
 *                  family. Only the traditional account password works.
 *
 *   MODE_WARN    — app passwords work in parallel with the account password.
 *                  Account-password logins are still accepted, but
 *                  when the user has active MFA, we log a deprecation
 *                  warning (the classic F1 issue in the audit).
 *                  Default value on UPDATES (fallback for existing users).
 *
 *   MODE_ENFORCE — account-password login is blocked when the user has
 *                  active MFA (closes the MFA bypass); users without MFA
 *                  can still use the account password.
 *
 *   MODE_STRICT  — account-password login is blocked ALWAYS,
 *                  regardless of MFA. Only app passwords work.
 *                  Default value on NEW INSTALLATIONS for DAV (harden by default).
	 */
	const MODE_OFF     = 'off';
	const MODE_WARN    = 'warn';
	const MODE_ENFORCE = 'enforce';
	const MODE_STRICT  = 'strict';

	const PLAIN_LENGTH_CHARS = 20;   // 20 chars from a 32-char alphabet ~= 100 bits entropy

	/**
	 * All scopes known to the schema, regardless of whether they are currently
	 * gated on.
	 *
	 * @return string[]
	 */
	public static function AllScopes()
	{
		return array(
			self::SCOPE_CALDAV,
			self::SCOPE_CARDDAV,
			self::SCOPE_WEBDAV,
			self::SCOPE_IMAP,
			self::SCOPE_POP3,
			self::SCOPE_SMTP,
		);
	}

	/**
	 * DAV-family scopes.
	 *
	 * @return string[]
	 */
	public static function DavScopes()
	{
		return array(self::SCOPE_CALDAV, self::SCOPE_CARDDAV, self::SCOPE_WEBDAV);
	}

	/**
	 * Mail-family scopes (POP3/IMAP/SMTP) - gated by
	 * $bm_prefs['app_password_mail_enable'] until BMS supports them.
	 *
	 * @return string[]
	 */
	public static function MailScopes()
	{
		return array(self::SCOPE_IMAP, self::SCOPE_POP3, self::SCOPE_SMTP);
	}

	/**
	 * Is the whole feature enabled globally?
	 *
	 * @return bool
	 */
	public static function IsGloballyEnabled()
	{
		global $bm_prefs;

		return isset($bm_prefs['app_password_enable'])
			&& $bm_prefs['app_password_enable'] === 'yes';
	}

	/**
	 * Are mail scopes currently allowed? Requires BOTH the global flag AND
	 * the explicit mail toggle.
	 *
	 * @return bool
	 */
	public static function AreMailScopesEnabled()
	{
		global $bm_prefs;

		return self::IsGloballyEnabled()
			&& isset($bm_prefs['app_password_mail_enable'])
			&& $bm_prefs['app_password_mail_enable'] === 'yes';
	}

	/**
	 * Is a specific scope allowed to be created / verified at the moment?
	 *
	 * @param string $scope
	 * @return bool
	 */
	public static function IsScopeEnabled($scope)
	{
		if(!self::IsGloballyEnabled())
			return false;

		if(in_array($scope, self::DavScopes(), true))
			return true;

		if(in_array($scope, self::MailScopes(), true))
			return self::AreMailScopesEnabled();

		return false;
	}

	/**
	 * Scopes the user may actually pick when creating a new app password,
	 * respecting global and group-level gates.
	 *
	 * @param int $groupID
	 * @return string[]
	 */
	public static function SelectableScopesForGroup($groupID)
	{
		$scopes = array();

		if(self::IsScopeEnabled(self::SCOPE_CALDAV))
			$scopes[] = self::SCOPE_CALDAV;
		if(self::IsScopeEnabled(self::SCOPE_CARDDAV))
			$scopes[] = self::SCOPE_CARDDAV;
		if(self::IsScopeEnabled(self::SCOPE_WEBDAV))
			$scopes[] = self::SCOPE_WEBDAV;
		if(self::IsScopeEnabled(self::SCOPE_IMAP))
			$scopes[] = self::SCOPE_IMAP;
		if(self::IsScopeEnabled(self::SCOPE_POP3))
			$scopes[] = self::SCOPE_POP3;
		if(self::IsScopeEnabled(self::SCOPE_SMTP))
			$scopes[] = self::SCOPE_SMTP;

		return $scopes;
	}

	/**
	 * Is the user allowed to manage app passwords for their group?
	 *
	 * @param int $groupID
	 * @return bool
	 */
	public static function LiUserMayManage($groupID)
	{
		if(!self::IsGloballyEnabled())
			return false;

		return count(self::SelectableScopesForGroup((int)$groupID)) > 0;
	}

	/**
	 * MFA enforcement mode for a scope family. Returns one of the MODE_*
	 * constants.
	 *
	 * @param string $scope
	 * @return string
	 */
	public static function EnforcementMode($scope)
	{
		global $bm_prefs;

		if(in_array($scope, self::DavScopes(), true))
		{
			$mode = isset($bm_prefs['app_password_dav_mode'])
				? $bm_prefs['app_password_dav_mode'] : self::MODE_WARN;
		}
		else if(in_array($scope, self::MailScopes(), true))
		{
			$mode = isset($bm_prefs['app_password_mail_mode'])
				? $bm_prefs['app_password_mail_mode'] : self::MODE_OFF;
		}
		else
		{
			$mode = self::MODE_OFF;
		}

		return in_array($mode, array(self::MODE_OFF, self::MODE_WARN, self::MODE_ENFORCE, self::MODE_STRICT), true)
			? $mode : self::MODE_OFF;
	}

	/**
	 * All valid enforcement mode values.
	 *
	 * @return string[]
	 */
	public static function AllModes()
	{
		return array(self::MODE_OFF, self::MODE_WARN, self::MODE_ENFORCE, self::MODE_STRICT);
	}

	/**
	 * Create a new app password.
	 *
	 * @param int      $userID
	 * @param string   $label
	 * @param string[] $scopes
	 * @param int      $expiresInDays 0 = never
	 * @param string   $clientHint    free-form ("ios", "thunderbird", ...)
	 * @return array|false Array(id, plain, expires) on success (plain returned exactly once), false on error/policy
	 */
	public static function Create($userID, $label, array $scopes, $expiresInDays = 0, $clientHint = '')
	{
		global $db, $bm_prefs;

		$userID = (int)$userID;
		if($userID <= 0)
			return false;

		if(!self::IsGloballyEnabled())
			return false;

		// filter/dedupe scopes; drop anything currently gated off
		$allowed = array();
		foreach($scopes as $s)
		{
			$s = strtolower(trim((string)$s));
			if($s !== '' && self::IsScopeEnabled($s) && !in_array($s, $allowed, true))
				$allowed[] = $s;
		}
		if(empty($allowed))
			return false;

		// per-user limit
		$max = isset($bm_prefs['app_password_max_per_user'])
			? max(1, (int)$bm_prefs['app_password_max_per_user']) : 20;
		$active = 0;
		$res = $db->Query('SELECT COUNT(*) FROM {pre}app_passwords WHERE user=? AND revoked_at=0', $userID);
		list($active) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		if((int)$active >= $max)
			return false;

		// enforce admin expiry policy
		$adminMax = isset($bm_prefs['app_password_expiry_days'])
			? max(0, (int)$bm_prefs['app_password_expiry_days']) : 0;
		$expiresInDays = max(0, (int)$expiresInDays);
		if($adminMax > 0 && ($expiresInDays === 0 || $expiresInDays > $adminMax))
			$expiresInDays = $adminMax;

		$expires = $expiresInDays > 0 ? time() + ($expiresInDays * 86400) : 0;

		$label = trim((string)$label);
		if($label === '')
			$label = 'App password';
		$label = substr($label, 0, 128);
		$clientHint = substr(trim((string)$clientHint), 0, 64);

		$plain = self::_generatePlain();
		$hash  = password_hash($plain, PASSWORD_DEFAULT);
		if($hash === false)
			return false;

		$db->Query('INSERT INTO {pre}app_passwords(user,label,client_hint,password_hash,scope,created,expires) '
			.'VALUES(?,?,?,?,?,?,?)',
			$userID,
			$label,
			$clientHint,
			$hash,
			implode(',', $allowed),
			time(),
			$expires);

		$id = (int)$db->InsertId();

		PutLog(sprintf('App password created for user <%d>: id=%d, scope=%s, expires=%d',
			$userID, $id, implode(',', $allowed), $expires),
			PRIO_NOTE, __FILE__, __LINE__);

		return array(
			'id'      => $id,
			'plain'   => $plain,
			'expires' => $expires,
			'label'   => $label,
			'scope'   => $allowed,
		);
	}

	/**
	 * Revoke an app password. Effect is immediate because verification is
	 * uncached and per-request.
	 *
	 * @param int $userID
	 * @param int $id
	 * @return bool
	 */
	public static function Revoke($userID, $id)
	{
		global $db;

		$userID = (int)$userID;
		$id = (int)$id;
		if($userID <= 0 || $id <= 0)
			return false;

		$res = $db->Query('SELECT id FROM {pre}app_passwords WHERE id=? AND user=? AND revoked_at=0',
			$id, $userID);
		$exists = $res->RowCount() === 1;
		$res->Free();
		if(!$exists)
			return false;

		$db->Query('UPDATE {pre}app_passwords SET revoked_at=? WHERE id=? AND user=?',
			time(), $id, $userID);

		PutLog(sprintf('App password <%d> revoked by user <%d>', $id, $userID),
			PRIO_NOTE, __FILE__, __LINE__);

		return true;
	}

	/**
	 * Revoke every active app password of a user (used on password change /
	 * MFA reset / self-service security actions).
	 *
	 * @param int    $userID
	 * @param string $reason (only for the audit log)
	 * @return int Number of revoked rows
	 */
	public static function RevokeAllForUser($userID, $reason = '')
	{
		global $db;

		$userID = (int)$userID;
		if($userID <= 0)
			return 0;

		$res = $db->Query('SELECT COUNT(*) FROM {pre}app_passwords WHERE user=? AND revoked_at=0', $userID);
		list($count) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		if((int)$count > 0)
		{
			$db->Query('UPDATE {pre}app_passwords SET revoked_at=? WHERE user=? AND revoked_at=0',
				time(), $userID);
			PutLog(sprintf('All app passwords revoked for user <%d> (%d rows, reason: %s)',
				$userID, (int)$count, $reason !== '' ? $reason : 'unspecified'),
				PRIO_NOTE, __FILE__, __LINE__);
		}

		return (int)$count;
	}

	/**
	 * Rename an existing (active) app password.
	 *
	 * @param int    $userID
	 * @param int    $id
	 * @param string $label
	 * @return bool
	 */
	public static function Rename($userID, $id, $label)
	{
		global $db;

		$userID = (int)$userID;
		$id = (int)$id;
		if($userID <= 0 || $id <= 0)
			return false;

		$label = trim((string)$label);
		if($label === '')
			return false;
		$label = substr($label, 0, 128);

		$res = $db->Query('SELECT id FROM {pre}app_passwords WHERE id=? AND user=? AND revoked_at=0',
			$id, $userID);
		$exists = $res->RowCount() === 1;
		$res->Free();
		if(!$exists)
			return false;

		$db->Query('UPDATE {pre}app_passwords SET label=? WHERE id=? AND user=?',
			$label, $id, $userID);

		return true;
	}

	/**
	 * List all app passwords for a user (active + revoked).
	 *
	 * @param int $userID
	 * @return array
	 */
	public static function ListForUser($userID)
	{
		global $db;

		$userID = (int)$userID;
		if($userID <= 0)
			return array();

		$rows = array();
		$res = $db->Query('SELECT id,label,client_hint,scope,created,expires,last_used,last_ip,last_scope,revoked_at '
			.'FROM {pre}app_passwords WHERE user=? ORDER BY revoked_at ASC, created DESC', $userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$row['scope_array'] = $row['scope'] !== '' ? explode(',', $row['scope']) : array();
			$row['is_active'] = ((int)$row['revoked_at'] === 0)
				&& ((int)$row['expires'] === 0 || (int)$row['expires'] > time());
			$rows[] = $row;
		}
		$res->Free();

		return $rows;
	}

	/**
	 * Verify a plain-text app password for a user, restricted to the given
	 * scope. Always iterates ALL of the user's active rows to avoid a
	 * timing side channel over the number of app passwords.
	 *
	 * @param int    $userID
	 * @param string $passwordPlain
	 * @param string $requiredScope
	 * @return array|false Row on success (with scope_array), false on failure
	 */
	public static function Verify($userID, $passwordPlain, $requiredScope)
	{
		global $db;

		$userID = (int)$userID;
		$passwordPlain = (string)$passwordPlain;
		$requiredScope = strtolower(trim((string)$requiredScope));

		if($userID <= 0 || $passwordPlain === '' || $requiredScope === '')
			return false;

		if(!self::IsScopeEnabled($requiredScope))
			return false;

		$match = false;
		$now = time();

		$res = $db->Query('SELECT id,label,password_hash,scope,expires,revoked_at '
			.'FROM {pre}app_passwords WHERE user=?', $userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$ok = password_verify($passwordPlain, $row['password_hash']);

			if(!$ok)
				continue;
			if((int)$row['revoked_at'] !== 0)
				continue;
			if((int)$row['expires'] !== 0 && (int)$row['expires'] < $now)
				continue;

			$scopes = $row['scope'] !== '' ? explode(',', $row['scope']) : array();
			if(!in_array($requiredScope, $scopes, true))
				continue;

			if($match === false)   // keep looping to preserve timing
			{
				$row['scope_array'] = $scopes;
				$match = $row;
			}
		}
		$res->Free();

		return $match;
	}

	/**
	 * Update last-used bookkeeping for a matched app password.
	 *
	 * @param int    $id
	 * @param string $ip
	 * @param string $scope
	 */
	public static function TouchLastUsed($id, $ip, $scope)
	{
		global $db;

		$id = (int)$id;
		if($id <= 0)
			return;

		$db->Query('UPDATE {pre}app_passwords SET last_used=?, last_ip=?, last_scope=? WHERE id=?',
			time(),
			substr((string)$ip, 0, 64),
			substr(strtolower((string)$scope), 0, 16),
			$id);
	}

	/**
	 * @return string 20 chars from a Crockford-friendly alphabet (no I/O/1/0),
	 *                formatted as XXXXX-XXXXX-XXXXX-XXXXX for humans.
	 */
	private static function _generatePlain()
	{
		$alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		$aLen = strlen($alphabet);
		$out = '';

		$bytes = random_bytes(self::PLAIN_LENGTH_CHARS);
		for($i = 0; $i < self::PLAIN_LENGTH_CHARS; $i++)
			$out .= $alphabet[ord($bytes[$i]) % $aLen];

		// XXXXX-XXXXX-XXXXX-XXXXX
		return substr($out, 0, 5) . '-'
			. substr($out, 5, 5) . '-'
			. substr($out, 10, 5) . '-'
			. substr($out, 15, 5);
	}
}
