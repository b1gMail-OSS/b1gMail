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
 * ZIP class
 *
 */
class BMZIP
{
	/**
	 * b1gZIP stream (used if b1gZIP is installed)
	 *
	 * @var resource
	 */
	var $_b1gzip_stream;

	/**
	 * output stream
	 *
	 * @var resource
	 */
	var $_fp;

	/**
	 * central directory structure
	 *
	 * @var array
	 */
	var $_centralDirStruct;

	/**
	 * constructor
	 *
	 * @param resource $fp Output stream
	 * @return BMZIP
	 */
	function __construct($fp)
	{
		// output stream
		$this->_fp = $fp;

		// use b1gZIP?
		if(function_exists('b1gzip_create')
			&& function_exists('b1gzip_add')
			&& function_exists('b1gzip_final'))
		{
			$this->_b1gzip_stream = b1gzip_create();
		}
		else
		{
			$this->_b1gzip_stream = false;
			$this->_centralDirStruct = array();
		}
	}

	/**
	 * add a file to ZIP file
	 *
	 * @param string $fileName File name
	 * @param string $zipFileName File name in ZIP file
	 * @return bool
	 */
	function AddFile($fileName, $zipFileName = false)
	{
		$fileFP = @fopen($fileName, 'rb');
		if($fileFP)
		{
			$result = $this->AddFileByFP($fileFP, $fileName, $zipFileName);
			fclose($fileFP);
			return($result);
		}
		else
			return(false);
	}

	/**
	 * add a file to ZIP file by file pointer
	 *
	 * @param resource $fileFP
	 * @param string $fileName
	 * @param string $zipFileName
	 * @return bool
	 */
	function AddFileByFP($fileFP, $fileName, $zipFileName = false)
	{
		if(!$zipFileName)
			$zipFileName = basename($fileName);

		// read file
		fseek($fileFP, 0, SEEK_SET);
		$fileData = '';
		while(is_resource($fileFP) && !feof($fileFP))
			$fileData .= @fread($fileFP, 4096);
		$uncompressedSize = strlen($fileData);

		// use b1gZIP
		if($this->_b1gzip_stream)
		{
			b1gzip_add($this->_b1gzip_stream, $fileData, $zipFileName);
			return(true);
		}

		// or own implementation
		else
		{
			// compute crc32
			$crc32 = crc32($fileData);
			$compressedData = gzcompress($fileData);
			unset($fileData);
			$compressedData = substr($compressedData, 2, -4);
			$compressedSize = strlen($compressedData);

			// write file header
			$this->_beginFile($crc32, $compressedSize, $uncompressedSize, $zipFileName);
			fwrite($this->_fp, $compressedData);
		}

		return(false);
	}

	/**
	 * begin file
	 *
	 * @param int $crc32
	 * @param int $compressedSize
	 * @param int $uncompressedSize
	 * @param string $fileName
	 */
	function _beginFile($crc32, $compressedSize, $uncompressedSize, $fileName)
	{
		// local header
		$header = pack('VvvvvvVVVvv',
			0x04034b50,
			0x0014,
			0x0,
			0x0008,
			(date('H') << 11) | (date('i') << 5) | round(date('s')/2, 0),
			(date('Y')-1980 << 9) | (date('m') << 5) | date('d'),
			$crc32,
			$compressedSize,
			$uncompressedSize,
			strlen($fileName),
			0x0);
		$offset = ftell($this->_fp);
		fwrite($this->_fp, $header);
		fwrite($this->_fp, $fileName);

		// central dir struct entry
		$entry = pack('VvvvvvvVVVvvvvvVV',
			0x02014b50,
			0x0,
			0x0014,
			0x0,
			0x0008,
			(date('H') << 11) | (date('i') << 5) | round(date('s')/2, 0),
			(date('Y')-1980 << 9) | (date('m') << 5) | date('d'),
			$crc32,
			$compressedSize,
			$uncompressedSize,
			strlen($fileName),
			0x0,
			0x0,
			0x0,
			0x0,
			32,
			$offset);
		$entry .= $fileName;
		$this->_centralDirStruct[] = $entry;
	}

	/**
	 * finish zip file
	 *
	 * @return int Size
	 */
	function Finish()
	{
		// use b1gZIP?
		if($this->_b1gzip_stream)
		{
			$zipData = b1gzip_final($this->_b1gzip_stream);
			fwrite($this->_fp, $zipData);
			fseek($this->_fp, 0, SEEK_SET);
			return(strlen($zipData));
		}

		// or own implementation
		else
		{
			// write central dir struct
			$offset = ftell($this->_fp);
			$dLength = 0;
			foreach($this->_centralDirStruct as $item)
			{
				fwrite($this->_fp, $item);
				$dLength += strlen($item);
			}

			// write footer
			$footer = pack('VvvvvVVv',
				0x06054b50,
				0x0,
				0x0,
				count($this->_centralDirStruct),
				count($this->_centralDirStruct),
				$dLength,
				$offset,
				0x0);
			fwrite($this->_fp, $footer);

			// return
			$len = ftell($this->_fp);
			fseek($this->_fp, 0, SEEK_SET);
			return($len);
		}
	}
}
