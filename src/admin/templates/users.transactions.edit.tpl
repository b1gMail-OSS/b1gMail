<fieldset>
	<legend>{lng p="edittransaction"} ({email value=$user.email}, #{$user.id})</legend>

	<form action="users.php?do=editTransaction&transactionid={$tx.transactionid}&save=true&sid={$sid}" method="post" onsubmit="spin(this);">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/transaction.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="description"}:</td>
				<td class="td2"><input type="text" style="width:85%;" required="required" name="description" value="{text value=$tx.description allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="credits"}:</td>
				<td class="td2"><input type="number" min="-999999" max="999999" step="1" name="amount" value="{$tx.amount}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="status"}:</td>
				<td class="td2"><select name="status">
					<option value="1"{if $tx.status==1} selected="selected"{/if}>{lng p="booked"}</option>
					<option value="2"{if $tx.status==2} selected="selected"{/if}>{lng p="cancelled"}</option>
				</select></td>
			</tr>
			<tr>
				<td>
					&nbsp;
				</td>
				<td align="right">
					<input class="button" type="submit" value=" {lng p="save"} " />
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<p>
	<div style="float:left" class="buttons">
		<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='users.php?do=transactions&id={$user.id}&sid={$sid}';" />
	</div>
</p>
