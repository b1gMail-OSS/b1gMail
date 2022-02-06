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

include('./serverlib/init.inc.php');
include('./serverlib/todo.class.php');
include('./serverlib/dashboard.class.php');
RequestPrivileges(PRIVILEGES_USER);

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));

/**
 * default action = start
 */
$tpl->addJSFile('li', $tpl->tplDir . 'js/organizer.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'start';
$tpl->assign('activeTab', 'organizer');

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
 * dashboard
 */
$dashboard = _new('BMDashboard', array(BMWIDGET_ORGANIZER));

/**
 * start page
 */
if($_REQUEST['action'] == 'start')
{
	$widgetOrder = $thisUser->GetPref('widgetOrderOrganizer');
	if($widgetOrder === false || trim($widgetOrder) == '')
		$widgetOrder = $bm_prefs['widget_order_organizer'];

	$tpl->assign('pageTitle', $lang_user['organizer']);
	$tpl->assign('widgetOrder', $widgetOrder);
	$tpl->assign('widgets', $dashboard->getWidgetArray($widgetOrder));
	$tpl->assign('pageContent', 'li/organizer.start.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * save widget order
 */
else if($_REQUEST['action'] == 'saveWidgetOrder'
			&& isset($_REQUEST['order']))
{
	$widgetOrder = $_REQUEST['order'];

	if($dashboard->checkWidgetOrder($widgetOrder))
	{
		$thisUser->SetPref('widgetOrderOrganizer', $widgetOrder);
		die('OK');
	}
	else
	{
		die('Invalid order');
	}
}

/**
 * customize widgets
 */
else if($_REQUEST['action'] == 'customize')
{
	$widgetOrder = $thisUser->GetPref('widgetOrderOrganizer');
	if($widgetOrder === false || trim($widgetOrder) == '')
		$widgetOrder = $bm_prefs['widget_order_organizer'];

	$tpl->assign('pageTitle', $lang_user['customize']);
	$tpl->assign('possibleWidgets', $dashboard->getPossibleWidgets($widgetOrder));
	$tpl->assign('pageContent', 'li/organizer.customize.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * save cutomization
 */
else if($_REQUEST['action'] == 'saveCustomize')
{
	$widgetOrder = $thisUser->GetPref('widgetOrderOrganizer');
	if($widgetOrder === false || trim($widgetOrder) == '')
		$widgetOrder = $bm_prefs['widget_order_organizer'];
	$newOrder = $dashboard->generateOrderStringFromPostForm($widgetOrder);

	$thisUser->SetPref('widgetOrderOrganizer', $newOrder);

	header('Location: organizer.php?sid=' . session_id());
	exit();
}
?>