<fieldset>
	<legend>{lng p="installedplugins"}</legend>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped">
				<thead>
				<tr>
					<th style="width: 20px;">&nbsp;</th>
					<th>{lng p="title"}</th>
					<th>{lng p="author"}</th>
					<th>{lng p="info"}</th>
					<th>{lng p="status"}</th>
					<th>&nbsp;</th>
				</tr>
				</thead>
				<thead>
				{foreach from=$plugins item=pluginPackage key=packageSignature}
				<tr class="tableSubHead">
					<td colspan="5">
						<a href="javascript:togglePluginPackage('{$packageSignature}');"><img src="{$tpldir}images/contract.png" border="0" alt="" align="absmiddle" id="packageImage_{$packageSignature}" heigbt="11" width="11" /></a>
						{if $pluginPackage.name}<b>{lng p="package"}:</b> {text value=$pluginPackage.name}{else}{lng p="withoutpackage"}{/if}
					</td>
					<td class="text-end">
						{if $packageSignature}<a href="plugins.php?action={$action}&do=deletePackage&package={$packageSignature}&sid={$sid}" onclick="return confirm('{lng p="realpackage"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>{else}&nbsp;{/if}
					</td>
				</tr>
				</thead>
				<tbody id="package_{$packageSignature}" style="display:;">
				{foreach from=$pluginPackage.plugins item=plugin}
					{cycle name=class values="td1,td2" assign=class}
					<tr class="{$class}">
						<td><i class="fa-solid fa-puzzle-piece {if !$plugin.installed}text-grey{else}text-yellow{/if}"></td>
						<td>
							{text value=$plugin.title}<br />
							<small>{text value=$plugin.name}</small>
						</td>
						<td>{text value=$plugin.author}</td>
						<td>{lng p="version"}: {text value=$plugin.version}<br /><small>{lng p="type"}: {$plugin.type}</small></td>
						<td>{if $plugin.installed}{lng p="installed"}{else}{lng p="notinstalled"}{/if}<br />
							{if $plugin.paused}<small>{lng p="paused"}</small>{/if}</td>
						<td class="text-nowrap text-end">
							<div class="btn-group btn-group-sm">
								<a href="plugins.php?action={$action}&sid={$sid}&do={if $plugin.installed}de{/if}activatePlugin&plugin={$plugin.name}" title="{lng p="acdeactivate"}" onclick="return confirm('{lng p="reallyplugin"}');" class="btn btn-sm"><i class="fa-solid fa-toggle-off"></i></a>
								{if $plugin.installed}<a href="plugins.php?action={$action}&sid={$sid}&do={if $plugin.paused}un{/if}pausePlugin&plugin={$plugin.name}" title="{if $plugin.paused}{lng p="continue"}{else}{lng p="pause"}{/if}" class="btn btn-sm">{if $plugin.paused}error{else}<i class="fa-regular fa-circle-check"></i>{/if}</a>{/if}
							</div>
						</td>
					</tr>
				{/foreach}
				</tbody>
				{/foreach}
			</table>
		</div>
	</div>
</fieldset>

{if $reloadMenu}
	<script>
		<!--
		parent.frames['menu'].location.href = 'main.php?action=menu&item=4&sid={$sid}';
		//-->
	</script>
{/if}
