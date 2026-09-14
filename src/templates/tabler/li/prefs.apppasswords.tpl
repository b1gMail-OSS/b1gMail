<div class="bm-prefs-page bm-prefs-page-apppasswords">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-key icon icon-sm" aria-hidden="true"></i>
		{lng p="apppasswords"}
	</div>
</div>

<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">

	{if $appPwError|default:''!=''}
		<div class="alert alert-danger alert-dismissible" role="alert">
			<div class="alert-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" class="icon alert-icon"><path d="M12 9v4"></path><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path><path d="M12 16h.01"></path></svg>
			</div>
			<div>
				<div class="alert-description">{text value=$appPwError}</div>
			</div>
			<a class="btn-close" data-bs-dismiss="alert" aria-label="{lng p="close"}"></a>
		</div>
	{/if}

	{if $appPwInfo|default:''!=''}
		<div class="alert alert-info alert-dismissible" role="alert">
			<div class="alert-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" class="icon alert-icon"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 9h.01"></path><path d="M11 12h1v4h1"></path></svg>
			</div>
			<div>
				<div class="alert-description">{text value=$appPwInfo}</div>
			</div>
			<a class="btn-close" data-bs-dismiss="alert" aria-label="{lng p="close"}"></a>
		</div>
	{/if}

	{if $appPwNewPlain|default:''!=''}
		<div class="alert alert-success" role="alert">
			<div class="alert-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" class="icon alert-icon"><path d="M5 12l5 5l10 -10"></path></svg>
			</div>
			<div class="w-100">
				<h4 class="alert-heading">{lng p="apppw_new_title"}</h4>
				<div class="alert-description">
					<p class="mb-2">{lng p="apppw_new_hint"}</p>
					<div class="input-group mb-2" style="max-width:32rem;">
						<input type="text" class="form-control font-monospace fs-4" value="{text value=$appPwNewPlain}" id="bm-apppw-new-value" readonly="readonly" onclick="this.select();" />
						<button type="button" class="btn" onclick="var el=document.getElementById('bm-apppw-new-value');el.select();try{ldelim}document.execCommand('copy');{rdelim}catch(e){ldelim}{rdelim}return false;">
							<i class="ti ti-copy" aria-hidden="true"></i> {lng p="copy"}
						</button>
					</div>
					<div class="small text-secondary">{lng p="apppw_new_label_hint"}: <strong>{text value=$appPwNewLabel}</strong></div>
				</div>
			</div>
		</div>
	{/if}

	<div class="row row-cards">

		{* ---- Info card ---- *}
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<p class="mb-2">{lng p="apppw_intro"}</p>

					{if $appPwUserHasMfa|default:false}
						{if $appPwDavEnforceMode == 'enforce'}
							<div class="alert alert-warning alert-dismissible mb-0 mt-3" role="alert">
								<div class="alert-icon">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" class="icon alert-icon"><path d="M12 9v4"></path><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path><path d="M12 16h.01"></path></svg>
								</div>
								<div>
									<h4 class="alert-heading">{lng p="apppw_mfa_active"}</h4>
									<div class="alert-description">{lng p="apppw_mfa_hint_enforce"}</div>
								</div>
								<a class="btn-close" data-bs-dismiss="alert" aria-label="{lng p="close"}"></a>
							</div>
						{else}
							<div class="alert alert-info alert-dismissible mb-0 mt-3" role="alert">
								<div class="alert-icon">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" class="icon alert-icon"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 9h.01"></path><path d="M11 12h1v4h1"></path></svg>
								</div>
								<div>
									<h4 class="alert-heading">{lng p="apppw_mfa_active"}</h4>
									<div class="alert-description">{lng p="apppw_mfa_hint_warn"}</div>
								</div>
								<a class="btn-close" data-bs-dismiss="alert" aria-label="{lng p="close"}"></a>
							</div>
						{/if}
					{/if}
				</div>
			</div>
		</div>

		{* ---- Existing list ---- *}
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{lng p="apppw_existing"}</h3>
				</div>
				{if $appPwRows|@count == 0}
					<div class="card-body">
						<div class="empty">
							<div class="empty-icon">
								<i class="ti ti-key-off" aria-hidden="true"></i>
							</div>
							<p class="empty-title">{lng p="apppw_none_yet"}</p>
						</div>
					</div>
				{else}
					<div class="table-responsive">
						<table class="table table-vcenter card-table">
							<thead>
								<tr>
									<th>{lng p="apppw_col_label"}</th>
									<th>{lng p="apppw_col_scope"}</th>
									<th>{lng p="apppw_col_created"}</th>
									<th>{lng p="apppw_col_last_used"}</th>
									<th class="text-center" style="width: 8rem;">{lng p="apppw_col_status"}</th>
									<th class="w-1"></th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$appPwRows item=row}
									<tr>
										<td>
											<div class="fw-bold">{text value=$row.label}</div>
											{if $row.client_hint != ''}<div class="small text-secondary">{text value=$row.client_hint}</div>{/if}
										</td>
										<td>
											{foreach from=$row.scope_array item=s}
												<span class="badge bg-blue-lt me-1">{$s}</span>
											{/foreach}
										</td>
										<td>
											{if $row.created > 0}{date timestamp=$row.created}{else}&mdash;{/if}
											{if $row.expires > 0}
												<div class="small text-secondary">{lng p="apppw_expires_on"}: {date timestamp=$row.expires}</div>
											{/if}
										</td>
										<td>
											{if $row.last_used > 0}
												{date timestamp=$row.last_used}
												{if $row.last_ip != ''}<div class="small text-secondary">{text value=$row.last_ip}{if $row.last_scope != ''} &middot; {$row.last_scope}{/if}</div>{/if}
											{else}
												<span class="text-secondary">{lng p="apppw_never_used"}</span>
											{/if}
										</td>
										<td class="text-center">
											{if $row.revoked_at > 0}
												<span class="badge bg-red-lt">{lng p="apppw_status_revoked"}</span>
											{elseif $row.expires > 0 && $row.expires < $appPwNowTs}
												<span class="badge bg-yellow-lt">{lng p="apppw_status_expired"}</span>
											{else}
												<span class="badge bg-green-lt">{lng p="apppw_status_active"}</span>
											{/if}
										</td>
										<td class="text-nowrap">
											{if $row.is_active}
												<form method="post" action="{sessionurl file='prefs.php' params='action=apppasswords'}" class="d-inline">
													{csrffield}
													<input type="hidden" name="do" value="revoke" />
													<input type="hidden" name="id" value="{$row.id}" />
													<button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('{lng p="apppw_revoke_confirm"}');">
														<i class="ti ti-trash" aria-hidden="true"></i>
														{lng p="apppw_revoke"}
													</button>
												</form>
											{/if}
										</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
					<div class="card-footer text-end">
						<form method="post" action="{sessionurl file='prefs.php' params='action=apppasswords'}" class="d-inline">
							{csrffield}
							<input type="hidden" name="do" value="revokeall" />
							<button type="submit" class="btn btn-link text-danger btn-sm" onclick="return confirm('{lng p="apppw_revoke_all_confirm"}');">
								{lng p="apppw_revoke_all"}
							</button>
						</form>
					</div>
				{/if}
			</div>
		</div>

		{* ---- Create new ---- *}
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{lng p="apppw_create_title"}</h3>
				</div>
				<form method="post" action="{sessionurl file='prefs.php' params='action=apppasswords'}" autocomplete="off">
					{csrffield}
					<input type="hidden" name="do" value="create" />
					<div class="card-body">
						<div class="mb-3 row">
							<label class="col-sm-3 col-form-label" for="bm-apppw-label">{lng p="apppw_col_label"}</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="label" id="bm-apppw-label" maxlength="128" required="required" placeholder="{lng p="apppw_label_placeholder"}" />
							</div>
						</div>
						<div class="mb-3 row">
							<label class="col-sm-3 col-form-label" for="bm-apppw-hint">{lng p="apppw_client_hint"}</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="client_hint" id="bm-apppw-hint" maxlength="64" placeholder="ios, thunderbird, ..." />
								<small class="form-hint">{lng p="apppw_client_hint_desc"}</small>
							</div>
						</div>
						<div class="mb-3 row">
							<label class="col-sm-3 col-form-label">{lng p="apppw_col_scope"}</label>
							<div class="col-sm-9">
								<div class="d-flex flex-wrap gap-3">
									{if $appPwSelectable.caldav}
										<label class="form-check">
											<input class="form-check-input" type="checkbox" name="scope[]" value="caldav" checked="checked" />
											<span class="form-check-label">CalDAV <span class="text-secondary small">({lng p="calendar"})</span></span>
										</label>
									{/if}
									{if $appPwSelectable.carddav}
										<label class="form-check">
											<input class="form-check-input" type="checkbox" name="scope[]" value="carddav" checked="checked" />
											<span class="form-check-label">CardDAV <span class="text-secondary small">({lng p="addressbook"})</span></span>
										</label>
									{/if}
									{if $appPwSelectable.webdav}
										<label class="form-check">
											<input class="form-check-input" type="checkbox" name="scope[]" value="webdav" />
											<span class="form-check-label">WebDAV <span class="text-secondary small">({lng p="webdisk"})</span></span>
										</label>
									{/if}

									{if $appPwSelectable.imap}
										<label class="form-check">
											<input class="form-check-input" type="checkbox" name="scope[]" value="imap" />
											<span class="form-check-label">IMAP</span>
										</label>
									{/if}
									{if $appPwSelectable.pop3}
										<label class="form-check">
											<input class="form-check-input" type="checkbox" name="scope[]" value="pop3" />
											<span class="form-check-label">POP3</span>
										</label>
									{/if}
									{if $appPwSelectable.smtp}
										<label class="form-check">
											<input class="form-check-input" type="checkbox" name="scope[]" value="smtp" />
											<span class="form-check-label">SMTP</span>
										</label>
									{/if}
								</div>

								{if $appPwMailPrepared}
									<div class="mt-3">
										<div class="small text-secondary mb-1">
											<i class="ti ti-info-circle" aria-hidden="true"></i>
											{lng p="apppw_mail_pending"}
										</div>
										<div class="d-flex flex-wrap gap-3">
											<label class="form-check">
												<input class="form-check-input" type="checkbox" disabled="disabled" />
												<span class="form-check-label text-secondary">IMAP <span class="small">({lng p="apppw_scope_coming_soon"})</span></span>
											</label>
											<label class="form-check">
												<input class="form-check-input" type="checkbox" disabled="disabled" />
												<span class="form-check-label text-secondary">POP3 <span class="small">({lng p="apppw_scope_coming_soon"})</span></span>
											</label>
											<label class="form-check">
												<input class="form-check-input" type="checkbox" disabled="disabled" />
												<span class="form-check-label text-secondary">SMTP <span class="small">({lng p="apppw_scope_coming_soon"})</span></span>
											</label>
										</div>
									</div>
								{/if}
							</div>
						</div>
						<div class="mb-3 row">
							<label class="col-sm-3 col-form-label" for="bm-apppw-expiry">{lng p="apppw_expiry"}</label>
							<div class="col-sm-9">
								<select class="form-select" name="expires_days" id="bm-apppw-expiry" style="max-width:16rem;">
									<option value="0">{lng p="apppw_expiry_never"}</option>
									<option value="30">30 {lng p="days"}</option>
									<option value="90">90 {lng p="days"}</option>
									<option value="180">180 {lng p="days"}</option>
									<option value="365">365 {lng p="days"}</option>
								</select>
								{if $appPwAdminMaxExpiryDays|default:0 > 0}
									<small class="form-hint">{lng p="apppw_expiry_admin_max"}: {$appPwAdminMaxExpiryDays} {lng p="days"}</small>
								{/if}
							</div>
						</div>
					</div>
					<div class="card-footer d-flex align-items-center">
						<span class="text-secondary small">{lng p="apppw_max_per_user"}: {$appPwMaxPerUser}</span>
						<button type="submit" class="btn btn-primary ms-auto">
							<i class="ti ti-plus" aria-hidden="true"></i>
							{lng p="apppw_create_btn"}
						</button>
					</div>
				</form>
			</div>
		</div>

	</div>

</div></div>
</div>
