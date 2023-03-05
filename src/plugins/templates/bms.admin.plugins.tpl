<fieldset>
	<legend>{lng p="installedplugins"}</legend>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped">
				<thead>
				<tr>
					<th style="width: 20px;">&nbsp;</th>
					<th>{lng p="title"}</th>

					{if !$updateCheck}
						<th>{lng p="author"}</th>
						<th>{lng p="info"}</th>
						<th>{lng p="status"}</th>
						<th>&nbsp;</th>
					{else}
						<th style="width: 120px;">{lng p="installed"}</th>
						<th style="width: 120px;">{lng p="current"}</th>
					{/if}
				</tr>
				</thead>
				<tbody>
				{foreach from=$plugins item=plugin}
					{cycle name=class values="td1,td2" assign=class}
					<tr class="{$class}">
						<td><i class="fa-solid fa-puzzle-piece {if !$plugin.active}text-grey{else}text-yellow{/if}"></td>
						<td>{text value=$plugin.title}<br /><small>{text value=$plugin.name}</small></td>
						{if !$updateCheck}
							<td>{text value=$plugin.author}</td>
							<td>{lng p="version"}: {text value=$plugin.version}<br /><small>{$plugin.filename}</small></td>
							<td>{if $plugin.active}{lng p="active"}{else}{lng p="inactive"}{/if}</td>
							<td><a href="{$pageURL}&action=plugins&sid={$sid}&do={if $plugin.active}de{/if}activatePlugin&filename={$plugin.filename}" onclick="return confirm('{lng p="reallyplugin"}');" class="btn btn-sm"><i class="fa-solid fa-toggle-off"></i></a></td>
						{else}
							<td>{lng p="version"}: {text value=$plugin.version}</td>
							<td id="updates_{$plugin.filename}" class="text-center">
								<img src="{$tpldir}images/load_16.gif" border="0" alt="" />
							</td>
						{/if}
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</fieldset>

{if !$updateCheck}
	<fieldset>
		<legend>{lng p="updates"}</legend>

		<div class="alert alert-info">{lng p="bms_updatesdesc"}</div>
		<div class="text-center"><input class="btn btn-primary" type="button" onclick="document.location.href='{$pageURL}&action=plugins&updateCheck=true&sid={$sid}';" value=" {lng p="searchupdatesnow"} &raquo; " /></div>
	</fieldset>
{else}
	<input class="btn btn-primary" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=plugins&sid={$sid}';" />
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