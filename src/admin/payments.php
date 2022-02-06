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
include('../serverlib/payment.class.php');
RequestPrivileges(PRIVILEGES_ADMIN);
AdminRequirePrivilege('payments');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'payments';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['payments'],
		'relIcon'	=> 'ico_prefs_payments.png',
		'link'		=> 'payments.php?',
		'active'	=> $_REQUEST['action'] == 'payments'
	),
	1 => array(
		'title'		=> $lang_admin['export2'],
		'relIcon'	=> 'ico_accentries.png',
		'link'		=> 'payments.php?action=export&',
		'active'	=> $_REQUEST['action'] == 'export'
	)
);

/**
 * payments
 */
if($_REQUEST['action'] == 'payments')
{
	if(!isset($_REQUEST['do']))
		$_REQUEST['do'] = 'list';

	//
	// list
	//
	if($_REQUEST['do'] == 'list')
	{
		// single action
		if(isset($_REQUEST['singleAction'])
			&& in_array($_REQUEST['singleAction'], array('download', 'activate', 'delete')))
		{
			$_REQUEST['executeMassAction'] = true;
			$_REQUEST['massAction'] = $_REQUEST['singleAction'];
			$_POST['payment'] = array((int)$_REQUEST['singleID'] => true);
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get payment IDs
			$paymentIDs = isset($_POST['payment']) ? $_POST['payment'] : array();
			if(!is_array($paymentIDs))
				$paymentIDs = array();
			else
				$paymentIDs = array_keys($paymentIDs);

			if(count($paymentIDs) > 0)
			{
				// delete payments
				if($_REQUEST['massAction'] == 'delete')
				{
					$db->Query('DELETE FROM {pre}orders WHERE `orderid` IN ?',
						$paymentIDs);
					$db->Query('DELETE FROM {pre}invoices WHERE `orderid` IN ?',
						$paymentIDs);
				}

				// activate payments
				else if($_REQUEST['massAction'] == 'activate')
				{
					$res = $db->Query('SELECT `orderid`,`amount` FROM {pre}orders WHERE `orderid` IN ? AND `status`=?',
									  $paymentIDs,
									  ORDER_STATUS_CREATED);
					while($row = $res->FetchArray(MYSQLI_ASSOC))
						BMPayment::ActivateOrder($row['orderid'], $row['amount']);
					$res->Free();
				}

				// download invoices
				else if($_REQUEST['massAction'] == 'download')
				{
					if(!class_exists('BMZIP'))
						include(B1GMAIL_DIR . 'serverlib/zip.class.php');

					// create zip archive
					$tempID = RequestTempFile(0);
					$tempFileName = TempFileName($tempID);
					$invTempID = RequestTempFile(0);
					$invTempFileName = TempFileName($invTempID);
					$fp = fopen($tempFileName, 'wb+');
					$zip = _new('BMZIP', array($fp));

					// fetch invoices
					$res = $db->Query('SELECT `orderid`,`invoice` FROM {pre}invoices WHERE `orderid` IN ?',
						$paymentIDs);
					while($row = $res->FetchArray(MYSQLI_ASSOC))
					{
						$invFP = fopen($invTempFileName, 'w+');
						fprintf($invFP, '<meta http-equiv="content-type" content="text/html; charset=%s" />',
								$currentCharset);
						fprintf($invFP, '<title>%s</title>', BMPayment::InvoiceNo($row['orderid']));
						fwrite($invFP, $row['invoice']);
						fclose($invFP);

						$zip->AddFile($invTempFileName, sprintf('%s.html', BMPayment::InvoiceNo($row['orderid'])));
					}
					$res->Free();

					// finish
					$size = $zip->Finish();

					// headers
					header('Pragma: public');
					header('Content-Disposition: attachment; filename="invoices.zip"');
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
					ReleaseTempFile(0, $invTempID);
					exit();
				}
			}
		}


		// sort options
		$sortBy = isset($_REQUEST['sortBy'])
					? $_REQUEST['sortBy']
					: 'created';
		$sortOrder = isset($_REQUEST['sortOrder'])
						? strtolower($_REQUEST['sortOrder'])
						: 'desc';
		$perPage = max(1, isset($_REQUEST['perPage'])
						? (int)$_REQUEST['perPage']
						: 50);

		// filter stuff
		$payMethods = BMPayment::GetCustomPaymentMethods();
		$statusIDs = array(0 => true, 1 => true);
		$paymentMethods = array(0 => true, 1 => true, 2 => true, 3 => true);
		foreach($payMethods as $methodID=>$method)
			$paymentMethods[-$methodID] = true;
		$queryAdd = '';
		if(isset($_REQUEST['filter']))
		{
			if(!isset($_REQUEST['status']))
				$_REQUEST['status'] = array();
			$statusIDs = $_REQUEST['status'];
			$queryStatus = count(array_keys($statusIDs)) > 0 ? implode(',', array_keys($statusIDs)) : '-1';
			$queryAdd = 'WHERE `status` IN(' . $queryStatus . ') ';

			if(!isset($_REQUEST['paymentMethod']))
				$_REQUEST['paymentMethod'] = array();
			$paymentMethods = $_REQUEST['paymentMethod'];
			$queryPaymentMethods = count(array_keys($paymentMethods)) > 0 ? implode(',', array_keys($paymentMethods)) : '-1';
			$queryAdd .= 'AND `paymethod` IN(' . $queryPaymentMethods . ') ';
		}

		// page calculation
		$res = $db->Query('SELECT COUNT(*) FROM {pre}orders ' . $queryAdd);
		list($paymentCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		$pageCount = ceil($paymentCount / $perPage);
		$pageNo = isset($_REQUEST['page'])
					? max(1, min($pageCount, (int)$_REQUEST['page']))
					: 1;
		$startPos = max(0, min($perPage*($pageNo-1), $paymentCount));

		// fetch rows
		$users = array();
		$payments = array();
		$res = $db->Query('SELECT * FROM {pre}orders ' . $queryAdd . ' ORDER BY ' . $sortBy . ' ' . $sortOrder . ' LIMIT ' . $startPos . ',' . $perPage);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if(!isset($users[$row['userid']]))
			{
				$res2 = $db->Query('SELECT `id`,`email`,`vorname`,`nachname` FROM {pre}users WHERE `id`=?',
								   $row['userid']);
				$users[$row['userid']] = $res2->FetchArray(MYSQLI_ASSOC);
				$res2->Free();
			}

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

			$row['user'] = $users[$row['userid']];
			$row['customerNo'] = BMPayment::CustomerNo($row['userid']);
			$row['invoiceNo'] = BMPayment::InvoiceNo($row['orderid']);
			$row['amount'] = sprintf('%.02f %s', $row['amount']/100, $bm_prefs['currency']);

			$payments[] = $row;
		}
		$res->Free();

		// assign
		$tpl->assign('payMethods',		$payMethods);
		$tpl->assign('payments',		$payments);
		$tpl->assign('pageNo', 			$pageNo);
		$tpl->assign('pageCount', 		$pageCount);
		$tpl->assign('sortBy', 			$sortBy);
		$tpl->assign('sortOrder', 		$sortOrder);
		$tpl->assign('sortOrderInv', 	$sortOrder == 'asc' ? 'desc' : 'asc');
		$tpl->assign('perPage', 		$perPage);
		$tpl->assign('paymentMethod', 	$paymentMethods);
		$tpl->assign('status', 			$statusIDs);
		$tpl->assign('bm_prefs', 		$bm_prefs);
		$tpl->assign('page', 			'payments.list.tpl');
	}

	//
	// details
	//
	else if($_REQUEST['do'] == 'details' && isset($_REQUEST['orderid']))
	{
		$payMethods = BMPayment::GetCustomPaymentMethods();

		$res = $db->Query('SELECT * FROM {pre}orders WHERE `orderid`=?',
			(int)$_REQUEST['orderid']);
		if($res->RowCount() != 1)
			die('Payment not found.');
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$row['paymethod_params'] = @unserialize($row['paymethod_params']);
		if(!is_array($row['paymethod_params']))
			$row['paymethod_params'] = array();

		// get user details
		$res = $db->Query('SELECT `id`,`email`,`vorname`,`nachname` FROM {pre}users WHERE `id`=?',
						   $row['userid']);
		$row['user'] = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// generate customer/invoice no, format amount
		$row['customerNo'] 	= BMPayment::CustomerNo($row['userid']);
		$row['invoiceNo'] 	= BMPayment::InvoiceNo($row['orderid']);
		$row['amount'] 		= sprintf('%.02f %s', $row['amount']/100, $bm_prefs['currency']);

		// get payment method title
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

		// invoice?
		$res = $db->Query('SELECT COUNT(*) FROM {pre}invoices WHERE `orderid`=?',
						   $row['orderid']);
		list($row['hasInvoice']) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		$tpl->assign('payment', 	$row);
		$tpl->assign('page',		'payments.details.tpl');
	}
}

/**
 * export
 */
else if($_REQUEST['action'] == 'export')
{
	if(!isset($_REQUEST['do']))
	{
		$tpl->assign('paymentMethods', 	BMPayment::GetCustomPaymentMethods());
		$tpl->assign('start', 			mktime(0, 0, 0, date('m', time()-TIME_ONE_MONTH), 1, date('Y', time()-TIME_ONE_MONTH)));
		$tpl->assign('end',				mktime(23, 59, 59, date('m', time()-TIME_ONE_MONTH), date('t', time()-TIME_ONE_MONTH), date('Y', time()-TIME_ONE_MONTH)));
		$tpl->assign('page', 			'payments.export.tpl');
	}

	else if($_REQUEST['do'] == 'exportAccEntries'
			&& isset($_REQUEST['startDay'])
			&& isset($_REQUEST['endDay']))
	{
		$from = SmartyDateTime('start');
		$to   = SmartyDateTime('end') + TIME_ONE_DAY;

		// headers
		header('Pragma: public');
		header('Content-Disposition: attachment; filename="acc_entries.csv"');
		header('Content-Type: text/csv');

		$res = $db->Query('SELECT `orderid`,`amount`,`tax`,`orderid`,`paymethod`,`activated`,`cart` FROM {pre}orders WHERE `status`=1 AND `activated`>=? AND `activated`<=? ORDER BY `activated` ASC',
						  $from,
						  $to);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$desc = '';
			$cart = @unserialize($row['cart']);
			if(!is_array($cart) || count($cart) == 0)
			{
				$desc = 'Order';
			}
			else
			{
				$desc = array();
				foreach($cart as $cartItem)
					$desc[] = $cartItem['text'];
				$desc = implode(', ', $desc);
			}

			printf("%s;%s;%s;%.02f;%04d;%04d;%s\n",
				   date('d.m.Y', $row['activated']),
				   str_replace(';', ',', $desc),
				   BMPayment::InvoiceNo($row['orderid']),
				   round($row['amount']/100, 2),
				   $_REQUEST['account'],
				   $_REQUEST['accounts'][$row['paymethod']],
				   $row['tax'] == 0 ? '-' : sprintf('USt%d', $row['tax']));
		}
		$res->Free();

		exit();
	}

	else if($_REQUEST['do'] == 'exportInvoices'
			&& isset($_REQUEST['startDay'])
			&& isset($_REQUEST['endDay']))
	{
		$from = SmartyDateTime('start');
		$to   = SmartyDateTime('end') + TIME_ONE_DAY;

		if(!class_exists('BMZIP'))
			include(B1GMAIL_DIR . 'serverlib/zip.class.php');

		// create zip archive
		$tempID = RequestTempFile(0);
		$tempFileName = TempFileName($tempID);
		$invTempID = RequestTempFile(0);
		$invTempFileName = TempFileName($invTempID);
		$fp = fopen($tempFileName, 'wb+');
		$zip = _new('BMZIP', array($fp));

		// fetch invoices
		if(isset($_REQUEST['paidOnly']))
		{
			$res = $db->Query('SELECT {pre}orders.`orderid`,{pre}invoices.`invoice` FROM {pre}orders,{pre}invoices WHERE {pre}orders.`activated`>=? AND {pre}orders.`activated`<=? AND {pre}invoices.`orderid`={pre}orders.`orderid`',
							  $from,
							  $to);
		}
		else
		{
			$res = $db->Query('SELECT {pre}orders.`orderid`,{pre}invoices.`invoice` FROM {pre}orders,{pre}invoices WHERE (({pre}orders.`status`=1 AND {pre}orders.`activated`>=? AND {pre}orders.`activated`<=?) OR ({pre}orders.`status`=0 AND {pre}orders.`created`>=? AND {pre}orders.`created`<=?)) AND {pre}invoices.`orderid`={pre}orders.`orderid`',
							  $from,
							  $to,
							  $from,
							  $to);
		}
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$invFP = fopen($invTempFileName, 'w+');
			fprintf($invFP, '<meta http-equiv="content-type" content="text/html; charset=%s" />',
					$currentCharset);
			fprintf($invFP, '<title>%s</title>', BMPayment::InvoiceNo($row['orderid']));
			fwrite($invFP, $row['invoice']);
			fclose($invFP);

			$zip->AddFile($invTempFileName, sprintf('%s.html', BMPayment::InvoiceNo($row['orderid'])));
		}
		$res->Free();

		// finish
		$size = $zip->Finish();

		// headers
		header('Pragma: public');
		header('Content-Disposition: attachment; filename="invoices.zip"');
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
		ReleaseTempFile(0, $invTempID);
		exit();
	}
}

/**
 * show invoice
 */
else if($_REQUEST['action'] == 'showInvoice'
		&& isset($_REQUEST['orderID']))
{
	$res = $db->Query('SELECT `invoice` FROM {pre}invoices WHERE `orderid`=?',
					  $_REQUEST['orderID']);
	if($res->RowCount() == 0)
		die('Invoice not found');
	list($invoice) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();

	printf('<title>%s</title>', BMPayment::InvoiceNo($_REQUEST['orderID']));
	echo($invoice);
	exit();
}

/**
 * activate payment RPC
 */
else if($_REQUEST['action'] == 'activatePayment'
		&& isset($_REQUEST['vkCode'])
		&& isset($_REQUEST['amount']))
{
	if(!empty($_REQUEST['vkCode']) && !empty($_REQUEST['amount']) && BMPayment::ActivateOrderWithVKCode($_REQUEST['vkCode'], round((float)str_replace(',', '.', $_REQUEST['amount'])*100, 0)))
		printf('OK:%s', $lang_admin['activate_ok']);
	else
		printf('ERROR:%s', $lang_admin['activate_err']);
	exit();
}


$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['usersgroups'] . ' &raquo; ' . $lang_admin['payments']);
$tpl->display('page.tpl');
?>