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
if(!defined('B1GMAIL_INIT'))
	require '../serverlib/init.inc.php';
include('../serverlib/payment.class.php');
include('../serverlib/stripe.class.php');

$orderID = isset($_REQUEST['oid']) ? (int)$_REQUEST['oid'] : 0;
if($orderID <= 0)
{
	PutLog(sprintf('Stripe return: missing order id from <%s>',
			$_SERVER['REMOTE_ADDR']),
		PRIO_DEBUG,
		__FILE__,
		__LINE__);
	header('Location: ' . $bm_prefs['selfurl']);
	exit;
}

$res = $db->Query('SELECT * FROM {pre}orders WHERE `orderid`=?',
	$orderID);
if($res->RowCount() != 1)
{
	$res->Free();
	header('Location: ' . $bm_prefs['selfurl']);
	exit;
}
$order = $res->FetchArray(MYSQLI_ASSOC);
$res->Free();

if(isset($_REQUEST['cancel']))
{
	PutLog(sprintf('Stripe return: order %d cancelled by buyer',
			$orderID),
		PRIO_DEBUG,
		__FILE__,
		__LINE__);
	BMStripe::RedirectToReturn($order);
}

$sessionID = isset($_REQUEST['session_id']) ? trim($_REQUEST['session_id']) : '';
BMStripe::CaptureReturn($orderID, $sessionID);

$res = $db->Query('SELECT * FROM {pre}orders WHERE `orderid`=?',
	$orderID);
$order = $res->FetchArray(MYSQLI_ASSOC);
$res->Free();
BMStripe::RedirectToReturn($order);
