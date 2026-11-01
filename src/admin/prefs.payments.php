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
AdminRequirePrivilege('prefs.payments');

function fieldSort($a, $b)
{
	return($a['pos'] - $b['pos']);
}

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'common';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['common'],
		'relIcon'	=> 'ico_prefs_payments.png',
		'link'		=> 'prefs.payments.php?',
		'active'	=> $_REQUEST['action'] == 'common'
	),
	1 => array(
		'title'		=> $lang_admin['paymentmethods'],
		'relIcon'	=> 'ico_pay_banktransfer.png',
		'link'		=> 'prefs.payments.php?action=paymethods&',
		'active'	=> $_REQUEST['action'] == 'paymethods'
	),
	2 => array(
		'title'		=> $lang_admin['invoices'],
		'relIcon'	=> 'ico_prefs_invoices.png',
		'link'		=> 'prefs.payments.php?action=invoices&',
		'active'	=> $_REQUEST['action'] == 'invoices'
	)
);

/**
 * common
 */
if($_REQUEST['action'] == 'common')
{
	if(!class_exists('BMPayment'))
		include(B1GMAIL_DIR . 'serverlib/payment.class.php');
	BMPayment::EnsureGatewayColumns();

	// sofortueberweisung.de return
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'suBack'
		&& isset($_REQUEST['user_id'])
		&& isset($_REQUEST['project_id'])
		&& isset($_REQUEST['prjPass'])
		&& isset($_REQUEST['notifyPass']))
	{
		echo '<script>' . "\n";
		echo '<!--' . "\n";
		printf(' try { window.opener.parent.frames[\'content\'].EBID(\'su_kdnr\').value=\'%s\';' . "\n",
			addslashes($_REQUEST['user_id']));
		printf(' window.opener.parent.frames[\'content\'].EBID(\'su_prjnr\').value=\'%s\';' . "\n",
			addslashes($_REQUEST['project_id']));
		printf(' window.opener.parent.frames[\'content\'].EBID(\'su_prjpass\').value=\'%s\';' . "\n",
			addslashes($_REQUEST['prjPass']));
		printf(' window.opener.parent.frames[\'content\'].EBID(\'su_notifypass\').value=\'%s\';' . "\n",
			addslashes($_REQUEST['notifyPass']));
		printf(' window.opener.parent.frames[\'content\'].EBID(\'su_enable\').checked = true;' . "\n");
		printf(' window.opener.parent.frames[\'content\'].EBID(\'su_inputcheck\').checked = true;' . "\n");
		printf(' window.opener.parent.frames[\'content\'].EBID(\'prefsForm\').submit();' . "\n");
		echo ' } catch(e) { } window.close();' . "\n";
		echo '//-->' . "\n";
		echo '</script>' . "\n";
		exit();
	}

	// save?
	if(isset($_REQUEST['save']))
	{
		$paypalSecret = isset($_REQUEST['paypal_client_secret']) ? trim($_REQUEST['paypal_client_secret']) : '';
		if($paypalSecret === '')
			$paypalSecret = isset($bm_prefs['paypal_client_secret']) ? $bm_prefs['paypal_client_secret'] : '';

		$paypalWebhookId = isset($_REQUEST['paypal_webhook_id']) ? trim($_REQUEST['paypal_webhook_id']) : '';

		$stripeSecret = isset($_REQUEST['stripe_secret_key']) ? trim($_REQUEST['stripe_secret_key']) : '';
		if($stripeSecret === '')
			$stripeSecret = isset($bm_prefs['stripe_secret_key']) ? $bm_prefs['stripe_secret_key'] : '';

		$stripeWebhookSecret = isset($_REQUEST['stripe_webhook_secret']) ? trim($_REQUEST['stripe_webhook_secret']) : '';
		if($stripeWebhookSecret === '')
			$stripeWebhookSecret = isset($bm_prefs['stripe_webhook_secret']) ? $bm_prefs['stripe_webhook_secret'] : '';

		$stripeWebhookId = isset($_REQUEST['stripe_webhook_id']) ? trim($_REQUEST['stripe_webhook_id']) : '';

		$paypalSurchargePercent = BMPayment::NormalizeSurchargePercent(isset($_REQUEST['paypal_surcharge_percent']) ? $_REQUEST['paypal_surcharge_percent'] : 0);
		$paypalSurchargeFixed = BMPayment::NormalizeSurchargeFixed(isset($_REQUEST['paypal_surcharge_fixed']) ? $_REQUEST['paypal_surcharge_fixed'] : 0);
		$stripeSurchargePercent = BMPayment::NormalizeSurchargePercent(isset($_REQUEST['stripe_surcharge_percent']) ? $_REQUEST['stripe_surcharge_percent'] : 0);
		$stripeSurchargeFixed = BMPayment::NormalizeSurchargeFixed(isset($_REQUEST['stripe_surcharge_fixed']) ? $_REQUEST['stripe_surcharge_fixed'] : 0);

		$db->Query('UPDATE {pre}prefs SET `currency`=?,`send_pay_notification`=?,`pay_notification_to`=?,`pay_emailfrom`=?,`pay_emailfromemail`=?,`mwst`=?,`enable_paypal`=?,`paypal_mail`=?,`enable_paypal_checkout`=?,`paypal_client_id`=?,`paypal_client_secret`=?,`paypal_sandbox`=?,`paypal_auto_activate`=?,`paypal_webhook_id`=?,`paypal_surcharge`=?,`paypal_surcharge_percent`=?,`paypal_surcharge_fixed`=?,`paypal_surcharge_cover`=?,`enable_stripe`=?,`stripe_secret_key`=?,`stripe_webhook_secret`=?,`stripe_webhook_id`=?,`stripe_auto_activate`=?,`stripe_enable_twint`=?,`stripe_surcharge`=?,`stripe_surcharge_percent`=?,`stripe_surcharge_fixed`=?,`stripe_surcharge_cover`=?,`enable_su`=?,`su_kdnr`=?,`su_prjnr`=?,`su_prjpass`=?,`su_notifypass`=?,`su_inputcheck`=?,`enable_vk`=?,`vk_kto_inh`=?,`vk_kto_nr`=?,`vk_kto_blz`=?,`vk_kto_inst`=?,`vk_kto_iban`=?,`vk_kto_bic`=?,`default_paymethod`=?,`enable_skrill`=?,`skrill_mail`=?,`skrill_secret`=?',
			$_REQUEST['currency'],
			isset($_REQUEST['send_pay_notification']) ? 'yes' : 'no',
			$_REQUEST['pay_notification_to'],
			$_REQUEST['pay_emailfrom'],
			EncodeEMail($_REQUEST['pay_emailfromemail']),
			$_REQUEST['mwst'],
			isset($_REQUEST['enable_paypal']) ? 'yes' : 'no',
			$_REQUEST['paypal_mail'],
			isset($_REQUEST['enable_paypal_checkout']) ? 'yes' : 'no',
			isset($_REQUEST['paypal_client_id']) ? trim($_REQUEST['paypal_client_id']) : '',
			$paypalSecret,
			isset($_REQUEST['paypal_sandbox']) ? 'yes' : 'no',
			isset($_REQUEST['paypal_auto_activate']) ? 'yes' : 'no',
			$paypalWebhookId,
			isset($_REQUEST['paypal_surcharge']) ? 'yes' : 'no',
			$paypalSurchargePercent,
			$paypalSurchargeFixed,
			isset($_REQUEST['paypal_surcharge_cover']) ? 'yes' : 'no',
			isset($_REQUEST['enable_stripe']) ? 'yes' : 'no',
			$stripeSecret,
			$stripeWebhookSecret,
			$stripeWebhookId,
			isset($_REQUEST['stripe_auto_activate']) ? 'yes' : 'no',
			isset($_REQUEST['stripe_enable_twint']) ? 'yes' : 'no',
			isset($_REQUEST['stripe_surcharge']) ? 'yes' : 'no',
			$stripeSurchargePercent,
			$stripeSurchargeFixed,
			isset($_REQUEST['stripe_surcharge_cover']) ? 'yes' : 'no',
			isset($_REQUEST['enable_su']) ? 'yes' : 'no',
			$_REQUEST['su_kdnr'],
			$_REQUEST['su_prjnr'],
			$_REQUEST['su_prjpass'],
			$_REQUEST['su_notifypass'],
			isset($_REQUEST['su_inputcheck']) ? 'yes' : 'no',
			isset($_REQUEST['enable_vk']) ? 'yes' : 'no',
			$_REQUEST['vk_kto_inh'],
			$_REQUEST['vk_kto_nr'],
			$_REQUEST['vk_kto_blz'],
			$_REQUEST['vk_kto_inst'],
			$_REQUEST['vk_kto_iban'],
			$_REQUEST['vk_kto_bic'],
			(int)$_REQUEST['default_paymethod'],
			isset($_REQUEST['enable_skrill']) ? 'yes' : 'no',
			$_REQUEST['skrill_mail'],
			$_REQUEST['skrill_secret']);
		ReadConfig();

		$paypalWebhookMsg = '';
		$paypalWebhookOk = false;
		if(isset($bm_prefs['enable_paypal_checkout']) && $bm_prefs['enable_paypal_checkout'] == 'yes'
			&& trim((string)$bm_prefs['paypal_client_id']) != ''
			&& trim((string)$bm_prefs['paypal_client_secret']) != '')
		{
			if(!class_exists('BMPayPal'))
				include(B1GMAIL_DIR . 'serverlib/paypal.class.php');
			$hookId = BMPayPal::EnsureWebhook();
			if($hookId)
			{
				if($hookId != $bm_prefs['paypal_webhook_id'])
				{
					$db->Query('UPDATE {pre}prefs SET `paypal_webhook_id`=?',
						$hookId);
					ReadConfig();
				}
				$paypalWebhookOk = true;
				$paypalWebhookMsg = $lang_admin['paypal_webhook_ok'];
			}
			else
			{
				$paypalWebhookMsg = $lang_admin['paypal_webhook_err'];
			}
		}
		$tpl->assign('paypalWebhookOk', $paypalWebhookOk);
		$tpl->assign('paypalWebhookMsg', $paypalWebhookMsg);

		$stripeWebhookMsg = '';
		$stripeWebhookOk = false;
		if(isset($bm_prefs['enable_stripe']) && $bm_prefs['enable_stripe'] == 'yes'
			&& trim((string)$bm_prefs['stripe_secret_key']) != '')
		{
			if(!class_exists('BMStripe'))
				include(B1GMAIL_DIR . 'serverlib/stripe.class.php');
			$hook = BMStripe::EnsureWebhook();
			if(is_array($hook) && !empty($hook['id']))
			{
				$needUpdate = false;
				if($hook['id'] != $bm_prefs['stripe_webhook_id'])
					$needUpdate = true;
				if(!empty($hook['secret']) && $hook['secret'] != $bm_prefs['stripe_webhook_secret'])
					$needUpdate = true;
				if($needUpdate)
				{
					$db->Query('UPDATE {pre}prefs SET `stripe_webhook_id`=?,`stripe_webhook_secret`=?',
						$hook['id'],
						!empty($hook['secret']) ? $hook['secret'] : $bm_prefs['stripe_webhook_secret']);
					ReadConfig();
				}
				$stripeWebhookOk = true;
				$stripeWebhookMsg = $lang_admin['stripe_webhook_ok'];
			}
			else
			{
				$stripeWebhookMsg = $lang_admin['stripe_webhook_err'];
			}
		}
		$tpl->assign('stripeWebhookOk', $stripeWebhookOk);
		$tpl->assign('stripeWebhookMsg', $stripeWebhookMsg);
	}

	// assign
	$tpl->assign('prjPass', 	substr(GenerateRandomKey('sofortueberweisungProjectPassword'), 0, 20));
	$tpl->assign('notifyPass', 	substr(GenerateRandomKey('sofortueberweisungNotifyPassword'), 0, 20));
	$tpl->assign('paypalWebhookUrl', rtrim($bm_prefs['selfurl'], '/') . '/interface/paypal_webhook.php');
	$tpl->assign('stripeWebhookUrl', rtrim($bm_prefs['selfurl'], '/') . '/interface/stripe_webhook.php');
	if($tpl->getTemplateVars('paypalWebhookMsg') === null)
	{
		$tpl->assign('paypalWebhookMsg', '');
		$tpl->assign('paypalWebhookOk', false);
	}
	if($tpl->getTemplateVars('stripeWebhookMsg') === null)
	{
		$tpl->assign('stripeWebhookMsg', '');
		$tpl->assign('stripeWebhookOk', false);
	}
	$tpl->assign('page', 		'prefs.payments.common.tpl');
	$tpl->assign('title', 		$lang_admin['payments'] . ' &raquo; ' . $lang_admin['common']);
}

/**
 * custom payment methods
 */
else if($_REQUEST['action'] == 'paymethods')
{
	//
	// list
	//
	if(!isset($_REQUEST['do']))
	{
		// delete?
		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}paymethods WHERE `methodid`=?',
				(int)$_REQUEST['delete']);
		}

		// mass delete?
		if(isset($_REQUEST['massAction']) && $_REQUEST['massAction'] == 'delete')
		{
			$ids = array();

			foreach($_POST as $key=>$val)
			{
				if(substr($key, 0, 7) == 'method_')
					$ids[] = (int)substr($key, 7);
			}

			if(count($ids) > 0)
				$db->Query('DELETE FROM {pre}paymethods WHERE `methodid` IN ?', $ids);
		}

		// enable?
		if(isset($_REQUEST['enable']))
		{
			$db->Query('UPDATE {pre}paymethods SET `enabled`=1 WHERE `methodid`=?',
				$_REQUEST['enable']);
		}

		// disable?
		if(isset($_REQUEST['disable']))
		{
			$db->Query('UPDATE {pre}paymethods SET `enabled`=0 WHERE `methodid`=?',
				$_REQUEST['disable']);
		}

		// add?
		if(isset($_REQUEST['add']) && isset($_POST['title']))
		{
			$db->Query('INSERT INTO {pre}paymethods(`title`,`fields`) VALUES(?,?)',
				$_POST['title'],
				serialize(array()));
			$id = $db->InsertId();
			header('Location: prefs.payments.php?action=paymethods&do=edit&methodid='.$id.'&sid='.session_id());
			exit();
		}

		$methods = array();
		$res = $db->Query('SELECT `methodid`,`enabled`,`title` FROM {pre}paymethods ORDER BY `methodid` ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$methods[$row['methodid']] = $row;
		}
		$res->Free();

		$tpl->assign('methods',		$methods);
		$tpl->assign('page', 		'prefs.payments.methods.tpl');
		$tpl->assign('title', 		$lang_admin['payments'] . ' &raquo; ' . $lang_admin['paymentmethods']);
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit' && isset($_REQUEST['methodid']))
	{
		$id = (int)$_REQUEST['methodid'];

		$res = $db->Query('SELECT * FROM {pre}paymethods WHERE `methodid`=?', $id);
		if($res->RowCount() != 1)
			die('Payment method not found.');
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$fields = @unserialize($row['fields']);
		if(!is_array($fields)) $fields = array();

		uasort($fields, 'fieldSort');

		if(isset($_REQUEST['save']) && isset($_POST['fields']) && is_array($_POST['fields']))
		{
			$fields = array();

			// build new fields array
			foreach($_POST['fields'] as $fieldID=>$fieldInfo)
			{
				if(isset($fieldInfo['delete']))
					continue;

				if($fieldID == 'new' && empty($fieldInfo['title']))
					continue;

				$fields[] = $fieldInfo;
			}

			$row['enabled'] = isset($_REQUEST['enabled']) ? 1 : 0;
			$row['invoice'] = $_REQUEST['invoice'];
			$row['title'] = $_REQUEST['title'];

			$db->Query('UPDATE {pre}paymethods SET `title`=?,`fields`=?,`enabled`=?,`invoice`=? WHERE `methodid`=?',
				$row['title'],
				serialize($fields),
				$row['enabled'],
				$row['invoice'],
				$id);
		}

		$tpl->assign('row',				$row);
		$tpl->assign('fields',			$fields);
		$tpl->assign('fieldTypeTable', 	$fieldTypeTable);
		$tpl->assign('page', 			'prefs.payments.methods.edit.tpl');
		$tpl->assign('title', 			$lang_admin['payments'] . ' &raquo; ' . $lang_admin['paymentmethods']
										. ' &raquo; ' . HTMLFormat($row['title']));
	}
}

/**
 * invoices
 */
else if($_REQUEST['action'] == 'invoices')
{
	if(isset($_POST['resetTemplate']))
	{
		$defaultFile = B1GMAIL_DIR . 'setup/rgtemplate.tpl';
		$defaultInvoice = is_readable($defaultFile) ? file_get_contents($defaultFile) : false;
		if(!is_string($defaultInvoice) || $defaultInvoice === '')
		{
			$tpl->assign('rgTemplateResetError', true);
		}
		else
		{
			$db->Query('UPDATE {pre}prefs SET `rgtemplate`=?', $defaultInvoice);
			ReadConfig();
			header('Location: prefs.payments.php?action=invoices&reset=ok&sid=' . session_id());
			exit();
		}
	}
	else if(isset($_REQUEST['save']))
	{
		$db->Query('UPDATE {pre}prefs SET `sendrg`=?,`rgnrfmt`=?,`kdnrfmt`=?,`rgtemplate`=?',
			isset($_REQUEST['sendrg']) ? 'yes' : 'no',
			$_REQUEST['rgnrfmt'],
			$_REQUEST['kdnrfmt'],
			$_REQUEST['rgtemplate']);
		ReadConfig();
	}

	$tpl->assign('rgTemplateReset', isset($_REQUEST['reset']) && $_REQUEST['reset'] === 'ok');

	// assign
	$tpl->assign('usertpldir', 	B1GMAIL_REL . 'templates/' . $bm_prefs['template'] . '/');
	$tpl->assign('page', 		'prefs.payments.invoices.tpl');
	$tpl->assign('title', 		$lang_admin['payments'] . ' &raquo; ' . $lang_admin['invoices']);
}

$tpl->assign('bm_prefs', 	$bm_prefs);
$tpl->assign('tabs', 		$tabs);
$tpl->display('page.tpl');
?>