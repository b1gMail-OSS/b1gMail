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
include('../serverlib/paypal.class.php');

$raw = file_get_contents('php://input');
PutLog(sprintf('PayPal webhook: <%s> called (%d bytes)',
		$_SERVER['REMOTE_ADDR'],
		strlen($raw)),
	PRIO_DEBUG,
	__FILE__,
	__LINE__);

if($raw == '')
{
	header('HTTP/1.1 400 Bad Request');
	echo 'Empty body';
	exit;
}

$event = json_decode($raw, true);
if(!is_array($event))
{
	header('HTTP/1.1 400 Bad Request');
	echo 'Invalid JSON';
	exit;
}

$headers = array();
if(function_exists('getallheaders'))
{
	$all = getallheaders();
	if(is_array($all))
		$headers = $all;
}
if(count($headers) == 0)
{
	foreach($_SERVER as $key => $val)
	{
		if(strpos($key, 'HTTP_') === 0)
			$headers[str_replace('_', '-', substr($key, 5))] = $val;
	}
}

if(!BMPayPal::VerifyWebhook($headers, $raw, $event))
{
	PutLog(sprintf('PayPal webhook: signature check failed (%s)',
			isset($event['event_type']) ? $event['event_type'] : 'n/a'),
		PRIO_WARNING,
		__FILE__,
		__LINE__);
	header('HTTP/1.1 400 Bad Request');
	echo 'Invalid signature';
	exit;
}

BMPayPal::HandleWebhookEvent($event);
header('HTTP/1.1 200 OK');
echo 'OK';
