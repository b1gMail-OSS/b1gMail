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
AdminRequirePrivilege('prefs.abuse');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'prefs';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['abuseprotect'],
		'relIcon'	=> 'abuse32.png',
		'link'		=> 'prefs.abuse.php?',
		'active'	=> $_REQUEST['action'] == 'prefs'
	)
);

/**
 * activity
 */
if($_REQUEST['action'] == 'prefs')
{
	if(isset($_REQUEST['save']) && isset($_POST['types']) && is_array($_POST['types']))
	{
		$db->Query('UPDATE {pre}prefs SET `ap_medium_limit`=?,`ap_hard_limit`=?,`ap_expire_time`=?,`ap_expire_mode`=?,`ap_autolock`=?,`ap_autolock_notify`=?,`ap_autolock_notify_to`=?',
			max(1, $_POST['ap_medium_limit']),
			max(1, $_POST['ap_hard_limit']),
			$_POST['ap_expire_time'] * TIME_ONE_HOUR,
			$_POST['ap_expire_mode'],
			isset($_POST['ap_autolock']) ? 'yes' : 'no',
			isset($_POST['ap_autolock_notify']) ? 'yes' : 'no',
			EncodeEMail($_POST['ap_autolock_notify_to']));
		ReadConfig();

		foreach($_POST['types'] as $type=>$details)
		{
			if(!isset($details['points']))
				continue;

			$prefs = array();
			if(isset($details['prefs']) && is_array($details['prefs']))
			{
				foreach($details['prefs'] as $key=>$val)
					$prefs[] = $key . '=' . str_replace(';', '\\;', str_replace('\\', '\\\\', $val));
			}
			$prefs = implode(';', $prefs);

			$db->Query('REPLACE INTO {pre}abuse_points_config(`type`,`points`,`prefs`) VALUES(?,?,?)',
				$type,
				$details['points'],
				$prefs);
		}
	}

	$types = GetAbuseTypes();

	foreach($types as $type=>$info)
	{
		$prefs = GetAbuseTypePrefs($type);
		foreach($prefs as $key=>$val)
			if(isset($types[$type]['prefs'][$key]))
				$types[$type]['prefs'][$key]['value'] = $val;
		$types[$type]['points'] = $types[$type]['defaultPoints'];
	}

	$res = $db->Query('SELECT `type`,`points`,`prefs` FROM {pre}abuse_points_config');
	while($row = $res->FetchArray(MYSQLI_ASSOC))
	{
		if(!isset($types[$row['type']]))
			continue;

		$types[$row['type']]['points'] = $row['points'];
	}
	$res->Free();

	$tpl->assign('apTypes', $types);
	$tpl->assign('page', 'prefs.abuse.tpl');
}

$tpl->assign('bm_prefs', $bm_prefs);
$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['abuseprotect']);
$tpl->display('page.tpl');
