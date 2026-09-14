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

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

include_once B1GMAIL_DIR.'serverlib/organizer.collections.inc.php';

/**
 * Ensure dates_attendees.partstat exists (RSVP status).
 */
function bmCalendarEnsureAttendeePartstat()
{
    global $db, $mysql;

    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $table = $mysql['prefix'].'dates_attendees';
    $res = $db->Query('SHOW COLUMNS FROM `'.$table.'` LIKE ?', 'partstat');
    if ($res->RowCount() == 0) {
        $db->Query('ALTER TABLE `'.$table.'` ADD `partstat` varchar(20) NOT NULL DEFAULT \'needs-action\'');
    }
    $res->Free();
}

/**
 * Normalize iCalendar PARTSTAT to stored value.
 *
 * @param string $partstat
 *
 * @return string needs-action|accepted|declined|tentative
 */
function bmCalendarNormalizePartstat($partstat)
{
    $partstat = strtolower(str_replace('_', '-', trim($partstat)));

    if (in_array($partstat, ['accepted', 'declined', 'tentative', 'needs-action'], true)) {
        return $partstat;
    }

    return 'needs-action';
}

/**
 * calendar class.
 */
class BMCalendar
{
    private $_userID;
    private $_sharedCalendars;

    /**
     * constructor.
     *
     * @param int $userID User ID
     *
     * @return BMCalendar
     */
    public function __construct($userID)
    {
        $this->_userID = $userID;
        bmOrganizerEnsureCollections();
    }

    /**
     * Get start day of week.
     *
     * @param int $timestamp Timestamp of a date somewhere in the week
     *
     * @return int Timestamp of week start day, 00:00:00
     */
    public function GetWeekStartDay($timestamp)
    {
        $calWeek = date('W', $timestamp);

        while ($timestamp > 0) {
            if (date('W', $timestamp - TIME_ONE_DAY) != $calWeek) {
                break;
            }
            $timestamp -= TIME_ONE_DAY;
        }

        return mktime(0, 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
    }

    /**
     * Get end day of week.
     *
     * @param int $timestamp Timestamp of a date somewhere in the week
     *
     * @return int Timestamp of week end day, 00:00:00
     */
    public function GetWeekEndDay($timestamp)
    {
        $calWeek = date('W', $timestamp);

        while ($timestamp < $timestamp + 8 * TIME_ONE_DAY) {
            if (date('W', $timestamp + TIME_ONE_DAY) != $calWeek) {
                break;
            }
            $timestamp += TIME_ONE_DAY;
        }

        return mktime(0, 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
    }

    /**
     * get day of week.
     *
     * @param int $day   Day
     * @param int $month Month
     * @param int $year  Year
     *
     * @return int
     */
    public static function GetDayOfWeek($day, $month, $year)
    {
        return date('w', mktime(1, 1, 1, $month, $day, $year));
    }

    /**
     * get days in month.
     *
     * @param int $month Month
     * @param int $year  Year
     *
     * @return int
     */
    public static function GetDaysInMonth($month, $year)
    {
        return date('t', mktime(1, 1, 1, $month, 1, $year));
    }

    /**
     * get days in year.
     *
     * @param int $year Year
     *
     * @return int
     */
    public static function GetDaysInYear($year)
    {
        return checkdate(2, 29, $year) ? 366 : 365;
    }

    /**
     * prepare date for another time.
     *
     * @param array $row
     * @param int   $time
     * @param int   $start
     * @param int   $end
     *
     * @return array
     */
    public static function _dateFor($row, $time, $start, $end)
    {
        $row['enddate'] = min($end, ($row['enddate'] - $row['startdate']) + $time);
        $row['startdate'] = max($start, $time);

        return $row;
    }

    /**
     * get date occurences for a certain timeframe.
     *
     * @param array $row   Row
     * @param int   $start Start
     * @param int   $end   End
     *
     * @return array Row of occurenced
     */
    public static function _getOccurencesInTimeframe($row, $start, $end)
    {
        $result = [];

        if ($start > $end) {
            return $result;
        }

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
        $repeating = ($row['repeat_flags'] & CLNDR_REPEATING_DAILY)
                            || ($row['repeat_flags'] & CLNDR_REPEATING_MONTHLY_MDAY)
                            || ($row['repeat_flags'] & CLNDR_REPEATING_MONTHLY_WDAY)
                            || ($row['repeat_flags'] & CLNDR_REPEATING_WEEKLY)
                            || ($row['repeat_flags'] & CLNDR_REPEATING_YEARLY);

        // 'fixed' first occurency
        if (($row['startdate'] >= $start && $row['startdate'] <= $end)
            || ($row['enddate'] > $start && $row['enddate'] <= $end)
            || ($row['startdate'] <= $start && $row['enddate'] >= $end)) {
            $result[] = BMCalendar::_dateFor($row, $row['startdate'], $start, $end);
        }

        // repeating
        if ($repeating) {
            // daily
            if ($repeatFlags & CLNDR_REPEATING_DAILY) {
                $exceptDays = explode(',', $row['repeat_extra1']);
                foreach ($exceptDays as $key => $val) {
                    if ($val != '') {
                        $exceptDays[$key] = (int) $val;
                    }
                }
                for ($time = $repeatStartDate + ($repeatValue - 1) * TIME_ONE_DAY; $time <= $timeTo; $time += $repeatValue * TIME_ONE_DAY) {
                    if (!in_array((int) date('w', $time), $exceptDays, true)) {
                        ++$repeatCount;
                        if (($repeatFlags & CLNDR_REPEATING_UNTIL_COUNT) && $repeatCount > $repeatTimes) {
                            break;
                        }
                        if ($time >= $start && $time <= $end) {
                            $result[] = BMCalendar::_dateFor($row,
                                mktime(0, 0, 0, date('m', $time), date('d', $time), date('Y', $time)) + $timeOffset,
                                $start,
                                $end);
                        }
                    }
                }
            }

            // weekly
            elseif ($repeatFlags & CLNDR_REPEATING_WEEKLY) {
                for ($time = $repeatStartDate - TIME_ONE_DAY / 2 + $repeatValue * TIME_ONE_WEEK; $time <= $timeTo; $time += $repeatValue * TIME_ONE_WEEK) {
                    ++$repeatCount;
                    if (($repeatFlags & CLNDR_REPEATING_UNTIL_COUNT) && $repeatCount > $repeatTimes) {
                        break;
                    }
                    if ($time >= $start && $time <= $end) {
                        $result[] = BMCalendar::_dateFor($row,
                            mktime(0, 0, 0, date('m', $time), date('d', $time), date('Y', $time)) + $timeOffset,
                            $start,
                            $end);
                    }
                }
            }

            // monthly
            elseif (($repeatFlags & CLNDR_REPEATING_MONTHLY_MDAY)
                    || ($repeatFlags & CLNDR_REPEATING_MONTHLY_WDAY)) {
                $monthRepeatStartDate = mktime(0, 0, 0, date('m', $repeatStartDate - TIME_ONE_DAY / 2), 1, date('Y', $repeatStartDate - TIME_ONE_DAY / 2));

                for ($time = $monthRepeatStartDate; $time <= $timeTo + TIME_ONE_MONTH; $time += $repeatValue * TIME_ONE_MONTH) {
                    $time += TIME_ONE_WEEK;
                    $time = mktime(0, 0, 0, date('m', $time), 1, date('Y', $time));

                    $day = 1;
                    $month = (int) date('m', $time);
                    $year = (int) date('Y', $time);

                    if ($repeatFlags & CLNDR_REPEATING_MONTHLY_MDAY) {
                        $day = max(1, $row['repeat_extra1']);
                        if ($day > BMCalendar::GetDaysInMonth($month, $year)) {
                            continue;
                        }
                    } elseif ($repeatFlags & CLNDR_REPEATING_MONTHLY_WDAY) {
                        $mDays = [];
                        for ($mDay = 1; $mDay < BMCalendar::GetDaysInMonth($month, $year); ++$mDay) {
                            if ((int) date('w', mktime(0, 0, 0, $month, $mDay, $year))
                                    == $row['repeat_extra2']) {
                                $mDays[] = $mDay;
                            }
                        }
                        if (count($mDays) > 0) {
                            if ($row['repeat_extra1'] == 4) {
                                $day = array_pop($mDays);
                            } elseif ($row['repeat_extra1'] < count($mDays)) {
                                $day = $mDays[$row['repeat_extra1']];
                            } else {
                                continue;
                            }
                        }
                    }

                    $myTime = mktime(0, 0, 0, $month, $day, $year) + $timeOffset;
                    if ($myTime >= $repeatStartDate + $timeOffset) {
                        ++$repeatCount;
                        if (($repeatFlags & CLNDR_REPEATING_UNTIL_COUNT) && $repeatCount > $repeatTimes) {
                            break;
                        }
                        if ($myTime >= $start && $myTime <= $end) {
                            $result[] = BMCalendar::_dateFor($row, $myTime, $start, $end);
                        }
                    }
                }
            }

            // yearly
            elseif ($repeatFlags & CLNDR_REPEATING_YEARLY) {
                $repeatY = date('Y', $repeatStartDate);
                while (true) {
                    ++$repeatCount;
                    if (($repeatFlags & CLNDR_REPEATING_UNTIL_COUNT) && $repeatCount > $repeatTimes) {
                        break;
                    }

                    $repeatY += $repeatValue;
                    $time = mktime(date('H', $startDate),
                        date('i', $startDate),
                        date('s', $startDate),
                        date('n', $startDate),
                        date('j', $startDate),
                        $repeatY);

                    if ($time > $end) {
                        break;
                    }

                    if ($time >= $start) {
                        $result[] = BMCalendar::_dateFor($row,
                            $time,
                            $start,
                            $end);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * date sort callback.
     */
    public function sortDatesForTimeframe($a, $b)
    {
        return $a['startdate'] - $b['startdate'];
    }

    /**
     * get dates in timeframe.
     *
     * @param int $start Start time
     * @param int $end   End time
     * @param int $group Group (-2 = all)
     *
     * @return array
     */
    public function GetDatesForTimeframe($start, $end, $group = -2, $calendarIDs = null)
    {
        global $db;

        $result = [];
        $accessIDs = array_map('intval', array_keys($this->GetCalendars() + $this->GetSharedCalendars()));
        if (is_array($calendarIDs) && count($calendarIDs) > 0) {
            $ids = array_values(array_intersect(array_map('intval', $calendarIDs), $accessIDs));
        } else {
            $ids = $accessIDs;
        }
        if (count($ids) === 0) {
            return $result;
        }
        $calFilter = ' AND `calendar_id` IN('.implode(',', $ids).')';

        $groupColors = [];
        $res = $db->Query('SELECT `id`,`color` FROM {pre}dates_groups WHERE `calendar_id` IN('.implode(',', $ids).')');
        while ($gRow = $res->FetchArray(MYSQLI_ASSOC)) {
            $groupColors[(int) $gRow['id']] = (int) $gRow['color'];
        }
        $res->Free();

        $calendarColors = [];
        $res = $db->Query('SELECT `id`,`color` FROM {pre}calendars WHERE `id` IN('.implode(',', $ids).')');
        while ($cRow = $res->FetchArray(MYSQLI_ASSOC)) {
            $calendarColors[(int) $cRow['id']] = (int) $cRow['color'];
        }
        $res->Free();

        // get dates
        $res = $db->Query('SELECT id,user,title,location,text,`group`,startdate,enddate,reminder,flags,repeat_flags,repeat_times,repeat_value,repeat_extra1,repeat_extra2,`calendar_id` '
                            .'FROM {pre}dates WHERE (((startdate>=? OR enddate<=? OR (startdate<=? AND enddate>=?)) AND repeat_flags=0) OR (repeat_flags>0 AND ((repeat_flags&'.CLNDR_REPEATING_UNTIL_DATE.')=0 OR repeat_value<=?)))'
                            .($group > -2 ? ' AND `group`='.(int) $group : '')
                            .$calFilter,
                            $start,
                            $end,
                            $start,
                            $end,
                            $start);
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $gid = (int) $row['group'];
            $cid = (int) $row['calendar_id'];
            if ($gid > 0 && isset($groupColors[$gid])) {
                $row['displayColor'] = $groupColors[$gid];
            } else {
                $row['displayColor'] = isset($calendarColors[$cid]) ? $calendarColors[$cid] : 0;
            }
            $dates = BMCalendar::_getOccurencesInTimeframe($row, $start, $end);
            if (count($dates) > 0) {
                $result = array_merge($result, $dates);
            }
        }
        $res->Free();

        // sort by start date
        uasort($result, [&$this, 'sortDatesForTimeframe']);

        return $result;
    }

    /**
     * send date notification.
     *
     * @param array $date Date row
     *
     * @return bool
     */
    private static function _sendNotification($date)
    {
        global $db, $bm_prefs;

        $ok = false;
        $user = _new('BMUser', [$date['user']]);

        // post notification (in-app + optionally push, if the push flag is set)
        $wantsNotify = (bool) ($date['flags'] & CLNDR_REMIND_NOTIFY);
        $wantsPush = (bool) ($date['flags'] & CLNDR_REMIND_PUSH);
        if ($wantsNotify || $wantsPush) {
            self::_postDateReminder($user, $date, $wantsNotify, $wantsPush);
            $ok = true;
        }

        // send e-mail notification
        if ($date['flags'] & CLNDR_REMIND_EMAIL) {
            // vars
            $vars = [
                'title' => $date['title'],
                'location' => $date['location'],
                'message' => $date['text'],
                'date' => date('d.m.Y', $date['startdate']),
                'time' => date('H:i', $date['startdate']),
            ];
            if (SystemMail($bm_prefs['passmail_abs'],
                $user->_row['email'],
                GetPhraseForUser($date['user'], 'lang_custom', 'clndr_subject'),
                'clndr_date_msg',
                $vars,
                $date['user'])) {
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
        if ($date['flags'] & CLNDR_REMIND_SMS) {
            // prepare SMS
            $toNo = $user->_row['mail2sms_nummer'];
            $smsText = GetPhraseForUser($date['user'], 'lang_custom', 'clndr_sms');
            $smsText = str_replace('%%date%%', date('d.m.Y', $date['startdate']), $smsText);
            $smsText = str_replace('%%time%%', date('H:i', $date['startdate']), $smsText);
            $smsText = str_replace('%%subtitle%%', $date['title'], $smsText);
            if (strlen($smsText) > 160) {
                $smsText = substr($smsText, 0, 157).'...';
            }

            // send SMS
            if (!class_exists('BMSMS')) {
                include B1GMAIL_DIR.'serverlib/sms.class.php';
            }
            $sms = _new('BMSMS', [$date['user'], &$user]);
            if ($sms->Send($bm_prefs['clndr_sms_abs'], $toNo, $smsText, $bm_prefs['clndr_sms_type'], true, true)) {
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

        // in-app + push for users the calendar is shared with (read or write)
        if (self::_notifyDateSharees($date)) {
            $ok = true;
        }

        // update last_reminder
        if ($ok) {
            $db->Query('UPDATE {pre}dates SET last_reminder=? WHERE id=?',
                time(),
                $date['id']);
        }

        // return
        return $ok;
    }

    /**
     * In-app notification and/or Web Push for a calendar reminder.
     *
     * Sending is controlled independently by the two flags:
     *   - $doInApp  – create a database notification (visible in-app)
     *   - $doPush   – deliver a Web Push message to the user's devices
     *
     * If both are true, we use the standard PostNotification() path (in-app + push).
     * If only in-app is requested, PostNotification() is called with $suppressPush=true.
     * If only push is requested, we bypass the in-app notification entirely and send
     * a stand-alone Web Push via BMPush::send().
     *
     * @param BMUser $user
     * @param array  $date
     * @param bool   $doInApp
     * @param bool   $doPush
     */
    private static function _postDateReminder($user, $date, $doInApp = true, $doPush = true)
    {
        global $lang_custom;

        if ($doInApp) {
            $user->PostNotification('notify_date',
                [HTMLFormat($date['title'])],
                sprintf('showCalendarDate(%d,%d,%d,false)', $date['id'], $date['startdate'], $date['enddate']),
                '%%tpldir%%images/li/notify_calendar.png',
                $date['startdate'],
                0,
                NOTIFICATION_FLAG_USELANG | NOTIFICATION_FLAG_JSLINK,
                '::dateReminder',
                false,
                !$doPush);

            return;
        }

        if (!$doPush) {
            return;
        }

        // Push-only path: no in-app notification, deliver Web Push directly.
        if (!class_exists('BMPush', false)) {
            include B1GMAIL_DIR.'serverlib/push.class.php';
        }
        if (!BMPush::isEnabled()) {
            return;
        }

        $phrase = isset($lang_custom['notify_date']) ? $lang_custom['notify_date'] : 'notify_date';
        $body = @sprintf($phrase, HTMLFormat($date['title']));
        if ($body === false) {
            $body = HTMLFormat($date['title']);
        }

        BMPush::send([
            'area' => BMPush::AREA_USER,
            'targetId' => (int) $user->_id,
            'type' => BMPush::TYPE_CALENDAR,
            'body' => $body,
            'url' => sprintf('organizer.calendar.php?date=%d&', (int) $date['id']),
            'icon' => 'pwa-icon.php?size=192',
        ]);
    }

    /**
     * Remind users a calendar is shared with.
     *
     * @param array $date
     *
     * @return bool
     */
    private static function _notifyDateSharees($date)
    {
        $calendarId = isset($date['calendar_id']) ? (int) $date['calendar_id'] : 0;
        $ownerId = (int) $date['user'];
        if ($calendarId <= 0 || $ownerId <= 0) {
            return false;
        }

        $sent = false;
        foreach (bmOrganizerListShareeIds(BM_ORGANIZER_SHARE_CAL, $calendarId, $ownerId) as $shareeId) {
            if ($shareeId <= 0 || $shareeId === $ownerId) {
                continue;
            }
            $sharee = _new('BMUser', [$shareeId]);
            if (!is_array($sharee->_row) || $sharee->_row['gesperrt'] === 'delete') {
                continue;
            }
            self::_postDateReminder($sharee, $date);
            $sent = true;
        }

        return $sent;
    }

    /**
     * send notifications.
     *
     * @return int Number of sent notifications
     */
    public static function ProcessNotifications()
    {
        global $db, $bm_prefs;

        $notifications = 0;

        // today!
        $start = time() - 31 * TIME_ONE_DAY;
        $end = time() + 31 * TIME_ONE_DAY;

        // get dates
        $remindDates = [];
        $res = $db->Query('SELECT {pre}dates.id,{pre}dates.last_reminder,{pre}dates.user,{pre}dates.title,{pre}dates.location,{pre}dates.`text`,{pre}dates.`group`,{pre}dates.startdate,{pre}dates.enddate,{pre}dates.reminder,{pre}dates.flags,{pre}dates.repeat_flags,{pre}dates.repeat_times,{pre}dates.repeat_value,{pre}dates.repeat_extra1,{pre}dates.repeat_extra2,{pre}dates.`calendar_id` '
                            .'FROM {pre}dates '
                            .'INNER JOIN {pre}users ON {pre}users.`id`={pre}dates.`user` '
                            .'WHERE {pre}users.`gesperrt`!=\'delete\' AND ({pre}dates.flags&'.(CLNDR_REMIND_EMAIL | CLNDR_REMIND_NOTIFY | CLNDR_REMIND_SMS | CLNDR_REMIND_PUSH).')!=0 AND ((({pre}dates.startdate>=? OR {pre}dates.enddate<=? OR ({pre}dates.startdate<=? AND {pre}dates.enddate>=?)) AND {pre}dates.repeat_flags=0) OR ({pre}dates.repeat_flags>0 AND (({pre}dates.repeat_flags&'.CLNDR_REPEATING_UNTIL_DATE.')=0 OR {pre}dates.repeat_value<=?))) '
                            .'ORDER BY {pre}dates.user ASC',
                            $start,
                            $end,
                            $start,
                            $end,
                            $start);
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $dates = BMCalendar::_getOccurencesInTimeframe($row, $start, $end);
            if (count($dates) > 0) {
                $remindDates = array_merge($remindDates, $dates);
            }
        }
        $res->Free();

        // notifications
        foreach ($remindDates as $date) {
            // send notification now?
            if (time() >= $date['startdate'] - $date['reminder'] - $bm_prefs['cron_interval']
                && time() <= $date['startdate']) {
                // not sent yet?
                if ($date['last_reminder'] < $date['startdate'] - $date['reminder'] - $bm_prefs['cron_interval']) {
                    // process
                    if (BMCalendar::_sendNotification($date)) {
                        ++$notifications;
                    }
                }
            }
        }

        // return count of successful notifications
        return $notifications;
    }

    /**
     * Get all calendars of the user.
     *
     * @return array
     */
    public function GetCalendars()
    {
        global $db, $lang_user;

        $result = [];
        $res = $db->Query('SELECT `id`,`user`,`title`,`color`,`is_default`,`dav_uri`,`dav_uid` FROM {pre}calendars WHERE `user`=? ORDER BY `is_default` DESC, `title` ASC',
            $this->_userID);
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            if (empty($row['is_default'])) {
                $row['dav_uri'] = bmOrganizerUpgradeGeneratedDavUri($this->_userID, (int) $row['id'], $row['title'], $row['dav_uri'], 'cal', '{pre}calendars');
            }
            bmOrganizerHydrateCollectionTitle($row, $lang_user['calendar']);
            $result[(int) $row['id']] = $row;
        }
        $res->Free();

        if (count($result) === 0) {
            $this->EnsureDefaultCalendar();
            $res = $db->Query('SELECT `id`,`user`,`title`,`color`,`is_default`,`dav_uri`,`dav_uid` FROM {pre}calendars WHERE `user`=? ORDER BY `is_default` DESC, `title` ASC',
                $this->_userID);
            while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
                if (empty($row['is_default'])) {
                    $row['dav_uri'] = bmOrganizerUpgradeGeneratedDavUri($this->_userID, (int) $row['id'], $row['title'], $row['dav_uri'], 'cal', '{pre}calendars');
                }
                bmOrganizerHydrateCollectionTitle($row, $lang_user['calendar']);
                $result[(int) $row['id']] = $row;
            }
            $res->Free();
        }

        return $result;
    }

    /**
     * Calendars shared with this user.
     *
     * @return array
     */
    public function GetSharedCalendars()
    {
        if ($this->_sharedCalendars === null) {
            $this->_sharedCalendars = bmOrganizerListSharedWith(BM_ORGANIZER_SHARE_CAL, $this->_userID);
        }

        return $this->_sharedCalendars;
    }

    /**
     * Own or shared calendar.
     *
     * @param int $id
     *
     * @return array|false
     */
    public function GetAccessibleCalendar($id)
    {
        $own = $this->GetCalendar($id);
        if ($own !== false) {
            $own['shared'] = false;
            $own['share_access'] = 'owner';

            return $own;
        }
        $shared = $this->GetSharedCalendars();
        $id = (int) $id;

        return isset($shared[$id]) ? $shared[$id] : false;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function CanWriteCalendar($id)
    {
        $cal = $this->GetAccessibleCalendar($id);
        if ($cal === false) {
            return false;
        }

        return empty($cal['shared']) || $cal['share_access'] === BM_ORGANIZER_ACCESS_WRITE;
    }

    /**
     * Get a calendar.
     *
     * @param int $id
     *
     * @return array|false
     */
    public function GetCalendar($id)
    {
        global $db, $lang_user;

        $res = $db->Query('SELECT `id`,`user`,`title`,`color`,`is_default`,`dav_uri`,`dav_uid` FROM {pre}calendars WHERE `user`=? AND `id`=?',
            $this->_userID,
            (int) $id);
        if ($res->RowCount() !== 1) {
            $res->Free();

            return false;
        }
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();
        bmOrganizerHydrateCollectionTitle($row, $lang_user['calendar']);

        return $row;
    }

    /**
     * Calendar IDs currently shown in the overlay (all, if none stored).
     *
     * @param array|null $calendars
     *
     * @return int[]
     */
    public function GetVisibleCalendarIDs($calendars = null)
    {
        global $thisUser;

        if ($calendars === null) {
            $calendars = $this->GetCalendars() + $this->GetSharedCalendars();
        }
        $all = array_map('intval', array_keys($calendars));
        if (!is_object($thisUser) || !method_exists($thisUser, 'GetPref')) {
            return $all;
        }
        $pref = $thisUser->GetPref('visibleCalendars');
        if ($pref === false || $pref === '') {
            return $all;
        }
        $wanted = array_filter(array_map('intval', explode(',', $pref)));
        $filtered = array_values(array_intersect($wanted, $all));

        return count($filtered) > 0 ? $filtered : $all;
    }

    /**
     * ID of the default calendar.
     *
     * @return int
     */
    public function GetDefaultCalendarID()
    {
        global $db;

        $res = $db->Query('SELECT `id` FROM {pre}calendars WHERE `user`=? AND `is_default`=1 LIMIT 1',
            $this->_userID);
        if ($res->RowCount() === 1) {
            list($id) = $res->FetchArray(MYSQLI_NUM);
            $res->Free();

            return (int) $id;
        }
        $res->Free();

        return $this->EnsureDefaultCalendar();
    }

    /**
     * Resolve a calendar ID, falling back to the default.
     *
     * @param int $calendarID
     *
     * @return int
     */
    public function ResolveCalendarID($calendarID)
    {
        $calendarID = (int) $calendarID;
        if ($calendarID > 0 && $this->GetAccessibleCalendar($calendarID) !== false) {
            return $calendarID;
        }

        return $this->GetDefaultCalendarID();
    }

    /**
     * Create the default calendar if missing.
     *
     * @return int
     */
    public function EnsureDefaultCalendar()
    {
        global $db;

        $res = $db->Query('SELECT `id` FROM {pre}calendars WHERE `user`=? AND `is_default`=1 LIMIT 1',
            $this->_userID);
        if ($res->RowCount() === 1) {
            list($id) = $res->FetchArray(MYSQLI_NUM);
            $res->Free();

            return (int) $id;
        }
        $res->Free();

        $db->Query('INSERT INTO {pre}calendars(`user`,`title`,`color`,`is_default`,`dav_uri`,`dav_uid`) VALUES(?,?,?,?,?,?)',
            $this->_userID,
            '',
            0,
            1,
            'calendar',
            '');

        return (int) $db->InsertId();
    }

    /**
     * Add a calendar.
     *
     * @param string $title
     * @param int    $color
     * @param string $davURI
     * @param string $davUID
     *
     * @return int
     */
    public function AddCalendar($title, $color = 0, $davURI = '', $davUID = '')
    {
        global $db;

        $this->EnsureDefaultCalendar();

        $db->Query('INSERT INTO {pre}calendars(`user`,`title`,`color`,`is_default`,`dav_uri`,`dav_uid`) VALUES(?,?,?,?,?,?)',
            $this->_userID,
            $title,
            (int) $color,
            0,
            $davURI,
            $davUID);
        $id = (int) $db->InsertId();
        if ($id > 0 && $davURI === '') {
            $db->Query('UPDATE {pre}calendars SET `dav_uri`=? WHERE `id`=? AND `user`=?',
                bmOrganizerUniqueCollectionDavUri($this->_userID, $title, 'cal', $id),
                $id,
                $this->_userID);
        }

        return $id;
    }

    /**
     * Update a calendar.
     *
     * @param int    $id
     * @param string $title
     * @param int    $color
     *
     * @return bool
     */
    public function UpdateCalendar($id, $title, $color = 0)
    {
        global $db, $lang_user;

        $existing = $this->GetCalendar($id);
        if ($existing === false) {
            return false;
        }
        $title = bmOrganizerCollectionTitleForStorage($title, $existing['is_default'], $lang_user['calendar']);

        $db->Query('UPDATE {pre}calendars SET `title`=?,`color`=? WHERE `id`=? AND `user`=?',
            $title,
            (int) $color,
            (int) $id,
            $this->_userID);

        return $db->AffectedRows() == 1;
    }

    /**
     * Delete a calendar (not the default). Dates and groups move to the default calendar.
     *
     * @param int $id
     *
     * @return bool
     */
    public function DeleteCalendar($id, $deleteDates = false)
    {
        global $db;

        $calendar = $this->GetCalendar($id);
        if ($calendar === false || !empty($calendar['is_default'])) {
            return false;
        }

        $id = (int) $id;
        if ($deleteDates) {
            $res = $db->Query('SELECT `id` FROM {pre}dates WHERE `user`=? AND `calendar_id`=?',
                $this->_userID,
                $id);
            while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
                ChangelogDeleted(BMCL_TYPE_CALENDAR, $row['id'], time());
                $db->Query('DELETE FROM {pre}dates_attendees WHERE `date`=?',
                    $row['id']);
            }
            $res->Free();
            $db->Query('DELETE FROM {pre}dates WHERE `user`=? AND `calendar_id`=?',
                $this->_userID,
                $id);
            $db->Query('DELETE FROM {pre}dates_groups WHERE `user`=? AND `calendar_id`=?',
                $this->_userID,
                $id);
        } else {
            $defaultID = $this->GetDefaultCalendarID();
            $db->Query('UPDATE {pre}dates SET `calendar_id`=?, `group`=-1 WHERE `user`=? AND `calendar_id`=?',
                $defaultID,
                $this->_userID,
                $id);
            $db->Query('UPDATE {pre}dates_groups SET `calendar_id`=? WHERE `user`=? AND `calendar_id`=?',
                $defaultID,
                $this->_userID,
                $id);
        }

        $db->Query('DELETE FROM {pre}calendars WHERE `id`=? AND `user`=? AND `is_default`=0',
            $id,
            $this->_userID);

        if ($db->AffectedRows() == 1) {
            bmOrganizerDeleteCollectionShares(BM_ORGANIZER_SHARE_CAL, $id);

            return true;
        }

        return false;
    }

    /**
     * get groups.
     *
     * @param string $sortColumn
     * @param string $sortOrder
     *
     * @return array
     */
    public function GetGroups($sortColumn = 'title', $sortOrder = 'asc', $calendarID = 0)
    {
        global $db, $lang_user;

        $result = [
            -1 => [
                    'id' => -1,
                    'user' => $this->_userID,
                    'title' => $lang_user['nocalcat'],
                    'color' => 0,
                    'calendar_id' => 0,
                ],
        ];

        $ownerIds = [$this->_userID];
        $sharedIds = [];
        foreach ($this->GetSharedCalendars() as $sid => $sharedCal) {
            $ownerIds[] = (int) $sharedCal['user'];
            $sharedIds[] = (int) $sid;
        }
        $ownerIds = array_unique(array_map('intval', $ownerIds));

        $sql = 'SELECT id,user,title,color,dav_uri,dav_uid,`calendar_id` FROM {pre}dates_groups WHERE user IN('.implode(',', $ownerIds).')';
        $params = [];
        if ((int) $calendarID > 0) {
            $cal = $this->GetAccessibleCalendar($calendarID);
            if ($cal === false) {
                return $result;
            }
            $sql .= ' AND `user`=? AND `calendar_id`=?';
            $params[] = (int) $cal['user'];
            $params[] = (int) $calendarID;
        } elseif (count($sharedIds) > 0) {
            $sql .= ' AND (`user`=? OR `calendar_id` IN('.implode(',', $sharedIds).'))';
            $params[] = $this->_userID;
        } else {
            $sql .= ' AND `user`=?';
            $params[] = $this->_userID;
        }
        $sql .= ' ORDER BY '.$sortColumn.' '.$sortOrder;

        $res = $db->Query($sql, ...$params);
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $result[$row['id']] = $row;
        }
        $res->Free();

        return $result;
    }

    /**
     * get group.
     *
     * @param int $id
     *
     * @return array
     */
    public function GetGroup($id)
    {
        global $db;

        $res = $db->Query('SELECT id,user,title,color,dav_uri,dav_uid,`calendar_id` FROM {pre}dates_groups WHERE user=? AND id=?',
            $this->_userID,
            (int) $id);
        if ($res->RowCount() == 1) {
            $row = $res->FetchArray(MYSQLI_ASSOC);
            $res->Free();

            return $row;
        }

        return false;
    }

    /**
     * add group.
     *
     * @param string $title Group title
     * @param int    $color Color (0-5)
     *
     * @return int
     */
    public function AddGroup($title, $color = 0, $davURI = '', $davUID = '', $calendarID = 0)
    {
        global $db;

        $calendarID = $this->ResolveCalendarID($calendarID);

        $db->Query('INSERT INTO {pre}dates_groups(user,title,color,dav_uri,dav_uid,`calendar_id`) VALUES(?,?,?,?,?,?)',
            $this->_userID,
            $title,
            (int) $color,
            $davURI,
            $davUID,
            $calendarID);

        return $db->InsertId();
    }

    /**
     * change group.
     *
     * @param int    $id    Group ID
     * @param string $title Group title
     * @param int    $color Color (0-5)
     *
     * @return bool
     */
    public function UpdateGroup($id, $title, $color = 0)
    {
        global $db;

        $db->Query('UPDATE {pre}dates_groups SET title=?,color=? WHERE id=? AND user=?',
            $title,
            (int) $color,
            (int) $id,
            $this->_userID);

        return $db->AffectedRows() == 1;
    }

    /**
     * delete group.
     *
     * @param int $groupID Group ID
     *
     * @return bool
     */
    public function DeleteGroup($groupID, $deleteDates = false)
    {
        global $db;

        if ($groupID < 1) {
            return false;
        }

        if (!$deleteDates) {
            $res = $db->Query('SELECT `id` FROM {pre}dates WHERE `group`=? AND `user`=?',
                (int) $groupID,
                $this->_userID);
            while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
                ChangelogUpdated(BMCL_TYPE_CALENDAR, $row['id'], time());
            }
            $res->Free();

            $db->Query('UPDATE {pre}dates SET `group`=-1 WHERE `group`=? AND user=?',
                (int) $groupID,
                $this->_userID);
        } else {
            $res = $db->Query('SELECT `id` FROM {pre}dates WHERE `user`=? AND `group`=?',
                $this->_userID,
                (int) $groupID);
            while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
                ChangelogDeleted(BMCL_TYPE_CALENDAR, $row['id'], time());
                $db->Query('DELETE FROM {pre}dates_attendees WHERE `date`=?',
                    $row['id']);
            }
            $res->Free();

            $db->Query('DELETE FROM {pre}dates WHERE `user`=? AND `group`=?',
                $this->_userID,
                (int) $groupID);
        }

        $db->Query('DELETE FROM {pre}dates_groups WHERE user=? AND id=?',
            $this->_userID,
            (int) $groupID);

        return $db->AffectedRows() == 1;
    }

    /**
     * get all dates for day.
     *
     * @param int $day   Day
     * @param int $month Month
     * @param int $year  Year
     *
     * @return array
     */
    public function GetDatesForDay($day, $month, $year, $calendarIDs = null)
    {
        $start = mktime(0, 0, 0, $month, $day, $year);
        $end = mktime(23, 59, 59, $month, $day, $year);
        if ($calendarIDs === null) {
            $calendarIDs = $this->GetVisibleCalendarIDs();
        }

        return $this->GetDatesForTimeframe($start, $end, -2, $calendarIDs);
    }

    /**
     * get date.
     *
     * @param int $id Date ID
     *
     * @return array
     */
    public function GetDate($id)
    {
        global $db;

        $res = $db->Query('SELECT * FROM {pre}dates WHERE id=?',
            (int) $id);
        if ($res->RowCount() == 1) {
            $row = $res->FetchArray(MYSQLI_ASSOC);
            $res->Free();
            if ($this->GetAccessibleCalendar((int) $row['calendar_id']) !== false) {
                return $row;
            }
        } else {
            $res->Free();
        }

        return false;
    }

    /**
     * get date attendees.
     *
     * @param int $id Date ID
     *
     * @return array
     */
    public function GetDateAttendees($id)
    {
        global $db;

        bmCalendarEnsureAttendeePartstat();

        $date = $this->GetDate($id);
        if ($date === false) {
            return [];
        }

        $result = [];
        $res = $db->Query('SELECT {pre}adressen.vorname AS vorname,{pre}adressen.nachname AS nachname,{pre}adressen.id AS id,{pre}adressen.email AS email,{pre}adressen.work_email AS work_email,{pre}adressen.default_address AS default_address,{pre}dates_attendees.partstat AS partstat FROM {pre}adressen,{pre}dates_attendees WHERE {pre}adressen.id={pre}dates_attendees.address AND {pre}dates_attendees.date=? AND {pre}adressen.user=?',
            $id,
            $date['user']);
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $row['partstat'] = bmCalendarNormalizePartstat($row['partstat'] ?? 'needs-action');
            $result[$row['id']] = $row;
        }
        $res->Free();

        return $result;
    }

    /**
     * Find a calendar event by DAV/iCal UID.
     *
     * @param string $uid
     *
     * @return array|false
     */
    public function FindDateByDavUid($uid)
    {
        global $db;

        $uid = trim($uid);
        if ($uid === '') {
            return false;
        }

        $res = $db->Query('SELECT * FROM {pre}dates WHERE user=? AND dav_uid=? LIMIT 1',
            $this->_userID,
            $uid);
        if ($res->RowCount() === 1) {
            $row = $res->FetchArray(MYSQLI_ASSOC);
            $res->Free();

            return $row;
        }
        $res->Free();

        return false;
    }

    /**
     * Find the most recent event with an exact title (fallback without UID).
     *
     * @param string $title
     *
     * @return array|false
     */
    public function FindDateByTitle($title)
    {
        global $db;

        $title = trim($title);
        if ($title === '') {
            return false;
        }

        $res = $db->Query('SELECT * FROM {pre}dates WHERE user=? AND title=? ORDER BY startdate DESC LIMIT 1',
            $this->_userID,
            $title);
        if ($res->RowCount() === 1) {
            $row = $res->FetchArray(MYSQLI_ASSOC);
            $res->Free();

            return $row;
        }
        $res->Free();

        return false;
    }

    /**
     * Update RSVP status for an attendee matched by e-mail address.
     *
     * @param int    $dateID
     * @param string $email
     * @param string $partstat
     *
     * @return bool
     */
    public function SetAttendeePartstatByEmail($dateID, $email, $partstat)
    {
        global $db;

        bmCalendarEnsureAttendeePartstat();

        $email = strtolower(trim($email));
        if ($email === '' || !preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $email)) {
            return false;
        }

        $partstat = bmCalendarNormalizePartstat($partstat);
        $dateID = (int) $dateID;

        $res = $db->Query('SELECT {pre}dates_attendees.address AS address FROM {pre}dates_attendees,{pre}adressen WHERE {pre}dates_attendees.date=? AND {pre}dates_attendees.address={pre}adressen.id AND {pre}adressen.user=? AND (LOWER({pre}adressen.email)=? OR LOWER({pre}adressen.work_email)=?)',
            $dateID,
            $this->_userID,
            $email,
            $email);
        if ($res->RowCount() !== 1) {
            $res->Free();

            return false;
        }

        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();

        $db->Query('UPDATE {pre}dates_attendees SET partstat=? WHERE `date`=? AND address=?',
            $partstat,
            $dateID,
            (int) $row['address']);

        return $db->AffectedRows() > 0;
    }

    /**
     * generate and return a mini month calendar.
     *
     * @param int $month  Month
     * @param int $year   Year
     * @param int $userID User ID (when used without class instance)
     *
     * @return string
     */
    public function GenerateMiniCalendar($month = -1, $year = -1, $userID = -1, $className = 'miniCalendarTable')
    {
        global $lang_user;

        if ($month == -1) {
            $month = (int) date('m');
        }
        if ($year == -1) {
            $year = (int) date('Y');
        }
        $thisMonth = (int) date('m') == $month && (int) date('Y') == $year;
        list($columns, $days) = $this->GenerateCalendar($month, $year, $userID, -2, $this->GetVisibleCalendarIDs());

        // start
        $html = '<table class="'.$className.'">'."\n";

        // month name
        $monthStart = mktime(0, 0, 0, $month, 1, $year);
        $html .= '	<tr>'."\n";
        $html .= sprintf('		<th class="Caption" colspan="7"><a href="%s">%s</a></th>'."\n",
            htmlspecialchars(SessionUrl('organizer.calendar.php?view=month&date='.$monthStart), ENT_QUOTES, 'UTF-8'),
            date('F Y', $monthStart));
        $html .= '	</tr>'."\n";

        // column headings
        $html .= '	<tr>'."\n";
        foreach ($columns as $wDay) {
            $html .= '		<th>'.$lang_user['weekdays_long'][$wDay].'</th>'."\n";
        }
        $html .= '	</tr>'."\n";

        // days
        $html .= '	<tr>'."\n";
        $c = 0;
        foreach ($days as $arrayKey => $dayItem) {
            // add day cell
            if ($dayItem === false) {
                $html .= '		<td class="Empty"></td>'."\n";
            } else {
                $dayStamp = mktime(0, 0, 0, $month, $dayItem['day'], $year);
                $html .= sprintf('		<td%s><a title="%d %s" href="%s">%d</a></td>'."\n",
                    ($thisMonth && $dayItem['day'] == (int) date('d')
                                ? ' class="Today"'
                                : (count($dayItem['dates']) > 0
                                    ? ' class="Date"'
                                    : '')),
                    count($dayItem['dates']),
                    $lang_user['dates'],
                    htmlspecialchars(SessionUrl('organizer.calendar.php?date='.$dayStamp), ENT_QUOTES, 'UTF-8'),
                    $dayItem['day']);
            }

            // increment / reset cell counter
            ++$c;
            if ($c == 7) {
                $html .= '	</tr>'."\n";
                $day_array_keys = array_keys($days);
                if ($arrayKey != array_pop($day_array_keys)) {
                    $html .= '	<tr>'."\n";
                }
                $c = 0;
            }
        }
        if ($c != 0) {
            for ($i = 1; $i <= 7 - $c; ++$i) {
                $html .= '		<td class="Empty"></td>'."\n";
            }
            $html .= '	</tr>'."\n";
        }

        // finish
        $html .= '</table>'."\n";

        return $html;
    }

    /**
     * generate and return a month calendar.
     *
     * @param int $month  Month
     * @param int $year   Year
     * @param int $userID User ID (when used without class instance)
     * @param int $group  Group (-2 = all)
     *
     * @return array Columns, Days
     */
    public function GenerateCalendar($month = -1, $year = -1, $userID = -1, $group = -2, $calendarIDs = null)
    {
        global $userRow;

        if ($month == -1) {
            $month = (int) date('m');
        }
        if ($year == -1) {
            $year = (int) date('Y');
        }
        $daysInMonth = BMCalendar::GetDaysInMonth($month, $year);
        $firstWeekDay = BMCalendar::GetDayOfWeek(1, $month, $year);

        // get first day of week and dates
        if ($userID == -1) {
            if ($this->_userID != $userRow['id']) {
                $userRow = BMUser::staticFetch($this->_userID);
            }
            $firstDay = $userRow['c_firstday'];
            $datesStart = mktime(0, 0, 0, $month, 1, $year);
            $datesEnd = mktime(23, 59, 59, $month, $daysInMonth, $year);
            $dates = $this->GetDatesForTimeframe($datesStart, $datesEnd, $group, $calendarIDs);
        }

        // called statically
        else {
            if ($userID != $userRow['id']) {
                $userRow = BMUser::staticFetch($userID);
            }
            $firstDay = $userRow['c_firstday'];
            $userCalendar = _new('BMCalendar', [$userID]);
            $datesStart = mktime(0, 0, 0, $month, 1, $year);
            $datesEnd = mktime(23, 59, 59, $month, $daysInMonth, $year);
            $dates = $userCalendar->GetDatesForTimeframe($datesStart, $datesEnd, $group, $calendarIDs);
        }

        // columns
        $columns = [];
        for ($w = $firstDay,$i = 0; $i <= 6; ++$i) {
            $columns[] = $w;
            ++$w;
            if ($w > 6) {
                $w = 0;
            }
        }

        // days
        $days = [];
        for ($i = 1; $i <= $daysInMonth; ++$i) {
            $dateStart = mktime(0, 0, 0, $month, $i, $year);
            $dateEnd = mktime(23, 59, 59, $month, $i, $year);
            $dayDates = [];

            // find dates
            foreach ($dates as $key => $val) {
                if (($val['startdate'] >= $dateStart && $val['startdate'] <= $dateEnd)
                    || ($val['enddate'] >= $dateStart && $val['enddate'] <= $dateEnd)
                    || ($val['startdate'] <= $dateStart && $val['enddate'] >= $dateEnd)) {
                    $dayDates[$key] = $val;
                }
            }

            // add day to array
            $days[] = [
                'day' => $i,
                'dayStart' => $dateStart,
                'dayEnd' => $dateEnd,
                'dates' => $dayDates,
                'today' => $dateStart == mktime(0, 0, 0, date('m'), date('d'), date('Y')),
            ];
        }

        // move to right day
        foreach ($columns as $columnWDay) {
            if ($columnWDay == $firstWeekDay) {
                break;
            }
            array_unshift($days, false);
        }

        // return!
        return [$columns, $days];
    }

    /**
     * add a date.
     *
     * @param array $row       Date row
     * @param array $attendees Attendees
     *
     * @return int
     */
    public function AddDate($row, $attendees)
    {
        global $db;

        // F10: The old implementation silently rewrote the target
        // calendar to the caller's default whenever the requested one
        // wasn't writable — so an ICS/CalDAV client that pushed an
        // event to a read-only shared calendar would see the response
        // succeed, but the event would silently land somewhere else.
        // The owner never got the entry, the sender saw a green
        // "saved" and the two mailboxes drifted apart.
        //
        // New semantics:
        //   - calendar_id == 0     → default calendar (unchanged).
        //   - calendar_id explicit → hard-fail (return false) when not
        //                            writable; caller must handle it.
        $requestedRaw = isset($row['calendar_id']) ? (int) $row['calendar_id'] : 0;
        $calendarID = $this->ResolveCalendarID($requestedRaw);
        if (!$this->CanWriteCalendar($calendarID)) {
            if ($requestedRaw > 0) {
                PutLog(sprintf('AddDate refused: calendar_id=%d not writable for user %d',
                    $requestedRaw, (int) $this->_userID),
                    PRIO_WARNING, __FILE__, __LINE__);
                return false;
            }
            // calendar_id was 0 → try the default. If even that fails
            // something is very wrong (no owned default) → also fail.
            $calendarID = $this->GetDefaultCalendarID();
            if (!$this->CanWriteCalendar($calendarID)) {
                PutLog(sprintf('AddDate refused: no writable default calendar for user %d',
                    (int) $this->_userID), PRIO_WARNING, __FILE__, __LINE__);
                return false;
            }
        }
        $cal = $this->GetAccessibleCalendar($calendarID);
        $ownerId = $cal !== false ? (int) $cal['user'] : $this->_userID;

        $db->Query('INSERT INTO {pre}dates(user,title,location,text,`group`,startdate,enddate,reminder,flags,repeat_flags,repeat_times,repeat_value,repeat_extra1,repeat_extra2,dav_uri,dav_uid,`calendar_id`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            $ownerId,
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
            (isset($row['dav_uri']) ? $row['dav_uri'] : ''),
            (isset($row['dav_uid']) ? $row['dav_uid'] : ''),
            $calendarID);

        // attendees
        if ($dateID = $db->InsertId()) {
            ChangelogAdded(BMCL_TYPE_CALENDAR, $dateID, time());

            bmCalendarEnsureAttendeePartstat();
            foreach ($attendees as $contactID) {
                $db->Query('INSERT INTO {pre}dates_attendees(date,address,partstat) VALUES(?,?,?)',
                    $dateID,
                    $contactID,
                    'needs-action');
            }

            $row['id'] = $dateID;
            $row['user'] = $ownerId;
            $row['calendar_id'] = $calendarID;
            bmOrganizerNotifyCalendarActivity($calendarID, $this->_userID, $row, 'add');
        }

        return $dateID;
    }

    /**
     * delete a date.
     *
     * @param int $id Date ID
     *
     * @return bool
     */
    public function DeleteDate($id)
    {
        global $db;

        $date = $this->GetDate($id);
        if ($date === false || !$this->CanWriteCalendar((int) $date['calendar_id'])) {
            return false;
        }

        // F9: If the actor is not the owner of this date (i.e. deleting
        // via a write-shared calendar) leave an audit trail before we
        // wipe the row — the owner only sees "gone" afterwards and would
        // otherwise have no way to reconstruct who did it.
        bmShareAuditLog($this->_userID, (int) $date['user'],
            'calendar_delete', (int) $id, isset($date['title']) ? (string) $date['title'] : '');

        $db->Query('DELETE FROM {pre}dates WHERE user=? AND id=?',
            $date['user'],
            (int) $id);
        if ($db->AffectedRows() == 1) {
            ChangelogDeleted(BMCL_TYPE_CALENDAR, $id, time());
            $db->Query('DELETE FROM {pre}dates_attendees WHERE date=?',
                (int) $id);
            bmOrganizerNotifyCalendarActivity((int) $date['calendar_id'], $this->_userID, $date, 'delete');

            return true;
        }

        return false;
    }

    /**
     * change date.
     *
     * @param int   $id        Date ID
     * @param array $row       Date row
     * @param array $attendees Attendees
     *
     * @return bool
     */
    public function ChangeDate($id, $row, $attendees)
    {
        global $db;

        $existing = $this->GetDate($id);
        if ($existing === false || !$this->CanWriteCalendar((int) $existing['calendar_id'])) {
            return false;
        }

        $calendarID = $this->ResolveCalendarID(isset($row['calendar_id']) ? $row['calendar_id'] : $existing['calendar_id']);
        if (!$this->CanWriteCalendar($calendarID)) {
            $calendarID = (int) $existing['calendar_id'];
        }

        $db->Query('UPDATE {pre}dates SET title=?, location=?, text=?, `group`=?, startdate=?, enddate=?, reminder=?, flags=?, repeat_flags=?, repeat_times=?, repeat_value=?, repeat_extra1=?, repeat_extra2=?, `dav_uri`=?, `dav_uid`=?, `calendar_id`=? WHERE id=? AND user=?',
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
            (isset($row['dav_uri']) ? $row['dav_uri'] : ''),
            (isset($row['dav_uid']) ? $row['dav_uid'] : ''),
            $calendarID,
            (int) $id,
            $existing['user']);

        ChangelogUpdated(BMCL_TYPE_CALENDAR, $id, time());

        foreach ($attendees as $key => $val) {
            $attendees[$key] = (int) $val;
        }

        if (count($attendees) > 0) {
            $db->Query('DELETE FROM {pre}dates_attendees WHERE date=? AND address NOT IN('.implode(',', $attendees).')',
                (int) $id);
            bmCalendarEnsureAttendeePartstat();
            foreach ($attendees as $contactID) {
                $contactID = (int) $contactID;
                $res = $db->Query('SELECT partstat FROM {pre}dates_attendees WHERE `date`=? AND address=?',
                    (int) $id,
                    $contactID);
                $existingPartstat = 'needs-action';
                if ($res->RowCount() === 1) {
                    $psRow = $res->FetchArray(MYSQLI_ASSOC);
                    $existingPartstat = bmCalendarNormalizePartstat($psRow['partstat'] ?? 'needs-action');
                }
                $res->Free();

                $db->Query('REPLACE INTO {pre}dates_attendees(date,address,partstat) VALUES(?,?,?)',
                    (int) $id,
                    $contactID,
                    $existingPartstat);
            }
        } else {
            $db->Query('DELETE FROM {pre}dates_attendees WHERE date=?',
                (int) $id);
        }

        $notifyRow = $row;
        $notifyRow['id'] = (int) $id;
        $notifyRow['user'] = $existing['user'];
        $notifyRow['calendar_id'] = $calendarID;
        if (self::_dateActivitySignature($existing) !== self::_dateActivitySignature($notifyRow)) {
            bmOrganizerNotifyCalendarActivity($calendarID, $this->_userID, $notifyRow, 'change');
        }

        return true;
    }

    /**
     * @param array $row
     *
     * @return string
     */
    private static function _dateActivitySignature($row)
    {
        return implode("\0", [
            isset($row['title']) ? $row['title'] : '',
            isset($row['location']) ? $row['location'] : '',
            isset($row['text']) ? $row['text'] : '',
            isset($row['startdate']) ? (string) (int) $row['startdate'] : '0',
            isset($row['enddate']) ? (string) (int) $row['enddate'] : '0',
            isset($row['calendar_id']) ? (string) (int) $row['calendar_id'] : '0',
        ]);
    }

    /**
     * generate date row from form data.
     *
     * @return array
     */
    public function Form2Row()
    {
        //
        // init
        //
        $row = [];
        $row['flags'] = 0;
        $row['repeat_flags'] = 0;
        $row['repeat_value'] = 0;
        $row['repeat_times'] = 0;
        $row['repeat_extra1'] = '';
        $row['repeat_extra2'] = '';

        //
        // misc
        //
        $row['title'] = $_REQUEST['title'];
        $row['location'] = $_REQUEST['location'];
        $row['text'] = $_REQUEST['text'];
        $row['startdate'] = SmartyDateTime('startdate');
        $row['group'] = isset($_REQUEST['group']) ? (int) $_REQUEST['group'] : -1;
        $row['calendar_id'] = $this->ResolveCalendarID(isset($_REQUEST['calendar']) ? $_REQUEST['calendar'] : 0);

        if ($row['group'] > 0) {
            $groupRow = $this->GetGroup($row['group']);
            if ($groupRow === false || (int) $groupRow['calendar_id'] !== (int) $row['calendar_id']) {
                $row['group'] = -1;
            }
        }

        //
        // date duration – form provides start + end datetime (with an optional
        // "whole day" checkbox). Legacy durationHours/durationMinutes fields
        // remain supported as a fallback.
        //
        $endDate = SmartyDateTime('enddate');
        $legacyDurationSec = 0;
        if (isset($_REQUEST['durationHours']) || isset($_REQUEST['durationMinutes'])) {
            $dh = isset($_REQUEST['durationHours']) ? max(0, (int) $_REQUEST['durationHours']) : 0;
            $dm = isset($_REQUEST['durationMinutes']) ? max(0, (int) $_REQUEST['durationMinutes']) : 0;
            $legacyDurationSec = $dh * TIME_ONE_HOUR + $dm * TIME_ONE_MINUTE;
        }

        if (isset($_REQUEST['wholeDay']) && $_REQUEST['wholeDay'] == 1) {
            $row['flags'] |= CLNDR_WHOLE_DAY;

            // Snap start to 00:00 of the start day.
            $row['startdate'] = mktime(0, 0, 0,
                (int) date('m', $row['startdate']),
                (int) date('d', $row['startdate']),
                (int) date('Y', $row['startdate']));

            // Determine end day: prefer the form's end date, then legacy duration,
            // then default to a single-day event.
            if ($endDate > 0) {
                $endDayEnd = mktime(23, 59, 59,
                    (int) date('m', $endDate),
                    (int) date('d', $endDate),
                    (int) date('Y', $endDate));
            } elseif ($legacyDurationSec > 0) {
                $endDayEnd = $row['startdate'] + $legacyDurationSec - 1;
            } else {
                $endDayEnd = $row['startdate'] + 59;
            }

            // Multi-day whole-day event → span across days; single-day whole-day
            // event → keep the historical startdate+59 marker for compatibility.
            if ($endDayEnd >= $row['startdate'] + TIME_ONE_DAY) {
                $row['enddate'] = $endDayEnd;
            } else {
                $row['enddate'] = $row['startdate'] + 59;
            }
        } else {
            if ($endDate > 0) {
                $row['enddate'] = max($row['startdate'] + TIME_ONE_MINUTE, $endDate);
            } elseif ($legacyDurationSec > 0) {
                $row['enddate'] = max($row['startdate'] + TIME_ONE_MINUTE,
                                        $row['startdate'] + $legacyDurationSec);
            } else {
                $row['enddate'] = $row['startdate'] + TIME_ONE_HOUR;
            }
        }

        //
        // reminder?
        //
        $row['reminder'] = max(0, $_REQUEST['reminder']) * TIME_ONE_MINUTE;
        if (isset($_REQUEST['reminder_email'])) {
            $row['flags'] |= CLNDR_REMIND_EMAIL;
        }
        if (isset($_REQUEST['reminder_sms'])) {
            $row['flags'] |= CLNDR_REMIND_SMS;
        }
        if (isset($_REQUEST['reminder_notify'])) {
            $row['flags'] |= CLNDR_REMIND_NOTIFY;
        }
        if (isset($_REQUEST['reminder_push'])) {
            $row['flags'] |= CLNDR_REMIND_PUSH;
        }

        //
        // repeating preferences
        //
        if (isset($_REQUEST['repeating'])) {
            //
            // duration
            //
            if (isset($_REQUEST['repeat_until']) && $_REQUEST['repeat_until'] == 'endless') {
                $row['repeat_times'] = 0;
                $row['repeat_flags'] |= CLNDR_REPEATING_UNTIL_ENDLESS;
            } elseif (isset($_REQUEST['repeat_until']) && $_REQUEST['repeat_until'] == 'count') {
                $row['repeat_times'] = max(1, $_REQUEST['repeat_until_count']);
                $row['repeat_flags'] |= CLNDR_REPEATING_UNTIL_COUNT;
            } elseif (isset($_REQUEST['repeat_until']) && $_REQUEST['repeat_until'] == 'date') {
                $row['repeat_times'] = max($row['startdate'] + TIME_ONE_MINUTE,
                                            SmartyDateTime('repeat_until_date'));
                $row['repeat_flags'] |= CLNDR_REPEATING_UNTIL_DATE;
            }

            //
            // interval
            //
            if(isset($_REQUEST['repeat_interval'])) {
                if ($_REQUEST['repeat_interval'] == 'daily') {
                    $row['repeat_flags'] |= CLNDR_REPEATING_DAILY;
                    $row['repeat_value'] = max(1, $_REQUEST['repeat_interval_daily']);
                    $row['repeat_extra1'] = isset($_REQUEST['repeat_daily_exceptions']) && is_array($_REQUEST['repeat_daily_exceptions']) && count($_REQUEST['repeat_daily_exceptions']) > 0
                                                ? implode(',', $_REQUEST['repeat_daily_exceptions'])
                                                : '';
                } elseif ($_REQUEST['repeat_interval'] == 'weekly') {
                    $row['repeat_flags'] |= CLNDR_REPEATING_WEEKLY;
                    $row['repeat_value'] = max(1, $_REQUEST['repeat_interval_weekly']);
                } elseif ($_REQUEST['repeat_interval'] == 'monthly_mday') {
                    $row['repeat_flags'] |= CLNDR_REPEATING_MONTHLY_MDAY;
                    $row['repeat_value'] = max(1, $_REQUEST['repeat_interval_monthly_mday']);
                    $row['repeat_extra1'] = max(1, min(31, $_REQUEST['repeat_interval_monthly_mday_extra1']));
                } elseif ($_REQUEST['repeat_interval'] == 'monthly_wday') {
                    $row['repeat_flags'] |= CLNDR_REPEATING_MONTHLY_WDAY;
                    $row['repeat_value'] = max(1, $_REQUEST['repeat_interval_monthly_wday']);
                    $row['repeat_extra1'] = max(0, min(4, $_REQUEST['repeat_interval_monthly_wday_extra1']));
                    $row['repeat_extra2'] = max(0, min(6, $_REQUEST['repeat_interval_monthly_wday_extra2']));
                } elseif ($_REQUEST['repeat_interval'] == 'yearly') {
                    $row['repeat_flags'] |= CLNDR_REPEATING_YEARLY;
                    $row['repeat_value'] = max(1, $_REQUEST['repeat_interval_yearly']);
                }
            }
        }

        return $row;
    }
}
