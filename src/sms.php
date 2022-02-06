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
include('./serverlib/sms.class.php');
RequestPrivileges(PRIVILEGES_USER);

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));

/**
 * SMS enabled?
 */
if(!$thisUser->SMSEnabled())
{
	header('Location: start.php?sid=' . session_id());
	exit();
}

/**
 * default action = compose
 */
$tpl->addJSFile('li', $tpl->tplDir . 'js/sms.js');
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'compose';
$tpl->assign('activeTab', 'sms');

/**
 * sms interface
 */
$sms = _new('BMSMS', array($userRow['id'], &$thisUser));

/**
 * page menu
 */
$tpl->assign('pageMenuFile', 'li/sms.sidebar.tpl');
$tpl->assign('pageToolbarFile', 'li/sms.toolbar.tpl');
$tpl->assign('accBalance', $thisUser->GetBalance());

$validationRequired = $groupRow['smsvalidation'] == 'yes' && $userRow['sms_validation'] == 0;

/**
 * compose
 */
if($validationRequired && $_REQUEST['action'] != 'outbox')
{
	$enterCode = ($userRow['sms_validation_code'] != '' && $userRow['sms_validation_time'] > 0);

	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'validate' && $enterCode
		&& isset($_REQUEST['sms_validation_code']))
	{
		if($thisUser->ValidateMobileNo($_REQUEST['sms_validation_code']))
		{
			header('Location: sms.php?sid=' . session_id());
			exit();
		}
		else
			$tpl->assign('error', true);
	}

	$tpl->assign('enterCode', $enterCode);
	$tpl->assign('pageContent', 'li/sms.validate.tpl');
	$tpl->display('li/index.tpl');
}
else if($_REQUEST['action'] == 'compose')
{
	// safe code?
	if($groupRow['sms_send_code'] == 'yes')
	{
		if(!class_exists('BMCaptcha'))
			include(B1GMAIL_DIR . 'serverlib/captcha.class.php');
		$captcha = BMCaptcha::createDefaultProvider();
		$tpl->assign('captchaHTML', $captcha->getHTML());
		$tpl->assign('captchaInfo', $captcha->getInfo());
	}

	// sender?
	if($groupRow['sms_ownfrom'] == 'yes')
	{
		$tpl->assign('ownFrom', true);
		$tpl->assign('smsFrom', $userRow['mail2sms_nummer']);
	}
	else
	{
		$tpl->assign('ownFrom', false);
		$tpl->assign('smsFrom', $groupRow['sms_from']);
	}

	// use validated no as sender?
	if($groupRow['smsvalidation'] == 'yes'
		&& $userRow['sms_validation'] > 0)
	{
		$tpl->assign('ownFrom', false);
		$tpl->assign('smsFrom', $userRow['mail2sms_nummer']);
	}

	// page output
	$tpl->assign('pageTitle', $lang_user['sendsms']);
	$tpl->assign('smsTypes', $sms->GetTypes());
	$tpl->assign('smsTo', isset($_REQUEST['to']) ? $_REQUEST['to'] : '');
	$tpl->assign('pageContent', 'li/sms.compose.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * send SMS
 */
else if($_REQUEST['action'] == 'sendSMS'
		&& IsPOSTRequest())
{
	$captcha = false;
	if($groupRow['sms_send_code'] == 'yes')
	{
		if(!class_exists('BMCaptcha'))
			include(B1GMAIL_DIR . 'serverlib/captcha.class.php');
		$captcha = BMCaptcha::createDefaultProvider();
	}

	// safecode?
	if($captcha !== false && !$captcha->check())
	{
		$tpl->assign('msg', $lang_user['invalidcode']);
		$tpl->assign('pageContent', 'li/error.tpl');
	}
	else
	{
		// get params
		$fromNo = $groupRow['smsvalidation'] == 'yes' && $userRow['sms_validation'] > 0
			? $userRow['mail2sms_nummer']
			: ($groupRow['sms_ownfrom'] == 'yes'
				? SmartyCellphoneNo('from')
				: $groupRow['sms_from']);
		$toNo = SmartyCellphoneNo('to');
		$typeID = (int)$_REQUEST['type'];
		$text = $_REQUEST['smsText'];

		// check pre
		if(!BMSMS::PreOK($toNo, $groupRow['sms_pre'])
			|| ($groupRow['sms_ownfrom'] == 'yes' && !BMSMS::PreOK($fromNo, $groupRow['sms_pre'])))
		{
			$result = false;
		}
		else
		{
			// add signature
			if(_strlen($text) > $sms->GetMaxChars($typeID))
				$text = _substr($text, 0, $sms->GetMaxChars($typeID));
			$text .= $groupRow['sms_sig'];

			// send
			$result = $sms->Send($fromNo, $toNo, $text, $typeID, true, true);
		}

		if($result)
		{
			$tpl->assign('accBalance', $thisUser->GetBalance());
			$tpl->assign('title', $lang_user['sendsms']);
			$tpl->assign('msg', $lang_user['smssent']);
			$tpl->assign('backLink', 'sms.php?sid=' . session_id());
			$tpl->assign('pageContent', 'li/msg.tpl');
		}
		else
		{
			$tpl->assign('msg', $lang_user['smssendfailed']);
			$tpl->assign('pageContent', 'li/error.tpl');
		}
	}

	$tpl->assign('pageTitle', $lang_user['sendsms']);
	$tpl->display('li/index.tpl');
}

/**
 * outbox
 */
else if($_REQUEST['action'] == 'outbox')
{
	// delete?
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'delete'
		&& isset($_REQUEST['id']))
	{
		$sms->DeleteOutboxEntry((int)$_REQUEST['id']);
	}

	// mass delete?
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'action'
			&& isset($_REQUEST['do2']) && $_REQUEST['do2'] == 'delete')
	{
		foreach($_POST as $key=>$val)
			if(substr($key, 0, 4) == 'sms_')
			{
				$id = (int)substr($key, 4);
				$sms->DeleteOutboxEntry($id);
			}
	}

	$sortColumns = array('from', 'to', 'date');

	// get sort info
	$sortColumn = (isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], $sortColumns))
					? $_REQUEST['sort']
					: 'date';
	$sortOrder = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')))
					? $_REQUEST['order']
					: 'desc';

	// page output
	$tpl->assign('pageTitle', $lang_user['smsoutbox']);
	$tpl->assign('outbox', $sms->GetOutbox($sortColumn, $sortOrder));
	$tpl->assign('sortColumn', $sortColumn);
	$tpl->assign('sortOrder', $sortOrder);
	$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
	$tpl->assign('pageContent', 'li/sms.outbox.tpl');
	$tpl->display('li/index.tpl');
}
?>