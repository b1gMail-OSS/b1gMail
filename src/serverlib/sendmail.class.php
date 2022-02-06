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

/**
 * mail sending system
 *
 */
class BMSendMail
{
	var $_recipients;
	var $_sender;
	var $_mailFrom;
	var $_subject;
	var $_fp;
	var $_userID;
	var $_smtp;

	/**
	 * constructor
	 *
	 * @return SendMail
	 */
	function __construct()
	{
		$this->_recipients = array();
		$this->_sender = '';
		$this->_subject = '';
		$this->_mailFrom = false;
		$this->_userID = USERID_UNKNOWN;
		$this->_smtp = false;
	}

	/**
	 * set sender user ID
	 *
	 * @param int $userID
	 */
	function SetUserID($userID)
	{
		$this->_userID = $userID;
	}

	/**
	 * set recipients
	 *
	 * @param mixed $recipients Recipient(s) (array or string)
	 */
	function SetRecipients($recipients)
	{
		if(!is_array($recipients))
		{
			if(($recipient = ExtractMailAddress($recipients)) != '')
				$this->_recipients = array($recipient);
		}
		else
		{
			$this->_recipients = array();
			foreach($recipients as $recipient)
				if(($recipient = ExtractMailAddress($recipient)) != '')
					$this->_recipients[] = $recipient;
		}
	}

	/**
	 * set sender
	 *
	 * @param string $sender
	 */
	function SetSender($sender)
	{
		$this->_sender = ExtractMailAddress($sender);
	}

	/**
	 * set mail from address
	 *
	 * @param string $sender
	 */
	function SetMailFrom($sender)
	{
		PutLog(sprintf('Differing mail from address set: <%s>', ExtractMailAddress($sender)),
			PRIO_DEBUG,
			__FILE__,
			__LINE__);
		$this->_mailFrom = ExtractMailAddress($sender);
	}

	/**
	 * set subject
	 *
	 * @param string $subject
	 */
	function SetSubject($subject)
	{
		$this->_subject = $subject;
	}

	/**
	 * set body stream
	 *
	 * @param resource $fp
	 */
	function SetBodyStream($fp)
	{
		$this->_fp = $fp;
	}

	/**
	 * send the mail
	 *
	 * @return bool
	 */
	function Send()
	{
		global $bm_prefs;

		if(count($this->_recipients) == 0)
			return(false);

		// send using mail()...
		if($bm_prefs['send_method'] == 'php')
			return($this->_sendUsingPHPMail());

		// ...or send using SMTP
		else if($bm_prefs['send_method'] == 'smtp')
			return($this->_sendUsingSMTP());

		// ...or send using sendmail
		else if($bm_prefs['send_method'] == 'sendmail')
			return($this->_sendUsingSendmail());
	}

	/**
	 * send mail using sendmail
	 *
	 * @return bool
	 */
	function _sendUsingSendmail()
	{
		global $bm_prefs;

		// build command
		$command = sprintf('%s -f "%s" %s',
			$bm_prefs['sendmail_path'],
			addslashes($this->_mailFrom !== false ? $this->_mailFrom : $this->_sender),
			addslashes(implode(' ', $this->_recipients)));

		// open
		$fp = popen($command, 'wb');
		if(!is_resource($fp))
		{
			PutLog(sprintf('Failed to execute sendmail command <%s>',
				$command),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

		// send
		fseek($this->_fp, 0, SEEK_SET);
		while(is_resource($this->_fp) && !feof($this->_fp) && ($line = fgets2($this->_fp)))
			fwrite($fp, rtrim($line, "\r\n") . "\n");

		// close
		return(pclose($fp) == 0);
	}

	/**
	 * send mail using PHP mail()
	 *
	 * @return bool
	 */
	function _sendUsingPHPMail()
	{
		// line separators
		if(substr(PHP_OS, 0, 3) == 'WIN')
			$headerEOL = "\r\n";
		else
			$headerEOL = "\n";

		// get mail
		$messageHeader = $messageBody = '';
		$inBody = false;
		fseek($this->_fp, 0, SEEK_SET);
		while(is_resource($this->_fp) && !feof($this->_fp))
		{
			$line = rtrim(fgets2($this->_fp), "\r\n");
			if(!$inBody && $line == '')
				$inBody = true;
			else
			{
				if($inBody)
					$messageBody .= $line . "\n";
				else if(substr($line, 0, 4) != 'To: '
					&& substr($line, 0, 9) != 'Subject: ')
					$messageHeader .= $line . $headerEOL;
			}
		}

		// send mail!
		if(ini_get('safe_mode'))
			$result = mail($this->_recipients[0],
				EncodeMailHeaderField($this->_subject),
				$messageBody,
				$messageHeader);
		else
			$result = mail($this->_recipients[0],
				EncodeMailHeaderField($this->_subject),
				$messageBody,
				$messageHeader,
				'-f "' . ($this->_mailFrom !== false ? $this->_mailFrom : $this->_sender) . '"');

		// return
		return($result);
	}

	/**
	 * send mail using SMTP
	 *
	 * @return bool
	 */
	function _sendUsingSMTP()
	{
		global $bm_prefs;

		// load class, if needed
		if(!class_exists('BMSMTP'))
			include(B1GMAIL_DIR . 'serverlib/smtp.class.php');

		// send using SMTP
		$this->_smtp = _new('BMSMTP', array($bm_prefs['smtp_host'], $bm_prefs['smtp_port'], $bm_prefs['b1gmta_host']));
		$this->_smtp->SetUserID($this->_userID);
		if($this->_smtp->Connect())
		{
			// login
			if($bm_prefs['smtp_auth'] == 'yes')
				$this->_smtp->Login($bm_prefs['smtp_user'], $bm_prefs['smtp_pass']);

			// submit mail
			if($this->_smtp->StartMail($this->_mailFrom !== false ? $this->_mailFrom : $this->_sender, $this->_recipients))
				$ok = $this->_smtp->SendMail($this->_fp);
			else
				$ok = false;

			// disconnect
			$this->_smtp->Disconnect();

			// return
			return($ok);
		}

		// return
		return(false);
	}

	/**
	 * associate sent mail with an outbox mail ID
	 *
	 */
	function SetDeliveryStatusOutboxID($outboxID)
	{
		if($this->_smtp !== false)
		{
			$this->_smtp->SetDeliveryStatusOutboxID($outboxID);
		}
	}
}
