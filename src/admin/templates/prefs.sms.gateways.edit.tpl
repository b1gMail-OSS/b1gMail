<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.sms.php?action=gateways&do=edit&save=true&id={$gateway.id}&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/gateway32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="title"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="titel" value="{text value=$gateway.titel}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="getstring"}:</td>
				<td class="td2"><textarea name="getstring" style="width:100%;height:80px;">{text value=$gateway.getstring}</textarea></td>
			</tr>
			<tr>
				<td class="td1">{lng p="returnvalue"}:</td>
				<td class="td2"><input type="text" size="10" name="success" value="{text value=$gateway.success allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="user"}:</td>
				<td class="td2"><input type="text" size="36" id="user" name="user" value="{text value=$gateway.user allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="password"}:</td>
				<td class="td2"><input type="password" autocomplete="off" size="36" id="pass" name="pass" value="{text value=$gateway.pass allowEmpty=true}" /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>