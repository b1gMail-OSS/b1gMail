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

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

if(extension_loaded('zip')) {
    /**
     * ZIP class.
     */
    class BMZIP
    {
        /**
         * ZipArchive instance.
         *
         * @var ZipArchive
         */
        private $_zip;

        /**
         * output stream.
         *
         * @var resource
         */
        private $_fp;

        // Temporary variables
        private $_tempID;
        private $_tempZipFile;

        /**
         * constructor.
         *
         * @param resource $fp Output stream
         *
         * @return BMZIP
         */
        public function __construct($fp)
        {
            // Set the output stream
            $this->_fp = $fp;

            // Create a new ZipArchive instance
            $this->_zip = new ZipArchive();
            $this->_tempID = RequestTempFile(0);
            $this->_tempZipFile = TempFileName($this->_tempID);
            if ($this->_zip->open($this->_tempZipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                //throw new Exception("Could not open or create temporary ZIP file: $tempZipFile");
                PutLog(sprintf('Could not open or create ZIP file: %s',
                                    $zipFile),
                                    PRIO_ERROR,
                                    __FILE__,
                                    __LINE__);
            }
        }

        /**
         * add a file to ZIP file.
         *
         * @param string $fileName    File name
         * @param string $zipFileName File name in ZIP file
         *
         * @return bool
         */
        public function AddFile($fileName, $zipFileName = false)
        {
            if (!$zipFileName) {
                $zipFileName = basename($fileName);
            }

            // Add file to the ZIP archive
            return $this->_zip->addFile($fileName, $zipFileName);
        }

        /**
         * add a file to ZIP file by file pointer.
         *
         * @param resource $fileFP
         * @param string   $fileName
         * @param string   $zipFileName
         *
         * @return bool
         */
        public function AddFileByFP($fileFP, $fileName, $zipFileName = false)
        {
            if (!$zipFileName) {
                $zipFileName = basename($fileName);
            }

            // Add file to the ZIP archive from file pointer
            return $this->_zip->addFromString($zipFileName, stream_get_contents($fileFP));
        }

        /**
         * finish zip file.
         *
         * @return int Size of the ZIP file
         */
        public function Finish()
        {
            // Close the ZIP archive
            $this->_zip->close();

            // Write the ZIP file to the output stream
            $zipData = file_get_contents($this->_tempZipFile);
            ReleaseTempFile(0, $this->_tempID);
            fwrite($this->_fp, $zipData);
            fseek($this->_fp, 0, SEEK_SET);

            // Return the size of the ZIP file
            return strlen($zipData);
        }
    }

}
else {
    require(B1GMAIL_DIR . 'serverlib/legacy_zip.class.php');
}