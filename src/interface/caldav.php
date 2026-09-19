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

define('INTERFACE_MODE', 		true);

if(!defined('B1GMAIL_INIT'))
	require '../serverlib/init.inc.php';
include('../serverlib/dav.inc.php');
include('../serverlib/organizerdav.inc.php');
include('../serverlib/calendar.class.php');
include('../serverlib/todo.class.php');

use Sabre\DAV;
use Sabre\VObject\DateTimeParser;

class BMCalDAVBackend extends Sabre\CalDAV\Backend\AbstractBackend
{
	private static $wDays = array('SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA');
	private static $supportedReminderIntervalMinutes = array(5, 10, 15, 30, 45, 60, 120, 240, 480, 720, 1440, 2880, 5760, 8640, 10080, 20160, 30240, 40320);

	public function getCalendarsForUser($principalUri)
	{
		global $os;

		// Sabre traverses the node tree BEFORE the DAV\Auth\Plugin finishes
		// authenticating the caller, and BMPrincipalBackend intentionally
		// returns a placeholder principal for unauthenticated probes (so
		// DAVACL can raise a proper 401 challenge instead of us leaking a
		// 404). Consequence: this backend can be invoked without our
		// BMCalDAVAuthBackend::setupState() having populated $os->calendar
		// yet. Signal 'auth required' rather than crashing on null.
		if(!$os->calendar || !$os->todo)
			throw new Sabre\DAV\Exception\NotAuthenticated('Authentication required');

		$result = array();

		foreach($os->calendar->GetCalendars() as $calendarRow)
		{
			$uri = !empty($calendarRow['dav_uri'])
				? $calendarRow['dav_uri']
				: (!empty($calendarRow['is_default'])
					? 'calendar'
					: bmOrganizerUniqueCollectionDavUri($os->userRow['id'], $calendarRow['title'], 'cal', $calendarRow['id']));
			$result[] = array(
				'id'				=> array(BMCL_TYPE_CALENDAR, $calendarRow['id']),
				'uri'				=> $uri,
				'principaluri'		=> $os->getPrincipalURI(),
				'{DAV:}displayname'	=> $calendarRow['title'],
				'{http://apple.com/ns/ical/}calendar-color' => bmOrganizerCalendarColorHex($calendarRow['color']),
				'{' . Sabre\CalDAV\Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre\CalDAV\Xml\Property\SupportedCalendarComponentSet(array('VEVENT')),
				// getctag = collection-level change signal for Thunderbird /
				// macOS Calendar / DAVx5 / Evolution: they check this cheap
				// property first and only fall back to a full PROPFIND
				// depth=1 + per-item GET when it changed. Without it, clients
				// happily use stale caches and never notice server-side
				// additions or deletions.
				'{http://calendarserver.org/ns/}getctag' => $os->getCollectionCtag($calendarRow['id'], BMCL_TYPE_CALENDAR)
			);
		}

		foreach($os->calendar->GetSharedCalendars() as $calendarRow)
		{
			// F8: A shared calendar handed to us with share_access=READ
			// must be advertised to the CalDAV client as read-only.
			// Sabre's Calendar::getACL() honours the boolean flag
			// `{http://sabredav.org/ns}read-only` and strips {DAV:}write
			// from the ACL when set — so Thunderbird / macOS Calendar /
			// DAVx5 see current-user-privilege-set without write and
			// disable the "add event" UI for the collection instead of
			// silently failing with 403 later.
			$readOnly = (isset($calendarRow['share_access'])
				&& $calendarRow['share_access'] !== BM_ORGANIZER_ACCESS_WRITE);

			$result[] = array(
				'id'				=> array(BMCL_TYPE_CALENDAR, $calendarRow['id']),
				'uri'				=> 'shared-cal-' . $calendarRow['id'],
				'principaluri'		=> $os->getPrincipalURI(),
				'{DAV:}displayname'	=> $calendarRow['title'] . ' (' . $calendarRow['owner_email'] . ')',
				'{http://apple.com/ns/ical/}calendar-color' => bmOrganizerCalendarColorHex($calendarRow['color']),
				'{' . Sabre\CalDAV\Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre\CalDAV\Xml\Property\SupportedCalendarComponentSet(array('VEVENT')),
				'{http://sabredav.org/ns}read-only' => $readOnly,
				'{http://calendarserver.org/ns/}getctag' => $os->getCollectionCtag($calendarRow['id'], BMCL_TYPE_CALENDAR)
			);
		}

		$taskLists = $os->todo->GetTaskLists();
		foreach($taskLists as $list)
		{
			// F8: Same treatment for shared task lists. The GetTaskLists()
			// resultset marks shared lists with `shared` + `share_access`.
			$readOnly = !empty($list['shared'])
				&& (isset($list['share_access']) ? $list['share_access'] : '') !== BM_ORGANIZER_ACCESS_WRITE;

			$result[] = array(
				'id'				=> array(BMCL_TYPE_TODO, $list['tasklistid']),
				'uri'				=> !empty($list['dav_uri']) ? $list['dav_uri'] : 'tasklist-' . $list['tasklistid'],
				'principaluri'		=> $os->getPrincipalURI(),
				'{DAV:}displayname'	=> $list['title'],
				'{' . Sabre\CalDAV\Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre\CalDAV\Xml\Property\SupportedCalendarComponentSet(array('VTODO')),
				'{http://sabredav.org/ns}read-only' => $readOnly,
				'{http://calendarserver.org/ns/}getctag' => $os->getCollectionCtag($list['tasklistid'], BMCL_TYPE_TODO)
			);
		}

		return($result);
	}

	public function createCalendar($principalUri, $calendarUri, array $properties)
	{
		global $os;

		$key = '{' . Sabre\CalDAV\Plugin::NS_CALDAV . '}supported-calendar-component-set';

		if(!isset($properties[$key]) || !($properties[$key] instanceof Sabre\CalDAV\Xml\Property\SupportedCalendarComponentSet))
			throw new Sabre\DAV\Exception('The ' . $key . ' property must be of type: \Sabre\CalDAV\Xml\Property\SupportedCalendarComponentSet');

		$type = strtoupper(implode(',', $properties[$key]->getValue()));

		if($type == 'VTODO')
		{
			$taskListID = $os->todo->AddTaskList($properties['{DAV:}displayname'], $calendarUri);
			return(array(BMCL_TYPE_TODO, $taskListID));
		}
		else if($type == 'VEVENT')
		{
			$color = 0;
			$colorKey = '{http://apple.com/ns/ical/}calendar-color';
			if(isset($properties[$colorKey]))
				$color = bmOrganizerCalendarColorFromHex($properties[$colorKey]);
			$calendarID = $os->calendar->AddCalendar($properties['{DAV:}displayname'], $color, $calendarUri);
			return(array(BMCL_TYPE_CALENDAR, $calendarID));
		}
		else
			throw new Sabre\DAV\Exception('Unsupported component set: ' . $type);
	}

	public function updateCalendar($calendarId, \Sabre\DAV\PropPatch $propPatch)
	{
		global $os;

		if(!is_array($calendarId))
			throw new Sabre\DAV\Exception\NotFound();

		if($calendarId[0] == BMCL_TYPE_TODO)
		{
			if($calendarId[1] == 0)
				throw new Sabre\DAV\Exception\Forbidden('Main task list cannot be altered');

			$supportedProperties = array('{DAV:}displayname');

			$propPatch->handle($supportedProperties, function($mutations) use($calendarId, $os)
			{
				foreach($mutations as $key=>$val)
				{
					if($key == '{DAV:}displayname')
					{
						$os->todo->ChangeTaskList($calendarId[1], $val);
					}
				}

				return(true);
			});
		}
		else if($calendarId[0] == BMCL_TYPE_CALENDAR)
		{
			$cal = $os->calendar->GetAccessibleCalendar($calendarId[1]);
			if($cal === false)
				throw new Sabre\DAV\Exception\NotFound();
			if(!empty($cal['shared']))
				throw new Sabre\DAV\Exception\Forbidden('Shared calendar cannot be altered');

			$supportedProperties = array('{DAV:}displayname', '{http://apple.com/ns/ical/}calendar-color');

			$propPatch->handle($supportedProperties, function($mutations) use($calendarId, $os, $cal)
			{
				$title = $cal['title_raw'];
				$color = (int)$cal['color'];
				foreach($mutations as $key=>$val)
				{
					if($key == '{DAV:}displayname')
						$title = $val;
					else if($key == '{http://apple.com/ns/ical/}calendar-color')
						$color = bmOrganizerCalendarColorFromHex($val);
				}
				$os->calendar->UpdateCalendar($calendarId[1], $title, $color);

				return(true);
			});
		}
		else
			throw new Sabre\DAV\Exception\NotFound();
	}

	public function deleteCalendar($calendarId)
	{
		global $os;

		if(!is_array($calendarId))
			throw new Sabre\DAV\Exception\NotFound();

		if($calendarId[0] == BMCL_TYPE_TODO)
		{
			$os->todo->DeleteTaskList($calendarId[1]);
		}
		else if($calendarId[0] == BMCL_TYPE_CALENDAR)
		{
			$cal = $os->calendar->GetAccessibleCalendar($calendarId[1]);
			if($cal === false)
				throw new Sabre\DAV\Exception\NotFound();
			if(!empty($cal['shared']))
				throw new Sabre\DAV\Exception\Forbidden('Shared calendar cannot be deleted');
			if(!$os->calendar->DeleteCalendar($calendarId[1], true))
				throw new Sabre\DAV\Exception\Forbidden('Default calendar cannot be deleted');
		}
		else
			throw new Sabre\DAV\Exception\NotFound();
	}

	public function getCalendarObjects($calendarId)
	{
		global $os, $db;

		$result = array();

		if(!is_array($calendarId))
			return($result);

		if($calendarId[0] == BMCL_TYPE_TODO)
		{
			$items = $os->todo->GetTodoList('id', 'ASC', -1, $calendarId[1]);

			if(count($items) > 0)
			{
				foreach($os->getLastModified(array_keys($items), BMCL_TYPE_TODO) as $itemID=>$lastModified)
					$items[$itemID]['lastmodified'] = $lastModified;
			}

			foreach($items as $item)
			{
				$data = $this->rowToVTodo($item)->serialize();

				$result[] = array(
					'id'			=> array(BMCL_TYPE_TODO, $item['id']),
					'uri'			=> !empty($item['dav_uri']) ? $item['dav_uri'] : 'item-' . $item['id'],
					'lastmodified'	=> $item['lastmodified'],
					'calendardata'	=> $data,
					'size'			=> strlen($data),
					'calendarid'	=> $calendarId,
					'component'		=> 'vtodo'
				);
			}
		}
		else if($calendarId[0] == BMCL_TYPE_CALENDAR)
		{
			$cal = $os->calendar->GetAccessibleCalendar($calendarId[1]);
			if($cal === false)
				throw new Sabre\DAV\Exception\NotFound();

			$res = $db->Query('SELECT * FROM {pre}dates WHERE `user`=? AND `calendar_id`=?',
				$cal['user'],
				$calendarId[1]);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$result[] = $this->eventRowToCalendarObject($row, $calendarId);
			$res->Free();
		}
		else
			throw new Sabre\DAV\Exception\NotFound();

		return($result);
	}

	public function getCalendarObject($calendarId, $objectUri)
	{
		global $os;

		if(!is_array($calendarId))
			return(null);

		if($calendarId[0] == BMCL_TYPE_TODO)
		{
			$item = $os->todo->GetTask($this->taskURItoID($objectUri));
			if(!$item)
				return(null);
			// Make DTSTAMP/LAST-MODIFIED deterministic — see rowToVTodo().
			$item['lastmodified'] = $os->getLastModified($item['id'], BMCL_TYPE_TODO);
			$data = $this->rowToVTodo($item)->serialize();

			return(array(
				'id'			=> array(BMCL_TYPE_TODO, $item['id']),
				'uri'			=> !empty($item['dav_uri']) ? $item['dav_uri'] : 'item-' . $item['id'],
				'lastmodified'	=> $item['lastmodified'],
				'calendardata'	=> $data,
				'size'			=> strlen($data),
				'calendarid'	=> $calendarId,
				'component'		=> 'vtodo'
			));
		}
		else if($calendarId[0] == BMCL_TYPE_CALENDAR)
		{
			$dateID = $this->eventURItoID($objectUri, $calendarId);
			if($dateID === false)
				return(null);
			$item = $os->calendar->GetDate($dateID);
			if(!$item)
				return(null);
			if((int)$item['calendar_id'] !== (int)$calendarId[1])
				return(null);
			return $this->eventRowToCalendarObject($item, $calendarId);
		}
		else
			throw new Sabre\DAV\Exception\NotFound();

		return(null);
	}

	public function createCalendarObject($calendarId, $objectUri, $calendarData)
	{
		global $os;

		if(!is_array($calendarId))
			return(null);

		if($calendarId[0] == BMCL_TYPE_TODO)
		{
			$todo = $this->vTodoToRow(Sabre\VObject\Reader::read($calendarData, Sabre\VObject\Reader::OPTION_FORGIVING));
			$os->todo->Add($todo['beginn'], $todo['faellig'], $todo['akt_status'], $todo['titel'], $todo['priority'], $todo['erledigt'],
				$todo['comments'], $calendarId[1], $objectUri, $todo['dav_uid']);

			return(null);
		}
		else if($calendarId[0] == BMCL_TYPE_CALENDAR)
		{
			if(!$os->calendar->CanWriteCalendar($calendarId[1]))
				throw new Sabre\DAV\Exception\Forbidden();

			$event = $this->vEventToRow(Sabre\VObject\Reader::read($calendarData, Sabre\VObject\Reader::OPTION_FORGIVING));

			$event['group'] 	= -1;
			$event['calendar_id'] = $calendarId[1];
			$event['dav_uri'] 	= $objectUri;

			$os->calendar->AddDate($event, array());

			return(null);
		}
		else
			throw new Sabre\DAV\Exception\NotFound();
	}

	public function updateCalendarObject($calendarId, $objectUri, $calendarData)
	{
		global $os;

		if(!is_array($calendarId))
			return(null);

		if($calendarId[0] == BMCL_TYPE_TODO)
		{
			$todoID = $this->taskURItoID($objectUri);
			if($todoID === false)
				return(null);

			$todo = $this->vTodoToRow(Sabre\VObject\Reader::read($calendarData, Sabre\VObject\Reader::OPTION_FORGIVING));
			$os->todo->Change($todoID, $todo['beginn'], $todo['faellig'], $todo['akt_status'], $todo['titel'], $todo['priority'], $todo['erledigt'],
				$todo['comments'], $calendarId[1], $todo['dav_uid']);

			return(null);
		}
		else if($calendarId[0] == BMCL_TYPE_CALENDAR)
		{
			if(!$os->calendar->CanWriteCalendar($calendarId[1]))
				throw new Sabre\DAV\Exception\Forbidden();

			$dateID = $this->eventURItoID($objectUri, $calendarId);
			if($dateID === false)
				return(null);

			$existing = $os->calendar->GetDate($dateID);
			$event = $this->vEventToRow(Sabre\VObject\Reader::read($calendarData, Sabre\VObject\Reader::OPTION_FORGIVING));

			$event['group'] 	= ($existing !== false && isset($existing['group'])) ? $existing['group'] : -1;
			$event['calendar_id'] = $calendarId[1];
			$event['dav_uri'] 	= $objectUri;

			$os->calendar->ChangeDate($dateID, $event, array());

			return(null);
		}
		else
			throw new Sabre\DAV\Exception\NotFound();
	}

	public function deleteCalendarObject($calendarId, $objectUri)
	{
		global $os;

		if(!is_array($calendarId))
			return;

		if($calendarId[0] == BMCL_TYPE_TODO)
		{
			$todoID = $this->taskURItoID($objectUri);
			if($todoID === false)
				return;

			$os->todo->Delete($todoID);

			return;
		}
		else if($calendarId[0] == BMCL_TYPE_CALENDAR)
		{
			if(!$os->calendar->CanWriteCalendar($calendarId[1]))
				throw new Sabre\DAV\Exception\Forbidden();

			$eventID = $this->eventURItoID($objectUri, $calendarId);
			if($eventID === false)
				return;

			$os->calendar->DeleteDate($eventID);

			return;
		}
		else
			throw new Sabre\DAV\Exception\NotFound();
	}

	private function eventRowToCalendarObject($row, $calendarId)
	{
		global $os;

		// Ensure DTSTAMP/LAST-MODIFIED are deterministic so ETag is stable
		// across reads (otherwise Sabre would md5 a payload with a fresh
		// DTSTAMP=now() every time and clients like Thunderbird would
		// see phantom "server-modified" conflicts on every sync).
		if(!isset($row['lastmodified']))
			$row['lastmodified'] = $os->getLastModified($row['id'], BMCL_TYPE_CALENDAR);
		$data = $this->rowToVEvent($row)->serialize();

		return array(
			'id'			=> array(BMCL_TYPE_CALENDAR, $row['id']),
			'uri'			=> !empty($row['dav_uri']) ? $row['dav_uri'] : 'item-' . $row['id'],
			'lastmodified'	=> $row['lastmodified'],
			'calendardata'	=> $data,
			'size'			=> strlen($data),
			'calendarid'	=> $calendarId,
			'component'		=> 'vevent'
		);
	}

	private function taskURItoID($taskUri)
	{
		global $db, $os;

		if(substr($taskUri, 0, 5) != 'item-' && !empty($taskUri))
		{
			$result = false;

			$res = $db->Query('SELECT `id` FROM {pre}tasks WHERE `user`=? AND `dav_uri`=?',
				$os->userRow['id'],
				$taskUri);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$result = $row['id'];
			}
			$res->Free();

			return($result);
		}

		$taskID = (int)substr($taskUri, 5);
		return($taskID);
	}

	private function eventURItoID($eventUri, $calendarId = null)
	{
		global $db, $os;

		$ownerId = $os->userRow['id'];
		if(is_array($calendarId) && $calendarId[0] == BMCL_TYPE_CALENDAR)
		{
			$cal = $os->calendar->GetAccessibleCalendar($calendarId[1]);
			if($cal !== false)
				$ownerId = $cal['user'];
		}

		if(substr($eventUri, 0, 5) != 'item-' && !empty($eventUri))
		{
			$result = false;

			$res = $db->Query('SELECT `id` FROM {pre}dates WHERE `user`=? AND `dav_uri`=?',
				$ownerId,
				$eventUri);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$result = $row['id'];
			}
			$res->Free();

			return($result);
		}

		$eventID = (int)substr($eventUri, 5);
		return($eventID);
	}

	private function vTodoToRow($obj, $baseRow = null)
	{
		global $os;

		if($baseRow !== null)
			$row = $baseRow;
		else
			$row = array();

		foreach($obj->VTODO as $vTodo)
		{
			$row['titel'] = (string)$vTodo->SUMMARY;

			if($vTodo->UID)
				$row['dav_uid'] = (string)$vTodo->UID;
			else
				$row['dav_uid'] = '';

			if($vTodo->DTSTART)
			{
				$dtStart = $vTodo->DTSTART->getDateTime();
				$dtStart->setTimezone(new DateTimeZone("UTC"));
				$row['beginn'] = $os->fromUTC($dtStart->getTimestamp());
			}
			else if(!$baseRow)
				$row['beginn'] = time();
			if($vTodo->DUE)
			{
				$due = $vTodo->DUE->getDateTime();
				$due->setTimezone(new DateTimeZone("UTC"));
				$row['faellig'] = $os->fromUTC($due->getTimestamp());
			}
			else if(!$baseRow)
				$row['faellig'] = time()+TIME_ONE_DAY;

			if($vTodo->DESCRIPTION)
				$row['comments'] = (string)$vTodo->DESCRIPTION;

			$row['erledigt'] = min(100, max(0, (int)((string)$vTodo->__get('PERCENT-COMPLETE'))));

			switch((string)$vTodo->STATUS)
			{
			case 'IN-PROCESS':
				$row['akt_status'] = TASKS_PROCESSING;
				break;

			case 'COMPLETED':
				$row['akt_status'] = TASKS_DONE;
				break;

			case 'CANCELLED':
				$row['akt_status'] = TASKS_POSTPONED;
				break;

			case 'NEEDS-ACTION':
			default:
				$row['akt_status'] = TASKS_NOTBEGUN;
				break;
			}

			$prio = (int)(string)$vTodo->PRIORITY;
			if($prio > 5)
			{
				$row['priority'] = -1;
			}
			else if($prio > 0 && $prio <= 4)
			{
				$row['priority'] = 1;
			}
			else
			{
				$row['priority'] = 0;
			}

			break;
		}

		return($row);
	}

	private function vEventToRow($obj, $baseRow = null)
	{
		global $os;

		if($baseRow !== null)
			$row = $baseRow;
		else
			$row = array();

		foreach($obj->VEVENT as $vEvent)
		{
			// CLNDR_REMIND_PUSH has no iCalendar/VALARM equivalent, so preserve
			// the existing push preference across a CalDAV round-trip.
			$preservedPush = ($baseRow !== null && !empty($baseRow['flags']))
				? ((int)$baseRow['flags'] & CLNDR_REMIND_PUSH)
				: 0;

			$row['flags']			= $preservedPush;
			$row['repeat_flags']	= 0;
			$row['repeat_value']	= '';
			$row['repeat_extra1']	= '';
			$row['repeat_extra2']	= '';
			$row['repeat_times']	= 0;
			$row['reminder']		= 0;
			$row['title']			= (string)$vEvent->SUMMARY;
			$row['text']			= (string)$vEvent->DESCRIPTION;
			$row['location']		= (string)$vEvent->LOCATION;

			if($vEvent->UID)
				$row['dav_uid'] = (string)$vEvent->UID;
			else
				$row['dav_uid'] = '';

			if($vEvent->DTSTART)
			{
				$dtStart = $vEvent->DTSTART->getDateTime();
				$dtStart->setTimezone(new DateTimeZone("UTC"));
				$row['startdate'] = $os->fromUTC($dtStart->getTimestamp());
			}
			else if(!$baseRow)
				$row['startdate'] = time();

			if($vEvent->DTEND)
			{
				$dtEnd = $vEvent->DTEND->getDateTime();
				$dtEnd->setTimezone(new DateTimeZone("UTC"));
				$row['enddate'] = $os->fromUTC($dtEnd->getTimestamp());
			}
			else if(!$baseRow)
				$row['enddate'] = time();

			if($row['enddate'] - $row['startdate'] == TIME_ONE_DAY)
			{
				$row['flags']		|= CLNDR_WHOLE_DAY;
				$row['enddate']		= $row['startdate'] + 59;
			}

			if($vEvent->RRULE)
			{
				$rRule = array();
				foreach($vEvent->RRULE->getParts() as $key=>$val)
					$rRule[strtoupper($key)] = strtoupper($val);

				$freq = $rRule['FREQ'];
				if($freq == 'DAILY')
				{
					$row['repeat_flags']	|= CLNDR_REPEATING_DAILY;
					$row['repeat_value']	= max(1, (int)$rRule['INTERVAL']);

					if(!empty($rRule['BYDAY']))
					{
						$exceptions = BMCalDAVBackend::$wDays;

						$byDay = explode(',', $rRule['BYDAY']);
						foreach($byDay as $val)
						{
							$index = array_search($val, $exceptions);
							if($index === false)
								continue;
							unset($exceptions[$index]);
						}

						$row['repeat_extra1'] = implode(',', array_keys($exceptions));
					}
				}
				else if($freq == 'WEEKLY')
				{
					$row['repeat_flags']			|= CLNDR_REPEATING_WEEKLY;
					$row['repeat_value']			= max(1, (int)$rRule['INTERVAL']);
				}
				else if($freq == 'MONTHLY')
				{
					$row['repeat_value']			= max(1, (int)$rRule['INTERVAL']);

					if(isset($rRule['BYMONTHDAY']))
					{
						$row['repeat_flags']		|= CLNDR_REPEATING_MONTHLY_MDAY;
						$row['repeat_extra1']		= $rRule['BYMONTHDAY'];
					}
					else if(isset($rRule['BYDAY']) && strlen($rRule['BYDAY']) > 2)
					{
						$wDay = substr($rRule['BYDAY'], -2);
						$wDayIndex = array_search($wDay, BMCalDAVBackend::$wDays);

						if($wDayIndex !== false)
						{
							$row['repeat_flags']	|= CLNDR_REPEATING_MONTHLY_WDAY;
							$row['repeat_extra2']	= $wDayIndex;
							$row['repeat_extra1']	= max(0, (int)substr($rRule['BYDAY'], 0, -2)-1);
						}
					}
				}
				else if($freq == 'YEARLY')
				{
					$row['repeat_flags']			|= CLNDR_REPEATING_YEARLY;
					$row['repeat_value']			= max(1, (int)$rRule['INTERVAL']);
				}

				if(isset($rRule['COUNT']))
				{
					$row['repeat_flags']			|= CLNDR_REPEATING_UNTIL_COUNT;
					$row['repeat_times']			= max(1, $rRule['COUNT']-1);
				}

				if(isset($rRule['UNTIL']) && $vEvent->DTSTART)
				{
					$row['repeat_flags']			|= CLNDR_REPEATING_UNTIL_DATE;

					$vEvent->DTSTART->getDateTime()->getTimezone();
					$until = DateTimeParser::parse($rRule['UNTIL'], $vEvent->DTSTART->getDateTime()->getTimezone());
					$until->setTimezone(new DateTimeZone("UTC"));
					$row['repeat_times'] 			= $os->fromUTC($until->getTimestamp());
				}
			}

			if(isset($vEvent->VALARM))
			{
				foreach($vEvent->VALARM as $vAlarm)
				{
					if(!$vAlarm->TRIGGER)
						continue;

					$dateRef	= new DateTime('@0');
					$interval 	= $vAlarm->TRIGGER->getDateInterval();
					$interval 	= abs($dateRef->add($interval)->getTimestamp());

					// Find the best match among supported reminder intervals
					$closestInterval 		= 0;
					$closestIntervalDiff 	= TIME_ONE_YEAR;
					foreach(BMCalDAVBackend::$supportedReminderIntervalMinutes as $refIntervalMin)
					{
						$refInterval 		= $refIntervalMin * TIME_ONE_MINUTE;
						$refIntervalDiff 	= abs($refInterval - $interval);

						if($refIntervalDiff	< $closestIntervalDiff)
						{
							$closestInterval 		= $refInterval;
							$closestIntervalDiff 	= $refIntervalDiff;
						}
					}

					if($closestInterval > 0 && $closestIntervalDiff < TIME_ONE_YEAR)
					{
						$row['reminder'] = $closestInterval;

						$action = (string)$vAlarm->ACTION;
						if($action == 'EMAIL')
						{
							$row['flags'] |= CLNDR_REMIND_EMAIL;
						}
						else if($action == 'AUDIO' || $action == 'DISPLAY')
						{
							$row['flags'] |= CLNDR_REMIND_NOTIFY;
						}
					}

					// b1gMail currently supported one alarm only
					break;
				}
			}
		}

		return($row);
	}

	private function rowToVEvent($row)
	{
		global $os;

		// Deterministic DTSTAMP/LAST-MODIFIED: use the item's last-modified
		// timestamp when known, otherwise fall back to now (Sabre VObject
		// would default to now() anyway). Stable timestamps keep the ETag
		// stable across reads, which is required for hassle-free client
		// sync (Thunderbird, iOS Calendar, DAVx5, ...).
		$stamp = !empty($row['lastmodified']) ? (int)$row['lastmodified'] : time();
		$stampDt = gmdate('Ymd\\THis\\Z', $stamp);

		$event = array(
			'DTSTAMP'			=> $stampDt,
			'LAST-MODIFIED'		=> $stampDt,
			'DTSTART'			=> new DateTime('@'.$os->toUTC($row['startdate'])),
			'DTEND'				=> new DateTime('@'.$os->toUTC($row['enddate'])),
			'SUMMARY'			=> $row['title'],
			'UID'				=> $os->genUID($row['dav_uid'], 'event-' . $row['id'] . '@' . $os->userRow['id'])
		);

		if($row['flags'] & CLNDR_WHOLE_DAY)
		{
			$event['DTSTART'] 	= date('Ymd', $os->toUTC($row['startdate']));
			$event['DTEND'] 	= date('Ymd', $os->toUTC($row['enddate']));
		}

		if(!empty($row['text']))
			$event['DESCRIPTION'] = $row['text'];

		if(!empty($row['location']))
			$event['LOCATION'] = $row['location'];

		if($row['repeat_flags'] & (CLNDR_REPEATING_DAILY|CLNDR_REPEATING_WEEKLY|CLNDR_REPEATING_MONTHLY_MDAY|CLNDR_REPEATING_MONTHLY_WDAY|CLNDR_REPEATING_YEARLY))
		{
			$rRule = array();

			if($row['repeat_flags'] & CLNDR_REPEATING_DAILY)
			{
				$rRule['FREQ'] = 'DAILY';
				$rRule['INTERVAL'] = $row['repeat_value'];

				if(!empty($row['repeat_extra1']))
				{
					$byDay = BMCalDAVBackend::$wDays;

					$exceptDays = explode(',', $row['repeat_extra1']);
					foreach($exceptDays as $key=>$val)
					{
						if(trim($val) == '')
							continue;
						if(isset($byDay[(int)$val]))
							unset($byDay[(int)$val]);
					}

					$rRule['BYDAY'] = implode(',', $byDay);
				}
			}
			else if($row['repeat_flags'] & CLNDR_REPEATING_WEEKLY)
			{
				$rRule['FREQ'] = 'WEEKLY';
				$rRule['INTERVAL'] = $row['repeat_value'];
			}
			else if($row['repeat_flags'] & CLNDR_REPEATING_MONTHLY_MDAY)
			{
				$rRule['FREQ'] = 'MONTHLY';
				$rRule['INTERVAL'] = $row['repeat_value'];
				$rRule['BYMONTHDAY'] = $row['repeat_extra1'];
			}
			else if($row['repeat_flags'] & CLNDR_REPEATING_MONTHLY_WDAY)
			{
				$rRule['FREQ'] = 'MONTHLY';
				$rRule['INTERVAL'] = $row['repeat_value'];
				$rRule['BYDAY'] = sprintf('%d%s', $row['repeat_extra1']+1, BMCalDAVBackend::$wDays[ $row['repeat_extra2'] ]);
			}
			else if($row['repeat_flags'] & CLNDR_REPEATING_YEARLY)
			{
				$rRule['FREQ'] = 'YEARLY';
				$rRule['INTERVAL'] = $row['repeat_value'];
			}

			if(count($rRule))
			{
				if($row['repeat_flags'] & CLNDR_REPEATING_UNTIL_COUNT)
				{
					$rRule['COUNT'] = $row['repeat_times']+1;
				}
				else if($row['repeat_flags'] & CLNDR_REPEATING_UNTIL_DATE)
				{
					$dt = new DateTime('@'.$os->toUTC($row['repeat_times']));
					$rRule['UNTIL'] = $dt->format('Ymd\\THis\\Z');
				}

				$event['RRULE'] = $rRule;
			}
		}

		$obj = new Sabre\VObject\Component\VCalendar(array('PRODID' => $os->getProdID()));
		$vEvent = $obj->add('VEVENT', $event);
		if($row['flags'] & (CLNDR_REMIND_EMAIL|CLNDR_REMIND_SMS|CLNDR_REMIND_NOTIFY|CLNDR_REMIND_PUSH))
		{
			if(($row['reminder'] % TIME_ONE_WEEK) == 0)
				$trigger = sprintf('-P%dW', $row['reminder']/TIME_ONE_WEEK);
			else if(($row['reminder'] % TIME_ONE_DAY) == 0)
				$trigger = sprintf('-P%d', $row['reminder']/TIME_ONE_DAY);
			else if(($row['reminder'] % TIME_ONE_HOUR) == 0)
				$trigger = sprintf('-PT%dH', $row['reminder']/TIME_ONE_HOUR);
			else if(($row['reminder'] % TIME_ONE_MINUTE) == 0)
				$trigger = sprintf('-PT%dM', $row['reminder']/TIME_ONE_MINUTE);
			else
				$trigger = sprintf('-PT%dS', $row['reminder']);

			// as SMS is not supported by the standard, we use the EMAIL action instead
			if($row['flags'] & (CLNDR_REMIND_EMAIL|CLNDR_REMIND_SMS))
				$vEvent->add('VALARM', array('TRIGGER' => $trigger, 'ACTION' => 'EMAIL'));

			// NOTIFY (in-app) or PUSH map to standard DISPLAY/AUDIO alarms.
			// CLNDR_REMIND_PUSH is preserved server-side across CalDAV writes.
			if($row['flags'] & (CLNDR_REMIND_NOTIFY|CLNDR_REMIND_PUSH))
			{
				$vEvent->add('VALARM', array('TRIGGER' => $trigger, 'ACTION' => 'AUDIO'));
				$vEvent->add('VALARM', array('TRIGGER' => $trigger, 'ACTION' => 'DISPLAY'));
			}
		}

		return($obj);
	}

	private function rowToVTodo($row)
	{
		global $os;

		// Deterministic DTSTAMP/LAST-MODIFIED — see rowToVEvent() for the
		// rationale. Without this, tasks flap between server and client
		// with a "modified on server" prompt on every completion toggle.
		$stamp = !empty($row['lastmodified']) ? (int)$row['lastmodified'] : time();
		$stampDt = gmdate('Ymd\\THis\\Z', $stamp);

		$todo = array(
			'DTSTAMP'			=> $stampDt,
			'LAST-MODIFIED'		=> $stampDt,
			'DTSTART'			=> new DateTime('@'.$os->toUTC($row['beginn'])),
			'DUE'				=> new DateTime('@'.$os->toUTC($row['faellig'])),
			'SUMMARY'			=> $row['titel'],
			'PERCENT-COMPLETE'	=> $row['erledigt'],
			'UID'				=> $os->genUID($row['dav_uid'], 'task-' . $row['id'] . '@' . $os->userRow['id'])
		);

		if(!empty($row['comments']))
			$todo['DESCRIPTION'] = $row['comments'];

		switch($row['priority'])
		{
		case -1:
			$todo['PRIORITY']	= 9;
			break;

		case 0:
			$todo['PRIORITY']	= 0;
			break;

		case 1:
			$todo['PRIORITY']	= 1;
			break;
		}

		switch($row['akt_status'])
		{
		case TASKS_NOTBEGUN:
			$todo['STATUS']				= 'NEEDS-ACTION';
			break;

		case TASKS_PROCESSING:
			$todo['STATUS']				= 'IN-PROCESS';
			break;

		case TASKS_DONE:
			$todo['STATUS']				= 'COMPLETED';
			$todo['PERCENT-COMPLETE']	= 100;
			break;

		case TASKS_POSTPONED:
			$todo['STATUS']				= 'CANCELLED';
			break;
		}

		$obj = new Sabre\VObject\Component\VCalendar(array('PRODID' => $os->getProdID()));
		$obj->add('VTODO', $todo);
		return($obj);
	}
}

class BMCalDAVAuthBackend extends BMAuthBackend
{
	protected function davScope()
	{
		return BMAppPassword::SCOPE_CALDAV;
	}

	function checkPermissions()
	{
		return($this->groupRow['organizerdav'] == 'yes');
	}

	function setupState()
	{
		global $os;

		$os->userObject 	= $this->userObject;
		$os->groupObject 	= $this->groupObject;
		$os->userRow 		= $this->userRow;
		$os->groupRow 		= $this->groupRow;
		$os->calendar 		= _new('BMCalendar', array($os->userRow['id']));
		$os->todo 			= _new('BMTodo', array($os->userRow['id']));
	}
}

$os = new BMOrganizerState;

$principalBackend 	= new BMPrincipalBackend;
$caldavBackend		= new BMCalDAVBackend;

$nodes = array(
	new \Sabre\CalDAV\Principal\Collection($principalBackend),
	new \Sabre\CalDAV\CalendarRoot($principalBackend, $caldavBackend)
);

$server = new DAV\Server($nodes);
$server->setBaseUri(bmDavDetermineBaseUri('caldav'));

$authBackend = new BMCalDAVAuthBackend;
$authBackend->setRealm($bm_prefs['titel'] . ' ' . $lang_user['calendar']);
$authPlugin = new DAV\Auth\Plugin($authBackend);
$server->addPlugin($authPlugin);

$server->addPlugin(new \Sabre\CalDAV\Plugin());

// DAVACL defaults to allowUnauthenticatedAccess=true, which in turn sets
// the Auth plugin's autoRequireLogin=false — meaning Sabre would NOT
// send a WWW-Authenticate: Basic realm=... header on 401 responses.
// Thunderbird (and every other conforming HTTP client) refuses to retry
// with credentials when that header is missing, so CalDAV sync would
// silently break.  We only support authenticated DAV access, so force
// the Auth plugin to challenge on every 401.
$aclPlugin = new \Sabre\DAVACL\Plugin();
$aclPlugin->allowUnauthenticatedAccess = false;
$server->addPlugin($aclPlugin);

$server->addPlugin(new \Sabre\DAV\Sync\Plugin());

$server->exec();
