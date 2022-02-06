<fieldset>
	<legend>{lng p="paymentmethods"}</legend>
	
	<form action="prefs.payments.php?action=paymethods&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'method_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="title"}</th>
			<th width="75">&nbsp;</th>
		</tr>
		
		{foreach from=$methods item=method}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/ico_pay_banktransfer.png" border="0" alt="" width="16" height="16" /></td>
			<td><input type="checkbox" name="method_{$method.methodid}" /></td>
			<td>{text value=$method.title}</td>
			<td>
				<a href="prefs.payments.php?action=paymethods&{if $method.enabled}dis{else}en{/if}able={$method.methodid}&sid={$sid}" title="{if $method.enabled}{lng p="disable"}{else}{lng p="enable"}{/if}"><img src="{$tpldir}images/{if $method.enabled}ok{else}error{/if}.png" width="16" height="16" alt="{if $method.enabled}{lng p="disable"}{else}{lng p="enable"}{/if}" border="0" /></a>
				<a href="prefs.payments.php?action=paymethods&do=edit&methodid={$method.methodid}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="prefs.payments.php?action=paymethods&delete={$method.methodid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
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
	<legend>{lng p="addpaymethod"}</legend>
	
	<form action="prefs.payments.php?action=paymethods&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/add32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1">{lng p="title"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="title" value="" /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>
