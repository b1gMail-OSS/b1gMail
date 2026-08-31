<div class="bm-prefs-page bm-prefs-page-payment">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-shopping-cart icon icon-sm" aria-hidden="true"></i>
		<a href="{sessionurl file='prefs.php' params='action=orders'}">{lng p="order"}</a>: {text value=$_pf.invoiceNo}
	</div>
</div>

<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">

{if $_pf.payMethod==0}
<div class="card bm-prefs-payment-card">
	<div class="card-body">
		<p class="text-secondary mb-4">{$_pf.ktoText}</p>
		<div class="row g-4">
			<div class="col-md-6">
				<p class="mb-3">
					<strong>{lng p="kto_inh"}:</strong><br />
					{text value=$_pf.ktoInh}
				</p>
				<p class="mb-3">
					<strong>{lng p="kto_blz"} ({lng p="kto_inst"}):</strong><br />
					{text value=$_pf.ktoBLZ} ({text value=$_pf.ktoInst})
				</p>
				{if $_pf.ktoIBAN&&$_pf.ktoBIC}
				<p class="mb-3 text-secondary">
					<strong>{lng p="kto_iban"}:</strong><br />
					{text value=$_pf.ktoIBAN}
				</p>
				{/if}
				<p class="mb-0">
					<strong>{lng p="kto_subject"}:</strong><br />
					{text value=$_pf.ktoSubject}
				</p>
			</div>
			<div class="col-md-6">
				<p class="mb-3">
					<strong>{lng p="kto_nr"}:</strong><br />
					{text value=$_pf.ktoNr}
				</p>
				{if $_pf.ktoIBAN&&$_pf.ktoBIC}
				<p class="mb-3 text-secondary">
					<strong>{lng p="kto_bic"}:</strong><br />
					{text value=$_pf.ktoBIC}
				</p>
				{/if}
				<p class="mb-0">
					<strong>{lng p="amount"}:</strong><br />
					{$_pf.amount}
				</p>
			</div>
		</div>
	</div>
</div>
{else}
{assign var=omitTable value=true}
{include file="li/payment.pay.tpl"}
{/if}

</div></div>
</div>
