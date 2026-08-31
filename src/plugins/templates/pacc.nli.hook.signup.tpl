<input type="hidden" name="paccPackage" value="{$paccPackage.id}" />

<div class="card mb-3">
	<div class="card-header">
		<h3 class="card-title mb-0">
			<i class="ti ti-shopping-cart icon icon-sm text-secondary me-1" aria-hidden="true"></i>
			{lng p="pacc_package"}
		</h3>
	</div>
	<div class="card-body">
		<strong>{text value=$paccPackage.title}</strong>
		{if $paccPackage.isFree}
			<span class="text-secondary">({lng p="pacc_free"})</span>
		{else}
			<span class="text-secondary">({text value=$paccPackage.priceInterval}
			{text value=$paccPackage.price}
			{text value=$paccPackage.priceTax})</span>
		{/if}
	</div>
</div>
