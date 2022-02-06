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
RequestPrivileges(PRIVILEGES_ADMIN);
AdminRequirePrivilege('prefs.toolbox');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'overview';

$tabs = array(
	0 => array(
			'title'		=> $lang_admin['toolbox'],
			'relIcon'	=> 'toolbox32.png',
			'link'		=> 'toolbox.php?',
			'active'	=> $_REQUEST['action'] == 'overview'
	)
);

/**
 * load config descriptors
 */
$tbxConfig = LoadTbxConfigDescriptors();
if(count($tbxConfig) < 1)
	die('No toolbox versions found.');
$tbxLatestVersion 	= key(array_slice($tbxConfig, -1, 1, true));
$tbxLatestConfig 	= $tbxConfig[$tbxLatestVersion];

if(!defined('TOOLBOX_SERVER')) die('Toolbox creation is not supported in this version');
if(TOOLBOX_SERVER=='') die('Toolbox creation is not supported in this version');

/**
 * versions
 */
if($_REQUEST['action'] == 'overview')
{
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'editVersionConfig' && isset($_REQUEST['versionid']))
	{
		$versionID = (int)$_REQUEST['versionid'];

		// get data
		$res = $db->Query('SELECT `versionid`,`base_version`,`config` FROM {pre}tbx_versions WHERE `versionid`=?',
			$versionID);
		if($res->RowCount() != 1)
			die('Version not found');
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// build config array
		if(!isset($tbxConfig[$row['base_version']]))
			die('Unsupported toolbox version');
		$versionConfig = $tbxConfig[$row['base_version']];
		$dbConfig = unserialize($row['config']);
		foreach($dbConfig as $groupName=>$group)
			foreach($group as $itemKey=>$itemValue)
				$versionConfig[$groupName]['options'][$itemKey]['value'] = $itemValue;

		// show image?
		if(isset($_REQUEST['showImage']) && isset($_REQUEST['group']) && isset($_REQUEST['key'])
			&& isset($versionConfig[$_REQUEST['group']]['options'][$_REQUEST['key']]))
		{
			$imgPath = $versionConfig[$_REQUEST['group']]['options'][$_REQUEST['key']]['value'];
			if(substr($imgPath, 0, 4) == 'res/')
				$imgPath = B1GMAIL_DIR . $imgPath;
			else
				$imgPath = B1GMAIL_DATA_DIR . $imgPath;

			header('Pragma: public');
			header('Content-Type: image/png');
			header(sprintf('Content-Length: %d', filesize($imgPath)));
			readfile($imgPath);
			exit();
		}

		$tpl->assign('versionID',		$versionID);
		$tpl->assign('configGroups', 	$versionConfig);
		$tpl->assign('versionNo', 		sprintf('%s.%d', $tbxLatestVersion, $versionID));
		$tpl->assign('page', 			'toolbox.versions.edit.tpl');
	}

	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'saveVersionConfig' && isset($_REQUEST['versionid'])
			&& isset($_POST['prefs']) && is_array($_POST['prefs']))
	{
		$versionID = (int)$_REQUEST['versionid'];

		// get data
		$res = $db->Query('SELECT `versionid`,`base_version`,`config` FROM {pre}tbx_versions WHERE `versionid`=?',
			$versionID);
		if($res->RowCount() != 1)
			die('Version not found');
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// build config array
		if(!isset($tbxConfig[$row['base_version']]))
			die('Unsupported toolbox version');
		$versionConfig = $tbxConfig[$row['base_version']];
		$oldConfig = unserialize($row['config']);

		// config array
		$dbConfig = array();
		$fileErrors = array();
		foreach($versionConfig as $groupName=>$group)
		{
			$array = array();

			foreach($group['options'] as $itemKey=>$item)
			{
				$itemValue = $_POST['prefs'][$groupName][$itemKey];

				if($item['type'] == FIELD_IMAGE && is_array($itemValue))
				{
					$newItemValue = $oldConfig[$groupName][$itemKey];

					if($itemValue['mode'] == 'upload'
						&& isset($_FILES['prefs']['error'][$groupName][$itemKey]['file']))
					{
						if($_FILES['prefs']['error'][$groupName][$itemKey]['file'] == 0
							&& substr(strtolower($_FILES['prefs']['name'][$groupName][$itemKey]['file']), -4) == '.png')
						{
							$relDestFileName = 'toolboxImage_' . $versionID . '_' . $groupName . '_' . $itemKey;
							$destFileName = B1GMAIL_DATA_DIR . $relDestFileName;

							if(file_exists($destFileName))
								@unlink($destFileName);

							if(@move_uploaded_file($_FILES['prefs']['tmp_name'][$groupName][$itemKey]['file'], $destFileName))
							{
								$imgOK = true;

								// check size
								if(function_exists('imagecreatefrompng'))
								{
									$im = @imagecreatefrompng($destFileName);

									if($im)
									{
										list($dimX, $dimY) = explode('x', $versionConfig[$groupName]['options'][$itemKey]['imgSize']);

										if(imagesx($im) != $dimX || imagesy($im) != $dimY)
											$imgOK = false;

										imagedestroy($im);
									}
									else
										$imgOK = false;
								}

								if($imgOK)
								{
									$newItemValue = $relDestFileName;
								}
								else
								{
									@unlink($destFileName);

									$fileErrors[] = array($versionConfig[$groupName]['title'],
															$versionConfig[$groupName]['options'][$itemKey]['title']);
								}
							}
							else
							{
								$fileErrors[] = array($versionConfig[$groupName]['title'],
														$versionConfig[$groupName]['options'][$itemKey]['title']);
							}
						}
						else
						{
							$fileErrors[] = array($versionConfig[$groupName]['title'],
													$versionConfig[$groupName]['options'][$itemKey]['title']);
						}
					}

					$itemValue = $newItemValue;
				}
				else if($item['type'] == FIELD_CHECKBOX)
				{
					$itemValue = isset($_POST['prefs'][$groupName][$itemKey]);
				}
				else if(is_string($itemValue))
				{
					$itemValue = trim($itemValue);
				}

				$array[$itemKey] = $itemValue;
			}

			$dbConfig[$groupName] = $array;
		}

		// store config
		$db->Query('UPDATE {pre}tbx_versions SET `config`=? WHERE `versionid`=?',
			serialize($dbConfig),
			$versionID);

		// errors?
		if(count($fileErrors) > 0)
		{
			// assign
			$tpl->assign('versionID',	$versionID);
			$tpl->assign('fileErrors',	$fileErrors);
			$tpl->assign('page', 		'toolbox.versions.error.tpl');
		}
		else
		{
			header('Location: toolbox.php?do=release&versionid='.$versionID.'&sid='.session_id());
			exit();
		}
	}

	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'doRelease' && isset($_REQUEST['versionid']))
	{
		$versionID = (int)$_REQUEST['versionid'];

		// get data
		$res = $db->Query('SELECT `versionid`,`base_version`,`release_files` FROM {pre}tbx_versions WHERE `versionid`=? AND `status`=?',
			$versionID,
			'created');
		if($res->RowCount() != 1)
			die('Version not found');
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// check release files
		$releaseFiles = @unserialize($row['release_files']);
		if(is_array($releaseFiles) && isset($releaseFiles['win']) && isset($releaseFiles['mac']))
		{
			$db->Query('UPDATE {pre}tbx_versions SET `status`=? WHERE `versionid`=?',
				'released',
				$versionID);

			// display msg
			$tpl->assign('backLink', 'toolbox.php?');
			$tpl->assign('msgIcon', 'info32');
			$tpl->assign('msgTitle', $lang_admin['release']);
			$tpl->assign('msgText', $lang_admin['releasedone']);
			$tpl->assign('page', 'msg.tpl');
		}
		else
			die('Release files missing');
	}

	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'release' && isset($_REQUEST['versionid']))
	{
		$versionID = (int)$_REQUEST['versionid'];

		// get data
		$res = $db->Query('SELECT `versionid`,`base_version`,`config` FROM {pre}tbx_versions WHERE `versionid`=? AND `status`=?',
			$versionID,
			'created');
		if($res->RowCount() != 1)
			die('Version not found');
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// assign
		$tpl->assign('versionID',		$versionID);
		$tpl->assign('versionNo', 		sprintf('%s.%d', $row['base_version'], $versionID));
		$tpl->assign('page', 			'toolbox.versions.release.tpl');
	}

	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'addVersion')
	{
		// create default config
		$versionConfig = array();
		foreach($tbxLatestConfig as $groupName=>$group)
		{
			$groupConfig = array();
			foreach($group['options'] as $itemKey=>$item)
				$groupConfig[$itemKey] = $item['default'];
			$versionConfig[$groupName] = $groupConfig;
		}

		// copy config from older version, if available
		$res = $db->Query('SELECT `config` FROM {pre}tbx_versions ORDER BY `versionid` DESC LIMIT 1');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$dbConfig = unserialize($row['config']);

			foreach($dbConfig as $groupName=>$group)
				foreach($group as $itemKey=>$itemValue)
					$versionConfig[$groupName][$itemKey] = $itemValue;
		}
		$res->Free();

		// save
		$db->Query('INSERT INTO {pre}tbx_versions(`base_version`,`status`,`create_date`,`config`) VALUES(?,?,?,?)',
			$tbxLatestVersion,
			'created',
			time(),
			serialize($versionConfig));
		$versionID = $db->InsertId();

		// redirect to version config page
		header('Location: toolbox.php?do=editVersionConfig&versionid='.$versionID.'&sid='.session_id());
		exit;
	}

	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'downloadVersion' && isset($_REQUEST['versionid'])
		&& isset($_REQUEST['os']))
	{
		$versionID = (int)$_REQUEST['versionid'];

		// get data
		$res = $db->Query('SELECT `versionid`,`base_version`,`release_files` FROM {pre}tbx_versions WHERE `versionid`=?',
			$versionID);
		if($res->RowCount() != 1)
			die('Version not found.');
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// get release files
		$releaseFiles = @unserialize($row['release_files']);
		if(!is_array($releaseFiles) || !isset($releaseFiles[$_REQUEST['os']]))
			die('No release for this OS found.');

		// send file
		$fileName = B1GMAIL_DATA_DIR . $releaseFiles[$_REQUEST['os']];
		if(!file_exists($fileName))
			die('Release file not found.');
		header('Pragma: public');
		header('Content-Type: application/octet-stream');
		header(sprintf('Content-Disposition: attachment; filename="Toolbox-%s.%d-%s.%s"',
			$row['base_version'],
			$versionID,
			$_REQUEST['os'],
			$_REQUEST['os'] == 'win' ? 'exe' : 'zip'));
		header('Content-Length: ' . filesize($fileName));
		readfile($fileName);
		exit();
	}

	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'generateVersion' && isset($_REQUEST['versionid'])
		&& isset($_REQUEST['os']) && in_array($_REQUEST['os'], array('win', 'mac')))
	{
		$versionID = (int)$_REQUEST['versionid'];

		// get data
		$res = $db->Query('SELECT `versionid`,`base_version`,`config`,`release_files` FROM {pre}tbx_versions WHERE `versionid`=? AND `status`=?',
			$versionID,
			'created');
		if($res->RowCount() != 1)
			die('ERROR:Version not found.');
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// build files array
		$dbConfig = unserialize($row['config']);
		if(!isset($tbxConfig[$row['base_version']]))
			die('Unsupported toolbox version');
		$versionConfig = $tbxConfig[$row['base_version']];
		$i = 0;
		foreach($versionConfig as $groupName=>$group)
		{
			foreach($group['options'] as $itemKey=>$item)
			{
				if($item['type'] != FIELD_IMAGE)
					continue;

				if(!isset($dbConfig[$groupName][$itemKey]))
					continue;

				$imgPath = $dbConfig[$groupName][$itemKey];

				if(substr($imgPath, 0, 4) == 'res/')
					$imgPath = B1GMAIL_DIR . $imgPath;
				else
					$imgPath = B1GMAIL_DATA_DIR . $imgPath;

				$files[$i] = getFileContents($imgPath);
				$dbConfig[$groupName][$itemKey] = $i;
				$i++;
			}
		}

		// build arguments array
		$args = array(
			'serial'		=> $bm_prefs['serial'],
			'baseVersion'	=> $row['base_version'],
			'versionID'		=> $versionID,
			'lang'			=> $currentLanguage,
			'utf8'			=> (int)$bm_prefs['db_is_utf8'],
			'action'		=> 'create_version',
			'os'			=> $_REQUEST['os'],
			'config'		=> serialize($dbConfig)
		);
		foreach($files as $key=>$val)
			$args['files['.$key.']'] = base64_encode($val);

		// load http class
		if(!class_exists('BMHTTP'))
			include(B1GMAIL_DIR . 'serverlib/http.class.php');

		// build post data
		$request = array();
		foreach($args as $key=>$val)
			$request[] = urlencode($key) . '=' . urlencode($val);
		$postData = implode('&', $request);

		// get temp file
		$tempFileID = RequestTempFile(0, time() + TIME_ONE_HOUR);
		$tempFileName = TempFileName($tempFileID);
		$tempFileFP = fopen($tempFileName, 'wb+');

		// make request
		$headers = null;
		$http = _new('BMHTTP', array(TOOLBOX_SERVER));
		$http->DownloadToFP_POST($tempFileFP, $postData, 'application/x-www-form-urlencoded', $headers);
		fclose($tempFileFP);

		if($headers['x-b1gmail-status'] != 'ok')
		{
			echo 'ERROR:' . $headers['x-b1gmail-text'];
		}
		else
		{
			// check file signature
			if($headers['x-b1gmail-signature'] == md5(md5_file($tempFileName) . B1GMAIL_SIGNKEY))
			{
				$relDestFileName = 'toolboxRelease_' . $versionID . '_' . $_REQUEST['os'];
				$destFileName = B1GMAIL_DATA_DIR . $relDestFileName;

				// copy to data folder
				if(copy($tempFileName, $destFileName))
				{
					// add to release files
					$releaseFiles = @unserialize($row['release_files']);
					if(!is_array($releaseFiles))
						$releaseFiles = array();
					$releaseFiles[$_REQUEST['os']] = $relDestFileName;
					$releaseFiles[$_REQUEST['os'].'_sig'] = $headers['x-b1gmail-file-signature'];
					$db->Query('UPDATE {pre}tbx_versions SET `release_files`=? WHERE `versionid`=?',
						serialize($releaseFiles),
						$versionID);

					echo 'OK';
				}
				else
				{
					echo 'ERROR:Failed to store release file.';
				}
			}
			else
			{
				echo 'ERROR:Signature mismatch.';
			}
		}

		// release temp file
		ReleaseTempFile(0, $tempFileID);
		exit();
	}

	else
	{
		$haveRelease = false;

		// get versions
		$tbxVersions = array();
		$res = $db->Query('SELECT `versionid`,`base_version`,`status` FROM {pre}tbx_versions ORDER BY `versionid` DESC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($row['status'] == 'released')
				$haveRelease = true;
			$tbxVersions[$row['versionid']] = $row;
		}
		$res->Free();

		$tpl->assign('haveRelease', 	$haveRelease);
		$tpl->assign('latestVersion', 	$tbxLatestVersion);
		$tpl->assign('versions', 		$tbxVersions);
		$tpl->assign('page', 			'toolbox.versions.tpl');
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['welcome'] . ' &raquo; ' . $lang_admin['toolbox']);
$tpl->display('page.tpl');
?>