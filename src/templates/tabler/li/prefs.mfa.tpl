<div class="bm-prefs-page bm-prefs-page-mfa">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-shield-lock icon icon-sm" aria-hidden="true"></i>
		{lng p="mfa"}
	</div>
</div>

<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">

	{if isset($mfaError)}
		<div class="alert alert-danger">{text value=$mfaError}</div>
	{/if}
	{if isset($mfaInfo)}
		<div class="alert alert-info">{text value=$mfaInfo}</div>
	{/if}

	{if isset($backupCodes)}
		{include file="li/mfa_backup_codes_alert.tpl"}
	{/if}

	{if $mfaAccount && $mfaAccount.enabled == 'yes' && empty($mfaPrefsSwitch)}
		<table class="listTable">
			<tr>
				<th class="listTableHead" colspan="2">{lng p="mfa"}</th>
			</tr>
			<tr>
				<td class="listTableLeftDesc"><i class="ti ti-shield-check icon icon-sm" aria-hidden="true"></i></td>
				<td class="listTableRightDesc">
					<p class="mb-1">{lng p="mfa_status_on"}</p>
					<p class="text-secondary mb-0">
						{if $mfaActiveMethod == 'email'}
							{lng p="mfa_method_email_active"}
						{else}
							{lng p="mfa_method_totp_active"}
						{/if}
						{if $mfaEnabledAtFormatted != ''}<br /><span class="small">{lng p="mfa_active_since_label"} {$mfaEnabledAtFormatted}</span>{/if}
					</p>
				</td>
			</tr>
		</table>

		<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}" class="mt-3">
			{csrffield}
			<input type="hidden" name="do" value="mfaSave" />
			<table class="listTable">
				<tr>
					<td class="listTableLeft"><label for="mfa_manage_password">{lng p="password"}:</label></td>
					<td class="listTableRight">
						<input type="password" class="form-control" name="password" id="mfa_manage_password" required="required" autocomplete="current-password" style="max-width:20rem;" />
					</td>
				</tr>
				<tr>
					<td class="listTableLeft">&nbsp;</td>
					<td class="listTableRight">
						<div class="d-flex flex-wrap align-items-center gap-2">
							<button type="submit" class="btn btn-outline-warning" name="mfa_action" value="reset_setup" onclick="return confirm('{lng p="mfa_reset_setup_confirm"}');">{lng p="mfa_reset_setup_btn"}</button>
							{if !$mfaMandatory}
								<button type="submit" class="btn btn-outline-danger" name="mfa_action" value="disable">{lng p="mfa_disable"}</button>
							{/if}
							<button type="submit" class="btn btn-secondary" name="mfa_action" value="regen_backup">{lng p="mfa_backup_regenerate"}</button>
							{if $mfaCanUseEmail && $mfaActiveMethod != 'email'}
								<button type="submit" class="btn btn-outline-primary" name="mfa_action" value="switch_to_email">{lng p="mfa_use_email_method"}</button>
							{elseif $mfaActiveMethod == 'email'}
								<button type="submit" class="btn btn-outline-primary" name="mfa_action" value="switch_to_totp">{lng p="mfa_use_totp_method"}</button>
							{/if}
						</div>
					</td>
				</tr>
			</table>
		</form>

	{elseif !empty($mfaPrefsSwitch)}
		<table class="listTable">
			<tr>
				<th class="listTableHead" colspan="2">{lng p="mfa"}</th>
			</tr>
			<tr>
				<td class="listTableLeftDesc"><i class="ti ti-switch-horizontal icon icon-sm" aria-hidden="true"></i></td>
				<td class="listTableRightDesc"><p class="mb-0">{lng p="mfa_switch_in_progress"}</p></td>
			</tr>
		</table>
		{assign var=mfaSwitchAction value="{sessionurl file='prefs.php' params='action=mfa'}"}
		{assign var=mfaSwitchDo value='mfaSave'}
		{include file="li/mfa_method_switch.tpl"}

		{if $mfaSetupMethod == 'email'}
			<p class="mt-3">{lng p="mfa_setup_hint_email"}</p>
			{if $mfaAltMailMasked != ''}
				<p class="text-secondary small">{lng p="altmail2"}: <strong>{$mfaAltMailMasked}</strong></p>
			{/if}
			<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<input type="hidden" name="mfa_action" value="confirm_switch_email" />
				<table class="listTable">
					<tr>
						<td class="listTableLeft"><label>{lng p="mfa_code"}:</label></td>
						<td class="listTableRight">
							<input type="text" class="form-control" name="email_code" inputmode="numeric" maxlength="6" required="required" autocomplete="one-time-code" style="max-width:12rem;" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft"><label>{lng p="password"}:</label></td>
						<td class="listTableRight">
							<input type="password" class="form-control" name="password" required="required" autocomplete="current-password" style="max-width:20rem;" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft">&nbsp;</td>
						<td class="listTableRight">
							<button type="submit" class="btn btn-primary">{lng p="mfa_verify_submit"}</button>
						</td>
					</tr>
				</table>
			</form>
			<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}" class="mt-2">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<button type="submit" class="btn btn-link btn-sm" name="mfa_action" value="resend_switch_email">{lng p="mfa_resend_code"}</button>
			</form>
		{else}
			<p class="mt-3">{lng p="mfa_setup_hint"}</p>
			{if $mfaQrSvg != ''}
				<div class="text-center my-3">{$mfaQrSvg nofilter}</div>
			{/if}
			<p class="text-secondary small">{lng p="mfa_secret_manual"}: <code>{$mfaSecret}</code></p>
			<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<input type="hidden" name="mfa_action" value="confirm_switch_totp" />
				<table class="listTable">
					<tr>
						<td class="listTableLeft"><label>{lng p="mfa_code"}:</label></td>
						<td class="listTableRight">
							<input type="text" class="form-control" name="totp_code" inputmode="numeric" maxlength="6" required="required" style="max-width:12rem;" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft"><label>{lng p="password"}:</label></td>
						<td class="listTableRight">
							<input type="password" class="form-control" name="password" required="required" autocomplete="current-password" style="max-width:20rem;" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft">&nbsp;</td>
						<td class="listTableRight">
							<button type="submit" class="btn btn-primary">{lng p="mfa_enable_totp"}</button>
						</td>
					</tr>
				</table>
			</form>
		{/if}

	{else}
		<table class="listTable">
			<tr>
				<th class="listTableHead" colspan="2">{lng p="mfa"}</th>
			</tr>
			<tr>
				<td class="listTableLeftDesc"><i class="ti ti-shield-off icon icon-sm" aria-hidden="true"></i></td>
				<td class="listTableRightDesc"><p class="mb-0">{lng p="mfa_status_off"}</p></td>
			</tr>
		</table>
		{assign var=mfaSwitchAction value="{sessionurl file='prefs.php' params='action=mfa'}"}
		{assign var=mfaSwitchDo value='mfaSave'}
		{include file="li/mfa_method_switch.tpl"}

		{if $mfaSetupMethod == 'email'}
			<p class="mt-3">{lng p="mfa_setup_hint_email"}</p>
			{if $mfaAltMailMasked != ''}
				<p class="text-secondary small">{lng p="altmail2"}: <strong>{$mfaAltMailMasked}</strong></p>
			{/if}
			<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<input type="hidden" name="mfa_action" value="enable_email" />
				<table class="listTable">
					<tr>
						<td class="listTableLeft"><label>{lng p="mfa_code"}:</label></td>
						<td class="listTableRight">
							<input type="text" class="form-control" name="email_code" inputmode="numeric" maxlength="6" required="required" autocomplete="one-time-code" style="max-width:12rem;" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft"><label>{lng p="password"}:</label></td>
						<td class="listTableRight">
							<input type="password" class="form-control" name="password" required="required" autocomplete="current-password" style="max-width:20rem;" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft">&nbsp;</td>
						<td class="listTableRight">
							<button type="submit" class="btn btn-primary">{lng p="mfa_enable_email"}</button>
						</td>
					</tr>
				</table>
			</form>
			<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}" class="mt-2">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<button type="submit" class="btn btn-link btn-sm" name="mfa_action" value="resend_enable_email">{lng p="mfa_resend_code"}</button>
			</form>
		{else}
			<p class="mt-3">{lng p="mfa_setup_hint"}</p>
			{if $mfaQrSvg != ''}
				<div class="text-center my-3">{$mfaQrSvg nofilter}</div>
			{/if}
			<p class="text-secondary small">{lng p="mfa_secret_manual"}: <code>{$mfaSecret}</code></p>
			<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<input type="hidden" name="mfa_action" value="enable_totp" />
				<table class="listTable">
					<tr>
						<td class="listTableLeft"><label>{lng p="mfa_code"}:</label></td>
						<td class="listTableRight">
							<input type="text" class="form-control" name="totp_code" inputmode="numeric" maxlength="6" required="required" style="max-width:12rem;" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft"><label>{lng p="password"}:</label></td>
						<td class="listTableRight">
							<input type="password" class="form-control" name="password" required="required" autocomplete="current-password" style="max-width:20rem;" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft">&nbsp;</td>
						<td class="listTableRight">
							<button type="submit" class="btn btn-primary">{lng p="mfa_enable_totp"}</button>
						</td>
					</tr>
				</table>
			</form>
		{/if}
	{/if}

</div></div>
</div>
