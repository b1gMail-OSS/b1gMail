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

include('../serverlib/init.inc.php');
if(!class_exists('BMCalendar'))
	include('../serverlib/calendar.class.php');
if(!class_exists('BMAddressbook'))
	include('../serverlib/addressbook.class.php');
RequestPrivileges(PRIVILEGES_USER | PRIVILEGES_MOBILE);

/**
 * calendar interface
 */
$calendar = _new('BMCalendar', array($userRow['id']));

/**
 * assign
 */
$tpl->assign('activeTab', 	'calendar');

/**
 * default action
 */
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'list';

/**
 * date list
 */
if($_REQUEST['action'] == 'list')
{
	$dates = $calendar->GetDatesForTimeframe(time(), time() + 6*TIME_ONE_MONTH);
	$dates = array_slice($dates, 0, 50);

	$tpl->assign('dates', $dates);
	$tpl->assign('pageTitle', $lang_user['calendar']);
	$tpl->assign('page', 'm/calendar.list.tpl');
	$tpl->display('m/index.tpl');
}

/**
 * date details
 */
else if($_REQUEST['action'] == 'show' && isset($_REQUEST['id']))
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
		$tpl->assign('notes', nl2br(HTMLFormat($date['text'])));
		$tpl->assign('date', $date);
		$tpl->assign('attendees', $attendees);
		$tpl->assign('mailTo', $mailTo);
		$tpl->assign('mailSubject', $mailSubject);
		$tpl->assign('pageTitle', HTMLFormat($date['title']));
		$tpl->assign('page', 'm/calendar.show.tpl');
		$tpl->display('m/index.tpl');
	}
}
?>