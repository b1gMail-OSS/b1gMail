<table cellspacing="0" cellpadding="0">
	<tr>
		{comment text="space"}
		<td><img align="absmiddle" src="{$tpldir}images/li/tb_sep.gif" border="0" alt="" /></td>
		<td><small>&nbsp; {lng p="space"}: &nbsp;</small></td>
		<td>{progressBar value=$spaceUsed max=$spaceLimit width=100}</td>
		<td><small>&nbsp; {size bytes=$spaceUsed} / {size bytes=$spaceLimit}</small></td>
		
		{if $trafficLimit>0}
		{comment text="traffic"}
		<td width="15">&nbsp;</td>
		<td><img align="absmiddle" src="{$tpldir}images/li/tb_sep.gif" border="0" alt="" /></td>
		<td><small>&nbsp; {lng p="traffic"}: &nbsp;</small></td>
		<td>{progressBar value=$trafficUsed max=$trafficLimit width=100}</td>
		<td><small>&nbsp; {size bytes=$trafficUsed} / {size bytes=$trafficLimit}</small></td>
		{/if}
		
		{comment text="viewmode"}
		<td width="15">&nbsp;</td>
		<td><img align="absmiddle" src="{$tpldir}images/li/tb_sep.gif" border="0" alt="" /></td>
		<td><small>&nbsp; {lng p="viewmode"}: &nbsp;</small></td>
		<td><select class="smallInput" onchange="updateWebdiskViewMode(this, '{$folderID}', '{$sid}')">
			<option value="icons"{if $viewMode=="icons"} selected="selected"{/if}>{lng p="icons"}</option>
			<option value="list"{if $viewMode=="list"} selected="selected"{/if}>{lng p="list"}</option>
		</select></td>
		{hook id="webdisk.toolbar.tpl:lastColumn"}
	</tr>
</table>
