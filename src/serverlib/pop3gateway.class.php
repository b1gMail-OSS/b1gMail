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

if(!class_exists('BMPOP3'))
	include(B1GMAIL_DIR . 'serverlib/pop3.class.php');
if(!class_exists('BMMailProcessor'))
	include(B1GMAIL_DIR . 'serverlib/mailprocessor.class.php');

/**
 * pop3 gateway
 *
 */
class BMPOP3Gateway
{
	var $_pop3;

	/**
	 * run pop3 fetcher
	 *
	 * @param $maxMails Max mails to process
	 * @return array Mail count, processed mail count
	 */
	function Run($maxMails = -1)
	{
		// connect
		if(!$this->ConnectToPOP3Box())
			return(false);

		// process mails
		$result = $this->ProcessMails($maxMails);

		// disconnect
		$this->_pop3->Disconnect();

		// return
		return($result);
	}

	/**
	 * process mails
	 *
	 * @param $maxMails Max mails to process
	 * @return array Mail count, processed mail count
	 */
	function ProcessMails($maxMails = -1)
	{
		global $bm_prefs;

		// get mail list
		$mailList = $this->_pop3->GetMailList();
		if(!is_array($mailList))
		{
			PutLog('Failed to retrieve mail list from catchall POP3 server',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
		}

		// walk through mails
		$i = 0;
		foreach($mailList as $mailNum=>$mailInfo)
		{
			// request temp file
			$tempFileID = RequestTempFile(0, -1, true);
			$tempFileName = TempFileName($tempFileID);
			$tempFileFP = fopen($tempFileName, 'wb+');
			assert('is_resource($tempFileFP)');

			// too big?
			if($mailInfo['size'] > $bm_prefs['mailmax'])
			{
				// yes -> log, process headers and bounce
				PutLog(sprintf('Message too big (hard limit; %d > %d bytes) - processing headers only',
					$mailInfo['size'],
					$bm_prefs['mailmax']),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
				$this->_pop3->RetrieveMailHeaders($mailNum, $tempFileFP);
				$failProcessing = STORE_RESULT_MAILTOOBIG;
			}
			else
			{
				// no -> process normally
				$this->_pop3->RetrieveMail($mailNum, $tempFileFP);
				$failProcessing = STORE_RESULT_OK;
			}

			// process mail
			$mailProcessor = _new('BMMailProcessor', array($tempFileFP));
			$mailProcessor->ProcessMail($failProcessing);

			// clean up
			fclose($tempFileFP);
			ReleaseTempFile(0, $tempFileID);

			// increment processed mail count
			$i++;

			// delete mail
			$this->_pop3->DeleteMail($mailNum);

			// do not process too many mails
			if($i == ($maxMails == -1 ? $bm_prefs['fetchcount'] : $maxMails))
				break;
		}

		// return mail count + count of processed mails
		return(array(count($mailList), $i));
	}

	/**
	 * connect to pop3 box
	 *
	 * @return bool
	 */
	function ConnectToPOP3Box()
	{
		global $bm_prefs;

		// connect
		$this->_pop3 = _new('BMPOP3', array($bm_prefs['pop3_host'], $bm_prefs['pop3_port']));
		if(!$this->_pop3->Connect())
		{
			PutLog(sprintf('Connection to catchall POP3 server <%s:%d> failed',
				$bm_prefs['pop3_host'],
				$bm_prefs['pop3_port']),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

		// login
		if(!$this->_pop3->Login($bm_prefs['pop3_user'], $bm_prefs['pop3_pass']))
		{
			PutLog('Login at catchall POP3 server failed',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

		return(true);
	}
}
