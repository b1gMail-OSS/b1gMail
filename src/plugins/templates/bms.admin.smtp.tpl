<form action="{$pageURL}&sid={$sid}&action=smtp&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="common"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="8" valign="top" width="40"><img src="../plugins/templates/images/bms_common.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_greeting"}:</td>
				<td class="td2"><input type="text" name="smtpgreeting" value="{text value=$bms_prefs.smtpgreeting allowEmpty=true}" size="32" style="width:95%;" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_timeout"}:</td>
				<td class="td2"><input type="text" name="smtp_timeout" value="{$bms_prefs.smtp_timeout}" size="6" />
								{lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="mailmax"}:</td>
				<td class="td2"><input type="text" name="smtp_size_limit" value="{$bms_prefs.smtp_size_limit/1024}" size="6" />
								KB</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_recipient_limit"}:</td>
				<td class="td2"><input type="text" name="smtp_recipient_limit" value="{$bms_prefs.smtp_recipient_limit}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_hop_limit"}:</td>
				<td class="td2"><input type="text" name="smtp_hop_limit" value="{$bms_prefs.smtp_hop_limit}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_smtp_auth"}?</td>
				<td class="td2"><input type="checkbox" name="smtp_auth_enabled"{if $bms_prefs.smtp_auth_enabled==1} checked="checked"{/if} id="smtp_auth_enabled" />
					<label for="smtp_auth_enabled"><b>{lng p="activate"}</b></label><br />
					<input type="checkbox" name="smtp_auth_no_received"{if $bms_prefs.smtp_auth_no_received==1} checked="checked"{/if} id="smtp_auth_no_received" />
					<label for="smtp_auth_no_received"><b>{lng p="bms_auth_no_received"}</b></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_reversedns"}?</td>
				<td class="td2">
					<input type="checkbox" name="smtp_reversedns"{if $bms_prefs.smtp_reversedns==1} checked="checked"{/if} id="smtp_reversedns" />
					<label for="smtp_reversedns"><b>{lng p="activate"}</b></label><br />
					<input type="checkbox" name="smtp_reject_noreversedns"{if $bms_prefs.smtp_reject_noreversedns==1} checked="checked"{/if} id="smtp_reject_noreversedns" />
					<label for="smtp_reject_noreversedns"><b>{lng p="bms_reject_norevdns"}</b></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_helo_check"}:</td>
				<td class="td2">
					<select name="smtp_check_helo">
						<option value="0"{if $bms_prefs.smtp_check_helo==0} selected="selected"{/if}>{lng p="bms_helo_disabled"}</option>
						<option value="1"{if $bms_prefs.smtp_check_helo==1} selected="selected"{/if}>{lng p="bms_helo_exact"}</option>
						<option value="2"{if $bms_prefs.smtp_check_helo==2} selected="selected"{/if}>{lng p="bms_helo_fuzzy"}</option>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="bms_peer_classification"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_classification.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_subnet_rules"}:</td>
				<td class="td2">{$subnetCount} {lng p="entries"} <input class="button" type="button" value=" {lng p="edit"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=smtp&do=subnetRules';" /></td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_dnsbl_rules"}:</td>
				<td class="td2">{$dnsblCount} {lng p="entries"} <input class="button" type="button" value=" {lng p="edit"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=smtp&do=dnsblRules';" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="bms_untrusted_limits"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="4" valign="top" width="40"><img src="../plugins/templates/images/bms_untrusted.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_greetingdelay"}:</td>
				<td class="td2"><input type="text" name="smtp_greeting_delay" value="{$bms_prefs.smtp_greeting_delay}" size="6" />
								{lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_error_delay"}:</td>
				<td class="td2"><input type="text" name="smtp_error_delay" value="{$bms_prefs.smtp_error_delay}" size="6" />
								{lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_error_softlimit"}:</td>
				<td class="td2"><input type="text" name="smtp_error_softlimit" value="{$bms_prefs.smtp_error_softlimit}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_error_hardlimit"}:</td>
				<td class="td2"><input type="text" name="smtp_error_hardlimit" value="{$bms_prefs.smtp_error_hardlimit}" size="6" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="bms_greylisting"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="5" valign="top" width="40"><img src="../plugins/templates/images/bms_greylisting.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="activate"}?</td>
				<td class="td2"><input type="checkbox" name="grey_enabled"{if $bms_prefs.grey_enabled==1} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_grey_interval"}:</td>
				<td class="td2"><input type="text" name="grey_interval" value="{$bms_prefs.grey_interval}" size="6" />
								{lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_grey_wait_time"}:</td>
				<td class="td2"><input type="text" name="grey_wait_time" value="{$bms_prefs.grey_wait_time/3600}" size="6" />
								{lng p="bms_hours"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_grey_good_time"}:</td>
				<td class="td2"><input type="text" name="grey_good_time" value="{$bms_prefs.grey_good_time/3600}" size="6" />
								{lng p="bms_hours"}</td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_list"}:</td>
				<td class="td2">{$greyCount} {lng p="entries"} <input class="button" type="button"{if $greyCount==0} disabled="disabled"{/if} value=" {lng p="show"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=smtp&do=greylist';" /> <input{if $greyCount==0} disabled="disabled"{/if} class="button" type="button" value=" {lng p="reset"} " onclick="document.location.href='{$pageURL}&action=smtp&resetGreyList=true&sid={$sid}';" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="bms_spf"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="4" valign="top" width="40"><img src="../plugins/templates/images/bms_spf.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="activate"}?</td>
				<td class="td2"><input type="checkbox" name="spf_enable"{if $bms_prefs.spf_enable==1} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_spf_injectheader"}?</td>
				<td class="td2"><input type="checkbox" name="spf_inject_header"{if $bms_prefs.spf_inject_header==1} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_spf_onpass"}:</td>
				<td class="td2">
					<input type="checkbox" id="spf_disable_greylisting" name="spf_disable_greylisting"{if $bms_prefs.spf_disable_greylisting==1} checked="checked"{/if} />
					<label for="spf_disable_greylisting"><strong>{lng p="bms_spf_disgrey"}</strong></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_spf_onfail"}:</td>
				<td class="td2">
					<input type="checkbox" id="spf_reject_mails" name="spf_reject_mails"{if $bms_prefs.spf_reject_mails==1} checked="checked"{/if} />
					<label for="spf_reject_mails"><strong>{lng p="bms_spf_reject"}</strong></label>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="bms_advanced"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="1" valign="top" width="40"><img src="../plugins/templates/images/bms_common.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_milters"}:</td>
				<td class="td2">
					<input class="button" type="button" value=" {lng p="edit"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=smtp&do=milters';" />
				</td>
			</tr>
		</table>
	</fieldset>

	<p>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
