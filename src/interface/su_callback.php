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

PutLog('SU-Callback: Called',
	PRIO_DEBUG,
	__FILE__,
	__LINE__);

//
// check input vars
//
if(!isset($_POST['hash']))
{
	PutLog('SU-Callback: Missing input variables',
		PRIO_DEBUG,
		__FILE__,
		__LINE__);
	die('Error: One or more missing input variables.');
}

//
// build origin check hash
//
$vars = array(
	'transaction', 'user_id', 'project_id', 'sender_holder',
	'sender_account_number', 'sender_bank_code', 'sender_bank_name',
	'sender_bank_bic', 'sender_iban', 'sender_country_id',
	'recipient_holder', 'recipient_account_number', 'recipient_bank_code',
	'recipient_bank_name', 'recipient_bank_bic', 'recipient_iban',
	'recipient_country_id', 'international_transaction', 'amount',
	'currency_id', 'reason_1', 'reason_2', 'security_criteria',
	'user_variable_0', 'user_variable_1', 'user_variable_2', 'user_variable_3',
	'user_variable_4', 'user_variable_5', 'created'
);
$values = array();
foreach($vars as $var)
{
	if(!isset($_POST[$var]))
		PutLog(sprintf('SU-Callback: Undefined SU variable: %s', $var),
			PRIO_DEBUG,
			__FILE__,
			__LINE__);
	$values[] = $_POST[$var];
}
if(trim($bm_prefs['su_notifypass']) != '')
	$values[] = $bm_prefs['su_notifypass'];
else
	$values[] = $bm_prefs['su_prjpass'];
$hash = md5(implode('|', $values));

//
// log
//
PutLog(sprintf('SU-Callback: SU hashes: given=%s, calculated=%s',
	$_POST['hash'],
	$hash),
	PRIO_DEBUG,
	__FILE__,
	__LINE__);

//
// check hash
//
if($_POST['hash'] !== $hash)
{
	PutLog('SU-Callback: sofortueberweisung.de hash comparison failed (wrong project password?)',
		PRIO_WARNING,
		__FILE__,
		__LINE__);
	die('Error: Invalid hash');
}

//
// check account + project numbers
//
if($_POST['user_id'] != $bm_prefs['su_kdnr']
	|| $_POST['project_id'] != $bm_prefs['su_prjnr'])
{
	PutLog('SU-Callback: User ID / project ID transmitted by sofortueberweisung.de does not match our records',
		PRIO_WARNING,
		__FILE__,
		__LINE__);
	die('Error: Invalid user id / project id');
}

//
// check currency
//
if($_POST['currency_id'] != $bm_prefs['currency'])
{
	PutLog(sprintf('SU-Callback: Wrong currency (%s != %s)', $_POST['currency_id'], $bm_prefs['currency']),
		   PRIO_WARNING,
		   __FILE__,
		   __LINE__);
	die('Error: Invalid currency');
}

//
// process payment
//
if(BMPayment::ActivateOrder($_POST['user_variable_0'], round($_POST['amount'], 2)*100))
{
	PutLog(sprintf('SU payment (%d) accepted',
				   $_POST['user_variable_0']),
		   PRIO_NOTE,
		   __FILE__,
		   __LINE__);
	die('OK');
}
else
{
	PutLog(sprintf('SU payment (%d) rejected by BMPayment API',
				   $_POST['user_variable_0']),
		   PRIO_WARNING,
		   __FILE__,
		   __LINE__);
	die('Error: Order activation failed');
}
