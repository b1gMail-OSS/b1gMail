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

define('INTERFACE_MODE', true);
include('../serverlib/init.inc.php');
include('../serverlib/payment.class.php');

PutLog(sprintf('IPN: <%s> Called',
		$_SERVER['REMOTE_ADDR']),
	PRIO_DEBUG,
	__FILE__,
	__LINE__);

//
// check input
//
if(!isset($_POST['item_name']) || !isset($_POST['item_number']) || !isset($_POST['payment_status'])
	|| !isset($_POST['mc_gross']) || !isset($_POST['mc_currency']) || !isset($_POST['txn_id'])
	|| !isset($_POST['receiver_email']) || !isset($_POST['payer_email']) || !isset($_POST['invoice']))
{
	PutLog(sprintf('IPN: <%s> Missing input variables',
			$_SERVER['REMOTE_ADDR']),
		PRIO_DEBUG,
		__FILE__,
		__LINE__);
	die('Error: One or more missing input variables.');
}

//
// read the post from PayPal system and add 'cmd'
//
$req = 'cmd=_notify-validate';
foreach($_POST as $key=>$value)
	$req .= sprintf('&%s=%s', urlencode($key), urlencode($value));
$header  = 'POST /cgi-bin/webscr HTTP/1.1' . "\r\n";
$header .= 'Host: www.paypal.com' . "\r\n";
$header .= 'Connection: close' . "\r\n";
$header .= 'Content-Type: application/x-www-form-urlencoded' . "\r\n";
$header .= 'Content-Length: ' . strlen($req) . "\r\n";
$header .= 'User-Agent: b1gMail/' . B1GMAIL_VERSION . "\r\n\r\n";

//
// open connection
//
$fp = fsockopen('ssl://www.paypal.com', 443, $errNo, $errStr, SOCKET_TIMEOUT);

//
// import vars
//
$paypal_mail 		= $bm_prefs['paypal_mail'];
$item_name 			= $_POST['item_name'];
$item_number 		= $_POST['item_number'];
$payment_status 	= $_POST['payment_status'];
$payment_amount 	= $_POST['mc_gross'];
$payment_currency 	= $_POST['mc_currency'];
$txn_id 			= $_POST['txn_id'];
$receiver_email 	= $_POST['receiver_email'];
$payer_email 		= $_POST['payer_email'];
$invoice 			= $_POST['invoice'];

//
// submit request
//
if(!$fp)
{
	// log
	PutLog(sprintf('PayPal payment (%d): Could not connect to www.paypal.com:80 (%d, %s)',
		$invoice,
		$errNo,
		$errStr),
		PRIO_ERROR,
		__FILE__,
		__LINE__);
}
else
{
	fputs($fp, $header . $req);
	PutLog(sprintf('IPN: <%s> Request posted',
			$_SERVER['REMOTE_ADDR']),
		PRIO_DEBUG,
		__FILE__,
		__LINE__);

	while(!feof($fp))
	{
		$res = fgets2($fp);
		if(trim($res) == 'VERIFIED')
		{
			if(trim($payment_status) == 'Completed')
			{
				// check that this txn_id has not been previously processed
				$res = $db->Query('SELECT `orderid` FROM {pre}orders WHERE `txnid`=?',
					$txn_id);
				if($res->RowCount() == 0)
				{
					// check that the receiver_email is the paypal mail address
					if(trim(strtolower($receiver_email)) == trim(strtolower($paypal_mail)))
					{
						// check currency
						if(trim(strtolower($payment_currency)) == trim(strtolower($bm_prefs['currency'])))
						{
							// set txn_id
							$db->Query('UPDATE {pre}orders SET `txnid`=? WHERE `orderid`=?',
								$txn_id,
								$invoice);

							// activate order
							if(BMPayment::ActivateOrder($invoice, round($payment_amount, 2)*100))
							{
								PutLog(sprintf('PayPal payment (%d, %s) accepted',
											   $invoice,
											   $txn_id),
									   PRIO_NOTE,
									   __FILE__,
									   __LINE__);
							}
							else
							{
								PutLog(sprintf('PayPal payment (%d, %s) rejected by BMPayment API',
											   $invoice,
											   $txn_id),
									   PRIO_WARNING,
									   __FILE__,
									   __LINE__);
							}
						}
						else
						{
							// log
							PutLog(sprintf('PayPal payment (%d): Invalid currency <%s> != <%s>',
								$invoice,
								$payment_currency,
								$bm_prefs['currency']),
								PRIO_WARNING,
								__FILE__,
								__LINE__);
						}
					}
					else
					{
						// log
						PutLog(sprintf('PayPal payment (%d): Payment receiver is not <%s>',
							$invoice,
							$receiver_email),
							PRIO_WARNING,
							__FILE__,
							__LINE__);
					}
				}
				else
				{
					// log
					PutLog(sprintf('PayPal payment (%d): txn_id has already been processed (%s)',
						$invoice,
						$txn_id),
						PRIO_WARNING,
						__FILE__,
						__LINE__);
					$res->Free();
				}
			}
			else
			{
				PutLog(sprintf('IPN: <%s> Payment not completed',
						$_SERVER['REMOTE_ADDR']),
					PRIO_DEBUG,
					__FILE__,
					__LINE__);
			}
		}
		else if(trim($res) == 'INVALID')
		{
			// log
			PutLog(sprintf('PayPal payment (%d): Got INVALID while trying to validate payment (%s)',
				$invoice,
				trim($txn_id) != '' ? $txn_id : 'n/a'),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
		}
	}
	fclose($fp);

	PutLog(sprintf('IPN: <%s> Request answer processed',
			$_SERVER['REMOTE_ADDR']),
		PRIO_DEBUG,
		__FILE__,
		__LINE__);
}
