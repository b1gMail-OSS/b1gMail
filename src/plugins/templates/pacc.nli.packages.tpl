{if !$nliPackages}<form action="index.php?action=paccOrder" method="post" onsubmit="submitSignupForm()">
<input type="hidden" name="userID" value="{$userID}" />
<input type="hidden" name="userToken" value="{$userToken}" />
{if $signUp}<input type="hidden" name="signUp" value="true" />{/if}{/if}

<style type="text/css">
{literal}
	.pacc-col { background-color: auto; }
	.pacc-col:nth-child(4n+4) { background-color: #FAFAFA; }

	COL.accent-1
	{
		border: 2px solid #D6E9C6;
	}
	TH.accent-1, BUTTON.accent-1
	{
		background-color: #DFF0D8;
		color: #3C763D;
	}

	COL.accent-2
	{
		border: 2px solid #BCE8F1;
	}
	TH.accent-2, BUTTON.accent-2
	{
		background-color: #D9EDF7;
		color: #31708F;
	}

	COL.accent-3
	{
		border: 2px solid #FAEBCC;
	}
	TH.accent-3, BUTTON.accent-3
	{
		background-color: #FCF8E3;
		color: #8A6D3B;
	}

	COL.pacc-spacer
	{
		width: 1px;
	}

	COL.pacc-spacer:last-of-type
	{
		visibility: collapse;
	}
{/literal}
</style>

<div class="container">
	<div class="page-header"><h1>{if $signUp}{lng p="signup"}{elseif $nliPackages}{lng p="pacc_packages"}{else}{lng p="login"}{/if}</h1></div>

	<p>
		{$orderText}
	</p>

	<table class="table">
		<colgroup>
			<col />
			{foreach from=$matrix.packages item=package}
			<col id="col_{$package.id}" class="pacc-col{if $package.accentuation} accent-{$package.accentuation}{/if}" />
			<col class="pacc-spacer" />
			{/foreach}
		</colgroup>

		<thead>
		<tr>
			<th>&nbsp;</th>
			{foreach from=$matrix.packages item=package}
			<th style="text-align:center;"{if $package.accentuation} class="accent-{$package.accentuation}"{/if}>
				<h3 class="panel-title">{if $package.accentuation==1}{lng p="pacc_accent_1"}
				{elseif $package.accentuation==2}{lng p="pacc_accent_2"}
				{elseif $package.accentuation==3}{lng p="pacc_accent_3"}
				{else}&nbsp;
				{/if}</h3>
			</th>
			<th></th>
			{/foreach}
		</tr>
		</thead>

		<thead>
		<tr>
			<th>&nbsp;</th>
			{foreach from=$matrix.packages item=package}
			<th style="text-align:center;"><label for="package_{$package.id}"><strong>{text value=$package.title cut=25}</strong></label></th>
			<th></th>
			{/foreach}
		</tr>
		</thead>
		
		<tbody>
		<tr>
			<td><h3>{lng p="pacc_infos"}</h3></td>
			{foreach from=$matrix.packages item=package}
			<td></td><td></td>
			{/foreach}
		</tr>
		<tr>
			<th scope="row"><b>{lng p="pacc_price"}</b></th>
			{foreach from=$matrix.packages item=package}
			<td align="center">
				{if $package.isFree}
					<small>&nbsp;</small><br />
					<span style="line-height:20px;"><b>{lng p="pacc_free"}</b></span>
				{else}
					<small>{text value=$package.priceInterval}</small><br />
					<span style="line-height:20px;"><b>{text value=$package.price}</b></span>
					<br /><small>{text value=$package.priceTax}</small>
				{/if}
			</td>
			<td></td>
			{/foreach}
		</tr>
		
		<tr>
			<td><h3>{lng p="pacc_features"}</h3></td>
			{foreach from=$matrix.packages item=package}
			<td></td><td></td>
			{/foreach}
		</tr>
		{foreach from=$matrix.fields item=fieldTitle key=fieldKey}
		<tr>
			<th scope="row">{$fieldTitle}</th>
			{foreach from=$matrix.packages item=package}
			<td align="center">{paccFormatField value=$package.fields.$fieldKey key=$fieldKey cut=25}</td>
			<td></td>
			{/foreach}
		</tr>
		{/foreach}
		
		{if !$nliPackages}
		<tr>
			<td><h3>{lng p="pacc_selection"}</h3></td>
			{foreach from=$matrix.packages item=package}
			<td></td><td></td>
			{/foreach}
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			{foreach from=$matrix.packages item=package}
			<td align="center"><input type="radio" onclick="$('#orderButton').prop('disabled',false);" name="package" id="package_{$package.id}" value="{$package.id}" /></td>
			<td></td>
			{/foreach}
		</tr>
		{elseif $regEnabled}
		<tr>
			<th scope="row">&nbsp;</th>
			{foreach from=$matrix.packages item=package}
			<td align="center"><button type="button" class="btn accent-{$package.accentuation}" onclick="document.location.href='index.php?action=signup&paccPackage={$package.id}';">
				{if $package.isFree}
					<span class="glyphicon glyphicon-user"></span> {lng p="signup"}
				{else}
					<span class="glyphicon glyphicon-shopping-cart"></span> {lng p="pacc_order"}
				{/if}
			</button></td>
			<td></td>
			{/foreach}
		</tr>
		{/if}
		</tbody>
	</table>

	{if !$nliPackages}
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

		<button type="submit" name="doOrder" id="orderButton" class="btn btn-success pull-right" data-loading-text="{lng p="pleasewait"}">
			<span class="glyphicon glyphicon-ok"></span> {lng p="pacc_doorder"}
		</button>
	</div>
	{/if}
</div>

{if !$nliPackages}</form>{/if}
