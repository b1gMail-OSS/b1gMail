<fieldset>
	<legend>{lng p="installedplugins"}</legend>
	
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th>{lng p="title"}</th>
			
			{if !$updateCheck}
			<th>{lng p="author"}</th>
			<th>{lng p="info"}</th>
			<th>{lng p="status"}</th>
			<th>&nbsp;</th>
			{else}
			<th width="120">{lng p="installed"}</th>
			<th width="120">{lng p="current"}</th>
			{/if}
		</tr>
		
		{foreach from=$plugins item=plugin}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td><img src="{$tpldir}images/plugin_{if !$plugin.active}in{/if}active.png" border="0" alt="" width="16" height="16" /></td>
			<td>{text value=$plugin.title}<br /><small>{text value=$plugin.name}</small></td>
			{if !$updateCheck}
			<td>{text value=$plugin.author}</td>
			<td>{lng p="version"}: {text value=$plugin.version}<br /><small>{$plugin.filename}</small></td>
			<td>{if $plugin.active}{lng p="active"}{else}{lng p="inactive"}{/if}</td>
			<td><a href="{$pageURL}&action=plugins&sid={$sid}&do={if $plugin.active}de{/if}activatePlugin&filename={$plugin.filename}" onclick="return confirm('{lng p="reallyplugin"}');"><img src="{$tpldir}images/plugin_switch.png" border="0" alt="{lng p="acdeactivate"}" border="0" width="16" height="16" /></a></td>
			{else}
			<td>{lng p="version"}: {text value=$plugin.version}</td>
			<td id="updates_{$plugin.filename}">
				<div align="center">
					<img src="{$tpldir}images/load_16.gif" border="0" alt="" />
				</div>
			</td>
			{/if}
		</tr>
		{/foreach}
	</table>
</fieldset>

{if !$updateCheck}
<fieldset>
	<legend>{lng p="updates"}</legend>
	
	<table width="100%">
	<tr>
		<td align="left" valign="top" width="40"><img src="{$tpldir}images/updates.png" border="0" alt="" width="32" height="32" /></td>
		<td>
			{lng p="bms_updatesdesc"}
	
			<div align="center">
				<br />
				<input class="button" type="button" onclick="document.location.href='{$pageURL}&action=plugins&updateCheck=true&sid={$sid}';" value=" {lng p="searchupdatesnow"} &raquo; " />
			</div>
		</td>
	</tr>
	</table>
</fieldset>
{else}
<p>
	<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=plugins&sid={$sid}';" />
</p>
<script>
<!--
{literal}
	function checkForBMSPluginUpdates(fileName)
	{
		MakeXMLRequest('{/literal}{$pageURL}{literal}&action=plugins&do=updateCheck&sid=' + currentSID 
							+ '&do=updateCheck'
							+ '&filename=' + fileName,
						_checkForPluginUpdates);
	}
{/literal}
{foreach from=$plugins item=plugin}
	checkForBMSPluginUpdates('{$plugin.filename}');
{/foreach}
//-->
</script>
{/if}
					