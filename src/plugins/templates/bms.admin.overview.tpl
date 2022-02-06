<fieldset>
	<legend>{lng p="license"}</legend>

	<table width="100%">
		<tr>
			<td rowspan="2" width="40" align="center" valign="top"><img src="../plugins/templates/images/bms_logo.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1" width="24%">{lng p="version"} ({lng p="bms_adminplugin"}):</td>
			<td class="td2" width="26%">{$adminVersion}</td>

			<td rowspan="2" width="40" align="center" valign="top"><img src="{$tpldir}images/ico_license.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1" width="24%">{lng p="bms_licstatus"}:</td>
			<td class="td2" width="26%">
					<a href="{$pageURL}&action=common&sid={$sid}">{if $bms_prefs.licstate==2}
					<font color="red">{lng p="bms_expired"}</font>
					{elseif $bms_prefs.licstate==0}
					<font color="red">{lng p="bms_invalid"}</font>
					{elseif $bms_prefs.licstate==1}
					<font color="darkgreen">{lng p="bms_valid"}</font>
					{else}
					{lng p="bms_validating"}
					{/if}</a>
			</td>
		</tr>
		<tr>
			<td class="td1">{lng p="version"} ({lng p="bms_core"}):</td>
			<td class="td2">{if $coreVersion}{$coreVersion}{else}<i>({lng p="unknown"})</i>{/if}</td>
			<td colspan="2">&nbsp;</td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="overview"}</legend>

	<table width="100%">
		{if ($bms_prefs.licfeatures&4)!=0}
		<tr>
			<td rowspan="3" width="40" align="center" valign="top"><img src="../plugins/templates/images/bms_common.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1" width="24%">{lng p="bms_pop3today"}:</td>
			<td class="td2" width="26%">{$pop3Today}</td>

			<td rowspan="3" width="40" align="center" valign="top"><img src="../plugins/templates/images/bms_stats.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1" width="24%">{lng p="bms_pop3traffic"}:</td>
			<td class="td2" width="26%">{size bytes=$pop3Traffic}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="bms_imaptoday"}:</td>
			<td class="td2">{$imapToday}</td>

			<td class="td1">{lng p="bms_imaptraffic"}:</td>
			<td class="td2">{size bytes=$imapTraffic}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="bms_smtptoday"}:</td>
			<td class="td2">{$smtpToday}</td>

			<td class="td1">{lng p="bms_smtptraffic"}:</td>
			<td class="td2">{size bytes=$smtpTraffic}</td>
		</tr>

		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
		{/if}
		<tr>
			<td rowspan="3" width="40" align="center" valign="top"><img src="../plugins/templates/images/bms_queue.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1">{lng p="bms_queueentries"}:</td>
			<td class="td2" width="20%">{$queueEntries}</td>

			<td rowspan="3" width="40" align="center" valign="top"><img src="../plugins/templates/images/bms_features.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1">{lng p="bms_queue"}?</td>
			<td class="td2" width="20%"><img src="templates/images/{if $queueRunning}ok{else}delete{/if}.png" align="absmiddle" border="0" alt="" width="16" height="16" />
										{if $queueRunning}{lng p="bms_running"} ({$threadCount} {lng p="bms_threads"}){else}{lng p="bms_not_running"}{/if}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="bms_inbound"}:</td>
			<td class="td2">{$queueInbound}</td>

			<td class="td1">{lng p="bms_feature_tls"}?</td>
			<td class="td2" width="20%"><img src="templates/images/{if ($bms_prefs.core_features&1)!=0}ok{else}delete{/if}.png" border="0" alt="" width="16" height="16" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="bms_outbound"}:</td>
			<td class="td2">{$queueOutbound}</td>

			<td class="td1">{lng p="bms_feature_sig"}?</td>
			<td class="td2"><img src="templates/images/{if ($bms_prefs.core_features&2)!=0}ok{else}delete{/if}.png" border="0" alt="" width="16" height="16" /></td>
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

<!--<script src="https://service.b1gmail.org/b1gmailserver/updates/?do=noticeJS&adminVersion={$adminVersion}&coreVersion={$coreVersion}&lang={$lang}"></script>-->
