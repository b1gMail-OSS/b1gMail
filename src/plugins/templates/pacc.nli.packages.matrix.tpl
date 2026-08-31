{if !$nliPackages}
<form action="{$paccOrderAction}" method="post" class="bm-pacc-nli-form" onsubmit="submitSignupForm()">
<input type="hidden" name="userID" value="{$userID}" />
<input type="hidden" name="userToken" value="{$userToken}" />
{if $signUp}<input type="hidden" name="signUp" value="true" />{/if}
{/if}

<div class="card bm-pacc-nli-matrix-card overflow-hidden">
<div class="table-responsive">
<table class="table table-vcenter bm-pacc-matrix mb-0">
	<colgroup>
		<col class="bm-pacc-label-col" />
		{foreach from=$matrix.packages item=package}
		<col id="col_{$package.id}"{if $package.accentuation==1} class="bm-pacc-col-accent bm-pacc-col-accent-1"{elseif $package.accentuation==2} class="bm-pacc-col-accent bm-pacc-col-accent-2"{elseif $package.accentuation==3} class="bm-pacc-col-accent bm-pacc-col-accent-3"{/if} />
		<col class="pacc-spacer" />
		{/foreach}
	</colgroup>

	<thead>
	<tr>
		<th>&nbsp;</th>
		{foreach from=$matrix.packages item=package}
		<th class="text-center{if $package.accentuation==1} bm-pacc-accent bm-pacc-accent-1{elseif $package.accentuation==2} bm-pacc-accent bm-pacc-accent-2{elseif $package.accentuation==3} bm-pacc-accent bm-pacc-accent-3{/if}">
			{if $package.accentuation==1}<span class="bm-pacc-accent-badge bm-pacc-accent-badge-1">{lng p="pacc_accent_1"}</span>
			{elseif $package.accentuation==2}<span class="bm-pacc-accent-badge bm-pacc-accent-badge-2">{lng p="pacc_accent_2"}</span>
			{elseif $package.accentuation==3}<span class="bm-pacc-accent-badge bm-pacc-accent-badge-3">{lng p="pacc_accent_3"}</span>
			{/if}
			<strong class="bm-pacc-package-title">{text value=$package.title cut=40}</strong>
		</th>
		<th class="pacc-spacer-cell"></th>
		{/foreach}
	</tr>
	</thead>

	<tbody>
	<tr>
		<td colspan="{math equation="x+x+1" x=$matrix.packages|@count}" class="folderGroup">
			<span class="bm-pacc-nli-section-title">{lng p="pacc_infos"}</span>
		</td>
	</tr>
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

	<tr>
		<td colspan="{math equation="x+x+1" x=$matrix.packages|@count}" class="folderGroup">
			<span class="bm-pacc-nli-section-title">{lng p="pacc_features"}</span>
		</td>
	</tr>
	{foreach from=$matrix.fields item=fieldTitle key=fieldKey}
	<tr>
		<th scope="row">{$fieldTitle}</th>
		{foreach from=$matrix.packages item=package}
		<td class="text-center{if $package.accentuation==1} bm-pacc-accent bm-pacc-accent-1{elseif $package.accentuation==2} bm-pacc-accent bm-pacc-accent-2{elseif $package.accentuation==3} bm-pacc-accent bm-pacc-accent-3{/if}">{paccFormatField value=$package.fields.$fieldKey key=$fieldKey cut=40}</td>
		<td class="pacc-spacer-cell"></td>
		{/foreach}
	</tr>
	{/foreach}

	{if !$nliPackages}
	<tr>
		<td colspan="{math equation="x+x+1" x=$matrix.packages|@count}" class="folderGroup">
			<span class="bm-pacc-nli-section-title">{lng p="pacc_selection"}</span>
		</td>
	</tr>
	<tr>
		<th scope="row">&nbsp;</th>
		{foreach from=$matrix.packages item=package}
		<td class="text-center{if $package.accentuation==1} bm-pacc-accent bm-pacc-accent-1{elseif $package.accentuation==2} bm-pacc-accent bm-pacc-accent-2{elseif $package.accentuation==3} bm-pacc-accent bm-pacc-accent-3{/if}">
			<input type="radio" class="form-check-input m-0 bm-pacc-package-radio" name="package" id="package_{$package.id}" value="{$package.id}"{if $paccDefaultPackageId == $package.id} checked="checked"{/if} />
		</td>
		<td class="pacc-spacer-cell"></td>
		{/foreach}
	</tr>
	{elseif $paccOrderButtons && $regEnabled}
	<tr>
		<td colspan="{math equation="x+x+1" x=$matrix.packages|@count}" class="folderGroup">
			<span class="bm-pacc-nli-section-title">{lng p="pacc_selection"}</span>
		</td>
	</tr>
	<tr>
		<th scope="row">&nbsp;</th>
		{foreach from=$matrix.packages item=package}
		<td class="text-center{if $package.accentuation==1} bm-pacc-accent bm-pacc-accent-1{elseif $package.accentuation==2} bm-pacc-accent bm-pacc-accent-2{elseif $package.accentuation==3} bm-pacc-accent bm-pacc-accent-3{/if}">
			<a href="{$paccSignupBase}?paccPackage={$package.id}{$sessionUrlSuffixHtml}" class="bm-pacc-order-btn bm-pacc-order-btn-{if $package.accentuation==1}1{elseif $package.accentuation==2}2{elseif $package.accentuation==3}3{else}0{/if}">
				{if $package.isFree}
					<span class="fa fa-user-plus" aria-hidden="true"></span> {lng p="signup"}
				{else}
					<span class="fa fa-shopping-cart" aria-hidden="true"></span> {lng p="pacc_order"}
				{/if}
			</a>
		</td>
		<td class="pacc-spacer-cell"></td>
		{/foreach}
	</tr>
	{/if}
	</tbody>
</table>
</div>
</div>

{if !$nliPackages}
<div class="alert alert-info mt-3" role="alert">
	<i class="ti ti-info-circle icon icon-sm me-1" aria-hidden="true"></i>
	{lng p="iprecord"}
</div>

<div class="d-flex flex-wrap gap-2 mt-3 bm-pacc-nli-actions">
	{if $signUp&&!$force}
	<button type="submit" name="dontOrder" class="btn btn-outline-secondary">
		<i class="ti ti-x icon icon-sm me-1" aria-hidden="true"></i>{lng p="pacc_dontorder"}
	</button>
	{elseif $signUp}
	<button type="submit" name="dontOrder" class="btn btn-warning">
		<i class="ti ti-x icon icon-sm me-1" aria-hidden="true"></i>{lng p="pacc_abort"}
	</button>
	{/if}

	{if $paccOrderButtons}
	<button type="submit" name="doOrder" id="orderButton" class="btn btn-primary ms-auto"{if !$paccDefaultPackageId} disabled="disabled"{/if} data-loading-text="{lng p="pleasewait"}">
		<i class="ti ti-check icon icon-sm me-1" aria-hidden="true"></i>{lng p="pacc_doorder"}
	</button>
	{/if}
</div>
</form>
{/if}

<script type="text/javascript">
(function() {
	var form = document.querySelector('.bm-pacc-nli-form');
	if(!form)
		return;
	var orderButton = document.getElementById('orderButton');
	if(!orderButton)
		return;
	form.querySelectorAll('.bm-pacc-package-radio').forEach(function(radio) {
		radio.addEventListener('change', function() {
			orderButton.disabled = false;
		});
	});
})();
</script>
