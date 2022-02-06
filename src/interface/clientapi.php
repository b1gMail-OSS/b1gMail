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

define('INTERFACE_MODE', true);
include('../serverlib/init.inc.php');

$exportedClasses = array(
	'BMToolInterface'	=> array(
		'fileName'				=> '../serverlib/toolinterface.class.php',
		'constructorParams'		=> array(),
		'requiredPrivileges'	=> 0
	)
);

$response = array();

if(!isset($_REQUEST['class'])
	|| !isset($exportedClasses[$_REQUEST['class']]))
{
	$response['status'] = 'INVALID_CLASS';
}
else
{
	$classInfo = $exportedClasses[$_REQUEST['class']];

	// load class
	if(!class_exists($_REQUEST['class']))
		include($classInfo['fileName']);

	// check privileges
	if(!RequestPrivileges($classInfo['requiredPrivileges'] | PRIVILEGES_CLIENTAPI, true))
	{
		// return access error
		$response['status'] = 'ACCESS_DENIED';
	}
	else
	{
		// instantiate
		$response['class'] = $_REQUEST['class'];
		if(isset($_REQUEST['method']))
			$response['method'] = $_REQUEST['method'];
		$instance = _new($_REQUEST['class'], $classInfo['constructorParams']);

		$params = isset($_REQUEST['params']) && is_array($_REQUEST['params']) ? $_REQUEST['params'] : array();

		// function
		if(!isset($_REQUEST['method']) || !method_exists($instance, $_REQUEST['method']))
		{
			$response['status'] = 'INVALID_METHOD';
			if(method_exists($instance, 'HandleNonexistentMethod'))
				call_user_func_array(array(&$instance, 'HandleNonexistentMethod'), array($_REQUEST['method'], $params, &$response));
		}
		else if(isset($_REQUEST['method']) && method_exists($instance, $_REQUEST['method']))
		{
			$response['status'] = 'OK';
			$response['result'] = call_user_func_array(array(&$instance, $_REQUEST['method']), $params);
		}
	}
}

NormalArray2XML($response, 'response');
