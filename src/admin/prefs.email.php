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

include('../serverlib/admin.inc.php');
include('../serverlib/zip.class.php');
RequestPrivileges(PRIVILEGES_ADMIN);
AdminRequirePrivilege('prefs.email');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'common';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['common'],
		'relIcon'	=> 'ico_prefs_common.png',
		'link'		=> 'prefs.email.php?',
		'active'	=> $_REQUEST['action'] == 'common'
	),
	1 => array(
		'title'		=> $lang_admin['receive'],
		'relIcon'	=> 'ico_prefs_receiving.png',
		'link'		=> 'prefs.email.php?action=receive&',
		'active'	=> $_REQUEST['action'] == 'receive'
	),
	2 => array(
		'title'		=> $lang_admin['send'],
		'relIcon'	=> 'ico_prefs_sending.png',
		'link'		=> 'prefs.email.php?action=send&',
		'active'	=> $_REQUEST['action'] == 'send'
	),
	3 => array(
		'title'		=> $lang_admin['antispam'],
		'relIcon'	=> 'antispam.png',
		'link'		=> 'prefs.email.php?action=antispam&',
		'active'	=> $_REQUEST['action'] == 'antispam'
	),
	4 => array(
		'title'		=> $lang_admin['antivirus'],
		'relIcon'	=> 'antivirus.png',
		'link'		=> 'prefs.email.php?action=antivirus&',
		'active'	=> $_REQUEST['action'] == 'antivirus'
	),
	5 => array(
		'title'		=> $lang_admin['smime'],
		'relIcon'	=> 'cert32.png',
		'link'		=> 'prefs.email.php?action=smime&',
		'active'	=> $_REQUEST['action'] == 'smime'
	)
);

/**
 * common
 */
if($_REQUEST['action'] == 'common')
{
	if(isset($_REQUEST['save']))
	{
		$db->Query('UPDATE {pre}prefs SET blobstorage_provider=?, blobstorage_compress=?, fts_bg_indexing=?',
			$_REQUEST['blobstorage_provider'],
			isset($_REQUEST['blobstorage_compress']) ? 'yes' : 'no',
			isset($_REQUEST['fts_bg_indexing']) ? 'yes' : 'no');
		ReadConfig();
	}

	// assign
	$tpl->assign('bsUserDBAvailable', BMBlobStorage::createProvider(BMBLOBSTORAGE_USERDB)->isAvailable());
	$tpl->assign('page', 'prefs.email.common.tpl');
}

/**
 * receive
 */
else if($_REQUEST['action'] == 'receive')
{
	if(isset($_REQUEST['save']))
	{
		$db->Query('UPDATE {pre}prefs SET receive_method=?,pop3_host=?,pop3_port=?,pop3_user=?,pop3_pass=?,fetchcount=?,errormail=?,failure_forward=?,mailmax=?,recipient_detection=?,detect_duplicates=?,returnpath_check=?',
			$_REQUEST['receive_method'],
			$_REQUEST['pop3_host'],
			(int)$_REQUEST['pop3_port'],
			$_REQUEST['pop3_user'],
			$_REQUEST['pop3_pass'],
			(int)$_REQUEST['fetchcount'],
			$_REQUEST['errormail'],
			isset($_REQUEST['failure_forward']) ? 'yes' : 'no',
			(int)$_REQUEST['mailmax']*1024,
			$_REQUEST['recipient_detection'],
			isset($_REQUEST['detect_duplicates']) ? 'yes' : 'no',
			isset($_REQUEST['returnpath_check']) ? 'yes' : 'no');
		ReadConfig();
	}

	// assign
	$tpl->assign('page', 'prefs.email.receive.tpl');
}

/**
 * send
 */
else if($_REQUEST['action'] == 'send')
{
	if(isset($_REQUEST['save']))
	{
		$blockedArray = explode("\n", $_REQUEST['blocked']);
		foreach($blockedArray as $key=>$val)
			if(($val = trim($val)) != '')
				$blockedArray[$key] = trim($val);
			else
				unset($blockedArray[$key]);
		$blocked = implode(':', $blockedArray);

		$db->Query('UPDATE {pre}prefs SET send_method=?,smtp_host=?,smtp_port=?,smtp_auth=?,smtp_user=?,smtp_pass=?,blocked=?,sendmail_path=?,passmail_abs=?,einsch_life=?,write_xsenderip=?,min_draft_save_interval=?',
			$_REQUEST['send_method'],
			$_REQUEST['smtp_host'],
			(int)$_REQUEST['smtp_port'],
			isset($_REQUEST['smtp_auth']) ? 'yes' : 'no',
			$_REQUEST['smtp_user'],
			$_REQUEST['smtp_pass'],
			$blocked,
			$_REQUEST['sendmail_path'],
			EncodeEMail($_REQUEST['passmail_abs']),
			$_REQUEST['einsch_life']*TIME_ONE_DAY,
			isset($_REQUEST['write_xsenderip']) ? 'yes' : 'no',
			max(5, $_REQUEST['min_draft_save_interval']));
		ReadConfig();
	}

	// assign
	$bm_prefs['blocked'] = str_replace(':', "\n", $bm_prefs['blocked']);
	$tpl->assign('page', 'prefs.email.send.tpl');
}

/**
 * antispam
 */
else if($_REQUEST['action'] == 'antispam')
{
	if(isset($_REQUEST['save']))
	{
		$dnsblArray = explode("\n", $_REQUEST['dnsbl']);
		foreach($dnsblArray as $key=>$val)
			if(($val = trim($val)) != '')
				$dnsblArray[$key] = $val;
			else
				unset($dnsblArray[$key]);
		$dnsbl = implode(':', $dnsblArray);

		$db->Query('UPDATE {pre}prefs SET spamcheck=?,dnsbl=?,use_bayes=?,bayes_mode=?,dnsbl_requiredservers=?',
			isset($_REQUEST['spamcheck']) ? 'yes' : 'no',
			$dnsbl,
			isset($_REQUEST['use_bayes']) ? 'yes' : 'no',
			$_REQUEST['bayes_mode'],
			$_REQUEST['dnsbl_requiredservers']);
		ReadConfig();
	}

	if(isset($_REQUEST['resetBayesDB']))
	{
		$db->Query('TRUNCATE TABLE {pre}spamindex');
		$db->Query('UPDATE {pre}prefs SET bayes_spam=0, bayes_nonspam=0');
		$db->Query('UPDATE {pre}users SET bayes_spam=0, bayes_nonspam=0');
	}

	// bayes resetable?
	$res = $db->Query('SELECT COUNT(*) FROM {pre}spamindex');
	list($bayesWordCount) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();

	// assign
	$bm_prefs['dnsbl'] = str_replace(':', "\n", $bm_prefs['dnsbl']);
	$tpl->assign('bayesWordCount', $bayesWordCount);
	$tpl->assign('page', 'prefs.email.antispam.tpl');
}

/**
 * antivirus
 */
else if($_REQUEST['action'] == 'antivirus')
{
	if(isset($_REQUEST['save']))
	{
		$db->Query('UPDATE {pre}prefs SET use_clamd=?,clamd_host=?,clamd_port=?',
			isset($_REQUEST['use_clamd']) ? 'yes' : 'no',
			$_REQUEST['clamd_host'],
			(int)$_REQUEST['clamd_port']);
		ReadConfig();
	}

	// assign
	$tpl->assign('page', 'prefs.email.antivirus.tpl');
}

/**
 * s/mime
 */
else if($_REQUEST['action'] == 'smime')
{
	// check for OpenSSL extension
	if(!SMIME_SUPPORT)
	{
		$tpl->assign('msgTitle', $lang_admin['smime']);
		$tpl->assign('msgText', $lang_admin['openssl_err']);
		$tpl->assign('msgIcon', 'error32');
		$tpl->assign('page', 'msg.tpl');
	}

	// extension is available
	else
	{
		// prefs + rootcerts
		if(!isset($_REQUEST['do']))
		{
			$stopIt = false;

			// add
			if(isset($_REQUEST['add']))
			{
				$success = false;
				$error = 'format';

				if(isset($_FILES['certfile'])
					&& $_FILES['certfile']['error'] == 0
					&& $_FILES['certfile']['size'] > 5)
				{
					// request temp file
					$tempFileID = RequestTempFile(0);
					$tempFileName = TempFileName($tempFileID);

					// move uploaded file to temp file
					if(move_uploaded_file($_FILES['certfile']['tmp_name'], $tempFileName))
					{
						// read file
						$fp = fopen($tempFileName, 'rb');
						$certData = fread($fp, filesize($tempFileName));
						fclose($fp);

						// parse
						$cp = @openssl_x509_read($certData);
						if($cp)
						{
							$certInfo = @openssl_x509_parse($cp, true);
							openssl_x509_free($cp);

							// exists?
							$res = $db->Query('SELECT COUNT(*) FROM {pre}certificates WHERE `hash`=? AND `type`=? AND `userid`=?',
								$certInfo['hash'],
								CERTIFICATE_TYPE_ROOT,
								0);
							list($certCount) = $res->FetchArray(MYSQLI_NUM);
							$res->Free();

							if($certCount != 0)
							{
								$error = 'exists';
							}
							else
							{
								// check purpose
								$signCA = $encryptCA = false;
								foreach($certInfo['purposes'] as $purpose)
								{
									if($purpose[2] == 'smimeencrypt'
										&& $purpose[1])
										$encryptCA = true;
									else if($purpose[2] == 'smimesign'
										&& $purpose[1])
										$signCA = true;
								}

								// s/mime CA?
								if($encryptCA || $signCA)
								{
									$db->Query('INSERT INTO {pre}certificates(`type`,`userid`,`hash`,`cn`,`validfrom`,`validto`,`pemdata`) '
										. 'VALUES(?,?,?,?,?,?,?)',
										CERTIFICATE_TYPE_ROOT,
										0,
										$certInfo['hash'],
										!isset($certInfo['subject']['CN'])
											? ((is_array($certInfo['subject']['OU']) ? array_shift($certInfo['subject']['OU']) : $certInfo['subject']['OU']) . ' (' . $certInfo['subject']['O'] . ')')
											: (is_array($certInfo['subject']['CN']) ? array_shift($certInfo['subject']['CN']) : $certInfo['subject']['CN']),
										$certInfo['validFrom_time_t'],
										$certInfo['validTo_time_t'],
										$certData);
									$success = true;
								}
								else
									$error = 'noca';
							}
						}
					}

					// release temp file
					ReleaseTempFile(0, $tempFileID);

					// display result on error
					if(!$success)
					{
						$tpl->assign('msgTitle', $lang_admin['error']);
						$tpl->assign('msgText', $lang_admin['cert_err_'.$error]);
						$tpl->assign('msgIcon', 'error32');
						$tpl->assign('page', 'msg.tpl');
						$stopIt = true;
					}
				}
			}

			// delete
			if(isset($_REQUEST['delete']))
			{
				$db->Query('DELETE FROM {pre}certificates WHERE `certificateid`=? AND `type`=? AND `userid`=?',
					(int)$_REQUEST['delete'],
					CERTIFICATE_TYPE_ROOT,
					0);
			}

			// export
			if(isset($_REQUEST['export']))
			{
				$res = $db->Query('SELECT `hash`,`pemdata` FROM {pre}certificates WHERE `certificateid`=? AND `type`=? AND `userid`=?',
					(int)$_REQUEST['export'],
					CERTIFICATE_TYPE_ROOT,
					0);
				while($row = $res->FetchArray(MYSQLI_ASSOC))
				{
					header('Pragma: public');
					header('Content-Type: application/x-pem-file');
					header('Content-Length: ' . strlen($row['pemdata']));
					header(sprintf('Content-Disposition: attachment; filename=cert-%s.pem',
						$row['hash']));
					echo $row['pemdata'];
					exit();
				}
				$res->Free();
			}

			// mass action
			if(isset($_REQUEST['massAction']) && isset($_REQUEST['certs'])
				&& is_array($_REQUEST['certs']))
			{
				$certs = $_REQUEST['certs'];

				if($_REQUEST['massAction'] == 'delete')
				{
					$db->Query('DELETE FROM {pre}certificates WHERE (`certificateid` IN ?) AND `type`=? AND `userid`=?',
						$certs,
						CERTIFICATE_TYPE_ROOT,
						0);
				}
				else if($_REQUEST['massAction'] == 'export')
				{
					$tempFileID = RequestTempFile(0);
					$tempFileName = TempFileName($tempFileID);

					// create ZIP file
					$fp = fopen($tempFileName, 'wb+');
					$zip = _new('BMZIP', array($fp));

					$res = $db->Query('SELECT `hash`,`pemdata` FROM {pre}certificates WHERE (`certificateid` IN ?) AND `type`=? AND `userid`=?',
						$certs,
						CERTIFICATE_TYPE_ROOT,
						0);
					while($row = $res->FetchArray(MYSQLI_ASSOC))
					{
						$certTempFileID = RequestTempFile(0);
						$certTempFileName = TempFileName($certTempFileID);

						$certFP = fopen($certTempFileName, 'wb');
						fwrite($certFP, $row['pemdata']);
						fclose($certFP);

						$zip->AddFile($certTempFileName, 'cert-' . $row['hash'] . '.pem');

						ReleaseTempFile(0, $certTempFileID);
					}
					$res->Free();
					$size = $zip->Finish();

					// headers
					header('Pragma: public');
					header('Content-Disposition: attachment; filename="certificates.zip"');
					header('Content-Type: application/zip');
					header(sprintf('Content-Length: %d',
						$size));

					// send
					while(is_resource($fp) && !feof($fp))
					{
						$block = fread($fp, 4096);
						echo $block;
					}

					// clean up
					fclose($fp);
					ReleaseTempFile(0, $tempFileID);
					exit();
				}
			}

			if(!$stopIt)
			{
				$certs = array();
				$res = $db->Query('SELECT `certificateid`,`cn`,`validfrom`,`validto` FROM {pre}certificates WHERE `type`=? AND `userid`=? ORDER BY `cn` ASC',
					CERTIFICATE_TYPE_ROOT, 0);
				while($row = $res->FetchArray(MYSQLI_ASSOC))
				{
					$row['valid'] = $row['validfrom'] <= time() && $row['validto'] >= time();
					$certs[$row['certificateid']] = $row;
				}
				$res->Free();

				$tpl->assign('caAvailable', trim($bm_prefs['ca_cert']) != '' && trim($bm_prefs['ca_cert_pk']) != '');
				$tpl->assign('now', time());
				$tpl->assign('certs', $certs);
				$tpl->assign('page', 'prefs.email.smime.tpl');
			}
		}

		// edit CA
		else if($_REQUEST['do'] == 'editca')
		{
			$stopIt = false;

			if(isset($_REQUEST['set']))
			{
				$certData = $keyData = '';

				// pem?
				if(isset($_FILES['cert_ca_pem'])
					&& $_FILES['cert_ca_pem']['error'] == 0
					&& $_FILES['cert_ca_pem']['size'] > 5)
				{
					// request temp file
					$tempFileID = RequestTempFile(0);
					$tempFileName = TempFileName($tempFileID);

					// move uploaded file to temp file
					if(move_uploaded_file($_FILES['cert_ca_pem']['tmp_name'], $tempFileName))
						$certData = getFileContents($tempFileName);

					ReleaseTempFile(0, $tempFileID);
				}

				// key?
				if(isset($_FILES['cert_ca_key'])
					&& $_FILES['cert_ca_key']['error'] == 0
					&& $_FILES['cert_ca_key']['size'] > 5)
				{
					// request temp file
					$tempFileID = RequestTempFile(0);
					$tempFileName = TempFileName($tempFileID);

					// move uploaded file to temp file
					if(move_uploaded_file($_FILES['cert_ca_key']['tmp_name'], $tempFileName))
						$keyData = getFileContents($tempFileName);

					ReleaseTempFile(0, $tempFileID);
				}

				$success = false;
				$error = 'format';

				if($certData && $keyData && strlen($certData) > 5 && strlen($keyData) > 5)
				{
					$certData = str_replace(' TRUSTED ', ' ', $certData);
					$cert = @openssl_x509_read(trim($certData));

					if($cert)
					{
						if(@openssl_x509_check_private_key($cert,
							!empty($_REQUEST['cert_ca_pass'])
								? array($keyData, $_REQUEST['cert_ca_pass'])
								: $keyData))
						{
							$certInfo = openssl_x509_parse($cert);

							// check purpose
							$signCA = $encryptCA = false;
							foreach($certInfo['purposes'] as $purpose)
							{
								if($purpose[2] == 'smimeencrypt'
									&& $purpose[1])
									$encryptCA = true;
								else if($purpose[2] == 'smimesign'
									&& $purpose[1])
									$signCA = true;
							}

							if($signCA && $encryptCA)
							{
								$db->Query('UPDATE {pre}prefs SET ca_cert=?,ca_cert_pk=?,ca_cert_pk_pass=?',
									$certData,
									$keyData,
									$_REQUEST['cert_ca_pass'] != '' ? base64_encode(CryptPKPassPhrase($_REQUEST['cert_ca_pass'])) : '');

								$res = $db->Query('SELECT COUNT(*) FROM {pre}certificates WHERE `hash`=? AND `type`=? AND `userid`=?',
									$certInfo['hash'],
									CERTIFICATE_TYPE_ROOT,
									0);
								list($certCount) = $res->FetchArray(MYSQLI_NUM);
								$res->Free();

								if($certCount == 0)
									$db->Query('INSERT INTO {pre}certificates(`type`,`userid`,`hash`,`cn`,`validfrom`,`validto`,`pemdata`) '
										. 'VALUES(?,?,?,?,?,?,?)',
										CERTIFICATE_TYPE_ROOT,
										0,
										$certInfo['hash'],
										$certInfo['subject']['CN'],
										$certInfo['validFrom_time_t'],
										$certInfo['validTo_time_t'],
										$certData);

								ReadConfig();
								$success = true;
							}
							else
								$error = 'purpose';
						}
						else
							$error = 'pkcheck';

						openssl_x509_free($cert);
					}
				}

				// display result on error
				if(!$success)
				{
					$tpl->assign('msgTitle', $lang_admin['error']);
					$tpl->assign('msgText', $lang_admin['cert_caerr_'.$error]);
					$tpl->assign('msgIcon', 'error32');
					$tpl->assign('page', 'msg.tpl');
					$stopIt = true;
				}
			}

			if(!$stopIt)
			{
				$certInfo = false;
				$cert = @openssl_x509_read($bm_prefs['ca_cert']);
				if($cert)
				{
					$certInfo = openssl_x509_parse($cert);
					openssl_x509_free($cert);
				}

				$tpl->assign('validCert', $certInfo && $certInfo['validFrom_time_t'] <= time() && $certInfo['validTo_time_t'] >= time());
				$tpl->assign('certInfo', postProcessCertInfo($certInfo));
				$tpl->assign('page', 'prefs.email.smime.ca.tpl');
			}
		}
	}
}

$tpl->assign('bm_prefs', $bm_prefs);
$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['email']);
$tpl->display('page.tpl');
