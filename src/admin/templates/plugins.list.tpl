<fieldset>
	<legend>{lng p="installedplugins"}</legend>
	
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th>{lng p="title"}</th>
			<th>{lng p="author"}</th>
			<th>{lng p="info"}</th>
			<th>{lng p="status"}</th>
			<th>&nbsp;</th>
		</tr>
		
		{foreach from=$plugins item=pluginPackage key=packageSignature}
		
		<tr class="tableSubHead">
			<td colspan="5">
				<a href="javascript:togglePluginPackage('{$packageSignature}');"><img src="{$tpldir}images/contract.png" border="0" alt="" align="absmiddle" id="packageImage_{$packageSignature}" heigbt="11" width="11" /></a>
				{if $pluginPackage.name}<b>{lng p="package"}:</b> {text value=$pluginPackage.name}{else}{lng p="withoutpackage"}{/if}
			</td>
			<td>
				{if $packageSignature}<a href="plugins.php?action={$action}&do=deletePackage&package={$packageSignature}&sid={$sid}" onclick="return confirm('{lng p="realpackage"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>{else}&nbsp;{/if}
			</td>
		</tr>
		
		<tbody id="package_{$packageSignature}" style="display:;">
		
		{foreach from=$pluginPackage.plugins item=plugin}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td><img src="{$tpldir}images/plugin_{if !$plugin.installed}in{/if}active.png" border="0" alt="" width="16" height="16" /></td>
			<td>{text value=$plugin.title}<br /><small>{text value=$plugin.name}</small></td>
			<td>{text value=$plugin.author}</td>
			<td>{lng p="version"}: {text value=$plugin.version}<br /><small>{lng p="type"}: {$plugin.type}</small></td>
			<td>{if $plugin.installed}{lng p="installed"}{else}{lng p="notinstalled"}{/if}<br />
				{if $plugin.paused}<small>{lng p="paused"}</small>{/if}</td>
			<td>
				<a href="plugins.php?action={$action}&sid={$sid}&do={if $plugin.installed}de{/if}activatePlugin&plugin={$plugin.name}" title="{lng p="acdeactivate"}" onclick="return confirm('{lng p="reallyplugin"}');"><img src="{$tpldir}images/plugin_switch.png" border="0" alt="{lng p="acdeactivate"}" border="0" width="16" height="16" /></a>
				{if $plugin.installed}<a href="plugins.php?action={$action}&sid={$sid}&do={if $plugin.paused}un{/if}pausePlugin&plugin={$plugin.name}" title="{if $plugin.paused}{lng p="continue"}{else}{lng p="pause"}{/if}"><img src="{$tpldir}images/{if $plugin.paused}error{else}ok{/if}.png" border="0" alt="{if $plugin.paused}{lng p="continue"}{else}{lng p="pause"}{/if}" border="0" width="16" height="16" /></a>{/if}
			</td>
		</tr>
		{/foreach}
		
		</tbody>
		
		{/foreach}
	</table>
</fieldset>

{if $reloadMenu}
<script>
<!--
	parent.frames['menu'].location.href = 'main.php?action=menu&item=4&sid={$sid}';
//-->
</script>
{/if}
