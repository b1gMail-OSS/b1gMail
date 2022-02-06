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
 * filter evaluation class
 *
 */
class BMFilterEval
{
	/**
	 * user object
	 *
	 * @var BMUser
	 */
	var $_user;

	/**
	 * mail object
	 *
	 * @var BMMail
	 */
	var $_mail;

	/**
	 * store result
	 *
	 * @var int
	 */
	var $_storeResult;

	/**
	 * mail folder
	 *
	 * @var int
	 */
	var $_folder;

	/**
	 * action flags
	 *
	 * @var int
	 */
	var $_flags;

	/**
	 * forward addresses
	 *
	 * @var array
	 */
	var $_forwardTo;

	/**
	 * respond draft IDs
	 *
	 * @var int
	 */
	var $_respondWith;

	/**
	 * new mail color
	 *
	 * @var int
	 */
	var $_mailColor;

	/**
	 * constructor
	 *
	 * @param BMUser $user User
	 * @param BMMail $mail Mail
	 * @return BMFilterEval
	 */
	function __construct(&$user, &$mail)
	{
		$this->_user = &$user;
		$this->_mail = &$mail;
		$this->_storeResult = STORE_RESULT_OK;
		$this->_folder = -1;
		$this->_flags = 0;
		$this->_mailColor = 0;
		$this->_forwardTo = array();
		$this->_respondWith = array();
	}

	/**
	 * check if filter conditions match mail
	 *
	 * @param int $filterID
	 * @param int $link
	 * @return bool
	 */
	function FilterMatches($filterID, $link)
	{
		$conditions = $this->_user->GetFilterConditions($filterID);
		$result = false;

		foreach($conditions as $conditionID=>$condition)
		{
			$field = $condition['field'];
			$op = $condition['op'];
			$val = trim($condition['val']);
			$data = '';
			$res = false;

			// get data of field
			if($field == MAILFIELD_SUBJECT)
				$data = trim($this->_mail->GetHeaderValue('subject'));
			else if($field == MAILFIELD_FROM)
				$data = trim(DecodeEMail($this->_mail->GetHeaderValue('from')));
			else if($field == MAILFIELD_TO)
				$data = trim(DecodeEMail($this->_mail->GetHeaderValue('to')));
			else if($field == MAILFIELD_CC)
				$data = trim(DecodeEMail($this->_mail->GetHeaderValue('cc')));
			else if($field == MAILFIELD_PRIORITY)
				$data = $this->_mail->priority;
			else if($field == MAILFIELD_ATTACHLIST)
			{
				$data = '';
				$attachments = $this->_mail->GetAttachments();
				foreach($attachments as $info)
					$data .= ' ' . $info['filename'];
			}

			// process field
			if($field == MAILFIELD_SUBJECT
				|| $field == MAILFIELD_FROM
				|| $field == MAILFIELD_TO
				|| $field == MAILFIELD_CC
				|| $field == MAILFIELD_ATTACHLIST)
			{
				if($op == BMOP_CONTAINS)
				{
					if(strlen($val) > 0 && strlen($data) > 0)
						$res = strpos(strtolower($data), strtolower($val)) !== false;
					else
						$res = false;
				}
				else if($op == BMOP_ENDSWITH)
				{
					$res = strtolower(substr($data, -(strlen($val)))) == strtolower($val);
				}
				else if($op == BMOP_EQUAL)
				{
					if($field == MAILFIELD_FROM
						|| $field == MAILFIELD_TO
						|| $field == MAILFIELD_CC)
					{
						$res = strtolower($data) == strtolower($val)
							|| in_array(strtolower($val), ExtractMailAddresses(strtolower($data)));
					}
					else
					{
						$res = strtolower($data) == strtolower($val);
					}
				}
				else if($op == BMOP_NOTCONTAINS)
				{
					if(strlen($val) > 0 && strlen($data) > 0)
						$res = strpos(strtolower($data), strtolower($val)) === false;
					else
						$res = true;
				}
				else if($op == BMOP_NOTEQUAL)
				{
					$res = strtolower($data) != strtolower($val);
				}
				else if($op == BMOP_STARTSWITH)
				{
					$res = strtolower(substr($data, 0, strlen($val))) == strtolower($val);
				}
			}
			else if($field == MAILFIELD_PRIORITY)
			{
				$res = ($data == ITEMPRIO_HIGH && $val == 'high')
						|| ($data == ITEMPRIO_LOW && $val == 'low')
						|| ($data == ITEMPRIO_NORMAL && $val == 'normal');
			}
			else if($field == MAILFIELD_ATTACHMENT)
			{
				$res = (($this->_mail->flags & FLAG_ATTACHMENT) != 0
						&& $val == 'yes')
						|| (($this->_mail->flags & FLAG_ATTACHMENT) == 0
							&& $val == 'no');
			}

			// debug log
			PutLog(sprintf('User filter condition <%d>: %d=<%s> %d <%s>: %s',
				$conditionID,
				$field,
				$data,
				$op,
				$val,
				$res ? 'true' :  'false'),
				PRIO_DEBUG,
				__FILE__,
				__LINE__);

			// go on?
			if($link == BMLINK_AND)
			{
				if($res)
				{
					$result = true;
				}
				else
				{
					$result = false;
					break;
				}
			}
			else if($link == BMLINK_OR)
			{
				if($res)
				{
					$result = true;
					break;
				}
			}
		}

		return($result);
	}

	/**
	 * execute filter actions
	 *
	 * @param int $filterID Filter ID
	 * @param int $filterFlags Filter flags
	 * @return bool
	 */
	function ExecuteActions($filterID, $filterFlags = 0)
	{
		$goOn = true;
		$actions = $this->_user->GetFilterActions($filterID);

		foreach($actions as $actionID=>$action)
		{
			$op = $action['op'];
			$val = $action['val'];
			$textVal = $action['text_val'];

			if($op == FILTER_ACTION_BLOCK)
			{
				$this->_storeResult = RECEIVE_RESULT_BLOCKED;
			}
			else if($op == FILTER_ACTION_DELETE)
			{
				$this->_storeResult = RECEIVE_RESULT_DELETE;
			}
			else if($op == FILTER_ACTION_MARK
					&& ($this->_mail->flags & FLAG_FLAGGED) == 0)
			{
				$this->_mail->flags |= FLAG_FLAGGED;
			}
			else if($op == FILTER_ACTION_MARKDONE
					&& ($this->_mail->flags & FLAG_DONE) == 0)
			{
				$this->_mail->flags |= FLAG_DONE;
			}
			else if($op == FILTER_ACTION_MARKREAD
					&& ($this->_mail->flags & FLAG_UNREAD) == 1)
			{
				$this->_mail->flags &= ~(FLAG_UNREAD);
			}
			else if($op == FILTER_ACTION_MARKSPAM
					&& ($this->_mail->flags & FLAG_SPAM) == 0)
			{
				$this->_mail->flags |= FLAG_SPAM;
			}
			else if($op == FILTER_ACTION_MOVETO)
			{
				$this->_folder = (int)$val;

				if(($filterFlags & FILTER_ACTIONFLAG_DO_NOT_OVERRIDE_SPAMFILTER) != 0)
					$this->_flags |= FILTER_ACTIONFLAG_DO_NOT_OVERRIDE_SPAMFILTER;
			}
			else if($op == FILTER_ACTION_SENDSMS)
			{
				$this->_flags |= FILTER_ACTIONFLAG_MAIL2SMS;
			}
			else if($op == FILTER_ACTION_NOTIFY)
			{
				$this->_flags |= FILTER_ACTIONFLAG_NOTIFY;
			}
			else if($op == FILTER_ACTION_RESPOND)
			{
				$this->_flags |= FILTER_ACTIONFLAG_RESPOND;
				$this->_respondWith[] = $val;
			}
			else if($op == FILTER_ACTION_FORWARD)
			{
				$this->_flags |= FILTER_ACTIONFLAG_FORWARD;
				$this->_forwardTo[] = $textVal;
			}
			else if($op == FILTER_ACTION_SETCOLOR)
			{
				$this->_mailColor = (int)$val;
			}
			else if($op == FILTER_ACTION_STOP)
			{
				$goOn = false;
			}
		}

		return($goOn);
	}

	/**
	 * eval all user filters
	 *
	 * @return array Store result, Folder ID, Flags
	 */
	function EvalFilters()
	{
		$filters = $this->_user->GetFilters();

		foreach($filters as $filterID=>$filter)
		{
			if($filter['active'] == 1)
			{
				// debug log
				PutLog(sprintf('Processing user filter <%d>',
					$filterID),
					PRIO_DEBUG,
					__FILE__,
					__LINE__);
				if($this->FilterMatches($filterID, $filter['link']))
				{
					// debug log
					PutLog(sprintf('User filter <%d> matches',
						$filterID),
						PRIO_DEBUG,
						__FILE__,
						__LINE__);

					$this->_user->IncFilter($filterID);
					if(!$this->ExecuteActions($filterID, $filter['flags']))
					{
						// debug log
						PutLog(sprintf('User filter <%d> aborted further filter processing',
							$filterID),
							PRIO_DEBUG,
							__FILE__,
							__LINE__);
						break;
					}
				}
			}
		}

		return(array($this->_storeResult, $this->_folder, $this->_flags, $this->_mailColor));
	}
}
