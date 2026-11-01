<fieldset>
	<legend>{lng p="accentries"}</legend>
	
	<form action="{sessionurl file='payments.php' params="action=export&do=exportAccEntries"}" method="post" target="_top">
		{csrffield}
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="from"}</label>
			<div class="col-sm-10">
				<div class="input-group">
					<span class="input-group-text"><i class="icon ti ti-calendar"></i></span>
					<input type="text" class="form-control" id="export-acc-start" name="start" data-bs-toggle="datepicker" data-bs-position-element="#export-acc-start" data-bs-date-format="DD.MM.YYYY" data-bs-locale="de" autocomplete="off" value="{date timestamp=$start format="d.m.Y"}" placeholder="TT.MM.JJJJ" aria-label="{lng p="from"}">
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="to"}</label>
			<div class="col-sm-10">
				<div class="input-group">
					<span class="input-group-text"><i class="icon ti ti-calendar"></i></span>
					<input type="text" class="form-control" id="export-acc-end" name="end" data-bs-toggle="datepicker" data-bs-position-element="#export-acc-end" data-bs-date-format="DD.MM.YYYY" data-bs-locale="de" autocomplete="off" value="{date timestamp=$end format="d.m.Y"}" placeholder="TT.MM.JJJJ" aria-label="{lng p="to"}">
				</div>
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
					<input type="text" class="form-control" name="accounts[4]" value="1101" placeholder="{lng p="paypal"}">
				</div>
				<div class="input-group mb-2">
					<span class="input-group-text">{lng p="stripe"}</span>
					<input type="text" class="form-control" name="accounts[5]" value="1102" placeholder="{lng p="stripe"}">
				</div>
				<div class="input-group mb-2">
					<span class="input-group-text">{lng p="paypal_classic"}</span>
					<input type="text" class="form-control" name="accounts[1]" value="1101" placeholder="{lng p="paypal_classic"}">
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
	
	<form action="{sessionurl file='payments.php' params="action=export&do=exportInvoices"}" method="post" target="_top">
		{csrffield}
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="from"}</label>
			<div class="col-sm-10">
				<div class="input-group">
					<span class="input-group-text"><i class="icon ti ti-calendar"></i></span>
					<input type="text" class="form-control" id="export-inv-start" name="start" data-bs-toggle="datepicker" data-bs-position-element="#export-inv-start" data-bs-date-format="DD.MM.YYYY" data-bs-locale="de" autocomplete="off" value="{date timestamp=$start format="d.m.Y"}" placeholder="TT.MM.JJJJ" aria-label="{lng p="from"}">
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="to"}</label>
			<div class="col-sm-10">
				<div class="input-group">
					<span class="input-group-text"><i class="icon ti ti-calendar"></i></span>
					<input type="text" class="form-control" id="export-inv-end" name="end" data-bs-toggle="datepicker" data-bs-position-element="#export-inv-end" data-bs-date-format="DD.MM.YYYY" data-bs-locale="de" autocomplete="off" value="{date timestamp=$end format="d.m.Y"}" placeholder="TT.MM.JJJJ" aria-label="{lng p="to"}">
				</div>
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
