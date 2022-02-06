{if !$haveRelease}
<fieldset>
	<legend>{lng p="welcome"}</legend>
	
	<table>
		<tr>
			<td width="36" valign="top"><img src="{$tpldir}images/software32.png" border="0" alt="" width="32" height="32" /></td>
			<td valign="top">
				<p>
					{lng p="tbx_welcome1"}
				</p>
				<p>
					{lng p="tbx_welcome2"}
				</p>
			</td>
		</tr>
	</table>
</fieldset>
{/if}

<fieldset>
	<legend>{lng p="versions"}</legend>

	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th>{lng p="version"}</th>
			<th width="180">{lng p="status"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$versions item=version key=versionID}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/toolbox.png" border="0" alt="" width="16" height="16" /></td>
			<td>{$version.base_version}.{$versionID}</td>
			<td>{if $version.status=='released'}{lng p="released"}{else}{lng p="created"}{/if}</td>
			<td>
				{if $version.status=='created'}
					<a href="toolbox.php?do=editVersionConfig&versionid={$versionID}&sid={$sid}" title="{lng p="prefs"}"><img src="{$tpldir}images/prefs.png" border="0" alt="{lng p="prefs"}" width="16" height="16" /></a>
					
					<a href="toolbox.php?do=release&versionid={$versionID}&sid={$sid}" title="{lng p="release"}"><img src="{$tpldir}images/release.png" border="0" alt="{lng p="release"}" width="16" height="16" /></a>
				{/if}
			</td>
		</tr>
		{/foreach}
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="addversion"}</legend>
	
	<form action="toolbox.php?do=addVersion&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/add32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="baseversion"}:</td>
				<td class="td2">
					{$latestVersion}
				</td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>
