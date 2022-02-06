<fieldset>
	<legend>{lng p="types"}</legend>
	
	<form action="prefs.sms.php?action=types&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'type_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="title"}</th>
			<th>{lng p="type"}</th>
			<th>{lng p="maxlength"}</th>
			<th>{lng p="price"} ({lng p="credits"})</th>
			<th width="75">&nbsp;</th>
		</tr>
		
		{foreach from=$types item=type}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/type{if $type.std}_std{/if}.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center">{if !$type.std}<input type="checkbox" name="type_{$type.id}" />{/if}</td>
			<td>{text value=$type.titel}</td>
			<td width="100">{text value=$type.typ}</td>
			<td width="100">{$type.maxlength}</td>
			<td width="100">{$type.price}</td>
			<td>
				<a href="prefs.sms.php?action=types&do=edit&id={$type.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				{if !$type.std}<a href="prefs.sms.php?action=types&setDefault={$type.id}&sid={$sid}"><img src="{$tpldir}images/type_std.png" border="0" alt="{lng p="setdefault"}" width="16" height="16" /></a>
				<a href="prefs.sms.php?action=types&delete={$type.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>{/if}
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
	<legend>{lng p="addtype"}</legend>
	
	<form action="prefs.sms.php?action=types&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/type32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="title"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="titel" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="gateway"}:</td>
				<td class="td2"><select name="gateway">
					<option value="0">({lng p="defaultgateway"})</option>
				{foreach from=$gateways item=gateway}
					<option value="{$gateway.id}">{text value=$gateway.titel}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="type"}:</td>
				<td class="td2"><input type="text" size="6" name="typ" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="price"}:</td>
				<td class="td2"><input type="text" size="6" name="price" /> {lng p="credits"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="maxlength"}:</td>
				<td class="td2"><input type="text" size="6" value="160" name="maxlength" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="prefs"}:</td>
				<td class="td2"><input type="checkbox" name="flags[1]" value="true" id="flag_1" />
								<label for="flag_1">{lng p="disablesender"}</label></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>