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

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'account';

$tabs = array(
	0 => array(
			'title'		=> $lang_admin['account'],
			'relIcon'	=> 'user_active32.png',
			'link'		=> 'admins.php?',
			'active'	=> $_REQUEST['action'] == 'account'
	),
	1 => array(
			'title'		=> $lang_admin['admins'],
			'relIcon'	=> 'ico_users.png',
			'link'		=> 'admins.php?action=admins&',
			'active'	=> $_REQUEST['action'] == 'admins'
	)
);

if($adminRow['type'] != 0)
	unset($tabs[1]);

/**
 * accounts
 */
if($_REQUEST['action'] == 'account')
{
	$displayPage = true;

	EnsureAdminEmailColumn();

	if(isset($_REQUEST['saveEmail']) && isset($_POST['email']))
	{
		CsrfEnforceOnPost();
		$email = trim((string)$_POST['email']);
		if($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false)
		{
			$tpl->assign('emailError', $lang_admin['addressinvalid']);
		}
		else
		{
			$db->Query('UPDATE {pre}admins SET `email`=? WHERE `adminid`=?',
				$email,
				(int)$adminRow['adminid']);
			$adminRow['email'] = $email;
			$tpl->assign('emailSaved', true);
		}
	}

	if(IsPOSTRequest() && !empty($_POST['avatarUpload']))
	{
		CsrfEnforceOnPost();
		$errKey = AvatarHandleUpload('admin', (int)$adminRow['adminid'], isset($_FILES['avatar_file']) ? $_FILES['avatar_file'] : null);
		if($errKey === '')
		{
			AvatarSetStoredSource('admin', (int)$adminRow['adminid'], 'upload');
			$tpl->assign('avatarSaved', true);
		}
		else
			$tpl->assign('avatarError', isset($lang_admin[$errKey]) ? $lang_admin[$errKey] : $lang_admin['error']);
	}
	else if(IsPOSTRequest() && !empty($_POST['avatarDelete']))
	{
		CsrfEnforceOnPost();
		AvatarDeleteCustom('admin', (int)$adminRow['adminid']);
		AvatarSetStoredSource('admin', (int)$adminRow['adminid'], 'initials');
		$tpl->assign('avatarDeleted', true);
	}

	if(isset($_REQUEST['changePassword']) && isset($_POST['newpw1']))
	{
		if(strlen($_POST['newpw1']) < 6 || $_POST['newpw1'] != $_POST['newpw2'])
		{
			$tpl->assign('msgTitle', $lang_admin['error']);
			$tpl->assign('msgText', $lang_admin['pwerror']);
			$tpl->assign('msgIcon', 'error32');
			$tpl->assign('page', 'msg.tpl');
			$displayPage = false;
		}
		else
		{
			$newPW = PasswordHashSetAdminPassword((int)$adminRow['adminid'], $_POST['newpw1']);
			$_SESSION['bm_adminAuth'] = AdminSessionAuthBind($newPW, (int)$adminRow['adminid']);
		}
	}

	if($displayPage)
	{
		$adminAvatarSource = AvatarGetAdminSource((int)$adminRow['adminid']);
		$tpl->assign('adminAvatarSource', $adminAvatarSource);
		$tpl->assign('adminAvatarDisplayMode', AvatarResolveDisplayMode($adminAvatarSource, 'admin', (int)$adminRow['adminid']));
		$tpl->assign('adminAvatarHasCustom', AvatarHasCustomImage('admin', (int)$adminRow['adminid']));
		AvatarAssignAdminTemplateVars($tpl, $adminRow);
		$tpl->assign('mfaAdminEnabled', $bm_prefs['mfa_admin_enable'] == 'yes');
		$tpl->assign('mfaAdminMayManage', BMMfa::AdminMayManageMfa());
		if(BMMfa::AdminMayManageMfa())
			include(__DIR__ . '/admins_mfa_account.inc.php');
		$tpl->assign('page', 'admins.account.tpl');
	}
}

	else if($_REQUEST['action'] == 'admins' && $adminRow['type'] == 0)
	{
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'edit')
		{
			$res = $db->Query('SELECT * FROM {pre}admins WHERE `adminid`=?',
				$_REQUEST['id']);
			if($res->RowCount() != 1)
				die('Admin not found');
			$admin = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			$displayPage = true;

			if(isset($_REQUEST['resetMfa']) && isset($_REQUEST['id']))
			{
				CsrfEnforceOnPost();
				BMMfa::ResetAccount('admin', (int)$admin['adminid'], 'full', array('setup_required' => true));
				PutLog(sprintf('Admin <%s> reset MFA for admin <%s> (%d) from <%s>',
					$adminRow['username'],
					$admin['username'],
					(int)$admin['adminid'],
					$_SERVER['REMOTE_ADDR']),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
				$tpl->assign('msg', isset($lang_admin['mfa_reset_admin_ok']) ? $lang_admin['mfa_reset_admin_ok'] : $lang_admin['mfa_reset_ok']);
			}
			else if(isset($_REQUEST['save']) && isset($_POST['username']))
		{
			$res = $db->Query('SELECT `adminid` FROM {pre}admins WHERE `username`=?',
				$_POST['username']);
			$existingCount = $res->RowCount();
			if($existingCount > 0)
				list($existingID) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			if(($existingCount > 0 && $existingID != $admin['adminid']) || trim($_POST['username']) == '')
			{
				$tpl->assign('msgTitle', $lang_admin['error']);
				$tpl->assign('msgText', $lang_admin['adminexists']);
				$tpl->assign('msgIcon', 'error32');
				$tpl->assign('page', 'msg.tpl');
				$displayPage = false;
			}
			else if(($_POST['newpw1'] != '' || $_POST['newpw2'] != '') && strlen($_POST['newpw1']) < 6 || $_POST['newpw1'] != $_POST['newpw2'])
			{
				$tpl->assign('msgTitle', $lang_admin['error']);
				$tpl->assign('msgText', $lang_admin['pwerror']);
				$tpl->assign('msgIcon', 'error32');
				$tpl->assign('page', 'msg.tpl');
				$displayPage = false;
			}
			else
			{
				if($_POST['newpw1'] != '')
				{
					$pw = PasswordHashSetAdminPassword((int)$admin['adminid'], $_POST['newpw1']);
					$salt = '';
				}
				else
				{
					$salt = $admin['password_salt'];
					$pw = $admin['password'];
				}

				if($admin['adminid'] == $adminRow['adminid'] && $_POST['newpw1'] != '')
				{
					$_SESSION['bm_adminAuth'] = AdminSessionAuthBind($pw, (int)$adminRow['adminid']);
				}

				if(isset($_POST['perms']) && is_array($_POST['perms']))
				{
					$privileges = serialize($_POST['perms']);
				}
				else
				{
					$privileges = serialize(array());
				}

				if($admin['adminid'] == 1)
				{
					$_POST['type'] = 0;
					$privileges = '';
				}

				$adminEmail = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
				if($adminEmail !== '' && filter_var($adminEmail, FILTER_VALIDATE_EMAIL) === false)
				{
					$tpl->assign('msgTitle', $lang_admin['error']);
					$tpl->assign('msgText', $lang_admin['addressinvalid']);
					$tpl->assign('msgIcon', 'error32');
					$tpl->assign('page', 'msg.tpl');
					$displayPage = false;
				}
				else
				{
				$db->Query('UPDATE {pre}admins SET `username`=?,`password`=?,`password_salt`=?,`firstname`=?,`lastname`=?,`email`=?,`type`=?,`privileges`=? WHERE `adminid`=?',
					$_POST['username'],
					$pw,
					$salt,
					$_POST['firstname'],
					$_POST['lastname'],
					$adminEmail,
					$_POST['type'],
					$privileges,
					$admin['adminid']);

				SessionRedirect('admins.php?action=admins');
				}
			}
		}

		if($displayPage)
		{
			EnsureAdminEmailColumn();

			$pluginList = array();

			// build plugin list
			foreach($plugins->_plugins as $className=>$pluginInfo)
			{
				if($plugins->getParam('admin_pages', $className))
					$pluginList[$className] = $plugins->getParam('admin_page_title', $className);
			}

			$admin['perms'] = @unserialize($admin['privileges']);

			$mfaAccount = BMMfa::GetAccount('admin', (int)$admin['adminid']);
			$mfaEnabledAt = is_array($mfaAccount) ? BMMfa::GetEnabledAtTimestamp($mfaAccount) : 0;

			$tpl->assign('permsTable',	$permsTable);
			$tpl->assign('admin', 		$admin);
			$tpl->assign('pluginList', 	$pluginList);
			$tpl->assign('mfaAdminEnabled', $bm_prefs['mfa_admin_enable'] == 'yes');
			$tpl->assign('mfaEnabled', is_array($mfaAccount) && $mfaAccount['enabled'] == 'yes');
			$tpl->assign('mfaSetupRequired', is_array($mfaAccount) && $mfaAccount['setup_required'] == 'yes');
			$tpl->assign('mfaEnabledAtFormatted', $mfaEnabledAt > 0 ? FormatDate($mfaEnabledAt) : '');
			$tpl->assign('mfaActiveMethod', is_array($mfaAccount) ? BMMfa::ActiveMethod($mfaAccount) : '');
			$tpl->assign('page', 		'admins.edit.tpl');
		}
	}
	else
	{
		$displayPage = true;

		if(isset($_REQUEST['add']) && isset($_POST['username']))
		{
			$res = $db->Query('SELECT COUNT(*) FROM {pre}admins WHERE `username`=?',
				$_POST['username']);
			list($existingCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			if($existingCount > 0 || trim($_POST['username']) == '')
			{
				$tpl->assign('msgTitle', $lang_admin['error']);
				$tpl->assign('msgText', $lang_admin['adminexists']);
				$tpl->assign('msgIcon', 'error32');
				$tpl->assign('page', 'msg.tpl');
				$displayPage = false;
			}
			else if(strlen($_POST['pw1']) < 6 || $_POST['pw1'] != $_POST['pw2'])
			{
				$tpl->assign('msgTitle', $lang_admin['error']);
				$tpl->assign('msgText', $lang_admin['pwerror']);
				$tpl->assign('msgIcon', 'error32');
				$tpl->assign('page', 'msg.tpl');
				$displayPage = false;
			}
			else
			{
				$pw = PasswordHashCreate($_POST['pw1'], 'admin');

				$adminEmail = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
				if($adminEmail !== '' && filter_var($adminEmail, FILTER_VALIDATE_EMAIL) === false)
				{
					$tpl->assign('msgTitle', $lang_admin['error']);
					$tpl->assign('msgText', $lang_admin['addressinvalid']);
					$tpl->assign('msgIcon', 'error32');
					$tpl->assign('page', 'msg.tpl');
					$displayPage = false;
				}
				else
				{
				EnsureAdminEmailColumn();
				$db->Query('INSERT INTO {pre}admins(`username`,`firstname`,`lastname`,`email`,`password`,`password_salt`,`type`) VALUES(?,?,?,?,?,?,?)',
					$_POST['username'],
					$_POST['firstname'],
					$_POST['lastname'],
					$adminEmail,
					$pw,
					'',
					$_POST['type']);
				$adminID = $db->InsertId();

				header('Location: admins.php?action=admins&do=edit&id='.$adminID.'&sid='.session_id());
				exit();
				}
			}
		}

		else if(isset($_REQUEST['delete']) && (int)$_REQUEST['delete']>1)
		{
			$db->Query('DELETE FROM {pre}admins WHERE `adminid`=?',
				(int)$_REQUEST['delete']);
		}

		// mass action?
		else if(isset($_REQUEST['executeMassAction']))
		{
			// get domains
			$massAdmins = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 6) == 'admin_' && (int)substr($key, 6) > 1)
					$massAdmins[] = substr($key, 6);

			if(count($massAdmins) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					// delete admin
					$db->Query('DELETE FROM {pre}admins WHERE `adminid` IN ?', $massAdmins);
				}
			}
		}

		if($displayPage)
		{
			EnsureAdminEmailColumn();

			$admins = array();
			$res = $db->Query('SELECT `adminid`,`username`,`firstname`,`lastname`,`email`,`type` FROM {pre}admins ORDER BY `username` ASC');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$admins[$row['adminid']] = $row;
			}
			$res->Free();

			if($bm_prefs['mfa_admin_enable'] == 'yes' && count($admins) > 0)
			{
				$mfaMap = BMMfa::GetAccountsForAdmins(array_keys($admins));
				foreach($admins as $aid => &$adminEntry)
				{
					$mfaRow = isset($mfaMap[$aid]) ? $mfaMap[$aid] : false;
					$st = BMMfa::ListStatusForAdmin($aid, $mfaRow);
					$adminEntry['mfaStatus'] = $st['status'];
					$adminEntry['mfaMethod'] = $st['method'];
					$adminEntry['mfaStatusTitle'] = $st['title'];
				}
				unset($adminEntry);
			}

			$tpl->assign('mfaAdminEnabled', $bm_prefs['mfa_admin_enable'] == 'yes');
			$tpl->assign('admins', 	$admins);
			$tpl->assign('page', 	'admins.admins.tpl');
		}
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['welcome'] . ' &raquo; ' . $lang_admin['admins']);
$tpl->display('page.tpl');
?>