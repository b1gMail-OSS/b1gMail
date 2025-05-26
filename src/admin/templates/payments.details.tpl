<fieldset>
	<legend>{lng p="payment"}: {$payment.invoiceNo}</legend>

	<div class="mb-3 row">
		<label class="col-sm-2 col-form-label">{lng p="invoiceno"}</label>
		<div class="col-sm-10">
			<div class="form-control-plaintext">{$payment.invoiceNo}</div>
		</div>
	</div>
	<div class="mb-3 row">
		<label class="col-sm-2 col-form-label">{lng p="customerno"}</label>
		<div class="col-sm-10">
			<div class="form-control-plaintext">{$payment.customerNo}</div>
		</div>
	</div>
	<div class="mb-3 row">
		<label class="col-sm-2 col-form-label">{lng p="user"}</label>
		<div class="col-sm-10">
			<div class="form-control-plaintext">
				<a href="users.php?do=edit&id={$payment.user.id}&sid={$sid}">{$payment.user.email}</a>
				({text value=$payment.user.vorname} {text value=$payment.user.nachname})
			</div>
		</div>
	</div>
	<div class="mb-3 row">
		<label class="col-sm-2 col-form-label">{lng p="amount"}</label>
		<div class="col-sm-10">
			<div class="form-control-plaintext">{$payment.amount}</div>
		</div>
	</div>
	<div class="mb-3 row">
		<label class="col-sm-2 col-form-label">{lng p="paymentmethod"}</label>
		<div class="col-sm-10">
			<div class="form-control-plaintext">{$payment.method}</div>
		</div>
	</div>
</fieldset>

{if $payment.paymethod_params}
	<fieldset>
		<legend>{lng p="details"}</legend>

		{foreach from=$payment.paymethod_params key=key item=value}
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{text value=$key}</label>
				<div class="col-sm-10">
					<div class="form-control-plaintext">{text value=$value}</div>
				</div>
			</div>
		{/foreach}
	</fieldset>
{/if}

<div class="row">
	<div class="col-md-4"><input class="btn" type="button" value="&laquo; {lng p="back"}" onclick="history.back(1);" /></div>
	<div class="col-md-4 text-center">{if $payment.hasInvoice}<input class="btn btn-muted" type="button" value="{lng p="invoice"}" onclick="openWindow('payments.php?action=showInvoice&orderID={$payment.orderid}&sid={$sid}','invoice_{$payment.orderid}',640,480);" />{/if}</div>
	<div class="col-md-4 text-end">{if $payment.status==0}<input class="btn btn-primary" type="button" value="{lng p="activate"}" onclick="document.location.href='payments.php?singleAction=activate&singleID={$payment.orderid}&sid={$sid}';" />{/if}</div>
</div>