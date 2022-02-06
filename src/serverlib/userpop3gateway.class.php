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

define('BMUSERPOP3_TIMEOUT', 15);

/**
 * user pop3 gateway
 *
 */
class BMUserPOP3Gateway
{
	var $_pop3;
	var $_userID;
	var $_userObject;
	var $_groupRow;
	var $_account;

	/**
	 * constructor
	 *
	 * @param int $userID
	 * @param BMUser $userObject
	 * @return BMUserPOP3Gateway
	 */
	function __construct($userID, &$userObject)
	{
		$this->_userID = $userID;
		$this->_userObject = &$userObject;
		$group = $this->_userObject->GetGroup();
        	$this->_groupRow = $group->Fetch();
	}

	/**
	 * run pop3 fetcher
	 *
	 * @param $maxMails Max mails to process
	 * @return array Mail count, processed mail count
	 */
	function Run($maxMails = -1)
	{
		global $db;
		$result = true;

		$pop3Accounts = $this->_userObject->GetPOP3Accounts('last_fetch', 'ASC', true);
		foreach($pop3Accounts as $accountID=>$account)
		{
			$this->_account = $account;
			if($account['last_fetch']+$this->_groupRow['ownpop3_interval'] < time())
			{
				$partResult = true;

				// update last fetch
				$db->Query('UPDATE {pre}pop3 SET last_fetch=?,last_success=? WHERE id=?',
					time(),
					-1,
					$this->_account['id']);

				// connect
				if(!$this->ConnectToPOP3Box($account))
					$partResult = false;

				// process mails
				if($partResult)
				{
					if($this->ProcessMails($maxMails) === false)
						$partResult = false;

					// disconnect
					$this->_pop3->Disconnect();
				}

				// update last fetch
				$db->Query('UPDATE {pre}pop3 SET last_fetch=?,last_success=? WHERE id=?',
					time(),
					$partResult ? 1 : 0,
					$this->_account['id']);

				if(!$partResult)
					$result = false;
			}
		}

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
		global $bm_prefs, $db;

		// get mail list
		$mailList = $this->_pop3->GetMailList();
		if(!is_array($mailList))
		{
			PutLog(sprintf('Failed to retrieve mail list from user defined POP3 server (user: %d)',
				$this->_userID),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

		// remove already downloaded mails
		$oldUIDs = array();
		if($this->_account['p_keep'])
		{
			$res = $db->Query('SELECT uid FROM {pre}uidindex WHERE pop3=?',
				$this->_account['id']);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$oldUIDs[] = $row['uid'];
			$res->Free();
		}

		// walk through mails
		$i = 0;
		$processedUIDs = array();
		foreach($mailList as $mailNum=>$mailInfo)
		{
			// too big?
			if($mailInfo['size'] > $bm_prefs['mailmax'])
			{
				// yes -> log, do not process
				PutLog(sprintf('Message not downloaded from user defined pop3 box (hard limit; %d > %d bytes; user: %d)',
					$mailInfo['size'],
					$bm_prefs['mailmax'],
					$this->_userID),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
			}
			else if(!in_array($mailInfo['uid'], $oldUIDs))
			{
				// request temp file
				$tempFileID = RequestTempFile(0, -1, true);
				$tempFileName = TempFileName($tempFileID);
				$tempFileFP = fopen($tempFileName, 'wb+');
				assert('is_resource($tempFileFP)');

				// download mail
				if(!$this->_pop3->RetrieveMail($mailNum, $tempFileFP))
				{
					fclose($tempFileFP);
					ReleaseTempFile(0, $tempFileID);
					continue;
				}

				// process mail
				$mailProcessor = _new('BMMailProcessor', array($tempFileFP));
				$mailProcessor->disableBounces = true;
				$mailProcessor->isUserPOP3 = true;
				$mailProcessor->AddRecipient($this->_userObject->_row['email']);
				if($this->_account['p_target'] != -1)
					$mailProcessor->SetTargetFolder($this->_account['p_target']);
				$mailProcessor->ProcessMail();

				// clean up
				fclose($tempFileFP);
				ReleaseTempFile(0, $tempFileID);

				// increment processed mail count
				$processedUIDs[] = $mailInfo['uid'];
				$i++;

				// delete mail
				if(!$this->_account['p_keep'])
					$this->_pop3->DeleteMail($mailNum);
			}

			// do not process too many mails
			if($i == ($maxMails == -1 ? $bm_prefs['fetchcount'] : $maxMails))
				break;
		}

		// update UID index
		foreach($processedUIDs as $uid)
			$db->Query('REPLACE INTO {pre}uidindex(pop3,uid) VALUES(?,?)',
				$this->_account['id'],
				$uid);

		// return mail count + count of processed mails
		return(array(count($mailList), $i));
	}

	/**
	 * connect to pop3 box
	 *
	 * @return bool
	 */
	function ConnectToPOP3Box($account)
	{
		$hostName = $account['p_host'];
		if(substr($hostName, 0, 6) != 'ssl://')
		{
			if($account['p_ssl'])
				$hostName = 'ssl://' . $hostName;
		}

		// connect
		$this->_pop3 = _new('BMPOP3', array($hostName, $account['p_port']));
		$this->_pop3->SetTimeout(BMUSERPOP3_TIMEOUT, true);
		if(!$this->_pop3->Connect())
		{
			PutLog(sprintf('Connection to user defined POP3 server <%s:%d> failed (user: %d)',
				$hostName,
				$account['p_port'],
				$this->_userID),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			return(false);
		}

		// login
		if(!$this->_pop3->Login($account['p_user'], $account['p_pass']))
		{
			PutLog(sprintf('Login at user defined POP3 server <%s:%d> failed (user: %d)',
				$hostName,
				$account['p_port'],
				$this->_userID),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			return(false);
		}

		return(true);
	}
}
