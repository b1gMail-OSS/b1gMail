<form action="prefs.common.php?action=signup&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="common"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="10"><img src="{$tpldir}images/ico_prefs_signup.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="enablereg"}?</td>
				<td class="td2"><input name="regenabled"{if $bm_prefs.regenabled=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="stateafterreg"}:</td>
				<td class="td2"><select name="usr_status">
					<option value="no"{if $bm_prefs.usr_status=='no'} selected="selected"{/if}>{lng p="active"}</option>
					<option value="locked"{if $bm_prefs.usr_status=='locked'} selected="selected"{/if}>{lng p="notactivated"}</option>
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="stdgroup"}:</td>
				<td class="td2"><select name="std_gruppe">
				{foreach from=$groups item=group}
					<option value="{$group.id}"{if $bm_prefs.std_gruppe==$group.id} selected="selected"{/if}>{text value=$group.title}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="usercountlimit"}:</td>
				<td class="td2"><input type="checkbox" name="user_count_limit_enable" id="user_count_limit_enable" onclick="if(!this.checked)EBID('user_count_limit').value='0';"{if $bm_prefs.user_count_limit>0} checked="checked"{/if} />
								<input type="text" name="user_count_limit" id="user_count_limit" value="{$bm_prefs.user_count_limit}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="minaddrlength"}:</td>
				<td class="td2"><input type="text" name="minuserlength" value="{$bm_prefs.minuserlength}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="minpasslength"}:</td>
				<td class="td2"><input type="text" name="min_pass_length" value="{$bm_prefs.min_pass_length}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="signupsuggestions"}?</td>
				<td class="td2"><input id="signup_suggestions" name="signup_suggestions"{if $bm_prefs.signup_suggestions=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="regnotify"}?</td>
				<td class="td2"><input id="notify_mail" name="notify_mail"{if $bm_prefs.notify_mail=='yes'} checked="checked"{/if} type="checkbox" /><label for="notify_mail"> {lng p="to2"}: </label><input type="text" name="notify_to" value="{email value=$bm_prefs.notify_to}" size="24" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="sendwelcomemail"}?</td>
				<td class="td2"><input id="welcome_mail" name="welcome_mail"{if $bm_prefs.welcome_mail=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="nosignupautodel"}?</td>
				<td class="td2"><input id="nosignup_autodel" name="nosignup_autodel"{if $bm_prefs.nosignup_autodel=='yes'} checked="checked"{/if} type="checkbox" />
									{lng p="after"}
									<input type="text" name="nosignup_autodel_days" value="{$bm_prefs.nosignup_autodel_days}" size="6" />
									{lng p="days2"}</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="reg_validation"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="3"><img src="{$tpldir}images/user_active32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="reg_validation"}?</td>
				<td class="td2"><select name="reg_validation">
					<option value="off"{if $bm_prefs.reg_validation=='off'} selected="selected" {/if}>{lng p="no"}</option>
					<option value="email"{if $bm_prefs.reg_validation=='email'} selected="selected" {/if}>{lng p="byemail"}</option>
					<option value="sms"{if $bm_prefs.reg_validation=='sms'} selected="selected" {/if}>{lng p="bysms"}</option>
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="max_resend_times"}:</td>
				<td class="td2">
					<input type="text" name="reg_validation_max_resend_times" value="{$bm_prefs.reg_validation_max_resend_times}" size="6" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="min_resend_interval"}:</td>
				<td class="td2">
					<input type="text" name="reg_validation_min_resend_interval" value="{$bm_prefs.reg_validation_min_resend_interval}" size="6" />
					{lng p="seconds"}
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="fields"}</legend>

		<table class="list">
			<tr>
				<th width="20">&nbsp;</th>
				<th>{lng p="field"}</th>
				<th width="110">{lng p="oblig"}</th>
				<th width="110">{lng p="available"}</th>
				<th width="110">{lng p="notavailable"}</th>
			</tr>

			<tr class="td1">
				<td><img src="{$tpldir}images/field.png" border="0" alt="" width="16" height="16" /></td>
				<td>{lng p="salutation"}</td>
				<td style="text-align:center;"><input type="radio" name="f_anrede" value="p"{if $bm_prefs.f_anrede=='p'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="f_anrede" value="v"{if $bm_prefs.f_anrede=='v'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="f_anrede" value="n"{if $bm_prefs.f_anrede=='n'} checked="checked"{/if} /></td>
			</tr>
			<tr class="td2">
				<td><img src="{$tpldir}images/field.png" border="0" alt="" width="16" height="16" /></td>
				<td>{lng p="address"}</td>
				<td style="text-align:center;"><input type="radio" name="f_strasse" value="p"{if $bm_prefs.f_strasse=='p'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="f_strasse" value="v"{if $bm_prefs.f_strasse=='v'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="f_strasse" value="n"{if $bm_prefs.f_strasse=='n'} checked="checked"{/if} /></td>
			</tr>
			<tr class="td1">
				<td><img src="{$tpldir}images/field.png" border="0" alt="" width="16" height="16" /></td>
				<td>{lng p="tel"}</td>
				<td style="text-align:center;"><input type="radio" name="f_telefon" value="p"{if $bm_prefs.f_telefon=='p'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="f_telefon" value="v"{if $bm_prefs.f_telefon=='v'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="f_telefon" value="n"{if $bm_prefs.f_telefon=='n'} checked="checked"{/if} /></td>
			</tr>
			<tr class="td2">
				<td><img src="{$tpldir}images/field.png" border="0" alt="" width="16" height="16" /></td>
				<td>{lng p="fax"}</td>
				<td style="text-align:center;"><input type="radio" name="f_fax" value="p"{if $bm_prefs.f_fax=='p'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="f_fax" value="v"{if $bm_prefs.f_fax=='v'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="f_fax" value="n"{if $bm_prefs.f_fax=='n'} checked="checked"{/if} /></td>
			</tr>
			<tr class="td1">
				<td><img src="{$tpldir}images/field.png" border="0" alt="" width="16" height="16" /></td>
				<td>{lng p="altmail"}</td>
				<td style="text-align:center;"><input type="radio" name="f_alternativ" value="p"{if $bm_prefs.f_alternativ=='p'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="f_alternativ" value="v"{if $bm_prefs.f_alternativ=='v'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="f_alternativ" value="n"{if $bm_prefs.f_alternativ=='n'} checked="checked"{/if} /></td>
			</tr>
			<tr class="td2">
				<td><img src="{$tpldir}images/field.png" border="0" alt="" width="16" height="16" /></td>
				<td>{lng p="cellphone"}</td>
				<td style="text-align:center;"><input type="radio" name="f_mail2sms_nummer" value="p"{if $bm_prefs.f_mail2sms_nummer=='p'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="f_mail2sms_nummer" value="v"{if $bm_prefs.f_mail2sms_nummer=='v'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="f_mail2sms_nummer" value="n"{if $bm_prefs.f_mail2sms_nummer=='n'} checked="checked"{/if} /></td>
			</tr>
			<tr class="td1">
				<td><img src="{$tpldir}images/field.png" border="0" alt="" width="16" height="16" /></td>
				<td>{lng p="safecode"}</td>
				<td style="text-align:center;"><input type="radio" name="f_safecode" value="p"{if $bm_prefs.f_safecode=='p'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" disabled="disabled" /></td>
				<td style="text-align:center;"><input type="radio" name="f_safecode" value="n"{if $bm_prefs.f_safecode=='n'} checked="checked"{/if} /></td>
			</tr>
		</table>

		<p align="center">
			{lng p="customfieldsat"} <a href="prefs.profilefields.php?sid={$sid}">&raquo; {lng p="profilefields"}</a>.
		</p>
	</fieldset>

	<fieldset>
		<legend>{lng p="datavalidation"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/ico_prefs_validation.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="regiplock"}:</td>
				<td class="td2"><input type="text" name="reg_iplock" value="{$bm_prefs.reg_iplock}" size="6" /> {lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="plzcheck"}?</td>
				<td class="td2"><input name="plz_check"{if $bm_prefs.plz_check=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="altcheck"}?</td>
				<td class="td2"><input name="alt_check"{if $bm_prefs.alt_check=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="lockedaltmails"}:</td>
				<td class="td2">
					<textarea style="width:100%;height:80px;" name="locked_altmail">{text value=$bm_prefs.locked_altmail allowEmpty=true}</textarea>
					<small>{lng p="altmailsepby"}</small>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="check_double_altmail"}?</td>
				<td class="td2"><input name="check_double_altmail"{if $bm_prefs.check_double_altmail=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="check_double_cellphone"}?</td>
				<td class="td2"><input name="check_double_cellphone"{if $bm_prefs.check_double_cellphone=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="signupdnsbl"}</legend>

		<table width="90%">
			<tr>
				<td align="left" rowspan="3" valign="top" width="40"><img src="{$tpldir}images/antispam_dnsbl.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="enable"}?</td>
				<td class="td2"><input name="signup_dnsbl_enable"{if $bm_prefs.signup_dnsbl_enable=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="dnsblservers"}:</td>
				<td class="td2">
					<textarea style="width:100%;height:80px;" name="signup_dnsbl">{text value=$bm_prefs.signup_dnsbl allowEmpty=true}</textarea>
					<small>{lng p="sepby"}</small>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="action"}:</td>
				<td class="td2"><select name="signup_dnsbl_action">
					<option value="block"{if $bm_prefs.signup_dnsbl_action=='block'} selected="selected"{/if}>{lng p="blocksignup"}</option>
					<option value="lock"{if $bm_prefs.signup_dnsbl_action=='lock'} selected="selected"{/if}>{lng p="activatemanually"}</option>
				</select></td>
			</tr>
		</table>
	</fieldset>

	<p>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
