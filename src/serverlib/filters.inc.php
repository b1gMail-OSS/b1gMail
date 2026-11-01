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

define('BMMAILFILTER_BAYES_MAXWORDS',		1000);
define('BMMAILFILTER_BAYES_BORDER',			0.9000);
define('BMMAILFILTER_BAYES_TEXTLIMIT',		32768);

/**
 * mail filter base class
 *
 */
class BMMailFilter
{
	/**
	 * BMMail object
	 *
	 * @var BMMail
	 */
	var $_mail;

	/**
	 * constructor
	 *
	 * @param BMMail $mail
	 * @return BMMailFilter
	 */
	function __construct(&$mail)
	{
		$this->_mail = &$mail;
	}

	/**
	 * execute filter
	 *
	 * @return new flags
	 */
	function Filter()
	{
		return($this->_mail->flags);
	}
}

/**
 * dnsbl filter
 *
 */
class BMMailFilter_DNSBL extends BMMailFilter
{
	/**
	 * execute filter
	 *
	 * @return new flags
	 */
	function Filter()
	{
		global $bm_prefs;

		// already spam?
		if(($this->_mail->flags & FLAG_SPAM) != 0)
			return($this->_mail->flags);

		// no => check
		$received = $this->_mail->GetHeaderValue('received');
		$ret_arr_full = array();

		// match IP addresses
		if(preg_match_all('/([0-9]){1,3}\.([0-9]){1,3}\.([0-9]){1,3}\.([0-9]){1,3}/', $received, $ret_arr_full))
		{
			$posServers = $ips = array();

			// get IPs
			foreach($ret_arr_full[0] as $ip)
				if(!in_array($ip, $ips))
					$ips[] = $ip;

			// check IPs
			foreach($ips as $ip)
			{
				// skip private IPs
				if(substr($ip, 0, 4) == '127.'
					|| substr($ip, 0, 3) == '10.'
					|| substr($ip, 0, 8) == '192.168.'
					|| (substr($ip, 0, 4) == '172.' && $ip[6] == '.' && (int)substr($ip, 4, 2) >= 16 && (int)substr($ip, 4, 2) <= 31))
					continue;

				// convert IP for lookup
				$ip = implode('.', array_reverse(explode('.', $ip)));
				$dnsblServers = explode(':', $bm_prefs['dnsbl']);

				// lookup
				foreach($dnsblServers as $server)
					if(@gethostbyname(strtolower($ip . '.' . $server . (substr($server, -1) != '.' ? '.' : ''))) != strtolower($ip . '.' . $server . (substr($server, -1) != '.' ? '.' : ''))
						&& !in_array($server, $posServers))
						$posServers[] = $server;

				// only check first (real) RECEIVED-ip
				break;
			}

			// spam?
			if(count($posServers) >= $bm_prefs['dnsbl_requiredservers'])
			{
				// log & mark as spam
				PutLog(sprintf('Mail identified as spam by DNSBL servers <%s>',
					implode('>, <', $posServers)),
					PRIO_DEBUG,
					__FILE__,
					__LINE__);
				$this->_mail->flags |= FLAG_SPAM;
			}
		}

		// return
		return($this->_mail->flags);
	}
}

/**
 * Open a ClamAV/clamd connection (TCP or Unix socket).
 *
 * Host may be a TCP hostname/IP, a filesystem path (/run/clamav/...),
 * or unix:///run/clamav/clamd.ctl. Port is ignored for Unix sockets.
 *
 * @param string $host
 * @param int $port
 * @param int $errNo
 * @param string $errStr
 * @return resource|false
 */
function BMClamAVConnect($host, $port, &$errNo, &$errStr)
{
	$host = trim((string)$host);
	$errNo = 0;
	$errStr = '';
	if($host === '')
	{
		$errStr = 'empty host';
		return(false);
	}

	$timeout = defined('SOCKET_TIMEOUT') ? SOCKET_TIMEOUT : 10;

	// Unix domain socket (Debian/Ubuntu default: /var/run/clamav/clamd.ctl)
	if($host[0] === '/'
		|| stripos($host, 'unix:') === 0)
	{
		$path = $host;
		if(stripos($path, 'unix://') === 0)
			$path = substr($path, 7);
		else if(stripos($path, 'unix:') === 0)
			$path = substr($path, 5);

		return(@stream_socket_client('unix://' . $path, $errNo, $errStr, $timeout));
	}

	return(@fsockopen($host, (int)$port, $errNo, $errStr, $timeout));
}

/**
 * Format ClamAV endpoint for log messages.
 *
 * @param string $host
 * @param int $port
 * @return string
 */
function BMClamAVEndpointLabel($host, $port)
{
	$host = trim((string)$host);
	if($host !== '' && ($host[0] === '/' || stripos($host, 'unix:') === 0))
		return($host);
	return(sprintf('%s:%d', $host, (int)$port));
}

/**
 * clamav filter
 *
 */
class BMMailFilter_ClamAV extends BMMailFilter
{
	/**
	 * execute filter
	 *
	 * @return new flags
	 */
	function Filter()
	{
		global $bm_prefs;

		// already marked as infected?
		if(($this->_mail->flags & FLAG_INFECTED) != 0)
			return($this->_mail->flags);

		$host = isset($bm_prefs['clamd_host']) ? $bm_prefs['clamd_host'] : '127.0.0.1';
		$port = isset($bm_prefs['clamd_port']) ? (int)$bm_prefs['clamd_port'] : 3310;
		$endpoint = BMClamAVEndpointLabel($host, $port);

		$sock = BMClamAVConnect($host, $port, $errNo, $errStr);
		if(!is_resource($sock))
		{
			PutLog(sprintf('Connection to ClamAV at <%s> failed (%d, %s)',
				$endpoint,
				$errNo,
				$errStr),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

		stream_set_timeout($sock, defined('SOCKET_TIMEOUT') ? SOCKET_TIMEOUT : 10);

		// Modern clamd: INSTREAM on the same connection (legacy STREAM was removed).
		if(@fwrite($sock, "nINSTREAM\n") === false)
		{
			PutLog(sprintf('Failed sending INSTREAM to ClamAV at <%s>', $endpoint),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			fclose($sock);
			return(false);
		}

		$oldOffset = ftell($this->_mail->_fp);
		fseek($this->_mail->_fp, 0, SEEK_SET);
		$ok = true;
		while(is_resource($this->_mail->_fp) && !feof($this->_mail->_fp))
		{
			$chunk = fread($this->_mail->_fp, 8192);
			if($chunk === false || $chunk === '')
				break;
			if(@fwrite($sock, pack('N', strlen($chunk)) . $chunk) === false)
			{
				$ok = false;
				break;
			}
		}
		fseek($this->_mail->_fp, $oldOffset, SEEK_SET);

		if($ok)
			$ok = (@fwrite($sock, pack('N', 0)) !== false);

		if(!$ok)
		{
			PutLog(sprintf('Failed streaming mail data to ClamAV at <%s>', $endpoint),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			fclose($sock);
			return(false);
		}

		$response = fgets2($sock);
		fclose($sock);

		if($response === false || $response === '')
		{
			PutLog(sprintf('Empty response from ClamAV at <%s>', $endpoint),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

		$response = trim($response);
		if($response === 'stream: OK'
			|| preg_match('/: OK$/i', $response))
		{
			return($this->_mail->flags);
		}

		// e.g. "stream: Eicar-Test-Signature FOUND" or "INSTREAM size limit exceeded"
		if(preg_match('/:\s*(.+)\s+FOUND$/i', $response, $m))
			$infectionName = trim($m[1]);
		else
			$infectionName = $response !== '' ? $response : '(unknown)';

		$this->_mail->flags |= FLAG_INFECTED;
		$this->_mail->infection = $infectionName;

		PutLog(sprintf('Infection <%s> detected in mail by ClamAV',
			$infectionName),
			PRIO_DEBUG,
			__FILE__,
			__LINE__);

		return($this->_mail->flags);
	}
}

/**
 * bayes filter word class
 *
 */
class BMMailFilter_Bayes_Word
{
	/**
	 * word token
	 *
	 * @var string
	 */
	var $token;

	/**
	 * count of appearance in SPAM mails
	 *
	 * @var int
	 */
	var $inSpam;

	/**
	 * count of appearance in NON SPAM mails
	 *
	 * @var int
	 */
	var $inNonSpam;

	/**
	 * spam probability
	 *
	 * @var double
	 */
	var $prob;

	/**
	 * interest
	 *
	 * @var double
	 */
	var $interest;

	/**
	 * constructor
	 *
	 * @param string $token Token
	 * @return BMMailFilter_Bayes_Word
	 */
	function __construct($token)
	{
		$this->token 		= $token;
		$this->inSpam 		= 0;
		$this->inNonSpam 	= 0;
		$this->prob 		= 0.4000;
		$this->interest 	= 0.0000;
	}

	/**
	 * calculate probability and interest
	 *
	 * @param int $bayesNonSpam Non spam mails
	 * @param int $bayesSpam Spam mails
	 */
	function Calculate($bayesNonSpam, $bayesSpam)
	{
		// calculate probability
		$allNonSpam 	= max($bayesNonSpam, 1);
		$allSpam 		= max($bayesSpam, 1);
		$good 			= $this->inNonSpam * 2;
		$bad 			= $this->inSpam;
		$pGood 			= (float) min((float) $good / (float) $allNonSpam, 1.0);
		$pBad 			= (float) min((float) $bad / (float) $allSpam, 1.0);
		$this->prob 	= max(min($pBad / ($pGood + $pBad), 0.9999), 0.0001);

		// calculate interest
		if($this->inNonSpam*2 + $this->inSpam >= 5)
			$this->interest = ($this->prob > 0.5)
				? $this->prob - 0.5
				: 0.5 - $this->prob;
	}
}

/**
 * bayes filter
 *
 */
class BMMailFilter_Bayes extends BMMailFilter
{
	/**
	 * User ID
	 *
	 * @var int
	 */
	var $_userID;

	/**
	 * Words array
	 *
	 * @var array
	 */
	var $_words;

	/**
	 * count of non spam mails
	 *
	 * @var int
	 */
	var $bayesNonSpam;

	/**
	 * count of spam mails
	 *
	 * @var int
	 */
	var $bayesSpam;

	/**
	 * spam border (%)
	 *
	 * @var double
	 */
	var $bayesBorder;

	/**
	 * constructor
	 *
	 * @param BMMail $mail Mail object
	 * @param int $userID User ID (0 = global)
	 * @return BMMailFilter_Bayes
	 */
	function __construct(&$mail, $userID = 0)
	{
		global $bm_prefs;

		$this->_mail = &$mail;
		$this->_userID = $userID;

		if($userID == 0)
		{
			$this->bayesNonSpam = $bm_prefs['bayes_nonspam'];
			$this->bayesSpam = $bm_prefs['bayes_spam'];
			$this->bayesBorder = BMMAILFILTER_BAYES_BORDER;
		}
		else
		{
			list($this->bayesNonSpam, $this->bayesSpam, $this->bayesBorder) = BMUser::GetBayesValues($userID);
			$this->bayesBorder /= 100.0;
		}
	}

	/**
	 * extract words from string
	 *
	 * @param string $string String to extract words from
	 * @param string $prefix Token prefix: H(TML) / S(ubject) / T(ext) / (A)ttachment name
	 */
	function ExtractWords($string, $prefix)
	{
		if(strlen($string) > BMMAILFILTER_BAYES_TEXTLIMIT)
			$string = substr($string, 0, BMMAILFILTER_BAYES_TEXTLIMIT);

		$wordList = preg_split('/[\s,]+/', $string);
		foreach($wordList as $word)
		{
			$word = strtolower(preg_replace('/[^0-9a-zA-Z]/', '', $word));
			if(strlen($word) > 1
				&& !is_numeric($word)
				&& count($this->_words) < BMMAILFILTER_BAYES_MAXWORDS)
			{
				$wordToken = $prefix . ':' . md5($word);

								// |  we do not use the _new class factory here because
								// v  this class is constructed very frequently
				$this->_words[] = new BMMailFilter_Bayes_Word($wordToken);
			}
		}
	}

	/**
	 * prepare HTML text for use in filter
	 *
	 * @param string $text Text
	 * @return string
	 */
	function _prepareHTMLText($text)
	{
		// remove tags
		$text = strip_tags(str_replace('>', '> ', $text));

		// resolve entities
		$text = DecodeHTMLEntities($text);

		return($text);
	}

	/**
	 * find words
	 *
	 */
	function FindWords()
	{
		$this->_words = array();

		// subject
		$subject = $this->_mail->GetHeaderValue('subject');
		if(trim($subject) != '')
			$this->ExtractWords($subject, 'S');

		// text parts
		$textParts = $this->_mail->GetTextParts();
		if(isset($textParts['html']) && trim($textParts['html']) != '')
			$this->ExtractWords($this->_prepareHTMLText($textParts['html']), 'H');
		if(isset($textParts['text']) && trim($textParts['text']) != '')
			$this->ExtractWords($textParts['text'], 'T');

		// attachment names
		$attachmentString = '';
		$attachments = $this->_mail->GetAttachments();
		foreach($attachments as $info)
			$attachmentString .= ' ' . str_replace(' ', '_', preg_replace('/[0-9]/', '', $info['filename']));
		if($attachmentString != '')
			$this->ExtractWords(trim($attachmentString), 'A');
	}

	/**
	 * set word props
	 *
	 * @param string $hash Hash
	 * @param int $inSpam In spam
	 * @param int $inNonSpam In non spam
	 * @param bool $calc Calc?
	 */
	function SetWordProps($hash, $inSpam, $inNonSpam, $calc = true)
	{
		foreach($this->_words as $key=>$val)
			if($this->_words[$key]->token == $hash)
			{
				$this->_words[$key]->inSpam = $inSpam;
				$this->_words[$key]->inNonSpam = $inNonSpam;
				if($calc)
					$this->_words[$key]->Calculate($this->bayesNonSpam, $this->bayesSpam);
			}
	}

	/**
	 * lookup words in spamindex
	 *
	 * @param bool $calc Calc?
	 */
	function LookupWords($calc = true)
	{
		global $db;

		// collect words
		$wordList = array();
		foreach($this->_words as $wordInfo)
		{
			$str = '\'' . $wordInfo->token . '\'';
			if(!in_array($str, $wordList))
				$wordList[] = $str;
		}

		// lookup
		if(count($wordList) > 0)
		{
			$wordList = implode(', ', $wordList);
			$res = $db->Query('SELECT hash,inspam,innonspam FROM {pre}spamindex WHERE userid=? AND hash IN(' . $wordList . ')',
				$this->_userID);
			while($row = $res->FetchArray(MYSQLI_NUM))
				$this->SetWordProps($row[0], $row[1], $row[2], $calc);
			$res->Free();
		}
	}

	/**
	 * sort words by word interest
	 *
	 */
	function SortWords()
	{
		// put interest values in an array
		$sortArray = array();
		foreach($this->_words as $key=>$value)
			$sortArray[$key] = $value->interest;
		asort($sortArray, SORT_NUMERIC);

		// create new array
		$newArray = array();
		foreach($sortArray as $key=>$value)
			$newArray[] = $this->_words[$key];

		// overwrite old array
		$this->_words = $newArray;
	}

	/**
	 * calculate mail spam probability
	 *
	 * @return double
	 */
	function CalculateProbability()
	{
		$p1 = $p2 = 1.0000;

		for($i=0; $i<min(15, count($this->_words)); $i++)
		{
			$p1 *= $this->_words[$i]->prob;
			$p2 *= 1 - $this->_words[$i]->prob;
		}

		$p = $p1 / ($p1 + $p2);
		$p = min(0.9999, max(round($p, 4), 0.0001));
		return($p);
	}

	/**
	 * mark mail as spam (wrapper for Train())
	 *
	 */
	function MarkAsSpam()
	{
		$this->Train(true);
	}

	/**
	 * mark mail as non spam (wrapper for Train())
	 *
	 */
	function MarkAsNonSpam()
	{
		$this->Train(false);
	}

	/**
	 * train words
	 *
	 * @param bool $isSpam Mail is spam?
	 */
	function TrainWords($isSpam)
	{
		global $db;

		$field = $isSpam ? 'inspam' : 'innonspam';
		$updatedWords = array();

		if(count($this->_words) > 0)
		{
			foreach($this->_words as $key=>$val)
			{
				if(!in_array($val->token, $updatedWords))
				{
					$db->Query('INSERT INTO {pre}spamindex(hash,userid,inspam,innonspam) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE `inspam`=`inspam`+?, `innonspam`=`innonspam`+?',
						$val->token,
						$this->_userID,
						($isSpam ? 1 : 0),
						($isSpam ? 0 : 1),
						($isSpam ? 1 : 0),
						($isSpam ? 0 : 1));
					$updatedWords[] = $val->token;
				}
			}
		}
	}

	/**
	 * train mail
	 *
	 * @param bool $isSpam Mail is spam?
	 */
	function Train($isSpam)
	{
		if(!$this->_mail->trained)
		{
			// find words
			$this->FindWords();

			// lookup words
			$this->LookupWords(false);

			// train words
			$this->TrainWords($isSpam);

			// update stats
			if($isSpam)
				$this->bayesSpam++;
			else
				$this->bayesNonSpam++;
			$this->UpdateStats();

			// mark as trained
			$this->_mail->MarkAsTrained();
		}
		else
			PutLog(sprintf('Mail <%d> already trained',
				$this->_mail->id),
				PRIO_DEBUG,
				__FILE__,
				__LINE__);
	}

	/**
	 * update bayes training stats
	 *
	 */
	function UpdateStats()
	{
		global $db;

		if($this->_userID == 0)
			$db->Query('UPDATE {pre}prefs SET bayes_nonspam=?, bayes_spam=?',
				$this->bayesNonSpam,
				$this->bayesSpam);
		else
			BMUser::UpdateBayesValues($this->bayesNonSpam, $this->bayesSpam, $this->_userID);
	}

	/**
	 * execute filter
	 *
	 * @return new flags
	 */
	function Filter()
	{
		// already spam?
		if(($this->_mail->flags & FLAG_SPAM) != 0)
			return($this->_mail->flags);

		// find words
		$this->FindWords();

		// lookup words in spamindex
		$this->LookupWords();

		// sort words
		$this->SortWords();

		// calculate mail spam probability
		$prob = $this->CalculateProbability();

		// is spam?
		if($prob > $this->bayesBorder)
		{
			// log & mark as spam
			PutLog(sprintf('Mail identified as spam by %s bayes spam filter (probability: %.04f)',
				$this->_userID == 0 ? 'global' : 'local',
				$prob),
				PRIO_DEBUG,
				__FILE__,
				__LINE__);
			$this->_mail->flags |= FLAG_SPAM;
		}
		else
		{
			// debug log
			PutLog(sprintf('Mail identified as NON spam by %s bayes spam filter (probability: %.04f)',
				$this->_userID == 0 ? 'global' : 'local',
				$prob),
				PRIO_DEBUG,
				__FILE__,
				__LINE__);
		}

		// return
		return($this->_mail->flags);
	}
}
