<fieldset>
	<legend>{lng p="accentries"}</legend>
	
	<form action="payments.php?action=export&do=exportAccEntries&sid={$sid}" method="post" target="_top">
		<table>
			<tr>
				<td width="40" valign="top" rowspan="4"><img src="{$tpldir}images/ico_accentries.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="120">{lng p="from"}:</td>
				<td class="td2">
					{html_select_date prefix="start" time=$start start_year="-5" field_order="DMY" field_separator="."} 
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="to"}:</td>
				<td class="td2">
					{html_select_date prefix="end" time=$end start_year="-5" field_order="DMY" field_separator="."}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="account_debit"}:</td>
				<td class="td2">
					<table>
						<tr>
							<td>{lng p="banktransfer"}:</td>
							<td><input type="text" name="accounts[0]" size="8" value="1100" /></td>
						</tr>
						<tr>
							<td>{lng p="su"}:</td>
							<td><input type="text" name="accounts[2]" size="8" value="1100" /></td>
						</tr>
						<tr>
							<td>{lng p="paypal"}:</td>
							<td><input type="text" name="accounts[1]" size="8" value="1101" /></td>
						</tr>
						<tr>
							<td>{lng p="skrill"}:</td>
							<td><input type="text" name="accounts[3]" size="8" value="1102" /></td>
						</tr>
						{foreach from=$paymentMethods key=methodID item=method}
						<tr>
							<td>{text value=$method.title}</td>
							<td><input type="text" name="accounts[-{$methodID}]" size="8" value="{$methodID+1102}" /></td>
						</tr>
						{/foreach}
					</table>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="account_credit"}:</td>
				<td class="td2">
					<input type="text" name="account" size="8" value="8400" />
				</td>
			</tr>
		</table>
		
		<p align="right">
			<input class="button" type="submit" value=" {lng p="export"} " />
		</p>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="invoices"}</legend>
	
	<form action="payments.php?action=export&do=exportInvoices&sid={$sid}" method="post" target="_top">
		<table>
			<tr>
				<td width="40" valign="top" rowspan="3"><img src="{$tpldir}images/ico_prefs_invoices.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="120">{lng p="from"}:</td>
				<td class="td2">
					{html_select_date prefix="start" time=$start start_year="-5" field_order="DMY" field_separator="."} 
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="to"}:</td>
				<td class="td2">
					{html_select_date prefix="end" time=$end start_year="-5" field_order="DMY" field_separator="."}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="options"}:</td>
				<td class="td2">
					<input type="checkbox" name="paidOnly" id="paidOnly" checked="checked" />
						<label for="paidOnly"><b>{lng p="paidonly"}</b></label>
				</td>
			</tr>
		</table>
		
		<p align="right">
			<input class="button" type="submit" value=" {lng p="export"} " />
		</p>
	</form>
</fieldset>
