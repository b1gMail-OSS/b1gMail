{if $_tplname=='modern'}
<div id="contentHeader">
	<div class="left">
		<i class="fa fa-user-plus" aria-hidden="true"></i>
		{lng p="pacc_mod"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
{else}
<h1><i class="fa fa-user-plus" aria-hidden="true"></i> {lng p="pacc_mod"}</h1>
{/if}

<style type="text/css">
{literal}
	.pacc-col { background-color: auto; }
	.pacc-col:nth-child(4n+4) { background-color: #FAFAFA; }

	COL.accent-1
	{
		border: 2px solid #D6E9C6;
	}
	TH.accent-1, INPUT.accent-1
	{
		background-color: #DFF0D8;
		color: #3C763D;
	}

	COL.accent-2
	{
		border: 2px solid #BCE8F1;
	}
	TH.accent-2, INPUT.accent-2
	{
		background-color: #D9EDF7;
		color: #31708F;
	}

	COL.accent-3
	{
		border: 2px solid #FAEBCC;
	}
	TH.accent-3, INPUT.accent-3
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

	.folderGroup
	{
		border-top: 1px solid #DDD;
	}
{/literal}
</style>

{lng p="pacc_prefs_intro"}

<h2>{lng p="pacc_activesubscription"}</h2>
{if $activeSubscription}
<table class="listTable">
	<tr>
		<th class="listTableHead" colspan="2"> {lng p="pacc_activesubscription"}</th>
	</tr>
	<tr>
		<td class="listTableLeft">{lng p="pacc_package"}:</td>
		<td class="listTableRight">
			<a href="javascript:void(0);" onclick="openOverlay('index.php?action=paccPackageDetails&id={$activeSubscription.package.id}','{lng p="pacc_packagedetails"}: {text value=$activeSubscription.package.titel escape=true}',450,{$poHeight});">
				<i class="fa fa-archive" aria-hidden="true"></i>
				{text value=$activeSubscription.package.titel}
			</a>
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">{lng p="pacc_lastpayment"}:</td>
		<td class="listTableRight">
			{date timestamp=$activeSubscription.letzte_zahlung}
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">{lng p="pacc_validuntil"}:</td>
		<td class="listTableRight">
			{if $activeSubscription.ablauf<=1}({lng p="unlimited"}){else}{date timestamp=$activeSubscription.ablauf}{/if}
		</td>
	</tr>
	{if !$activeSubscription.package.geloescht&&$activeSubscription.ablauf>=1}
	<tr>
		<td class="listTableLeft">&nbsp;</td>
		<td class="listTableRight">
			<input type="button" class="primary" value=" {lng p="pacc_renew"} " onclick="document.location.href='prefs.php?action=pacc_mod&do=order&id={$activeSubscription.package.id}&sid={$sid}';" />
		</td>
	</tr>
	{/if}
</table>
{else}
<i>{lng p="pacc_noactivesubscription"}</i>
{/if}

<h2>{lng p="pacc_order"}</h2>
<table class="listTable" style="border-collapse:collapse;">
	<colgroup>
		<col />
		<col />
		{foreach from=$matrix.packages item=package}
		<col id="col_{$package.id}" class="pacc-col{if $package.accentuation} accent-{$package.accentuation}{/if}" />
		<col class="pacc-spacer" />
		{/foreach}
	</colgroup>

	<thead>
	<tr>
		<th class="listTableHead" colspan="2">&nbsp;</th>
		{foreach from=$matrix.packages item=package}
		<th class="listTableHead{if $package.accentuation} accent-{$package.accentuation}{/if}" style="text-align:center;"><strong>{text value=$package.title cut=25}</strong></th>
		<th class="listTableHead"></th>
		{/foreach}
	</tr>
	</thead>

	<tr>
		<td colspan="{math equation="2*x+y" x=$matrix.packages|@count y=2}" class="folderGroup">
			<a style="display:block;" href="javascript:toggleGroup(0);">&nbsp;<img id="groupImage_0" src="{$tpldir}images/contract.png" width="11" height="11" border="0" align="absmiddle" alt="" />
			&nbsp;{lng p="pacc_infos"}
		</td>
	</tr>
	<tbody id="group_0" style="display:;">
	<tr>
		<td class="listTableLeft" style="vertical-align:middle;">{lng p="pacc_price"}</td>
		<td></td>
		{foreach from=$matrix.packages item=package}
		<td align="center">
		{if $package.isFree}
			<small>&nbsp;</small><br />
			<span style="line-height:20px;"><b>{lng p="pacc_free"}</b></span>
			<br /><small>&nbsp;</small>
		{else}
			<small>{text value=$package.priceInterval}</small><br />
			<span style="line-height:20px;"><b>{text value=$package.price}</b></span>
			<br /><small>{text value=$package.priceTax allowEmpty=true}</small>
		{/if}
		</td>
		<td></td>
		{/foreach}
	</tr>
	</tbody>

	<tr>
		<td colspan="{math equation="2*x+y" x=$matrix.packages|@count y=2}" class="folderGroup">
			<a style="display:block;" href="javascript:toggleGroup(1);">&nbsp;<img id="groupImage_1" src="{$tpldir}images/contract.png" width="11" height="11" border="0" align="absmiddle" alt="" />
			&nbsp;{lng p="pacc_features"}
		</td>
	</tr>
	<tbody id="group_1" style="display:;">
	{foreach from=$matrix.fields item=fieldTitle key=fieldKey}
	<tr>
		<td class="listTableLeft" style="vertical-align:middle;">{$fieldTitle}</td>
		<td></td>
		{foreach from=$matrix.packages item=package}
		<td align="center">{paccFormatField value=$package.fields.$fieldKey key=$fieldKey cut=25}</td>
		<td></td>
		{/foreach}
	</tr>
	{/foreach}
	</tbody>

	<tr>
		<td colspan="{math equation="2*x+y" x=$matrix.packages|@count y=2}" class="folderGroup">
			<a style="display:block;" href="javascript:toggleGroup(2);">&nbsp;<img id="groupImage_2" src="{$tpldir}images/contract.png" width="11" height="11" border="0" align="absmiddle" alt="" />
			&nbsp;{lng p="pacc_selection"}
		</td>
	</tr>
	<tbody id="group_2" style="display:;">
	<tr>
		<td class="listTableLeft">&nbsp;</td>
		<td></td>
		{foreach from=$matrix.packages item=package}
		<td align="center">
			<input type="button"{if $package.accentuation} class="accent-{$package.accentuation}"{/if} onclick="document.location.href='prefs.php?action=pacc_mod&do=order&id={$package.id}&sid={$sid}';" value=" {lng p="pacc_order"} " style="margin:5px;" />
			<div style="padding:4px;padding-top:0px;"><small><a href="javascript:void(0);" onclick="openOverlay('prefs.php?action=paccPackageDetails&id={$package.id}&sid={$sid}','{lng p="pacc_packagedetails"}: {text value=$package.title escape=true}',450,{$poHeight});">{lng p="pacc_packagedetails"}</a></small></div>
		</td>
		<td></td>
		{/foreach}
	</tr>
	</tbody>
</table>

{if $_tplname=='modern'}
</div></div>
{/if}
