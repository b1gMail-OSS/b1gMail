{if $_tplname=='modern'}
<div id="contentHeader">
	<div class="left">
		<i class="fa fa-certificate" aria-hidden="true"></i>
		{lng p="pacc_order"}: {text value=$package.titel}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
{else}
<h1><i class="fa fa-certificate" aria-hidden="true"></i> {lng p="pacc_order"}: {text value=$package.titel}</h1>
{/if}

<p>
	{$package.beschreibung}
</p>

{if $otherPackage}
<div class="note">
	{lng p="pacc_otherpackwarning"}
</div>
<br />
{/if}

{if $errorMsg}
<div class="note">
	{$errorMsg}
</div>
<br />
{/if}

<form action="prefs.php?action=pacc_mod&do=placeOrder&id={$package.id}&sid={$sid}" method="post">
<table class="listTable">
	<tr>
		<th class="listTableHead" colspan="2"> {lng p="pacc_order"}</th>
	</tr>

	<tr>
		<td class="listTableLeftDesc"><i class="fa fa-certificate" aria-hidden="true"></i></td>
		<td class="listTableRightDesc" style="border-top:0 none;">{lng p="pacc_subscription"}</td>
	</tr>
	<tr>
		<td class="listTableLeft">{lng p="pacc_runtime"}:</td>
		<td class="listTableRight">
			{if $package.abrechnung=='einmalig'}
				({lng p="pacc_unlimited"})
			{else}
				{if $package.laufzeiten!='*'}
				<select name="abrechnung_t" id="abrechnung_t" onchange="paccCalc();" onclick="paccCalc();">
					{foreach from=$package.laufzeiten item=laufzeit}
					<option value="{$laufzeit}"{if $laufzeit==$abrechnung_t} selected="selected"{/if}>{$laufzeit}</option>
					{/foreach}
				</select>
				{else}
				<input type="text" name="abrechnung_t" id="abrechnung_t" size="6" value="{$abrechnung_t}" onkeyup="paccCalc();" />
				{/if}

				{$intervalStr}

				<span class="note" style="padding:2px;margin-left:5px;display:none;" id="runtimeNote">{$runtimeNote}</span>
			{/if}
		</td>
	</tr>

{if $_pf.sendrg=='yes'&&!$package.isFree}
	<tr>
		<td class="listTableLeftDesc"><i class="fa fa-address-card-o" aria-hidden="true"></i></td>
		<td class="listTableRightDesc">{lng p="invoiceaddress"}</td>
	</tr>
	<tr>
		<td class="listTableLeft">* <label for="vorname">{lng p="firstname"}</label>/<label for="nachname">{lng p="surname"}</label>:</td>
		<td class="listTableRight">
			<input type="text" name="vorname" id="vorname" value="{text value=$_pf.vorname allowEmpty=true}" size="22" />
			<input type="text" name="nachname" id="nachname" value="{text value=$_pf.nachname allowEmpty=true}" size="22" />
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">* <label for="strasse">{lng p="streetnr"}</label>:</td>
		<td class="listTableRight">
			<input type="text" name="strasse" id="strasse" value="{text value=$_pf.strasse allowEmpty=true}" size="35" />
			<input type="text" name="hnr" id="hnr" value="{text value=$_pf.hnr allowEmpty=true}" size="6" />
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">* <label for="plz">{lng p="zipcity"}:</label></td>
		<td class="listTableRight">
			<input type="text" name="plz" id="plz" value="{text value=$_pf.plz allowEmpty=true}" size="6" />
			<input type="text" name="ort" id="ort" value="{text value=$_pf.ort allowEmpty=true}" size="35" />
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">* <label for="land">{lng p="country"}:</label></td>
		<td class="listTableRight">
			<select name="land" id="land" onclick="updatePaymentCountry(this)" onchange="updatePaymentCountry(this)">
				{foreach from=$_pf.countryList item=country key=id}
				<option value="{$id}"{if $_pf.land==$id} selected="selected"{/if}>{$country.land}</option>
				{/foreach}
			</select>
		</td>
	</tr>
{/if}

	<tr>
		<td class="listTableLeftDesc"><i class="fa fa-money" aria-hidden="true"></i></td>
		<td class="listTableRightDesc">{lng p="pacc_paymentmethod"}</td>
	</tr>
	<tr>
		<td class="listTableLeft"><label for="paymentMethod">{lng p="paymentmethod"}:</label></td>
		<td class="listTableRight">
			{if $package.isFree}
			<input type="hidden" name="paymentMethod" value="0" /> -
			{else}
			<select name="paymentMethod" id="paymentMethod" onclick="updatePaymentMethod(this)" onchange="updatePaymentMethod(this)">
				{if $_pf.enable_su=='yes'}<option value="2"{if $_pf.paymentMethod==2} selected="selected"{/if}>{lng p="su"}</option>{/if}
				{if $_pf.enable_paypal=='yes'}<option value="1"{if $_pf.paymentMethod==1} selected="selected"{/if}>{lng p="paypal"}</option>{/if}
				{if $_pf.enable_skrill=='yes'}<option value="3"{if $_pf.paymentMethod==3} selected="selected"{/if}>{lng p="skrill"}</option>{/if}
				{if $_pf.enable_vk=='yes'}<option value="0"{if $_pf.paymentMethod===0} selected="selected"{/if}>{lng p="banktransfer"}</option>{/if}
				{foreach from=$_pf.customMethods key=methodID item=methodInfo}
				<option value="-{$methodID}"{if $_pf.paymentMethod==-$methodID} selected="selected"{/if}>{text value=$methodInfo.title}</option>
				{/foreach}
			</select>
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
				<input type="text" name="fields[{$methodID}][{$fieldID}]" id="field_{$methodID}_{$fieldID}" value="{text value=$smarty.post.fields.$methodID.$fieldID allowEmpty=true}" size="40" />
			{elseif $field.type==2}
				<input type="checkbox" name="fields[{$methodID}][{$fieldID}]" id="field_{$methodID}_{$fieldID}" value="true"{if $smarty.post.fields.$methodID.$fieldID} checked="checked"{/if} />
			{elseif $field.type==4}
				<select name="fields[{$methodID}][{$fieldID}]" id="field_{$methodID}_{$fieldID}">
					{foreach from=$field.options item=fieldOption}
					<option value="{text value=$fieldOption allowEmpty=true}"{if $smarty.post.fields.$methodID.$fieldID==$fieldOption} selected="selected"{/if}>{text value=$fieldOption}</option>
					{/foreach}
				</select>
			{elseif $field.type==8}
				{foreach from=$field.options key=fieldOptionID item=fieldOption}
					<input type="radio" name="fields[{$methodID}][{$fieldID}]" value="{text value=$fieldOption allowEmpty=true}" id="field_{$methodID}_{$fieldID}_{$fieldOptionID}"{if $smarty.post.fields.$methodID.$fieldID==$fieldOption} checked="checked"{/if} />
					<label for="field_{$methodID}_{$fieldID}_{$fieldOptionID}">{text value=$fieldOption}</label>
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
			<b>
			 	<span id="finalAmount">-</span>
			</b>
			<small id="taxNote">{$taxNote}</small>
		</td>
	</tr>

	<tr>
		<td class="listTableLeft">&nbsp;</td>
		<td class="listTableRight">
			<input type="submit" class="primary" value="{if $package.isFree}{lng p="pacc_placeorderfree"}{else}{lng p="pacc_placeorder"}{/if}" id="orderButton"{if $package.abrechnung!='einmalig'} disabled="disabled"{/if} />
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
		var divs = document.getElementsByTagName('div');

		for(var i=0; i<divs.length; i++)
		{
			if(divs[i].id.length > 14 && divs[i].id.substr(0, 14) == 'paymentMethod_')
			{
				var id = divs[i].id.substr(14);
				if(-id == paymentMethodID)
					divs[i].style.display = '';
				else
					divs[i].style.display = 'none';
			}
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
			amount_field = EBID('finalAmount'), order_button = EBID('orderButton');

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

{if $_tplname=='modern'}
</div></div>
{/if}
