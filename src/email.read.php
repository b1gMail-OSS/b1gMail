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
	require './serverlib/init.inc.php';
if(!class_exists('BMMailbox'))
	include('./serverlib/mailbox.class.php');
include('./serverlib/vcard.class.php');
include('./serverlib/email.attachment.inc.php');
include('./serverlib/zip.class.php');
include('./serverlib/unzip.class.php');
RequestPrivileges(PRIVILEGES_USER);

define('BM_MAIL_IFRAME_STYLE', '<style id="bmMailIframeTheme">html,body{background:#fff!important;color:#333!important;color-scheme:light;margin:0;padding:0}#__bmMailText{background:#fff;color:#333;padding:1rem 1.25rem;box-sizing:border-box}a{color:#066fd1}</style>');

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));

/**
 * shared email code
 */
include('./serverlib/email.top.php');

/**
 * default action = start
 */
$tpl->addJSFile('li', $tpl->tplDir . 'js/email.js');
$tpl->addJSFile('li', 'clientlib/pdfjs/pdf.min.js');
$tpl->addJSFile('li', $tpl->tplDir . 'js/webdisk.preview.js');
$tpl->addJSFile('li', $tpl->tplDir . 'js/email.attachment.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'read';

/**
 * read mail
 */
if($_REQUEST['action'] == 'read'
	&& isset($_REQUEST['id']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);
	if($mail === false)
		die('Mail not found.');
	$mail->Parse();

	if($mail !== false)
	{
		if($groupRow['organizer'] == 'yes')
		{
			try
			{
				bmMailProcessCalendarReplyMail($userRow, $mail);
			}
			catch(Throwable $calendarEx)
			{
				PutLog('Calendar reply processing failed: '.$calendarEx->getMessage(),
					PRIO_WARNING, __FILE__, __LINE__);
			}
		}

		$enableExternal = isset($_REQUEST['enableExternal']) || ($mail->flags & FLAG_SHOWEXTERNAL) != 0;

		// unread?
		if(($mail->flags & FLAG_UNREAD) != 0)
		{
			// mark as read
			$mailbox->FlagMail(FLAG_UNREAD, false, (int)$_REQUEST['id']);
		}

		// confirmation?
		$confirmationTo = 0;
		if(($mail->flags & FLAG_DNSENT) == 0
			&& $mail->_row['folder'] != FOLDER_OUTBOX)
		{
			$dispositionNotificationTo = $mail->GetHeaderValue('disposition-notification-to');
			if($dispositionNotificationTo)
				$confirmationTo = ExtractMailAddress($dispositionNotificationTo);
			if($confirmationTo == '')
				$confirmationTo = 0;

			// auto-send disposition notification, if enabled
			if($confirmationTo
				&& $thisUser->GetPref('autosend_dn')
				&& $mail->SendDispositionNotification())
			{
				$mailbox->FlagMail(FLAG_DNSENT, true, (int)$_REQUEST['id']);
				$mail->flags |= FLAG_DNSENT;
				$confirmationTo = 0;
			}
		}

		// save meta?
		if(isset($_POST['do']) && $_POST['do'] == 'saveMeta')
		{
			if(isset($_POST['flags'][FLAG_UNREAD]))
			{
				$mailbox->FlagMail(FLAG_UNREAD, true, (int)$_REQUEST['id']);
				$mail->flags |= FLAG_UNREAD;
			}
			else
			{
				$mailbox->FlagMail(FLAG_UNREAD, false, (int)$_REQUEST['id']);
				$mail->flags &= ~FLAG_UNREAD;
			}

			if(isset($_POST['flags'][FLAG_FLAGGED]))
			{
				$mailbox->FlagMail(FLAG_FLAGGED, true, (int)$_REQUEST['id']);
				$mail->flags |= FLAG_FLAGGED;
			}
			else
			{
				$mailbox->FlagMail(FLAG_FLAGGED, false, (int)$_REQUEST['id']);
				$mail->flags &= ~FLAG_FLAGGED;
			}

			if(isset($_POST['flags'][FLAG_DONE]))
			{
				$mailbox->FlagMail(FLAG_DONE, true, (int)$_REQUEST['id']);
				$mail->flags |= FLAG_DONE;
			}
			else
			{
				$mailbox->FlagMail(FLAG_DONE, false, (int)$_REQUEST['id']);
				$mail->flags &= ~FLAG_DONE;
			}

			$mailbox->ColorMail((int)$_REQUEST['id'], (int)$_POST['color']);
			$mailbox->SetMailNotes((int)$_REQUEST['id'], trim($_POST['notes']));
			$mail->color = (int)$_POST['color'];
		}

		// get attachments
		$attachments = $mail->GetAttachments();

		// get text part
		$textParts = $mail->GetTextParts();
		if(isset($textParts['html'])
			&& ($userRow['soforthtml'] == 'yes' || isset($_REQUEST['htmlView'])
				|| $mail->IsTrusted()))
		{
			$textMode = 'html';
			$text = formatEMailHTMLText($textParts['html'],
				$enableExternal || $mail->IsTrusted(),
				$attachments,
				(int)$_REQUEST['id']);
		}
		else if(isset($textParts['text']))
		{
			$textMode = 'text';
			$text = formatEMailText($textParts['text']);
		}
		else if(isset($textParts['html']) && $userRow['soforthtml'] == 'no')
		{
			$textMode = 'text';
			$text = formatEMailText(htmlToText($textParts['html']));
		}
		else
		{
			$textMode = 'text';
			$text = '';
		}

		if($textMode == 'html')
			$text = '<base target="_blank" />' . BM_MAIL_IFRAME_STYLE . '<div id="__bmMailText"><font face="arial" size="2">' . $text . '</font></div>';
		else
			$text = '<base target="_blank" />' . BM_MAIL_IFRAME_STYLE . '<div id="__bmMailText"><font face="' . ($userRow['plaintext_courier']=='yes' ? 'courier' : 'arial') . '" size="2">' . $text . '</font></div>';

		// attachment action?
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'downloadAttachments'
		   && isset($_REQUEST['att']) && is_array($_REQUEST['att']))
		{
			$parts = $mail->GetPartList();

			$tempFileID = RequestTempFile($userRow['id'], time()+TIME_ONE_HOUR);
			$tempFileName = TempFileName($tempFileID);
			$tempFileID2 = RequestTempFile($userRow['id'], time()+TIME_ONE_HOUR);
			$tempFileName2 = TempFileName($tempFileID2);

			// create ZIP file
			$fp = fopen($tempFileName, 'wb+');
			$zip = _new('BMZIP', array($fp));

			foreach($_REQUEST['att'] as $attKey)
			{
				if(!isset($parts[$attKey]))
					continue;

				$part = $parts[$attKey];
				$attData = &$part['body'];
				$attData->Init();

				$tempFileFP = fopen($tempFileName2, 'wb');
				if($tempFileFP)
				{
					while($block = $attData->DecodeBlock(PART_CHUNK_SIZE))
					{
						fwrite($tempFileFP, $block);
					}
					fclose($tempFileFP);

					$zip->AddFile($tempFileName2, $part['filename']);

					unlink($tempFileName2);
				}

				$attData->Finish();
			}

			$size = $zip->Finish();

			header('Pragma: public');
			header(sprintf('Content-Disposition: attachment; filename="attachments-%d.zip"', (int)$_REQUEST['id']));
			header('Content-Type: application/zip');
			header(sprintf('Content-Length: %d',
				$size));

			readfile($tempFileName);

			ReleaseTempFile($userRow['id'], $tempFileID);
			ReleaseTempFile($userRow['id'], $tempFileID2);
			exit();
		}

		// vcards?
		$vcards = array();
		foreach($attachments as $key=>$info)
		{
			if(in_array(strtolower($info['mimetype']),
						array('text/directory',
								'text/x-vcard',
								'application/vcard',
								'text/anytext',
								'text/plain',
								'application/x-versit',
								'text/x-versit',
								'text/x-vcalendar'))
				&& stristr($info['filename'], '.vcf') !== false)
			{
				$tempID = RequestTempFile($userRow['id'], time()+TIME_ONE_MINUTE);
				$cardFP = fopen(TempFileName($tempID), 'w+');
				if($mail->AttachmentToFP($key, $cardFP))
				{
					$reader = _new('VCardReader', array($cardFP));
					$cardData = $reader->Parse();
					$vcards[$key] = $cardData;
				}
				fclose($cardFP);
				ReleaseTempFile($userRow['id'], $tempID);
			}
		}
		if(count($vcards) > 0)
			$tpl->assign('vcards', $vcards);

		// prev & next mail
		list($prevID, $nextID) = $mailbox->GetPrevNextMail($mail->_row['folder'], (int)$_REQUEST['id']);
		if($prevID != -1)
			$tpl->assign('prevID', $prevID);
		if($nextID != -1)
			$tpl->assign('nextID', $nextID);

		$tpl->assign('notes', $mailbox->GetMailNotes((int)$_REQUEST['id']));
		$tpl->assign('color', $mail->color);
		$tpl->assign('pageTitle', $mail->GetHeaderValue('subject'));
		$tpl->assign('smimeCertificateHash', $mail->smimeCertificateHash);
		$tpl->assign('smimeStatus', $mail->smimeStatus);
		$tpl->assign('folderID', $mail->_row['folder']);
		if(isset($folderList[$mail->_row['folder']]))
		{
			$tpl->assign('folderInfo', $folderList[$mail->_row['folder']]);
		}
		else
		{
			$folderInfo =$mailbox->GetFolder($mail->_row['folder']);
			$folderInfo['title'] = $folderInfo['titel'];
			$folderInfo['type'] = 'folder';
			$tpl->assign('folderInfo', $folderInfo);
		}
		$tpl->assign('flags', $mail->flags);
		$tpl->assign('infection', $mail->infection);
		$tpl->assign('trained', $mail->trained || $bm_prefs['use_bayes'] == 'no' || $userRow['spamfilter'] == 'no' || $mail->_row['folder'] == FOLDER_OUTBOX || $mail->IsTrusted());
		$tpl->assign('subject', $mail->GetHeaderValue('subject'));
		$tpl->assign('fromAddresses', ParseMailList($mail->GetHeaderValue('from')));
		$tpl->assign('toAddresses', ParseMailList($mail->GetHeaderValue('to')));
		$tpl->assign('ccAddresses', ParseMailList($mail->GetHeaderValue('cc')));
		$tpl->assign('replyToAddresses', ParseMailList($mail->GetHeaderValue('reply-to')));
		$tpl->assign('date', $mail->date);
		$tpl->assign('priority', (int)$mail->priority);
		$tpl->assign('text', $text);
		$tpl->assign('textMode', $textMode);
		$tpl->assign('mailID', (int)$_REQUEST['id']);
		bmMailEnrichAttachmentOpenKinds($attachments);
		$tpl->assign('attachments', $attachments);

		if($groupRow['organizer'] == 'yes')
		{
			try
			{
				$inviteCard = bmMailGetCalendarInviteCard($mail, $userRow);
				if($inviteCard !== false)
					$tpl->assign('calendarInviteCard', $inviteCard);
			}
			catch(Throwable $calendarEx)
			{
				PutLog('Calendar invite card failed: '.$calendarEx->getMessage(),
					PRIO_WARNING, __FILE__, __LINE__);
			}
		}
		$tpl->assign('noExternal', !$enableExternal && ($textMode == 'html') && !$mail->IsTrusted() && formatEMailHTMLText($textParts['html'], true) != formatEMailHTMLText($textParts['html'], false));
		$tpl->assign('htmlAvailable', isset($textParts['html']) && $textMode != 'html');
		$tpl->assign('confirmationTo', $confirmationTo);
		$tpl->assign('conversationView', $userRow['conversation_view'] == 'yes');

		if($groupRow['maildeliverystatus'] == 'yes')
		{
			$tpl->assign('deliveryStatus', $mail->GetDeliveryStatus());
		}

		if(isset($_REQUEST['preview']))
		{
			$tpl->assign('preview', true);
			$tpl->assign('narrow', isset($_REQUEST['narrow']));
			$tpl->assign('pageContent', 'li/email.read.tpl');
			$tpl->display('li/email.preview.tpl');
		}
		else if(isset($_REQUEST['print']))
		{
			if($textMode == 'html')
				$tpl->assign('text', formatEMailHTMLText(isset($textParts['html']) ? $textParts['html'] : '', $enableExternal || $mail->IsTrusted(), $attachments, (int)$_REQUEST['id']));
			$tpl->assign('plaintextCourier', $textMode!='html' && $userRow['plaintext_courier']=='yes');
			$tpl->display('li/email.print.tpl');
		}
		else
		{
			$tpl->assign('pageContent', 'li/email.read.tpl');
			$tpl->display('li/index.tpl');
		}
	}
}

/**
 * delivery status
 */
else if($_REQUEST['action'] == 'deliveryStatus'
		&& isset($_REQUEST['id'])
		&& $groupRow['maildeliverystatus'] == 'yes')
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		$tpl->assign('deliveryStatus', $mail->GetDeliveryStatus());
		$tpl->display('li/dialog.deliverystatus.tpl');
	}
}

/**
 * attached ZIP
 */
else if($_REQUEST['action'] == 'attachedZIP'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['attachment']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		$parts = $mail->GetPartList();
		if(isset($parts[$_REQUEST['attachment']]))
		{
			$part = $parts[$_REQUEST['attachment']];

			// copy attachment to temp file
			$tempFileID = RequestTempFile($userRow['id'], time()+TIME_ONE_HOUR);
			$tempFileName = TempFileName($tempFileID);
			$tempFileFP = fopen($tempFileName, 'wb+');
			$attData = &$part['body'];
			$attData->Init();
			while($block = $attData->DecodeBlock(PART_CHUNK_SIZE))
			{
				fwrite($tempFileFP, $block);
			}
			$attData->Finish();
			fseek($tempFileFP, 0, SEEK_SET);

			// try to read ZIP file
			$zip = _new('BMUnZIP', array(&$tempFileFP));
			$files = $zip->GetFileList();
			$fileTree = $zip->GetFileTree();

			if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'extract'
			   && isset($_REQUEST['fileNo'])
			   && isset($files[$_REQUEST['fileNo']]))
			{
				$file = $files[$_REQUEST['fileNo']];

				// headers
				header('Pragma: public');
				header(sprintf('Content-Disposition: attachment; filename="%s"',
					addslashes(basename($file['fileName']))));
				header('Content-Type: application/octet-stream');
				header(sprintf('Content-Length: %d',
					$file['uncompressedSize']));

				// extract & send
				$zip->ExtractFile($_REQUEST['fileNo'], false);
				exit();
			}

			// display
			$tpl->assign('id', 			$_REQUEST['id']);
			$tpl->assign('attachment', 	$_REQUEST['attachment']);
			$tpl->assign('filename',	$part['filename']);
			$tpl->assign('files', 		$fileTree);
			$tpl->display('li/dialog.zipbrowser.tpl');

			// release temp file
			ReleaseTempFile($userRow['id'], $tempFileID);
		}
	}
}

/**
 * attached mail
 */
else if($_REQUEST['action'] == 'attachedMail'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['attachment']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		$parts = $mail->GetPartList();
		if(isset($parts[$_REQUEST['attachment']]))
		{
			$part = $parts[$_REQUEST['attachment']];

			// copy mail to temp file
			$tempFileID = RequestTempFile($userRow['id'], time()+TIME_ONE_HOUR);
			$tempFileName = TempFileName($tempFileID);
			$tempFileFP = fopen($tempFileName, 'wb+');
			$attData = &$part['body'];
			$attData->Init();
			while($block = $attData->DecodeBlock(PART_CHUNK_SIZE))
			{
				fwrite($tempFileFP, $block);
			}
			$attData->Finish();
			fseek($tempFileFP, 0, SEEK_SET);

			// parse mail
			$attMail = _new('BMMail', array($userRow['id'], false, $tempFileFP, false, $tempFileName, &$thisUser));
			$attMail->ParseInfo();

			// get text part
			$textParts = $attMail->GetTextParts();
			if(isset($textParts['html'])
				&& ($userRow['soforthtml'] == 'yes' || isset($_REQUEST['htmlView'])))
			{
				$textMode = 'html';
				$text = $textParts['html'];
			}
			else if(isset($textParts['text']))
			{
				$textMode = 'text';
				$text = formatEMailText($textParts['text']);
			}
			else if(isset($textParts['html']) && $userRow['soforthtml'] == 'no')
			{
				$textMode = 'text';
				$text = htmlToText($textParts['html']);
			}
			else
			{
				$textMode = 'text';
				$text = '';
			}

			// get attachments
			$attachments = $attMail->GetAttachments();

			$tpl->assign('subject', $attMail->GetHeaderValue('subject'));
			$tpl->assign('fromAddresses', ParseMailList($attMail->GetHeaderValue('from')));
			$tpl->assign('toAddresses', ParseMailList($attMail->GetHeaderValue('to')));
			$tpl->assign('ccAddresses', ParseMailList($attMail->GetHeaderValue('cc')));
			$tpl->assign('replyToAddresses', ParseMailList($attMail->GetHeaderValue('reply-to')));
			$tpl->assign('date', $attMail->date);
			$tpl->assign('priority', (int)$attMail->priority);
			$tpl->assign('text', $text);
			$tpl->assign('textMode', $textMode);
			$tpl->assign('attachments', $attachments);
			$tpl->assign('noExternal', $textMode == 'html' && formatEMailHTMLText($textParts['html'], true) != formatEMailHTMLText($textParts['html'], false));
			$tpl->assign('htmlAvailable', isset($textParts['html']) && $textMode != 'html');

			$tpl->display('li/email.read.inline.tpl');

			// release temp file
			ReleaseTempFile($userRow['id'], $tempFileID);
		}
	}
}

/**
 * html mail
 */
else if($_REQUEST['action'] == 'inlineHTML'
		&& isset($_REQUEST['mode']) && in_array($_REQUEST['mode'], array('html', 'text')))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		$textParts = $mail->GetTextParts();
		$attachments = $mail->GetAttachments();

		$enableExternal = isset($_REQUEST['enableExternal']) || ($mail->flags & FLAG_SHOWEXTERNAL) != 0;

		if(isset($_REQUEST['enableExternal']) && ($mail->flags & FLAG_SHOWEXTERNAL) == 0)
			$mailbox->FlagMail(FLAG_SHOWEXTERNAL, true, $mail->id);

		if($_REQUEST['mode'] == 'html')
			$text = '<base target="_blank" />' . BM_MAIL_IFRAME_STYLE . '<div id="__bmMailText"><font face="arial" size="2">' . formatEMailHTMLText(isset($textParts['html']) ? $textParts['html'] : '', $enableExternal || $mail->IsTrusted(), $attachments, (int)$_REQUEST['id']) . '</font></div>';
		else
			$text = '<base target="_blank" />' . BM_MAIL_IFRAME_STYLE . '<div id="__bmMailText"><font face="' . ($userRow['plaintext_courier']=='yes' ? 'courier' : 'arial') . '" size="2">' . formatEMailText(isset($textParts['text']) ? $textParts['text'] : (isset($textParts['html']) ? htmlToText($textParts['html']) : '')) . '</font></div>';

		echo($text);
	}
}

/**
 * show thread
 */
else if($_REQUEST['action'] == 'showThread'
		&& isset($_REQUEST['id']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		$thread = $mail->GetThread();
		$tpl->assign('thread', $thread);
		$tpl->assign('mailID', (int)$_REQUEST['id']);
		$tpl->display('li/email.read.thread.tpl');
	}
}

/**
 * move mail
 */
if($_REQUEST['action'] == 'move'
	&& isset($_REQUEST['id']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		if(isset($_REQUEST['dest']))
		{
			$mailbox->MoveMail((int)$_REQUEST['id'], (int)$_REQUEST['dest']);

			echo '<script>' . "\n";
			echo '<!--' . "\n";
			echo 'parent.document.location.reload();' . "\n";
			echo 'parent.hideOverlay();' . "\n";
			echo '//-->' . "\n";
			echo '</script>' . "\n";
		}
		else
		{
			$tpl->assign('mailID', (int)$_REQUEST['id']);
			$tpl->assign('moveFolderList', $mailbox->GetMoveFolderList());
			$tpl->display('li/email.read.move.tpl');
		}
	}
}

/**
 * download e-mail
 */
else if($_REQUEST['action'] == 'download'
		&& isset($_REQUEST['id']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		// open message
		$messageFP = $mail->GetMessageFP(false);
		if($messageFP)
		{
			// get size
			$messageSize = $mail->GetMessageSize(false);

			// headers
			header('Pragma: public');
			header(sprintf('Content-Disposition: attachment; filename="%d.eml"',
				(int)$_REQUEST['id']));
			header('Content-Type: message/rfc822');
			header(sprintf('Content-Length: %d',
				$messageSize));

			// send it
			while(!feof($messageFP))
				echo(fread($messageFP, 4096));

			// close & exit
			fclose($messageFP);
			exit();
		}
	}
}

/**
 * download an attachment
 */
else if($_REQUEST['action'] == 'downloadAttachment'
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
			// 2024-05-02 Check if it is a viewable type
			$viewable_or_attachment = 'attachment';
			if(isset($_REQUEST['view']) && in_array(strtolower($part['content-type']), $VIEWABLE_TYPES)) {
				$viewable_or_attachment = 'inline';
			}
			// 2024-05-02 End Check if it is a viewable type
			header(sprintf('Content-Disposition: %s; filename="%s"',$viewable_or_attachment,
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
 * attachment dialog (VCF / ICS / generic)
 */
else if($_REQUEST['action'] == 'attachmentDialog'
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
			$attachments = $mail->GetAttachments();
			$attInfo = isset($attachments[$_REQUEST['attachment']])
				? $attachments[$_REQUEST['attachment']]
				: array(
					'filename' => $part['filename'],
					'mimetype' => $part['content-type'],
					'filetype' => strtolower(substr($part['filename'], -4))
				);
			$openKind = bmMailAttachmentOpenKind($attInfo);

			$tpl->assign('mailID', (int)$_REQUEST['id']);
			$tpl->assign('attachment', $_REQUEST['attachment']);
			$tpl->assign('filename', $attInfo['filename']);
			$tpl->assign('openKind', $openKind);
			$tpl->assign('organizerEnabled', $groupRow['organizer'] == 'yes');

			if($openKind == 'vcf')
			{
				$card = bmMailParseVcfAttachment($mail, $_REQUEST['attachment']);
				if($card !== false)
					$tpl->assign('vcard', $card);
			}
			else if($openKind == 'ics' && $groupRow['organizer'] == 'yes')
			{
				$event = bmMailParseIcsAttachment($mail, $_REQUEST['attachment']);
				if($event !== false)
				{
					$event = bmMailFillOrganizerFromMail($event, $mail);
					$tpl->assign('calendarEvent', $event);
					$tpl->assign('canCalendarReply', bmMailCanSendCalendarReply($event, $mail));
				}
			}

			$tpl->display('li/email.attachment.dialog.tpl');
		}
	}
}

/**
 * import a VCF attachment
 */
else if($_REQUEST['action'] == 'importVCF'
		&& isset($_REQUEST['attachment'])
		&& isset($_REQUEST['id']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		$tempID = RequestTempFile($userRow['id'], time()+TIME_ONE_HOUR);
		$tempName = TempFileName($tempID);
		$cardFP = fopen($tempName, 'w+');

		if($mail->AttachmentToFP($_REQUEST['attachment'], $cardFP))
		{
			fclose($cardFP);
			bmMailOverlayParentRedirect('organizer.addressbook.php'
				. '&action=addContact&importFile=' . $tempID);
		}
		else
		{
			fclose($cardFP);
			ReleaseTempFile($userRow['id'], $tempID);
		}
	}
}

/**
 * RSVP to a calendar invitation (accept / decline / tentative)
 */
else if($_REQUEST['action'] == 'calendarRsvp'
		&& isset($_REQUEST['attachment'])
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['partstat'])
		&& $groupRow['organizer'] == 'yes')
{
	include('./serverlib/calendar.class.php');

	header('Content-Type: application/json; charset=' . $currentCharset);

	$result = array('ok' => false);
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		$event = bmMailParseIcsAttachment($mail, $_REQUEST['attachment']);

		if($event === false)
			$event = bmMailBuildCalendarEventFallback($mail);

		if($event !== false)
		{
			if(empty($event['_attachmentKey']))
				$event['_attachmentKey'] = $_REQUEST['attachment'];

			$event = bmMailFillOrganizerFromMail($event, $mail);
			$mapped = bmMailCalendarPartstatMap($_REQUEST['partstat']);
			$comment = isset($_REQUEST['comment']) ? trim($_REQUEST['comment']) : '';
			$calendar = _new('BMCalendar', array($userRow['id']));
			$dateID = 0;

			if(in_array($mapped['store'], array('accepted', 'tentative'), true))
			{
				$exists = false;
				if(!empty($event['uid']))
					$exists = ($calendar->FindDateByDavUid($event['uid']) !== false);

				if(!$exists)
				{
					$groups = $calendar->GetGroups();
					$group = (($group = $thisUser->GetPref('calendarGroup')) !== false && isset($groups[$group])
						? $group
						: -2);

					$row = array(
						'title' => $event['title'] != '' ? $event['title'] : $lang_user['calendar'],
						'location' => $event['location'],
						'text' => $event['text'],
						'group' => $group,
						'startdate' => $event['startdate'],
						'enddate' => $event['enddate'],
						'reminder' => 0,
						'flags' => $event['wholeDay'] ? CLNDR_WHOLE_DAY : 0,
						'repeat_flags' => 0,
						'repeat_times' => 0,
						'repeat_value' => 0,
						'repeat_extra1' => '',
						'repeat_extra2' => '',
						'dav_uid' => !empty($event['uid']) ? $event['uid'] : ''
					);
					$dateID = (int)$calendar->AddDate($row, array());
				}
				else if(!empty($event['uid']))
				{
					$dateRow = $calendar->FindDateByDavUid($event['uid']);
					if($dateRow !== false)
						$dateID = (int)$dateRow['id'];
				}
			}

			$replySent = false;
			if(bmMailCanSendCalendarReply($event, $mail))
				$replySent = bmMailSendCalendarReply($event, $userRow, $thisUser, $mapped['store'], $comment);

			$result = array(
				'ok' => true,
				'partstat' => $mapped['store'],
				'replySent' => $replySent,
				'dateID' => $dateID
			);
		}
	}

	echo json_encode($result);
	exit();
}

/**
 * import an ICS attachment into the calendar
 */
else if($_REQUEST['action'] == 'importICS'
		&& isset($_REQUEST['attachment'])
		&& isset($_REQUEST['id'])
		&& $groupRow['organizer'] == 'yes')
{
	include('./serverlib/calendar.class.php');

	$ajax = isset($_REQUEST['ajax']);
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);
	$result = array('ok' => false);

	if($mail !== false)
	{
		$event = bmMailParseIcsAttachment($mail, $_REQUEST['attachment']);

		if($event !== false)
		{
			$event = bmMailFillOrganizerFromMail($event, $mail);

			$calendar = _new('BMCalendar', array($userRow['id']));
			$groups = $calendar->GetGroups();
			$group = (($group = $thisUser->GetPref('calendarGroup')) !== false && isset($groups[$group])
				? $group
				: -2);

			$row = array(
				'title' => $event['title'] != '' ? $event['title'] : $lang_user['calendar'],
				'location' => $event['location'],
				'text' => $event['text'],
				'group' => $group,
				'startdate' => $event['startdate'],
				'enddate' => $event['enddate'],
				'reminder' => 0,
				'flags' => $event['wholeDay'] ? CLNDR_WHOLE_DAY : 0,
				'repeat_flags' => 0,
				'repeat_times' => 0,
				'repeat_value' => 0,
				'repeat_extra1' => '',
				'repeat_extra2' => '',
				'dav_uid' => !empty($event['uid']) ? $event['uid'] : ''
			);

			$dateID = $calendar->AddDate($row, array());

			if($dateID)
			{
				$replySent = false;
				if(!empty($_REQUEST['sendReply']) && bmMailCanSendCalendarReply($event, $mail))
				{
					$partstat = isset($_REQUEST['partstat']) ? $_REQUEST['partstat'] : 'accepted';
					$comment = isset($_REQUEST['comment']) ? trim($_REQUEST['comment']) : '';
					$replySent = bmMailSendCalendarReply($event, $userRow, $thisUser, $partstat, $comment);
				}

				$result = array(
					'ok' => true,
					'dateID' => (int)$dateID,
					'title' => $row['title'],
					'replySent' => $replySent
				);
			}
		}
	}

	if($ajax)
	{
		header('Content-Type: application/json; charset=' . $currentCharset);
		echo json_encode($result);
		exit();
	}

	if($result['ok'])
	{
		bmMailOverlayParentRedirect('email.read.php?id=' . (int)$_REQUEST['id']
			. '' . '&calendarAdded=1');
	}

	bmMailOverlayParentRedirect('email.read.php?id=' . (int)$_REQUEST['id']);
}

/**
 * send confirmation
 */
else if($_REQUEST['action'] == 'sendConfirmation'
		&& isset($_REQUEST['id']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		if($mail->SendDispositionNotification())
		{
			$mailbox->FlagMail(FLAG_DNSENT, true, (int)$_REQUEST['id']);
			die('1');
		}
	}

	die('0');
}

/**
 * set spam status for one mail
 */
else if($_REQUEST['action'] == 'setSpamStatus'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['spam']))
{
	$mailbox->SetSpamStatus((int)$_REQUEST['id'], $_REQUEST['spam']=='true');
	die('1');
}

/**
 * set spam status for multiple mails
 */
else if($_REQUEST['action'] == 'setSpamStatus'
		&& isset($_REQUEST['ids']) && is_array($_REQUEST['ids'])
		&& isset($_REQUEST['spam']))
{
	foreach($_REQUEST['ids'] as $id)
		$mailbox->SetSpamStatus((int)$id, $_REQUEST['spam']=='true');
	die('1');
}

/**
 * show source
 */
else if($_REQUEST['action'] == 'showSource'
		&& isset($_REQUEST['id']))
{
	$mail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($mail !== false)
	{
		// open message
		$messageFP = $mail->GetMessageFP(false);
		if($messageFP)
		{
			// get size
			$messageSize = $mail->GetMessageSize(false);
			$messageHeader = $messageBody = '';

			// read header + body
			$passedHeaders = false;
			$bodySkipped = false;
			while(!feof($messageFP))
			{
				$line = rtrim(fgets2($messageFP), "\r\n");

				if(!$passedHeaders)
				{
					if($line == '')
					{
						$passedHeaders = true;

						// skip body for messages > 64 KB
						if($messageSize > 64*1024)
						{
							$bodySkipped = true;
							$messageBody = $lang_user['bodyskipped'];
							break;
						}
					}
					else
					{
						$messageHeader .= $line . "\n";
					}
				}
				else
				{
					$messageBody .= $line . "\n";
				}
			}

			// close
			fclose($messageFP);

			// decode header (required by some bogus formatted mails)
			$messageHeader = CharsetDecode($messageHeader, 'ISO-8859-15');

			// format header
			$messageHeader = '<font color="blue">'
								. preg_replace('/^([^\t][^\:]*)\:/m', '<font color="green">$1:</font>', HTMLFormat($messageHeader))
							. '</font>';

			// decode body
			if(!$bodySkipped)
			{
				$parts = $mail->GetPartList();
				$part = array_shift($parts);
				if(isset($part['charset']))
					$messageBody = CharsetDecode($messageBody, $part['charset']);
			}

			// format body
			$messageBody = HTMLFormat(_wordwrap($messageBody, 72, "\n"), true);

			// tabs!
			$messageHeader = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $messageHeader);
			$messageBody = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $messageBody);

			// source
			$messageSource = nl2br($messageHeader) . '<br /><br />' . nl2br($messageBody);

			// display
			$tpl->assign('source', $messageSource);
			$tpl->display('li/email.read.source.tpl');
		}
	}
}
?>