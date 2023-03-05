<fieldset>
	<legend>{lng p="updates"}</legend>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped">
				<thead>
				<tr>
					<th style="width: 20px;">&nbsp;</th>
					<th>{lng p="title"}</th>
					<th style="width: 120px;">{lng p="installed"}</th>
					<th style="width: 120px;">{lng p="current"}</th>
				</tr>
				</thead>
				<thead>
				{foreach from=$plugins item=pluginPackage key=packageSignature}
				<tr class="tableSubHead">
					<td colspan="4">
						<a href="javascript:togglePluginPackage('{$packageSignature}');"><img src="{$tpldir}images/contract.png" border="0" alt="" align="absmiddle" id="packageImage_{$packageSignature}" heigbt="11" width="11" /></a>
						{if $pluginPackage.name}<b>{lng p="package"}:</b> {text value=$pluginPackage.name}{else}{lng p="withoutpackage"}{/if}
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
						<td>{lng p="version"}: {text value=$plugin.version}</td>
						<td class="text-end" id="updates_{$plugin.name}">
							<img src="{$tpldir}images/load_16.gif" border="0" alt="" />
						</td>
					</tr>
				{/foreach}
				</tbody>
				{/foreach}
			</table>
		</div>
	</div>
</fieldset>

<script>
	<!--
	{foreach from=$plugins item=pluginPackage key=packageSignature}{foreach from=$pluginPackage.plugins item=plugin}
	checkForPluginUpdates('{$plugin.name}');
	{/foreach}{/foreach}
	//-->
</script>
