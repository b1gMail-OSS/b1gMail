<table class="listTable">
	<tr>
		<th class="listTableHead" colspan="2"> {$_pf.title}</th>
	</tr>
	
	<tr>
		<td class="listTableLeftDesc"><i class="fa fa-address-card-o" aria-hidden="true"></i></td>
		<td class="listTableRightDesc">{lng p="invoiceaddress"}</td>
	</tr>
{if $_pf.sendrg=='yes'}
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
{/if}
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

	<tr>
		<td class="listTableLeftDesc"><i class="fa fa-money" aria-hidden="true"></i></td>
		<td class="listTableRightDesc">{lng p="paymentmethod"}</td>
	</tr>
	<tr>
		<td class="listTableLeft"><label for="paymentMethod">{lng p="paymentmethod"}:</label></td>
		<td class="listTableRight">
			<select name="paymentMethod" id="paymentMethod" onclick="updatePaymentMethod(this)" onchange="updatePaymentMethod(this)">
				{if $_pf.enable_su=='yes'}<option value="2"{if $_pf.paymentMethod==2} selected="selected"{/if}>{lng p="su"}</option>{/if}
				{if $_pf.enable_paypal=='yes'}<option value="1"{if $_pf.paymentMethod==1} selected="selected"{/if}>{lng p="paypal"}</option>{/if}
				{if $_pf.enable_skrill=='yes'}<option value="3"{if $_pf.paymentMethod==3} selected="selected"{/if}>{lng p="skrill"}</option>{/if}
				{if $_pf.enable_vk=='yes'}<option value="0"{if $_pf.paymentMethod===0} selected="selected"{/if}>{lng p="banktransfer"}</option>{/if}
				{foreach from=$_pf.customMethods key=methodID item=methodInfo}
				<option value="-{$methodID}"{if $_pf.paymentMethod==-$methodID} selected="selected"{/if}>{text value=$methodInfo.title}</option>
				{/foreach}
			</select>
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
		<td class="listTableLeft">{lng p="finalamount"}:</td>
		<td class="listTableRight">
			<b id="paymentAmount"></b>
			<small id="taxNote"></small>
		</td>
	</tr>
	
	<tr>
		<td class="listTableLeft" style="width:25%;">&nbsp;</td>
		<td class="listTableRight">
			<input type="submit" class="primary" value="{lng p="placeorder"}" id="orderButton" />
		</td>
	</tr>
</table>

<script>
<!--
	var bmPayment = {ldelim}
		vatRates: {ldelim}{foreach from=$_pf.countryList item=country key=id}{if $country.vat>0}{$id}: {$country.vat},{/if}{/foreach}{rdelim},
		vatMode: '{$_pf.mwst}',
		currency: '{$_pf.currency}',
		baseAmount: {$_pf.amount}
	{rdelim};
	updatePaymentMethod(EBID('paymentMethod'));
	updatePaymentCountry(EBID('land'));
{if $_pf.invalidFields}
{foreach from=$_pf.invalidFields item=field}
	markFieldAsInvalid('{$field}');
{/foreach}
{/if}
//-->
</script>
