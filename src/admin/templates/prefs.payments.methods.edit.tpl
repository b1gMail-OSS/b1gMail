<form action="prefs.payments.php?action=paymethods&do=edit&methodid={$row.methodid}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">

	<fieldset>
		<legend>{lng p="paymentmethod"}: {text value=$row.title}</legend>
	
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="3"><img src="{$tpldir}images/ico_pay_banktransfer.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="180">{lng p="title"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="title" value="{text value=$row.title allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="enable"}?</td>
				<td class="td2"><input type="checkbox" name="enabled"{if $row.enabled} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="invoice"}:</td>
				<td class="td2"><select name="invoice">
					<option value="at_order"{if $row.invoice=='at_order'} selected="selected"{/if}>{lng p="at_order"}</option>
					<option value="at_activation"{if $row.invoice=='at_activation'} selected="selected"{/if}>{lng p="at_activation"}</option>
				</select></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="fields"}</legend>
		
		<table class="list">
			<tr>
				<th width="25">&nbsp;</th>
				<th>{lng p="title"}</th>
				<th width="120">{lng p="type"}</th>
				<th>{lng p="options"}</th>
				<th width="70">{lng p="oblig"}?</th>
				<th>{lng p="validityrule"}</th>
				<th width="70">{lng p="pos"}</th>
				<th width="70">{lng p="delete"}?</th>
			</tr>
			
			{foreach from=$fields item=field key=fieldID}
			{cycle name=class values="td1,td2" assign=class}
			{assign var=lastPos value=$field.pos}
			<tr class="{$class}">
				<td><img src="{$tpldir}images/field.png" border="0" alt="" width="16" height="16" /></td>
				<td><input type="text" style="width: 90%;" name="fields[{$fieldID}][title]" value="{text value=$field.title allowEmpty=true}" /></td>
				<td><select name="fields[{$fieldID}][type]">
				{foreach from=$fieldTypeTable key=id item=text}
					<option value="{$id}"{if $id==$field.type} selected="selected"{/if}>{$text}</option>
				{/foreach}
				</select></td>
				<td><input type="text" style="width: 90%;" name="fields[{$fieldID}][options]" value="{text value=$field.options allowEmpty=true}" /></td>
				<td style="text-align:center;"><input type="checkbox" name="fields[{$fieldID}][oblig]"{if $field.oblig} checked="checked"{/if} /></td>
				<td><input type="text" style="width: 90%;" name="fields[{$fieldID}][rule]" value="{text value=$field.rule allowEmpty=true}" /></td>
				<td><input type="text" name="fields[{$fieldID}][pos]" value="{text value=$field.pos allowEmpty=true}" size="5" /></td>
				<td style="text-align:center;"><input type="checkbox" name="fields[{$fieldID}][delete]" /></td>
			</tr>
			{/foreach}
			
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
				<td><img src="{$tpldir}images/add32.png" border="0" alt="" width="16" height="16" /></td>
				<td><input type="text" style="width: 90%;" name="fields[new][title]" /></td>
				<td><select name="fields[new][type]">
				{foreach from=$fieldTypeTable key=id item=text}
					<option value="{$id}"{if $id==1} selected="selected"{/if}>{$text}</option>
				{/foreach}
				</select></td>
				<td><input type="text" style="width: 90%;" name="fields[new][options]" /></td>
				<td style="text-align:center;"><input type="checkbox" name="fields[new][oblig]" /></td>
				<td><input type="text" style="width: 90%;" name="fields[new][rule]" /></td>
				<td><input type="text" name="fields[new][pos]" value="{$lastPos+10}" size="5" /></td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</fieldset>

	<p>
		<div style="float:left;" class="buttons">
			<input class="button" type="button" onclick="document.location.href='prefs.payments.php?action=paymethods&sid={$sid}';" value=" &laquo; {lng p="back"} " />
		</div>
		
		<div style="float:right;" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>

</form>
