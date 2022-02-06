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
if(!class_exists('BMMailBuilder'))
	include('../serverlib/mailbuilder.class.php');
RequestPrivileges(PRIVILEGES_ADMIN);
AdminRequirePrivilege('newsletter');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'newsletter';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['newsletter'],
		'relIcon'	=> 'newsletter.png',
		'link'		=> 'newsletter.php?',
		'active'	=> $_REQUEST['action'] == 'newsletter'
	),
	1 => array(
		'title'		=> $lang_admin['templates'],
		'relIcon'	=> 'template32.png',
		'link'		=> 'newsletter.php?action=templates&',
		'active'	=> $_REQUEST['action'] == 'templates'
	)
);

if($_REQUEST['action'] == 'newsletter')
{
	//
	// form
	//
	if(!isset($_REQUEST['do']))
	{
		// templates
		$templates = array();
		$res = $db->Query('SELECT `templateid`,`title` FROM {pre}newsletter_templates ORDER BY `title` ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$templates[$row['templateid']] = $row['title'];
		}
		$res->Free();

		// countries
		$allCountries = CountryList();
		$countries = array($bm_prefs['std_land'] => $allCountries[$bm_prefs['std_land']]);
		$res = $db->Query('SELECT DISTINCT(`land`) FROM {pre}users');
		while($row = $res->FetchArray(MYSQLI_NUM))
			if(isset($allCountries[$row[0]]))
				$countries[$row[0]] = $allCountries[$row[0]];
			else
				$countries[145] = $allCountries[145];
		$res->Free();
		asort($countries);

		// assign
		$tpl->assign('from', sprintf('"%s - %s" <%s>',
			$bm_prefs['titel'],
			$lang_admin['team'],
			GetPostmasterMail()));
		$tpl->assign('countries', $countries);
		$tpl->assign('templates', $templates);
		$tpl->assign('groups', BMGroup::GetSimpleGroupList());
		$tpl->assign('usertpldir', B1GMAIL_REL . 'templates/' . $bm_prefs['template'] . '/');
		$tpl->assign('page', 'newsletter.tpl');
	}

	//
	// get TPL data
	//
	else if($_REQUEST['do'] == 'getTemplateData' && isset($_REQUEST['templateID']))
	{
		$res = $db->Query('SELECT * FROM {pre}newsletter_templates WHERE `templateid`=?',
			(int)$_REQUEST['templateID']);
		if($res->RowCount() != 1)
			die('Template not found');
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		header('Content-Type: application/json; charset="' . $currentCharset . '"');
		echo '{ ' . "\n";

		$i = 0;
		foreach($row as $key=>$val)
		{
			printf("\t\"%s\" : \"%s\"%s\n", addslashes($key), str_replace(array("\r\n", "\n"), '\n', addslashes($val)),
				++$i == count($row) ? ' ' : ',');
		}

		echo '}' . "\n";
		exit;
	}

	//
	// determine recipient count
	//
	else if($_REQUEST['do'] == 'determineRecipients')
	{
		$groups = preg_replace('/[^0-9,]/', '', $_REQUEST['groups']);
		$sendto = $_REQUEST['sendto'];

		// status?
		$lockedValues 			= array();
		$statusActive 			= isset($_REQUEST['statusActive']);
		$statusLocked 			= isset($_REQUEST['statusLocked']);
		$statusNotActivated 	= isset($_REQUEST['statusNotActivated']);
		$statusDeleted 			= isset($_REQUEST['statusDeleted']);
		if($statusActive) 		$lockedValues[] = '\'no\'';
		if($statusLocked) 		$lockedValues[] = '\'yes\'';
		if($statusNotActivated) $lockedValues[] = '\'locked\'';
		$countries				= $_REQUEST['countries'];
		if(!is_array($countries))
			$countries = array();
		if(in_array(145, $countries))
			$countries[] = 0;
		foreach($countries as $key=>$val)
			$countries[$key] = (int)$val;
		if(count($countries) == 0)
			$countries[] = -1;

		// no groups or locked values?
		if(trim($groups) == '' || count($lockedValues) == 0)
			die('0');

		// determine count
		$res = $db->Query('SELECT COUNT(*) FROM {pre}users,{pre}gruppen WHERE {pre}gruppen.id={pre}users.gruppe AND {pre}users.land IN ? AND {pre}users.gesperrt IN(' . implode(',', $lockedValues) . ') AND {pre}users.gruppe IN(' . $groups . ') AND ({pre}gruppen.allow_newsletter_optout=\'no\' OR {pre}users.newsletter_optin=\'yes\')'
					. ($sendto == 'altmails' ? ' AND LENGTH({pre}users.altmail)>3' : ''),
					$countries);
		list($recpCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		die($recpCount);
	}

	//
	// send initiator
	//
	else if($_REQUEST['do'] == 'send')
	{
		// groups?
		$groups = array();
		foreach($_POST as $key=>$val)
			if(substr($key, 0, 6) == 'group_')
				$groups[] = (int)substr($key, 6);

		// sendto?
		$sendto = $_REQUEST['sendto'];

		// status?
		$lockedValues 			= array();
		$statusActive 			= isset($_REQUEST['statusActive']);
		$statusLocked 			= isset($_REQUEST['statusLocked']);
		$statusNotActivated 	= isset($_REQUEST['statusNotActivated']);
		$statusDeleted 			= isset($_REQUEST['statusDeleted']);
		if($statusActive) 		$lockedValues[] = '\'no\'';
		if($statusLocked) 		$lockedValues[] = '\'yes\'';
		if($statusNotActivated) $lockedValues[] = '\'locked\'';
		$countries				= $_REQUEST['countries'];
		if(!is_array($countries))
			$countries = array();
		if(in_array(145, $countries))
			$countries[] = 0;
		foreach($countries as $key=>$val)
			$countries[$key] = (int)$val;
		if(count($countries) == 0)
			$countries[] = -1;

		// no groups or locked values?
		if(count($groups) == 0 || count($lockedValues) == 0)
			die('No recipients');

		// build condition
		$condition = '{pre}gruppen.id={pre}users.gruppe AND {pre}users.land IN (' . implode(',', $countries) . ') AND {pre}users.gesperrt IN(' . implode(',', $lockedValues) . ') AND {pre}users.gruppe IN(' . implode(',', $groups) . ')'
			. ($sendto == 'altmails' ? ' AND LENGTH({pre}users.altmail)>3' : '');
		$condition .= ' AND ({pre}gruppen.allow_newsletter_optout=\'no\' OR {pre}users.newsletter_optin=\'yes\')';

		// determine count
		$res = $db->Query('SELECT COUNT(*) FROM {pre}users,{pre}gruppen WHERE ' . $condition);
		list($recpCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		if($recpCount == 0)
			die('No recipients');

		// just export?
		if(isset($_REQUEST['exportRecipients']))
		{
			// headers
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="newsletter-recipients.csv"');
			header('Content-Type: text/csv');

			// send recipients
			$res = $db->Query('SELECT {pre}users.id AS UserID,{pre}users.email AS Email,{pre}users.altmail AS AltEmail,{pre}users.vorname AS Firstname,{pre}users.nachname AS Lastname,{pre}users.anrede AS Salutation FROM {pre}users,{pre}gruppen WHERE '
							. $condition . ' ORDER BY {pre}users.id ASC');
			$count = $res->RowCount();
			if($count > 0)
			{
				PutLog(sprintf('Admin <%s> exports %d newsletter recipients as CSV file', $adminRow['username'], $count),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
			}
			$res->ExportCSV();
			$res->Free();

			exit;
		}

		// put newsletter info to array
		$newsletter = array(
			'from'			=> $_REQUEST['from'],
			'subject'		=> $_REQUEST['subject'],
			'priority'		=> $_REQUEST['priority'],
			'text'			=> $_REQUEST['emailText'],
			'textMode'		=> $_REQUEST['mode'],
			'sendto'		=> $sendto,
			'condition'		=> $condition,
			'recpCount'		=> $recpCount,
			'success'		=> 0,
			'failed'		=> 0
		);

		// save array to session
		if(!isset($_SESSION['newsletters']))
			$_SESSION['newsletters'] = array();
		$newsletterID = GenerateRandomKey('newsletter');
		$_SESSION['newsletters'][$newsletterID] = $newsletter;

		// log
		PutLog(sprintf('Admin <%s> sends newsletter to %d recipients', $adminRow['username'], $recpCount),
			PRIO_NOTE,
			__FILE__,
			__LINE__);

		// assign
		$tpl->assign('id', $newsletterID);
		$tpl->assign('recpCount', $recpCount);
		$tpl->assign('perPage', max(1, (int)$_REQUEST['perpage']));
		$tpl->assign('page', 'newsletter.send.tpl');
	}

	//
	// send step
	//
	else if($_REQUEST['do'] == 'sendStep'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['perpage'])
		&& isset($_REQUEST['pos']))
	{
		// check input
		$id = $_REQUEST['id'];
		if(!isset($_SESSION['newsletters']) || !isset($_SESSION['newsletters'][$id]))
			die('DONE');

		// get newsletter data
		$newsletter = $_SESSION['newsletters'][$id];

		// position
		$pos = (int)$_REQUEST['pos'];

		// get count
		$res = $db->Query('SELECT COUNT(*) FROM {pre}users,{pre}gruppen WHERE ' . $newsletter['condition']);
		list($count) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		if($pos >= $count)
		{
			die('DONE');
		}
		else
		{
			// select recipients
			$res = $db->Query('SELECT {pre}users.id,{pre}users.email,{pre}users.altmail,{pre}users.vorname,{pre}users.nachname,{pre}users.anrede FROM {pre}users,{pre}gruppen WHERE '
				. $newsletter['condition'] . ' ORDER BY {pre}users.id ASC LIMIT ' . $pos . ',' . max(1, (int)$_REQUEST['perpage']));
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$to = $newsletter['sendto'] == 'altmails' ? $row['altmail'] : $row['email'];

				if($row['anrede'] == 'herr')
				{
					$salutation = $lang_admin['mr'];
					$greeting = sprintf($lang_admin['greeting_mr'], $row['nachname']);
				}
				else if($row['anrede'] == 'frau')
				{
					$salutation = $lang_admin['mrs'];
					$greeting = sprintf($lang_admin['greeting_mrs'], $row['nachname']);
				}
				else
				{
					$salutation = '';
					$greeting = $lang_admin['greeting_none'];
				}

				// create text
				$text = $newsletter['text'];
				$text = str_replace('%%email%%', 		DecodeEMail($row['email']),	$text);
				$text = str_replace('%%greeting%%',     $greeting, 					$text);
				$text = str_replace('%%salutation%%',   $salutation, 				$text);
				$text = str_replace('%%firstname%%', 	$row['vorname'], 			$text);
				$text = str_replace('%%lastname%%', 	$row['nachname'], 			$text);

				// create mail
				$mail = _new('BMMailBuilder', array(true));
				$mail->SetUserID(USERID_ADMIN);
				$mail->AddHeaderField('From',		$newsletter['from']);
				$mail->AddHeaderField('To',			$to);
				$mail->AddHeaderField('Subject',	$newsletter['subject']);

				// priority
				if($newsletter['priority'] != 0)
				{
					$mail->AddHeaderField('X-Priority', $newsletter['priority'] == ITEMPRIO_HIGH
						? 1
						: ($newsletter['priority'] == ITEMPRIO_LOW
							? 5
							: 3));
				}

				// text
				$mail->AddText($text,
					$newsletter['textMode'] == 'html' ? 'html' : 'plain',
					$currentCharset);

				// send
				$result = $mail->Send() !== false;
				$mail->CleanUp();

				// stats
				if($result)
					$_SESSION['newsletters'][$id]['success']++;
				else
					$_SESSION['newsletters'][$id]['failed']++;

				$pos++;
			}
			$res->Free();

			if($pos >= $count)
				die('DONE');
			else
				die($pos . '/' . $count);
		}
	}

	//
	// done
	//
	else if($_REQUEST['do'] == 'done'
		&& isset($_REQUEST['id'])
		&& isset($_SESSION['newsletters'])
		&& isset($_SESSION['newsletters'][$_REQUEST['id']]))
	{
		// remove entry from session
		$newsletter = $_SESSION['newsletters'][$_REQUEST['id']];
		unset($_SESSION['newsletters'][$_REQUEST['id']]);

		// assign
		$tpl->assign('msgTitle', $lang_admin['newsletter']);
		$tpl->assign('msgText', sprintf($lang_admin['newsletter_done'], $newsletter['success'], $newsletter['failed']));
		$tpl->assign('msgIcon', 'info32');
		$tpl->assign('backLink', 'newsletter.php?');
		$tpl->assign('page', 'msg.tpl');
	}
}

else if($_REQUEST['action'] == 'templates')
{
	$tpl->assign('usertpldir', B1GMAIL_REL . 'templates/' . $bm_prefs['template'] . '/');

	//
	// list
	//
	if(!isset($_REQUEST['do']))
	{
		// delete?
		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}newsletter_templates WHERE `templateid`=?',
				(int)$_REQUEST['delete']);
		}

		// add?
		if(isset($_REQUEST['add']))
		{
			$db->Query('INSERT INTO {pre}newsletter_templates(`title`,`subject`,`from`,`mode`,`priority`,`body`) VALUES(?,?,?,?,?,?)',
				$_REQUEST['title'],
				$_REQUEST['subject'],
				$_REQUEST['from'],
				$_REQUEST['mode'],
				$_REQUEST['priority'],
				$_REQUEST['emailText']);
		}

		// templates
		$templates = array();
		$res = $db->Query('SELECT `templateid`,`title`,`subject` FROM {pre}newsletter_templates ORDER BY `title` ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$templates[$row['templateid']] = $row;
		}
		$res->Free();

		// show page
		$tpl->assign('from', sprintf('"%s - %s" <%s>',
			$bm_prefs['titel'],
			$lang_admin['team'],
			GetPostmasterMail()));
		$tpl->assign('templates', $templates);
		$tpl->assign('page', 'newsletter.templates.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit' && isset($_REQUEST['templateID']))
	{
		if(isset($_REQUEST['save']) && isset($_POST['priority']))
		{
			$db->Query('UPDATE {pre}newsletter_templates SET `title`=?,`mode`=?,`from`=?,`subject`=?,`priority`=?,`body`=? WHERE `templateid`=?',
				$_REQUEST['title'],
				$_REQUEST['mode'],
				$_REQUEST['from'],
				$_REQUEST['subject'],
				$_REQUEST['priority'],
				$_REQUEST['emailText'],
				(int)$_REQUEST['templateID']
			);
			header('Location: newsletter.php?action=templates&sid=' . session_id());
			exit;
		}

		$res = $db->Query('SELECT * FROM {pre}newsletter_templates WHERE `templateid`=?',
			(int)$_REQUEST['templateID']);
		if($res->RowCount() != 1)
			die('Template not found');
		$template = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$tpl->assign('tpl', $template);
		$tpl->assign('page', 'newsletter.templates.edit.tpl');
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['usersgroups'] . ' &raquo; ' . $lang_admin['newsletter']);
$tpl->display('page.tpl');
