<?php
/**
 * MFA self-service on admins.php?action=account (included from admins.php).
 *
 * Expects: $adminRow, $tpl, $db, $bm_prefs, $lang_admin
 */

if(!BMMfa::AdminMayManageMfa())
	return;

$adminID = (int)$adminRow['adminid'];
$mfaAccount = BMMfa::GetAccount('admin', $adminID);
$mfaMandatory = BMMfa::IsMandatoryForAccount('admin', $adminID, 0);
$mfaCanUseEmail = BMMfa::AdminCanUseEmailMfa($adminID);
$mfaAdminEmail = $mfaCanUseEmail ? BMMfa::EmailAddressForAdminMfa($adminID) : false;

if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'mfaSave' && isset($_POST['switch_method']))
{
	CsrfEnforceOnPost();
	BMMfa::SetSetupMethodForAdmin($_POST['switch_method'], $adminID);
	unset($_SESSION['bm_adminMfaPrefsSwitch']);
	header('Location: ' . SessionUrl('admins.php?action=account'));
	exit();
}

$mfaSetupMethod = BMMfa::ResolveSetupMethodForAdmin($adminID);
$mfaPrefsSwitch = isset($_SESSION['bm_adminMfaPrefsSwitch']) ? $_SESSION['bm_adminMfaPrefsSwitch'] : '';

if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'mfaSave')
{
	CsrfEnforceOnPost();

	$needsPassword = isset($_POST['mfa_action'])
		&& !in_array($_POST['mfa_action'], array('resend_enable_email', 'resend_switch_email'), true);
	$password = isset($_POST['password']) ? $_POST['password'] : '';

	if($needsPassword && ($password === '' || !BMMfa::VerifyAdminPassword($adminID, $password)))
		$tpl->assign('mfaError', isset($lang_admin['mfa_wrong_password']) ? $lang_admin['mfa_wrong_password'] : 'Wrong password.');
	else if(isset($_POST['mfa_action']))
	{
		$action = $_POST['mfa_action'];

		if($action === 'resend_enable_email' || $action === 'resend_switch_email')
		{
			if(BMMfa::SendSetupEmailCodeForAdmin($adminID))
				$tpl->assign('mfaInfo', isset($lang_admin['mfa_code_sent']) ? $lang_admin['mfa_code_sent'] : '');
			else
				$tpl->assign('mfaError', isset($lang_admin['mfa_email_send_failed']) ? $lang_admin['mfa_email_send_failed'] : '');
		}
		else if($action === 'enable_totp')
		{
			$secret = isset($_SESSION['bm_adminMfaPrefsSecret']) ? $_SESSION['bm_adminMfaPrefsSecret'] : BMMfa::GenerateTotpSecret();
			if(isset($_POST['totp_code']) && BMMfa::VerifyTotp($secret, $_POST['totp_code']))
			{
				$mfaID = is_array($mfaAccount) ? (int)$mfaAccount['id'] : BMMfa::EnsureAdminAccount($adminID, 'totp', $secret);
				$db->Query('UPDATE {pre}mfa_accounts SET totp_secret=?,totp_enabled=?,email_enabled=?,method=? WHERE id=?',
					$secret, 'yes', 'no', 'totp', $mfaID);
				BMMfa::ActivateMethod($mfaID, 'totp');
				$tpl->assign('backupCodes', BMMfa::GenerateBackupCodes($mfaID));
				BMMfa::CompleteSetup($mfaID);
				unset($_SESSION['bm_adminMfaPrefsSecret'], $_SESSION['bm_adminMfaPrefsMethod']);
				PutLog(sprintf('Admin <%s> enabled MFA (TOTP) from <%s>', $adminRow['username'], $_SERVER['REMOTE_ADDR']), PRIO_NOTE, __FILE__, __LINE__);
				$tpl->assign('mfaInfo', isset($lang_admin['mfa_enabled']) ? $lang_admin['mfa_enabled'] : 'MFA enabled.');
			}
			else
				$tpl->assign('mfaError', isset($lang_admin['mfa_verify_failed']) ? $lang_admin['mfa_verify_failed'] : 'Invalid code.');
		}
		else if($action === 'enable_email' && $mfaCanUseEmail)
		{
			$acc = BMMfa::GetAccount('admin', $adminID);
			$mfaID = is_array($acc) ? (int)$acc['id'] : 0;
			if($mfaID <= 0)
			{
				BMMfa::SendSetupEmailCodeForAdmin($adminID);
				$acc = BMMfa::GetAccount('admin', $adminID);
				$mfaID = is_array($acc) ? (int)$acc['id'] : 0;
			}
			if($mfaID > 0 && isset($_POST['email_code']) && BMMfa::VerifyEmailCode($mfaID, $_POST['email_code']))
			{
				BMMfa::ActivateMethod($mfaID, 'email');
				$tpl->assign('backupCodes', BMMfa::GenerateBackupCodes($mfaID));
				BMMfa::CompleteSetup($mfaID);
				unset($_SESSION['bm_adminMfaPrefsMethod']);
				PutLog(sprintf('Admin <%s> enabled MFA (e-mail) from <%s>', $adminRow['username'], $_SERVER['REMOTE_ADDR']), PRIO_NOTE, __FILE__, __LINE__);
				$tpl->assign('mfaInfo', isset($lang_admin['mfa_enabled']) ? $lang_admin['mfa_enabled'] : 'MFA enabled.');
			}
			else
				$tpl->assign('mfaError', isset($lang_admin['mfa_verify_failed']) ? $lang_admin['mfa_verify_failed'] : 'Invalid code.');
		}
		else if($action === 'switch_to_email' && $mfaCanUseEmail && is_array($mfaAccount) && $mfaAccount['enabled'] == 'yes')
		{
			if(BMMfa::SendSetupEmailCodeForAdmin($adminID))
			{
				$_SESSION['bm_adminMfaPrefsSwitch'] = 'email';
				BMMfa::ResolveSetupMethodForAdmin($adminID);
				$_SESSION['bm_adminMfaPrefsMethod'] = 'email';
				$tpl->assign('mfaInfo', sprintf(
					isset($lang_admin['mfa_email_sent_to']) ? $lang_admin['mfa_email_sent_to'] : 'Code sent to %s.',
					BMMfa::MaskEmail($mfaAdminEmail)
				));
			}
			else
				$tpl->assign('mfaError', isset($lang_admin['mfa_email_send_failed']) ? $lang_admin['mfa_email_send_failed'] : '');
		}
		else if($action === 'switch_to_totp' && is_array($mfaAccount) && $mfaAccount['enabled'] == 'yes')
		{
			$_SESSION['bm_adminMfaPrefsSwitch'] = 'totp';
			$_SESSION['bm_adminMfaPrefsMethod'] = 'totp';
			if(!isset($_SESSION['bm_adminMfaPrefsSecret']))
				$_SESSION['bm_adminMfaPrefsSecret'] = BMMfa::GenerateTotpSecret();
		}
		else if($action === 'confirm_switch_email' && $mfaCanUseEmail && is_array($mfaAccount))
		{
			$mfaID = (int)$mfaAccount['id'];
			if(isset($_POST['email_code']) && BMMfa::VerifyEmailCode($mfaID, $_POST['email_code']))
			{
				BMMfa::ActivateMethod($mfaID, 'email');
				BMMfa::CompleteSetup($mfaID);
				unset($_SESSION['bm_adminMfaPrefsSwitch'], $_SESSION['bm_adminMfaPrefsMethod']);
				PutLog(sprintf('Admin <%s> switched MFA to e-mail from <%s>', $adminRow['username'], $_SERVER['REMOTE_ADDR']), PRIO_NOTE, __FILE__, __LINE__);
				$tpl->assign('mfaInfo', isset($lang_admin['mfa_method_switched']) ? $lang_admin['mfa_method_switched'] : 'Method updated.');
			}
			else
				$tpl->assign('mfaError', isset($lang_admin['mfa_verify_failed']) ? $lang_admin['mfa_verify_failed'] : 'Invalid code.');
		}
		else if($action === 'confirm_switch_totp' && is_array($mfaAccount))
		{
			$secret = isset($_SESSION['bm_adminMfaPrefsSecret']) ? $_SESSION['bm_adminMfaPrefsSecret'] : BMMfa::GenerateTotpSecret();
			if(isset($_POST['totp_code']) && BMMfa::VerifyTotp($secret, $_POST['totp_code']))
			{
				$mfaID = (int)$mfaAccount['id'];
				$db->Query('UPDATE {pre}mfa_accounts SET totp_secret=?,totp_enabled=?,email_enabled=?,method=? WHERE id=?',
					$secret, 'yes', 'no', 'totp', $mfaID);
				BMMfa::ActivateMethod($mfaID, 'totp');
				BMMfa::CompleteSetup($mfaID);
				unset($_SESSION['bm_adminMfaPrefsSecret'], $_SESSION['bm_adminMfaPrefsSwitch'], $_SESSION['bm_adminMfaPrefsMethod']);
				PutLog(sprintf('Admin <%s> switched MFA to TOTP from <%s>', $adminRow['username'], $_SERVER['REMOTE_ADDR']), PRIO_NOTE, __FILE__, __LINE__);
				$tpl->assign('mfaInfo', isset($lang_admin['mfa_method_switched']) ? $lang_admin['mfa_method_switched'] : 'Method updated.');
			}
			else
				$tpl->assign('mfaError', isset($lang_admin['mfa_verify_failed']) ? $lang_admin['mfa_verify_failed'] : 'Invalid code.');
		}
		else if($action === 'disable' && !$mfaMandatory)
		{
			BMMfa::ResetAccount('admin', $adminID, 'full');
			unset($_SESSION['bm_adminMfaPrefsSecret'], $_SESSION['bm_adminMfaPrefsMethod'], $_SESSION['bm_adminMfaPrefsSwitch']);
			PutLog(sprintf('Admin <%s> disabled MFA from <%s>', $adminRow['username'], $_SERVER['REMOTE_ADDR']), PRIO_NOTE, __FILE__, __LINE__);
			$mfaAccount = false;
			$tpl->assign('mfaInfo', isset($lang_admin['mfa_disabled']) ? $lang_admin['mfa_disabled'] : 'MFA disabled.');
		}
		else if($action === 'regen_backup' && is_array($mfaAccount) && $mfaAccount['enabled'] == 'yes')
		{
			$tpl->assign('backupCodes', BMMfa::GenerateBackupCodes((int)$mfaAccount['id']));
			PutLog(sprintf('Admin <%s> regenerated MFA backup codes from <%s>', $adminRow['username'], $_SERVER['REMOTE_ADDR']), PRIO_NOTE, __FILE__, __LINE__);
		}
		else if($action === 'reset_setup' && is_array($mfaAccount) && $mfaAccount['enabled'] == 'yes')
		{
			BMMfa::ResetAccount('admin', $adminID, 'full', array('setup_required' => true));
			unset($_SESSION['bm_adminMfaPrefsSecret'], $_SESSION['bm_adminMfaPrefsMethod'], $_SESSION['bm_adminMfaPrefsSwitch'], $_SESSION['bm_adminMfaPrefsEmailSent']);
			$mfaAccount = BMMfa::GetAccount('admin', $adminID);
			$mfaSetupMethod = BMMfa::ResolveSetupMethodForAdmin($adminID);
			$tpl->assign('mfaInfo', isset($lang_admin['mfa_reset_setup_info']) ? $lang_admin['mfa_reset_setup_info'] : '');
		}

		$mfaAccount = BMMfa::GetAccount('admin', $adminID);
		$mfaPrefsSwitch = isset($_SESSION['bm_adminMfaPrefsSwitch']) ? $_SESSION['bm_adminMfaPrefsSwitch'] : '';
	}
}

if($mfaSetupMethod === 'email' && $mfaCanUseEmail
		&& (!is_array($mfaAccount) || $mfaAccount['enabled'] != 'yes')
		&& empty($mfaPrefsSwitch)
		&& empty($_SESSION['bm_adminMfaPrefsEmailSent']))
	{
		if(BMMfa::SendSetupEmailCodeForAdmin($adminID))
		{
			$_SESSION['bm_adminMfaPrefsEmailSent'] = true;
			$tpl->assign('mfaInfo', sprintf(
				isset($lang_admin['mfa_email_sent_to']) ? $lang_admin['mfa_email_sent_to'] : 'Code sent to %s.',
				BMMfa::MaskEmail($mfaAdminEmail)
			));
		}
	}

	if($mfaSetupMethod !== 'email')
		unset($_SESSION['bm_adminMfaPrefsEmailSent']);

	if(!isset($_SESSION['bm_adminMfaPrefsSecret']))
		$_SESSION['bm_adminMfaPrefsSecret'] = BMMfa::GenerateTotpSecret();
	$setupSecret = $_SESSION['bm_adminMfaPrefsSecret'];
	$issuer = isset($bm_prefs['titel']) ? $bm_prefs['titel'] : 'b1gMail';
	$label = $mfaAdminEmail !== false ? $mfaAdminEmail : $adminRow['username'];
	$uri = BMMfa::ProvisioningUri($label, $setupSecret, $issuer);

$mfaEnabledAt = is_array($mfaAccount) ? BMMfa::GetEnabledAtTimestamp($mfaAccount) : 0;

$tpl->assign('mfaAccount', $mfaAccount);
$tpl->assign('mfaMandatory', $mfaMandatory);
$tpl->assign('mfaCanUseEmail', $mfaCanUseEmail);
$tpl->assign('mfaAltMailMasked', $mfaAdminEmail !== false ? BMMfa::MaskEmail($mfaAdminEmail) : '');
$tpl->assign('mfaSetupMethod', $mfaSetupMethod);
$tpl->assign('mfaPrefsSwitch', $mfaPrefsSwitch);
$tpl->assign('mfaActiveMethod', is_array($mfaAccount) ? BMMfa::ActiveMethod($mfaAccount) : '');
$tpl->assign('mfaEnabledAtFormatted', $mfaEnabledAt > 0 ? FormatDate($mfaEnabledAt) : '');
$tpl->assign('mfaQrSvg', $mfaSetupMethod === 'totp' ? BMMfa::QrSvg($uri) : '');
$tpl->assign('mfaSecretManual', $setupSecret);
$tpl->assign('mfaSwitchAction', 'admins.php?action=account');
$tpl->assign('mfaSwitchDo', 'mfaSave');
$tpl->assign('mfaAdminMayManage', true);
?>