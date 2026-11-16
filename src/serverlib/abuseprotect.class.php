<?php
/*
 * b1gMail
 * Copyright (c) 2026 HM, Switzerland
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
 * abuse protection class
 */
class BMAbuseProtect
{
	/**
	 * Number of removed mails the abuse protection lists (subject read, named in the notification)
	 */
	const QUEUE_LIST_MAX = 50;

	/**
	 * Get config for an abuse type
	 *
	 * @param int $type Abuse type ID
	 * @return array Config
	 */
	public static function getTypePrefs($type)
	{
		global $db;

		$apTypes = self::getTypes();
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
	public static function getTypes()
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
	public static function addPoint($userID, $type, $comment)
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
			$group = _new('BMGroup', array($userGroup));
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
	 * ID of the group abused accounts are moved to (0 = not configured)
	 *
	 * @return int
	 */
	public static function moveGroup()
	{
		global $bm_prefs;

		return(isset($bm_prefs['ap_automove_group']) ? (int)$bm_prefs['ap_automove_group'] : 0);
	}

	/**
	 * Does a user with this ID exist?
	 *
	 * @param int $userID User ID
	 * @return bool
	 */
	public static function userExists($userID)
	{
		global $db;

		$res = $db->Query('SELECT COUNT(*) FROM {pre}users WHERE `id`=?', $userID);
		list($count) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($count > 0);
	}

	/**
	 * Title of a group
	 *
	 * @param int $groupID Group ID
	 * @return string|null Null if the group does not exist
	 */
	public static function groupTitle($groupID)
	{
		global $db;

		$res = $db->Query('SELECT `titel` FROM {pre}gruppen WHERE `id`=?', $groupID);
		$title = $res->RowCount() == 1 ? $res->FetchArray(MYSQLI_NUM)[0] : null;
		$res->Free();

		return($title);
	}

	/**
	 * Sum of the abuse points of a user that have not expired
	 *
	 * @param int $userID User ID
	 * @return int
	 */
	public static function pointSum($userID)
	{
		global $db;

		$res = $db->Query('SELECT SUM(`points`) FROM {pre}abuse_points WHERE `userid`=? AND `expired`=0', $userID);
		list($pointSum) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return((int)$pointSum);
	}

	/**
	 * Does an active plugin offer to remove the waiting outgoing mails of a user from a mail queue?
	 *
	 * @return bool
	 */
	public static function queueSupported()
	{
		global $plugins;

		if(!isset($plugins) || !is_object($plugins))
			return(false);

		foreach($plugins->callFunction('OnAbuseQueueSupported', false, true) as $supported)
			if($supported === true)
				return(true);

		return(false);
	}

	/**
	 * Remove the waiting outgoing mails of a user from the mail queue, if the option is on and a plugin offers it
	 *
	 * @param int $userID User ID
	 * @return array|null Removed queue entries (to, subject, status), null if the option is off or no plugin offers it
	 */
	public static function clearQueue($userID)
	{
		global $bm_prefs, $plugins;

		if(!isset($bm_prefs['ap_clearqueue']) || $bm_prefs['ap_clearqueue'] != 'yes' || !self::queueSupported())
			return(null);

		$removed = array();
		foreach($plugins->callFunction('OnAbuseClearQueue', false, true, array((int)$userID)) as $items)
		{
			if(is_array($items))
				$removed = array_merge($removed, $items);
		}

		return($removed);
	}

	/**
	 * Move a user to another group because of abuse: adds a note to the user and writes a log entry
	 *
	 * @param int $userID User ID
	 * @param int $groupID Target group ID
	 * @param string $by Name of the admin who moves the user, empty if the abuse protection moves it by itself
	 * @param array|null $removed Mails removed from the mail queue (to, subject, status), null if the queue was not cleared
	 * @return bool True if the user has been moved
	 */
	public static function moveUser($userID, $groupID, $by = '', &$removed = null)
	{
		global $db, $bm_prefs, $lang_admin;

		$removed = null;

		// target group
		$newTitle = self::groupTitle($groupID);
		if($newTitle === null)
			return(false);

		// user and current group
		$res = $db->Query('SELECT {pre}users.`email`,{pre}users.`gruppe`,{pre}gruppen.`titel` FROM {pre}users '
				. 'LEFT JOIN {pre}gruppen ON {pre}gruppen.`id`={pre}users.`gruppe` '
				. 'WHERE {pre}users.`id`=?',
			$userID);
		$found = $res->RowCount() == 1;
		if($found)
			list($email, $oldID, $oldTitle) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		if(!$found || $oldID == $groupID)
			return(false);

		// note for the user, in the language of the admin area
		$pointSum = self::pointSum($userID);
		$when     = date('r');
		$oldTitle = $oldTitle === null ? '?' : $oldTitle;
		if($by === '')
			$note = sprintf($lang_admin['ap_move_log_auto'],
				$when, $newTitle, $groupID, $oldTitle, $oldID, $pointSum, $bm_prefs['ap_hard_limit']);
		else
			$note = sprintf($lang_admin['ap_move_log_manual'],
				$when, $by, $newTitle, $groupID, $oldTitle, $oldID, $pointSum, $bm_prefs['ap_hard_limit']);

		$db->Query('UPDATE {pre}users SET `gruppe`=?,`notes`=CONCAT(`notes`,?) WHERE `id`=?',
			$groupID,
			"\n" . $note,
			$userID);

		PutLog(sprintf('User <%s> (#%d) moved from group <%s> (#%d) to group <%s> (#%d) %s (abuse points: %d)',
				$email, $userID, $oldTitle, $oldID, $newTitle, $groupID,
				$by === '' ? 'by the abuse protection' : 'by admin <' . $by . '>',
				$pointSum),
			PRIO_NOTE,
			__FILE__,
			__LINE__);

		// waiting outgoing mails of the user
		$removed = self::clearQueue($userID);
		if($removed !== null && count($removed) > 0)
			PutLog(sprintf('User <%s> (#%d): %d waiting outgoing mail(s) removed from the mail queue (abuse protection)',
					$email, $userID, count($removed)),
				PRIO_NOTE,
				__FILE__,
				__LINE__);

		return(true);
	}

	/**
	 * An admin takes a user out of the abuse group by hand: the abuse points expire (like with "unlock"), a note
	 * is added to the user and a log entry is written. The points are kept when a user is moved into the group,
	 * so without this the abuse protection would move the user back with its next run.
	 *
	 * @param int $userID User ID
	 * @param int $groupID Group the user is moved to
	 * @param string $by Name of the admin
	 * @return bool True if the user was in the abuse group and the points have been reset
	 */
	public static function releaseUser($userID, $groupID, $by)
	{
		global $db, $lang_admin;

		$moveGroup = self::moveGroup();
		if($moveGroup <= 0 || (int)$groupID == $moveGroup)
			return(false);

		// is the user in the abuse group?
		$res = $db->Query('SELECT `email` FROM {pre}users WHERE `id`=? AND `gruppe`=?',
			$userID,
			$moveGroup);
		$found = $res->RowCount() == 1;
		if($found)
			list($email) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		if(!$found)
			return(false);

		// the points expire, a note documents it
		$pointSum = self::pointSum($userID);
		$db->Query('UPDATE {pre}abuse_points SET `expired`=1 WHERE `userid`=?', $userID);
		$db->Query('UPDATE {pre}users SET `notes`=CONCAT(`notes`,?) WHERE `id`=?',
			"\n" . sprintf($lang_admin['ap_release_log'], date('r'), $by, $pointSum),
			$userID);

		PutLog(sprintf('User <%s> (#%d) taken out of the abuse group (#%d) by admin <%s>, abuse points reset (%d)',
				$email, $userID, $moveGroup, $by, $pointSum),
			PRIO_NOTE,
			__FILE__,
			__LINE__);

		return(true);
	}

	/**
	 * Text for the notification mail about the mail queue of a moved user: a line with the number and one block per removed mail
	 *
	 * @param array|null $removed Mails removed from the mail queue (to, subject, status), null if the queue was not cleared
	 * @return string
	 */
	public static function queueText($removed)
	{
		global $lang_admin;

		if($removed === null)
			return($lang_admin['ap_queue_line_none']);

		$listMax = self::QUEUE_LIST_MAX;
		$count   = count($removed);
		$text    = pluralize($lang_admin['ap_queue_line'], $count);

		// labels, padded to the same width
		$labels = array($lang_admin['ap_queue_to'], $lang_admin['ap_queue_subject'], $lang_admin['ap_queue_status']);
		$width  = max(array_map('_strlen', $labels));
		foreach($labels as $key => $label)
			$labels[$key] = $label . ':' . str_repeat(' ', $width - _strlen($label)) . ' ';
		list($labelTo, $labelSubject, $labelStatus) = $labels;

		$indent = str_repeat(' ', 5);
		foreach(array_slice($removed, 0, $listMax) as $no => $item)
		{
			$text .= "\n\n" . sprintf('%3d. ', $no + 1) . $labelTo . $item['to']
				. "\n" . $indent . $labelSubject . ($item['subject'] != '' ? $item['subject'] : '-')
				. "\n" . $indent . $labelStatus . ($item['status'] != '' ? $item['status'] : $lang_admin['ap_queue_nostatus']);
		}
		if($count > $listMax)
			$text .= "\n\n" . $indent . sprintf($lang_admin['ap_queue_more'], $count - $listMax);

		return($text);
	}

	/**
	 * Send the mail notification about a locked or moved user to the admin, if it is switched on
	 *
	 * @param array $row User (id, email, pointsum)
	 * @param string $subjectKey Name of the custom subject phrase
	 * @param string $textKey Name of the custom text phrase
	 * @param array $extraVars More variables for the text
	 */
	public static function notify($row, $subjectKey, $textKey, $extraVars = array())
	{
		global $db, $bm_prefs, $lang_custom;

		if($bm_prefs['ap_autolock_notify'] != 'yes' || ($notifyTo = ExtractMailAddress($bm_prefs['ap_autolock_notify_to'])) == '')
			return;

		$types  = self::getTypes();
		$points = array();
		$res    = $db->Query('SELECT `points`,`type` FROM {pre}abuse_points '
				. 'WHERE `userid`=? AND `expired`=0 '
				. 'ORDER BY `entryid` DESC',
			$row['id']);
		while($pointRow = $res->FetchArray(MYSQLI_ASSOC))
		{
			$points[] = sprintf('%5d    %s',
				$pointRow['points'],
				$types[$pointRow['type']]['title']);
		}
		$res->Free();

		$vars = array_merge(array(
			'datum'		=> FormatDate(),
			'id'		=> $row['id'],
			'email'		=> DecodeEMail($row['email']),
			'pointsum'	=> $row['pointsum'],
			'points'	=> implode("\n", $points),
			'link'		=> sprintf('%sadmin/?jump=%s',
				$bm_prefs['selfurl'],
				urlencode(sprintf('abuse.php?do=show&userid=%d&', $row['id'])))
		), $extraVars);
		SystemMail($bm_prefs['passmail_abs'],
			$notifyTo,
			$lang_custom[$subjectKey],
			$textKey,
			$vars);
	}
}
