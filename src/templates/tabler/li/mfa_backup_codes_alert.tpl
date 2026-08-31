<div class="alert alert-warning alert-dismissible" role="alert">
	<div class="alert-icon">
		<i class="ti ti-alert-triangle icon alert-icon icon-2" aria-hidden="true"></i>
	</div>
	<div>
		<h4 class="alert-heading">{lng p="mfa_backup_new"}</h4>
		<div class="alert-description">
			<p class="mb-2">{lng p="mfa_backup_hint"}</p>
			<ul class="list-unstyled font-monospace mb-0">
				{foreach from=$backupCodes item=code}
					<li>{$code}</li>
				{/foreach}
			</ul>
		</div>
	</div>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{lng p="close"}"></button>
</div>
