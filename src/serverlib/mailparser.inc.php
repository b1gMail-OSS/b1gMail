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
 * maximum line size for php < 4.3.0
 */
define('PARSER_LINE_MAX', 8096);

/**
 * MailParser body class - represents a part body
 *
 */
class BMMailParser_PartBody
{
	var $_fp;
	var $oldOffset;
	var $offsetStart;
	var $offsetLength;
	var $contentEncoding;
	var $contentType;

	/**
	 * constructor
	 *
	 * @param resource $fp Pointer to mail file
	 * @param int $offsetStart Start offset
	 * @param int $offsetLength Length
	 * @param BMMailParser_Header $header Reference to header object
	 * @return BMMailParser_PartBody
	 */
	function __construct($fp, $offsetStart, $offsetLength, &$header)
	{
		$this->_fp = $fp;
		$this->oldOffset = ftell($this->_fp);
		$this->offsetStart = $offsetStart;
		$this->offsetLength = $offsetLength;
		$this->contentEncoding = $header->GetValueOnly('content-transfer-encoding');
		$this->contentType = $header->GetValueOnly('content-type');
	}

	/**
	 * prepare for body read
	 *
	 * @return void
	 */
	function Init()
	{
		fseek($this->_fp, $this->offsetStart, SEEK_SET);
		while(in_array($c = fgetc($this->_fp), array(' ', "\n", "\r")))
		{
			--$this->offsetLength;
			++$this->offsetStart;
		}
		fseek($this->_fp, -1, SEEK_CUR);
	}

	/**
	 * finalize body read
	 *
	 * @return void
	 */
	function Finish()
	{
		fseek($this->_fp, $this->oldOffset, SEEK_SET);
	}

	/**
	 * read body block
	 *
	 * @param int $size Block size
	 * @return string
	 */
	function Read($size)
	{
		if(ftell($this->_fp) + $size > $this->offsetStart + $this->offsetLength)
			$size = ($this->offsetStart + $this->offsetLength) - ftell($this->_fp);
		$str = str_replace(array("\n", "\r", " "), '', fread($this->_fp, $size));
		while((strlen($str) < $size) && (ftell($this->_fp) <= $this->offsetStart + $this->offsetLength) && is_resource($this->_fp) && !feof($this->_fp))
			$str = $str . trim(fgetc($this->_fp));
		return($str);
	}

	/**
	 * read and decode body block
	 *
	 * @param int $size Block size (multiple of 4)
	 * @return string
	 */
	function DecodeBlock($size)
	{
		if(ftell($this->_fp) >= $this->offsetStart + $this->offsetLength)
			return(false);

		if($size == -1)
			$size = $this->offsetLength;

		if(strtolower($this->contentEncoding) == 'base64')
			$size += $size % 4;

		$size = min($size, max(0, ($this->offsetStart + $this->offsetLength) - ftell($this->_fp)));

		if($size == 0)
			return('');

		switch(strtolower($this->contentEncoding))
		{
		case 'base64':
			$block = $this->Read($size);
			$block = base64_decode($block);
			break;

		case 'quoted-printable':
			$block = fread($this->_fp, $size);

			if(substr($block, -1, 1) == '=')
				$block .= fread($this->_fp, 2);
			else if(substr($block, -2, 1) == '=')
				$block .= fread($this->_fp, 1);

			$block = str_replace(array("=\n", "=\r\n"), '', $block);
			$block = quoted_printable_decode($block);
			break;

		default:
			$block = fread($this->_fp, $size);
			return($block);
		}

		return($block);
	}
}

/**
 * MailParser header class - represents a part header
 *
 */
class BMMailParser_Header
{
	var $items;
	var $_lastKey;

	/**
	 * constructor
	 *
	 * @return BMMailParser_Header
	 */
	function __construct()
	{
		$this->items = array();
		$this->_lastKey = '';
	}

	/**
	 * get value of a header field, even if it contains more parameters
	 *
	 * @param string $key Field key
	 * @return string
	 */
	function GetValueOnly($key)
	{
		if(!isset($this->items[$key]))
			return('');
		if(is_array($this->items[$key]))
			return($this->items[$key]['value']);
		return($this->items[$key]);
	}

	/**
	 * get value of a parameter of a header field
	 *
	 * @param string $key Field key
	 * @param string $param Parameter key
	 * @return string
	 */
	function GetValueParam($key, $param)
	{
		if(isset($this->items[$key]) && isset($this->items[$key]['params']) && isset($this->items[$key]['params'][$param]))
			return($this->items[$key]['params'][$param]);
		else
			return('');
	}

	/**
	 * parse a header line
	 *
	 * @param string $line
	 * @return void
	 */
	function ParseLine($line)
	{
		$line = rtrim($line, "\r\n");
		$dotPos = strpos($line, ':');
		$spacePos = strpos($line, ' ');
		$tabPos = strpos($line, "\t");

		if($spacePos === false)
			$spacePos = $tabPos;
		else if($tabPos !== false && $tabPos < $spacePos)
			$spacePos = $tabPos;

		if($dotPos !== false && ($spacePos === false || $dotPos < $spacePos))
		{
			$fieldKey = strtolower(trim(substr($line, 0, $dotPos)));
			$fieldVal = trim(substr($line, $dotPos+1));

			if(isset($this->items[$fieldKey]) && ($fieldKey == 'received' || $fieldKey == 'references' || $fieldKey == 'delivered-to'))
				$this->items[$fieldKey] .= ' ' . $fieldVal;
			else
				$this->items[$fieldKey] = $fieldVal;
			$this->_lastKey = $fieldKey;
		}
		else if(($dotPos === false || ($spacePos !== false && $dotPos > $spacePos)) && $this->_lastKey != '')
		{
			$this->items[$this->_lastKey] .= ' '  . $line;
		}
	}

	/**
	 * split a header value if it contains parameters
	 *
	 * @param string $value Value
	 * @return mixed Array or string
	 */
	function SplitParams($value)
	{
		$result = $value;

		// parameters possible?
		if(($semiPos = strpos($value, ';')) !== false)
		{
			$result = array();

			$result['value'] = trim(substr($value, 0, $semiPos));
			$result['params'] = array();
			$paramString = trim(substr($value, $semiPos+1));

			$inQuote = $inString = false;
			$inKey = true;
			$tmpStr = $lastKey = '';
			$quoteChar = '';
			for($i=0; $i<strlen($paramString); $i++)
			{
				$c = $paramString[$i];

				if($inQuote)
					$tmpStr .= $c;
				else if($c == '\\')
					$inQuote = true;
				else if($c == '"' || $c == '\'')
				{
					if(!$inString)
					{
						$quoteChar = $c;
						$inString = true;
					}
					else
					{
						if($quoteChar == $c)
							$inString = false;
						else
							$tmpStr .= $c;
					}
				}
				else if($c == ';' && !$inString)
				{
					$result['params'][$lastKey] = trim($tmpStr);
					$lastKey = '';
					$inKey = true;
					$tmpStr = '';
				}
				else if($c == '=' && !$inString && $inKey)
				{
					$inKey = false;
					$lastKey = strtolower(trim($tmpStr));
					$tmpStr = '';
				}
				else
					$tmpStr .= $c;
			}

			if($lastKey != '')
				$result['params'][$lastKey] = trim($tmpStr);
		}

		return($result);
	}

	/**
	 * decode every header entry
	 *
	 * @return void
	 */
	function DecodeHeaderEntries()
	{
		foreach($this->items as $key=>$val)
		{
			$this->items[$key] = DecodeMailHeaderField(CharsetDecode($val, FALLBACK_CHARSET));
		}
	}

	/**
	 * parse parameters of every header entry
	 *
	 * @return void
	 */
	function SplitParamsForEntries()
	{
		foreach($this->items as $key=>$val)
			if(!in_array(strtolower($key), array('to', 'cc', 'bcc', 'reply-to', 'received', 'subject')))
				$this->items[$key] = $this->SplitParams($this->items[$key]);
	}

	/**
	 * do finishing parsing work - must be called after parsing the header lines
	 *
	 * @return void
	 */
	function Finish()
	{
		$this->DecodeHeaderEntries();
		$this->SplitParamsForEntries();
	}
}

/**
 * MailParser part class - represents a mail part
 *
 */
class BMMailParser_Part
{
	var $_fp;
	var $_offset;
	var $_myBoundary;
	var $_isMultipart = false;
	var $_boundary = '';
	var $bodyOffset;
	var $bodyLength;
	var $subParts;
	var $header;
	var $body;

	/**
	 * constructor
	 *
	 * @param BMMailParser_Part $parent Part parent
	 * @param string $myBoundary End of part - boundary
	 * @return BMMailParser_Part
	 */
	function __construct(&$parent, $myBoundary)
	{
		$this->_fp = $parent->_fp;
		$this->_offset = ftell($this->_fp);
		$this->_myBoundary = $myBoundary;
		$this->body = false;
		$this->header = _new('BMMailParser_Header');
		$this->subParts = array();
	}

	/**
	 * parse the part
	 *
	 * @return void
	 */
	function Parse()
	{
		$this->ParseHeader();
		$this->ParseInfo();
		$this->ParseBody();
	}

	/**
	 * parse part info (multipart?)
	 *
	 * @return void
	 */
	function ParseInfo()
	{
		$contentType = strtolower($this->header->GetValueOnly('content-type'));

		if(strpos($contentType, 'multipart/') !== false)
			if(($this->_boundary = $this->header->GetValueParam('content-type', 'boundary')) != '')
				$this->_isMultipart = true;
		else
			$this->_isMultipart = false;
	}

	/**
	 * parse the part's body
	 *
	 * @return void
	 */
	function ParseBody()
	{
		// store body starting offset
		$this->bodyOffset = ftell($this->_fp);
		$lineLength = 0;

		// parse non-attachment message/rfc822-parts as subpart
		if(strtolower($this->header->GetValueOnly('content-disposition')) != 'attachment'
			&& strtolower($this->header->GetValueOnly('content-type')) == 'message/rfc822')
		{
			$newPart = _new('BMMailParser_Part', array($this, $this->_myBoundary));
			$newPart->Parse();
			$this->subParts[] = $newPart;
		}

		// default processing
		else
		{
			while(is_resource($this->_fp) && !feof($this->_fp))
			{
				$line = fgets2($this->_fp, PARSER_LINE_MAX);
				$lineLength = strlen($line);

				// break if my part is over
				if($this->_myBoundary !== false
					&& strpos($line, '--' . $this->_myBoundary) === 0)
				{
					if(strpos($line, '--' . $this->_myBoundary . '--') === false)
					{
						fseek($this->_fp, -$lineLength, SEEK_CUR);
						$lineLength = 0;
					}
					break;
				}

				// nextpart?
				if($this->_isMultipart)
				{
					if(strpos($line, '--' . $this->_boundary) === 0)
					{
						$newPart = _new('BMMailParser_Part', array($this, $this->_boundary));
						$newPart->Parse();
						$this->subParts[] = $newPart;
					}
				}
			}
		}

		// store body length
		$this->bodyLength = ftell($this->_fp) - $this->bodyOffset - (feof($this->_fp) ? 0 : $lineLength);

		// store body object
		$this->body = _new('BMMailParser_PartBody', array($this->_fp,
								$this->bodyOffset,
								$this->bodyLength,
								$this->header));
	}

	/**
	 * parse part header
	 *
	 * @return void
	 */
	function ParseHeader()
	{
		while(!$this->EmptyLine($line = fgets2($this->_fp, PARSER_LINE_MAX)) && is_resource($this->_fp) && !feof($this->_fp))
				$this->header->ParseLine($line);
		$this->header->Finish();
	}

	/**
	 * check if $line is an empty line (body/header separator)
	 *
	 * @param string $line Line
	 * @return bool
	 */
	function EmptyLine($line)
	{
		return($line == "\n" || $line == "\r\n" || $line == "");
	}

	/**
	 * inherit mail fp
	 *
	 * @param resource $fp
	 */
	function InheritFP($fp)
	{
		$this->_fp = $fp;

		foreach($this->subParts as $key=>$val)
			$this->subParts[$key]->InheritFP($fp);
	}
}

/**
 * MailParser
 *
 */
class BMMailParser
{
	var $_fp;
	var $_offset = 0;
	var $partList;
	var $rootPart;

	/**
	 * constructor
	 *
	 * @param resource $fp Pointer to mail file
	 * @return BMMailParser
	 */
	function __construct($fp)
	{
		$this->_fp = $fp;
		$this->rootPart = _new('BMMailParser_Part', array($this, false));
		$this->partList = false;
	}

	/**
	 * parse the mail
	 *
	 * @return void
	 */
	function Parse()
	{
		if(is_resource($this->_fp))
		{
			// time measurement
			$processingTime = microtime_float();

			// parse
			$this->rootPart->Parse();
			$this->GetPartList();

			// log time
			$processingTime = microtime_float() - $processingTime;
			if(DEBUG)
				PutLog(sprintf('Parsed mail in %.04f seconds (%d bytes; throughput: %.02f KB/s)',
					$processingTime,
					ftell($this->_fp),
					round(ftell($this->_fp) / $processingTime / 1024, 2)),
					PRIO_DEBUG,
					__FILE__,
					__LINE__);
		}
	}

	/**
	 * get a flat part list
	 *
	 * @return array
	 */
	function GetPartList()
	{
		if(!$this->partList)
		{
			$this->partList = array();
			$this->_generatePartListPart('1', $this->rootPart);
		}
		return($this->partList);
	}

	/**
	 * inherit mail fp (needed after restoring a BMMailParser-object from cache)
	 *
	 * @param resource $fp
	 */
	function InheritFP($fp)
	{
		$this->_fp = $fp;

		$this->rootPart->_fp = $fp;

		if($this->partList !== false)
		{
			foreach($this->partList as $key=>$val)
			{
				if($this->partList[$key]['body'] !== false)
					$this->partList[$key]['body']->_fp = $fp;
			}
		}

		$this->rootPart->InheritFP($fp);
	}

	/**
	 * helper for GeneratePartList
	 *
	 * @param string $part Part descriptor
	 * @param array $a Part array
	 */
	function _generatePartListPart($part, &$a)
	{
		$filename = $a->header->GetValueParam('content-disposition', 'filename');
		if($filename == '')
			$filename = $a->header->GetValueParam('content-disposition', 'name');
		if($filename == '')
			$filename = $a->header->GetValueParam('content-type', 'filename');
		if($filename == '')
			$filename = $a->header->GetValueParam('content-type', 'name');
		if($filename == '')
			$filename = 'unnamed';

		$this->partList[$part] = array(
			'filename'						=> $filename,
			'content-type'					=> $a->header->GetValueOnly('content-type'),
			'content-disposition'			=> $a->header->GetValueOnly('content-disposition'),
			'content-transfer-encoding'		=> $a->header->GetValueOnly('content-transfer-encoding'),
			'content-id'					=> $a->header->GetValueOnly('content-id'),
			'charset'						=> $a->header->GetValueParam('content-type', 'charset'),
			'body'							=> &$a->body
		);

		foreach($a->subParts as $key=>$value)
			$this->_generatePartListPart($part . '.' . ($key+1), $a->subParts[$key]);
	}
}
