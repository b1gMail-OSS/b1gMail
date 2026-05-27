<table cellspacing="0" cellpadding="0" width="100%" height="{$height}" class="bm-webdisk-columns-table">
	<tr>
{foreach from=$columns item=column}
		<td class="contentColumn bm-webdisk-column">
			<div class="contentColumnDiv bm-webdisk-column-scroll" style="height:{$height}px;">
			{foreach from=$column item=item}
				<a id="{if $item.type==1}folder{else}file{/if}_{$item.id}" class="contentItem bm-webdisk-item{if $item.inPath} contentItemActive bm-webdisk-item-active{/if}" href="javascript:{if $item.type==1}changePath({$item.id}){else}changeFile({$item.id}, {$item.folderID}, '{text value=$item.title escape=true}'){/if};">
					<span class="bm-webdisk-item-icon">
						<i class="ti ti-{if $item.type==1}folder{else}file{/if} icon" aria-hidden="true"></i>
					</span>
					<span class="bm-webdisk-item-label">{text value=$item.title cut=24}</span>
					{if $item.type==1}<i class="ti ti-chevron-right icon bm-webdisk-item-chevron" aria-hidden="true"></i>{/if}
				</a>
			{/foreach}
			</div>
		</td>
{/foreach}
		<td class="bm-webdisk-column-spacer">&nbsp;</td>
	</tr>
</table>
