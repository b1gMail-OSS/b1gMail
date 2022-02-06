<fieldset>
	<legend>{lng p="profilefields"}</legend>
	
	<form action="prefs.profilefields.php?sid={$sid}" method="post" name="f1" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'field_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="field"}</th>
			<th>{lng p="validityrule"}</th>
			<th width="100">{lng p="type"}</th>
			<th width="50">{lng p="oblig"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$fields item=field}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/field.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center"><input type="checkbox" name="field_{$field.id}" /></td>
			<td>{text value=$field.feld}<br /><small>{text value=$field.extra}</small></td>
			<td>{text value=$field.rule}</td>
			<td>{$field.typ}</td>
			<td><input type="checkbox" disabled="disabled"{if $field.pflicht} checked="checked"{/if} /></td>
			<td>
				<a href="prefs.profilefields.php?do=edit&id={$field.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="prefs.profilefields.php?delete={$field.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
			</td>
			
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="7">
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
	<legend>{lng p="addprofilefield"}</legend>
	
	<form action="prefs.profilefields.php?add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/field32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="field"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="feld" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="validityrule"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="rule" value="" />
					<br /><small>{lng p="pfrulenote"}</small></td>
			</tr>
			<tr>
				<td class="td1">{lng p="type"}:</td>
				<td class="td2"><select name="typ">
				{foreach from=$fieldTypeTable key=id item=text}
					<option value="{$id}">{$text}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="oblig"}?</td>
				<td class="td2"><input type="checkbox" name="pflicht" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="show"}:</td>
				<td class="td2">
					<input type="checkbox" name="show_signup" id="show_signup" checked="checked" />
					<label for="show_signup">{lng p="signup"}</label><br />
					<input type="checkbox" name="show_li" id="show_li" checked="checked" />
					<label for="show_li">{lng p="li"}</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="options"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="extra" value="" />
					<br /><small>{lng p="optionsdesc"}</small></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>