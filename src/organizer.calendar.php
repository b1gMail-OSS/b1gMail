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

require './serverlib/init.inc.php';
include('./serverlib/todo.class.php');
include('./serverlib/calendar.class.php');
include('./serverlib/addressbook.class.php');
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
	header('Location: start.php?sid=' . session_id());
	exit();
}

/**
 * default action = start
 */
$tpl->addJSFile('li', $tpl->tplDir . 'js/organizer.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'start';
$tpl->assign('activeTab', 'organizer');
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
 * calendar interface
 */
$calendar = _new('BMCalendar', array($userRow['id']));

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
$groups = $calendar->GetGroups();
$group = (($group = $thisUser->GetPref('calendarGroup')) !== false && isset($groups[$group]) ? $group : -2);
$tpl->assign('groups', 			$groups);
$tpl->assign('theGroup',		$group);
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
$tpl->assign('weekYear',		date('o') == 'o' ? date('Y', $date) : date('o', $date));
$tpl->assign('thisMonthText',	_strftime('%B %Y', mktime(0, 0, 0, date('m', $date), 15, date('Y', $date))));
$tpl->assign('smsEnabled',		$thisUser->SMSEnabled());

/**
 * page menu
 */
$todo = _new('BMTodo', array($userRow['id']));
$sideTasks = $todo->GetTodoList('faellig', 'asc', 6, 0, true);
$tpl->assign('tasks_haveMore', count($sideTasks) > 5);
if(count($sideTasks) > 5)
	$sideTasks = array_slice($sideTasks, 0, 5);
$tpl->assign('tasks', $sideTasks);
$tpl->assign('pageMenuFile', 'li/organizer.sidebar.tpl');

/**
 * start page
 */
if($_REQUEST['action'] == 'start')
{
	if($viewMode == 'day')
	{
		$dates = $calendar->GetDatesForTimeframe($dateStart, $dateEnd, $group);
		$tpl->assign('weekDay', date('l', $dateStart));
		$tpl->assign('calWeek', date(date('o') == 'o' ? 'W/Y' : 'W/o', $dateStart));
		$tpl->assign('dates', $dates);
		$tpl->assign('pageContent', 'li/organizer.calendar.dayview.tpl');
	}
	else if($viewMode == 'week')
	{
		$dates = array();
		for($d=0; $d<7; $d++)
		{
			$ts = $dateStart+$d*TIME_ONE_DAY;
			$dDateStart = mktime(0, 0, 0, date('m', $ts), date('d', $ts), date('Y', $ts));
			$dDateEnd = mktime(23, 59, 59, date('m', $ts), date('d', $ts), date('Y', $ts));
			$dates[ _strftime('%A, %d.', $ts) ] = $calendar->GetDatesForTimeframe($dDateStart, $dDateEnd, $group);
		}

		$tpl->assign('curYear', (int)date('Y'));
		$tpl->assign('weekStartDate', $dateStart);
		$tpl->assign('weekEndDate', $dateEnd);
		$tpl->assign('calWeekNo', date('W', $dateStart));
		$tpl->assign('calWeek', date(date('o') == 'o' ? 'W/Y' : 'W/o', $dateStart));
		$tpl->assign('dates', $dates);
		$tpl->assign('pageContent', 'li/organizer.calendar.weekview.tpl');
	}
	else if($viewMode == 'month')
	{
		list($columns, $days) = $calendar->GenerateCalendar((int)date('m', $dateStart), (int)date('Y', $dateStart), -1, $group);
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
	$dates = $calendar->GetDatesForTimeframe($dateStart, $dateEnd, $group);
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
		$tpl->assign('attendees', $attendees);
		$tpl->assign('mailTo', $mailTo);
		$tpl->assign('mailSubject', $mailSubject);
		$tpl->display('li/organizer.calendar.showdate.tpl');
	}
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
		// save
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'save'
			&& isset($_REQUEST['id']))
		{
			$calendar->UpdateGroup((int)$_REQUEST['id'],
				$_REQUEST['title'],
				(int)$_REQUEST['color']);
		}

		// delete
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'delete'
			&& isset($_REQUEST['id']))
		{
			$calendar->DeleteGroup((int)$_REQUEST['id']);
		}

		// mass delete?
		else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'action'
				&& isset($_REQUEST['do2']) && $_REQUEST['do2'] == 'delete')
		{
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 6) == 'group_')
				{
					$id = (int)substr($key, 6);
					$calendar->DeleteGroup($id);
				}
		}

		// add?
		else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'add'
				&& isset($_REQUEST['title']) && isset($_REQUEST['color']))
		{
			$calendar->AddGroup($_REQUEST['title'], (int)$_REQUEST['color']);
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

		// group list
		$groups = $calendar->GetGroups($sortColumn, $sortOrder);

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

	// assign
	$tpl->assign('pageTitle', $lang_user['adddate']);
	$tpl->assign('weekDays', $lang_user['full_weekdays']);
	$tpl->assign('startDate', $startDate);
	$tpl->assign('startTime', $startTime);
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
	$dateID = $calendar->AddDate($row, $attendees);
	header('Location: organizer.calendar.php?sid=' . session_id() . '&date=' . $row['startdate']);
	exit();
}

/**
 * delete date
 */
else if($_REQUEST['action'] == 'deleteDate'
		&& isset($_REQUEST['id']))
{
	$calendar->DeleteDate((int)$_REQUEST['id']);
	header('Location: organizer.calendar.php?sid=' . session_id());
	exit();
}

/**
 * edit date
 */
else if($_REQUEST['action'] == 'editDate'
		&& isset($_REQUEST['id']))
{
	$date = $calendar->GetDate((int)$_REQUEST['id']);
	if($date)
	{
		$date['repeating'] = ($date['repeat_flags']&CLNDR_REPEATING_DAILY)
							|| ($date['repeat_flags']&CLNDR_REPEATING_MONTHLY_MDAY)
							|| ($date['repeat_flags']&CLNDR_REPEATING_MONTHLY_WDAY)
							|| ($date['repeat_flags']&CLNDR_REPEATING_WEEKLY)
							|| ($date['repeat_flags']&CLNDR_REPEATING_YEARLY);

		if(($date['flags']&CLNDR_WHOLE_DAY) == 0)
		{
			$duration = $date['enddate']-$date['startdate'];
			$durationHours = floor($duration / TIME_ONE_HOUR);
			$durationMinutes = ceil(($duration-$durationHours*TIME_ONE_HOUR) / TIME_ONE_MINUTE);
			$tpl->assign('durationHours', $durationHours);
			$tpl->assign('durationMinutes', $durationMinutes);
		}

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
		$tpl->assign('pageTitle', $lang_user['editdate']);
		$tpl->assign('attendees', implode(';', $attendeeStr));
		$tpl->assign('startDate', $date['startdate']);
		$tpl->assign('startTime', $date['startdate']);
		$tpl->assign('eDate', $date);
		$tpl->assign('weekDays', $lang_user['full_weekdays']);
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
	$calendar->ChangeDate((int)$_REQUEST['id'], $row, $attendees);

	$jumpbackDate = isset($_REQUEST['jumpbackDate']) ? (int)$_REQUEST['jumpbackDate'] : $row['startdate'];

	header('Location: organizer.calendar.php?sid=' . session_id() . '&date=' . $jumpbackDate);
	exit();
}
?>