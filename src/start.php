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
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'start';
$tpl->assign('activeTab', 'start');
$tpl->assign('pageTitle', $lang_user['start']);

/**
 * page sidebar
 */
$tpl->assign('pageMenuFile', 'li/start.sidebar.tpl');

/**
 * dashboard
 */
$dashboard = _new('BMDashboard', array(BMWIDGET_START));

/**
 * start page
 */
if($_REQUEST['action'] == 'start')
{
	$widgetOrder = $thisUser->GetPref('widgetOrderStart');
	if($widgetOrder === false || trim($widgetOrder) == '')
		$widgetOrder = $bm_prefs['widget_order_start'];

	$tpl->assign('autoSetPreviewPos', !$thisUser->GetPref('previewPosition'));
	$tpl->assign('pageTitle', $lang_user['welcome']);
	$tpl->assign('widgetOrder', $widgetOrder);
	$tpl->assign('widgets', $dashboard->getWidgetArray($widgetOrder));
	$tpl->assign('pageContent', 'li/start.page.tpl');
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
		$thisUser->SetPref('widgetOrderStart', $widgetOrder);
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
	$widgetOrder = $thisUser->GetPref('widgetOrderStart');
	if($widgetOrder === false || trim($widgetOrder) == '')
		$widgetOrder = $bm_prefs['widget_order_start'];

	$tpl->assign('pageTitle', $lang_user['customize']);
	$tpl->assign('possibleWidgets', $dashboard->getPossibleWidgets($widgetOrder));
	$tpl->assign('pageContent', 'li/start.customize.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * save cutomization
 */
else if($_REQUEST['action'] == 'saveCustomize')
{
	$widgetOrder = $thisUser->GetPref('widgetOrderStart');
	if($widgetOrder === false || trim($widgetOrder) == '')
		$widgetOrder = $bm_prefs['widget_order_start'];
	$newOrder = $dashboard->generateOrderStringFromPostForm($widgetOrder);

	$thisUser->SetPref('widgetOrderStart', $newOrder);

	header('Location: start.php?sid=' . session_id());
	exit();
}

/**
 * search
 */
else if($_REQUEST['action'] == 'search'
		&& isset($_REQUEST['q']))
{
	$url = sprintf($bm_prefs['search_engine'], urlencode($_REQUEST['q']));
	header('Location: ' . $url);
	exit();
}

/**
 * widget preferences
 */
else if($_REQUEST['action'] == 'showWidgetPrefs'
		&& isset($_REQUEST['name']))
{
	$dashboard->showWidgetPrefs($_REQUEST['name']);
}

/**
 * safe code validation RPC
 */
else if($_REQUEST['action'] == 'checkSafeCode')
{
	if(!class_exists('BMCaptcha'))
		include(B1GMAIL_DIR . 'serverlib/captcha.class.php');
	$captcha = BMCaptcha::createDefaultProvider();
	if($captcha->check(false))
		echo '1';
	else
		echo '0';
	exit();
}

/**
 * notifications
 */
else if($_REQUEST['action'] == 'getNotifications')
{
	$tpl->assign('bmNotifications', $thisUser->GetNotifications());
	$tpl->display('li/notifications.tpl');
}

/**
 * notification count
 */
else if($_REQUEST['action'] == 'getNotificationCount')
{
	echo $thisUser->GetUnreadNotifications();
	exit();
}

/**
 * logout
 */
else if($_REQUEST['action'] == 'logout')
{
	$thisUser->Logout();
	header('Location: ' . $bm_prefs['logouturl']);
	exit();
}
?>