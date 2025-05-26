<fieldset>
	<legend>{lng p="accentries"}</legend>
	
	<form action="payments.php?action=export&do=exportAccEntries&sid={$sid}" method="post" target="_top">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="from"}</label>
			<div class="col-sm-10">
				{html_select_date prefix="start" time=$start start_year="-5" field_order="DMY" field_separator="."}
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="to"}</label>
			<div class="col-sm-10">
				{html_select_date prefix="end" time=$end start_year="-5" field_order="DMY" field_separator="."}
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="account_debit"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<span class="input-group-text">{lng p="banktransfer"}</span>
					<input type="text" class="form-control" name="accounts[0]" value="1100" placeholder="{lng p="banktransfer"}">
				</div>
				<div class="input-group mb-2">
					<span class="input-group-text">{lng p="su"}</span>
					<input type="text" class="form-control" name="accounts[2]" value="1100" placeholder="{lng p="su"}">
				</div>
				<div class="input-group mb-2">
					<span class="input-group-text">{lng p="paypal"}</span>
					<input type="text" class="form-control" name="accounts[1]" value="1101" placeholder="{lng p="paypal"}">
				</div>
				<div class="input-group mb-2">
					<span class="input-group-text">{lng p="skrill"}</span>
					<input type="text" class="form-control" name="accounts[2]" value="1100" placeholder="{lng p="skrill"}">
				</div>
				{foreach from=$paymentMethods key=methodID item=method}
				<div class="input-group mb-2">
					<span class="input-group-text">{text value=$method.title}</span>
					<input type="text" class="form-control" name="accounts[-{$methodID}]" value="{$methodID+1102}" placeholder="{text value=$method.title}">
				</div>
				{/foreach}
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="account_credit"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="account" value="8400" placeholder="{lng p="account_credit"}">
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="export"}" />
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="invoices"}</legend>
	
	<form action="payments.php?action=export&do=exportInvoices&sid={$sid}" method="post" target="_top">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="from"}</label>
			<div class="col-sm-10">
				{html_select_date prefix="start" time=$start start_year="-5" field_order="DMY" field_separator="."}
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="to"}</label>
			<div class="col-sm-10">
				{html_select_date prefix="end" time=$end start_year="-5" field_order="DMY" field_separator="."}
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="options"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="paidOnly" id="paidOnly" checked="checked">
					<span class="form-check-label">{lng p="paidonly"}</span>
				</label>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="export"}" />
		</div>
	</form>
</fieldset>
