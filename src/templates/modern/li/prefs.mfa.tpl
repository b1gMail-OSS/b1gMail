<div id="contentHeader">
	<div class="left">
		<i class="fa fa-shield" aria-hidden="true"></i>
		{lng p="mfa"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

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
				<td class="listTableLeftDesc"><i class="fa fa-shield" aria-hidden="true"></i></td>
				<td class="listTableRightDesc">
					<p>{lng p="mfa_status_on"}</p>
					<p class="text-muted">
						{if $mfaActiveMethod == 'email'}
							{lng p="mfa_method_email_active"}
						{else}
							{lng p="mfa_method_totp_active"}
						{/if}
						{if $mfaEnabledAtFormatted != ''}<br /><span>{lng p="mfa_active_since_label"} {$mfaEnabledAtFormatted}</span>{/if}
					</p>
				</td>
			</tr>
		</table>

		<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}" style="margin-top:12px;">
			{csrffield}
			<input type="hidden" name="do" value="mfaSave" />
			<table class="listTable">
				<tr>
					<td class="listTableLeft"><label for="mfa_manage_password">{lng p="password"}:</label></td>
					<td class="listTableRight">
						<input type="password" name="password" id="mfa_manage_password" required="required" autocomplete="current-password" style="max-width:20rem;" />
					</td>
				</tr>
				<tr>
					<td class="listTableLeft">&nbsp;</td>
					<td class="listTableRight">
						<div>
							<button type="submit" class="btn btn-warning" name="mfa_action" value="reset_setup" onclick="return confirm('{lng p="mfa_reset_setup_confirm"}');">{lng p="mfa_reset_setup_btn"}</button>
							{if !$mfaMandatory}
								<button type="submit" class="btn btn-danger" name="mfa_action" value="disable">{lng p="mfa_disable"}</button>
							{/if}
							<button type="submit" class="btn btn-default" name="mfa_action" value="regen_backup">{lng p="mfa_backup_regenerate"}</button>
							{if $mfaCanUseEmail && $mfaActiveMethod != 'email'}
								<button type="submit" class="btn btn-info" name="mfa_action" value="switch_to_email">{lng p="mfa_use_email_method"}</button>
							{elseif $mfaActiveMethod == 'email'}
								<button type="submit" class="btn btn-info" name="mfa_action" value="switch_to_totp">{lng p="mfa_use_totp_method"}</button>
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
				<td class="listTableLeftDesc"><i class="fa fa-exchange" aria-hidden="true"></i></td>
				<td class="listTableRightDesc"><p>{lng p="mfa_switch_in_progress"}</p></td>
			</tr>
		</table>
		{assign var=mfaSwitchAction value="{sessionurl file='prefs.php' params='action=mfa'}"}
		{assign var=mfaSwitchDo value='mfaSave'}
		{include file="li/mfa_method_switch.tpl"}

		{if $mfaSetupMethod == 'email'}
			<p style="margin-top:12px;">{lng p="mfa_setup_hint_email"}</p>
			{if $mfaAltMailMasked != ''}
				<p class="text-muted">{lng p="altmail2"}: <strong>{$mfaAltMailMasked}</strong></p>
			{/if}
			<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<input type="hidden" name="mfa_action" value="confirm_switch_email" />
				<table class="listTable">
					<tr>
						<td class="listTableLeft"><label>{lng p="mfa_code"}:</label></td>
						<td class="listTableRight">
							<input type="text" name="email_code" inputmode="numeric" maxlength="6" required="required" autocomplete="one-time-code" style="max-width:12rem;" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft"><label>{lng p="password"}:</label></td>
						<td class="listTableRight">
							<input type="password" name="password" required="required" autocomplete="current-password" style="max-width:20rem;" />
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
			<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}" style="margin-top:8px;">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<button type="submit" class="btn btn-link btn-sm" name="mfa_action" value="resend_switch_email">{lng p="mfa_resend_code"}</button>
			</form>
		{else}
			<p style="margin-top:12px;">{lng p="mfa_setup_hint"}</p>
			{if $mfaQrSvg != ''}
				<div class="text-center my-3">{$mfaQrSvg nofilter}</div>
			{/if}
			<p class="text-muted">{lng p="mfa_secret_manual"}: <code>{$mfaSecret}</code></p>
			<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<input type="hidden" name="mfa_action" value="confirm_switch_totp" />
				<table class="listTable">
					<tr>
						<td class="listTableLeft"><label>{lng p="mfa_code"}:</label></td>
						<td class="listTableRight">
							<input type="text" name="totp_code" inputmode="numeric" maxlength="6" required="required" style="max-width:12rem;" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft"><label>{lng p="password"}:</label></td>
						<td class="listTableRight">
							<input type="password" name="password" required="required" autocomplete="current-password" style="max-width:20rem;" />
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
				<td class="listTableLeftDesc"><i class="fa fa-shield" aria-hidden="true"></i></td>
				<td class="listTableRightDesc"><p>{lng p="mfa_status_off"}</p></td>
			</tr>
		</table>
		{assign var=mfaSwitchAction value="{sessionurl file='prefs.php' params='action=mfa'}"}
		{assign var=mfaSwitchDo value='mfaSave'}
		{include file="li/mfa_method_switch.tpl"}

		{if $mfaSetupMethod == 'email'}
			<p style="margin-top:12px;">{lng p="mfa_setup_hint_email"}</p>
			{if $mfaAltMailMasked != ''}
				<p class="text-muted">{lng p="altmail2"}: <strong>{$mfaAltMailMasked}</strong></p>
			{/if}
			<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<input type="hidden" name="mfa_action" value="enable_email" />
				<table class="listTable">
					<tr>
						<td class="listTableLeft"><label>{lng p="mfa_code"}:</label></td>
						<td class="listTableRight">
							<input type="text" name="email_code" inputmode="numeric" maxlength="6" required="required" autocomplete="one-time-code" style="max-width:12rem;" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft"><label>{lng p="password"}:</label></td>
						<td class="listTableRight">
							<input type="password" name="password" required="required" autocomplete="current-password" style="max-width:20rem;" />
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
			<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}" style="margin-top:8px;">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<button type="submit" class="btn btn-link btn-sm" name="mfa_action" value="resend_enable_email">{lng p="mfa_resend_code"}</button>
			</form>
		{else}
			<p style="margin-top:12px;">{lng p="mfa_setup_hint"}</p>
			{if $mfaQrSvg != ''}
				<div class="text-center my-3">{$mfaQrSvg nofilter}</div>
			{/if}
			<p class="text-muted">{lng p="mfa_secret_manual"}: <code>{$mfaSecret}</code></p>
			<form method="post" action="{sessionurl file='prefs.php' params='action=mfa'}">
				{csrffield}
				<input type="hidden" name="do" value="mfaSave" />
				<input type="hidden" name="mfa_action" value="enable_totp" />
				<table class="listTable">
					<tr>
						<td class="listTableLeft"><label>{lng p="mfa_code"}:</label></td>
						<td class="listTableRight">
							<input type="text" name="totp_code" inputmode="numeric" maxlength="6" required="required" style="max-width:12rem;" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft"><label>{lng p="password"}:</label></td>
						<td class="listTableRight">
							<input type="password" name="password" required="required" autocomplete="current-password" style="max-width:20rem;" />
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
