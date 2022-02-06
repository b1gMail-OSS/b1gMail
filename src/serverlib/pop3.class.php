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
 * pop3 access class
 *
 */
class BMPOP3
{
	var $_host;
	var $_port;
	var $_sock;
	var $_timeout;
	var $_enableStreamTimeout;

	/**
	 * constructor
	 *
	 * @param string $host
	 * @param int $port
	 * @return BMPOP3
	 */
	function __construct($host, $port)
	{
		$this->_host = $host;
		$this->_port = $port;
		$this->_timeout = SOCKET_TIMEOUT;
		$this->_enableStreamTimeout = false;
	}

	/**
	 * set timeout for connection
	 *
	 * @param int $timeout Seconds
	 * @param bool $enableStreamTimeout Also enable stream timeout (not just connect timeout)?
	 */
	function SetTimeout($timeout, $enableStreamTimeout = false)
	{
		$this->_timeout = $timeout;
		$this->_enableStreamTimeout = $enableStreamTimeout;
	}

	/**
	 * establish connection
	 *
	 * @return bool
	 */
	function Connect()
	{
		$this->_sock = @fsockopen($this->_host, $this->_port, $errNo, $errStr, $this->_timeout);

		if(!is_resource($this->_sock))
		{
			PutLog(sprintf('POP3 connection to <%s:%d> failed (%d, %s)',
				$this->_host,
				$this->_port,
				$errNo,
				$errStr),
				PRIO_DEBUG,
				__FILE__,
				__LINE__);
			return(false);
		}
		else
		{
			if($this->_enableStreamTimeout && function_exists('stream_set_timeout'))
				stream_set_timeout($this->_sock, $this->_timeout);

			$responseLine = fgets2($this->_sock);
			if(substr($responseLine, 0, 1) != '+')
			{
				PutLog(sprintf('POP3 server <%s:%d> did not return +OK',
					$this->_host,
					$this->_port),
					PRIO_DEBUG,
					__FILE__,
					__LINE__);
				return(false);
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
		if(fwrite($this->_sock, 'USER ' . EncodeEMail($user) . "\r\n")
			&& substr(fgets2($this->_sock), 0, 1) == '+')
		{
			if(fwrite($this->_sock, 'PASS ' . $pass . "\r\n")
				&& substr(fgets2($this->_sock), 0, 1) == '+')
			{
				return(true);
			}
			else
			{
				PutLog(sprintf('POP3 server <%s:%d> rejected password for user <%s>',
					$this->_host,
					$this->_port,
					EncodeEMail($user)),
					PRIO_DEBUG,
					__FILE__,
					__LINE__);
			}
		}
		else
		{
			PutLog(sprintf('POP3 server <%s:%d> rejected username <%s>',
				$this->_host,
				$this->_port,
				EncodeEMail($user)),
				PRIO_DEBUG,
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
			&& fgets2($this->_sock);
		fclose($this->_sock);
		return(true);
	}

	/**
	 * get list of mails
	 *
	 * @return array
	 */
	function GetMailList()
	{
		$result = array();
		$msgNum = $msgSize = $msgUID = 0;

		// LIST command -> get msg numbers and sizes
		if(fwrite($this->_sock, 'LIST' . "\r\n")
			&& substr(fgets2($this->_sock), 0, 1) == '+')
		{
			while(($line = trim(fgets2($this->_sock))) != '.'
					&& $line != '')
			{
				if(sscanf($line, '%d %d', $msgNum, $msgSize) == 2)
				{
					$result[$msgNum] = array(
						'num'	=> $msgNum,
						'size'	=> $msgSize,
						'uid'	=> false
					);
				}
			}

			// try to get UIDs
			if(fwrite($this->_sock, 'UIDL' . "\r\n")
				&& substr(fgets2($this->_sock), 0, 1) == '+')
			{
				while(($line = trim(fgets2($this->_sock))) != '.'
						&& $line != '')
				{
					if(sscanf($line, '%d %s', $msgNum, $msgUID) == 2)
						if(isset($result[$msgNum]))
							$result[$msgNum]['uid'] = $msgUID;
				}
			}

			return($result);
		}
		else
		{
			PutLog(sprintf('LIST command at POP3 server <%s:%d> failed',
				$this->_host,
				$this->_port),
				PRIO_DEBUG,
				__FILE__,
				__LINE__);
			return(false);
		}
	}

	/**
	 * retrieve mail to file pointer
	 *
	 * @param int $num Message number
	 * @param resource $fp File pointer
	 * @return bool
	 */
	function RetrieveMail($num, $fp)
	{
		if(fwrite($this->_sock, 'RETR ' . (int)$num . "\r\n")
			&& substr(fgets2($this->_sock), 0, 1) == '+')
		{
			$oldPos = ftell($fp);
			$lineNo = 0;
			while(($line = fgets2($this->_sock))
					&& !(substr($line, 0, 1) == '.' && trim($line) == '.'))
			{
				if($line[0] == '.')
					$line = substr($line, 1);

				$line = rtrim($line, "\r\n") . "\r\n";
				if($lineNo > 0 || substr($line, 0, 5) != 'From ')
					fwrite($fp, $line);
				$lineNo++;
			}
			fseek($fp, $oldPos, SEEK_SET);
			return(true);
		}
		return(false);
	}

	/**
	 * retrieve mail headers to file pointer
	 *
	 * @param int $num Message number
	 * @param resource $fp File pointer
	 * @param int $additionalBodyLines Additional body lines to fetch
	 * @return bool
	 */
	function RetrieveMailHeaders($num, $fp, $additionalBodyLines = 0)
	{
		if(fwrite($this->_sock, 'TOP ' . (int)$num . ' ' . (int)$additionalBodyLines . "\r\n")
			&& substr(fgets2($this->_sock), 0, 1) == '+')
		{
			$oldPos = ftell($fp);
			while(($line = fgets2($this->_sock))
					&& !(substr($line, 0, 1) == '.' && trim($line) == '.'))
			{
				if($line[0] == '.')
					$line = substr($line, 1);

				$line = rtrim($line, "\r\n") . "\r\n";
				fwrite($fp, $line);
			}
			fseek($fp, $oldPos, SEEK_SET);
			return(true);
		}
		return(false);
	}

	/**
	 * mark mail for deletion
	 *
	 * @param int $num Message number
	 * @return bool
	 */
	function DeleteMail($num)
	{
		return(fwrite($this->_sock, 'DELE ' . (int)$num . "\r\n")
			&& substr(fgets2($this->_sock), 0, 1) == '+');
	}
}
