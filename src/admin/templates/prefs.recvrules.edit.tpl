<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.recvrules.php?do=edit&id={$rule.id}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/rule32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="field"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="field" value="{text value=$rule.field}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="expression"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="expression" value="{text value=$rule.expression allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="action"}:</td>
				<td class="td2"><select name="ruleAction">
				{foreach from=$ruleActionTable key=id item=text}
					<option value="{$id}"{if $rule.action==$id} selected="selected"{/if}>{$text}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="value"}:</td>
				<td class="td2"><input type="text" size="10" name="value" value="{$rule.value}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="type"}:</td>
				<td class="td2"><select name="type">
				{foreach from=$ruleTypeTable key=id item=text}
					<option value="{$id}"{if $rule.type==$id} selected="selected"{/if}>{$text}</option>
				{/foreach}
				</select></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>
