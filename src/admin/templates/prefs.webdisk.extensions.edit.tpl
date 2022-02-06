<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.webdisk.php?action=extensions&do=edit&id={$extension.id}&save=true&sid={$sid}" method="post" onsubmit="spin(this)" enctype="multipart/form-data">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/extension.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="filetypes"}:</td>
				<td class="td2"><input{if $extension.ext[0]=='.'} disabled="disabled"{/if} type="text" style="width:85%;" name="ext" value="{text value=$extension.ext}" /></td>
			</tr>
			<tr>
				<td class="td1" width="150">{lng p="icon"}:</td>
				<td class="td2"><input type="file" name="icon" style="width:440px;" accept="image/*" /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>