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

require './serverlib/init.inc.php';
if(!class_exists('BMMailbox'))
	include('./serverlib/mailbox.class.php');
include('./serverlib/addressbook.class.php');
if(!class_exists('BMMailBuilder'))
	include('./serverlib/mailbuilder.class.php');
RequestPrivileges(PRIVILEGES_USER);

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
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'compose';

/**
 * compose
 */
if($_REQUEST['action'] == 'compose')
{
	$possibleSenders = $thisUser->GetPossibleSenders();
	$mail = array();
	$reference = '';
	$textMode = $userRow['soforthtml'] == 'yes' ? 'html' : 'text';
	$book = _new('BMAddressbook', array($userRow['id']));

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
	$sigText = $sigHTML = false;
	if(isset($composeDefaults['signature']) && $composeDefaults['signature'] > 0)
	{
		$signature = $thisUser->GetSignature($composeDefaults['signature']);
		if(is_array($signature))
		{
			if(trim($signature['text']) != '')
				$sigText = $signature['text'];
			if(trim($signature['html']) != '')
				$sigHTML = $signature['html'];
		}
	}

	// set S/MIME settings
	$mail['smimeSign'] = $thisUser->GetPref('smimeSign');
	$mail['smimeEncrypt'] = $thisUser->GetPref('smimeEncrypt');

	// reply, forward or redirect?
	if(isset($_REQUEST['reply'])			// all?
		|| (isset($_REQUEST['forward']) && !is_array($_REQUEST['forward']))
		|| isset($_REQUEST['redirect']))
	{
		$action 	= isset($_REQUEST['reply'])
						? 'reply'
						: (isset($_REQUEST['forward'])
							? 'forward'
							: 'redirect');
		$sourceID	= (int)(isset($_REQUEST['reply'])
							? $_REQUEST['reply']
							: (isset($_REQUEST['forward'])
								? $_REQUEST['forward']
								: $_REQUEST['redirect']));
		$reference = $action . ':' . $sourceID;
		$sourceMail	= $mailbox->GetMail($sourceID);

		// get text part
		$textParts = $sourceMail->GetTextParts();
		if(isset($textParts['html'])
			&& ($userRow['soforthtml'] == 'yes' || isset($_REQUEST['htmlView'])))
		{
			$textMode = 'html';
			$text = formatEMailHTMLText($textParts['html'], true, array(), -1, false, true);
		}
		else if(isset($textParts['text']))
		{
			$textMode = 'text';
			$text = formatEMailText($textParts['text'], false);
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

		// ...and text
		if(isset($_REQUEST['text']))
		{
			$textMode = 'text';
			$mail['text'] = formatComposeText($_REQUEST['text'], 'text', $action, $sourceMail,
											  $userRow['reply_quote'] == 'yes',
											  $sigText);
		}
		else
			$mail['text'] = formatComposeText($text, $textMode, $action, $sourceMail,
											  $userRow['reply_quote'] == 'yes',
											  $textMode == 'html' ? $sigHTML : $sigText);

		// subject
		$mail['subject'] = ($action == 'reply'
								? trim($userRow['re']) . ' '
								: ($action == 'redirect'
									? ''
									: trim($userRow['fwd']) . ' ')) . $sourceMail->GetHeaderValue('subject');

		// suggest encryption if source mail is encrypted
		$mail['smimeEncrypt'] = $mail['smimeEncrypt'] || ($sourceMail->smimeStatus & SMIME_DECRYPTED) != 0;

		// recipients
		if($action == 'redirect')
		{
			$mail['to'] = DecodeEMail($sourceMail->GetHeaderValue('to'));
			$mail['cc'] = DecodeEMail($sourceMail->GetHeaderValue('cc'));
			$mail['bcc'] = DecodeEMail($sourceMail->GetHeaderValue('bcc'));
			$mail['replyto'] = DecodeEMail($sourceMail->GetHeaderValue('reply-to'));
			$mail['priority'] = $sourceMail->GetHeaderValue('x-priority') == 5
				? ITEMPRIO_LOW
				: ($sourceMail->GetHeaderValue('x-priority') == 1
					? ITEMPRIO_HIGH
					: ITEMPRIO_NORMAL);
			$mail['isAutoSavedDraft'] = ($sourceMail->flags & FLAG_AUTOSAVEDDRAFT) != 0;
			if($mail['isAutoSavedDraft'])
				$mail['baseDraftID'] = $sourceMail->id;
		}
		else if($action == 'reply')
		{
			if(($replyTo = $sourceMail->GetHeaderValue('reply-to')) && $replyTo != '')
				$mail['to'] = DecodeEMail($replyTo);
			else
				$mail['to'] = DecodeEMail($sourceMail->GetHeaderValue('from'));
			if(($origTo = ExtractMailAddress($sourceMail->GetHeaderValue('to'))) != ''
				&& (new BMWorkgroup($id))->GetIDbyMail($origTo) != 0)
				$mail['replyto'] = DecodeEMail($origTo);

			if(isset($_REQUEST['all']))
			{
				$recpList = array_merge(ExtractMailAddresses($sourceMail->GetHeaderValue('to')),
											ExtractMailAddresses($sourceMail->GetHeaderValue('cc')));
				foreach($recpList as $key=>$val)
					if(strtolower($val) == strtolower(ExtractMailAddress($mail['to'])))
						unset($recpList[$key]);
					else
						foreach($possibleSenders as $possibleSender)
							if(strtolower(ExtractMailAddress($possibleSender)) == strtolower($val))
							{
								unset($recpList[$key]);
								break;
							}
				$mail['cc'] = DecodeEMail(implode('; ', $recpList));
			}
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


		// attachments?
		if($action == 'forward'
				|| $action == 'redirect')
		{
			$attachments = $sourceMail->GetAttachments();
			$parts = $sourceMail->GetPartList();
			$attachmentList = '';

			foreach($attachments as $attID=>$attInfo)
			{
				// get temp file
				$tempFileID = RequestTempFile($userRow['id'], time()+8*TIME_ONE_HOUR);
				$tempFileName = TempFileName($tempFileID);
				$tempFP = fopen($tempFileName, 'wb');
				assert('is_resource($tempFP)');

				// copy attachment to temp file
				$part = $parts[$attID];
				$attData = &$part['body'];
				$attData->Init();
				while($block = $attData->DecodeBlock(PART_CHUNK_SIZE))
				{
					fwrite($tempFP, $block);
				}
				$attData->Finish();

				// add to list
				$attachmentString = sprintf(';%d,%s,%s',
					$tempFileID,
					str_replace(array("\r", "\n", ',', ';', '/', '\\', '\'', '"'), '', $attInfo['filename']),
					preg_replace('/[^0-9a-zA-Z\_\.\/-]/', '', $attInfo['mimetype']));
				$attachmentList .= $attachmentString;
			}

			if($attachmentList != '')
				$mail['attachments'] = $attachmentList;
		}
	}

	// multi forward
	else if(isset($_REQUEST['forward']) && is_array($_REQUEST['forward']))
	{
		$attachmentList = '';

		foreach($_REQUEST['forward'] as $forwardID)
		{
			$forwardMail = $mailbox->GetMail((int)$forwardID);

			if($forwardMail !== false)
			{
				$messageFP = $forwardMail->GetMessageFP();

				// get temp file
				$tempFileID = RequestTempFile($userRow['id'], time()+8*TIME_ONE_HOUR);
				$tempFileName = TempFileName($tempFileID);
				$tempFP = fopen($tempFileName, 'wb');
				assert('is_resource($tempFP)');

				// copy attachment to temp file
				while($block = fread($messageFP, 4096))
				{
					fwrite($tempFP, $block);
				}
				fclose($messageFP);

				// add to list
				$attachmentString = sprintf(';%d,%d.eml,%s',
					$tempFileID,
					(int)$forwardID,
					'message/rfc822');
				$attachmentList .= $attachmentString;
			}
		}

		if($attachmentList != '')
			$mail['attachments'] = $attachmentList;
	}

	// new email
	else
	{
		// find auto-saved draft
		$latestDraftID = $mailbox->GetLatestAutoSavedDraft(8*TIME_ONE_HOUR);
		if($latestDraftID !== false)
		{
			$latestDraftMail = $mailbox->GetMail($latestDraftID);
			if($latestDraftMail)
			{
				$latestDraftTo = ExtractMailAddresses($latestDraftMail->GetHeaderValue('to'));
				if(count($latestDraftTo) > 1)
					$latestDraftTo = array_shift($latestDraftTo) . ', ...';
				else if(is_array($latestDraftTo))
					$latestDraftTo = array_shift($latestDraftTo);
				else
					$latestDraftTo = '';

				$latestDraft = array(
					'id'		=> $latestDraftID,
					'lastEdit'	=> $latestDraftMail->date,
					'subject'	=> $latestDraftMail->GetHeaderValue('subject'),
					'to'		=> $latestDraftTo
				);
				$tpl->assign('latestDraft', $latestDraft);
			}
		}
	}

	// to, cc or subject given?
	if(isset($_REQUEST['to']))
		$mail['to'] = $_REQUEST['to'];
	else if(isset($_REQUEST['toGroup']))
	{
		$groupTitle = $book->GetGroupTitle((int)$_REQUEST['toGroup']);
		$mail['to'] = sprintf('"%s" <%d@contact.groups>', $groupTitle, (int)$_REQUEST['toGroup']);
	}
	if(isset($_REQUEST['cc']))
		$mail['cc'] = $_REQUEST['cc'];
	if(isset($_REQUEST['subject']))
		$mail['subject'] = $_REQUEST['subject'];

	// set edit mode
	$mail['textMode'] = $textMode;

	// text?
	if(!isset($mail['text']))
	{
		$mail['text'] = '';

		if(isset($_POST['text']))
		{
			if($textMode == 'html')
				$mail['text'] = '<font face="arial" size="2">' . nl2br(HTMLFormat($_POST['text'])) . '</font>';
			else
				$mail['text'] = $_POST['text'];
		}

		// signature
		if($textMode == 'html' && $sigHTML !== false)
			$mail['text'] .= '<br />' . $sigHTML;
		else if($textMode == 'text' && $sigText !== false)
			$mail['text'] .= "\n\n" . $sigText;
	}

	// subject?
	if(!isset($mail['subject']) && isset($_POST['subject']))
		$mail['subject'] = $_POST['subject'];

	// sender?
	if(!isset($mail['from']))
		$mail['from'] = $userRow['defaultSender'];

	// safe code?
	if($groupRow['mail_send_code'] == 'yes')
	{
		if(!class_exists('BMCaptcha'))
			include(B1GMAIL_DIR . 'serverlib/captcha.class.php');
		$captcha = BMCaptcha::createDefaultProvider();
		$tpl->assign('captchaHTML', $captcha->getHTML());
		$tpl->assign('captchaInfo', $captcha->getInfo());
	}

	// defaults
	$mail['attachVCard']		= isset($composeDefaults['attachVCard']);
	$mail['certMail']			= isset($composeDefaults['certMail']);
	$mail['mailConfirmation']	= isset($composeDefaults['mailConfirmation']);
	if(!isset($mail['priority']))
		$mail['priority']		= $composeDefaults['priority'];
	if($mail['textMode'] == 'html')
		$mail['text']			= '<span style="font-family:arial,helvetica,sans-serif;font-size:12px;">' . $mail['text'] . '</span>';

	// assign
	$tpl->assign('autoSaveDrafts', $groupRow['auto_save_drafts'] == 'yes' && $userRow['auto_save_drafts'] == 'yes');
	$tpl->assign('autoSaveDraftsInterval', max($bm_prefs['min_draft_save_interval'], $userRow['auto_save_drafts_interval']));
	$tpl->assign('composeDefaults', $composeDefaults);
	$tpl->assign('pageTitle', $lang_user['sendmail']);
	$tpl->assign('useCourier', $userRow['plaintext_courier'] == 'yes');
	$tpl->assign('signatures', $thisUser->GetSignatures());
	$tpl->assign('reference', $reference);
	$tpl->assign('mail', $mail);
	$tpl->assign('possibleSenders', $possibleSenders);
	$tpl->assign('smime', $groupRow['smime'] == 'yes');
	$tpl->assign('attKeywords', $lang_user['att_keywords']);
	$tpl->assign('attCheck', $userRow['attcheck'] == 'yes');
	$tpl->assign('lineSep', $thisUser->GetPref('linesep'));
	$tpl->assign('actionToken', CreateActionToken($userRow['id'], ATACTION_SENDMAIL, time() + TIME_ONE_DAY));
	$tpl->assign('pageContent', 'li/email.compose.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * add attachment
 */
else if($_REQUEST['action'] == 'addAttachment')
{
	// compute max size
	$attSize = 0;
	if(strlen(trim($_REQUEST['attList'])) > 3)
	{
		$attachments = explode(';', _unescape($_REQUEST['attList']));
		foreach($attachments as $attachment)
			if(strlen(trim($attachment)) > 3)
			{
				list($tempFileID, $fileName, $contentType) = explode(',', $attachment);
				$tempFileID = (int)$tempFileID;
				if(ValidTempFile($userRow['id'], $tempFileID))
					$attSize += filesize(TempFileName($tempFileID));
			}
	}

	// space left?
	$attLeft = $groupRow['anlagen'] - $attSize;

	// assign
	$tpl->assign('bar', array('value' => $attSize, 'max' => $groupRow['anlagen']));
	$tpl->assign('title', $lang_user['addattach']);
	$tpl->assign('text', $lang_user['addattachtext']);
	$tpl->assign('multiple', true);
	$tpl->assign('formAction', 'email.compose.php?action=uploadAttachment&spaceLeft=' . $attLeft . '&sid=' . session_id());
	$tpl->assign('fieldName', 'attachFile');
	$tpl->display('li/dialog.openfile.tpl');
}

/**
 * get attachment
 */
else if($_REQUEST['action'] == 'getAttachment'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['type'])
		&& isset($_REQUEST['name'])
		&& ValidTempFile($userRow['id'], (int)$_REQUEST['id']))
{
	$contentDisposition = (in_array(strtolower($_REQUEST['type']), $VIEWABLE_TYPES))
		? 'inline'
		: 'attachment';
	$tempFileName = TempFileName((int)$_REQUEST['id']);
	$tempFP = fopen($tempFileName, 'rb');

	if($tempFP)
	{
		// headers
		header('Pragma: public');
		header(sprintf('Content-Disposition: %s; filename="%s"',
			$contentDisposition,
			addslashes(_unescape($_REQUEST['name']))));
		header(sprintf('Content-Type: %s',
			$_REQUEST['type']));
		header(sprintf('Content-Length: %d',
			filesize($tempFileName)));

		// output
		readfile($tempFileName);
		exit();
	}
}

/**
 * set no draft notify flag
 */
else if($_REQUEST['action'] == 'setNoDraftNotify')
{
	$mailbox->SetNoDraftNotify();
	echo '1';
	exit();
}

/**
 * delete draft
 */
else if($_REQUEST['action'] == 'deleteDraft' && isset($_REQUEST['id']))
{
	$draftMail = $mailbox->GetMail((int)$_REQUEST['id']);

	if($draftMail === false || ($draftMail->flags & FLAG_AUTOSAVEDDRAFT) == 0)
	{
		echo '0';
		exit();
	}

	$mailbox->DeleteMail($draftMail->id, true);
	echo '1';
	exit();
}

/**
 * upload attachment
 */
else if($_REQUEST['action'] == 'uploadAttachment'
	&& isset($_REQUEST['spaceLeft'])
	&& $_REQUEST['spaceLeft'] <= $groupRow['anlagen']
	&& IsPOSTRequest())
{
	echo '<script>' . "\n";
	echo '<!--' . "\n";

	$spaceLeft = (int)$_REQUEST['spaceLeft'];
	$files = getUploadedFiles('attachFile');
	foreach($files as $file)
	{
		if($file['size'] <= $spaceLeft)
		{
			$spaceLeft -= $file['size'];

			$attachmentString = sprintf(';%d,%s,%s',
				$file['dest_id'],
				str_replace(array('/', ',', ';', '\\'), '', $file['name']),
				str_replace(array(',', ';', '\\'), '', $file['type']));
			echo 'parent.document.getElementById(\'attachments\').value += \'' . addslashes($attachmentString) . '\';' . "\n";
		}
		else
		{
			ReleaseTempFile($userRow['id'], $file['dest_id']);
			echo 'alert(\'' . addslashes(sprintf($lang_user['toobigattach'], round($groupRow['anlagen']/1024, 2))) . '\');' . "\n";
			break;
		}
	}

	echo 'parent.generateAttachmentList();' . "\n";
	echo 'parent.hideOverlay();' . "\n";
	echo '//-->' . "\n";
	echo '</script>' . "\n";
}

/**
 * upload attachment from DnD
 */
else if($_REQUEST['action'] == 'uploadDnDAttachment'
		&& isset($_REQUEST['filename'])
		&& isset($_REQUEST['size'])
		&& isset($_REQUEST['type'])
		&& IsPOSTRequest())
{
	// compute max size
	$attSize = 0;
	if(strlen(trim($_REQUEST['attList'])) > 3)
	{
		$attachments = explode(';', _unescape($_REQUEST['attList']));
		foreach($attachments as $attachment)
			if(strlen(trim($attachment)) > 3)
			{
				list($tempFileID, $fileName, $contentType) = explode(',', $attachment);
				$tempFileID = (int)$tempFileID;
				if(ValidTempFile($userRow['id'], $tempFileID))
					$attSize += filesize(TempFileName($tempFileID));
			}
	}

	// calc space left
	$attLeft = $groupRow['anlagen'] - $attSize;

	// file info
	$fileName = $_REQUEST['filename'];
	$fileSize = (int)$_REQUEST['size'];
	$mimeType = $_REQUEST['type'];

	if($mimeType == '' || $mimeType == 'application/octet-stream')
		$mimeType = GuessMIMEType($fileName);

	$tempFileID = RequestTempFile($userRow['id'], time()+8*TIME_ONE_HOUR);
	$tempFileName = TempFileName($tempFileID);

	// get uploaded file
	$success = false;
	if($fileSize <= $attLeft)
	{
		$fpOut = @fopen($tempFileName, 'wb');
		if($fpOut)
		{
			$fp = @fopen('php://input', 'rb');

			if($fp)
			{
				$readBytes = 0;
				while(!feof($fp))
				{
					$chunkSize = 4*1024;

					$chunk = base64_decode(fread($fp, $chunkSize));
					fwrite($fpOut, $chunk);

					$readBytes += strlen($chunk);

					if($readBytes >= $fileSize)
						break;
				}
				fclose($fp);

				$success = true;
			}

			fclose($fpOut);
		}
	}
	else
	{
		echo 'alert(\'' . addslashes(sprintf($lang_user['toobigattach'], round($groupRow['anlagen']/1024, 2))) . '\');' . "\n";
	}

	if(!$success)
	{
		ReleaseTempFile($userRow['id'], $tempFileID);
	}
	else
	{
		$attachmentString = sprintf(';%d,%s,%s',
			$tempFileID,
			str_replace(array('/', ',', ';', '\\'), '', $fileName),
			str_replace(array(',', ';', '\\'), '', $mimeType));
		echo 'parent.document.getElementById(\'attachments\').value += \'' . addslashes($attachmentString) . '\';' . "\n";
		echo 'parent.generateAttachmentList();' . "\n";
	}
}

/**
 * delete attachment
 */
else if($_REQUEST['action'] == 'deleteAttachment'
	&& isset($_REQUEST['id']))
{
	$id = (int)$_REQUEST['id'];
	die(ReleaseTempFile($userRow['id'], $id) ? '1' : '0');
}

/**
 * send mail
 */
else if($_REQUEST['action'] == 'sendMail'
		&& IsPOSTRequest()
		&& isset($_POST['actionToken'])
		&& CheckActionToken($userRow['id'], ATACTION_SENDMAIL, $_POST['actionToken'], false))
{
	$captcha = false;
	if($groupRow['mail_send_code'] == 'yes')
	{
		if(!class_exists('BMCaptcha'))
			include(B1GMAIL_DIR . 'serverlib/captcha.class.php');
		$captcha = BMCaptcha::createDefaultProvider();
	}

	// safecode?
	if($captcha !== false && !$captcha->check())
	{
		$tpl->assign('msg', $lang_user['invalidcode']);
		$tpl->assign('pageContent', 'li/error.tpl');
	}
	else
	{
		// sanitize + expand addresses
		$_REQUEST['to'] = PrepareSendAddresses($_REQUEST['to']);
		$_REQUEST['cc'] = PrepareSendAddresses($_REQUEST['cc']);
		$_REQUEST['bcc'] = PrepareSendAddresses($_REQUEST['bcc']);

		// get recipients
		$recipients = ExtractMailAddresses($_REQUEST['to'] . ' ' . $_REQUEST['cc'] . ' ' . $_REQUEST['bcc']);

		// check if recipients are blocked
		$blockedRecipients = array();
		foreach($recipients as $recp)
			if(RecipientBlocked($recp))
				$blockedRecipients[] = $recp;

		// no recipients?
		if(count($recipients) > 0 || $_REQUEST['do'] == 'saveDraft')
		{
			// too many recipients?
			if($_REQUEST['do'] != 'saveDraft' && count($recipients) > $groupRow['max_recps'])
			{
				AddAbusePoint($userRow['id'], BMAP_SEND_RECP_LIMIT,
					sprintf($lang_admin['ap_comment_1'], count($recipients)));
				$tpl->assign('msg', sprintf($lang_user['toomanyrecipients'], $groupRow['max_recps'], count($recipients)));
				$tpl->assign('pageContent', 'li/error.tpl');
			}

			// blocked recipients?
			else if($_REQUEST['do'] != 'saveDraft' && count($blockedRecipients) > 0)
			{
				AddAbusePoint($userRow['id'], BMAP_SEND_RECP_BLOCKED,
					sprintf($lang_admin['ap_comment_3'], implode(', ', $blockedRecipients)));
				$tpl->assign('msg', sprintf($lang_user['blockedrecipients'], HTMLFormat(implode(', ', $blockedRecipients))));
				$tpl->assign('pageContent', 'li/error.tpl');
			}

			// over send limit?
			else if($_REQUEST['do'] != 'saveDraft' && !$thisUser->MaySendMail(count($recipients)))
			{
				AddAbusePoint($userRow['id'], BMAP_SEND_FREQ_LIMIT,
					sprintf($lang_admin['ap_comment_1'], count($recipients)));
				$tpl->assign('msg', sprintf($lang_user['exceededsendlimit'], $groupRow['send_limit_count'], $groupRow['send_limit_time']));
				$tpl->assign('pageContent', 'li/error.tpl');
			}

			else
			{
				$deleteDraftAfterSend = 0;

				//
				// headers
				//
				$to 	= $_REQUEST['to'];
				$cc 	= $_REQUEST['cc'];
				$bcc 	= $_REQUEST['bcc'];

				// sender?
				$senderAddresses = $thisUser->GetPossibleSenders();
				if(isset($senderAddresses[$_REQUEST['from']]))
					$from = $senderAddresses[$_REQUEST['from']];
				else
					$from = $senderAddresses[0];

				// check if sender address is ext. an alias
				$senderAddressIsAlias = false;
				$aliases = $thisUser->GetAliases();
				foreach($aliases as $alias)
				{
					if($alias['type'] == ALIAS_SENDER)
					{
						if(strtolower(ExtractMailAddress($alias['email']))
							== strtolower(ExtractMailAddress($from)))
						{
							$senderAddressIsAlias = true;
							break;
						}
					}
				}

				// prepare header fields
				$subject = trim(str_replace(array("\r", "\t", "\n"), '', $_REQUEST['subject']));
				if(count(ExtractMailAddresses($_REQUEST['replyto'])) > 0)
					$replyTo = trim(str_replace(array("\r", "\t", "\n"), '', $_REQUEST['replyto']));
				else
					$replyTo = $from;

				// build the mail
				$mail = _new('BMMailBuilder');
				$mail->SetUserID($userRow['id']);

				// s/mime?
				if($groupRow['smime'] == 'yes')
				{
					$mail->_smimeUser = &$thisUser;

					// sign?
					if(isset($_REQUEST['smimeSign']) && !isset($_REQUEST['certMail']))
						$mail->_smimeSign = true;

					// encrypt?
					if(isset($_REQUEST['smimeEncrypt']) && !isset($_REQUEST['certMail']))
						$mail->_smimeEncrypt = true;
				}

				// mandatory headers
				if($bm_prefs['write_xsenderip'] == 'yes')
					$mail->AddHeaderField('X-Sender-IP',$_SERVER['REMOTE_ADDR']);
				$mail->AddHeaderField('From', 			$from);
				$mail->AddHeaderField('Subject', 		$subject);
				$mail->AddHeaderField('Reply-To', 		$replyTo);

				// ext. alias sender?
				if($senderAddressIsAlias)
					$mail->SetMailFrom($userRow['email']);

				// optional headers
				if($to != '')
					$mail->AddHeaderField('To',	 	$to);
				if($cc != '')
					$mail->AddHeaderField('Cc', 	$cc);
				if($bcc != '')
					$mail->AddHeaderField('Bcc', 	$bcc);

				// priority
				if($_REQUEST['priority'] != 0)
				{
					$mail->AddHeaderField('X-Priority', $_REQUEST['priority'] == ITEMPRIO_HIGH
						? 1
						: ($_REQUEST['priority'] == ITEMPRIO_LOW
							? 5
							: 3));
				}

				// mail confirmation?
				if(isset($_REQUEST['mailConfirmation']) && !isset($_REQUEST['certMail']))
					$mail->AddHeaderField('Disposition-Notification-To', $replyTo);

				// ref headers
				if(isset($_REQUEST['reference']) && strpos($_REQUEST['reference'], ':') !== false)
				{
					list($type, $id) = explode(':', $_REQUEST['reference']);

					$referencedMail = $mailbox->GetMail($id);
					if($referencedMail !== false)
					{
						if($type == 'reply')
							$mail->AddHeaderField('In-Reply-To', trim(str_replace(array("\r", "\t", "\n"), '', $referencedMail->GetHeaderValue('message-id'))));

						if($type == 'reply' || $type == 'forward')
						{
							$msgReferences = ExtractMessageIDs($referencedMail->GetHeaderValue('references'));
							$msgReferences[] = trim(str_replace(array("\r", "\t", "\n"), '', $referencedMail->GetHeaderValue('message-id')));

							if(count($msgReferences) > 10)
								$msgReferences = array_slice($msgReferences, count($msgReferences)-10);

							$mail->AddHeaderField('References', implode(' ', $msgReferences));
						}
					}
				}

				// based on an auto-saved draft which needs to be deleted?
				if(isset($_REQUEST['baseDraftID']))
				{
					$baseDraftMail = $mailbox->GetMail((int)$_REQUEST['baseDraftID']);

					if($baseDraftMail !== false && ($baseDraftMail->flags & FLAG_AUTOSAVEDDRAFT) != 0)
						$deleteDraftAfterSend = $baseDraftMail->id;
				}

				//
				// add text
				//
				$mailText = $_REQUEST['emailText'];
				if($_REQUEST['newTextMode'] == 'html')
				{
					if($_REQUEST['do'] != 'saveDraft')
					{
						$mailText .= GetSigStr('html');
						ModuleFunction('OnSendMail', array(&$mailText, true));
					}

					// html mail
					$mail->AddText('<html>' . $mailText . '</html>',
						'html',
						$currentCharset);
				}
				else
				{
					if($_REQUEST['do'] != 'saveDraft')
					{
						$mailText .= GetSigStr('text');
						ModuleFunction('OnSendMail', array(&$mailText, false));
					}

					// text mail
					$mail->AddText($mailText,
						'plain',
						$currentCharset);
				}

				//
				// add attachments
				//
				$attSize = 0;
				$attTempFiles = array();
				if(strlen(trim($_REQUEST['attachments'])) > 3)
				{
					$attachments = explode(';', $_REQUEST['attachments']);
					foreach($attachments as $attachment)
					{
						if(strlen(trim($attachment)) > 3)
						{
							list($tempFileID, $fileName, $contentType) = explode(',', $attachment);
							$tempFileID = (int)$tempFileID;
							if(ValidTempFile($userRow['id'], $tempFileID))
							{
								if(($attSize + filesize(TempFileName($tempFileID))) <= $groupRow['anlagen'])
								{
									// open attachment
									if($tempFileFP = fopen(TempFileName($tempFileID), 'rb'))
									{
										// add to mail
										$mail->AddAttachment($tempFileFP,
											$contentType,
											$fileName);

										// add temp id to delete list
										$attTempFiles[] = array($tempFileID, $tempFileFP);
										$attSize += filesize(TempFileName($tempFileID));
									}
								}
								else
								{
									// add temp id to delete list
									$attTempFiles[] = array($tempFileID, $tempFileFP);
								}
							}
						}
					}
				}

				//
				// add vcard?
				//
				if(isset($_REQUEST['attachVCard']))
				{
					// add to mail
					$mail->AddAttachment($thisUser->BuildVCard(),
						'text/x-vcard',
						preg_replace('/[^0-9a-zA-Z ]/', '', $userRow['vorname'] . ' ' . $userRow['nachname']) . '.vcf');
				}

				//
				// send!
				//
				if($_REQUEST['do'] == 'saveDraft'
					|| isset($_REQUEST['certMail']))
				{
					$outboxFP = $mail->Build();
				}
				else
				{
					$actionTokenAge = GetActionTokenAge($userRow['id'], $_POST['actionToken']);

					if(CheckActionToken($userRow['id'], ATACTION_SENDMAIL, $_POST['actionToken'], true))
					{
						$sendFastPrefs = GetAbuseTypePrefs(BMAP_SEND_FAST);
						if(isset($sendFastPrefs['interval']) && $actionTokenAge !== false
							&& $actionTokenAge < $sendFastPrefs['interval'])
						{
							AddAbusePoint($userRow['id'], BMAP_SEND_FAST,
								sprintf($lang_admin['ap_comment_7'], $actionTokenAge));
						}

						$outboxFP = $mail->Send();
					}
					else
						$outboxFP = false;
				}

				//
				// ok?
				//
				if($outboxFP && is_resource($outboxFP))
				{
					if($_REQUEST['do'] != 'saveDraft')
					{
						//
						// update stats
						//
						Add2Stat('send');
						$domains = GetDomainList();
						$local = false;
						foreach($domains as $domain)
							if(strpos(strtolower($to . $cc . $bcc), '@'.strtolower($domain)) !== false)
								$local = true;
						Add2Stat('send_'.($local ? 'intern' : 'extern'));
						$thisUser->AddSendStat(count($recipients));
						$thisUser->UpdateLastSend(count($recipients));

						//
						// add log entry
						//
						PutLog(sprintf('<%s> (%d, IP: %s) sends%s mail from <%s> to <%s> using compose form',
							$userRow['email'],
							$userRow['id'],
							$_SERVER['REMOTE_ADDR'],
							isset($_REQUEST['certMail']) ? ' certified' : '',
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

						ModuleFunction('AfterSendMail', array($userRow['id'], ExtractMailAddress($from), $recipients, $outboxFP));

						//
						// are there recipients who are not in the addressbook?
						//
						$book = false;
						$recpList = ParseMailList($to . ', ' . $cc . ', ' . $bcc);
						$addrMails = array();
						foreach($recpList as $recp)
						{
							$address = ExtractMailAddress($recp['mail']);
							if(empty($address)) continue;
							if(strtolower($address) == strtolower($userRow['email'])) continue;
							$isAlias = false;
							foreach($aliases as $alias)
							{
								if(strtolower($address) == strtolower($alias['email']))
								{
									$isAlias = true;
									break;
								}
							}
							if($isAlias) continue;
							if($book === false) $book = _new('BMAddressbook', array($userRow['id']));
							if($book->LookupEmail($address) > 0
								|| $book->LookupEmail(DecodeEMail($address)) > 0) continue;

							$firstName = $lastName = '';

							$nameParts = explode(',', $recp['name']);
							if(count($nameParts) == 2)
							{
								$lastName = trim($nameParts[0]);
								$firstName = trim($nameParts[1]);
							}
							else
							{
								$nameParts = explode(' ', $recp['name']);

								if(count($nameParts) == 1)
								{
									$lastName = $nameParts[0];
								}
								else if(count($nameParts) == 2)
								{
									$firstName = $nameParts[0];
									$lastName = $nameParts[1];
								}
								else if(count($nameParts) > 2)
								{
									$firstName = array_shift($nameParts);
									$lastName = implode(' ', $nameParts);
								}
							}

							$addrMails[] = array(
								'email'		=> DecodeEMail($address),
								'firstname'	=> $firstName,
								'lastname'	=> $lastName
							);
						}

						if($book)
							$tpl->assign('groups', $book->GetGroupList());
						$tpl->assign('addrMails', $addrMails);
						$tpl->assign('pageContent', 'li/email.sent.tpl');
					}

					//
					// save copy
					//
					$mailID = 0;
					$saveTo = $_REQUEST['do'] == 'saveDraft'
						? FOLDER_DRAFTS
						: (int)$_REQUEST['savecopy'];
					$mailObj = _new('BMMail', array(0, false, $outboxFP, false));
					$mailObj->Parse();
					$mailObj->ParseInfo();
					if($_REQUEST['do'] == 'saveDraft' && isset($_REQUEST['autoSave']))
						$mailObj->flags |= FLAG_AUTOSAVEDDRAFT;
					if($saveTo != -128)
					{
						$mailbox->StoreMail($mailObj, $saveTo);
						$mailID = $mailbox->_lastInsertId;

						if($_REQUEST['do'] != 'saveDraft' && !isset($_REQUEST['certMail']) && is_object($mail))
							$mail->SetDeliveryStatusOutboxID($mailID);
					}

					//
					// certified mail?
					//
					if(isset($_REQUEST['certMail'])
						&& (!isset($_REQUEST['do']) || $_REQUEST['do'] != 'saveDraft')
						&& $mailID)
					{
						$mailbox->SendCertMail($mailID, $mailObj);
					}

					//
					// train as NON-spam
					//
					if($userRow['spamfilter'] == 'yes' && $bm_prefs['use_bayes'] == 'yes'
						&& $userRow['unspamme'] == 'yes'
						&& $_REQUEST['do'] != 'saveDraft')
					{
						$mailbox->SetSpamStatus($mailbox->_lastInsertId, false);
					}

					//
					// clean up
					//
					$mail->CleanUp();
					if(!isset($_REQUEST['autoSave']))
					{
						foreach($attTempFiles as $attData)
						{
							list($tempFileID, $tempFileFP) = $attData;
							fclose($tempFileFP);
							ReleaseTempFile($userRow['id'], $tempFileID);
						}
					}

					//
					// delete old draft, if required
					//
					if($deleteDraftAfterSend > 0)
						$mailbox->DeleteMail($deleteDraftAfterSend, true);

					//
					// draft folder?
					//
					if($_REQUEST['do'] == 'saveDraft')
					{
						if(isset($_REQUEST['autoSave']))
						{
							echo $mailID;
							exit();
						}
						else
						{
							header('Location: email.php?folder=' . FOLDER_DRAFTS . '&sid=' . session_id());
							exit();
						}
					}
				}
				else
				{
					$tpl->assign('msg', $lang_user['sendfailed']);
					$tpl->assign('pageContent', 'li/error.tpl');
				}
			}
		}
		else
		{
			$tpl->assign('msg', $lang_user['norecipients']);
			$tpl->assign('pageContent', 'li/error.tpl');
		}
	}

	$tpl->assign('pageTitle', $lang_user['sendmail']);
	$tpl->display('li/index.tpl');
}

/**
 * get signature RPC
 */
else if($_REQUEST['action'] == 'getSignature'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['mode']))
{
	$signature = $thisUser->GetSignature((int)$_REQUEST['id']);

	if($signature)
		if($_REQUEST['mode'] == 'html')
		{
			if(trim(strip_tags($signature['html'])) != '')
				echo($signature['html']);
			else
				echo(nl2br(HTMLFormat($signature['text'])));
		}
		else
		{
			if(trim($signature['text']) != '')
				echo($signature['text']);
			else
				echo(htmlToText($signature['html']));
		}
	exit();
}

/**
 * check S/MIME params RPC
 */
else if($_REQUEST['action'] == 'checkSMIMEParams'
		&& isset($_REQUEST['sign'])
		&& isset($_REQUEST['encrypt'])
		&& isset($_REQUEST['from'])
		&& isset($_REQUEST['to'])
		&& isset($_REQUEST['cc'])
		&& isset($_REQUEST['bcc']))
{
	$senderAddresses 	= $thisUser->GetPossibleSenders();
	$sign 				= $_REQUEST['sign'] == '1';
	$encrypt			= $_REQUEST['encrypt'] == '1';
	$recipients 		= ExtractMailAddresses(_unescape($_REQUEST['to'] . ' ' . $_REQUEST['cc'] . ' ' . $_REQUEST['bcc']));
	$sender				= isset($senderAddresses[$_REQUEST['from']])
							? ExtractMailAddress($senderAddresses[$_REQUEST['from']])
							: ExtractMailAddress($senderAddresses[0]);

	// check recicpient count
	if(count($recipients) < 1)
	{
		echo($lang_user['smimeerr0']);
		exit();
	}

	// check PK
	if($sign)
	{
		$missing = $thisUser->GetRecipientsWithMissingCertificate(array($sender), CERTIFICATE_TYPE_PRIVATE);

		if(count($missing) > 0)
		{
			echo($lang_user['smimeerr1']);
			exit();
		}
	}

	// check if certs for recipients exist
	if($encrypt)
	{
		$missing = $thisUser->GetRecipientsWithMissingCertificate($recipients);

		if(count($missing) > 0)
		{
			echo($lang_user['smimeerr2'] . "\n\n  - ");
			echo(implode("\n  - ", $missing));
			exit();
		}
	}

	// OK
	echo('1');
	exit();
}
