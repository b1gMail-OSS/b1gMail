{if !$omitTable}<div id="contentHeader">
	<div class="left">
		<i class="fa fa-shopping-cart" aria-hidden="true"></i>
		<a href="prefs.php?action=orders&sid={$sid}">{lng p="order"}</a>: {text value=$_pf.invoiceNo}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<table class="listTable">
	<tr>
		<th class="listTableHead" colspan="2">{lng p="order"}: {text value=$_pf.invoiceNo}</th>
	</tr>{/if}
	
{if $_pf.payMethod==0}
	{if !$omitTable}<tr>
		<td class="listTableRight">{/if}<p>
			<p>
				{$_pf.ktoText}
				<br />
			</p>
			
			<div style="float:left;width:49%;">
				<p>
					<strong>{lng p="kto_inh"}:</strong><br />
					{text value=$_pf.ktoInh}
				</p>
	
				<p>
					<strong>{lng p="kto_blz"} ({lng p="kto_inst"}):</strong><br />
					{text value=$_pf.ktoBLZ} ({text value=$_pf.ktoInst})
				</p>
				{if $_pf.ktoIBAN&&$_pf.ktoBIC}
				<p style="color:#666666;">
					<strong>{lng p="kto_iban"}:</strong><br />
					{text value=$_pf.ktoIBAN}
				</p>
				{/if}
				<p>
					<strong>{lng p="kto_subject"}:</strong><br />
					{text value=$_pf.ktoSubject}
				</p>
			</div>
			<div style="float:left;">
				<p>
					<strong>{lng p="kto_nr"}:</strong><br />
					{text value=$_pf.ktoNr}
				</p>
	
				<p>
					<br />
					<br />
				</p>
				{if $_pf.ktoIBAN&&$_pf.ktoBIC}
				<p style="color:#666666;">
					<strong>{lng p="kto_bic"}:</strong><br />
					{text value=$_pf.ktoBIC}
				</p>
				{/if}
				<p>
					<strong>{lng p="amount"}:</strong><br />
					{$_pf.amount}
				</p>
			</div>
			<br style="clear:both;" />
		</p>{if !$omitTable}</td>
	</tr>{/if}
{elseif $_pf.payMethod==1}
	{if !$omitTable}<tr>
		<td class="listTableRight">{/if}
			<p><center>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="ppForm">
					<input type="hidden" name="cmd" value="_xclick" />
					<input type="hidden" name="business" value="{text value=$_pf.payPalMail}" />
					<input type="hidden" name="item_name" value="{$_pf.itemName}" />
					<input type="hidden" name="item_number" value="{$_pf.invoiceNo}" />
					<input type="hidden" name="invoice" value="{$_pf.orderID}" />
					<input type="hidden" name="amount" value="{$_pf.amountEN}" />
					<input type="hidden" name="no_shipping" value="1" />
					<input type="hidden" name="notify_url" value="{$_pf.notifyURL}" />
					<input type="hidden" name="return" value="{$_pf.returnURL}" />
					<input type="hidden" name="no_note" value="1" />
					<input type="hidden" name="currency_code" value="{$_pf.currency}" />
					<input type="submit" value=" {lng p="buynow"} " />
				</form>
				<script>
				<!--
					document.forms['ppForm'].submit();
				//-->
				</script>
				<br /><br />
			</center></p>
		{if !$omitTable}</td>
	</tr>{/if}
{elseif $_pf.payMethod==3}
{if !$omitTable}<tr>
	<td class="listTableRight">{/if}
		<p><center>
			<form action="https://www.moneybookers.com/app/payment.pl" method="post" name="ppForm">
				<input type="hidden" name="transaction_id" value="{$_pf.orderID}" />
				<input type="hidden" name="return_url" value="{$_pf.returnURL}" />
				<input type="hidden" name="cancel_url" value="{$_pf.returnURL}" />
				<input type="hidden" name="pay_to_email" value="{text value=$_pf.skrillMail}" />
				<input type="hidden" name="status_url" value="{$_pf.notifyURL}" />
				<input type="hidden" name="language" value="{lng p="langCode"}" />
				<input type="hidden" name="amount" value="{$_pf.amountEN}" />
				<input type="hidden" name="currency" value="{$_pf.currency}" />
				<input type="hidden" name="detail1_description" value="{$_pf.itemName}" />
				<input type="hidden" name="detail1_text" value="{$_pf.invoiceNo}" />
				<input type="submit" value=" {lng p="buynow"} ">
			</form>
			<script>
			<!--
				document.forms['ppForm'].submit();
			//-->
			</script>
			<br /><br />
		</center></p>
	{if !$omitTable}</td>
</tr>{/if}
{elseif $_pf.payMethod==2}
	{if !$omitTable}<tr>
		<td class="listTableRight">
			{/if}<p><center>
				<form action="https://www.sofortueberweisung.de/payment/start" method="post" name="suForm">
					<input type="hidden" name="user_id" value="{$_pf.suKdNr}" />
					<input type="hidden" name="project_id" value="{$_pf.suPrjNr}" />
					<input type="hidden" name="amount" value="{$_pf.amountEN}" />
					<input type="hidden" name="currency_id" value="{$_pf.currency}" />
					<input type="hidden" name="reason_1" value="{$_pf.invoiceNo}" />
					<input type="hidden" name="reason_2" value="{$_pf.itemName}" />
					<input type="hidden" name="user_variable_0" value="{$_pf.orderID}" />
					<input type="hidden" name="user_variable_1" value="{$sid}" />
					<input type="hidden" name="user_variable_2" value="{$_pf.returnURL_SU}" />
					<input type="hidden" name="user_variable_3" value="{$_pf.returnURL_SU}" />
					{if $_pf.hash}<input type="hidden" name="hash" value="{$_pf.hash}" />{/if}
					<input type="submit" value=" {lng p="buynow"} " />
				</form> 
				<script>
				<!--
					document.forms['suForm'].submit();
				//-->
				</script>
				<br /><br />
			</center></p>{if !$omitTable}
		</td>
	</tr>{/if}
{elseif $_pf.payMethod<0}
	{if !$omitTable}<tr>
		<td class="listTableRight">
			{/if}<p>
				{$_pf.payMethodText}
			</p>{if !$omitTable}
		</td>
	</tr>{/if}
{/if}

{if !$omitTable}
</table>

</div></div>
{/if}
