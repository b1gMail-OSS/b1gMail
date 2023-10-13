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
 * Prepare to start page output using compression (if enabled)
 */
function StartPageOutput()
{
	global $bm_prefs;
	if(!ADMIN_MODE && $bm_prefs['compress_pages'] == 'yes' && function_exists('ob_gzhandler') && !in_array('ob_gzhandler', ob_list_handlers()))
		@ob_start('ob_gzhandler');
}

/**
 * Decode email address (with a possibly IDN-encoded) domain to human-readable form.
 *
 * @param string $email
 * @return string
 */
function DecodeSingleEMail($email)
{
	if(strpos($email, '@') !== false)
	{
		list($localPart, $domainPart) = explode('@', $email);
		$email = $localPart . '@' . DecodeDomain($domainPart);
	}
	return $email;
}

/**
 * Decode all possibly IDN-encoded email addresses in a string to human-readable form.
 *
 * @param string $email
 * @return string
 */
function DecodeEMail($email)
{
	$addresses = ExtractMailAddresses($email);
	foreach($addresses as $address)
	{
		$email = str_replace($address, DecodeSingleEMail($address), $email);
	}
	return($email);
}

/**
 * Encode an email address (with a possible non-ASCII domain) to IDN form.
 *
 * @param string $email
 * @return string
 */
function EncodeSingleEMail($email)
{
	if(strpos($email, '@') !== false)
	{
		list($localPart, $domainPart) = explode('@', $email);
		$email = $localPart . '@' . EncodeDomain($domainPart);
	}
	return $email;
}

/**
 * Encode all email addresses within a string to IDN-form.
 *
 * @param string $email
 * @return string
 */
function EncodeEMail($email)
{
	$addresses = ExtractMailAddresses($email, true);
	foreach($addresses as $address)
	{
		$email = str_replace($address, EncodeSingleEMail($address), $email);
	}
	return($email);
}

/**
 * Encode a (possible non-ASCII) domain to IDN form.
 *
 * @param string $domain
 * @return string
 */
function EncodeDomain($domain)
{
	if(IDN_SUPPORT)
	{
		$domain = CharsetDecode($domain, false, 'utf8');
		return idn_to_ascii($domain);
	}
	return $domain;
}

/**
 * Decode a (possibly IDN-encoded) domain to human-readable form.
 *
 * @param string $domain
 * @return string
 */
function DecodeDomain($domain)
{
	if(IDN_SUPPORT)
	{
		$domain = idn_to_utf8($domain);
		return CharsetDecode($domain, 'utf8');
	}
	return $domain;
}

/**
 * Post-process a cert info array as returned by openssl_x509_parse to prepare
 * it for display in a template.
 *
 * @param array $certInfo
 * @return array
 */
function postProcessCertInfo($certInfo)
{
	if(isset($certInfo['subject']))
	{
		if(isset($certInfo['subject']['OU']) && is_array($certInfo['subject']['OU']))
			$certInfo['subject']['OU'] = array_shift($certInfo['subject']['OU']);
		if(isset($certInfo['subject']['CN']) && is_array($certInfo['subject']['CN']))
			$certInfo['subject']['CN'] = array_shift($certInfo['subject']['CN']);
	}
	return $certInfo;
}

/**
 * Check if a string looks like an MD5 hash.
 *
 * @param string $in String
 * @return bool
 */
function LooksLikeMD5Hash($in)
{
	if(strlen($in) != 32)
		return false;

	if(!preg_match('/^[a-fA-F0-9]*$/', $in))
		return false;

	return true;
}

/**
 * Create a new action token.
 *
 * @param int $userID User ID of token owner
 * @param int $action Action ID
 * @param int $expires Expirity timestamp
 * @return string Token
 */
function CreateActionToken($userID, $action, $expires)
{
	global $db;

	$token = GenerateRandomKey(sprintf('user:%d,action=%d', $userID, $action));

	$db->Query('INSERT INTO {pre}actiontokens(`userid`,`token`,`action`,`created`,`expires`) VALUES(?,?,?,?,?)',
		$userID, $token, $action, time(), $expires);

	return $token;
}

/**
 * Check action token.
 *
 * @param int $userID User ID of token owner
 * @param int $action Action ID
 * @param string $token Token to check
 * @param bool $deleteIfValid If set to true, the token will be deleted if valdiation succeeds
 * @return bool true if token is valid
 */
function CheckActionToken($userID, $action, $token, $deleteIfValid = true)
{
	global $db;

	if(strlen($token) != 32)
		return false;

	$res = $db->Query('SELECT `actiontokenid` FROM {pre}actiontokens WHERE `userid`=? AND `action`=? AND `token`=? AND `expires`>?',
		$userID, $action, $token, time());
	if($res->RowCount() == 0)
		return false;
	list($actionTokenID) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();

	if($deleteIfValid)
	{
		$db->Query('DELETE FROM {pre}actiontokens WHERE `actiontokenid`=?',
			$actionTokenID);
	}

	return true;
}

/**
 * Get the age of an action token.
 *
 * @param int $userID User ID of token owner
 * @param string $token Token
 * @return int Age (in seconds)
 */
function GetActionTokenAge($userID, $token)
{
	global $db;

	if(strlen($token) != 32)
		return false;

	$res = $db->Query('SELECT `created` FROM {pre}actiontokens WHERE `userid`=? AND `token`=?',
		$userID, $token);
	if($res->RowCount() == 0)
		return false;
	list($created) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();

	if($created > time())
		return false;

	return(time() - $created);
}

/**
 * Convert a PHP shorthand size value to bytes
 *
 * @param string $size
 * @return int
 */
function ParsePHPSize($size)
{
	if(strlen($size) < 1)
		return 0;

	$lastChar = strtoupper(substr($size, -1));
	if($lastChar == 'K')
		return intval(substr($size, 0, -1) * 1024);
	if($lastChar == 'M')
		return intval(substr($size, 0, -1) * 1024 * 1024);
	if($lastChar == 'G')
		return intval(substr($size, 0, -1) * 1024 * 1024 * 1024);

	return intval($size);
}

/**
 * Create a new mail delivery status entry
 *
 * @param int $userID User ID of owner
 * @return int Delivery status ID
 */
function CreateMailDeliveryStatusEntry($userID, $recipient)
{
	global $db;

	$db->Query('INSERT INTO {pre}maildeliverystatus(`userid`,`recipient`,`created`,`updated`,`status`) VALUES(?,?,?,?,?)',
		$userID,
		$recipient,
		time(),
		time(),
		MDSTATUS_INVALID);
	return($db->InsertId());
}

/**
 * Associate delivery status entry with an outbox email
 *
 * @param int $dsIDs Delivery status ID(s)
 * @param int $outboxID ID of outbox email
 */
function SetDeliveryStatusOutboxID($dsIDs, $outboxID)
{
	global $db;

	if(!is_array($dsIDs))
		$dsIDs = array($dsIDs);

	$db->Query('UPDATE {pre}maildeliverystatus SET `outboxid`=? WHERE `deliverystatusid` IN ?',
		$outboxID,
		$dsIDs);
}

/**
 * Update delivery status
 *
 * @param int $dsIDs Delivery status ID(s)
 * @param int $status Status code
 */
function UpdateDeliveryStatus($dsIDs, $status)
{
	global $db;

	if(!is_array($dsIDs))
		$dsIDs = array($dsIDs);

	$db->Query('UPDATE {pre}maildeliverystatus SET `status`=?,`updated`=? WHERE `deliverystatusid` IN ?',
		$status,
		time(),
		$dsIDs);
}

/**
 * Check if timestamp fullfills validity rule expression
 *
 * @param int $date Timestamp
 * @param string $ruleExpression Rule expression, e.g. >= 18y
 * @return bool
 */
function CheckDateValidity($date, $ruleExpression)
{
	$rule = strtoupper(trim($ruleExpression));
	if($rule == '')
		return(true);

	$results = null;

	if(!preg_match('/([\<\>\=]+)\s*([0-9]+)\s*([DMY])/', $rule, $results) || count($results) != 4)
	{
		PutLog(sprintf('Invalid rule expression for date field: "%s"', $ruleExpression),
			PRIO_WARNING,
			__FILE__,
			__LINE__);
		return(false);
	}

	list(, $op, $num, $unit) = $results;

	if(!in_array($op, array('=', '>=', '<=', '>', '<')))
	{
		PutLog(sprintf('Invalid comparison operator "%s" in rule expression for date field: "%s"', $op, $ruleExpression),
			PRIO_WARNING,
			__FILE__,
			__LINE__);
		return(false);
	}

	$timeOffset = $num;
	switch($unit)
	{
	case 'D':
		$timeOffset *= TIME_ONE_DAY;
		break;

	case 'M':
		$timeOffset *= TIME_ONE_MONTH;
		break;

	case 'Y':
		$timeOffset *= TIME_ONE_YEAR;
		break;
	};

	return(eval(sprintf('return %d %s %d;', time() - $date, $op, $timeOffset)));
}

/**
 * Get config for an abuse type
 *
 * @param int $type Abuse type ID
 * @return array Config
 */
function GetAbuseTypePrefs($type)
{
	global $db;

	$apTypes = GetAbuseTypes();
	$result = array();

	if(!isset($apTypes[$type]) || !isset($apTypes[$type]['prefs']))
		return $result;

	foreach($apTypes[$type]['prefs'] as $key=>$info)
		$result[$key] = $info['default'];

	$res = $db->Query('SELECT `prefs` FROM {pre}abuse_points_config WHERE `type`=?',
		$type);
	while($row = $res->FetchArray(MYSQLI_ASSOC))
	{
		$prefsItems = ExplodeOutsideOfQuotation($row['prefs'], ';');

		foreach($prefsItems as $item)
		{
			$eqPos = strpos($item, '=');
			if($eqPos === false)
				continue;
			$key = trim(substr($item, 0, $eqPos));
			$value = trim(substr($item, $eqPos+1));

			$result[$key] = $value;
		}
	}
	$res->Free();

	return $result;
}

/**
 * Get abuse protect point types
 *
 * @return array
 */
function GetAbuseTypes()
{
	global $apTypes, $lang_admin, $lang_user;

	if(!isset($apTypes))
		include(B1GMAIL_DIR . 'serverlib/abuseprotect.config.php');

	return($apTypes);
}

/**
 * Add abuse point
 *
 * @param int $userID User ID
 * @param int $type Abuse point type
 * @param string $comment Comment (reason)
 * @return int ID of added point
 */
function AddAbusePoint($userID, $type, $comment)
{
	global $db, $apConfigCache, $thisUser, $groupRow;

	// activated for group?
	if(isset($thisUser) && is_object($thisUser) && $thisUser->_id == $userID)
		$userGroup = $thisUser->_row['gruppe'];
	else
	{
		$user = _new('BMUser', array($userID));
		$userGroup = $user->_row['gruppe'];
	}
	if(isset($groupRow) && is_array($groupRow) && $groupRow['id'] == $userGroup)
		$apEnabled = $groupRow['abuseprotect'] == 'yes';
	else
	{
		$group = _new('BMGroup', $userGroup);
		$apEnabled = $group->_row['abuseprotect'] == 'yes';
	}
	if(!$apEnabled)
		return(false);

	// how many points?
	if(!isset($apConfigCache))
	{
		$apConfigCache = array();
		$res = $db->Query('SELECT `type`,`points` FROM {pre}abuse_points_config');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$apConfigCache[$row['type']] = $row['points'];
		}
		$res->Free();
	}
	if(!isset($apConfigCache[$type]))
		$points = 0;
	else
		$points = $apConfigCache[$type];

	// add entry
	$db->Query('INSERT INTO {pre}abuse_points(`userid`,`date`,`type`,`points`,`comment`) VALUES(?,?,?,?,?)',
		$userID,
		time(),
		$type,
		$points,
		$comment);
	return($db->InsertId());
}

/**
 * Archive/delete log entries
 *
 * @param int $date Every log entry which is older than this timestamp is being archived/deleted
 * @param bool $saveInArchive Save logs in archive?
 * @return bool Success
 */
function ArchiveLogs($date, $saveInArchive = true, &$archivedLogEntryCount = null)
{
	global $db;

	if($saveInArchive)
	{
		$fileName = B1GMAIL_REL . 'logs/b1gMailLog-' . time() . '.log';
		if(function_exists('bzopen'))
		{
			$fp = fopen('compress.bzip2://' . $fileName . '.bz2', 'w+');
		}

		if(!isset($fp) || !$fp)
		{
			$fp = fopen($fileName, 'w+');
		}

		if(!$fp)
		{
			PutLog(sprintf('Failed to create log archive file: %s - log archiving has been aborted',
					$fileName),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

		fwrite($fp, '#' . "\n");
		fwrite($fp, '# b1gMail ' . B1GMAIL_VERSION . "\n");
		fwrite($fp, '# Log file' . "\n");
		fwrite($fp, '#' . "\n");
		fwrite($fp, '# To: ' . date('r', $date) . "\n");
		fwrite($fp, '# Generated: ' . date('r') . "\n");
		fwrite($fp, '#' . "\n");
		fwrite($fp, "\n");

		$res = $db->Query('SELECT prio,eintrag,zeitstempel FROM {pre}logs WHERE zeitstempel<'.$date.' ORDER BY id ASC');
		while($row = $res->FetchArray())
		{
			fwrite($fp, sprintf('%s [%d]: %s' . "\n",
					date('r', $row['zeitstempel']),
					$row['prio'],
					$row['eintrag']));
		}
		$res->Free();

		fclose($fp);
	}

	$db->Query('DELETE FROM {pre}logs WHERE zeitstempel<'.$date);
	$archivedLogEntryCount = $db->AffectedRows();
	return(true);
}

/**
 * check if visitor uses a mobile user agent which is compatible
 * with the b1gMail mobile interface
 *
 * @return bool
 */
function IsMobileUserAgent()
{
	$mobileUserAgents = array('iPhone', 'Android', 'webOS', 'BlackBerry', 'iPod');

	foreach($mobileUserAgents as $ua)
		if(strpos($_SERVER['HTTP_USER_AGENT'],  $ua) !== false)
			return(true);

	return(false);
}

/**
 * sanitize email addresses for sending and expand group addresses
 *
 * @param string $in Input
 * @return string
 */
function PrepareSendAddresses($in)
{
	global $thisUser;

	$out = array();
	$in = EncodeEMail(trim(str_replace(array("\r", "\t", "\n"), '', $in)));
	$addresses = ParseMailList($in);

	if(!class_exists('BMAddressbook'))
		include(B1GMAIL_DIR . 'serverlib/addressbook.class.php');
	$book = _new('BMAddressbook', array($thisUser->_id));

	$groups = array();
	foreach($addresses as $addr)
	{
		if(strlen($addr['mail']) > 15
			&& substr($addr['mail'], -15) == '@contact.groups')
		{
			$groupID = (int)array_shift(explode('@', $addr['mail']));
			$groups[] = $groupID;
			continue;
		}

		if(trim($addr['name']) != '')
			$out[] = sprintf('"%s" <%s>', $addr['name'], $addr['mail']);
		else
			$out[] = sprintf('<%s>', $addr['mail']);
	}

	if(count($groups) > 0)
	{
		$addresses = $book->GetGroupContactMails($groups);

		foreach($addresses as $contact)
		{
			$email = $contact['default_address'] == ADDRESS_WORK
							? $contact['work_email']
							: $contact['email'];

			if(trim($email) != '')
				$out[] = str_replace(array("\r", "\t", "\n"), '',
							sprintf('"%s, %s" <%s>',
								$contact['nachname'],
								$contact['vorname'],
								EncodeEMail($email)));
		}
	}

	return(implode(', ', $out));
}

/**
 * check if address is locked for use as alt mail
 *
 * @param string $addr Address
 * @return bool
 */
function AltMailLocked($addr)
{
	global $bm_prefs;

	$lamArray = explode(':', $bm_prefs['locked_altmail']);
	foreach($lamArray as $lam)
	{
		$lam = trim($lam);
		if(strlen($lam) == 0)
			continue;

		$expr = preg_quote($lam, '/');
		$expr = str_replace('\*', '.+', $expr);
		$expr = '/^'.$expr.'$/i';

		if(preg_match($expr, $addr))
			return(true);
	}

	return(false);
}

/**
 * check if recipient email address is blocked
 *
 * @param string $addr Address
 * @return bool
 */
function RecipientBlocked($addr)
{
	global $bm_prefs;

	$addr = ExtractMailAddress($addr);

	$lamArray = explode(':', $bm_prefs['blocked']);
	foreach($lamArray as $lam)
	{
		$lam = trim($lam);
		if(strlen($lam) == 0)
			continue;

		$expr = preg_quote($lam, '/');
		$expr = str_replace('\*', '.+', $expr);
		$expr = '/^'.$expr.'$/i';

		if(preg_match($expr, $addr))
			return(true);
	}

	return(false);
}

function ChangelogAdded($itemType, $itemID, $added)
{
	global $db, $userRow;

	$res = $db->Query('SELECT COUNT(*) FROM {pre}changelog WHERE `itemtype`=? AND `itemid`=?',
	$itemType, $itemID);
	list($count) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();

	if($count == 0)
	{
		$db->Query('INSERT INTO {pre}changelog(`itemtype`,`itemid`,`userid`,`created`) VALUES(?,?,?,?)',
		$itemType, $itemID, $userRow['id'], $added);
	}
	else
	{
		$db->Query('UPDATE {pre}changelog SET `created`=? WHERE `itemtype`=? AND `itemid`=?',
		$added, $itemType, $itemID);
	}
}

function ChangelogUpdated($itemType, $itemID, $updated)
{
	global $db, $userRow;

	$res = $db->Query('SELECT COUNT(*) FROM {pre}changelog WHERE `itemtype`=? AND `itemid`=?',
	$itemType, $itemID);
	list($count) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();

	if($count == 0)
	{
		$db->Query('INSERT INTO {pre}changelog(`itemtype`,`itemid`,`userid`,`updated`) VALUES(?,?,?,?)',
		$itemType, $itemID, $userRow['id'], $updated);
	}
	else
	{
		$db->Query('UPDATE {pre}changelog SET `updated`=? WHERE `itemtype`=? AND `itemid`=?',
		$updated, $itemType, $itemID);
	}
}

function ChangelogDeleted($itemType, $itemID, $deleted)
{
	global $db, $userRow;

	$res = $db->Query('SELECT COUNT(*) FROM {pre}changelog WHERE `itemtype`=? AND `itemid`=?',
	$itemType, $itemID);
	list($count) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();

	if($count == 0)
	{
		$db->Query('INSERT INTO {pre}changelog(`itemtype`,`itemid`,`userid`,`deleted`) VALUES(?,?,?,?)',
		$itemType, $itemID, $userRow['id'], $deleted);
	}
	else
	{
		$db->Query('UPDATE {pre}changelog SET `deleted`=? WHERE `itemtype`=? AND `itemid`=?',
		$deleted, $itemType, $itemID);
	}
}

/**
 * guess MIME type by extension
 *
 * @param string $fileName Filename
 * @return string
 */
function GuessMIMEType($fileName)
{
	$table = array(
		'txt'		=> 'text/plain',
		'jpe'		=> 'image/jpeg',
		'jpg'		=> 'image/jpeg',
		'jpeg'		=> 'image/jpeg',
		'gif'		=> 'image/gif',
		'png'		=> 'image/png',
		'tif'		=> 'image/tiff',
		'tiff'		=> 'image/tiff',
		'ico'		=> 'image/x-icon',
		'eml'		=> 'message/rfc822',
		'htm'		=> 'text/html',
		'html'		=> 'text/html',
		'zip'		=> 'application/zip',
		'z'			=> 'application/x-compressed',
		'gz'		=> 'application/x-compressed',
		'tgz'		=> 'application/x-compressed',
		'tar'		=> 'application/x-tar',
		'swf'		=> 'application/x-shockwave-flash',
		'xpm'		=> 'application/x-pixmap',
		'svg'		=> 'image/svg+xml',
		'vcf'		=> 'text/x-vcard',
		'sh'		=> 'application/x-sh',
		'rtf'		=> 'application/rtf',
		'ppd'		=> 'application/vnd.ms-powerpoint',
		'pps'		=> 'application/vnd.ms-powerpoint',
		'ppm'		=> 'image/x-portable-pixmap',
		'mpe'		=> 'video/mpeg',
		'mpeg'		=> 'video/mpeg',
		'mpg'		=> 'video/mpeg',
		'mpg'		=> 'video/mpeg',
		'mpa'		=> 'video/mpeg',
		'mp2'		=> 'video/mpeg',
		'mp3'		=> 'audio/mpeg',
		'wav'		=> 'audio/x-wav',
		'tex'		=> 'application/x-tex',
		'pdf'		=> 'application/pdf',
		'doc'		=> 'application/msword',
		'dot'		=> 'application/msword',
		'c'			=> 'text/plain',
		'cpp'		=> 'text/plain',
		'h'			=> 'text/plain',
		'hpp'		=> 'text/plain',
		'js'		=> 'text/javascript',
		'css'		=> 'text/css',
		'php'		=> 'text/php',
		'php3'		=> 'text/php',
		'php4'		=> 'text/php',
		'php5'		=> 'text/php'
	);

	$ext = strtolower(array_pop(explode('.', $fileName)));

	if(isset($table[$ext]))
		return($table[$ext]);
	return('application/octet-stream');
}

/**
 * XOR-encrypt a string
 *
 * @param string $str String to encrypt
 * @param string $key Key, should be at least as long as $str
 * @return string Encrypted string
 */
function XORCrypt($str, $key)
{
	if(strlen($key) < strlen($str))
		$key = str_repeat($key, ceil(strlen($str)/strlen($key)));

	for($i=0; $i<strlen($str); $i++)
		$str[$i] = chr(ord($str[$i]) ^ ord($key[$i]));

	return($str);
}

/**
 * crypt private key passphrase
 *
 * @param string $str Input
 * @return string
 */
function CryptPKPassPhrase($str)
{
	return(XORCrypt($str, B1GMAIL_SIGNKEY));
}

/**
 * format a string for html output
 *
 * @param string $in
 * @return string
 */
function HTMLFormat($in, $allowDoubleEncoding = false, $allowEncodingRepair = true)
{
	global $currentCharset;

	$res = @htmlspecialchars($in, ENT_COMPAT, $currentCharset, $allowDoubleEncoding);

	if($allowEncodingRepair && strlen($in) > 0 && strlen($res) == 0 && function_exists('mb_detect_encoding'))
	{
		$fromEncoding = mb_detect_encoding($in); //FIXME: Better detection of invalid Encoding
		if($fromEncoding === FALSE) $fromEncoding='UTF-8'; // Uncaught ValueError: mb_convert_encoding(): Argument #3 ($from_encoding) must specify at least one encoding
		$in = @mb_convert_encoding($in, $currentCharset, $fromEncoding);
		return(HTMLFormat($in, $allowDoubleEncoding, false));
	}

	return($res);
}

/**
 * generate mail trust token
 *
 * @param string $messageID
 * @param string $from
 * @param string $to
 * @param string $subject
 * @return string
 */
function GenerateTrustToken($messageID, $from, $to, $subject)
{
	global $bm_prefs;

	return(sprintf('<%s@%s>',
		md5(sprintf('%s:%s:%s:%s:%s',
			$messageID,
			EncodeMailHeaderField($from),
			EncodeMailHeaderField($to),
			EncodeMailHeaderField($subject),
			md5(B1GMAIL_SIGNKEY))),
		$bm_prefs['b1gmta_host']));
}

/**
 * generate session token
 *
 * @return string
 */
function SessionToken()
{
	global $bm_prefs;

	$token = 'sessionToken';
	if($bm_prefs['ip_lock'] == 'yes')
		$token .= $_SERVER['REMOTE_ADDR'];
	if($bm_prefs['cookie_lock'] == 'yes')
		if(isset($_COOKIE['sessionSecret_' . substr(session_id(), 0, 16)]))
			$token .= $_COOKIE['sessionSecret_' . substr(session_id(), 0, 16)];
		else if(strpos(strtolower($_SERVER['PHP_SELF']), 'webdisk.php') !== false
			&& isset($_POST['key']))
			$token .= $_POST['key'];
	return(md5($token));
}

/**
 * decode HTML entities
 *
 * @param string $text Input
 * @return string
 */
function DecodeHTMLEntities($text)
{
	global $currentCharset;
	return(@html_entity_decode($text, ENT_COMPAT, $currentCharset));
}

/**
 * convert HTML text to plain text
 *
 * @param string $str Input
 * @return string
 */
function htmlToText($str)
{
	$str = str_replace(array("\r", "\n"), '', $str);
	$str = preg_replace('/\<p([^\>]*)\>/i', "\r\n", $str);
	$str = preg_replace('/\<br([^\>]*)\>/i', "\r\n", $str);
	$str = strip_tags(str_replace('>', '> ', $str));
	$str = DecodeHTMLEntities($str);
	return($str);
}

/**
 * get all mail service domains
 *
 * @param bool $includeGroupDomains Include group domains?
 * @return array
 */
function MyDomains($includeGroupDomains = true, $includeUserDomains = true)
{
	global $bm_prefs;

	$domains = GetDomainList('aliases');

	if($includeGroupDomains)
		$domains = array_merge($domains, BMGroup::GetGroupDomains());
	if($includeUserDomains)
		$domains = array_merge($domains, BMUser::GetUserDomains());

	return($domains);
}

/**
 * get list of service domains
 *
 * @param string $purpose Purpose (signup, login, aliases)
 * @return array
 */
function GetDomainList($purpose = '')
{
	global $db, $plugins;

	switch($purpose)
	{
	case 'signup':
		$where = ' WHERE `in_signup`=1';
		break;

	case 'login':
		$where = ' WHERE `in_login`=1';
		break;

	case 'aliases':
		$where = ' WHERE `in_aliases`=1';
		break;

	default:
		$where = '';
		break;
	}

	$result = array();
	$res = $db->Query('SELECT `domain` FROM {pre}domains' . $where . ' ORDER BY `pos` ASC,`domain` ASC');
	while($row = $res->FetchArray(MYSQLI_ASSOC))
		$result[] = $row['domain'];
	$res->Free();

	if(isset($plugins) && is_object($plugins))
		ModuleFunction('OnGetDomainList', array(&$result));

	return($result);
}

/**
 * query b1gMail signature server
 *
 * @param string $command Command
 * @param array $params Parameters
 * @return array
 */
function QuerySignatureServer($command, $params = array())
{
	if(!defined('SIGNATURE_SERVER')) {
		$result = array(
			'type'		=> 'error',
			'text'		=> 'Signature server is disabled.'
		);
		// return
		return($result);
	}
	// load class, if needed
	if(!class_exists('BMHTTP'))
		include(B1GMAIL_DIR . 'serverlib/http.class.php');

	// base url
	$url = sprintf(SIGNATURE_SERVER . '?action=%s',
		urlencode($command));

	// params
	foreach($params as $key=>$val)
		$url .= '&' . urlencode($key) . '=' . urlencode($val);

	// request
	$http = _new('BMHTTP', array($url));
	$result = $http->DownloadToString();

	// check signature
	$signature = substr($result, -32);
	$result = substr($result, 0, -32);
	if($signature === md5($result . B1GMAIL_SIGNKEY))
	{
		$result = @unserialize($result);

		// error?
		if(!is_array($result))
		{
			$result = array(
				'type'		=> 'error',
				'text'		=> 'Signature server returned unexpected result. Please try again later.'
			);
		}
	}
	else
	{
		$result = array(
			'type'		=> 'error',
			'text'		=> 'The respone signature returned by the signature server is invalid.'
		);
	}

	// return
	return($result);
}

/**
 * check POP3 login
 *
 * @param string $host
 * @param int $port
 * @param string $user
 * @param string $pass
 * @param bool $ssl
 * @return bool
 */
function CheckPOP3Login($host, $port, $user, $pass, $ssl = false)
{
	$ok = false;

	if(!class_exists('BMPOP3'))
		include(B1GMAIL_DIR . 'serverlib/pop3.class.php');

	$pop3 = _new('BMPOP3', array(($ssl ? 'ssl://' : '') . $host, $port));
	if($pop3->Connect())
	{
		if($pop3->Login($user, $pass))
		{
			$ok = true;
		}
		$pop3->Disconnect();
	}

	return($ok);
}

/**
 * get microtime as float value
 *
 * @return float
 */
function microtime_float()
{
    list($usec, $sec) = explode(' ', microtime());
    return((float)$usec + (float)$sec);
}

/**
 * get postmaster mail address
 *
 * @return string
 */
function GetPostmasterMail()
{
	global $bm_prefs;

	list($firstDomain) = GetDomainList();
	$mail = 'postmaster@' . $firstDomain;

	return($mail);
}

/**
 * build signature string
 *
 * @param string $mode Mode (text/html)
 * @return string
 */
function GetSigStr($mode = 'text')
{
	global $groupRow;

	$sigStr = '';
	if(trim($groupRow['signatur']) != '')
	{
		if($mode == 'html')
		{
			$sigStr = "\r\n<br /><br />\r\n"
						. str_repeat(SIGNATURE_LINE_CHAR, SIGNATURE_LINE_LENGTH)
						. "<br />\r\n"
						. nl2br(HTMLFormat($groupRow['signatur']));
		}
		else if($mode == 'text')
		{
			$sigStr = "\r\n\r\n"
						. str_repeat(SIGNATURE_LINE_CHAR, SIGNATURE_LINE_LENGTH)
						. "\r\n"
						. $groupRow['signatur'];
		}
	}

	return($sigStr);
}

/**
 * generate a random secret key
 *
 * @param string $str Some prefix string/salt
 * @return string
 */
function GenerateRandomKey($str)
{
	return(md5(microtime().(function_exists('posix_getpid') ? posix_getpid() : mt_rand(0, 1000)).uniqid($str).md5(B1GMAIL_SIGNKEY)));
}

/**
 * generate random salt
 *
 * @param int $length Length
 * @return string
 */
function GenerateRandomSalt($length = 8)
{
	$saltChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.,_-&$#"\';:!%(){}[]=?+<>';
	$salt = '';
	for($i=0; $i<$length; $i++)
		$salt .= substr($saltChars, mt_rand(0, strlen($saltChars)-1), 1);
	return($salt);
}

/**
 * synchronize DB structure against an DB structure array
 *
 * @param array $databaseStructure (New/correct) DB structure
 * @return array
 */
function SyncDBStruct($databaseStructure)
{
	global $db, $bm_prefs;

	// queries to execute
	$syncQueries = array();

	// get tables
	$defaultTables = array();
	$res = $db->Query('SHOW TABLES');
	while($row = $res->FetchArray(MYSQLI_NUM))
		$myTables[] = $row[0];
	$res->Free();

	// compare tables
	foreach($databaseStructure as $tableName=>$tableInfo)
	{
		$tableFields = $tableInfo['fields'];
		$tableIndexes = $tableInfo['indexes'];

		//
		// table exists => compare fields and indexes
		//
		if(in_array($tableName, $myTables))
		{
			// get my fields
			$myFields = array();
			$res = $db->Query('SHOW FIELDS FROM ' . $tableName);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				if($row['Null'] == '') $row['Null'] = 'NO';
				$myFields[$row['Field']] = array($row['Field'], $row['Type'], $row['Null'], $row['Key'], $row['Default'], $row['Extra']);
			}
			$res->Free();

			// get my indexes
			$myIndexes = array();
			$res = $db->Query('SHOW INDEX FROM ' . $tableName);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				if(isset($myIndexes[$row['Key_name']]))
					$myIndexes[$row['Key_name']][] = $row['Column_name'];
				else
					$myIndexes[$row['Key_name']] = array($row['Column_name']);
			$res->Free();

			// compare fields
			foreach($tableFields as $field)
			{
				$op = false;

				if(!isset($myFields[$field[0]]))
				{
					$op = 'ADD';
				}
				else
				{
					$myField = $myFields[$field[0]];
					if($myField[1] != $field[1]
						|| $myField[2] != $field[2]
						|| ($myField[4] != $field[4] && !(($myField[4]==0 && $field[4]=='') || ($myField[4]=='' && $field[4]==0)))
						|| $myField[5] != $field[5])
					{
						$op = 'MODIFY';
					}
				}

				if($op !== false)
				{
					$utf8_collate = ' CHARACTER SET utf8 COLLATE utf8_general_ci';
					if(defined('DB_CHARSET')) {
						if(DB_CHARSET=='utf8mb4') $utf8_collate = ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
					}
					$syncQueries[] = sprintf('ALTER TABLE %s %s `%s` %s%s%s%s%s',
						$tableName,
						$op,
						$field[0],
						$field[1],
						$bm_prefs['db_is_utf8'] == 1 ? (strpos($field[1], 'char') !== false || strpos($field[2], 'text') !== false
														? $utf8_collate
														: '') : '',
						$field[2] == 'NO' ? ' NOT NULL' : '',
						$field[4] == 'NULL' ? ' DEFAULT NULL' : ($field[4] != ''
							? (is_numeric($field[4])
									? ' DEFAULT ' . $field[4]
									: ' DEFAULT \'' . $db->Escape($field[4]) . '\'')
							: ''),
						$field[5] != '' ? ' ' . $field[5] : '');
				}
			}

			// compare indexes
			foreach($tableIndexes as $indexName=>$indexFields)
			{
				// keys
				if($indexName != 'PRIMARY')
				{
					$op = false;

					if(!isset($myIndexes[$indexName]))
					{
						$op = true;
					}
					else if($myIndexes[$indexName] != $indexFields)
					{
						$op = true;
						$syncQueries[] = sprintf('ALTER TABLE %s DROP KEY `%s`',
							$tableName,
							$indexName);
					}

					if($op)
					{
						$syncQueries[] = sprintf('ALTER TABLE %s ADD KEY `%s`(%s)',
							$tableName,
							$indexName,
							'`' . implode('`,`', $indexFields) . '`');
					}
				}

				// primary keys
				else
				{
					if(!isset($myIndexes[$indexName]))
					{
						// add
						$syncQueries[] = sprintf('ALTER TABLE %s ADD PRIMARY KEY(%s)',
							$tableName,
							'`' . implode('`,`', $indexFields) . '`');
					}
					else if($myIndexes[$indexName] != $indexFields)
					{
						// drop, add
						$syncQueries[] = sprintf('ALTER TABLE %s DROP PRIMARY KEY, ADD PRIMARY KEY(%s)',
							$tableName,
							'`' . implode('`,`', $indexFields) . '`');
					}
				}
			}
		}

		//
		// table does not exist => create
		//
		else
		{
			$stmt = sprintf('CREATE TABLE %s(' . "\n",
				$tableName);

			// fields
			foreach($tableFields as $field)
			{
				$utf8_collate = ' CHARACTER SET utf8 COLLATE utf8_general_ci';
					if(defined('DB_CHARSET')) {
						if(DB_CHARSET=='utf8mb4') $utf8_collate = ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
					}
				$stmt .= sprintf(' `%s` %s%s%s%s%s,' . "\n",
					$field[0],
					$field[1],
					$bm_prefs['db_is_utf8'] == 1 ? (strpos($field[1], 'char') !== false || strpos($field[2], 'text') !== false
													? $utf8_collate
													: '') : '',
					$field[2] == 'NO' ? ' NOT NULL' : '',
					$field[4] == 'NULL' ? ' DEFAULT NULL' : ($field[4] != ''
						? (is_numeric($field[4])
								? ' DEFAULT ' . $field[4]
								: ' DEFAULT \'' . $db->Escape($field[4]) . '\'')
						: ''),
					$field[5] != '' ? ' ' . $field[5] : '');
			}

			// indexes
			foreach($tableIndexes as $indexName=>$indexFields)
			{
				if($indexName == 'PRIMARY')
					$stmt .= sprintf(' PRIMARY KEY (%s),' . "\n",
						'`' . implode('`,`', $indexFields) . '`');
				else
					$stmt .= sprintf(' KEY `%s`(%s),' . "\n",
						$indexName,
						'`' . implode('`,`', $indexFields) . '`');
			}

			$stmt = substr($stmt, 0, -2) . "\n" . ')';

			$syncQueries[] = $stmt;
		}
	}

	// execute queries
	$result = array();
	foreach($syncQueries as $query)
		if($db->Query($query))
			$result[$query] = true;
		else
			$result[$query] = false;

	// return
	return($result);
}

/**
 * send system mail
 *
 * @param string $from Sender
 * @param string $to Recipient
 * @param string $subject Subject
 * @param string $templateName Name of e-mail template (from lang_custom)
 * @param array $vars Variables (key => value)
 * @param int $forUser User ID for localized phrases
 * @return bool
 */
function SystemMail($from, $to, $subject, $templateName, $vars, $forUser = -1)
{
	global $lang_custom, $currentCharset;

	// load class, if needed
	if(!class_exists('BMMailBuilder'))
		include(B1GMAIL_DIR . 'serverlib/mailbuilder.class.php');

	// create text
	if($forUser != -1)
		$text = GetPhraseForUser($forUser, 'lang_custom', $templateName);
	else
		$text = $lang_custom[$templateName];
	if(is_array($vars))
	{
		foreach($vars as $key=>$val)
		{
			$text 		= str_replace('%%'.$key.'%%', $val, $text);
			$subject 	= str_replace('%%'.$key.'%%', $val, $subject);
		}
	}

	// create mail
	$mail = _new('BMMailBuilder', array(true));
	$mail->SetUserID(USERID_SYSTEM);
	$mail->AddHeaderField('From',			$from);
	$mail->AddHeaderField('To',				$to);
	$mail->AddHeaderField('Subject',		$subject);
	$mail->AddHeaderField('Auto-Submitted',	'auto-generated');
	$mail->AddText($text, 'plain', $currentCharset);
	$result = $mail->Send() !== false;
	$mail->CleanUp();

	// stats, log
	if($result)
	{
		Add2Stat('sysmail');
		PutLog(sprintf('Sent system mail <%s> from <%s> to <%s>',
			$templateName,
			ExtractMailAddress($from),
			ExtractMailAddress($to)),
			PRIO_NOTE,
			__FILE__,
			__LINE__);
	}
	else
	{
		PutLog(sprintf('Failed to send system mail <%s> from <%s> to <%s>',
			$templateName,
			ExtractMailAddress($from),
			ExtractMailAddress($to)),
			PRIO_WARNING,
			__FILE__,
			__LINE__);
	}

	// return
	return($result);
}

/**
 * get a phrase of user's language file
 *
 * @param int $userID User ID
 * @param string $language Language
 * @param string $var Phrase pool (lang_main, lang_admin, lang_client, lang_custom)
 * @param string $phrase Phrase key
 * @return string
 */
function GetPhraseForUser($userID, $var, $phrase)
{
	global $db, $bm_prefs;

	// language?
	$res = $db->Query('SELECT language FROM {pre}users WHERE id=?',
		(int)$userID);
	list($language) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();
	if(trim($language) == '')
		$language = $bm_prefs['language'];

	// return phrase
	return(GetPhraseForLanguage($language, $var, $phrase));
}

/**
 * get phrase of a certain language file
 *
 * @param string $language Language
 * @param string $var Phrase pool (lang_main, lang_admin, lang_client, lang_custom)
 * @param string $phrase Phrase key
 * @return string
 */
function GetPhraseForLanguage($language, $var, $phrase)
{
	global $cacheManager, $currentLanguage;

	// already loaded?
	if($currentLanguage == $language)
		return($GLOBALS[$var][$phrase]);

	// avail languages?
	$availableLanguages = GetAvailableLanguages();
	if(isset($availableLanguages[$language]))
	{
		if($var == 'lang_custom' && ($lang_custom = $cacheManager->Get('langCustom:' . $language)))
		{
			return($lang_custom[$phrase]);
		}

		$lang_custom = $lang_client = $lang_user = $lang_admin = array();
		@include(B1GMAIL_DIR . 'languages/' . $language . '.lang.php');

		if(!MAINTENANCE_MODE)
		{
			// module handler
			ModuleFunction('OnReadLang', array(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $language));
		}

		if($var == 'lang_custom')
		{
			$lang_custom = GetCustomLanguage($language, $lang_custom);
		}

		return(${$var}[$phrase]);
	}

	// not found
	return('#UNKNOWN_PHRASE(' . $phrase . ')#');
}

/**
 * update statistics
 *
 * @param string $type
 * @param int $count
 */
function Add2Stat($type, $count = 1)
{
	global $db;

	$d = date('d');
	$m = date('m');
	$y = date('Y');
	$id = 0;

	// is there already a row for this?
	$res = $db->Query('SELECT id FROM {pre}stats WHERE d=? AND m=? AND y=? AND typ=? LIMIT 1',
		$d,
		$m,
		$y,
		$type);
	if($res->RowCount() == 1)
	{
		list($id) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
	}

	// update existing row...
	if($id != 0)
	{
		$db->Query('UPDATE {pre}stats SET anzahl=anzahl+'.(int)$count.' WHERE id=?',
			$id);
	}

	// ...or insert new row
	else
	{
		$db->Query('INSERT INTO {pre}stats(d,m,y,typ,anzahl) VALUES (?,?,?,?,?)',
			$d,
			$m,
			$y,
			$type,
			$count);
	}
}

/**
 * fgets wrapper
 *
 * @param resource $handle
 * @param int $length
 * @return string
 */
function fgets2($handle, $length = 40960)
{
	$result = fgets($handle);

	if(function_exists('stream_get_meta_data'))
	{
		$meta = stream_get_meta_data($handle);
		if(isset($meta['timed_out']) && $meta['timed_out'])
			$result = false;
	}

	return($result);
}

/**
 * validate mail address against MX server
 *
 * @param string $address Address
 */
function ValidateMailAddress($address)
{
	global $bm_prefs;

	list($userName, $hostName) = explode('@', $address);
	$errNo = $errStr = '';
	$mailFrom = GetPostmasterMail();

	// windows does not support this
	if(!function_exists('getmxrr'))
		return(true);

	// get mx records
	$mxHosts = array();
	$mxHosts[] = $hostName;

	if(@getmxrr($hostName, $mxHosts))
	{
		return(true);
	}
	else if($sock = @fsockopen($hostName, 25, $errNo, $errStr, SOCKET_TIMEOUT))
	{
		@fclose($sock);
		return(true);
	}

	return(false);
}

/**
 * class factory
 *
 * @param string $class Class name
 * @param array $args Arguments, if any (as an array)
 * @return Object
 */
function _new($class, $args = array())
{
	global $plugins;

	// overridden?
	$origClass = $class;
	if(isset($plugins) && is_object($plugins))
		foreach($plugins->_plugins as $key=>$value)
			if(($replacement = $plugins->callFunction('getClassReplacement', $key, false, array($class))) !== false)
			{
				$replacingModule = $key;
				$class = $replacement;
				break;
			}

	// check if class exists
	if(!class_exists($class))
	{
		DisplayError(isset($replacingModule) ?  0x13 : 0x12, 'Class not found',
			'The requested class cannot be found.' . (isset($replacingModule) ? ' An installed plugin seems to be faulty.' : ''),
			isset($replacingModule)
				? sprintf("Original class name:\n%s\n\nOverridden class name:\n%s\n\nOverridden by plugin:\n%s", $origClass, $class, $replacingModule)
				: sprintf("Class name:\n%s", $class),
			__FILE__,
			__LINE__);
		die();
	}

	// build arg string
	$argStr = '';
	for($key=0; $key<count($args); $key++)
		$argStr .= '$args[' . $key . '],';

	// call!
	return eval('return new ' . $class . '(' . substr($argStr, 0, -1) . ');');
}

/**
 * get day of week (1-7)
 *
 * @return int
 */
function RealW()
{
	$a = date('w');
	if($a==0) {
		$a = 7;
	}
	return($a);
}

/**
 * put a given date into a date category
 *
 * @param int $date Date (timestamp)
 * @return array
 */
function categorizeDate($date)
{
	global $lang_user;

	// generate date cats
	$ts = array();
	$ts[0] = array(
		'from' 	 => mktime(0,0,0,date('m'),date('d'),date('Y')),
		'to'	 => mktime(24,0,0,date('m'),date('d'),date('Y')),
		'text'	 => $lang_user['today'],
		'date'   => mktime(0,0,0,date('m'),date('d'),date('Y'))
	);
	$last = mktime(0,0,0,date('m'),date('d'),date('Y'));
	for($i=1; $i<RealW(); $i++)
	{
		$a = RealW() - $i;
		$last -= 86400;
		$ts[$a] = array(
			'from' 	=> $last,
			'to'	=> $last+86400,
			'text' 	=> date('l', $last),
			'date' 	=> $last
		);
	}
	$ts[-2] = array(
		'from'	=> $last-(7 * 86400),
		'to'	=> $last,
		'text'	=> $lang_user['lastweek'],
		'date'	=> -1
	);
	$last -= 7 * 86400;
	$ts[-1] = array(
		'from'	=> -1,
		'to'	=> $last,
		'text'	=> $lang_user['later'],
		'date'	=> -1
	);

	// where do we fit in?
	$myCat = -1;
	foreach($ts as $key=>$value)
		if($date >= $value['from'] && $date <= $value['to'])
			$myCat = $key;

	// return
	return(array($myCat, $ts[$myCat]));
}

/**
 * format compose text (reply, redirect, forward)
 *
 * @param string $text Text
 * @param string $textMode Text mode (html, text)
 * @param string $action Action (reply, redirect, forward)
 * @param BMMail $mail Mail object
 * @param bool $insertQuote Insert quote?
 * @param string $signature Signature (or false)
 * @return string
 */
function formatComposeText($text, $textMode, $action, &$mail, $insertQuote = true, $signature = false)
{
	global $lang_user, $thisUser;

	if(!$insertQuote)
		return('');

	if($textMode == 'html')
	{
		if($action == 'redirect')
		{
			$result = $text;
		}
		else
		{
			$result = sprintf('<span style="font-family:arial;font-size:12px;">%s<br />'
								. '<blockquote style="margin:1.5em 0 0 0;padding:0;border-top:1px solid #CCC;">'
								. '<p style="color:#555;margin: 0.5em 0 1em 0;"><b>%s:</b> %s<br />'
								. '<b>%s:</b> %s<br />'
								. '<b>%s:</b> %s<br />'
								. '<b>%s:</b> %s</p>',
						$signature !== false ? '<br />'.$signature.'<br />' : '&nbsp;',
						$lang_user['from'], 	HTMLFormat(HTMLFormat(DecodeEMail($mail->GetHeaderValue('from'))), true),
						$lang_user['date'],  	date($thisUser->_row['datumsformat'], $mail->date),
						$lang_user['to'], 		HTMLFormat(HTMLFormat(DecodeEMail($mail->GetHeaderValue('to'))), true),
						$lang_user['subject'],  HTMLFormat(HTMLFormat($mail->GetHeaderValue('subject')), true))
						. $text
						. '</blockquote></span>';
		}
	}
	else
	{
		if($action == 'redirect')
		{
			$result = $text;
		}
		else
		{
			$result = sprintf("\n%s\n"
								. '--- %s ---' . "\n"
								. '%s: %s' . "\n"
								. '%s: %s' . "\n"
								. '%s: %s' . "\n"
								. '%s: %s' . "\n"
								. "\n",
						$signature !== false ? "\n".$signature."\n" : '',
						$lang_user['srcmsg'],
						$lang_user['from'], 	DecodeEMail($mail->GetHeaderValue('from')),
						$lang_user['date'],		date($thisUser->_row['datumsformat'], $mail->date),
						$lang_user['to'], 		DecodeEMail($mail->GetHeaderValue('to')),
						$lang_user['subject'], 	$mail->GetHeaderValue('subject'))
						. _wordwrap($text, 72, "\r\n");
		}
	}

	return(isset($result) ? $result : '');
}

/**
 * check if request is a POST request
 *
 * @return bool
 */
function IsPOSTRequest()
{
	return(!isset($_SERVER['REQUEST_METHOD'])
			|| $_SERVER['REQUEST_METHOD'] == 'POST');
}

/**
 * format html e-mail text
 *
 * @param string $in Input
 * @param bool $showExternal Enable external objects and scripts?
 * @param array $attachments Attachment list (for CID replacement)
 * @param int $mailID Mail ID
 * @param bool $mobile For mobile interface?
 * @return string
 */
function formatEMailHTMLText($in, $showExternal = false, $attachments = array(), $mailID = -1, $mobile = false, $replyMode = false)
{
	global $currentCharset;

	if(!class_exists('BMHTMLEMailFormatter'))
		include(B1GMAIL_DIR . 'serverlib/htmlemailformatter.class.php');

	$formatter = _new('BMHTMLEMailFormatter', array($in, $currentCharset));
	$formatter->setLevel(0);
	$formatter->setAllowExternal($showExternal);
	$formatter->setAttachments($attachments);
	$formatter->setReplyMode($replyMode);

	if($mobile)
	{
		$formatter->setComposeBaseURL('email.php?action=compose&sid=' . session_id() . '&to=');
		$formatter->setAttachmentBaseURL('email.php?action=attachment&view=true&id=' . $mailID . '&sid=' . session_id() . '&attachment=');
	}
	else
	{
		$formatter->setComposeBaseURL('email.compose.php?sid=' . session_id() . '&to=');
		$formatter->setAttachmentBaseURL('email.read.php?action=downloadAttachment&view=true&id=' . $mailID . '&sid=' . session_id() . '&attachment=');
	}

	$result = $formatter->format();

	return($result);
}

/**
 * format e-mail text
 *
 * @param string $in Input
 * @param bool $html For HTML output?
 * @param bool $mobile For mobile interface?
 * @return string
 */
function formatEMailText($in, $html = true, $mobile = false)
{
	global $currentCharset;

	if(strtolower($currentCharset) == 'utf-8' || strtolower($currentCharset) == 'utf8')
		$pcreSuffix = 'u';
	else
		$pcreSuffix = '';

	// html entities
	$in = HTMLFormat($in);

	// for HTML output?
	if($html)
	{
		$in = nl2br($in);

		// tabs
		$in = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $in);

		// links
		$bmLinks = array();
		$in = preg_replace_callback("/[[:alpha:]]+:\/\/[^<>[:space:]]+[[:alnum:]\/=?&@%]/",
			function($matches) use(&$bmLinks)
			{
				$bmLinks[] = $matches[0];
				return(sprintf(':::_b1gMailLink:%d_:::', count($bmLinks)-1));
			},
			$in);

		// e-mail addresses
		$links = array();
		$in = preg_replace_callback("/[a-zA-Z0-9\.\_-]*\@[^ ]*\.[a-zA-Z0-9\.\_-]*/" . $pcreSuffix,
			function($matches) use($mobile)
			{
				$match = HTMLEntities($matches[0]);
				return(!$mobile
						? '<a target="_top" href="email.compose.php?to='.$match.'&sid=' . session_id() . '">'.$match.'</a>'
						: '<a target="_top" href="email.php?action=compose&to='.$match.'&sid=' . session_id() . '">'.$match.'</a>');
			},
			$in);

		// build links
		foreach($bmLinks as $i=>$link)
		{
			$in = str_replace(sprintf(':::_b1gMailLink:%d_:::', $i),
							  sprintf('<a href="deref.php?%s" title="%s" target="_blank" rel="noopener noreferrer">%s</a>', $link, $link, $link),
							  $in);
		}
	}

	return($in);
}

/**
 * return numeric php version
 *
 * @return int
 */
function PHPNumVersion()
{
	$ver = phpversion();
	if(strlen($ver) > strlen('0.0.0'))
		$ver = substr($ver, 0, strlen('0.0.0'));
	return((int)str_replace('.', '', $ver));
}

/**
 * get full file contents
 *
 * @param string $fileName Filename
 * @return string
 */
function getFileContents($fileName)
{
	// we should not read too big files at once
	if(@filesize($fileName) > 1024*500)
		PutLog(sprintf('Getting whole file contents of big file <%s> (%.02f KB > 500 KB)',
						$fileName,
						round(filesize($fileName)/1024, 2)),
				PRIO_DEBUG,
				__FILE__,
				__LINE__);

	// read contents
	$fp = @fopen($fileName, 'rb');
	if(!$fp)
		return(false);
	$contents = '';
	while(!feof($fp))
		$contents .= fread($fp, 8096);
	fclose($fp);

	// return contents
	return($contents);
}

/**
 * get/copy file(s) selected using the webdisk upload widget
 *
 * @param string $webdiskFile Webdisk file ID
 * @param int $destinationFile Destination file name
 * @return array
 */
function getUploadedWebdiskFile($webdiskFile, $destinationFile)
{
	global $userRow;

	// user row available?
	if(!isset($userRow) || !is_array($userRow))
		return(array());

	$resultArray = false;

	if(!class_exists('BMWebdisk'))
		include(B1GMAIL_DIR . 'serverlib/webdisk.class.php');
	$webdisk = _new('BMWebdisk', array($userRow['id']));
	$file = $webdisk->GetFileInfo($webdiskFile);

	if($file)
	{
		$sourceFP = BMBlobStorage::createProvider($file['blobstorage'], $userRow['id'])->loadBlob(BMBLOB_TYPE_WEBDISK, $webdiskFile);
		if($sourceFP)
		{
			$destFP = fopen($destinationFile, 'wb');
			if($destFP)
			{
				while(!feof($sourceFP))
				{
					$chunk = fread($sourceFP, 4096);
					fwrite($destFP, $chunk);
				}
				fclose($destFP);

				$resultArray = array(
					'name'		=> $file['dateiname'],
					'type'		=> $file['contenttype'],
					'size'		=> $file['size'],
					'tmp_name'	=> '-none-',
					'error'		=> 0,
					'dest'		=> $destinationFile
				);
			}
		}

		fclose($sourceFP);
	}

	return($resultArray);
}

/**
 * get/copy file(s) uploaded using the local/webdisk upload widget
 *
 * @param string $fieldName Field name
 * @param int $lifeTime Lifetime for temp files
 * @return array
 */
function getUploadedFiles($fieldName, $lifeTime = 28800)
{
	global $userRow;

	// user row available?
	if(!isset($userRow) || !is_array($userRow))
		return(array());

	// get file vars
	$localFile = $_FILES['localFile_' . $fieldName];
	$webdiskFile = (int)$_REQUEST['webdiskFile_' . $fieldName . '_id'];

	// file uploaded
	if(isset($localFile) && is_array($localFile) && ((is_array($localFile['error']) && in_array(UPLOAD_ERR_OK, $localFile['error'])) || (!is_array($localFile['error']) && $localFile['error'] == UPLOAD_ERR_OK)))
	{
		if(is_array($localFile['tmp_name']))
		{
			$result = array();

			foreach($localFile['tmp_name'] as $fileID=>$tmpName)
			{
				if($localFile['error'][$fileID] != UPLOAD_ERR_OK)
					continue;

				$destinationFileID = RequestTempFile($userRow['id'], time()+$lifeTime);
				$destinationFile = TempFileName($destinationFileID);

				if(!@move_uploaded_file($tmpName, $destinationFile))
				{
					ReleaseTempFile($userRow['id'], $destinationFileID);
					continue;
				}

				$resultArray = array(
					'name'		=> $localFile['name'][$fileID],
					'type'		=> $localFile['type'][$fileID],
					'size'		=> $localFile['size'][$fileID],
					'tmp_name'	=> $localFile['tmp_name'][$fileID],
					'error'		=> $localFile['error'][$fileID],
					'dest'		=> $destinationFile,
					'dest_id'	=> $destinationFileID
				);

				$result[] = $resultArray;
			}

			return $result;
		}
		else if($localFile['error'] == UPLOAD_ERR_OK)
		{
			$destinationFileID = RequestTempFile($userRow['id'], time()+$lifeTime);
			$destinationFile = TempFileName($destinationFileID);

			if(@move_uploaded_file($localFile['tmp_name'], $destinationFile))
			{
				$localFile['dest'] = $destinationFile;
				$localFile['dest_id'] = $destinationFileID;
				return(array($localFile));
			}
			else
			{
				ReleaseTempFile($userRow['id'], $destinationFileID);
			}
		}
	}

	// file from webdisk
	else if($webdiskFile > 0)
	{
		$destinationFileID = RequestTempFile($userRow['id'], time()+$lifeTime);
		$destinationFile = TempFileName($destinationFileID);

		$resultItem = getUploadedWebdiskFile($webdiskFile, $destinationFile);
		if($resultItem)
		{
			$resultItem['dest_id'] = $destinationFileID;
			return(array($resultItem));
		}
		else
		{
			ReleaseTempFile($userRow['id'], $destinationFileID);
		}
	}

	// no file
	return(array());
}

/**
 * get/copy a file uploaded using the local/webdisk upload widget
 *
 * @param string $fieldName Field name
 * @param string $destinationFile Destination path
 * @return array
 */
function getUploadedFile($fieldName, $destinationFile)
{
	global $userRow;

	// user row available?
	if(!isset($userRow) || !is_array($userRow))
		return(false);

	// get file vars
	$localFile = $_FILES['localFile_' . $fieldName];
	$webdiskFile = (int)$_REQUEST['webdiskFile_' . $fieldName . '_id'];

	// file uploaded
	if(isset($localFile) && is_array($localFile) && $localFile['error'] == UPLOAD_ERR_OK)
	{
		if(@move_uploaded_file($localFile['tmp_name'], $destinationFile))
		{
			return($localFile);
		}
		else
		{
			return(false);
		}
	}

	// file from webdisk
	else if($webdiskFile > 0)
	{
		return(getUploadedWebdiskFile($webdiskFile, $destinationFile));
	}

	// no file
	return(false);
}

/**
 * check if $s is equal to $in or $s is in $in
 *
 * @param mixed $in Array or string to check againt
 * @param string $s String
 * @return bool
 */
function eqOrIn($in, $s)
{
	return((!is_array($in) && $in == $s)
			|| (is_array($in) && in_array($s, $in)));
}

/**
 * strip slashes for a whole array (recursively)
 *
 * @param array $in Input array
 * @return array
 */
function stripslashes_array($in)
{
	foreach($in as $key=>$val)
	{
		if(is_array($val))
			$in[$key] = stripslashes_array($val);
		else
			$in[$key] = stripslashes($val);
	}
	return($in);
}

/**
 * prepare input vars
 *
 */
function PrepareInputVars()
{

}

/**
 * get cellphone number from smarty cellphone number control
 *
 * @param string $field Field name (prefix)
 * @return string
 */
function SmartyCellphoneNo($field)
{
	if(isset($_REQUEST[$field.'_pre']))
	{
		if(trim($_REQUEST[$field.'_no']) == '')
			$no = '';
		else
			$no = trim($_REQUEST[$field.'_pre'])
					. trim($_REQUEST[$field.'_no']);
	}
	else
	{
		$no = trim($_REQUEST[$field]);
	}

	$no = str_replace('+', '00', $no);
	$no = preg_replace('/[^0-9]/', '', $no);
	return($no);
}

/**
 * generate timestamp from smarty date/time controls
 *
 * @param string $field Field name (prefix)
 * @return int
 */
function SmartyDateTime($field)
{
	$d = $m = $y = 1;
	$h = $i = $s = 0;
	$y = (int)date('Y');

	if(isset($_REQUEST[$field.'Day']))
		$d = (int)$_REQUEST[$field.'Day'];
	if(isset($_REQUEST[$field.'Month']))
		$m = (int)$_REQUEST[$field.'Month'];
	if(isset($_REQUEST[$field.'Year']))
		$y = (int)$_REQUEST[$field.'Year'];
	if(isset($_REQUEST[$field.'Hour']))
		$h = (int)$_REQUEST[$field.'Hour'];
	if(isset($_REQUEST[$field.'Minute']))
		$i = (int)$_REQUEST[$field.'Minute'];
	if(isset($_REQUEST[$field.'Second']))
		$s = (int)$_REQUEST[$field.'Second'];

	return(($d+$m+$y+$h+$i+$s) == 0
		? 0
		: mktime($h, $i, $s, $m, $d, $y));
}

/**
 * send a file stream with speed limit
 *
 * @param resource $fp File stream
 * @param int $speed Speed limit (kb/s)
 * @return int Sent bytes
 */
function SendFileFP($fp, $speed = -1)
{
	$sentBytes = 0;
	if($speed == -1)
		$bufferSize = 4096;
	else
		$bufferSize = ($speed*1024) / 100;
	while(is_resource($fp) && !feof($fp))
	{
		$buffer = fread($fp, $bufferSize);
		echo($buffer);
		$sentBytes += strlen($buffer);
		if($speed != -1)
			usleep(10000);
	}
	return($sentBytes);
}

/**
 * send a file with speed limit
 *
 * @param string $file Filename
 * @param int $speed Speed limit (kb/s)
 * @return int Sent bytes
 */
function SendFile($file, $speed = -1)
{
	if($fp = @fopen($file, 'rb'))
	{
		$result = SendFileFP($fp, $speed);
		fclose($fp);
		return($result);
	}
	else
	{
		PutLog(sprintf('Cannot open file <%s> for sending', $file),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
		return(-1);
	}
}

/**
 * split a list of mail addresses and names into pairs of name and mail address
 *
 * @param string $list List string
 * @return array
 */
function ParseMailList($list, $utf8 = false)
{
	$result = array();

	$parts = ExplodeOutsideOfQuotation($list, array(',', ';'));

	foreach($parts as $part)
	{
		$resultName = trim(ExtractMailName($part));
		$resultMail = trim(ExtractMailAddress($part, $utf8));
		$result[] = array(
			'name'		=> $resultName == '' ? /*$resultMail*/ '' : $resultName,
			'mail'		=> $resultMail
		);
	}

	return($result);
}

/**
 * split string by $separator, taking care of "quotations"
 *
 * @param string $string Input
 * @param mixed $separator Separator(s), may be an array
 * @return array
 */
function ExplodeOutsideOfQuotation($string, $separator, $preserveQuotes = false)
{
	$result = array();

	$inEscape = $inQuote = false;
	$tmp = '';
	for($i=0; $i<strlen($string); $i++)
	{
		$c = $string[$i];
		if(((!is_array($separator) && $c == $separator)
			|| (is_array($separator) && in_array($c, $separator)))
			&& !$inQuote
			&& !$inEscape)
		{
			if(trim($tmp) != '')
			{
				$result[] = trim($tmp);
				$tmp = '';
			}
		}
		else if($c == '"' && !$inEscape)
		{
			$inQuote = !$inQuote;
			if($preserveQuotes)
				$tmp .= $c;
		}
		else if($c == '\\' && !$inEscape)
			$inEscape = true;
		else
		{
			$tmp .= $c;
			$inEscape = false;
		}
	}
	if(trim($tmp) != '')
	{
		$result[] = trim($tmp);
		$tmp = '';
	}

	return($result);
}

/**
 * get filename of data item
 *
 * @param int $id Item ID
 * @param string $fx Extension
 * @param bool $readOnly Set this parameter to true to prevent folder creation
 * @return string
 */
function DataFilename($id, $fx = 'msg', $readOnly = false)
{
	global $plugins, $bm_prefs;

	// plugin?
	foreach($plugins->_plugins as $className=>$pluginInfo)
	{
		if(($result = $plugins->callFunction('DataFilename',  $className, false,
						array($id, $fx))) !== false
			&& $result
			&& strlen($result) > strlen($id . $fx))
		{
			return($result);
		}
	}

	// generate filename
	$dir = B1GMAIL_DATA_DIR;

	if(file_exists($dir . $id . '.' . $fx))
		return($dir . $id . '.' . $fx);

	for($i=0; $i<strlen((string)$id); $i++)
	{
		$dir .= substr((string)$id, $i, 1);
		if(($i+1) % 2 == 0)
		{
			$dir .= '/';
			if(!$readOnly && !file_exists($dir) && $bm_prefs['structstorage'] == 'yes' && ($i<strlen((string)$id)-1))
			{
				@mkdir($dir, 0777);
				@chmod($dir, 0777);
			}
		}
	}

	if(substr($dir, -1) == '/')
		$dir = substr($dir, 0, -1);
	$dir .= '.' . $fx;

	if(file_exists($dir) || $bm_prefs['structstorage'] == 'yes')
	{
		// structured storage
		return($dir);
	}
	else
	{
		// default
		return(B1GMAIL_DATA_DIR . $id . '.' . $fx);
	}
}

/**
 * extract message ids from string
 *
 * @param string $str
 * @return array
 */
function ExtractMessageIDs($str)
{
	$ret_arr = $result = array();
	preg_match_all('/<([^>]+)>/', $str, $ret_arr);
	foreach($ret_arr[0] as $ret)
		if(!in_array($ret, $result))
			$result[] = $ret;
	return($result);
}

/**
 * extract name from a string
 *
 * @param string $string
 * @return string
 */
function ExtractMailName($string)
{
	$newString = '';

	for($i=0; $i<strlen($string); $i++)
	{
		if($string[$i] == '<')
			break;
		if($string[$i] != '"' && $string[$i] != '\'')
			$newString .= $string[$i];
	}

	if(trim($string) == trim(ExtractMailAddress($string)))
		return('');
	else
		return(trim(stripslashes($newString)));
}

/**
 * extract mail address from a string
 *
 * @param string $string
 * @return string
 */
function ExtractMailAddress($string, $utf8 = false)
{
	$ret = '';
	$ret_arr = array();
	if($utf8)
	{
		if(preg_match_all('/[a-zA-Z0-9&=\'\\.\\-_\\+]+@[^ ]+\\.+[a-zA-Z]{2,12}/u', CharsetDecode($string, false, 'utf8'), $ret_arr) > 0)
			$ret = CharsetDecode($ret_arr[0][0], 'utf8');
	}
	else
	{
		if(preg_match_all('/[a-zA-Z0-9&=\'\\.\\-_\\+]+@[a-zA-Z0-9.-]+\\.+[a-zA-Z]{2,12}/', $string, $ret_arr) > 0)
			$ret = $ret_arr[0][0];
	}
	return($ret);
}

/**
 * extract mail addresses from string
 *
 * @param string $string
 * @return array
 */
function ExtractMailAddresses($string, $utf8 = false)
{
	$result = $ret_arr = array();
	if($utf8)
	{
		preg_match_all('/[a-zA-Z0-9&=\'\\.\\-_\\+]+@[^ ]+\\.+[a-zA-Z]{2,12}/u', CharsetDecode($string, false, 'utf8'), $ret_arr);
	}
	else
	{
		preg_match_all('/[a-zA-Z0-9&=\'\\.\\-_\\+]+@[a-zA-Z0-9.-]+\\.+[a-zA-Z]{2,12}/', $string, $ret_arr);
	}
	foreach($ret_arr[0] as $ret)
		if(!in_array($ret, $result))
			$result[] = $utf8 ? CharsetDecode($ret, 'utf8') : $ret;
	return($result);
}

/**
 * request privileges
 *
 * @param int $privileges Bitmask
 */
function RequestPrivileges($privileges, $return = false)
{
	global $db, $tpl, $userRow, $groupRow, $bm_prefs, $thisUser, $thisGroup, $currentLanguage,
			$lang_user, $lang_client, $lang_custom, $lang_admin, $adminRow;
	$ok = true;

	// sessions are always needed for more privileges
	@session_start();

	// user privileges?
	if(($privileges & PRIVILEGES_USER) != 0)
		if(!isset($_SESSION['bm_userLoggedIn']) || !$_SESSION['bm_userLoggedIn']
			|| !isset($_SESSION['bm_userID']) || !isset($_SESSION['bm_sessionToken'])
			|| $_SESSION['bm_sessionToken'] != SessionToken())
		{
			$ok = false;
		}
		else
		{
			$thisUser = _new('BMUser', array($_SESSION['bm_userID']));
			$userRow = $thisUser->Fetch();

			if(isset($tpl) && is_object($tpl))
				$tpl->assign('sid', session_id());

			if($userRow !== false && is_array($userRow) && $userRow['gesperrt'] == 'no')
			{
				if(DetermineLanguage() != $currentLanguage)
				{
					ReadLanguage();
					if(!MAINTENANCE_MODE)
					{
						// module handler
						ModuleFunction('OnReadLang', array(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $currentLanguage));
					}
					ReadCustomLanguage();
				}

				if(isset($_SESSION['bm_timezone']) && $bm_prefs['auto_tz'] == 'yes')
					SetTimeZoneByOffsetSeconds((int)$_SESSION['bm_timezone']);

				$thisGroup = $thisUser->GetGroup();
				$groupRow = $thisGroup->Fetch();
				if($groupRow === false || !is_array($groupRow))
				{
					PutLog(sprintf('Group <%d> of user <%s> not found!', $userRow['gruppe'], $userRow['email']),
						PRIO_WARNING,
						__FILE__,
						__LINE__);
					$ok = false;
				}
			}
			else
			{
				$ok = false;
			}
		}

	// admin privileges?
	if(($privileges & PRIVILEGES_ADMIN) != 0)
		if(!isset($_SESSION['bm_adminLoggedIn']) || !$_SESSION['bm_adminLoggedIn']
			|| !isset($_SESSION['bm_adminID'])
			|| !isset($_SESSION['bm_adminAuth']) || !isset($_SESSION['bm_sessionToken'])
			|| $_SESSION['bm_sessionToken'] != SessionToken())
		{
			$ok = false;
		}
		else
		{
			if(isset($tpl) && is_object($tpl))
				$tpl->assign('sid', session_id());

			$ok = false;

			$res = $db->Query('SELECT * FROM {pre}admins WHERE `adminid`=?', $_SESSION['bm_adminID']);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				if(md5($row['password'].$_SERVER['HTTP_USER_AGENT']) === $_SESSION['bm_adminAuth'])
				{
					$ok = true;
					$row['privileges'] = @unserialize($row['privileges']);
					if(!is_array($row['privileges']))
						$row['privileges'] = array();
					$adminRow = $row;
				}
			}
			$res->Free();

			if($ok && isset($_SESSION['bm_timezone']) && $bm_prefs['auto_tz'] == 'yes')
				SetTimeZoneByOffsetSeconds((int)$_SESSION['bm_timezone']);
		}

	// mobile privileges?
	if(($privileges & PRIVILEGES_MOBILE) != 0 && $ok)
		$ok = $groupRow['wap'] == 'yes';

	// client api access privileges?
	if(($privileges & PRIVILEGES_CLIENTAPI) != 0 && $ok)
		$ok = true;

	// requested action not allowed
	if(!$ok && !$return)
	{
		if(ADMIN_MODE)
		{
			DisplayError(0x02, 'Unauthorized', 'You are not authorized to view or change this dataset or page. Possible reasons are too few permissions or an expired session.',
				sprintf("Requested privileges:\n%d\n\nLogged in:\n%s",
					$privileges,
					isset($_SESSION['bm_userLoggedIn']) && $_SESSION['bm_userLoggedIn'] ? 'Yes' : 'No'),
				__FILE__,
				__LINE__);
		}
		else
		{
			$tpl->assign('title', $lang_user['sess_expired']);
			$tpl->assign('description', $lang_user['sess_expired_desc']);

			if(($privileges & PRIVILEGES_MOBILE) != 0)
			{
				$tpl->assign('isDialog', true);
				$tpl->assign('page', 'm/error.tpl');
				$tpl->display('m/index.tpl');
			}
			else
				$tpl->display('nli/error.tpl');
		}
		exit();
	}
	else if($return)
		return($ok);
}

/**
 * format date
 *
 * @param int $date
 * @return string
 */
function FormatDate($date = -1)
{
	global $bm_prefs;

	if($date == -1)
		$date = time();
	return(date($bm_prefs['datumsformat'], $date));
}

/**
 * check zip/city
 *
 * @param string $plz
 * @param string $ort
 * @param int $staat
 * @return bool
 */
function ZIPCheck($plz, $ort, $staat)
{
	global $currentCharset;

	if(in_array(strtolower($currentCharset), array('utf8', 'utf-8')))
		$ort = CharsetDecode($text, false, 'ISO-8859-1');

	$strip_chars = array(',', ';', '-', '?', ':', '?', '1', ' ', 'ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', "ae", "oe", "ue", "AE", "OE", "UE", "Ae", "Oe","Ue");

	$plz = preg_replace('/^([0]*)/', '', $plz);
	$ort = strtolower($ort);							# [ORT_STR] erstellen
	$ort = str_replace($strip_chars, '', $ort);			# [ORT_STR] erstellen
	$hash = $plz . soundex($ort);						# [PLZ][ORT_STR]
	$hash = crc32($hash);								# CRC32
	$hash = pack('i', $hash);							# In einen Binaerstring packen

	$plzfile = B1GMAIL_DIR . 'plz/' . $staat . '.plz';

	if(file_exists($plzfile))
	{
		$fp = fopen($plzfile, 'r');
		$plz_filesize = filesize($plzfile);
		if ($plz_filesize == 0) return(false);
		$inh = fread($fp, $plz_filesize);
		fclose($fp);

		$pos = strpos($inh, $hash);						# In der PLZ-Datei nach dem PLZ/Ort-Paar suchen
		$pos2 = strpos($inh, $hash[3] . $hash[2] . $hash[1] . $hash[0]);
		unset($inh);

		if($pos === false && $pos2 === false)
		{
			return(false);
		}
		else
		{
			return(true);
		}
	}
	else
	{
		return(true);
	}
}

/**
 * xml encode
 *
 */
function XMLEncode($str)
{
	$newstr = '';
	for($i=0; $i<strlen($str); $i++)
	{
		$c = substr($str, $i, 1);
		if((ord($c) < 32 || ord($c) == 127 || $c == '<' || $c == '>' || $c == '"' || $c == '&')
		   && ($c != "\n" && $c != "\r" && $c != "\t"))
			$newstr .= '&#' . ord($c) . ';';
		else
			$newstr .= $c;
	}

	return($newstr);
}

/**
 * country list
 *
 */
function CountryList($withDetails = false)
{
	global $db, $cacheManager;

	$cacheKey = 'countryList' . ($withDetails ? 'WithDetails' : '');

	if(!($laender = $cacheManager->Get($cacheKey)))
	{
		$laender = array();
		$res = $db->Query('SELECT id,land,is_eu,vat FROM {pre}staaten ORDER BY land ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($withDetails)
				$laender[$row['id']] = $row;
			else
				$laender[$row['id']] = $row['land'];
		}
		$res->Free();

		$cacheManager->Set($cacheKey, $laender);
	}

	return($laender);
}

/**
 * check if data is binary
 *
 * @param string $string String
 * @return bool
 */
function isBinary($string)
{
	for($i=0; $i<min(strlen($string), 32*1024); $i++)
	{
		$c = $string[$i];
		$cc = ord($c);

		//if(($cc < 32 || $cc > 126) && $cc != 13 && $cc != 10 && $cc != 9)
		if(($cc < 32 || $cc == 127) && ($cc != 13 && $cc != 10 && $cc != 9))
			return(true);
	}

	return(false);
}

/**
 * Base64 ouput incl. word wrap
 *
 * @param string $string String
 */
function base64Output($string)
{
	echo wordwrap(base64_encode($string), 72, "\n", true);
}

/**
 * generate xml respone from any array
 *
 */
function NormalArray2XML($array, $responseString = 'response', $first = true)
{
	global $currentCharset;

	// header
	if($first)
	{
		header('Content-Type: text/xml; charset=' . $currentCharset);
		header('Pragma: no-cache');
		header('Cache-Control: no-cache');
		echo '<?xml version="1.0" encoding="' . $currentCharset . '" ?>' . "\n";
		echo '<' . $responseString . '>' . "\n";
	}
	else if(is_array($array))
		echo '<array>' . "\n";

	if(is_array($array))
	{
		foreach($array as $key=>$val)
		{
			if(is_numeric($key))
				echo '<item key="' . $key . '">';
			else
				echo '<' . $key . '>';

			NormalArray2XML($val, $responseString, false);

			if(is_numeric($key))
				echo '</item>' . "\n";
			else
				echo '</' . $key . '>'. "\n";
		}
	}
	else
	{
		if($array !== '')
		{
			if(is_numeric($array) || is_int($array))
			{
				echo $array;
			}
			else if(is_bool($array))
			{
				echo $array ? 1 : 0;
			}
			else if(!isBinary($array) && strpos($array, "\n") === false && strpos($array, "\r") === false)
			{
				echo XMLEncode($array);
			}
			else
			{
				echo '<![CDATA[';
				if(isBinary($array))
				{
					echo '[[BASE64]]';
					base64Output($array);
				}
				else
					echo $array;
				echo ']]>' . "\n";
			}
		}
	}

	// footer
	if($first)
	{
		echo '</' . $responseString . '>' . "\n";
	}
	else if(is_array($array))
		echo '</array>' . "\n";
}

/**
 * generate xml response from specialy prepared array
 *
 */
function Array2XML($array, $responseString = 'response', $first = true, $return = false)
{
	global $currentCharset;

	$result = '';

	// header
	if($first)
	{
		header('Content-Type: text/xml; charset=' . $currentCharset);
		header('Pragma: no-cache');
		header('Cache-Control: no-cache');
		$result .= '<?xml version="1.0" encoding="' . $currentCharset . '" ?>' . "\n";
		$result .= '<' . $responseString . '>' . "\n";
	}

	// array => node output
	if(is_array($array))
	{
		foreach($array as $key=>$value)
		{
			// custom node name?
			if(is_array($value) && isset($value['#nodeName']))
				$key = $value['#nodeName'];

			// ignore special children (starting with #)
			if(!is_array($key) && strlen($key) > 0 && $key[0] == '#')
				continue;

			// node opening
			$result .= '<' . $key . ' ';

			// node parameters
			if(is_array($value) && isset($value['#nodeParam']) && is_array($value['#nodeParam']))
				foreach($value['#nodeParam'] as $paramKey=>$paramVal)
					$result .= sprintf(' %s="%s"', $paramKey, addslashes(XMLEncode($paramVal)));

			$result .= '>';

			if(is_array($value) && isset($value['#nodeCDATA']))
			{
				// cdata
				$result .= '<![CDATA[' . ($value['#nodeCDATA']) . ']]>';
			}
			else if(is_array($value) && isset($value['#nodeData']))
			{
				$result .= XMLEncode($value['#nodeData']);
			}
			else
			{
				// node data / children
				$result .= Array2XML($value, $responseString, false, true);
			}

			// node ending
			$result .= '</' . $key . '>' . "\n";
		}
	}

	// non-array => string output
	else
		$result .= XMLEncode($array);

	// footer
	if($first)
	{
		$result .= '</' . $responseString . '>' . "\n";
	}

	if(!$return)
		echo $result;
	else
		return($result);

	return('');
}

/**
 * execute a module function
 *
 */
function ModuleFunction($function, $args = false)
{
	global $plugins;

	if(isset($plugins) && is_object($plugins))
	{
		if($args === false)
			$args = array();
		$args = array($function, false, false, $args);
		call_user_func_array(array(&$plugins, 'callFunction'), $args);
	}
	else
	{
		if(MAINTENANCE_MODE)
			return(false);

		// abort
		DisplayError(0x17, 'Illegal plugin handler call', 'b1gMail tried to call a plugin handler before the plugin system was intialized.',
			sprintf("Function:\n%s", $function),
			__FILE__,
			__LINE__);
		die();
	}
}

/**
 * get language file info
 *
 */
function GetLanguageInfo($file)
{
	global $bm_prefs, $cacheManager;

	$fileName = B1GMAIL_DIR . 'languages/' . $file . '.lang.php';
	$cacheKey = 'langInfo:' . $file;

	// in cache?
	$cacheData = $cacheManager->Get($cacheKey);
	if($cacheData && $cacheData['ctime'] == filemtime($fileName))
	{
		$cacheData['default'] = $bm_prefs['language'] == $file;
		return($cacheData);
	}

	// no -> read from file
	$result = array();
	$fp = @fopen($fileName, 'r');
	if(is_resource($fp))
	{
		while($line = fgets($fp))
		{
			if(substr($line, 0, strlen('// b1gMailLang::')) == '// b1gMailLang::')
			{
				$fields = explode('::', trim($line));
				list(, $langTitle,
						$langAuthor,
						$langAuthorMail,
						$langAuthorWeb,
						$langCharset,
						$langLocale,
						$langCode) = $fields;
				$result['ctime'] = filemtime($fileName);
				$result['title'] = $langTitle;
				$result['author'] = $langAuthor;
				$result['authorMail'] = $langAuthorMail;
				$result['authorWeb'] = $langAuthorWeb;
				$result['charset'] = $langCharset;
				$result['locale'] = $langLocale;
				$result['code'] = isset($fields[7]) ? $fields[7] : '';
				$result['writeable'] = is_writeable(B1GMAIL_DIR . 'languages/' . $file . '.lang.php');
				$result['default'] = $bm_prefs['language'] == $file;
				break;
			}
		}

		fclose($fp);
	}

	// put to cache
	$cacheManager->Set($cacheKey, $result);

	return($result);
}

/**
 * get template info array
 *
 * @param string $template Template folder name
 * @return array
 */
function GetTemplateInfo($template)
{
	global $lang_admin, $lang_user;

	if(!file_exists(B1GMAIL_DIR . 'templates/' . $template . '/info.php'))
		return(false);

	include(B1GMAIL_DIR . 'templates/' . $template . '/info.php');
	return($templateInfo);
}

/**
 * get template prefs
 *
 * @param string $template
 * @return array
 */
function GetTemplatePrefs($template)
{
	global $db;

	$info = GetTemplateInfo($template);
	if(!$info || !isset($info['prefs']) || !is_array($info['prefs']) || count($info['prefs']) == 0) return(false);

	$result = array();
	$res = $db->Query('SELECT `key`,`value` FROM {pre}templateprefs WHERE `template`=?', $template);
	while($row = $res->FetchArray(MYSQLI_ASSOC))
	{
		$result[$row['key']] = $row['value'];
	}
	$res->Free();

	foreach($info['prefs'] as $key=>$info)
	{
		if(!isset($result[$key])) $result[$key] = $info['default'];
	}

	return($result);
}

/**
 * get available templates
 *
 * @return array
 */
function GetAvailableTemplates()
{
	$result = array();

	$dir = @dir(B1GMAIL_DIR . 'templates/');
	if(is_object($dir))
	{
		while($file = $dir->read())
			if($file != '.'
				&& $file != '..'
				&& is_dir(B1GMAIL_DIR . 'templates/' . $file)
				&& file_exists(B1GMAIL_DIR . 'templates/' . $file . '/cache/')
				&& file_exists(B1GMAIL_DIR . 'templates/' . $file . '/info.php'))
		{
			$info = GetTemplateInfo($file);
			$result[$file] = $info;
		}
		$dir->close();
	}

	return($result);
}

/**
 * get available languages
 *
 * @return array
 */
function GetAvailableLanguages()
{
	global $bm_prefs, $currentLanguage;

	$result = array();

	$dir = @dir(B1GMAIL_DIR . 'languages/');
	if(is_object($dir))
	{
		while($file = $dir->read())
			if($file != '.' && $file != '..' && substr($file, -9) == '.lang.php')
				if($info = GetLanguageInfo(substr($file, 0, -9)))
				{
					if(strtolower($bm_prefs['language']) == strtolower(substr($file, 0, -9)))
						$info['default'] = true;
					$info['active'] = $currentLanguage == substr($file, 0, -9);
					$result[substr($file, 0, -9)] = $info;
				}
		$dir->close();
	}

	return($result);
}

/**
 * auto-detect language based on Accept-Language HTTP header
 * fall back to default language
 *
 * @return string
 */
function DetectLanguage()
{
	global $bm_prefs;

	if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		return($bm_prefs['language']);

	$availableLanguages = GetAvailableLanguages();
	$acceptLanguages = array_map('strtolower', array_filter(array_map('trim', explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']))));

	$availableCodes = array();
	foreach($availableLanguages as $lang=>$info)
	{
		if(!isset($info['code']) || empty($info['code']))
			continue;
		$availableCodes[ strtolower($info['code']) ] = $lang;
	}

	foreach($acceptLanguages as $lang)
	{
		if(($sPos = strpos($lang, ';')) !== false)
			$lang = substr($lang, 0, $sPos);
		if(($sPos = strpos($lang, '-')) !== false)
			$lang = substr($lang, 0, $sPos);

		if(isset($availableCodes[$lang]))
			return($availableCodes[$lang]);
	}

	return($bm_prefs['language']);
}

/**
 * determine language to load
 *
 * @return string
 */
function DetermineLanguage()
{
	if(isset($_SESSION['bm_sessionLanguage']))
		$language = $_SESSION['bm_sessionLanguage'];
	else if(isset($_COOKIE['bm_language']))
		$language = $_COOKIE['bm_language'];
	else
		$language = DetectLanguage();

	return($language);
}

/**
 * read language file
 *
 */
function ReadLanguage()
{
	global $db, $currentCharset, $lang_admin, $lang_user, $lang_client, $lang_custom, $bm_prefs, $currentLanguage, $lang_info, $cacheManager;

	// initialize arrays
	$lang_admin = $lang_user = $lang_client = $lang_custom = array();

	// determine language
	$language = DetermineLanguage();

	// remove bad characters
	$language = preg_replace('/([^a-zA-Z0-9\-\_]*)/', '', $language);

	// exists?
	if(!file_exists(B1GMAIL_DIR . 'languages/' . $language . '.lang.php'))
	{
		// fall back?
		if(file_exists(B1GMAIL_DIR . 'languages/' . $bm_prefs['language'] . '.lang.php'))
		{
			// yes, default language exists
			$language = $bm_prefs['language'];
		}
		else
		{
			// no, abort
			DisplayError(0x03, 'Language file not found', 'The requested language file and the default language file do not exist.',
				sprintf("Language file:\n%s", $bm_prefs['language'] . '.lang.php'),
				__FILE__,
				__LINE__);
			exit();
		}
	}

	// include
	$lang_info = GetLanguageInfo($language);
	$langCTime = $lang_info['ctime'];

	if(($langData = $cacheManager->Get('langData:' . $language))
		&& $langData['ctime'] == $langCTime)
	{
		$lang_admin		= $langData['admin'];
		$lang_user 		= $langData['user'];
		$lang_client 	= $langData['client'];
		$lang_custom 	= $langData['custom'];
		unset($langData);
	}
	else
	{
		if(!@include(B1GMAIL_DIR . 'languages/' . $language . '.lang.php'))
		{
			// parse error
			DisplayError(0x04, 'Language file invalid', 'Parse error while including requested language file (corrupt file?).',
				sprintf("Language file:\n%s", $language . '.lang.php'),
				__FILE__,
				__LINE__);
			exit();
		}

		$cacheManager->Set('langData:' . $language, array(
			'ctime'		=> $langCTime,
			'admin'		=> $lang_admin,
			'user'		=> $lang_user,
			'client'	=> $lang_client,
			'custom'	=> $lang_custom
		));
		$cacheManager->Delete('langCustom:' . $language);
	}

	// get info
	$currentLanguage = $language;
	$currentCharset = $lang_info['charset'];

	// locale
	$localeOK = false;
	$locales = explode('|', $lang_info['locale']);

	setlocale(LC_ALL, $locales);

	// charset
	if(strtolower($currentCharset) == 'utf-8' || strtolower($currentCharset) == 'utf8')
	{
		if(function_exists('mb_internal_encoding'))
			mb_internal_encoding('UTF-8');
		if(defined('DB_CHARSET') AND DB_CHARSET!='') {
			$db->SetCharset(DB_CHARSET);
		}
		else {
			$db->SetCharset('utf8');
		}
	}
	else
		$db->SetCharset('latin1'); // deprecated, will remove in b1gMail 7.5

	ReadConfig();

	// output charset
	//header('Connection: close');
	header('Content-Type: text/html; charset=' . $currentCharset);
	return(true);
}

/**
 * get custom language phrases
 *
 */
function GetCustomLanguage($language, $defaultCustomLang)
{
	global $db, $cacheManager;

	$result = $defaultCustomLang;

	// get custom texts
	if(!($lang_custom_cmgr = $cacheManager->Get('langCustom:' . $language)))
	{
		$res = $db->Query('SELECT `key`,`text` FROM {pre}texts WHERE language=?',
			$language);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$result[$row['key']] = $row['text'];
		$res->Free();

		$cacheManager->Set('langCustom:' . $language, $result);
	}
	else
	{
		foreach($lang_custom_cmgr as $key=>$val)
			$result[$key] = $val;
	}

	return $result;
}

/**
 * read custom language phrases
 *
 */
function ReadCustomLanguage()
{
	global $currentLanguage, $lang_custom;
	$lang_custom = GetCustomLanguage($currentLanguage, $lang_custom);
}

/**
 * decode mail header field
 *
 * @param string $text Text
 * @return string
 */
function DecodeMailHeaderField($text)
{
	$text = preg_replace('/\?=(\s*)=\?/', '?==?', $text);
	preg_match_all('/(=\?([^?]+)\?(Q|B)\?([^?]*)\?=)/i', $text, $ret_arr);
	if(!is_array($ret_arr) || !isset($ret_arr[3]) || count($ret_arr[3]) < 1)
		return($text);
	foreach($ret_arr[3] as $k=>$ret)
	{
		$str = $ret_arr[4][$k];
		switch(strtoupper($ret))
		{
		case 'Q':
			$str = quoted_printable_decode(str_replace('_', ' ', $str));
			break;
		case 'B':
			$str = base64_decode($str);
			break;
		default:
			break;
		}
		$str = CharsetDecode($str, $ret_arr[2][$k]);
		$text = str_replace($ret_arr[0][$k], $str, $text);
	}
	return($text);
}

/**
 * should $text be encoded?
 *
 * @param string $text Text
 * @return bool
 */
function ShouldEncodeMailHeaderFieldText($text)
{
	for($i = 0; $i < strlen($text); $i++)
	{
		$dec = ord($text[ $i ]);
		if(($dec < 32) || ($dec > 126))
			return(true);
	}
	return(false);
}

/**
 * encode mail header field
 *
 * @param string $text Text
 * @param string $charset Charset
 * @return string
 */
function EncodeMailHeaderField($text, $charset = '')
{
	global $currentCharset;

	// get charset, replace line feeds and line breaks
	if($charset == '')
		$charset = $currentCharset;
	$text = str_replace(array("\r", "\n"), '', $text);

	// check if string is 8bit or contains non-printable characters
	$encode = ShouldEncodeMailHeaderFieldText($text);

	// encode, if needed
	if($encode)
	{
		$fieldParts = array();
		$words = ExplodeOutsideOfQuotation($text, ' ', true);
		$i = 0;
		foreach($words as $word)
		{
			$encode = ShouldEncodeMailHeaderFieldText($word);

			if(isset($fieldParts[$i]))
			{
				if($fieldParts[$i][0] == $encode)
					$fieldParts[$i][1] .= ' ' . $word;
				else
					$fieldParts[++$i] = array($encode, $word);
			}
			else
				$fieldParts[$i] = array($encode, $word);
		}

		$encodedText = '';
		foreach($fieldParts as $fieldPart)
		{
			if($fieldPart[0])
				$encodedText .= ' ' . sprintf('=?%s?B?%s?=',
					$charset,
					base64_encode(trim($fieldPart[1])));
			else
				$encodedText .= ' ' . trim($fieldPart[1]);
		}

		return(trim($encodedText));
	}
	else
		return($text);
}

/**
 * write to log table
 *
 * @param string $entry
 * @param int $prio
 * @param string $at_file
 * @param int $at_line
 */
function PutLog($entry, $prio = PRIO_NOTE, $at_file = __FILE__, $at_line = __LINE__)
{
	global $db;

	if($prio == PRIO_DEBUG && !DEBUG)
		return;

	$sep = (strpos($at_file, '\\') !== false) ? '\\' : '/';
	$at_file = explode($sep, $at_file);
	$at_file = $at_file[ count($at_file)-1 ];

	$db->Query('INSERT INTO {pre}logs(eintrag, zeitstempel, prio) VALUES(?, UNIX_TIMESTAMP(), ?)',
		"(${at_file}:${at_line}) $entry",
		$prio);
}

/**
 * initialize modules
 *
 */
function InitializePlugins()
{
	global $plugins;

	$plugins = _new('BMPluginInterface');
	$plugins->loadPlugins();
}

/**
 * shutdown
 *
 */
function b1gMailShutdown()
{
	global $db, $tempFilesToReleaseAtShutdown;

	if(isset($tempFilesToReleaseAtShutdown) && is_array($tempFilesToReleaseAtShutdown))
	{
		foreach($tempFilesToReleaseAtShutdown as $item)
		{
			if(isset($item[2]))
				@fclose($item[2]);
			ReleaseTempFile($item[0], $item[1]);
		}
	}

	@mysqli_close($db->_handle);
}

/**
 * display error
 *
 * @param int $number
 * @param string $title
 * @param string $description
 * @param string $text
 * @param string $file
 * @param int $line
 */
function DisplayError($number, $title, $description, $text = false, $file = '', $line = '')
{
	if(INTERFACE_MODE)
	{
		if(isset($_SERVER['HTTP_USER_AGENT']))
			echo '<pre>';

		printf('%s (Error 0x%02X)' . "\n\n", $title, $number);
		printf('%s' . "\n\n", $description);

		if(DEBUG)
		{
			printf('Module: %s' . "\n" . 'Line: %s' . "\n" . '%s',
				str_replace(B1GMAIL_DIR, '', $file),
				$line,
				$text !== false && $text != '' ? "\n" . $text . "\n\n" : '');
		}

		printf('%s' . "\n\n", date('r'));

		if(isset($_SERVER['HTTP_USER_AGENT']))
			echo '</pre>';
	}
	else
	{
	?>
<html>
<head>
	<title>b1gMail: Error <?php echo(sprintf('0x%02X', $number)); ?></title>
	<style>
	<!--
		*			{ font-family: tahoma, arial, verdana; font-size: 12px; }
		H1			{ font-size: 16px; font-weight: bold; border-bottom: 1px solid #DDDDDD; }
		H2			{ font-size: 14px; font-weight: normal; }
		.addInfo	{ font-family: courier, courier new; font-size: 10px; height: 100px; overflow: auto;
						border: 1px solid #DDDDDD; padding: 5px; }
		.box		{ width: 600px; border: 1px solid #CCC; border-radius: 10px; background-color: #FFF;
						padding: 30px 15px; margin-top: 3em; margin-left: auto; margin-right: auto; }
	//-->
	</style>
	<link href="<?php echo(B1GMAIL_REL); ?>/clientlib/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#F1F2F6">

	<div class="box">
		<table width="100%">
			<tr>
				<td align="center" width="80" valign="top"><i class="fa fa-exclamation-triangle fa-5x" aria-hidden="true"></td>
				<td valign="top" align="left">

					<h1><?php echo(sprintf('%s (Error 0x%02X)', HTMLFormat($title), $number)); ?></h1>
					<h2><?php echo(HTMLFormat($description)); ?></h2>

					<hr size="1" color="#DDDDDD" width="100%" noshade="noshade" />

					<?php if(DEBUG) { ?>
					Additional information:<br /><br />

					<div class="addInfo">
						Module:<br /><?php echo(str_replace(B1GMAIL_DIR, '', $file)); ?><br /><br />
						Line:<br /><?php echo($line); ?><br /><br />
						<?php if($text !== false && $text != '') echo(nl2br(HTMLFormat($text))); ?>
					</div>

					<hr size="1" color="#DDDDDD" width="100%" noshade="noshade" />
					<?php } ?>
					A notification about this error has been sent to the administrator. The problem
					will be fixed as soon as possible.

					<br /><br />
					<input type="button" value="&nbsp; Try again &nbsp;" onclick="document.location.reload()" style="padding: 1px;" />
					<input type="button" value="&nbsp; Start page &nbsp;" onclick="document.location.href='<?php echo(B1GMAIL_REL); ?>';" style="padding: 1px;" />

				</td>
			</tr>
		</table>
	</div>

</body>
</html>
	<?php
	}
}

/**
 * connect to db
 *
 */
function ConnectDB()
{
	global $db, $mysql;

	// try to connect
	$mysqlHandle = @mysqli_connect($mysql['host'], $mysql['user'], $mysql['pass']);
	if($mysqlHandle)
	{
		if(@mysqli_select_db($mysqlHandle, $mysql['db']))
		{
			@mysqli_query($mysqlHandle, 'SET SESSION sql_mode=\'\'');
			$db = _new('DB', array($mysqlHandle));
			return(true);
		}
		else
		{
			$error = 1;
		}
	}
	else
	{
		$error = 0;
	}

	// failed
	DisplayError(0x01, 'Database error', 'Failed to connect to MySQL database backend.',
		sprintf("Process:\n%s\n\nError number:\n%d\n\nError description:\n%s",
			$error == 1 ? 'Database selection' : 'Connect',
			$error == 1 || !$mysqlHandle ? mysqli_connect_errno() : mysqli_errno($mysqlHandle),
			$error == 1 || !$mysqlHandle ? mysqli_connect_error() : mysqli_error($mysqlHandle)),
		__FILE__,
		__LINE__);
	exit();
}

/**
 * fetch configuration from db
 *
 */
function ReadConfig()
{
	global $bm_prefs, $db;

	$res = $db->Query('SELECT * FROM {pre}prefs LIMIT 1');
	if($res->RowCount() == 1)
		$bm_prefs = $res->FetchArray(MYSQLI_ASSOC);
	$res->Free();

	// for backward compatibility
	$bm_prefs['domains'] = GetDomainList();
}

/**
 * generate temp file name by file id
 *
 * @param int $id
 * @return string
 */
function TempFileName($tempID)
{
	if($tempID == -2)
		return('php://temp');
	return(B1GMAIL_DIR . 'temp/tmpfile_' . $tempID . '.tmp');
}

/**
 * cleanup temp files
 *
 * @return boolean
 */
function CleanupTempFiles()
{
	global $db;

	$res = $db->Query('SELECT id,user FROM {pre}tempfiles WHERE expires<'.time());
	while($row = $res->FetchArray())
		ReleaseTempFile($row['user'], $row['id']);
	$res->Free();

	return(true);
}

/**
 * check if temp file is valid and belongs to user
 *
 * @param int $userID
 * @param int $tempID
 * @return bool
 */
function ValidTempFile($userID, $tempID)
{
	global $db;

	$res = $db->Query('SELECT COUNT(*) FROM {pre}tempfiles WHERE id=? AND user=?',
		$tempID,
		$userID);
	list($count) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();

	return($count == 1);
}

/**
 * request new temp file id
 *
 * @param int $expires Expire date
 * @return int
 */
function RequestTempFile($userID, $expires = -1, $allowMem = false)
{
	global $db;

	if($allowMem)
		return(-2);

	// default expire timestamp (now + one day)
	if($expires == -1)
		$expires = time() + TIME_ONE_DAY;

	// register file
	$db->Query('INSERT INTO {pre}tempfiles(expires,user) VALUES(?,?)',
		$expires,
		$userID);
	$tempID = $db->InsertID();

	// return filename
	return($tempID);
}

/**
 * release and delete temp file
 *
 * @param int $tempID
 * @param int $userID
 * @return bool
 */
function ReleaseTempFile($userID, $tempID)
{
	global $db;

	if($tempID == -2)
		return(true);

	if(ValidTempFile($userID, $tempID))
	{
		// release
		$db->Query('DELETE FROM {pre}tempfiles WHERE id=? AND user=?',
			$tempID,
			$userID);

		// delete
		$fileName = TempFileName($tempID);
		if(file_exists($fileName))
			@unlink($fileName);

		// return
		return(true);
	}

	// return
	return(false);
}

/**
 * set timezone by zone name
 *
 * @param string $name Name
 * @return bool Success
 */
function SetTimeZoneByName($name)
{
	
	date_default_timezone_set($name);
	return(true);
}

/**
 * set timezone by GMT offset in seconds
 *
 * @param int $offset Offset
 * @return bool Success
 */
function SetTimeZoneByOffsetSeconds($offset)
{
	$timezones = array(
		'Pacific/Kwajalein',
		'Pacific/Samoa',
		'Pacific/Honolulu',
		'America/Juneau',
		'America/Los_Angeles',
		'America/Denver',
		'America/Mexico_City',
		'America/New_York',
		'America/Grenada',
		'America/St_Johns',
		'America/Argentina/Buenos_Aires',
		'America/Scoresbysund',
		'Atlantic/Azores',
		'Europe/London',
		'Europe/Berlin',
		'Europe/Helsinki',
		'Europe/Moscow',
		'Asia/Tehran',
		'Asia/Baku',
		'Asia/Kabul',
		'Asia/Karachi',
		'Asia/Calcutta',
		'Asia/Colombo',
		'Asia/Bangkok',
		'Asia/Singapore',
		'Asia/Tokyo',
		'Australia/Darwin',
		'Pacific/Guam',
		'Asia/Magadan',
		'Asia/Kamchatka'
    );

	foreach($timezones as $timezone)
	{
		SetTimeZoneByName($timezone);
		$thisOffset = date('Z');

		if($thisOffset == $offset)
			return(true);
	}

	return(false);
}