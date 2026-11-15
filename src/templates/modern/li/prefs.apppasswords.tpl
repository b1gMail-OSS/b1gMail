<div id="contentHeader">
	<div class="left">
		<i class="fa fa-key" aria-hidden="true"></i>
		{lng p="apppasswords"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

	{if $appPwError|default:''!=''}
		<div class="alert alert-danger">{text value=$appPwError}</div>
	{/if}

	{if $appPwInfo|default:''!=''}
		<div class="alert alert-info">{text value=$appPwInfo}</div>
	{/if}

	{if $appPwNewPlain|default:''!=''}
		<div class="alert alert-success">
			<strong>{lng p="apppw_new_title"}</strong>
			<p>{lng p="apppw_new_hint"}</p>
			<p>
				<input type="text" class="smallInput" style="width:22rem;font-family:monospace;font-size:1.1em;" value="{text value=$appPwNewPlain}" id="bm-apppw-new-value" readonly="readonly" onclick="this.select();" />
				<button type="button" class="btn btn-default" onclick="var el=document.getElementById('bm-apppw-new-value');el.select();try{ldelim}document.execCommand('copy');{rdelim}catch(e){ldelim}{rdelim}return false;">
					<i class="fa fa-copy" aria-hidden="true"></i> {lng p="copy"}
				</button>
			</p>
			<p class="text-muted">{lng p="apppw_new_label_hint"}: <strong>{text value=$appPwNewLabel}</strong></p>
		</div>
	{/if}

	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2">{lng p="apppasswords"}</th>
		</tr>
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-info-circle" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="apppw_intro"}</td>
		</tr>
		{if $appPwUserHasMfa|default:false}
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-shield" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">
				<strong>{lng p="apppw_mfa_active"}</strong><br />
				{if $appPwDavEnforceMode == 'enforce'}
					{lng p="apppw_mfa_hint_enforce"}
				{else}
					{lng p="apppw_mfa_hint_warn"}
				{/if}
			</td>
		</tr>
		{/if}
	</table>

	<table class="listTable" style="margin-top:1em;">
		<tr>
			<th class="listTableHead" colspan="2">{lng p="apppw_existing"}</th>
		</tr>
		{if $appPwRows|@count == 0}
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">{lng p="apppw_none_yet"}</td>
		</tr>
		{else}
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight" style="padding:0;">
				<table class="bigTable" style="border:0;">
					<tr>
						<th>{lng p="apppw_col_label"}</th>
						<th>{lng p="apppw_col_scope"}</th>
						<th width="140">{lng p="apppw_col_created"}</th>
						<th width="140">{lng p="apppw_col_last_used"}</th>
						<th width="90">{lng p="apppw_col_status"}</th>
						<th width="40">&nbsp;</th>
					</tr>
					<tbody class="listTBody">
					{foreach from=$appPwRows item=row}
					{cycle values="listTableTD,listTableTD2" assign="class"}
					<tr>
						<td class="{$class}">
							<strong>{text value=$row.label}</strong>
							{if $row.client_hint != ''}<br /><span class="text-muted">{text value=$row.client_hint}</span>{/if}
						</td>
						<td class="{$class}">
							{foreach from=$row.scope_array item=s}
								{$s}{if !$s@last}, {/if}
							{/foreach}
						</td>
						<td class="{$class}">
							{if $row.created > 0}{date timestamp=$row.created}{else}&mdash;{/if}
							{if $row.expires > 0}<br /><span class="text-muted">{lng p="apppw_expires_on"}: {date timestamp=$row.expires}</span>{/if}
						</td>
						<td class="{$class}">
							{if $row.last_used > 0}
								{date timestamp=$row.last_used}
								{if $row.last_ip != ''}<br /><span class="text-muted">{text value=$row.last_ip}{if $row.last_scope != ''} &middot; {$row.last_scope}{/if}</span>{/if}
							{else}
								<span class="text-muted">{lng p="apppw_never_used"}</span>
							{/if}
						</td>
						<td class="{$class}">
							{if $row.revoked_at > 0}
								{lng p="apppw_status_revoked"}
							{elseif $row.expires > 0 && $row.expires < $appPwNowTs}
								{lng p="apppw_status_expired"}
							{else}
								{lng p="apppw_status_active"}
							{/if}
						</td>
						<td class="{$class}" nowrap="nowrap">
							{if $row.is_active}
							<form method="post" action="{sessionurl file='prefs.php' params='action=apppasswords'}" style="display:inline;">
								{csrffield}
								<input type="hidden" name="do" value="revoke" />
								<input type="hidden" name="id" value="{$row.id}" />
								<button type="submit" class="btn btn-danger btn-sm" title="{lng p="apppw_revoke"}" onclick="return confirm('{lng p="apppw_revoke_confirm"}');">
									<i class="fa fa-trash-o" aria-hidden="true"></i>
								</button>
							</form>
							{/if}
						</td>
					</tr>
					{/foreach}
					</tbody>
				</table>
				<form method="post" action="{sessionurl file='prefs.php' params='action=apppasswords'}" style="margin-top:0.5em;">
					{csrffield}
					<input type="hidden" name="do" value="revokeall" />
					<button type="submit" class="btn btn-link btn-sm" onclick="return confirm('{lng p="apppw_revoke_all_confirm"}');">
						{lng p="apppw_revoke_all"}
					</button>
				</form>
			</td>
		</tr>
		{/if}
	</table>

	<form method="post" action="{sessionurl file='prefs.php' params='action=apppasswords'}" autocomplete="off" style="margin-top:1em;">
		{csrffield}
		<input type="hidden" name="do" value="create" />
		<table class="listTable">
			<tr>
				<th class="listTableHead" colspan="2">{lng p="apppw_create_title"}</th>
			</tr>
			<tr>
				<td class="listTableLeft"><label for="bm-apppw-label">* {lng p="apppw_col_label"}:</label></td>
				<td class="listTableRight">
					<input type="text" name="label" id="bm-apppw-label" maxlength="128" required="required" placeholder="{lng p="apppw_label_placeholder"}" style="width:22rem;" />
				</td>
			</tr>
			<tr>
				<td class="listTableLeft"><label for="bm-apppw-hint">{lng p="apppw_client_hint"}:</label></td>
				<td class="listTableRight">
					<input type="text" name="client_hint" id="bm-apppw-hint" maxlength="64" placeholder="ios, thunderbird, ..." style="width:22rem;" />
					<br /><span class="text-muted">{lng p="apppw_client_hint_desc"}</span>
				</td>
			</tr>
			<tr>
				<td class="listTableLeft">{lng p="apppw_col_scope"}:</td>
				<td class="listTableRight">
					{if $appPwSelectable.caldav}
						<label><input type="checkbox" name="scope[]" value="caldav" checked="checked" /> CalDAV ({lng p="calendar"})</label><br />
					{/if}
					{if $appPwSelectable.carddav}
						<label><input type="checkbox" name="scope[]" value="carddav" checked="checked" /> CardDAV ({lng p="addressbook"})</label><br />
					{/if}
					{if $appPwSelectable.webdav}
						<label><input type="checkbox" name="scope[]" value="webdav" /> WebDAV ({lng p="webdisk"})</label><br />
					{/if}
					{if $appPwSelectable.imap}
						<label><input type="checkbox" name="scope[]" value="imap" /> IMAP</label><br />
					{/if}
					{if $appPwSelectable.pop3}
						<label><input type="checkbox" name="scope[]" value="pop3" /> POP3</label><br />
					{/if}
					{if $appPwSelectable.smtp}
						<label><input type="checkbox" name="scope[]" value="smtp" /> SMTP</label><br />
					{/if}

					{if $appPwMailPrepared}
						<p class="text-muted" style="margin-top:0.75em;">
							<i class="fa fa-info-circle" aria-hidden="true"></i>
							{lng p="apppw_mail_pending"}
						</p>
						<label class="text-muted"><input type="checkbox" disabled="disabled" /> IMAP ({lng p="apppw_scope_coming_soon"})</label><br />
						<label class="text-muted"><input type="checkbox" disabled="disabled" /> POP3 ({lng p="apppw_scope_coming_soon"})</label><br />
						<label class="text-muted"><input type="checkbox" disabled="disabled" /> SMTP ({lng p="apppw_scope_coming_soon"})</label>
					{/if}
				</td>
			</tr>
			<tr>
				<td class="listTableLeft"><label for="bm-apppw-expiry">{lng p="apppw_expiry"}:</label></td>
				<td class="listTableRight">
					<select name="expires_days" id="bm-apppw-expiry">
						<option value="0">{lng p="apppw_expiry_never"}</option>
						<option value="30">30 {lng p="days"}</option>
						<option value="90">90 {lng p="days"}</option>
						<option value="180">180 {lng p="days"}</option>
						<option value="365">365 {lng p="days"}</option>
					</select>
					{if $appPwAdminMaxExpiryDays|default:0 > 0}
						<br /><span class="text-muted">{lng p="apppw_expiry_admin_max"}: {$appPwAdminMaxExpiryDays} {lng p="days"}</span>
					{/if}
				</td>
			</tr>
			<tr>
				<td class="listTableLeft">&nbsp;</td>
				<td class="listTableRight">
					<span class="text-muted">{lng p="apppw_max_per_user"}: {$appPwMaxPerUser}</span>
					&nbsp;
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-plus-circle" aria-hidden="true"></i>
						{lng p="apppw_create_btn"}
					</button>
				</td>
			</tr>
		</table>
	</form>

</div></div>
