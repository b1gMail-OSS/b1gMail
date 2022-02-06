<fieldset>
	<legend>{lng p="license"}</legend>

	<table width="100%">
		<tr>
			<td rowspan="2" width="40" align="center" valign="top"><img src="../plugins/templates/images/modfax_logo.png" border="0" alt="" width="32" heigh="32" /></td>
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
			<td rowspan="3" width="40" align="center" valign="top"><img src="../plugins/templates/images/modfax_fax32.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1">{lng p="modfax_faxtoday"}:</td>
			<td class="td2" width="15%">{$faxToday}</td>

			<td rowspan="3" width="40" align="center" valign="top"><img src="../plugins/templates/images/modfax_faxerr32.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1">{lng p="modfax_errtoday"}:</td>
			<td class="td2" width="15%">{$errToday}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="modfax_faxmonth"}:</td>
			<td class="td2" width="15%">{$faxMonth}</td>

			<td class="td1">{lng p="modfax_errmonth"}:</td>
			<td class="td2" width="15%">{$errMonth}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="modfax_faxall"}:</td>
			<td class="td2" width="15%">{$faxAll}</td>

			<td class="td1">{lng p="modfax_errall"}:</td>
			<td class="td2" width="15%">{$errAll}</td>
		</tr>

		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
		<tr>
			<td rowspan="3" width="40" align="center" valign="top"><img src="../plugins/templates/images/modfax_credits32.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1">{lng p="modfax_creditstoday"}:</td>
			<td class="td2" width="15%">{$creditsToday}</td>

			<td rowspan="3" width="40" align="center" valign="top"><img src="../plugins/templates/images/modfax_refunds32.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1">{lng p="modfax_refundstoday"}:</td>
			<td class="td2" width="15%">{$refundsToday}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="modfax_creditsmonth"}:</td>
			<td class="td2">{$creditsMonth}</td>

			<td class="td1">{lng p="modfax_refundsmonth"}:</td>
			<td class="td2" width="15%">{$refundsMonth}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="modfax_creditsall"}:</td>
			<td class="td2">{$creditsAll}</td>

			<td class="td1">{lng p="modfax_refundsall"}:</td>
			<td class="td2" width="15%">{$refundsAll}</td>
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

<!--<script src="https://service.b1gmail.org/fax/updates/?do=noticeJS&version={$version}&lang={$lang}"></script>-->
