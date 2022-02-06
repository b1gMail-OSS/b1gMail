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

/**
 * constants for all base widgets
 *
 */
define('BASEWIDGET_ITEMCOUNT',		25);

/**
 * web search widget
 *
 */
class BMPlugin_Widget_Websearch extends BMPlugin
{
	function __construct()
	{
		global $lang_user;

		$this->type				= BMPLUGIN_WIDGET;
		$this->name				= 'Web search widget';
		$this->author			= 'b1gMail Project';
		$this->widgetTemplate	= 'widget.search.tpl';
		$this->widgetTitle		= $lang_user['websearch'];
		$this->website			= 'https://www.b1gmail.org/';
		$this->update_url		= 'https://service.b1gmail.org/plugin_updates/';
		$this->version			= '1.1';

		$this->widgetIcon		= 'widget_websearch.png';
	}

	function isWidgetSuitable($for)
	{
		return($for == BMWIDGET_START
				|| $for == BMWIDGET_ORGANIZER);
	}

	function renderWidget()
	{
		return(true);
	}
}

/**
 * mail space widget
 *
 */
class BMPlugin_Widget_Mailspace extends BMPlugin
{
	function __construct()
	{
		global $lang_user;

		$this->type				= BMPLUGIN_WIDGET;
		$this->name				= 'Mail space widget';
		$this->author			= 'b1gMail Project';
		$this->widgetTemplate	= 'widget.mailspace.tpl';
		$this->widgetTitle		= $lang_user['space'] . ' (' . $lang_user['email'] . ')';
		$this->website			= 'https://www.b1gmail.org/';
		$this->update_url		= 'https://service.b1gmail.org/plugin_updates/';
		$this->version			= '1.1';

		$this->widgetIcon		= 'widget_mailspace.png';
	}

	function isWidgetSuitable($for)
	{
		return($for == BMWIDGET_START);
	}

	function renderWidget()
	{
		global $groupRow, $userRow, $tpl;
		$tpl->assign('bmwidget_mailspace_spaceUsed', $userRow['mailspace_used']);
		$tpl->assign('bmwidget_mailspace_spaceLimit', $groupRow['storage'] + $userRow['mailspace_add']);
		return(true);
	}
}

/**
 * webdisk space widget
 *
 */
class BMPlugin_Widget_Webdiskspace extends BMPlugin
{
	function __construct()
	{
		global $lang_user;

		$this->type				= BMPLUGIN_WIDGET;
		$this->name				= 'Webdisk space widget';
		$this->author			= 'b1gMail Project';
		$this->widgetTemplate	= 'widget.webdiskspace.tpl';
		$this->widgetTitle		= $lang_user['space'] . ' (' . $lang_user['webdisk'] . ')';
		$this->website			= 'https://www.b1gmail.org/';
		$this->update_url		= 'https://service.b1gmail.org/plugin_updates/';
		$this->version			= '1.1';

		$this->widgetIcon		= 'widget_webdiskspace.png';
	}

	function isWidgetSuitable($for)
	{
		return($for == BMWIDGET_START);
	}

	function renderWidget()
	{
		global $tpl, $thisUser;

		if(!class_exists('BMWebdisk'))
			include(B1GMAIL_DIR . 'serverlib/webdisk.class.php');

		$webdisk 	= _new('BMWebdisk', array($thisUser->_id));
		$spaceLimit = $webdisk->GetSpaceLimit();
		$usedSpace 	= $webdisk->GetUsedSpace();

		$tpl->assign('bmwidget_webdiskspace_spaceUsed', $usedSpace);
		$tpl->assign('bmwidget_webdiskspace_spaceLimit', $spaceLimit);
		return(true);
	}
}

/**
 * tasks widget
 *
 */
class BMPlugin_Widget_Tasks extends BMPlugin
{
	function __construct()
	{
		global $lang_user;

		$this->type				= BMPLUGIN_WIDGET;
		$this->name				= 'Task list widget';
		$this->author			= 'b1gMail Project';
		$this->widgetTemplate	= 'widget.tasks.tpl';
		$this->widgetTitle		= $lang_user['tasks'];
		$this->website			= 'https://www.b1gmail.org/';
		$this->update_url		= 'https://service.b1gmail.org/plugin_updates/';
		$this->version			= '1.2';

		$this->widgetIcon		= 'widget_tasks.png';
	}

	function isWidgetSuitable($for)
	{
		return($for == BMWIDGET_START
				|| $for == BMWIDGET_ORGANIZER);
	}

	function renderWidget()
	{
		global $tpl, $thisUser;

		if(!class_exists('BMTodo'))
			include(B1GMAIL_DIR . 'serverlib/todo.class.php');

		$todoList 	= _new('BMTodo', array($thisUser->_id));
		$tasks 		= $todoList->GetTodoList('faellig', 'desc', BASEWIDGET_ITEMCOUNT+1, 0, true);
		$tpl->assign('bmwidget_tasks_haveMore', count($tasks) > BASEWIDGET_ITEMCOUNT);
		if(count($tasks) > BASEWIDGET_ITEMCOUNT)
			$tasks 	= array_slice($tasks, 0, BASEWIDGET_ITEMCOUNT);
		$tpl->assign('bmwidget_tasks_items', $tasks);
		return(true);
	}
}

/**
 * welcome widget
 *
 */
class BMPlugin_Widget_Welcome extends BMPlugin
{
	function __construct()
	{
		global $lang_user;

		$this->type				= BMPLUGIN_WIDGET;
		$this->name				= 'Welcome widget';
		$this->author			= 'b1gMail Project';
		$this->widgetTemplate	= 'widget.welcome.tpl';
		$this->widgetTitle		= $lang_user['welcome'];
		$this->website			= 'https://www.b1gmail.org/';
		$this->update_url		= 'https://service.b1gmail.org/plugin_updates/';
		$this->version			= '1.2';

		$this->widgetIcon		= 'widget_welcome.png';
	}

	function isWidgetSuitable($for)
	{
		return($for == BMWIDGET_START);
	}

	function renderWidget()
	{
		global $lang_user, $userRow, $tpl, $bm_prefs, $thisUser;

		if(!class_exists('BMMailbox'))
			include(B1GMAIL_DIR . 'serverlib/mailbox.class.php');
		if(!class_exists('BMTodo'))
			include(B1GMAIL_DIR . 'serverlib/todo.class.php');
		if(!class_exists('BMCalendar'))
			include(B1GMAIL_DIR . 'serverlib/calendar.class.php');

		// count mails
		$mailbox = _new('BMMailbox', array($thisUser->_id, $userRow['email'], $thisUser));
		$mailCount = $mailbox->GetMailCount(-1, true);

		// count tasks
		$todo = _new('BMTodo', array($thisUser->_id));
		$taskCount = $todo->GetUndoneTaskCount();

		// count dates
		$calendar = _new('BMCalendar', array($thisUser->_id));
		$dateCount = count($calendar->GetDatesForDay((int)date('d'), (int)date('m'), (int)date('Y')));

		$tpl->assign('bmwidget_welcome_welcomeText', sprintf($lang_user['welcometext'],
			HTMLFormat($bm_prefs['titel']),
			HTMLFormat($userRow['vorname'] . ' ' . $userRow['nachname'])));
		$tpl->assign('bmwidget_welcome_mails', sprintf($mailCount == 1 ? $lang_user['newmailtext1'] : $lang_user['newmailtext'],
			$mailCount));
		$tpl->assign('bmwidget_welcome_dates', sprintf($dateCount == 1 ? $lang_user['datetext1'] : $lang_user['datetext'],
			$dateCount));
		$tpl->assign('bmwidget_welcome_tasks', sprintf($taskCount == 1 ? $lang_user['tasktext1'] : $lang_user['tasktext'],
			$taskCount));

		return(true);
	}
}

/**
 * webdisk drop target widget
 *
 */
class BMPlugin_Widget_WebdiskDND extends BMPlugin
{
	function __construct()
	{
		global $lang_user;

		$this->type				= BMPLUGIN_WIDGET;
		$this->name				= 'Webdisk drag and drop widget';
		$this->author			= 'b1gMail Project';
		$this->widgetTemplate	= 'widget.webdiskdnd.tpl';
		$this->widgetTitle		= $lang_user['webdisk'] . ' ' . $lang_user['dnd_upload'];
		$this->website			= 'https://www.b1gmail.org/';
		$this->update_url		= 'https://service.b1gmail.org/plugin_updates/';
		$this->version			= '1.1';

		$this->widgetIcon		= 'widget_webdiskdnd.png';
	}

	function isWidgetSuitable($for)
	{
		return($for == BMWIDGET_START);
	}

	function renderWidget()
	{
		global $tpl;
		$tpl->assign('bmwidget_webdiskdnd_userAgent',		$_SERVER['HTTP_USER_AGENT']);
		$tpl->assign('bmwidget_webdiskdnd_dndKey',			isset($_COOKIE['sessionSecret_' . substr(session_id(), 0, 16)]) ? $_COOKIE['sessionSecret_' . substr(session_id(), 0, 16)] : '');
		return(true);
	}
}

/**
 * calendar widget
 *
 */
class BMPlugin_Widget_Calendar extends BMPlugin
{
	function __construct()
	{
		global $lang_user;

		$this->type				= BMPLUGIN_WIDGET;
		$this->name				= 'Calendar widget';
		$this->author			= 'b1gMail Project';
		$this->widgetTemplate	= 'widget.calendar.tpl';
		$this->widgetTitle		= $lang_user['calendar'];
		$this->website			= 'https://www.b1gmail.org/';
		$this->update_url		= 'https://service.b1gmail.org/plugin_updates/';
		$this->version			= '1.1';

		$this->widgetIcon		= 'widget_calendar.png';
	}

	function isWidgetSuitable($for)
	{
		return($for == BMWIDGET_START
				|| $for == BMWIDGET_ORGANIZER);
	}

	function renderWidget()
	{
		global $tpl, $thisUser;

		if(!class_exists('BMCalendar'))
			include(B1GMAIL_DIR . 'serverlib/calendar.class.php');

		$calendar	= _new('BMCalendar', array($thisUser->_id));

		$nextDatesFrom = time();
		$nextDatesTo = time()+TIME_ONE_MONTH;
		$nextDatesMax = 5;

		$nextDatesMore = 0;
		$nextDates = $calendar->GetDatesForTimeframe($nextDatesFrom, $nextDatesTo);
		if(count($nextDates) > $nextDatesMax+1)
		{
			$nextDatesMore = count($nextDates)-$nextDatesMax;
			$nextDates = array_slice($nextDates, 0, $nextDatesMax);
		}

		$tpl->assign('bmwidget_calendar_html', $calendar->GenerateMiniCalendar(-1, -1, -1, 'miniCalendarTable inWidget'));
		$tpl->assign('bmwidget_calendar_nextDates', $nextDates);
		$tpl->assign('bmwidget_calendar_nextDatesMore', $nextDatesMore);

		return(true);
	}
}

/**
 * notes widget
 *
 */
class BMPlugin_Widget_Notes extends BMPlugin
{
	function __construct()
	{
		global $lang_user;

		$this->type				= BMPLUGIN_WIDGET;
		$this->name				= 'Notes widget';
		$this->author			= 'b1gMail Project';
		$this->widgetTemplate	= 'widget.notes.tpl';
		$this->widgetTitle		= $lang_user['notes'];
		$this->website			= 'https://www.b1gmail.org/';
		$this->update_url		= 'https://service.b1gmail.org/plugin_updates/';
		$this->version			= '1.1';

		$this->widgetIcon		= 'widget_notes.png';
	}

	function isWidgetSuitable($for)
	{
		return($for == BMWIDGET_START
				|| $for == BMWIDGET_ORGANIZER);
	}

	function renderWidget()
	{
		global $tpl, $thisUser;

		if(!class_exists('BMNotes'))
			include(B1GMAIL_DIR . 'serverlib/notes.class.php');

		$notesList 	= _new('BMNotes', array($thisUser->_id));

		$tpl->assign('bmwidget_notes_items', $notesList->GetNoteList('date', 'DESC', BASEWIDGET_ITEMCOUNT));
		return(true);
	}
}

/**
 * e-mail widget
 *
 */
class BMPlugin_Widget_EMail extends BMPlugin
{
	function __construct()
	{
		global $lang_user;

		$this->type				= BMPLUGIN_WIDGET;
		$this->name				= 'EMail widget';
		$this->author			= 'b1gMail Project';
		$this->widgetTemplate	= 'widget.email.tpl';
		$this->widgetTitle		= $lang_user['email'];
		$this->website			= 'https://www.b1gmail.org/';
		$this->update_url		= 'https://service.b1gmail.org/plugin_updates/';
		$this->version			= '1.2';

		$this->widgetPrefs 			= true;
		$this->widgetPrefsWidth		= 320;
		$this->widgetPrefsHeight	= 140;
		$this->widgetIcon			= 'widget_email.png';
	}

	function isWidgetSuitable($for)
	{
		return($for == BMWIDGET_START);
	}

	function renderWidget()
	{
		global $tpl, $thisUser, $userRow;

		if(!class_exists('BMMailbox'))
			include(B1GMAIL_DIR . 'serverlib/mailbox.class.php');

		$mailbox = _new('BMMailbox', array($thisUser->_id, $userRow['email'], $thisUser));
		$folderList = $mailbox->GetFolderList(true, true, true);
		$emailItems = array();

		$hideSystemFolders = (bool)$thisUser->getPref('widget_email.hideSystemFolders');
		$hideCustomFolders = (bool)$thisUser->getPref('widget_email.hideCustomFolders');
		$hideIntelliFolders = (bool)$thisUser->getPref('widget_email.hideIntelliFolders');

		foreach($folderList as $folderID=>$folder)
		{
			if($hideSystemFolders && $folderID <= 0)
				continue;

			if($hideIntelliFolders && $folder['intelligent'])
				continue;

			if($hideCustomFolders && $folderID > 0 && !$folder['intelligent'])
				continue;

			$emailItems[$folderID] = array(
					'link'			=> 'email.php?folder=' . $folderID . '&sid=' . session_id(),
					'text'			=> $folder['title'],
					'icon'			=> $folder['type'],
					'unreadMails'	=> $folder['unread'],
					'allMails'		=> $folder['all'],
					'flaggedMails'	=> $mailbox->GetMailCount($folderID, false, true)
			);
		}

		$tpl->assign('bmwidget_email_items', $emailItems);
		return(true);
	}

	function renderWidgetPrefs()
	{
		global $tpl, $thisUser;

		if(!isset($_REQUEST['save']))
		{
			$tpl->assign('hideSystemFolders', $thisUser->getPref('widget_email.hideSystemFolders') == 1);
			$tpl->assign('hideCustomFolders', $thisUser->getPref('widget_email.hideCustomFolders') == 1);
			$tpl->assign('hideIntelliFolders', $thisUser->getPref('widget_email.hideIntelliFolders') == 1);
			$tpl->display($this->_templatePath('widget.email.prefs.tpl'));
		}
		else
		{
			$thisUser->setPref('widget_email.hideSystemFolders', isset($_REQUEST['hideSystemFolders']) ? 1 : 0);
			$thisUser->setPref('widget_email.hideCustomFolders', isset($_REQUEST['hideCustomFolders']) ? 1 : 0);
			$thisUser->setPref('widget_email.hideIntelliFolders', isset($_REQUEST['hideIntelliFolders']) ? 1 : 0);
			$this->_closeWidgetPrefs();
		}
	}
}

/**
 * quick links widget
 *
 */
class BMPlugin_Widget_Quicklinks extends BMPlugin
{
	function __construct()
	{
		global $lang_user;

		$this->type				= BMPLUGIN_WIDGET;
		$this->name				= 'Quick links widget';
		$this->author			= 'b1gMail Project';
		$this->widgetTemplate	= 'widget.quicklinks.tpl';
		$this->widgetTitle		= $lang_user['quicklinks'];
		$this->website			= 'https://www.b1gmail.org/';
		$this->update_url		= 'https://service.b1gmail.org/plugin_updates/';
		$this->version			= '1.1';

		$this->widgetIcon		= 'widget_quicklinks.png';
	}

	function isWidgetSuitable($for)
	{
		return($for == BMWIDGET_START
				|| $for == BMWIDGET_ORGANIZER);
	}

	function renderWidget()
	{
		return(true);
	}
}

/**
 * register widgets
 */
$plugins->registerPlugin('BMPlugin_Widget_Websearch');
$plugins->registerPlugin('BMPlugin_Widget_Mailspace');
$plugins->registerPlugin('BMPlugin_Widget_Webdiskspace');
$plugins->registerPlugin('BMPlugin_Widget_Tasks');
$plugins->registerPlugin('BMPlugin_Widget_Welcome');
$plugins->registerPlugin('BMPlugin_Widget_WebdiskDND');
$plugins->registerPlugin('BMPlugin_Widget_Calendar');
$plugins->registerPlugin('BMPlugin_Widget_Notes');
$plugins->registerPlugin('BMPlugin_Widget_EMail');
$plugins->registerPlugin('BMPlugin_Widget_Quicklinks');
