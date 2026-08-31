{if $mfaCanUseEmail && $mfaSetupMethod == 'totp'}
	<form id="bmMfaSwitchForm" method="post" action="{$mfaSwitchAction|escape}" class="d-none">
		{csrffield}
		<input type="hidden" name="do" value="{$mfaSwitchDo|escape}" />
		<input type="hidden" name="switch_method" value="email" />
	</form>
	<button type="submit" form="bmMfaSwitchForm" formnovalidate="formnovalidate" class="btn btn-outline-secondary btn-sm mb-2">{lng p="mfa_use_email_method"}</button>
{elseif $mfaSetupMethod == 'email'}
	<form id="bmMfaSwitchForm" method="post" action="{$mfaSwitchAction|escape}" class="d-none">
		{csrffield}
		<input type="hidden" name="do" value="{$mfaSwitchDo|escape}" />
		<input type="hidden" name="switch_method" value="totp" />
	</form>
	<button type="submit" form="bmMfaSwitchForm" formnovalidate="formnovalidate" class="btn btn-outline-secondary btn-sm mb-2">{lng p="mfa_use_totp_method"}</button>
{/if}
