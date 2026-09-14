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
	require './serverlib/init.inc.php';
include('./serverlib/calendar.class.php');
include('./serverlib/addressbook.class.php');
include('./serverlib/email.attachment.inc.php');
RequestPrivileges(PRIVILEGES_USER);

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));
/**
 * organizer enabled?
 */
if($groupRow['organizer']=='no')
{
	SessionRedirect('start.php');
	exit();
}

/**
 * default action = start
 */
$tpl->addJSFile('li', $tpl->tplDir . 'js/organizer.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'start';
$tpl->assign('activeTab', 'calendar');
$tpl->assign('pageTitle', $lang_user['calendar']);

/**
 * view mode?
 */
if(isset($_REQUEST['view'])
	&& in_array($_REQUEST['view'], array('day', 'month', 'week')))
{
	$thisUser->SetPref('calendarViewMode', $_REQUEST['view']);
}

/**
 * group?
 */
if(isset($_REQUEST['switchGroup']))
{
	$thisUser->SetPref('calendarGroup', (int)$_REQUEST['switchGroup']);
}

/**
 * visible calendars?
 */
if(isset($_REQUEST['visibleCal']))
{
	$visiblePref = trim($_REQUEST['visibleCal']);
	$thisUser->SetPref('visibleCalendars', $visiblePref);
}

/**
 * calendar interface
 */
$calendar = _new('BMCalendar', array($userRow['id']));
$calendars = $calendar->GetCalendars();
$sharedCalendars = $calendar->GetSharedCalendars();
$defaultCalendarID = $calendar->GetDefaultCalendarID();

$visibleCalendarIDs = $calendar->GetVisibleCalendarIDs($calendars + $sharedCalendars);

$currentCalendarID = $defaultCalendarID;
if(isset($_REQUEST['calendar']))
{
	$currentCalendarID = $calendar->ResolveCalendarID($_REQUEST['calendar']);
	$thisUser->SetPref('currentCalendar', $currentCalendarID);
}
else if(($prefCal = $thisUser->GetPref('currentCalendar')) !== false)
{
	$currentCalendarID = $calendar->ResolveCalendarID($prefCal);
}

$writableCalendars = $calendars;
foreach($sharedCalendars as $sid => $sharedCal)
{
	if($sharedCal['share_access'] === BM_ORGANIZER_ACCESS_WRITE)
		$writableCalendars[$sid] = $sharedCal;
}
$eventCalendarID = $calendar->CanWriteCalendar($currentCalendarID) ? $currentCalendarID : $defaultCalendarID;

/**
 * date & view mode
 */
$viewMode = (($viewMode = $thisUser->GetPref('calendarViewMode')) === false ? $bm_prefs['calendar_defaultviewmode'] : $viewMode);
$date = time();
if(isset($_REQUEST['date']))
	$date = (int)$_REQUEST['date'];
else if(isset($_REQUEST['date_Month']) && !isset($_REQUEST['jumpToday']))
{
	if($viewMode == 'day')
		$date = mktime(0, 0, 0, $_REQUEST['date_Month'], $_REQUEST['date_Day'], $_REQUEST['date_Year']);
	else if($viewMode == 'month')
		$date = mktime(0, 0, 0, $_REQUEST['date_Month'], 1, $_REQUEST['date_Year']);
}
else if(isset($_REQUEST['date_Week']) && isset($_REQUEST['date_Year']) && !isset($_REQUEST['jumpToday'])
	&& $viewMode == 'week')
{
	$date = mktime(0, 0, 0, 1, 1, $_REQUEST['date_Year']);
	if(date('W', $date) > 1)
		$date += TIME_ONE_WEEK;
	while((int)date('W', $date) < (int)$_REQUEST['date_Week'])
		$date += TIME_ONE_WEEK;
}
if($viewMode == 'day')
{
	$dateStart = mktime(0, 0, 0, date('m', $date), date('d', $date), date('Y', $date));
	$dateEnd = mktime(23, 59, 59, date('m', $date), date('d', $date), date('Y', $date));
}
else if($viewMode == 'month')
{
	$dateStart = mktime(0, 0, 0, date('m', $date), 1, date('Y', $date));
	$dateEnd = mktime(23, 59, 59, date('m', $date), $calendar->GetDaysInMonth(date('m', $date), date('Y', $date)), date('Y', $date));
}
else if($viewMode == 'week')
{
	$dateStart = $calendar->GetWeekStartDay($date);
	$dateEnd = $calendar->GetWeekEndDay($date) + 86400 - 1;
}
$groups = $calendar->GetGroups('title', 'asc');
$group = (($group = $thisUser->GetPref('calendarGroup')) !== false && isset($groups[$group]) ? $group : -2);
if($group > 0 && isset($groups[$group]) && !in_array((int)$groups[$group]['calendar_id'], $visibleCalendarIDs, true))
	$group = -2;
$tpl->assign('calendars',		$calendars);
$tpl->assign('sharedCalendars',	$sharedCalendars);
$tpl->assign('writableCalendars', $writableCalendars);
$tpl->assign('calendarCount',	count($calendars) + count($sharedCalendars));
$tpl->assign('visibleCalendarIDs', $visibleCalendarIDs);
$tpl->assign('currentCalendarID', $currentCalendarID);
$tpl->assign('eventCalendarID', $eventCalendarID);
$tpl->assign('defaultCalendarID', $defaultCalendarID);
$tpl->assign('groups', 			$groups);
$tpl->assign('theGroup',		$group);
$groupsByCalendar = array();
foreach($groups as $gid => $gRow)
{
	if($gid <= 0)
		continue;
	$cid = (int)$gRow['calendar_id'];
	if(!isset($groupsByCalendar[$cid]))
		$groupsByCalendar[$cid] = array();
	$groupsByCalendar[$cid][$gid] = $gRow['title'];
}
$tpl->assign('groupsByCalendar', $groupsByCalendar);
$tpl->assign('theDate', 		$date);
$tpl->assign('pageToolbarFile', 'li/organizer.calendar.toolbar.tpl');
$tpl->assign('viewMode', 		$viewMode);
$tpl->assign('date',			$date);
$tpl->assign('dayStart',		$userRow['workday_start']);
$tpl->assign('dayEnd',			$userRow['workday_end']);
$tpl->assign('prevMonth',		mktime(0, 0, 0, date('m', $date), 15, date('Y', $date)) - TIME_ONE_MONTH);
$tpl->assign('nextMonth',		mktime(0, 0, 0, date('m', $date), 15, date('Y', $date)) + TIME_ONE_MONTH);
$tpl->assign('prevWeek',		$date - TIME_ONE_WEEK);
$tpl->assign('nextWeek',		$date + TIME_ONE_WEEK);
$tpl->assign('calWeekNo',		(int)date('W', $date));
$tpl->assign('weekYear',		date('o') == 'o' ? date('Y', $date) : date('o', $date));
$tpl->assign('thisMonthText',	_strftime('%B %Y', mktime(0, 0, 0, date('m', $date), 15, date('Y', $date))));
$tpl->assign('smsEnabled',		$thisUser->SMSEnabled());
if (!class_exists('BMPush', false)) {
	include B1GMAIL_DIR.'serverlib/push.class.php';
}
$tpl->assign('pushEnabled',		BMPush::isEnabled());

/**
 * page menu
 */
$tpl->assign('pageMenuFile', 'li/organizer.sidebar.tpl');

/**
 * start page
 */
if($_REQUEST['action'] == 'start')
{
	if($viewMode == 'day')
	{
		$dates = $calendar->GetDatesForTimeframe($dateStart, $dateEnd, $group, $visibleCalendarIDs);
		$tpl->assign('weekDay', date('l', $dateStart));
		$tpl->assign('calWeek', date(date('o') == 'o' ? 'W/Y' : 'W/o', $dateStart));
		$tpl->assign('dates', $dates);
		$tpl->assign('pageContent', 'li/organizer.calendar.dayview.tpl');
	}
	else if($viewMode == 'week')
	{
		$dates = array();
		$weekDayTimestamps = array();
		for($d=0; $d<7; $d++)
		{
			$ts = $dateStart+$d*TIME_ONE_DAY;
			$dDateStart = mktime(0, 0, 0, date('m', $ts), date('d', $ts), date('Y', $ts));
			$dDateEnd = mktime(23, 59, 59, date('m', $ts), date('d', $ts), date('Y', $ts));
			$dates[ _strftime('%A, %d.', $ts) ] = $calendar->GetDatesForTimeframe($dDateStart, $dDateEnd, $group, $visibleCalendarIDs);
			$weekDayTimestamps[$d] = $dDateStart;
		}

		$tpl->assign('curYear', (int)date('Y'));
		$tpl->assign('weekStartDate', $dateStart);
		$tpl->assign('weekEndDate', $dateEnd);
		$tpl->assign('calWeekNo', date('W', $dateStart));
		$tpl->assign('calWeek', date(date('o') == 'o' ? 'W/Y' : 'W/o', $dateStart));
		$tpl->assign('dates', $dates);
		$tpl->assign('weekDayTimestamps', $weekDayTimestamps);
		$tpl->assign('pageContent', 'li/organizer.calendar.weekview.tpl');
	}
	else if($viewMode == 'month')
	{
		list($columns, $days) = $calendar->GenerateCalendar((int)date('m', $dateStart), (int)date('Y', $dateStart), -1, $group, $visibleCalendarIDs);
		$tpl->assign('lastDayKey', count($days)-1);
		$tpl->assign('wdays', $lang_user['full_weekdays']);
		$tpl->assign('columns', $columns);
		$tpl->assign('days', $days);
		$tpl->assign('pageContent', 'li/organizer.calendar.monthview.tpl');
	}

	$tpl->display('li/index.tpl');
}

/**
 * day view (iframe)
 */
else if($_REQUEST['action'] == 'dayView')
{
	$dates = $calendar->GetDatesForTimeframe($dateStart, $dateEnd, $group, $visibleCalendarIDs);
	$tpl->assign('dateStart', $dateStart);
	$tpl->assign('dates', $dates);
	$tpl->display('li/organizer.calendar.dayview.view.tpl');
}

/**
 * show date
 */
else if($_REQUEST['action'] == 'showDate'
		&& isset($_REQUEST['id']))
{
	$date = $calendar->GetDate((int)$_REQUEST['id']);
	if($date !== false)
	{
		// override start/enddates, if given (neccessary for repeating dates)
		if(isset($_REQUEST['start']) && $_REQUEST['start'] != $date['startdate'])
		{
			$date['orig_startdate'] = $date['startdate'];
			$date['startdate'] = (int)$_REQUEST['start'];
		}
		if(isset($_REQUEST['end']) && $_REQUEST['end'] != $date['enddate'])
		{
			$date['orig_enddate'] = $date['enddate'];
			$date['enddate'] = (int)$_REQUEST['end'];
		}

		// attendee + mail stuff
		$attendees = $calendar->GetDateAttendees((int)$_REQUEST['id']);
		$attendeeMail = array();
		foreach($attendees as $person)
			$attendeeMail[] = sprintf('"%s, %s" <%s>',
				$person['nachname'],
				$person['vorname'],
				$person['default_address'] == ADDRESS_PRIVATE
					? $person['email']
					: $person['work_email']);
		$mailTo = urlencode(implode('; ', $attendeeMail));
		$mailSubject = urlencode($lang_user['btr'] . ' "' . $date['title'] . '" (' . date($userRow['datumsformat'], $date['startdate']) . ')');

		// page output
		$tpl->assign('date', $date);
		$tpl->assign('dateWritable', $calendar->CanWriteCalendar((int)$date['calendar_id']));
		$tpl->assign('attendees', $attendees);
		$tpl->assign('mailTo', $mailTo);
		$tpl->assign('mailSubject', $mailSubject);
		$tpl->display('li/organizer.calendar.showdate.tpl');
	}
}

/**
 * calendars
 */
else if($_REQUEST['action'] == 'calendars')
{
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'edit'
		&& isset($_REQUEST['id']))
	{
		$cal = $calendar->GetCalendar((int)$_REQUEST['id']);
		if($cal !== false)
		{
			$tpl->assign('calendarItem', $cal);
			$tpl->display('li/organizer.calendar.calendars.dialog.tpl');
			exit();
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'addForm')
	{
		$tpl->display('li/organizer.calendar.calendars.dialog.tpl');
		exit();
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'deleteForm'
		&& isset($_REQUEST['id']))
	{
		$cal = $calendar->GetCalendar((int)$_REQUEST['id']);
		if($cal !== false && empty($cal['is_default']))
		{
			$tpl->assign('calendarItem', $cal);
			$tpl->display('li/organizer.calendar.calendars.delete.tpl');
			exit();
		}
	}
	else
	{
		// F2: state-changing endpoints must carry a valid CSRF token.
		// CsrfEnforceOnStateChange() accepts POST (with hidden field or
		// header) and GET (with ?csrf_token=...) so classic <a href> UI
		// keeps working after templates are updated.
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'save'
			&& isset($_REQUEST['id']))
		{
			CsrfEnforceOnStateChange();
			$calendar->UpdateCalendar((int)$_REQUEST['id'],
				$_REQUEST['title'],
				isset($_REQUEST['color']) ? (int)$_REQUEST['color'] : 0);
			SessionRedirect('organizer.calendar.php');
		}

		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'delete'
			&& isset($_REQUEST['id']))
		{
			CsrfEnforceOnStateChange();
			$calendar->DeleteCalendar((int)$_REQUEST['id']);
			SessionRedirect('organizer.calendar.php');
		}
		else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add'
				&& isset($_REQUEST['title']))
		{
			CsrfEnforceOnStateChange();
			$calendar->AddCalendar($_REQUEST['title'], isset($_REQUEST['color']) ? (int)$_REQUEST['color'] : 0);
			SessionRedirect('organizer.calendar.php');
		}

		$calendars = $calendar->GetCalendars();
		$tpl->assign('calendars', $calendars);
		$tpl->assign('pageContent', 'li/organizer.calendar.calendars.tpl');
		$tpl->display('li/index.tpl');
	}
}

/**
 * share calendar
 */
else if($_REQUEST['action'] == 'share' && isset($_REQUEST['id']))
{
	if(!bmOrganizerGroupCanShare('calendar'))
	{
		SessionRedirect('organizer.calendar.php');
		exit();
	}
	$cal = $calendar->GetCalendar((int)$_REQUEST['id']);
	if($cal === false)
	{
		SessionRedirect('organizer.calendar.php');
		exit();
	}

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add' && isset($_REQUEST['email']) && IsPOSTRequest())
	{
		$targetId = bmOrganizerShareTargetUserId($_REQUEST['email']);
		$access = isset($_REQUEST['access']) ? $_REQUEST['access'] : BM_ORGANIZER_ACCESS_READ;
		$result = bmOrganizerAddShare(BM_ORGANIZER_SHARE_CAL, (int)$cal['id'], $userRow['id'], $targetId,
			$access,
			bmOrganizerNotifyFlag(isset($_REQUEST['notify_email']) ? $_REQUEST['notify_email'] : 0),
			bmOrganizerNotifyFlag(isset($_REQUEST['notify_push']) ? $_REQUEST['notify_push'] : 0));
		if(is_string($result) && isset($lang_user[$result]))
			$tpl->assign('shareError', $lang_user[$result]);
		else if((int)$result > 0)
		{
			bmOrganizerNotifyShareInvite(BM_ORGANIZER_SHARE_CAL, $cal, $userRow['id'], $targetId, $access);
			$tpl->assign('shareSuccess', $lang_user['shareinvited']);
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'saveNotify' && IsPOSTRequest())
	{
		bmOrganizerSetOwnerNotify((int)$cal['id'], $userRow['id'],
			bmOrganizerNotifyFlag(isset($_REQUEST['owner_notify_email']) ? $_REQUEST['owner_notify_email'] : 0),
			bmOrganizerNotifyFlag(isset($_REQUEST['owner_notify_push']) ? $_REQUEST['owner_notify_push'] : 0));
		$shareNotifyEmail = isset($_REQUEST['share_notify_email']) && is_array($_REQUEST['share_notify_email'])
			? $_REQUEST['share_notify_email']
			: array();
		$shareNotifyPush = isset($_REQUEST['share_notify_push']) && is_array($_REQUEST['share_notify_push'])
			? $_REQUEST['share_notify_push']
			: array();
		foreach(bmOrganizerListShares(BM_ORGANIZER_SHARE_CAL, (int)$cal['id'], $userRow['id']) as $shareID => $shareRow)
		{
			bmOrganizerSetShareNotify(BM_ORGANIZER_SHARE_CAL, (int)$shareID, $userRow['id'],
				bmOrganizerNotifyFlag(isset($shareNotifyEmail[$shareID]) ? $shareNotifyEmail[$shareID] : 0),
				bmOrganizerNotifyFlag(isset($shareNotifyPush[$shareID]) ? $shareNotifyPush[$shareID] : 0));
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'remove'
		&& isset($_REQUEST['share'])
		&& IsPOSTRequest())
	{
		// F2: share revocation was reachable via a plain GET — an
		// attacker could trick the owner into visiting a link that
		// silently unshares a calendar with a specific recipient.
		bmOrganizerRemoveShare(BM_ORGANIZER_SHARE_CAL, (int)$_REQUEST['share'], $userRow['id']);
	}

	$ownerNotify = bmOrganizerGetOwnerNotify((int)$cal['id'], $userRow['id']);
	$tpl->assign('shareItem', $cal);
	$tpl->assign('shareList', bmOrganizerListShares(BM_ORGANIZER_SHARE_CAL, (int)$cal['id'], $userRow['id']));
	$tpl->assign('shareType', 'calendar');
	$tpl->assign('shareAction', 'organizer.calendar.php');
	$tpl->assign('ownerNotifyEmail', $ownerNotify['notify_email']);
	$tpl->assign('ownerNotifyPush', $ownerNotify['notify_push']);
	$tpl->display('li/organizer.share.dialog.tpl');
	exit();
}

/**
 * leave a calendar shared with this user
 */
else if($_REQUEST['action'] == 'leaveshare' && isset($_REQUEST['id']))
{
	$cal = $calendar->GetAccessibleCalendar((int)$_REQUEST['id']);
	if($cal === false || empty($cal['shared']))
	{
		SessionRedirect('organizer.calendar.php');
		exit();
	}

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'leave' && IsPOSTRequest())
	{
		bmOrganizerLeaveShare(BM_ORGANIZER_SHARE_CAL, (int)$cal['id'], $userRow['id']);
		$thisUser->SetPref('currentCalendar', $defaultCalendarID);
		SessionRedirect('organizer.calendar.php');
		exit();
	}

	$tpl->assign('leaveItem', $cal);
	$tpl->assign('leaveAction', 'organizer.calendar.php');
	$tpl->display('li/organizer.share.leave.tpl');
	exit();
}

/**
 * groups
 */
else if($_REQUEST['action'] == 'groups')
{
	//
	// edit
	//
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'edit'
		&& isset($_REQUEST['id']))
	{
		$group = $calendar->GetGroup((int)$_REQUEST['id']);

		if($group !== false)
		{
			// page output
			$tpl->assign('group', $group);
			$tpl->assign('pageContent', 'li/organizer.calendar.groups.edit.tpl');
			$tpl->display('li/index.tpl');
		}
	}

	//
	// add
	//
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'addForm')
	{
		// page output
		$tpl->assign('pageContent', 'li/organizer.calendar.groups.edit.tpl');
		$tpl->display('li/index.tpl');
	}

	//
	// list
	//
	else
	{
		// save — F2: CSRF-safe on any method with a valid token.
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'save'
			&& isset($_REQUEST['id']))
		{
			CsrfEnforceOnStateChange();
			$calendar->UpdateGroup((int)$_REQUEST['id'],
				$_REQUEST['title'],
				(int)$_REQUEST['color']);
		}

		// delete — F2: CSRF-token required.
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'delete'
			&& isset($_REQUEST['id']))
		{
			CsrfEnforceOnStateChange();
			$calendar->DeleteGroup((int)$_REQUEST['id']);
		}

		// mass delete — F2: CSRF-token required.
		else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'action'
				&& isset($_REQUEST['do2']) && $_REQUEST['do2'] == 'delete')
		{
			CsrfEnforceOnStateChange();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 6) == 'group_')
				{
					$id = (int)substr($key, 6);
					$calendar->DeleteGroup($id);
				}
		}

		// add — F2: CSRF-token required.
		else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add'
				&& isset($_REQUEST['title']) && isset($_REQUEST['color']))
		{
			CsrfEnforceOnStateChange();
			$calendar->AddGroup($_REQUEST['title'], (int)$_REQUEST['color'], '', '',
				isset($_REQUEST['calendar']) ? (int)$_REQUEST['calendar'] : $currentCalendarID);
		}


		$sortColumns = array('title', 'color');

		// get sort info
		$sortColumn = (isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], $sortColumns))
						? $_REQUEST['sort']
						: 'title';
		$sortOrder = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')))
						? $_REQUEST['order']
						: 'asc';
		$sortOrderFA = ($sortOrder=="desc")?'fa-arrow-down': 'fa-arrow-up';

		// group list (colour filters belong to the current calendar)
		$groups = $calendar->GetGroups($sortColumn, $sortOrder, $currentCalendarID);

		// page output
		$tpl->assign('haveGroups', count($groups) > 1);
		$tpl->assign('groups', $groups);
		$tpl->assign('sortColumn', $sortColumn);
		$tpl->assign('sortOrder', $sortOrderFA);
		$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
		$tpl->assign('pageContent', 'li/organizer.calendar.groups.tpl');
		$tpl->display('li/index.tpl');
	}
}

/**
 * add form
 */
else if($_REQUEST['action'] == 'addDate')
{
	$startDate = isset($_REQUEST['date'])
		? (int)$_REQUEST['date']
		: time();
	$startTime = isset($_REQUEST['time'])
		? (int)$_REQUEST['time']
		: time();

	// determine pre-filled end date/time
	$isWholeDay = (isset($_REQUEST['wholeDay']) && (int)$_REQUEST['wholeDay'] === 1);
	if(isset($_REQUEST['endTime']))
	{
		$endTime = (int)$_REQUEST['endTime'];
	}
	else if(isset($_REQUEST['durationHours']) || isset($_REQUEST['durationMinutes']))
	{
		$dh = isset($_REQUEST['durationHours']) ? max(0, (int)$_REQUEST['durationHours']) : 0;
		$dm = isset($_REQUEST['durationMinutes']) ? max(0, (int)$_REQUEST['durationMinutes']) : 0;
		// clamp to something reasonable
		$dh = min($dh, 24 * 366);
		$dm = min($dm, 59);
		$endTime = $startTime + $dh * TIME_ONE_HOUR + $dm * TIME_ONE_MINUTE;
	}
	else
	{
		// sensible default: 1 hour appointment
		$endTime = $startTime + TIME_ONE_HOUR;
	}

	// For whole-day pre-selection, snap the visible fields to day boundaries
	if($isWholeDay)
	{
		$startTime = mktime(0, 0, 0, date('m', $startTime), date('d', $startTime), date('Y', $startTime));
		$endTime = mktime(0, 0, 0, date('m', $endTime), date('d', $endTime), date('Y', $endTime));
		$tpl->assign('preWholeDay', true);
	}

	// assign
	$tpl->assign('date', $startDate);
	$tpl->assign('theDate', $startDate);
	$tpl->assign('calWeekNo', (int)date('W', $startDate));
	$tpl->assign('prevMonth', mktime(0, 0, 0, date('m', $startDate), 15, date('Y', $startDate)) - TIME_ONE_MONTH);
	$tpl->assign('nextMonth', mktime(0, 0, 0, date('m', $startDate), 15, date('Y', $startDate)) + TIME_ONE_MONTH);
	$tpl->assign('prevWeek', $startDate - TIME_ONE_WEEK);
	$tpl->assign('nextWeek', $startDate + TIME_ONE_WEEK);
	$tpl->assign('thisMonthText', _strftime('%B %Y', mktime(0, 0, 0, date('m', $startDate), 15, date('Y', $startDate))));
	$tpl->assign('pageTitle', $lang_user['adddate']);
	$tpl->assign('weekDays', $lang_user['full_weekdays']);
	$tpl->assign('startDate', $startDate);
	$tpl->assign('startTime', $startTime);
	$tpl->assign('endTime', $endTime);
	// ISO strings for HTML5 <input type="date"> / <input type="time">
	$tpl->assign('startDateISO', date('Y-m-d', $startTime));
	$tpl->assign('startTimeISO', date('H:i', $startTime));
	$tpl->assign('endDateISO', date('Y-m-d', $endTime));
	$tpl->assign('endTimeISO', date('H:i', $endTime));
	$tpl->assign('pageContent', 'li/organizer.calendar.edit.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * create date
 */
else if($_REQUEST['action'] == 'createDate'
		&& IsPOSTRequest())
{
	$attendees = array();
	if(trim($_REQUEST['attendees']) != '')
	{
		$attendeeList = explode(';', _unescape($_REQUEST['attendees']));
		foreach($attendeeList as $attendeeItem)
		{
			list($attendeeID) = explode(',', $attendeeItem);
			$attendees[] = $attendeeID;
		}
	}

	$row = $calendar->Form2Row();
	if(empty($row['dav_uid']))
		$row['dav_uid'] = 'b1gmail-'.uniqid('', true).'@'.preg_replace('/[^a-zA-Z0-9.\-]/', '', $_SERVER['HTTP_HOST'] ?? 'local');
	$dateID = $calendar->AddDate($row, $attendees);

	if($dateID && isset($_REQUEST['sendInvites']) && count($attendees) > 0)
	{
		$row['id'] = $dateID;
		bmCalendarSendInvites($userRow, $thisUser, $row, $attendees);
	}

	SessionRedirect('organizer.calendar.php?date=' . $row['startdate']);
	exit();
}

/**
 * delete date
 */
else if($_REQUEST['action'] == 'deleteDate'
		&& isset($_REQUEST['id']))
{
	// F2: dates were deletable via GET (click-trick / <img>-based CSRF).
	CsrfEnforceOnStateChange();
	$calendar->DeleteDate((int)$_REQUEST['id']);
	SessionRedirect('organizer.calendar.php');
	exit();
}

/**
 * edit date
 */
else if($_REQUEST['action'] == 'editDate'
		&& isset($_REQUEST['id']))
{
	$date = $calendar->GetDate((int)$_REQUEST['id']);
	if($date && !$calendar->CanWriteCalendar((int)$date['calendar_id']))
	{
		SessionRedirect('organizer.calendar.php');
		exit();
	}
	if($date)
	{
		$date['repeating'] = ($date['repeat_flags']&CLNDR_REPEATING_DAILY)
							|| ($date['repeat_flags']&CLNDR_REPEATING_MONTHLY_MDAY)
							|| ($date['repeat_flags']&CLNDR_REPEATING_MONTHLY_WDAY)
							|| ($date['repeat_flags']&CLNDR_REPEATING_WEEKLY)
							|| ($date['repeat_flags']&CLNDR_REPEATING_YEARLY);

		// end date/time for the form (replaces the legacy duration input)
		if(($date['flags']&CLNDR_WHOLE_DAY) != 0)
		{
			// whole-day event: normalize end to the day the event ends on
			$endTimeForForm = mktime(0, 0, 0, date('m', $date['enddate']), date('d', $date['enddate']), date('Y', $date['enddate']));
		}
		else
		{
			$endTimeForForm = $date['enddate'];
		}
		$tpl->assign('endTime', $endTimeForForm);

		// attendees
		$attendees = $calendar->GetDateAttendees((int)$_REQUEST['id']);
		$attendeeStr = array();
		foreach($attendees as $attendee)
			$attendeeStr[] = sprintf('%d,%s,%s',
				$attendee['id'],
				str_replace(array(',', ';'), '', $attendee['nachname']),
				str_replace(array(',', ';'), '', $attendee['vorname']));

		// days?
		if($date['repeat_flags'] & CLNDR_REPEATING_DAILY)
		{
			$repeatExtraDays = array_flip(explode(',', $date['repeat_extra1']));
			foreach($repeatExtraDays as $key=>$val)
				$repeatExtraDays[$key] = true;
			$tpl->assign('repeatExtraDays', $repeatExtraDays);
		}

		// assign
		$apptDate = $date['startdate'];
		$tpl->assign('date', $apptDate);
		$tpl->assign('theDate', $apptDate);
		$tpl->assign('calWeekNo', (int)date('W', $apptDate));
		$tpl->assign('prevMonth', mktime(0, 0, 0, date('m', $apptDate), 15, date('Y', $apptDate)) - TIME_ONE_MONTH);
		$tpl->assign('nextMonth', mktime(0, 0, 0, date('m', $apptDate), 15, date('Y', $apptDate)) + TIME_ONE_MONTH);
		$tpl->assign('prevWeek', $apptDate - TIME_ONE_WEEK);
		$tpl->assign('nextWeek', $apptDate + TIME_ONE_WEEK);
		$tpl->assign('thisMonthText', _strftime('%B %Y', mktime(0, 0, 0, date('m', $apptDate), 15, date('Y', $apptDate))));
		$tpl->assign('pageTitle', $lang_user['editdate']);
		$tpl->assign('attendees', implode(';', $attendeeStr));
		$tpl->assign('startDate', $date['startdate']);
		$tpl->assign('startTime', $date['startdate']);
		$tpl->assign('eDate', $date);
		$tpl->assign('weekDays', $lang_user['full_weekdays']);
		// ISO strings for HTML5 <input type="date"> / <input type="time">
		$tpl->assign('startDateISO', date('Y-m-d', $date['startdate']));
		$tpl->assign('startTimeISO', date('H:i', $date['startdate']));
		$tpl->assign('endDateISO', date('Y-m-d', $endTimeForForm));
		$tpl->assign('endTimeISO', date('H:i', $endTimeForForm));
		$tpl->assign('pageContent', 'li/organizer.calendar.edit.tpl');
		$tpl->display('li/index.tpl');
	}
}

/**
 * save date
 */
else if($_REQUEST['action'] == 'saveDate'
		&& isset($_REQUEST['id'])
		&& IsPOSTRequest())
{
	$attendees = array();
	if(trim($_REQUEST['attendees']) != '')
	{
		$attendeeList = explode(';', $_REQUEST['attendees']);
		foreach($attendeeList as $attendeeItem)
		{
			list($attendeeID) = explode(',', $attendeeItem);
			$attendees[] = $attendeeID;
		}
	}

	$row = $calendar->Form2Row();
	$dateID = (int)$_REQUEST['id'];
	$calendar->ChangeDate($dateID, $row, $attendees);

	if(isset($_REQUEST['sendInvites']) && count($attendees) > 0)
	{
		$row['id'] = $dateID;
		if(empty($row['dav_uid']))
		{
			$existing = $calendar->GetDate($dateID);
			if($existing !== false && !empty($existing['dav_uid']))
				$row['dav_uid'] = $existing['dav_uid'];
		}
		bmCalendarSendInvites($userRow, $thisUser, $row, $attendees);
	}

	$jumpbackDate = isset($_REQUEST['jumpbackDate']) ? (int)$_REQUEST['jumpbackDate'] : $row['startdate'];

	SessionRedirect('organizer.calendar.php?date=' . $jumpbackDate);
	exit();
}
