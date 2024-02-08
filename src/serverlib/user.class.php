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
 * user class
 */
class BMUser
{
	var $_id;
	var $_row;

	/**
	 * constructor
	 *
	 * @param int $id User ID
	 * @return BMUser
	 */
	function __construct($id)
	{
		$this->_id = $id;
		$this->_row = $this->Fetch();
	}

	/**
	 * get user's group
	 *
	 * @return BMGroup
	 */
	public function GetGroup()
	{
		return(_new('BMGroup', array($this->_row['gruppe'])));
	}

	/**
	 * check if user is allowed to send email (check send limit)
	 *
	 * @param $recipientCount Number of recipients
	 * @return bool
	 */
	public function MaySendMail($recipientCount)
	{
		global $db;

		$group = $this->GetGroup();
		$groupRow = $group->Fetch();

		if($recipientCount < 1)
			return(false);

		if($groupRow['send_limit_count'] <= 0 || $groupRow['send_limit_time'] <= 0)
			return(true);

		if($recipientCount > $groupRow['send_limit_count'])
			return(false);

		$res = $db->Query('SELECT SUM(`recipients`) FROM {pre}sendstats WHERE `userid`=? AND `time`>=?',
			$this->_id,
			time() - 60 * $groupRow['send_limit_time']);
		$row = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		$count = (int)$row[0];

		return($count + $recipientCount <= $groupRow['send_limit_count']);
	}

	/**
	 * add email to send stats (for send limit)
	 *
	 * @param $recipientCount Number of recipients
	 */
	public function AddSendStat($recipientCount)
	{
		global $db;

		$db->Query('INSERT INTO {pre}sendstats(`userid`,`recipients`,`time`) VALUES(?,?,?)',
			$this->_id,
			max(1, $recipientCount),
			time());
	}

	/**
	 * add email to receive stats (for incoming limits)
	 *
	 * @param int $size Size of email
	 */
	public function AddRecvStat($size)
	{
		global $db;

		$db->Query('INSERT INTO {pre}recvstats(`userid`,`size`,`time`) VALUES(?,?,?)',
			$this->_id,
			$size,
			time());
	}

	/**
	 * Get count of received mails since a certain time.
	 *
	 * @param int $since Start time
	 * @return int Count
	 */
	public function GetReceivedMailsCount($since)
	{
		global $db;

		$res = $db->Query('SELECT COUNT(*) FROM {pre}recvstats WHERE `userid`=? AND `time`>=?',
			$this->_id,
			$since);
		list($result) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return((int)$result);
	}

	/**
	 * Get size of received mails since a certain time.
	 *
	 * @param int $since Start time
	 * @return int Size in bytes
	 */
	public function GetReceivedMailsSize($since)
	{
		global $db;

		$res = $db->Query('SELECT SUM(`size`) FROM {pre}recvstats WHERE `userid`=? AND `time`>=?',
			$this->_id,
			$since);
		list($result) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return((int)$result);
	}

	/**
	 * get unread notifications count
	 *
	 * @return int
	 */
	public function GetUnreadNotifications()
	{
		global $db;

		$result = 0;

		$res = $db->Query('SELECT COUNT(*) FROM {pre}notifications WHERE `userid`=? AND `read`=0 AND (`expires`=0 OR `expires`<?)',
			$this->_id,
			time(),
			time());
		while($row = $res->FetchArray(MYSQLI_NUM))
		{
			$result = (int)$row[0];
		}
		$res->Free();

		return($result);
	}

	/**
	 * get latest notifications
	 *
	 * @param bool $markRead Whether to mark all notifications as read after fetching them
	 * @return array
	 */
	public function GetNotifications($markRead = true)
	{
		global $db, $tpl, $lang_custom;

		$result = array();

		$res = $db->Query('SELECT `notificationid`,`date`,`read`,`flags`,`text_phrase`,`text_params`,`link`,`icon` FROM {pre}notifications WHERE `userid`=? AND (`expires`=0 OR `expires`<?) ORDER BY `notificationid` DESC LIMIT ' . NOTIFICATION_LIMIT,
			$this->_id,
			time(),
			time());
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			switch ($row['icon']) {
				case '%%tpldir%%images/li/notify_newemail.png':
					$row['faIcon'] = str_replace('%%tpldir%%images/li/notify_newemail.png', 'fa-envelope-square', $row['icon']);
					break;
				case '%%tpldir%%images/li/notify_email.png':
					$row['faIcon'] = str_replace('%%tpldir%%images/li/notify_email.png', 'fa-envelope-o', $row['icon']);
					break;
				case '%%tpldir%%images/li/notify_birthday.png':
					$row['faIcon'] = str_replace('%%tpldir%%images/li/notify_birthday.png', 'fa-birthday-cake', $row['icon']);
					break;
				case '%%tpldir%%images/li/notify_calendar.png':
					$row['faIcon'] = str_replace('%%tpldir%%images/li/notify_calendar.png', 'fa-calendar', $row['icon']);
					break;
			}
			$row['icon'] = str_replace('%%tpldir%%', $tpl->tplDir, $row['icon']);

			if(($row['flags'] & NOTIFICATION_FLAG_USELANG) != 0)
				$row['text_phrase'] = $lang_custom[ $row['text_phrase'] ];

			$row['text'] = vsprintf($row['text_phrase'], ExplodeOutsideOfQuotation($row['text_params'], ','));

			$row['old'] = $row['read'] && $row['date'] < mktime(0, 0, 0);

			$result[] = $row;
		}
		$res->Free();

		if($markRead)
		{
			$db->Query('UPDATE {pre}notifications SET `read`=1 WHERE `userid`=? AND (`expires`=0 OR `expires`<?)',
				$this->_id,
				time(),
				time());
		}

		return($result);
	}

	/**
	 * post a new notification
	 *
	 * @param string $textPhrase Text phrase or key in $lang_custom array when used with NOTIFICATION_FLAG_USELANG
	 * @param array $textParams Parameters array for format string
	 * @param string $link Notification link
	 * @param string $icon Icon path (can use %%tpldir%% variable)
	 * @param int $date Notification date (0 = now)
	 * @param int $expires Expiration date (0 = never)
	 * @param int $flags Flags
	 * @param string $class Unique name of notification class (optional)
	 * @param bool $uniqueClass Set to true to remove all previous notifications of the same class
	 * @return int Notification ID
	 */
	public function PostNotification($textPhrase, $textParams = array(), $link = '', $icon = '', $date = 0, $expires = 0, $flags = 0, $class = '', $uniqueClass = false)
	{
		global $db;

		if($date == 0)
			$date = time();

		if(count($textParams))
			$textParams = '"' . implode('","', array_map('addslashes', $textParams)) . '"';
		else
			$textParams = '';

		if($uniqueClass && !empty($class))
		{
			$db->Query('DELETE FROM {pre}notifications WHERE `userid`=? AND `class`=?',
				$this->_id,
				$class);
		}

		$db->Query('INSERT INTO {pre}notifications(`userid`,`date`,`expires`,`flags`,`text_phrase`,`text_params`,`link`,`icon`,`class`) '
			. 'VALUES(?,?,?,?,?,?,?,?,?)',
			$this->_id,
			$date,
			$expires,
			$flags,
			$textPhrase,
			$textParams,
			$link,
			$icon,
			$class);
		return($db->InsertId());
	}

	/**
	 * check if user may see SMS stuff
	 *
	 * @return bool
	 */
	public function SMSEnabled()
	{
		global $bm_prefs, $db;

		$this->Fetch();

		$group = $this->GetGroup();
		$groupRow = $group->Fetch();

		$res = $db->Query('SELECT COUNT(*) FROM {pre}smsgateways');
		list($gatewayCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($gatewayCount > 0
				&& ($this->GetStaticBalance() > 0
					|| $groupRow['mail2sms'] == 'yes'
					|| $groupRow['sms_monat'] > 0
					|| $bm_prefs['sms_enable_charge'] == 'yes'));
	}

	/**
	 * update bayes training values for user
	 *
	 * @param int $nonSpam Count of NON spam mails
	 * @param int $spam Count of spam mails
	 * @param int $userID User ID
	 * @return bool
	 */
	public static function UpdateBayesValues($nonSpam, $spam, $userID)
	{
		global $db;

		$db->Query('UPDATE {pre}users SET bayes_nonspam=?, bayes_spam=? WHERE id=?',
			$nonSpam,
			$spam,
			$userID);

		return($db->AffectedRows() == 1);
	}

	/**
	 * get bayes training values for an user
	 *
	 * @param int $userID
	 * @return array bayes_nonspam, bayes_spam, bayes_border (%)
	 */
	public static function GetBayesValues($userID)
	{
		global $db;

		$res = $db->Query('SELECT bayes_nonspam,bayes_spam,bayes_border FROM {pre}users WHERE id=?',
			$userID);
		if($res->RowCount() == 1)
		{
			$ret = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
		}
		else
			$ret = array(0, 0, 90);

		return($ret);
	}

	/**
	 * set preference
	 *
	 * @param string $key Key
	 * @param string $value Value
	 * @return bool
	 */
	public function SetPref($key, $value)
	{
		global $db;
		$db->Query('REPLACE INTO {pre}userprefs(userID, `key`,`value`) VALUES(?, ?, ?)',
			(int)$this->_id,
			$key,
			$value);
		return($db->AffectedRows() == 1);
	}

	/**
	 * get preference
	 *
	 * @param string $key Key
	 * @return string
	 */
	public function GetPref($key)
	{
		global $db;
		$res = $db->Query('SELECT `value` FROM {pre}userprefs WHERE userID=? AND `key`=?',
			(int)$this->_id,
			$key);
		if($res->RowCount() == 1)
		{
			$row = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
			return($row[0]);
		}
		else
		{
			$res->Free();
			return(false);
		}
	}

	/**
	 * delete preference
	 *
	 * @param string $key Key
	 * @return bool
	 */
	public function DeletePref($key)
	{
		global $db;

		$db->Query('DELETE FROM {pre}userprefs WHERE userID=? AND `key`=?',
			(int)$this->_id,
			$key);
		return($db->AffectedRows() == 1);
	}

	/**
	 * check if address is locked
	 *
	 * @param string $userm
	 * @return bool
	 */
	public static function AddressLocked($userm)
	{
		global $db;

		$userm = strtolower($userm);
		$locked = false;
		$res = $db->Query('SELECT * FROM {pre}locked');
		while($row = $res->FetchObject())
		{
			$laenge = strlen($row->benutzername);
			$row->benutzername = strtolower($row->benutzername);

			if(($row->typ == 'start') && (preg_match('/^' . preg_quote($row->benutzername) . '/i', $userm)))
			{
				$locked = true;
			}
			else if(($row->typ == 'ende') && (preg_match('/' . preg_quote($row->benutzername). '$/i', $userm)))
			{
				$locked = true;
			}
			else if(($row->typ == 'mitte') && (strstr($userm, $row->benutzername) !== false))
			{
				$locked = true;
			}
			else if(($row->typ == 'gleich') && ($row->benutzername == $userm))
			{
				$locked = true;
			}
		}
		$res->Free();

		if(!$locked)
		{
			$res = $db->Query('SELECT COUNT(*) FROM {pre}workgroups WHERE email=?',
				$userm);
			list($wgCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			if($wgCount > 0)
				$locked = true;
		}

		return($locked);
	}

	/**
	 * check address availability
	 *
	 * @param string $address
	 * @return bool
	 */
	public static function AddressAvailable($address)
	{
		global $db;

		if(BMUser::GetID($address) != 0)
			return(false);

		$res = $db->Query('SELECT COUNT(*) FROM {pre}workgroups WHERE `email`=?',
						  $address);
		list($wgCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($wgCount == 0);
	}

	/**
	 * check address validity
	 *
	 * @param string $address
	 * @return bool
	 */
	public static function AddressValid($address, $forRegistration = true)
	{
		@list($preAt, $afterAt) = explode('@', $address);
		if(preg_match('/^[a-zA-Z0-9&\'\\.\\-_\\+]+@[a-zA-Z0-9.-]+\\.+[a-zA-Z]{2,12}$/', $address) == 1)
			if($forRegistration && (substr($preAt, -1) == '.' || substr($preAt, -1) == '_' || substr($preAt, -1) == '-' || substr($preAt, 0, 1) == '.' || substr($preAt, 0, 1) == '_' || substr($preAt, 0, 1) == '-'
				|| strpos($preAt, '..') !== false))
				return(false);
			else
				return(true);
		else
			return(false);
	}

	/**
	 * update last send timestamp and recipient count
	 *
	 * @param int $recipientCount Recipient count
	 * @return bool
	 */
	public function UpdateLastSend($recipientCount = 1)
	{
		global $db;
		$db->Query('UPDATE {pre}users SET last_send=?,sent_mails=sent_mails+? WHERE id=?',
			time(),
			(int)$recipientCount,
			$this->_id);
		return($db->AffectedRows() == 1);
	}

	/**
	 * update receive count
	 *
	 * @param int $mailCount Mail count
	 * @return bool
	 */
	public function UpdateLastReceive($mailCount = 1)
	{
		global $db;
		$db->Query('UPDATE {pre}users SET received_mails=received_mails+? WHERE id=?',
			(int)$mailCount,
			$this->_id);
		return($db->AffectedRows() == 1);
	}

	/**
	 * password reset / activation
	 *
	 * @param int $userID User ID
	 * @param string $resetKey Reset key
	 */
	public static function ResetPassword($userID, $resetKey)
	{
		global $db;

		$result = false;

		// prepare variables
		$userID = (int)$userID;
		$resetKey = trim($resetKey);

		// do not accept empty keys
		if(strlen($resetKey) == 32
			&& $userID > 0)
		{
			// check key, activate password
			$db->Query('UPDATE {pre}users SET passwort=pw_reset_new,pw_reset_new=?,pw_reset_key=? WHERE id=? AND LENGTH(pw_reset_new)=32 AND LENGTH(pw_reset_key)=32 AND pw_reset_key=?',
				'',
				'',
				$userID,
				$resetKey);
			$result = ($db->AffectedRows() == 1);
		}

		// log & return
		if($result)
		{
			// log
			PutLog(sprintf('Password reset for user <%d> confirmed (key: %s, IP: %s)',
				$userID,
				$resetKey,
				$_SERVER['REMOTE_ADDR']),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			return(true);
		}
		else
		{
			// log
			PutLog(sprintf('Password reset for user <%d> failed (key: %s, IP: %s)',
				$userID,
				$resetKey,
				$_SERVER['REMOTE_ADDR']),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			return(false);
		}
	}

	/**
	 * password reset request
	 *
	 * @param string $email User's E-Mail address
	 * @return bool
	 */
	public static function LostPassword($email)
	{
		global $db, $bm_prefs, $lang_custom;

		// user ID?
		$userID = BMUser::GetID($email, true);
		if($userID > 0)
		{
			// get alt. mail address
			$res = $db->Query('SELECT altmail,vorname,nachname,anrede,passwort_salt FROM {pre}users WHERE id=?',
				$userID);
			list($altMail, $firstName, $lastName, $salutation, $salt) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			// extract mail address
			$altMail = ExtractMailAddress($altMail);

			// alt mail specified?
			if(strlen(trim($altMail)) > 5)
			{
				// generate new password
				$pwResetNew = '';
				for($i=0; $i<PASSWORD_LENGTH; $i++)
					$pwResetNew .= substr(PASSWORD_CHARS, mt_rand(0, strlen(PASSWORD_CHARS)-1), 1);

				// generate key
				$pwResetKey = GenerateRandomKey('pwResetKey');

				// update row
				$db->Query('UPDATE {pre}users SET pw_reset_new=?,pw_reset_key=? WHERE id=?',
					md5(md5($pwResetNew).$salt),
					$pwResetKey,
					$userID);

				// link
				$vars = array(
					'mail'		=> DecodeEMail($email),
					'anrede'	=> ucfirst($salutation),
					'vorname'	=> $firstName,
					'nachname'	=> $lastName,
					'passwort'	=> $pwResetNew,
					'link'		=> sprintf('%sindex.php?action=resetPassword&user=%d&key=%s',
						$bm_prefs['selfurl'],
						$userID,
						$pwResetKey)
				);
				if(SystemMail($bm_prefs['passmail_abs'],
					$altMail,
					$lang_custom['passmail_sub'],
					'passmail_text',
					$vars))
				{
					// log
					PutLog(sprintf('User <%s> (%d) requested password reset (IP: %s)',
						$email,
						$userID,
						$_SERVER['REMOTE_ADDR']),
						PRIO_NOTE,
						__FILE__,
						__LINE__);
					return(true);
				}
			}
		}

		return(false);
	}

	/**
	 * create a new account
	 *
	 * @param string $email
	 * @param string $firstname
	 * @param string $surname
	 * @param string $street
	 * @param string $no
	 * @param string $zip
	 * @param string $city
	 * @param int $country
	 * @param string $phone
	 * @param string $fax
	 * @param string $altmail
	 * @param string $mobile_nr
	 * @param string $password
	 * @param array $profilefields
	 * @param bool $allowNotification
	 * @return int User ID
	 */
	public static function CreateAccount($email, $firstname, $surname, $street, $no, $zip, $city, $country, $phone, $fax, $altmail, $mobile_nr, $password, $profilefields = array(), $allowNotification = true, $c_uid = '', $salutation = '', $createLocked = false)
	{
		global $db, $bm_prefs, $currentCharset, $currentLanguage, $lang_custom;

		// serialize profile fields
		if(!is_array($profilefields))
			$profilefields = array();
		$profilefields = serialize($profilefields);

		// check if user already exists and if address is valid
		if(BMUser::AddressAvailable($email)
			&& BMUser::AddressValid($email))
		{
			$defaultGroupRow = (new BMGroup(-1))->Fetch($bm_prefs['std_gruppe']);
			$instantHTML = $defaultGroupRow['soforthtml'];

			// status?
			if(ADMIN_MODE)
			{
				$userStatus = 'no';
			}
			else
			{
				if($bm_prefs['reg_validation'] != 'off' || $createLocked)
					$userStatus = 'locked';
				else
					$userStatus = $bm_prefs['usr_status'];
			}

			// validation code?
			if(($bm_prefs['reg_validation'] == 'sms'
				&& trim($mobile_nr) != '')
			   || ($bm_prefs['reg_validation'] == 'email'
				&& trim($altmail) != ''))
			{
				$ValidationCode = '';
				for($i=0; $i<VALIDATIONCODE_LENGTH; $i++)
					$ValidationCode .= substr(VALIDATIONCODE_CHARS, mt_rand(0, strlen(VALIDATIONCODE_CHARS)-1), 1);
			}
			else
				$ValidationCode = '';

			// create salt
			$salt = GenerateRandomSalt(8);

			// create account
			$db->Query('INSERT INTO {pre}users(email,vorname,nachname,strasse,hnr,plz,ort,land,tel,fax,altmail,mail2sms_nummer,passwort,passwort_salt,gruppe,gesperrt,mail2sms,c_firstday,lastlogin,reg_ip,reg_date,profilfelder,datumsformat,charset,language,soforthtml,uid,sms_validation_code,sms_validation_last_send,sms_validation_send_times,anrede,preview) '
						. 'VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,\'no\',\'1\',?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
						$email,
						$firstname,
						$surname,
						$street,
						$no,
						$zip,
						$city,
						$country,
						$phone,
						$fax,
						$altmail,
						$mobile_nr,
						!LooksLikeMD5Hash($password) ? md5(md5($password).$salt) : md5($password.$salt),
						$salt,
						$bm_prefs['std_gruppe'],
						$userStatus,
						0,
						$_SERVER['REMOTE_ADDR'],
						time(),
						$profilefields,
						$bm_prefs['datumsformat'],
						$currentCharset,
						$currentLanguage,
						$instantHTML,
						$c_uid,
						$ValidationCode,
						$ValidationCode != '' ? time() : 0,
						0,
						$salutation,
						'yes');
			$uid = $db->InsertId();

			// prefs
			if($bm_prefs['hotkeys_default'] == 'yes')
			{
				$db->Query('INSERT INTO {pre}userprefs(`userid`,`key`,`value`) VALUES(?,?,?)',
					$uid,
					'hotkeys',
					1);
			}

			// send notify mail
			if($allowNotification && $bm_prefs['notify_mail'] == 'yes')
			{
				list(, $userDomain) = explode('@', $email);
				$countryList = CountryList();
				$countryName = $countryList[$country];
				$vars = array(
					'datum'		=> FormatDate(),
					'email'		=> DecodeEMail($email),
					'domain'	=> $userDomain,
					'anrede'	=> ucfirst($salutation),
					'name'		=> $surname . ', ' . $firstname,
					'strasse'	=> $street . ' ' . $no,
					'plzort'	=> $zip . ' ' . $city,
					'land'		=> $countryName . ' (#' . $country . ')',
					'tel'		=> $phone,
					'fax'		=> $fax,
					'altmail'	=> $altmail,
					'link'		=> sprintf('%sadmin/?jump=%s',
						$bm_prefs['selfurl'],
						urlencode(sprintf('users.php?do=edit&id=%d&', $uid)))
				);
				SystemMail($bm_prefs['passmail_abs'],
					$bm_prefs['notify_to'],
					$lang_custom['snotify_sub'],
					'snotify_text',
					$vars);
			}

			// send welcome mail
			if($bm_prefs['welcome_mail'] == 'yes')
			{
				list(, $userDomain) = explode('@', $email);
				$countryList = CountryList();
				$countryName = $countryList[$country];
				$vars = array(
					'datum'		=> FormatDate(),
					'email'		=> DecodeEMail($email),
					'domain'	=> $userDomain,
					'anrede'	=> ucfirst($salutation),
					'vorname'	=> $firstname,
					'nachname'	=> $surname,
					'strasse'	=> $street . ' ' . $no,
					'plzort'	=> $zip . ' ' . $city,
					'land'		=> $countryName . ' (#' . $country . ')',
					'tel'		=> $phone,
					'fax'		=> $fax,
					'altmail'	=> $altmail
				);
				SystemMail($bm_prefs['passmail_abs'],
					$email,
					$lang_custom['welcome_sub'],
					'welcome_text',
					$vars);
			}

			// send validation sms/mail?
			if($ValidationCode != '')
			{
				if($bm_prefs['reg_validation'] == 'sms')
				{
					if(!class_exists('BMSMS'))
						include(B1GMAIL_DIR . 'serverlib/sms.class.php');

					$smsText = GetPhraseForUser($uid, 'lang_custom', 'validationsms');
					$smsText = str_replace('%%code%%', $ValidationCode, $smsText);

					$sms = _new('BMSMS', array(0, false));
					$sms->Send($bm_prefs['mail2sms_abs'], preg_replace('/[^0-9]/', '', str_replace('+', '00', $mobile_nr)), $smsText, $bm_prefs['smsvalidation_type'], false, false);
				}
				else if($bm_prefs['reg_validation'] == 'email')
				{
					$vars = array(
						'activationcode'	=> $ValidationCode,
						'email'			=> DecodeEMail($email),
						'url'			=> sprintf('%sindex.php?action=activateAccount&id=%d&code=%s',
										$bm_prefs['selfurl'],
										$uid,
										$ValidationCode)
					);

					SystemMail($bm_prefs['passmail_abs'],
						   $altmail,
						   $lang_custom['activationmail_sub'],
						   'activationmail_text',
						   $vars);
				}
			}

			// module handler
			ModuleFunction('OnSignup', array($uid, $email));

			// log
			PutLog(sprintf('User <%s> (%d) created (adminMode: %d, allowNotification: %d, notification: %d, createLocked: %d, IP: %s)',
				$email,
				$uid,
				defined('ADMIN_MODE') && ADMIN_MODE ? 1 : 0,
				$allowNotification ?  1 : 0,
				$allowNotification && $bm_prefs['notify_mail'] == 'yes' ? 1 : 0,
				$createLocked ? 1 : 0,
				$_SERVER['REMOTE_ADDR']),
				PRIO_NOTE,
				__FILE__,
				__LINE__);

			return($uid);
		}

		return(false);
	}

	/**
	 * check if coupon is valid
	 *
	 * @param string $code Coupon code
	 * @param string $where Validation context (signup, loggedin)
	 * @return bool
	 */
	public static function CouponValid($code, $where = '')
	{
		global $db;

		$result = false;

		// get code from DB
		$res = $db->Query('SELECT id,code,von,bis,usedby,anzahl,used,ver,valid_signup,valid_loggedin FROM {pre}codes WHERE code=?',
			$code);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			// additional case comparison
			if($row['code'] == $code)
			{
				// check if coupon is active
				if(($row['von'] == -1 && $row['bis'] == -1)
					|| ($row['von'] == -1  && $row['bis'] >= time())
					|| ($row['von'] <= time() && $row['bis'] == -1)
					|| ($row['von'] <= time() && $row['bis'] >= time()))
				{
					if($where == ''
						|| ($where == 'signup' && $row['valid_signup'] == 'yes')
						|| ($where == 'loggedin' && $row['valid_loggedin'] == 'yes'))
					{
						$result = true;
					}
				}
			}
		}
		$res->Free();

		return($result);
	}

	/**
	 * redeem coupon
	 *
	 * @param string $code Coupon code
	 * @param string $where Redeem context (signup, loggedin)
	 * @return bool
	 */
	public function RedeemCoupon($code, $where = '')
	{
		global $db, $lang_user;

		$result = false;

		// get code from DB
		$res = $db->Query('SELECT id,code,von,bis,usedby,anzahl,used,ver,valid_signup,valid_loggedin FROM {pre}codes WHERE code=?',
			$code);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			// additional case comparison
			if($row['code'] == $code)
			{
				// check if coupon is active
				if(($row['von'] == -1 && $row['bis'] == -1)
					|| ($row['von'] == -1  && $row['bis'] >= time())
					|| ($row['von'] <= time() && $row['bis'] == -1)
					|| ($row['von'] <= time() && $row['bis'] >= time()))
				{
					if($where == ''
						|| ($where == 'signup' && $row['valid_signup'] == 'yes')
						|| ($where == 'loggedin' && $row['valid_loggedin'] == 'yes'))
					{
						$usedBy = @unserialize($row['usedby']);
						if(!is_array($usedBy))
							$usedBy = array();

						// check if user may use this coupon
						if(!in_array($this->_id, $usedBy)
							&& (($row['anzahl']==-1)
								|| ($row['anzahl'] > $row['used'])))
						{
							$benefit = @unserialize($row['ver']);

							if(is_array($benefit))
							{
								// activate benefits
								if($benefit['sms'] > 0)
									$this->Debit($benefit['sms'], sprintf($lang_user['tx_coupon'], $code));
								if($benefit['gruppe'] > 0)
								{
									$db->Query('UPDATE {pre}users SET gruppe=? WHERE id=?',
										$benefit['gruppe'],
										$this->_id);
								}

								// update coupon
								$usedBy[] = $this->_id;
								$db->Query('UPDATE {pre}codes SET used=used+1,usedby=? WHERE id=?',
									serialize($usedBy),
									$row['id']);

								// log
								PutLog(sprintf('User <%d> redeemed coupon <%s> (%d)',
									$this->_id,
									$code,
									$row['id']),
									PRIO_NOTE,
									__FILE__,
									__LINE__);

								// break
								$result = true;
								break;
							}
						}
					}
				}
			}
		}
		$res->Free();

		return($result);
	}

	/**
	 * get id for user identified by e-mail address
	 *
	 * @param string $email
	 * @param bool $excludeDeleted
	 * @param bool $isAlias Output indicating if $email is an alias
	 * @return int
	 */
	public static function GetID($email, $excludeDeleted = false, &$isAlias = null)
	{
		global $db;
		$userID = 0;

		// look in user-table
		$res = $db->Query('SELECT id FROM {pre}users WHERE email=? ' . ($excludeDeleted ? 'AND gesperrt!=\'delete\' ' : '') . 'LIMIT 1',
			$email);
		if($res->RowCount() == 1)
			list($userID) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// not in user-table -> alias?
		if($userID == 0)
		{
			$res = $db->Query('SELECT {pre}users.id AS id FROM {pre}users,{pre}aliase WHERE {pre}aliase.email=? AND ({pre}aliase.type&'.ALIAS_RECIPIENT.')!=0 AND {pre}users.id={pre}aliase.user ' . ($excludeDeleted ? 'AND {pre}users.gesperrt!=\'delete\' ' : '') . 'LIMIT 1',
				$email);
			if($res->RowCount() == 1)
			{
				list($userID) = $res->FetchArray(MYSQLI_NUM);
				$isAlias = true;
			}
			$res->Free();
		}
		else
			$isAlias = false;

		// return ID
		return($userID);
	}

	/**
	 * plugin auth helper
	 *
	 * @param string $email
	 * @param string $password
	 * @return mixed false or array
	 */
	private static function _pluginAuth($email, $passwordMD5, $passwordPlain)
	{
		global $plugins;

		// prepare variables
		$userParts 	= explode('@', trim($email));
		$userName 	= isset($userParts[0]) ? $userParts[0] : '';
		$userDomain = isset($userParts[1]) ? $userParts[1] : '';

		// search for an auth handler
		foreach($plugins->_plugins as $className=>$pluginInfo)
		{
			if(($result = $plugins->callFunction('OnAuthenticate',  $className, false,
							array($userName,
							$userDomain,
							$passwordMD5,
							$passwordPlain))) !== false
				&& is_array($result))
			{
				return($result);
			}
		}

		// no auth handler useful
		return(false);
	}

	/**
	 * activate a user account using it's activation code
	 *
	 * @param int $id User ID
	 * @param string $code Code
	 * @return bool Success
	 */
	public static function ActivateAccount($id, $code)
	{
		global $db, $bm_prefs;

		$id = (int)$id;
		$code = trim($code);

		// check code length
		if(strlen($code) != VALIDATIONCODE_LENGTH)
			return(false);

		// get user row
		$res = $db->Query('SELECT `email`,`sms_validation_code` FROM {pre}users WHERE `id`=?',
				  $id);
		if($res->RowCount() != 1)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// check if validation is required
		if(!BMUser::RequiresValidation($row['email']))
			return(false);

		// validation code ok?
		if(strtoupper(trim($code)) == strtoupper($row['sms_validation_code']))
		{
			$db->Query('UPDATE {pre}users SET `sms_validation_code`=?,`gesperrt`=?,`sms_validation_time`=?,`sms_validation`=? WHERE `id`=?',
				'',
				'no',
				time(),
				$bm_prefs['reg_validation'] == 'sms' ? time() : 0,
				$id);
			return(true);
		}
	}

	/**
	 * login a user
	 *
	 * @param string $email E-Mail
	 * @param string $passwordPlain Password (PLAIN)
	 * @param bool $createSession Create session?
	 * @param bool $successLog Log successful logins?
	 * @return string Session-ID
	 */
	public static function Login($email, $passwordPlain, $createSession = true, $successLog = true, $ValidationCode = '', $skipSalting = false)
	{
		global $db, $currentCharset, $currentLanguage, $bm_prefs;

		$passwordPlain = CharsetDecode($passwordPlain, false, 'ISO-8859-15');
		$result = array(USER_DOES_NOT_EXIST, false);
		$row = false;
		$userID = 0;
		$password = LooksLikeMD5Hash($passwordPlain) ? $passwordPlain : md5($passwordPlain);

		// try plugin authentication first
		$pluginAuth = BMUser::_pluginAuth($email, $password, $passwordPlain);

		// no plugin auth
		if(!is_array($pluginAuth))
		{
			// get user ID
			$userID = BMUser::GetID($email,false,$isAlias);
			$aliaslogin='no';
			if($isAlias === true) { // Check if alias login is allowed
				$res = $db->Query('SELECT login FROM {pre}aliase WHERE user=? AND email=? LIMIT 1',
				$userID,$email);
				list($aliaslogin) = $res->FetchArray(MYSQLI_NUM);
				if($aliaslogin=='no') $userID=0; // Set userID to empty
			}
			$res = $db->Query('SELECT id,gesperrt,passwort,passwort_salt,email,last_login_attempt,sms_validation_code,ip,lastlogin,preferred_language,last_timezone FROM {pre}users WHERE id=? LIMIT 1',
				$userID);
			$row = $res->FetchArray();
			$res->Free();
		}

		// plugin auth
		else
		{
			// find user
			$res = $db->Query('SELECT id,gesperrt,passwort,passwort_salt,email,last_login_attempt,sms_validation_code,ip,lastlogin,preferred_language,last_timezone FROM {pre}users WHERE uid=? LIMIT 1',
				$pluginAuth['uid']);
			if($res->RowCount() == 1)
			{
				$row = $res->FetchArray();
				$res->Free();

				// vars
				$row['passwort'] = md5($password.$row['passwort_salt']);
				$userID = $row['id'];

				// update profile
				if(isset($pluginAuth['profile'])
					&& $row['gesperrt'] == 'no')
				{
					$theOldUserRow = $theUserRow = BMUser::staticFetch($row['id']);

					$theUserRow['passwort'] = md5($password.$row['passwort_salt']);
					foreach($pluginAuth['profile'] as $key=>$val)
						$theUserRow[$key] = $val;

					if($theOldUserRow != $theUserRow)
						BMUser::UpdateContactData($theUserRow, false, true, $userID);
				}
			}
		}

		if(isset($row) && $userID > 0)
		{
			$adminAuthOK = false;
			if(isset($_REQUEST['adminAuth']))
			{
				$adminAuth = @explode(',', @base64_decode($_REQUEST['adminAuth']));

				if(is_array($adminAuth) && count($adminAuth) == 3 && $adminAuth[0] == $userID)
				{
					$ares = $db->Query('SELECT * FROM {pre}admins WHERE `adminid`=?', $adminAuth[1]);
					while($arow = $ares->FetchArray(MYSQLI_ASSOC))
					{
						$adminPrivs = @unserialize($arow['privileges']);
						if(!is_array($adminPrivs)) $adminPrivs = array();
						if($arow['type'] != 0 && !in_array('users', $adminPrivs)) continue;

						$correctToken = md5(sprintf('%d,%d', $userID, $adminAuth[1]).md5($arow['password'].$_SERVER['HTTP_USER_AGENT']));

						if($correctToken === $adminAuth[2])
							$adminAuthOK = true;
					}
					$ares->Free();
				}
			}

			if($skipSalting)
			{
				$saltedPassword = $passwordPlain;
			}
			else
			{
				$saltedPassword = md5($password.$row['passwort_salt']);
			}

			// user exists
			if((strtolower($row['passwort']) === strtolower($saltedPassword) || $adminAuthOK)
				&& ($row['last_login_attempt'] < 100 || $row['last_login_attempt']+ACCOUNT_LOCK_TIME < time()))
			{
				// validation unlock?
				if($ValidationCode != ''
					&& BMUser::RequiresValidation($email))
				{
					if(BMUser::ActivateAccount($userID, $ValidationCode))
						$row['gesperrt'] = 'no';
				}

				// password ok
				if($row['gesperrt'] == 'no')
				{
					if(isset($row['preferred_language']) && !empty($row['preferred_language']))
						$userLanguage = $row['preferred_language'];
					else
						$userLanguage = false;

					$availableLanguages = GetAvailableLanguages();
					if(!isset($availableLanguages[$userLanguage]))
						$userLanguage = false;

					// okay => update user row
					$db->Query('UPDATE {pre}users SET ip=?,lastlogin=?,last_login_attempt=0,charset=?,language=?,last_timezone=? WHERE id=?',
						$adminAuthOK ? $row['ip'] : $_SERVER['REMOTE_ADDR'],
						$adminAuthOK ? $row['lastlogin'] : time(),
						$currentCharset,
						$userLanguage ? $userLanguage : $currentLanguage,
						isset($_SESSION['bm_timezone']) ? (int)$_SESSION['bm_timezone'] : (isset($_REQUEST['timezone']) ? $_REQUEST['timezone'] : $row['last_timezone']),
						$userID);

					// create session
					if($createSession)
					{
						@session_start();
						$sessionID = session_id();

						if($bm_prefs['cookie_lock'] == 'yes')
						{
							$sessionSecret = GenerateRandomKey('sessionSecret');
							setcookie('sessionSecret_'.substr($sessionID, 0, 16), $sessionSecret, 0, '/');
							$_COOKIE['sessionSecret_'.substr($sessionID, 0, 16)] = $sessionSecret;
						}

						$_SESSION['bm_userLoggedIn']	= true;
						$_SESSION['bm_userID']			= $userID;
						$_SESSION['bm_loginTime']		= time();
						$_SESSION['bm_sessionToken']	= SessionToken();
						$_SESSION['bm_xorCryptKey']		= BMUser::GenerateXORCryptKey($userID, $passwordPlain);

						if($userLanguage)
							$_SESSION['bm_sessionLanguage'] = $userLanguage;
					}
					else
						$sessionID = $userID;

					// set result
					$result = array(USER_OK, $sessionID);
					ModuleFunction('OnLogin', array($userID));
				}
				else
				{
					// locked
					$result = array(USER_LOCKED, false);
					ModuleFunction('OnLoginFailed', array($email, $password, BM_LOCKED));
				}
			}
			else
			{
				// bad password or login lock
				$result = array(USER_BAD_PASSWORD, false);
				ModuleFunction('OnLoginFailed', array($email, $password, BM_WRONGLOGIN));

				// bruteforce login protection
				$lastLoginAttempt = $row['last_login_attempt'];
				if($lastLoginAttempt < 100)
				{
					// register new attempt
					$result = array(USER_BAD_PASSWORD, $lastLoginAttempt+1);
					if(++$lastLoginAttempt >= 5)
						$lastLoginAttempt = time();
					$db->Query('UPDATE {pre}users SET last_login_attempt=? WHERE id=?',
						$lastLoginAttempt,
						$userID);
				}
				else
				{
					// account still locked
					$lockedUntil = $lastLoginAttempt + ACCOUNT_LOCK_TIME;
					if($lockedUntil < time())
					{
						// first attempt
						$db->Query('UPDATE {pre}users SET last_login_attempt=? WHERE id=?',
							1,
							$userID);
						$result = array(USER_BAD_PASSWORD, 1);
					}
					else
					{
						// locked
						$result = array(USER_LOGIN_BLOCK, $lockedUntil);
					}
				}
			}
		}

		// log
		if($result[0] != USER_OK || $successLog)
			PutLog(sprintf('Login attempt as <%s> %s (%s; IP: %s)',
							$email,
							$result[0] == USER_OK ? 'succeeded' : 'failed',
							($result[0] == USER_LOGIN_BLOCK ? 'account locked because of too many login attempts'
						:	($result[0] == USER_BAD_PASSWORD ? 'bad password'
						:	($result[0] == USER_OK ? 'success'
						:	($result[0] == USER_DOES_NOT_EXIST ? 'user does not exist'
						:	($result[0] == USER_LOCKED ? 'account locked'
						:	'unknown reason'))))),
							$_SERVER['REMOTE_ADDR']), PRIO_NOTE, __FILE__, __LINE__);
		return($result);
	}

	/**
	 * log out
	 *
	 */
	public function Logout()
	{
		ModuleFunction('OnLogout', array($_SESSION['bm_userID']));

		$_SESSION['bm_userLoggedIn']	= false;
		$_SESSION['bm_userID']			= -1;

		if(!isset($_SESSION['bm_adminLoggedIn']))
		{
			if(isset($_COOKIE['sessionSecret_'.substr(session_id(), 0, 16)]))
				setcookie('sessionSecret_'.substr(session_id(), 0, 16), '', time()-TIME_ONE_HOUR, '/');
			session_destroy();
		}
	}

	/**
	 * check if user account requires validation
	 *
	 * @param string $email User account e-mail
	 * @return bool
	 */
	public static function RequiresValidation($email)
	{
		global $db;

		$res = $db->Query('SELECT `gesperrt`,`sms_validation_code` FROM {pre}users WHERE `email`=?',
			$email);
		if($res->RowCount() != 1)
			return(false);
		list($userStatus, $ValidationCode) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($userStatus == 'locked' && strlen($ValidationCode) == VALIDATIONCODE_LENGTH);
	}

	/**
	 * validate mobile no
	 *
	 * @param string $code Validation code
	 * @return bool
	 */
	public function ValidateMobileNo($code)
	{
		global $db;

		$res = $db->Query('SELECT `sms_validation_code`,`sms_validation` FROM {pre}users WHERE `id`=?',
			$this->_id);
		list($smsValidationCode, $smsValidation) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		if(trim(strtoupper($code)) == strtoupper($smsValidationCode) && strlen($smsValidationCode) == VALIDATIONCODE_LENGTH)
		{
			$db->Query('UPDATE {pre}users SET `sms_validation_code`=?,`sms_validation`=? WHERE `id`=?',
				'',
				time(),
				$this->_id);
			return(true);
		}

		return(false);
	}

	/**
	 * fetch a user row (assoc)
	 *
	 * @param int $id
	 * @return array
	 */
	public function Fetch($id = -1, $re = false)
	{
		global $db;

		if($id == -1)
		{
			$id = $this->_id;
			if(!$re && is_array($this->_row))
				return($this->_row);
		}

		$res = $db->Query('SELECT * FROM {pre}users WHERE id=?',
			$id);
		if($res->RowCount() == 0)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return($row);
	}

	/**
	 * fetch a user row (static function)
	 *
	 * @param int $id
	 * @return array
	 */
	public static function staticFetch($id)
	{
		global $db;
		if(!isset($id)) 
			return false;
		$res = $db->Query('SELECT * FROM {pre}users WHERE id=?',
			$id);
		if($res->RowCount() == 0)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return($row);
	}

	/**
	 * refresh user row
	 *
	 * @return array
	 */
	public function ReFetch()
	{
		$this->_row = $this->Fetch(-1, true);
		return($this->_row);
	}

	/**
	 * get user's pop3 accounts
	 *
	 * @param string $sortColumn
	 * @param string $sortOrder
	 * @return array
	 */
	public function GetPOP3Accounts($sortColumn = 'p_user', $sortOrder = 'ASC', $activeOnly = false)
	{
		global $db;

		$accounts = array();
		$res = $db->Query('SELECT id,p_host,p_user,p_pass,p_target,p_port,p_keep,last_fetch,last_success,p_ssl,paused FROM {pre}pop3 WHERE user=? '
							. ($activeOnly ? 'AND `paused`=\'no\' ' : '')
							. 'ORDER BY ' . $sortColumn . ' ' . $sortOrder,
			$this->_id);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$accounts[$row['id']] = array(
				'id'			=> $row['id'],
				'p_host'		=> $row['p_host'],
				'p_user'		=> $row['p_user'],
				'p_pass'		=> $row['p_pass'],
				'p_target'		=> $row['p_target'],
				'p_port'		=> $row['p_port'],
				'p_keep'		=> $row['p_keep'] == 'yes',
				'p_ssl'			=> $row['p_ssl'] == 'yes',
				'paused'		=> $row['paused'] == 'yes',
				'last_fetch'	=> $row['last_fetch'],
				'last_success'	=> $row['last_success']
			);
		$res->Free();

		return($accounts);
	}

	/**
	 * get pop3 account
	 *
	 * @param int $id
	 * @return array
	 */
	public function GetPOP3Account($id)
	{
		global $db;

		$res = $db->Query('SELECT id,p_host,p_user,p_pass,p_target,p_port,p_keep,last_fetch,p_ssl,paused FROM {pre}pop3 WHERE id=? AND user=?',
			$id,
			$this->_id);
		if($res->RowCount() == 0)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$result = array(
				'id'			=> $row['id'],
				'p_host'		=> $row['p_host'],
				'p_user'		=> $row['p_user'],
				'p_pass'		=> $row['p_pass'],
				'p_target'		=> $row['p_target'],
				'p_port'		=> $row['p_port'],
				'p_keep'		=> $row['p_keep'] == 'yes',
				'p_ssl'			=> $row['p_ssl'] == 'yes',
				'last_fetch'	=> $row['last_fetch'],
				'paused'		=> $row['paused'] == 'yes'
			);
		return($result);
	}

	public function UpdatePOP3Account($id, $p_host, $p_user, $p_pass, $p_target, $p_port, $p_keep, $p_ssl, $paused)
	{
		global $db;

		$db->Query('UPDATE {pre}pop3 SET p_host=?,p_user=?,p_pass=?,p_target=?,p_port=?,p_keep=?,p_ssl=?,paused=? WHERE id=? AND user=?',
			$p_host,
			$p_user,
			$p_pass,
			(int)$p_target,
			(int)$p_port,
			$p_keep ? 'yes' : 'no',
			$p_ssl ? 'yes' : 'no',
			$paused ? 'yes' : 'no',
			(int)$id,
			$this->_id);
		return($db->AffectedRows() == 1);
	}

	/**
	 * add pop3 account
	 *
	 * @param string $p_host
	 * @param string $p_user
	 * @param string $p_pass
	 * @param int $p_target
	 * @param int $p_port
	 * @param bool $p_keep
	 * @param bool $p_ssl
	 * @return int
	 */
	public function AddPOP3Account($p_host, $p_user, $p_pass, $p_target, $p_port, $p_keep, $p_ssl = false)
	{
		global $db;

		$db->Query('INSERT INTO {pre}pop3(user,p_host,p_user,p_pass,p_target,p_port,p_keep,p_ssl) '
					. 'VALUES(?,?,?,?,?,?,?,?)',
					$this->_id,
					$p_host,
					$p_user,
					$p_pass,
					(int)$p_target,
					(int)$p_port,
					$p_keep ? 'yes' : 'no',
					$p_ssl ? 'yes' : 'no');
		return($db->InsertId());
	}

	/**
	 * delete pop3 account
	 *
	 * @param int $id Account ID
	 * @return bool
	 */
	public function DeletePOP3Account($id)
	{
		global $db;

		$db->Query('DELETE FROM {pre}pop3 WHERE id=? AND user=?',
			$id,
			$this->_id);

		if($db->AffectedRows() == 1)
		{
			$db->Query('DELETE FROM {pre}uidindex WHERE pop3=?',
				$id);
			return(true);
		}
		else
		{
			return(false);
		}
	}

	/**
	 * get user's signatures
	 *
	 * @param string $sortColumn
	 * @param string $sortOrder
	 * @return array
	 */
	public function GetSignatures($sortColumn = 'titel', $sortOrder = 'ASC')
	{
		global $db, $lang_user;

		$signatures = array();
		$res = $db->Query('SELECT id,titel,text,html FROM {pre}signaturen WHERE user=? '
							. 'ORDER BY ' . $sortColumn . ' ' . $sortOrder,
			$this->_id);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$signatures[$row['id']] = $row;
		$res->Free();

		return($signatures);
	}

	/**
	 * delete signature
	 *
	 * @param int $signatureID Signature ID
	 * @return bool
	 */
	public function DeleteSignature($signatureID)
	{
		global $db;

		// delete
		$db->Query('DELETE FROM {pre}signaturen WHERE id=? AND user=?',
			(int)$signatureID,
			$this->_id);

		// return
		return($db->AffectedRows() == 1);
	}

	/**
	 * get signature
	 *
	 * @param int $signatureID Signature ID
	 * @return array
	 */
	public function GetSignature($signatureID)
	{
		global $db;

		// get signature
		$res = $db->Query('SELECT id,titel,text,html FROM {pre}signaturen WHERE id=? AND user=?',
			$signatureID,
			$this->_id);
		if($res->RowCount() != 1)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return($row);
	}

	/**
	 * update signature
	 *
	 * @param int $id Signature ID
	 * @param string $title Title
	 * @param string $text Text
	 * @param string $html HTML
	 * @return bool
	 */
	public function UpdateSignature($signatureID, $title, $text, $html)
	{
		global $db;

		$db->Query('UPDATE {pre}signaturen SET titel=?,text=?,html=? WHERE id=? AND user=?',
			$title,
			$text,
			$html,
			$signatureID,
			$this->_id);

		return($db->AffectedRows() == 1);
	}

	/**
	 * add signature
	 *
	 * @param string $title Title
	 * @param string $text Text
	 * @param string $html HTML
	 * @return int
	 */
	public function AddSignature($title, $text, $html)
	{
		global $db;

		$db->Query('INSERT INTO {pre}signaturen(user,titel,text,html) VALUES(?,?,?,?)',
			$this->_id,
			$title,
			$text,
			$html);

		return($db->InsertId());
	}

	/**
	 * get user's aliases
	 *
	 * @param string $sortColumn
	 * @param string $sortOrder
	 * @return array
	 */
	public function GetAliases($sortColumn = 'email', $sortOrder = 'ASC')
	{
		global $db, $lang_user;

		$aliases = array();
		$res = $db->Query('SELECT id,email,type,sendername,login FROM {pre}aliase WHERE user=? '
							. 'ORDER BY ' . $sortColumn . ' ' . $sortOrder,
			$this->_id);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$typeTexts = array();
			if($row['type'] & ALIAS_SENDER)
				$typeTexts[] = $lang_user['aliastype_1'];
			if($row['type'] & ALIAS_RECIPIENT)
				$typeTexts[] = $lang_user['aliastype_2'];
			if($row['type'] & ALIAS_PENDING)
				$typeTexts = array($lang_user['aliastype_4']);
			$row['typeText'] = implode(', ', $typeTexts);
			$aliases[$row['id']] = $row;
		}
		$res->Free();

		return($aliases);
	}

	/**
	 * get possible senders
	 *
	 * @return array
	 */
	public function GetPossibleSenders()
	{
		$senders = array();
		$aliases = $this->GetAliases();
		$worgroups = $this->GetWorkgroups(false);

		$senders[] = sprintf('<%s>' ,
						$this->_row['email']);
		foreach($aliases as $alias)
			if(($alias['type']&ALIAS_SENDER) != 0
				&& ($alias['type']&ALIAS_PENDING) == 0)
				$senders[] = sprintf('<%s>',
								$alias['email']);

		foreach($worgroups as $workgroup)
			$senders[] = sprintf('<%s>',
							$workgroup['email']);

		if(trim($this->_row['absendername']) != '')
			$senders[] = sprintf('"%s" <%s>',
							$this->_row['absendername'],
							$this->_row['email']);
		else
			$senders[] = sprintf('"%s %s" <%s>',
							$this->_row['vorname'],
							$this->_row['nachname'],
							$this->_row['email']);

		foreach($aliases as $alias)
			if(($alias['type']&ALIAS_SENDER) != 0
				&& ($alias['type']&ALIAS_PENDING) == 0)
					if(trim($alias['sendername']) != '')
						$senders[] = sprintf('"%s" <%s>',
										$alias['sendername'],
										$alias['email']);
					else if(trim($this->_row['absendername']) != '')
						$senders[] = sprintf('"%s" <%s>',
										$this->_row['absendername'],
										$alias['email']);
					else
						$senders[] = sprintf('"%s %s" <%s>',
										$this->_row['vorname'],
										$this->_row['nachname'],
										$alias['email']);

		foreach($worgroups as $workgroup)
			$senders[] = sprintf('"%s" <%s>',
							$workgroup['title'],
							$workgroup['email']);

		return($senders);
	}

	/**
	 * get default sender
	 *
	 * @return string
	 */
	public function GetDefaultSender()
	{
		$senders = $this->GetPossibleSenders();
		return(isset($senders[$this->_row['defaultSender']])
			? $senders[$this->_row['defaultSender']]
			: array_shift($senders));
	}

	/**
	 * set default sender
	 *
	 * @param int $senderID Sender ID (from possible senders table)
	 * @return bool
	 */
	public function SetDefaultSender($senderID)
	{
		global $db;

		$db->Query('UPDATE {pre}users SET defaultSender=? WHERE id=?',
			$senderID,
			$this->_id);
		return($db->AffectedRows() == 1);
	}

	/**
	 * delete an alias
	 *
	 * @param int $aliasID Alias ID
	 * @return bool
	 */
	public function DeleteAlias($aliasID)
	{
		global $db;

		// sender
		$defaultSender = $this->GetDefaultSender();

		// get email
		$res = $db->Query('SELECT email FROM {pre}aliase WHERE id=? AND user=?',
			(int)$aliasID,
			$this->_id);
		assert('$res->RowCount() != 0');
		list($aliasEMail) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// delete
		$db->Query('DELETE FROM {pre}aliase WHERE id=? AND user=?',
			(int)$aliasID,
			$this->_id);

		// save sender
		$possibleSenders = $this->GetPossibleSenders();
		foreach($possibleSenders as $senderID=>$senderString)
			if($defaultSender == $senderString)
			{
				$this->SetDefaultSender($senderID);
				break;
			}

		// log
		PutLog(sprintf('User <%s> (%d) deleted alias <%s> (%d)',
			$this->_row['email'],
			$this->_id,
			$aliasEMail,
			$aliasID),
			PRIO_NOTE,
			__FILE__,
			__LINE__);

		// return
		return($db->AffectedRows() == 1);
	}

	/**
	 * confirm an alias
	 *
	 * @param int $id Alias ID
	 * @param string $code Confirmation code
	 * @return bool
	 */
	public static function ConfirmAlias($id, $code)
	{
		global $db;

		// get user id
		$res = $db->Query('SELECT `user` FROM {pre}aliase WHERE `id`=? AND `code`=? AND `code`!=?',
			$id,
			$code,
			'');
		if($res->RowCount() != 1)
			return(false);
		list($userID) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// store sender
		$_obj = _new('BMUser', array($userID));
		if($_obj->_row === false)
			return(false);
		$defaultSender = $_obj->GetDefaultSender();

		$db->Query('UPDATE {pre}aliase SET type=(type^'.ALIAS_PENDING.'),code=? WHERE id=? AND code=? AND code!=?',
			'',
			$id,
			$code,
			'');
		// log
		if($db->AffectedRows() == 1)
		{
			// save sender
			$possibleSenders = $_obj->GetPossibleSenders();
			foreach($possibleSenders as $senderID=>$senderString)
				if($defaultSender == $senderString)
				{
					$_obj->SetDefaultSender($senderID);
					break;
				}

			PutLog(sprintf('External alias <%d> confirmed with code <%s> from <%s>',
				$id,
				$code,
				$_SERVER['REMOTE_ADDR']),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			return(true);
		}
		return(false);
	}

	/**
	 * add an alias
	 *
	 * @param string $email Alias e-mail address
	 * @param int $type Alias type
	 * @param string $sendername Alias name
	 * @return in
	 */
	public function AddAlias($email, $type, $sendername='',$aliaslogin='no')
	{
		global $db, $lang_custom, $bm_prefs, $thisUser;

		$result = 0;

		// default sender
		$defaultSender = $this->GetDefaultSender();

		//
		// internal alias
		//
		if($type == (ALIAS_RECIPIENT|ALIAS_SENDER))
		{
			// add
			$db->Query('INSERT INTO {pre}aliase(email,user,type,date,sendername,login) VALUES(?,?,?,?,?,?)',
				$email,
				$this->_id,
				$type,
				time(),
				$sendername,
				$aliaslogin);
			$id = $db->InsertId();

			// log
			PutLog(sprintf('User <%s> (%d) created internal alias <%s> (%d)',
				$this->_row['email'],
				$this->_id,
				$email,
				$id),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			$result = $id;
		}

		//
		// external alias
		//
		else if($type == ALIAS_SENDER)
		{
			// add
			$code = GenerateRandomKey('aliasCode');
			$db->Query('INSERT INTO {pre}aliase(email,user,type,code,date,sendername) VALUES(?,?,?,?,?,?)',
				$email,
				$this->_id,
				$type|ALIAS_PENDING,
				$code,
				time(),
				$sendername);
			$id = $db->InsertId();

			// send mail
			$link = $bm_prefs['selfurl'] . 'index.php?action=confirmAlias&id=' . $id . '&code=' . $code;
			$vars = array(
				'email'			=> DecodeEMail($this->_row['email']),
				'aliasemail'	=> DecodeEMail($email),
				'link'			=> $link
			);
			SystemMail($thisUser->GetDefaultSender(),
				$email,
				$lang_custom['alias_sub'],
				'alias_text',
				$vars);

			// log
			PutLog(sprintf('User <%s> (%d) created external alias <%s> (%d)',
				$this->_row['email'],
				$this->_id,
				$email,
				$id),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			$result = $id;
		}

		// save sender
		$possibleSenders = $this->GetPossibleSenders();
		foreach($possibleSenders as $senderID=>$senderString)
			if($defaultSender == $senderString)
			{
				$this->SetDefaultSender($senderID);
				break;
			}

		return($result);
	}

	/**
	 * get used month sms credits
	 *
	 * @return int
	 */
	public function GetUsedMonthSMS()
	{
		global $db;

		$res = $db->Query('SELECT SUM(price) FROM {pre}smsend WHERE user=? AND monat=?',
			$this->_id,
			(int)date('mY'));
		list($usedMonthSMS) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($usedMonthSMS);
	}

	/**
	 * get user count
	 *
	 * @return int
	 */
	public static function GetUserCount()
	{
		global $db;

		$res = $db->Query('SELECT COUNT(*) FROM {pre}users');
		list($userCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($userCount);
	}

	/**
	 * get user's workgroups
	 *
	 * @param bool $withMembers Include members?
	 * @return array
	 */
	public function GetWorkgroups($withMembers = false)
	{
		return(BMWorkgroup::GetSimpleWorkgroupList($this->_id, $withMembers));
	}

	/**
	 * get account balance
	 *
	 * @return int
	 */
	public function GetBalance()
	{
		$group = $this->GetGroup();
		$groupRow = $group->Fetch();

		$smsPerMonth = $groupRow['sms_monat'];
		$balance = max(0, $smsPerMonth - $this->GetUsedMonthSMS()) + $this->GetStaticBalance();

		return($balance);
	}

	/**
	 * Get static-only credit balance at a certain point int ime
	 *
	 * Does not take into account monthly dynamic credits.
	 *
	 * @param int $when Timestamp (0 = now)
	 * @return int Credits
	 */
	public function GetStaticBalance($when = 0)
	{
		global $db;

		if($when == 0)
			$when = time();

		$result = 0;

		$res = $db->Query('SELECT SUM(`amount`) FROM {pre}transactions WHERE `userid`=? AND `status`=? AND `date`<=?',
			$this->_id, TRANSACTION_BOOKED, $when);
		list($result) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return (int)$result;
	}

	/**
	 * charge account
	 *
	 * @param int $credits Credits
	 * @return int -1 = failed, 0 = ok, but no smsend ID, > 0 = smsend ID
	 */
	public function Debit($credits, $description = 'Unknown')
	{
		global $db;

		$result = -1;
		$group = $this->GetGroup();
		$groupRow = $group->Fetch();

		$smsPerMonth = $groupRow['sms_monat'];
		$freeMonthCredits = $smsPerMonth - $this->GetUsedMonthSMS();
		$freeStaticCredits = $this->GetStaticBalance();

		if($credits < 0)
		{
			$credits = abs($credits);
			if($credits <= ($freeStaticCredits+$freeMonthCredits))
			{
				$monthPart = min($freeMonthCredits, $credits);
				$staticPart = $credits - $monthPart;

				if($monthPart > 0)
				{
					$db->Query('INSERT INTO {pre}smsend(user,monat,price,isSMS) VALUES(?,?,?,?)',
						$this->_id,
						(int)date('mY'),
						$credits,
						0);
					$result = $db->InsertId();
				}

				if($staticPart > 0)
				{
					$db->Query('INSERT INTO {pre}transactions(`userid`,`description`,`amount`,`date`,`status`) '
						. 'VALUES(?,?,?,?,?)',
						$this->_id,
						$description,
						- abs($staticPart),
						time(),
						TRANSACTION_BOOKED);
					if($result == -1)
						$result = 0;
				}

				return($result);
			}
			else
			{
				return(-1);
			}
		}
		else
		{
			$db->Query('INSERT INTO {pre}transactions(`userid`,`description`,`amount`,`date`,`status`) '
				. 'VALUES(?,?,?,?,?)',
				$this->_id,
				$description,
				abs($credits),
				time(),
				TRANSACTION_BOOKED);
			return(0);
		}
	}

	/**
	 * get transactions in a certain timeframe
	 *
	 * @param int $from Start timestamp
	 * @param int $to End timestamp
	 * @param string $sortBy Sort field (date|transactionid|description|status|amount)
	 * @param string $sortOrder Sort order (ASC|DESC)
	 * @return array
	 */
	public function GetTransactions($from, $to, $sortBy = 'date', $sortOrder = 'ASC')
	{
		global $db, $lang_user;

		if(!in_array($sortBy, array('date', 'transactionid', 'description', 'status', 'amount')))
			$sortBy = 'date';
		if(!in_array($sortOrder, array('ASC', 'DESC')))
			$sortOrder = 'ASC';

		$result = array();

		$res = $db->Query('SELECT * FROM {pre}transactions WHERE `userid`=? AND `date`>=? AND `date`<=? AND `status`>0 '
				. 'ORDER BY `' . $sortBy . '` ' . $sortOrder,
			$this->_id, $from, $to);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if(strlen($row['description']) > 5 && substr($row['description'], 0, 5) == 'lang:'
				&& isset($lang_user[substr($row['description'], 5)]))
				$row['description'] = $lang_user[substr($row['description'], 5)];
			$result[$row['transactionid']] = $row;
		}
		$res->Free();

		return $result;
	}

	/**
	 * cancel account
	 *
	 * @return bool
	 */
	public function CancelAccount()
	{
		global $db;

		$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id=?',
			'delete',
			$this->_id);

		return($db->AffectedRows() == 1);
	}

	/**
	 * get user autoresponder
	 *
	 * @return array $active, $subject, $text
	 */
	public function GetAutoresponder()
	{
		global $db;

		$active = 'no';
		$subject = $text = '';
		$lastSend = 0;

		$res = $db->Query('SELECT active,betreff,mitteilung,last_send FROM {pre}autoresponder WHERE userid=?',
			$this->_id);
		if($res->RowCount() > 0)
		{
			list($active, $subject, $text, $lastSend) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
		}

		return(array($active == 'yes', $subject, $text, $lastSend));
	}

	/**
	 * set last_sent field of autoresponder
	 *
	 * @param string $lastSend Last mail address
	 * @return bool
	 */
	public function SetAutoresponderLastSend($lastSend)
	{
		global $db;

		$db->Query('UPDATE {pre}autoresponder SET last_send=? WHERE userid=?',
			strtolower($lastSend),
			$this->_id);
		return($db->AffectedRows() == 1);
	}

	/**
	 * set autoresponder settings
	 *
	 * @param bool $active Active?
	 * @param string $subject Subject
	 * @param string $text Text
	 * @return int
	 */
	public function SetAutoresponder($active, $subject, $text)
	{
		global $db;

		$res = $db->Query('SELECT id FROM {pre}autoresponder WHERE userid=?',
			$this->_id);
		if($res->RowCount() > 0)
		{
			list($id) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			$db->Query('UPDATE {pre}autoresponder SET active=?,betreff=?,mitteilung=? WHERE id=?',
				$active ? 'yes' : 'no',
				$subject,
				$text,
				$id);
			return($db->AffectedRows() == 1);
		}
		else
		{
			$db->Query('INSERT INTO {pre}autoresponder(active,userid,betreff,mitteilung) VALUES(?,?,?,?)',
				$active ? 'yes' : 'no',
				$this->_id,
				$subject,
				$text);
			return($db->InsertId() != 0);
		}
	}

	/**
	 * get spam index size (entry count)
	 *
	 * @return int
	 */
	public function GetSpamIndexSize()
	{
		global $db;

		$res = $db->Query('SELECT COUNT(*) FROM {pre}spamindex WHERE userid=?',
			$this->_id);
		list($size) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($size);
	}

	/**
	 * reset spam index
	 *
	 * @return bool
	 */
	public function ResetSpamIndex()
	{
		global $db;

		$db->Query('DELETE FROM {pre}spamindex WHERE userid=?',
			$this->_id);
		$db->Query('UPDATE {pre}users SET bayes_spam=0, bayes_nonspam=0 WHERE id=?',
			$this->_id);

		return(true);
	}

	/**
	 * set antivirus settings
	 *
	 * @param bool $active Filter active?
	 * @param int $action Virus action
	 * @return bool
	 */
	public function SetAntivirusSettings($active, $action)
	{
		global $db;
		$db->Query('UPDATE {pre}users SET virusfilter=?, virusaction=? WHERE id=?',
			$active ? 'yes' : 'no',
			$action,
			$this->_id);
		return($db->AffectedRows() == 1);
	}

	/**
	 * set antispam settings
	 *
	 * @param bool $active Filter active?
	 * @param int $action Spam action
	 * @param bool $unspamMe Mark sent mails as NON-spam?
	 * @param int $bayesBorder Bayes border (%)
	 * @param bool $addressbookNoSpam Mark as NON-spam when sender is in the address book?
	 * @return bool
	 */
	public function SetAntispamSettings($active, $action, $unspamMe, $bayesBorder = false, $addressbookNoSpam = false)
	{
		global $db;
		$db->Query('UPDATE {pre}users SET spamfilter=?, spamaction=?, unspamme=?, addressbook_nospam=?'
			. ($bayesBorder !== false ? ', bayes_border=' . (int)$bayesBorder : '')
			. ' WHERE id=?',
			$active ? 'yes' : 'no',
			$action,
			$unspamMe ? 'yes' : 'no',
			$addressbookNoSpam ? 'yes' : 'no',
			$this->_id);
		return($db->AffectedRows() == 1);
	}

	/**
	 * get user's filters
	 *
	 * @return array
	 */
	public function GetFilters($sortColumn = 'orderpos', $sortOrder = 'ASC')
	{
		global $db;

		$filters = array();
		$res = $db->Query('SELECT id,title,applied,active,link,orderpos,flags FROM {pre}filter WHERE userid=? '
							. 'ORDER BY ' . $sortColumn . ' ' . $sortOrder,
			$this->_id);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$filters[$row['id']] = $row;
		}
		$res->Free();

		return($filters);
	}

	/**
	 * move filter
	 *
	 * @param int $id Filter ID
	 * @param int $direction Direction (-1 = up, 1 = down)
	 */
	public function MoveFilter($id, $direction)
	{
		global $db;

		$filters = $this->GetFilters();
		$newFilters = array();
		$maxPos = 0;

		foreach($filters as $filter)
			if($filter['orderpos'] > $maxPos)
				$maxPos = $filter['orderpos'];

		$newPos = max(1, min($maxPos, $filters[$id]['orderpos'] + $direction));

		foreach($filters as $filterID=>$filter)
		{
			if(count($newFilters) + 1 == $newPos)
			{
				$newFilters[$id] = $filters[$id];
				$newFilters[$id]['orderpos'] = $newPos;
			}

			if($filterID != $id)
			{
				$filter['orderpos'] = count($newFilters) + 1;
				$newFilters[$filterID] = $filter;
			}
		}

		if(!isset($newFilters[$id]))
		{
			$newFilters[$id] = $filters[$id];
			$newFilters[$id]['orderpos'] = $newPos;
		}

		foreach($newFilters as $filterID=>$newFilter)
		{
			if($newFilter['orderpos'] != $filters[$filterID]['orderpos'])
				$db->Query('UPDATE {pre}filter SET orderpos=? WHERE id=?',
					$newFilter['orderpos'],
					$filterID);
		}
	}

	/**
	 * get a filter
	 *
	 * @param int $id Filter ID
	 * @return array
	 */
	public function GetFilter($id)
	{
		global $db;

		$res = $db->Query('SELECT id,userid,title,applied,active,link,orderpos,flags FROM {pre}filter WHERE id=? AND userid=?',
			(int)$id,
			$this->_id);
		if($res->RowCount() == 1)
		{
			$row = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();
			return($row);
		}

		return(false);
	}

	/**
	 * add a filter
	 *
	 * @param string $title Title
	 * @param bool $active Active?
	 * @return int
	 */
	public function AddFilter($title, $active)
	{
		global $db;

		$orderPos = 0;
		$res = $db->Query('SELECT orderpos FROM {pre}filter WHERE userid=? ORDER BY orderpos DESC LIMIT 1',
			$this->_id);
		if($res->RowCount() == 1)
		{
			list($orderPos) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
		}

		$db->Query('INSERT INTO {pre}filter(userid,title,active,orderpos) VALUES(?,?,?,?)',
			$this->_id,
			$title,
			$active ? 1 : 0,
			++$orderPos);
		$id = $db->InsertId();

		if($id > 0)
		{
			$this->AddFilterCondition($id);
			$this->AddFilterAction($id);
			return($id);
		}

		return(0);
	}

	/**
	 * update a filter
	 *
	 * @param int $id Filter ID
	 * @param string $title Title
	 * @param bool $active Active?
	 * @param int $link Link type
	 * @param int $flags Filter flags
	 * @return bool
	 */
	public function UpdateFilter($id, $title, $active, $link, $flags = 0)
	{
		global $db;

		$db->Query('UPDATE {pre}filter SET title=?,active=?,link=?,flags=? WHERE id=? AND userid=?',
			$title,
			$active ? 1 : 0,
			(int)$link,
			(int)$flags,
			(int)$id,
			$this->_id);
		return($db->AffectedRows() == 1);
	}

	/**
	 * delete a filter
	 *
	 * @param int $id Filter ID
	 * @return bool
	 */
	public function DeleteFilter($id)
	{
		global $db;

		$db->Query('DELETE FROM {pre}filter WHERE id=? AND userid=?',
			(int)$id,
			$this->_id);
		if($db->AffectedRows() == 1)
		{
			$db->Query('DELETE FROM {pre}filter_conditions WHERE filter=?',
				(int)$id);
			return(true);
		}

		return(false);
	}

	/**
	 * get filter conditions
	 *
	 * @param int $filterID Filter ID
	 * @return array
	 */
	public function GetFilterConditions($filterID)
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT id,field,op,val FROM {pre}filter_conditions WHERE filter=? ORDER BY id ASC',
			(int)$filterID);
		while($row = $res->FetchArray())
			$result[$row['id']] = $row;
		$res->Free();

		return($result);
	}

	/**
	 * delete filter condition
	 *
	 * @param int $conditionID Condition ID
	 * @param int $filterID Filter ID
	 * @return bool
	 */
	public function DeleteFilterCondition($conditionID, $filterID)
	{
		global $db;

		$db->Query('DELETE FROM {pre}filter_conditions WHERE id=? AND filter=?',
			(int)$conditionID,
			(int)$filterID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * add filter condition
	 *
	 * @param int $filterID Filter ID
	 * @return int
	 */
	public function AddFilterCondition($filterID)
	{
		global $db;

		$db->Query('INSERT INTO {pre}filter_conditions(filter,field,op,val) VALUES(?,?,?,?)',
			(int)$filterID,
			1,
			1,
			'');
		return($db->InsertID());
	}

	/**
	 * update filter condition
	 *
	 * @param int $conditionID Condition ID
	 * @param int $filterID Filter ID
	 * @param int $field Field constant
	 * @param int $op Op constant
	 * @param string $val Value
	 * @return bool
	 */
	public function UpdateFilterCondition($conditionID, $filterID, $field, $op, $val)
	{
		global $db;

		$db->Query('UPDATE {pre}filter_conditions SET field=?,op=?,val=? WHERE id=? AND filter=?',
			(int)$field,
			(int)$op,
			$val,
			(int)$conditionID,
			(int)$filterID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * get filter actions
	 *
	 * @param int $filterID Filter ID
	 * @return array
	 */
	public function GetFilterActions($filterID)
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT id,filter,op,val,text_val FROM {pre}filter_actions WHERE filter=? ORDER BY id ASC',
			(int)$filterID);
		while($row = $res->FetchArray())
			$result[$row['id']] = $row;
		$res->Free();

		return($result);
	}

	/**
	 * delete filter action
	 *
	 * @param int $actionID Action ID
	 * @param int $filterID Filter ID
	 * @return bool
	 */
	public function DeleteFilterAction($actionID, $filterID)
	{
		global $db;

		$db->Query('DELETE FROM {pre}filter_actions WHERE id=? AND filter=?',
			(int)$actionID,
			(int)$filterID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * add filter action
	 *
	 * @param int $filterID Filter ID
	 * @return int
	 */
	public function AddFilterAction($filterID)
	{
		global $db;

		$db->Query('INSERT INTO {pre}filter_actions(filter,op,val) VALUES(?,?,?)',
			(int)$filterID,
			1,
			0);
		return($db->InsertID());
	}

	/**
	 * update filter action
	 *
	 * @param int $actionID Action ID
	 * @param int $filterID Filter ID
	 * @param int $field Field constant
	 * @param int $op Op constant
	 * @param string $val Value
	 * @return bool
	 */
	public function UpdateFilterAction($actionID, $filterID, $op, $val, $textVal)
	{
		global $db;

		$db->Query('UPDATE {pre}filter_actions SET op=?,val=?,text_val=? WHERE id=? AND filter=?',
			(int)$op,
			$val,
			$textVal,
			(int)$actionID,
			(int)$filterID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * increment filter applied-counter
	 *
	 * @param int $filterID Filter ID
	 * @return bool
	 */
	public function IncFilter($filterID)
	{
		global $db;

		$db->Query('UPDATE {pre}filter SET applied=applied+1 WHERE id=? AND userid=?',
			(int)$filterID,
			$this->_id);
		return($db->AffectedRows() == 1);
	}

	/**
	 * Update common preferences
	 *
	 * @param int $inboxRefresh
	 * @param bool $instantHTML
	 * @param int $firstDayOfWeek
	 * @param string $dateFormat
	 * @param string $senderName
	 * @param int $defaultSender
	 * @param string $rePrefix
	 * @param string $fwdPrefix
	 * @param bool $mailToSMS
	 * @param bool $forwardEnabled
	 * @param string $forwardTo
	 * @param bool $forwardDelete
	 * @param bool $enablePreview
	 * @param bool $conversationView
	 * @return bool
	 */
	public function UpdateCommonSettings($inboxRefresh, $instantHTML, $firstDayOfWeek, $dateFormat, $senderName, $defaultSender, $rePrefix, $fwdPrefix, $mailToSMS, $forwardEnabled, $forwardTo, $forwardDelete, $enablePreview, $conversationView, $newsletterOptIn, $plaintextCourier, $replyQuote, $hotkeys, $attCheck, $searchDetailsDefault, $preferredLanguage,
		$notifySound, $notifyEMail, $notifyBirthday, $autoSaveDrafts, $autoSaveDraftsInterval)
	{
		global $db, $bm_prefs;

		$this->SetPref('hotkeys', $hotkeys);

		$db->Query('UPDATE {pre}users SET in_refresh=?, soforthtml=?, c_firstday=?, datumsformat=?, absendername=?, defaultSender=?, re=?, fwd=?, mail2sms=?, forward=?, forward_to=?, forward_delete=?, preview=?, conversation_view=?, newsletter_optin=?, plaintext_courier=?, reply_quote=?, attcheck=?, search_details_default=?, preferred_language=?, notify_sound=?, notify_email=?, notify_birthday=?, auto_save_drafts=?, auto_save_drafts_interval=? WHERE id=?',
			$inboxRefresh,
			$instantHTML ? 'yes' : 'no',
			$firstDayOfWeek,
			$dateFormat,
			$senderName,
			$defaultSender,
			$rePrefix,
			$fwdPrefix,
			$mailToSMS ? 'yes' : 'no',
			$forwardEnabled ? 'yes' : 'no',
			$forwardTo,
			$forwardDelete ? 'yes' : 'no',
			$enablePreview ? 'yes' : 'no',
			$conversationView ? 'yes' : 'no',
			$newsletterOptIn ? 'yes' : 'no',
			$plaintextCourier ? 'yes' : 'no',
			$replyQuote ? 'yes' : 'no',
			$attCheck ? 'yes' : 'no',
			$searchDetailsDefault ? 'yes' : 'no',
			$preferredLanguage,
			$notifySound ? 'yes' : 'no',
			$notifyEMail ? 'yes' : 'no',
			$notifyBirthday ? 'yes' : 'no',
			$autoSaveDrafts ? 'yes' : 'no',
			max($bm_prefs['min_draft_save_interval'], $autoSaveDraftsInterval),
			$this->_id);
		return($db->AffectedRows() == 1);
	}

	/**
	 * update user contact data
	 *
	 * @param array $userRow Updates user row
	 * @param array $profileFields Profile field data
	 * @param bool $noHistory No history?
	 * @return bool
	 */
	public function UpdateContactData($userRow, $profileFields, $noHistory = false, $userID = 0, $passwordPlain = false)
	{
		global $db, $bm_prefs;

		if($noHistory || $userRow != $this->_row || ($profileFields !== false && $profileFields != @unserialize($userRow['profilfelder'])))
		{
			// save contact history?
			if(!$noHistory)
			{
				$contactHistory = $this->_row['contactHistory'];
				if($bm_prefs['contact_history'] == 'yes')
				{
					$contactHistory = @unserialize($this->_row['contactHistory']);
					if(!is_array($contactHistory))
						$contactHistory = array();
					$contactHistory[] = array(
						'anrede'			=> $this->_row['anrede'],
						'vorname'			=> $this->_row['vorname'],
						'nachname'			=> $this->_row['nachname'],
						'strasse'			=> $this->_row['strasse'],
						'hnr'				=> $this->_row['hnr'],
						'plz'				=> $this->_row['plz'],
						'ort'				=> $this->_row['ort'],
						'land'				=> (int)$this->_row['land'],
						'tel'				=> $this->_row['tel'],
						'fax'				=> $this->_row['fax'],
						'mail2sms_nummer'	=> $this->_row['mail2sms_nummer'],
						'altmail'			=> $this->_row['altmail'],
						'profilfelder'		=> $this->_row['profilfelder'],
						'company'			=> $this->_row['company'],
						'taxid'				=> $this->_row['taxid'],
						'changeDate'		=> time()
					);
					$contactHistory = serialize($contactHistory);
				}
			}
			else
			{
				if($userID == 0)
					$contactHistory = $this->_row['contactHistory'];
				else
				{
					$user = _new('BMUser', array($userID));
					$row = $user->Fetch();
					$contactHistory = $row['contactHistory'];
				}
			}

			// profile fields
			if($profileFields === false)
			{
				$profileFields = @unserialize($userRow['profilfelder']);
				if(!is_array($profileFields))
					$profileFields = array();
			}

			// store data
			$db->Query('UPDATE {pre}users SET vorname=?, nachname=?, strasse=?, hnr=?, plz=?, ort=?, land=?, tel=?, fax=?, mail2sms_nummer=?, altmail=?, profilfelder=?, passwort=?, company=?, taxid=?, contactHistory=?, sms_validation=?, anrede=? WHERE id=?',
				$userRow['vorname'],
				$userRow['nachname'],
				$userRow['strasse'],
				$userRow['hnr'],
				$userRow['plz'],
				$userRow['ort'],
				(int)$userRow['land'],
				$userRow['tel'],
				$userRow['fax'],
				$userRow['mail2sms_nummer'],
				$userRow['altmail'],
				serialize($profileFields),
				$userRow['passwort'],
				$userRow['company'],
				$userRow['taxid'],
				$contactHistory,
				$userID != 0
					? 0
					: (trim($userRow['mail2sms_nummer']) != trim($this->_row['mail2sms_nummer'])
						? 0
						: $this->_row['sms_validation']),
				$userRow['anrede'],
				$userID != 0 ? $userID : $this->_id);
			if($db->AffectedRows() == 1 || true)
			{
				// pw changed?
				if($userID == 0 && ($this->_row['passwort'] != $userRow['passwort']))
				{
					ModuleFunction('OnUserPasswordChange', array($this->_id, $this->_row['passwort'], $userRow['passwort'], $passwordPlain));

					if(isset($_SESSION['bm_xorCryptKey']) && $passwordPlain !== false)
					{
						$privateKeyPasswords = $this->GetPrivateKeyPasswords();

						$_SESSION['bm_xorCryptKey'] = $this->GenerateXORCryptKey($this->_id, $passwordPlain);

						if($privateKeyPasswords)
							$this->SetPrivateKeyPasswords($privateKeyPasswords);
					}
				}

				// mobile no changed?
				if($userID == 0
					&&
						((trim($userRow['mail2sms_nummer']) != trim($this->_row['mail2sms_nummer']) || ($this->_row['sms_validation'] == 0 && $this->_row['sms_validation_time']  == 0))
						&& trim($userRow['mail2sms_nummer']) != ''))
				{
					$userGroupRow = $this->GetGroup();

					if($userGroupRow->_row['smsvalidation'] == 'yes')
					{
						// generate validation code
						$smsValidationCode = '';
						for($i=0; $i<VALIDATIONCODE_LENGTH; $i++)
							$smsValidationCode .= substr(VALIDATIONCODE_CHARS, mt_rand(0, strlen(VALIDATIONCODE_CHARS)-1), 1);

						// send sms
						if(!class_exists('BMSMS'))
							include(B1GMAIL_DIR . 'serverlib/sms.class.php');

						$smsText = GetPhraseForUser($userID != 0 ? $userID : $this->_id, 'lang_custom', 'validationsms2');
						$smsText = str_replace('%%code%%', $smsValidationCode, $smsText);

						$sms = _new('BMSMS', array(0, false));
						$sms->Send($bm_prefs['mail2sms_abs'], preg_replace('/[^0-9]/', '', str_replace('+', '00', $userRow['mail2sms_nummer'])), $smsText, $bm_prefs['smsvalidation_type'], false, false);

						// set code
						$db->Query('UPDATE {pre}users SET `sms_validation_code`=?,`sms_validation_time`=?,`sms_validation`=? WHERE `id`=?',
							$smsValidationCode,
							time(),
							0,
							$userID != 0 ? $userID : $this->_id);
					}
				}
			}
			else
				return(false);
		}

		return(true);
	}

	/**
	 * get user's VCard
	 *
	 * @return string
	 */
	public function BuildVCard()
	{
		if(!class_exists('VCardBuilder'))
			include(B1GMAIL_DIR . 'serverlib/vcard.class.php');

		// fields
		$countryList = CountryList();
		$fields = array(
			'vorname'		=> $this->_row['vorname'],
			'nachname'		=> $this->_row['nachname'],
			'strassenr'		=> trim($this->_row['strasse'] . ' '  . $this->_row['hnr']),
			'plz'			=> $this->_row['plz'],
			'ort'			=> $this->_row['ort'],
			'land'			=> $countryList[$this->_row['land']],
			'tel'			=> $this->_row['tel'],
			'fax'			=> $this->_row['fax'],
			'handy'			=> $this->_row['mail2sms_nummer'],
			'firma'			=> $this->_row['company'],
			'email'			=> ExtractMailAddress($this->GetDefaultSender())
		);

		// generate vcf
		$vcardBuilder = _new('VCardBuilder', array($fields));
		return($vcardBuilder->Build());
	}

	/**
	 * get root certificates of user
	 *
	 * @return array
	 */
	public function GetRootCertificates()
	{
		global $db;

		$certs = array();
		$res = $db->Query('SELECT `hash`,`pemdata` FROM {pre}certificates WHERE `userid`=? AND `type`=?',
			$this->_id,
			CERTIFICATE_TYPE_ROOT);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$certs[$row['hash']] = $row['pemdata'];
		$res->Free();

		return($certs);
	}

	/**
	 * get certificate for e-mail address
	 *
	 * @param string $email E-Mail address
	 * @param int $type Certificate type
	 * @return mixed Array with certificate info or false on error
	 */
	public function GetCertificateForAddress($email, $type = CERTIFICATE_TYPE_PUBLIC)
	{
		global $db;

		$result = false;
		$res = $db->Query('SELECT `certificateid`,`hash`,`cn`,`email`,`validfrom`,`validto`,`pemdata`,`type` FROM {pre}certificates WHERE `userid`=? AND `type`=? AND `email`=? AND `validfrom`<=? AND `validto`>=? ORDER BY `validfrom` ASC LIMIT 1',
			$this->_id,
			$type,
			$email,
			time(),
			time());
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$result = $row;
		$res->Free();

		return($result);
	}

	/**
	 * get keyring of user
	 *
	 * @return array
	 */
	public function GetKeyRing($sortColumn = 'certificateid', $sortOrder = 'ASC', $type = CERTIFICATE_TYPE_PUBLIC)
	{
		global $db;

		$certs = array();
		$res = $db->Query('SELECT `certificateid`,`hash`,`cn`,`email`,`validfrom`,`validto`,`pemdata`,`type` FROM {pre}certificates WHERE `userid`=? AND `type`=? ORDER BY ' . $sortColumn . ' ' . $sortOrder,
			$this->_id,
			$type);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$certs[$row['certificateid']] = $row;
		$res->Free();

		return($certs);
	}

	/**
	 * store a x509-certificate in PEM format in the user's keyring
	 *
	 * @param string $pemData PEM data
	 * @return mixed Certificate hash or false on error
	 */
	public function StoreCertificate($pemData, $certType = CERTIFICATE_TYPE_PUBLIC)
	{
		global $db;

		// parse cert
		$cert = openssl_x509_read($pemData);
		if(!$cert)
			return(false);
		$certInfo = openssl_x509_parse($cert);
		openssl_x509_free($cert);

		// check purpose
		$smimeSign = $smimeEncrypt = false;
		foreach($certInfo['purposes'] as $purpose)
		{
			if($purpose[2] == 'smimeencrypt' && $purpose[0])
				$smimeEncrypt = true;
			if($purpose[2] == 'smimesign' && $purpose[0])
				$smimeSign = true;
			if($purpose[2] == 'any' && $purpose[0])
			{
				$smimeEncrypt = true;
				$smimeSign = true;
			}
		}
		if(!$smimeSign && !$smimeEncrypt)
			return(false);

		// check if exists
		$res = $db->Query('SELECT COUNT(*) FROM {pre}certificates WHERE `hash`=? AND `userid`=? AND `type`=?',
			$certInfo['hash'],
			$this->_id,
			$certType);
		list($certCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// add?
		if($certCount == 0)
		{
			$certMail = '';

			if(isset($certInfo['extensions']['subjectAltName'])
				&& substr($certInfo['extensions']['subjectAltName'], 0, 6) == 'email:')
			{
				$certMail = substr($certInfo['extensions']['subjectAltName'], 6);
			}
			else if(isset($certInfo['subject']['emailAddress']))
			{
				$certMail = $certInfo['subject']['emailAddress'];
			}

			$db->Query('INSERT INTO {pre}certificates(`type`,`userid`,`hash`,`cn`,`email`,`validfrom`,`validto`,`pemdata`) VALUES(?,?,?,?,?,?,?,?)',
				$certType,
				$this->_id,
				$certInfo['hash'],
				is_array($certInfo['subject']['CN']) ? array_unshift($certInfo['subject']['CN']) : $certInfo['subject']['CN'],
				$certMail,
				$certInfo['validFrom_time_t'],
				$certInfo['validTo_time_t'],
				$pemData);
		}

		// return
		return($certInfo['hash']);
	}

	/**
	 * export cert + pk + chain certs as PKCS12 file
	 *
	 * @param string $hash Certificate hash
	 * @param string $pass Password for PKCS12 file
	 * @return mixed String with PKCS12 data or false on error
	 */
	public function ExportPrivateCertificateAsPKCS12($hash, $pass)
	{
		$result = false;

		$certData = $this->GetCertificateByHash($hash);
		if(!$certData)
			return(false);

		$privKeyPEMData = $this->GetPrivateKey($hash);
		if(!$privKeyPEMData)
			return(false);

		$privKeyPass = $this->GetPrivateKeyPassword($hash);
		$privKey = !empty($privKeyPass) ? array($privKeyPEMData, $privKeyPass) : $privKeyPEMData;

		$chainCerts = $this->GetChainCerts($hash);
		if($chainCerts && is_array($chainCerts) && count($chainCerts) > 0)
			$args = array('extracerts' => $chainCerts);
		else
			$args = array();

		if(openssl_pkcs12_export($certData['pemdata'], $result, $privKey, $pass, $args))
			return($result);

		return(false);
	}

	/**
	 * store a private certificate
	 *
	 * @param string $certData Certificate PEM data
	 * @param string $keyData Private key PEM data
	 * @param string $pw Private key password
	 * @param array $chainCerts Chain certs array
	 * @return mixed Certificate hash or false on error
	 */
	public function StorePrivateCertificate($certData, $keyData, $pw, $chainCerts = false)
	{
		if($certData && $keyData && strlen($certData) > 5 && strlen($keyData) > 5)
		{
			$certData = str_replace(' TRUSTED ', ' ', $certData);
			$cert = @openssl_x509_read(trim($certData));

			if($cert)
			{
				// check if PK fits
				if(@openssl_x509_check_private_key($cert,
					!empty($pw) ? array($keyData, $pw) : $keyData))
				{
					$certInfo = openssl_x509_parse($cert);

					// check purpose
					$smimeSign = $smimeEncrypt = false;
					foreach($certInfo['purposes'] as $purpose)
					{
						if($purpose[2] == 'smimeencrypt' && $purpose[0])
							$smimeEncrypt = true;
						if($purpose[2] == 'smimesign' && $purpose[0])
							$smimeSign = true;
						if($purpose[2] == 'any' && $purpose[0])
						{
							$smimeEncrypt = true;
							$smimeSign = true;
						}
					}
					if(!$smimeSign && !$smimeEncrypt)
						return(false);

					// add cert
					if(($hash = $this->StoreCertificate($certData, CERTIFICATE_TYPE_PRIVATE)) !== false)
					{
						$this->SetPrivateKey($hash, $keyData);
						if(!empty($pw))
							$this->SetPrivateKeyPassword($hash, $pw);
						if($chainCerts !== false && is_array($chainCerts) && count($chainCerts) > 0)
							$this->SetChainCerts($hash, $chainCerts);
						return($hash);
					}
				}
			}
		}

		return(false);
	}

	/**
	 * delete certificate by supplying the certificate hash
	 *
	 * @param string $hash Certificate hash
	 * @return bool
	 */
	public function DeleteCertificateByHash($hash, $type = 0)
	{
		global $db;

		if($type == CERTIFICATE_TYPE_PRIVATE)
		{
			$this->DeletePref('ChainCerts_' . $hash);
			$this->DeletePref('PrivateKey_' . $hash);
			$this->DeletePref('PrivateKeyPassword_' . $hash);
		}

		$db->Query('DELETE FROM {pre}certificates WHERE `hash`=? AND `userid`=?'
			. ($type > 0 ? ' AND `type`=' . (int)$type : ''),
			$hash,
			$this->_id);
		if($db->AffectedRows() == 1)
			return(true);

		return(false);
	}

	/**
	 * set chain certs
	 *
	 * @param string $hash Certificate hash
	 * @param array $certs Chain certs
	 */
	public function SetChainCerts($hash, $certs)
	{
		$this->SetPref('ChainCerts_' . $hash, serialize($certs));
	}

	/**
	 * get chain certs
	 *
	 * @param string $hash Certificate hash
	 * @return array
	 */
	public function GetChainCerts($hash)
	{
		$result = @unserialize($this->GetPref('ChainCerts_' . $hash));
		return(is_array($result) ? $result : false);
	}

	/**
	 * return an array of recipients with missing certificate
	 *
	 * @param array $recipients Recipient list
	 * @return array
	 */
	public function GetRecipientsWithMissingCertificate($recipients, $type = CERTIFICATE_TYPE_PUBLIC)
	{
		global $db;

		foreach($recipients as $key=>$val)
			$recipients[$key] = strtolower($val);

		$res = $db->Query('SELECT `email` FROM {pre}certificates WHERE `userid`=? AND `email` IN ? AND `type`=? AND `validfrom`<=? AND `validto`>=?',
			$this->_id,
			$recipients,
			$type,
			time(),
			time());
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			while(($arrayKey = array_search(strtolower($row['email']), $recipients)) !== false)
				unset($recipients[$arrayKey]);
		}
		$res->Free();

		return($recipients);
	}

	/**
	 * fetch a certificate from keyring by supplying the certificate hash
	 *
	 * @param string $hash Certificate hash
	 * @return array
	 */
	public function GetCertificateByHash($hash)
	{
		global $db;

		$res = $db->Query('SELECT `type`,`userid`,`hash`,`cn`,`email`,`validfrom`,`validto`,`pemdata` FROM {pre}certificates WHERE `userid`=? AND `hash`=? LIMIT 1',
			$this->_id,
			$hash);
		if($res->RowCount() == 1)
		{
			$result = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			return($result);
		}

		return(false);
	}

	/**
	 * get the user's xor key salt
	 *
	 * @return string
	 */
	public function GetXORSalt()
	{
		$salt = $this->GetPref('XORKeySalt');

		if(!$salt || strlen($salt) < 64)
		{
			$salt = '';
			for($i=0; $i<64; $i++)
				$salt .= chr(mt_rand(0, 255));
			$salt = base64_encode($salt);
			$this->SetPref('XORKeySalt', $salt);
		}

		$salt = base64_decode($salt);
		return($salt);
	}

	/**
	 * generate XOR crypt key for user
	 *
	 * @param int $userID User ID
	 * @param string $passwordPlain Plaintext user password
	 * @return string
	 */
	static function GenerateXORCryptKey($userID, $passwordPlain)
	{
		$user = _new('BMUser', array($userID));
		$salt = $user->GetXORSalt();
		return(md5($passwordPlain . $salt));
	}

	/**
	 * encrypt and set private key password
	 *
	 * @param string $pw Plaintext password
	 * @return bool
	 */
	public function SetPrivateKeyPassword($certID, $pw)
	{
		if(!isset($_SESSION['bm_xorCryptKey']))
			return(false);

		$encryptedPW = XORCrypt($pw, $_SESSION['bm_xorCryptKey']);
		$this->SetPref('PrivateKeyPassword_' . $certID, base64_encode($encryptedPW));

		return(true);
	}

	/**
	 * get and decrypt private key password
	 *
	 * @return string Plaintext password
	 */
	public function GetPrivateKeyPassword($certID)
	{
		if(!isset($_SESSION['bm_xorCryptKey']))
			return(false);

		$encryptedPW = $this->GetPref('PrivateKeyPassword_' . $certID);
		if(!$encryptedPW || strlen($encryptedPW) == 0)
			return('');

		$pw = XORCrypt(base64_decode($encryptedPW), $_SESSION['bm_xorCryptKey']);
		return($pw);
	}

	/**
	 * get all available private key passwords
	 *
	 * @return array
	 */
	public function GetPrivateKeyPasswords()
	{
		global $db;

		if(!isset($_SESSION['bm_xorCryptKey']))
			return(false);

		$result = array();

		$res = $db->Query('SELECT `key`,`value` FROM {pre}userprefs WHERE userID=? AND `key` LIKE ?',
			(int)$this->_id,
			'PrivateKeyPassword_%');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			list(, $certID)  = explode('_', $row['key']);
			$result[$certID] = XORCrypt(base64_decode($row['value']), $_SESSION['bm_xorCryptKey']);
		}
		$res->Free();

		return($result);
	}

	/**
	 * set private key passwords
	 *
	 * @param array $in Input (hash => pw)
	 * @return bool
	 */
	public function SetPrivateKeyPasswords($in)
	{
		if(!isset($_SESSION['bm_xorCryptKey']))
			return(false);

		foreach($in as $certID=>$pw)
		{
			$encryptedPW = XORCrypt($pw, $_SESSION['bm_xorCryptKey']);
			$this->SetPref('PrivateKeyPassword_' . $certID, base64_encode($encryptedPW));
		}

		return(true);
	}

	/**
	 * set private key for cert
	 *
	 * @param int $certID Certificate hash
	 * @param string $data PEM data
	 */
	public function SetPrivateKey($certID, $data)
	{
		$this->SetPref('PrivateKey_' . $certID, $data);
	}

	/**
	 * get private key for cert
	 *
	 * @param string $certID Certificate hash
	 * @return string PEM data
	 */
	public function GetPrivateKey($certID)
	{
		return($this->GetPref('PrivateKey_' . $certID));
	}

	/**
	 * get order list
	 *
	 * @param string $sortColumn Column to sort by
	 * @param string $sortOrder Sort order
	 * @return array
	 */
	public function GetOrderList($sortColumn = 'created', $sortOrder = 'DESC')
	{
		global $db, $bm_prefs;

		if(!class_exists('BMPayment'))
			include(B1GMAIL_DIR . 'serverlib/payment.class.php');

		// fetch orders
		$result = array();
		$res = $db->Query('SELECT * FROM {pre}orders WHERE `userid`=? ORDER BY `' . $sortColumn . '` ' . $sortOrder,
						  $this->_id);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$row['cart'] = @unserialize($row['cart']);
			if(!is_array($row['cart']))
				$row['cart'] = array();

			$row['amountText']			= sprintf('%.02f %s', $row['amount']/100, $bm_prefs['currency']);
			$row['invoiceNo']			= BMPayment::InvoiceNo($row['orderid']);
			$row['invoiceAvailable']	= false;		// checked later

			$result[$row['orderid']] = $row;
		}
		$res->Free();

		// check for invoices
		if(count($result) > 0)
		{
			$res = $db->Query('SELECT `orderid` FROM {pre}invoices WHERE `orderid` IN ?',
							  array_keys($result));
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$result[$row['orderid']]['invoiceAvailable'] = true;
			$res->Free();
		}

		return($result);
	}

	/**
	 * get order details
	 *
	 * @param int $orderID Order ID
	 * @return array
	 */
	public function GetOrder($orderID)
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}orders WHERE `userid`=? AND `orderid`=?',
						  $this->_id,
						  $orderID);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$row['cart'] = @unserialize($row['cart']);
		if(!is_array($row['cart']))
			$row['cart'] = array();

		return($row);
	}

	/**
	 * get order invoice
	 *
	 * @param int $orderID Order ID
	 * @return string
	 */
	public function GetOrderInvoice($orderID)
	{
		global $db;

		$result = false;

		if($this->GetOrder($orderID) !== false)
		{
			$res = $db->Query('SELECT `invoice` FROM {pre}invoices WHERE `orderid`=?',
							  $orderID);
			if($res->RowCount() == 1)
				list($result) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
		}

		return($result);
	}

	/**
	* get all user-specific domains
	*
	* @return array
	*/
	public static function GetUserDomains()
	{
		global $db;

		$domains = array();
		$res = $db->Query('SELECT saliase FROM {pre}users WHERE LENGTH(saliase)!=0');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$userDomains = explode(':', strtolower($row['saliase']));
			foreach($userDomains as $domain)
				if(!in_array($domain, $domains))
					$domains[] = $domain;
		}
		$res->Free();

		return($domains);
	}

	/**
	 * OTP-encrypt user password and store key in DB
	 *
	 * @param string $passwordPlain Plaintext password
 	 * @return string Encrypted password (cookie token)
 	 */
	public static function SaveLogin($passwordPlain)
	{
		global $db;

		$pwLength = strlen($passwordPlain);
		$cookieToken = '';
		$dbToken = '';

		for($i=0; $i<$pwLength; $i++)
		{
			$rand 			= mt_rand(0, 255);
			$dbToken 		.= chr($rand);
			$cookieToken 	.= chr(ord($passwordPlain[$i]) ^ $rand);
		}

		$dbToken 	 = base64_encode($dbToken);
		$cookieToken = base64_encode($cookieToken);

		$db->Query('INSERT INTO {pre}savedlogins(`expires`,`token`) VALUES(?,?)',
			time()+TIME_ONE_YEAR,
			$dbToken);
		return($db->InsertId() . ':' . $cookieToken);
	}

	/**
	 * decrypt saved password using DB token
	 *
	 * @param string $token Cookie token
	 * @return string
	 */
	public static function LoadLogin($token)
	{
		global $db;

		if(strlen($token) < 3 || strpos($token, ':') === false)
			return(false);

		list($tokenID, $encryptedPW) = explode(':', $token);
		$res = $db->Query('SELECT `token` FROM {pre}savedlogins WHERE `id`=?',
			$tokenID);
		if($res->RowCount() != 1)
			return(false);
		list($dbToken) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		$dbToken 		= base64_decode($dbToken);
		$encryptedPW 	= base64_decode($encryptedPW);

		if(strlen($dbToken) != strlen($encryptedPW))
			return(false);

		$passwordPlain = '';
		for($i=0; $i<strlen($dbToken); $i++)
			$passwordPlain .= chr(ord($encryptedPW[$i]) ^ ord($dbToken[$i]));

		return($passwordPlain);
	}

	/**
	 * delete a saved login token
	 *
	 * @param string $token Cookie token
	 */
	public static function DeleteSavedLogin($token)
	{
		global $db;

		if(strlen($token) < 3 || strpos($token, ':') === false)
			return(false);

		list($tokenID, $encryptedPW) = explode(':', $token);
		$db->Query('DELETE FROM {pre}savedlogins WHERE `id`=?',
			$tokenID);
		return(true);
	}
}
