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

if(!class_exists('BMHTTP'))
	include(B1GMAIL_DIR . 'serverlib/http.class.php');

/**
 * sms class
 */
class BMSMS
{
	var $_userID;
	var $_userObject;

	/**
	 * constructor
	 *
	 * @param int $userID User ID
	 * @param BMUser $userObject User object
	 * @return BMSMS
	 */
	function __construct($userID, &$userObject)
	{
		$this->_userID = (int)$userID;
		$this->_userObject = &$userObject;
	}

	/**
	 * get outbox
	 *
	 * @return array
	 */
	function GetOutbox($sortColumn = 'id', $sortOrder = 'DESC')
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT id,`from`,`to`,`text`,price,`date` FROM {pre}smsend WHERE isSMS=1 AND user=? AND deleted=0 ORDER BY `' . $sortColumn . '` ' . $sortOrder . ' LIMIT 15',
			$this->_userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$result[$row['id']] = $row;
		$res->Free();

		return($result);
	}

	/**
	 * delete sms outbox entry
	 *
	 * @param int $id ID
	 */
	function DeleteOutboxEntry($id)
	{
		global $db;

		$db->Query('UPDATE {pre}smsend SET `deleted`=1,`from`=?,`to`=?,`text`=? WHERE id=? AND user=?',
			'', '', '',
			$id, $this->_userID);
		return($db->AffectedRows() == 1);
	}

	/**
	 * check if $no conforms $pre-list
	 *
	 * @param string $no
	 * @param string $pre
	 * @return bool
	 */
	function PreOK($no, $pre)
	{
		if(trim($pre) != '')
		{
			$ok = false;
			$entries = explode(':', $pre);
			foreach($entries as $entry)
			{
				$entry = str_replace('+', '00', preg_replace('/[^0-9]/', '', $entry));
				if(substr($no, 0, strlen($entry)) == $entry)
				{
					$ok = true;
					break;
				}
			}
		}
		else
			$ok = true;

		return($ok);
	}

	/**
	 * get available SMS types
	 *
	 * @return array
	 */
	function GetTypes()
	{
		global $db;

		if(is_object($this->_userObject))
		{
			$group = $this->_userObject->GetGroup();
			$groupRow = $group->Fetch();
		}
		else
			$groupRow = array('sms_sig' => '');

		$result = array();
		$res = $db->Query('SELECT id,titel,typ,std,price,gateway,flags,maxlength FROM {pre}smstypen ORDER BY titel ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$result[$row['id']] = array(
				'id'		=> $row['id'],
				'title'		=> $row['titel'],
				'type'		=> $row['typ'],
				'default'	=> $row['std'] == 1,
				'price'		=> $row['price'],
				'gateway'	=> $row['gateway'],
				'flags'		=> $row['flags'],
				'maxlength'	=> $row['maxlength'] - strlen($groupRow['sms_sig'])
			);
		$res->Free();

		return($result);
	}

	/**
	 * get max SMS chars for user
	 *
	 * @return int
	 */
	function GetMaxChars($typeID = 0)
	{
		global $db;

		if(is_object($this->_userObject))
		{
			$group = $this->_userObject->GetGroup();
			$groupRow = $group->Fetch();
		}
		else
			$groupRow = array('sms_sig' => '');

		$maxChars = 160;

		if($typeID > 0)
		{
			$res = $db->Query('SELECT maxlength FROM {pre}smstypen WHERE id=?', $typeID);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$maxChars = $row['maxlength'];
			$res->Free();
		}

		return($maxChars - strlen($groupRow['sms_sig']));
	}

	/**
	 * get default gateway or gateway specified by ID
	 *
	 * @param int $id ID (0 = default gateway)
	 * @return array
	 */
	function GetGateway($id = 0)
	{
		global $bm_prefs, $db;

		if($id == 0)
			$id = $bm_prefs['sms_gateway'];

		$res = $db->Query('SELECT id,titel,getstring,success,`user`,`pass` FROM {pre}smsgateways WHERE id=?',
			$id);
		if($res->RowCount() == 1)
		{
			$row = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			return(array(
				'id'		=> $row['id'],
				'title'		=> $row['titel'],
				'getstring'	=> $row['getstring'],
				'success'	=> $row['success'],
				'user'		=> $row['user'],
				'pass'		=> $row['pass']
			));
		}
		return(false);
	}

	/**
	 * send SMS
	 *
	 * @param string $from From
	 * @param string $to To
	 * @param string $text Text
	 * @param int $type Type ID (0 = default type)
	 * @param bool $charge Charge user account?
	 * @param bool $putToOutbox Put SMS to outbox?
	 * @param int $outboxID ID of outbox entry, if already available
	 * @return bool
	 */
	function Send($from, $to, $text, $type = 0, $charge = true, $putToOutbox = true, $outboxID = 0)
	{
		global $bm_prefs, $db, $lang_user;

		// module handler
		ModuleFunction('OnSendSMS', array(&$text, &$type, &$from, &$to, &$this->_userObject));

		// get type and type list
		$types = $this->GetTypes();
		if($type == 0)
		{
			foreach($types as $typeID=>$typeInfo)
				if($typeInfo['default'])
					$type = $typeID;
			if($type == 0)
			{
				PutLog(sprintf('Default SMS type <%d> not found while trying to send SMS from <%s> to <%s> (userID: %d)',
					$type,
					$from,
					$to,
					$this->_userID),
					PRIO_WARNING,
					__FILE__,
					__LINE__);
				return(false);
			}
		}
		if(isset($types[$type]))
			$type = $types[$type];
		else
		{
			PutLog(sprintf('SMS type <%d> not found while trying to send SMS from <%s> to <%s> (userID: %d)',
				$type,
				$from,
				$to,
				$this->_userID),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

		// crop text
		if(_strlen($text) > $this->GetMaxChars($type['id']))
			$text = _substr($text, 0, $this->GetMaxChars($type['id']));

		// check account balance
		if($charge)
		{
			$balance = $this->_userObject->GetBalance();
			if($balance < $type['price'])
			{
				PutLog(sprintf('Failed to send SMS from <%s> to <%s>: Not enough credits (userID: %d)',
					$from,
					$to,
					$this->_userID),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
				return(false);
			}
		}

		// get gateway info
		$gateway = $this->GetGateway($type['gateway']);
		if(!$gateway)
		{
			PutLog(sprintf('Gateway <%d> not found while trying to send SMS with type <%d> from <%s> to <%s> (userID: %d)',
				$type['gateway'],
				$type['id'],
				$from,
				$to,
				$this->_userID),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

		// prepare formatted numbers
		$fromPlus = preg_replace('/^00/', '+', $from);
		$toPlus = preg_replace('/^00/', '+', $to);

		// build GET string
		$getString = $gateway['getstring'];
		$getString = str_replace('%%user%%',
								 _urlencode($gateway['user']),
								 $getString);
		$getString = str_replace('%%passwort%%',
								 _urlencode($gateway['pass']),
								 $getString);
		$getString = str_replace('%%from%%',
								 urlencode(CharsetDecode($from, false, 'ISO-8859-1')),
								 $getString);
		$getString = str_replace('%%fromPlus%%',
								 urlencode(CharsetDecode($fromPlus, false, 'ISO-8859-1')),
								 $getString);
		$getString = str_replace('%%from_utf8%%',
								 urlencode(CharsetDecode($from, false, 'UTF-8')),
								 $getString);
		$getString = str_replace('%%to%%',
								 _urlencode($to),
								 $getString);
		$getString = str_replace('%%toPlus%%',
								 _urlencode($toPlus),
								 $getString);
		$getString = str_replace('%%msg%%',
								 urlencode(CharsetDecode($text, false, 'ISO-8859-1')),
								 $getString);
		$getString = str_replace('%%msg_utf8%%',
								 urlencode(CharsetDecode($text, false, 'UTF-8')),
								 $getString);
		if($this->_userObject)
		{
			$getString = str_replace('%%usermail%%',
									 _urlencode($this->_userObject->_row['email']),
									 $getString);
			$getString = str_replace('%%userid%%',
									 _urlencode($this->_userObject->_id),
									 $getString);
		}
		$getString = str_replace('%%typ%%',
								 _urlencode($type['type']),
								 $getString);

		// request!
		$http = _new('BMHTTP', array($getString));
		$result = $http->DownloadToString();
		$success = (trim($gateway['success']) == '' && trim($result) == '')
			|| (strpos(strtolower($result), strtolower($gateway['success'])) !== false);

		// log
		PutLog(sprintf('SMS success: %d; expected result: <%s>; gateway result: <%s> (userID: %d)',
			$success,
			$gateway['success'],
			$result,
			$this->_userID),
			PRIO_DEBUG,
			__FILE__,
			__LINE__);

		// ok?
		if($success)
		{
			// status ID?
			if(preg_match('/Status\:([0-9]*)/', $result, $reg) && is_array($reg) && isset($reg[1]))
				$statusID = $reg[1];
			else
				$statusID = -1;

			// stats
			Add2Stat('sms');

			// charge, if requested
			if($charge)
				$outboxID = $this->_userObject->Debit($type['price']*-1, $lang_user['tx_sms']);

			// put to outbox?
			if($putToOutbox)
				if($outboxID == 0)
				{
					$db->Query('INSERT INTO {pre}smsend(user,monat,price,isSMS,`from`,`to`,`text`,statusid,`date`) VALUES(?,?,?,?,?,?,?,?,?)',
						$this->_userID,
						(int)date('mY'),
						0,
						1,
						$from,
						$to,
						$text,
						$statusID,
						time());
					$outboxID = $db->InsertId();
				}
				else
					$db->Query('UPDATE {pre}smsend SET isSMS=?,`from`=?,`to`=?,`text`=?,statusid=?,`date`=? WHERE id=? AND user=?',
						1,
						$from,
						$to,
						$text,
						$statusID,
						time(),
						$outboxID,
						$this->_userID);

			// module handler
			ModuleFunction('AfterSendSMS', array(true, $result, $outboxID));

			// log
			PutLog(sprintf('Sent SMS from <%s> to <%s> (type: %d; statusID: %d; charged: %d; userID: %d)',
				$from,
				$to,
				$type['id'],
				$statusID,
				$charge ? $type['price'] : 0,
				$this->_userID),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			return(true);
		}
		else
		{
			// module handler
			ModuleFunction('AfterSendSMS', array(false, $result, $outboxID));

			// log
			PutLog(sprintf('Failed to send SMS from <%s> to <%s> (type: %d; charged: 0; userID: %d)',
				$from,
				$to,
				$type['id'],
				$this->_userID),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			return(false);
		}
	}
}
