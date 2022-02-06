<form action="prefs.email.php?action=send&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="sendmethod"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="1"><img src="{$tpldir}images/ico_prefs_sending.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="sendmethod"}:</td>
				<td class="td2"><select name="send_method">
					<option value="smtp"{if $bm_prefs.send_method=='smtp'} selected="selected"{/if}>{lng p="smtp"}</option>
					<option value="php"{if $bm_prefs.send_method=='php'} selected="selected"{/if}>{lng p="phpmail"}</option>
					<option value="sendmail"{if $bm_prefs.send_method=='sendmail'} selected="selected"{/if}>{lng p="sendmail2"}</option>
				</select></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="smtp"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/ico_prefs_login.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="smtphost"}:</td>
				<td class="td2"><input type="text" name="smtp_host" value="{text allowEmpty=true value=$bm_prefs.smtp_host}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smtpport"}:</td>
				<td class="td2"><input type="text" name="smtp_port" value="{$bm_prefs.smtp_port}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smtpauth"}?</td>
				<td class="td2"><input name="smtp_auth"{if $bm_prefs.smtp_auth=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smtpuser"}:</td>
				<td class="td2"><input type="text" name="smtp_user" value="{text allowEmpty=true value=$bm_prefs.smtp_user}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smtppass"}:</td>
				<td class="td2"><input type="password" autocomplete="off" name="smtp_pass" value="{text allowEmpty=true value=$bm_prefs.smtp_pass}" size="36" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="sendmail2"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="1"><img src="{$tpldir}images/ico_prefs_cmd.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="sendmailpath"}:</td>
				<td class="td2"><input type="text" name="sendmail_path" value="{text allowEmpty=true value=$bm_prefs.sendmail_path}" size="36" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="miscprefs"}</legend>

		<table width="90%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/ico_prefs_misc.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="sysmailsender"}:</td>
				<td class="td2"><input type="text" name="passmail_abs" value="{email value=$bm_prefs.passmail_abs}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="blockedrecps"}:</td>
				<td class="td2">
					<textarea style="width:100%;height:80px;" name="blocked">{text value=$bm_prefs.blocked allowEmpty=true}</textarea>
					<small>{lng p="altmailsepby"}</small>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="certmaillife"}:</td>
				<td class="td2"><input type="text" name="einsch_life" value="{$bm_prefs.einsch_life/86400}" size="4" />
								{lng p="days"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="min_draft_save"}:</td>
				<td class="td2"><input type="text" name="min_draft_save_interval" value="{$bm_prefs.min_draft_save_interval}" size="4" />
								{lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="write_xsenderip"}?</td>
				<td class="td2"><input name="write_xsenderip"{if $bm_prefs.write_xsenderip=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
		</table>
	</fieldset>

	<p>
		<div style="float:right;" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />&nbsp;
		</div>
	</p>
</form>
