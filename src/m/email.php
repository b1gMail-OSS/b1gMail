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

include('../serverlib/init.inc.php');
if(!class_exists('BMMailbox'))
	include('../serverlib/mailbox.class.php');
if(!class_exists('BMMailBuilder'))
	include('../serverlib/mailbuilder.class.php');
RequestPrivileges(PRIVILEGES_USER | PRIVILEGES_MOBILE);

/**
 * open mailbox
 */
$mailbox = _new('BMMailbox', array($userRow['id'], $userRow['email'], $thisUser));
$folderList = $mailbox->GetFolderList(true);

/**
 * assign
 */
$tpl->assign('activeTab', 'email');

/**
 * default action = inbox
 */
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'inbox';

/**
 * inbox
 */
if($_REQUEST['action'] == 'inbox')
{
	// get folder id (default = inbox)
	$folderID = (isset($_REQUEST['folder']) && isset($folderList[(int)$_REQUEST['folder']]))
					? (int)$_REQUEST['folder']
					: FOLDER_INBOX;
	$folderName = $folderList[$folderID]['title'];

	// page stuff
	$mailsPerPage = $mailbox->GetMailsPerPage($folderID);
	$pageNo = (isset($_REQUEST['page']))
					? (int)$_REQUEST['page']
					: 1;
	$mailCount = $mailbox->GetMailCount($folderID);
	$pageCount = max(1, ceil($mailCount / max(1, $mailsPerPage)));
	$pageNo = min($pageCount, max(1, $pageNo));

	// get mail list
	$mailList = $mailbox->GetMailList($folderID,
										$pageNo,
										$mailsPerPage);

	// assign
	$tpl->assign('listOnly', isset($_REQUEST['listOnly']));
	$tpl->assign('haveMoreMails', $pageNo < $pageCount);
	$tpl->assign('perPage', $mailsPerPage);
	$tpl->assign('pageNo', $pageNo);
	$tpl->assign('nextPageNo', $pageNo+1);
	$tpl->assign('pageCount', $pageCount);
	$tpl->assign('folderID', $folderID);
	$tpl->assign('mails', $mailList);
	$tpl->assign('pageTitle', $folderName);

	if(isset($_REQUEST['listOnly']))
	{
		$tpl->display('m/folder.tpl');
	}
	else
	{
		$tpl->assign('page', 'm/folder.tpl');
		$tpl->display('m/index.tpl');
	}
}

/**
 * folders
 */
else if($_REQUEST['action'] == 'folders')
{
	// assign
	$tpl->assign('folders', $folderList);
	$tpl->assign('pageTitle', $lang_user['folders']);
	$tpl->assign('page', 'm/folders.tpl');
	$tpl->display('m/index.tpl');
}

/**
 * read
 */
else if($_REQUEST['action'] == 'read'
		&& isset($_REQUEST['id']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		// unread? => mark as read
		if(($mail->flags & FLAG_UNREAD) != 0 && !isset($_REQUEST['unread']))
			$mailbox->FlagMail(FLAG_UNREAD, false, (int)$_REQUEST['id']);
		else if(($mail->flags & FLAG_UNREAD) == 0 && isset($_REQUEST['unread']))
			$mailbox->FlagMail(FLAG_UNREAD, true, (int)$_REQUEST['id']);

		// get attachments
		$attachments = $mail->GetAttachments();

		// get text part
		$textParts = $mail->GetTextParts();
		if(isset($textParts['text']))
		{
			$textMode = 'text';
			$text = formatEMailText($textParts['text'], true, true);
		}
		else if(isset($textParts['html']))
		{
			$textMode = 'html';
			$text = formatEMailHTMLText($textParts['html'],
				true,
				$attachments,
				(int)$_REQUEST['id'],
				true);
		}
		else
		{
			$textMode = 'text';
			$text = '';
		}

		// prev & next mail
		list($prevID, $nextID) = $mailbox->GetPrevNextMail($mail->_row['folder'], (int)$_REQUEST['id']);
		if($prevID != -1)
			$tpl->assign('prevID', $prevID);
		if($nextID != -1)
			$tpl->assign('nextID', $nextID);

		// reply to
		if(($replyTo = $mail->GetHeaderValue('reply-to')) && $replyTo != '')
			$replyTo = $replyTo;
		else
			$replyTo = $mail->GetHeaderValue('from');

		// assign
		$tpl->assign('attachments', $attachments);
		$tpl->assign('isUnread', isset($_REQUEST['unread']));
		$tpl->assign('subject', $mail->GetHeaderValue('subject'));
		$tpl->assign('folderID', $mail->_row['folder']);
		$tpl->assign('folderName', $folderList[$mail->_row['folder']]['title']);
		$tpl->assign('mailID', (int)$_REQUEST['id']);
		$tpl->assign('pageTitle', HTMLFormat(strlen($mail->GetHeaderValue('subject')) > 25
			? _substr($mail->GetHeaderValue('subject'), 0, 23) . '...'
			: $mail->GetHeaderValue('subject')));
		$tpl->assign('replyTo', ExtractMailAddress($replyTo));
		$tpl->assign('replySubject', urlencode($userRow['re'] . ' ' . $mail->GetHeaderValue('subject')));
		$tpl->assign('from', $mail->GetHeaderValue('from'));
		$tpl->assign('to', $mail->GetHeaderValue('to'));
		$tpl->assign('cc', $mail->GetHeaderValue('cc'));
		$tpl->assign('date', $mail->date);
		$tpl->assign('text', $text);
		$tpl->assign('page', 'm/read.tpl');
		$tpl->display('m/index.tpl');
	}
}

/**
 * attachment
 */
else if($_REQUEST['action'] == 'attachment'
		&& isset($_REQUEST['attachment'])
		&& isset($_REQUEST['id']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		$parts = $mail->GetPartList();
		if(isset($parts[$_REQUEST['attachment']]))
		{
			$part = $parts[$_REQUEST['attachment']];

			header('Pragma: public');
			if(isset($part['charset']) && trim($part['charset']) != '')
				header('Content-Type: ' . $part['content-type'] . '; charset=' . $part['charset']);
			else
				header('Content-Type: ' . $part['content-type']);
			header(sprintf('Content-Disposition: %s; filename="%s"',
						isset($_REQUEST['view']) ? 'inline' : 'attachment',
						addslashes($part['filename'])));

			$attData = &$part['body'];
			$attData->Init();
			while($block = $attData->DecodeBlock(PART_CHUNK_SIZE))
			{
				echo $block;
			}
			$attData->Finish();
		}
	}
}

/**
 * compose
 */
else if($_REQUEST['action'] == 'compose')
{
	$possibleSenders = $thisUser->GetPossibleSenders();
	$mail = array();

	// defaults
	$composeDefaults = @unserialize($thisUser->GetPref('composeDefaults'));
	if(!is_array($composeDefaults))
	{
		$composeDefaults = array(
				'savecopy'		=> -2,
				'priority'		=> 0,
				'signature'		=> 0
		);
	}

	// signature?
	$sigText = false;
	if(isset($composeDefaults['signature']) && $composeDefaults['signature'] > 0)
	{
		$signature = $thisUser->GetSignature($composeDefaults['signature']);
		if(is_array($signature))
		{
			if(trim($signature['text']) != '')
				$sigText = $signature['text'];
		}
	}

	// reply?
	if(isset($_REQUEST['reply']) || isset($_REQUEST['forward']))
	{
		$sourceMail = $mailbox->GetMail(isset($_REQUEST['reply']) ? (int)$_REQUEST['reply'] : (int)$_REQUEST['forward']);

		if($sourceMail !== false)
		{
			// recipient
			if(isset($_REQUEST['reply']))
			{
				if(($replyTo = $sourceMail->GetHeaderValue('reply-to')) && $replyTo != '')
					$mail['to'] = $replyTo;
				else
					$mail['to'] = $sourceMail->GetHeaderValue('from');
			}

			// sender
			$recpList = array_merge(ExtractMailAddresses($sourceMail->GetHeaderValue('to')),
										ExtractMailAddresses($sourceMail->GetHeaderValue('cc')));
			$defaultSenderWithName = strchr($thisUser->GetDefaultSender(), '"') !== false;
			foreach($recpList as $val)
			{
				foreach($possibleSenders as $possibleSenderID=>$possibleSender)
					if(strtolower(ExtractMailAddress($possibleSender)) == strtolower($val))
					{
						if((strchr($possibleSender, '"') !== false && $defaultSenderWithName)
							|| (strchr($possibleSender, '"') === false && !$defaultSenderWithName))
						{
							$mail['from'] = $possibleSenderID;
							break;
						}
					}
			}

			// subject
			$mail['subject'] = trim($userRow[isset($_REQUEST['reply']) ? 're' : 'fwd']) . ' ' . $sourceMail->GetHeaderValue('subject');

			// get text part
			$textParts = $sourceMail->GetTextParts();
			if(isset($textParts['text']))
			{
				$text = formatEMailText($textParts['text'], false);
			}
			else if(isset($textParts['html']))
			{
				$text = htmlToText($textParts['html']);
			}
			else
			{
				$text = '';
			}

			// text
			$mail['text'] = formatComposeText($text, 'text', 'reply', $sourceMail,
											  $userRow['reply_quote'] == 'yes',
											  $sigText);
		}
	}
	else
	{
		if(isset($_REQUEST['to']))
			$mail['to'] = $_REQUEST['to'];
		if(isset($_REQUEST['subject']))
			$mail['subject'] = $_REQUEST['subject'];
	}

	// sender?
	if(!isset($mail['from']))
		$mail['from'] = $userRow['defaultSender'];

	// assign
	$tpl->assign('possibleSenders', $possibleSenders);
	$tpl->assign('mail', $mail);
	$tpl->assign('pageTitle', $lang_user['sendmail']);
	$tpl->assign('actionToken', CreateActionToken($userRow['id'], ATACTION_SENDMAIL, time() + TIME_ONE_DAY));
	$tpl->assign('page', 'm/compose.tpl');
	$tpl->display('m/index.tpl');
}

/**
 * delete mail
 */
else if($_REQUEST['action'] == 'deleteMail'
		&& isset($_REQUEST['id']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		$folderID = $mail->_row['folder'];
		$mailbox->DeleteMail($_REQUEST['id']);
		header('Location: email.php?folder=' . $folderID . '&sid=' . session_id());
	}
}

/**
 * move mail
 */
else if($_REQUEST['action'] == 'moveMail'
		&& isset($_REQUEST['id']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		if(isset($_REQUEST['to']) && isset($folderList[$_REQUEST['to']]))
		{
			$mailbox->MoveMail((int)$_REQUEST['id'], $_REQUEST['to']);
			header('Location: email.php?action=read&id='.((int)$_REQUEST['id']).'&sid='.session_id());
			exit();
		}
		else
		{
			// assign
			$tpl->assign('mailID', (int)$_REQUEST['id']);
			$tpl->assign('currentFolder', $mail->_row['folder']);
			$tpl->assign('folders', $folderList);
			$tpl->assign('pageTitle', $lang_user['targetfolder'].':');
			$tpl->assign('page', 'm/movemail.tpl');
			$tpl->display('m/index.tpl');
		}
	}
}

/**
 * send mail
 */
else if($_REQUEST['action'] == 'sendMail')
{
	$tpl->assign('backLink', 'email.php?action=compose&sid=' . session_id());

	// get recipients
	$recipients = ExtractMailAddresses($_REQUEST['to'] . ' ' . $_REQUEST['cc']);

	// check if recipients are blocked
	$blockedRecipients = array();
	foreach($recipients as $recp)
		if(RecipientBlocked($recp))
			$blockedRecipients[] = $recp;

	// no recipients?
	if(count($recipients) > 0)
	{
		// too much recipients?
		if(count($recipients) > $groupRow['max_recps'])
		{
			AddAbusePoint($userRow['id'], BMAP_SEND_RECP_LIMIT,
				sprintf($lang_admin['ap_comment_1_m'], count($recipients)));
			$tpl->assign('msg', sprintf($lang_user['toomanyrecipients'], $groupRow['max_recps'], count($recipients)));
		}
		// blocked recipients?
		else if(count($blockedRecipients) > 0)
		{
			AddAbusePoint($userRow['id'], BMAP_SEND_RECP_BLOCKED,
				sprintf($lang_admin['ap_comment_3_m'], implode(', ', $blockedRecipients)));
			$tpl->assign('msg', sprintf($lang_user['blockedrecipients'], HTMLFormat(implode(', ', $blockedRecipients))));
		}
		// over send limit?
		else if(!$thisUser->MaySendMail(count($recipients)))
		{
			AddAbusePoint($userRow['id'], BMAP_SEND_FREQ_LIMIT,
				sprintf($lang_admin['ap_comment_1_m'], count($recipients)));
			$tpl->assign('msg', sprintf($lang_user['exceededsendlimit'], $groupRow['send_limit_count'], $groupRow['send_limit_time']));
		}
		else
		{
			//
			// headers
			//
			$to 	= $_REQUEST['to'];
			$cc 	= $_REQUEST['cc'];

			// sender?
			$senderAddresses = $thisUser->GetPossibleSenders();
			if(isset($senderAddresses[$_REQUEST['from']]))
				$from = $senderAddresses[$_REQUEST['from']];
			else
				$from = $senderAddresses[0];

			// prepare header fields
			$to = trim(str_replace(array("\r", "\t", "\n"), '', $to));
			$cc = trim(str_replace(array("\r", "\t", "\n"), '', $cc));
			$subject = trim(str_replace(array("\r", "\t", "\n"), '', $_REQUEST['subject']));
			$replyTo = $from;

			// build the mail
			$mail = _new('BMMailBuilder');
			$mail->SetUserID($userRow['id']);

			// mandatory headers
			if($bm_prefs['write_xsenderip'] == 'yes')
				$mail->AddHeaderField('X-Sender-IP',$_SERVER['REMOTE_ADDR']);
			$mail->AddHeaderField('From', 			$from);
			$mail->AddHeaderField('Subject', 		$subject);
			$mail->AddHeaderField('Reply-To', 		$replyTo);

			// optional headers
			if($to != '')
				$mail->AddHeaderField('To',	 	$to);
			if($cc != '')
				$mail->AddHeaderField('Cc', 	$cc);

			//
			// add text
			//
			$mailText = $_REQUEST['text'] . GetsigStr('text');
			ModuleFunction('OnSendMail', array(&$mailText, false));
			$mail->AddText($mailText,
				'plain',
				$currentCharset);

			//
			// send!
			//
			if(CheckActionToken($userRow['id'], ATACTION_SENDMAIL, $_POST['actionToken'], true))
			{
				$outboxFP = $mail->Send();
			}

			//
			// ok?
			//
			if($outboxFP && is_resource($outboxFP))
			{
				//
				// update stats
				//
				Add2Stat('send');
				$domains = GetDomainList();
				$local = false;
				foreach($domains as $domain)
					if(strpos(strtolower($to . $cc), '@'.strtolower($domain)) !== false)
						$local = true;
				Add2Stat('send_'.($local ? 'intern' : 'extern'));
				$thisUser->AddSendStat(count($recipients));
				$thisUser->UpdateLastSend(count($recipients));

				//
				// add log entry
				//
				PutLog(sprintf('<%s> (%d, IP %s) sends mail from <%s> to <%s> using mobile compose form',
					$userRow['email'],
					$userRow['id'],
					$_SERVER['REMOTE_ADDR'],
					ExtractMailAddress($from),
					implode('>, <', $recipients)),
					PRIO_NOTE,
					__FILE__,
					__LINE__);

				//
				// reference
				//
				if(isset($_REQUEST['reference']) && strpos($_REQUEST['reference'], ':') !== false)
				{
					list($type, $id) = explode(':', $_REQUEST['reference']);
					if($type == 'reply')
						$mailbox->FlagMail(FLAG_ANSWERED, true, (int)$id);
					else if($type == 'forward')
						$mailbox->FlagMail(FLAG_FORWARDED, true, (int)$id);
				}

				//
				// plugin handler
				//
				ModuleFunction('AfterSendMail', array($userRow['id'], ExtractMailAddress($from), $recipients, $outboxFP));

				//
				// save copy
				//
				$saveTo = FOLDER_OUTBOX;
				$mailObj = _new('BMMail', array(0, false, $outboxFP, false));
				$mailObj->Parse();
				$mailObj->ParseInfo();
				$mailbox->StoreMail($mailObj, $saveTo);

				//
				// clean up
				//
				$mail->CleanUp();

				//
				// done
				//
				$tpl->assign('msg', $lang_user['mailsent']);
				$tpl->assign('backLink', 'email.php?sid=' . session_id());
			}
			else
			{
				$tpl->assign('msg', $lang_user['sendfailed']);
			}
		}
	}
	else
	{
		$tpl->assign('msg', $lang_user['norecipients']);
	}

	// assign
	$tpl->assign('page', 'm/message.tpl');
	$tpl->assign('pageTitle', $lang_user['sendmail']);
	$tpl->assign('backLink', 'email.php?sid='.session_id());
	$tpl->display('m/index.tpl');
}

/**
 * logout
 */
else if($_REQUEST['action'] == 'logout')
{
	BMUser::Logout();
	header('Location: ./index.php');
	exit();
}
?>