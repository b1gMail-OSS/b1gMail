<form action="prefs.email.php?action=send&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="sendmethod"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="sendmethod"}</label>
			<div class="col-sm-10">
				<select name="send_method" class="form-control">
					<option value="smtp"{if $bm_prefs.send_method=='smtp'} selected="selected"{/if}>{lng p="smtp"}</option>
					<option value="php"{if $bm_prefs.send_method=='php'} selected="selected"{/if}>{lng p="phpmail"}</option>
					<option value="sendmail"{if $bm_prefs.send_method=='sendmail'} selected="selected"{/if}>{lng p="sendmail2"}</option>
				</select>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="sendmail2"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="sendmailpath"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="sendmail_path" value="{text allowEmpty=true value=$bm_prefs.sendmail_path}" placeholder="{lng p="sendmailpath"}">
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="smtp"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="smtphost"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="smtp_host" value="{text allowEmpty=true value=$bm_prefs.smtp_host}" placeholder="{lng p="smtphost"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="smtpport"}</label>
					<div class="col-sm-8">
						<input type="number" class="form-control" name="smtp_port" value="{$bm_prefs.smtp_port}" placeholder="{lng p="smtpport"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="smtpauth"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="smtp_auth"{if $bm_prefs.smtp_auth=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="smtpuser"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="smtp_user" value="{text allowEmpty=true value=$bm_prefs.smtp_user}" placeholder="{lng p="smtpuser"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="smtppass"}</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" name="smtp_pass" value="{text allowEmpty=true value=$bm_prefs.smtp_pass}" placeholder="{lng p="smtppass"}">
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="miscprefs"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="sysmailsender"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="passmail_abs" value="{text allowEmpty=true value=$bm_prefs.passmail_abs}" placeholder="{lng p="sysmailsender"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="blockedrecps"}</label>
					<div class="col-sm-8">
						<textarea class="form-control" name="blocked" placeholder="{lng p="blockedrecps"}">{text value=$bm_prefs.blocked allowEmpty=true}</textarea>
						<small>{lng p="altmailsepby"}</small>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="certmaillife"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="number" class="form-control" name="einsch_life" value="{$bm_prefs.einsch_life/86400}" placeholder="{lng p="certmaillife"}">
							<span class="input-group-text">{lng p="days"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="min_draft_save"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="number" class="form-control" name="min_draft_save_interval" value="{$bm_prefs.min_draft_save_interval}" placeholder="{lng p="min_draft_save"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="write_xsenderip"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="write_xsenderip"{if $bm_prefs.write_xsenderip=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />&nbsp;
	</div>
</form>
