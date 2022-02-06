<fieldset>
	<legend>{lng p="lockedusernames"}</legend>
	
	<form action="prefs.common.php?action=lockedusernames&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'locked_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="username"}</th>
			<th width="55">&nbsp;</th>
		</tr>
		
		{foreach from=$lockedUsernames item=locked}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/lockedusername.png" border="0" alt="" width="16" height="16" /></td>
			<td><input type="checkbox" name="locked_{$locked.id}" /></td>
			<td>{$locked.type} &quot;{text value=$locked.username}&quot;</td>
			<td>
				<a href="prefs.common.php?action=lockedusernames&delete={$locked.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="4">
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
	<legend>{lng p="addlockedusername"}</legend>
	
	<form action="prefs.common.php?action=lockedusernames&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/lockedusername32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="type"}:</td>
				<td class="td2"><select name="typ">
					{foreach from=$lockedTypeTable key=id item=text}
						<option value="{$id}">{$text}</option>
					{/foreach}
					</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="username"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="benutzername" value="" /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>
