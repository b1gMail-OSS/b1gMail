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
	die('Directly calling this file is not supported');

/**
 * Stripe Checkout (Payment Intents / Checkout Sessions)
 */
class BMStripe
{
	/**
	 * Prefs helper with defaults for pre-migration installs
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function Pref($key, $default = '')
	{
		global $bm_prefs;

		return isset($bm_prefs[$key]) ? $bm_prefs[$key] : $default;
	}

	/**
	 * Stripe checkout enabled and secret key present?
	 *
	 * @return bool
	 */
	public static function IsConfigured()
	{
		return self::Pref('enable_stripe', 'no') == 'yes'
			&& trim((string)self::Pref('stripe_secret_key')) != '';
	}

	/**
	 * Auto-activate after confirmed payment?
	 *
	 * @return bool
	 */
	public static function AutoActivate()
	{
		return self::Pref('stripe_auto_activate', 'yes') == 'yes';
	}

	/**
	 * REST API base URL
	 *
	 * @return string
	 */
	public static function ApiBase()
	{
		return 'https://api.stripe.com';
	}

	/**
	 * Public webhook listener URL
	 *
	 * @return string
	 */
	public static function WebhookUrl()
	{
		global $bm_prefs;

		return rtrim($bm_prefs['selfurl'], '/') . '/interface/stripe_webhook.php';
	}

	/**
	 * Public return URL for a b1gMail order
	 *
	 * @param int $orderID
	 * @param bool $cancel
	 * @return string
	 */
	public static function ReturnUrl($orderID, $cancel = false)
	{
		global $bm_prefs;

		$url = sprintf('%sinterface/stripe_return.php?oid=%d',
			$bm_prefs['selfurl'],
			(int)$orderID);
		if($cancel)
			$url .= '&cancel=1';
		else
			$url .= '&session_id={CHECKOUT_SESSION_ID}';
		return $url;
	}

	/**
	 * Amount in Stripe's smallest currency unit
	 *
	 * @param int $amountCents Internal amount in cents
	 * @param string $currency
	 * @return int
	 */
	public static function FormatAmount($amountCents, $currency)
	{
		$currency = strtoupper($currency);
		$zeroDecimal = array(
			'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA',
			'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'
		);
		if(in_array($currency, $zeroDecimal, true))
			return (int)round($amountCents / 100);
		return (int)$amountCents;
	}

	/**
	 * Stripe amount (smallest unit) to internal cents
	 *
	 * @param int $value
	 * @param string $currency
	 * @return int
	 */
	public static function AmountToCents($value, $currency)
	{
		$currency = strtoupper($currency);
		$zeroDecimal = array(
			'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA',
			'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'
		);
		if(in_array($currency, $zeroDecimal, true))
			return (int)round((int)$value * 100);
		return (int)$value;
	}

	/**
	 * Create Checkout Session and return hosted URL
	 *
	 * @param int $orderID
	 * @param array $orderRow
	 * @param string $returnUrl Customer return URL after checkout
	 * @return string|false
	 */
	public static function StartCheckout($orderID, $orderRow, $returnUrl)
	{
		global $bm_prefs;

		if(!self::IsConfigured())
			return false;

		$params = @unserialize($orderRow['paymethod_params']);
		if(!is_array($params))
			$params = array();

		$params['return_url'] = $returnUrl;

		if(!empty($params['stripe_session_id']))
		{
			$existing = self::GetSession($params['stripe_session_id']);
			if(is_array($existing))
			{
				$status = isset($existing['status']) ? $existing['status'] : '';
				$payStatus = isset($existing['payment_status']) ? $existing['payment_status'] : '';
				if($status == 'complete' && ($payStatus == 'paid' || $payStatus == 'no_payment_required'))
				{
					self::FinalizeFromSession($orderID, $existing, 'reuse');
					return false;
				}
				if($status == 'open' && !empty($existing['url']))
				{
					self::StoreParams($orderID, $params);
					return $existing['url'];
				}
			}
		}

		$cart = @unserialize($orderRow['cart']);
		if(!is_array($cart))
			$cart = array();
		$names = array();
		foreach($cart as $item)
		{
			if(isset($item['text']))
				$names[] = $item['text'];
		}
		$desc = substr(implode(', ', $names), 0, 250);
		if($desc == '')
			$desc = BMPayment::InvoiceNo($orderID);

		$currency = strtoupper($bm_prefs['currency']);
		$methods = array('card');
		if(self::Pref('stripe_enable_twint', 'yes') == 'yes' && $currency == 'CHF')
			$methods[] = 'twint';

		$body = array(
			'mode' => 'payment',
			'success_url' => self::ReturnUrl($orderID),
			'cancel_url' => self::ReturnUrl($orderID, true),
			'client_reference_id' => (string)$orderID,
			'payment_method_types' => $methods,
			'line_items' => array(
				array(
					'quantity' => 1,
					'price_data' => array(
						'currency' => strtolower($currency),
						'unit_amount' => self::FormatAmount($orderRow['amount'], $currency),
						'product_data' => array(
							'name' => $desc
						)
					)
				)
			),
			'metadata' => array(
				'order_id' => (string)$orderID,
				'invoice_no' => BMPayment::InvoiceNo($orderID)
			),
			'payment_intent_data' => array(
				'description' => $desc,
				'metadata' => array(
					'order_id' => (string)$orderID,
					'invoice_no' => BMPayment::InvoiceNo($orderID)
				)
			)
		);

		if(!empty($orderRow['userid']))
		{
			$user = _new('BMUser', array((int)$orderRow['userid']));
			if(is_object($user) && is_array($user->_row) && !empty($user->_row['email']))
				$body['customer_email'] = $user->_row['email'];
		}

		$idem = 'bm-order-' . (int)$orderID . '-' . (int)$orderRow['amount'];
		if(!empty($params['stripe_session_id']))
			$idem .= '-r' . substr(md5($params['stripe_session_id']), 0, 8);

		$res = self::ApiRequest('POST', '/v1/checkout/sessions', $body, array(
			'Idempotency-Key: ' . $idem
		));
		if((!$res['ok'] || empty($res['data']['url'])) && count($methods) > 1)
		{
			PutLog(sprintf('Stripe Checkout: session with TWINT failed for order %d (HTTP %d), retrying with card only',
					$orderID,
					$res['status']),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			$body['payment_method_types'] = array('card');
			$res = self::ApiRequest('POST', '/v1/checkout/sessions', $body, array(
				'Idempotency-Key: ' . $idem . '-card'
			));
		}
		if(!$res['ok'] || !is_array($res['data']) || empty($res['data']['id']) || empty($res['data']['url']))
		{
			$err = '';
			if(is_array($res['data']) && isset($res['data']['error']['message']))
				$err = ' ' . $res['data']['error']['message'];
			PutLog(sprintf('Stripe Checkout: failed to create session for order %d (HTTP %d)%s',
					$orderID,
					$res['status'],
					$err),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		$params['stripe_session_id'] = $res['data']['id'];
		if(!empty($res['data']['payment_intent']) && is_string($res['data']['payment_intent']))
			$params['stripe_payment_intent'] = $res['data']['payment_intent'];
		self::StoreParams($orderID, $params);

		return $res['data']['url'];
	}

	/**
	 * Confirm payment after buyer return
	 *
	 * @param int $orderID
	 * @param string $sessionID
	 * @return bool
	 */
	public static function CaptureReturn($orderID, $sessionID = '')
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}orders WHERE `orderid`=?',
			(int)$orderID);
		if($res->RowCount() != 1)
			return false;
		$order = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		if($order['paymethod'] != PAYMENT_METHOD_STRIPE)
			return false;

		$params = @unserialize($order['paymethod_params']);
		if(!is_array($params))
			$params = array();

		if($sessionID == '' && !empty($params['stripe_session_id']))
			$sessionID = $params['stripe_session_id'];
		if($sessionID == '')
			return false;

		$session = self::GetSession($sessionID);
		if(!is_array($session))
			return false;

		$customID = self::OrderIdFromSession($session);
		if($customID > 0 && $customID != (int)$orderID)
		{
			PutLog(sprintf('Stripe Checkout: order id mismatch (%d != %d)',
					$customID,
					$orderID),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		$payStatus = isset($session['payment_status']) ? $session['payment_status'] : '';
		if($payStatus == 'paid' || $payStatus == 'no_payment_required')
			return self::FinalizeFromSession($orderID, $session, 'return');

		return false;
	}

	/**
	 * Handle webhook JSON payload
	 *
	 * @param array $event
	 * @return bool
	 */
	public static function HandleWebhookEvent($event)
	{
		$type = isset($event['type']) ? $event['type'] : '';
		$obj = isset($event['data']['object']) && is_array($event['data']['object'])
			? $event['data']['object']
			: array();

		if($type == 'checkout.session.completed'
			|| $type == 'checkout.session.async_payment_succeeded')
		{
			$orderID = self::OrderIdFromSession($obj);
			$sessionID = isset($obj['id']) ? $obj['id'] : '';
			if($orderID <= 0 && $sessionID != '')
				$orderID = self::FindOrderBySessionId($sessionID);
			if($orderID <= 0)
			{
				PutLog('Stripe webhook: could not map checkout session to order',
					PRIO_WARNING,
					__FILE__,
					__LINE__);
				return false;
			}

			$payStatus = isset($obj['payment_status']) ? $obj['payment_status'] : '';
			if($payStatus != 'paid' && $payStatus != 'no_payment_required'
				&& $type != 'checkout.session.async_payment_succeeded')
				return true;

			return self::FinalizeFromSession($orderID, $obj, 'webhook');
		}

		if($type == 'payment_intent.succeeded')
		{
			$piId = isset($obj['id']) ? $obj['id'] : '';
			$orderID = 0;
			if(!empty($obj['metadata']['order_id']))
				$orderID = (int)$obj['metadata']['order_id'];
			if($orderID <= 0 && $piId != '')
				$orderID = self::FindOrderByPaymentIntent($piId);
			if($orderID <= 0)
			{
				PutLog('Stripe webhook: could not map payment_intent to order',
					PRIO_WARNING,
					__FILE__,
					__LINE__);
				return false;
			}

			$currency = isset($obj['currency']) ? $obj['currency'] : '';
			$paid = isset($obj['amount_received'])
				? self::AmountToCents($obj['amount_received'], $currency)
				: (isset($obj['amount']) ? self::AmountToCents($obj['amount'], $currency) : 0);
			$txn = $piId;
			if(!empty($obj['latest_charge']) && is_string($obj['latest_charge']))
				$txn = $obj['latest_charge'];

			return self::FinalizePayment($orderID, $txn, $paid, $currency, 'webhook-pi');
		}

		return true;
	}

	/**
	 * Verify Stripe-Signature header
	 *
	 * @param array $headers
	 * @param string $rawBody
	 * @return bool
	 */
	public static function VerifyWebhook($headers, $rawBody)
	{
		$secret = trim((string)self::Pref('stripe_webhook_secret'));
		if($secret == '')
		{
			PutLog('Stripe webhook: missing webhook secret',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		$header = self::HeaderVal($headers, 'STRIPE-SIGNATURE');
		if($header == '')
			return false;

		$timestamp = '';
		$signatures = array();
		foreach(explode(',', $header) as $part)
		{
			$kv = explode('=', trim($part), 2);
			if(count($kv) != 2)
				continue;
			if($kv[0] == 't')
				$timestamp = $kv[1];
			else if($kv[0] == 'v1')
				$signatures[] = $kv[1];
		}

		if($timestamp == '' || count($signatures) == 0)
			return false;

		if(abs(time() - (int)$timestamp) > 300)
		{
			PutLog('Stripe webhook: signature timestamp outside tolerance',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		$expected = hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret);
		foreach($signatures as $sig)
		{
			if(hash_equals($expected, $sig))
				return true;
		}

		return false;
	}

	/**
	 * Create or reuse webhook endpoint; returns array(id, secret)|false
	 *
	 * @return array|false
	 */
	public static function EnsureWebhook()
	{
		if(!self::IsConfigured())
			return false;

		$url = self::WebhookUrl();
		if(stripos($url, 'https://') !== 0)
		{
			PutLog('Stripe webhook: selfurl must be HTTPS to register a webhook',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		$storedId = trim((string)self::Pref('stripe_webhook_id'));
		$storedSecret = trim((string)self::Pref('stripe_webhook_secret'));
		$events = array(
			'checkout.session.completed',
			'checkout.session.async_payment_succeeded',
			'payment_intent.succeeded'
		);

		if($storedId != '')
		{
			$existing = self::ApiRequest('GET', '/v1/webhook_endpoints/' . rawurlencode($storedId));
			if($existing['ok'] && is_array($existing['data'])
				&& isset($existing['data']['url']) && $existing['data']['url'] == $url
				&& (!isset($existing['data']['status']) || $existing['data']['status'] == 'enabled'))
			{
				if($storedSecret != '')
					return array('id' => $storedId, 'secret' => $storedSecret);
			}
		}

		$list = self::ApiRequest('GET', '/v1/webhook_endpoints?limit=100');
		if($list['ok'] && is_array($list['data']) && isset($list['data']['data']) && is_array($list['data']['data']))
		{
			foreach($list['data']['data'] as $hook)
			{
				if(isset($hook['url'], $hook['id']) && $hook['url'] == $url)
				{
					if($storedSecret != '')
						return array('id' => $hook['id'], 'secret' => $storedSecret);
					PutLog('Stripe webhook: endpoint exists but signing secret is unknown',
						PRIO_NOTE,
						__FILE__,
						__LINE__);
					return false;
				}
			}
		}

		$created = self::ApiRequest('POST', '/v1/webhook_endpoints', array(
			'url' => $url,
			'enabled_events' => $events
		));
		if($created['ok'] && is_array($created['data']) && !empty($created['data']['id']))
		{
			return array(
				'id' => $created['data']['id'],
				'secret' => isset($created['data']['secret']) ? $created['data']['secret'] : $storedSecret
			);
		}

		PutLog(sprintf('Stripe webhook: registration failed (HTTP %d)',
				$created['status']),
			PRIO_WARNING,
			__FILE__,
			__LINE__);
		return false;
	}

	/**
	 * Redirect buyer back to the original return URL
	 *
	 * @param array $order
	 */
	public static function RedirectToReturn($order)
	{
		global $bm_prefs;

		$params = @unserialize($order['paymethod_params']);
		$url = (is_array($params) && !empty($params['return_url']))
			? $params['return_url']
			: $bm_prefs['selfurl'] . 'prefs.php?action=paymentReturn';
		header('Location: ' . $url);
		exit;
	}

	/**
	 * GET Checkout Session
	 *
	 * @param string $sessionID
	 * @return array|false
	 */
	public static function GetSession($sessionID)
	{
		$res = self::ApiRequest('GET', '/v1/checkout/sessions/' . rawurlencode($sessionID));
		if(!$res['ok'] || !is_array($res['data']))
			return false;
		return $res['data'];
	}

	/**
	 * Finalize a captured payment
	 *
	 * @param int $orderID
	 * @param string $txnId
	 * @param int $paidCents
	 * @param string $currency
	 * @param string $source
	 * @return bool
	 */
	public static function FinalizePayment($orderID, $txnId, $paidCents, $currency, $source)
	{
		global $db, $bm_prefs;

		$res = $db->Query('SELECT `amount`,`status`,`paymethod`,`txnid` FROM {pre}orders WHERE `orderid`=?',
			(int)$orderID);
		if($res->RowCount() != 1)
			return false;
		$order = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		if($order['paymethod'] != PAYMENT_METHOD_STRIPE)
			return false;

		if($order['status'] == ORDER_STATUS_ACTIVATED)
			return true;

		if($order['status'] != ORDER_STATUS_CREATED)
			return false;

		$expectedCurrency = strtoupper($bm_prefs['currency']);
		if($currency != '' && strtoupper($currency) != $expectedCurrency)
		{
			PutLog(sprintf('Stripe Checkout (%d): currency %s != %s',
					$orderID,
					$currency,
					$expectedCurrency),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		if($paidCents < (int)$order['amount'])
		{
			PutLog(sprintf('Stripe Checkout (%d): paid amount %d < order amount %d',
					$orderID,
					$paidCents,
					$order['amount']),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		if($txnId != '' && $order['txnid'] != '' && $order['txnid'] != $txnId)
		{
			PutLog(sprintf('Stripe Checkout (%d): txnid already set to %s',
					$orderID,
					$order['txnid']),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		if($txnId != '' && ($order['txnid'] == '' || $order['txnid'] != $txnId))
		{
			$db->Query('UPDATE {pre}orders SET `txnid`=? WHERE `orderid`=?',
				$txnId,
				(int)$orderID);
		}

		if(!self::AutoActivate())
		{
			PutLog(sprintf('Stripe Checkout (%d, %s) recorded without auto-activate (%s)',
					$orderID,
					$txnId,
					$source),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			return true;
		}

		if(!class_exists('BMPayment'))
			include(B1GMAIL_DIR . 'serverlib/payment.class.php');

		if(BMPayment::ActivateOrder($orderID, $paidCents))
		{
			PutLog(sprintf('Stripe Checkout (%d, %s) accepted (%s)',
					$orderID,
					$txnId,
					$source),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			return true;
		}

		PutLog(sprintf('Stripe Checkout (%d, %s) rejected by BMPayment (%s)',
				$orderID,
				$txnId,
				$source),
			PRIO_WARNING,
			__FILE__,
			__LINE__);
		return false;
	}

	private static function FinalizeFromSession($orderID, $session, $source)
	{
		$currency = isset($session['currency']) ? $session['currency'] : '';
		$paid = 0;
		if(isset($session['amount_total']))
			$paid = self::AmountToCents($session['amount_total'], $currency);

		$txnId = '';
		if(!empty($session['payment_intent']) && is_string($session['payment_intent']))
			$txnId = $session['payment_intent'];
		else if(isset($session['id']))
			$txnId = $session['id'];

		return self::FinalizePayment($orderID, $txnId, $paid, $currency, $source);
	}

	private static function OrderIdFromSession($session)
	{
		if(!empty($session['client_reference_id']))
			return (int)$session['client_reference_id'];
		if(!empty($session['metadata']['order_id']))
			return (int)$session['metadata']['order_id'];
		return 0;
	}

	private static function StoreParams($orderID, $params)
	{
		global $db;

		$db->Query('UPDATE {pre}orders SET `paymethod_params`=? WHERE `orderid`=?',
			serialize($params),
			(int)$orderID);
	}

	private static function FindOrderBySessionId($sessionID)
	{
		return self::FindOrderByParam('stripe_session_id', $sessionID);
	}

	private static function FindOrderByPaymentIntent($piId)
	{
		return self::FindOrderByParam('stripe_payment_intent', $piId);
	}

	private static function FindOrderByParam($key, $value)
	{
		global $db;

		$res = $db->Query('SELECT `orderid`,`paymethod_params` FROM {pre}orders WHERE `paymethod`=? AND `status`=? ORDER BY `orderid` DESC LIMIT 50',
			PAYMENT_METHOD_STRIPE,
			ORDER_STATUS_CREATED);
		$orderID = 0;
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$params = @unserialize($row['paymethod_params']);
			if(is_array($params) && isset($params[$key]) && $params[$key] == $value)
			{
				$orderID = (int)$row['orderid'];
				break;
			}
		}
		$res->Free();
		return $orderID;
	}

	private static function HeaderVal($headers, $name)
	{
		$want = strtolower($name);
		foreach($headers as $key => $val)
		{
			if(strtolower($key) == $want)
				return $val;
		}
		$serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
		return isset($_SERVER[$serverKey]) ? $_SERVER[$serverKey] : '';
	}

	private static function ApiRequest($method, $path, $body = null, $extraHeaders = array())
	{
		$secret = trim((string)self::Pref('stripe_secret_key'));
		if($secret == '')
			return array('ok' => false, 'status' => 0, 'data' => null, 'raw' => '');

		$headers = array_merge(array(
			'Authorization: Bearer ' . $secret,
			'Accept: application/json'
		), $extraHeaders);

		$payload = null;
		if($body !== null && $method != 'GET')
		{
			$headers[] = 'Content-Type: application/x-www-form-urlencoded';
			$payload = http_build_query($body);
		}

		return self::Http($method, self::ApiBase() . $path, $payload, $headers);
	}

	private static function Http($method, $url, $body, $headers)
	{
		$status = 0;
		$raw = '';
		$ok = false;

		if(function_exists('curl_init'))
		{
			$ch = curl_init($url);
			$opts = array(
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_HTTPHEADER => $headers,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_PROTOCOLS => defined('CURLPROTO_HTTPS') ? CURLPROTO_HTTPS : 2
			);
			if($body !== null && $method != 'GET')
				$opts[CURLOPT_POSTFIELDS] = $body;
			curl_setopt_array($ch, $opts);
			$raw = curl_exec($ch);
			$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$curlErr = curl_error($ch);
			curl_close($ch);
			if($raw === false)
			{
				$raw = '';
				PutLog(sprintf('Stripe HTTP: curl error for %s: %s',
						$method,
						$curlErr),
					PRIO_WARNING,
					__FILE__,
					__LINE__);
			}
			$ok = $status >= 200 && $status < 300;
		}
		else
		{
			$ctx = stream_context_create(array(
				'http' => array(
					'method' => $method,
					'header' => implode("\r\n", $headers) . "\r\n",
					'content' => ($body !== null && $method != 'GET') ? $body : '',
					'timeout' => 30,
					'ignore_errors' => true
				)
			));
			$raw = @file_get_contents($url, false, $ctx);
			if($raw === false)
				$raw = '';
			if(isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m))
				$status = (int)$m[1];
			$ok = $status >= 200 && $status < 300;
		}

		$data = json_decode($raw, true);
		if(!is_array($data))
			$data = null;

		return array(
			'ok' => $ok,
			'status' => $status,
			'data' => $data,
			'raw' => $raw
		);
	}
}
