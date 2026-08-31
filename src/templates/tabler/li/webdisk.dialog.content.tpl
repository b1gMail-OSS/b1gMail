{if isset($history) && $history|@count > 0}
<nav class="bm-webdisk-breadcrumb" aria-label="{lng p="webdisk"}">
	{foreach from=$history item=pathItem name=pathLoop}
	{if !$smarty.foreach.pathLoop.first}<span class="bm-webdisk-breadcrumb-sep" aria-hidden="true">/</span>{/if}
	{if $smarty.foreach.pathLoop.last}
	<span class="bm-webdisk-breadcrumb-current">{text value=$pathItem.title cut=32}</span>
	{else}
	<a href="javascript:changePath({$pathItem.id});" class="bm-webdisk-breadcrumb-link">{text value=$pathItem.title cut=24}</a>
	{/if}
	{/foreach}
</nav>
{/if}
<div class="bm-webdisk-picker-split">
	<div class="bm-webdisk-picker-pane bm-webdisk-picker-folders" role="region" aria-label="{lng p="folders"}">
		<div class="bm-webdisk-picker-pane-head">{lng p="folders"}</div>
		<div class="bm-webdisk-picker-pane-scroll">
			{if $parentID >= 0}
			<a class="contentItem bm-webdisk-item bm-webdisk-folder-item bm-webdisk-parent-item" href="javascript:changePath({$parentID});">
				<span class="bm-webdisk-item-icon">
					<i class="ti ti-arrow-up icon" aria-hidden="true"></i>
				</span>
				<span class="bm-webdisk-item-label">{lng p="parentfolder"}</span>
			</a>
			{/if}
			{foreach from=$folders item=item}
			<a id="folder_{$item.id}" class="contentItem bm-webdisk-item bm-webdisk-folder-item" href="javascript:changePath({$item.id});">
				<span class="bm-webdisk-item-icon">
					<i class="ti ti-folder icon" aria-hidden="true"></i>
				</span>
				<span class="bm-webdisk-item-label">{text value=$item.title cut=28}</span>
				<i class="ti ti-chevron-right icon bm-webdisk-item-chevron" aria-hidden="true"></i>
			</a>
			{foreachelse}
			{if $parentID < 0}<div class="bm-webdisk-picker-empty text-secondary">–</div>{/if}
			{/foreach}
		</div>
	</div>
	<div class="bm-webdisk-picker-pane bm-webdisk-picker-files" role="region" aria-label="{lng p="filename"}">
		<div class="bm-webdisk-picker-pane-head">{lng p="filename"}</div>
		<div class="bm-webdisk-picker-pane-scroll">
			{foreach from=$files item=item}
			<a id="file_{$item.id}" class="contentItem bm-webdisk-item bm-webdisk-file-item" href="javascript:changeFile({$item.id}, {$pathID}, '{text value=$item.title escape=true}');">
				<span class="bm-webdisk-item-icon">
					<i class="ti ti-file icon" aria-hidden="true"></i>
				</span>
				<span class="bm-webdisk-item-label">{text value=$item.title cut=32}</span>
			</a>
			{foreachelse}
			<div class="bm-webdisk-picker-empty text-secondary">–</div>
			{/foreach}
		</div>
	</div>
</div>
