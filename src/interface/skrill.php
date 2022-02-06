<?php
/*
 * b1gMail
 * (c) 2021 Patrick Schlangen et al
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

PutLog(sprintf('Skrill: <%s> Called',
		$_SERVER['REMOTE_ADDR']),
	PRIO_DEBUG,
	__FILE__,
	__LINE__);

//
// check input
//
if(!isset($_POST['pay_to_email']) || !isset($_POST['transaction_id']) || !isset($_POST['mb_transaction_id'])
	|| !isset($_POST['pay_from_email']) || !isset($_POST['merchant_id']) || !isset($_POST['mb_amount'])
	|| !isset($_POST['mb_currency']) || !isset($_POST['status']) || !isset($_POST['md5sig'])
	|| !isset($_POST['amount']) || !isset($_POST['currency']))
{
	PutLog(sprintf('Skrill: <%s> Missing input variables',
			$_SERVER['REMOTE_ADDR']),
		PRIO_DEBUG,
		__FILE__,
		__LINE__);
	die('Error: One or more missing input variables.');
}

//
// check signature
//
$mySig = md5($_POST['merchant_id'] . $_POST['transaction_id'] . strtoupper(md5($bm_prefs['skrill_secret']))
			. $_POST['mb_amount'] . $_POST['mb_currency'] . $_POST['status']);
if(strtolower($mySig) !== strtolower($_POST['md5sig']))
{
	PutLog(sprintf('Skrill: <%s> Signature comparison failed (invalid secret word?)',
			$_SERVER['REMOTE_ADDR']),
		PRIO_WARNING,
		__FILE__,
		__LINE__);
	die('Error: Invalid signature.');
}

//
// check account
//
if($_POST['pay_to_email'] != $bm_prefs['skrill_mail'])
{
	PutLog(sprintf('Skrill: <%s> Payment receiver <%s> does not match our expectation <%s>',
			$_SERVER['REMOTE_ADDR'],
			$_POST['pay_to_email'],
			$bm_prefs['skrill_mail']),
		PRIO_WARNING,
		__FILE__,
		__LINE__);
	die('Error: Invalid payment receiver.');
}

//
// check currency
//
if($_POST['currency'] != $bm_prefs['currency'])
{
	PutLog(sprintf('Skrill: <%s> Wrong currency (%s != %s)',
			$_SERVER['REMOTE_ADDR'],
			$_POST['currency'],
			$bm_prefs['currency']),
		PRIO_WARNING,
		__FILE__,
		__LINE__);
	die('Error: Invalid currency.');
}

//
// check status
//
if($_POST['status'] == '2')
{
	if(BMPayment::ActivateOrder($_POST['transaction_id'], round($_POST['amount'], 2)*100))
	{
		PutLog(sprintf('Skrill payment (%d) accepted',
					   $_POST['transaction_id']),
			   PRIO_NOTE,
			   __FILE__,
			   __LINE__);
		die('OK');
	}
	else
	{
		PutLog(sprintf('Skrill payment (%d) rejected by BMPayment API',
					   $_POST['transaction_id']),
			   PRIO_WARNING,
			   __FILE__,
			   __LINE__);
		die('Error: Order activation failed');
	}
}
else
{
	PutLog(sprintf('Skrill: <%s> Payment status <%s>',
			$_SERVER['REMOTE_ADDR'],
			$_POST['status']),
		PRIO_DEBUG,
		__FILE__,
		__LINE__);
	die('OK');
}
