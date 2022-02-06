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
 * calendar class
 */
class BMCalendar
{
	var $_userID;

	/**
	 * constructor
	 *
	 * @param int $userID User ID
	 * @return BMCalendar
	 */
	function __construct($userID)
	{
		$this->_userID = $userID;
	}

	/**
	 * Get start day of week
	 *
	 * @param int $timestamp Timestamp of a date somewhere in the week
	 * @return int Timestamp of week start day, 00:00:00
	 */
	function GetWeekStartDay($timestamp)
	{
		$calWeek = date('W', $timestamp);

		while($timestamp > 0)
		{
			if(date('W', $timestamp-TIME_ONE_DAY) != $calWeek)
				break;
			$timestamp -= TIME_ONE_DAY;
		}

		return(mktime(0, 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp)));
	}

	/**
	 * Get end day of week
	 *
	 * @param int $timestamp Timestamp of a date somewhere in the week
	 * @return int Timestamp of week end day, 00:00:00
	 */
	function GetWeekEndDay($timestamp)
	{
		$calWeek = date('W', $timestamp);

		while($timestamp < $timestamp+8*TIME_ONE_DAY)
		{
			if(date('W', $timestamp+TIME_ONE_DAY) != $calWeek)
				break;
			$timestamp += TIME_ONE_DAY;
		}

		return(mktime(0, 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp)));
	}

	/**
	 * get day of week
	 *
	 * @param int $day Day
	 * @param int $month Month
	 * @param int $year Year
	 * @return int
	 */
	static function GetDayOfWeek($day, $month, $year)
	{
		return(date('w', mktime(1, 1, 1, $month, $day, $year)));
	}

	/**
	 * get days in month
	 *
	 * @param int $month Month
	 * @param int $year Year
	 * @return int
	 */
	static function GetDaysInMonth($month, $year)
	{
		return(date('t', mktime(1, 1, 1, $month, 1, $year)));
	}

	/**
	 * get days in year
	 *
	 * @param int $year Year
	 * @return int
	 */
	static function GetDaysInYear($year)
	{
		return(checkdate(2, 29, $year) ? 366 : 365);
	}

	/**
	 * prepare date for another time
	 *
	 * @param array $row
	 * @param int $time
	 * @param int $start
	 * @param int $end
	 * @return array
	 */
	function _dateFor($row, $time, $start, $end)
	{
		$row['enddate'] = min($end, ($row['enddate'] - $row['startdate']) + $time);
		$row['startdate'] = max($start, $time);
		return($row);
	}

	/**
	 * get date occurences for a certain timeframe
	 *
	 * @param array $row Row
	 * @param int $start Start
	 * @param int $end End
	 * @return array Row of occurenced
	 */
	function _getOccurencesInTimeframe($row, $start, $end)
	{
		$result = array();

		if($start > $end)
			return($result);

		$flags = $row['flags'];
		$repeatFlags = $row['repeat_flags'];
		$repeatValue = max(1, $row['repeat_value']);
		$repeatTimes = $row['repeat_times'];
		$startDate = $row['startdate'];
		$repeatCount = 0;
		$timeTo = ($repeatFlags & CLNDR_REPEATING_UNTIL_DATE)
					? min($repeatTimes, $end)
					: $end;
		$timeOffset = $startDate - mktime(0,
			0,
			0,
			date('m', $startDate),
			date('d', $startDate),
			date('Y', $startDate));
		$repeatStartDate = mktime(0,
			0,
			0,
			date('m', $startDate),
			date('d', $startDate),
			date('Y', $startDate)) + TIME_ONE_DAY;

		// repeating?
		$repeating = ($row['repeat_flags']&CLNDR_REPEATING_DAILY)
							|| ($row['repeat_flags']&CLNDR_REPEATING_MONTHLY_MDAY)
							|| ($row['repeat_flags']&CLNDR_REPEATING_MONTHLY_WDAY)
							|| ($row['repeat_flags']&CLNDR_REPEATING_WEEKLY)
							|| ($row['repeat_flags']&CLNDR_REPEATING_YEARLY);

		// 'fixed' first occurency
		if(($row['startdate'] >= $start && $row['startdate'] <= $end)
			|| ($row['enddate'] > $start && $row['enddate'] <= $end)
			|| ($row['startdate'] <= $start && $row['enddate'] >= $end))
			$result[] = BMCalendar::_dateFor($row, $row['startdate'], $start, $end);

		// repeating
		if($repeating)
		{
			// daily
			if($repeatFlags & CLNDR_REPEATING_DAILY)
			{
				$exceptDays = explode(',', $row['repeat_extra1']);
				foreach($exceptDays as $key=>$val)
					if($val != '')
						$exceptDays[$key] = (int)$val;
				for($time=$repeatStartDate+($repeatValue-1)*TIME_ONE_DAY; $time<=$timeTo; $time+=$repeatValue*TIME_ONE_DAY)
					if(!in_array((int)date('w', $time), $exceptDays, true))
					{
						$repeatCount++;
						if(($repeatFlags & CLNDR_REPEATING_UNTIL_COUNT) && $repeatCount>$repeatTimes)
							break;
						if($time >= $start && $time <= $end)
							$result[] = BMCalendar::_dateFor($row,
								mktime(0, 0, 0, date('m', $time), date('d', $time), date('Y', $time)) + $timeOffset,
								$start,
								$end);
					}
			}

			// weekly
			else if($repeatFlags & CLNDR_REPEATING_WEEKLY)
			{
				for($time = $repeatStartDate-TIME_ONE_DAY/2+$repeatValue*TIME_ONE_WEEK; $time<=$timeTo; $time+=$repeatValue*TIME_ONE_WEEK)
				{
					$repeatCount++;
					if(($repeatFlags & CLNDR_REPEATING_UNTIL_COUNT) && $repeatCount>$repeatTimes)
						break;
					if($time >= $start && $time <= $end)
						$result[] = BMCalendar::_dateFor($row,
							mktime(0, 0, 0, date('m', $time), date('d', $time), date('Y', $time)) + $timeOffset,
							$start,
							$end);
				}
			}

			// monthly
			else if(($repeatFlags & CLNDR_REPEATING_MONTHLY_MDAY)
					|| ($repeatFlags & CLNDR_REPEATING_MONTHLY_WDAY))
			{
				$monthRepeatStartDate = mktime(0, 0, 0, date('m', $repeatStartDate-TIME_ONE_DAY/2), 1, date('Y', $repeatStartDate-TIME_ONE_DAY/2));

				for($time=$monthRepeatStartDate; $time<=$timeTo+TIME_ONE_MONTH; $time+=$repeatValue*TIME_ONE_MONTH)
				{
					$time += TIME_ONE_WEEK;
					$time = mktime(0, 0, 0, date('m', $time), 1, date('Y', $time));

					$day = 1;
					$month = (int)date('m', $time);
					$year = (int)date('Y', $time);

					if($repeatFlags & CLNDR_REPEATING_MONTHLY_MDAY)
					{
						$day = max(1, $row['repeat_extra1']);
						if($day > BMCalendar::GetDaysInMonth($month, $year))
							continue;
					}
					else if($repeatFlags & CLNDR_REPEATING_MONTHLY_WDAY)
					{
						$mDays = array();
						for($mDay=1; $mDay<BMCalendar::GetDaysInMonth($month, $year); $mDay++)
						{
							if((int)date('w', mktime(0, 0, 0, $month, $mDay, $year))
									== $row['repeat_extra2'])
								$mDays[] = $mDay;
						}
						if(count($mDays) > 0)
						{
							if($row['repeat_extra1'] == 4)
								$day = array_pop($mDays);
							else if($row['repeat_extra1'] < count($mDays))
								$day = $mDays[$row['repeat_extra1']];
							else
								continue;
						}
					}

					$myTime = mktime(0, 0, 0, $month, $day, $year) + $timeOffset;
					if($myTime >= $repeatStartDate+$timeOffset)
					{
						$repeatCount++;
						if(($repeatFlags & CLNDR_REPEATING_UNTIL_COUNT) && $repeatCount>$repeatTimes)
							break;
						if($myTime >= $start && $myTime <= $end)
							$result[] = BMCalendar::_dateFor($row, $myTime, $start, $end);
					}
				}
			}

			// yearly
			else if($repeatFlags & CLNDR_REPEATING_YEARLY)
			{
				$repeatY = date('Y', $repeatStartDate);
				while(true)
				{
					$repeatCount++;
					if(($repeatFlags & CLNDR_REPEATING_UNTIL_COUNT) && $repeatCount>$repeatTimes)
						break;

					$repeatY += $repeatValue;
					$time = mktime(date('H', $startDate),
						date('i', $startDate),
						date('s', $startDate),
						date('n', $startDate),
						date('j', $startDate),
						$repeatY);

					if($time > $end)
						break;

					if($time >= $start)
					{
						$result[] = BMCalendar::_dateFor($row,
							$time,
							$start,
							$end);
					}
				}
			}

		}

		return($result);
	}

	/**
	 * date sort callback
	 */
	function sortDatesForTimeframe($a, $b)
	{
		return($a['startdate'] - $b['startdate']);
	}

	/**
	 * get dates in timeframe
	 *
	 * @param int $start Start time
	 * @param int $end End time
	 * @param int $group Group (-2 = all)
	 * @return array
	 */
	function GetDatesForTimeframe($start, $end, $group = -2)
	{
		global $db;

		$result = array();

		// get dates
		$res = $db->Query('SELECT id,user,title,location,text,`group`,startdate,enddate,reminder,flags,repeat_flags,repeat_times,repeat_value,repeat_extra1,repeat_extra2 '
							. 'FROM {pre}dates WHERE (((startdate>=? OR enddate<=? OR (startdate<=? AND enddate>=?)) AND repeat_flags=0) OR (repeat_flags>0 AND ((repeat_flags&'.CLNDR_REPEATING_UNTIL_DATE.')=0 OR repeat_value<=?))) AND user=?'
							. ($group > -2 ? ' AND `group`=' . (int)$group : ''),
							$start,
							$end,
							$start,
							$end,
							$start,
							$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$dates = $this->_getOccurencesInTimeframe($row, $start, $end);
			if(count($dates) > 0)
				$result = array_merge($result, $dates);
		}
		$res->Free();

		// sort by start date
		uasort($result, array(&$this, 'sortDatesForTimeframe'));

		return($result);
	}

	/**
	 * send date notification
	 *
	 * @param array $date Date row
	 * @return bool
	 */
	function _sendNotification($date)
	{
		global $db, $bm_prefs;

		$ok = false;
		$user = _new('BMUser', array($date['user']));

		// post notification
		if($date['flags'] & CLNDR_REMIND_NOTIFY)
		{
			$user->PostNotification('notify_date',
				array(HTMLFormat($date['title'])),
				sprintf('showCalendarDate(%d,%d,%d,false)', $date['id'], $date['startdate'], $date['enddate']),
				'%%tpldir%%images/li/notify_calendar.png',
				$date['startdate'],
				0,
				NOTIFICATION_FLAG_USELANG | NOTIFICATION_FLAG_JSLINK,
				'::dateReminder');
			$ok = true;
		}

		// send e-mail notification
		if($date['flags'] & CLNDR_REMIND_EMAIL)
		{
			// vars
			$vars = array(
				'title'		=> $date['title'],
				'location'	=> $date['location'],
				'message'	=> $date['text'],
				'date'		=> date('d.m.Y', $date['startdate']),
				'time'		=> date('H:i', $date['startdate'])
			);
			if(SystemMail($bm_prefs['passmail_abs'],
				$user->_row['email'],
				GetPhraseForUser($date['user'], 'lang_custom', 'clndr_subject'),
				'clndr_date_msg',
				$vars,
				$date['user']))
			{
				// log
				PutLog(sprintf('Sent e-mail notification about date <%d> to user <%s> (%d)',
					$date['id'],
					$user->_row['email'],
					$date['user']),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
				$ok = true;
			}
		}

		// send sms notification
		if($date['flags'] & CLNDR_REMIND_SMS)
		{
			// prepare SMS
			$toNo = $user->_row['mail2sms_nummer'];
			$smsText = GetPhraseForUser($date['user'], 'lang_custom', 'clndr_sms');
			$smsText = str_replace('%%date%%', date('d.m.Y', $date['startdate']), $smsText);
			$smsText = str_replace('%%time%%', date('H:i', $date['startdate']), $smsText);
			$smsText = str_replace('%%subtitle%%', $date['title'], $smsText);
			if(strlen($smsText) > 160)
				$smsText = substr($smsText, 0, 157) . '...';

			// send SMS
			if(!class_exists('BMSMS'))
				include(B1GMAIL_DIR . 'serverlib/sms.class.php');
			$sms = _new('BMSMS', array($date['user'], &$user));
			if($sms->Send($bm_prefs['clndr_sms_abs'], $toNo, $smsText, $bm_prefs['clndr_sms_type'], true, true))
			{
				// log
				PutLog(sprintf('Sent SMS notification about date <%d> to <%s> (user %d)',
					$date['id'],
					$user->_row['mail2sms_nummer'],
					$date['user']),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
			}
			$ok = true;
		}

		// update last_reminder
		if($ok)
			$db->Query('UPDATE {pre}dates SET last_reminder=? WHERE id=?',
				time(),
				$date['id']);

		// return
		return($ok);
	}

	/**
	 * send notifications
	 *
	 * @return int Number of sent notifications
	 */
	function ProcessNotifications()
	{
		global $db, $bm_prefs;

		$notifications = 0;

		// today!
		$start = time()-31*TIME_ONE_DAY;
		$end = time()+31*TIME_ONE_DAY;

		// get dates
		$remindDates = array();
		$res = $db->Query('SELECT {pre}dates.id,{pre}dates.last_reminder,{pre}dates.user,{pre}dates.title,{pre}dates.location,{pre}dates.`text`,{pre}dates.`group`,{pre}dates.startdate,{pre}dates.enddate,{pre}dates.reminder,{pre}dates.flags,{pre}dates.repeat_flags,{pre}dates.repeat_times,{pre}dates.repeat_value,{pre}dates.repeat_extra1,{pre}dates.repeat_extra2 '
							. 'FROM {pre}dates '
							. 'INNER JOIN {pre}users ON {pre}users.`id`={pre}dates.`user` '
							. 'WHERE {pre}users.`gesperrt`!=\'delete\' AND ({pre}dates.flags&'.(CLNDR_REMIND_EMAIL|CLNDR_REMIND_NOTIFY|CLNDR_REMIND_SMS).')!=0 AND ((({pre}dates.startdate>=? OR {pre}dates.enddate<=? OR ({pre}dates.startdate<=? AND {pre}dates.enddate>=?)) AND {pre}dates.repeat_flags=0) OR ({pre}dates.repeat_flags>0 AND (({pre}dates.repeat_flags&'.CLNDR_REPEATING_UNTIL_DATE.')=0 OR {pre}dates.repeat_value<=?))) '
							. 'ORDER BY {pre}dates.user ASC',
							$start,
							$end,
							$start,
							$end,
							$start);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$dates = BMCalendar::_getOccurencesInTimeframe($row, $start, $end);
			if(count($dates) > 0)
				$remindDates = array_merge($remindDates, $dates);
		}
		$res->Free();

		// notifications
		foreach($remindDates as $date)
		{
			// send notification now?
			if(time() >= $date['startdate']-$date['reminder']-$bm_prefs['cron_interval']
				&& time() <= $date['startdate'])
			{
				// not sent yet?
				if($date['last_reminder'] < $date['startdate']-$date['reminder']-$bm_prefs['cron_interval'])
				{
					// process
					if(BMCalendar::_sendNotification($date))
						$notifications++;
				}
			}
		}

		// return count of successful notifications
		return($notifications);
	}

	/**
	 * get groups
	 *
	 * @param string $sortColumn
	 * @param string $sortOrder
	 * @return array
	 */
	function GetGroups($sortColumn = 'title', $sortOrder = 'asc')
	{
		global $db, $lang_user;

		$result = array(
			-1	=> array(
					'id'		=> -1,
					'user'		=> $this->_userID,
					'title'		=> $lang_user['nocalcat'],
					'color'		=> 0
				)
		);

		$res = $db->Query('SELECT id,user,title,color,dav_uri,dav_uid FROM {pre}dates_groups WHERE user=? '
			. 'ORDER BY ' . $sortColumn . ' ' . $sortOrder,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$result[$row['id']] = $row;
		}
		$res->Free();

		return($result);
	}

	/**
	 * get group
	 *
	 * @param int $id
	 * @return array
	 */
	function GetGroup($id)
	{
		global $db;

		$res = $db->Query('SELECT id,user,title,color,dav_uri,dav_uid FROM {pre}dates_groups WHERE user=? AND id=?',
			$this->_userID,
			(int)$id);
		if($res->RowCount() == 1)
		{
			$row = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			return($row);
		}

		return(false);
	}

	/**
	 * add group
	 *
	 * @param string $title Group title
	 * @param int $color Color (0-5)
	 * @return int
	 */
	function AddGroup($title, $color = 0, $davURI = '', $davUID = '')
	{
		global $db;

		$db->Query('INSERT INTO {pre}dates_groups(user,title,color,dav_uri,dav_uid) VALUES(?,?,?,?,?)',
			$this->_userID,
			$title,
			(int)$color,
			$davURI,
			$davUID);
		return($db->InsertId());
	}

	/**
	 * change group
	 *
	 * @param int $id Group ID
	 * @param string $title Group title
	 * @param int $color Color (0-5)
	 * @return bool
	 */
	function UpdateGroup($id, $title, $color = 0)
	{
		global $db;

		$db->Query('UPDATE {pre}dates_groups SET title=?,color=? WHERE id=? AND user=?',
			$title,
			(int)$color,
			(int)$id,
			$this->_userID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * delete group
	 *
	 * @param int $groupID Group ID
	 * @return bool
	 */
	function DeleteGroup($groupID, $deleteDates = false)
	{
		global $db;

		if($groupID < 1)
			return(false);

		if(!$deleteDates)
		{
			$res = $db->Query('SELECT `id` FROM {pre}dates WHERE `group`=? AND `user`=?',
				(int)$groupID,
				$this->_userID);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				ChangelogUpdated(BMCL_TYPE_CALENDAR, $row['id'], time());
			$res->Free();

			$db->Query('UPDATE {pre}dates SET `group`=-1 WHERE `group`=? AND user=?',
				(int)$groupID,
				$this->_userID);
		}
		else
		{
			$res = $db->Query('SELECT `id` FROM {pre}dates WHERE `user`=? AND `group`=?',
				$this->_userID,
				(int)$groupID);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				ChangelogDeleted(BMCL_TYPE_CALENDAR, $row['id'], time());
				$db->Query('DELETE FROM {pre}dates_attendees WHERE `date`=?',
					$row['id']);
			}
			$res->Free();

			$db->Query('DELETE FROM {pre}dates WHERE `user`=? AND `group`=?',
				$this->_userID,
				(int)$groupID);
		}

		$db->Query('DELETE FROM {pre}dates_groups WHERE user=? AND id=?',
			$this->_userID,
			(int)$groupID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * get all dates for day
	 *
	 * @param int $day Day
	 * @param int $month Month
	 * @param int $year Year
	 * @return array
	 */
	function GetDatesForDay($day, $month, $year)
	{
		$start = mktime(0, 0, 0, $month, $day, $year);
		$end = mktime(23, 59, 59, $month, $day, $year);
		return($this->GetDatesForTimeframe($start, $end));
	}

	/**
	 * get date
	 *
	 * @param int $id Date ID
	 * @return array
	 */
	function GetDate($id)
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}dates WHERE id=? AND user=?',
			(int)$id,
			$this->_userID);
		if($res->RowCount() == 1)
		{
			$row = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			return($row);
		}

		return(false);
	}

	/**
	 * get date attendees
	 *
	 * @param int $id Date ID
	 * @return array
	 */
	function GetDateAttendees($id)
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT {pre}adressen.vorname AS vorname,{pre}adressen.nachname AS nachname,{pre}adressen.id AS id,{pre}adressen.email AS email,{pre}adressen.work_email AS work_email,{pre}adressen.default_address AS default_address FROM {pre}adressen,{pre}dates_attendees WHERE {pre}adressen.id={pre}dates_attendees.address AND {pre}dates_attendees.date=? AND {pre}adressen.user=?',
			$id,
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$result[$row['id']] = $row;
		}
		$res->Free();

		return($result);
	}

	/**
	 * generate and return a mini month calendar
	 *
	 * @param int $month Month
	 * @param int $year Year
	 * @param int $userID User ID (when used without class instance)
	 * @return string
	 */
	function GenerateMiniCalendar($month = -1, $year = -1, $userID = -1, $className = 'miniCalendarTable')
	{
		global $lang_user;

		if($month == -1)		$month = (int)date('m');
		if($year == -1)			$year = (int)date('Y');
		$thisMonth = 			(int)date('m') == $month && (int)date('Y') == $year;
		list($columns, $days) =	$this->GenerateCalendar($month, $year, $userID);

		// start
		$html  = '<table class="'.$className.'">' . "\n";

		// month name
		$html .= '	<tr>' . "\n";
		$html .= sprintf('		<th class="Caption" colspan="7"><a href="organizer.calendar.php?view=month&date=%d&sid=%s">%s</a></th>' . "\n",
			mktime(0, 0, 0, $month, 1, $year),
			session_id(),
			_strftime('%B %Y', mktime(0, 0, 0, $month, 1, $year)));
		$html .= '	</tr>' . "\n";

		// column headings
		$html .= '	<tr>' . "\n";
		foreach($columns as $wDay)
			$html .= '		<th>' . $lang_user['weekdays_long'][$wDay] . '</th>' . "\n";
		$html .= '	</tr>' . "\n";

		// days
		$html .= '	<tr>' . "\n";
		$c = 0;
		foreach($days as $arrayKey=>$dayItem)
		{
			// add day cell
			if($dayItem === false)
				$html .= '		<td class="Empty"></td>' . "\n";
			else
				$html .= sprintf('		<td%s><a title="%d %s" href="organizer.calendar.php?date=%d&sid=%s">%d</a></td>' . "\n",
					($thisMonth && $dayItem['day'] == (int)date('d')
								? ' class="Today"'
								: (count($dayItem['dates']) > 0
									? ' class="Date"'
									: '')),
					count($dayItem['dates']),
					$lang_user['dates'],
					mktime(0, 0, 0, $month, $dayItem['day'], $year),
					session_id(),
					$dayItem['day']);

			// increment / reset cell counter
			$c++;
			if($c == 7)
			{
				$html .= '	</tr>' . "\n";
				if($arrayKey != array_pop(array_keys($days)))
					$html .= '	<tr>' . "\n";
				$c = 0;
			}
		}
		if($c != 0)
		{
			for($i=1; $i<=7-$c; $i++)
				$html .= '		<td class="Empty"></td>' . "\n";
			$html .= '	</tr>' . "\n";
		}

		// finish
		$html .= '</table>' . "\n";
		return($html);
	}

	/**
	 * generate and return a month calendar
	 *
	 * @param int $month Month
	 * @param int $year Year
	 * @param int $userID User ID (when used without class instance)
	 * @param int $group Group (-2 = all)
	 * @return array Columns, Days
	 */
	function GenerateCalendar($month = -1, $year = -1, $userID = -1, $group = -2)
	{
		global $userRow;

		if($month == -1)	$month = (int)date('m');
		if($year == -1)		$year = (int)date('Y');
		$daysInMonth		= BMCalendar::GetDaysInMonth($month, $year);
		$firstWeekDay		= BMCalendar::GetDayOfWeek(1, $month, $year);

		// get first day of week and dates
		if($userID == -1)
		{
			if($this->_userID != $userRow['id'])
				$userRow	= BMUser::Fetch($this->_userID);
			$firstDay		= $userRow['c_firstday'];
			$datesStart 	= mktime(0, 0, 0, $month, 1, $year);
			$datesEnd 		= mktime(23, 59, 59, $month, $daysInMonth, $year);
			$dates 			= $this->GetDatesForTimeframe($datesStart, $datesEnd, $group);
		}

		// called statically
		else
		{
			if($userID != $userRow['id'])
				$userRow	= BMUser::Fetch($userID);
			$firstDay		= $userRow['c_firstday'];
			$userCalendar 	= _new('BMCalendar', array($userID));
			$datesStart 	= mktime(0, 0, 0, $month, 1, $year);
			$datesEnd 		= mktime(23, 59, 59, $month, $daysInMonth, $year);
			$dates 			= $userCalendar->GetDatesForTimeframe($datesStart, $datesEnd, $group);
		}

		// columns
		$columns = array();
		for($w=$firstDay,$i=0; $i<=6; $i++)
		{
			$columns[] = $w;
			$w++;
			if($w > 6) $w = 0;
		}

		// days
		$days = array();
		for($i=1; $i<=$daysInMonth; $i++)
		{
			$dateStart 	= mktime(0, 0, 0, $month, $i, $year);
			$dateEnd 	= mktime(23, 59, 59, $month, $i, $year);
			$dayDates	= array();

			// find dates
			foreach($dates as $key=>$val)
			{
				if(($val['startdate'] >= $dateStart && $val['startdate'] <= $dateEnd)
					|| ($val['enddate'] >= $dateStart && $val['enddate'] <= $dateEnd)
					|| ($val['startdate'] <= $dateStart && $val['enddate'] >= $dateEnd))
					$dayDates[$key] = $val;
			}

			// add day to array
			$days[] = array(
				'day'		=> $i,
				'dayStart'	=> $dateStart,
				'dayEnd'	=> $dateEnd,
				'dates'		=> $dayDates,
				'today'		=> $dateStart == mktime(0, 0, 0, date('m'), date('d'), date('Y'))
			);
		}

		// move to right day
		foreach($columns as $columnWDay)
		{
			if($columnWDay == $firstWeekDay)
				break;
			array_unshift($days, false);
		}

		// return!
		return(array($columns, $days));
	}

	/**
	 * add a date
	 *
	 * @param array $row Date row
	 * @param array $attendees Attendees
	 * @return int
	 */
	function AddDate($row, $attendees)
	{
		global $db;

		$db->Query('INSERT INTO {pre}dates(user,title,location,text,`group`,startdate,enddate,reminder,flags,repeat_flags,repeat_times,repeat_value,repeat_extra1,repeat_extra2,dav_uri,dav_uid) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
			$this->_userID,
			$row['title'],
			$row['location'],
			$row['text'],
			$row['group'],
			$row['startdate'],
			$row['enddate'],
			$row['reminder'],
			$row['flags'],
			$row['repeat_flags'],
			$row['repeat_times'],
			$row['repeat_value'],
			$row['repeat_extra1'],
			$row['repeat_extra2'],
			$row['dav_uri'],
			$row['dav_uid']);

		// attendees
		if($dateID = $db->InsertId())
		{
			ChangelogAdded(BMCL_TYPE_CALENDAR, $dateID, time());

			foreach($attendees as $contactID)
			{
				$db->Query('INSERT INTO {pre}dates_attendees(date,address) VALUES(?,?)',
					$dateID,
					$contactID);
			}
		}

		return($dateID);
	}

	/**
	 * delete a date
	 *
	 * @param int $id Date ID
	 * @return bool
	 */
	function DeleteDate($id)
	{
		global $db;

		$db->Query('DELETE FROM {pre}dates WHERE user=? AND id=?',
			$this->_userID,
			(int)$id);
		if($db->AffectedRows() == 1)
		{
			ChangelogDeleted(BMCL_TYPE_CALENDAR, $id, time());
			$db->Query('DELETE FROM {pre}dates_attendees WHERE date=?',
				(int)$id);
			return(true);
		}

		return(false);
	}

	/**
	 * change date
	 *
	 * @param int $id Date ID
	 * @param array $row Date row
	 * @param array $attendees Attendees
	 * @return bool
	 */
	function ChangeDate($id, $row, $attendees)
	{
		global $db;

		$db->Query('UPDATE {pre}dates SET title=?, location=?, text=?, `group`=?, startdate=?, enddate=?, reminder=?, flags=?, repeat_flags=?, repeat_times=?, repeat_value=?, repeat_extra1=?, repeat_extra2=?, `dav_uri`=?, `dav_uid`=? WHERE id=? AND user=?',
			$row['title'],
			$row['location'],
			$row['text'],
			$row['group'],
			$row['startdate'],
			$row['enddate'],
			$row['reminder'],
			$row['flags'],
			$row['repeat_flags'],
			$row['repeat_times'],
			$row['repeat_value'],
			$row['repeat_extra1'],
			$row['repeat_extra2'],
			$row['dav_uri'],
			$row['dav_uid'],
			(int)$id,
			$this->_userID);

		ChangelogUpdated(BMCL_TYPE_CALENDAR, $id, time());

		foreach($attendees as $key=>$val)
			$attendees[$key] = (int)$val;

		if(count($attendees) > 0)
		{
			$db->Query('DELETE FROM {pre}dates_attendees WHERE date=? AND address NOT IN(' . implode(',', $attendees) . ')',
				(int)$id);
			foreach($attendees as $contactID)
				$db->Query('REPLACE INTO {pre}dates_attendees(date,address) VALUES(?,?)',
					(int)$id,
					$contactID);
		}
		else
		{
			$db->Query('DELETE FROM {pre}dates_attendees WHERE date=?',
				(int)$id);
		}

		return(true);
	}

	/**
	 * generate date row from form data
	 *
	 * @return array
	 */
	function Form2Row()
	{
		//
		// init
		//
		$row = array();
		$row['flags']			= 0;
		$row['repeat_flags']	= 0;
		$row['repeat_value']	= 0;
		$row['repeat_times']	= 0;
		$row['repeat_extra1']	= '';
		$row['repeat_extra2']	= '';

		//
		// misc
		//
		$row['title']			= $_REQUEST['title'];
		$row['location']		= $_REQUEST['location'];
		$row['text']			= $_REQUEST['text'];
		$row['startdate']		= SmartyDateTime('startdate');
		$row['group']			= $_REQUEST['group'];

		//
		// date duration
		//
		if($_REQUEST['wholeDay'] == 1)
		{
			$row['flags']		|= CLNDR_WHOLE_DAY;
			$row['enddate']		= $row['startdate'] + 59;
		}
		else
		{
			$row['enddate']		= max($row['startdate']+TIME_ONE_MINUTE,
									$row['startdate']
										+ $_REQUEST['durationHours'] * TIME_ONE_HOUR
										+ $_REQUEST['durationMinutes'] * TIME_ONE_MINUTE);
		}

		//
		// reminder?
		//
		$row['reminder']		= max(0, $_REQUEST['reminder']) * TIME_ONE_MINUTE;
		if(isset($_REQUEST['reminder_email']))
			$row['flags']		|= CLNDR_REMIND_EMAIL;
		if(isset($_REQUEST['reminder_sms']))
			$row['flags']		|= CLNDR_REMIND_SMS;
		if(isset($_REQUEST['reminder_notify']))
			$row['flags']		|= CLNDR_REMIND_NOTIFY;

		//
		// repeating preferences
		//
		if(isset($_REQUEST['repeating']))
		{
			//
			// duration
			//
			if($_REQUEST['repeat_until'] == 'endless')
			{
				$row['repeat_times']	= 0;
				$row['repeat_flags'] 	|= CLNDR_REPEATING_UNTIL_ENDLESS;
			}
			else if($_REQUEST['repeat_until'] == 'count')
			{
				$row['repeat_times']	= max(1, $_REQUEST['repeat_until_count']);
				$row['repeat_flags']	|= CLNDR_REPEATING_UNTIL_COUNT;
			}
			else if($_REQUEST['repeat_until'] == 'date')
			{
				$row['repeat_times']	= max($row['startdate']+TIME_ONE_MINUTE,
											SmartyDateTime('repeat_until_date'));
				$row['repeat_flags'] 	|= CLNDR_REPEATING_UNTIL_DATE;
			}

			//
			// interval
			//
			if($_REQUEST['repeat_interval'] == 'daily')
			{
				$row['repeat_flags']	|= CLNDR_REPEATING_DAILY;
				$row['repeat_value']	= max(1, $_REQUEST['repeat_interval_daily']);
				$row['repeat_extra1']	= isset($_REQUEST['repeat_daily_exceptions']) && is_array($_REQUEST['repeat_daily_exceptions']) && count($_REQUEST['repeat_daily_exceptions']) > 0
											? implode(',', $_REQUEST['repeat_daily_exceptions'])
											: '';
			}
			else if($_REQUEST['repeat_interval'] == 'weekly')
			{
				$row['repeat_flags']	|= CLNDR_REPEATING_WEEKLY;
				$row['repeat_value']	= max(1, $_REQUEST['repeat_interval_weekly']);
			}
			else if($_REQUEST['repeat_interval'] == 'monthly_mday')
			{
				$row['repeat_flags']	|= CLNDR_REPEATING_MONTHLY_MDAY;
				$row['repeat_value']	= max(1, $_REQUEST['repeat_interval_monthly_mday']);
				$row['repeat_extra1']	= max(1, min(31, $_REQUEST['repeat_interval_monthly_mday_extra1']));
			}
			else if($_REQUEST['repeat_interval'] == 'monthly_wday')
			{
				$row['repeat_flags']	|= CLNDR_REPEATING_MONTHLY_WDAY;
				$row['repeat_value']	= max(1, $_REQUEST['repeat_interval_monthly_wday']);
				$row['repeat_extra1']	= max(0, min(4, $_REQUEST['repeat_interval_monthly_wday_extra1']));
				$row['repeat_extra2']	= max(0, min(6, $_REQUEST['repeat_interval_monthly_wday_extra2']));
			}
			else if($_REQUEST['repeat_interval'] == 'yearly')
			{
				$row['repeat_flags']	|= CLNDR_REPEATING_YEARLY;
				$row['repeat_value']	= max(1, $_REQUEST['repeat_interval_yearly']);
			}
		}

		return($row);
	}
}
