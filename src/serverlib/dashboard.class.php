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
 * dashboard class
 */
class BMDashboard
{
	var $_type;

	/**
	 * constructor
	 *
	 * @param int $type Dashboard type (BMWIDGET_*-constant)
	 * @return BMDashboard
	 */
	function __construct($type)
	{
		$this->_type = $type;
	}

	/**
	 * get array of shown widgets
	 *
	 * @param string $widgetOrder Widget order string
	 * @return array
	 */
	function getWidgetArray($widgetOrder)
	{
		global $plugins;

		$widgetList = explode(',', str_replace(';', ',', $widgetOrder));
		$tplWidgets = array();
		$widgets = $plugins->getWidgetsSuitableFor($this->_type);
		foreach($widgets as $widget)
		{
			if(in_array($widget, $widgetList))
			{
				$plugins->callFunction('renderWidget', $widget);
				$tplWidgets[$widget] = array(
					'template'		=> $plugins->pluginResourcePath($plugins->getParam('widgetTemplate', $widget), $widget, 'template'),
					'hasPrefs'		=> $plugins->getParam('widgetPrefs', $widget),
					'prefsW'		=> (int)$plugins->getParam('widgetPrefsWidth', $widget),
					'prefsH'		=> (int)$plugins->getParam('widgetPrefsHeight', $widget),
					'icon'			=> $plugins->getParam('widgetIcon', $widget) !== false
										? './plugins/templates/images/' . $plugins->getParam('widgetIcon', $widget)
										: '',
					'title'			=> $plugins->getParam('widgetTitle', $widget)
				);
			}
		}

		return($tplWidgets);
	}

	/**
	 * check widget order string
	 *
	 * @param string $widgetOrder Order string
	 * @return bool
	 */
	function checkWidgetOrder($widgetOrder)
	{
		global $plugins;

		$widgets = $plugins->getWidgetsSuitableFor($this->_type);
		$widgetList = explode(',', str_replace(';', ',', $widgetOrder));

		foreach($widgetList as $widget)
			if($widget != '' && !in_array($widget, $widgets))
				return(false);

		return(true);
	}

	/**
	 * widget sort callback for uasort()
	 *
	 * @param array $a
	 * @param array $b
	 * @return number
	 */
	function _sortWidgets($a, $b)
	{
		return(strcmp($a['title'], $b['title']));
	}

	/**
	 * get all possible widgets suitable for this dashboard
	 *
	 * @param string $widgetOrder Current order string
	 * @return array
	 */
	function getPossibleWidgets($widgetOrder)
	{
		global $plugins;

		$possibleWidgets = $plugins->getWidgetsSuitableFor($this->_type);
		$activeWidgets = explode(',', str_replace(';', ',', $widgetOrder));

		$myWidgets = array();
		foreach($possibleWidgets as $widget)
		{
			$myWidgets[$widget] = array(
				'icon'			=> $plugins->getParam('widgetIcon', $widget) !== false
									? './plugins/templates/images/' . $plugins->getParam('widgetIcon', $widget)
									: '',
				'title'		=> $plugins->getParam('widgetTitle', $widget),
				'active'	=> in_array($widget, $activeWidgets)
			);
		}

		uasort($myWidgets, array($this, '_sortWidgets'));

		return($myWidgets);
	}

	/**
	 * shows widgets preferences
	 *
	 * @param string $widgetName Widget name
	 */
	function showWidgetPrefs($widgetName)
	{
		global $plugins, $tpl;

		$allWidgets = array_merge($plugins->getWidgetsSuitableFor(BMWIDGET_ORGANIZER),
								  $plugins->getWidgetsSuitableFor(BMWIDGET_START));

		if(in_array($widgetName, $allWidgets)
		   && $plugins->getParam('widgetPrefs', $widgetName))
		{
			$tpl->assign('widgetPrefsURL', sprintf('start.php?action=showWidgetPrefs&name=%s&sid=%s',
												   $widgetName,
												   session_id()));
			$plugins->callFunction('renderWidgetPrefs', $widgetName);
		}
	}

	/**
	 * generate new order string from posted customize form
	 *
	 * @param string $widgetOrder Current order string
	 * @return string New order string
	 */
	function generateOrderStringFromPostForm($widgetOrder)
	{
		global $plugins;

		$possibleWidgets = $plugins->getWidgetsSuitableFor($this->_type);

		// get al activated widgets
		$newOrder = '';
		$newWidgets = array();
		foreach($_POST as $key=>$val)
			if(substr($key, 0, 7) == 'widget_'
				&& in_array(substr($key, 7), $possibleWidgets))
				$newWidgets[] = substr($key, 7);

		// explode old order string
		$rows = explode(';', $widgetOrder);
		$newRows = array();

		// remove deactivated widgets
		foreach($rows as $row)
		{
			$cols = explode(',', $row);

			foreach($cols as $col)
				if($col != '' && !in_array($col, $newWidgets))
					$newOrder .= ',';
				else
				{
					$newOrder .= $col . ',';
					if($col != '')
						unset($newWidgets[array_search($col, $newWidgets)]);
				}

			$newOrder = substr($newOrder, 0, -1) . ';';
		}

		// add new activated widgets
		$i = 0;
		foreach($newWidgets as $widget)
		{
			$i++;
			$newOrder .= $widget . ',';

			if($i%3 == 0)
				$newOrder = substr($newOrder, 0, -1) . ';';
		}

		// trim empty lines and trailing characters
		$newOrder = preg_replace('/;$/', '', $newOrder);
		$newOrder = str_replace(';,,;', ';', $newOrder);
		$newOrder = preg_replace('/^,,;/', '', $newOrder);
		$newOrder = preg_replace('/;,,$/', '', $newOrder);

		return($newOrder);
	}
}
