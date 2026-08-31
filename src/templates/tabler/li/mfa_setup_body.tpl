{if isset($mfaSetupMandatory) && $mfaSetupMandatory}
	<div class="alert alert-warning">{lng p="mfa_setup_mandatory"}</div>
{/if}

{if isset($mfaError)}
	<div class="alert alert-danger">{text value=$mfaError}</div>
{/if}
{if isset($mfaInfo)}
	<div class="alert alert-info{if $mfaEmailCodeExpiresAt|default:0 > 0} bm-mfa-code-validity-alert{/if}"{if $mfaEmailCodeExpiresAt|default:0 > 0} id="bmMfaCodeValidityAlert"{/if}>{text value=$mfaInfo}</div>
{/if}

{if $mfaStep == 3}
	{include file="li/mfa_backup_codes_alert.tpl"}
	<form method="post" action="{sessionurl file='start.php' params='action=mfaSetup'}" class="mt-3">
		{csrffield}
		<input type="hidden" name="do" value="mfaSetup" />
		<input type="hidden" name="step" value="3" />
		<div class="form-check mb-3">
			<input type="checkbox" class="form-check-input" name="backup_ack" value="1" id="backup_ack" required="required" />
			<label class="form-check-label" for="backup_ack">{lng p="mfa_backup_saved"}</label>
		</div>
		<button type="submit" class="btn btn-primary w-100">{lng p="save"}</button>
	</form>
{else}
	{assign var=mfaSwitchAction value="{sessionurl file='start.php' params='action=mfaSetup'}"}
	{assign var=mfaSwitchDo value='mfaSetup'}
	{include file="li/mfa_method_switch.tpl"}

	{if $mfaSetupMethod == 'email'}
		<p>{lng p="mfa_setup_hint_email"}</p>
		{if $mfaAltMailMasked != ''}
			<p class="text-secondary small">{lng p="altmail2"}: <strong>{$mfaAltMailMasked}</strong></p>
		{/if}
		<form id="bmMfaResendForm" method="post" action="{sessionurl file='start.php' params='action=mfaSetup'}" class="d-none">
			{csrffield}
			<input type="hidden" name="do" value="mfaSetup" />
			<input type="hidden" name="step" value="2" />
			<input type="hidden" name="resend_email" value="1" />
		</form>
		<form method="post" action="{sessionurl file='start.php' params='action=mfaSetup'}">
			{csrffield}
			<input type="hidden" name="do" value="mfaSetup" />
			<input type="hidden" name="step" value="2" />
			<div class="mb-3">
				<label class="form-label">{lng p="mfa_code"}</label>
				<input type="text" class="form-control" name="email_code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required="required" autocomplete="one-time-code" />
			</div>
			<button type="submit" class="btn btn-primary w-100">{lng p="mfa_verify_submit"}</button>
		</form>
		{include file="li/mfa_email_resend_wait.tpl"}
	{else}
		<p>{lng p="mfa_setup_hint"}</p>
		{if $mfaQrSvg != ''}
			<div class="text-center my-3">{$mfaQrSvg nofilter}</div>
		{/if}
		<p class="text-secondary small">{lng p="mfa_secret_manual"}: <code>{$mfaSecret}</code></p>
		<form method="post" action="{sessionurl file='start.php' params='action=mfaSetup'}">
			{csrffield}
			<input type="hidden" name="do" value="mfaSetup" />
			<input type="hidden" name="step" value="2" />
			<div class="mb-3">
				<label class="form-label">{lng p="mfa_code"}</label>
				<input type="text" class="form-control" name="totp_code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required="required" autocomplete="one-time-code" />
			</div>
			<button type="submit" class="btn btn-primary w-100">{lng p="mfa_verify_submit"}</button>
		</form>
	{/if}
{/if}
