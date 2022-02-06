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

include('../serverlib/admin.inc.php');
RequestPrivileges(PRIVILEGES_ADMIN);
AdminRequirePrivilege('prefs.widgetlayouts');

if(!class_exists('BMDashboard'))
	include(B1GMAIL_DIR . 'serverlib/dashboard.class.php');

function getWidgetArray($type, $widgetOrder)
{
	global $plugins;

	$widgetList = explode(',', str_replace(';', ',', $widgetOrder));
	$tplWidgets = array();
	$widgets = $plugins->getWidgetsSuitableFor($type);
	foreach($widgets as $widget)
	{
		if(in_array($widget, $widgetList))
		{
			$tplWidgets[$widget] = array(
				'title'			=> $plugins->getParam('widgetTitle', $widget)
			);
		}
	}

	return($tplWidgets);
}

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'start';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['startwidgets'],
		'relIcon'	=> 'start32.png',
		'link'		=> 'prefs.widgetlayouts.php?',
		'active'	=> $_REQUEST['action'] == 'start'
	),
	1 => array(
		'title'		=> $lang_admin['organizerwidgets'],
		'relIcon'	=> 'organizer32.png',
		'link'		=> 'prefs.widgetlayouts.php?action=organizer&',
		'active'	=> $_REQUEST['action'] == 'organizer'
	)
);

if($_REQUEST['action'] == 'start')
{
	$widgetType = BMWIDGET_START;
	$orderKey = 'widget_order_start';
}
else if($_REQUEST['action'] == 'organizer')
{
	$widgetType = BMWIDGET_ORGANIZER;
	$orderKey = 'widget_order_organizer';
}
else
	die('Invalid action');

$dashboard = _new('BMDashboard', array($widgetType));

//
// overview
//
if(!isset($_REQUEST['do']))
{
	// save order?
	if(isset($_REQUEST['saveOrder'])
		&& isset($_REQUEST['order']))
	{
		$widgetOrder = trim($_REQUEST['order']);

		if($dashboard->checkWidgetOrder($widgetOrder))
		{
			$db->Query('UPDATE {pre}prefs SET ' . $orderKey . '=?',
				$widgetOrder);
			$bm_prefs[$orderKey] = $widgetOrder;
		}
	}

	// reset order?
	if(isset($_REQUEST['resetOrder'])
		&& isset($_REQUEST['groups'])
		&& is_array($_REQUEST['groups']))
	{
		$db->Query('UPDATE {pre}userprefs SET `value`=? WHERE `key`=? AND `userid` IN (SELECT `id` FROM {pre}users WHERE `gruppe` IN ?)',
			$bm_prefs[$orderKey],
			$orderKey == 'widget_order_start' ? 'widgetOrderStart' : 'widgetOrderOrganizer',
			$_REQUEST['groups']);
	}

	$widgetOrder = $bm_prefs[$orderKey];
	$widgets = getWidgetArray($widgetType, $widgetOrder);

	$tpl->assign('groups', BMGroup::GetSimpleGroupList());
	$tpl->assign('widgetOrder', $widgetOrder);
	$tpl->assign('widgets', $widgets);
	$tpl->assign('page', 'prefs.widgetlayouts.layout.tpl');
}

//
// add/remove
//
else if($_REQUEST['do'] == 'addremove')
{
	$widgetOrder = $bm_prefs[$orderKey];

	// save?
	if(isset($_REQUEST['save']))
	{
		$widgetOrder = $dashboard->generateOrderStringFromPostForm($widgetOrder);
		$db->Query('UPDATE {pre}prefs SET ' . $orderKey . '=?',
			$widgetOrder);
		header('Location: prefs.widgetlayouts.php?action=' . $_REQUEST['action'] . '&sid=' . session_id());
		exit();
	}

	$tpl->assign('possibleWidgets', $dashboard->getPossibleWidgets($widgetOrder));
	$tpl->assign('page', 'prefs.widgetlayouts.addremove.tpl');
}

$tpl->assign('action', $_REQUEST['action']);
$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['widgetlayouts']);
$tpl->display('page.tpl');
?>