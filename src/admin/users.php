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
include('../serverlib/mailbox.class.php');
include('../serverlib/payment.class.php');
RequestPrivileges(PRIVILEGES_ADMIN);
AdminRequirePrivilege('users');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'users';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['users'],
		'relIcon'	=> 'ico_users.png',
		'link'		=> 'users.php?',
		'active'	=> $_REQUEST['action'] == 'users'
	),
	1 => array(
		'title'		=> $lang_admin['search'],
		'relIcon'	=> 'user_search.png',
		'link'		=> 'users.php?action=search&',
		'active'	=> $_REQUEST['action'] == 'search'
	),
	2 => array(
		'title'		=> $lang_admin['create'],
		'relIcon'	=> 'ico_users.png',
		'link'		=> 'users.php?action=create&',
		'active'	=> $_REQUEST['action'] == 'create'
	)
);

/**
 * users
 */
if($_REQUEST['action'] == 'users')
{
	if(!isset($_REQUEST['do']))
		$_REQUEST['do'] = 'list';

	//
	// list
	//
	if($_REQUEST['do'] == 'list')
	{
		// single action?
		if(isset($_REQUEST['singleAction']))
		{
			if($_REQUEST['singleAction'] == 'lock')
			{
				$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id=?',
					'yes',
					$_REQUEST['singleID']);
			}
			else if($_REQUEST['singleAction'] == 'unlock'
						|| $_REQUEST['singleAction'] == 'activate'
						|| $_REQUEST['singleAction'] == 'recover')
			{
				$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id=?',
					'no',
					$_REQUEST['singleID']);
			}
			else if($_REQUEST['singleAction'] == 'delete')
			{
				$res = $db->Query('SELECT gesperrt FROM {pre}users WHERE id=?',
					$_REQUEST['singleID']);
				list($userStatus) = $res->FetchArray(MYSQLI_NUM);
				$res->Free();

				if($userStatus != 'delete')
				{
					$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id=?',
						'delete',
						$_REQUEST['singleID']);
				}
				else
				{
					DeleteUser((int)$_REQUEST['singleID']);
				}
			}
			else if($_REQUEST['singleAction'] == 'emptyTrash')
			{
				// get user info
				$userObject = _new('BMUser', array($_REQUEST['singleID']));
				$userRow = $userObject->Fetch();
				$userMail = $userRow['email'];

				// open mailbox
				$mailbox = _new('BMMailbox', array($_REQUEST['singleID'], $userMail, $userObject));

				// empty trash
				$deletedMails = $mailbox->EmptyFolder(FOLDER_TRASH);
			}
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get user IDs
			$userIDs = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 5) == 'user_')
					$userIDs[] = (int)substr($key, 5);

			if(count($userIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					// get states
					$markIDs = $deleteIDs = array();
					$res = $db->Query('SELECT id,gesperrt FROM {pre}users WHERE id IN(' . implode(',', $userIDs) . ')');
					while($row = $res->FetchArray(MYSQLI_ASSOC))
						if($row['gesperrt'] == 'delete')
							$deleteIDs[] = $row['id'];
						else
							$markIDs[] = $row['id'];

					// mark users
					if(count($markIDs) > 0)
						$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id IN(' . implode(',', $markIDs) . ')',
							'delete');

					// delete users
					foreach($deleteIDs as $userID)
						DeleteUser($userID);
				}
				else if($_REQUEST['massAction'] == 'restore'
						|| $_REQUEST['massAction'] == 'unlock')
				{
					$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id IN(' . implode(',', $userIDs) . ')',
						'no');
				}
				else if($_REQUEST['massAction'] == 'lock')
				{
					$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id IN(' . implode(',', $userIDs) . ')',
						'yes');
				}
				else if(substr($_REQUEST['massAction'], 0, 7) == 'moveto_')
				{
					$groupID = (int)substr($_REQUEST['massAction'], 7);
					$db->Query('UPDATE {pre}users SET gruppe=? WHERE id IN(' . implode(',', $userIDs) . ')',
						$groupID);
				}
			}
		}

		// sort options
		$sortBy = isset($_REQUEST['sortBy'])
					? $_REQUEST['sortBy']
					: 'email';
		$sortOrder = isset($_REQUEST['sortOrder'])
						? strtolower($_REQUEST['sortOrder'])
						: 'asc';
		$perPage = max(1, isset($_REQUEST['perPage'])
						? (int)$_REQUEST['perPage']
						: 50);

		// filter options
		$statusRegistered = $statusActive = $statusLocked = $statusNotActivated = $statusDeleted = true;
		if(isset($_REQUEST['filter']))
		{
			$statusRegistered = isset($_REQUEST['statusRegistered']);
			$statusActive = isset($_REQUEST['statusActive']);
			$statusLocked = isset($_REQUEST['statusLocked']);
			$statusNotActivated = isset($_REQUEST['statusNotActivated']);
			$statusDeleted = isset($_REQUEST['statusDeleted']);
		}
		$groups = BMGroup::GetSimpleGroupList();

		// profile fields
		$fields = array();
		$res = $db->Query("SELECT id,feld,typ FROM {pre}profilfelder");
		while($row = $res->FetchArray())
		{
			$row['checked'] = isset($_REQUEST['field_'.$row['id']]);
			$fields[$row['id']] = $row;
		}
		$res->Free();

		// query stuff
		$groupIDs = array();
		foreach($groups as $groupID=>$groupInfo)
		{
			$groups[$groupID]['checked'] = ((!isset($_REQUEST['filter']) && !isset($_REQUEST['onlyGroup']))
											|| isset($_REQUEST['group_'.$groupID]))
											|| (isset($_REQUEST['onlyGroup']) && $_REQUEST['onlyGroup'] == $groupID)
											|| isset($_REQUEST['allGroups']);
			if($groups[$groupID]['checked'])
				$groupIDs[] = $groupID;
		}
		$lockedValues = array();
		if($statusActive) 						$lockedValues[] = '\'no\'';
		if($statusLocked) 						$lockedValues[] = '\'yes\'';
		if($statusNotActivated) 				$lockedValues[] = '\'locked\'';
		if($statusDeleted) 						$lockedValues[] = '\'delete\'';
		$queryGroups = count($groupIDs) > 0 ? implode(',', $groupIDs) : '0';
		$queryLocked = count($lockedValues) > 0 ? implode(',', $lockedValues) : '0';
		$theQuery = 'FROM {pre}users WHERE ('
					. 'gruppe IN(' . $queryGroups . ') AND '
					. '(gesperrt IN (' . $queryLocked . ')';
		if($statusRegistered)
			$theQuery .= ' OR (lastlogin=0 AND gesperrt=\'no\')';
		else
			$theQuery .= ' AND (lastlogin>0 OR gesperrt!=\'no\')';
		$theQuery .= '))';

		// search?
		if(isset($_REQUEST['query']))
		{
			$query = json_decode($_REQUEST['query']);

			if(is_array($query) && count($query) == 2
				&& is_array($query[1]) && is_string($query[0])
				&& count($query[1]) > 0)
			{
				list($queryString, $queryFields) = $query;

				// query suffix
				$theQuery .= sprintf(' AND (CAST(CONCAT(`%s`) AS CHAR) LIKE \'%%%s%%\'',
					implode('`,\' \',`', $queryFields),
					$db->Escape($queryString));

				// alias search?
				if(in_array('email', $queryFields))
				{
					$aliasUserIDs = array();

					$res = $db->Query('SELECT `user` FROM {pre}aliase WHERE `email` LIKE \'%' . $db->Escape($queryString) . '%\'');
					while($row = $res->FetchArray(MYSQLI_ASSOC))
					{
						if(!in_array($row['user'], $aliasUserIDs))
							$aliasUserIDs[] = $row['user'];
					}
					$res->Free();

					if(count($aliasUserIDs) > 0)
						$theQuery .= ' OR `id` IN (' . implode(',', $aliasUserIDs) . ')';
				}

				$theQuery .= ')';

				// template stuff
				$tabs[0]['active'] = false;
				$tabs[1]['active'] = true;
				$tpl->assign('searchQuery', $queryString);
			}
		}

		// page calculation
		$res = $db->Query('SELECT COUNT(*) ' . $theQuery);
		list($userCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		$pageCount = ceil($userCount / $perPage);
		$pageNo = isset($_REQUEST['page'])
					? max(1, min($pageCount, (int)$_REQUEST['page']))
					: 1;
		$startPos = max(0, min($perPage*($pageNo-1), $userCount));

		// do the query!
		$users = array();
		$res = $db->Query('SELECT id,email,vorname,nachname,strasse,hnr,plz,ort,gruppe,gesperrt,lastlogin,profilfelder,absendername ' . $theQuery . ' '
					. 'ORDER BY ' . $sortBy . ' '
					. $sortOrder . ' '
					. 'LIMIT ' . $startPos . ',' . $perPage);
		while($row = $res->FetchArray())
		{
			$aliases = array();
			$aliasRes = $db->Query('SELECT email FROM {pre}aliase WHERE type=? AND user=? ORDER BY email ASC',
				ALIAS_RECIPIENT|ALIAS_SENDER,
				$row['id']);
			while($aliasRow = $aliasRes->FetchArray())
				$aliases[] = DecodeSingleEMail($aliasRow['email']);
			$aliasRes->Free();

			$row['groupName'] 	= isset($groups[$row['gruppe']])
									? $groups[$row['gruppe']]['title']
									: $lang_admin['missing'];
			$row['aliases'] 	= count($aliases) > 0
									? implode(', ', $aliases)
									: '';

			if($row['lastlogin'] == 0 && $row['gesperrt'] == 'no')
			{
				$row['status'] 		= $statusTable['registered'];
				$row['statusImg'] 	= $statusImgTable['registered'];
			}
			else
			{
				$row['status'] 		= $statusTable[$row['gesperrt']];
				$row['statusImg'] 	= $statusImgTable[$row['gesperrt']];
			}

			$profileData = @unserialize($row['profilfelder']);
			if(!is_array($profileData))
				$profileData = array();
			$row['profileData'] = $profileData;

			$users[$row['id']] 	= $row;
		}
		$res->Free();

		// assign
		$tpl->assign('users', $users);
		$tpl->assign('fields', $fields);
		$tpl->assign('pageNo', $pageNo);
		$tpl->assign('pageCount', $pageCount);
		$tpl->assign('sortBy', $sortBy);
		$tpl->assign('sortOrder', $sortOrder);
		$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
		$tpl->assign('statusRegistered', $statusRegistered);
		$tpl->assign('statusActive', $statusActive);
		$tpl->assign('statusLocked', $statusLocked);
		$tpl->assign('statusNotActivated', $statusNotActivated);
		$tpl->assign('statusDeleted', $statusDeleted);
		$tpl->assign('queryString', isset($_REQUEST['query']) ? $_REQUEST['query'] : '');
		$tpl->assign('groups', $groups);
		$tpl->assign('perPage', $perPage);
		$tpl->assign('page', 'users.list.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit')
	{
		// save?
		if(isset($_REQUEST['save']) && isset($_POST['email']))
		{
			// prepare aliases
			$saliaseArray = explode("\n", $_REQUEST['saliase']);
			foreach($saliaseArray as $key=>$val)
				if(($val = trim($val)) != '')
					$saliaseArray[$key] = EncodeDomain($val);
				else
					unset($saliaseArray[$key]);
			$saliase = implode(':', $saliaseArray);

			// profile fields
			$profileData = array();
			$res = $db->Query('SELECT id,typ FROM {pre}profilfelder ORDER BY id ASC');
			while($row = $res->FetchArray())
			{
				if($row['typ'] == FIELD_DATE)
				{
					$profileData[$row['id']] = sprintf('%04d-%02d-%02d',
						$_POST['field_'.$row['id'].'Year'],
						$_POST['field_'.$row['id'].'Month'],
						$_POST['field_'.$row['id'].'Day']);
				}
				else
				{
					$profileData[$row['id']] = $row['typ'] == FIELD_CHECKBOX
												? isset($_REQUEST['field_'.$row['id']])
												: (isset($_REQUEST['field_'.$row['id']])
													? $_REQUEST['field_'.$row['id']]
													: false);
				}
			}
			$res->Free();

			// update common stuff
			$db->Query('UPDATE {pre}users SET profilfelder=?, email=?, vorname=?, nachname=?, company=?, strasse=?, hnr=?, plz=?, ort=?, land=?, tel=?, fax=?, taxid=?, mail2sms_nummer=?, altmail=?, gruppe=?, gesperrt=?, notes=?, re=?, fwd=?, mail2sms=?, forward=?, forward_to=?, `newsletter_optin`=?, datumsformat=?, absendername=?, anrede=?, saliase=?, mailspace_add=?, diskspace_add=?, traffic_add=? WHERE id=?',
				serialize($profileData),
				EncodeEMail($_REQUEST['email']),
				$_REQUEST['vorname'],
				$_REQUEST['nachname'],
				$_REQUEST['company'],
				$_REQUEST['strasse'],
				$_REQUEST['hnr'],
				$_REQUEST['plz'],
				$_REQUEST['ort'],
				$_REQUEST['land'],
				$_REQUEST['tel'],
				$_REQUEST['fax'],
				$_REQUEST['taxid'],
				$_REQUEST['mail2sms_nummer'],
				EncodeEMail($_REQUEST['altmail']),
				$_REQUEST['gruppe'],
				$_REQUEST['gesperrt'],
				$_REQUEST['notes'],
				$_REQUEST['re'],
				$_REQUEST['fwd'],
				$_REQUEST['mail2sms'],
				$_REQUEST['forward'],
				EncodeEMail($_REQUEST['forward_to']),
				$_REQUEST['newsletter_optin'],
				$_REQUEST['datumsformat'],
				$_REQUEST['absendername'],
				$_REQUEST['anrede'],
				$saliase,
				$_REQUEST['mailspace_add']*1024*1024,
				$_REQUEST['diskspace_add']*1024*1024,
				$_REQUEST['traffic_add']*1024*1024,
				$_REQUEST['id']);

			// update password?
			if(isset($_REQUEST['passwort']) && strlen(trim($_REQUEST['passwort'])) > 0)
			{
				$salt = GenerateRandomSalt(8);
				$db->Query('UPDATE {pre}users SET passwort=?,passwort_salt=? WHERE id=?',
					md5(md5(CharsetDecode($_REQUEST['passwort'], false, 'ISO-8859-15')) . $salt),
					$salt,
					$_REQUEST['id']);
			}
		}

		// move?
		if(isset($_REQUEST['moveToGroup']))
		{
			$db->Query('UPDATE {pre}users SET gruppe=? WHERE id=?',
				(int)$_REQUEST['moveToGroup'],
				(int)$_REQUEST['id']);
		}

		// delete alias?
		if(isset($_REQUEST['deleteAlias']))
		{
			$db->Query('DELETE FROM {pre}aliase WHERE id=? AND user=?',
				(int)$_REQUEST['deleteAlias'],
				(int)$_REQUEST['id']);
			$tpl->assign('showAliases', true);
		}

		// delete payment?
		if(isset($_REQUEST['deletePayment']))
		{
			$db->Query('DELETE FROM {pre}orders WHERE `orderid`=?',
				(int)$_REQUEST['deletePayment']);
			$db->Query('DELETE FROM {pre}invoices WHERE `orderid`=?',
				(int)$_REQUEST['deletePayment']);
			$tpl->assign('showPayments', true);
		}

		// activate payment?
		if(isset($_REQUEST['activatePayment']))
		{
			$res = $db->Query('SELECT `orderid`,`amount` FROM {pre}orders WHERE `orderid`=? AND `status`=?',
							  (int)$_REQUEST['activatePayment'],
							  ORDER_STATUS_CREATED);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				BMPayment::ActivateOrder($row['orderid'], $row['amount']);
			$res->Free();
			$tpl->assign('showPayments', true);
		}

		// get user data
		$userObject = _new('BMUser', array((int)$_REQUEST['id']));
		$userRow = $user = $userObject->Fetch();
		$userMailbox = _new('BMMailbox', array($userRow['id'], $userRow['email'], $userObject));

		// re-send validation sms
		if(isset($_REQUEST['resendValidationSMS']))
		{
			if(!class_exists('BMSMS'))
				include(B1GMAIL_DIR . 'serverlib/sms.class.php');

			$smsText = GetPhraseForUser($userRow['id'], 'lang_custom', 'validationsms');
			$smsText = str_replace('%%code%%', $userRow['sms_validation_code'], $smsText);

			$sms = _new('BMSMS', array(0, false));
			$sms->Send($bm_prefs['mail2sms_abs'], preg_replace('/[^0-9]/', '', str_replace('+', '00', $userRow['mail2sms_nummer'])), $smsText, $bm_prefs['smsvalidation_type'], false, false);

			$tpl->assign('msg', $lang_admin['val_code_resent']);
		}

		// re-send validation email
		if(isset($_REQUEST['resendValidationEmail']))
		{
			$vars = array(
				'activationcode' 	=> $userRow['sms_validation_code'],
				'email'				=> DecodeEMail($userRow['email']),
				'url'				=> sprintf('%sindex.php?action=activateAccount&id=%d&code=%s',
											$bm_prefs['selfurl'],
											$userRow['id'],
											$userRow['sms_validation_code'])
			);

			SystemMail($bm_prefs['passmail_abs'],
				$userRow['altmail'],
				$lang_custom['activationmail_sub'],
				'activationmail_text',
			    $vars,
				$userRow['id']);

			$tpl->assign('msg', $lang_admin['val_code_resent']);
		}

		// aliases
		$aliases = $userObject->GetAliases();
		foreach($aliases as $key=>$val)
			$aliases[$key]['type'] = $aliasTypeTable[$val['type']];

		// get group data
		$groupObject = $userObject->GetGroup();
		$group = $groupObject->Fetch();

		// used month sms
		$usedMonthSMS = $userObject->GetUsedMonthSMS();

		// traffic?
		if($user['traffic_status'] != (int)date('m'))
			$user['traffic_down'] = $user['traffic_up'] = 0;

		// get usage stuff
		$res = $db->Query('SELECT COUNT(*) FROM {pre}mails WHERE userid=?',
			$user['id']);
		list($emailMails) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		$res = $db->Query('SELECT COUNT(*) FROM {pre}folders WHERE userid=?',
			$user['id']);
		list($emailFolders) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		$res = $db->Query('SELECT COUNT(*) FROM {pre}diskfiles WHERE user=?',
			$user['id']);
		list($diskFiles) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		$res = $db->Query('SELECT COUNT(*) FROM {pre}diskfolders WHERE user=?',
			$user['id']);
		list($diskFolders) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// abuse protect
		if($group['abuseprotect'] == 'yes')
		{
			$res = $db->Query('SELECT SUM(`points`) FROM {pre}abuse_points WHERE `userid`=? AND `expired`=0',
				$user['id']);
			list($abusePoints) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			$abusePoints = (int)$abusePoints;

			if($abusePoints >= $bm_prefs['ap_hard_limit'])
				$abuseIndicator = 'red';
			else if($abusePoints >= $bm_prefs['ap_medium_limit'])
				$abuseIndicator = 'yellow';
		 	else
		 		$abuseIndicator = 'green';
		}
		else
		{
			$abusePoints = '-';
			$abuseIndicator = 'grey';
		}

		// profile fields
		$profileFields = array();
		$profileData = array();
		if(strlen($user['profilfelder']) > 2)
		{
			$profileData = @unserialize($user['profilfelder']);
			if(!is_array($profileData))
				$profileData = array();
		}
		$res = $db->Query('SELECT id,typ,feld,extra FROM {pre}profilfelder ORDER BY feld ASC');
		while($row = $res->FetchArray())
			$profileFields[] = array(
				'id'		=> $row['id'],
				'title'		=> $row['feld'],
				'type'		=> $row['typ'],
				'extra'		=> explode(',', $row['extra']),
				'value'		=> isset($profileData[$row['id']]) ? $profileData[$row['id']] : false
			);
		$res->Free();

		// history?
		$historyCount = 0;
		if(trim($user['contactHistory']) != '')
		{
			$contactHistory = @unserialize($user['contactHistory']);
			if(is_array($contactHistory))
				$historyCount = count($contactHistory);
		}

		$user['saliase'] = implode("\n", array_map('DecodeDomain', explode(':', $user['saliase'])));

		// payments
		$payMethods = BMPayment::GetCustomPaymentMethods();
		$payments = array();
		$res = $db->Query('SELECT * FROM {pre}orders WHERE `userid`=? ORDER BY `orderid` DESC LIMIT 15', $_REQUEST['id']);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$res2 = $db->Query('SELECT COUNT(*) FROM {pre}invoices WHERE `orderid`=?',
							   $row['orderid']);
			list($row['hasInvoice']) = $res2->FetchArray(MYSQLI_NUM);
			$res2->Free();

			if($row['paymethod'] == PAYMENT_METHOD_BANKTRANSFER)
				$row['method'] = $lang_admin['banktransfer'];
			else if($row['paymethod'] == PAYMENT_METHOD_PAYPAL)
				$row['method'] = $lang_admin['paypal'];
			else if($row['paymethod'] == PAYMENT_METHOD_SOFORTUEBERWEISUNG)
				$row['method'] = $lang_admin['su'];
			else if($row['paymethod'] == PAYMENT_METHOD_SKRILL)
				$row['method'] = $lang_admin['skrill'];
			else if($row['paymethod'] < 0)
			{
				if(isset($payMethods[abs($row['paymethod'])]))
					$row['method'] = $payMethods[abs($row['paymethod'])]['title'];
				else
					$row['method'] = $lang_admin['unknown'];
			}

			$row['customerNo'] = BMPayment::CustomerNo($row['userid']);
			$row['invoiceNo'] = BMPayment::InvoiceNo($row['orderid']);
			$row['amount'] = sprintf('%.02f %s', $row['amount']/100, $bm_prefs['currency']);

			$payments[] = $row;
		}
		$res->Free();

		// assign
		$tpl->assign('staticBalance',	$userObject->GetStaticBalance());
		$tpl->assign('payments',		$payments);
		$tpl->assign('abusePoints',		$abusePoints);
		$tpl->assign('abuseIndicator',	$abuseIndicator);
		$tpl->assign('regValidation',	$bm_prefs['reg_validation']);
		$tpl->assign('historyCount',	$historyCount);
		$tpl->assign('user', 			$user);
		$tpl->assign('group', 			$group);
		$tpl->assign('groups', 			BMGroup::GetSimpleGroupList());
		$tpl->assign('aliases', 		$aliases);
		$tpl->assign('usedMonthSMS', 	(int)$usedMonthSMS);
		$tpl->assign('countries', 		CountryList());
		$tpl->assign('emailMails', 		$emailMails);
		$tpl->assign('emailFolders', 	$emailFolders);
		$tpl->assign('profileFields', 	$profileFields);
		$tpl->assign('diskFiles',		$diskFiles);
		$tpl->assign('diskFolders', 	$diskFolders);
		$tpl->assign('page', 			'users.edit.tpl');
	}

	//
	// transaction history
	//
	else if($_REQUEST['do'] == 'transactions'
			&& isset($_REQUEST['id']))
	{
		// get user data
		$userObject = _new('BMUser', array((int)$_REQUEST['id']));
		$user = $userObject->Fetch();
		if(!$user)
			die('User not found');

		// single action?
		if(isset($_REQUEST['singleAction']))
		{
			if($_REQUEST['singleAction'] == 'delete')
			{
				$db->Query('DELETE FROM {pre}transactions WHERE `userid`=? AND `transactionid`=?',
					$userObject->_id, $_REQUEST['singleID']);
			}
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get transaction IDs
			$transactionIDs = array();
			if(isset($_POST['transactions']) && is_array($_POST['transactions']))
				$transactionIDs = array_map('intval', $_POST['transactions']);

			if(count($transactionIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					$db->Query('DELETE FROM {pre}transactions WHERE `transactionid` IN ? AND `userid`=?',
						$transactionIDs, $userObject->_id);
				}
				else if($_REQUEST['massAction'] == 'cancel')
				{
					$db->Query('UPDATE {pre}transactions SET `status`=? WHERE `transactionid` IN ? AND `userid`=?',
						TRANSACTION_CANCELLED,
						$transactionIDs, $userObject->_id);
				}
				else if($_REQUEST['massAction'] == 'uncancel')
				{
					$db->Query('UPDATE {pre}transactions SET `status`=? WHERE `transactionid` IN ? AND `userid`=?',
						TRANSACTION_BOOKED,
						$transactionIDs, $userObject->_id);
				}
			}
		}

		// add transaction?
		if(isset($_REQUEST['add']) && isset($_POST['amount']))
		{
			$db->Query('INSERT INTO {pre}transactions(`userid`,`description`,`amount`,`date`,`status`) '
				. 'VALUES(?,?,?,?,?)',
				$userObject->_id,
				$_POST['description'],
				(int)$_POST['amount'],
				time(),
				(int)$_POST['status']);
		}

		// sort options
		$sortBy = isset($_REQUEST['sortBy'])
					? $_REQUEST['sortBy']
					: 'date';
		$sortOrder = isset($_REQUEST['sortOrder'])
						? strtolower($_REQUEST['sortOrder'])
						: 'desc';
		$perPage = max(1, isset($_REQUEST['perPage'])
						? (int)$_REQUEST['perPage']
						: 50);

		// page calculation
		$res = $db->Query('SELECT COUNT(*) FROM {pre}transactions WHERE `userid`=?',
			$userObject->_id);
		list($transactionCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		$pageCount = ceil($transactionCount / $perPage);
		$pageNo = isset($_REQUEST['page'])
					? max(1, min($pageCount, (int)$_REQUEST['page']))
					: 1;
		$startPos = max(0, min($perPage*($pageNo-1), $transactionCount));

		// get transactions
		$transactions = array();
		$res = $db->Query('SELECT * FROM {pre}transactions WHERE `userid`=? '
					. 'ORDER BY ' . $sortBy . ' '
					. $sortOrder . ' '
					. 'LIMIT ' . $startPos . ',' . $perPage,
					$userObject->_id);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if(strlen($row['description']) > 5 && substr($row['description'], 0, 5) == 'lang:'
				&& isset($lang_user[substr($row['description'], 5)]))
				$row['description'] = $lang_user[substr($row['description'], 5)];
			$transactions[$row['transactionid']] = $row;
		}
		$res->Free();

		// assign
		$tpl->assign('staticBalance',	$userObject->GetStaticBalance());
		$tpl->assign('transactions',	$transactions);
		$tpl->assign('user', 			$user);
		$tpl->assign('pageNo', 			$pageNo);
		$tpl->assign('pageCount', 		$pageCount);
		$tpl->assign('sortBy', 			$sortBy);
		$tpl->assign('sortOrder', 		$sortOrder);
		$tpl->assign('sortOrderInv', 	$sortOrder == 'asc' ? 'desc' : 'asc');
		$tpl->assign('perPage', 		$perPage);
		$tpl->assign('page', 			'users.transactions.tpl');
	}

	//
	// edit transactiopn
	//
	else if($_REQUEST['do'] == 'editTransaction'
			&& isset($_REQUEST['transactionid']))
	{
		// get transaction
		$res = $db->Query('SELECT * FROM {pre}transactions WHERE `transactionid`=?',
			$_REQUEST['transactionid']);
		if($res->RowCount() != 1)
			die('Transaction not found');
		$tx = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// save?
		if(isset($_REQUEST['save']) && isset($_POST['amount']))
		{
			$db->Query('UPDATE {pre}transactions SET `description`=?,`amount`=?,`status`=? WHERE `transactionid`=?',
				$_POST['description'],
				(int)$_POST['amount'],
				(int)$_POST['status'],
				(int)$_REQUEST['transactionid']);
			header('Location: users.php?do=transactions&id='.$tx['userid'].'&sid='.session_id());
			exit();
		}

		// get user data
		$userObject = _new('BMUser', array($tx['userid']));
		$user = $userObject->Fetch();
		if(!$user)
			die('User not found');

		// assign
		$tpl->assign('tx', 				$tx);
		$tpl->assign('user', 			$user);
		$tpl->assign('page', 			'users.transactions.edit.tpl');
	}

	//
	// contact history
	//
	else if($_REQUEST['do'] == 'contactHistory'
			&& isset($_REQUEST['id']))
	{
		// get user data
		$userObject = _new('BMUser', array((int)$_REQUEST['id']));
		$user = $userObject->Fetch();
		$history = @unserialize($user['contactHistory']);
		if(!is_array($history))
			$history = array();
		$history[] = $user;
		$history = array_reverse($history);

		// assign
		$tpl->assign('countries',		CountryList());
		$tpl->assign('history',			$history);
		$tpl->assign('user', 			$user);
		$tpl->assign('page', 			'users.contacthistory.tpl');
	}

	//
	// clear contact history
	//
	else if($_REQUEST['do'] == 'clearHistory'
			&& isset($_REQUEST['id']))
	{
		$db->Query('UPDATE {pre}users SET contactHistory=? WHERE id=?',
			'',
			(int)$_REQUEST['id']);
		header('Location: users.php?do=edit&id=' . $_REQUEST['id'] . '&sid=' . session_id());
		exit();
	}

	//
	// login
	//
	else if($_REQUEST['do'] == 'login')
	{
		$userObject = _new('BMUser', array((int)$_REQUEST['id']));
		$userRow = $userObject->Fetch();

		// log this
		PutLog(sprintf('Admin logs in as user <%s> (%d) from <%s>',
				$userRow['email'],
				$userRow['id'],
				$_SERVER['REMOTE_ADDR']),
			PRIO_NOTE,
			__FILE__,
			__LINE__);
		$adminAuth = sprintf('%d,%d', $userRow['id'], $adminRow['adminid']);
		$adminAuth .= ',' . md5($adminAuth.$_SESSION['bm_adminAuth']);

		// create new session
		header(sprintf('Location: ../index.php?do=login&email_full=%s&adminAuth=%s',
			urlencode($userRow['email']),
			urlencode(base64_encode($adminAuth))));
		exit();
	}
}

/**
 * search
 */
else if($_REQUEST['action'] == 'search')
{
	// display form
	if(!isset($_REQUEST['do']))
	{
		// assign
		$tpl->assign('page', 'users.search.tpl');
	}

	// build search URL and redirect
	else if($_REQUEST['do'] == 'search')
	{
		// check params
		if(!isset($_REQUEST['searchIn'])
			|| !is_array($_REQUEST['searchIn'])
			|| strlen(trim($_REQUEST['q'])) < 1)
		{
			header('Location: users.php?action=search&sid=' . session_id());
			exit();
		}

		// collect fields
		$fields = array();
		foreach($_REQUEST['searchIn'] as $field=>$val)
		{
			if($field == 'id')
				$fields[] = 'id';
			else if($field == 'email')
				$fields[] = 'email';
			else if($field == 'altmail')
				$fields[] = 'altmail';
			else if($field == 'name')
				$fields = array_merge($fields, array('vorname', 'nachname'));
			else if($field == 'address')
				$fields = array_merge($fields, array('strasse', 'hnr', 'plz', 'ort', 'land'));
			else if($field == 'telfaxmobile')
				$fields = array_merge($fields, array('tel', 'fax', 'mail2sms_nummer'));
			else if($field == 'absendername')
                $fields[] = 'absendername';
		}

		// build query string
		$queryString = json_encode(array(trim($_REQUEST['q']), $fields));
		header('Location: users.php?query=' . urlencode($queryString) . '&sid='  . session_id());
		exit();
	}
}

/**
 * create user
 */
else if($_REQUEST['action'] == 'create')
{
	// create user
	if(isset($_REQUEST['create']))
	{
		$msgIcon = 'error32';
		$msgText = '?';

		// check address syntax
		$email = trim($_REQUEST['email']) . '@' . $_REQUEST['emailDomain'];
		if(BMUser::AddressValid($email))
		{
			// check address availability
			if(BMUser::AddressAvailable($email))
			{
				// profile fields
				$profileData = array();
				$res = $db->Query('SELECT id,typ FROM {pre}profilfelder ORDER BY id ASC');
				while($row = $res->FetchArray())
				{
					if($row['typ'] == FIELD_DATE)
					{
						$profileData[$row['id']] = sprintf('%04d-%02d-%02d',
							$_POST['field_'.$row['id'].'Year'],
							$_POST['field_'.$row['id'].'Month'],
							$_POST['field_'.$row['id'].'Day']);
					}
					else
					{
						$profileData[$row['id']] = $row['typ'] == FIELD_CHECKBOX
													? isset($_REQUEST['field_'.$row['id']])
													: (isset($_REQUEST['field_'.$row['id']])
														? $_REQUEST['field_'.$row['id']]
														: false);
					}
				}
				$res->Free();

				// create account
				$userID = BMUser::CreateAccount($email,
					$_REQUEST['vorname'],
					$_REQUEST['nachname'],
					$_REQUEST['strasse'],
					$_REQUEST['hnr'],
					$_REQUEST['plz'],
					$_REQUEST['ort'],
					$_REQUEST['land'],
					$_REQUEST['tel'],
					$_REQUEST['fax'],
					$_REQUEST['altmail'],
					$_REQUEST['mail2sms_nummer'],
					$_REQUEST['passwort'],
					$profileData,
					false,
					'',
					$_REQUEST['anrede']);

				// update misc stuff
				$db->Query('UPDATE {pre}users SET mail2sms_nummer=?, gruppe=?, gesperrt=?, notes=? WHERE id=?',
					$_REQUEST['mail2sms_nummer'],
					$_REQUEST['gruppe'],
					$_REQUEST['gesperrt'],
					$_REQUEST['notes'],
					$userID);

				$msgIcon = 'info32';
				$msgText = sprintf($lang_admin['accountcreated'], $userID, session_id());
				$tpl->assign('backLink',		'users.php?action=create&');
			}
			else
			{
				$msgText = $lang_admin['addresstaken'];
				$msgIcon = 'error32';
			}
		}
		else
		{
			$msgText = $lang_admin['addressinvalid'];
			$msgIcon = 'error32';
		}

		// assign
		$tpl->assign('msgTitle',		$lang_admin['create']);
		$tpl->assign('msgText',			$msgText);
		$tpl->assign('msgIcon',			$msgIcon);
		$tpl->assign('page',			'msg.tpl');
	}

	// display form
	else
	{
		// profile fields
		$profileFields = array();
		$res = $db->Query('SELECT id,typ,feld,extra FROM {pre}profilfelder ORDER BY feld ASC');
		while($row = $res->FetchArray())
			$profileFields[] = array(
				'id'		=> $row['id'],
				'title'		=> $row['feld'],
				'type'		=> $row['typ'],
				'extra'		=> explode(',', $row['extra'])
			);
		$res->Free();

		// assign
		$tpl->assign('profileFields',	$profileFields);
		$tpl->assign('groups',			BMGroup::GetSimpleGroupList());
		$tpl->assign('defaultGroup',	$bm_prefs['std_gruppe']);
		$tpl->assign('countries',		CountryList());
		$tpl->assign('defaultCountry',	$bm_prefs['std_land']);
		$tpl->assign('domainList', 		GetDomainList());
		$tpl->assign('page', 			'users.create.tpl');
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['usersgroups'] . ' &raquo; ' . $lang_admin['users']);
$tpl->display('page.tpl');