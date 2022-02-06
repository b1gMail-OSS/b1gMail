<fieldset>
	<legend>{lng p="updates"}</legend>
	
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th>{lng p="title"}</th>
			<th width="120">{lng p="installed"}</th>
			<th width="120">{lng p="current"}</th>
		</tr>
		
		{foreach from=$plugins item=pluginPackage key=packageSignature}
		
		<tr class="tableSubHead">
			<td colspan="4">
				<a href="javascript:togglePluginPackage('{$packageSignature}');"><img src="{$tpldir}images/contract.png" border="0" alt="" align="absmiddle" id="packageImage_{$packageSignature}" heigbt="11" width="11" /></a>
				{if $pluginPackage.name}<b>{lng p="package"}:</b> {text value=$pluginPackage.name}{else}{lng p="withoutpackage"}{/if}
			</td>
		</tr>
		
		<tbody id="package_{$packageSignature}" style="display:;">
		
		{foreach from=$pluginPackage.plugins item=plugin}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td><img src="{$tpldir}images/plugin_{if !$plugin.installed}in{/if}active.png" border="0" alt="" width="16" height="16" /></td>
			<td>{text value=$plugin.title}<br /><small>{text value=$plugin.name}</small></td>
			<td>{lng p="version"}: {text value=$plugin.version}</td>
			<td id="updates_{$plugin.name}">
				<div align="center">
					<img src="{$tpldir}images/load_16.gif" border="0" alt="" />
				</div>
			</td>
		</tr>
		{/foreach}
		
		</tbody>
		
		{/foreach}
	</table>
</fieldset>

<script>
<!--
{foreach from=$plugins item=pluginPackage key=packageSignature}{foreach from=$pluginPackage.plugins item=plugin}
	checkForPluginUpdates('{$plugin.name}');
{/foreach}{/foreach}	
//-->
</script>
