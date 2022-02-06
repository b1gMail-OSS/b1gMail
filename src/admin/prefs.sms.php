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
AdminRequirePrivilege('prefs.sms');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'common';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['common'],
		'relIcon'	=> 'ico_prefs_common.png',
		'link'		=> 'prefs.sms.php?',
		'active'	=> $_REQUEST['action'] == 'common'
	),
	1 => array(
		'title'		=> $lang_admin['gateways'],
		'relIcon'	=> 'gateway32.png',
		'link'		=> 'prefs.sms.php?action=gateways&',
		'active'	=> $_REQUEST['action'] == 'gateways'
	),
	2 => array(
		'title'		=> $lang_admin['types'],
		'relIcon'	=> 'type32.png',
		'link'		=> 'prefs.sms.php?action=types&',
		'active'	=> $_REQUEST['action'] == 'types'
	)
);

/**
 * common
 */
if($_REQUEST['action'] == 'common')
{
	// save?
	if(isset($_REQUEST['save']))
	{
		$db->Query('UPDATE {pre}prefs SET sms_gateway=?, clndr_sms_abs=?, mail2sms_abs=?, smsreply_abs=?, charge_min_amount=?, clndr_sms_type=?, mail2sms_type=?, smsvalidation_type=?, sms_enable_charge=?',
			$_REQUEST['sms_gateway'],
			$_REQUEST['clndr_sms_abs'],
			$_REQUEST['mail2sms_abs'],
			$_REQUEST['smsreply_abs'],
			(double)str_replace(',', '.', $_REQUEST['charge_min_amount']) * 100,
			$_REQUEST['clndr_sms_type'],
			$_REQUEST['mail2sms_type'],
			$_REQUEST['smsvalidation_type'],
			isset($_REQUEST['sms_enable_charge']) ? 'yes' : 'no');
		ReadConfig();
	}

	// fetch gateways
	$gateways = array();
	$res = $db->Query('SELECT id,titel FROM {pre}smsgateways ORDER BY titel ASC');
	while($row = $res->FetchArray(MYSQLI_ASSOC))
		$gateways[$row['id']] = $row;
	$res->Free();

	// fetch types
	$types = array();
	$res = $db->Query('SELECT id,titel FROM {pre}smstypen ORDER BY titel ASC');
	while($row = $res->FetchArray(MYSQLI_ASSOC))
		$types[$row['id']] = $row;
	$res->Free();

	// prepare values
	$bm_prefs['charge_min_amount'] = sprintf('%.02f', $bm_prefs['charge_min_amount']/100);

	// assign
	$tpl->assign('prjPass', substr(GenerateRandomKey('sofortueberweisungProjectPassword'), 0, 16));
	$tpl->assign('gateways', $gateways);
	$tpl->assign('types', $types);
	$tpl->assign('page', 'prefs.sms.common.tpl');
}

/**
 * gateways
 */
else if($_REQUEST['action'] == 'gateways')
{
	if(!isset($_REQUEST['do']))
		$_REQUEST['do'] = 'list';

	//
	// list
	//
	if($_REQUEST['do'] == 'list')
	{
		// delete?
		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}smsgateways WHERE id=?',
				$_REQUEST['delete']);
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get gateway IDs
			$gatewayIDs = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 8) == 'gateway_')
					$gatewayIDs[] = (int)substr($key, 8);

			if(count($gatewayIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					// delete row
					$db->Query('DELETE FROM {pre}smsgateways WHERE id IN(' . implode(',', $gatewayIDs) . ')');
				}
			}
		}

		// add?
		if(isset($_REQUEST['add']))
		{
			$db->Query('INSERT INTO {pre}smsgateways(titel,getstring,success,`user`,`pass`) VALUES(?,?,?,?,?)',
				$_REQUEST['titel'],
				$_REQUEST['getstring'],
				$_REQUEST['success'],
				$_REQUEST['user'],
				$_REQUEST['pass']);
		}

		// fetch
		$gateways = array();
		$res = $db->Query('SELECT id,titel FROM {pre}smsgateways ORDER BY titel ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$row['default'] = $bm_prefs['sms_gateway'] == $row['id'];
			$gateways[$row['id']] = $row;
		}
		$res->Free();

		// assign
		$tpl->assign('gateways', $gateways);
		$tpl->assign('lang', $currentLanguage);
		$tpl->assign('page', 'prefs.sms.gateways.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit'
			&& isset($_REQUEST['id']))
	{
		// save?
		if(isset($_REQUEST['save']))
		{
			$db->Query('UPDATE {pre}smsgateways SET titel=?, getstring=?, success=?, `user`=?, `pass`=? WHERE id=?',
				$_REQUEST['titel'],
				$_REQUEST['getstring'],
				$_REQUEST['success'],
				$_REQUEST['user'],
				$_REQUEST['pass'],
				$_REQUEST['id']);
			header('Location: prefs.sms.php?action=gateways&sid=' . session_id());
			exit();
		}

		// fetch
		$res = $db->Query('SELECT id,titel,getstring,success,`user`,`pass` FROM {pre}smsgateways WHERE id=?',
			$_REQUEST['id']);
		$gateway = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// assign
		$tpl->assign('gateway', $gateway);
		$tpl->assign('page', 'prefs.sms.gateways.edit.tpl');
	}
}

/**
 * types
 */
else if($_REQUEST['action'] == 'types')
{
	if(!isset($_REQUEST['do']))
		$_REQUEST['do'] = 'list';

	// fetch gateways
	$gateways = array();
	$res = $db->Query('SELECT id,titel FROM {pre}smsgateways ORDER BY titel ASC');
	while($row = $res->FetchArray(MYSQLI_ASSOC))
		$gateways[$row['id']] = $row;
	$res->Free();

	//
	// list
	//
	if($_REQUEST['do'] == 'list')
	{
		// delete?
		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}smstypen WHERE id=?',
				$_REQUEST['delete']);
		}

		// set default
		if(isset($_REQUEST['setDefault']))
		{
			$db->Query('UPDATE {pre}smstypen SET std=(id=?)',
				(int)$_REQUEST['setDefault']);
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get type IDs
			$typeIDs = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 5) == 'type_')
					$typeIDs[] = (int)substr($key, 5);

			if(count($typeIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					// delete row
					$db->Query('DELETE FROM {pre}smstypen WHERE id IN(' . implode(',', $typeIDs) . ')');
				}
			}
		}

		// add?
		if(isset($_REQUEST['add']))
		{
			$res = $db->Query('SELECT COUNT(*) FROM {pre}smstypen WHERE std=1');
			list($stdCount) = $res->FetchArray();
			$res->Free();

			$db->Query('INSERT INTO {pre}smstypen(titel,typ,price,gateway,std,flags,maxlength) VALUES(?,?,?,?,?,?,?)',
				$_REQUEST['titel'],
				$_REQUEST['typ'],
				$_REQUEST['price'],
				$_REQUEST['gateway'],
				$stdCount == 0 ? 1 : 0,
				isset($_REQUEST['flags']) ? array_sum(array_keys($_REQUEST['flags'])) : 0,
				(int)$_REQUEST['maxlength']);
		}

		// fetch
		$types = array();
		$res = $db->Query('SELECT id,titel,typ,std,price,maxlength FROM {pre}smstypen ORDER BY titel ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$types[$row['id']] = $row;
		}
		$res->Free();

		// assign
		$tpl->assign('types', $types);
		$tpl->assign('gateways', $gateways);
		$tpl->assign('page', 'prefs.sms.types.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit'
			&& isset($_REQUEST['id']))
	{
		// save?
		if(isset($_REQUEST['save']))
		{
			$db->Query('UPDATE {pre}smstypen SET titel=?, typ=?, price=?, gateway=?, flags=?, maxlength=? WHERE id=?',
				$_REQUEST['titel'],
				$_REQUEST['typ'],
				$_REQUEST['price'],
				$_REQUEST['gateway'],
				isset($_REQUEST['flags']) ? array_sum(array_keys($_REQUEST['flags'])) : 0,
				(int)$_REQUEST['maxlength'],
				$_REQUEST['id']);
			header('Location: prefs.sms.php?action=types&sid=' . session_id());
			exit();
		}

		// fetch
		$res = $db->Query('SELECT id,titel,typ,std,price,gateway,flags,maxlength FROM {pre}smstypen WHERE id=?',
			$_REQUEST['id']);
		$type = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// assign
		$tpl->assign('type', 		$type);
		$tpl->assign('gateways', 	$gateways);
		$tpl->assign('page', 		'prefs.sms.types.edit.tpl');
	}
}

$tpl->assign('bm_prefs', $bm_prefs);
$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['sms']);
$tpl->display('page.tpl');
?>
