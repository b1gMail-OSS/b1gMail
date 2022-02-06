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
 * vcard builder
 *
 */
class VCardBuilder
{
	var $_fields;

	/**
	 * constructor
	 *
	 * @return VCardBuilder
	 */
	function __construct($fields)
	{
		$this->_fields = $fields;
	}

	/**
	 * build vcard
	 *
	 * @return string
	 */
	function Build()
	{
		$lines = array();
		$lines[] = 'BEGIN:VCARD';
		$lines[] = 'VERSION:3.0';

		// process fields
		foreach($this->_fields as $key=>$value)
		{
			// type?
			if(substr($key, 0, 5) == 'work_')
			{
				$pre = 'work_';
				$type = 'WORK';
				$key = substr($key, 5);
			}
			else
			{
				$pre = '';
				$type = 'HOME';
			}

			// skip empty fields
			if(trim($value) == ''
				&& $key != 'vorname'
				&& $key != 'strassenr')
				continue;

			// process fields
			if($key == 'vorname')
			{
				$vorname = $value;
				$nachname = isset($this->_fields['nachname'])
					? $this->_fields['nachname']
					: '';
				$anrede = isset($this->_fields['anrede'])
					? ($this->_fields['anrede'] == 'herr'
						? 'Mr.'
						: ($this->_fields['anrede'] == 'frau'
							? 'Ms.'
							: ''))
					: '';
				$lines[] = 'N:' . $nachname . ';' . $vorname . ';;' . $anrede . ';';
			}
			else if($key == 'firma')
			{
				$lines[] = 'ORG:' . $value;
			}
			else if($key == 'strassenr')
			{
				$ort = isset($this->_fields[$pre.'ort'])
						? $this->_fields[$pre.'ort']
						: '';
				$plz = isset($this->_fields[$pre.'plz'])
						? $this->_fields[$pre.'plz']
						: '';
				$land = isset($this->_fields[$pre.'land'])
						? $this->_fields[$pre.'land']
						: '';
				$lines[] = 'ADR;type=' . $type . ':;;' . $value . ';' . $ort . ';;' . $plz . ';' . $land;
			}
			else if($key == 'tel')
			{
				$lines[] = 'TEL;type=' . $type . ':' . $value;
			}
			else if($key == 'fax')
			{
				$lines[] = 'TEL;type=' . $type . ';type=FAX:' . $value;
			}
			else if($key == 'handy')
			{
				$lines[] = 'TEL;type=' . $type . ';type=CELL:' . $value;
			}
			else if($key == 'email')
			{
				$lines[] = 'EMAIL;type=INTERNET;type=' . $type . ':' . $value;
			}
			else if($key == 'position')
			{
				$lines[] = 'TITLE:' . $value;
			}
			else if($key == 'geburtsdatum')
			{
				$lines[] = 'BDAY:' . date('Y-m-d', $value);
			}
		}

		$lines[] = 'END:VCARD';
		return(implode("\r\n", $lines));
	}
}

/**
 * vcard reader
 *
 */
class VCardReader
{
	var $_fp;

	/**
	 * constructor
	 *
	 * @param resource $fp VCF File handle
	 * @return VCardReader
	 */
	function __construct($fp)
	{
		$this->_fp = $fp;
		fseek($this->_fp, 0, SEEK_SET);
	}

	/**
	 * parse key field
	 *
	 * @param string $key Key string
	 * @return array
	 */
	function _parseKeyField($key)
	{
		$return = array();
		$items = explode(';', $key);

		if(strpos($items[0], '.') !== false)
			$items[0] = substr($items[0], strpos($items[0], '.')+1);

		$return['name'] = strtoupper($items[0]);
		$return['parameters'] = array();
		$items = array_slice($items, 1);
		foreach($items as $item)
		{
			$eqPos = strpos($item, '=');
			$key = $value = '';

			if($eqPos !== false)
			{
				$key = strtoupper(trim(substr($item, 0, $eqPos)));
				$value = strtoupper(trim(substr($item, $eqPos+1)));
			}
			else
			{
				$key = $item;
			}

			if(isset($return['parameters'][$key]))
				if(is_array($return['parameters'][$key]))
					$return['parameters'][$key][] = $value;
				else
					$return['parameters'][$key] = array($return['parameters'][$key], $value);
			else
				$return['parameters'][$key] = $value;
		}

		return($return);
	}

	/**
	 * parse value field
	 *
	 * @param string $value Value string
	 * @return array
	 */
	function _parseValueField($value)
	{
		$return = array();
		$values = explode(';', $value);
		$values = stripslashes_array($values);

		return($values);
	}

	/**
	 * parse the vcard and return key-value array
	 *
	 * @return array
	 */
	function Parse()
	{
		$result = array();

		while(is_resource($this->_fp) && !feof($this->_fp))
		{
			$line = trim(str_replace(chr(0), '', fgets($this->_fp, 4096)));

			$dPos = strpos($line, ':');
			if($dPos !== false)
			{
				$key = $this->_parseKeyField(trim(substr($line, 0, $dPos)));
				$value = $this->_parseValueField(trim(substr($line, $dPos+1)));

				// N field
				if($key['name'] == 'N')
				{
					$nachname = $value[0];
					$vorname = $value[1];
					$vorname .= $value[2];
					$anrede = $value[3];
					if(strtoupper($anrede) == 'MR.' || strtoupper($anrede) == 'MR')
						$anrede = 'herr';
					else if(strtoupper($anrede) == 'MS.' || strtoupper($anrede) == 'MS'
							|| strtoupper($anrede) == 'MRS.' || strtoupper($anrede) == 'MRS')
						$anrede = 'frau';
					else
						$anrede = '';

					if($nachname != '')
						$result['nachname'] = $nachname;
					if($vorname != '')
						$result['vorname'] = $vorname;
					if($anrede != '')
						$result['anrede'] = $anrede;
				}

				// BDAY field
				else if($key['name'] == 'BDAY')
				{
					$bday = substr($value[0], 0, 10);
					list($y, $m, $d) = explode('-', $bday);
					$result['geburtsdatum'] = mktime(0, 0, 0, $m, $d, $y);
				}

				// EMAIL field
				else if($key['name'] == 'EMAIL')
				{
					$prefix = '';
					if(isset($key['parameters']['TYPE']) && eqOrIn($key['parameters']['TYPE'], 'WORK'))
						$prefix = 'work_';

					$result[$prefix . 'email'] = $value[0];
				}

				// TEL field
				else if($key['name'] == 'TEL')
				{
					$prefix = '';
					if(isset($key['parameters']['TYPE']) && eqOrIn($key['parameters']['TYPE'], 'WORK'))
						$prefix = 'work_';

					if(eqOrIn($key['parameters']['TYPE'], 'CELL'))
						$result[$prefix . 'handy'] = $value[0];
					else if(eqOrIn($key['parameters']['TYPE'], 'FAX'))
						$result[$prefix . 'fax'] = $value[0];
					else
						$result[$prefix . 'tel'] = $value[0];
				}

				// ORG field
				else if($key['name'] == 'ORG')
				{
					$result['firma'] = $value[0];
				}

				// TITLE field
				else if($key['name'] == 'TITLE')
				{
					$result['position'] = $value[0];
				}

				// NOTE field
				else if($key['name'] == 'NOTE')
				{
					$result['kommentar'] = $value[0];
				}

				// URL field
				else if($key['name'] == 'URL')
				{
					$result['web'] = $value[0];
				}

				// ADR field
				else if($key['name'] == 'ADR')
				{
					$prefix = '';
					if(isset($key['parameters']['TYPE']) && eqOrIn($key['parameters']['TYPE'], 'WORK'))
						$prefix = 'work_';

					if($value[2] != '')
						$result[$prefix . 'strassenr'] = $value[2];
					if($value[3] != '')
						$result[$prefix . 'ort'] = $value[3];
					if($value[5] != '')
						$result[$prefix . 'plz'] = $value[5];
					if($value[6] != '')
						$result[$prefix . 'land'] = $value[6];
				}
			}
		}

		return($result);
	}
}
