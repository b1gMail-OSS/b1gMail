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
 * csv reader
 *
 */
class CSVReader
{
	var $_fp;
	var $_data;
	var $_rp = 0;
	var $_encoding = false;

	/**
	 * constructor
	 *
	 * @param resource $fp File pointer to CSV file
	 * @return CSVReader
	 */
	function __construct($fp, $encoding = 'UTF-8')
	{
		$this->_encoding = $encoding;
		$this->_fp = $fp;
		$this->_data = $this->_parse_file($fp);
	}

	/**
	 * fetch row from CSV file
	 *
	 * @return array
	 */
	function FetchRow()
	{
		return(isset($this->_data[++$this->_rp]) ? $this->_generate_assoc_row($this->_rp) : false);
	}

	/**
	 * return fields
	 *
	 * @return array
	 */
	function Fields()
	{
		return($this->_data[0]);
	}

	/**
	 * return number of fields
	 *
	 * @return int
	 */
	function NumFields()
	{
		return(count($this->_data[0]));
	}

	/**
	 * generate a associate array for row
	 *
	 * @param int $index Row index
	 * @return array
	 */
	function _generate_assoc_row($index)
	{
		$result = array();
		$row = $this->_data[$index];
		$fields = $this->Fields();

		if($index == 0)
			return($fields);

		foreach($row as $key=>$val)
			$result[$fields[$key]] = $val;

		return($result);
	}

	/**
	 * parse the CSV file to an array
	 *
	 * @param resource $fp File pointer
	 * @return array
	 */
	function _parse_file($fp)
	{
		$rows = array(array(''));
		$inString = false;
		$inQuote = false;
		$columnIndex = 0;
		$rowIndex = 0;

		$contents = '';
		while(is_resource($fp) && !feof($fp))
			$contents .= fread($fp, 4096);
		$contents = CharsetDecode($contents, $this->_encoding);

		// parse file char by char
		$_i = 0;
		while($_i < strlen($contents))
		{
			$c = $contents[$_i++];

			if(($c == '"' || $c == '\'') && (!$inQuote))
			{
				$inString = !$inString;
			}
			else if($c == '\\')
			{
				if($inQuote)
					$rows[$rowIndex][$columnIndex] .= $c;
				$inQuote = !$inQuote;
			}
			else if(($c == ',' || $c == ';') && (!$inString && !$inQuote))
			{
				$rows[$rowIndex][++$columnIndex] = '';
			}
			else if(($c == "\n" || $c == "\r") && (!$inString && !$inQuote))
			{
				$rows[++$rowIndex] = array('');
				$columnIndex = 0;
			}
			else
			{
				$inQuote = false;
				$rows[$rowIndex][$columnIndex] .= $c;
			}
		}

		// remove/fix broken rows
		$result = array();
		if(count($rows) >= 1)
		{
			$fieldCount = count($rows[0]);
			foreach($rows as $row)
			{
				if(count($row) == $fieldCount)
					$result[] = $row;
				else if(count($row) > $fieldCount/3)
					$result[] = array_pad($row, $fieldCount, '');
				else
					continue;
			}
		}

		return($result);
	}
}
