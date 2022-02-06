<fieldset>
	<legend>{lng p="payment"}: {$payment.invoiceNo}</legend>

	<table>
		<tr>
			<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/ico_prefs_payments.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="180">{lng p="invoiceno"}:</td>
			<td class="td2">{$payment.invoiceNo}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="customerno"}:</td>
			<td class="td2">{$payment.customerNo}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="user"}:</td>
			<td class="td2">
				<img src="{$tpldir}images/user_action.png" width="16" height="16" align="absmiddle" border="0" alt="" />
				<a href="users.php?do=edit&id={$payment.user.id}&sid={$sid}">{$payment.user.email}</a>
				({text value=$payment.user.vorname} {text value=$payment.user.nachname})
			</td>
		</tr>
		<tr>
			<td class="td1">{lng p="amount"}:</td>
			<td class="td2">{$payment.amount}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="paymentmethod"}:</td>
			<td class="td2">{$payment.method}</td>
		</tr>
	</table>
</fieldset>

{if $payment.paymethod_params}
<fieldset>
	<legend>{lng p="details"}</legend>
	<table>
		{foreach from=$payment.paymethod_params key=key item=value}
		<tr>
			<td width="40">&nbsp;</td>
			<td class="td1" width="180">{text value=$key}</td>
			<td class="td2">{text value=$value}</td>
		</tr>
		{/foreach}
	</table>
</fieldset>	
{/if}

<p>
	<div style="float:left" class="buttons">
		<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="history.back(1);" />
	</div>

	<div style="float:right" class="buttons">
		{if $payment.hasInvoice}
		<input class="button" type="button" value=" {lng p="invoice"} " onclick="openWindow('payments.php?action=showInvoice&orderID={$payment.orderid}&sid={$sid}','invoice_{$payment.orderid}',640,480);" />
		{/if}

		{if $payment.status==0}
		<input class="button" type="button" value=" {lng p="activate"} " onclick="document.location.href='payments.php?singleAction=activate&singleID={$payment.orderid}&sid={$sid}';" />
		{/if}
	</div>
</p>
