<form action="{$sessionFormAction}{$sessionUrlSuffix}" method="post" onsubmit="spin(this)">
	{csrffield}
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="session_security_section"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="sessioniplock"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="ip_lock"{if $bm_prefs.ip_lock=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="sessioncookielock"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="cookie_lock"{if $bm_prefs.cookie_lock=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="session_lifetime"}</label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="number" min="0" class="form-control" name="session_lifetime" value="{$bm_prefs.session_lifetime|default:480}">
							<span class="input-group-text">{lng p="minutes"}</span>
						</div>
						<small class="form-hint">{lng p="session_lifetime_hint"}</small>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="session_idle_timeout"}</label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="number" min="0" class="form-control" name="session_idle_timeout" value="{$bm_prefs.session_idle_timeout|default:30}">
							<span class="input-group-text">{lng p="minutes"}</span>
						</div>
						<small class="form-hint">{lng p="session_idle_timeout_hint"}</small>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="session_warn_before"}</label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="number" min="0" class="form-control" name="session_warn_before" value="{$bm_prefs.session_warn_before|default:2}">
							<span class="input-group-text">{lng p="minutes"}</span>
						</div>
						<small class="form-hint">{lng p="session_warn_before_hint"}</small>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="session_cookie_mode"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="session_cookie_mode"{if $bm_prefs.session_cookie_mode|default:'yes'=='yes'} checked="checked"{/if}>
						</label>
						<small class="form-hint">{lng p="session_cookie_mode_hint"}</small>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="session_url_compat"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="session_url_compat"{if $bm_prefs.session_url_compat|default:'no'=='yes'} checked="checked"{/if}>
						</label>
						<small class="form-hint">{lng p="session_url_compat_hint"}</small>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="admin_whitelist"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="admin_whitelist" value="{text allowEmpty=true value=$adminwhitelist}" placeholder="{lng p="whitelist"}">
						<small class="form-hint">{lng p="admin_whitelist_hint"}</small>
					</div>
				</div>
			</fieldset>

			<fieldset class="mt-3">
				<legend>{lng p="mfa_global_section"}</legend>

				<div class="table-responsive">
					<table class="table table-vcenter card-table">
						<thead>
							<tr>
								<th>{lng p="mfa_global_setting"}</th>
								<th class="text-center" style="width: 9rem;">{lng p="mfa_global_li"}</th>
								<th class="text-center" style="width: 9rem;">{lng p="mfa_global_admin"}</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>{lng p="mfa_li_enable"}</td>
								<td class="text-center">
									<label class="form-check d-inline-block mb-0">
										<input class="form-check-input" type="checkbox" name="mfa_li_enable"{if $bm_prefs.mfa_li_enable=='yes'} checked="checked"{/if}>
									</label>
								</td>
								<td class="text-center">
									<label class="form-check d-inline-block mb-0">
										<input class="form-check-input" type="checkbox" name="mfa_admin_enable"{if $bm_prefs.mfa_admin_enable=='yes'} checked="checked"{/if}>
									</label>
								</td>
							</tr>
							<tr>
								<td>
									{lng p="mfa_row_allow_setup"}
									<div class="text-secondary small">{lng p="mfa_li_user_setup"} / {lng p="mfa_admin_user_setup"}</div>
								</td>
								<td class="text-center">
									<label class="form-check d-inline-block mb-0">
										<input class="form-check-input" type="checkbox" name="mfa_li_user_setup"{if $bm_prefs.mfa_li_user_setup|default:'yes'=='yes'} checked="checked"{/if}>
									</label>
								</td>
								<td class="text-center">
									<label class="form-check d-inline-block mb-0">
										<input class="form-check-input" type="checkbox" name="mfa_admin_user_setup"{if $bm_prefs.mfa_admin_user_setup|default:'yes'=='yes'} checked="checked"{/if}>
									</label>
								</td>
							</tr>
							<tr>
								<td>{lng p="mfa_li_default"}</td>
								<td class="text-center">
									<select class="form-select form-select-sm d-inline-block" style="width: auto; min-width: 6.5rem;" name="mfa_li_default">
										<option value="totp"{if $bm_prefs.mfa_li_default|default:'totp'=='totp'} selected="selected"{/if}>TOTP</option>
										<option value="email"{if $bm_prefs.mfa_li_default=='email'} selected="selected"{/if}>E-Mail</option>
									</select>
								</td>
								<td class="text-center">
									<select class="form-select form-select-sm d-inline-block" style="width: auto; min-width: 6.5rem;" name="mfa_admin_default">
										<option value="totp"{if $bm_prefs.mfa_admin_default|default:'totp'=='totp'} selected="selected"{/if}>TOTP</option>
										<option value="email"{if $bm_prefs.mfa_admin_default=='email'} selected="selected"{/if}>E-Mail</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>{lng p="mfa_admin_required"}</td>
								<td class="text-center"></td>
								<td class="text-center">
									<label class="form-check d-inline-block mb-0">
										<input class="form-check-input" type="checkbox" name="mfa_admin_required"{if $bm_prefs.mfa_admin_required=='yes'} checked="checked"{/if}>
									</label>
								</td>
							</tr>
							<tr>
								<td>{lng p="login_notify_li"}</td>
								<td class="text-center">
									<label class="form-check d-inline-block mb-0">
										<input class="form-check-input" type="checkbox" name="login_notify_li"{if $bm_prefs.login_notify_li=='yes'} checked="checked"{/if}>
									</label>
								</td>
								<td class="text-center">
									<label class="form-check d-inline-block mb-0">
										<input class="form-check-input" type="checkbox" name="login_notify_admin"{if $bm_prefs.login_notify_admin=='yes'} checked="checked"{/if}>
									</label>
								</td>
							</tr>
							<tr>
								<td>
									{lng p="pw_hash_algo"}
									<div class="text-secondary small">{lng p="pw_hash_algo_hint"}</div>
								</td>
								<td class="text-center">
									<select class="form-select form-select-sm d-inline-block" style="width: auto; min-width: 6.5rem;" name="pw_hash_li_algo">
										<option value="bcrypt"{if $bm_prefs.pw_hash_li_algo|default:'bcrypt'=='bcrypt'} selected="selected"{/if}>bcrypt</option>
										{if $passwordHashArgon2Available}
										<option value="argon2id"{if $bm_prefs.pw_hash_li_algo=='argon2id'} selected="selected"{/if}>Argon2id</option>
										{/if}
									</select>
								</td>
								<td class="text-center">
									<select class="form-select form-select-sm d-inline-block" style="width: auto; min-width: 6.5rem;" name="pw_hash_admin_algo">
										<option value="bcrypt"{if $bm_prefs.pw_hash_admin_algo|default:'bcrypt'=='bcrypt'} selected="selected"{/if}>bcrypt</option>
										{if $passwordHashArgon2Available}
										<option value="argon2id"{if $bm_prefs.pw_hash_admin_algo=='argon2id'} selected="selected"{/if}>Argon2id</option>
										{/if}
									</select>
								</td>
							</tr>
							<tr>
								<td>
									{lng p="pw_hash_cost"}
									<div class="text-secondary small">{lng p="pw_hash_cost_hint"}</div>
								</td>
								<td class="text-center">
									<input type="number" min="10" max="15" class="form-control form-control-sm d-inline-block" style="width: 4.5rem;" name="pw_hash_li_cost" value="{$bm_prefs.pw_hash_li_cost|default:12}">
								</td>
								<td class="text-center">
									<input type="number" min="10" max="15" class="form-control form-control-sm d-inline-block" style="width: 4.5rem;" name="pw_hash_admin_cost" value="{$bm_prefs.pw_hash_admin_cost|default:12}">
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</fieldset>
		</div>

		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="nliarea"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="domain_combobox"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="domain_combobox"{if $bm_prefs.domain_combobox=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="redirectmobile"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="redirect_mobile"{if $bm_prefs.redirect_mobile=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="logouturl"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="logouturl" value="{text allowEmpty=true value=$bm_prefs.logouturl}" placeholder="{lng p="logouturl"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="savehistory"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="contact_history"{if $bm_prefs.contact_history=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
			</fieldset>

			<fieldset class="mt-3">
				<legend>{lng p="ssl"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="ssl_url"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="ssl_url" value="{text allowEmpty=true value=$bm_prefs.ssl_url}" placeholder="{lng p="ssl_url"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="ssl_login_option"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="ssl_login_option"{if $bm_prefs.ssl_login_option=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="ssl_login_enable"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="ssl_login_enable"{if $bm_prefs.ssl_login_enable=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="form-footer">
		<button type="submit" class="btn btn-primary">{lng p="save"}</button>
	</div>
</form>
