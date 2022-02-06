<fieldset>
	<legend>{$msgTitle}</legend>
	
	{if $msgIcon}
	<table>
		<tr>
			<td width="36" valign="top"><img src="{$tpldir}images/{$msgIcon}.png" border="0" alt="" width="32" height="32" /></td>
			<td valign="top">{$msgText}</td>
		</tr>
	</table>
	{else}
	{$msgText}
	{/if}
	
	{if $backLink}
	<p align="right">
		<input class="button" type="button" onclick="document.location.href='{$backLink}sid={$sid}';" value=" {lng p="back"} " />
	</p>
	{else}
	<p align="right">
		<input class="button" type="button" onclick="history.back(1);" value=" {lng p="back"} " />
	</p>	
	{/if}
</fieldset>

{if $reloadMenu}
<script>
<!--
	parent.frames['menu'].location.href = 'main.php?action=menu&item=4&sid={$sid}';
//-->
</script>
{/if}
