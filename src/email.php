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

include('./serverlib/init.inc.php');
if(!class_exists('BMMailbox'))
	include('./serverlib/mailbox.class.php');
include('./serverlib/zip.class.php');
RequestPrivileges(PRIVILEGES_USER);

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));

/**
 * shared email code
 */
include('./serverlib/email.top.php');

/**
 * default action = start
 */
$tpl->addJSFile('li', 'clientlib/selectable.js');
$tpl->addJSFile('li', $tpl->tplDir . 'js/email.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'folder';

/**
 * folder view
 */
if($_REQUEST['action'] == 'folder')
{
	$sortColumns = array('von', 'an', 'betreff', 'recdate', 'size');

	// get folder id (default = inbox)
	$folderID = (isset($_REQUEST['folder']) && (isset($folderList[(int)$_REQUEST['folder']]) || $mailbox->FolderExists((int)$_REQUEST['folder'])))
					? (int)$_REQUEST['folder']
					: FOLDER_INBOX;

	/**
	 * folder actions
	 */
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'flagMessage'
								&& isset($_REQUEST['id'])
								&& isset($_REQUEST['flag'])
								&& isset($_REQUEST['value']))
	{
		$mailbox->FlagMail((int)$_REQUEST['flag'],
			$_REQUEST['value']==1 ? true : false,
			(int)$_REQUEST['id']);
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'emptyFolder')
	{
		$mailbox->EmptyFolder($folderID);
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'markAllAsRead')
	{
		$mails = $mailbox->GetMailIDList($folderID);

		foreach($mails as $mailID)
			if($mailbox->FlagMail(FLAG_UNREAD, isset($_REQUEST['unread']) ? true : false, $mailID));
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'downloadAll')
	{
		$mails = $mailbox->GetMailIDList($folderID);

		$tempFileID = RequestTempFile($userRow['id'], time()+TIME_ONE_HOUR, true);
		$tempFileName = TempFileName($tempFileID);

		// create ZIP file
		$fp = fopen($tempFileName, 'wb+');
		$zip = _new('BMZIP', array($fp));
		foreach($mails as $mailID)
		{
			if($mailbox->MailExists($mailID))
			{
				$theMail = $mailbox->GetMail($mailID);
				if(is_object($theMail))
				{
					$mailFP  = $theMail->GetMessageFP();
					if(is_resource($mailFP))
					{
						$zip->AddFileByFP($mailFP, $mailID . '.eml');
						fclose($mailFP);
					}
				}
			}
		}
		$size = $zip->Finish();

		// headers
		header('Pragma: public');
		header('Content-Disposition: attachment; filename="mails.zip"');
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
		ReleaseTempFile($userRow['id'], $tempFileID);
		exit();
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'deleteMail'
								&& (isset($_REQUEST['id']) || (isset($_REQUEST['ids']) && is_array($_REQUEST['ids']))))
	{
		StartPageOutput();

		if(isset($_REQUEST['id']))
		{
			$mailbox->DeleteMail((int)$_REQUEST['id']);
		}
		else if(isset($_REQUEST['ids']))
		{
			foreach($_REQUEST['ids'] as $id)
				$mailbox->DeleteMail((int)$id);
		}
		if(isset($_REQUEST['rpc']))
		{
			echo('1');

			if(isset($_REQUEST['getFolderList']))
			{
				echo ',';
				$tpl->reassignFolderList = true;
				$tpl->display('li/email.folderlist.tpl');
			}

			exit();
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'setViewOptions'
		&& IsPOSTRequest())
	{
		if(in_array($_REQUEST['group_mode'],
					array('-', 'fetched', 'von', 'beantwortet', 'weitergeleitet', 'attach', 'gelesen', 'flagged', 'done', 'color')))
			$mailbox->SetGroupMode($folderID, $_REQUEST['group_mode']);
		$mailbox->SetMailsPerPage($folderID, (int)$_REQUEST['perpage']);

		if(isset($_REQUEST['overlay']))
		{
			echo '<script>' . "\n";
			echo '<!--' . "\n";
			echo 'parent.switchPage(0);' . "\n";
			echo 'parent.hideOverlay();' . "\n";
			echo '//-->' . "\n";
			echo '</script>' . "\n";
			exit();
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'viewOptions')
	{
		$tpl->assign('folderID', 	$folderID);
		$tpl->assign('perPage',	 	$mailbox->GetMailsPerPage($folderID));
		$tpl->assign('groupMode',	$mailbox->GetGroupMode($folderID));

		$tpl->display('li/email.folder.viewoptions.tpl');
		exit();
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'action')
	{
		//
		// collect IDs
		//
		$mailIDs = array();

		if(isset($_POST['selectedMailIDs']) && trim($_POST['selectedMailIDs'])!='')
		{
			$_mailIDs = explode(';', $_POST['selectedMailIDs']);
			foreach($_mailIDs as $_mailID)
				$mailIDs[] = (int)$_mailID;
		}
		else
		{
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 5) == 'mail_')
					$mailIDs[] = (int)substr($key, 5);
		}

		//
		// execute action
		//
		if(count($mailIDs) > 0)
		{
			// delete
			if($_REQUEST['massAction'] == 'delete')
			{
				foreach($mailIDs as $mailID)
					$mailbox->DeleteMail($mailID);
			}

			// forward
			else if($_REQUEST['massAction'] == 'forward')
			{
				$url = 'email.compose.php?sid=' . session_id();
				foreach($mailIDs as $mailKey=>$mailID)
					$url .= '&forward[' . $mailKey . ']=' . $mailID;
				header('Location: ' . $url);
				exit();
			}

			// download
			else if($_REQUEST['massAction'] == 'download')
			{
				$tempFileID = RequestTempFile($userRow['id'], time()+TIME_ONE_HOUR, true);
				$tempFileName = TempFileName($tempFileID);

				// create ZIP file
				$fp = fopen($tempFileName, 'wb+');
				$zip = _new('BMZIP', array($fp));
				foreach($mailIDs as $mailID)
				{
					if($mailbox->MailExists($mailID))
					{
						$theMail = $mailbox->GetMail($mailID);
						if(is_object($theMail))
						{
							$mailFP  = $theMail->GetMessageFP();
							if(is_resource($mailFP))
							{
								$zip->AddFileByFP($mailFP, $mailID . '.eml');
								fclose($mailFP);
							}
						}
					}
				}
				$size = $zip->Finish();

				// headers
				header('Pragma: public');
				header('Content-Disposition: attachment; filename="mails.zip"');
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
				ReleaseTempFile($userRow['id'], $tempFileID);
				exit();
			}

			// markread
			else if($_REQUEST['massAction'] == 'markread')
			{
				foreach($mailIDs as $mailID)
					$mailbox->FlagMail(FLAG_UNREAD,
						false,
						$mailID);
			}

			// markunread
			else if($_REQUEST['massAction'] == 'markunread')
			{
				foreach($mailIDs as $mailID)
					$mailbox->FlagMail(FLAG_UNREAD,
						true,
						$mailID);
			}

			// mark
			else if($_REQUEST['massAction'] == 'mark')
			{
				foreach($mailIDs as $mailID)
					$mailbox->FlagMail(FLAG_FLAGGED,
						true,
						$mailID);
			}

			// unmark
			else if($_REQUEST['massAction'] == 'unmark')
			{
				foreach($mailIDs as $mailID)
					$mailbox->FlagMail(FLAG_FLAGGED,
						false,
						$mailID);
			}

			// mark done
			else if($_REQUEST['massAction'] == 'done')
			{
				foreach($mailIDs as $mailID)
					$mailbox->FlagMail(FLAG_DONE,
						true,
						$mailID);
			}

			// unmark done
			else if($_REQUEST['massAction'] == 'undone')
			{
				foreach($mailIDs as $mailID)
					$mailbox->FlagMail(FLAG_DONE,
						false,
						$mailID);
			}

			// mark as spam
			else if($_REQUEST['massAction'] == 'markspam')
			{
				foreach($mailIDs as $mailID)
					$mailbox->SetSpamStatus($mailID, true);
			}

			// mark as nonspam
			else if($_REQUEST['massAction'] == 'marknonspam')
			{
				foreach($mailIDs as $mailID)
					$mailbox->SetSpamStatus($mailID, false);
			}

			// move to group
			else if(substr($_REQUEST['massAction'], 0, 7) == 'moveto_')
			{
				$destFolderID = (int)substr($_REQUEST['massAction'], 7);
				$mailbox->MoveMail($mailIDs, $destFolderID);
			}

			// move to group
			else if(substr($_REQUEST['massAction'], 0, 6) == 'color_')
			{
				$newColor = (int)substr($_REQUEST['massAction'], 6);
				$mailbox->ColorMail($mailIDs, $newColor);
			}
		}
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'setPreviewPosition'
		&& isset($_REQUEST['pos']))
	{
		$thisUser->SetPref('previewPosition', $_REQUEST['pos']);
		$tpl->assign('narrow', $_REQUEST['pos'] == 'right');
	}

	// get page info
	$mailsPerPage = $mailbox->GetMailsPerPage($folderID);
	$pageNo = (isset($_REQUEST['page']))
					? (int)$_REQUEST['page']
					: 1;
	$mailCount = $mailbox->GetMailCount($folderID);
	$pageCount = max(1, ceil($mailCount / max(1, $mailsPerPage)));
	$pageNo = min($pageCount, max(1, $pageNo));
	$groupMode = $mailbox->GetGroupMode($folderID);

	// get sort info
	$sortColumn = (isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], $sortColumns))
					? $_REQUEST['sort']
					: 'fetched';
	$sortOrder = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')))
					? $_REQUEST['order']
					: 'desc';

	// get mail list
	$mailList = $mailbox->GetMailList($folderID,
										$pageNo,
										$mailsPerPage,
										$sortColumn,
										$sortOrder,
										$groupMode);

	// groups
	if($groupMode != '-')
		$mailList = $mailbox->GroupMailList($mailList, $groupMode);

	// update last notify date
	if($folderID == FOLDER_INBOX)
		$mailbox->UpdateLastNotifyDate();

	// page output
	$tpl->assign('enablePreview', $userRow['preview'] == 'yes');
	$tpl->assign('refreshEnabled', $userRow['in_refresh'] > 0);
	$tpl->assign('refreshInterval', max(15, $userRow['in_refresh']));
	$tpl->assign('perPage', $mailsPerPage);
	$tpl->assign('groupMode', $groupMode);
	$tpl->assign('pageNo', $pageNo);
	$tpl->assign('pageCount', $pageCount);
	$tpl->assign('folderID', $folderID);
	$tpl->assign('folderString', sprintf('folder=%d&sort=%s&order=%s&page=%d',
											$folderID, $sortColumn, $sortOrder, $pageNo));
	$tpl->assign('mailList', $mailList);
	$tpl->assign('sortColumn', $sortColumn);
	$tpl->assign('sortOrder', $sortOrder);
	$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
	if(isset($folderList[$folderID]))
	{
		$tpl->assign('folderInfo', $folderList[$folderID]);
		$folderTitle = $folderList[$folderID]['title'];
	}
	else
	{
		$folderInfo = $mailbox->GetFolder($folderID);
		$folderInfo['title'] = $folderInfo['titel'];
		$folderInfo['type'] = 'folder';
		$tpl->assign('folderInfo', $folderInfo);
		$folderTitle = $folderInfo['title'];
	}
	$tpl->assign('pageTitle', HTMLFormat($folderTitle));
	$tpl->assign('flexSpans', $bm_prefs['flexspans'] == 'yes');

	if(isset($_REQUEST['inline']))
	{
		$html = '';
		if(isset($_REQUEST['narrow']))
			$html = $tpl->fetch('li/email.folder.contents.narrow.tpl');
		else
			$html = $tpl->fetch('li/email.folder.contents.tpl');
		header('Content-Type: application/json; charset="' . $currentCharset . '"');
		echo '{ ' . "\n";
		echo "\t\"id\": " . $folderID . ",\n";
		echo "\t\"windowTitle\": \"" . addslashes(DecodeHTMLEntities($folderTitle) . ' - ' . $bm_prefs['titel']) . "\",\n";
		echo "\t\"sortColumn\": \"$sortColumn\",\n";
		echo "\t\"sortOrder\": \"$sortOrder\",\n";
		echo "\t\"pageNo\": $pageNo,\n";
		echo "\t\"pageCount\": $pageCount,\n";
		echo "\t\"html\": \"" . str_replace(array("\r\n", "\r", "\n", "\t"), ' ', addslashes($html)) . "\"\n";
		echo '}' . "\n";
	}
	else
	{
		$tpl->assign('pageContent', 'li/email.folder.tpl');
		$tpl->display('li/index.tpl');
	}
}

/**
 * rpc move mails
 */
else if($_REQUEST['action'] == 'moveMails'
	&& isset($_REQUEST['mails'])
	&& isset($_REQUEST['destFolderID']))
{
	StartPageOutput();

	$mailbox->MoveMail(explode(',', $_REQUEST['mails']), (int)$_REQUEST['destFolderID']);

	echo('1');

	if(isset($_REQUEST['getFolderList']))
	{
		echo ',';
		$tpl->reassignFolderList = true;
		$tpl->display('li/email.folderlist.tpl');
	}

	exit();
}

/**
 * rpc flag
 */
else if($_REQUEST['action'] == 'flagMessage'
		&& (isset($_REQUEST['id']) || (isset($_REQUEST['ids']) && is_array($_REQUEST['ids'])))
		&& isset($_REQUEST['flag'])
		&& isset($_REQUEST['value']))
{
	StartPageOutput();

	$ids = array();
	if(isset($_REQUEST['id']))
	{
		$ids[] = (int)$_REQUEST['id'];
	}
	else if(isset($_REQUEST['ids']) && is_array($_REQUEST['ids']) && count($_REQUEST['ids']) > 0)
	{
		$ids = array_map('intval', $_REQUEST['ids']);
	}
	else
		exit();

	$results = array();
	foreach($ids as $id)
	{
		$newFlags = $mailbox->FlagMail((int)$_REQUEST['flag'],
			$_REQUEST['value']==1 ? true : false,
			$id);
		$results[] = ($newFlags & (int)$_REQUEST['flag']) ? '1' : '0';
	}
	echo(implode(',', $results));

	if(isset($_REQUEST['getFolderList']))
	{
		echo ',';
		$tpl->display('li/email.folderlist.tpl');
	}

	exit();
}

/**
 * rpc color
 */
else if($_REQUEST['action'] == 'colorMessage'
		&& isset($_REQUEST['id'])
		&& isset($_REQUEST['color']))
{
	$mailbox->ColorMail((int)$_REQUEST['id'],
						(int)$_REQUEST['color']);
	die((int)$_REQUEST['id'] . ',' . (int)$_REQUEST['color']);
}

/**
 * rpc recent mail count
 */
else if($_REQUEST['action'] == 'getRecentMailCount')
{
	die($mailbox->GetRecentMailCount(isset($_REQUEST['folder'])
		? (int)$_REQUEST['folder']
		: FOLDER_ROOT));
}

/**
 * rpc get folder list
 */
else if($_REQUEST['action'] == 'getFolderList')
{
	$tpl->display('li/email.folderlist.tpl');
}

/**
 * rpc set preview position
 */
else if($_REQUEST['action'] == 'setPreviewPosition' && isset($_REQUEST['pos']))
{
	$thisUser->SetPref('previewPosition', $_REQUEST['pos']);
	die('1');
}
?>