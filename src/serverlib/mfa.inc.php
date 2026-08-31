<?php
/*
 * b1gMail MFA (TOTP, E-Mail-OTP, Backup-Codes) — no external services
 */

/**
 * Create DB tables / prefs columns for MFA (idempotent).
 */
function EnsureMfaSchema()
{
	global $db;

	$db->Query('CREATE TABLE IF NOT EXISTS {pre}mfa_accounts (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`account_type` enum(\'user\',\'admin\') NOT NULL,
		`account_id` int(11) NOT NULL,
		`method` enum(\'totp\',\'email\') NOT NULL DEFAULT \'totp\',
		`totp_secret` varchar(255) NOT NULL DEFAULT \'\',
		`email_enabled` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
		`totp_enabled` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
		`enabled` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
		`setup_required` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\',
		`recovery_mode` enum(\'no\',\'altmail\') NOT NULL DEFAULT \'no\',
		`created` int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY (`id`),
		UNIQUE KEY `account` (`account_type`,`account_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

	$db->Query('CREATE TABLE IF NOT EXISTS {pre}mfa_backup_codes (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`mfa_account_id` int(11) NOT NULL,
		`code_hash` varchar(255) NOT NULL,
		`used_at` int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY (`id`),
		KEY `mfa_account_id` (`mfa_account_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

	$db->Query('CREATE TABLE IF NOT EXISTS {pre}mfa_email_codes (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`mfa_account_id` int(11) NOT NULL,
		`code_hash` varchar(255) NOT NULL,
		`expires` int(11) NOT NULL DEFAULT 0,
		`created` int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY (`id`),
		KEY `mfa_account_id` (`mfa_account_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

	$prefColumns = array(
		'mfa_admin_enable'       => "enum('yes','no') NOT NULL DEFAULT 'no'",
		'mfa_admin_user_setup'   => "enum('yes','no') NOT NULL DEFAULT 'yes'",
		'mfa_admin_default'      => "enum('email','totp') NOT NULL DEFAULT 'totp'",
		'mfa_admin_required'     => "enum('yes','no') NOT NULL DEFAULT 'no'",
		'login_notify_admin'       => "enum('yes','no') NOT NULL DEFAULT 'no'",
		'mfa_li_enable'          => "enum('yes','no') NOT NULL DEFAULT 'no'",
		'mfa_li_user_setup'      => "enum('yes','no') NOT NULL DEFAULT 'yes'",
		'mfa_li_default'         => "enum('email','totp') NOT NULL DEFAULT 'totp'",
		'login_notify_li'        => "enum('yes','no') NOT NULL DEFAULT 'no'",
	);

	$mfaColumns = array(
		'setup_required' => "enum('yes','no') NOT NULL DEFAULT 'no'",
		'recovery_mode'  => "enum('no','altmail') NOT NULL DEFAULT 'no'",
		'enabled_at'     => "int(11) NOT NULL DEFAULT 0",
	);
	foreach($mfaColumns as $column => $definition)
	{
		$res = $db->Query('SHOW COLUMNS FROM {pre}mfa_accounts LIKE ?', $column);
		$exists = $res->RowCount() > 0;
		$res->Free();
		if(!$exists)
			$db->Query('ALTER TABLE {pre}mfa_accounts ADD COLUMN `' . $column . '` ' . $definition);
	}

	$db->Query('UPDATE {pre}mfa_accounts SET enabled_at=created WHERE enabled=? AND (enabled_at=0 OR enabled_at IS NULL)',
		'yes');

	foreach($prefColumns as $column => $definition)
	{
		$res = $db->Query('SHOW COLUMNS FROM {pre}prefs LIKE ?', $column);
		$exists = $res->RowCount() > 0;
		$res->Free();

		if(!$exists)
			$db->Query('ALTER TABLE {pre}prefs ADD COLUMN `' . $column . '` ' . $definition);
	}
}

function MfaApplyPrefDefaults()
{
	global $bm_prefs;

	$defaults = array(
		'mfa_admin_enable'     => 'no',
		'mfa_admin_user_setup' => 'yes',
		'mfa_admin_default'    => 'totp',
		'mfa_admin_required'   => 'no',
		'login_notify_admin'   => 'no',
		'mfa_li_enable'        => 'no',
		'mfa_li_user_setup'    => 'yes',
		'mfa_li_default'       => 'totp',
		'login_notify_li'      => 'no',
	);

	foreach($defaults as $key => $val)
	{
		if(!isset($bm_prefs[$key]) || $bm_prefs[$key] === '')
			$bm_prefs[$key] = $val;
	}
}

/**
 * Group option helpers (module core).
 */
function MfaGroupOption($groupID, $key, $default = 'no')
{
	global $db;

	$res = $db->Query('SELECT value FROM {pre}groupoptions WHERE gruppe=? AND module=? AND `key`=?',
		(int)$groupID, 'core', $key);
	if($res->RowCount() != 1)
	{
		$res->Free();
		return $default;
	}
	list($val) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();

	if($val === '' || $val === null)
		return $default;

	// group form checkboxes are stored as 0/1
	if($val === '1' || $val === 1)
		return 'yes';
	if($val === '0' || $val === 0)
		return 'no';

	return $val;
}

/**
 * Split GetGroupOptions() into core (MFA/login) and plugin options for templates.
 *
 * @param array $groupOptions
 * @return array{0: array, 1: array} [core, plugins]
 */
function MfaSplitGroupOptionsForTemplate($groupOptions)
{
	$core = array();
	$pluginOpts = array();

	if(!is_array($groupOptions))
		return array($core, $pluginOpts);

	foreach($groupOptions as $key => $info)
	{
		if(is_array($info) && isset($info['module']) && $info['module'] === 'core')
			$core[$key] = $info;
		else
			$pluginOpts[$key] = $info;
	}

	return array($core, $pluginOpts);
}

/**
 * Register MFA group options (core module).
 */
function MfaRegisterCoreGroupOptions()
{
	global $plugins, $lang_admin;

	if(!isset($plugins) || !is_object($plugins))
		return;

	$plugins->RegisterCoreGroupOption('mfa_allow_setup',
		FIELD_CHECKBOX,
		isset($lang_admin['mfa_group_allow_setup']) ? $lang_admin['mfa_group_allow_setup'] : 'MFA: users may configure',
		'',
		'yes');
	$plugins->RegisterCoreGroupOption('mfa_required',
		FIELD_CHECKBOX,
		isset($lang_admin['mfa_group_required']) ? $lang_admin['mfa_group_required'] : 'MFA: required',
		'',
		'no');
	$plugins->RegisterCoreGroupOption('mfa_default',
		FIELD_DROPDOWN,
		isset($lang_admin['mfa_group_default']) ? $lang_admin['mfa_group_default'] : 'MFA: default method',
		array('totp' => 'TOTP', 'email' => 'E-Mail'),
		'totp');
	$plugins->RegisterCoreGroupOption('login_notify',
		FIELD_CHECKBOX,
		isset($lang_admin['login_notify_group']) ? $lang_admin['login_notify_group'] : 'Login notification (new IP)',
		'',
		'no');
}

class BMMfa
{
	const TOTP_PERIOD = 30;
	const TOTP_DIGITS = 6;
	const TOTP_WINDOW = 1;
	const EMAIL_CODE_TTL = 600;
	const PENDING_TTL = 600;
	const BACKUP_CODE_COUNT = 10;

	/**
	 * @param string $accountType user|admin
	 * @param int    $accountID
	 * @return array|false
	 */
	public static function GetAccount($accountType, $accountID)
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}mfa_accounts WHERE account_type=? AND account_id=?',
			$accountType, (int)$accountID);
		if($res->RowCount() != 1)
		{
			$res->Free();
			return false;
		}
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return $row;
	}

	/**
	 * @param string $accountType
	 * @param int    $accountID
	 * @param int    $groupID  LI only
	 * @return bool
	 */
	public static function IsRequiredForLogin($accountType, $accountID, $groupID = 0)
	{
		global $bm_prefs;

		if($accountType === 'admin')
		{
			if($bm_prefs['mfa_admin_enable'] != 'yes')
				return false;
			if($bm_prefs['mfa_admin_required'] == 'yes')
				return true;
		}
		else
		{
			if($bm_prefs['mfa_li_enable'] != 'yes')
				return false;
			if($groupID > 0 && MfaGroupOption($groupID, 'mfa_required', 'no') == 'yes')
				return true;
		}

		$row = self::GetAccount($accountType, $accountID);
		return is_array($row) && $row['enabled'] == 'yes';
	}

	/**
	 * @return string Base32 secret (16 chars)
	 */
	public static function GenerateTotpSecret()
	{
		$alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
		$secret = '';
		for($i = 0; $i < 16; $i++)
			$secret .= $alphabet[random_int(0, 31)];
		return $secret;
	}

	/**
	 * @param string $secret Base32
	 * @param int|null $timeSlice
	 * @return string 6-digit code
	 */
	public static function TotpCode($secret, $timeSlice = null)
	{
		if($timeSlice === null)
			$timeSlice = floor(time() / self::TOTP_PERIOD);

		$key = self::Base32Decode($secret);
		$time = pack('N*', 0, $timeSlice);
		$hash = hash_hmac('sha1', $time, $key, true);
		$offset = ord(substr($hash, -1)) & 0x0F;
		$value = (
			((ord($hash[$offset]) & 0x7F) << 24)
			| ((ord($hash[$offset + 1]) & 0xFF) << 16)
			| ((ord($hash[$offset + 2]) & 0xFF) << 8)
			| (ord($hash[$offset + 3]) & 0xFF)
		) % (10 ** self::TOTP_DIGITS);

		return str_pad((string)$value, self::TOTP_DIGITS, '0', STR_PAD_LEFT);
	}

	/**
	 * @param string $secret
	 * @param string $code
	 * @return bool
	 */
	public static function VerifyTotp($secret, $code)
	{
		$code = preg_replace('/\s+/', '', (string)$code);
		if(!preg_match('/^\d{6}$/', $code))
			return false;

		$slice = floor(time() / self::TOTP_PERIOD);
		for($i = -self::TOTP_WINDOW; $i <= self::TOTP_WINDOW; $i++)
		{
			if(hash_equals(self::TotpCode($secret, $slice + $i), $code))
				return true;
		}
		return false;
	}

	/**
	 * otpauth:// URI for authenticator apps
	 *
	 * @param string $label e.g. user@domain
	 * @param string $secret
	 * @param string $issuer
	 * @return string
	 */
	public static function ProvisioningUri($label, $secret, $issuer = 'b1gMail')
	{
		$params = array(
			'secret' => $secret,
			'issuer' => rawurlencode($issuer),
			'algorithm' => 'SHA1',
			'digits' => self::TOTP_DIGITS,
			'period' => self::TOTP_PERIOD,
		);

		return 'otpauth://totp/' . rawurlencode($issuer . ':' . $label)
			. '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
	}

	/**
	 * SVG QR code (no external HTTP).
	 *
	 * TODO (Phase 4): Do not depend on Datenschutz-Plugin TCPDF — move encoder to
	 * serverlib/3rdparty/qrcode/ (see SESSION-SECURITY-PLAN.md §4.2).
	 *
	 * @param string $uri
	 * @param int    $moduleSize px per module
	 * @return string SVG
	 */
	public static function QrSvg($uri, $moduleSize = 4)
	{
		$qrFile = B1GMAIL_DIR . 'serverlib/3rdparty/qrcode/qrcode.php';
		if(!file_exists($qrFile))
			$qrFile = B1GMAIL_DIR . 'plugins/php/tcpdf/include/barcodes/qrcode.php';
		if(!file_exists($qrFile))
			return '';

		require_once $qrFile;

		$qr = new QRcode($uri, 'L');
		$barcode = $qr->getBarcodeArray();
		if(!is_array($barcode) || empty($barcode['bcode']))
			return '';

		$rows = (int)$barcode['num_rows'];
		$cols = (int)$barcode['num_cols'];
		$w = $cols * $moduleSize;
		$h = $rows * $moduleSize;

		$svg = '<?xml version="1.0" encoding="UTF-8"?>'
			. '<svg xmlns="http://www.w3.org/2000/svg" width="' . $w . '" height="' . $h . '" viewBox="0 0 ' . $w . ' ' . $h . '">'
			. '<rect width="100%" height="100%" fill="#fff"/>';

		for($y = 0; $y < $rows; $y++)
		{
			for($x = 0; $x < $cols; $x++)
			{
				if(!empty($barcode['bcode'][$y][$x]))
				{
					$svg .= '<rect x="' . ($x * $moduleSize) . '" y="' . ($y * $moduleSize)
						. '" width="' . $moduleSize . '" height="' . $moduleSize . '" fill="#000"/>';
				}
			}
		}

		$svg .= '</svg>';
		return $svg;
	}

	/**
	 * @param int $mfaAccountID
	 * @return array Plaintext codes (show once to user)
	 */
	public static function GenerateBackupCodes($mfaAccountID)
	{
		global $db;

		$db->Query('DELETE FROM {pre}mfa_backup_codes WHERE mfa_account_id=?', (int)$mfaAccountID);

		$plain = array();
		for($i = 0; $i < self::BACKUP_CODE_COUNT; $i++)
		{
			$code = self::RandomDigits(8);
			$plain[] = $code;
			$db->Query('INSERT INTO {pre}mfa_backup_codes(mfa_account_id,code_hash,used_at) VALUES(?,?,0)',
				(int)$mfaAccountID,
				password_hash($code, PASSWORD_DEFAULT));
		}

		return $plain;
	}

	/**
	 * @param int    $mfaAccountID
	 * @param string $code
	 * @return bool
	 */
	public static function VerifyBackupCode($mfaAccountID, $code)
	{
		global $db;

		$code = preg_replace('/\s+/', '', strtoupper((string)$code));

		$res = $db->Query('SELECT id,code_hash FROM {pre}mfa_backup_codes WHERE mfa_account_id=? AND used_at=0',
			(int)$mfaAccountID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if(password_verify($code, $row['code_hash']))
			{
				$db->Query('UPDATE {pre}mfa_backup_codes SET used_at=? WHERE id=?',
					time(), (int)$row['id']);
				$res->Free();
				return true;
			}
		}
		$res->Free();

		return false;
	}

	/**
	 * Audit log for MFA e-mail code dispatch (never logs the code itself).
	 *
	 * @param string $context
	 * @param int    $mfaAccountID
	 * @param string $toEmail
	 * @param string $outcome sent|failed|blocked|no_recipient|skipped_active|skipped_session|unsupported
	 * @param int    $forUserID
	 * @param string $transport
	 */
	private static function LogEmailCodeEvent($context, $mfaAccountID, $toEmail, $outcome, $forUserID = -1, $transport = '')
	{
		global $db, $bm_prefs;

		if($transport === '')
			$transport = isset($bm_prefs['send_method']) ? $bm_prefs['send_method'] : 'unknown';

		$accountType = 'user';
		$accountID = $forUserID >= 0 ? (int)$forUserID : 0;

		if($mfaAccountID > 0)
		{
			$res = $db->Query('SELECT account_type,account_id FROM {pre}mfa_accounts WHERE id=?', (int)$mfaAccountID);
			if($res->RowCount() == 1)
			{
				$row = $res->FetchArray(MYSQLI_ASSOC);
				$accountType = $row['account_type'];
				$accountID = (int)$row['account_id'];
				$res->Free();
			}
		}

		$masked = ($toEmail !== '') ? self::MaskEmail($toEmail) : '-';
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$prio = PRIO_NOTE;
		if(in_array($outcome, array('failed', 'blocked', 'no_recipient', 'unsupported'), true))
			$prio = PRIO_WARNING;

		PutLog(sprintf('MFA e-mail code %s [%s] via=<%s> %s <%d> mfa=<%d> to=<%s> IP=<%s>',
			$outcome,
			$context,
			$transport,
			$accountType,
			$accountID,
			(int)$mfaAccountID,
			$masked,
			$ip),
			$prio,
			__FILE__,
			__LINE__);
	}

	/**
	 * @param int $mfaAccountID
	 * @return string|false 6-digit code sent
	 */
	public static function SendEmailCode($mfaAccountID, $toEmail, $forUserID = -1, $context = 'unknown')
	{
		global $bm_prefs, $db;

		$toEmail = ExtractMailAddress(DecodeEMail(trim((string)$toEmail)));
		if($toEmail === '')
		{
			self::LogEmailCodeEvent($context, (int)$mfaAccountID, '', 'no_recipient', $forUserID);
			return false;
		}
		if(RecipientBlocked($toEmail))
		{
			self::LogEmailCodeEvent($context, (int)$mfaAccountID, $toEmail, 'blocked', $forUserID);
			return false;
		}

		$code = self::RandomDigits(6);
		$db->Query('DELETE FROM {pre}mfa_email_codes WHERE mfa_account_id=?', (int)$mfaAccountID);
		$db->Query('INSERT INTO {pre}mfa_email_codes(mfa_account_id,code_hash,expires,created) VALUES(?,?,?,?)',
			(int)$mfaAccountID,
			password_hash($code, PASSWORD_DEFAULT),
			time() + self::EMAIL_CODE_TTL,
			time());

		$subject = isset($GLOBALS['lang_user']['mfa_email_subject'])
			? $GLOBALS['lang_user']['mfa_email_subject']
			: 'Your login code';
		$template = 'mfa_email_code';
		$transport = isset($bm_prefs['send_method']) ? $bm_prefs['send_method'] : 'unknown';

		if(!SystemMail(
			$bm_prefs['passmail_abs'],
			$toEmail,
			str_replace('%%code%%', $code, $subject),
			$template,
			array('code' => $code),
			$forUserID
		))
		{
			$db->Query('DELETE FROM {pre}mfa_email_codes WHERE mfa_account_id=?', (int)$mfaAccountID);
			self::LogEmailCodeEvent($context, (int)$mfaAccountID, $toEmail, 'failed', $forUserID, $transport);
			return false;
		}

		self::LogEmailCodeEvent($context, (int)$mfaAccountID, $toEmail, 'sent', $forUserID, $transport);
		return $code;
	}

	/**
	 * @param int    $mfaAccountID
	 * @param string $code
	 * @return bool
	 */
	public static function VerifyEmailCode($mfaAccountID, $code)
	{
		global $db;

		$code = preg_replace('/\s+/', '', (string)$code);
		$res = $db->Query('SELECT id,code_hash,expires FROM {pre}mfa_email_codes WHERE mfa_account_id=? ORDER BY id DESC LIMIT 1',
			(int)$mfaAccountID);
		if($res->RowCount() != 1)
		{
			$res->Free();
			return false;
		}
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		if((int)$row['expires'] < time())
			return false;

		if(!password_verify($code, $row['code_hash']))
			return false;

		$db->Query('DELETE FROM {pre}mfa_email_codes WHERE id=?', (int)$row['id']);
		return true;
	}

	/**
	 * @param int $mfaAccountID
	 * @return bool
	 */
	public static function HasActiveEmailCode($mfaAccountID)
	{
		return self::GetActiveEmailCodeRemainingSeconds($mfaAccountID) > 0;
	}

	/**
	 * @param int $mfaAccountID
	 * @return int seconds until expiry, 0 if none or expired
	 */
	public static function GetActiveEmailCodeRemainingSeconds($mfaAccountID)
	{
		global $db;

		$res = $db->Query('SELECT expires FROM {pre}mfa_email_codes WHERE mfa_account_id=? ORDER BY id DESC LIMIT 1',
			(int)$mfaAccountID);
		if($res->RowCount() != 1)
		{
			$res->Free();
			return 0;
		}
		list($expires) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		$remaining = (int)$expires - time();

		return $remaining > 0 ? $remaining : 0;
	}

	/**
	 * @param int $mfaAccountID
	 * @return int Unix timestamp when active code expires, 0 if none
	 */
	public static function GetActiveEmailCodeExpiresAt($mfaAccountID)
	{
		global $db;

		$res = $db->Query('SELECT expires FROM {pre}mfa_email_codes WHERE mfa_account_id=? ORDER BY id DESC LIMIT 1',
			(int)$mfaAccountID);
		if($res->RowCount() != 1)
		{
			$res->Free();
			return 0;
		}
		list($expires) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		$expires = (int)$expires;

		return $expires >= time() ? $expires : 0;
	}

	/**
	 * @param int $seconds
	 * @return string
	 */
	public static function FormatRemainingWaitTime($seconds)
	{
		$seconds = max(0, (int)$seconds);
		$m = (int)floor($seconds / 60);
		$s = $seconds % 60;

		return $m . ':' . ($s < 10 ? '0' : '') . $s;
	}

	/**
	 * @param int    $userID
	 * @param int    $mfaAccountID
	 * @param string $langKey
	 * @return string
	 */
	public static function EmailCodeValidityMessage($userID, $mfaAccountID, $langKey = 'mfa_email_code_validity')
	{
		global $lang_user;

		$to = self::EmailAddressForUserMfa((int)$userID);
		$masked = $to !== false ? self::MaskEmail($to) : '';
		$wait = self::FormatRemainingWaitTime(self::GetActiveEmailCodeRemainingSeconds((int)$mfaAccountID));
		$phrase = (is_array($lang_user) && !empty($lang_user[$langKey]))
			? $lang_user[$langKey]
			: 'A code was sent to %s. It remains valid for %s.';

		return sprintf($phrase, $masked, $wait);
	}

	/**
	 * Store pending MFA step after password OK.
	 *
	 * @param string $accountType
	 * @param int    $accountID
	 * @param array  $meta email, etc.
	 */
	public static function BeginPending($accountType, $accountID, $meta = array())
	{
		@session_start();
		$_SESSION['bm_mfaPending'] = array(
			'type'       => $accountType,
			'account_id' => (int)$accountID,
			'expires'    => time() + self::PENDING_TTL,
			'attempts'   => 0,
			'meta'       => $meta,
		);
	}

	/**
	 * @return array|false
	 */
	public static function GetPending()
	{
		if(empty($_SESSION['bm_mfaPending']))
			return false;

		$p = $_SESSION['bm_mfaPending'];
		if(!is_array($p) || (int)$p['expires'] < time())
		{
			unset($_SESSION['bm_mfaPending']);
			return false;
		}

		return $p;
	}

	public static function ClearPending()
	{
		unset($_SESSION['bm_mfaPending']);
	}

	/**
	 * Remove LI session flags while MFA verification is pending.
	 */
	public static function ClearUserLoginSession()
	{
		unset(
			$_SESSION['bm_userLoggedIn'],
			$_SESSION['bm_userID'],
			$_SESSION['bm_sessionToken'],
			$_SESSION['bm_xorCryptKey'],
			$_SESSION['bm_sessionEpoch']
		);
	}

	/**
	 * @param string $accountType user|admin
	 * @param int    $accountID
	 * @return bool true if obsolete pending state was removed
	 */
	public static function ClearPendingIfVerifyNotRequired($accountType, $accountID)
	{
		$pending = self::GetPending();
		if($pending === false)
			return false;
		if($pending['type'] !== $accountType || (int)$pending['account_id'] !== (int)$accountID)
			return false;

		$row = self::GetAccount($accountType, $accountID);
		if(is_array($row) && self::RequiresMfaVerifyAtLogin($row))
			return false;

		self::ClearPending();
		return true;
	}

	/**
	 * @param string $code
	 * @param bool   $isBackup
	 * @return bool
	 */
	public static function VerifyPendingCode($code, $isBackup = false)
	{
		$pending = self::GetPending();
		if($pending === false)
			return false;

		$pending['attempts'] = (int)$pending['attempts'] + 1;
		$_SESSION['bm_mfaPending'] = $pending;

		if($pending['attempts'] > 10)
		{
			self::ClearPending();
			return false;
		}

		$row = self::GetAccount($pending['type'], $pending['account_id']);
		if(!is_array($row) || !self::RequiresMfaVerifyAtLogin($row))
		{
			self::ClearPending();
			return false;
		}

		if($isBackup)
			return self::VerifyBackupCode((int)$row['id'], $code);

		if($row['totp_secret'] != '')
		{
			if(self::VerifyTotp($row['totp_secret'], $code))
				return true;
		}

		if($row['email_enabled'] == 'yes')
			return self::VerifyEmailCode((int)$row['id'], $code);

		return false;
	}

	/**
	 * @param int $userID
	 * @return string|false
	 */
	public static function RecoveryEmailForUser($userID)
	{
		global $db;

		$res = $db->Query('SELECT altmail FROM {pre}users WHERE id=?', (int)$userID);
		if($res->RowCount() != 1)
		{
			$res->Free();
			return false;
		}
		list($altMail) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		$altMail = DecodeEMail($altMail);
		$altMail = ExtractMailAddress($altMail);
		if(strlen(trim($altMail)) <= 5)
			return false;

		return $altMail;
	}

	/**
	 * @param int $userID
	 * @return bool
	 */
	public static function UserCanUseEmailMfa($userID)
	{
		return self::RecoveryEmailForUser((int)$userID) !== false;
	}

	/**
	 * E-mail address for MFA codes (alternative e-mail).
	 *
	 * @param int $userID
	 * @return string|false
	 */
	public static function EmailAddressForUserMfa($userID)
	{
		return self::RecoveryEmailForUser((int)$userID);
	}

	/**
	 * @param string $email
	 * @return string
	 */
	public static function MaskEmail($email)
	{
		$email = trim((string)$email);
		if($email === '' || strpos($email, '@') === false)
			return '***';

		list($local, $domain) = explode('@', $email, 2);
		$len = strlen($local);
		if($len <= 1)
			$maskedLocal = '*';
		else if($len <= 3)
			$maskedLocal = substr($local, 0, 1) . str_repeat('*', $len - 1);
		else
			$maskedLocal = substr($local, 0, 2) . str_repeat('*', $len - 2);

		return $maskedLocal . '@' . $domain;
	}

	/**
	 * @param int    $userID
	 * @param int    $groupID
	 * @param string $sessionKey
	 * @return string totp|email
	 */
	public static function ResolveSetupMethod($userID, $groupID, $sessionKey = 'bm_mfaSetupMethod')
	{
		$userID = (int)$userID;
		if(isset($_SESSION[$sessionKey])
			&& ($_SESSION[$sessionKey] === 'email' || $_SESSION[$sessionKey] === 'totp'))
			$method = $_SESSION[$sessionKey];
		else
			$method = self::DefaultMethodForGroup((int)$groupID);

		if($method === 'email' && !self::UserCanUseEmailMfa($userID))
			$method = 'totp';

		$_SESSION[$sessionKey] = $method;

		return $method;
	}

	/**
	 * @param string $method
	 * @param int    $userID
	 * @param int    $groupID
	 * @param string $sessionKey
	 */
	public static function SetSetupMethod($method, $userID, $groupID, $sessionKey = 'bm_mfaSetupMethod')
	{
		$method = ($method === 'email') ? 'email' : 'totp';
		if($method === 'email' && !self::UserCanUseEmailMfa((int)$userID))
			$method = 'totp';

		$_SESSION[$sessionKey] = $method;
		unset($_SESSION['bm_mfaSetupSecret'], $_SESSION['bm_mfaSetupEmailSent'], $_SESSION['bm_mfaSetupStep'],
			$_SESSION['bm_mfaPrefsEmailSent']);
		self::ResetIncompleteSetup('user', (int)$userID, $method);
	}

	/**
	 * @param string $accountType
	 * @param int    $accountID
	 * @param string $method totp|email
	 */
	public static function ResetIncompleteSetup($accountType, $accountID, $method = 'totp')
	{
		global $db;

		$row = self::GetAccount($accountType, (int)$accountID);
		if(!is_array($row) || $row['enabled'] == 'yes')
			return;

		$mfaID = (int)$row['id'];
		$method = ($method === 'email') ? 'email' : 'totp';

		$db->Query('DELETE FROM {pre}mfa_backup_codes WHERE mfa_account_id=?', $mfaID);
		$db->Query('DELETE FROM {pre}mfa_email_codes WHERE mfa_account_id=?', $mfaID);
		$db->Query('UPDATE {pre}mfa_accounts SET method=?,totp_secret=?,totp_enabled=?,email_enabled=?,enabled=?,setup_required=? WHERE id=?',
			$method,
			'',
			'no',
			'no',
			'no',
			'yes',
			$mfaID);
	}

	/**
	 * @param int $mfaAccountID
	 * @param string $method totp|email
	 */
	public static function ActivateMethod($mfaAccountID, $method)
	{
		global $db;

		$method = ($method === 'email') ? 'email' : 'totp';
		if($method === 'email')
		{
			$db->Query('UPDATE {pre}mfa_accounts SET method=?,totp_secret=?,totp_enabled=?,email_enabled=?,enabled=?,enabled_at=? WHERE id=?',
				'email',
				'',
				'no',
				'yes',
				'yes',
				time(),
				(int)$mfaAccountID);
		}
		else
		{
			$db->Query('UPDATE {pre}mfa_accounts SET method=?,email_enabled=?,enabled=?,enabled_at=? WHERE id=?',
				'totp',
				'no',
				'yes',
				time(),
				(int)$mfaAccountID);
		}
	}

	/**
	 * @param int $userID
	 * @param int $groupID
	 * @return bool
	 */
	public static function SendSetupEmailCode($userID, $groupID, $context = 'setup')
	{
		$userID = (int)$userID;
		$to = self::EmailAddressForUserMfa($userID);
		if($to === false)
		{
			self::LogEmailCodeEvent($context, 0, '', 'no_recipient', $userID);
			return false;
		}

		$mfaID = self::EnsureUserAccount($userID, (int)$groupID, 'email', '');
		global $db;
		$db->Query('UPDATE {pre}mfa_accounts SET method=?,totp_secret=?,totp_enabled=?,email_enabled=?,enabled=?,setup_required=? WHERE id=?',
			'email',
			'',
			'no',
			'no',
			'no',
			'yes',
			(int)$mfaID);

		return self::SendEmailCode((int)$mfaID, $to, $userID, $context) !== false;
	}

	/**
	 * Send setup e-mail code when the wizard/prefs UI shows e-mail verification.
	 *
	 * @param int    $userID
	 * @param int    $groupID
	 * @param string $sentSessionKey
	 * @param bool   $force            Ignore session flag and send even if a code exists
	 * @return string sent|failed|active|unsupported
	 */
	public static function TrySendSetupEmailCodeIfNeeded($userID, $groupID, $sentSessionKey = 'bm_mfaSetupEmailSent', $force = false, $context = 'setup_wizard')
	{
		$userID = (int)$userID;
		if(!self::UserCanUseEmailMfa($userID))
		{
			self::LogEmailCodeEvent($context, 0, '', 'unsupported', $userID);
			return 'unsupported';
		}

		$acc = self::GetAccount('user', $userID);
		$mfaID = is_array($acc) ? (int)$acc['id'] : 0;
		$toForLog = self::EmailAddressForUserMfa($userID);
		if($toForLog === false)
			$toForLog = '';

		if(!$force)
		{
			if($mfaID > 0 && self::HasActiveEmailCode($mfaID))
			{
				if($context !== 'setup_wizard_auto')
					self::LogEmailCodeEvent($context, $mfaID, $toForLog, 'skipped_active', $userID);
				return 'active';
			}

			// Stale session flag without a valid code (e.g. after expiry) must not block re-send
			if(!empty($_SESSION[$sentSessionKey]))
				unset($_SESSION[$sentSessionKey]);
		}

		if(self::SendSetupEmailCode($userID, (int)$groupID, $context))
		{
			$_SESSION[$sentSessionKey] = true;
			return 'sent';
		}

		return 'failed';
	}

	/**
	 * @param array|false $account
	 * @return string totp|email|''
	 */
	public static function ActiveMethod($account)
	{
		if(!is_array($account) || $account['enabled'] != 'yes')
			return '';

		if($account['email_enabled'] == 'yes')
			return 'email';
		if($account['totp_enabled'] == 'yes')
			return 'totp';

		return isset($account['method']) && $account['method'] === 'email' ? 'email' : 'totp';
	}

	/**
	 * @param string $accountType
	 * @param int    $accountID
	 * @param string $mode full|invalidate_totp
	 * @param array  $options setup_required (bool)
	 * @return bool
	 */
	public static function ResetAccount($accountType, $accountID, $mode = 'full', $options = array())
	{
		global $db;

		$accountID = (int)$accountID;
		$row = self::GetAccount($accountType, $accountID);
		$mfaID = is_array($row) ? (int)$row['id'] : 0;

		if($mfaID > 0)
		{
			$db->Query('DELETE FROM {pre}mfa_backup_codes WHERE mfa_account_id=?', $mfaID);
			$db->Query('DELETE FROM {pre}mfa_email_codes WHERE mfa_account_id=?', $mfaID);
		}

		$setupRequired = !empty($options['setup_required']) ? 'yes' : 'no';

		if($mode === 'invalidate_totp')
		{
			if($mfaID > 0)
			{
				$db->Query('UPDATE {pre}mfa_accounts SET totp_secret=?,totp_enabled=?,recovery_mode=?,setup_required=?,email_enabled=?,enabled=? WHERE id=?',
					'',
					'no',
					'altmail',
					$setupRequired,
					'yes',
					'yes',
					$mfaID);
			}
			else
			{
				$db->Query('INSERT INTO {pre}mfa_accounts(account_type,account_id,method,totp_secret,email_enabled,totp_enabled,enabled,setup_required,recovery_mode,created) VALUES(?,?,?,?,?,?,?,?,?,?)',
					$accountType,
					$accountID,
					'email',
					'',
					'yes',
					'no',
					'yes',
					$setupRequired,
					'altmail',
					time());
			}
		}
		else
		{
			if($mfaID > 0)
				$db->Query('DELETE FROM {pre}mfa_accounts WHERE id=?', $mfaID);
			if($setupRequired === 'yes')
			{
				$db->Query('INSERT INTO {pre}mfa_accounts(account_type,account_id,method,totp_secret,email_enabled,totp_enabled,enabled,setup_required,recovery_mode,created) VALUES(?,?,?,?,?,?,?,?,?,?)',
					$accountType,
					$accountID,
					'email',
					'',
					'no',
					'no',
					'no',
					'yes',
					'no',
					time());
			}
		}

		if($accountType === 'user')
			BMUser::InvalidateRememberMeForUser($accountID);

		return true;
	}

	/**
	 * @param array $row mfa_accounts row
	 * @return bool
	 */
	public static function RequiresMfaVerifyAtLogin($row)
	{
		if(!is_array($row))
			return false;

		if($row['totp_secret'] != '' && $row['totp_enabled'] == 'yes')
			return true;

		if($row['email_enabled'] == 'yes')
			return true;

		if($row['recovery_mode'] === 'altmail')
			return true;

		// Active MFA row with TOTP secret but inconsistent flags (e.g. after aborted setup wizard)
		if($row['enabled'] == 'yes' && $row['totp_secret'] != '')
			return true;

		return false;
	}

	/**
	 * MFA is active enough for login (TOTP or e-mail code at sign-in).
	 *
	 * @param string $accountType
	 * @param int    $accountID
	 * @return bool
	 */
	public static function IsLoginReady($accountType, $accountID)
	{
		$row = self::GetAccount($accountType, $accountID);
		if(!is_array($row) || $row['enabled'] != 'yes')
			return false;

		return self::RequiresMfaVerifyAtLogin($row);
	}

	/**
	 * @param string $accountType
	 * @param int    $accountID
	 * @param int    $groupID
	 * @return bool
	 */
	public static function IsFullyConfigured($accountType, $accountID, $groupID = 0)
	{
		$row = self::GetAccount($accountType, $accountID);
		if(!is_array($row) || $row['enabled'] != 'yes')
			return false;

		$hasSecondFactor = ($row['totp_enabled'] == 'yes' && $row['totp_secret'] != '')
			|| $row['email_enabled'] == 'yes';

		if(!$hasSecondFactor)
			return false;

		global $db;
		$res = $db->Query('SELECT COUNT(*) FROM {pre}mfa_backup_codes WHERE mfa_account_id=? AND used_at=0',
			(int)$row['id']);
		list($cnt) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return (int)$cnt > 0;
	}

	/**
	 * @param string $accountType
	 * @param int    $accountID
	 * @param int    $groupID
	 * @return bool
	 */
	public static function NeedsSetupWizard($accountType, $accountID, $groupID = 0)
	{
		global $bm_prefs;

		if($accountType === 'user' && $bm_prefs['mfa_li_enable'] != 'yes')
			return false;
		if($accountType === 'admin' && $bm_prefs['mfa_admin_enable'] != 'yes')
			return false;

		if(self::IsLoginReady($accountType, $accountID))
			return false;

		$row = self::GetAccount($accountType, $accountID);
		if(self::RequiresMfaVerifyAtLogin($row))
			return false;

		if(is_array($row) && $row['setup_required'] == 'yes'
			&& self::IsMandatoryForAccount($accountType, $accountID, $groupID))
			return true;

		if(self::IsMandatoryForAccount($accountType, $accountID, $groupID))
			return true;

		return false;
	}

	/**
	 * Clear setup_required when MFA is no longer mandatory (e.g. group option disabled).
	 *
	 * @param string $accountType
	 * @param int    $accountID
	 * @param int    $groupID
	 */
	public static function ClearObsoleteSetupRequired($accountType, $accountID, $groupID = 0)
	{
		if(self::IsMandatoryForAccount($accountType, $accountID, $groupID))
			return;

		$row = self::GetAccount($accountType, $accountID);
		if(!is_array($row) || $row['setup_required'] != 'yes')
			return;

		global $db;
		$db->Query('UPDATE {pre}mfa_accounts SET setup_required=? WHERE id=?', 'no', (int)$row['id']);
	}

	/**
	 * Clear setup_required after prefs or MFA verify when login already works.
	 *
	 * @param string $accountType
	 * @param int    $accountID
	 */
	public static function ClearStaleSetupRequired($accountType, $accountID)
	{
		if(!self::IsLoginReady($accountType, $accountID))
			return;

		$row = self::GetAccount($accountType, $accountID);
		if(!is_array($row) || $row['setup_required'] != 'yes')
			return;

		global $db;
		$db->Query('UPDATE {pre}mfa_accounts SET setup_required=? WHERE id=?', 'no', (int)$row['id']);
		self::SetSetupRequiredSession(false);
	}

	/**
	 * @param string $accountType
	 * @param int    $accountID
	 * @param int    $groupID
	 * @return bool
	 */
	public static function IsMandatoryForAccount($accountType, $accountID, $groupID = 0)
	{
		global $bm_prefs;

		if($accountType === 'admin')
			return $bm_prefs['mfa_admin_enable'] == 'yes' && $bm_prefs['mfa_admin_required'] == 'yes';

		if($bm_prefs['mfa_li_enable'] != 'yes')
			return false;
		if($groupID > 0 && MfaGroupOption($groupID, 'mfa_required', 'no') == 'yes')
			return true;

		return false;
	}

	/**
	 * @param int $groupID
	 * @return string totp|email
	 */
	public static function DefaultMethodForGroup($groupID)
	{
		global $bm_prefs;

		if($groupID > 0)
		{
			$g = MfaGroupOption($groupID, 'mfa_default', '');
			if($g === 'email' || $g === 'totp')
				return $g;
		}

		return isset($bm_prefs['mfa_li_default']) && $bm_prefs['mfa_li_default'] === 'email'
			? 'email' : 'totp';
	}

	/**
	 * @param int $groupID
	 * @return bool
	 */
	public static function LiUserMayManageMfa($groupID)
	{
		global $bm_prefs;

		if($bm_prefs['mfa_li_enable'] != 'yes')
			return false;
		if($bm_prefs['mfa_li_user_setup'] != 'yes')
			return false;
		if($groupID > 0 && MfaGroupOption($groupID, 'mfa_allow_setup', 'yes') != 'yes')
			return false;

		return true;
	}

	public static function SetSetupRequiredSession($required = true)
	{
		if($required)
			$_SESSION['bm_mfaSetupRequired'] = true;
		else
			unset($_SESSION['bm_mfaSetupRequired']);
	}

	/**
	 * @param int $userID
	 * @param int $groupID
	 */
	public static function SyncSetupRequiredSession($userID, $groupID = 0)
	{
		$userID = (int)$userID;
		$groupID = (int)$groupID;

		self::ClearObsoleteSetupRequired('user', $userID, $groupID);

		if(self::NeedsSetupWizard('user', $userID, $groupID))
			self::SetSetupRequiredSession(true);
		else
			self::SetSetupRequiredSession(false);
	}

	/**
	 * User-initiated MFA reset → setup wizard (modal on start.php).
	 *
	 * @param int $userID
	 * @param int $groupID
	 */
	public static function PrepareUserSelfServiceReset($userID, $groupID = 0)
	{
		$userID = (int)$userID;
		$groupID = (int)$groupID;

		self::ResetAccount('user', $userID, 'full', array('setup_required' => true));

		unset(
			$_SESSION['bm_mfaSetupSecret'],
			$_SESSION['bm_mfaSetupEmailSent'],
			$_SESSION['bm_mfaSetupStep'],
			$_SESSION['bm_mfaPrefsSecret'],
			$_SESSION['bm_mfaPrefsMethod'],
			$_SESSION['bm_mfaPrefsSwitch'],
			$_SESSION['bm_mfaPrefsEmailSent']
		);
		unset($_SESSION['bm_mfaSetupMethod']);

		self::ResolveSetupMethod($userID, $groupID, 'bm_mfaSetupMethod');
		self::SetSetupRequiredSession(true);
	}

	/**
	 * @param int $groupID
	 * @return bool
	 */
	public static function AllowUserRequest($groupID = 0)
	{
		$userID = isset($_SESSION['bm_userID']) ? (int)$_SESSION['bm_userID'] : 0;
		if($userID > 0)
			self::SyncSetupRequiredSession($userID, $groupID);

		if(empty($_SESSION['bm_mfaSetupRequired']))
			return true;

		$script = RouteRequestScriptBasename();
		$action = isset($_REQUEST['action']) ? RouteRestoreLegacyAction($_REQUEST['action']) : '';

		$sessionApi = array('sessionStatus', 'sessionUnlock', 'sessionKeepAlive', 'sessionLock', 'sessionLockNow');
		if($script === 'start.php' && ($action === 'mfaSetup' || in_array($action, $sessionApi, true)))
			return true;
		if($script === 'index.php' && isset($_REQUEST['do']) && $_REQUEST['do'] === 'logout')
			return true;

		return false;
	}

	/**
	 * @param int    $userID
	 * @param string $passwordPlain
	 * @return bool
	 */
	public static function VerifyUserPassword($userID, $passwordPlain)
	{
		$res = $GLOBALS['db']->Query('SELECT passwort,passwort_salt FROM {pre}users WHERE id=?', (int)$userID);
		if($res->RowCount() != 1)
		{
			$res->Free();
			return false;
		}
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return BMUser::VerifyPassword($passwordPlain, $row);
	}

	/**
	 * @param int    $userID
	 * @param array  $row       users row (gesperrt, gruppe, email, ...)
	 * @param string $passwordPlain
	 * @return bool  true if login deferred for MFA verify
	 */
	public static function DeferUserLoginForMfa($userID, $row, $passwordPlain)
	{
		global $bm_prefs;

		if($bm_prefs['mfa_li_enable'] != 'yes')
			return false;

		$groupID = (int)$row['gruppe'];
		$account = self::GetAccount('user', $userID);

		if(!self::RequiresMfaVerifyAtLogin($account))
		{
			self::ClearPendingIfVerifyNotRequired('user', $userID);
			return false;
		}

		@session_start();
		self::ClearUserLoginSession();
		SessionRegenerateOnLogin();

		$meta = array(
			'email'          => $row['email'],
			'group_id'       => $groupID,
			'password_plain' => $passwordPlain,
			'recovery'       => $account['recovery_mode'] === 'altmail',
		);

		self::BeginPending('user', $userID, $meta);

		if($account['recovery_mode'] === 'altmail')
		{
			$alt = self::RecoveryEmailForUser($userID);
			if($alt !== false)
			{
				$acc = self::GetAccount('user', $userID);
				if(is_array($acc))
					self::SendEmailCode((int)$acc['id'], $alt, $userID, 'login_recovery');
			}
		}
		else if($account['email_enabled'] == 'yes')
		{
			$acc = self::GetAccount('user', $userID);
			$to = self::EmailAddressForUserMfa($userID);
			if($to === false)
				$to = DecodeEMail($row['email']);
			if(is_array($acc) && $to !== false && $to !== '')
				self::SendEmailCode((int)$acc['id'], $to, $userID, 'login');
		}

		return true;
	}

	/**
	 * Complete LI login after MFA verify.
	 *
	 * @return string|false redirect URL
	 */
	public static function FinalizeUserLoginFromPending()
	{
		$pending = self::GetPending();
		if($pending === false || $pending['type'] !== 'user')
			return false;

		global $db, $bm_prefs, $currentCharset, $currentLanguage;

		$userID = (int)$pending['account_id'];
		$res = $db->Query('SELECT * FROM {pre}users WHERE id=?', $userID);
		if($res->RowCount() != 1)
		{
			$res->Free();
			self::ClearPending();
			return false;
		}
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$passwordPlain = isset($pending['meta']['password_plain']) ? $pending['meta']['password_plain'] : '';

		if($bm_prefs['cookie_lock'] == 'yes')
		{
			$sessionSecret = GenerateRandomKey('sessionSecret');
			BMSecureSetCookie(
				'sessionSecret_'.substr(session_id(), 0, 16),
				$sessionSecret,
				0
			);
		}

		$_SESSION['bm_userLoggedIn']   = true;
		$_SESSION['bm_userID']         = $userID;
		$_SESSION['bm_sessionToken']   = SessionToken();
		if($passwordPlain !== '')
			$_SESSION['bm_xorCryptKey'] = BMUser::GenerateXORCryptKey($userID, $passwordPlain);
		BMUser::SyncSessionEpochToSession($userID);
		SessionInitLoginTimestamps(false);

		$account = self::GetAccount('user', $userID);
		if(is_array($account))
		{
			global $db;

			if($account['recovery_mode'] === 'altmail')
				$db->Query('UPDATE {pre}mfa_accounts SET recovery_mode=? WHERE id=?', 'no', (int)$account['id']);

			if($account['enabled'] != 'yes' || $account['setup_required'] == 'yes')
			{
				if($account['totp_secret'] != '')
					$db->Query('UPDATE {pre}mfa_accounts SET totp_enabled=? WHERE id=?', 'yes', (int)$account['id']);
				self::CompleteSetup((int)$account['id']);
				$account = self::GetAccount('user', $userID);
			}
		}

		self::ClearPending();
		unset($pending['meta']['password_plain']);

		$groupID = (int)$row['gruppe'];
		self::ClearStaleSetupRequired('user', $userID);

		if(class_exists('BMLoginNotify'))
			BMLoginNotify::OnSuccessfulLogin('user', $userID, DecodeEMail($row['email']), $groupID);

		if(self::NeedsSetupWizard('user', $userID, $groupID))
		{
			self::SetSetupRequiredSession(true);
			return SessionUrl('start.php?action=mfaSetup');
		}

		return SessionUrl('start.php');
	}

	/**
	 * @param int    $adminID
	 * @param array  $adminRow  adminid, username, password, ...
	 * @param array  $meta      timezone, jump, ...
	 * @return bool  true if login deferred for MFA verify
	 */
	public static function DeferAdminLoginForMfa($adminID, $adminRow, $meta = array())
	{
		global $bm_prefs;

		if($bm_prefs['mfa_admin_enable'] != 'yes')
			return false;

		$adminID = (int)$adminID;
		$account = self::GetAccount('admin', $adminID);
		if(!self::RequiresMfaVerifyAtLogin($account))
			return false;

		@session_start();
		SessionRegenerateOnLogin();

		$meta = array_merge(array(
			'username' => $adminRow['username'],
			'password' => $adminRow['password'],
			'timezone' => date('Z'),
			'jump'     => '',
		), $meta);

		self::BeginPending('admin', $adminID, $meta);

		if(is_array($account) && $account['email_enabled'] == 'yes')
		{
			$to = self::EmailAddressForAdminMfa($adminID);
			if($to !== false && $to !== '')
				self::SendEmailCode((int)$account['id'], $to, -1, 'admin_login');
		}

		return true;
	}

	/**
	 * Complete ACP login after MFA verify.
	 *
	 * @return string|false redirect URL
	 */
	public static function FinalizeAdminLoginFromPending()
	{
		$pending = self::GetPending();
		if($pending === false || $pending['type'] !== 'admin')
			return false;

		global $db;

		$adminID = (int)$pending['account_id'];
		$res = $db->Query('SELECT `adminid`,`username`,`password` FROM {pre}admins WHERE `adminid`=?', $adminID);
		if($res->RowCount() != 1)
		{
			$res->Free();
			self::ClearPending();
			return false;
		}
		$adminUserRow = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$passwordHash = isset($pending['meta']['password']) ? $pending['meta']['password'] : $adminUserRow['password'];
		$timezone = isset($pending['meta']['timezone']) ? (int)$pending['meta']['timezone'] : date('Z');
		$jump = isset($pending['meta']['jump']) ? trim((string)$pending['meta']['jump']) : '';

		@session_start();
		SessionRegenerateOnLogin();
		$sessionID = session_id();
		$_SESSION['bm_adminLoggedIn']	= true;
		$_SESSION['bm_adminID']			= $adminID;
		$_SESSION['bm_adminAuth']		= AdminSessionAuthBind($passwordHash, $adminID);
		$_SESSION['bm_sessionToken']	= SessionToken();
		$_SESSION['bm_timezone']		= $timezone;
		SessionInitLoginTimestamps(true);
		$_SESSION['adminsessionSecret'] = GenerateRandomKey('sessionSecret');
		BMSecureSetCookie(
			'bm_admin_sessionSecret_'.substr($sessionID, 0, 16),
			$_SESSION['adminsessionSecret'],
			0
		);

		$account = self::GetAccount('admin', $adminID);
		if(is_array($account))
		{
			if($account['enabled'] != 'yes' || $account['setup_required'] == 'yes')
			{
				if($account['totp_secret'] != '')
					$db->Query('UPDATE {pre}mfa_accounts SET totp_enabled=? WHERE id=?', 'yes', (int)$account['id']);
				self::CompleteSetup((int)$account['id']);
			}
		}

		self::ClearPending();

		PutLog(sprintf('Admin <%s> logged in from <%s>',
			$adminUserRow['username'],
			$_SERVER['REMOTE_ADDR']),
			PRIO_NOTE,
			__FILE__,
			__LINE__);

		if(class_exists('BMLoginNotify'))
			BMLoginNotify::OnSuccessfulLogin('admin', $adminID, '', 0);

		if(self::NeedsSetupWizard('admin', $adminID, 0))
			return SessionUrl('admins.php?action=account');

		$jump = AdminLoginJumpTarget($jump);

		return SessionUrl($jump);
	}

	/**
	 * Resend e-mail MFA code for pending login (user or admin).
	 *
	 * @return bool
	 */
	public static function ResendPendingEmailCode()
	{
		$pending = self::GetPending();
		if($pending === false)
			return false;

		$row = self::GetAccount($pending['type'], $pending['account_id']);
		if(!is_array($row) || $row['email_enabled'] != 'yes')
			return false;

		if($pending['type'] === 'admin')
			$to = self::EmailAddressForAdminMfa((int)$pending['account_id']);
		else
		{
			$to = !empty($pending['meta']['recovery'])
				? self::RecoveryEmailForUser((int)$pending['account_id'])
				: self::EmailAddressForUserMfa((int)$pending['account_id']);
			if($to === false && isset($pending['meta']['email']))
				$to = DecodeEMail($pending['meta']['email']);
		}

		if($to === false || $to === '')
			return false;

		return self::SendEmailCode(
			(int)$row['id'],
			$to,
			$pending['type'] === 'user' ? (int)$pending['account_id'] : -1,
			'login_resend') !== false;
	}

	/**
	 * @param int    $userID
	 * @param int    $groupID
	 * @param string $method totp|email
	 * @param string $totpSecret
	 * @return int|false mfa_account id
	 */
	public static function EnsureUserAccount($userID, $groupID, $method = 'totp', $totpSecret = '')
	{
		global $db;

		$row = self::GetAccount('user', $userID);
		if(is_array($row))
			return (int)$row['id'];

		$db->Query('INSERT INTO {pre}mfa_accounts(account_type,account_id,method,totp_secret,email_enabled,totp_enabled,enabled,setup_required,recovery_mode,created) VALUES(?,?,?,?,?,?,?,?,?,?)',
			'user',
			(int)$userID,
			$method === 'email' ? 'email' : 'totp',
			$totpSecret,
			$method === 'email' ? 'yes' : 'no',
			($method === 'totp' && $totpSecret !== '') ? 'yes' : 'no',
			'no',
			'yes',
			'no',
			time());

		return (int)$db->InsertId();
	}

	/**
	 * @param int $mfaAccountID
	 */
	public static function CompleteSetup($mfaAccountID)
	{
		global $db;

		$db->Query('UPDATE {pre}mfa_accounts SET setup_required=?,enabled=?,enabled_at=? WHERE id=?',
			'no',
			'yes',
			time(),
			(int)$mfaAccountID);
		self::SetSetupRequiredSession(false);
	}

	/**
	 * @param array $row mfa_accounts row
	 * @return int
	 */
	public static function GetEnabledAtTimestamp($row)
	{
		if(!is_array($row) || $row['enabled'] != 'yes')
			return 0;

		if(isset($row['enabled_at']) && (int)$row['enabled_at'] > 0)
			return (int)$row['enabled_at'];

		return isset($row['created']) ? (int)$row['created'] : 0;
	}

	/**
	 * @param array $userIDs
	 * @return array<int, array> account_id => row
	 */
	public static function GetAccountsForUsers($userIDs)
	{
		return self::GetAccountsForIds('user', $userIDs);
	}

	/**
	 * @param array $adminIDs
	 * @return array<int, array>
	 */
	public static function GetAccountsForAdmins($adminIDs)
	{
		return self::GetAccountsForIds('admin', $adminIDs);
	}

	/**
	 * @param string $accountType user|admin
	 * @param array  $accountIDs
	 * @return array<int, array>
	 */
	private static function GetAccountsForIds($accountType, $accountIDs)
	{
		global $db;

		$ids = array();
		foreach($accountIDs as $id)
		{
			$id = (int)$id;
			if($id > 0)
				$ids[$id] = $id;
		}
		if(count($ids) === 0)
			return array();

		$res = $db->Query('SELECT * FROM {pre}mfa_accounts WHERE account_type=? AND account_id IN('
			. implode(',', $ids) . ')',
			$accountType);
		$map = array();
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$map[(int)$row['account_id']] = $row;
		$res->Free();

		return $map;
	}

	/**
	 * @return bool
	 */
	public static function AdminMayManageMfa()
	{
		global $bm_prefs;

		return isset($bm_prefs['mfa_admin_enable']) && $bm_prefs['mfa_admin_enable'] == 'yes'
			&& isset($bm_prefs['mfa_admin_user_setup']) && $bm_prefs['mfa_admin_user_setup'] == 'yes';
	}

	/**
	 * @return string totp|email
	 */
	public static function DefaultMethodForAdmin()
	{
		global $bm_prefs;

		return isset($bm_prefs['mfa_admin_default']) && $bm_prefs['mfa_admin_default'] === 'email'
			? 'email' : 'totp';
	}

	/**
	 * @return bool
	 */
	public static function AdminTableHasEmailColumn()
	{
		global $db;

		static $has = null;
		if($has !== null)
			return $has;

		$res = $db->Query('SHOW COLUMNS FROM {pre}admins LIKE ?', 'email');
		$has = $res->RowCount() > 0;
		$res->Free();

		return $has;
	}

	/**
	 * @param int $adminID
	 * @return string|false
	 */
	public static function EmailAddressForAdminMfa($adminID)
	{
		global $db;

		$adminID = (int)$adminID;
		if($adminID <= 0 || !self::AdminTableHasEmailColumn())
			return false;

		if(function_exists('EnsureAdminEmailColumn'))
			EnsureAdminEmailColumn();

		$res = $db->Query('SELECT `email` FROM {pre}admins WHERE `adminid`=?', $adminID);
		if($res->RowCount() != 1)
		{
			$res->Free();
			return false;
		}

		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$email = trim((string)($row['email'] ?? ''));
		if($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false)
			return false;

		return $email;
	}

	/**
	 * @param int $adminID
	 * @return bool
	 */
	public static function AdminCanUseEmailMfa($adminID)
	{
		return self::EmailAddressForAdminMfa((int)$adminID) !== false;
	}

	/**
	 * @param int    $adminID
	 * @param string $passwordPlain
	 * @return bool
	 */
	public static function VerifyAdminPassword($adminID, $passwordPlain)
	{
		global $db;

		$adminID = (int)$adminID;
		$passwordPlain = trim((string)$passwordPlain);
		if($adminID <= 0 || $passwordPlain === '')
			return false;

		$res = $db->Query('SELECT password,password_salt FROM {pre}admins WHERE adminid=?', $adminID);
		if($res->RowCount() != 1)
		{
			$res->Free();
			return false;
		}
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		if(BMUser::PasswordIsModern($row['password']))
			return password_verify($passwordPlain, $row['password']);

		return md5($passwordPlain . $row['password_salt']) === $row['password'];
	}

	/**
	 * @param int    $adminID
	 * @param string $method totp|email
	 * @param string $totpSecret
	 * @return int|false
	 */
	public static function EnsureAdminAccount($adminID, $method = 'totp', $totpSecret = '')
	{
		global $db;

		$adminID = (int)$adminID;
		$row = self::GetAccount('admin', $adminID);
		if(is_array($row))
			return (int)$row['id'];

		$method = ($method === 'email') ? 'email' : 'totp';
		$db->Query('INSERT INTO {pre}mfa_accounts(account_type,account_id,method,totp_secret,email_enabled,totp_enabled,enabled,setup_required,recovery_mode,created) VALUES(?,?,?,?,?,?,?,?,?,?)',
			'admin',
			$adminID,
			$method,
			$totpSecret,
			$method === 'email' ? 'yes' : 'no',
			($method === 'totp' && $totpSecret !== '') ? 'yes' : 'no',
			'no',
			'yes',
			'no',
			time());

		return (int)$db->InsertId();
	}

	/**
	 * @param int $adminID
	 * @return bool
	 */
	public static function SendSetupEmailCodeForAdmin($adminID, $context = 'admin_setup')
	{
		$adminID = (int)$adminID;
		$to = self::EmailAddressForAdminMfa($adminID);
		if($to === false)
		{
			self::LogEmailCodeEvent($context, 0, '', 'no_recipient', -1);
			return false;
		}

		$mfaID = self::EnsureAdminAccount($adminID, 'email', '');
		global $db;
		$db->Query('UPDATE {pre}mfa_accounts SET method=?,totp_secret=?,totp_enabled=?,email_enabled=?,enabled=?,setup_required=? WHERE id=?',
			'email',
			'',
			'no',
			'no',
			'no',
			'yes',
			(int)$mfaID);

		return self::SendEmailCode((int)$mfaID, $to, -1, $context) !== false;
	}

	/**
	 * @param int    $adminID
	 * @param string $sessionKey
	 * @return string totp|email
	 */
	public static function ResolveSetupMethodForAdmin($adminID, $sessionKey = 'bm_adminMfaPrefsMethod')
	{
		$adminID = (int)$adminID;
		if(isset($_SESSION[$sessionKey])
			&& ($_SESSION[$sessionKey] === 'email' || $_SESSION[$sessionKey] === 'totp'))
			$method = $_SESSION[$sessionKey];
		else
			$method = self::DefaultMethodForAdmin();

		if($method === 'email' && !self::AdminCanUseEmailMfa($adminID))
			$method = 'totp';

		$_SESSION[$sessionKey] = $method;

		return $method;
	}

	/**
	 * @param string $method
	 * @param int    $adminID
	 * @param string $sessionKey
	 */
	public static function SetSetupMethodForAdmin($method, $adminID, $sessionKey = 'bm_adminMfaPrefsMethod')
	{
		$method = ($method === 'email') ? 'email' : 'totp';
		if($method === 'email' && !self::AdminCanUseEmailMfa((int)$adminID))
			$method = 'totp';

		$_SESSION[$sessionKey] = $method;
		unset($_SESSION['bm_adminMfaPrefsSecret'], $_SESSION['bm_adminMfaPrefsEmailSent']);
		self::ResetIncompleteSetup('admin', (int)$adminID, $method);
	}

	/**
	 * MFA list column for ACP user overview (icons + tooltip).
	 *
	 * @param int        $userID
	 * @param int        $groupID
	 * @param array|false $row
	 * @param bool       $adminLang use $lang_admin
	 * @return array{status:string,method:string,title:string}
	 */
	public static function ListStatusForUser($userID, $groupID, $row = null, $adminLang = true)
	{
		global $bm_prefs;

		$lang = $adminLang ? $GLOBALS['lang_admin'] : $GLOBALS['lang_user'];
		$tr = function($key, $fallback) use ($lang) {
			return isset($lang[$key]) ? $lang[$key] : $fallback;
		};

		if($bm_prefs['mfa_li_enable'] != 'yes')
			return array('status' => 'off', 'method' => '', 'title' => '');

		$userID = (int)$userID;
		$groupID = (int)$groupID;
		if($row === null)
			$row = self::GetAccount('user', $userID);

		if(self::IsLoginReady('user', $userID))
		{
			$method = is_array($row) ? self::ActiveMethod($row) : 'totp';
			$methodLabel = $method === 'email'
				? $tr('mfa_list_active_email', 'E-Mail')
				: $tr('mfa_list_active_totp', 'TOTP');
			$title = $tr('mfa_list_active', 'MFA active') . ' (' . $methodLabel . ')';
			$ts = is_array($row) ? self::GetEnabledAtTimestamp($row) : 0;
			if($ts > 0)
				$title .= ' — ' . sprintf($tr('mfa_active_since', 'since %s'), FormatDate($ts));

			return array('status' => 'active', 'method' => $method, 'title' => $title);
		}

		if(self::NeedsSetupWizard('user', $userID, $groupID))
		{
			$mandatory = self::IsMandatoryForAccount('user', $userID, $groupID);

			return array(
				'status' => $mandatory ? 'pending' : 'setup',
				'method' => '',
				'title'  => $mandatory
					? $tr('mfa_list_pending', 'Required — not configured')
					: $tr('mfa_list_setup', 'Setup incomplete'),
			);
		}

		if(is_array($row) && self::RequiresMfaVerifyAtLogin($row))
			return array('status' => 'partial', 'method' => '', 'title' => $tr('mfa_list_partial', 'Incomplete configuration'));

		return array('status' => 'none', 'method' => '', 'title' => $tr('mfa_list_none', 'MFA not active'));
	}

	/**
	 * MFA list column for ACP admin overview.
	 *
	 * @param int         $adminID
	 * @param array|false $row
	 * @return array{status:string,method:string,title:string}
	 */
	public static function ListStatusForAdmin($adminID, $row = null)
	{
		global $bm_prefs;

		$lang = $GLOBALS['lang_admin'];
		$tr = function($key, $fallback) use ($lang) {
			return isset($lang[$key]) ? $lang[$key] : $fallback;
		};

		if($bm_prefs['mfa_admin_enable'] != 'yes')
			return array('status' => 'off', 'method' => '', 'title' => '');

		$adminID = (int)$adminID;
		if($row === null)
			$row = self::GetAccount('admin', $adminID);

		if(self::IsLoginReady('admin', $adminID))
		{
			$method = is_array($row) ? self::ActiveMethod($row) : 'totp';
			$methodLabel = $method === 'email'
				? $tr('mfa_list_active_email', 'E-Mail')
				: $tr('mfa_list_active_totp', 'TOTP');
			$title = $tr('mfa_list_active', 'MFA active') . ' (' . $methodLabel . ')';
			$ts = is_array($row) ? self::GetEnabledAtTimestamp($row) : 0;
			if($ts > 0)
				$title .= ' — ' . sprintf($tr('mfa_active_since', 'since %s'), FormatDate($ts));

			return array('status' => 'active', 'method' => $method, 'title' => $title);
		}

		if(self::NeedsSetupWizard('admin', $adminID, 0))
		{
			$mandatory = self::IsMandatoryForAccount('admin', $adminID, 0);

			return array(
				'status' => $mandatory ? 'pending' : 'setup',
				'method' => '',
				'title'  => $mandatory
					? $tr('mfa_list_pending', 'Required — not configured')
					: $tr('mfa_list_setup', 'Setup incomplete'),
			);
		}

		if(is_array($row) && self::RequiresMfaVerifyAtLogin($row))
			return array('status' => 'partial', 'method' => '', 'title' => $tr('mfa_list_partial', 'Incomplete configuration'));

		return array('status' => 'none', 'method' => '', 'title' => $tr('mfa_list_none', 'MFA not active'));
	}

	private static function RandomDigits($len)
	{
		$out = '';
		for($i = 0; $i < $len; $i++)
			$out .= (string)random_int(0, 9);
		return $out;
	}

	private static function Base32Decode($secret)
	{
		$secret = strtoupper(preg_replace('/[^A-Z2-7]/', '', $secret));
		$alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
		$buffer = 0;
		$bitsLeft = 0;
		$output = '';

		for($i = 0, $len = strlen($secret); $i < $len; $i++)
		{
			$val = strpos($alphabet, $secret[$i]);
			if($val === false)
				continue;
			$buffer = ($buffer << 5) | $val;
			$bitsLeft += 5;
			if($bitsLeft >= 8)
			{
				$bitsLeft -= 8;
				$output .= chr(($buffer >> $bitsLeft) & 0xFF);
			}
		}

		return $output;
	}
}
