{capture assign="standalonePageTitle"}{$service_title} - {lng p="maintenance"}{/capture}
{include file="nli/standalone.open.tpl"}

<div class="page page-center flex-fill">
	<div class="container-tight py-4">
		{include file="nli/login.brand.tpl"}

		<div class="empty">
			<div class="empty-img">
				<i class="ti ti-tool icon text-primary nli-status-icon" aria-hidden="true"></i>
			</div>
			<p class="empty-title">{lng p="maintenance"}</p>
			<div class="empty-subtitle text-secondary bm-nli-maintenance-text">
				{$text}
			</div>
		</div>

		<div class="text-center text-secondary mt-4 small">
			{if isset($year)}&copy; {$year} {/if}{$service_title}
		</div>
	</div>
</div>

{include file="nli/standalone.close.tpl"}
