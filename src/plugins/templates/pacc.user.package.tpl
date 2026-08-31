{capture assign="dialogTitleText"}{lng p="pacc_packagedetails"}: {text value=$package.titel}{/capture}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-pacc-package bm-dialog-modal-sections" dialogOnLoad="documentLoader()"}

<div class="bm-dialog-page bm-dialog-pacc-package-page">
	<div class="card mb-3">
		<div class="card-header">
			<h3 class="card-title mb-0">
				<i class="ti ti-package icon icon-sm text-secondary me-1" aria-hidden="true"></i>
				{text value=$package.titel}
				{if $package.geloescht}<small class="text-secondary"><em>({lng p="pacc_deletedpackage"})</em></small>{/if}
			</h3>
		</div>
		<div class="card-body">
			<h4 class="h5 mb-2">{lng p="pacc_description"}</h4>
			<div class="text-secondary mb-0">{$package.beschreibung}</div>
		</div>
	</div>

	<div class="card mb-3">
		<div class="table-responsive">
			<table class="table table-vcenter table-bordered mb-0">
				<tr>
					<td class="fw-bold text-nowrap">{lng p="pacc_price"}</td>
					{foreach from=$matrix.packages item=package}
					<td>{if $package.isFree}{lng p="pacc_free"}{else}<strong>{text value=$package.price}</strong> <small class="text-secondary">({text value=$package.priceInterval}{if $package.priceTax}, {text value=$package.priceTax}{/if})</small>{/if}</td>
					{/foreach}
				</tr>

				{foreach from=$matrix.fields item=fieldTitle key=fieldKey}
				<tr>
					<td class="fw-bold text-nowrap">{$fieldTitle}</td>
					{foreach from=$matrix.packages item=package}
					<td>{paccFormatField value=$package.fields.$fieldKey key=$fieldKey cut=25}</td>
					{/foreach}
				</tr>
				{/foreach}
			</table>
		</div>
	</div>

	<div class="bm-dialog-actions">
		<div class="bm-dialog-actions-right ms-auto">
			<button type="button" class="btn btn-primary" onclick="parent.hideOverlay();">
				<i class="ti ti-x icon" aria-hidden="true"></i>
				{lng p="close"}
			</button>
		</div>
	</div>
</div>

{include file="li/dialog.foot.tpl"}
