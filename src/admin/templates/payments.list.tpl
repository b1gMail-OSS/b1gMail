<form action="payments.php?filter=true&sid={$sid}" method="post" onsubmit="if(EBID('massAction').value!='download') spin(this)" name="f1">
<input type="hidden" name="page" id="page" value="{$pageNo}" />
<input type="hidden" name="sortBy" id="sortBy" value="{$sortBy}" />
<input type="hidden" name="sortOrder" id="sortOrder" value="{$sortOrder}" />
<input type="hidden" name="singleAction" id="singleAction" value="" />
<input type="hidden" name="singleID" id="singleID" value="" />

<fieldset>
	<legend>{lng p="payments"}</legend>

	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'payment[');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th><a href="javascript:updateSort('userid');">{lng p="user"}
				{if $sortBy=='userid'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th><a href="javascript:updateSort('orderid');">{lng p="orderno"}
				{if $sortBy=='orderid'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="135"><a href="javascript:updateSort('amount');">{lng p="amount"}
				{if $sortBy=='amount'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="145"><a href="javascript:updateSort('created');">{lng p="date"}
				{if $sortBy=='created'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="65">&nbsp;</th>
		</tr>

		{foreach from=$payments item=payment}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="templates/images/{if $payment.status==1}yes{else}no{/if}.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center"><input type="checkbox" name="payment[{$payment.orderid}]" /></td>
			<td><a href="users.php?do=edit&id={$payment.user.id}&sid={$sid}">{email value=$payment.user.email cut=25}</a><br />
				<small>{text value=$payment.user.nachname cut=20}, {text value=$payment.user.vorname cut=20}</small></td>
			<td>{text value=$payment.invoiceNo}<br /><small>{text value=$payment.customerNo}</small></td>
			<td>
				<div style="float:left;">
					{$payment.amount}<br /><small>{$payment.method}</small>
				</div>
				{if $payment.paymethod<0}
				<div style="float:right;">
					<a href="payments.php?do=details&orderid={$payment.orderid}&sid={$sid}" title="{lng p="details"}"><img src="{$tpldir}images/ico_prefs_payments.png" border="0" alt="{lng p="details"}" width="16" height="16" /></a>
				</div>
				{/if}
			</td>
			<td>{date timestamp=$payment.created nice=true}</td>
			<td>
				{if $payment.hasInvoice}<a href="javascript:void(0);" onclick="openWindow('payments.php?action=showInvoice&orderID={$payment.orderid}&sid={$sid}','invoice_{$payment.orderid}',640,480);" title="{lng p="showinvoice"}"><img src="{$tpldir}images/file.png" border="0" alt="{lng p="showinvoice"}" width="16" height="16" /></a>{/if}
				{if $payment.status==0}<a href="{if $payment.paymethod<0}payments.php?do=details&orderid={$payment.orderid}&sid={$sid}{else}javascript:singleAction('activate', '{$payment.orderid}');{/if}" title="{lng p="activatepayment"}"><img src="{$tpldir}images/unlock.png" border="0" alt="{lng p="activatepayment"}" width="16" height="16" /></a>{/if}
				<a href="javascript:singleAction('delete', '{$payment.orderid}');" onclick="return confirm('{lng p="realdel"}');" title="{lng p="delete"}"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}

		<tr>
			<td class="footer" colspan="7">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" id="massAction" class="smallInput">
						<option value="-">------------</option>

						<optgroup label="{lng p="actions"}">
							<option value="download">{lng p="downloadinvoices"}</option>
							<option value="activate">{lng p="activatepayment"}</option>
							<option value="delete">{lng p="delete"}</option>
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
				<div style="float:right;padding-top:3px;">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
				</div>
			</td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="filter"}</legend>

	<table width="100%">
		<tr>
			<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/filter.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="100">{lng p="status"}:</td>
			<td class="td2">
				<input type="checkbox" name="status[0]" id="status_0" {if $status[0]} checked="checked"{/if} />
					<label for="status_0"><b>{lng p="orderstatus_0"}</b></label><br />
				<input type="checkbox" name="status[1]" id="status_1" {if $status[1]} checked="checked"{/if} />
					<label for="status_1"><b>{lng p="orderstatus_1"}</b></label><br />
			</td>
			<td class="td1" width="130">{lng p="paymentmethods"}:</td>
			<td class="td2">
				<input type="checkbox" name="paymentMethod[0]" id="paymentMethod_0" {if $paymentMethod[0]} checked="checked"{/if} />
					<label for="paymentMethod_0"><b>{lng p="banktransfer"}</b></label><br />
				<input type="checkbox" name="paymentMethod[1]" id="paymentMethod_1" {if $paymentMethod[1]} checked="checked"{/if} />
					<label for="paymentMethod_1"><b>{lng p="paypal"}</b></label><br />
				<input type="checkbox" name="paymentMethod[2]" id="paymentMethod_2" {if $paymentMethod[2]} checked="checked"{/if} />
					<label for="paymentMethod_2"><b>{lng p="su"}</b></label><br />
				<input type="checkbox" name="paymentMethod[3]" id="paymentMethod_3" {if $paymentMethod[3]} checked="checked"{/if} />
					<label for="paymentMethod_3"><b>{lng p="skrill"}</b></label><br />
				{foreach from=$payMethods key=methodID item=method}
				<input type="checkbox" name="paymentMethod[-{$methodID}]" id="paymentMethod_-{$methodID}" {if $paymentMethod[$method.negID]} checked="checked"{/if} />
					<label for="paymentMethod_-{$methodID}"><b>{text value=$method.title}</b></label><br />
				{/foreach}
			</td>
		</tr>
	</table>

	<p align="right">
		{lng p="perpage"}:
		<input type="text" name="perPage" value="{$perPage}" size="5" />
		<input class="button" type="submit" value=" {lng p="apply"} " />
	</p>
</fieldset>

</form>

<fieldset>
	<legend>{lng p="activatepayment"}</legend>

	<table>
		<tr>
			<td align="left" rowspan="3" valign="top" width="40"><img src="templates/images/ico_prefs_payments.png" border="0" alt="" width="32" height="32" /></td>
			<td colspan="2">{lng p="activate_desc"}</td>
		</tr>
		<tr>
			<td class="td1" width="120">{lng p="vkcode"}:</td>
			<td class="td2"><input type="text" name="vkCode" id="vkCode" value="VK-" size="26" onkeypress="return handleActivatePaymentInput(event, 0);"  /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="amount"}:</td>
			<td class="td2"><input type="text" name="amount" id="amount" value="" size="10" onkeypress="return handleActivatePaymentInput(event, 1);" /> {text value=$bm_prefs.currency}</td>
		</tr>
	</table>

	<p>
		<div style="float:left;font-weight:bold;padding-top:4px;" id="activationResult">&nbsp;</div>
		<div style="float:right">
			<input class="button" type="button" onclick="activatePayment()" id="activateButton" value=" {lng p="activate"} " />
		</div>
	</p>
</fieldset>
