<form action="payments.php?filter=true&sid={$sid}" method="post" onsubmit="if(EBID('massAction').value!='download') spin(this)" name="f1">
	<input type="hidden" name="page" id="page" value="{$pageNo}" />
	<input type="hidden" name="sortBy" id="sortBy" value="{$sortBy}" />
	<input type="hidden" name="sortOrder" id="sortOrder" value="{$sortOrder}" />
	<input type="hidden" name="singleAction" id="singleAction" value="" />
	<input type="hidden" name="singleID" id="singleID" value="" />

	<fieldset>
		<legend>{lng p="payments"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
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
					</thead>
					<tbody>
					{foreach from=$payments item=payment}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center">{if $payment.status==1}<i class="fa-regular fa-circle-check text-green"></i>{else}<i class="fa-regular fa-circle-xmark text-red"></i>{/if}</td>
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
							<td class="text-nowrap text-end">
								<div class="btn-group btn-group-sm">
									{if $payment.hasInvoice}<a href="javascript:void(0);" onclick="openWindow('payments.php?action=showInvoice&orderID={$payment.orderid}&sid={$sid}','invoice_{$payment.orderid}',640,480);" title="{lng p="showinvoice"}" class="btn btn-sm"><i class="fa-solid fa-file-invoice-dollar"></i></a>{/if}
									{if $payment.status==0}<a href="{if $payment.paymethod<0}payments.php?do=details&orderid={$payment.orderid}&sid={$sid}{else}javascript:singleAction('activate', '{$payment.orderid}');{/if}" title="{lng p="activatepayment"}" class="btn btn-sm"><i class="fa-solid fa-lock-open"></i></a>{/if}
									<a href="javascript:singleAction('delete', '{$payment.orderid}');" onclick="return confirm('{lng p="realdel"}');" title="{lng p="delete"}" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div style="float: left;">{lng p="action"}:&nbsp;</div>
				<div style="float: left;">
					<div class="btn-group btn-group-sm">
						<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="download">{lng p="downloadinvoices"}</option>
								<option value="activate">{lng p="activatepayment"}</option>
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>
						<input type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" />
					</div>
				</div>
				<div class="text-end">{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;</div>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="filter"}</legend>

		<div class="row">
			<div class="col-md-6">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="status"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="status[0]" id="status_0" {if $status[0]} checked="checked"{/if}>
							<span class="form-check-label">{lng p="orderstatus_0"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="status[1]" id="status_1" {if $status[1]} checked="checked"{/if}>
							<span class="form-check-label">{lng p="orderstatus_1"}</span>
						</label>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="paymentmethods"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="paymentMethod[0]" id="paymentMethod_0" {if $paymentMethod[0]} checked="checked"{/if}>
							<span class="form-check-label">{lng p="banktransfer"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="paymentMethod[1]" id="paymentMethod_1" {if $paymentMethod[1]} checked="checked"{/if}>
							<span class="form-check-label">{lng p="paypal"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="paymentMethod[2]" id="paymentMethod_2" {if $paymentMethod[2]} checked="checked"{/if}>
							<span class="form-check-label">{lng p="su"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="paymentMethod[3]" id="paymentMethod_3" {if $paymentMethod[3]} checked="checked"{/if}>
							<span class="form-check-label">{lng p="skrill"}</span>
						</label>
						{foreach from=$payMethods key=methodID item=method}
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="paymentMethod[-{$methodID}]" id="paymentMethod_-{$methodID}" {if $paymentMethod[$method.negID]} checked="checked"{/if}>
								<span class="form-check-label">{text value=$method.title}</span>
							</label>
						{/foreach}
					</div>
				</div>
			</div>
		</div>

		<div class="text-end">
			<div style="float: right;"><input class="btn btn-sm btn-primary" type="submit" value="{lng p="apply"}" /></div>
			<div style="float: right;"><input type="text" class="form-control form-control-sm" name="perPage" value="{$perPage}" size="5" />&nbsp; </div>
			<div style="float: right;">{lng p="perpage"}:&nbsp; </div>
		</div>
	</fieldset>

</form>

<fieldset>
	<legend>{lng p="activatepayment"}</legend>

	<div id="activationResult">&nbsp;</div>

	<p>{lng p="activate_desc"}</p>

	<div class="mb-3 row">
		<label class="col-sm-2 col-form-label">{lng p="vkcode"}</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="vkCode" id="vkCode" value="VK-" onkeypress="return handleActivatePaymentInput(event, 0);" placeholder="{lng p="vkcode"}">
		</div>
	</div>
	<div class="mb-3 row">
		<label class="col-sm-2 col-form-label">{lng p="amount"}</label>
		<div class="col-sm-10">
			<div class="input-group mb-2">
				<input type="text" class="form-control" name="amount" id="amount" value="" onkeypress="return handleActivatePaymentInput(event, 1);" placeholder="{lng p="amount"}">
				<span class="input-group-text">{text value=$bm_prefs.currency}</span>
			</div>
		</div>
	</div>

	<div class="text-end">
		<input class="btn btn-primary" type="button" onclick="activatePayment()" id="activateButton" value="{lng p="activate"}" />
	</div>
</fieldset>
