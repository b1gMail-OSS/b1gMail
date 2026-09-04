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
	<form method="post" action="{sessionurl file='start.php' params='action=mfaSetup'}" style="margin-top:12px;">
		{csrffield}
		<input type="hidden" name="do" value="mfaSetup" />
		<input type="hidden" name="step" value="3" />
		<div class="checkbox">
			<label>
				<input type="checkbox" name="backup_ack" value="1" id="backup_ack" required="required" />
				{lng p="mfa_backup_saved"}
			</label>
		</div>
		<button type="submit" class="btn btn-primary">{lng p="save"}</button>
	</form>
{else}
	{assign var=mfaSwitchAction value="{sessionurl file='start.php' params='action=mfaSetup'}"}
	{assign var=mfaSwitchDo value='mfaSetup'}
	{include file="li/mfa_method_switch.tpl"}

	{if $mfaSetupMethod == 'email'}
		<p>{lng p="mfa_setup_hint_email"}</p>
		{if $mfaAltMailMasked != ''}
			<p class="text-muted"><small>{lng p="altmail2"}: <strong>{$mfaAltMailMasked}</strong></small></p>
		{/if}
		<form id="bmMfaResendForm" method="post" action="{sessionurl file='start.php' params='action=mfaSetup'}" style="display:none;">
			{csrffield}
			<input type="hidden" name="do" value="mfaSetup" />
			<input type="hidden" name="step" value="2" />
			<input type="hidden" name="resend_email" value="1" />
		</form>
		<form method="post" action="{sessionurl file='start.php' params='action=mfaSetup'}">
			{csrffield}
			<input type="hidden" name="do" value="mfaSetup" />
			<input type="hidden" name="step" value="2" />
			<div class="form-group">
				<label>{lng p="mfa_code"}</label>
				<input type="text" class="form-control" name="email_code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required="required" autocomplete="one-time-code" />
			</div>
			<button type="submit" class="btn btn-primary">{lng p="mfa_verify_submit"}</button>
		</form>
		{include file="li/mfa_email_resend_wait.tpl"}
	{else}
		<p>{lng p="mfa_setup_hint"}</p>
		{if $mfaQrSvg != ''}
			<div class="text-center" style="margin:16px 0;">{$mfaQrSvg nofilter}</div>
		{/if}
		<p class="text-muted"><small>{lng p="mfa_secret_manual"}: <code>{$mfaSecret}</code></small></p>
		<form method="post" action="{sessionurl file='start.php' params='action=mfaSetup'}">
			{csrffield}
			<input type="hidden" name="do" value="mfaSetup" />
			<input type="hidden" name="step" value="2" />
			<div class="form-group">
				<label>{lng p="mfa_code"}</label>
				<input type="text" class="form-control" name="totp_code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required="required" autocomplete="one-time-code" />
			</div>
			<button type="submit" class="btn btn-primary">{lng p="mfa_verify_submit"}</button>
		</form>
	{/if}
{/if}
