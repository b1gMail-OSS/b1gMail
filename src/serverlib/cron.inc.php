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
 * Clean up expired action tokens
 *
 */
function CleanupActionTokens()
{
	global $db;

	$db->Query('DELETE FROM {pre}actiontokens WHERE `expires`<=?',
		time());
}

/**
 * Clean up the mail send stats
 *
 */
function CleanupSendStats()
{
	global $db;

	$db->Query('DELETE FROM {pre}sendstats WHERE `time`<?',
		time()-TIME_ONE_WEEK);
}

/**
 * Clean up the mail receive stats
 *
 */
function CleanupRecvStats()
{
	global $db;

	$db->Query('DELETE FROM {pre}recvstats WHERE `time`<?',
		time()-TIME_ONE_WEEK);
}

/**
 * Auto-delete users who never logged in
 *
 */
function ProcessNoSignupAutoDel()
{
	global $bm_prefs, $db;

	if($bm_prefs['nosignup_autodel'] == 'yes' && $bm_prefs['nosignup_autodel_days'] >= 1)
	{
		$userIDs = array();

		$res = $db->Query('SELECT `id`,`email` FROM {pre}users WHERE `id`!=1 AND `lastlogin`=0 AND `last_pop3`=0 AND `last_imap`=0 AND `last_smtp`=0 AND `reg_date`<? AND `gesperrt`!=?',
			time()-TIME_ONE_DAY*$bm_prefs['nosignup_autodel_days'],
			'delete');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			PutLog(sprintf('Marking user <%s> (#%d) as deleted because no login occured within %d days after signup',
					$row['email'], $row['id'], $bm_prefs['nosignup_autodel_days']),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			$userIDs[] = $row['id'];
		}
		$res->Free();

		if(count($userIDs) > 0)
		{
			$db->Query('UPDATE {pre}users SET `gesperrt`=? WHERE `id` IN ?',
				'delete',
				$userIDs);
		}
	}
}

/**
 * Delete old mail delivery status entries
 *
 */
function CleanupMailDeliveryStatus()
{
	global $db;

	$db->Query('DELETE FROM {pre}maildeliverystatus WHERE (`status`=? OR `outboxid`=0) AND `created`<?',
		MDSTATUS_INVALID,
		time() - TIME_ONE_HOUR);
}

/**
 * Delete old notifications
 *
 */
function CleanupNotifications()
{
	global $db, $bm_prefs;

	$lifetime = max(1, $bm_prefs['notify_lifetime']) * TIME_ONE_DAY;

	$db->Query('DELETE FROM {pre}notifications WHERE `date`<? AND `read`=1',
		time()-$lifetime);
}

/**
 * Birthday notification cron job
 *
 */
function ProcessBirthdayNotifications()
{
	global $db, $bm_prefs;

	$todayD = (int)date('j');
	$todayM = (int)date('n');
	$todayY = (int)date('Y');

	$res = $db->Query('SELECT {pre}adressen.`id`,{pre}adressen.`user`,{pre}adressen.`geburtsdatum`,{pre}adressen.`vorname`,{pre}adressen.`nachname` FROM {pre}adressen '
		. 'INNER JOIN {pre}users ON {pre}users.`id`={pre}adressen.`user` '
		. 'WHERE {pre}users.`notify_birthday`=? AND {pre}adressen.`last_bd_reminder`!=? AND {pre}adressen.`geburtsdatum`!=0',
		'yes', $todayY);
	while($row = $res->FetchArray(MYSQLI_ASSOC))
	{
		if((int)date('j', $row['geburtsdatum']) == $todayD
			&& (int)date('n', $row['geburtsdatum']) == $todayM)
		{
			$years = (int)date('Y') - (int)date('Y', $row['geburtsdatum']);

			$user = _new('BMUser', array($row['user']));
			$user->PostNotification('notify_birthday',
				array(HTMLFormat($row['vorname'] . ' ' . $row['nachname']), $years),
				'organizer.addressbook.php?action=editContact&id='.$row['id'].'&',
				'%%tpldir%%images/li/notify_birthday.png',
				mktime(0, 0, 0, $todayM, $todayD, $todayY),
				0,
				NOTIFICATION_FLAG_USELANG,
				'::birthdayReminder');

			$db->Query('UPDATE {pre}adressen SET `last_bd_reminder`=? WHERE `id`=?',
				$todayY,
				$row['id']);
		}
	}
	$res->Free();
}

/**
 * Abuse protection cron job
 *
 */
function AbuseCron()
{
	global $db, $bm_prefs, $lang_admin, $lang_custom;

	// step 1: process expired points
	if($bm_prefs['ap_expire_last_run'] < time()-TIME_ONE_HOUR)		// run every hour
	{
		if($bm_prefs['ap_expire_mode'] == 'dynamic')
		{
			$res = $db->Query('SELECT `userid` FROM {pre}abuse_points WHERE `expired`=0 GROUP BY `userid` HAVING MAX(`date`)<?',
				time() - $bm_prefs['ap_expire_time']);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$db->Query('UPDATE {pre}abuse_points SET `expired`=1 WHERE `userid`=?',
					$row['userid']);
			}
			$res->Free();
		}
		else if($bm_prefs['ap_expire_mode'] == 'static')
		{
			$db->Query('UPDATE {pre}abuse_points SET `expired`=1 WHERE `expired`=0 AND `date`<?',
				time() - $bm_prefs['ap_expire_time']);
		}

		$db->Query('UPDATE {pre}prefs SET `ap_expire_last_run`=?',
			time());
	}

	// step 2: find and lock users who exceeded hard point limit
	if($bm_prefs['ap_autolock'] == 'yes' && $bm_prefs['ap_hard_limit'] > 0)
	{
		$res = $db->Query('SELECT {pre}users.`id` AS `id`,`email`,SUM(`points`) AS `pointsum` FROM {pre}users '
			. 'INNER JOIN {pre}abuse_points ON {pre}abuse_points.`userid`={pre}users.`id` '
			. 'INNER JOIN {pre}gruppen ON {pre}gruppen.`id`={pre}users.`gruppe` '
			. 'WHERE {pre}abuse_points.`expired`=0 AND {pre}users.`gesperrt`=? AND {pre}gruppen.`abuseprotect`=? '
			. 'GROUP BY {pre}users.`id` '
			. 'HAVING SUM(`points`)>=?',
			'no',
			'yes',
			$bm_prefs['ap_hard_limit']);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$db->Query('UPDATE {pre}users SET `gesperrt`=?,`notes`=CONCAT(`notes`,?) WHERE `id`=?',
				'yes',
				sprintf($lang_admin['ap_autolock_log'], date('r'), $row['pointsum'], $bm_prefs['ap_hard_limit']),
				$row['id']);
			PutLog(sprintf('User <%s> (#%d) exceeded abuse points hard limit (%d >= %d) and has been locked',
					$row['email'], $row['id'], $row['pointsum'], $bm_prefs['ap_hard_limit']),
				PRIO_NOTE,
				__FILE__,
				__LINE__);

			// send mail notification?
			if($bm_prefs['ap_autolock_notify'] == 'yes' && ($notifyTo = ExtractMailAddress($bm_prefs['ap_autolock_notify_to'])) != '')
			{
				if(!isset($types))
					$types = GetAbuseTypes();

				$points = array();
				$res2 = $db->Query('SELECT `points`,`type` FROM {pre}abuse_points WHERE `userid`=? AND `expired`=0 ORDER BY `entryid` DESC',
					$row['id']);
				while($pointRow = $res2->FetchArray(MYSQLI_ASSOC))
				{
					$points[] = sprintf('%5d    %s',
						$pointRow['points'],
						$types[$pointRow['type']]['title']);
				}
				$res2->Free();

				$vars = array(
					'datum'		=> FormatDate(),
					'id'		=> $row['id'],
					'email'		=> DecodeEMail($row['email']),
					'pointsum'	=> $row['pointsum'],
					'points'	=> implode("\n", $points),
					'link'		=> sprintf('%sadmin/?jump=%s',
						$bm_prefs['selfurl'],
						urlencode(sprintf('abuse.php?do=show&userid=%d&', $row['id'])))
				);
				SystemMail($bm_prefs['passmail_abs'],
					$notifyTo,
					$lang_custom['ap_autolock_sub'],
					'ap_autolock_text',
					$vars);
			}
		}
		$res->Free();
	}
}

/**
 * Delete expired saved login tokens
 *
 */
function CleanupSavedLogins()
{
	global $db;

	$db->Query('DELETE FROM {pre}savedlogins WHERE `expires`<?',
		time());
}

/**
 * reset webdisk traffic
 *
 */
function ResetWebdiskTraffic()
{
	global $db;

	$db->Query('UPDATE {pre}users SET traffic_down=0,traffic_up=0,traffic_status=? WHERE traffic_status!=?',
		(int)date('m'),
		(int)date('m'));
}

/**
 * clean up webdisk locks
 *
 */
function CleanupWebdiskLocks()
{
	global $db;

	$db->Query('DELETE FROM {pre}disklocks WHERE (expires>0 AND expires<?) OR (expires=0 AND modified<?)',
		time(),
		time()-7*TIME_ONE_DAY);
}

/**
 * clean up expired cert mails
 *
 */
function CleanupCertMails()
{
	global $db, $bm_prefs;

	$date = time() - $bm_prefs['einsch_life'];

	$res = $db->Query('SELECT DISTINCT(mail) AS mailID FROM {pre}certmails WHERE date<' . $date);
	while($row = $res->FetchArray(MYSQLI_ASSOC))
	{
		$db->Query('UPDATE {pre}mails SET flags=flags&(~'.FLAG_CERTMAIL.') WHERE id=?',
			$row['mailID']);
	}
	$res->Free();

	$db->Query('DELETE FROM {pre}certmails WHERE date<' . $date);
}

/**
 * clean up expired safe codes
 *
 */
function CleanupSafeCodes()
{
	global $db;
	$db->Query('DELETE FROM {pre}safecode WHERE generation<' . (time()-8*TIME_ONE_HOUR));
}

/**
 * delete outdated, pending aliases
 *
 * @return bool
 */
function CleanupAliases()
{
	global $db;

	$db->Query('DELETE FROM {pre}aliase WHERE (type&'.ALIAS_PENDING.')!=0 AND date<'.(time()-TIME_ONE_WEEK));
	return(true);
}

/**
 * process store time row
 *
 * @param array $row Row
 */
function ProcessStoreTimeRow($row)
{
	global $db;

	if(!class_exists('BMMailBox'))
		include(B1GMAIL_DIR . 'serverlib/mailbox.class.php');

	$userID 		= $row['userID'];
	$userObject		= _new('BMUser', array($userID));
	$userMail		= $userObject->_row['email'];
	$userMailbox 	= _new('BMMailBox', array($userID, $userMail, $userObject));
	$mailIDs 		= explode(',', $row['mailIDs']);
	$blobStorages	= explode(',', $row['blobStorages']);
	$freedSpace 	= $row['mailSizes'];

	foreach($mailIDs as $entryNo=>$mailID)
	{
		BMBlobStorage::createProvider($blobStorages[$entryNo], $userID)->deleteBlob(BMBLOB_TYPE_MAIL, $mailID);
 		ModuleFunction('AfterDeleteMail', array($mailID, &$userMailbox));
	}

	$db->Query('DELETE FROM {pre}mails WHERE `id` IN ?',
		$mailIDs);
	$db->Query('DELETE FROM {pre}certmails WHERE `mail` IN ?',
		$mailIDs);
	$db->Query('UPDATE {pre}users SET `mailspace_used`=`mailspace_used`-LEAST(`mailspace_used`,'.((int)abs($freedSpace)).'),`mailbox_generation`=`mailbox_generation`+1 WHERE `id`=?',
		$userID);
}

/**
 * enforce store times
 *
 * @return int Number of deleted mails
 */
function StoreTimeCron()
{
	global $db, $cacheManager;

	// user folders
	$res = $db->Query('SELECT GROUP_CONCAT({pre}mails.`id` SEPARATOR ?) AS mailIDs,GROUP_CONCAT({pre}mails.`blobstorage` SEPARATOR ?) AS blobStorages,SUM({pre}mails.`size`) AS mailSizes,{pre}mails.`userid` AS userID FROM {pre}mails,{pre}folders '
		. 'WHERE {pre}mails.folder>0 '
		. 'AND {pre}folders.id={pre}mails.folder '
		. 'AND {pre}folders.storetime>0 '
		. 'AND {pre}mails.datum<?-{pre}folders.storetime '
		. 'GROUP BY {pre}mails.`userid`',
		',', ',',
		time());
	while($row = $res->FetchArray(MYSQLI_ASSOC))
		ProcessStoreTimeRow($row);
	$res->Free();

	// system folders
	$res = $db->Query('SELECT `userid` AS userID,GROUP_CONCAT(`key` SEPARATOR ?) AS `keys`,GROUP_CONCAT(`value` SEPARATOR ?) AS `values` FROM {pre}userprefs WHERE `key` LIKE ? AND `value`>0 GROUP BY `userid`',
		',', ',',
		'storetime_%');
	while($row = $res->FetchArray(MYSQLI_ASSOC))
	{
		$storeTimes = array();
		$keys = explode(',', $row['keys']);
		$values = explode(',', $row['values']);

		foreach($keys as $index=>$key)
			$storeTimes[(int)substr($key, 10)] = $values[$index];

		$cond = array();
		foreach($storeTimes as $folder=>$time)
			$cond[] = sprintf('(`folder`=%d AND `datum`<%d)', $folder, time()-$time);
		$cond = '(' . implode(' OR ', $cond) . ')';

		$res2 = $db->Query('SELECT `userid` AS userID,GROUP_CONCAT(`id` SEPARATOR ?) AS mailIDs,GROUP_CONCAT(`blobstorage` SEPARATOR ?) AS blobStorages,SUM(`size`) AS mailSizes,`userid` FROM {pre}mails '
			. 'WHERE ' . $cond . ' '
			. 'AND `userid`=? '
			. 'GROUP BY `userid`',
			',', ',',
			$row['userID']);
		while($row2 = $res2->FetchArray(MYSQLI_ASSOC))
			ProcessStoreTimeRow($row2);
		$res2->Free();
	}
	$res->Free();
}

/**
 * auto-archive logs
 *
 */
function AutoArchiveLogs()
{
	global $bm_prefs, $db;

	if($bm_prefs['logs_autodelete'] == 'yes'
		&& $bm_prefs['logs_autodelete_days'] >= 1
		&& $bm_prefs['logs_autodelete_last'] < time()-86400)
	{
		$db->Query('UPDATE {pre}prefs SET `logs_autodelete_last`=?',
			time());
		$date = time() - TIME_ONE_DAY*$bm_prefs['logs_autodelete_days'];
		$archiveLogs = $bm_prefs['logs_autodelete_archive'] == 'yes';

		$count = 0;
		if(ArchiveLogs($date, $archiveLogs, $count))
		{
			PutLog(sprintf('Auto-archived %d log entries',
				$count),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
		}
	}
}
