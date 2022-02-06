<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.sms.php?action=types&do=edit&save=true&id={$type.id}&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/type32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="title"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="titel" value="{text value=$type.titel}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="gateway"}:</td>
				<td class="td2"><select name="gateway">
					<option value="0"{if $gateway.id==0} selected="selected"{/if}>({lng p="defaultgateway"})</option>
				{foreach from=$gateways item=gateway}
					<option value="{$gateway.id}"{if $type.gateway==$gateway.id} selected="selected"{/if}>{text value=$gateway.titel}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="type"}:</td>
				<td class="td2"><input type="text" size="6" name="typ" value="{text value=$type.typ allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="price"}:</td>
				<td class="td2"><input type="text" size="6" name="price" value="{$type.price}" /> {lng p="credits"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="maxlength"}:</td>
				<td class="td2"><input type="text" size="6" name="maxlength" value="{$type.maxlength}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="prefs"}:</td>
				<td class="td2"><input type="checkbox" name="flags[1]" value="true" id="flag_1"{if $type.flags&1} checked="checked"{/if} />
								<label for="flag_1">{lng p="disablesender"}</label></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>