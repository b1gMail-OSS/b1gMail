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
 * payment class
 */
class BMPayment
{
	/**
	 * Check if payment system is available
	 *
	 * @return bool
	 */
	static function Available()
	{
		global $bm_prefs, $db;

		if($bm_prefs['enable_vk'] == 'yes'
			   || $bm_prefs['enable_paypal'] == 'yes'
			   || BMPayment::PayPalCheckoutEnabled()
			   || BMPayment::StripeEnabled()
			   || $bm_prefs['enable_su'] == 'yes'
			   || $bm_prefs['enable_skrill'] == 'yes')
		{
			return(true);
		}
		else
		{
			$res = $db->Query('SELECT COUNT(*) FROM {pre}paymethods WHERE `enabled`=1');
			list($count) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			return($count > 0);
		}
	}

	/**
	 * Built-in PayPal methods (classic WPS and Checkout)
	 *
	 * @param int $paymethod
	 * @return bool
	 */
	static function IsPayPalMethod($paymethod)
	{
		return($paymethod == PAYMENT_METHOD_PAYPAL
			|| $paymethod == PAYMENT_METHOD_PAYPAL_CHECKOUT);
	}

	/**
	 * Human-readable payment method title
	 *
	 * @param int $paymethod
	 * @param array $lang Language array (user or admin)
	 * @return string
	 */
	static function MethodTitle($paymethod, $lang)
	{
		if($paymethod == PAYMENT_METHOD_BANKTRANSFER)
			return isset($lang['banktransfer']) ? $lang['banktransfer'] : 'Bank';
		if($paymethod == PAYMENT_METHOD_PAYPAL)
			return isset($lang['paypal_classic']) ? $lang['paypal_classic'] : $lang['paypal'];
		if($paymethod == PAYMENT_METHOD_PAYPAL_CHECKOUT)
			return isset($lang['paypal']) ? $lang['paypal'] : 'PayPal';
		if($paymethod == PAYMENT_METHOD_STRIPE)
			return self::StripeTitle($lang);
		if($paymethod == PAYMENT_METHOD_SOFORTUEBERWEISUNG)
			return isset($lang['su']) ? $lang['su'] : 'sofortueberweisung.de';
		if($paymethod == PAYMENT_METHOD_SKRILL)
			return isset($lang['skrill']) ? $lang['skrill'] : 'Skrill';
		if($paymethod < 0)
		{
			$payMethods = BMPayment::GetCustomPaymentMethods();
			$payMethodID = abs($paymethod);
			if(isset($payMethods[$payMethodID]))
				return $payMethods[$payMethodID]['title'];
		}
		return isset($lang['unknown']) ? $lang['unknown'] : '?';
	}

	/**
	 * Customer-facing Stripe name. Empty preference falls back to the language string.
	 *
	 * @param array|null $lang
	 * @return string
	 */
	public static function StripeTitle($lang = null)
	{
		global $bm_prefs, $lang_user;

		self::EnsureGatewayColumns();

		$title = isset($bm_prefs['stripe_title']) ? trim((string)$bm_prefs['stripe_title']) : '';
		if($title !== '')
			return $title;

		if(!is_array($lang))
			$lang = $lang_user;
		if(is_array($lang) && isset($lang['stripe']) && $lang['stripe'] !== '')
			return $lang['stripe'];
		return 'Kreditkarte';
	}

	/**
	 * Copy a configured Stripe name into the language arrays used by templates.
	 *
	 * @return void
	 */
	public static function ApplyStripePaymentTitle()
	{
		global $bm_prefs, $lang_user, $lang_admin;

		$title = isset($bm_prefs['stripe_title']) ? trim((string)$bm_prefs['stripe_title']) : '';
		if($title === '')
			return;

		if(isset($lang_user) && is_array($lang_user))
		{
			$lang_user['stripe'] = $title;
			$lang_user['pacc_stripe'] = $title;
		}
		if(isset($lang_admin) && is_array($lang_admin))
		{
			$lang_admin['stripe'] = $title;
			$lang_admin['pacc_stripe'] = $title;
		}
	}

	/**
	 * Custom payment method field sort callback
	 *
	 * @param $a
	 * @param $b
	 * @return int
	 */
	static function CustomPaymentFieldSort($a, $b)
	{
		return($a['pos'] - $b['pos']);
	}

	/**
	 * Get custom payment methods
	 *
	 * @return array
	 */
	static function GetCustomPaymentMethods()
	{
		global $db;

		$result = array();
		$res = $db->Query('SELECT * FROM {pre}paymethods WHERE `enabled`=1 ORDER BY `title` ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$row['negID'] 	= -$row['methodid'];
			$row['fields'] 	= @unserialize($row['fields']);
			if(!is_array($row['fields'])) $row['fields'] = array();

			foreach($row['fields'] as $fieldID=>$field)
				$row['fields'][$fieldID]['options'] = array_map('trim', explode(',', $field['options']));

			uasort($row['fields'], array('BMPayment', 'CustomPaymentFieldSort'));

			$result[$row['methodid']] = $row;
		}
		$res->Free();

		return($result);
	}

	/**
	 * Add PayPal Checkout / Stripe prefs columns on existing installs that have not run DB sync yet.
	 *
	 * @return void
	 */
	public static function EnsureGatewayColumns()
	{
		global $db, $bm_prefs;

		static $done = false;
		if($done || !isset($db) || !is_object($db))
			return;
		$done = true;

		$cols = array(
			'enable_paypal_checkout' => "enum('yes','no') NOT NULL DEFAULT 'no'",
			'paypal_client_id' => "varchar(255) NOT NULL DEFAULT ''",
			'paypal_client_secret' => "varchar(255) NOT NULL DEFAULT ''",
			'paypal_sandbox' => "enum('yes','no') NOT NULL DEFAULT 'no'",
			'paypal_auto_activate' => "enum('yes','no') NOT NULL DEFAULT 'yes'",
			'paypal_webhook_id' => "varchar(64) NOT NULL DEFAULT ''",
			'enable_stripe' => "enum('yes','no') NOT NULL DEFAULT 'no'",
			'stripe_secret_key' => "varchar(255) NOT NULL DEFAULT ''",
			'stripe_webhook_secret' => "varchar(255) NOT NULL DEFAULT ''",
			'stripe_webhook_id' => "varchar(64) NOT NULL DEFAULT ''",
			'stripe_auto_activate' => "enum('yes','no') NOT NULL DEFAULT 'yes'",
			'stripe_enable_twint' => "enum('yes','no') NOT NULL DEFAULT 'yes'",
			'paypal_surcharge' => "enum('yes','no') NOT NULL DEFAULT 'no'",
			'paypal_surcharge_percent' => "varchar(16) NOT NULL DEFAULT '0'",
			'paypal_surcharge_fixed' => "varchar(16) NOT NULL DEFAULT '0'",
			'paypal_surcharge_cover' => "enum('yes','no') NOT NULL DEFAULT 'yes'",
			'stripe_surcharge' => "enum('yes','no') NOT NULL DEFAULT 'no'",
			'stripe_surcharge_percent' => "varchar(16) NOT NULL DEFAULT '0'",
			'stripe_surcharge_fixed' => "varchar(16) NOT NULL DEFAULT '0'",
			'stripe_surcharge_cover' => "enum('yes','no') NOT NULL DEFAULT 'yes'",
			'stripe_title' => "varchar(80) NOT NULL DEFAULT ''"
		);
		$added = false;
		foreach($cols as $name => $def)
		{
			$res = $db->Query('SHOW COLUMNS FROM {pre}prefs LIKE ?', $name);
			$exists = $res && $res->RowCount() > 0;
			if($res)
				$res->Free();
			if(!$exists)
			{
				$db->Query('ALTER TABLE {pre}prefs ADD `'.$name.'` '.$def);
				$added = true;
			}
		}
		if($added)
			ReadConfig();

		$defaults = array(
			'enable_paypal_checkout' => 'no',
			'paypal_sandbox' => 'no',
			'paypal_auto_activate' => 'yes',
			'enable_stripe' => 'no',
			'stripe_auto_activate' => 'yes',
			'stripe_enable_twint' => 'yes',
			'paypal_surcharge' => 'no',
			'paypal_surcharge_percent' => '0',
			'paypal_surcharge_fixed' => '0',
			'paypal_surcharge_cover' => 'yes',
			'stripe_surcharge' => 'no',
			'stripe_surcharge_percent' => '0',
			'stripe_surcharge_fixed' => '0',
			'stripe_surcharge_cover' => 'yes',
			'stripe_title' => ''
		);
		foreach($defaults as $key => $val)
		{
			if(!isset($bm_prefs[$key]) || $bm_prefs[$key] === '' || $bm_prefs[$key] === null)
				$bm_prefs[$key] = $val;
		}
	}

	/**
	 * @return void
	 */
	public static function EnsureStripeColumns()
	{
		self::EnsureGatewayColumns();
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	public static function NormalizeSurchargePercent($value)
	{
		$n = (float)str_replace(',', '.', trim((string)$value));
		if($n < 0)
			$n = 0;
		if($n > 50)
			$n = 50;
		return number_format($n, 4, '.', '');
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	public static function NormalizeSurchargeFixed($value)
	{
		$n = (float)str_replace(',', '.', trim((string)$value));
		if($n < 0)
			$n = 0;
		if($n > 1000)
			$n = 1000;
		return number_format($n, 2, '.', '');
	}

	/**
	 * Whether PayPal Checkout is enabled in prefs
	 *
	 * @return bool
	 */
	public static function PayPalCheckoutEnabled()
	{
		global $bm_prefs;

		self::EnsureGatewayColumns();
		return isset($bm_prefs['enable_paypal_checkout']) && $bm_prefs['enable_paypal_checkout'] == 'yes';
	}

	/**
	 * Whether Stripe checkout is enabled in prefs
	 *
	 * @return bool
	 */
	public static function StripeEnabled()
	{
		global $bm_prefs;

		self::EnsureGatewayColumns();
		return isset($bm_prefs['enable_stripe']) && $bm_prefs['enable_stripe'] == 'yes';
	}

	/**
	 * Prepares payment form (payment.form.tpl)
	 *
	 * @param &$tpl Template object
	 * @param $title Payment form title
	 * @param $amount Payment amount
	 */
	public static function PreparePaymentForm(&$tpl, $title, $amount, $userRow = false)
	{
		global $bm_prefs, $thisUser, $lang_user;

		self::EnsureGatewayColumns();

		if($userRow === false)
			$userRow = $thisUser->_row;

		$pf = array();

		$pf['title']			= $title;
		$pf['sendrg']			= $bm_prefs['sendrg'];
		$pf['currency']			= $bm_prefs['currency'];
		$pf['enable_su']		= $bm_prefs['enable_su'];
		$pf['enable_vk']		= $bm_prefs['enable_vk'];
		$pf['enable_paypal']	= $bm_prefs['enable_paypal'];
		$pf['enable_paypal_checkout'] = self::PayPalCheckoutEnabled() ? 'yes' : 'no';
		$pf['enable_stripe']	= self::StripeEnabled() ? 'yes' : 'no';
		$pf['enable_skrill']	= $bm_prefs['enable_skrill'];
		$pf['customMethods']	= BMPayment::GetCustomPaymentMethods();
		$pf['mwst']				= $bm_prefs['mwst'];
		$pf['paymentMethod']	= (int)$bm_prefs['default_paymethod'];
		$pf['countryList']		= CountryList(true);
		$pf['amount']			= number_format($amount, 4, '.', '');

		$pf['vorname']			= $userRow['vorname'];
		$pf['nachname']			= $userRow['nachname'];
		$pf['strasse']			= $userRow['strasse'];
		$pf['hnr']				= $userRow['hnr'];
		$pf['plz']				= $userRow['plz'];
		$pf['ort']				= $userRow['ort'];
		$pf['land']				= $userRow['land'];
		$pf['company']			= $userRow['company'];
		$pf['taxid']			= $userRow['taxid'];
		$pf['f_company']		= $bm_prefs['f_company'];
		$pf['f_taxid']			= $bm_prefs['f_taxid'];
		$surchargeTpl = self::SurchargeTplData();
		$pf['surchargePaypal']	= $surchargeTpl['paypal'];
		$pf['surchargeStripe']	= $surchargeTpl['stripe'];
		$pf['surchargeJs']		= $surchargeTpl['js'];

		$tpl->assign('_pf', $pf);
	}

	/**
	 * Whether a cart row is the gateway surcharge line
	 *
	 * @param array $cartItem
	 * @return bool
	 */
	public static function IsSurchargeItem($cartItem)
	{
		return is_array($cartItem)
			&& ((!empty($cartItem['surcharge']))
				|| (isset($cartItem['key']) && $cartItem['key'] == 'b1gMail.surcharge'));
	}

	/**
	 * Surcharge settings for a payment method
	 *
	 * @param int $paymethod
	 * @return array{enabled:bool,percent:float,fixedCents:int,cover:bool}
	 */
	public static function GatewaySurchargeConfig($paymethod)
	{
		global $bm_prefs;

		self::EnsureGatewayColumns();

		$prefix = '';
		if(self::IsPayPalMethod($paymethod))
			$prefix = 'paypal';
		else if($paymethod == PAYMENT_METHOD_STRIPE)
			$prefix = 'stripe';
		else
			return array('enabled' => false, 'percent' => 0.0, 'fixedCents' => 0, 'cover' => true);

		$percent = isset($bm_prefs[$prefix.'_surcharge_percent'])
			? (float)str_replace(',', '.', (string)$bm_prefs[$prefix.'_surcharge_percent'])
			: 0.0;
		$fixed = isset($bm_prefs[$prefix.'_surcharge_fixed'])
			? (float)str_replace(',', '.', (string)$bm_prefs[$prefix.'_surcharge_fixed'])
			: 0.0;
		$enabled = isset($bm_prefs[$prefix.'_surcharge']) && $bm_prefs[$prefix.'_surcharge'] == 'yes'
			&& ($percent > 0 || $fixed > 0);
		$cover = !isset($bm_prefs[$prefix.'_surcharge_cover']) || $bm_prefs[$prefix.'_surcharge_cover'] != 'no';

		if($percent < 0)
			$percent = 0;
		if($percent > 50)
			$percent = 50;
		if($fixed < 0)
			$fixed = 0;

		return array(
			'enabled' => $enabled,
			'percent' => $percent,
			'fixedCents' => (int)round($fixed * 100),
			'cover' => $cover
		);
	}

	/**
	 * Surcharge values for templates / JS (always present)
	 *
	 * @return array{paypal:array,stripe:array,js:string}
	 */
	public static function SurchargeTplData()
	{
		$paypal = self::GatewaySurchargeConfig(PAYMENT_METHOD_PAYPAL_CHECKOUT);
		$stripe = self::GatewaySurchargeConfig(PAYMENT_METHOD_STRIPE);

		$fmt = function($cfg)
		{
			return array(
				'enabled' => !empty($cfg['enabled']) ? 1 : 0,
				'percent' => (float)$cfg['percent'],
				'fixed' => round($cfg['fixedCents'] / 100, 2),
				'cover' => !empty($cfg['cover']) ? 1 : 0
			);
		};

		$paypalTpl = $fmt($paypal);
		$stripeTpl = $fmt($stripe);

		$jsEntry = function($row)
		{
			$percent = number_format((float)$row['percent'], 4, '.', '');
			$percent = rtrim(rtrim($percent, '0'), '.');
			if($percent === '')
				$percent = '0';
			return sprintf('{enabled:%s,percent:%s,fixed:%s,cover:%s}',
				$row['enabled'] ? 'true' : 'false',
				$percent,
				number_format((float)$row['fixed'], 2, '.', ''),
				$row['cover'] ? 'true' : 'false');
		};

		return array(
			'paypal' => $paypalTpl,
			'stripe' => $stripeTpl,
			'js' => sprintf('1:%s,4:%s,5:%s',
				$jsEntry($paypalTpl),
				$jsEntry($paypalTpl),
				$jsEntry($stripeTpl))
		);
	}

	/**
	 * Surcharge config for the checkout JS (method id => settings)
	 *
	 * @return object
	 */
	public static function SurchargeConfigForJs()
	{
		$data = self::SurchargeTplData();
		$out = new stdClass();
		$out->{'1'} = array(
			'enabled' => (bool)$data['paypal']['enabled'],
			'percent' => $data['paypal']['percent'],
			'fixed' => $data['paypal']['fixed'],
			'cover' => (bool)$data['paypal']['cover']
		);
		$out->{'4'} = $out->{'1'};
		$out->{'5'} = array(
			'enabled' => (bool)$data['stripe']['enabled'],
			'percent' => $data['stripe']['percent'],
			'fixed' => $data['stripe']['fixed'],
			'cover' => (bool)$data['stripe']['cover']
		);
		return $out;
	}

	/**
	 * Extra cents so the merchant still nets $baseCents after provider fees
	 *
	 * Cover: gross = (base + fixed) / (1 - percent/100)
	 * Add:   extra = base * percent/100 + fixed
	 *
	 * @param int $baseCents Payable amount before surcharge
	 * @param int $paymethod
	 * @return int
	 */
	public static function CalcGatewaySurchargeCents($baseCents, $paymethod)
	{
		$cfg = self::GatewaySurchargeConfig($paymethod);
		$baseCents = (int)$baseCents;
		if(!$cfg['enabled'] || $baseCents <= 0)
			return 0;

		$percent = $cfg['percent'];
		$fixedCents = $cfg['fixedCents'];

		if($cfg['cover'])
		{
			$rate = $percent / 100.0;
			if($rate >= 0.99)
				$rate = 0.99;
			$gross = (int)round(($baseCents + $fixedCents) / (1.0 - $rate));
			return max(0, $gross - $baseCents);
		}

		return max(0, (int)round($baseCents * $percent / 100.0) + $fixedCents);
	}

	/**
	 * Append a surcharge cart line for PayPal / Stripe
	 *
	 * @param array $cart
	 * @param int $paymethod
	 * @param float $vatRate
	 * @return array
	 */
	public static function ApplyGatewaySurcharge(array $cart, $paymethod, $vatRate)
	{
		global $lang_user;

		$clean = array();
		foreach($cart as $item)
		{
			if(self::IsSurchargeItem($item))
				continue;
			$clean[] = $item;
		}

		$baseCents = (int)round(self::CalcTotal($clean, $vatRate) * 100);
		$surchargeCents = self::CalcGatewaySurchargeCents($baseCents, $paymethod);
		if($surchargeCents <= 0)
			return $clean;

		$text = isset($lang_user['payment_surcharge'])
			? $lang_user['payment_surcharge']
			: 'Transaktionskosten';

		$clean[] = array(
			'key'		=> 'b1gMail.surcharge',
			'count'		=> 1,
			'amount'	=> $surchargeCents,
			'total'		=> $surchargeCents,
			'text'		=> $text,
			'surcharge'	=> true
		);
		return $clean;
	}

	/**
	 * calculate total amount of cart
	 *
	 * @param $cart Cart array
	 * @return int Amount
	 */
	static function CalcTotal($cart, $vatRate)
	{
		global $bm_prefs;

		$amount = 0;
		$surcharge = 0;

		foreach($cart as $cartItem)
		{
			if(self::IsSurchargeItem($cartItem))
				$surcharge += $cartItem['total'];
			else
				$amount += $cartItem['total'];
		}

		if($bm_prefs['mwst'] == 'add')
			$amount = round($amount * (1+$vatRate/100), 0);

		$amount += $surcharge;
		$amount = round($amount/100, 2);

		return($amount);
	}

	/**
	 * activate order with VK code
	 *
	 * @param string $vkCode VK code
	 * @param int $paidAmount Paid order amount (in cents)
	 * @return bool Success
	 */
	public static function ActivateOrderWithVKCode($vkCode, $paidAmount)
	{
		global $db;

		$vkCode = preg_replace('/^vk\-/i', '', trim($vkCode));

		$res = $db->Query('SELECT `orderid` FROM {pre}orders WHERE `vkcode`=? AND `status`=?',
						  $vkCode,
						  ORDER_STATUS_CREATED);
		if($res->RowCount() == 0)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return(BMPayment::ActivateOrder($row['orderid'], $paidAmount));
	}

	/**
	 * activate order with order ID
	 *
	 * @param int $orderID Order ID
	 * @param int $paidAmount Paid order amount (in cents)
	 * @return bool Success
	 */
	static function ActivateOrder($orderID, $paidAmount)
	{
		global $db, $bm_prefs, $lang_custom, $lang_user;

		// retrieve order
		$res = $db->Query('SELECT `amount`,`status`,`userid`,`cart`,`paymethod`,`txnid` FROM {pre}orders WHERE `orderid`=?',
						  $orderID);
		if($res->RowCount() != 1)
		{
			PutLog(sprintf('Failed to activate order %d (order not found)', $orderID),
				   PRIO_NOTE,
				   __FILE__,
				   __LINE__);
			return(false);
		}
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// check order status
		if($row['status'] != ORDER_STATUS_CREATED)
		{
			PutLog(sprintf('Failed to activate order %d (already activated or cancelled)', $orderID),
				   PRIO_NOTE,
				   __FILE__,
				   __LINE__);
			return(false);
		}

		// check amount
		if($row['amount'] > $paidAmount)
		{
			PutLog(sprintf('Failed to activate order %d (paid amount (%.02f) < order amount (%.02f))',
						   $orderID,
						   $paidAmount/100,
						   $row['amount']/100),
				   PRIO_NOTE,
				   __FILE__,
				   __LINE__);
			return(false);
		}

		// get user object
		$user = _new('BMUser', array($row['userid']));
		if(!is_array($user->_row))
		{
			PutLog(sprintf('Failed to activate order %d (user %d not found)', $orderID, $row['userid']),
				   PRIO_WARNING,
				   __FILE__,
				   __LINE__);
			return(false);
		}

		// process cart
		$cart = @unserialize($row['cart']);
		if(!is_array($cart)) $cart = array();

		foreach($cart as $cartItemID=>$cartItem)
		{
			if(self::IsSurchargeItem($cartItem))
				continue;

			if(!isset($cartItem['key']) || !isset($cartItem['count'])
			   || $cartItem['count'] <= 0)
			{
				PutLog(sprintf('Invalid cart item %d in order %d',
							   $cartItemID,
							   $orderID),
					   PRIO_WARNING,
					   __FILE__,
					   __LINE__);
			}

			if($cartItem['key'] == 'b1gMail.credits')
			{
				$user->Debit($cartItem['count'], sprintf($lang_user['tx_charge'], BMPayment::InvoiceNo($orderID)));
			}
			else
			{
				PutLog(sprintf('Unknown cart item key: %s - calling plugins', $cartItem['key']),
					   PRIO_DEBUG,
					   __FILE__,
					   __LINE__);
				ModuleFunction('ActivateOrderItem',
							   array($orderID, $row['userid'], $cartItem));
			}
		}

		// mark as activated
		$db->Query('UPDATE {pre}orders SET `status`=?,`activated`=? WHERE `orderid`=?',
				   ORDER_STATUS_ACTIVATED,
				   time(),
				   $orderID);

		// get custom payment methods
		if($row['paymethod'] < 0)
		{
			$payMethods = BMPayment::GetCustomPaymentMethods();
			$payMethodID = abs($row['paymethod']);
		}

		// generate invoice?
		$generateInvoice = false;
		if($bm_prefs['sendrg'] == 'yes' && $row['amount'] > 0)
		{
			if(in_array($row['paymethod'], array(PAYMENT_METHOD_PAYPAL, PAYMENT_METHOD_PAYPAL_CHECKOUT, PAYMENT_METHOD_STRIPE, PAYMENT_METHOD_SKRILL, PAYMENT_METHOD_SOFORTUEBERWEISUNG)))
			{
				$generateInvoice = true;
			}
			else if($row['paymethod'] < 0)
			{
				if(isset($payMethods[$payMethodID]) && $payMethods[$payMethodID]['invoice'] == 'at_activation')
					$generateInvoice = true;
			}
		}
		if($generateInvoice)
		{
			try
			{
				BMPayment::GenerateInvoice($orderID);
			}
			catch(Throwable $e)
			{
				PutLog(sprintf('Failed to generate invoice for order %d: %s',
							   $orderID,
							   $e->getMessage()),
					   PRIO_WARNING,
					   __FILE__,
					   __LINE__);
			}
		}

		// payment notification?
		if($bm_prefs['send_pay_notification'] == 'yes' && $row['amount'] > 0)
		{
			$payMethod = BMPayment::MethodTitle($row['paymethod'], $lang_user);

			SystemMail(sprintf('"%s" <%s>', $bm_prefs['pay_emailfrom'], $bm_prefs['pay_emailfromemail']),
					   $bm_prefs['pay_notification_to'],
					   $lang_custom['paynotify_sub'],
					   'paynotify_text',
						array(
							'payment_method'	=> $payMethod,
							'order_amount'		=> sprintf('%.02f %s', $row['amount']/100, $bm_prefs['currency']),
							'paid_amount'		=> sprintf('%.02f %s', $paidAmount/100, $bm_prefs['currency']),
							'user_id'			=> $row['userid'],
							'order_id'			=> $orderID,
							'invoice_no'		=> BMPayment::InvoiceNo($orderID),
							'customer_no'		=> BMPayment::CustomerNo($row['userid']),
							'txn_id'			=> empty($row['txnid']) ? '-' : $row['txnid']
						));
		}

		// order confirmation
		SystemMail(sprintf('"%s" <%s>', $bm_prefs['pay_emailfrom'], $bm_prefs['pay_emailfromemail']),
				   $user->_row['email'],
				   $lang_custom['orderconfirm_sub'],
				   'orderconfirm_text',
					array(
						'invoice_no'		=> BMPayment::InvoiceNo($orderID)
					));

		// log
		PutLog(sprintf('Order %d activated', $orderID),
			   PRIO_NOTE,
			   __FILE__,
			   __LINE__);
		return(true);
	}

	/**
	 * Initiate payment
	 *
	 * @param &$tpl Template object
	 * @param $orderID Order ID
	 * @param $returnURL Return URL
	 * @param $pageVarName Name of page variable in template
	 * @return bool 'true' on success, 'false' on error (error message will be displayed)
	 */
	static function InitiatePayment(&$tpl, $orderID, $returnURL = '', $pageVarName = 'pageContent', $userID = 0)
	{
		global $db, $thisUser, $lang_user, $bm_prefs;

		if($userID == 0)
			$userID = $thisUser->_row['id'];

		$res = $db->Query('SELECT `userid`,`paymethod`,`amount`,`status`,`cart`,`vkcode`,`paymethod_params` FROM {pre}orders WHERE `userid`=? AND `orderid`=?',
						  $userID,
						  $orderID);
		if($res->RowCount() != 1)
			die('Invalid order ID');
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		if($row['status'] != ORDER_STATUS_CREATED)
		{
			$tpl->assign($pageVarName, 'li/msg.tpl');
			$tpl->assign('title', $lang_user['error']);
			$tpl->assign('msg', $lang_user['orderalreadypaid']);
			if($pageVarName == 'pageContent')
				BMPayment::UsePrefsPageLayout($tpl);
			return(false);
		}

		$cart = @unserialize($row['cart']);
		if(!is_array($cart))
			$cart = array();

		$pf = array(
			'orderID'		=> $orderID,
			'invoiceNo'		=> BMPayment::InvoiceNo($orderID),
			'payMethod'		=> $row['paymethod'],
			'amountEN'		=> number_format($row['amount']/100, 2, '.', ''),
			'amount'		=> sprintf('%.02f %s', $row['amount']/100, $bm_prefs['currency']),
			'currency'		=> $bm_prefs['currency'],
			'returnURL'		=> ($returnURL != '' ? $returnURL : sprintf('%sprefs.php?action=paymentReturn&sid=%s', $bm_prefs['selfurl'], session_id()))
		);
		$pf['returnURL_SU'] = preg_replace('/^(http|https)\:\/\//i', '', $pf['returnURL']);

		$pf['itemName']			= array();
		foreach($cart as $cartItem)
			$pf['itemName'][]	= $cartItem['text'];
		$pf['itemName'] 		= HTMLFormat(implode(', ', $pf['itemName']));

		if($row['paymethod'] == PAYMENT_METHOD_PAYPAL)
		{
			$pf['payPalMail']		= $bm_prefs['paypal_mail'];
			$pf['notifyURL']		= sprintf('%sinterface/ipn.php', $bm_prefs['selfurl']);
		}

		else if($row['paymethod'] == PAYMENT_METHOD_PAYPAL_CHECKOUT)
		{
			if(!class_exists('BMPayPal'))
				include(B1GMAIL_DIR . 'serverlib/paypal.class.php');

			$approveUrl = BMPayPal::StartCheckout($orderID, $row, $pf['returnURL']);
			if(is_string($approveUrl) && $approveUrl != '')
			{
				header('Location: ' . $approveUrl);
				exit;
			}

			$resPaid = $db->Query('SELECT `status`,`txnid` FROM {pre}orders WHERE `orderid`=?',
				$orderID);
			$paidRow = $resPaid->FetchArray(MYSQLI_ASSOC);
			$resPaid->Free();
			if($paidRow && $paidRow['status'] != ORDER_STATUS_CREATED)
			{
				$tpl->assign($pageVarName, 'li/msg.tpl');
				$tpl->assign('title', $lang_user['error']);
				$tpl->assign('msg', $lang_user['orderalreadypaid']);
				if($pageVarName == 'pageContent')
					BMPayment::UsePrefsPageLayout($tpl);
				return(false);
			}
			if($paidRow && !empty($paidRow['txnid']))
			{
				$tpl->assign('title', $lang_user['thankyou']);
				$tpl->assign('msg', $lang_user['paymentreturn_txt']);
				$tpl->assign('msgTone', 'success');
				$tpl->assign('backLink', SessionUrl('prefs.php?action=orders'));
				$payReturnTpl = 'li/prefs.paymentreturn.tpl';
				$tplName = isset($bm_prefs['template']) ? trim((string)$bm_prefs['template']) : '';
				if($pageVarName != 'pageContent' || $tplName === '' || !is_file(B1GMAIL_DIR . 'templates/' . $tplName . '/' . $payReturnTpl))
					$payReturnTpl = 'li/msg.tpl';
				$tpl->assign($pageVarName, $payReturnTpl);
				if($pageVarName == 'pageContent' && $payReturnTpl == 'li/msg.tpl')
					BMPayment::UsePrefsPageLayout($tpl);
				return(false);
			}

			$pf['paypalError'] = true;
			$pf['paypalErrorMsg'] = $lang_user['paypal_error'];
		}

		else if($row['paymethod'] == PAYMENT_METHOD_STRIPE)
		{
			if(!class_exists('BMStripe'))
				include(B1GMAIL_DIR . 'serverlib/stripe.class.php');

			$checkoutUrl = BMStripe::StartCheckout($orderID, $row, $pf['returnURL']);
			if(is_string($checkoutUrl) && $checkoutUrl != '')
			{
				header('Location: ' . $checkoutUrl);
				exit;
			}

			$resPaid = $db->Query('SELECT `status`,`txnid` FROM {pre}orders WHERE `orderid`=?',
				$orderID);
			$paidRow = $resPaid->FetchArray(MYSQLI_ASSOC);
			$resPaid->Free();
			if($paidRow && $paidRow['status'] != ORDER_STATUS_CREATED)
			{
				$tpl->assign($pageVarName, 'li/msg.tpl');
				$tpl->assign('title', $lang_user['error']);
				$tpl->assign('msg', $lang_user['orderalreadypaid']);
				if($pageVarName == 'pageContent')
					BMPayment::UsePrefsPageLayout($tpl);
				return(false);
			}
			if($paidRow && !empty($paidRow['txnid']))
			{
				$tpl->assign('title', $lang_user['thankyou']);
				$tpl->assign('msg', $lang_user['paymentreturn_txt']);
				$tpl->assign('msgTone', 'success');
				$tpl->assign('backLink', SessionUrl('prefs.php?action=orders'));
				$payReturnTpl = 'li/prefs.paymentreturn.tpl';
				$tplName = isset($bm_prefs['template']) ? trim((string)$bm_prefs['template']) : '';
				if($pageVarName != 'pageContent' || $tplName === '' || !is_file(B1GMAIL_DIR . 'templates/' . $tplName . '/' . $payReturnTpl))
					$payReturnTpl = 'li/msg.tpl';
				$tpl->assign($pageVarName, $payReturnTpl);
				if($pageVarName == 'pageContent' && $payReturnTpl == 'li/msg.tpl')
					BMPayment::UsePrefsPageLayout($tpl);
				return(false);
			}

			$pf['stripeError'] = true;
			$pf['stripeErrorMsg'] = $lang_user['stripe_error'];
		}

		else if($row['paymethod'] == PAYMENT_METHOD_SKRILL)
		{
			$pf['skrillMail']		= $bm_prefs['skrill_mail'];
			$pf['notifyURL']		= sprintf('%sinterface/skrill.php', $bm_prefs['selfurl']);
		}

		else if($row['paymethod'] == PAYMENT_METHOD_SOFORTUEBERWEISUNG)
		{
			$pf['suKdNr']			= $bm_prefs['su_kdnr'];
			$pf['suPrjNr']			= $bm_prefs['su_prjnr'];
			if($bm_prefs['su_inputcheck'] == 'yes')
				$pf['hash']				= md5(sprintf('%s|%s|||||%s|%s|%s|%s|%d|%s|%s|%s|||%s',
												  $bm_prefs['su_kdnr'],
												  $bm_prefs['su_prjnr'],
												  $pf['amountEN'],
												  $bm_prefs['currency'],
												  $pf['invoiceNo'],
												  $pf['itemName'],
												  $pf['orderID'],
												  session_id(),
												  $pf['returnURL_SU'],
												  $pf['returnURL_SU'],
												  $bm_prefs['su_prjpass']));
			else
				$pf['hash']				= false;
		}

		else if($row['paymethod'] == PAYMENT_METHOD_BANKTRANSFER)
		{
			$pf['ktoInh']			= $bm_prefs['vk_kto_inh'];
			$pf['ktoNr']			= $bm_prefs['vk_kto_nr'];
			$pf['ktoBLZ']			= $bm_prefs['vk_kto_blz'];
			$pf['ktoInst']			= $bm_prefs['vk_kto_inst'];
			$pf['ktoIBAN']			= $bm_prefs['vk_kto_iban'];
			$pf['ktoBIC']			= $bm_prefs['vk_kto_bic'];
			$pf['ktoSubject']		= 'VK-'.$row['vkcode'];
			$pf['ktoText']			= sprintf($lang_user['pn_banktransfer'],
											  $row['amount']/100,
											  $bm_prefs['currency'],
											  $row['vkcode']);
			$pf['invoiceID']		= $bm_prefs['sendrg'] == 'yes' ? $orderID : 0;
		}

		else if($row['paymethod'] < 0)
		{
			$payMethods = BMPayment::GetCustomPaymentMethods();
			$payMethodID = abs($row['paymethod']);

			if(isset($payMethods[$payMethodID]))
			{
				$pf['payMethodTitle']	= $payMethods[$payMethodID]['title'];
				$pf['payMethodText']	= sprintf($lang_user['pn_customtext'], $payMethods[$payMethodID]['title']);
			}
		}

		$tpl->assign($pageVarName, 'li/payment.pay.tpl');
		$tpl->assign('_pf', $pf);

		if($pageVarName == 'pageContent')
			BMPayment::UsePrefsPageLayout($tpl);
	}

	/**
	 * Wrap payment/message page for prefs layout (Tabler bm-prefs-page)
	 *
	 * @param &$tpl Template object
	 */
	static function UsePrefsPageLayout(&$tpl)
	{
		global $bm_prefs;

		$pageContent = $tpl->getTemplateVars('pageContent');
		$tplName = $tpl->getTemplateVars('_tplname');
		$themeDir = (is_string($tplName) && $tplName != '')
			? B1GMAIL_DIR . 'templates/' . $tplName . '/'
			: (isset($bm_prefs['template'])
				? B1GMAIL_DIR . 'templates/' . $bm_prefs['template'] . '/'
				: $tpl->tplDir);

		if($pageContent == 'li/payment.pay.tpl'
			&& file_exists($themeDir . 'li/prefs.payment.tpl'))
			$tpl->assign('pageContent', 'li/prefs.payment.tpl');
		else if($pageContent == 'li/msg.tpl'
			&& file_exists($themeDir . 'li/prefs.message.tpl'))
			$tpl->assign('pageContent', 'li/prefs.message.tpl');
	}

	/**
	 * Process payment form submission
	 *
	 * @param &$tpl Template object
	 * @param $cart Cart array
	 * @return mixed 'false' on error or order ID on success
	 */
	static function ProcessPaymentForm(&$tpl, $cart, $fail = false, $userID = 0)
	{
		global $bm_prefs, $db, $thisUser, $lang_user;

		if($userID == 0)
			$userID = $thisUser->_row['id'];

		if(!is_array($cart) || count($cart) == 0)
		{
			PutLog('BMPayment::ProcessPaymentForm(): $cart is empty',
				   PRIO_WARNING,
				   __FILE__,
				   __LINE__);
			return(false);
		}

		$invalidFields = array();

		// check address input
		if($bm_prefs['sendrg'] == 'yes')
		{
			$firstName = trim($_POST['vorname']);
			if(strlen($firstName) < 2)
				$invalidFields[] = 'vorname';

			$lastName = $_POST['nachname'];
			if(strlen($lastName) < 2)
				$invalidFields[] = 'nachname';

			// street
			$street = trim($_POST['strasse']);
			if(strlen($street) < 3)
				$invalidFields[] = 'strasse';

			// no
			$no = trim($_POST['hnr']);
			if(strlen($no) < 1)
				$invalidFields[] = 'hnr';

			// zip
			$zip = trim($_POST['plz']);
			if(strlen($zip) < 3)
				$invalidFields[] = 'plz';

			// city
			$city = trim($_POST['ort']);
			if(strlen($city) < 3)
				$invalidFields[] = 'ort';

			// country
			$countryList = CountryList();
			$country = $countryList[$_POST['land']];

			// zip check
			if(!in_array('plz', $invalidFields)
				&& !in_array('ort', $invalidFields)
				&& $bm_prefs['plz_check'] == 'yes'
				&& !ZIPCheck($zip, $city, $_POST['land']))
			{
				$invalidFields[] = 'plz';
				$invalidFields[] = 'ort';
			}

			// company
			if($bm_prefs['f_company'] != 'n')
			{
				$company = trim($_POST['company']);
				if((strlen($company) < 2) && (strlen($company) > 0 || $bm_prefs['f_company'] == 'p'))
					$invalidFields[] = 'company';
			}
			else
				$_POST['company'] = '';

			// taxid
			if($bm_prefs['f_taxid'] != 'n')
			{
				$taxid = trim($_POST['taxid']);
				if((strlen($taxid) < 2) && (strlen($taxid) > 0 || $bm_prefs['f_taxid'] == 'p'))
					$invalidFields[] = 'taxid';
			}
			else
				$_POST['taxid'] = '';
		}

		// check payment method
		$payMethods = BMPayment::GetCustomPaymentMethods();
		if(!(
				($_POST['paymentMethod'] == PAYMENT_METHOD_PAYPAL && $bm_prefs['enable_paypal'] == 'yes')
			||	($_POST['paymentMethod'] == PAYMENT_METHOD_PAYPAL_CHECKOUT && BMPayment::PayPalCheckoutEnabled())
			||	($_POST['paymentMethod'] == PAYMENT_METHOD_STRIPE && BMPayment::StripeEnabled())
			||	($_POST['paymentMethod'] == PAYMENT_METHOD_BANKTRANSFER && $bm_prefs['enable_vk'] == 'yes')
			||	($_POST['paymentMethod'] == PAYMENT_METHOD_SOFORTUEBERWEISUNG && $bm_prefs['enable_su'] == 'yes')
			||	($_POST['paymentMethod'] == PAYMENT_METHOD_SKRILL && $bm_prefs['enable_skrill'] == 'yes')
			))
		{
			if($_POST['paymentMethod'] >= 0
				|| !isset($payMethods[abs($_POST['paymentMethod'])])
				|| $payMethods[abs($_POST['paymentMethod'])]['enabled'] != 1)
			{
				$invalidFields[] = 'paymentMethod';
			}
		}

		// check custom payment method fields
		$payMethodData = array();
		$dateFields = array();
		if($_POST['paymentMethod'] < 0)
		{
			$methodID = abs($_POST['paymentMethod']);
			if(isset($payMethods[$methodID]))
			{
				$payMethod = $payMethods[$methodID];
				foreach($payMethod['fields'] as $fieldID=>$field)
				{
					$fieldOK 	= false;
					$fieldName 	= 'field_' . $methodID . '_' . $fieldID;
					$fieldTitle	= $field['title'];
					switch($field['type'])
					{
					case FIELD_CHECKBOX:
						$fieldOK = isset($_POST['fields'][$methodID][$fieldID]);
						$payMethodData[$fieldTitle] = isset($_POST['fields'][$methodID][$fieldID]) ? $lang_user['yes'] : $lang_user['no'];
						break;
					case FIELD_DROPDOWN:
						$fieldOK = true;
						$payMethodData[$fieldTitle] = $_POST['fields'][$methodID][$fieldID];
						break;
					case FIELD_RADIO:
						$fieldOK = isset($_POST['fields'][$methodID][$fieldID]);
						if($fieldOK)
							$payMethodData[$fieldTitle] = $_POST['fields'][$methodID][$fieldID];
						break;
					case FIELD_TEXT:
						$fieldOK = empty($field['rule']) || preg_match('/'.$field['rule'].'/', $_POST['fields'][$methodID][$fieldID]);
						if(isset($_POST['fields'][$methodID][$fieldID]))
							$payMethodData[$fieldTitle] = $_POST['fields'][$methodID][$fieldID];
						break;
					case FIELD_DATE:
						$fieldOK = !empty($_POST[$fieldName.'Day'])
									&& !empty($_POST[$fieldName.'Month'])
									&& !empty($_POST[$fieldName.'Year'])
									&& $_POST[$fieldName.'Day'] != '--'
									&& $_POST[$fieldName.'Month'] != '--'
									&& $_POST[$fieldName.'Year'] != '--';
						if($fieldOK)
						{
							$payMethodData[$fieldTitle] = sprintf('%04d-%02d-%02d',
								$_POST[$fieldName.'Year'],
								$_POST[$fieldName.'Month'],
								$_POST[$fieldName.'Day']);
							$dateFields[$fieldName] = $payMethodData[$fieldTitle];
						}
						break;
					}
					if((isset($field['oblig']) || (isset($_POST['fields'][$methodID][$fieldID]) && strlen($_POST['fields'][$methodID][$fieldID]) > 0)) && (!$fieldOK))
					{
						if($field['type'] != FIELD_DATE)
						{
							$invalidFields[] = $fieldName;
						}
						else
						{
							$invalidFields[] = $fieldName . 'Day';
							$invalidFields[] = $fieldName . 'Month';
							$invalidFields[] = $fieldName . 'Year';
						}
					}
				}
			}
		}

		// error?
		if(count($invalidFields) > 0 || $fail)
		{
			$pf = &$tpl->_tpl_vars['_pf'];

			$pf['vorname']			= $_POST['vorname'];
			$pf['nachname']			= $_POST['nachname'];
			$pf['strasse']			= $_POST['strasse'];
			$pf['hnr']				= $_POST['hnr'];
			$pf['plz']				= $_POST['plz'];
			$pf['ort']				= $_POST['ort'];
			$pf['land']				= $_POST['land'];
			$pf['paymentMethod']	= $_POST['paymentMethod'];
			$pf['company']			= $_POST['company'];
			$pf['taxid']			= $_POST['taxid'];

			$pf['invalidFields']	= $invalidFields;
			$pf['dateFields']		= $dateFields;
		}

		else
		{
			$countryID = $_POST['land'];
			$countries = CountryList(true);
			$vatRate = isset($countries[$countryID]) ? $countries[$countryID]['vat'] : 0;

			$cart = BMPayment::ApplyGatewaySurcharge($cart, (int)$_POST['paymentMethod'], $vatRate);
			$totalAmount = BMPayment::CalcTotal($cart, $vatRate);

			if($_POST['paymentMethod'] == PAYMENT_METHOD_BANKTRANSFER)
				$vkCode = BMPayment::GenerateVKCode();
			else
				$vkCode = '';

			$db->Query('INSERT INTO {pre}orders(`userid`,`vkcode`,`cart`,`paymethod`,`paymethod_params`,`amount`,`tax`,`inv_firstname`,`inv_lastname`,`inv_street`,`inv_no`,`inv_zip`,`inv_city`,`inv_country`,`inv_company`,`inv_taxid`,`created`,`status`) VALUES '
					   . '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
					   $userID,
					   $vkCode,
					   serialize($cart),
					   $_POST['paymentMethod'],
					   serialize($payMethodData),
					   round($totalAmount*100, 0),
					   $bm_prefs['mwst'] == 'nomwst' ? 0 : $vatRate,
					   $bm_prefs['sendrg'] == 'yes' ? $_POST['vorname'] : '',
					   $bm_prefs['sendrg'] == 'yes' ? $_POST['nachname'] : '',
					   $bm_prefs['sendrg'] == 'yes' ? $_POST['strasse'] : '',
					   $bm_prefs['sendrg'] == 'yes' ? $_POST['hnr'] : '',
					   $bm_prefs['sendrg'] == 'yes' ? $_POST['plz'] : '',
					   $bm_prefs['sendrg'] == 'yes' ? $_POST['ort'] : '',
					   $_POST['land'],
					   $bm_prefs['sendrg'] == 'yes' ? $_POST['company'] : '',
					   $bm_prefs['sendrg'] == 'yes' ? $_POST['taxid'] : '',
					   time(),
					   ORDER_STATUS_CREATED);
			$orderId = $db->InsertId();

			if($bm_prefs['sendrg'] == 'yes'
				&& ($_POST['paymentMethod'] == PAYMENT_METHOD_BANKTRANSFER
					|| ($_POST['paymentMethod'] < 0 && $payMethods[abs($_POST['paymentMethod'])]['invoice'] == 'at_order')))
			{
				BMPayment::GenerateInvoice($orderId);
			}

			return($orderId);
		}

		return(0);
	}

	/**
	 * CKEditor stores quotes inside Smarty tags as &quot;. Smarty 5 then aborts with Unexpected "&".
	 *
	 * @param string $template
	 * @return string
	 */
	static function NormalizeInvoiceTemplate($template)
	{
		if(!is_string($template) || $template === '')
			return $template;

		if(strpos($template, '&') !== false)
		{
			$decoded = preg_replace_callback('/\{[^{}]*\}/', function($match) {
				$tag = $match[0];
				if(strpos($tag, '&') === false)
					return $tag;

				for($i = 0; $i < 3; $i++)
				{
					$next = html_entity_decode($tag, ENT_QUOTES | ENT_HTML5, 'UTF-8');
					if($next === $tag)
						break;
					$tag = $next;
				}

				return $tag;
			}, $template);
			if(is_string($decoded))
				$template = $decoded;
		}

		return self::RepairInvoiceCartLoop($template);
	}

	/**
	 * CKEditor moves the cart row behind {/foreach}. Smarty then prints only the last line.
	 *
	 * @param string $template
	 * @return string
	 */
	static function RepairInvoiceCartLoop($template)
	{
		$replaced = preg_replace(
			'/\{foreach\s+from=\$cart\s+item=pos\}((?:(?!\$pos\.).)*?)\{\/foreach\}(\s*(?:<\/p>\s*)*)(<tr\b[^>]*>.*?\$pos\..*?<\/tr>)/s',
			"{foreach from=\$cart item=pos}\n$3\n{/foreach}",
			$template,
			1,
			$count
		);

		return (is_string($replaced) && $count > 0) ? $replaced : $template;
	}

	/**
	 * Generate invoice for order
	 *
	 * @param int $orderID Order ID
	 * @param bool $regenerate Regenerate if exists?
	 * @return mixed 'false' on error or invoice HTML code on success (also stored in DB)
	 */
	static function GenerateInvoice($orderID, $regenerate = false)
	{
		global $db, $lang_user, $bm_prefs;

		// check if already generated
		if(!$regenerate)
		{
			$res = $db->Query('SELECT COUNT(*) FROM {pre}invoices WHERE `orderid`=?',
							  $orderID);
			list($invCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			if($invCount != 0)
				return(false);
		}

		// fetch order row
		$res = $db->Query('SELECT * FROM {pre}orders WHERE `orderid`=?',
						  $orderID);
		if($res->RowCount() == 0)
			return(false);
		$orderRow = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// check payment method
		if($orderRow['status'] != ORDER_STATUS_ACTIVATED)
		{
			$abort = true;

			if($orderRow['paymethod'] == PAYMENT_METHOD_BANKTRANSFER)
			{
				$abort = false;
			}
			else if($orderRow['paymethod'] < 0)
			{
				$payMethodID = abs($orderRow['paymethod']);
				$payMethods = BMPayment::GetCustomPaymentMethods();
				if(isset($payMethods[$payMethodID]) && $payMethods[$payMethodID]['invoice'] == 'at_order')
					$abort = false;
			}

			if($abort)
			{
				PutLog(sprintf('Cannot generate invoice for payment method %d in advance for order #%d',
							   $orderRow['paymethod'],
							   $orderID),
					   PRIO_WARNING,
					   __FILE__,
					   __LINE__);
				return(false);
			}
		}

		// prepare
		$invoiceNo		= BMPayment::InvoiceNo($orderID);
		$customerNo		= BMPayment::CustomerNo($orderRow['userid']);
		$amount 		= $orderRow['amount'] / 100;
		$taxRate		= $orderRow['tax'];
		$rawCart		= @unserialize($orderRow['cart']);
		if(!is_array($rawCart))
			$rawCart = array();
		$cart 			= array();
		$surchargeCents	= 0;
		$productCents	= 0;

		// prepare cart
		$pos = 0;
		foreach($rawCart as $rawPos)
		{
			$isSurcharge = self::IsSurchargeItem($rawPos);
			if($isSurcharge)
				$surchargeCents += isset($rawPos['total']) ? $rawPos['total'] : 0;
			else
				$productCents += isset($rawPos['total']) ? $rawPos['total'] : 0;

			$vatDivisor = (!$isSurcharge && $bm_prefs['mwst']=='enthalten' && $taxRate > 0)
				? (1+$taxRate/100)
				: 1;
			$cart[] = array(
				'pos'		=> ++$pos,
				'count'		=> $rawPos['count'],
				'text'		=> $rawPos['text'],
				'amount'	=> sprintf('%.02f', ($rawPos['amount']/$vatDivisor)/100),
				'total'		=> sprintf('%.02f', ($rawPos['total']/$vatDivisor)/100)
			);
		}

		$surchargeAmount = $surchargeCents / 100;
		$productGross = $amount - $surchargeAmount;
		if($productGross < 0)
			$productGross = 0;
		if($bm_prefs['mwst'] == 'add' && $taxRate > 0)
		{
			$netAmount = round($productCents / 100, 2);
			$taxAmount = round($productGross - $netAmount, 2);
		}
		else if($taxRate > 0)
		{
			$netAmount = round($productGross / (1+$taxRate/100), 2);
			$taxAmount = round($productGross - $netAmount, 2);
		}
		else
		{
			$netAmount = $productGross;
			$taxAmount = 0;
		}

		// payment note
		if($orderRow['paymethod'] == PAYMENT_METHOD_BANKTRANSFER)
		{
			$paymentNote = sprintf($lang_user['pn_banktransfer'], $amount, $bm_prefs['currency'],
				$orderRow['vkcode']);
		}
		else if(BMPayment::IsPayPalMethod($orderRow['paymethod']))
		{
			$paymentNote = sprintf($lang_user['pn_paypal'], $amount, $bm_prefs['currency']);
		}
		else if($orderRow['paymethod'] == PAYMENT_METHOD_STRIPE)
		{
			$paymentNote = sprintf($lang_user['pn_stripe'], $amount, $bm_prefs['currency'], self::StripeTitle($lang_user));
		}
		else if($orderRow['paymethod'] == PAYMENT_METHOD_SKRILL)
		{
			$paymentNote = sprintf($lang_user['pn_skrill'], $amount, $bm_prefs['currency']);
		}
		else if($orderRow['paymethod'] == PAYMENT_METHOD_SOFORTUEBERWEISUNG)
		{
			$paymentNote = sprintf($lang_user['pn_sofortueberweisung'], $amount, $bm_prefs['currency']);
		}
		else if($orderRow['paymethod'] < 0)
		{
			$payMethods = BMPayment::GetCustomPaymentMethods();
			$payMethod = $payMethods[ abs($orderRow['paymethod']) ];
			$paymentNote = sprintf($lang_user['pn_custom'], $amount, $bm_prefs['currency'], $payMethod['title']);
		}

		// create invoice template
		$rgTpl = new Template();

		// payment info
		$rgTpl->assign('currency', 	$bm_prefs['currency']);
		$rgTpl->assign('datum', 	date('d.m.Y'));
		$rgTpl->assign('rgnr', 		$invoiceNo);
		$rgTpl->assign('kdnr', 		$customerNo);
		$rgTpl->assign('netto',		sprintf('%.02f', $netAmount));
		$rgTpl->assign('brutto', 	sprintf('%.02f', $amount));
		$rgTpl->assign('mwst', 		sprintf('%.02f', $taxAmount));
		$rgTpl->assign('transaktionskosten', sprintf('%.02f', $surchargeAmount));
		$rgTpl->assign('mwstsatz', 	sprintf('%.02f', $taxRate));
		$rgTpl->assign('zahlungshinweis', $paymentNote);
		$rgTpl->assign('cart', 		$cart);

		// address
		$countryList = CountryList();
		$rgTpl->assign('vorname', 	$orderRow['inv_firstname']);
		$rgTpl->assign('nachname', 	$orderRow['inv_lastname']);
		$rgTpl->assign('strasse', 	$orderRow['inv_street']);
		$rgTpl->assign('nr', 		$orderRow['inv_no']);
		$rgTpl->assign('plz', 		$orderRow['inv_zip']);
		$rgTpl->assign('ort', 		$orderRow['inv_city']);
		$rgTpl->assign('land', 		isset($countryList[$orderRow['inv_country']])
										? $countryList[$orderRow['inv_country']]
										: '');
		$rgTpl->assign('company', 	$orderRow['inv_company']);
		$rgTpl->assign('taxid', 	$orderRow['inv_taxid']);

		// bank account info
		$rgTpl->assign('ktonr', 		$bm_prefs['vk_kto_nr']);
		$rgTpl->assign('ktoinhaber',	$bm_prefs['vk_kto_inh']);
		$rgTpl->assign('ktoblz', 		$bm_prefs['vk_kto_blz']);
		$rgTpl->assign('ktoinstitut', 	$bm_prefs['vk_kto_inst']);
		$rgTpl->assign('ktoiban', 		$bm_prefs['vk_kto_iban']);
		$rgTpl->assign('ktobic', 		$bm_prefs['vk_kto_bic']);

		// load invoice template from prefs (Smarty 5 resource plugin)
		$rgTpl->registerResource('prefsdb', new class extends \Smarty\Resource\CustomPlugin {
			protected function fetch($name, &$source, &$mtime)
			{
				global $bm_prefs;

				if(!isset($bm_prefs[$name]))
				{
					$source = null;
					$mtime = null;
					return;
				}

				$source = BMPayment::NormalizeInvoiceTemplate($bm_prefs[$name]);
				$mtime = time();
			}
		});

		// generate invoice
		$invoice = $rgTpl->fetch('prefsdb:rgtemplate');

		// save invoice
		$db->Query('REPLACE INTO {pre}invoices(`orderid`,`invoice`) VALUES(?,?)',
			$orderID,
			$invoice);

		return($invoice);
	}

	/**
	 * Generate invoice number
	 *
	 * @param int $orderID Order ID
	 * @return string
	 */
	static function InvoiceNo($orderID)
	{
		global $bm_prefs;

		return(str_replace('?', $orderID, $bm_prefs['rgnrfmt']));
	}

	/**
	 * Generate customer number
	 *
	 * @param int $userID User ID
	 * @return string
	 */
	static function CustomerNo($userID)
	{
		global $bm_prefs;

		return(str_replace('?', $userID, $bm_prefs['kdnrfmt']));
	}

	/**
	 * Generate random VK code (bank transfer code, VK-...)
	 *
	 * @return string
	 */
	static function GenerateVKCode()
	{
		$result = '';

		for($i=0; $i<VKCODE_LENGTH; $i++)
			$result .= substr(VKCODE_CHARS, mt_rand(0, strlen(VKCODE_CHARS)-1), 1);

		return($result);
	}

}
