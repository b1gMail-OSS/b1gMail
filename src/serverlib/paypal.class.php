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
 * PayPal Checkout (Orders API v2)
 */
class BMPayPal
{
	private static $_token = null;
	private static $_tokenExp = 0;

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
	 * REST checkout enabled and credentials present?
	 *
	 * @return bool
	 */
	public static function IsConfigured()
	{
		return self::Pref('enable_paypal_checkout', 'no') == 'yes'
			&& trim((string)self::Pref('paypal_client_id')) != ''
			&& trim((string)self::Pref('paypal_client_secret')) != '';
	}

	/**
	 * Checkout auto-activate after capture?
	 *
	 * @return bool
	 */
	public static function AutoActivate()
	{
		return self::Pref('paypal_auto_activate', 'yes') == 'yes';
	}

	/**
	 * REST API base URL
	 *
	 * @return string
	 */
	public static function ApiBase()
	{
		return self::Pref('paypal_sandbox', 'no') == 'yes'
			? 'https://api-m.sandbox.paypal.com'
			: 'https://api-m.paypal.com';
	}

	/**
	 * Public webhook listener URL
	 *
	 * @return string
	 */
	public static function WebhookUrl()
	{
		global $bm_prefs;

		return rtrim($bm_prefs['selfurl'], '/') . '/interface/paypal_webhook.php';
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

		$url = sprintf('%sinterface/paypal_return.php?oid=%d',
			$bm_prefs['selfurl'],
			(int)$orderID);
		if($cancel)
			$url .= '&cancel=1';
		return $url;
	}

	/**
	 * Format amount for PayPal
	 *
	 * @param int $amountCents
	 * @param string $currency
	 * @return string
	 */
	public static function FormatAmount($amountCents, $currency)
	{
		$currency = strtoupper($currency);
		$zeroDecimal = array('JPY', 'HUF', 'TWD');
		if(in_array($currency, $zeroDecimal, true))
			return (string)(int)round($amountCents / 100);
		return number_format($amountCents / 100, 2, '.', '');
	}

	/**
	 * Amount string to cents
	 *
	 * @param string $value
	 * @return int
	 */
	public static function AmountToCents($value)
	{
		return (int)round((float)str_replace(',', '.', (string)$value) * 100);
	}

	/**
	 * Create PayPal order and return approve URL
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

		if(!empty($params['paypal_order_id']))
		{
			$existing = self::GetOrder($params['paypal_order_id']);
			if(is_array($existing))
			{
				$status = isset($existing['status']) ? $existing['status'] : '';
				if($status == 'COMPLETED')
				{
					self::FinalizeFromOrderPayload($orderID, $existing, 'reuse');
					return false;
				}
				if($status == 'APPROVED' || $status == 'CREATED' || $status == 'PAYER_ACTION_REQUIRED')
				{
					$approve = self::ApproveLink($existing);
					if($approve != '')
					{
						self::StoreParams($orderID, $params);
						return $approve;
					}
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
		$desc = substr(implode(', ', $names), 0, 127);
		if($desc == '')
			$desc = BMPayment::InvoiceNo($orderID);

		$currency = strtoupper($bm_prefs['currency']);
		$body = array(
			'intent' => 'CAPTURE',
			'purchase_units' => array(
				array(
					'reference_id' => (string)$orderID,
					'invoice_id' => BMPayment::InvoiceNo($orderID),
					'custom_id' => (string)$orderID,
					'description' => $desc,
					'amount' => array(
						'currency_code' => $currency,
						'value' => self::FormatAmount($orderRow['amount'], $currency)
					)
				)
			),
			'application_context' => array(
				'brand_name' => substr((string)$bm_prefs['pay_emailfrom'], 0, 127),
				'shipping_preference' => 'NO_SHIPPING',
				'user_action' => 'PAY_NOW',
				'return_url' => self::ReturnUrl($orderID),
				'cancel_url' => self::ReturnUrl($orderID, true)
			)
		);

		$res = self::ApiRequest('POST', '/v2/checkout/orders', $body, array(
			'PayPal-Request-Id: bm-order-' . (int)$orderID
		));
		if(!$res['ok'] || !is_array($res['data']) || empty($res['data']['id']))
		{
			PutLog(sprintf('PayPal Checkout: failed to create order %d (HTTP %d)',
					$orderID,
					$res['status']),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		$params['paypal_order_id'] = $res['data']['id'];
		self::StoreParams($orderID, $params);

		$approve = self::ApproveLink($res['data']);
		if($approve == '')
		{
			PutLog(sprintf('PayPal Checkout: no approve URL for order %d',
					$orderID),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		return $approve;
	}

	/**
	 * Capture after buyer return
	 *
	 * @param int $orderID
	 * @param string $paypalOrderID
	 * @return bool
	 */
	public static function CaptureReturn($orderID, $paypalOrderID = '')
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}orders WHERE `orderid`=?',
			(int)$orderID);
		if($res->RowCount() != 1)
			return false;
		$order = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		if($order['paymethod'] != PAYMENT_METHOD_PAYPAL_CHECKOUT)
			return false;

		$params = @unserialize($order['paymethod_params']);
		if(!is_array($params))
			$params = array();

		if($paypalOrderID == '' && !empty($params['paypal_order_id']))
			$paypalOrderID = $params['paypal_order_id'];
		if($paypalOrderID == '')
			return false;

		$ppOrder = self::GetOrder($paypalOrderID);
		if(!is_array($ppOrder))
			return false;

		$customID = self::CustomIdFromOrder($ppOrder);
		if($customID != '' && (int)$customID != (int)$orderID)
		{
			PutLog(sprintf('PayPal Checkout: custom_id mismatch (%s != %d)',
					$customID,
					$orderID),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		$status = isset($ppOrder['status']) ? $ppOrder['status'] : '';
		if($status == 'COMPLETED')
			return self::FinalizeFromOrderPayload($orderID, $ppOrder, 'return-completed');

		if($status == 'APPROVED' || $status == 'CREATED')
		{
			$captured = self::CaptureOrder($paypalOrderID);
			if(!is_array($captured))
				return false;
			return self::FinalizeFromOrderPayload($orderID, $captured, 'return-capture');
		}

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
		$type = isset($event['event_type']) ? $event['event_type'] : '';
		$resource = isset($event['resource']) && is_array($event['resource'])
			? $event['resource']
			: array();

		if($type == 'CHECKOUT.ORDER.APPROVED')
		{
			$paypalOrderID = isset($resource['id']) ? $resource['id'] : '';
			$orderID = (int)self::CustomIdFromOrder($resource);
			if($orderID <= 0 && $paypalOrderID != '')
				$orderID = self::FindOrderByPaypalId($paypalOrderID);
			if($orderID <= 0)
				return false;
			return self::CaptureReturn($orderID, $paypalOrderID);
		}

		if($type == 'PAYMENT.CAPTURE.COMPLETED')
		{
			$captureId = isset($resource['id']) ? $resource['id'] : '';
			$orderID = 0;
			if(!empty($resource['custom_id']))
				$orderID = (int)$resource['custom_id'];
			if($orderID <= 0 && !empty($resource['invoice_id']))
				$orderID = self::OrderIdFromInvoiceNo($resource['invoice_id']);
			if($orderID <= 0 && !empty($resource['supplementary_data']['related_ids']['order_id']))
				$orderID = self::FindOrderByPaypalId($resource['supplementary_data']['related_ids']['order_id']);
			if($orderID <= 0)
			{
				PutLog('PayPal webhook: could not map CAPTURE.COMPLETED to order',
					PRIO_WARNING,
					__FILE__,
					__LINE__);
				return false;
			}

			$paid = 0;
			$currency = '';
			if(isset($resource['amount']['value']))
				$paid = self::AmountToCents($resource['amount']['value']);
			if(isset($resource['amount']['currency_code']))
				$currency = $resource['amount']['currency_code'];

			return self::FinalizePayment($orderID, $captureId, $paid, $currency, 'webhook');
		}

		return true;
	}

	/**
	 * Verify webhook signature via PayPal API
	 *
	 * @param array $headers
	 * @param string $rawBody
	 * @param array $event
	 * @return bool
	 */
	public static function VerifyWebhook($headers, $rawBody, $event)
	{
		$webhookId = trim((string)self::Pref('paypal_webhook_id'));
		if($webhookId == '')
		{
			PutLog('PayPal webhook: missing webhook ID',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		$body = array(
			'auth_algo' => self::HeaderVal($headers, 'PAYPAL-AUTH-ALGO'),
			'cert_url' => self::HeaderVal($headers, 'PAYPAL-CERT-URL'),
			'transmission_id' => self::HeaderVal($headers, 'PAYPAL-TRANSMISSION-ID'),
			'transmission_sig' => self::HeaderVal($headers, 'PAYPAL-TRANSMISSION-SIG'),
			'transmission_time' => self::HeaderVal($headers, 'PAYPAL-TRANSMISSION-TIME'),
			'webhook_id' => $webhookId,
			'webhook_event' => $event
		);

		$res = self::ApiRequest('POST', '/v1/notifications/verify-webhook-signature', $body);
		if(!$res['ok'] || !is_array($res['data']))
			return false;

		return isset($res['data']['verification_status'])
			&& $res['data']['verification_status'] == 'SUCCESS';
	}

	/**
	 * Create or reuse webhook subscription
	 *
	 * @return string|false Webhook ID
	 */
	public static function EnsureWebhook()
	{
		if(!self::IsConfigured())
			return false;

		$url = self::WebhookUrl();
		if(stripos($url, 'https://') !== 0)
		{
			PutLog('PayPal webhook: selfurl must be HTTPS to register a webhook',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		$storedId = trim((string)self::Pref('paypal_webhook_id'));
		if($storedId != '')
		{
			$existing = self::ApiRequest('GET', '/v1/notifications/webhooks/' . rawurlencode($storedId));
			if($existing['ok'] && is_array($existing['data'])
				&& isset($existing['data']['url']) && $existing['data']['url'] == $url)
			{
				return $storedId;
			}
		}

		$list = self::ApiRequest('GET', '/v1/notifications/webhooks');
		if($list['ok'] && is_array($list['data']) && isset($list['data']['webhooks']) && is_array($list['data']['webhooks']))
		{
			foreach($list['data']['webhooks'] as $hook)
			{
				if(isset($hook['url'], $hook['id']) && $hook['url'] == $url)
					return $hook['id'];
			}
		}

		$created = self::ApiRequest('POST', '/v1/notifications/webhooks', array(
			'url' => $url,
			'event_types' => array(
				array('name' => 'PAYMENT.CAPTURE.COMPLETED'),
				array('name' => 'CHECKOUT.ORDER.APPROVED')
			)
		));
		if($created['ok'] && is_array($created['data']) && !empty($created['data']['id']))
			return $created['data']['id'];

		PutLog(sprintf('PayPal webhook: registration failed (HTTP %d)',
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
	 * GET PayPal order
	 *
	 * @param string $paypalOrderID
	 * @return array|false
	 */
	public static function GetOrder($paypalOrderID)
	{
		$res = self::ApiRequest('GET', '/v2/checkout/orders/' . rawurlencode($paypalOrderID));
		if(!$res['ok'] || !is_array($res['data']))
			return false;
		return $res['data'];
	}

	/**
	 * CAPTURE PayPal order
	 *
	 * @param string $paypalOrderID
	 * @return array|false
	 */
	public static function CaptureOrder($paypalOrderID)
	{
		$res = self::ApiRequest('POST',
			'/v2/checkout/orders/' . rawurlencode($paypalOrderID) . '/capture',
			new stdClass(),
			array('Prefer: return=representation', 'PayPal-Request-Id: bm-capture-' . $paypalOrderID));
		if(!$res['ok'] || !is_array($res['data']))
		{
			PutLog(sprintf('PayPal Checkout: capture failed for %s (HTTP %d)',
					$paypalOrderID,
					$res['status']),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}
		return $res['data'];
	}

	/**
	 * Finalize a captured payment
	 *
	 * @param int $orderID
	 * @param string $captureId
	 * @param int $paidCents
	 * @param string $currency
	 * @param string $source
	 * @return bool
	 */
	public static function FinalizePayment($orderID, $captureId, $paidCents, $currency, $source)
	{
		global $db, $bm_prefs;

		$res = $db->Query('SELECT `amount`,`status`,`paymethod`,`txnid` FROM {pre}orders WHERE `orderid`=?',
			(int)$orderID);
		if($res->RowCount() != 1)
			return false;
		$order = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		if($order['paymethod'] != PAYMENT_METHOD_PAYPAL_CHECKOUT)
			return false;

		if($order['status'] == ORDER_STATUS_ACTIVATED)
			return true;

		if($order['status'] != ORDER_STATUS_CREATED)
			return false;

		$expectedCurrency = strtoupper($bm_prefs['currency']);
		if($currency != '' && strtoupper($currency) != $expectedCurrency)
		{
			PutLog(sprintf('PayPal Checkout (%d): currency %s != %s',
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
			PutLog(sprintf('PayPal Checkout (%d): paid amount %d < order amount %d',
					$orderID,
					$paidCents,
					$order['amount']),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		if($captureId != '' && $order['txnid'] != '' && $order['txnid'] != $captureId)
		{
			PutLog(sprintf('PayPal Checkout (%d): txnid already set to %s',
					$orderID,
					$order['txnid']),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		if($captureId != '' && ($order['txnid'] == '' || $order['txnid'] != $captureId))
		{
			$db->Query('UPDATE {pre}orders SET `txnid`=? WHERE `orderid`=?',
				$captureId,
				(int)$orderID);
		}

		if(!self::AutoActivate())
		{
			PutLog(sprintf('PayPal Checkout (%d, %s) recorded without auto-activate (%s)',
					$orderID,
					$captureId,
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
			PutLog(sprintf('PayPal Checkout (%d, %s) accepted (%s)',
					$orderID,
					$captureId,
					$source),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			return true;
		}

		PutLog(sprintf('PayPal Checkout (%d, %s) rejected by BMPayment (%s)',
				$orderID,
				$captureId,
				$source),
			PRIO_WARNING,
			__FILE__,
			__LINE__);
		return false;
	}

	private static function FinalizeFromOrderPayload($orderID, $ppOrder, $source)
	{
		$capture = self::FirstCapture($ppOrder);
		$captureId = isset($capture['id']) ? $capture['id'] : '';
		$paid = 0;
		$currency = '';
		if(isset($capture['amount']['value']))
			$paid = self::AmountToCents($capture['amount']['value']);
		else if(isset($ppOrder['purchase_units'][0]['amount']['value']))
			$paid = self::AmountToCents($ppOrder['purchase_units'][0]['amount']['value']);
		if(isset($capture['amount']['currency_code']))
			$currency = $capture['amount']['currency_code'];
		else if(isset($ppOrder['purchase_units'][0]['amount']['currency_code']))
			$currency = $ppOrder['purchase_units'][0]['amount']['currency_code'];

		if($captureId == '' && isset($ppOrder['id']))
			$captureId = $ppOrder['id'];

		return self::FinalizePayment($orderID, $captureId, $paid, $currency, $source);
	}

	private static function FirstCapture($ppOrder)
	{
		if(!isset($ppOrder['purchase_units'][0]['payments']['captures'][0])
			|| !is_array($ppOrder['purchase_units'][0]['payments']['captures'][0]))
			return array();
		return $ppOrder['purchase_units'][0]['payments']['captures'][0];
	}

	private static function CustomIdFromOrder($ppOrder)
	{
		if(!empty($ppOrder['purchase_units'][0]['custom_id']))
			return (string)$ppOrder['purchase_units'][0]['custom_id'];
		if(!empty($ppOrder['custom_id']))
			return (string)$ppOrder['custom_id'];
		return '';
	}

	private static function ApproveLink($ppOrder)
	{
		if(!isset($ppOrder['links']) || !is_array($ppOrder['links']))
			return '';
		$fallback = '';
		foreach($ppOrder['links'] as $link)
		{
			if(!isset($link['rel'], $link['href']))
				continue;
			if($link['rel'] == 'approve' || $link['rel'] == 'payer-action')
				return $link['href'];
			if($fallback == '' && !empty($link['href']))
				$fallback = $link['href'];
		}
		return $fallback;
	}

	private static function StoreParams($orderID, $params)
	{
		global $db;

		$db->Query('UPDATE {pre}orders SET `paymethod_params`=? WHERE `orderid`=?',
			serialize($params),
			(int)$orderID);
	}

	private static function FindOrderByPaypalId($paypalOrderID)
	{
		global $db;

		$res = $db->Query('SELECT `orderid`,`paymethod_params` FROM {pre}orders WHERE `paymethod`=? AND `status`=? ORDER BY `orderid` DESC LIMIT 50',
			PAYMENT_METHOD_PAYPAL_CHECKOUT,
			ORDER_STATUS_CREATED);
		$orderID = 0;
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$params = @unserialize($row['paymethod_params']);
			if(is_array($params) && isset($params['paypal_order_id'])
				&& $params['paypal_order_id'] == $paypalOrderID)
			{
				$orderID = (int)$row['orderid'];
				break;
			}
		}
		$res->Free();
		return $orderID;
	}

	private static function OrderIdFromInvoiceNo($invoiceNo)
	{
		if(preg_match('/(\d+)\s*$/', (string)$invoiceNo, $m))
			return (int)$m[1];
		return 0;
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

	private static function GetAccessToken()
	{
		if(self::$_token != '' && self::$_tokenExp > time() + 30)
			return self::$_token;

		$clientId = trim((string)self::Pref('paypal_client_id'));
		$secret = trim((string)self::Pref('paypal_client_secret'));
		if($clientId == '' || $secret == '')
			return false;

		$res = self::Http(
			'POST',
			self::ApiBase() . '/v1/oauth2/token',
			'grant_type=client_credentials',
			array(
				'Accept: application/json',
				'Content-Type: application/x-www-form-urlencoded'
			),
			$clientId . ':' . $secret
		);
		if(!$res['ok'] || !is_array($res['data']) || empty($res['data']['access_token']))
		{
			PutLog(sprintf('PayPal Checkout: OAuth failed (HTTP %d)',
					$res['status']),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return false;
		}

		self::$_token = $res['data']['access_token'];
		$expires = isset($res['data']['expires_in']) ? (int)$res['data']['expires_in'] : 300;
		self::$_tokenExp = time() + max(60, $expires);
		return self::$_token;
	}

	private static function ApiRequest($method, $path, $body = null, $extraHeaders = array())
	{
		$token = self::GetAccessToken();
		if($token === false)
			return array('ok' => false, 'status' => 0, 'data' => null, 'raw' => '');

		$headers = array_merge(array(
			'Authorization: Bearer ' . $token,
			'Accept: application/json',
			'Content-Type: application/json'
		), $extraHeaders);

		$payload = null;
		if($body !== null)
			$payload = json_encode($body);

		return self::Http($method, self::ApiBase() . $path, $payload, $headers);
	}

	private static function Http($method, $url, $body, $headers, $basicUserPass = null)
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
			if($basicUserPass !== null)
				$opts[CURLOPT_USERPWD] = $basicUserPass;
			curl_setopt_array($ch, $opts);
			$raw = curl_exec($ch);
			$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$curlErr = curl_error($ch);
			curl_close($ch);
			if($raw === false)
			{
				$raw = '';
				PutLog(sprintf('PayPal HTTP: curl error for %s: %s',
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
			$hdr = $headers;
			if($basicUserPass !== null)
				$hdr[] = 'Authorization: Basic ' . base64_encode($basicUserPass);
			$ctx = stream_context_create(array(
				'http' => array(
					'method' => $method,
					'header' => implode("\r\n", $hdr) . "\r\n",
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
