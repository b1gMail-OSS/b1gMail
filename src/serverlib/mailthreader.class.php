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

/**
 * mail threader
 */
class BMMailThreader
{
	var $_messages;
	var $_userID;
	var $_userObject;

	/**
	 * constructor
	 *
	 * @param int $userID User ID
	 * @return BMMailThreader
	 */
	function __construct($userID, &$userObject)
	{
		$this->_userID = (int)$userID;
		$this->_userObject = &$userObject;
	}

	/**
	 * process a set of references
	 *
	 * @param array $refs References
	 * @param int $level Base level
	 * @param string $base Base message ID
	 */
	function _processReferences($refs, $level, $base)
	{
		global $db;

		// base in refs?
		if(!in_array($base, $refs))
			return;

		// search base
		$baseKey = array_search($base, $refs);

		// process refs
		foreach($refs as $i=>$ref)
			if(!isset($this->_messages[$ref]))
			{
				$thisLevel = $i+($level-$baseKey);
				$this->_messages[$ref] = array('level' => $thisLevel);

				// find messages related to this message
				$res = $db->Query('SELECT refs,msg_id FROM {pre}mails WHERE refs LIKE ? AND userid=?',
					'%'.$ref.'%',
					$this->_userID);
				while($row = $res->FetchArray(MYSQLI_ASSOC))
				{
					$this->_processReferences(array_merge(ExtractMessageIDs($row['refs']), array($row['msg_id'])),
						$thisLevel,
						$ref);
				}
				$res->Free();
			}
	}

	/**
	 * normalize levels
	 *
	 */
	function _normalizeLevels()
	{
		$smallestLevel = 0;
		foreach($this->_messages as $val)
			if($val['level'] < $smallestLevel)
				$smallestLevel = $val['level'];

		foreach($this->_messages as $key=>$val)
			$this->_messages[$key]['level'] += abs($smallestLevel);
	}

	/**
	 * fetch messages
	 *
	 */
	function _fetchMessages()
	{
		global $db;

		$res = $db->Query('SELECT id,von,betreff,datum,msg_id FROM {pre}mails WHERE userid=? AND msg_id IN ?',
			$this->_userID,
			array_keys($this->_messages));
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$this->_messages[$row['msg_id']] = array(
				'id'			=> $row['id'],
				'level'			=> $this->_messages[$row['msg_id']]['level'],
				'from_name'		=> ExtractMailName($row['von']),
				'from_mail'		=> ExtractMailAddress($row['von']),
				'date'			=> $row['datum'],
				'subject'		=> $row['betreff']
			);
		}
		$res->Free();
	}

	/**
	 * get thread of a certain mail
	 *
	 * @param int $mailID Mail ID
	 * @return array
	 */
	function GetThread($mailID)
	{
		global $db;

		// get message info
		$res = $db->Query('SELECT refs,msg_id FROM {pre}mails WHERE id=? AND userid=?',
			(int)$mailID,
			$this->_userID);
		if($res->RowCount() == 0)
			return(array());
		$mailRow = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// run
		$this->_messages = array();
		$this->_processReferences(array_merge(ExtractMessageIDs($mailRow['refs']), array($mailRow['msg_id'])), 0, $mailRow['msg_id']);
		$this->_fetchMessages();
		$this->_normalizeLevels();

		// return
		return($this->_messages);
	}
}
