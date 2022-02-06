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
 * smime class
 */
class BMSMIME
{
	var $_userID;
	var $_userObject;

	/**
	 * constructor
	 *
	 * @param int $userID User ID
	 * @param BMUser $userObject User object
	 * @return BMSMIME
	 */
	function __construct($userID, &$userObject)
	{
		$this->_userID = (int)$userID;
		$this->_userObject = &$userObject;
	}

	/**
	 * write certs from array to disk
	 *
	 * @param array $certs Certs
	 * @return int Temp file ID
	 */
	function WriteCertsToDisk($certs)
	{
		// request temp file
		$tempFileID = RequestTempFile($this->_userID, time()+TIME_ONE_MINUTE);
		$tempFileName = TempFileName($tempFileID);

		// open file
		$fp = fopen($tempFileName, 'wb');

		// write certs
		foreach($certs as $cert)
			fwrite($fp, $cert."\r\n");

		// close
		fclose($fp);

		// return
		return($tempFileID);
	}

	function EncryptMail(&$mailBuilder, $recipients)
	{
		$result = false;

		if(!is_array($recipients) || count($recipients) == 0)
			return(false);

		// create temp file for message
		$tempID = RequestTempFile($this->_userID, time()+TIME_ONE_MINUTE);
		$tempFileName = TempFileName($tempID);
		$tempFP = fopen($tempFileName, 'wb');

		// write msg to temp file
		fseek($mailBuilder->_fp, 0, SEEK_SET);

		// copy message to temp file
		while(is_resource($mailBuilder->_fp) && !feof($mailBuilder->_fp))
		{
			$buff = fread($mailBuilder->_fp, 4096);
			fwrite($tempFP, $buff);
		}

		// close file
		fclose($tempFP);

		// reset original mail file pointer
		fseek($mailBuilder->_fp, 0, SEEK_SET);

		// create dest temp file
		$destTempID = RequestTempFile($this->_userID, time()+TIME_ONE_MINUTE);
		$destTempFileName = TempFileName($destTempID);

		// get recipient certificates
		$certs = array();
		foreach($recipients as $recipient)
		{
			$cert = $this->_userObject->GetCertificateForAddress($recipient, CERTIFICATE_TYPE_PUBLIC);
			if(!$cert)
				return(false);
			$certs[] = $cert['pemdata'];
		}

		// add our own certificate (we should be able to read our own e-mail!)
		$cert = $this->_userObject->GetCertificateForAddress(ExtractMailAddress($mailBuilder->_headerFields['From']), CERTIFICATE_TYPE_PRIVATE);
		if($cert)
			$certs[] = $cert['pemdata'];

		// encrypt
		if(openssl_pkcs7_encrypt($tempFileName, $destTempFileName, $certs, $mailBuilder->_smimeHeaders))
		{
			// set original mail size to 0
			ftruncate($mailBuilder->_fp, 0);

			// open signed message
			$encryptedFP = fopen($destTempFileName, 'rb');

			// write encrypted message to original message file
			while(is_resource($encryptedFP) && !feof($encryptedFP))
			{
				$buff = fread($encryptedFP, 4096);
				fwrite($mailBuilder->_fp, $buff);
			}

			// reset FP agan
			fseek($mailBuilder->_fp, 0, SEEK_SET);

			// close encrypted message
			fclose($encryptedFP);

			$result = true;
		}

		// release
		ReleaseTempFile($this->_userID, $tempID);
		ReleaseTempFile($this->_userID, $destTempID);

		return($result);
	}

	/**
	 * Sign a mail
	 *
	 * @param BMMailBuilder $mailBuilder Mail builder object
	 * @return bool
	 */
	function SignMail(&$mailBuilder)
	{
		$result = false;

		// create temp file for message
		$tempID = RequestTempFile($this->_userID, time()+TIME_ONE_MINUTE);
		$tempFileName = TempFileName($tempID);
		$tempFP = fopen($tempFileName, 'wb');

		// write msg to temp file
		fseek($mailBuilder->_fp, 0, SEEK_SET);

		// copy message to temp file
		while(is_resource($mailBuilder->_fp) && !feof($mailBuilder->_fp))
		{
			$buff = fread($mailBuilder->_fp, 4096);
			fwrite($tempFP, $buff);
		}

		// close file
		fclose($tempFP);

		// reset original mail file pointer
		fseek($mailBuilder->_fp, 0, SEEK_SET);

		// create dest temp file
		$destTempID = RequestTempFile($this->_userID, time()+TIME_ONE_MINUTE);
		$destTempFileName = TempFileName($destTempID);

		// find certificate
		$cert = $this->_userObject->GetCertificateForAddress(ExtractMailAddress($mailBuilder->_headerFields['From']), CERTIFICATE_TYPE_PRIVATE);
		if($cert === false)
			return(false);
		$certPEMData = $cert['pemdata'];
		$privKeyPEMData = $this->_userObject->GetPrivateKey($cert['hash']);
		$privKeyPass = $this->_userObject->GetPrivateKeyPassword($cert['hash']);

		// chain?
		$chainFileID = 0;
		$chainCerts = $this->_userObject->GetChainCerts($cert['hash']);
		if(is_array($chainCerts) && count($chainCerts) > 0)
			$chainFileID = $this->WriteCertsToDisk($chainCerts);

		// sign
		if($chainFileID > 0)
		{
			$signResult = @openssl_pkcs7_sign($tempFileName,
				$destTempFileName,
				$certPEMData,
				!empty($privKeyPass) ? array($privKeyPEMData, $privKeyPass) : $privKeyPEMData,
				$mailBuilder->_smimeHeaders,
				PKCS7_DETACHED,
				TempFileName($chainFileID));
			ReleaseTempFile($this->_userID, $chainFileID);
		}
		else
		{
			$signResult = @openssl_pkcs7_sign($tempFileName,
				$destTempFileName,
				$certPEMData,
				!empty($privKeyPass) ? array($privKeyPEMData, $privKeyPass) : $privKeyPEMData,
				$mailBuilder->_smimeHeaders);
		}

		// success?
		if($signResult)
		{
			// set original mail size to 0
			ftruncate($mailBuilder->_fp, 0);

			// open signed message
			$signedFP = fopen($destTempFileName, 'rb');

			// write signed message to original message file
			while(is_resource($signedFP) && !feof($signedFP))
			{
				$buff = fread($signedFP, 4096);
				fwrite($mailBuilder->_fp, $buff);
			}

			// reset FP agan
			fseek($mailBuilder->_fp, 0, SEEK_SET);

			// close signed message
			fclose($signedFP);

			$result = true;
		}

		// release
		ReleaseTempFile($this->_userID, $tempID);
		ReleaseTempFile($this->_userID, $destTempID);

		return($result);
	}

	/**
	 * decrypt a signed mail
	 *
	 * @param BMMail $mail Mail object
	 * @return array Result code, mail object
	 */
	function DecryptMail(&$mail)
	{
		// check if mail is encrypted
		if(!$mail->IsEncrypted())
			return(array(SMIME_NOT_ENCRYPTED));

		// get available priv. certs
		$privateCerts = $this->_userObject->GetKeyRing('certificateid', 'ASC', CERTIFICATE_TYPE_PRIVATE);

		// create temp file for message
		$tempID = RequestTempFile($this->_userID, time()+TIME_ONE_MINUTE);
		$tempFileName = TempFileName($tempID);
		$tempFP = fopen($tempFileName, 'wb');
		$messageFP = $mail->GetMessageFP();

		// copy message to temp file
		while(is_resource($messageFP) && !feof($messageFP))
		{
			$buff = fread($messageFP, 4096);
			fwrite($tempFP, $buff);
		}

		// close files
		fclose($messageFP);
		fclose($tempFP);

		// create dest temp file
		$destTempID = RequestTempFile($this->_userID, time()+TIME_ONE_MINUTE);
		$destTempFileName = TempFileName($destTempID);

		// try to dectypt it
		$success = false;
		foreach($privateCerts as $cert)
		{
			$privKeyPEMData = $this->_userObject->GetPrivateKey($cert['hash']);
			if(!$privKeyPEMData)
				continue;
			$privKeyPass = $this->_userObject->GetPrivateKeyPassword($cert['hash']);

			$result = @openssl_pkcs7_decrypt($tempFileName, $destTempFileName, $cert['pemdata'], !empty($privKeyPass) ? array($privKeyPEMData, $privKeyPass) : $privKeyPEMData);
			if($result)
			{
				$success = true;
				break;
			}
		}

		// success?
		if($success)
			$resultCode = SMIME_DECRYPTED;
		else
			$resultCode = SMIME_DECRYPTION_FAILED;

		// release
		ReleaseTempFile($this->_userID, $tempID);

		// create new mail object
		if($resultCode == SMIME_DECRYPTED && file_exists($destTempFileName))
		{
			$destFP = fopen($destTempFileName, 'rb');
			$GLOBALS['tempFilesToReleaseAtShutdown'][] = array($this->_userID, $destTempID, $destFP);
			$newMail = _new('BMMail', array($this->_userID, $mail->_row, $destFP, false, $destTempFileName, &$this->_userObject));
			$newMail->Parse();

			foreach($mail->_parsed->rootPart->header->items as $key=>$val)
				if(!isset($newMail->_parsed->rootPart->header->items[$key]))
					$newMail->_parsed->rootPart->header->items[$key] = $val;
		}
		else
			ReleaseTempFile($this->_userID, $tempID);

		// build result
		$result = array($resultCode);
		if(isset($newMail))
			$result[] = $newMail;
		else
			$result[] = false;

		// return
		return($result);
	}

	/**
	 * get b1gMail root certificates as hash => pemdata array
	 *
	 * @return array
	 */
	function GetRootCertificates()
	{
		global $db;

		$certs = array();
		$res = $db->Query('SELECT `hash`,`pemdata` FROM {pre}certificates WHERE `userid`=? AND `type`=?',
			0,
			CERTIFICATE_TYPE_ROOT);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$certs[$row['hash']] = $row['pemdata'];
		$res->Free();

		return($certs);
	}

	/**
	 * check mail signature
	 *
	 * @param BMMail $mail Mail object
	 * @return array Result code, mail object, cert hash
	 */
	function CheckMailSignature(&$mail)
	{
		// check if mail is signed
		if(!$mail->IsSigned())
			return(array(SMIME_SIGNATURE_NOT_SIGNED));

		// get root certs
		$rootCerts = array_values(array_merge($this->GetRootCertificates(), $this->_userObject->GetRootCertificates()));

		// create temp file for message
		$tempID = RequestTempFile($this->_userID, time()+TIME_ONE_MINUTE);
		$tempFileName = TempFileName($tempID);
		$tempFP = fopen($tempFileName, 'wb');
		$messageFP = $mail->GetMessageFP();

		// copy message to temp file
		while(is_resource($messageFP) && !feof($messageFP))
		{
			$buff = fread($messageFP, 4096);
			fwrite($tempFP, $buff);
		}

		// close files
		fclose($messageFP);
		fclose($tempFP);

		// write certs to disk :(
		$certFileID = $this->WriteCertsToDisk($rootCerts);

		// create dest temp file
		$destTempID = RequestTempFile($this->_userID, time()+TIME_ONE_MINUTE);
		$destTempFileName = TempFileName($destTempID);
		$certTempID = RequestTempFile($this->_userID, time()+TIME_ONE_MINUTE);
		$certTempFileName = TempFileName($certTempID);

		// check it
		$result = openssl_pkcs7_verify($tempFileName,
			0,
			$certTempFileName,
			array(TempFileName($certFileID)),
			B1GMAIL_DIR . 'res/dummy.pem',
			$destTempFileName);
		if(!$result)
		{
			$result = openssl_pkcs7_verify($tempFileName,
				PKCS7_NOVERIFY,
				$certTempFileName,
				array(TempFileName($certFileID)),
				B1GMAIL_DIR . 'res/dummy.pem',
				$destTempFileName);
			$resultCode = $result === true ? SMIME_SIGNATURE_OK_NOVERIFY : SMIME_SIGNATURE_BAD;
		}
		else
		{
			$resultCode = $result === true ? SMIME_SIGNATURE_OK : SMIME_SIGNATURE_BAD;
		}

		// process cert
		if(file_exists($certTempFileName))
			$certHash = $this->_userObject->StoreCertificate(getFileContents($certTempFileName));

		// release
		ReleaseTempFile($this->_userID, $certFileID);
		ReleaseTempFile($this->_userID, $tempID);
		ReleaseTempFile($this->_userID, $certTempID);

		// create new mail object
		if(file_exists($destTempFileName))
		{
			$destFP = fopen($destTempFileName, 'rb');
			$GLOBALS['tempFilesToReleaseAtShutdown'][] = array($this->_userID, $destTempID, $destFP);
			$newMail = _new('BMMail', array($this->_userID, $mail->_row, $destFP, false, $destTempFileName, &$this->_userObject));
			$newMail->Parse();

			foreach($mail->_parsed->rootPart->header->items as $key=>$val)
				if(!isset($newMail->_parsed->rootPart->header->items[$key]))
					$newMail->_parsed->rootPart->header->items[$key] = $val;
		}
		else
			ReleaseTempFile($this->_userID, $tempID);

		// build result
		$result = array($resultCode);
		if(isset($newMail))
			$result[] = $newMail;
		else
			$result[] = false;
		if(isset($certHash))
			$result[] = $certHash;
		else
			$result[] = false;

		// return
		return($result);
	}
}
