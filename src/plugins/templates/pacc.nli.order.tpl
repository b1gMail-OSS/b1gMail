<form action="index.php?action=paccPlaceOrder&id={$package.id}{$sessionUrlSuffixHtml}" method="post" onsubmit="submitSignupForm()">
	{csrffield}
<input type="hidden" name="userID" value="{$userID}" />
<input type="hidden" name="userToken" value="{$userToken}" />
{if $signUp}<input type="hidden" name="signUp" value="true" />{/if}

<h2 class="h3 mb-3">{lng p="pacc_order"}: {text value=$package.titel}</h2>

<p class="text-secondary mb-4">{$package.beschreibung}</p>

{if $errorMsg}
<div class="alert alert-danger" role="alert">
	<i class="ti ti-alert-circle icon icon-sm me-1" aria-hidden="true"></i>
	{$errorMsg}
</div>
{/if}

<div class="card mb-3">
	<div class="card-header">
		<h3 class="card-title mb-0">
			<i class="ti ti-certificate icon icon-sm text-secondary me-1" aria-hidden="true"></i>
			{lng p="pacc_subscription"}
		</h3>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-8">
				<div class="mb-3">
					<label class="form-label" for="abrechnung_t">
						{lng p="pacc_runtime"}
						{if $package.abrechnung!='einmalig'&&$package.laufzeiten=='*'}<span class="text-secondary">({$intervalStr})</span>{/if}
					</label>
					{if $package.abrechnung=='einmalig'}
					<div class="form-control-plaintext">({lng p="pacc_unlimited"})</div>
					{elseif $package.laufzeiten!='*'}
					<select class="form-select" name="abrechnung_t" id="abrechnung_t" onchange="paccCalc();" onclick="paccCalc();">
						{foreach from=$package.laufzeiten item=laufzeit}
						<option value="{$laufzeit}"{if $laufzeit==$abrechnung_t} selected="selected"{/if}>{$laufzeit} {$intervalStr}</option>
						{/foreach}
					</select>
					{else}
					<input class="form-control" type="text" name="abrechnung_t" id="abrechnung_t" size="6" value="{$abrechnung_t}" onkeyup="paccCalc();" />
					{/if}
				</div>
				<div class="alert alert-info" style="display:none;" role="alert" id="runtimeNote">{$runtimeNote}</div>
			</div>
		</div>
	</div>
</div>

{if $_pf.sendrg=='yes'}
<div class="card mb-3">
	<div class="card-header">
		<h3 class="card-title mb-0">
			<i class="ti ti-id icon icon-sm text-secondary me-1" aria-hidden="true"></i>
			{lng p="pacc_invoiceaddress"}
		</h3>
	</div>
	<div class="card-body">
		<div class="row g-3">
			<div class="col-md-6">
				<label class="form-label" for="vorname">{lng p="firstname"} <span class="text-danger">*</span></label>
				<input type="text" class="form-control" required="true" name="vorname" id="vorname" value="{if isset($_pf.vorname)}{text value=$_pf.vorname allowEmpty=true}{/if}" />
			</div>
			<div class="col-md-6">
				<label class="form-label" for="nachname">{lng p="surname"} <span class="text-danger">*</span></label>
				<input type="text" class="form-control" required="true" name="nachname" id="nachname" value="{if isset($_pf.nachname)}{text value=$_pf.nachname allowEmpty=true}{/if}" />
			</div>
			{if $_pf.f_company!="n"}
			<div class="col-md-6">
				<label class="form-label" for="company">{lng p="company"}{if $_pf.f_company=="p"} <span class="text-danger">*</span>{/if}</label>
				<input type="text" class="form-control"{if $_pf.f_company=="p"} required="true"{/if} name="company" id="company" value="{if isset($_pf.company)}{text value=$_pf.company allowEmpty=true}{/if}" />
			</div>
			{/if}
			{if $_pf.f_taxid!="n"}
			<div class="col-md-6">
				<label class="form-label" for="taxid">{lng p="taxid"}{if $_pf.f_taxid=="p"} <span class="text-danger">*</span>{/if}</label>
				<input type="text" class="form-control"{if $_pf.f_taxid=="p"} required="true"{/if} name="taxid" id="taxid" value="{if isset($_pf.taxid)}{text value=$_pf.taxid allowEmpty=true}{/if}" />
			</div>
			{/if}
			<div class="col-md-8">
				<label class="form-label" for="strasse">{lng p="street"} <span class="text-danger">*</span></label>
				<input type="text" class="form-control" required="true" name="strasse" id="strasse" value="{if isset($_pf.strasse)}{text value=$_pf.strasse allowEmpty=true}{/if}" />
			</div>
			<div class="col-md-4">
				<label class="form-label" for="hnr">{lng p="nr"} <span class="text-danger">*</span></label>
				<input type="text" class="form-control" required="true" name="hnr" id="hnr" value="{if isset($_pf.hnr)}{text value=$_pf.hnr allowEmpty=true}{/if}" />
			</div>
			<div class="col-md-4">
				<label class="form-label" for="plz">{lng p="zip"} <span class="text-danger">*</span></label>
				<input type="text" class="form-control" required="true" name="plz" id="plz" value="{if isset($_pf.plz)}{text value=$_pf.plz allowEmpty=true}{/if}" />
			</div>
			<div class="col-md-8">
				<label class="form-label" for="ort">{lng p="city"} <span class="text-danger">*</span></label>
				<input type="text" class="form-control" required="true" name="ort" id="ort" value="{if isset($_pf.ort)}{text value=$_pf.ort allowEmpty=true}{/if}" />
			</div>
			<div class="col-12">
				<label class="form-label" for="land">{lng p="country"} <span class="text-danger">*</span></label>
				<select class="form-select" name="land" id="land" onclick="updatePaymentCountry(this)" onchange="updatePaymentCountry(this)">
					{foreach from=$_pf.countryList item=country key=id}
					<option value="{$id}"{if $_pf.land==$id} selected="selected"{/if}>{$country.land}</option>
					{/foreach}
				</select>
			</div>
		</div>
	</div>
</div>
{/if}

<div class="card mb-3">
	<div class="card-header">
		<h3 class="card-title mb-0">
			<i class="ti ti-credit-card icon icon-sm text-secondary me-1" aria-hidden="true"></i>
			{lng p="pacc_paymentmethod"}
		</h3>
	</div>
	<div class="card-body">
		<div class="row g-3">
			<div class="col-md-6">
				<label class="form-label" for="paymentMethod">{lng p="pacc_paymentmethod"}</label>
				{if $package.isFree}
				<input type="hidden" name="paymentMethod" value="0" /><div class="form-control-plaintext">-</div>
				{else}
				<select class="form-select" name="paymentMethod" id="paymentMethod" onclick="updatePaymentMethod(this)" onchange="updatePaymentMethod(this)">
					{if $_pf.enable_su=='yes'}<option value="2"{if $_pf.paymentMethod==2} selected="selected"{/if}>{lng p="su"}</option>{/if}
					{if $_pf.enable_paypal_checkout=='yes'}<option value="4"{if $_pf.paymentMethod==4} selected="selected"{/if}{if $_pf.surchargePaypal.enabled} data-surcharge-percent="{$_pf.surchargePaypal.percent}" data-surcharge-fixed="{$_pf.surchargePaypal.fixed}" data-surcharge-cover="{$_pf.surchargePaypal.cover}"{/if}>{lng p="paypal"}{if $_pf.surchargePaypal.enabled} ({lng p="payment_surcharge_opt"}){/if}</option>{/if}
					{if $_pf.enable_stripe=='yes'}<option value="5"{if $_pf.paymentMethod==5} selected="selected"{/if}{if $_pf.surchargeStripe.enabled} data-surcharge-percent="{$_pf.surchargeStripe.percent}" data-surcharge-fixed="{$_pf.surchargeStripe.fixed}" data-surcharge-cover="{$_pf.surchargeStripe.cover}"{/if}>{lng p="stripe"}{if $_pf.surchargeStripe.enabled} ({lng p="payment_surcharge_opt"}){/if}</option>{/if}
					{if $_pf.enable_paypal=='yes'}<option value="1"{if $_pf.paymentMethod==1} selected="selected"{/if}{if $_pf.surchargePaypal.enabled} data-surcharge-percent="{$_pf.surchargePaypal.percent}" data-surcharge-fixed="{$_pf.surchargePaypal.fixed}" data-surcharge-cover="{$_pf.surchargePaypal.cover}"{/if}>{lng p="paypal_classic"}{if $_pf.surchargePaypal.enabled} ({lng p="payment_surcharge_opt"}){/if}</option>{/if}
					{if $_pf.enable_skrill=='yes'}<option value="3"{if $_pf.paymentMethod==3} selected="selected"{/if}>{lng p="skrill"}</option>{/if}
					{if $_pf.enable_vk=='yes'}<option value="0"{if $_pf.paymentMethod===0} selected="selected"{/if}>{lng p="banktransfer"}</option>{/if}
					{foreach from=$_pf.customMethods key=methodID item=methodInfo}
					<option value="-{$methodID}"{if $_pf.paymentMethod==-$methodID} selected="selected"{/if}>{text value=$methodInfo.title}</option>
					{/foreach}
				</select>
				{if $_pf.enable_paypal=='yes' && $_pf.enable_paypal_checkout=='yes'}
				<small class="text-secondary d-block mt-1">{lng p="paypal_both_notice"}</small>
				{/if}
				{/if}
			</div>
		</div>

		{foreach from=$_pf.customMethods key=methodID item=method}<div id="paymentMethod_{$methodID}" style="display:none;" class="mt-3">
			{foreach from=$method.fields key=fieldID item=field}
			{assign var=fieldName value="field_$methodID"|cat:"_"|cat:$fieldID}
			<div class="row g-3">
				<div class="col-md-6">
					{if $field.type==1}
					<label class="form-label" for="field_{$methodID}_{$fieldID}">{text value=$field.title}{if $field.oblig} <span class="text-danger">*</span>{/if}</label>
					<input class="form-control" type="text" name="fields[{$methodID}][{$fieldID}]" id="field_{$methodID}_{$fieldID}" value="{if isset($smarty.post.fields.$methodID.$fieldID)}{text value=$smarty.post.fields.$methodID.$fieldID allowEmpty=true}{/if}" />
					{elseif $field.type==2}
					<label class="form-check"><input class="form-check-input" type="checkbox" name="fields[{$methodID}][{$fieldID}]" id="field_{$methodID}_{$fieldID}" value="true"{if $smarty.post.fields.$methodID.$fieldID} checked="checked"{/if} /><span class="form-check-label">{text value=$field.title}</span></label>
					{elseif $field.type==4}
					<label class="form-label" for="field_{$methodID}_{$fieldID}">{text value=$field.title}{if $field.oblig} <span class="text-danger">*</span>{/if}</label>
					<select class="form-select" name="fields[{$methodID}][{$fieldID}]" id="field_{$methodID}_{$fieldID}">
						{foreach from=$field.options item=fieldOption}
						<option value="{if isset($fieldOption)}{text value=$fieldOption allowEmpty=true}{/if}"{if $smarty.post.fields.$methodID.$fieldID==$fieldOption} selected="selected"{/if}>{text value=$fieldOption}</option>
						{/foreach}
					</select>
					{elseif $field.type==8}
					<div class="mb-2 fw-bold">{text value=$field.title}{if $field.oblig} <span class="text-danger">*</span>{/if}</div>
					{foreach from=$field.options key=fieldOptionID item=fieldOption}
					<label class="form-check"><input class="form-check-input" type="radio" name="fields[{$methodID}][{$fieldID}]" value="{if isset($fieldOption)}{text value=$fieldOption allowEmpty=true}{/if}" id="field_{$methodID}_{$fieldID}_{$fieldOptionID}"{if $smarty.post.fields.$methodID.$fieldID==$fieldOption} checked="checked"{/if} /><span class="form-check-label">{text value=$fieldOption}</span></label>
					{/foreach}
					{elseif $field.type==32}
					<label class="form-label">{text value=$field.title}{if $field.oblig} <span class="text-danger">*</span>{/if}</label>
					<div>{if $_pf.dateFields[$fieldName]}
						{html_select_date time=$_pf.dateFields[$fieldName] year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix=$fieldName field_order="DMY" class="form-select d-inline-block w-auto"}
					{else}
						{html_select_date time="---" year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix=$fieldName field_order="DMY" class="form-select d-inline-block w-auto"}
					{/if}</div>
					{/if}
				</div>
			</div>
			{/foreach}
		</div>{/foreach}

		<hr />

		<div class="row g-3">
			<div class="col-md-6">
				<label class="form-label">{lng p="pacc_finalamount"}</label>
				<div class="form-control fw-bold"><span id="finalAmount">-</span></div>
				<div class="text-secondary small mt-1">
					<div id="taxNote"></div>
					<div id="surchargeNote"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="alert alert-info" role="alert">
	<i class="ti ti-info-circle icon icon-sm me-1" aria-hidden="true"></i>
	{lng p="iprecord"}
</div>

<div class="d-flex flex-wrap gap-2">
	{if $signUp&&!$force}<button type="submit" name="dontOrder" class="btn btn-outline-secondary">
		<i class="ti ti-x icon icon-sm me-1" aria-hidden="true"></i>{lng p="pacc_dontorder"}
	</button>
	{elseif $signUp}<button type="submit" name="dontOrder" class="btn btn-warning">
		<i class="ti ti-x icon icon-sm me-1" aria-hidden="true"></i>{lng p="pacc_abort"}
	</button>{/if}

	<button{if $package.abrechnung!='einmalig'} disabled="disabled"{/if} type="submit" name="doOrder" id="orderButton" class="btn btn-primary ms-auto" data-loading-text="{lng p="pleasewait"}">
		<i class="ti ti-check icon icon-sm me-1" aria-hidden="true"></i>{if $package.isFree}{lng p="pacc_placeorderfree"}{else}{lng p="pacc_placeorder"}{/if}
	</button>
</div>

</form>

<script>
<!--
	var bmPayment = {ldelim}
		vatRates: {ldelim}{foreach from=$_pf.countryList item=country key=id}{if $country.vat>0}{$id}: {$country.vat},{/if}{/foreach}{rdelim},
		vatMode: '{$_pf.mwst}',
		currency: '{$_pf.currency}',
		baseAmount: {$package.preis_cent}/100,
		surcharge: {ldelim}{if isset($_pf.surchargeJs)}{$_pf.surchargeJs}{else}1:{ldelim}enabled:false,percent:0,fixed:0,cover:true{rdelim},4:{ldelim}enabled:false,percent:0,fixed:0,cover:true{rdelim},5:{ldelim}enabled:false,percent:0,fixed:0,cover:true{rdelim}{/if}{rdelim},
		surchargeNote: '{lng p="pacc_surchargenote"}'
	{rdelim};

{if $invalidFields}
{foreach from=$invalidFields item=field}
	markFieldAsInvalid('{$field}');
{/foreach}
{/if}

{if $_pf.invalidFields}
{foreach from=$_pf.invalidFields item=field}
	markFieldAsInvalid('{$field}');
{/foreach}
{/if}

{literal}
	function applyPaymentSurcharge(amount)
	{
		amount = parseFloat(amount);
		if(isNaN(amount))
			amount = 0;
		var baseCents = Math.round(amount * 100);
		var methodEl = document.getElementById('paymentMethod');
		if(!methodEl)
			return baseCents / 100;
		var cfg = null;
		if(methodEl.selectedIndex >= 0)
		{
			var opt = methodEl.options[methodEl.selectedIndex];
			if(opt && opt.getAttribute('data-surcharge-percent') != null)
				cfg = {
					enabled: true,
					percent: parseFloat(opt.getAttribute('data-surcharge-percent')) || 0,
					fixed: parseFloat(opt.getAttribute('data-surcharge-fixed')) || 0,
					cover: opt.getAttribute('data-surcharge-cover') != '0'
				};
		}
		if(!cfg && typeof bmPayment != 'undefined' && bmPayment.surcharge)
			cfg = bmPayment.surcharge[methodEl.value] || bmPayment.surcharge[parseInt(methodEl.value, 10)];
		if(!cfg || !cfg.enabled)
			return baseCents / 100;
		var percent = parseFloat(cfg.percent);
		if(isNaN(percent) || percent < 0)
			percent = 0;
		var fixed = parseFloat(cfg.fixed);
		if(isNaN(fixed) || fixed < 0)
			fixed = 0;
		var fixedCents = Math.round(fixed * 100);
		var grossCents;
		if(cfg.cover)
		{
			var rate = percent / 100;
			if(rate >= 0.99)
				rate = 0.99;
			grossCents = Math.round((baseCents + fixedCents) / (1 - rate));
		}
		else
			grossCents = baseCents + Math.round(baseCents * percent / 100) + fixedCents;
		return grossCents / 100;
	}

	function paymentSurchargeNote(baseAmount, grossAmount)
	{
		var extra = Math.round((grossAmount - baseAmount) * 100) / 100;
		if(extra <= 0 || typeof bmPayment == 'undefined')
			return '';
		var fmt = bmPayment.surchargeNote || '';
		if(!fmt || fmt.indexOf('UNKNOWN') !== -1 || fmt.indexOf('inkl') === 0 || fmt.indexOf('incl') === 0)
			fmt = 'Transaktionskosten %1 %2';
		return fmt.replace('%1', formatNumber(extra, 2)).replace('%2', bmPayment.currency);
	}

	function refreshPaymentAmount(baseAmount, taxNote)
	{
		var gross = applyPaymentSurcharge(baseAmount);
		var amountEl = document.getElementById('finalAmount');
		if(amountEl)
			amountEl.innerHTML = formatNumber(gross, 2) + ' ' + bmPayment.currency;
		var noteEl = document.getElementById('taxNote');
		if(noteEl)
			noteEl.innerHTML = taxNote || '';
		var surchargeEl = document.getElementById('surchargeNote');
		if(surchargeEl)
		{
			var surcharge = paymentSurchargeNote(baseAmount, gross);
			surchargeEl.innerHTML = surcharge;
			surchargeEl.style.display = surcharge ? '' : 'none';
		}
	}

	function updatePaymentMethod(field)
	{
		var paymentMethodID = field.value;
		var nodes = document.querySelectorAll('[id^="paymentMethod_"]');

		for(var i=0; i<nodes.length; i++)
		{
			var id = nodes[i].id.substr(14);
			if(-id == paymentMethodID)
				nodes[i].style.display = '';
			else
				nodes[i].style.display = 'none';
		}

		if(document.getElementById('land'))
			updatePaymentCountry(document.getElementById('land'));
		else if(typeof refreshPaymentAmount == 'function')
			refreshPaymentAmount(bmPayment.baseAmount, '');
	}

	function formatNumber(num, decimals)
	{
		return parseFloat(num).toFixed(decimals).replace('.', lang['decsep']);
	}

	function updatePaymentCountry(field)
	{
		var countryID = field.value;
		var amount = 0, tax = 0, taxRate = 0, showTaxNote = false, taxNote = '';

		if(typeof(bmPayment.vatRates[countryID]) != 'undefined')
		{
			taxRate = bmPayment.vatRates[countryID];
			tax = bmPayment.baseAmount * (taxRate / 100);

			if(bmPayment.vatMode == 'enthalten')
			{
				amount = bmPayment.baseAmount;
				showTaxNote = true;
			}
			else if(bmPayment.vatMode == 'add')
			{
				amount = bmPayment.baseAmount + tax;
				showTaxNote = true;
			}
			else if(bmPayment.vatMode == 'nomwst')
			{
				amount = bmPayment.baseAmount;
				showTaxNote = false;
			}
		}
		else
		{
			amount = bmPayment.baseAmount;
			showTaxNote = false;
		}

		if(showTaxNote)
			taxNote = lang['taxnote'].replace('%1', formatNumber(taxRate, 2));

		if(typeof refreshPaymentAmount == 'function')
			refreshPaymentAmount(amount, taxNote);
		else
		{
			document.getElementById('finalAmount').innerHTML = formatNumber(amount, 2) + ' ' + bmPayment.currency;
			document.getElementById('taxNote').innerHTML = taxNote;
		}
	}

	function paccCalc()
	{
		var f = $('#abrechnung_t'), i, multiplier = {/literal}{$package.abrechnung_t}{literal},
			amount_base = {/literal}{$package.preis_cent}{literal}, note = $('#runtimeNote'), amount,
			order_button = $('#orderButton');

		{/literal}{if $package.abrechnung!='einmalig'}{literal}
		if(isNaN(f.val()) || f.val().indexOf('.')>=0 || (i=parseInt(f.val())) < 1
			|| i%multiplier != 0)
		{
			f.closest('.mb-3').addClass('has-error');
			note.show();
			order_button.prop('disabled', true);
			i = multiplier;
		}
		else
		{
			f.closest('.mb-3').removeClass('has-error');
			note.hide();
			order_button.prop('disabled', false);
		}

		amount = ((i/multiplier) * amount_base) / 100;
		amount = Math.round(amount*100) / 100;
		bmPayment.baseAmount = amount;
		{/literal}{/if}{literal}

		if(document.getElementById('paymentMethod'))
			updatePaymentMethod(document.getElementById('paymentMethod'));

		if(document.getElementById('land'))
		{
			updatePaymentCountry(document.getElementById('land'));
		}
		else
		{
			if(typeof refreshPaymentAmount == 'function')
				refreshPaymentAmount(amount, '');
			else
			{
				document.getElementById('finalAmount').innerHTML = formatNumber(amount, 2) + ' ' + bmPayment.currency;
				document.getElementById('taxNote').innerHTML = '';
			}
		}
	}

	$(document).ready(paccCalc);
//-->
</script>{/literal}
