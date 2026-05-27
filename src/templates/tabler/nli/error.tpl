{capture assign="standalonePageTitle"}{$service_title}: {lng p="error"}{/capture}
{include file="nli/standalone.open.tpl"}

<div class="page page-center flex-fill">
	<div class="container-tight py-4">
		{include file="nli/login.brand.tpl"}

		<div class="empty">
			<div class="empty-img">
				<i class="ti ti-alert-triangle icon text-warning nli-status-icon" aria-hidden="true"></i>
			</div>
			<p class="empty-title">{$title}</p>
			<p class="empty-subtitle text-secondary">{$description}</p>
			<div class="empty-action">
				<a href="./" class="btn btn-primary">
					<i class="ti ti-arrow-left icon icon-2" aria-hidden="true"></i>
					{lng p="start"}
				</a>
			</div>
		</div>

		<div class="text-center text-secondary mt-4 small">
			{if isset($year)}&copy; {$year} {/if}{$service_title}
		</div>
	</div>
</div>

{include file="nli/standalone.close.tpl"}
