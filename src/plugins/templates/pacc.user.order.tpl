<style type="text/css">
#mainContent .bm-prefs-page-pacc-order .listTableRight:not(.listTableRightDesc) {
	display: table-cell !important;
	width: auto !important;
	max-width: none !important;
	vertical-align: top;
}
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row {
	width: 28rem !important;
	max-width: 100% !important;
	box-sizing: border-box;
}
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row-pair {
	display: flex !important;
	flex-wrap: nowrap;
	align-items: stretch;
	gap: 0.5rem;
}
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row input,
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row select,
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row .form-control,
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row .form-select {
	box-sizing: border-box;
	min-width: 0;
	width: auto !important;
	max-width: none !important;
	flex: unset !important;
}
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row-full > input,
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row-full > select,
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row-full > .form-control,
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row-full > .form-select {
	display: block !important;
	width: 100% !important;
	max-width: 100% !important;
}
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row-pair .bm-pacc-field-grow {
	flex: 1 1 0 !important;
	width: auto !important;
	min-width: 0;
}
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row-pair .bm-pacc-field-nr {
	flex: 0 0 3.75rem !important;
	width: 3.75rem !important;
	max-width: 3.75rem !important;
}
#mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row-pair .bm-pacc-field-plz {
	flex: 0 0 5.5rem !important;
	width: 5.5rem !important;
	max-width: 5.5rem !important;
}
body:not(.layout-fluid) #mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row input[type="text"],
body:not(.layout-fluid) #mainContent .bm-prefs-page-pacc-order .listTableRight .bm-pacc-field-row select {
	padding: 4px 6px;
	border: 1px solid #ccc;
	border-radius: 3px;
	font: inherit;
}
</style>
<div class="bm-prefs-page bm-prefs-page-pacc-order">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-shopping-cart icon icon-sm" aria-hidden="true"></i>
		{lng p="pacc_order"}: {text value=$package.titel}
	</div>
</div>

<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">

<p class="text-secondary mb-3">{$package.beschreibung}</p>

{if $otherPackage}
<div class="alert alert-warning" role="alert">
	{lng p="pacc_otherpackwarning"}
</div>
{/if}

{if $errorMsg}
<div class="alert alert-danger" role="alert">
	{$errorMsg}
</div>
{/if}

<form action="{sessionurl file='prefs.php' params="action=pacc_mod&do=placeOrder&id={$package.id}"}" method="post">
	{csrffield}
<table class="listTable">
	<tr>
		<th class="listTableHead" colspan="2">{lng p="pacc_order"}</th>
	</tr>

	<tr>
		<td class="listTableLeftDesc"><i class="ti ti-certificate icon icon-sm" aria-hidden="true"></i></td>
		<td class="listTableRightDesc" style="border-top:0 none;">{lng p="pacc_subscription"}</td>
	</tr>
	<tr>
		<td class="listTableLeft"><label for="abrechnung_t">{lng p="pacc_runtime"}:</label></td>
		<td class="listTableRight">
			{if $package.abrechnung=='einmalig'}
				({lng p="pacc_unlimited"})
			{else}
				{if $package.laufzeiten!='*'}
				<select class="form-select form-select-sm d-inline-block w-auto" name="abrechnung_t" id="abrechnung_t" onchange="paccCalc();" onclick="paccCalc();">
					{foreach from=$package.laufzeiten item=laufzeit}
					<option value="{$laufzeit}"{if $laufzeit==$abrechnung_t} selected="selected"{/if}>{$laufzeit}</option>
					{/foreach}
				</select>
				{else}
				<input class="form-control form-control-sm d-inline-block w-auto" type="text" name="abrechnung_t" id="abrechnung_t" size="6" value="{$abrechnung_t}" onkeyup="paccCalc();" />
				{/if}

				{$intervalStr}

				<span class="badge bg-yellow-lt text-dark ms-2" style="display:none;" id="runtimeNote">{$runtimeNote}</span>
			{/if}
		</td>
	</tr>

{if $_pf.sendrg=='yes'&&!$package.isFree}
	<tr>
		<td class="listTableLeftDesc"><i class="ti ti-id icon icon-sm" aria-hidden="true"></i></td>
		<td class="listTableRightDesc">{lng p="invoiceaddress"}</td>
	</tr>
	{if $_pf.f_company!="n"}<tr>
		<td class="listTableLeft">{if $_pf.f_company=="p"}* {/if}<label for="company">{lng p="company"}</label>:</td>
		<td class="listTableRight">
			<div class="bm-pacc-field-row bm-pacc-field-row-full">
				<input class="form-control" type="text" name="company" id="company" value="{if isset($_pf.company)}{text value=$_pf.company allowEmpty=true}{/if}"{if $_pf.f_company=="p"} required{/if} />
			</div>
		</td>
	</tr>{/if}
	<tr>
		<td class="listTableLeft">* <label for="vorname">{lng p="firstname"}</label>/<label for="nachname">{lng p="surname"}</label>:</td>
		<td class="listTableRight">
			<div class="bm-pacc-field-row bm-pacc-field-row-pair">
				<input class="form-control bm-pacc-field-grow" type="text" name="vorname" id="vorname" value="{if isset($_pf.vorname)}{text value=$_pf.vorname allowEmpty=true}{/if}" />
				<input class="form-control bm-pacc-field-grow" type="text" name="nachname" id="nachname" value="{if isset($_pf.nachname)}{text value=$_pf.nachname allowEmpty=true}{/if}" />
			</div>
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">* <label for="strasse">{lng p="streetnr"}</label>:</td>
		<td class="listTableRight">
			<div class="bm-pacc-field-row bm-pacc-field-row-pair">
				<input class="form-control bm-pacc-field-grow" type="text" name="strasse" id="strasse" value="{if isset($_pf.strasse)}{text value=$_pf.strasse allowEmpty=true}{/if}" />
				<input class="form-control bm-pacc-field-nr" type="text" name="hnr" id="hnr" value="{if isset($_pf.hnr)}{text value=$_pf.hnr allowEmpty=true}{/if}" />
			</div>
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">* <label for="plz">{lng p="zipcity"}:</label></td>
		<td class="listTableRight">
			<div class="bm-pacc-field-row bm-pacc-field-row-pair">
				<input class="form-control bm-pacc-field-plz" type="text" name="plz" id="plz" value="{if isset($_pf.plz)}{text value=$_pf.plz allowEmpty=true}{/if}" />
				<input class="form-control bm-pacc-field-grow" type="text" name="ort" id="ort" value="{if isset($_pf.ort)}{text value=$_pf.ort allowEmpty=true}{/if}" />
			</div>
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">* <label for="land">{lng p="country"}:</label></td>
		<td class="listTableRight">
			<div class="bm-pacc-field-row bm-pacc-field-row-full">
				<select class="form-select" name="land" id="land" onclick="updatePaymentCountry(this)" onchange="updatePaymentCountry(this)">
					{foreach from=$_pf.countryList item=country key=id}
					<option value="{$id}"{if $_pf.land==$id} selected="selected"{/if}>{$country.land}</option>
					{/foreach}
				</select>
			</div>
		</td>
	</tr>
	{if $_pf.f_taxid!="n"}<tr>
		<td class="listTableLeft">{if $_pf.f_taxid=="p"}* {/if}<label for="taxid">{lng p="taxid"}</label>:</td>
		<td class="listTableRight">
			<div class="bm-pacc-field-row bm-pacc-field-row-full">
				<input class="form-control" type="text" name="taxid" id="taxid" value="{if isset($_pf.taxid)}{text value=$_pf.taxid allowEmpty=true}{/if}"{if $_pf.f_taxid=="p"} required{/if} />
			</div>
		</td>
	</tr>{/if}
{/if}

	<tr>
		<td class="listTableLeftDesc"><i class="ti ti-credit-card icon icon-sm" aria-hidden="true"></i></td>
		<td class="listTableRightDesc">{lng p="pacc_paymentmethod"}</td>
	</tr>
	<tr>
		<td class="listTableLeft"><label for="paymentMethod">{lng p="paymentmethod"}:</label></td>
		<td class="listTableRight">
			{if $package.isFree}
			<input type="hidden" name="paymentMethod" value="0" /> -
			{else}
			<div class="bm-pacc-field-row bm-pacc-field-row-full">
			<select class="form-select" name="paymentMethod" id="paymentMethod" onclick="updatePaymentMethod(this)" onchange="updatePaymentMethod(this)">
				{if $_pf.enable_su=='yes'}<option value="2"{if $_pf.paymentMethod==2} selected="selected"{/if}>{lng p="su"}</option>{/if}
				{if $_pf.enable_paypal=='yes'}<option value="1"{if $_pf.paymentMethod==1} selected="selected"{/if}>{lng p="paypal"}</option>{/if}
				{if $_pf.enable_skrill=='yes'}<option value="3"{if $_pf.paymentMethod==3} selected="selected"{/if}>{lng p="skrill"}</option>{/if}
				{if $_pf.enable_vk=='yes'}<option value="0"{if $_pf.paymentMethod===0} selected="selected"{/if}>{lng p="banktransfer"}</option>{/if}
				{foreach from=$_pf.customMethods key=methodID item=methodInfo}
				<option value="-{$methodID}"{if $_pf.paymentMethod==-$methodID} selected="selected"{/if}>{text value=$methodInfo.title}</option>
				{/foreach}
			</select>
			</div>
			{/if}
		</td>
	</tr>

	{foreach from=$_pf.customMethods key=methodID item=method}
	<tbody id="paymentMethod_{$methodID}" style="display:none;">
		{foreach from=$method.fields key=fieldID item=field}
		{assign var=fieldName value="field_$methodID"|cat:"_"|cat:$fieldID}
		<tr>
			<td class="listTableLeft">
				{if $field.oblig}*{/if}
				{text value=$field.title}
			</td>
			<td class="listTableRight">
			{if $field.type==1}
				<input class="form-control" type="text" name="fields[{$methodID}][{$fieldID}]" id="field_{$methodID}_{$fieldID}" value="{if isset($smarty.post.fields.$methodID.$fieldID)}{text value=$smarty.post.fields.$methodID.$fieldID allowEmpty=true}{/if}" />
			{elseif $field.type==2}
				<label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="fields[{$methodID}][{$fieldID}]" id="field_{$methodID}_{$fieldID}" value="true"{if $smarty.post.fields.$methodID.$fieldID} checked="checked"{/if} /><span class="form-check-label">{text value=$field.title}</span></label>
			{elseif $field.type==4}
				<select class="form-select" name="fields[{$methodID}][{$fieldID}]" id="field_{$methodID}_{$fieldID}">
					{foreach from=$field.options item=fieldOption}
					<option value="{if isset($fieldOption)}{text value=$fieldOption allowEmpty=true}{/if}"{if $smarty.post.fields.$methodID.$fieldID==$fieldOption} selected="selected"{/if}>{text value=$fieldOption}</option>
					{/foreach}
				</select>
			{elseif $field.type==8}
				{foreach from=$field.options key=fieldOptionID item=fieldOption}
					<label class="form-check"><input class="form-check-input" type="radio" name="fields[{$methodID}][{$fieldID}]" value="{if isset($fieldOption)}{text value=$fieldOption allowEmpty=true}{/if}" id="field_{$methodID}_{$fieldID}_{$fieldOptionID}"{if $smarty.post.fields.$methodID.$fieldID==$fieldOption} checked="checked"{/if} /><span class="form-check-label">{text value=$fieldOption}</span></label>
				{/foreach}
			{elseif $field.type==32}
				{if $_pf.dateFields[$fieldName]}
					{html_select_date time=$_pf.dateFields[$fieldName] year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix=$fieldName field_order="DMY"}
				{else}
					{html_select_date time="---" year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix=$fieldName field_order="DMY"}
				{/if}
			{/if}
			</td>
		</tr>
		{/foreach}
	</tbody>
	{/foreach}

	<tr>
		<td class="listTableLeft">{lng p="pacc_finalamount"}:</td>
		<td class="listTableRight">
			<strong><span id="finalAmount">-</span></strong>
			<small class="text-secondary" id="taxNote">{$taxNote}</small>
		</td>
	</tr>

	<tr>
		<td class="listTableLeft">&nbsp;</td>
		<td class="listTableRight">
			<input type="submit" class="btn btn-primary" value="{if $package.isFree}{lng p="pacc_placeorderfree"}{else}{lng p="pacc_placeorder"}{/if}" id="orderButton"{if $package.abrechnung!='einmalig'} disabled="disabled"{/if} />
		</td>
	</tr>
</table>
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

		EBID('finalAmount').innerHTML = formatNumber(amount, 2) + ' ' + bmPayment.currency;
		EBID('taxNote').innerHTML = taxNote;
	}

	function paccCalc()
	{
		var f = EBID('abrechnung_t'), i, multiplier = {/literal}{$package.abrechnung_t}{literal},
			amount_base = {/literal}{$package.preis_cent}{literal}, note = EBID('runtimeNote'), amount,
			order_button = EBID('orderButton');

		{/literal}{if $package.abrechnung!='einmalig'}{literal}
		if(isNaN(f.value) || f.value.indexOf('.')>=0 || (i=parseInt(f.value)) < 1
			|| i%multiplier != 0)
		{
			note.style.display = '';
			order_button.disabled = true;
			i = multiplier;
		}
		else
		{
			note.style.display = 'none';
			order_button.disabled = false;
		}

		amount = ((i/multiplier) * amount_base) / 100;
		amount = Math.round(amount*100) / 100;
		bmPayment.baseAmount = amount;
		{/literal}{/if}{if $package.isFree}amount = 0;{/if}{literal}

		if(EBID('paymentMethod'))
			updatePaymentMethod(EBID('paymentMethod'));

		if(EBID('land'))
		{
			updatePaymentCountry(EBID('land'));
		}
		else
		{
			EBID('finalAmount').innerHTML = formatNumber(amount, 2) + ' ' + bmPayment.currency;
			EBID('taxNote').innerHTML = '';
		}
	}

	registerLoadAction(paccCalc);
	{/literal}
//-->
</script>

</div></div>
</div>
