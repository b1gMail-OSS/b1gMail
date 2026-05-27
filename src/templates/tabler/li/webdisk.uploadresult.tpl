<br />
<table>
	<tr>
		<td valign="top" width="64" align="center"><i class="fa fa-info-circle fa-3x" aria-hidden="true"></i></td>
		<td valign="top">
			<b>{lng p="uploadfiles"}</b>
			<br /><br />
			
			<table>
				{foreach from=$success key=file item=msg}
				<tr>
					<td width="16"><i class="fa fa-check" aria-hidden="true"></i></td>
					<td><b>{text value=$file cut=30}</b>&nbsp;&nbsp;</td>
					<td>{$msg}</td>
				</tr>
				{/foreach}
				{foreach from=$error key=file item=msg}
				<tr>
					<td width="16"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></td>
					<td><b>{text value=$file cut=30}</b>&nbsp;&nbsp;</td>
					<td>{$msg}</td>
				</tr>
				{/foreach}
			</table>
			
			<br /><input type="button" value="&laquo; {lng p="back"}" onclick="document.location.href='webdisk.php?folder={$folderID}&sid={$sid}';" />
		</td>
	</tr>
</table>