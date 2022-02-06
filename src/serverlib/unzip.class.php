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
 * Basic UnZIP class
 *
 */
class BMUnZIP
{
	var $_fp;
	var $_centralDirStruct;

	/**
	 * constructor
	 *
	 * @param resource $fp Input file stream
	 * @return BMUnZIP
	 */
	function __construct($fp)
	{
		$this->_fp = $fp;
		$this->_readCentralDirStruct();
	}

	/**
	 * get ZIP file directory listing
	 *
	 * @return array
	 */
	function GetFileList()
	{
		return($this->_centralDirStruct);
	}

	/**
	 * get ZIP file directory listing prepared for tree display
	 *
	 * @return array
	 */
	function GetFileTree()
	{
		$tree = array();
		$folderNoCounter = count($this->_centralDirStruct);

		// add files
		foreach($this->_centralDirStruct as $fileNo=>$file)
		{
			$file['parentID']		= -2;
			$file['type']			= 'file';
			$file['fileNo'] 		= $fileNo;
			$file['baseName']		= basename($file['fileName']);

			$tree[] 				= $file;
		}

		// add folders
		$again = true;
		while($again)
		{
			$again = false;

			foreach($tree as $id=>$item)
			{
				if($item['parentID'] != -2)
					continue;

				$parentFolderName	= dirname($item['fileName']);

				if($parentFolderName != '.' && $parentFolderName != '')
				{
					$parentID			= -2;

					foreach($tree as $id2=>$item2)
					{
						if($item2['type'] == 'folder' && $item2['fileName'] == $parentFolderName)
						{
							$parentID = $item2['fileNo'];
							break;
						}
					}

					if($parentID == -2)
					{
						$folderNo = $folderNoCounter++;
						$tree[] = array('type' 			=> 'folder',
										'fileName'		=> $parentFolderName,
										'baseName' 		=> basename($parentFolderName),
										'fileNo' 		=> $folderNo,
										'parentID'		=> -2);
						$parentID = $folderNo;
						$again = true;
					}

					$tree[$id]['parentID'] = $parentID;
				}
			}
		}

		return($tree);
	}

	/**
	 * extract a file by file no
	 *
	 * @param int $fileNo File no (from GetFileList() array)
	 * @param resource $fp Output file stream
	 * @return bool
	 */
	function ExtractFile($fileNo, $fp = false, $sizeLimit = -1)
	{
		if(!isset($this->_centralDirStruct[$fileNo]))
			return(false);

		if(!in_array($this->_centralDirStruct[$fileNo]['compressionMethod'], array(8, 12)))
			return(false);

		fseek($this->_fp, $this->_centralDirStruct[$fileNo]['relativeOffset'], SEEK_SET);

		$fileHeader = fread($this->_fp, 30);

		$_fileHeader = @unpack('Vsignature/vversionNeeded/vflags/vcompressionMethod/vmTime/vmDate/Vcrc32/VcompressedSize/VuncompressedSize/vfileNameLength/vextraFieldLength',
						 	   $fileHeader);

		if(!$_fileHeader || $_fileHeader['signature'] != 0x04034b50)
			return(false);

		fseek($this->_fp, $_fileHeader['fileNameLength']+$_fileHeader['extraFieldLength'], SEEK_CUR);

		$_fileHeader = $this->_centralDirStruct[$fileNo];

		if($_fileHeader['compressedSize'] > 0)
		{
			$compressedData = fread($this->_fp, $_fileHeader['compressedSize']);
			$uncompressedData = '';

			if($_fileHeader['compressionMethod'] == 8)
			{
				$uncompressedData = @gzinflate($compressedData);
			}
			else if($_fileHeader['compressionMethod'] == 12)
			{
				$uncompressedData = @bzdecompress($compressedData);
			}

			unset($compressedData);

			if(crc32($uncompressedData) != $_fileHeader['crc32'])
				return(false);

			if($fp !== false)
				fwrite($fp, $uncompressedData, $sizeLimit != -1 ? $sizeLimit : strlen($uncompressedData));
			else
				echo($uncompressedData);
		}
		else if($_fileHeader['crc32'] != 0)
		{
			return(false);
		}

		return(true);
	}

	/**
	 * read central dir struct from ZIP file
	 *
	 */
	function _readCentralDirStruct()
	{
		$this->_centralDirStruct = array();

		fseek($this->_fp, -22, SEEK_END);
		$endOfCDS = fread($this->_fp, 22);

		while(substr($endOfCDS, 0, 4) != pack('V', 0x06054b50))
		{
			fseek($this->_fp, -1, SEEK_CUR);
			$endOfCDS = fgetc($this->_fp) . $endOfCDS;
			fseek($this->_fp, -1, SEEK_CUR);

			if(ftell($this->_fp) < 2)
				break;
		}

		if(substr($endOfCDS, 0, 4) != pack('V', 0x06054b50))
		{
			if(DEBUG) trigger_error('File corrupt or not in ZIP format', E_USER_NOTICE);
			return;
		}

		// parse endOfCDS record
		$_endOfCDS = @unpack('Vsignature/vdiskNo/vcdsStartDiskNo/vcdsDiskEntryCount/vcdsTotalEntryCount/VcdsSize/VcdsOffset/vcommentLength', $endOfCDS);
		if(!$_endOfCDS)
		{
			if(DEBUG) trigger_error('File corrupt or not in ZIP format (eoCDS broken)', E_USER_NOTICE);
			return;
		}

		// seek to CDS offset
		fseek($this->_fp, $_endOfCDS['cdsOffset'], SEEK_SET);

		// read CDS entries
		for($i=0; $i<$_endOfCDS['cdsDiskEntryCount'] && !feof($this->_fp); $i++)
		{
			$fileHeader = fread($this->_fp, 46);

			$_fileHeader = @unpack('Vsignature/vversion/vversionNeeded/vflags/vcompressionMethod/vmTime/vmDate/Vcrc32/VcompressedSize/VuncompressedSize/vfileNameLength/vextraFieldLength/vfileCommentLength/vdiskNumberStart/vinternalAttrs/VexternalAttrs/VrelativeOffset',
								   $fileHeader);
			if(!$_fileHeader || $_fileHeader['signature'] != 0x02014b50)
			{
				if(DEBUG) trigger_error('File corrupt (CDS broken)');
				return;
			}

			$_fileHeader['cdsOffset'] = ftell($this->_fp) - strlen($fileHeader);

			if($_fileHeader['fileNameLength'] > 0)
				$_fileHeader['fileName'] 	= fread($this->_fp, $_fileHeader['fileNameLength']);
			if($_fileHeader['extraFieldLength'] > 0)
				$_fileHeader['extraField']	= fread($this->_fp, $_fileHeader['extraFieldLength']);
			if($_fileHeader['fileCommentLength'] > 0)
				$_fileHeader['fileComment']	= fread($this->_fp, $_fileHeader['fileCommentLength']);

			if(substr($_fileHeader['fileName'], -1) == '/' && $_fileHeader['uncompressedSize'] == 0)
				continue;

			$this->_centralDirStruct[] = $_fileHeader;
		}

		// sort by filename
		uasort($this->_centralDirStruct, array(&$this, '_sortHandler'));
	}

	function _sortHandler($s1, $s2)
	{
		return(strcmp($s1['fileName'], $s2['fileName']));
	}
}
