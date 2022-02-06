<br />
<table>
	<tr>
		<td valign="top" width="64" align="center"><i class="fa fa-exclamation-triangle fa-5x" aria-hidden="true"></i></td>
		<td valign="top">
			<b>{lng p="error"}</b>
			<br />{$msg}
			<br /><br />
			<input type="button" value="&laquo; {lng p="back"}" onclick="{if !$backLink}history.back(){else}document.location.href='{$backLink}'{/if};" />
			{if $otherButton}<input type="button" value="{text value=$otherButton.caption}" onclick="document.location.href='{$otherButton.href}';" />{/if}
		</td>
	</tr>
</table>