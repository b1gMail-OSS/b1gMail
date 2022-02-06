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

if(!class_exists('BMMailbox'))
	include(B1GMAIL_DIR . 'serverlib/mailbox.class.php');
if(!class_exists('BMAddressbook'))
	include(B1GMAIL_DIR . 'serverlib/addressbook.class.php');
if(!class_exists('BMMailBuilder'))
	include(B1GMAIL_DIR . 'serverlib/mailbuilder.class.php');
if(!class_exists('BMWebdisk'))
	include(B1GMAIL_DIR . 'serverlib/webdisk.class.php');

/**
 * interface for b1gMail tools (like mailchecker)
 *
 */
class BMToolInterface
{
	var $_sms, $_user, $_group;

	/**
	 * constructor
	 *
	 * @return BMToolInterface
	 */
	function __construct()
	{
	}

	/**
	 * create a web session
	 *
	 * @param string $userName User name
	 * @param string $passwordPlain Password
	 * @param int $timezoneOffset Timezone offset
	 * @return array
	 */
	function CreateWebSession($userName, $passwordPlain, $timezoneOffset)
	{
		$result = array();

		$userName = EncodeEMail($userName);
		list($res, $param) = BMUser::Login($userName, $passwordPlain, true, true);

		if($res == USER_OK)
		{
			$_SESSION['bm_timezone'] = $timezoneOffset;
			$_SESSION['bm_loginFromClientAPI'] = true;
			$result['status'] = 'OK';
			$result['sessionID'] = session_id();
			$result['sessionSecret'] = $_COOKIE['sessionSecret_'.substr(session_id(), 0, 16)];
		}
		else
		{
			$result['status'] = 'Invalid login';
		}

		return($result);
	}

	/**
	 * check for new mails (mailchecker interface) and notifications
	 *
	 * @param string $userName
	 * @param string $passwordHash
	 * @return array
	 */
	function CheckForMails($userName, $passwordHash)
	{
		$result = array();
		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0)
		{
			$userID = $userInfo['userID'];
			$mailbox = _new('BMMailbox', array($userID, $userName, $this->_user));
			$result['status'] = 'OK';
			$result['recentMails'] = $mailbox->GetRecentMailCount();
			$result['unreadNotifications'] = $this->_user->GetUnreadNotifications();
		}
		else
		{
			$result['status'] = 'Invalid login';
		}
		return($result);
	}

	/**
	 * get webdisk folder listing
	 *
	 * @param string $userName
	 * @param string $passwordHash
	 * @param int $folderID
	 * @return array
	 */
	function GetWebdiskFolder($userName, $passwordHash, $folderID = 0)
	{
		$result = array();
		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0)
		{
			$userID 			= $userInfo['userID'];
			$webdisk			= _new('BMWebdisk', array($userID));
			$result['status'] 	= 'OK';
			$result['contents']	= $webdisk->GetFolderContent($folderID);
		}
		else
		{
			$result['status'] 	= 'Invalid login';
		}

		return($result);
	}

	/**
	 * create a webdisk folder
	 *
	 * @param string $userName
	 * @param string $passwordHash
	 * @param int $parentFolderID
	 * @param string $fileName
	 * @return array
	 */
	function CreateWebdiskFolder($userName, $passwordHash, $parentFolderID, $fileName)
	{
		$result = array();
		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0)
		{
			$userID 			= $userInfo['userID'];
			$webdisk			= _new('BMWebdisk', array($userID));
			$result['folderID']	= $webdisk->CreateFolder($parentFolderID, $fileName);
			$result['status']	= $result['folderID'] > 0 ? 'OK' : 'Failed';
		}
		else
		{
			$result['status'] 	= 'Invalid login';
		}

		return($result);
	}

	/**
	 * delete a webdisk file
	 *
	 * @param string $userName
	 * @param string $passwordHash
	 * @param int $fileID
	 * @return array
	 */
	function DeleteWebdiskFile($userName, $passwordHash, $fileID)
	{
		$result = array();
		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0)
		{
			$userID 			= $userInfo['userID'];
			$webdisk			= _new('BMWebdisk', array($userID));
			$result['status']	= $webdisk->DeleteFile($fileID) ? 'OK' : 'Failed';
		}
		else
		{
			$result['status'] 	= 'Invalid login';
		}

		return($result);
	}

	/**
	 * delete a webdisk folder
	 *
	 * @param string $userName
	 * @param string $passwordHash
	 * @param int $folderID
	 * @return array
	 */
	function DeleteWebdiskFolder($userName, $passwordHash, $folderID)
	{
		$result = array();
		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0)
		{
			$userID 			= $userInfo['userID'];
			$webdisk			= _new('BMWebdisk', array($userID));
			$result['status']	= $webdisk->DeleteFolder($folderID) ? 'OK' : 'Failed';
		}
		else
		{
			$result['status'] 	= 'Invalid login';
		}

		return($result);
	}

	/**
	 * get webdisk file info
	 *
	 * @param string $userName
	 * @param string $passwordHash
	 * @param int $fileID
	 * @return array
	 */
	function GetWebdiskFileInfo($userName, $passwordHash, $fileID)
	{
		$result = array();
		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0)
		{
			$userID 			= $userInfo['userID'];
			$webdisk			= _new('BMWebdisk', array($userID));
			$result['info']		= $webdisk->GetFileInfo($fileID);
			$result['status']	= $result['info'] !== false;
		}
		else
		{
			$result['status'] 	= 'Invalid login';
		}

		return($result);
	}

	/**
	 * create a webdisk file
	 *
	 * @param string $userName
	 * @param string $passwordHash
	 * @param int $destFolderID
	 * @param string $fileName
	 */
	function CreateWebdiskFile($userName, $passwordHash, $destFolderID, $fileName)
	{
		global $db;

		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0)
		{
			$userID 			= $userInfo['userID'];
			$groupRow 			= $this->_group->_row;
			$userRow 			= $this->_user->_row;
			$webdisk			= _new('BMWebdisk', array($userID));
			$fileSize 			= (int)$_SERVER['CONTENT_LENGTH'];
			$mimeType 			= $_SERVER['CONTENT_TYPE'];
			if(empty($mimeType) || $mimeType == 'application/octet-stream')
				$mimeType = GuessMIMEType($fileName);
			$spaceLimit			= $this->_group->_row['webdisk'] + $this->_user->_row['diskspace_add'];

			if($groupRow['traffic'] <= 0 || ($userRow['traffic_down']+$userRow['traffic_up']+$fileSize) <= $groupRow['traffic']+$userRow['traffic_add'])
			{
				if(($exID = $webdisk->FileExists($destFolderID, $fileName)))
					$webdisk->DeleteFile($exID);
				$usedSpace		= $webdisk->GetUsedSpace();

				if($spaceLimit == -1 || $usedSpace+$fileSize <= $spaceLimit)
				{
					if(($fileID = $webdisk->CreateFile($destFolderID, $fileName, $mimeType, $fileSize)) !== false)
					{
						$uploadOK = false;

						$fpIn = fopen('php://input', 'rb');

						if($fpIn)
						{
							$uploadOK = BMBlobStorage::createDefaultWebdiskProvider($userRow['id'])
								->storeBlob(BMBLOB_TYPE_WEBDISK, $fileID, $fpIn, $fileSize);

							fclose($fpIn);
						}

						if(!$uploadOK)
						{
							$webdisk->DeleteFile($fileID);

							// log
							PutLog('Failed to create file, deleting webdisk file',
								PRIO_ERROR,
								__FILE__,
								__LINE__);

							header('HTTP/1.1 500 Internal server error');
							exit();
						}
						else
						{
							$usedSpace += $fileSize;
							$db->Query('UPDATE {pre}users SET traffic_up=traffic_up+? WHERE id=?',
								$fileSize,
								$userRow['id']);
							Add2Stat('wd_up', ceil($fileSize/1024));

							return($this->GetWebdiskFileInfo($userName, $passwordHash, $fileID));
						}
					}
					else
					{
						header('HTTP/1.1 500 Internal server error');
						exit();
					}
				}
				else
				{
					header('HTTP/1.1 403 Not enough space left');
					exit();
				}
			}
			else
			{
				header('HTTP/1.1 509 Bandwith limit exceeded');
				exit();
			}
		}
		else
		{
			header('HTTP/1.1 403 Forbidden');
			exit();
		}

		exit();
	}

	/**
	 * download a webdisk file
	 *
	 * @param string $userName
	 * @param string $passwordHash
	 * @param int $fileID
	 */
	function GetWebdiskFile($userName, $passwordHash, $fileID)
	{
		global $db;

		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0)
		{
			$userID 			= $userInfo['userID'];
			$webdisk			= _new('BMWebdisk', array($userID));

			$fileInfo = $webdisk->GetFileInfo($fileID);
			if($fileInfo !== false)
			{
				$groupRow = $this->_group->_row;
				$userRow = $this->_user->_row;

				if($groupRow['traffic'] <= 0 || ($userRow['traffic_down']+$userRow['traffic_up']+$fileInfo['size']) <= $groupRow['traffic']+$userRow['traffic_add'])
				{
					// ok
					$speedLimit = $groupRow['wd_member_kbs'] <= 0 ? -1 : $groupRow['wd_member_kbs'];
					$db->Query('UPDATE {pre}users SET traffic_down=traffic_down+? WHERE id=?',
						$fileInfo['size'],
						$userRow['id']);

					// send file
					header('Pragma: public');
					header('Content-Type: ' . $fileInfo['contenttype']);
					header('Content-Length: ' . $fileInfo['size']);
					header('Content-Disposition: attachment; filename="' . addslashes($fileInfo['dateiname']) . '"');
					Add2Stat('wd_down', ceil($fileInfo['size']/1024));
					SendFileFP(BMBlobStorage::CreateProvider($fileInfo['blobstorage'], $userRow['id'])->loadBlob(BMBLOB_TYPE_WEBDISK, $fileInfo['id']),
						$speedLimit);
					exit();
				}
				else
				{
					header('HTTP/1.1 509 Bandwith limit exceeded');
					exit();
				}
			}

			header('HTTP/1.1 404 Not found');
			exit();
		}
		else
		{
			header('HTTP/1.1 403 Forbidden');
			exit();
		}

		exit();
	}

	/**
	 * send a SMS (smsmanager interface)
	 *
	 * @param string $userName
	 * @param string $passwordHash
	 * @param string $from
	 * @param string $to
	 * @param int $type
	 * @param string $text
	 * @return array
	 */
	function SendSMS($userName, $passwordHash, $from, $to, $type, $text)
	{
		$result = array('sendOK' => false);
		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0 && is_object($this->_sms))
		{
			$userID = $userInfo['userID'];

			// validation required?
			$validationRequired = $this->_group->_row['smsvalidation'] == 'yes' && $this->_user->_row['sms_validation'] == 0;
			if($validationRequired)
			{
				$result['sendOK'] = false;
				return($result);
			}

			// prepare from number
			if($this->_group->_row['sms_ownfrom'] == 'yes')
			{
				$from = str_replace('+', '00', $from);
				$from = preg_replace('/[^0-9]/', '', $from);
			}
			else
				$from = $this->_group->_row['sms_from'];

			// prepare to number
			$to = str_replace('+', '00', $to);
			$to = preg_replace('/[^0-9]/', '', $to);

			// add signature
			if(strlen($text) > $this->_sms->GetMaxChars((int)$type))
				$text = substr($text, 0, $this->_sms->GetMaxChars((int)$type));
			$text .= $this->_group->_row['sms_sig'];

			if(!BMSMS::PreOK($to, $this->_group->_row['sms_pre'])
				|| ($this->_group->_row['sms_ownfrom'] == 'yes' && !BMSMS::PreOK($from, $this->_group->_row['sms_pre'])))
			{
				$result['sendOK'] = false;
			}
			else
			{
				$result['sendOK'] = $this->_sms->Send($from, $to, $text, (int)$type, true, true);
			}

			$this->_user->Fetch(-1, true);
			$result['balance'] = $this->_user->GetBalance();
		}
		else
		{
			$result['status'] = 'Invalid login';
		}
		return($result);
	}

	/**
	 * get SMS outbox (smsmanager interface)
	 *
	 * @param string $userName
	 * @param string $passwordHash
	 * @return array
	 */
	function GetSMSOutbox($userName, $passwordHash)
	{
		$result = array('sendOK' => false);
		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0 && is_object($this->_sms))
		{
			$result['outbox'] = $this->_sms->GetOutbox('date', 'desc');
			$result['status'] = 'OK';
		}
		else
		{
			$result['status'] = 'Invalid login';
		}
		return($result);
	}

	/**
	 * get SMS/fax addressbook (smsmanager interface)
	 *
	 * @param string $userName
	 * @param string $passwordHash
	 * @return array
	 */
	function GetSMSAddressbook($userName, $passwordHash)
	{
		$result = array('sendOK' => false);
		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0 && is_object($this->_sms))
		{
			$userID = $userInfo['userID'];

			// addressbook
			$addresses = array();
			$book = _new('BMAddressbook', array($userID));
			$addressBook = $book->GetAddressBook('*', -1, 'nachname', 'asc');
			foreach($addressBook as $id=>$entry)
			{
				if(trim($entry['handy']) != '' || trim($entry['work_handy']) != '')
					$addresses[] = array('firstname' 		=> $entry['vorname'],
											'lastname'		=> $entry['nachname'],
											'handy'			=> $entry['handy'],
											'work_handy'	=> $entry['work_handy'],
											'fax'			=> $entry['fax'],
											'work_fax'		=> $entry['work_fax'],
											'id'			=> $entry['id']);
			}

			$result['addresses'] = $addresses;
			$result['status'] = 'OK';
		}
		else
		{
			$result['status'] = 'Invalid login';
		}
		return($result);
	}

	/**
	 * get service info
	 *
	 * @return array
	 */
	function GetServiceInfo()
	{
		global $bm_prefs;

		$domains = GetDomainList('login');

		$result = array(
			'title'		=> $bm_prefs['titel'],
			'domains'	=> explode(':', $bm_prefs['domains'])
		);

		return($result);
	}

	/**
	 * create a draft
	 *
	 * @return array
	 */
	function CreateDraft($userName, $passwordHash)
	{
		global $currentCharset;

		$result = array('draftID' => -1);
		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0)
		{
			$userID = $userInfo['userID'];

			if(!isset($_POST['msg']) || !is_array($_POST['msg']))
			{
				$result['status'] = 'Missing message';
			}
			else
			{
				$msg = $_POST['msg'];

				// build mail
				$mail = _new('BMMailBuilder');
				$mail->SetUserID($userID);

				// subject
				if(isset($msg['subject']))
					$mail->AddHeaderField('Subject', $msg['subject']);

				// subject
				if(isset($msg['from']))
					$mail->AddHeaderField('From', $msg['from']);
				else
					$mail->AddHeaderField('From', $this->_user->GetDefaultSender());

				// text
				if(!isset($msg['text']))
					$msg['text'] = '';
				$mail->AddText($msg['text'], 'plain', $currentCharset);

				// attachments
				$tempFiles = array();
				if(isset($_FILES['msg']) && is_array($_FILES['msg']))
				{
					foreach($_FILES['msg']['name']['files'] as $key=>$val)
					{
						if($_FILES['msg']['error']['files'][$key] == 0)
						{
							$tempFileID = RequestTempFile($userID);
							$tempFileName = TempFileName($tempFileID);

							if(@move_uploaded_file($_FILES['msg']['tmp_name']['files'][$key],
								$tempFileName))
							{
								$tempFileFP = fopen($tempFileName, 'rb');
								$tempFiles[] = array(
									'id'	=> $tempFileID,
									'fp'	=> $tempFileFP
								);

								// add to mail
								$mail->AddAttachment($tempFileFP,
									'application/octet-stream',
									$val);
							}
							else
								ReleaseTempFile($tempFileID);
						}
					}
				}

				// save to drafts folder
				$mailFP = $mail->Build();
				$mailbox = _new('BMMailbox', array($userID, $userName, $this->_user));
				$mailObj = _new('BMMail', array(0, false, $mailFP, false));
				$mailObj->Parse();
				$mailObj->ParseInfo();
				if($mailbox->StoreMail($mailObj, FOLDER_DRAFTS) == STORE_RESULT_OK)
				{
					$result['draftID'] = $mailbox->_lastInsertId;
					$result['status'] = 'OK';
				}

				// clean up
				foreach($tempFiles as $tempFile)
				{
					fclose($tempFile['fp']);
					ReleaseTempFile($userID, $tempFile['id']);
				}

				$result['status'] = 'Error';
			}
		}
		else
		{
			$result['status'] = 'Invalid login';
		}
		return($result);
	}

	/**
	 * download update file
	 *
	 * @param string $userName User name
	 * @param string $passwordHash Password hash
	 * @param string $os Operating system (win/mac)
	 */
	function DownloadCurrentVersion($userName, $passwordHash, $os)
	{
		global $db;

		$userInfo = $this->CheckLogin($userName, $passwordHash);

		if($userInfo['loginOK'] > 0)
		{
			$res = $db->Query('SELECT `base_version`,`versionid`,`release_files` FROM {pre}tbx_versions WHERE `status`=? ORDER BY `versionid` DESC LIMIT 1',
				'released');
			if($res->RowCount() != 1)
			{
				header('HTTP/1.1 404 Not found');
				exit();
			}
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$releaseFiles = @unserialize($row['release_files']);
				if(!is_array($releaseFiles) || !isset($releaseFiles[$os]))
				{
					header('HTTP/1.1 404 Not found');
					exit();
				}

				$fileName = B1GMAIL_DATA_DIR . $releaseFiles[$os];
				header('Pragma: public');
				header('X-b1gMail-File-Signature: ' . $releaseFiles[$os.'_sig']);
				header('Content-Type: application/octet-stream');
				header('Content-Length: ' . filesize($fileName));
				header(sprintf('Content-Disposition: attachment; filename="Toolbox-%s.%d-%s.%s"',
					$tbxRow['base_version'],
					$tbxRow['versionid'],
					$os,
					$os == 'win' ? 'exe' : 'zip'));
				readfile($fileName);

				exit();
			}
			$res->Free();
		}
		else
		{
			header('HTTP/1.1 401 Unauthorized');
			exit();
		}
	}

	/**
	 * check user login, return user id on success, 0 otherwise
	 *
	 * @param string $userName User E-Mail
	 * @param string $passwordHash Password hash (MD5)
	 * @return int
	 */
	function CheckLogin($userName, $passwordHash)
	{
		global $db, $bm_prefs, $plugins;

		$userName = EncodeEMail($userName);
		$userID = BMUser::GetID($userName);
		if($userID != 0)
		{
			$res = $db->Query('SELECT passwort,passwort_salt,gesperrt,gruppe,mail2sms_nummer FROM {pre}users WHERE id=?',
				$userID);
			if($res->RowCount() == 1)
			{
				$row = $res->FetchArray();

				$user = _new('BMUser', array($userID));

				if(strtolower($row['passwort']) === strtolower(md5($passwordHash.$row['passwort_salt']))
					&& $row['gesperrt'] == 'no')
				{
					$group = $user->GetGroup();
					$groupRow = $group->_row;
					$this->_group = $group;

					// get latest toolbox version no
					$latestVersion = '0.0.0';
					$verRes = $db->Query('SELECT `versionid`,`base_version` FROM {pre}tbx_versions WHERE `status`=? ORDER BY `versionid` DESC LIMIT 1',
						'released');
					while($verRow = $verRes->FetchArray(MYSQLI_ASSOC))
						$latestVersion = sprintf('%s.%d', $verRow['base_version'], $verRow['versionid']);
					$verRes->Free();

					$result = array('loginOK' => 1, 'userID' => $userID,
						'hostName' => $bm_prefs['b1gmta_host'],
						'pop3Access' => $groupRow['pop3'] == 'yes',
						'imapAccess' => $groupRow['imap'] == 'yes',
						'smtpAccess' => $groupRow['smtp'] == 'yes',
						'smsAccess' => $user->SMSEnabled() && $groupRow['tbx_smsmanager'] == 'yes',
						'webdiskAccess' => $groupRow['webdisk'] + $user->_row['diskspace_add'] > 0 && $groupRow['tbx_webdisk'] == 'yes',
						'balance' => $user->GetBalance(),
						'latestVersion' => $latestVersion,
						'notificationInterval' => $groupRow['notifications'] == 'yes' ? $bm_prefs['notify_interval'] : 0,
						'plugins' => array());

					if($result['webdiskAccess'])
					{
						$result['webdiskSpaceLimit'] = $groupRow['webdisk'] + $user->_row['diskspace_add'];
						$result['webdiskUsedSpace'] = $user->_row['diskspace_used'];
						$result['webdiskTrafficLimit'] = $groupRow['traffic'] + $user->_row['traffic_add'];
						$result['webdiskUsedTraffic'] = $user->_row['traffic_down']+$user->_row['traffic_up'];
						$result['webdiskMaxFileSize'] = ParsePHPSize(ini_get('upload_max_filesize'));
					}

					$moduleResult = $plugins->callFunction('ToolInterfaceCheckLogin', false, true, array(&$user));
					foreach($moduleResult as $pluginName=>$addInfo)
						if(is_array($addInfo) && count($addInfo) > 0)
							$result['plugins'][$pluginName] = $addInfo;

					if($result['smsAccess'])
					{
						$sms = _new('BMSMS', array($userID, &$user));
						$result['smsMaxChars'] = $sms->GetMaxChars();
						$result['smsTypes'] = $sms->GetTypes();
						$result['smsPre'] = trim($groupRow['sms_pre']) == ''
							? '0'
							: $groupRow['sms_pre'];

						if($groupRow['sms_ownfrom'] == 'yes')
						{
							$result['smsOwnFrom'] = true;
							$result['smsFrom'] = $row['mail2sms_nummer'];
						}
						else
						{
							$result['smsOwnFrom'] = false;
							$result['smsFrom'] = $groupRow['sms_from'];
						}

						$this->_sms = $sms;
					}

					$this->_user = $user;
					return($result);
				}
			}
			$res->Free();
		}
		return(array('loginOK' => 0));
	}

	function HandleNonexistentMethod($method, $params, &$result)
	{
		ModuleFunction('ToolInterfaceHandler', array($method, $params, &$result, &$this));
	}
}
