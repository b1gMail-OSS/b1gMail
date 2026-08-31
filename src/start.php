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
	require './serverlib/init.inc.php';
include('./serverlib/dashboard.class.php');

if(SessionIsLogoutRequest())
{
	SessionHandleUserLogout();
	exit();
}

$sessionApiActions = array('sessionStatus', 'sessionUnlock', 'sessionKeepAlive', 'sessionLock', 'sessionLockNow');
if(isset($_REQUEST['action']) && in_array($_REQUEST['action'], $sessionApiActions, true))
{
	if(!RequestPrivileges(PRIVILEGES_USER, true))
		SessionJsonResponse(array(
			'ok'             => false,
			'sessionExpired' => true,
			'redirect'       => SessionLoginRedirectUrl(false),
		), 401);

	SessionProcessLifecycleAfterAuth(PRIVILEGES_USER, SessionAllowsLockedAccess());
	SessionHandleUserApi($_REQUEST['action']);
	exit();
}

RequestPrivileges(PRIVILEGES_USER);

$userID = isset($_SESSION['bm_userID']) ? (int)$_SESSION['bm_userID'] : 0;
$setupGroupID = isset($groupRow['id']) ? (int)$groupRow['id'] : 0;
if($userID > 0)
	BMMfa::SyncSetupRequiredSession($userID, $setupGroupID);

if(isset($_POST['switch_method'])
	|| (isset($_REQUEST['do']) && $_REQUEST['do'] === 'mfaSetup'))
	$_REQUEST['action'] = 'mfaSetup';

if(!empty($_SESSION['bm_mfaSetupRequired'])
	&& (!isset($_REQUEST['action']) || RouteRestoreLegacyAction($_REQUEST['action']) !== 'mfaSetup'))
{
	SessionRedirect('start.php?action=mfaSetup');
	exit();
}

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));

/**
 * default action = start
 */
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'start';
else
	$_REQUEST['action'] = RouteRestoreLegacyAction($_REQUEST['action']);
$tpl->assign('activeTab', 'start');
$tpl->assign('pageTitle', $lang_user['start']);

/**
 * page sidebar
 */
$tpl->assign('pageMenuFile', 'li/start.sidebar.tpl');

/**
 * dashboard
 */
$dashboard = _new('BMDashboard', array(BMWIDGET_START));

/**
 * start page
 */
if($_REQUEST['action'] == 'start')
{
	$widgetOrder = $thisUser->GetPref('widgetOrderStart');
	if($widgetOrder === false || trim($widgetOrder) == '')
		$widgetOrder = $bm_prefs['widget_order_start'];

	$tpl->assign('autoSetPreviewPos', !$thisUser->GetPref('previewPosition'));
	$tpl->assign('pageTitle', $lang_user['welcome']);
	$tpl->assign('widgetOrder', $widgetOrder);
	$tpl->assign('widgets', $dashboard->getWidgetArray($widgetOrder));
	$tpl->assign('pageContent', 'li/start.page.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * save widget order
 */
else if($_REQUEST['action'] == 'saveWidgetOrder'
			&& isset($_REQUEST['order']))
{
	$widgetOrder = $_REQUEST['order'];

	if($dashboard->checkWidgetOrder($widgetOrder))
	{
		$thisUser->SetPref('widgetOrderStart', $widgetOrder);
		die('OK');
	}
	else
	{
		die('Invalid order');
	}
}

/**
 * customize widgets
 */
else if($_REQUEST['action'] == 'customize')
{
	$widgetOrder = $thisUser->GetPref('widgetOrderStart');
	if($widgetOrder === false || trim($widgetOrder) == '')
		$widgetOrder = $bm_prefs['widget_order_start'];

	$tpl->assign('pageTitle', $lang_user['customize']);
	$tpl->assign('possibleWidgets', $dashboard->getPossibleWidgets($widgetOrder));
	$tpl->assign('pageContent', 'li/start.customize.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * save cutomization
 */
else if($_REQUEST['action'] == 'saveCustomize')
{
	$widgetOrder = $thisUser->GetPref('widgetOrderStart');
	if($widgetOrder === false || trim($widgetOrder) == '')
		$widgetOrder = $bm_prefs['widget_order_start'];
	$newOrder = $dashboard->generateOrderStringFromPostForm($widgetOrder);

	$thisUser->SetPref('widgetOrderStart', $newOrder);

	SessionRedirect('start.php');
	exit();
}

/**
 * search
 */
else if($_REQUEST['action'] == 'search'
		&& isset($_REQUEST['q']))
{
	$url = SearchEngineBuildRedirect($bm_prefs['search_engine'], $_REQUEST['q']);
	if($url === false)
		SessionRedirect('start.php');
	header('Location: ' . $url);
	exit();
}

/**
 * widget preferences
 */
else if($_REQUEST['action'] == 'showWidgetPrefs'
		&& isset($_REQUEST['name']))
{
	$dashboard->showWidgetPrefs($_REQUEST['name']);
}

/**
 * safe code validation RPC
 */
else if($_REQUEST['action'] == 'checkSafeCode')
{
	if(!class_exists('BMCaptcha'))
		include(B1GMAIL_DIR . 'serverlib/captcha.class.php');
	$captcha = BMCaptcha::createDefaultProvider();
	if($captcha->check(false))
		echo '1';
	else
		echo '0';
	exit();
}

/**
 * notifications
 */
else if($_REQUEST['action'] == 'getNotifications')
{
	$tpl->assign('bmNotifications', $thisUser->GetNotifications());
	$tpl->display('li/notifications.tpl');
	exit();
}

/**
 * notification count
 */
else if($_REQUEST['action'] == 'getNotificationCount')
{
	echo $thisUser->GetUnreadNotifications();
	exit();
}

/**
 * test Web Push delivery (logged-in user)
 */
else if($_REQUEST['action'] == 'testPush')
{
	include B1GMAIL_DIR.'serverlib/push.class.php';
	header('Content-Type: application/json; charset=utf-8');

	if (!BMPush::isEnabled()) {
		echo json_encode(['ok' => false, 'error' => 'disabled']);
		exit();
	}

	$pushPrefs = BMPush::getUserPushPrefs($thisUser->_id);
	$result = BMPush::sendTestPush($thisUser->_id);

	$reason = isset($result['reason']) ? $result['reason'] : '';
	if ($result['sent'] == 0 && $reason == '' && !empty($pushPrefs['enabled']) && empty($pushPrefs['types'][BMPush::TYPE_MAIL])) {
		$reason = 'mail_type_disabled';
	}

	echo json_encode([
		'ok' => $result['sent'] > 0,
		'sent' => $result['sent'],
		'failed' => isset($result['failed']) ? $result['failed'] : 0,
		'removed' => isset($result['removed']) ? $result['removed'] : 0,
		'subscriptions' => BMPush::countSubscriptions(BMPush::AREA_USER, $thisUser->_id),
		'prefsEnabled' => !empty($pushPrefs['enabled']),
		'reason' => $reason,
		'lastError' => isset($result['lastError']) ? $result['lastError'] : '',
	]);
	exit();
}

/**
 * MFA setup wizard (required before full LI access)
 */
else if(isset($_REQUEST['action']) && RouteRestoreLegacyAction($_REQUEST['action']) == 'mfaSetup')
{
	$userID = (int)$_SESSION['bm_userID'];
	$groupID = isset($groupRow['id']) ? (int)$groupRow['id'] : 0;

	if(!BMMfa::NeedsSetupWizard('user', $userID, $groupID) && empty($_SESSION['bm_mfaSetupRequired']))
		SessionRedirect('start.php');

	if(isset($_POST['switch_method']) && in_array($_POST['switch_method'], array('email', 'totp'), true))
	{
		$switchTo = $_POST['switch_method'];
		BMMfa::SetSetupMethod($switchTo, $userID, $groupID);
		if($switchTo === 'email')
		{
			$_SESSION['bm_mfaSetupStep'] = 2;
			$altMail = BMMfa::EmailAddressForUserMfa($userID);
			if(BMMfa::SendSetupEmailCode($userID, $groupID, 'setup_wizard_switch'))
			{
				$_SESSION['bm_mfaSetupEmailSent'] = true;
				$acc = BMMfa::GetAccount('user', $userID);
				$mfaID = is_array($acc) ? (int)$acc['id'] : 0;
				$_SESSION['bm_mfaSetupFlash'] = array(
					'type' => 'info',
					'msg'  => $mfaID > 0
						? BMMfa::EmailCodeValidityMessage($userID, $mfaID)
						: sprintf(
							isset($lang_user['mfa_email_sent_to']) ? $lang_user['mfa_email_sent_to'] : 'Code sent to %s.',
							$altMail !== false ? BMMfa::MaskEmail($altMail) : ''
						),
				);
			}
			else
			{
				$_SESSION['bm_mfaSetupFlash'] = array(
					'type' => 'error',
					'msg'  => isset($lang_user['mfa_email_send_failed']) ? $lang_user['mfa_email_send_failed'] : '',
				);
			}
		}
		SessionRedirect('start.php?action=mfaSetup');
		exit();
	}

	$mfaAccount = BMMfa::GetAccount('user', $userID);
	if(is_array($mfaAccount) && BMMfa::RequiresMfaVerifyAtLogin($mfaAccount)
		&& $mfaAccount['setup_required'] !== 'yes')
	{
		BMMfa::SetSetupRequiredSession(false);
		if(!BMMfa::IsLoginReady('user', $userID))
		{
			unset($_SESSION['bm_userLoggedIn'], $_SESSION['bm_userID'], $_SESSION['bm_sessionToken'], $_SESSION['bm_xorCryptKey']);
			BMMfa::ClearPending();
			SessionRedirect('index.php');
			exit();
		}
		SessionRedirect('start.php');
	}

	$mfaInfoSet = false;
	if(isset($_SESSION['bm_mfaSetupFlash']) && is_array($_SESSION['bm_mfaSetupFlash']))
	{
		$flash = $_SESSION['bm_mfaSetupFlash'];
		unset($_SESSION['bm_mfaSetupFlash']);
		if(!empty($flash['msg']))
		{
			if(isset($flash['type']) && $flash['type'] === 'error')
				$tpl->assign('mfaError', $flash['msg']);
			else
			{
				$tpl->assign('mfaInfo', $flash['msg']);
				$mfaInfoSet = true;
			}
		}
	}

	$method = BMMfa::ResolveSetupMethod($userID, $groupID);
	$mfaCanUseEmail = BMMfa::UserCanUseEmailMfa($userID);
	$mfaAltMail = $mfaCanUseEmail ? BMMfa::EmailAddressForUserMfa($userID) : false;
	$emailLabel = DecodeEMail($userRow['email']);
	$setupSecret = isset($_SESSION['bm_mfaSetupSecret']) ? $_SESSION['bm_mfaSetupSecret'] : '';
	$mfaStep = isset($_SESSION['bm_mfaSetupStep']) ? (int)$_SESSION['bm_mfaSetupStep'] : 1;
	$mfaEmailSendHandled = false;

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'mfaSetup')
	{
		$step = isset($_REQUEST['step']) ? (int)$_REQUEST['step'] : 1;

		if($method === 'email' && $step === 2 && isset($_POST['resend_email']))
		{
			unset($_SESSION['bm_mfaSetupEmailSent']);
			$mfaEmailSendHandled = true;
			if(BMMfa::SendSetupEmailCode($userID, $groupID, 'setup_wizard_resend'))
			{
				$_SESSION['bm_mfaSetupEmailSent'] = true;
				$acc = BMMfa::GetAccount('user', $userID);
				$mfaID = is_array($acc) ? (int)$acc['id'] : 0;
				if($mfaID > 0)
				{
					$tpl->assign('mfaInfo', BMMfa::EmailCodeValidityMessage($userID, $mfaID));
					$mfaInfoSet = true;
				}
			}
			else
				$tpl->assign('mfaError', isset($lang_user['mfa_email_send_failed']) ? $lang_user['mfa_email_send_failed'] : '');
			$mfaStep = 2;
			$_SESSION['bm_mfaSetupStep'] = 2;
		}
		else if($method === 'totp' && $step === 2 && $setupSecret !== '' && isset($_POST['totp_code']))
		{
			if(BMMfa::VerifyTotp($setupSecret, $_POST['totp_code']))
			{
				global $db;
				$mfaID = is_array($mfaAccount) ? (int)$mfaAccount['id'] : BMMfa::EnsureUserAccount($userID, $groupID, 'totp', $setupSecret);
				$db->Query('UPDATE {pre}mfa_accounts SET totp_enabled=?,totp_secret=?,method=? WHERE id=?',
					'yes', $setupSecret, 'totp', (int)$mfaID);
				$tpl->assign('backupCodes', BMMfa::GenerateBackupCodes($mfaID));
				$mfaStep = 3;
				$_SESSION['bm_mfaSetupStep'] = 3;
			}
			else
			{
				$tpl->assign('mfaError', isset($lang_user['mfa_verify_failed']) ? $lang_user['mfa_verify_failed'] : 'Invalid code.');
				$mfaStep = 2;
			}
		}
		else if($method === 'email' && $step === 2 && isset($_POST['email_code']))
		{
			$acc = BMMfa::GetAccount('user', $userID);
			$mfaID = is_array($acc) ? (int)$acc['id'] : 0;
			if($mfaID > 0 && BMMfa::VerifyEmailCode($mfaID, $_POST['email_code']))
			{
				BMMfa::ActivateMethod($mfaID, 'email');
				$tpl->assign('backupCodes', BMMfa::GenerateBackupCodes($mfaID));
				$mfaStep = 3;
				$_SESSION['bm_mfaSetupStep'] = 3;
			}
			else
			{
				$tpl->assign('mfaError', isset($lang_user['mfa_verify_failed']) ? $lang_user['mfa_verify_failed'] : 'Invalid code.');
				$mfaStep = 2;
			}
		}
		else if($step === 3 && isset($_POST['backup_ack']))
		{
			$mfaID = is_array($mfaAccount) ? (int)$mfaAccount['id'] : 0;
			if($mfaID <= 0)
			{
				$acc = BMMfa::GetAccount('user', $userID);
				$mfaID = is_array($acc) ? (int)$acc['id'] : 0;
			}
			if($mfaID > 0)
			{
				if($method === 'totp')
				{
					global $db;
					$db->Query('UPDATE {pre}mfa_accounts SET totp_enabled=?,email_enabled=?,method=? WHERE id=?',
						'yes', 'no', 'totp', (int)$mfaID);
				}
				BMMfa::CompleteSetup($mfaID);
			}
			unset($_SESSION['bm_mfaSetupSecret'], $_SESSION['bm_mfaSetupEmailSent'], $_SESSION['bm_mfaSetupStep'], $_SESSION['bm_mfaSetupMethod']);
			BMMfa::SetSetupRequiredSession(false);
			SessionRedirect('start.php');
			exit();
		}
	}

	if($mfaStep === 1)
	{
		if($method === 'email')
		{
			$mfaStep = 2;
			$_SESSION['bm_mfaSetupStep'] = 2;
		}
		else
		{
			if($setupSecret === '')
			{
				$setupSecret = BMMfa::GenerateTotpSecret();
				$_SESSION['bm_mfaSetupSecret'] = $setupSecret;
			}
			$mfaID = BMMfa::EnsureUserAccount($userID, $groupID, 'totp', $setupSecret);
			global $db;
			$db->Query('UPDATE {pre}mfa_accounts SET totp_secret=?,totp_enabled=?,email_enabled=?,method=?,setup_required=? WHERE id=?',
				$setupSecret, 'no', 'no', 'totp', 'yes', (int)$mfaID);
			$mfaStep = 2;
			$_SESSION['bm_mfaSetupStep'] = 2;
		}
	}

	// Same auto-send behaviour as prefs.php when the e-mail setup UI is shown
	if($method === 'email' && $mfaCanUseEmail
		&& (!is_array($mfaAccount) || $mfaAccount['enabled'] != 'yes')
		&& !$mfaEmailSendHandled
		&& empty($_SESSION['bm_mfaSetupEmailSent']))
	{
		if($mfaStep < 2)
		{
			$mfaStep = 2;
			$_SESSION['bm_mfaSetupStep'] = 2;
		}

		if(BMMfa::SendSetupEmailCode($userID, $groupID, 'setup_wizard_auto'))
		{
			$_SESSION['bm_mfaSetupEmailSent'] = true;
			$acc = BMMfa::GetAccount('user', $userID);
			$mfaID = is_array($acc) ? (int)$acc['id'] : 0;
			if($mfaID > 0 && !$mfaInfoSet)
			{
				$tpl->assign('mfaInfo', BMMfa::EmailCodeValidityMessage($userID, $mfaID));
				$mfaInfoSet = true;
			}
		}
		else if(!$mfaInfoSet)
			$tpl->assign('mfaError', isset($lang_user['mfa_email_send_failed']) ? $lang_user['mfa_email_send_failed'] : '');
	}
	else if($method !== 'email')
		unset($_SESSION['bm_mfaSetupEmailSent']);

	if($method === 'email' && !$mfaInfoSet && !$mfaEmailSendHandled)
	{
		$acc = BMMfa::GetAccount('user', $userID);
		$mfaID = is_array($acc) ? (int)$acc['id'] : 0;
		if($mfaID > 0 && BMMfa::GetActiveEmailCodeRemainingSeconds($mfaID) > 0)
		{
			$tpl->assign('mfaInfo', BMMfa::EmailCodeValidityMessage($userID, $mfaID, 'mfa_email_code_active'));
			$mfaInfoSet = true;
		}
	}

	$mfaEmailCodeRemainingSec = 0;
	$mfaEmailCodeExpiresAt = 0;
	if($method === 'email' && $mfaCanUseEmail)
	{
		$acc = BMMfa::GetAccount('user', $userID);
		if(is_array($acc))
		{
			$mfaEmailCodeRemainingSec = BMMfa::GetActiveEmailCodeRemainingSeconds((int)$acc['id']);
			$mfaEmailCodeExpiresAt = BMMfa::GetActiveEmailCodeExpiresAt((int)$acc['id']);
		}
	}
	$tpl->assign('mfaEmailCodeRemainingSec', $mfaEmailCodeRemainingSec);
	$tpl->assign('mfaEmailCodeExpiresAt', $mfaEmailCodeExpiresAt);
	$tpl->assign('mfaResendWaitPrefix', isset($lang_user['mfa_resend_wait']) ? $lang_user['mfa_resend_wait'] : 'Resend in %s');

	$tpl->assign('mfaStep', $mfaStep);
	$tpl->assign('mfaSetupMethod', $method);
	$tpl->assign('mfaCanUseEmail', $mfaCanUseEmail);
	$tpl->assign('mfaAltMailMasked', $mfaAltMail !== false ? BMMfa::MaskEmail($mfaAltMail) : '');

	$issuer = isset($bm_prefs['titel']) ? $bm_prefs['titel'] : 'b1gMail';
	$uri = BMMfa::ProvisioningUri($emailLabel, $setupSecret, $issuer);
	$tpl->assign('mfaQrSvg', $method === 'totp' ? BMMfa::QrSvg($uri) : '');
	$tpl->assign('mfaSecret', $setupSecret);
	$tpl->assign('mfaUri', $uri);
	$tpl->assign('pageTitle', isset($lang_user['mfa_setup_title']) ? $lang_user['mfa_setup_title'] : 'Set up two-factor authentication');
	$tpl->assign('mfaSetupMandatory', BMMfa::IsMandatoryForAccount('user', $userID, $groupID));
	$tpl->assign('mfaSetupMode', true);

	$widgetOrder = $thisUser->GetPref('widgetOrderStart');
	if($widgetOrder === false || trim($widgetOrder) == '')
		$widgetOrder = $bm_prefs['widget_order_start'];
	$tpl->assign('autoSetPreviewPos', !$thisUser->GetPref('previewPosition'));
	$tpl->assign('widgetOrder', $widgetOrder);
	$tpl->assign('widgets', $dashboard->getWidgetArray($widgetOrder));
	$tpl->assign('pageContent', 'li/start.page.tpl');
	$tpl->display('li/index.tpl');
	exit();
}
?>