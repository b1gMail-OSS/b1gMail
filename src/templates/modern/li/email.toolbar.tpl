<table cellspacing="0" cellpadding="0">
	<tr>
		{hook id="email.toolbar.tpl:firstColumn"}
		{comment text="space"}
		<td><img align="absmiddle" src="{$tpldir}images/li/tb_sep.gif" border="0" alt="" /></td>
		<td><small>&nbsp; {lng p="space"}: &nbsp;</small></td>
		<td>{progressBar value=$spaceUsed max=$spaceLimit width=100}</td>
		<td><small>&nbsp; {size bytes=$spaceUsed} / {size bytes=$spaceLimit} {lng p="used"}</small></td>
		
		{if $enablePreview}
		<td width="15">&nbsp;</td>
		<td><img align="absmiddle" src="{$tpldir}images/li/tb_sep.gif" border="0" alt="" /></td>
		<td><small>&nbsp; {lng p="preview"}: &nbsp;</small></td>
		<td><select class="smallInput" onchange="updatePreviewPosition(this)">
			<option value="bottom"{if !$narrow} selected="selected"{/if}>{lng p="bottom"}</option>
			<option value="right"{if $narrow} selected="selected"{/if}>{lng p="right"}</option>
		</select></td>
		{/if}
		
		{hook id="email.toolbar.tpl:lastColumn"}
	</tr>
</table>
