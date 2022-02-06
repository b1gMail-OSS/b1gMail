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

// chdir to file dir
chdir(__DIR__);

define('INTERFACE_MODE', true);
include('../serverlib/init.inc.php');
include(B1GMAIL_DIR . 'serverlib/mailprocessor.class.php');

function ProcessPipeMail(&$tempFileFP, $inputSize, $recps, &$error, &$errorCode, $flags = 0)
{
	global $bm_prefs;

	// empty?
	if($inputSize < 3)
	{
		// yes -> log, do not process
		PutLog('Message not processed (input size < 3 bytes)',
			PRIO_DEBUG,
			__FILE__,
			__LINE__);
		$error = 'Message processing aborted (no input)';
		$errorCode = 451;
		return(false);
	}

	// too big?
	if($inputSize > $bm_prefs['mailmax'])
	{
		// yes -> log, do not process
		PutLog(sprintf('Message not processed (hard limit; %d > %d bytes)',
			$inputSize,
			$bm_prefs['mailmax']),
			PRIO_NOTE,
			__FILE__,
			__LINE__);
		$error = sprintf('Message processing aborted (hard limit; %d > %d bytes)',
					$inputSize,
					$bm_prefs['mailmax']);
		$errorCode = 552;
		return(false);
	}

	// process mail
	$mailProcessor = _new('BMMailProcessor', array($tempFileFP));
	if(count($recps) > 0)
		$mailProcessor->SetRecipients($recps);
	$mailProcessor->Setb1gMailServerFlags($flags);
	$mailProcessor->ProcessMail();
	$error = 'Message delivered';
	$errorCode = 250;

	return(true);
}

// exit code
$exitCode = -1;

// parse args
$recps = array();
$passed_h = false;
$timeout = -1;
$keepAlive = false;
$flags = 0;
if(isset($_SERVER['argv']))
{
	foreach($_SERVER['argv'] as $param)
	{
		if($param == '--')
			$passed_h = true;
		else if($param == '--keep-alive')
			$keepAlive = true;
		else if(substr($param, 0, 10) == '--timeout=')
			$timeout = (int)substr($param, 10);
		else if(substr($param, 0, 8) == '--flags=')
			$flags = (int)substr($param, 8);
		else if($passed_h)
			$recps[] = $param;
	}
}

// set timeout
if($keepAlive)
	@set_time_limit(0);
else if($timeout > -1)
	@set_time_limit($timeout);

// request temp file
$tempFileID = RequestTempFile(0, -1, true);
$tempFileName = TempFileName($tempFileID);
$tempFileFP = fopen($tempFileName, 'wb+');
assert('is_resource($tempFileFP)');

// normal mode
if(!$keepAlive)
{
	// get mail from stdin
	$handle = fopen('php://stdin', 'rb');
	$lineNo = 0;
	while(!feof($handle))
	{
		$buff = fgets2($handle);

		if($lineNo > 0 || substr($buff, 0, 5) != 'From ')
			fwrite($tempFileFP, rtrim($buff, "\r\n") . "\r\n");

		$lineNo++;
	}
	fclose($handle);

	$inputSize = ftell($tempFileFP);
	fseek($tempFileFP, 0, SEEK_SET);

	$error = '';
	$errorCode = 550;
	if(ProcessPipeMail($tempFileFP, $inputSize, $recps, $error, $errorCode, $flags))
	{
		$exitCode = 0;
	}
	else
	{
		echo $error;
		$exitCode = -1;
	}
}

// keep alive mode
else
{
	$recps = array();
	$flags = 0;
	$dataMode = false;

	// get data from stdin
	$handle = fopen('php://stdin', 'rb');
	while(!feof($handle))
	{
		$buff = fgets2($handle);

		// command mode
		if(!$dataMode)
		{
			$buff = trim($buff);

			if(strtolower(substr($buff, 0, 8)) == 'rcpt to:')
			{
				$address = ExtractMailAddress(substr($buff, 8));
				if($address != '' && !in_array($address, $recps))
				{
					$recps[] = $address;
					echo('250 Recipient OK' . "\r\n");
					flush();
				}
				else
				{
					echo('501 Invalid mail address in RCPT TO:' . "\r\n");
					flush();
				}
			}
			else if(strtolower(substr($buff, 0, 5)) == 'flags')
			{
				$flags = (int)substr($buff, 5);
				echo('250 OK' . "\r\n");
				flush();
			}
			else if(strtolower($buff) == 'data')
			{
				$dataMode = true;
				printf('354 Send data (max %d bytes), end with \r\n.\r\n' . "\r\n",
					   $bm_prefs['mailmax']);
				flush();
			}
			else if(strtolower($buff) == 'quit')
			{
				echo('250 Closing connection' . "\r\n");
				flush();
				$exitCode = 0;
				break;
			}
			else
			{
				echo('500 Unrecognized command / syntax error' . "\r\n");
				flush();
			}
		}

		// data mode
		else
		{
			// process
			if(rtrim($buff) == '.')
			{
				// get size
				$inputSize = ftell($tempFileFP);

				// rewing
				fseek($tempFileFP, 0, SEEK_SET);

				// process
				$error = '';
				$errorCode = 550;
				ProcessPipeMail($tempFileFP, $inputSize, $recps, $error, $errorCode, $flags);
				printf('%03d %s' . "\r\n",
					   $errorCode,
					   $error);

				// reset
				fseek($tempFileFP, 0, SEEK_SET);
				ftruncate($tempFileFP, 0);
				$recps = array();
				$dataMode = false;
				$flags = 0;
			}

			// append data
			else
			{
				if(substr($buff, 0, 1) == '.')
					$buff = substr($buff, 1);
				fwrite($tempFileFP, rtrim($buff, "\r\n") . "\r\n");
			}
		}
	}
	fclose($handle);
}

// clean up
fclose($tempFileFP);
ReleaseTempFile(0, $tempFileID);

exit($exitCode);
