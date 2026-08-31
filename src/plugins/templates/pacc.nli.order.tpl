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
					{if $_pf.enable_paypal=='yes'}<option value="1"{if $_pf.paymentMethod==1} selected="selected"{/if}>{lng p="paypal"}</option>{/if}
					{if $_pf.enable_skrill=='yes'}<option value="3"{if $_pf.paymentMethod==3} selected="selected"{/if}>{lng p="skrill"}</option>{/if}
					{if $_pf.enable_vk=='yes'}<option value="0"{if $_pf.paymentMethod===0} selected="selected"{/if}>{lng p="banktransfer"}</option>{/if}
					{foreach from=$_pf.customMethods key=methodID item=methodInfo}
					<option value="-{$methodID}"{if $_pf.paymentMethod==-$methodID} selected="selected"{/if}>{text value=$methodInfo.title}</option>
					{/foreach}
				</select>
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
				<label class="form-label">{lng p="pacc_finalamount"} <span class="text-secondary" id="taxNote"></span></label>
				<div class="form-control fw-bold"><span id="finalAmount">-</span></div>
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
		baseAmount: {$package.preis_cent}/100
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

		document.getElementById('finalAmount').innerHTML = formatNumber(amount, 2) + ' ' + bmPayment.currency;
		document.getElementById('taxNote').innerHTML = taxNote;
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
			document.getElementById('finalAmount').innerHTML = formatNumber(amount, 2) + ' ' + bmPayment.currency;
			document.getElementById('taxNote').innerHTML = '';
		}
	}

	$(document).ready(paccCalc);
//-->
</script>{/literal}
