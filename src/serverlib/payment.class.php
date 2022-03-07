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
	 * Prepares payment form (payment.form.tpl)
	 *
	 * @param &$tpl Template object
	 * @param $title Payment form title
	 * @param $amount Payment amount
	 */
	function PreparePaymentForm(&$tpl, $title, $amount, $userRow = false)
	{
		global $bm_prefs, $thisUser, $lang_user;

		if($userRow === false)
			$userRow = $thisUser->_row;

		$pf = array();

		$pf['title']			= $title;
		$pf['sendrg']			= $bm_prefs['sendrg'];
		$pf['currency']			= $bm_prefs['currency'];
		$pf['enable_su']		= $bm_prefs['enable_su'];
		$pf['enable_vk']		= $bm_prefs['enable_vk'];
		$pf['enable_paypal']	= $bm_prefs['enable_paypal'];
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

		$tpl->assign('_pf', $pf);
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

		foreach($cart as $cartItem)
			$amount += $cartItem['total'];

		if($bm_prefs['mwst'] == 'add')
			$amount = round($amount * (1+$vatRate/100), 0);

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
	function ActivateOrderWithVKCode($vkCode, $paidAmount)
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

		// process cart
		$cart = @unserialize($row['cart']);
		if(!is_array($cart)) $cart = array();

		foreach($cart as $cartItemID=>$cartItem)
		{
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
			if(in_array($row['paymethod'], array(PAYMENT_METHOD_PAYPAL, PAYMENT_METHOD_SKRILL, PAYMENT_METHOD_SOFORTUEBERWEISUNG)))
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
			BMPayment::GenerateInvoice($orderID);

		// payment notification?
		if($bm_prefs['send_pay_notification'] == 'yes' && $row['amount'] > 0)
		{
			if($row['paymethod'] == PAYMENT_METHOD_BANKTRANSFER)
				$payMethod = $lang_user['banktransfer'];
			else if($row['paymethod'] == PAYMENT_METHOD_PAYPAL)
				$payMethod = $lang_user['paypal'];
			else if($row['paymethod'] == PAYMENT_METHOD_SOFORTUEBERWEISUNG)
				$payMethod = $lang_user['su'];
			else if($row['paymethod'] == PAYMENT_METHOD_SKRILL)
				$payMethod = $lang_user['skrill'];
			else if($row['paymethod'] < 0)
				$payMethod = $payMethods[$payMethodID]['title'];
			else
				$payMethod = '?';

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

		$res = $db->Query('SELECT `paymethod`,`amount`,`status`,`cart`,`vkcode` FROM {pre}orders WHERE `userid`=? AND `orderid`=?',
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
		}

		// check payment method
		$payMethods = BMPayment::GetCustomPaymentMethods();
		if(!(
				($_POST['paymentMethod'] == PAYMENT_METHOD_PAYPAL && $bm_prefs['enable_paypal'] == 'yes')
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

			$totalAmount = BMPayment::CalcTotal($cart, $vatRate);

			if($_POST['paymentMethod'] == PAYMENT_METHOD_BANKTRANSFER)
				$vkCode = BMPayment::GenerateVKCode();
			else
				$vkCode = '';

			$db->Query('INSERT INTO {pre}orders(`userid`,`vkcode`,`cart`,`paymethod`,`paymethod_params`,`amount`,`tax`,`inv_firstname`,`inv_lastname`,`inv_street`,`inv_no`,`inv_zip`,`inv_city`,`inv_country`,`inv_firma`,`inv_taxid`,`created`,`status`) VALUES '
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
		$netAmount		= round($amount / (1+$taxRate/100), 2);
		$taxAmount		= $amount - $netAmount;
		$rawCart		= unserialize($orderRow['cart']);
		$cart 			= array();

		// prepare cart
		$pos = 0;
		foreach($rawCart as $rawPos)
		{
			$cart[] = array(
				'pos'		=> ++$pos,
				'count'		=> $rawPos['count'],
				'text'		=> $rawPos['text'],
				'amount'	=> sprintf('%.02f', ($rawPos['amount']/($bm_prefs['mwst']=='enthalten'?(1+$taxRate/100):1))/100),
				'total'		=> sprintf('%.02f', ($rawPos['total']/($bm_prefs['mwst']=='enthalten'?(1+$taxRate/100):1))/100)
			);
		}

		// payment note
		if($orderRow['paymethod'] == PAYMENT_METHOD_BANKTRANSFER)
		{
			$paymentNote = sprintf($lang_user['pn_banktransfer'], $amount, $bm_prefs['currency'],
				$orderRow['vkcode']);
		}
		else if($orderRow['paymethod'] == PAYMENT_METHOD_PAYPAL)
		{
			$paymentNote = sprintf($lang_user['pn_paypal'], $amount, $bm_prefs['currency']);
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

		// set input resource
		$bmPayment = _new('BMPayment');
		$rgTpl->register_resource('prefsdb', array(&$bmPayment, '__tpl_getTemplate', '__tpl_getTimestamp', '__tpl_getSecure', '__tpl_getTrusted'));

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

	//
	// smarty callbacks
	//
	function __tpl_getTemplate($tpl_name, &$tpl_source, &$smarty_obj)
	{
		global $bm_prefs;
		if(!isset($bm_prefs[$tpl_name]))
			return(false);
		$tpl_source = $bm_prefs[$tpl_name];
		return(true);
	}
	function __tpl_getTimestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		global $bm_prefs;
		if(!isset($bm_prefs[$tpl_name]))
			return(false);
		$tpl_timestamp = time();
		return(true);
	}
	function __tpl_getSecure($tpl_name, &$smarty_obj)
	{
		global $bm_prefs;
		if(!isset($bm_prefs[$tpl_name]))
			return(false);
		return(true);
	}
	function __tpl_getTrusted($tpl_name, &$smarty_obj)
	{
		global $bm_prefs;
		if(!isset($bm_prefs[$tpl_name]))
			return(false);
		return(true);
	}
}
