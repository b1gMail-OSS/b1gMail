<fieldset>
	<legend>{lng p="recvrules"}</legend>

	<form action="prefs.recvrules.php?sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'rule_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="field"}</th>
			<th>{lng p="expression"}</th>
			<th width="210">{lng p="action"}</th>
			<th width="45">{lng p="value"}</th>
			<th width="120">{lng p="type"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$rules item=rule}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/rule.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center"><input type="checkbox" name="rule_{$rule.id}" /></td>
			<td>{text value=$rule.field}</td>
			<td>{text value=$rule.expression}</td>
			<td>{$rule.action}</td>
			<td>{$rule.value}</td>
			<td>{$rule.type}</td>
			<td>
				<a href="prefs.recvrules.php?do=edit&id={$rule.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="prefs.recvrules.php?delete={$rule.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="8">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>
						
						<optgroup label="{lng p="actions"}">
							<option value="export">{lng p="export"}</option>
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
	<legend>{lng p="addrecvrule"}</legend>
	
	<form action="prefs.recvrules.php?add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/rule32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="field"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="field" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="expression"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="expression" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="action"}:</td>
				<td class="td2"><select name="ruleAction">
				{foreach from=$ruleActionTable key=id item=text}
					<option value="{$id}">{$text}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="value"}:</td>
				<td class="td2"><input type="text" size="10" name="value" value="0" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="type"}:</td>
				<td class="td2"><select name="type">
				{foreach from=$ruleTypeTable key=id item=text}
					<option value="{$id}">{$text}</option>
				{/foreach}
				</select></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="import"}</legend>
	
	<form action="prefs.recvrules.php?import=true&sid={$sid}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
		<p>
			{lng p="ruledesc"}
		</p>
	
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/rules_import.png" border="0" alt="" width="32" height="32" /></td>
				<td>{lng p="rulefile"}:<br />
					<input type="file" name="rulefile" style="width:440px;" accept=".bmrecvrules" /></td>
			</tr>
		</table>
		
		<p align="right">
			<input class="button" type="submit" value=" {lng p="import"} " />
		</p>
	</form>
</fieldset>
