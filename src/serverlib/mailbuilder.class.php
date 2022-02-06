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

if(!class_exists('BMSendMail'))
	include(B1GMAIL_DIR . 'serverlib/sendmail.class.php');

/**
 * mail builder class
 *
 */
class BMMailBuilder
{
	var $_smimeSkipHeaders = array('from', 'to', 'cc', 'bcc', 'date', 'message-id', 'x-mailer',
									'reply-to', 'subject', 'x-priority', 'in-reply-to',
									'x-sender-ip', 'disposition-notification-to', 'references');
	var $_tempID;
	var $_fp;
	var $_headerFields;
	var $_parts;
	var $_trustedMail;
	var $_mailFrom = false;
	var $_smimeUser = false;
	var $_smimeSign = false;
	var $_smimeEncrypt = false;
	var $_smimeHeaders = array();
	var $_userID;
	var $_sendMail;

	/**
	 * constructor
	 *
	 */
	function __construct($trustedMail = false)
	{
		// init
		$this->_fp = false;
		$this->_headerFields = array();
		$this->_parts = array();
		$this->_initHeaderFields();
		$this->_trustedMail = $trustedMail;
		$this->_userID = USERID_UNKNOWN;
		$this->_sendMail = false;
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
	 * set differing mail from adress
	 *
	 * @param string $address Address
	 */
	function SetMailFrom($address)
	{
		$this->_mailFrom = ExtractMailAddress($address);
	}

	/**
	 * add header field
	 *
	 * @param string $key
	 * @param string $value
	 */
	function AddHeaderField($key, $value)
	{
		$this->_headerFields[$key] = $value;
	}

	/**
	 * add text part
	 *
	 * @param mixed $data Data (file pointer or string)
	 * @param string $type Text type (plain or html)
	 * @param string $charset Text charset
	 */
	function AddText($data, $type = 'plain', $charset = 'iso-8859-1', $additionalParams = '')
	{
		$cte = '8bit';

		if(!is_resource($data) && is_string($data))
		{
			$data = str_replace("\n", "\r\n", str_replace("\r", '', $data));

			if(function_exists('quoted_printable_encode'))
			{
				$data = quoted_printable_encode($data);
				$cte = 'quoted-printable';
			}
			else
			{
				$data = _wordwrap($data, 900, "\r\n", false);
			}
		}

		$this->_parts[] = array(
			'data'			=> $data,
			'base64'		=> false,
			'headerFields'	=> array(
				'Content-Type'					=> sprintf('text/%s; charset="%s"%s', $type, $charset, $additionalParams),
				'Content-Transfer-Encoding'		=> $cte,
				'Content-Disposition'			=> 'inline'
			)
		);
	}

	/**
	 * add an attachment
	 *
	 * @param mixed $data Data (file pointer or string)
	 * @param string $contentType Content type
	 * @param string $fileName File name
	 * @param string $contentDisposition Content disposition (attachment or inline)
	 */
	function AddAttachment($data, $contentType, $fileName, $contentDisposition = 'attachment')
	{
		$this->_parts[] = array(
			'data'			=> $data,
			'base64'		=> $contentType != 'message/rfc822',
			'noHeaderEncode'=> true,
			'headerFields'	=> array(
				'Content-Type'					=> sprintf('%s; name="%s"', $contentType, EncodeMailHeaderField($fileName)),
				'Content-Transfer-Encoding'		=> $contentType == 'message/rfc822' ? '8bit' : 'base64',
				'Content-Disposition'			=> sprintf('%s; filename="%s"', $contentDisposition, EncodeMailHeaderField($fileName))
			)
		);
	}

	/**
	 * build the mail
	 *
	 * @return resource
	 */
	function Build($forSending = false, $recipients = false)
	{
		// add trust signature?
		if($this->_trustedMail)
		{
			$this->AddHeaderField('X-b1gMail-Trusted',
				GenerateTrustToken($this->_headerFields['Message-ID'],
									$this->_headerFields['From'],
									$this->_headerFields['To'],
									$this->_headerFields['Subject']));
		}

		// create stream
		$this->_tempID = RequestTempFile(-1, -1, true);
		$this->_fp = fopen(TempFileName($this->_tempID), 'wb+');
		assert('is_resource($this->_fp)');

		// multipart?
		if(count($this->_parts) > 1)
		{
			$multiPart = true;
			$boundary = '--Boundary-=_' .  GenerateRandomKey('mimeBoundary');
			$this->AddHeaderField('Content-Type', sprintf('multipart/mixed; boundary="%s"', $boundary));
		}
		else if(count($this->_parts) == 1)
		{
			$multiPart = false;
			$this->_headerFields = array_merge($this->_headerFields, $this->_parts[0]['headerFields']);
		}
		else
		{
			return(false);
		}

		// write header fields
		$this->_writePartHeader($this->_headerFields, true);

		// write parts
		if($multiPart)
		{
			fwrite($this->_fp, 'This is a multi-part message in MIME format.' . "\r\n");
			foreach($this->_parts as $part)
			{
				fwrite($this->_fp, "\r\n" . '--' . $boundary . "\r\n");
				$this->_writePartHeader($part['headerFields'], false, !isset($part['noHeaderEncode']));
				$this->_writePartBody($part);
			}
			fwrite($this->_fp, "\r\n" . '--' . $boundary . '--' . "\r\n");
		}
		else
		{
			$this->_writePartBody($this->_parts[0]);
		}

		// s/mime?
		if($this->_smimeUser !== false && ($this->_smimeSign || $this->_smimeEncrypt))
		{
			if(!class_exists('BMSMIME'))
				include(B1GMAIL_DIR . 'serverlib/smime.class.php');

			$smime = _new('BMSMIME', array($this->_smimeUser->_id, &$this->_smimeUser));

			if($this->_smimeSign)
			{
				if(!$smime->SignMail($this))
				{
					PutLog(sprintf('Failed to sign S/MIME message for user <%s> (#%d)',
						$this->_smimeUser->_row['email'],
						$this->_smimeUser->_id),
						PRIO_NOTE,
						__FILE__,
						__LINE__);
					return(false);
				}
			}

			if($this->_smimeEncrypt)
			{
				if(!$smime->EncryptMail($this, $recipients))
				{
					PutLog(sprintf('Failed to encrypt S/MIME message for user <%s> (#%d)',
						$this->_smimeUser->_row['email'],
						$this->_smimeUser->_id),
						PRIO_NOTE,
						__FILE__,
						__LINE__);
					return(false);
				}
			}
		}

		// return file pointer
		fseek($this->_fp, 0, SEEK_SET);

		return($this->_fp);
	}

	/**
	 * send the mail
	 *
	 * @return resource File pointer of sent mail
	 */
	function Send()
	{
		global $bm_prefs;

		// prepare sender
		if(isset($this->_headerFields['Return-Path']))
			$sender = ExtractMailAddress($this->_headerFields['Return-Path']);
		else if(isset($this->_headerFields['From']))
			$sender = ExtractMailAddress($this->_headerFields['From']);
		else
			$sender = '';

		// prepare recipients
		$recpString = '';
		if(isset($this->_headerFields['To']))
			$recpString .= $this->_headerFields['To'] . ' ';
		if(isset($this->_headerFields['Cc']))
			$recpString .= $this->_headerFields['Cc'] . ' ';
		if(isset($this->_headerFields['Bcc']))
			$recpString .= $this->_headerFields['Bcc'];
		$recipients = ExtractMailAddresses($recpString);
		unset($this->_headerFields['Bcc']);

		// build, if needed
		if($this->Build(true, $recipients) === false || !is_resource($this->_fp))
			return(false);

		// load class, if needed
		if(!class_exists('BMSendMail'))
			include(B1GMAIL_DIR . 'serverlib/sendmail.class.php');

		// send
		$this->_sendMail = _new('BMSendMail');
		$this->_sendMail->SetUserID($this->_userID);
		$this->_sendMail->SetSender($sender);
		if($this->_mailFrom !== false)
			$this->_sendMail->SetMailFrom($this->_mailFrom);
		$this->_sendMail->SetRecipients($recipients);
		$this->_sendMail->SetSubject(isset($this->_headerFields['Subject'])
			? $this->_headerFields['Subject']
			: '(no subject)');
		$this->_sendMail->SetBodyStream($this->_fp);

		// return
		fseek($this->_fp, 0, SEEK_SET);
		if($this->_sendMail->Send())
		{
			fseek($this->_fp, 0, SEEK_SET);
			return($this->_fp);
		}
		else
		{
			fseek($this->_fp, 0, SEEK_SET);
			return(false);
		}
	}

	/**
	 * clean up
	 *
	 */
	function CleanUp()
	{
		if($this->_fp !== false)
			fclose($this->_fp);
		ReleaseTempFile(-1, $this->_tempID);
	}

	/**
	 * associate sent mail with an outbox mail ID
	 *
	 */
	function SetDeliveryStatusOutboxID($outboxID)
	{
		if($this->_sendMail !== false)
		{
			$this->_sendMail->SetDeliveryStatusOutboxID($outboxID);
		}
	}

	/**
	 * write part header
	 *
	 * @param array $headerFields Header fields
	 * @param bool $rootHeaders Root headers?
	 */
	function _writePartHeader($headerFields, $rootHeaders = false, $doEncode = true)
	{
		foreach($headerFields as $key=>$value)
		{
			if($doEncode)
				$value = EncodeMailHeaderField($value);

			if((!$this->_smimeEncrypt
				&& !$this->_smimeSign)
				|| !in_array(strtolower($key), $this->_smimeSkipHeaders))
			{
				fwrite($this->_fp, sprintf('%s: %s' . "\r\n", $key, wordwrap($value, 72, "\r\n\t")));
			}
			else
			{
				$this->_smimeHeaders[$key] = $value;
			}
		}
		fwrite($this->_fp, "\r\n");
	}

	/**
	 * write part body
	 *
	 * @param array $part Part array
	 */
	function _writePartBody($part)
	{
		// encode
		if($part['base64'])
		{
			if(!is_resource($part['data']))
				fwrite($this->_fp, wordwrap(base64_encode($part['data']), 72, "\r\n", true));
			else
			{
				fseek($part['data'], 0, SEEK_SET);
				while(!feof($part['data']))
				{
					$data = base64_encode(fread($part['data'], 54));
					fwrite($this->_fp, $data . "\r\n");
				}
			}
		}

		// just copy
		else
		{
			if(!is_resource($part['data']))
				fwrite($this->_fp, $part['data']);
			else
			{
				fseek($part['data'], 0, SEEK_SET);
				while(!feof($part['data']) && ($data = fread($part['data'], 4096)))
					fwrite($this->_fp, $data);
			}
		}
	}

	/**
	 * generate a message id
	 *
	 * @return string
	 */
	function _generateMessageID()
	{
		global $bm_prefs;
		return(sprintf('<%s@%s>',
			GenerateRandomKey('messageID'),
			$bm_prefs['b1gmta_host']));
	}

	/**
	 * initialize header fields
	 *
	 */
	function _initHeaderFields()
	{
		$this->AddHeaderField('Date',			date('r'));
		$this->AddHeaderField('MIME-Version',	'1.0');
		$this->AddHeaderField('Message-ID',		$this->_generateMessageID());
		$this->AddHeaderField('X-Mailer',		'b1gMail/' . B1GMAIL_VERSION);
	}
}
