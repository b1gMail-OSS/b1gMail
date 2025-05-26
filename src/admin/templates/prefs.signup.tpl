<form action="prefs.common.php?action=signup&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="common"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="enablereg"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="regenabled"{if $bm_prefs.regenabled=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="stateafterreg"}</label>
					<div class="col-sm-8">
						<select name="usr_status" class="form-select">
							<option value="no"{if $bm_prefs.usr_status=='no'} selected="selected"{/if}>{lng p="active"}</option>
							<option value="locked"{if $bm_prefs.usr_status=='locked'} selected="selected"{/if}>{lng p="notactivated"}</option>
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="stdgroup"}</label>
					<div class="col-sm-8">
						<select name="std_gruppe" class="form-select">
							{foreach from=$groups item=group}
								<option value="{$group.id}"{if $bm_prefs.std_gruppe==$group.id} selected="selected"{/if}>{text value=$group.title}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="usercountlimit"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
                        <span class="input-group-text">
                        	<input class="form-check-input m-0" type="checkbox" name="user_count_limit_enable" onclick="if(!this.checked)EBID('user_count_limit').value='0';"{if $bm_prefs.user_count_limit>0} checked="checked"{/if}>
                        </span>
							<input type="text" class="form-control" name="user_count_limit" value="{$bm_prefs.user_count_limit}" placeholder="{lng p="usercountlimit"}">
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="minaddrlength"}</label>
					<div class="col-sm-8">
						<input type="number" class="form-control" name="minuserlength" value="{$bm_prefs.minuserlength}" placeholder="{lng p="minaddrlength"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="minpasslength"}</label>
					<div class="col-sm-8">
						<input type="number" class="form-control" name="min_pass_length" value="{$bm_prefs.min_pass_length}" placeholder="{lng p="minpasslength"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="signupsuggestions"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="signup_suggestions"{if $bm_prefs.signup_suggestions=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="regnotify"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
                        <span class="input-group-text">
                        	<input class="form-check-input m-0" type="checkbox" name="notify_mail"{if $bm_prefs.notify_mail=='yes'} checked="checked"{/if}>
                        </span>
							<span class="input-group-text">{lng p="to2"}:</span>
							<input type="text" class="form-control" name="notify_to" value="{email value=$bm_prefs.notify_to}" placeholder="{lng p="regnotify"}">
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="sendwelcomemail"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="welcome_mail"{if $bm_prefs.welcome_mail=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="nosignupautodel"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
                        <span class="input-group-text">
                        	<input class="form-check-input m-0" type="checkbox" name="nosignup_autodel"{if $bm_prefs.nosignup_autodel=='yes'} checked="checked"{/if}>
                        </span>
							<span class="input-group-text">{lng p="after"}</span>
							<input type="number" class="form-control" name="nosignup_autodel_days" value="{$bm_prefs.nosignup_autodel_days}" placeholder="{lng p="nosignupautodel"}">
							<span class="input-group-text">{lng p="days2"}</span>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="reg_validation"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="reg_validation"}</label>
					<div class="col-sm-8">
						<select name="reg_validation" class="form-select">
							<option value="off"{if $bm_prefs.reg_validation=='off'} selected="selected" {/if}>{lng p="no"}</option>
							<option value="email"{if $bm_prefs.reg_validation=='email'} selected="selected" {/if}>{lng p="byemail"}</option>
							<option value="sms"{if $bm_prefs.reg_validation=='sms'} selected="selected" {/if}>{lng p="bysms"}</option>
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="max_resend_times"}</label>
					<div class="col-sm-8">
						<input type="number" class="form-control" name="reg_validation_max_resend_times" value="{$bm_prefs.reg_validation_max_resend_times}" placeholder="{lng p="max_resend_times"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="min_resend_interval"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="number" class="form-control" name="reg_validation_min_resend_interval" value="{$bm_prefs.reg_validation_min_resend_interval}" placeholder="{lng p="min_resend_interval"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<fieldset>
		<legend>{lng p="fields"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th>{lng p="field"}</th>
						<th style="width: 110px;">{lng p="oblig"}</th>
						<th style="width: 110px;">{lng p="available"}</th>
						<th style="width: 110px;">{lng p="notavailable"}</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>{lng p="salutation"}</td>
						<td style="text-align:center;"><input type="radio" name="f_anrede" value="p"{if $bm_prefs.f_anrede=='p'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" name="f_anrede" value="v"{if $bm_prefs.f_anrede=='v'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" name="f_anrede" value="n"{if $bm_prefs.f_anrede=='n'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td>{lng p="address"}</td>
						<td style="text-align:center;"><input type="radio" name="f_strasse" value="p"{if $bm_prefs.f_strasse=='p'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" name="f_strasse" value="v"{if $bm_prefs.f_strasse=='v'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" name="f_strasse" value="n"{if $bm_prefs.f_strasse=='n'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td>{lng p="tel"}</td>
						<td style="text-align:center;"><input type="radio" name="f_telefon" value="p"{if $bm_prefs.f_telefon=='p'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" name="f_telefon" value="v"{if $bm_prefs.f_telefon=='v'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" name="f_telefon" value="n"{if $bm_prefs.f_telefon=='n'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td>{lng p="fax"}</td>
						<td style="text-align:center;"><input type="radio" name="f_fax" value="p"{if $bm_prefs.f_fax=='p'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" name="f_fax" value="v"{if $bm_prefs.f_fax=='v'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" name="f_fax" value="n"{if $bm_prefs.f_fax=='n'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td>{lng p="altmail"}</td>
						<td style="text-align:center;"><input type="radio" name="f_alternativ" value="p"{if $bm_prefs.f_alternativ=='p'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" name="f_alternativ" value="v"{if $bm_prefs.f_alternativ=='v'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" name="f_alternativ" value="n"{if $bm_prefs.f_alternativ=='n'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td>{lng p="cellphone"}</td>
						<td style="text-align:center;"><input type="radio" name="f_mail2sms_nummer" value="p"{if $bm_prefs.f_mail2sms_nummer=='p'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" name="f_mail2sms_nummer" value="v"{if $bm_prefs.f_mail2sms_nummer=='v'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" name="f_mail2sms_nummer" value="n"{if $bm_prefs.f_mail2sms_nummer=='n'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td>{lng p="safecode"}</td>
						<td style="text-align:center;"><input type="radio" name="f_safecode" value="p"{if $bm_prefs.f_safecode=='p'} checked="checked"{/if} /></td>
						<td style="text-align:center;"><input type="radio" disabled="disabled" /></td>
						<td style="text-align:center;"><input type="radio" name="f_safecode" value="n"{if $bm_prefs.f_safecode=='n'} checked="checked"{/if} /></td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="card-footer text-end">
				{lng p="customfieldsat"} <a href="prefs.profilefields.php?sid={$sid}">&raquo; {lng p="profilefields"}</a>.
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="datavalidation"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="regiplock"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="number" class="form-control" name="reg_iplock" value="{$bm_prefs.reg_iplock}" placeholder="{lng p="regiplock"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="plzcheck"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="plz_check"{if $bm_prefs.plz_check=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="altcheck"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="alt_check"{if $bm_prefs.alt_check=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="lockedaltmails"}</label>
					<div class="col-sm-8">
						<textarea class="form-control" name="locked_altmail" placeholder="{lng p="lockedaltmails"}">{text value=$bm_prefs.locked_altmail allowEmpty=true}</textarea>
						<small>{lng p="altmailsepby"}</small>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="check_double_altmail"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="check_double_altmail"{if $bm_prefs.check_double_altmail=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="check_double_cellphone"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="check_double_cellphone"{if $bm_prefs.check_double_cellphone=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="signupdnsbl"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="enable"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="signup_dnsbl_enable"{if $bm_prefs.signup_dnsbl_enable=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="dnsblservers"}</label>
					<div class="col-sm-8">
						<textarea class="form-control" name="signup_dnsbl" placeholder="{lng p="dnsblservers"}">{text value=$bm_prefs.signup_dnsbl allowEmpty=true}</textarea>
						<small>{lng p="sepby"}</small>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="action"}</label>
					<div class="col-sm-8">
						<select name="signup_dnsbl_action" class="form-select">
							<option value="block"{if $bm_prefs.signup_dnsbl_action=='block'} selected="selected"{/if}>{lng p="blocksignup"}</option>
							<option value="lock"{if $bm_prefs.signup_dnsbl_action=='lock'} selected="selected"{/if}>{lng p="activatemanually"}</option>
						</select>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>