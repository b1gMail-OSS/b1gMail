<fieldset>
	<legend>{lng p="modfax_fax"}</legend>
	<p class="text-secondary mb-0">{lng p="version"}: <strong>{$version|escape}</strong></p>
</fieldset>

<fieldset>
	<legend>{lng p="overview"}</legend>
	<div class="row g-3">
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title"><i class="ti ti-printer me-1"></i> {lng p="modfax_faxtoday"} / {lng p="modfax_faxmonth"} / {lng p="modfax_faxall"}</h3>
				</div>
				<div class="table-responsive">
					<table class="table table-vcenter table-striped card-table mb-0">
						<tr><td class="text-secondary">{lng p="modfax_faxtoday"}</td><td class="text-end"><strong>{$faxToday}</strong></td></tr>
						<tr><td class="text-secondary">{lng p="modfax_faxmonth"}</td><td class="text-end"><strong>{$faxMonth}</strong></td></tr>
						<tr><td class="text-secondary">{lng p="modfax_faxall"}</td><td class="text-end"><strong>{$faxAll}</strong></td></tr>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title"><i class="ti ti-alert-triangle me-1"></i> {lng p="modfax_errtoday"} / {lng p="modfax_errmonth"} / {lng p="modfax_errall"}</h3>
				</div>
				<div class="table-responsive">
					<table class="table table-vcenter table-striped card-table mb-0">
						<tr><td class="text-secondary">{lng p="modfax_errtoday"}</td><td class="text-end"><strong>{$errToday}</strong></td></tr>
						<tr><td class="text-secondary">{lng p="modfax_errmonth"}</td><td class="text-end"><strong>{$errMonth}</strong></td></tr>
						<tr><td class="text-secondary">{lng p="modfax_errall"}</td><td class="text-end"><strong>{$errAll}</strong></td></tr>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title"><i class="ti ti-coins me-1"></i> {lng p="credits"}</h3>
				</div>
				<div class="table-responsive">
					<table class="table table-vcenter table-striped card-table mb-0">
						<tr><td class="text-secondary">{lng p="modfax_creditstoday"}</td><td class="text-end"><strong>{$creditsToday}</strong></td></tr>
						<tr><td class="text-secondary">{lng p="modfax_creditsmonth"}</td><td class="text-end"><strong>{$creditsMonth}</strong></td></tr>
						<tr><td class="text-secondary">{lng p="modfax_creditsall"}</td><td class="text-end"><strong>{$creditsAll}</strong></td></tr>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title"><i class="ti ti-receipt-refund me-1"></i> {lng p="modfax_refundstoday"}</h3>
				</div>
				<div class="table-responsive">
					<table class="table table-vcenter table-striped card-table mb-0">
						<tr><td class="text-secondary">{lng p="modfax_refundstoday"}</td><td class="text-end"><strong>{$refundsToday}</strong></td></tr>
						<tr><td class="text-secondary">{lng p="modfax_refundsmonth"}</td><td class="text-end"><strong>{$refundsMonth}</strong></td></tr>
						<tr><td class="text-secondary">{lng p="modfax_refundsall"}</td><td class="text-end"><strong>{$refundsAll}</strong></td></tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</fieldset>

{if $notices|@count > 0}
<fieldset>
	<legend>{lng p="notices"}</legend>
	<div class="card">
		<div class="list-group list-group-flush">
		{foreach from=$notices item=notice}
			<div class="list-group-item d-flex align-items-start gap-2">
				{if $notice.type == 'error'}<i class="ti ti-alert-circle text-danger"></i>{else}<i class="ti ti-info-circle text-info"></i>{/if}
				<div class="flex-fill">{$notice.text}</div>
				{if $notice.link}<a href="{$notice.link}{$sessionUrlSuffixHtml}" class="btn btn-sm btn-ghost-primary"><i class="ti ti-external-link"></i></a>{/if}
			</div>
		{/foreach}
		</div>
	</div>
</fieldset>
{/if}
