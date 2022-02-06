<fieldset>
	<legend>{lng p="webdiskicons"}</legend>

	<form action="prefs.webdisk.php?action=extensions&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="36">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'ext_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="filetypes"}</th>
			<th width="200">{lng p="type"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$extensions item=ext}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="prefs.webdisk.php?action=displayExt&id={$ext.id}&sid={$sid}" border="0" alt="" /></td>
			<td align="center">{if $ext.ext[0]!='.'}<input type="checkbox" name="ext_{$ext.id}" />{/if}</td>
			<td>{text value=$ext.ext}</td>
			<td>{text value=$ext.ctype}</td>
			<td>
				<a href="prefs.webdisk.php?action=extensions&do=edit&id={$ext.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				{if $ext.ext[0]!='.'}<a href="prefs.webdisk.php?action=extensions&delete={$ext.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>{/if}
			</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="8">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>
						
						<optgroup label="{lng p="actions"}">
							<option value="delete">{lng p="delete"}</option>
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
			</td>
		</tr>
	</table>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="addwebdiskicon"}</legend>
	
	<form action="prefs.webdisk.php?action=extensions&add=true&sid={$sid}" method="post" onsubmit="spin(this)" enctype="multipart/form-data">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/extension_add.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="filetypes"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="ext" value="" /></td>
			</tr>
			<tr>
				<td class="td1" width="150">{lng p="icon"}:</td>
				<td class="td2"><input type="file" name="icon" style="width:440px;" accept="image/*" /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>