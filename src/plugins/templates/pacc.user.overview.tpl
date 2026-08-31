<div class="bm-prefs-page bm-prefs-page-pacc">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-package icon icon-sm" aria-hidden="true"></i>
		{lng p="pacc_mod"}
	</div>
</div>

<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">

<p class="text-secondary mb-4">{lng p="pacc_prefs_intro"}</p>

<h2>{lng p="pacc_activesubscription"}</h2>
<div class="card bm-pacc-subscription-card">
<div class="card-body">
{if !empty($activeSubscription)}
<dl class="row mb-0">
	<dt class="col-sm-4 col-lg-3 text-secondary">{lng p="pacc_package"}</dt>
	<dd class="col-sm-8 col-lg-9 mb-2">
		<a href="javascript:void(0);" onclick="openOverlay('index.php?action=paccPackageDetails&id={$activeSubscription.package.id}','{lng p="pacc_packagedetails"}: {text value=$activeSubscription.package.titel escape=true}',450,{$poHeight});">
			<i class="ti ti-package icon icon-sm text-secondary me-1" aria-hidden="true"></i>
			{text value=$activeSubscription.package.titel}
		</a>
	</dd>
	<dt class="col-sm-4 col-lg-3 text-secondary">{lng p="pacc_lastpayment"}</dt>
	<dd class="col-sm-8 col-lg-9 mb-2">{date timestamp=$activeSubscription.letzte_zahlung}</dd>
	<dt class="col-sm-4 col-lg-3 text-secondary">{lng p="pacc_validuntil"}</dt>
	<dd class="col-sm-8 col-lg-9 mb-2">{if $activeSubscription.ablauf<=1}({lng p="unlimited"}){else}{date timestamp=$activeSubscription.ablauf}{/if}</dd>
	{if !$activeSubscription.package.geloescht&&$activeSubscription.ablauf>=1}
	<dt class="col-sm-4 col-lg-3">&nbsp;</dt>
	<dd class="col-sm-8 col-lg-9 mb-0">
		<button type="button" class="btn btn-primary btn-sm" onclick="document.location.href='{sessionurl file='prefs.php' params="action=pacc_mod&do=order&id={$activeSubscription.package.id}"|escape:'javascript'}';">
			{lng p="pacc_renew"}
		</button>
	</dd>
	{/if}
</dl>
{else}
<p class="text-secondary mb-0"><em>{lng p="pacc_noactivesubscription"}</em></p>
{/if}
</div>
</div>

<h2>{lng p="pacc_order"}</h2>
<div class="card bm-prefs-table-card bm-pacc-matrix-card overflow-hidden">
<div class="table-responsive bm-prefs-table-wrap">
<table class="table table-vcenter bm-pacc-matrix mb-0">
	<colgroup>
		<col class="bm-pacc-label-col" />
		{foreach from=$matrix.packages item=package}
		<col id="col_{$package.id}" />
		<col class="pacc-spacer" />
		{/foreach}
	</colgroup>

	<thead>
	<tr>
		<th>&nbsp;</th>
		{foreach from=$matrix.packages item=package}
		<th class="text-center{if $package.accentuation==1} bm-pacc-accent bm-pacc-accent-1{elseif $package.accentuation==2} bm-pacc-accent bm-pacc-accent-2{elseif $package.accentuation==3} bm-pacc-accent bm-pacc-accent-3{/if}">
			{if $package.accentuation==1}<span class="badge bg-success-lt text-success mb-1">{lng p="pacc_accent_1"}</span>
			{elseif $package.accentuation==2}<span class="badge bg-info-lt text-info mb-1">{lng p="pacc_accent_2"}</span>
			{elseif $package.accentuation==3}<span class="badge bg-warning-lt text-warning mb-1">{lng p="pacc_accent_3"}</span>
			{/if}
			<strong class="d-block">{text value=$package.title cut=25}</strong>
		</th>
		<th class="pacc-spacer-cell"></th>
		{/foreach}
	</tr>
	</thead>

	<tbody>
	<tr>
		<td colspan="{math equation="x+x+1" x=$matrix.packages|@count}" class="folderGroup">
			<button type="button" class="bm-pacc-section-toggle" onclick="toggleGroup(0,'pacc0');">
				<i class="ti ti-chevron-{if isset($smarty.cookies.toggleGroup.pacc0) && $smarty.cookies.toggleGroup.pacc0=='closed'}right{else}down{/if} icon icon-sm text-secondary" id="groupImage_0" aria-hidden="true"></i>
				{lng p="pacc_infos"}
			</button>
		</td>
	</tr>
	</tbody>
	<tbody id="group_0" style="display:{if isset($smarty.cookies.toggleGroup.pacc0) && $smarty.cookies.toggleGroup.pacc0=='closed'}none{/if};">
	<tr>
		<th scope="row">{lng p="pacc_price"}</th>
		{foreach from=$matrix.packages item=package}
		<td class="text-center{if $package.accentuation==1} bm-pacc-accent bm-pacc-accent-1{elseif $package.accentuation==2} bm-pacc-accent bm-pacc-accent-2{elseif $package.accentuation==3} bm-pacc-accent bm-pacc-accent-3{/if}">
		{if $package.isFree}
			<span class="fw-bold">{lng p="pacc_free"}</span>
		{else}
			<small class="text-secondary d-block">{text value=$package.priceInterval}</small>
			<span class="fw-bold">{text value=$package.price}</span>
			<small class="text-secondary d-block">{text value=$package.priceTax allowEmpty=true}</small>
		{/if}
		</td>
		<td class="pacc-spacer-cell"></td>
		{/foreach}
	</tr>
	</tbody>

	<tbody>
	<tr>
		<td colspan="{math equation="x+x+1" x=$matrix.packages|@count}" class="folderGroup">
			<button type="button" class="bm-pacc-section-toggle" onclick="toggleGroup(1,'pacc1');">
				<i class="ti ti-chevron-{if isset($smarty.cookies.toggleGroup.pacc1) && $smarty.cookies.toggleGroup.pacc1=='closed'}right{else}down{/if} icon icon-sm text-secondary" id="groupImage_1" aria-hidden="true"></i>
				{lng p="pacc_features"}
			</button>
		</td>
	</tr>
	</tbody>
	<tbody id="group_1" style="display:{if isset($smarty.cookies.toggleGroup.pacc1) && $smarty.cookies.toggleGroup.pacc1=='closed'}none{/if};">
	{foreach from=$matrix.fields item=fieldTitle key=fieldKey}
	<tr>
		<th scope="row">{$fieldTitle}</th>
		{foreach from=$matrix.packages item=package}
		<td class="text-center{if $package.accentuation==1} bm-pacc-accent bm-pacc-accent-1{elseif $package.accentuation==2} bm-pacc-accent bm-pacc-accent-2{elseif $package.accentuation==3} bm-pacc-accent bm-pacc-accent-3{/if}">{paccFormatField value=$package.fields.$fieldKey key=$fieldKey cut=25}</td>
		<td class="pacc-spacer-cell"></td>
		{/foreach}
	</tr>
	{/foreach}
	</tbody>

	<tbody>
	<tr>
		<td colspan="{math equation="x+x+1" x=$matrix.packages|@count}" class="folderGroup">
			<button type="button" class="bm-pacc-section-toggle" onclick="toggleGroup(2,'pacc2');">
				<i class="ti ti-chevron-{if isset($smarty.cookies.toggleGroup.pacc2) && $smarty.cookies.toggleGroup.pacc2=='closed'}right{else}down{/if} icon icon-sm text-secondary" id="groupImage_2" aria-hidden="true"></i>
				{lng p="pacc_selection"}
			</button>
		</td>
	</tr>
	</tbody>
	<tbody id="group_2" style="display:{if isset($smarty.cookies.toggleGroup.pacc2) && $smarty.cookies.toggleGroup.pacc2=='closed'}none{/if};">
	<tr>
		<th scope="row">&nbsp;</th>
		{foreach from=$matrix.packages item=package}
		<td class="text-center{if $package.accentuation==1} bm-pacc-accent bm-pacc-accent-1{elseif $package.accentuation==2} bm-pacc-accent bm-pacc-accent-2{elseif $package.accentuation==3} bm-pacc-accent bm-pacc-accent-3{/if}">
			<button type="button" class="btn btn-sm{if $package.accentuation==1} btn-success{elseif $package.accentuation==2} btn-info{elseif $package.accentuation==3} btn-warning{else} btn-primary{/if} my-1" onclick="document.location.href='{sessionurl file='prefs.php' params="action=pacc_mod&do=order&id={$package.id}"|escape:'javascript'}';">
				{lng p="pacc_order"}
			</button>
			<div class="small"><a href="javascript:void(0);" onclick="openOverlay('{sessionurl file='prefs.php' params="action=paccPackageDetails&id={$package.id}"|escape:'javascript'}','{lng p="pacc_packagedetails"}: {text value=$package.title escape=true}',450,{$poHeight});">{lng p="pacc_packagedetails"}</a></div>
		</td>
		<td class="pacc-spacer-cell"></td>
		{/foreach}
	</tr>
	</tbody>
</table>
</div>
</div>

</div></div>
</div>
