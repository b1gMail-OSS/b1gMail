<form action="index.php?action=paccPlaceOrder&id={$package.id}" method="post" onsubmit="submitSignupForm()">
<input type="hidden" name="userID" value="{$userID}" />
<input type="hidden" name="userToken" value="{$userToken}" />
{if $signUp}<input type="hidden" name="signUp" value="true" />{/if}

<div class="container">
	<div class="page-header"><h1>{lng p="pacc_order"}: {text value=$package.titel}</h1></div>

	<div class="row"><div class="col-md-8">

	<p>
		{$package.beschreibung}
	</p>

	{if $errorMsg}
	<div class="alert alert-danger">
		<span class="glyphicon glyphicon-exclamation-sign"></span>
		{$errorMsg}
	</div>
	{/if}

	<div class="panel-group" id="pacc">
		<div class="panel panel-primary">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-pushpin"></span>
				{lng p="pacc_subscription"}
			</div>
			<div class="panel-collapse collapse in" id="pacc-package">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label class="control-label" for="abrechnung_t">
									{lng p="pacc_runtime"}
									{if $package.abrechnung!='einmalig'&&$package.laufzeiten=='*'}<span class="required">({$intervalStr})</span>{/if}
								</label>
								{if $package.abrechnung=='einmalig'}
								<div class="form-control">({lng p="pacc_unlimited"})</div>
								{elseif $package.laufzeiten!='*'}
								<select class="form-control" name="abrechnung_t" id="abrechnung_t" onchange="paccCalc();" onclick="paccCalc();">
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
		</div>

{if $_pf.sendrg=='yes'}
		<div class="panel panel-default">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-list-alt"></span>
				{lng p="pacc_invoiceaddress"}
			</div>
			<div class="panel-collapse collapse in" id="pacc-address">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="vorname">
									{lng p="firstname"}
									<span class="required">{lng p="required"}</span>
								</label>
								<input type="text" class="form-control" required="true" name="vorname" id="vorname" value="{text value=$_pf.vorname allowEmpty=true}" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="nachname">
									{lng p="surname"}
									<span class="required">{lng p="required"}</span>
								</label>
								<input type="text" class="form-control" required="true" name="nachname" id="nachname" value="{text value=$_pf.nachname allowEmpty=true}" />
							</div>
						</div>
					</div>

					<hr />

					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label class="control-label" for="strasse">
									{lng p="street"}
									<span class="required">{lng p="required"}</span>
								</label>
								<input type="text" class="form-control" required="true" name="strasse" id="strasse" value="{text value=$_pf.strasse allowEmpty=true}" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label" for="hnr">
									{lng p="nr"}
									<span class="required">{lng p="required"}</span>
								</label>
								<input type="text" class="form-control" required="true" name="hnr" id="hnr" value="{text value=$_pf.hnr allowEmpty=true}" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label" for="plz">
									{lng p="zip"}
									<span class="required">{lng p="required"}</span>
								</label>
								<input type="text" class="form-control" required="true" name="plz" id="plz" value="{text value=$_pf.plz allowEmpty=true}" />
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								<label class="control-label" for="ort">
									{lng p="city"}
									<span class="required">{lng p="required"}</span>
								</label>
								<input type="text" class="form-control" required="true" name="ort" id="ort" value="{text value=$_pf.ort allowEmpty=true}" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="land">
									{lng p="country"}
									<span class="required">{lng p="required"}</span>
								</label>
								<select class="form-control" name="land" id="land" onclick="updatePaymentCountry(this)" onchange="updatePaymentCountry(this)">
									{foreach from=$_pf.countryList item=country key=id}
									<option value="{$id}"{if $_pf.land==$id} selected="selected"{/if}>{$country.land}</option>
									{/foreach}
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
{/if}

		<div class="panel panel-default">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-shopping-cart"></span>
				{lng p="pacc_paymentmethod"}
			</div>
			<div class="panel-collapse collapse in" id="pacc-payment">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="paymentMethod">
									{lng p="pacc_paymentmethod"}
									{if $package.abrechnung!='einmalig'&&$package.laufzeiten=='*'}<span class="required">({$intervalStr})</span>{/if}
								</label>
								{if $package.isFree}
								<input type="hidden" name="paymentMethod" value="0" /><div class="form-control">-</div>
								{else}
								<select class="form-control" name="paymentMethod" id="paymentMethod" onclick="updatePaymentMethod(this)" onchange="updatePaymentMethod(this)">
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
					</div>

					{foreach from=$_pf.customMethods key=methodID item=method}<div id="paymentMethod_{$methodID}" style="display:none;">
						{foreach from=$method.fields key=fieldID item=field}
						{assign var=fieldName value="field_$methodID"|cat:"_"|cat:$fieldID}
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									{if $field.type!=2}<label class="control-label" for="field_{$methodID}_{$fieldID}">
										{text value=$field.title}
										{if $field.oblig}<span class="required">{lng p="required"}</span>{/if}
									</label>{/if}
									{if $field.type==1}
										<input class="form-control" type="text" name="fields[{$methodID}][{$fieldID}]" id="field_{$methodID}_{$fieldID}" value="{text value=$smarty.post.fields.$methodID.$fieldID allowEmpty=true}" size="40" />
									{elseif $field.type==2}
									<label class="control-label">
										<input type="checkbox" name="fields[{$methodID}][{$fieldID}]" id="field_{$methodID}_{$fieldID}" value="true"{if $smarty.post.fields.$methodID.$fieldID} checked="checked"{/if} />
										{text value=$field.title}
									</label>
									{elseif $field.type==4}
										<select class="form-control" name="fields[{$methodID}][{$fieldID}]" id="field_{$methodID}_{$fieldID}">
											{foreach from=$field.options item=fieldOption}
											<option value="{text value=$fieldOption allowEmpty=true}"{if $smarty.post.fields.$methodID.$fieldID==$fieldOption} selected="selected"{/if}>{text value=$fieldOption}</option>
											{/foreach}
										</select>
									{elseif $field.type==8}
										{foreach from=$field.options key=fieldOptionID item=fieldOption}
										<div class="radio">
											<label>
												<input type="radio" name="fields[{$methodID}][{$fieldID}]" value="{text value=$fieldOption allowEmpty=true}" id="field_{$methodID}_{$fieldID}_{$fieldOptionID}"{if $smarty.post.fields.$methodID.$fieldID==$fieldOption} checked="checked"{/if} />
												<label for="field_{$methodID}_{$fieldID}_{$fieldOptionID}">{text value=$fieldOption}</label>
											</label>
										</div>
										{/foreach}
									{elseif $field.type==32}
										<div>{if $_pf.dateFields[$fieldName]}
											{html_select_date time=$_pf.dateFields[$fieldName] year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix=$fieldName field_order="DMY" class="form-control" style="width:auto;display:inline-block;"}
										{else}
											{html_select_date time="---" year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix=$fieldName field_order="DMY" class="form-control" style="width:auto;display:inline-block;"}
										{/if}</div>
									{/if}
								</div>
							</div>
						</div>
						{/foreach}
					</div>{/foreach}

					<hr />

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">
									{lng p="pacc_finalamount"}
									<span class="required" id="taxNote"></span>
								</label>
								<div class="form-control" style="font-weight:bold;">
					 				<span id="finalAmount">-</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="alert alert-info">
		<span class="glyphicon glyphicon-info-sign"></span>
		{lng p="iprecord"}
	</div>

	<div class="form-group">
		{if $signUp&&!$force}<button type="submit" name="dontOrder" class="btn">
			<span class="glyphicon glyphicon-remove"></span> {lng p="pacc_dontorder"}
		</button>
		{elseif $signUp}<button type="submit" name="dontOrder" class="btn btn-warning">
			<span class="glyphicon glyphicon-remove"></span> {lng p="pacc_abort"}
		</button>{/if}

		<button{if $package.abrechnung!='einmalig'} disabled="disabled"{/if} type="submit" name="doOrder" id="orderButton" class="btn btn-success pull-right" data-loading-text="{lng p="pleasewait"}">
			<span class="glyphicon glyphicon-ok"></span> {if $package.isFree}{lng p="pacc_placeorderfree"}{else}{lng p="pacc_placeorder"}{/if}
		</button>
	</div>

	</div></div>
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

		document.getElementById('finalAmount').innerHTML = formatNumber(amount, 2) + ' ' + bmPayment.currency;
		document.getElementById('taxNote').innerHTML = taxNote;
	}

	function paccCalc()
	{
		var f = $('#abrechnung_t'), i, multiplier = {/literal}{$package.abrechnung_t}{literal},
			amount_base = {/literal}{$package.preis_cent}{literal}, note = $('#runtimeNote'), amount,
			amount_field = $('#finalAmount'), order_button = $('#orderButton');

		{/literal}{if $package.abrechnung!='einmalig'}{literal}
		if(isNaN(f.val()) || f.val().indexOf('.')>=0 || (i=parseInt(f.val())) < 1
			|| i%multiplier != 0)
		{
			f.closest('[class~="form-group"]').addClass('has-error');
			note.css('display', '');
			order_button.prop('disabled', true);
			i = multiplier;
		}
		else
		{
			f.closest('[class~="form-group"]').removeClass('has-error');
			note.css('display', 'none');
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
