<table cellspacing="0" cellpadding="0" width="100%" height="{$height}">
	<tr>
{foreach from=$columns item=column}
		<td class="contentColumn">
			<div class="contentColumnDiv" style="height:{$height}px;">
			{foreach from=$column item=item}
				<a id="{if $item.type==1}folder{else}file{/if}_{$item.id}" class="contentItem{if $item.inPath}Active{/if}" href="javascript:{if $item.type==1}changePath({$item.id}){else}changeFile({$item.id}, {$item.folderID}, '{text value=$item.title escape=true}'){/if};">
					&nbsp;
					<img src="{$tpldir}images/li/webdisk_{if $item.type==1}folder{else}file{/if}.png" width="16" height="16" border="0" alt="" />
					{text value=$item.title cut=20}
					{if $item.type==1}<img style="float: right; position: relative; top: -11px; left: -3px;" src="{$tpldir}images/li/mini_arrow_right.png" border="0" alt="" width="8" height="8" />{/if}
				</a>
			{/foreach}
			</div>
		</td>
{/foreach}
		<td>&nbsp;</td>
	</tr>
</table>
