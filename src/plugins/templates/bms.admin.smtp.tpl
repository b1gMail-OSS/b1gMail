<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=smtp&save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="common"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="8" valign="top" width="40"><img src="../plugins/templates/images/bms_common.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_greeting"}:</td>
				<td class="td2"><input class="form-control" type="text" name="smtpgreeting" value="{if isset($bms_prefs.smtpgreeting)}{text value=$bms_prefs.smtpgreeting allowEmpty=true}{/if}" size="32" style="width:95%;" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_timeout"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="smtp_timeout" value="{$bms_prefs.smtp_timeout}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="seconds"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="mailmax"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="smtp_size_limit" value="{$bms_prefs.smtp_size_limit/1024}" style="max-width:6rem;" />
						<span class="input-group-text">KB</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_recipient_limit"}:</td>
				<td class="td2"><input class="form-control" type="text" name="smtp_recipient_limit" value="{$bms_prefs.smtp_recipient_limit}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_hop_limit"}:</td>
				<td class="td2"><input class="form-control" type="text" name="smtp_hop_limit" value="{$bms_prefs.smtp_hop_limit}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_smtp_auth"}?</td>
				<td class="td2"><label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="smtp_auth_enabled"{if $bms_prefs.smtp_auth_enabled==1} checked="checked"{/if} id="smtp_auth_enabled" /><span class="form-check-label" for="smtp_auth_enabled"><b>{lng p="activate"}</b></span></label><br />
					<label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="smtp_auth_no_received"{if $bms_prefs.smtp_auth_no_received==1} checked="checked"{/if} id="smtp_auth_no_received" /><span class="form-check-label" for="smtp_auth_no_received"><b>{lng p="bms_auth_no_received"}</b></span></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_reversedns"}?</td>
				<td class="td2">
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="smtp_reversedns"{if $bms_prefs.smtp_reversedns==1} checked="checked"{/if} id="smtp_reversedns" /><span class="form-check-label" for="smtp_reversedns"><b>{lng p="activate"}</b></span></label><br />
					<label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="smtp_reject_noreversedns"{if $bms_prefs.smtp_reject_noreversedns==1} checked="checked"{/if} id="smtp_reject_noreversedns" /><span class="form-check-label" for="smtp_reject_noreversedns"><b>{lng p="bms_reject_norevdns"}</b></span></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_helo_check"}:</td>
				<td class="td2">
					<select class="form-select" name="smtp_check_helo">
						<option value="0"{if $bms_prefs.smtp_check_helo==0} selected="selected"{/if}>{lng p="bms_helo_disabled"}</option>
						<option value="1"{if $bms_prefs.smtp_check_helo==1} selected="selected"{/if}>{lng p="bms_helo_exact"}</option>
						<option value="2"{if $bms_prefs.smtp_check_helo==2} selected="selected"{/if}>{lng p="bms_helo_fuzzy"}</option>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_peer_classification"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_classification.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_subnet_rules"}:</td>
				<td class="td2">{$subnetCount} {lng p="entries"} <button type="button" class="btn btn-outline-secondary btn-sm"  onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=smtp&action=subnetRules"}';" >{lng p="edit"}</button></td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_dnsbl_rules"}:</td>
				<td class="td2">{$dnsblCount} {lng p="entries"} <button type="button" class="btn btn-outline-secondary btn-sm"  onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=smtp&action=dnsblRules"}';" >{lng p="edit"}</button></td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_untrusted_limits"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="4" valign="top" width="40"><img src="../plugins/templates/images/bms_untrusted.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_greetingdelay"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="smtp_greeting_delay" value="{$bms_prefs.smtp_greeting_delay}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="seconds"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_error_delay"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="smtp_error_delay" value="{$bms_prefs.smtp_error_delay}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="seconds"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_error_softlimit"}:</td>
				<td class="td2"><input class="form-control" type="text" name="smtp_error_softlimit" value="{$bms_prefs.smtp_error_softlimit}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_error_hardlimit"}:</td>
				<td class="td2"><input class="form-control" type="text" name="smtp_error_hardlimit" value="{$bms_prefs.smtp_error_hardlimit}" size="6" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_greylisting"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="5" valign="top" width="40"><img src="../plugins/templates/images/bms_greylisting.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="activate"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="grey_enabled"{if $bms_prefs.grey_enabled==1} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_grey_interval"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="grey_interval" value="{$bms_prefs.grey_interval}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="seconds"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_grey_wait_time"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="grey_wait_time" value="{$bms_prefs.grey_wait_time/3600}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="bms_hours"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_grey_good_time"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="grey_good_time" value="{$bms_prefs.grey_good_time/3600}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="bms_hours"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_list"}:</td>
				<td class="td2">
					{$greyCount} {lng p="entries"}
					<button type="button" class="btn btn-outline-secondary btn-sm"{if $greyCount==0} disabled="disabled"{/if} onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=smtp&action=greylist"}';">{lng p="show"}</button>
					{if $greyCount>0}
					<button type="submit" class="btn btn-outline-secondary btn-sm" form="bmsResetGreyForm">{lng p="reset"}</button>
					{else}
					<button type="button" class="btn btn-outline-secondary btn-sm" disabled="disabled">{lng p="reset"}</button>
					{/if}
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_spf"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="4" valign="top" width="40"><img src="../plugins/templates/images/bms_spf.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="activate"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="spf_enable"{if $bms_prefs.spf_enable==1} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_spf_injectheader"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="spf_inject_header"{if $bms_prefs.spf_inject_header==1} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_spf_onpass"}:</td>
				<td class="td2">
					<label class="form-check mb-0"><input class="form-check-input" type="checkbox" id="spf_disable_greylisting" name="spf_disable_greylisting"{if $bms_prefs.spf_disable_greylisting==1} checked="checked"{/if} /><span class="form-check-label" for="spf_disable_greylisting"><strong>{lng p="bms_spf_disgrey"}</strong></span></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_spf_onfail"}:</td>
				<td class="td2">
					<label class="form-check mb-0"><input class="form-check-input" type="checkbox" id="spf_reject_mails" name="spf_reject_mails"{if $bms_prefs.spf_reject_mails==1} checked="checked"{/if} /><span class="form-check-label" for="spf_reject_mails"><strong>{lng p="bms_spf_reject"}</strong></span></label>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_advanced"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="1" valign="top" width="40"><img src="../plugins/templates/images/bms_common.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_milters"}:</td>
				<td class="td2">
					<button type="button" class="btn btn-outline-secondary btn-sm"  onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=smtp&action=milters"}';" >{lng p="edit"}</button>
				</td>
			</tr>
		</table>
	</fieldset>
	{include file=$bmsSaveFooterTpl}
</form>

<form id="bmsResetGreyForm" method="post" action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=smtp"}" style="display:none;" aria-hidden="true">
	{csrffield}
	<input type="hidden" name="resetGreyList" value="1" />
</form>
