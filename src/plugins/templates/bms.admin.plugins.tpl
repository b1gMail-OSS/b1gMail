<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="installedplugins"}</legend>
	
	<div class="card mb-3"><div class="table-responsive"><table class="table table-vcenter table-striped card-table">
		<tr>
			<th style="width: 2rem;">&nbsp;</th>
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
		<tr>
			<td class="text-nowrap"><i class="fa-solid fa-puzzle-piece{if $plugin.active} text-yellow{else} text-secondary{/if}" aria-hidden="true"></i></td>
			<td>{text value=$plugin.title}<br /><small>{text value=$plugin.name}</small></td>
			{if !$updateCheck}
			<td>{text value=$plugin.author}</td>
			<td>{lng p="version"}: {text value=$plugin.version}<br /><small>{$plugin.filename}</small></td>
			<td>{if $plugin.active}{lng p="active"}{else}{lng p="inactive"}{/if}</td>
			<td>
				<form method="post" action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=plugins"}" class="d-inline" onsubmit="return confirm('{lng p="reallyplugin"}');">
					{csrffield}
					<input type="hidden" name="do" value="{if $plugin.active}deactivatePlugin{else}activatePlugin{/if}" />
					<input type="hidden" name="filename" value="{$plugin.filename|escape}" />
					<button type="submit" class="btn btn-sm btn-ghost-secondary p-0" title="{lng p="acdeactivate"}"><img src="{$tpldir}images/plugin_switch.png" alt="{lng p="acdeactivate"}" width="16" height="16" /></button>
				</form>
			</td>
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
	</table></div></div>
</fieldset>

{if !$updateCheck}
<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="updates"}</legend>
	
	<table width="100%">
	<tr>
		<td align="left" valign="top" width="40"><img src="{$tpldir}images/updates.png" border="0" alt="" width="32" height="32" /></td>
		<td>
			{lng p="bms_updatesdesc"}
	
			<div align="center">
				<br />
				<button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=plugins&updateCheck=true"}';"  >{lng p="searchupdatesnow"} &raquo;</button>
			</div>
		</td>
	</tr>
	</table>
</fieldset>
{else}
<p>
	<button type="button" class="btn btn-outline-secondary btn-sm"  onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=plugins"}';" >&laquo; {lng p="back"}</button>
</p>
<script>
<!--
{literal}
	function checkForBMSPluginUpdates(fileName)
	{
		MakeXMLRequest('{/literal}{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=plugins&action=updateCheck"}{literal}&filename=' + fileName,
						_checkForPluginUpdates);
	}
{/literal}
{foreach from=$plugins item=plugin}
	checkForBMSPluginUpdates('{$plugin.filename}');
{/foreach}
//-->
</script>
{/if}
					