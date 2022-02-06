<fieldset>
	<legend>{lng p="gateways"}</legend>

	<form action="prefs.sms.php?action=gateways&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'gateway_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="title"}</th>
			<th width="60">&nbsp;</th>
		</tr>

		{foreach from=$gateways item=gateway}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/gateway.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center">{if !$gateway.default}<input type="checkbox" name="gateway_{$gateway.id}" />{/if}</td>
			<td>{text value=$gateway.titel}</td>
			<td>
				<a href="prefs.sms.php?action=gateways&do=edit&id={$gateway.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				{if !$gateway.default}<a href="prefs.sms.php?action=gateways&delete={$gateway.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>{/if}
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
	<legend>{lng p="addgateway"}</legend>

	<form action="prefs.sms.php?action=gateways&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/gateway32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="title"}:</td>
				<td class="td2"><input type="text" style="width:85%;" id="titel" name="titel" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="getstring"}:</td>
				<td class="td2"><textarea id="getstring" name="getstring" style="width:100%;height:80px;"></textarea></td>
			</tr>
			<tr>
				<td class="td1">{lng p="returnvalue"}:</td>
				<td class="td2"><input type="text" size="10" id="success" name="success" value="100" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="user"}:</td>
				<td class="td2"><input type="text" size="36" id="user" name="user" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="password"}:</td>
				<td class="td2"><input type="password" autocomplete="off" size="36" id="pass" name="pass" value="" /></td>
			</tr>
		</table>

		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="defaults"}</legend>

	<script src="{$tpldir}js/smsgateways.js"></script>
</fieldset>