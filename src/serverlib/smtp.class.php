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
 * smtp class
 *
 */
class BMSMTP
{
	var $_host;
	var $_port;
	var $_sock;
	var $_helo;
	var $_my_host;
	var $_isb1gMailServer;
	var $_userID;
	var $_dsIDs;

	/**
	 * constructor
	 *
	 * @param string $host
	 * @param int $port
	 * @return BMSMTP
	 */
	function __construct($host, $port, $my_host)
	{
		$this->_host = $host;
		$this->_port = $port;
		$this->_helo = false;
		$this->_my_host = $my_host;
		$this->_isb1gMailServer = false;
		$this->_userID = USERID_UNKNOWN;
		$this->_dsIDs = array();
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
	 * establish connection
	 *
	 * @return bool
	 */
	function Connect()
	{
		$this->_sock = @fsockopen($this->_host, $this->_port, $errNo, $errStr, SOCKET_TIMEOUT);

		if(!is_resource($this->_sock))
		{
			PutLog(sprintf('SMTP connection to <%s:%d> failed (%d, %s)',
				$this->_host,
				$this->_port,
				$errNo,
				$errStr),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}
		else
		{
			$responseLine = $this->_getResponse();
			if(substr($responseLine, 0, 3) != '220')
			{
				PutLog(sprintf('SMTP server <%s:%d> did not return +OK',
					$this->_host,
					$this->_port),
					PRIO_DEBUG,
					__FILE__,
					__LINE__);
				return(false);
			}
			$this->_isb1gMailServer = strpos($responseLine, "[bMS-") !== false;
			if($this->_isb1gMailServer)
			{
				PutLog(sprintf('SMTP server <%s:%d> identified as b1gMailServer',
					$this->_host,
					$this->_port),
					PRIO_DEBUG,
					__FILE__,
					__LINE__);
			}
			return(true);
		}
	}

	/**
	 * log in
	 *
	 * @param string $user
	 * @param string $pass
	 * @return bool
	 */
	function Login($user, $pass)
	{
		fwrite($this->_sock, 'EHLO ' . $this->_my_host . "\r\n")
			&& substr($this->_getResponse(), 0, 3) == '250'
			&& $this->_helo = true;

		if(fwrite($this->_sock, 'AUTH LOGIN' . "\r\n")
			&& substr($this->_getResponse(), 0, 3) == '334')
		{
			if(fwrite($this->_sock, base64_encode(EncodeEMail($user)) . "\r\n")
				&& substr($this->_getResponse(), 0, 3) == '334')
			{
				if(fwrite($this->_sock, base64_encode($pass) . "\r\n")
					&& substr($this->_getResponse(), 0, 3) == '235')
				{
					return(true);
				}
				else
				{
					PutLog(sprintf('SMTP server <%s:%d> rejected username or password for user <%s>',
						$this->_host,
						$this->_port,
						EncodeEMail($user)),
						PRIO_WARNING,
						__FILE__,
						__LINE__);
				}
			}
			else
			{
				PutLog(sprintf('SMTP server <%s:%d> rejected username <%s>',
					$this->_host,
					$this->_port,
					EncodeEMail($user)),
					PRIO_WARNING,
					__FILE__,
					__LINE__);
			}
		}
		else
		{
			PutLog(sprintf('SMTP server <%s:%d> does not seem to support LOGIN authentication',
				$this->_host,
				$this->_port),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
		}

		return(false);
	}

	/**
	 * disconnect
	 *
	 * @return bool
	 */
	function Disconnect()
	{
		fwrite($this->_sock, 'QUIT' . "\r\n")
			&& $this->_getResponse();
		fclose($this->_sock);
		return(true);
	}

	/**
	 * initiate mail transfer
	 *
	 * @param string $from Sender address
	 * @param mixed $to Recipients (single address or array of addresses)
	 * @return bool
	 */
	function StartMail($from, $to)
	{
		$this->_dsIDs = array();

		// send helo, if not sent yet (e.g. at login)
		if(!$this->_helo)
			fwrite($this->_sock, 'HELO ' . $this->_my_host . "\r\n")
				&& substr($this->_getResponse(), 0, 3) == '250'
				&& $this->_helo = true;

		// send MAIL FROM
		$mailFromCmd = 'MAIL FROM:<' . $from . '>';
		if($this->_isb1gMailServer)
		{
			$mailFromCmd .= ' X-B1GMAIL-USERID=' . $this->_userID;
		}
		$mailFromCmd .= "\r\n";
		if(fwrite($this->_sock, $mailFromCmd)
			&& substr($this->_getResponse(), 0, 3) == '250')
		{
			if(!is_array($to))
				$to = array($to);

			// send RCPT TO
			foreach($to as $address)
			{
				$rcptAdd = '';

				if($this->_isb1gMailServer && $this->_userID > 0)
				{
					$dsID = CreateMailDeliveryStatusEntry($this->_userID, $address);
					$rcptAdd = ' X-B1GMAIL-DSID=' . $dsID;
					$this->_dsIDs[$dsID] = $address;
				}

				fwrite($this->_sock, 'RCPT TO:<' . $address . '>' . $rcptAdd . "\r\n")
					&& $this->_getResponse();
			}

			// ok!
			return(true);
		}
		else
		{
			PutLog(sprintf('SMTP server <%s:%d> did not accept sender address <%s>',
				$this->_host,
				$this->_port,
				$from),
				PRIO_DEBUG,
				__FILE__,
				__LINE__);
		}

		return(false);
	}

	/**
	 * send mail data
	 *
	 * @param resource $fp File pointer
	 * @return bool
	 */
	function SendMail($fp)
	{
		// send DATA command
		if(fwrite($this->_sock, 'DATA' . "\r\n")
			&& substr($this->_getResponse(), 0, 3) == '354')
		{
			// send mail
			fseek($fp, 0, SEEK_SET);
			while(is_resource($fp)
					&& !feof($fp)
					&& ($line = fgets2($fp)) !== false)
			{
				if(substr($line, 0, 1) == '.')
					$line = '.' . $line;

				if(fwrite($this->_sock, rtrim($line) . "\r\n") === false)
					break;
			}

			if(count($this->_dsIDs) > 0)
			{
				UpdateDeliveryStatus(array_keys($this->_dsIDs), MDSTATUS_SUBMITTED_TO_MTA);
			}

			// finish
			$success = (fwrite($this->_sock, "\r\n" . '.' . "\r\n")
					&& substr($this->_getResponse(), 0, 3) == '250');
			return($success);
		}

		return(false);
	}

	/**
	 * reset session
	 *
	 * @return bool
	 */
	function Reset()
	{
		return(fwrite($this->_sock, 'RSET' . "\r\n")
				&& substr($this->_getResponse(), 0, 3) == '250');
	}

	/**
	 * associate sent mail with an outbox mail ID
	 *
	 */
	function SetDeliveryStatusOutboxID($outboxID)
	{
		if(count($this->_dsIDs) > 0)
		{
			SetDeliveryStatusOutboxID(array_keys($this->_dsIDs), $outboxID);
		}
	}

	/**
	 * get smtp server response (may consist of multiple lines)
	 *
	 * @return string
	 */
	function _getResponse()
	{
		$response = '';
		while($line = fgets2($this->_sock))
		{
			$response .= $line;
			if($line[3] != '-')
				break;
		}
		return($response);
	}
}
