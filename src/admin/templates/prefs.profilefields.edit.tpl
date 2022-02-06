<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.profilefields.php?do=edit&id={$field.id}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/field32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="field"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="feld" value="{text value=$field.feld allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="validityrule"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="rule" value="{text value=$field.rule allowEmpty=true}" />
					<br /><small>{lng p="pfrulenote"}</small></td>
			</tr>
			<tr>
				<td class="td1">{lng p="type"}:</td>
				<td class="td2"><select name="typ">
				{foreach from=$fieldTypeTable key=id item=text}
					<option value="{$id}"{if $field.typ==$id} selected="selected"{/if}>{$text}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="oblig"}?</td>
				<td class="td2"><input type="checkbox" name="pflicht"{if $field.pflicht=='yes'} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="show"}:</td>
				<td class="td2">
					<input type="checkbox" name="show_signup" id="show_signup"{if $field.show_signup=='yes'} checked="checked"{/if} />
					<label for="show_signup">{lng p="signup"}</label><br />
					<input type="checkbox" name="show_li" id="show_li"{if $field.show_li=='yes'} checked="checked"{/if} />
					<label for="show_li">{lng p="li"}</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="options"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="extra" value="{text value=$field.extra allowEmpty=true}" />
					<br /><small>{lng p="optionsdesc"}</small></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>
