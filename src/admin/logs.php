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
AdminRequirePrivilege('logs');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'logs';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['logs'],
		'relIcon'	=> 'logs.png',
		'link'		=> 'logs.php?',
		'active'	=> $_REQUEST['action'] == 'logs'
	),
	1 => array(
		'title'		=> $lang_admin['archiving'],
		'relIcon'	=> 'archiving.png',
		'link'		=> 'logs.php?action=archiving&',
		'active'	=> $_REQUEST['action'] == 'archiving'
	)
);

/**
 * logs
 */
if($_REQUEST['action'] == 'logs')
{
	$prioImg = array(
		PRIO_DEBUG		=> 'debug',
		PRIO_ERROR		=> 'error',
		PRIO_NOTE		=> 'info',
		PRIO_WARNING	=> 'warning',
		PRIO_PLUGIN		=> 'plugin'
	);
	$start = isset($_REQUEST['startDay']) ? SmartyDateTime('start')
				: (isset($_REQUEST['start']) ? (int)$_REQUEST['start']
					: mktime(0, 0, 0, date('m'), date('d'), date('Y')));
	$end = isset($_REQUEST['endDay']) ? SmartyDateTime('end') + 59
				: (isset($_REQUEST['end']) ? (int)$_REQUEST['end']
					: time());
	$addQ = isset($_REQUEST['q']) && trim($_REQUEST['q']) != ''
		? ' AND eintrag LIKE \'%' . $db->Escape($_REQUEST['q']) . '%\''
		: '';
	$prio = isset($_REQUEST['prio']) && is_array($_REQUEST['prio'])
		? $_REQUEST['prio']
		: array(PRIO_DEBUG => false, PRIO_ERROR => true, PRIO_NOTE => true,
				PRIO_WARNING => true, PRIO_PLUGIN => true);

	/**
	 * export?
	 */
	$exportMode = false;
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'export')
	{
		$exportMode = true;
		header('Pragma: public');
		header('Content-Type: text/plain');
		header(sprintf('Content-Disposition: attachment; filename=b1gMailLog-%d-%d.log',
			$start, $end));

		// header
		echo '#' . "\n";
		echo '# b1gMail ' . B1GMAIL_VERSION . "\n";
		echo '# Log file' . "\n";
		echo '#' . "\n";
		echo '# From: ' . date('r', $start) . "\n";
		echo '# To: ' . date('r', $end) . "\n";
		echo '# Generated: ' . date('r') . "\n";
		echo '#' . "\n";
		echo "\n";
	}

	$entries = array();
	$res = $db->Query('SELECT prio,eintrag,zeitstempel FROM {pre}logs WHERE zeitstempel>='.$start.' AND zeitstempel<='.$end.$addQ.' AND prio IN ? ORDER BY id ASC',
		array_keys($prio));
	while($row = $res->FetchArray())
	{
		if($exportMode)
		{
			printf('%s [%d]: %s' . "\n",
				date('r', $row['zeitstempel']),
				$row['prio'],
				$row['eintrag']);
		}
		else
		{
			$row['prioImg'] = $prioImg[$row['prio']];
			$entries[] = $row;
		}
	}
	$res->Free();

	if($exportMode)
		die();

	$prioQ = '';
	foreach($prio as $key=>$val)
		$prioQ .= '&prio[' . ((int)$key) . ']=true';

	$tpl->assign('prioQ', $prioQ);
	$tpl->assign('prio', $prio);
	$tpl->assign('q', isset($_REQUEST['q']) ? $_REQUEST['q'] : '');
	$tpl->assign('ueQ', isset($_REQUEST['q']) ? urlencode($_REQUEST['q']) : '');
	$tpl->assign('start', $start);
	$tpl->assign('end', $end);
	$tpl->assign('entries', $entries);
	$tpl->assign('page', 'logs.tpl');
}

/**
 * archiving
 */
else if($_REQUEST['action'] == 'archiving')
{
	/**
	 * do it?
	 */
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'archive')
	{
		$date = SmartyDateTime('date');
		$archive = isset($_REQUEST['saveCopy']);

		if(!ArchiveLogs($date, $archive))
		{
			DisplayError(0x15, 'Cannot create log archive file',
				'Failed to create a new log archive file. The archiving procedure has been aborted.',
				'See logs.',
				__FILE__,
				__LINE__);
		}
		else
		{
			PutLog(sprintf('Admin <%s> deleted all log entries before %s (save archive copy: %d)',
				$adminRow['username'],
				date('r', $date),
				$archive ? 1 : 0),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
		}
	}

	$tpl->assign('page', 'logs.archiving.tpl');
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['tools'] . ' &raquo; ' . $lang_admin['logs']);
$tpl->display('page.tpl');
?>