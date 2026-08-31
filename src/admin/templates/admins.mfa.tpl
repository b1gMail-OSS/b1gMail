<fieldset>
	<legend>{lng p="mfa"}</legend>

	{if isset($mfaError)}
		<div class="alert alert-danger">{text value=$mfaError}</div>
	{/if}
	{if isset($mfaInfo)}
		<div class="alert alert-info">{text value=$mfaInfo}</div>
	{/if}

	{if isset($backupCodes)}
		<div class="alert alert-warning alert-dismissible" role="alert">
			<div class="alert-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2" aria-hidden="true">
					<path d="M12 9v4"></path>
					<path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
					<path d="M12 16h.01"></path>
				</svg>
			</div>
			<div>
				<h4 class="alert-heading">{lng p="mfa_backup_new"}</h4>
				<div class="alert-description">
					<p class="mb-2">{lng p="mfa_backup_hint"}</p>
					<ul class="list-unstyled font-monospace mb-0">
						{foreach from=$backupCodes item=code}
							<li>{$code}</li>
						{/foreach}
					</ul>
				</div>
			</div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{lng p="close"}"></button>
		</div>
	{/if}

	{if $mfaAccount && $mfaAccount.enabled == 'yes' && empty($mfaPrefsSwitch)}
		<p>{lng p="mfa_status_on"}</p>
		<p class="text-secondary">
			{if $mfaActiveMethod == 'email'}
				{lng p="mfa_method_email_active"}
			{else}
				{lng p="mfa_method_totp_active"}
			{/if}
			{if $mfaEnabledAtFormatted != ''}<br /><small>{lng p="mfa_active_since_label"} {$mfaEnabledAtFormatted}</small>{/if}
		</p>
		<form method="post" action="{sessionurl file='admins.php' params="action=account"}">
			{csrffield}
			<input type="hidden" name="do" value="mfaSave" />
			<div class="mb-3">
				<label class="form-label" for="mfa_manage_password">{lng p="password"}</label>
				<input type="password" class="form-control" name="password" id="mfa_manage_password" required="required" autocomplete="current-password" />
			</div>
			<div class="d-flex flex-wrap align-items-center gap-2">
				<button type="submit" class="btn btn-outline-warning btn-sm" name="mfa_action" value="reset_setup" onclick="return confirm('{lng p="mfa_reset_setup_confirm"}');">{lng p="mfa_reset_setup_btn"}</button>
				{if !$mfaMandatory}
					<button type="submit" class="btn btn-outline-danger btn-sm" name="mfa_action" value="disable">{lng p="mfa_disable"}</button>
				{/if}
				<button type="submit" class="btn btn-secondary btn-sm" name="mfa_action" value="regen_backup">{lng p="mfa_backup_regenerate"}</button>
				{if $mfaCanUseEmail && $mfaActiveMethod != 'email'}
					<button type="submit" class="btn btn-outline-primary btn-sm" name="mfa_action" value="switch_to_email">{lng p="mfa_use_email_method"}</button>
				{elseif $mfaActiveMethod == 'email'}
					<button type="submit" class="btn btn-outline-primary btn-sm" name="mfa_action" value="switch_to_totp">{lng p="mfa_use_totp_method"}</button>
				{/if}
			</div>
		</form>
	{elseif !empty($mfaPrefsSwitch)}
		<p class="text-secondary">{lng p="mfa_switch_in_progress"}</p>
		{include file="admins.mfa_method_switch.tpl"}

		{if $mfaSetupMethod == 'email'}
			<p>{lng p="mfa_setup_hint_email"}</p>
			{if $mfaAltMailMasked != ''}
				<p class="text-secondary small">{lng p="mfa_admin_email_target"}: <strong>{$mfaAltMailMasked}</strong></p>
			{/if}
			<form method="post" action="{sessionurl file='admins.php' params="action=account"}">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<input type="hidden" name="mfa_action" value="confirm_switch_email" />
				<div class="mb-3">
					<label class="form-label">{lng p="mfa_code"}</label>
					<input type="text" class="form-control" name="email_code" inputmode="numeric" maxlength="6" required="required" autocomplete="one-time-code" />
				</div>
				<div class="mb-3">
					<label class="form-label">{lng p="password"}</label>
					<input type="password" class="form-control" name="password" required="required" autocomplete="current-password" />
				</div>
				<button type="submit" class="btn btn-primary">{lng p="mfa_verify_submit"}</button>
			</form>
			<form method="post" action="{sessionurl file='admins.php' params="action=account"}" class="mt-2">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<button type="submit" class="btn btn-link btn-sm" name="mfa_action" value="resend_switch_email">{lng p="mfa_resend_code"}</button>
			</form>
		{else}
			<p>{lng p="mfa_setup_hint"}</p>
			{if $mfaQrSvg != ''}
				<div class="text-center my-3">{$mfaQrSvg nofilter}</div>
			{/if}
			<p class="small text-secondary">{lng p="mfa_secret_manual"}: <code>{$mfaSecretManual}</code></p>
			<form method="post" action="{sessionurl file='admins.php' params="action=account"}">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<input type="hidden" name="mfa_action" value="confirm_switch_totp" />
				<div class="mb-3">
					<label class="form-label">{lng p="mfa_code"}</label>
					<input type="text" class="form-control" name="totp_code" inputmode="numeric" maxlength="6" required="required" autocomplete="one-time-code" />
				</div>
				<div class="mb-3">
					<label class="form-label">{lng p="password"}</label>
					<input type="password" class="form-control" name="password" required="required" autocomplete="current-password" />
				</div>
				<button type="submit" class="btn btn-primary">{lng p="mfa_verify_submit"}</button>
			</form>
		{/if}
	{else}
		{if !$mfaCanUseEmail && $mfaSetupMethod == 'email'}
			<div class="alert alert-warning">{lng p="mfa_admin_email_required"}</div>
		{/if}
		{include file="admins.mfa_method_switch.tpl"}

		{if $mfaSetupMethod == 'email'}
			<p>{lng p="mfa_setup_hint_email"}</p>
			{if $mfaAltMailMasked != ''}
				<p class="text-secondary small">{lng p="mfa_admin_email_target"}: <strong>{$mfaAltMailMasked}</strong></p>
			{/if}
			<form method="post" action="{sessionurl file='admins.php' params="action=account"}">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<input type="hidden" name="mfa_action" value="enable_email" />
				<div class="mb-3">
					<label class="form-label">{lng p="mfa_code"}</label>
					<input type="text" class="form-control" name="email_code" inputmode="numeric" maxlength="6" required="required" autocomplete="one-time-code" />
				</div>
				<div class="mb-3">
					<label class="form-label">{lng p="password"}</label>
					<input type="password" class="form-control" name="password" required="required" autocomplete="current-password" />
				</div>
				<button type="submit" class="btn btn-primary">{lng p="mfa_enable_email"}</button>
			</form>
			<form method="post" action="{sessionurl file='admins.php' params="action=account"}" class="mt-2">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<button type="submit" class="btn btn-link btn-sm" name="mfa_action" value="resend_enable_email">{lng p="mfa_resend_code"}</button>
			</form>
		{else}
			<p>{lng p="mfa_setup_hint"}</p>
			{if $mfaQrSvg != ''}
				<div class="text-center my-3">{$mfaQrSvg nofilter}</div>
			{/if}
			<p class="small text-secondary">{lng p="mfa_secret_manual"}: <code>{$mfaSecretManual}</code></p>
			<form method="post" action="{sessionurl file='admins.php' params="action=account"}">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<input type="hidden" name="mfa_action" value="enable_totp" />
				<div class="mb-3">
					<label class="form-label">{lng p="mfa_code"}</label>
					<input type="text" class="form-control" name="totp_code" inputmode="numeric" maxlength="6" required="required" autocomplete="one-time-code" />
				</div>
				<div class="mb-3">
					<label class="form-label">{lng p="password"}</label>
					<input type="password" class="form-control" name="password" required="required" autocomplete="current-password" />
				</div>
				<button type="submit" class="btn btn-primary">{lng p="mfa_enable_totp"}</button>
			</form>
		{/if}
	{/if}
</fieldset>
