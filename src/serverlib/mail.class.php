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

if(!class_exists('BMMailParser'))
	include(B1GMAIL_DIR . 'serverlib/mailparser.inc.php');

/**
 * mail class
 */
class BMMail
{
	var $_userID;
	var $_userObject;
	var $_row;
	var $_fp;
	var $_parsed;
	var $_useCache;
	var $_msgFileName;
	var $trusted;
	var $infection;
	var $id;
	var $flags;
	var $date;
	var $priority;
	var $trained;
	var $blobStorage;
	var $color;
	var $smimeStatus = SMIME_UNKNOWN;
	var $smimeCertificateHash = false;

	/**
	 * constructor
	 *
	 * @param array $row Message row
	 * @return BMMail
	 */
	function __construct($userID, $row = false, $fp = false, $useCache = true, $msgFileName = false, &$userObject = false)
	{
		$this->_useCache = $useCache;
		$this->_userID = $userID;
		$this->_userObject = &$userObject;
		$this->_parsed = false;
		if($row !== false)
			$this->_fromRow($row);
		$this->_fp = $fp;
		$this->_msgFileName = $msgFileName;

		ModuleFunction('OnGetMail', array($this->id, $this->_userID));
	}

	/**
	 * parse mail
	 *
	 * @return void
	 */
	function Parse()
	{
		global $bm_prefs, $db, $cacheManager;

		if($this->_parsed !== false)
			return;

		if($this->_fp === false)
			$this->_fp = $this->GetMessageFP();

		//
		// mail in cache?
		//
		if($this->_useCache
			&& $this->id > 0)
		{
			// try to find parsed message in cache
			$this->_parsed = $cacheManager->Get('parsedMsg:' . $this->_userID . ':' . $this->id);

			if($this->_parsed)
			{
				$this->_parsed->InheritFP($this->_fp);
				return;
			}
			else
				$createCacheFile = true;
		}

		$this->_parsed = _new('BMMailParser', array($this->_fp));
		$this->_parsed->Parse();

		//
		// create cache entry?
		//
		if(isset($createCacheFile) && $createCacheFile)
			$cacheManager->Add('parsedMsg:' . $this->_userID . ':'  . $this->id, $this->_parsed, TIME_ONE_DAY);
	}

	/**
	 * get part list
	 *
	 * @return array
	 */
	function GetPartList()
	{
		$this->Parse();
		return($this->_parsed->GetPartList());
	}

	/**
	 * load mail from row
	 *
	 * @param array $row Row (of bm60_mails)
	 */
	function _fromRow($row)
	{
		$this->_row			= $row;
		$this->id			= $row['id'];
		$this->blobStorage 	= $row['blobstorage'];

		$this->date			= $row['datum'];
		$this->priority		= $row['priority'] == 'high' ? ITEMPRIO_HIGH :
									($row['priority'] == 'low' ? ITEMPRIO_LOW :
									ITEMPRIO_NORMAL);
		$this->flags		= $row['flags'];
		$this->infection	= $row['virnam'];
		$this->trained		= $row['trained'] == 1;
		$this->color 		= isset($row['color']) ? $row['color'] : 0;
	}

	/**
	 * check if mail is trusted
	 *
	 * @return bool
	 */
	function IsTrusted()
	{
		if(($trustToken = $this->GetHeaderValue('x-b1gmail-trusted')) != '')
		{
			$validToken = GenerateTrustToken($this->GetHeaderValue('message-id'),
				$this->GetHeaderValue('from'),
				$this->GetHeaderValue('to'),
				$this->GetHeaderValue('subject'));
			return($validToken == $trustToken);
		}
	}

	/**
	 * check if mail is signed
	 *
	 * @return bool
	 */
	function IsSigned()
	{
		$this->Parse();
		$partList = $this->_parsed->GetPartList();

		return(isset($partList[1]) && isset($partList[1]['content-type'])
				&& in_array(strtolower($partList[1]['content-type']), array('multipart/signed'/*, 'multipart/encrypted'*/)));
	}

	/**
	 * check if mail is encrypted
	 *
	 * @return bool
	 */
	function IsEncrypted()
	{
		$this->Parse();
		$partList = $this->_parsed->GetPartList();

		return(isset($partList[1]) && isset($partList[1]['content-type'])
				&& in_array(strtolower($partList[1]['content-type']), array('application/pkcs7-mime', 'application/x-pkcs7-mime')));
	}

	/**
	 * mark mail as trained
	 *
	 */
	function MarkAsTrained()
	{
		global $db;

		$db->Query('UPDATE {pre}mails SET trained=1 WHERE id=?',
			$this->_row['id']);
	}

	/**
	 * get header value
	 *
	 * @param string $key Field key
	 * @return string
	 */
	function GetHeaderValue($key)
	{
		$this->Parse();
		return($this->_parsed->rootPart->header->GetValueOnly($key));
	}

	/**
	 * get mail plain text for full text search
	 *
	 * @param bool $withSubject
	 * @return string
	 */
	function GetSearchText($withSubject = true)
	{
		$textParts = $this->GetTextParts();
		$text = '';

		if(isset($textParts['text']))
			$text = trim($textParts['text']);

		if(isset($textParts['html']) && strlen($text) < 3)
			$text = trim(htmlToText($textParts['html']));

		if($withSubject)
		{
			$from = DecodeEMail(ExtractMailAddress($this->GetHeaderValue('from')));
			$to = DecodeEMail(ExtractMailAddress($this->GetHeaderValue('to')));

			$text = '(' . $from . ' -> ' . $to . ': ' . $this->GetHeaderValue('subject') . ') ' . $text;
		}

		return($text);
	}

	/**
	 * add mail to a search index
	 *
	 * @param BMSearchIndex $idx
	 */
	function AddToIndex(&$idx)
	{
		$text = $this->GetSearchText();
		$idx->addTextToIndex($text, $this->id);
	}

	/**
	 * get text parts
	 *
	 * @return array
	 */
	function GetTextParts()
	{
		$this->Parse();
		$result = array('html' => '', 'text' => '');
		$parts = $this->GetPartList();

		foreach($parts as $id=>$info)
		{
			// we just need inline parts
			if(strtolower($info['content-disposition']) != 'attachment'
				&& (!isset($info['filename']) || trim($info['filename']) == 'unnamed'))
			{
				if(strtolower($info['content-type']) == 'text/plain'
					|| strtolower($info['content-type']) == 'text'
					|| strtolower($info['content-type']) == 'plain'
					|| strtolower($info['content-type']) == '')
				{
					$textBody = &$info['body'];
					if($textBody !== false)
					{
						$textBody->Init();
						$textPart = $textBody->DecodeBlock(-1);
						$textBody->Finish();
					}
					else
						$textPart = '';
					$result['text'] .= CharsetDecode($textPart, $info['charset']!=''?$info['charset']:FALLBACK_CHARSET);
				}
				else if(strtolower($info['content-type']) == 'text/html')
				{
					$htmlBody = &$info['body'];
					if($htmlBody !== false)
					{
						$htmlBody->Init();
						$htmlPart = $htmlBody->DecodeBlock(-1);
						$htmlBody->Finish();
					}
					else
						$htmlPart = '';
					$result['html'] .= CharsetDecode($htmlPart, $info['charset']!=''?$info['charset']:FALLBACK_CHARSET);
				}
			}
		}

		if($result['html'] == '')
			unset($result['html']);
		if($result['text'] == '')
			unset($result['text']);

		return($result);
	}

	/**
	 * get mail attachments
	 *
	 * @return array
	 */
	function GetAttachments()
	{
		global $VIEWABLE_TYPES;

		$this->Parse();
		$result = array();
		$parts = $this->GetPartList();
		$attachmentTypes = array('image', 'application');
		$attachmentTypes2 = array(	'text/calendar' 	=> 'ics',
									'text/vcard' 		=> 'vcf',
									'text/x-vcard'		=> 'vcf'	);

		foreach($parts as $id=>$info)
		{
			$contentType = strtolower($info['content-type']);
			list($primType) = explode('/', $contentType);

			if(strtolower($info['content-disposition']) == 'attachment'
				|| in_array($primType, $attachmentTypes)
				|| isset($attachmentTypes2[$contentType])
				|| (isset($info['filename']) && trim($info['filename']) != 'unnamed'))
			{
				$fileName = $info['filename'];
				if(isset($attachmentTypes2[$contentType]) && strpos($fileName, '.') == false)
					$fileName .= '.' . $attachmentTypes2[$contentType];

				$result[$id] = array(
					'filename'		=> $fileName,
					'size'			=> $this->EstimatePartSize($info),
					'mimetype'		=> $info['content-type'],
					'cid'			=> $info['content-id'],
					'viewable'		=> in_array(strtolower($info['content-type']), $VIEWABLE_TYPES),
					'filetype'		=> strtolower(substr($info['filename'], -4))
				);
			}
		}

		return($result);
	}

	/**
	 * estimate part size
	 *
	 * @param array $info
	 * @return int
	 */
	function EstimatePartSize(&$info)
	{
		if($info['body'] !== false)
			$result = $info['body']->offsetLength;
		else
			$result = 0;

		switch($info['content-transfer-encoding'])
		{
		case 'base64':
			$result = round(((($result - 2 * ($result / 72)) / 4) * 3), 0);
			break;
		}

		return($result);
	}

	/**
	 * save attachment to file pointer
	 *
	 * @param string $key
	 * @param resource $fp
	 * @return bool
	 */
	function AttachmentToFP($key, $fp)
	{
		$parts = $this->GetPartList();
		if(isset($parts[$key]))
		{
			$part = $parts[$key];
			$attData = &$part['body'];
			if($attData !== false)
			{
				$attData->Init();
				while($block = $attData->DecodeBlock(PART_CHUNK_SIZE))
				{
					fwrite($fp, $block);
				}
				$attData->Finish();
				fseek($fp, 0, SEEK_SET);
				return(true);
			}
			else
				return(false);
		}
		return(false);
	}

	/**
	 * get message as XML array
	 *
	 * @return array
	 */
	function GetXML()
	{
		$this->Parse();
		$result = array();

		$result['mail'] = array(
				'#nodeParam'		=> array(
					'id'				=> $this->id,
					'flags'				=> $this->flags,
					'from_name'			=> ExtractMailName($this->GetHeaderValue('from')),
					'from_mail'			=> ExtractMailAddress($this->GetHeaderValue('from')),
					'timestamp'			=> $this->date,
					'subject'			=> $this->GetHeaderValue('subject'),
					'priority'			=> $this->priority
				)
		);

		$result['recipients'] = array();
		$recps = array(
			'to'	=> ParseMailList($this->GetHeaderValue('to')),
			'cc'	=> ParseMailList($this->GetHeaderValue('cc')),
			'bcc'	=> ParseMailList($this->GetHeaderValue('bcc'))
		);

		foreach($recps as $key=>$val)
			foreach($val as $recp)
				$result['recipients'][] = array(
					'#nodeName'		=> $key,
					'#nodeParam'	=> array(
						'name'			=> $recp['name'],
						'mail'			=> $recp['mail'],
						'inbook'		=> 0
					)
				);

		$result['parts'] = array();
		$textParts = $this->GetTextParts();
		$partID = 0;
		foreach($textParts as $type=>$content)
			if($content != '')
				$result['parts'][] = array(
					'#nodeName'			=> 'part',
					'#nodeParam'		=> array(
						'type'				=> PART_TYPE_TEXT,
						'content'			=> $type,
						'id'				=> $partID++
					),
					'#nodeCDATA'		=> $content
				);

		$attachments = $this->GetAttachments();
		foreach($attachments as $key=>$att)
			$result['parts'][] = array (
				'#nodeName'				=> 'part',
				'#nodeParam'			=> array(
					'type'					=> PART_TYPE_ATTACHMENT,
					'content'				=> $att['filename'],
					'id'					=> $key,
					'size'					=> $att['size'],
					'mimetype'				=> $att['mimetype']
				)
			);

		return($result);
	}

	/**
	 * get message file pointer
	 *
	 * @return resource
	 */
	function GetMessageFP($allowOverride = true)
	{
		global $plugins;

		// plugin?
		foreach($plugins->_plugins as $className=>$pluginInfo)
		{
			if(($result = $plugins->callFunction('OnGetMessageFP',  $className, false,
							array($this->id, $allowOverride, &$this))) !== false
				&& is_resource($result))
			{
				return($result);
			}
		}

		return(BMBlobStorage::createProvider($this->blobStorage, $this->_userID)->loadBlob(BMBLOB_TYPE_MAIL, $this->id));
	}

	/**
	 * get message size
	 *
	 * @return int
	 */
	function GetMessageSize($allowOverride = true)
	{
		return(BMBlobStorage::createProvider($this->blobStorage, $this->_userID)->getBlobSize(BMBLOB_TYPE_MAIL, $this->id));
	}

	/**
	 * get message references
	 *
	 * @return array
	 */
	function GetReferences()
	{
		// get header values
		$references = $this->GetHeaderValue('references');
		$inReplyTo = $this->GetHeaderValue('in-reply-to');

		// extract message ids
		return(ExtractMessageIDs($references . ' ' . $inReplyTo));
	}

	/**
	 * parse some mail info
	 *
	 */
	function ParseInfo()
	{
		$this->Parse();

		// not trained
		$this->trained = false;

		// attachments?
		if(count($this->GetAttachments()) > 0
			&& ($this->flags & FLAG_ATTACHMENT) == 0)
			$this->flags |= FLAG_ATTACHMENT;

		// date?
		if($this->GetHeaderValue('date') != ''
			&& ($this->date = strtotime($this->GetHeaderValue('date'))) < 1)
			$this->date = time();

		// priority?
		switch((int)$this->GetHeaderValue('x-priority'))
		{
		case 1:
			$this->priority = ITEMPRIO_HIGH;
			break;

		case 5:
			$this->priority = ITEMPRIO_LOW;
			break;

		default:
			$this->priority = ITEMPRIO_NORMAL;
			break;
		}
	}

	/**
	 * generate and return mail thread
	 *
	 * @return array
	 */
	function GetThread()
	{
		if(!class_exists('BMMailThreader'))
			include(B1GMAIL_DIR . 'serverlib/mailthreader.class.php');

		// get thread
		$threader = _new('BMMailThreader', array($this->_userID, &$this->_userObject));
		$thread = $threader->GetThread($this->id);
		return($thread);
	}

	/**
	 * send disposition notification for mail
	 *
	 * @return bool
	 */
	function SendDispositionNotification()
	{
		global $lang_user;

		$to = ExtractMailAddress($this->GetHeaderValue('disposition-notification-to'));

		if($to != '' && ($this->flags & FLAG_DNSENT) == 0)
		{
			$subject = $lang_user['read'] . ': ' . $this->GetHeaderValue('subject');
			$vars = array(
				'subject'	=> $this->GetHeaderValue('subject'),
				'date'		=> date($this->_userObject->_row['datumsformat'])
			);
			if(SystemMail($this->_userObject->GetDefaultSender(), $to, $subject, 'receipt_text', $vars))
				return(true);
		}

		return(false);
	}

	function GetDeliveryStatus()
	{
		global $db, $lang_user;

		$result = array('statusText' => '',
			'processingCount' => 0,
			'deliveredCount' => 0,
			'deferredCount' => 0,
			'failedCount' => 0,
			'allCount' => 0,
			'allDelivered' => false,
			'exception' => false,
			'recipients' => array());

		$res = $db->Query('SELECT `recipient`,`status`,`delivered_to`,`updated` FROM {pre}maildeliverystatus WHERE `outboxid`=? ORDER BY `recipient` ASC',
			$this->_row['id']);
		if($res->RowCount() == 0)
			return false;
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			++$result['allCount'];
			if($row['status'] == MDSTATUS_DELIVERED_BY_MTA)
				++$result['deliveredCount'];
			else if($row['status'] == MDSTATUS_DELIVERY_DEFERRED)
				++$result['deferredCount'];
			else if($row['status'] == MDSTATUS_DELIVERY_FAILED)
				++$result['failedCount'];
			else if($row['status'] == MDSTATUS_SUBMITTED_TO_MTA || $row['status'] == MDSTATUS_RECEIVED_BY_MTA)
				++$result['processingCount'];
			$result['recipients'][] = $row;
		}
		$res->Free();

		$result['allDelivered'] = ($result['deliveredCount'] == $result['allCount']);

		if($result['deliveredCount'])
			$result['statusText'] .= ' ' . sprintf($lang_user['mds_delivered'], $result['deliveredCount']);
		if($result['deferredCount'])
			$result['statusText'] .= ' ' . sprintf($lang_user['mds_deferred'], $result['deferredCount']);
		if($result['failedCount'])
		{
			$result['statusText'] .= ' ' . sprintf($lang_user['mds_failed'], $result['failedCount']);
			$result['exception'] = true;
		}

		if(empty($result['statusText']) && $result['processingCount'] > 0)
			$result['statusText'] .= $lang_user['mds_processing'];

		return $result;
	}
}
