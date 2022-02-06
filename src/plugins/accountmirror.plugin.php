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

/**
 * account mirror plugin
 *
 */
class AccountMirror extends BMPlugin
{
	function __construct()
	{
		$this->name					= 'Account mirror';
		$this->author				= 'b1gMail Project';
		$this->web					= 'https://www.b1gmail.org/';
		$this->mail					= 'info@b1gmail.org';
		$this->version				= '1.4';
		$this->designedfor			= '7.2.0';
		$this->type					= BMPLUGIN_DEFAULT;
		$this->update_url			= 'https://service.b1gmail.org/plugin_updates/';
		$this->website				= 'https://www.b1gmail.org/';

		$this->admin_pages			= true;
		$this->admin_page_title		= 'Account Mirror';
		$this->admin_page_icon		= 'accountmirror_logo.png';
	}

	function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
	{
		if($lang == 'deutsch')
		{
			$lang_admin['am_notice1']			= 'Die Verwendung dieses Plugins ist nur unter strenger Einhaltung der entsprechenden Gesetze, insbesondere der Datenschutzbestimmungen, gestattet.';
			$lang_admin['am_notice2']			= 'Es wird keinerlei Funktionsgarantie oder Garantie f&uuml;r die Eignung zu einem bestimmten Zweck &uuml;bernommen. Spiegelungen k&ouml;nnen unter Umst&auml;nden z.B. unvollst&auml;ndig oder fehlerhaft sein.';
			$lang_admin['am_notice3']			= 'Die Spiegelung erfolgt nur f&uuml;r das Ereignis &quot;E-Mail im Account gespeichert&quot;, z.B. bei Empfang einer E-Mail. Andere Ereignisse, z.B. Markierung, Verschiebung, L&ouml;schung von E-Mails werden nicht gespiegelt. Es erfolgt keine Spiegelung/Ber&uuml;cksichtigung der Ordnerstruktur (mit Ausnahme von System-Ordnern). Andere Daten, z.B. Webdisk oder Adressbuch/Kalender, werden nicht gespiegelt. Die Spiegelung erfolgt nur f&uuml;r E-Mails, die im Zeitraum der Spiegelma&szlig;nahme gespeichert werden.';
			$lang_admin['am_notice4']			= 'Der Spiegel-Ziel-Account sollte <em>mindestens</em> doppelt so viel Speicher zur Verf&uuml;gung haben wie der Quell-Account.';
			$lang_admin['am_notice5']			= 'Im Spiegel-Ziel-Account sollten keine Filterregeln, Autoresponder oder andere Benachrichtigungsfunktionen oder Funktionen, die auf den Empfang einer E-Mail reagieren, aktiviert sein.';
			$lang_admin['am_mirrorings']		= 'Spiegelungen';
			$lang_admin['am_source']			= 'Quelle';
			$lang_admin['am_dest']				= 'Ziel';
			$lang_admin['am_timeframe']			= 'Zeitraum';
			$lang_admin['am_errors']			= 'Fehler';
			$lang_admin['am_add']				= 'Spiegelung hinzuf&uuml;gen';
			$lang_admin['am_accemail']			= 'Account-Haupt-E-Mail-Adresse';
			$lang_admin['am_from']				= 'ab';
			$lang_admin['am_to']				= 'bis';
			$lang_admin['am_error_0']			= 'Der Quelle-/Ziel-Account wurde nicht gefunden. Bitte geben Sie die prim&auml;re E-Mail-Adresse des jeweiligen Accounts korrekt an.';
			$lang_admin['am_error_1']			= 'Ein Account kann nicht in sich selbst gespiegelt werden.';
			$lang_admin['am_error_2']			= 'Der Ziel-Account darf nicht die Quelle einer anderen Spiegelma&szlig;nahme sein.';
			$lang_admin['am_error_3']			= 'Der Endzeitpunkt darf nicht vor dem Anfangszeitpunkt liegen.';
		}
		else
		{
			$lang_admin['am_notice1']			= 'This plugin may only be used in compliance with privacy laws.';
			$lang_admin['am_notice2']			= 'This plugin comes without any warranties. Mirrorings may contain errors or be incomplete.';
			$lang_admin['am_notice3']			= 'Only events of type &quot;Email stored&quot; will be mirrored. The folder structure will not be preserved (with exception of system folders). Only mails saved in the specified timeframe will be mirrored.';
			$lang_admin['am_notice4']			= 'The target account should have <em>at least</em> twice the free storage of the source account.';
			$lang_admin['am_notice5']			= 'There should be no filter rules or autoresponders activated in the target account.';
			$lang_admin['am_mirrorings']		= 'Mirrorings';
			$lang_admin['am_source']			= 'Source';
			$lang_admin['am_dest']				= 'Target';
			$lang_admin['am_timeframe']			= 'Timeframe';
			$lang_admin['am_errors']			= 'Errors';
			$lang_admin['am_add']				= 'Add mirroring';
			$lang_admin['am_accemail']			= 'primary account email address';
			$lang_admin['am_from']				= 'from';
			$lang_admin['am_to']				= 'to';
			$lang_admin['am_error_0']			= 'The source/target account could not be found. Please ensure you entered the correct primary email addresses of the accounts.';
			$lang_admin['am_error_1']			= 'You cannot mirror an account to itself.';
			$lang_admin['am_error_2']			= 'The target account may not be the source account of another mirroring.';
			$lang_admin['am_error_3']			= 'The end time may not be before the start time.';
		}
	}

	function Install()
	{
		global $db;

		// db struct
		$databaseStructure =                      // checksum: da0bba6e13cd8b6e5adcd3e795c8d844
			  'YToxOntzOjIyOiJibTYwX21vZF9hY2NvdW50bWlycm9yIjthOjI6e3M6NjoiZmllbGRzIjthOjc'
			. '6e2k6MDthOjY6e2k6MDtzOjg6Im1pcnJvcmlkIjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOi'
			. 'JOTyI7aTozO3M6MzoiUFJJIjtpOjQ7TjtpOjU7czoxNDoiYXV0b19pbmNyZW1lbnQiO31pOjE7Y'
			. 'To2OntpOjA7czo2OiJ1c2VyaWQiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7'
			. 'czozOiJNVUwiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6MjthOjY6e2k6MDtzOjk6Im1pcnJ'
			. 'vcl90byI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MT'
			. 'oiMCI7aTo1O3M6MDoiIjt9aTozO2E6Njp7aTowO3M6NToiYmVnaW4iO2k6MTtzOjc6ImludCgxN'
			. 'CkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NDth'
			. 'OjY6e2k6MDtzOjM6ImVuZCI7aToxO3M6NzoiaW50KDE0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA'
			. '6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo1O2E6Njp7aTowO3M6MTA6Im1haWxfY291bn'
			. 'QiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO'
			. '2k6NTtzOjA6IiI7fWk6NjthOjY6e2k6MDtzOjExOiJlcnJvcl9jb3VudCI7aToxO3M6NzoiaW50'
			. 'KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9fXM'
			. '6NzoiaW5kZXhlcyI7YToyOntzOjc6IlBSSU1BUlkiO2E6MTp7aTowO3M6ODoibWlycm9yaWQiO3'
			. '1zOjY6InVzZXJpZCI7YToxOntpOjA7czo2OiJ1c2VyaWQiO319fX0=';
		$databaseStructure = unserialize(base64_decode($databaseStructure));

		// sync struct
		SyncDBStruct($databaseStructure);

		// log
		PutLog(sprintf('%s v%s installed',
			$this->name,
			$this->version),
			PRIO_PLUGIN,
			__FILE__,
			__LINE__);

		return(true);
	}


	function AdminHandler()
	{
		global $tpl, $db, $lang_admin;

		$tabs = array(
			0 => array(
				'title'		=> 'Account Mirror',
				'icon'		=> '../plugins/templates/images/accountmirror_logo.png',
				'link'		=> $this->_adminLink() . '&',
				'active'	=> true
			)
		);

		$tpl->assign('pageURL', 	$this->_adminLink());
		$tpl->assign('tabs', 		$tabs);

		if(isset($_REQUEST['add']))
		{
			$userID = BMUser::GetID($_POST['email_source']);
			$mirrorTo = BMUser::GetID($_POST['email_dest']);

			if($userID == 0 || $mirrorTo == 0)
			{
				$tpl->assign('msgText',		$lang_admin['am_error_0']);
				$tpl->assign('msgTitle',	$lang_admin['error']);
				$tpl->assign('msgIcon',		'error32');
				$tpl->assign('page', 		'msg.tpl');
				return;
			}

			if($userID == $mirrorTo)
			{
				$tpl->assign('msgText',		$lang_admin['am_error_1']);
				$tpl->assign('msgTitle',	$lang_admin['error']);
				$tpl->assign('msgIcon',		'error32');
				$tpl->assign('page', 		'msg.tpl');
				return;
			}

			$res = $db->Query('SELECT COUNT(*) FROM {pre}mod_accountmirror WHERE `userid`=?',
				$mirrorTo);
			list($count) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			if($count != 0)
			{
				$tpl->assign('msgText',		$lang_admin['am_error_2']);
				$tpl->assign('msgTitle',	$lang_admin['error']);
				$tpl->assign('msgIcon',		'error32');
				$tpl->assign('page', 		'msg.tpl');
				return;
			}

			$begin = isset($_REQUEST['von_unlim']) ? 0 : SmartyDateTime('von');
			$end = isset($_REQUEST['bis_unlim']) ? 0 : SmartyDateTime('bis');

			if($end != 0 && $begin != 0 && $end < $begin)
			{
				$tpl->assign('msgText',		$lang_admin['am_error_3']);
				$tpl->assign('msgTitle',	$lang_admin['error']);
				$tpl->assign('msgIcon',		'error32');
				$tpl->assign('page', 		'msg.tpl');
				return;
			}
		}

		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}mod_accountmirror WHERE `mirrorid`=?',
				$_REQUEST['delete']);
		}

		if(isset($_REQUEST['executeMassAction']))
		{
			$mirrorIDs = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 10) == 'mirroring_')
					$mirrorIDs[] = (int)substr($key, 10);

			if(count($mirrorIDs) > 0)
				if($_REQUEST['massAction'] == 'delete')
					$db->Query('DELETE FROM {pre}mod_accountmirror WHERE `mirrorid` IN(' . implode(',', $mirrorIDs) . ')');
		}

		$mirrorings = array();
		$res = $db->Query('SELECT * FROM {pre}mod_accountmirror ORDER BY `mirrorid` ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$source = _new('BMUser', array($row['userid']));
			$dest = _new('BMUser', array($row['mirror_to']));

			$row['source'] = $source->_row['email'];
			$row['dest'] = $dest->_row['email'];

			$mirrorings[$row['mirrorid']] = $row;
		}
		$res->Free();

		$tpl->assign('mirrorings', $mirrorings);
		$tpl->assign('page', $this->_templatePath('accountmirror.main.tpl'));
	}

	function AfterStoreMail($mailID, &$mail, &$mailbox)
	{
		global $db;

		if(!is_object($mailbox) || !is_object($mail))
			return;

		$res = $db->Query('SELECT * FROM {pre}mod_accountmirror WHERE `userid`=?',
			$mailbox->_userID);
		if($res->RowCount() < 1)
			return;

		$mail2 = $mailbox->GetMail($mailID);
		if($mail2 === false || !is_object($mail2))
		{
			$res->Free();

			PutLog(sprintf('Account mirror plugin: Could not retrieve mail #%d from mailbox',
					$mailID),
				PRIO_WARNING,
				__FILE__,
				__LINE__);

			return;
		}

		$folderID = $mail2->_row['folder'];

		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($row['begin'] > time() || ($row['end'] < time() && $row['end'] > 0))
				continue;

			// create target user object
			$userObject = _new('BMUser', array($row['mirror_to']));
			$userRow = $userObject->Fetch();
			$userMail = $userRow['email'];

			// open target user's mailbox
			$targetMailbox = _new('BMMailbox', array($row['mirror_to'], $userMail, $userObject));

			// store the mail
			$recipientResult = $targetMailbox->StoreMail($mail,
				$folderID <= 0 ? $folderID : FOLDER_INBOX);

			// check result
			if($recipientResult != STORE_RESULT_OK)
			{
				PutLog(sprintf('Account mirror plugin: Mirroring of mail to user #%d to target mailbox #%d failed (%d)',
					$mailbox->_userID,
					$row['mirror_to'],
					$recipientResult),
					PRIO_WARNING,
					__FILE__,
					__LINE__);
				$db->Query('UPDATE {pre}mod_accountmirror SET `error_count`=`error_count`+1 WHERE `mirrorid`=?',
					$row['mirrorid']);
			}
			else
			{
				PutLog(sprintf('Account mirror plugin: Mirrored mail to user #%d to target mailbox #%d',
					$mailbox->_userID,
					$row['mirror_to']),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
				$db->Query('UPDATE {pre}mod_accountmirror SET `mail_count`=`mail_count`+1 WHERE `mirrorid`=?',
					$row['mirrorid']);
			}
		}
		$res->Free();
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('AccountMirror');
