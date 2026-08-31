<?php
/*
 * b1gMail login notifications (new IP) — no external services
 */

/**
 * @return bool
 */
function EnsureAdminEmailColumn()
{
	global $db;

	$res = $db->Query('SHOW COLUMNS FROM {pre}admins LIKE ?', 'email');
	$exists = $res->RowCount() > 0;
	$res->Free();

	if(!$exists)
		$db->Query('ALTER TABLE {pre}admins ADD COLUMN `email` varchar(255) NOT NULL DEFAULT \'\' AFTER `lastname`');

	return true;
}

/**
 * E-Mail address for admin login notifications.
 *
 * @param int $adminID
 * @return string|false
 */
function AdminLoginNotifyEmail($adminID)
{
	global $db;

	EnsureAdminEmailColumn();

	$res = $db->Query('SELECT `email` FROM {pre}admins WHERE `adminid`=?', (int)$adminID);
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

function EnsureLoginNotifySchema()
{
	global $db;

	EnsureAdminEmailColumn();

	$db->Query('CREATE TABLE IF NOT EXISTS {pre}known_logins (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`account_type` enum(\'user\',\'admin\') NOT NULL,
		`account_id` int(11) NOT NULL,
		`ip` varchar(45) NOT NULL,
		`ua_hash` char(64) NOT NULL,
		`first_seen` int(11) NOT NULL DEFAULT 0,
		`last_seen` int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY (`id`),
		UNIQUE KEY `login_ip` (`account_type`,`account_id`,`ip`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

	LoginNotifyMigrateKnownLoginsIndex();

	$res = $db->Query('SHOW COLUMNS FROM {pre}users LIKE ?', 'notify_login_new_ip');
	$exists = $res->RowCount() > 0;
	$res->Free();
	if(!$exists)
		$db->Query('ALTER TABLE {pre}users ADD COLUMN `notify_login_new_ip` enum(\'yes\',\'no\') NOT NULL DEFAULT \'yes\' AFTER `notify_birthday`');

	return true;
}

/**
 * One known-login row per account + IP (legacy index included user-agent).
 */
function LoginNotifyMigrateKnownLoginsIndex()
{
	global $db;

	$res = $db->Query("SHOW INDEX FROM {pre}known_logins WHERE Key_name='login_key'");
	$hasLegacyIndex = $res->RowCount() > 0;
	$res->Free();

	if(!$hasLegacyIndex)
		return;

	$db->Query('DELETE t1 FROM {pre}known_logins t1 INNER JOIN {pre}known_logins t2 ON t1.account_type=t2.account_type AND t1.account_id=t2.account_id AND t1.ip=t2.ip AND t1.id < t2.id');
	$db->Query('ALTER TABLE {pre}known_logins DROP INDEX `login_key`');

	$res = $db->Query("SHOW INDEX FROM {pre}known_logins WHERE Key_name='login_ip'");
	$hasNewIndex = $res->RowCount() > 0;
	$res->Free();

	if(!$hasNewIndex)
		$db->Query('ALTER TABLE {pre}known_logins ADD UNIQUE KEY `login_ip` (`account_type`,`account_id`,`ip`)');
}

/**
 * Subject line for login notification mails (user language or system default).
 *
 * @param string $accountType user|admin
 * @param int    $accountID
 * @return string
 */
function LoginNotifyMailSubject($accountType, $accountID)
{
	global $bm_prefs;

	$phrase = 'login_notify_sub';

	if($accountType === 'user' && (int)$accountID > 0)
		$subject = GetPhraseForUser((int)$accountID, 'lang_custom', $phrase);
	else
		$subject = GetPhraseForLanguage(isset($bm_prefs['language']) ? $bm_prefs['language'] : 'deutsch', 'lang_custom', $phrase);

	if(!is_string($subject) || $subject === '' || strpos($subject, '#UNKNOWN_PHRASE') === 0)
	{
		$subject = GetPhraseForLanguage('deutsch', 'lang_custom', $phrase);
		if(!is_string($subject) || $subject === '' || strpos($subject, '#UNKNOWN_PHRASE') === 0)
			$subject = 'Anmeldung von neuer IP-Adresse';
	}

	return $subject;
}

class BMLoginNotify
{
	/** @var array<string, bool> */
	private static $handledThisRequest = array();

	/**
	 * @param string $email
	 * @return string
	 */
	private static function NormalizeNotifyEmail($email)
	{
		if(!is_string($email) || trim($email) === '')
			return '';

		$email = ExtractMailAddress(DecodeEMail(trim($email)));
		if($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false)
			return '';

		return strtolower($email);
	}

	/**
	 * Parse User-Agent without external library.
	 *
	 * @param string|null $ua
	 * @return array browser, os, language
	 */
	public static function ParseClient($ua = null)
	{
		if($ua === null)
			$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

		$browser = 'Unknown';
		$os = 'Unknown';

		if(preg_match('/Edg\/([0-9.]+)/', $ua, $m))
			$browser = 'Microsoft Edge ' . $m[1];
		else if(preg_match('/Chrome\/([0-9.]+)/', $ua, $m))
			$browser = 'Chrome ' . $m[1];
		else if(preg_match('/Firefox\/([0-9.]+)/', $ua, $m))
			$browser = 'Firefox ' . $m[1];
		else if(preg_match('/Version\/([0-9.]+).*Safari/', $ua, $m))
			$browser = 'Safari ' . $m[1];

		if(preg_match('/Windows NT 10/', $ua))
			$os = 'Windows 10/11';
		else if(preg_match('/Windows NT 6\.3/', $ua))
			$os = 'Windows 8.1';
		else if(preg_match('/Windows NT 6\.1/', $ua))
			$os = 'Windows 7';
		else if(preg_match('/Mac OS X ([0-9_]+)/', $ua, $m))
			$os = 'macOS ' . str_replace('_', '.', $m[1]);
		else if(preg_match('/Android ([0-9.]+)/', $ua, $m))
			$os = 'Android ' . $m[1];
		else if(preg_match('/iPhone OS ([0-9_]+)/', $ua, $m))
			$os = 'iOS ' . str_replace('_', '.', $m[1]);
		else if(stripos($ua, 'Linux') !== false)
			$os = 'Linux';

		$lang = '';
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$parts = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$lang = trim(explode(';', $parts[0])[0]);
		}

		return array(
			'browser'  => $browser,
			'os'       => $os,
			'language' => $lang,
			'raw'      => substr($ua, 0, 512),
		);
	}

	/**
	 * @param string $accountType user|admin
	 * @param int    $accountID
	 * @param int    $groupID     LI only
	 * @return bool
	 */
	public static function IsEnabled($accountType, $accountID, $groupID = 0)
	{
		global $bm_prefs;

		if($accountType === 'admin')
			return isset($bm_prefs['login_notify_admin']) && $bm_prefs['login_notify_admin'] == 'yes';

		if(!isset($bm_prefs['login_notify_li']) || $bm_prefs['login_notify_li'] != 'yes')
			return false;

		if($groupID > 0 && MfaGroupOption($groupID, 'login_notify', 'no') != 'yes')
			return false;

		if((int)$accountID > 0 && !self::UserWantsNotify((int)$accountID))
			return false;

		return true;
	}

	/**
	 * @param int $userID
	 * @return bool
	 */
	public static function UserWantsNotify($userID)
	{
		global $db;

		EnsureLoginNotifySchema();

		$res = $db->Query('SELECT notify_login_new_ip FROM {pre}users WHERE id=?', (int)$userID);
		if($res->RowCount() != 1)
		{
			$res->Free();
			return true;
		}

		list($pref) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return $pref === 'yes';
	}

	/**
	 * @param string $accountType
	 * @param int    $accountID
	 * @param string $primaryEmail
	 * @return array
	 */
	public static function NotifyRecipients($accountType, $accountID, $primaryEmail)
	{
		$seen = array();
		$recipients = array();

		$primaryNorm = self::NormalizeNotifyEmail($primaryEmail);
		if($primaryNorm !== '')
		{
			$seen[$primaryNorm] = true;
			$recipients[] = $primaryNorm;
		}

		if($accountType === 'user' && (int)$accountID > 0 && class_exists('BMMfa', false))
		{
			$altMail = BMMfa::RecoveryEmailForUser((int)$accountID);
			$altNorm = self::NormalizeNotifyEmail($altMail !== false ? $altMail : '');
			if($altNorm !== '' && !isset($seen[$altNorm]))
				$recipients[] = $altNorm;
		}

		return $recipients;
	}

	/**
	 * Record login; send mail if IP is new for this account.
	 *
	 * @param string $accountType
	 * @param int    $accountID
	 * @param string $email
	 * @param int    $groupID
	 * @return bool true if notification was sent
	 */
	public static function OnSuccessfulLogin($accountType, $accountID, $email, $groupID = 0)
	{
		if(!self::IsEnabled($accountType, $accountID, $groupID))
			return false;

		$accountID = (int)$accountID;
		$requestKey = $accountType . ':' . $accountID;
		if(isset(self::$handledThisRequest[$requestKey]))
			return false;

		global $db, $bm_prefs;

		EnsureLoginNotifySchema();

		$ip = function_exists('SessionClientIp') ? SessionClientIp() : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
		$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$uaHash = hash('sha256', $ua);
		$now = time();

		self::$handledThisRequest[$requestKey] = true;

		$res = $db->Query('SELECT id FROM {pre}known_logins WHERE account_type=? AND account_id=? AND ip=? LIMIT 1',
			$accountType, $accountID, $ip);
		$known = $res->RowCount() > 0;
		$res->Free();

		if($known)
		{
			$db->Query('UPDATE {pre}known_logins SET last_seen=?, ua_hash=? WHERE account_type=? AND account_id=? AND ip=?',
				$now, $uaHash, $accountType, $accountID, $ip);
			return false;
		}

		$db->Query('INSERT INTO {pre}known_logins(account_type,account_id,ip,ua_hash,first_seen,last_seen) VALUES(?,?,?,?,?,?)',
			$accountType, $accountID, $ip, $uaHash, $now, $now);

		if($accountType === 'admin')
		{
			$notifyEmail = AdminLoginNotifyEmail((int)$accountID);
			if($notifyEmail === false)
				return false;
			$email = $notifyEmail;
		}

		$recipients = self::NotifyRecipients($accountType, (int)$accountID, $email);
		if(count($recipients) === 0)
			return false;

		$client = self::ParseClient($ua);
		$hostname = $ip;
		if($ip !== '' && filter_var($ip, FILTER_VALIDATE_IP))
		{
			$resolved = @gethostbyaddr($ip);
			if($resolved && $resolved !== $ip)
				$hostname = $resolved . ' (' . $ip . ')';
		}

		$forUser = ($accountType === 'user') ? (int)$accountID : -1;
		$subject = LoginNotifyMailSubject($accountType, (int)$accountID);
		$template = 'login_notify_new_ip';

		$vars = array(
			'time'       => $forUser > 0 ? FormatDate($now) : date('r'),
			'ip'         => $ip,
			'hostname'   => $hostname,
			'browser'    => $client['browser'],
			'os'         => $client['os'],
			'language'   => $client['language'],
			'user_agent' => $client['raw'],
			'account'    => ExtractMailAddress(DecodeEMail($email)),
		);

		$sent = false;
		foreach($recipients as $recipient)
		{
			if(SystemMail(
				$bm_prefs['passmail_abs'],
				$recipient,
				$subject,
				$template,
				$vars,
				$forUser
			))
				$sent = true;
		}

		return $sent;
	}

	/**
	 * Remove stored login IPs / user-agents for an account (e.g. after password reset).
	 *
	 * @param string $accountType user|admin
	 * @param int    $accountID
	 */
	public static function ClearKnownLogins($accountType, $accountID)
	{
		global $db;

		$accountID = (int)$accountID;
		if($accountID <= 0 || ($accountType !== 'user' && $accountType !== 'admin'))
			return;

		EnsureLoginNotifySchema();

		$db->Query('DELETE FROM {pre}known_logins WHERE account_type=? AND account_id=?',
			$accountType,
			$accountID);
	}
}
