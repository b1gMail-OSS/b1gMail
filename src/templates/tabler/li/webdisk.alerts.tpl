{if isset($uploadErrors) && $uploadErrors|@count > 0}
<div class="alert alert-danger alert-dismissible bm-webdisk-alert" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-alert-circle alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-webdisk-alert-body">
			{if $uploadErrors|@count == 1}
				{foreach from=$uploadErrors key=file item=msg}
					{if $file}<div class="fw-semibold mb-1">{text value=$file}</div>{/if}
					<div>{$msg}</div>
				{/foreach}
			{else}
				<ul class="mb-0 ps-3">
				{foreach from=$uploadErrors key=file item=msg}
					<li>{if $file}<span class="fw-semibold">{text value=$file cut=60}</span>: {/if}{$msg}</li>
				{/foreach}
				</ul>
			{/if}
		</div>
	</div>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
{/if}
{if isset($uploadSuccess) && $uploadSuccess|@count > 0}
<div class="alert alert-success alert-dismissible bm-webdisk-alert" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-check alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-webdisk-alert-body">
			{if $uploadSuccess|@count == 1}
				{foreach from=$uploadSuccess key=file item=msg}
					{if $file}<div class="fw-semibold mb-1">{text value=$file}</div>{/if}
					<div>{$msg}</div>
				{/foreach}
			{else}
				<ul class="mb-0 ps-3">
				{foreach from=$uploadSuccess key=file item=msg}
					<li>{if $file}<span class="fw-semibold">{text value=$file cut=60}</span>: {/if}{$msg}</li>
				{/foreach}
				</ul>
			{/if}
		</div>
	</div>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
{/if}
{if isset($fileShareNoticeURL) && $fileShareNoticeURL != ''}
<div class="alert alert-success alert-dismissible bm-webdisk-alert" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-link alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-webdisk-alert-body">
			<div class="fw-semibold mb-1">{lng p="sharing"}: {if $fileShareNoticeName|default:'' != ''}{text value=$fileShareNoticeName}{/if}</div>
			<div><a href="{$fileShareNoticeURL|escape}" target="_blank">{$fileShareNoticeURL|escape}</a></div>
		</div>
	</div>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
{/if}
