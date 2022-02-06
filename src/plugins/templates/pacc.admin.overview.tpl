<fieldset>
	<legend>{lng p="license"}</legend>

	<table width="100%">
		<tr>
			<td rowspan="2" width="40" align="center" valign="top"><img src="../plugins/templates/images/pacc_logo.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1" width="24%">{lng p="version"}:</td>
			<td class="td2" width="26%">{$version}</td>

			<td rowspan="2" colspan="3">&nbsp;</td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="overview"}</legend>

	<table width="100%">
		<tr>
			<td rowspan="4" width="40" align="center" valign="top"><img src="../plugins/templates/images/pacc_payments32.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1" width="24%">{lng p="pacc_outstandingpayments"}:</td>
			<td class="td2" width="26%">{$outstandingPayments}</td>

			<td rowspan="2" width="40" align="center" valign="top"><img src="../plugins/templates/images/pacc_packages32.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1" width="24%">{lng p="pacc_packages"}:</td>
			<td class="td2" width="26%">{$packageCount}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="pacc_banktransfer"}:</td>
			<td class="td2">{$advancePayments}</td>

			<td class="td1">{lng p="pacc_subscribers"}:</td>
			<td class="td2">{$subscriberCount}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="pacc_sofortueberweisung"}:</td>
			<td class="td2">{$suPayments}</td>

			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td class="td1">{lng p="pacc_paypal"}:</td>
			<td class="td2">{$paypalPayments}</td>

			<td colspan="3">&nbsp;</td>
		</tr>

		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
		<tr>
			<td rowspan="2" width="40" align="center" valign="top"><img src="../plugins/templates/images/pacc_revenue32.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1">{lng p="pacc_revenue"} ({lng p="overall"}):</td>
			<td class="td2">{$overallRevenue}</td>

			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td class="td1">{lng p="pacc_revenue"} ({lng p="pacc_thismonth"}):</td>
			<td class="td2">{$monthRevenue}</td>

			<td colspan="3">&nbsp;</td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="notices"}</legend>

	<table width="100%" id="noticeTable">
	{foreach from=$notices item=notice}
		<tr>
			<td width="20" valign="top"><img src="{$tpldir}images/{$notice.type}.png" width="16" height="16" border="0" alt="" align="absmiddle" /></td>
			<td valign="top">{$notice.text}</td>
			<td align="right" valign="top" width="20">{if $notice.link}<a href="{$notice.link}sid={$sid}"><img src="{$tpldir}images/go.png" border="0" alt="" width="16" height="16" /></a>{else}&nbsp;{/if}</td>
		</tr>
	{/foreach}
	</table>
</fieldset>

<!--<script src="https://service.b1gmail.org/premiumaccount/updates/?do=noticeJS&version={$version}&lang={$lang}"></script>-->
