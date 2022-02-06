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
	die('Directly calling this file is not supported');

if(!class_exists('BMMail'))
	include(B1GMAIL_DIR . 'serverlib/mail.class.php');
if(!class_exists('BMMailbox'))
	include(B1GMAIL_DIR . 'serverlib/mailbox.class.php');
if(!class_exists('BMMailBuilder'))
	include(B1GMAIL_DIR . 'serverlib/mailbuilder.class.php');

/**
 * Mail processing system
 *
 */
class BMMailProcessor
{
	var $_fp;
	var $_recipients = array();
	var $_targetFolder;
	var $_mail;
	var $_aliasTable;
	var $bmsFlags;
	var $disableBounces;
	var $isUserPOP3;

	/**
	 * constructor
	 *
	 * @param resource $fp Pointer to mail file
	 * @return BMMailProcessor
	 */
	function __construct($fp)
	{
		$this->isUserPOP3 = false;
		$this->disableBounces = false;
		$this->_fp = $fp;
		$this->_targetFolder = FOLDER_INBOX;
		$this->_aliasTable = array();
		$this->bmsFlags = 0;
	}

	/**
	 * set b1gMailServer mail flags
	 *
	 * @param int $flags Flags
	 */
	function Setb1gMailServerFlags($flags)
	{
		$this->bmsFlags = $flags;
	}

	/**
	 * set/override target folder
	 *
	 * @param int $folder Folder ID
	 */
	function SetTargetFolder($folder)
	{
		$this->_targetFolder = $folder;
	}

	/**
	 * set recipient list
	 *
	 * @param array $recipients Mail addresses
	 */
	function SetRecipients($recipients)
	{
		$this->_recipients = $recipients;
	}

	/**
	 * clear recipient list
	 *
	 */
	function ClearRecipients()
	{
		$this->_recipients = array();
	}

	/**
	 * add a recipient
	 *
	 * @param string $recipient Mail address
	 */
	function AddRecipient($recipient)
	{
		$this->_recipients[] = $recipient;
	}

	/**
	 * process internal mail
	 *
	 */
	function ProcessInternalMail($uid)
	{
		global $db;

		// read mail contents
		$mailContents = '';
		fseek($this->_fp, 0, SEEK_SET);
		while(is_resource($this->_fp) && !feof($this->_fp))
			$mailContents .= fread($this->_fp, 4096);

		// store mail in DB
		$db->Query('UPDATE {pre}testmails SET data=?, received=?, recv_count=recv_count+1 WHERE uid=?',
			$mailContents,
			time(),
			$uid);
	}

	/**
	 * process routine
	 *
	 */
	function ProcessMail($failProcessing = STORE_RESULT_OK)
	{
		global $db;

		// time measurement
		$mailboxTime = 0;
		$processingTime = microtime_float();

		// results
		$storeResult = RECEIVE_RESULT_NO_RECIPIENTS;
		$recipientErrors = array();

		// parse mail
		$this->_mail = _new('BMMail', array(0, false, $this->_fp, false));
		$this->_mail->Parse();

		// special internal message?
		/*if(($xb1gMailTestUID = trim($this->_mail->GetHeaderValue('x-b1gmail-test-uid'))) != '')
		{
			$res = $db->Query('SELECT COUNT(*) FROM {pre}testmails WHERE uid=?',
				$xb1gMailTestUID);
			list($rowCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			if($rowCount == 1)
			{
				$this->ProcessInternalMail($xb1gMailTestUID);
				return;
			}
		}*/

		// bounce because of $failProcessing?
		if($failProcessing != STORE_RESULT_OK)
		{
			// not delivered to one ore more recipients
			$this->Bounce($failProcessing, array());
			return;
		}

		// determine recipients
		$this->DetermineRecipients();

		// has recipients?
		if(count($this->_recipients) > 0)
		{
			// parse basic mail info
			$this->_mail->ParseInfo();

			// do some basic filtering
			if(($storeResult = $this->CommonFilters()) == STORE_RESULT_OK)
			{
				// this mail is unread
				if(($this->_mail->flags & FLAG_UNREAD) == 0)
					$this->_mail->flags |= FLAG_UNREAD;

				// process b1gMailServer flags
				if(($this->bmsFlags & BMSFLAG_IS_SPAM) != 0)
					$this->_mail->flags |= FLAG_SPAM;
				if(($this->bmsFlags & BMSFLAG_IS_INFECTED) != 0)
					$this->_mail->flags |= FLAG_INFECTED;

				// deliver mail
				$mailboxTime = microtime_float();
				foreach($this->_recipients as $recpMail=>$userID)
				{
					// create user object
					$userObject = _new('BMUser', array($userID));
					$userRow = $userObject->Fetch();
					$userMail = $userRow['email'];

					// open user's mailbox
					$mailbox = _new('BMMailbox', array($userID, $userMail, $userObject));

					// receive the mail
					if(isset($this->_aliasTable[$userID]))
						$aliasReceiver = $this->_aliasTable[$userID];
					else
						$aliasReceiver = false;
					$recipientResult = $mailbox->ReceiveMail($this->_mail, $this->_targetFolder, $aliasReceiver, $this->isUserPOP3);

					// check result
					if($recipientResult != STORE_RESULT_OK
						&& $recipientResult != RECEIVE_RESULT_DELETE)
						$recipientErrors[$recpMail] = $recipientResult;

					// clean up
					unset($mailbox);
					unset($userObject);
					unset($userRow);
					unset($userMail);
				}
				$mailboxTime = microtime_float() - $mailboxTime;
			}
		}
		else
		{
			ModuleFunction('OnMailWithoutValidRecipient', array(&$this->_mail));
		}

		// bounce?
		if(($storeResult != STORE_RESULT_OK || count($recipientErrors) > 0)
			&& $storeResult != RECEIVE_RESULT_DELETE)
		{
			// not delivered to one ore more recipients
			$this->Bounce($storeResult, $recipientErrors);
		}

		// log time
		$processingTime = microtime_float() - $processingTime;
		if(DEBUG)
			PutLog(sprintf('Processed mail in %.04f seconds (mailbox processing time: %.04f seconds)',
				$processingTime,
				$mailboxTime),
				PRIO_DEBUG,
				__FILE__,
				__LINE__);
	}

	/**
	 * bounce the message - send NDN
	 *
	 * @param int $storeResult Store result
	 * @param array $recipientErrors Erroneous recipients
	 */
	function Bounce($storeResult, $recipientErrors)
	{
		global $bm_prefs;

		if($this->disableBounces)
			return;

		$errorTable = array(
			STORE_RESULT_OK
				=> 'The message was not delivered to one or more recipients.',
			STORE_RESULT_INTERNALERROR
				=> 'A severe internal error occured while trying to process the message.',
			STORE_RESULT_MAILTOOBIG
				=> 'The message is too large. This server accepts messages with a limited size only.',
			STORE_RESULT_NOTENOUGHSPACE
				=> 'The user exceeded his storage limit. Please try to send the message again later.',
			RECEIVE_RESULT_BLOCKED
				=> 'The message was blocked by a content filter.',
			RECEIVE_RESULT_NO_RECIPIENTS
				=> 'No valid recipients for this message could be determined.'
		);

		//
		// send error mails?
		//
		if($bm_prefs['errormail'] == 'yes'
			|| ($bm_prefs['errormail'] == 'soft' && $storeResult != RECEIVE_RESULT_NO_RECIPIENTS))
		{
			$returnPath = ExtractMailAddress($this->_mail->GetHeaderValue('return-path'));
			$bounceSender = GetPostmasterMail();

			// check for return path
			if($returnPath != ''
				&& strpos(strtolower($this->_mail->GetHeaderValue('content-type')), 'delivery-status') === false)
			{
				// prepare text
				$text  = sprintf('The original message was received at %s.',
					date('r')) . "\r\n\r\n";
				$text .= '   ----- Error description -----' . "\r\n";
				$text .= $errorTable[$storeResult]. "\r\n\r\n";

				// recipient details
				if(count($recipientErrors) > 0)
				{
					$text .= '   ----- The message was not delivered to the following addresses -----' . "\r\n";
					foreach($recipientErrors as $recipientAddress=>$recipientResult)
						$text .= sprintf('<%s>' . "\r\n" . '   (reason: %s)' . "\r\n",
							$recipientAddress,
							$errorTable[$recipientResult]);
				}

				// send mail
				$mail = _new('BMMailBuilder');
				$mail->SetUserID(USERID_SYSTEM);
				$mail->AddHeaderField('Return-Path', 	'<>');
				$mail->AddHeaderField('To',				$returnPath);
				$mail->AddHeaderField('From',			'"Mail Delivery System" <' . $bounceSender . '>');
				$mail->AddHeaderField('Subject',		'Returned mail: see error report for details');
				$mail->AddHeaderField('Precedence',		'junk');
				$mail->AddText($text,
					'plain',
					'US-ASCII',
					'; report-type="delivery-status"');
				$mail->AddAttachment($this->_fp,
					'message/rfc822',
					'returned-message.eml',
					'attachment');
				$success = $mail->Send() !== false;
				$mail->CleanUp();

				// stats
				Add2Stat('sysmail');

				// log
				if($success)
					PutLog(sprintf('Sent delivery status notification about error %d (%d invalid recipients) to <%s>',
						$storeResult,
						count($recipientErrors),
						$returnPath),
						PRIO_NOTE,
						__FILE__,
						__LINE__);
				else
					PutLog(sprintf('Failed to send delivery status notification about error %d (%d invalid recipients) to <%s>',
						$storeResult,
						count($recipientErrors),
						$returnPath),
						PRIO_WARNING,
						__FILE__,
						__LINE__);
			}
			else
			{
				// log
				PutLog(sprintf('Sent no error mail to notify about error %d (%d invalid recipients): empty return-path',
					$storeResult,
					count($recipientErrors)),
					PRIO_DEBUG,
					__FILE__,
					__LINE__);
			}
		}

		//
		// do not send error mails => log
		//
		else
		{
			// log
			PutLog(sprintf('Sent no error mail to notify about error %d (%d invalid recipients): error reporting disabled for this error',
				$storeResult,
				count($recipientErrors)),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
		}
	}

	/**
	 * common filtering (enabled for every user)
	 *
	 */
	function CommonFilters()
	{
		global $bm_prefs, $plugins, $db;

		// process custom receive rules
		$recipients = array();
		$res = $db->Query('SELECT id,field,expression,action,value FROM {pre}recvrules WHERE type=? ORDER BY action,id ASC',
			RECVRULE_TYPE_CUSTOMRULE);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$ruleResult = false;
			if(($headerValue = trim($this->_mail->GetHeaderValue(strtolower($row['field'])))) != '')
			{
				$expression = $row['expression'];
				$expression = str_replace('%EMAILADDRESS',		'/[a-zA-Z0-9&\'\\.\\-_\\+]+@[a-zA-Z0-9.-]+\\.+[a-zA-Z]{2,12}/',
					$expression);
				$expression = str_replace('%MESSAGEID',			'/<([^>]+)>/',
					$expression);
				if($expression == '')
					$expression = '/(.+)/';
				$ruleResult = @preg_match_all($expression, $headerValue, $subPatterns, PREG_SET_ORDER);

				if($ruleResult > 0)
				{
					$resultValues = array();
					foreach($subPatterns as $subPattern)
						$resultValues[] = $subPattern[$row['value']];

					if($row['action'] == RECVRULE_ACTION_BOUNCE)
					{
						PutLog(sprintf('Mail bounced because of receive rule <%d>',
							$row['id']),
							PRIO_NOTE,
							__FILE__,
							__LINE__);
						return(RECEIVE_RESULT_BLOCKED);
					}
					else if($row['action'] == RECVRULE_ACTION_DELETE)
					{
						PutLog(sprintf('Mail deleted because of receive rule <%d>',
							$row['id']),
							PRIO_NOTE,
							__FILE__,
							__LINE__);
						return(RECEIVE_RESULT_DELETE);
					}
					else if($row['action'] == RECVRULE_ACTION_MARKINFECTED
							&& ($this->_mail->flags & FLAG_INFECTED) == 0)
					{
						$this->_mail->flags |= FLAG_INFECTED;
					}
					else if($row['action'] == RECVRULE_ACTION_MARKREAD
							&& ($this->_mail->flags & FLAG_UNREAD) != 0)
					{
						$this->_mail->flags &= ~(FLAG_UNREAD);
					}
					else if($row['action'] == RECVRULE_ACTION_MARKSPAM
							&& ($this->_mail->flags & FLAG_SPAM) == 0)
					{
						$this->_mail->flags |= FLAG_SPAM;
					}
					else if($row['action'] == RECVRULE_ACTION_SETINFECTION)
					{
						$this->_mail->infection = $resultValues[0];
					}
				}
			}
		}
		$res->Free();

		// collect filter modules
		if(!class_exists('BMMailFilter'))
			include(B1GMAIL_DIR . 'serverlib/filters.inc.php');
		$filterModules = array();
		if($bm_prefs['spamcheck'] == 'yes')
			$filterModules[] = _new('BMMailFilter_DNSBL', array(&$this->_mail));
		if($bm_prefs['use_bayes'] == 'yes' && $bm_prefs['bayes_mode'] == 'global')
			$filterModules[] = _new('BMMailFilter_Bayes', array(&$this->_mail));
		if($bm_prefs['use_clamd'] == 'yes')
			$filterModules[] = _new('BMMailFilter_ClamAV', array(&$this->_mail));

		// run filters
		foreach($filterModules as $filter)
			$filter->Filter();

		// ok
		return(STORE_RESULT_OK);
	}

	/**
	 * parse mail recipients
	 *
	 */
	function ParseRecipients()
	{
		global $bm_prefs, $db;

		// the old way
		if($bm_prefs['recipient_detection'] == 'static')
		{
			// x-b1gmail-to?
			$xb1gMailTo = ExtractMailAddress($this->_mail->GetHeaderValue('x-b1gmail-to'));
			if($xb1gMailTo != '' && BMUser::GetID($xb1gMailTo, false) != 0)
			{
				$this->_recipients = array($xb1gMailTo);
				return;
			}

			// x-forward-to?
			$xForwardTo = ExtractMailAddress($this->_mail->GetHeaderValue('x-forward-to'));
			if($xForwardTo != '' && BMUser::GetID($xForwardTo) != 0)
			{
				$this->_recipients = array($xForwardTo);
				return;
			}

			// x-forwarded-to?
			$xForwardedTo = ExtractMailAddress($this->_mail->GetHeaderValue('x-forwarded-to'));
			if($xForwardedTo != '' && BMUser::GetID($xForwardedTo) != 0)
			{
				$this->_recipients = array($xForwardedTo);
				return;
			}

			// try to get recipient from other suitable header fields
			$recipients = ExtractMailAddresses($this->_mail->GetHeaderValue('to'));
			$recipients = array_merge($recipients,
							ExtractMailAddresses($this->_mail->GetHeaderValue('cc')));
			$recipients = array_merge($recipients,
							ExtractMailAddresses($this->_mail->GetHeaderValue('bcc')));
			$recipients = array_merge($recipients,
							ExtractMailAddresses($this->_mail->GetHeaderValue('delivered-to')));
			$recipients = array_merge($recipients,
							ExtractMailAddresses($this->_mail->GetHeaderValue('envelope-to')));
			$recipients = array_merge($recipients,
							ExtractMailAddresses($this->_mail->GetHeaderValue('apparently-to')));
			$recipients = array_merge($recipients,
							ExtractMailAddresses($this->_mail->GetHeaderValue('x-original-to')));
			$recipients = array_merge($recipients,
							ExtractMailAddresses($this->_mail->GetHeaderValue('webde-forward')));
			$this->_recipients = array();
			foreach($recipients as $recipient)
				if(!in_array(strtolower($recipient), $this->_recipients))
					$this->_recipients[] = strtolower($recipient);
			return;
		}

		// the new way
		else if($bm_prefs['recipient_detection'] == 'dynamic')
		{
			// process rules
			$recipients = array();
			$res = $db->Query('SELECT field,expression,action,value FROM {pre}recvrules WHERE type=? ORDER BY action,id ASC',
				RECVRULE_TYPE_RECEIVERULE);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$ruleResult = false;
				if(($headerValue = trim($this->_mail->GetHeaderValue(strtolower($row['field'])))) != '')
				{
					$headerValue = str_replace(array("\r","\n"), '', $headerValue);
					$expression = $row['expression'];
					$expression = str_replace('%EMAILADDRESS',		'/[a-zA-Z0-9&\'\\.\\-_\\+]+@[a-zA-Z0-9.-]+\\.+[a-zA-Z]{2,12}/',
						$expression);
					$expression = str_replace('%MESSAGEID',			'/<([^>]+)>/',
						$expression);
					if($expression == '')
						$expression = '/(.+)/';
					$ruleResult = @preg_match_all($expression, $headerValue, $subPatterns, PREG_SET_ORDER);

					if($ruleResult > 0)
					{
						$resultValues = array();
						foreach($subPatterns as $subPattern)
							$resultValues[] = $subPattern[$row['value']];

						if($row['action'] == RECVRULE_ACTION_ISRECIPIENT)
						{
							$recipients = array(ExtractMailAddress($resultValues[0]));
							break;
						}
						else if($row['action'] == RECVRULE_ACTION_SETRECIPIENT)
						{
							if(BMUser::GetID(ExtractMailAddress($resultValues[0])) != 0
								|| BMWorkgroup::GetIDbyMail(ExtractMailAddress($resultValues[0])) != 0)
							{
								$recipients = array(ExtractMailAddress($resultValues[0]));
								break;
							}
						}
						else if($row['action'] == RECVRULE_ACTION_ADDRECIPIENT)
						{
							$recipients = array_merge($recipients,
								ExtractMailAddresses(implode(' ', $resultValues)));
						}
					}
				}
			}
			$res->Free();

			// transfer recipients
			$this->_recipients = array();
			foreach($recipients as $recipient)
				if(!in_array(strtolower($recipient), $this->_recipients))
					$this->_recipients[] = strtolower($recipient);
		}
	}

	/**
	 * determine recipients
	 *
	 */
	function DetermineRecipients()
	{
		global $bm_prefs;

		$recipients = array();

		// parse recps, if none given
		if(count($this->_recipients) == 0)
			$this->ParseRecipients();

		// remove non-existing / double recipients
		foreach($this->_recipients as $address)
		{
			$address = ExtractMailAddress($address);
			if($address != '' && BMUser::AddressValid($address, false))
			{
				// user?
				if(($userID = BMUser::GetID($address, true, $isAlias)) != 0)
				{
					$recipients[$address] = $userID;
					if($isAlias)
						$this->_aliasTable[$userID] = $address;
				}

				// workgroup?
				else if(($workgroupID = BMWorkgroup::GetIDbyMail($address)) != 0)
				{
					$workgroupMembers = BMWorkgroup::GetMembers($workgroupID, true);
					foreach($workgroupMembers as $workgroupMember)
					{
						$recipients[$workgroupMember['email']] = $workgroupMember['id'];
						$this->_aliasTable[$workgroupMember['id']] = $workgroupMember['email'];
					}
				}
			}
		}

		// forward to postmaster?
		if(count($recipients) == 0
			&& $bm_prefs['failure_forward'] == 'yes'
			&& ($postmasterID = BMUser::GetID($postmasterAddress = GetPostmasterMail(), true)) != 0)
		{
			// log
			PutLog(sprintf('Forwarding mail to postmaster (<%s>) because no valid recipients were found',
				$postmasterAddress),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			$recipients[$postmasterAddress] = $postmasterID;
		}

		// set recipients
		$this->_recipients = $recipients;
	}
}
