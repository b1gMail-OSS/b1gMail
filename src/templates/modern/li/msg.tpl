<br />
<table>
	<tr>
		<td valign="top" width="64" align="center"><i class="fa fa-info-circle fa-5x" aria-hidden="true"></i></td>
		<td valign="top">
			<b>{$title}</b>
			<br />{$msg}
			<br /><br />
			<input type="button" value="&laquo; {lng p="back"}" onclick="{if !$backLink}history.back(){else}document.location.href='{$backLink}'{/if};" />
		</td>
	</tr>
</table>