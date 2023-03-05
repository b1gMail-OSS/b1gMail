<form action="{$pageURL}&sid={$sid}&action=smtp&save=true" method="post" onsubmit="spin(this)">
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="common"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_greeting"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="smtpgreeting" value="{if isset($bms_prefs.smtpgreeting)}{text value=$bms_prefs.smtpgreeting allowEmpty=true}{/if}" placeholder="{lng p="bms_greeting"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_timeout"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="smtp_timeout" value="{$bms_prefs.smtp_timeout}" placeholder="{lng p="bms_timeout"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="mailmax"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="smtp_size_limit" value="{$bms_prefs.smtp_size_limit/1024}" placeholder="{lng p="mailmax"}">
							<span class="input-group-text">KB</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_recipient_limit"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="smtp_recipient_limit" value="{$bms_prefs.smtp_recipient_limit}" placeholder="{lng p="bms_recipient_limit"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_hop_limit"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="smtp_hop_limit" value="{$bms_prefs.smtp_hop_limit}" placeholder="{lng p="bms_hop_limit"}">
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_smtp_auth"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="smtp_auth_enabled"{if $bms_prefs.smtp_auth_enabled==1} checked="checked"{/if} id="smtp_auth_enabled">
							<span class="form-check-label">{lng p="activate"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="smtp_auth_no_received"{if $bms_prefs.smtp_auth_no_received==1} checked="checked"{/if} id="smtp_auth_no_received">
							<span class="form-check-label">{lng p="bms_auth_no_received"}</span>
						</label>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_reversedns"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="smtp_reversedns"{if $bms_prefs.smtp_reversedns==1} checked="checked"{/if} id="smtp_reversedns">
							<span class="form-check-label">{lng p="activate"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="smtp_reject_noreversedns"{if $bms_prefs.smtp_reject_noreversedns==1} checked="checked"{/if} id="smtp_reject_noreversedns">
							<span class="form-check-label">{lng p="bms_reject_norevdns"}</span>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_helo_check"}</label>
					<div class="col-sm-8">
						<select name="smtp_check_helo" class="form-select">
							<option value="0"{if $bms_prefs.smtp_check_helo==0} selected="selected"{/if}>{lng p="bms_helo_disabled"}</option>
							<option value="1"{if $bms_prefs.smtp_check_helo==1} selected="selected"{/if}>{lng p="bms_helo_exact"}</option>
							<option value="2"{if $bms_prefs.smtp_check_helo==2} selected="selected"{/if}>{lng p="bms_helo_fuzzy"}</option>
						</select>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="bms_peer_classification"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_subnet_rules"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<span class="input-group-text">{$subnetCount} {lng p="entries"}</span>
							<input class="btn btn-primary" type="button" value=" {lng p="edit"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=smtp&do=subnetRules';" />
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_dnsbl_rules"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<span class="input-group-text">{$dnsblCount} {lng p="entries"}</span>
							<input class="btn btn-primary" type="button" value=" {lng p="edit"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=smtp&do=dnsblRules';" />
						</div>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="bms_untrusted_limits"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_greetingdelay"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="smtp_greeting_delay" value="{$bms_prefs.smtp_greeting_delay}" placeholder="{lng p="bms_greetingdelay"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_error_delay"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="smtp_error_delay" value="{$bms_prefs.smtp_error_delay}" placeholder="{lng p="bms_error_delay"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_error_softlimit"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="smtp_error_softlimit" value="{$bms_prefs.smtp_error_softlimit}" placeholder="{lng p="bms_error_softlimit"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_error_hardlimit"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="smtp_error_hardlimit" value="{$bms_prefs.smtp_error_hardlimit}" placeholder="{lng p="bms_error_hardlimit"}">
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="bms_greylisting"}</legend>

				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="activate"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="grey_enabled"{if $bms_prefs.grey_enabled==1} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_grey_interval"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="grey_interval" value="{$bms_prefs.grey_interval}" placeholder="{lng p="bms_grey_interval"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_grey_wait_time"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="grey_wait_time" value="{$bms_prefs.grey_wait_time/3600}" placeholder="{lng p="bms_grey_wait_time"}">
							<span class="input-group-text">{lng p="bms_hours"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_grey_good_time"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="grey_good_time" value="{$bms_prefs.grey_good_time/3600}" placeholder="{lng p="bms_grey_good_time"}">
							<span class="input-group-text">{lng p="bms_hours"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_list"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<span class="input-group-text">{$greyCount} {lng p="entries"}</span>
							<input class="btn btn-primary" type="button"{if $greyCount==0} disabled="disabled"{/if} value=" {lng p="show"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=smtp&do=greylist';" />
							<input{if $greyCount==0} disabled="disabled"{/if} class="btn btn-primary" type="button" value=" {lng p="reset"} " onclick="document.location.href='{$pageURL}&action=smtp&resetGreyList=true&sid={$sid}';" />
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="bms_spf"}</legend>

				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="activate"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="spf_enable"{if $bms_prefs.spf_enable==1} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_spf_injectheader"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="spf_inject_header"{if $bms_prefs.spf_inject_header==1} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_spf_onpass"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" id="spf_disable_greylisting" name="spf_disable_greylisting"{if $bms_prefs.spf_disable_greylisting==1} checked="checked"{/if}>
							<span class="form-check-label">{lng p="bms_spf_disgrey"}</span>
						</label>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_spf_onfail"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" id="spf_reject_mails" name="spf_reject_mails"{if $bms_prefs.spf_reject_mails==1} checked="checked"{/if}>
							<span class="form-check-label">{lng p="bms_spf_reject"}</span>
						</label>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="bms_advanced"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_milters"}</label>
					<div class="col-sm-8">
						<input class="btn btn-primary" type="button" value=" {lng p="edit"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=smtp&do=milters';" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value=" {lng p="save"} " />
	</div>
</form>
