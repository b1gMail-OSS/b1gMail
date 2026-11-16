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
	$groups = BMGroup::GetSimpleGroupList();

	if(isset($_REQUEST['save']) && isset($_POST['types']) && is_array($_POST['types']))
	{
		// reaction to abuse: nothing, lock or move to the target group, never more than one of them.
		// A disabled field is not posted, then the saved value stays.
		$action    = isset($_POST['ap_action']) ? $_POST['ap_action'] : 'none';
		$moveGroup = isset($_POST['ap_automove_group'])
						? (int)$_POST['ap_automove_group']
						: (isset($bm_prefs['ap_automove_group']) ? (int)$bm_prefs['ap_automove_group'] : 0);
		if(!isset($groups[$moveGroup]))
			$moveGroup = 0;
		$autoMove = $action == 'move' && $moveGroup > 0;
		$autoLock = $action == 'lock';
		$notifyTo = isset($_POST['ap_autolock_notify_to']) ? $_POST['ap_autolock_notify_to'] : $bm_prefs['ap_autolock_notify_to'];

		// the option is only offered if a plugin can clear a mail queue, and it is only active for "move".
		// Otherwise the checkbox is disabled and not posted, then the saved value stays.
		$clearQueue = isset($bm_prefs['ap_clearqueue']) ? $bm_prefs['ap_clearqueue'] : 'no';
		if(BMAbuseProtect::queueSupported() && $action == 'move')
			$clearQueue = isset($_POST['ap_clearqueue']) ? 'yes' : 'no';

		$db->Query('UPDATE {pre}prefs SET '
				. '`ap_medium_limit`=?,'
				. '`ap_hard_limit`=?,'
				. '`ap_expire_time`=?,'
				. '`ap_expire_mode`=?,'
				. '`ap_autolock`=?,'
				. '`ap_automove`=?,'
				. '`ap_automove_group`=?,'
				. '`ap_clearqueue`=?,'
				. '`ap_autolock_notify`=?,'
				. '`ap_autolock_notify_to`=?',
			max(1, $_POST['ap_medium_limit']),
			max(1, $_POST['ap_hard_limit']),
			$_POST['ap_expire_time'] * TIME_ONE_HOUR,
			$_POST['ap_expire_mode'],
			$autoLock ? 'yes' : 'no',
			$autoMove ? 'yes' : 'no',
			$moveGroup,
			$clearQueue,
			isset($_POST['ap_autolock_notify']) ? 'yes' : 'no',
			EncodeEMail($notifyTo));
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

	$types = BMAbuseProtect::getTypes();

	foreach($types as $type=>$info)
	{
		$prefs = BMAbuseProtect::getTypePrefs($type);
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

	// hint: a single violation must not reach the lock limit
	$maxPoints = 0;
	foreach($types as $info)
		$maxPoints = max($maxPoints, (int)$info['points']);
	if($bm_prefs['ap_hard_limit'] <= $maxPoints)
		$tpl->assign('apLimitHint', sprintf($lang_admin['ap_limit_hint'], $maxPoints));

	// the saved reaction, for the radio buttons
	if(isset($bm_prefs['ap_automove']) && $bm_prefs['ap_automove'] == 'yes' && BMAbuseProtect::moveGroup() > 0)
		$tpl->assign('apAction', 'move');
	else if($bm_prefs['ap_autolock'] == 'yes')
		$tpl->assign('apAction', 'lock');
	else
		$tpl->assign('apAction', 'none');

	// offered only if a plugin (the mail server) can clear its queue
	$tpl->assign('apQueueSupported', BMAbuseProtect::queueSupported());

	$tpl->assign('apTypes', $types);
	$tpl->assign('apGroups', $groups);
	$tpl->assign('page', 'prefs.abuse.tpl');
}

$tpl->assign('bm_prefs', $bm_prefs);
$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['abuseprotect']);
$tpl->display('page.tpl');
