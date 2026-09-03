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

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'mfaVerify')
{
	@session_start();
	$pending = BMMfa::GetPending();
	if($pending === false || $pending['type'] !== 'admin')
		SessionRedirect('index.php');

	if(SessionIsLoggedIn('admin'))
		SessionRedirect('welcome.php');

	if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST')
	{
		SessionEnsureActiveWithCookie();
		CsrfEnforceOnPost();
	}

	$tpl->assign('pageTitle', isset($lang_admin['mfa_verify_title'])
		? $lang_admin['mfa_verify_title']
		: 'Two-factor sign-in');
	$tpl->assign('recoveryMode', false);

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'mfaVerify'
		&& isset($_POST['mfa_code']))
	{
		$code = trim((string)$_POST['mfa_code']);
		$isBackup = isset($_POST['mfa_use_backup']) && $_POST['mfa_use_backup'] == '1';
		if(BMMfa::VerifyPendingCode($code, $isBackup))
		{
			$redirect = BMMfa::FinalizeAdminLoginFromPending();
			if($redirect !== false)
				SessionRedirect($redirect);
		}
		$tpl->assign('mfaError', isset($lang_admin['mfa_verify_failed'])
			? $lang_admin['mfa_verify_failed']
			: 'Invalid code.');
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'mfaResend')
	{
		if(BMMfa::ResendPendingEmailCode())
			$tpl->assign('mfaInfo', isset($lang_admin['mfa_code_sent'])
				? $lang_admin['mfa_code_sent']
				: 'A new code has been sent.');
	}

	SessionAssignLoginPageActive('admin');
	$tpl->display('login.mfa_verify.tpl');
	exit();
}

if(isset($_REQUEST['action']) && $_REQUEST['action']=='login')
{
	if(SessionIsLoggedIn('admin'))
	{
		$jump = isset($_REQUEST['jump'])
			? AdminLoginJumpTarget($_REQUEST['jump'])
			: 'welcome.php';
		SessionRedirect($jump);
	}

	if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST')
	{
		SessionEnsureActiveWithCookie();
		CsrfEnforceOnPost();

		$username = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
		$pw       = isset($_POST['password']) ? trim((string)$_POST['password']) : '';

		if($username !== '' && $pw !== '')
		{
			$res = $db->Query('SELECT `adminid`,`username`,`password`,`password_salt`,`last_try` FROM {pre}admins WHERE `username`=?',
				$username);
			if($res->RowCount() == 1)
			{
				$adminUserRow = $res->FetchArray(MYSQLI_ASSOC);
				$res->Free();

				if($adminUserRow['last_try'] > (time()-60*5))
				{
					$errorMsg = sprintf($lang_admin['dattempt2'], FormatDate($adminUserRow['last_try']+60*5));
				}
				else
				{
					if($adminUserRow['last_try'] > 10)
					{
						$adminUserRow['last_try'] = 0;
						$db->Query('UPDATE {pre}admins SET `last_try`=0 WHERE `adminid`=?', $adminUserRow['adminid']);
					}
					$legacyOk = hash_equals((string)$adminUserRow['password'], md5($pw.$adminUserRow['password_salt']));
					$modernOk = PasswordHashIsModern($adminUserRow['password']) && password_verify($pw, $adminUserRow['password']);
					if($legacyOk || $modernOk)
					{
						$upgradedHash = PasswordHashUpgradeAdmin(
							(int)$adminUserRow['adminid'],
							$pw,
							$adminUserRow['password'],
							isset($adminUserRow['password_salt']) ? $adminUserRow['password_salt'] : ''
						);
						if($upgradedHash !== false)
							$adminUserRow['password'] = $upgradedHash;

						$db->Query('UPDATE {pre}admins SET `last_try`=0 WHERE `adminid`=?', $adminUserRow['adminid']);

						$jump = isset($_REQUEST['jump'])
							? AdminLoginJumpTarget($_REQUEST['jump'])
							: 'welcome.php';
						$timezone = isset($_REQUEST['timezone'])
							? (int)$_REQUEST['timezone']
							: date('Z');

						if(BMMfa::DeferAdminLoginForMfa((int)$adminUserRow['adminid'], $adminUserRow, array(
							'timezone' => $timezone,
							'jump'     => $jump,
						)))
						{
							SessionRedirect('index.php?action=mfaVerify');
							exit();
						}

						// create session
						SessionStart();
						SessionRegenerateOnLogin();
						$sessionID = session_id();
						$_SESSION['bm_adminLoggedIn']	= true;
						$_SESSION['bm_adminID']			= $adminUserRow['adminid'];
						$_SESSION['bm_adminAuth']		= AdminSessionAuthBind($adminUserRow['password'], (int)$adminUserRow['adminid']);
						$_SESSION['bm_sessionToken']	= SessionToken();
						$_SESSION['bm_timezone']		= $timezone;
						SessionInitLoginTimestamps(true);
						// Create session cookie lock
						$_SESSION['adminsessionSecret'] = GenerateRandomKey('sessionSecret');
						BMSecureSetCookie(
							'bm_admin_sessionSecret_'.substr($sessionID, 0, 16),
							$_SESSION['adminsessionSecret'],
							0
						);

						// log
						PutLog(sprintf('Admin <%s> logged in from <%s>',
							$adminUserRow['username'],
							$_SERVER['REMOTE_ADDR']),
							PRIO_NOTE,
							__FILE__,
							__LINE__);

						if(class_exists('BMLoginNotify'))
							BMLoginNotify::OnSuccessfulLogin('admin', (int)$adminUserRow['adminid'], '', 0);

						if(BMMfa::NeedsSetupWizard('admin', (int)$adminUserRow['adminid'], 0))
							SessionRedirect(SessionUrl('admins.php?action=account'));

						SessionRedirect($jump);
					}
					else
					{
						// log
						PutLog(sprintf('Admin login from <%s> as <%s> failed (invalid password)',
							$_SERVER['REMOTE_ADDR'],
							$adminUserRow['username']),
							PRIO_NOTE,
							__FILE__,
							__LINE__);

						if($adminUserRow['last_try']+1 > 4)
						{
							// log
							PutLog(sprintf('Admin login for <%s> locked until %s',
								$adminUserRow['last_try'],
								date('r', time()+60*5)),
								PRIO_WARNING,
								__FILE__,
								__LINE__);
							$last_try = time();
						}
						else
							$last_try = $adminUserRow['last_try']+1;
						$db->Query('UPDATE {pre}admins SET `last_try`=? WHERE `adminid`=?',
							$last_try,
							$adminUserRow['adminid']);
						$errorMsg = $lang_admin['loginerror'];
					}
				}
			}
			else
			{
				$errorMsg = $lang_admin['loginerror'];

				// log
				PutLog(sprintf('Admin login from <%s> as <%s> failed (invalid username)',
					$_SERVER['REMOTE_ADDR'],
					$username),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
			}

			if(isset($errorMsg))
				$tpl->assign('error', $errorMsg);
		}
	}
}
else if(isset($_REQUEST['action']) && $_REQUEST['action']=='logout')
{
	RequestPrivileges(PRIVILEGES_ADMIN);
	$adminId = isset($_SESSION['bm_adminID']) ? (int) $_SESSION['bm_adminID'] : 0;
	if ($adminId > 0) {
		if (!class_exists('BMPush', false)) {
			@include_once B1GMAIL_DIR.'serverlib/push.class.php';
		}
		if (class_exists('BMPush', false) && BMPush::isEnabled()) {
			BMPush::unsubscribeAll(BMPush::AREA_ADMIN, $adminId);
		}
	}
	$_SESSION = array();
	session_destroy();
	SessionRedirect('index.php');
}

if(isset($_REQUEST['jump']))
	$tpl->assign('jump', AdminLoginJumpTarget($_REQUEST['jump'], ''));
$tpl->assign('timezone', date('Z'));
$tpl->assign('sessionExpired', isset($_REQUEST['expired']) && (string)$_REQUEST['expired'] !== '' && (string)$_REQUEST['expired'] !== '0');
SessionAssignLoginPageActive('admin');
$tpl->display('login.tpl');
?>