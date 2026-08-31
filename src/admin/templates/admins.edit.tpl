<form action="{sessionurl file='admins.php' params="action=admins&do=edit&id={$admin.adminid}&save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="editadmin"}: {text value=$admin.username}</legend>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="username"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="username" name="username" value="{if isset($admin.username)}{text value=$admin.username}{/if}" placeholder="{lng p="username"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="firstname"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="firstname" name="firstname" value="{if isset($admin.firstname)}{text value=$admin.firstname allowEmpty=true}{/if}" placeholder="{lng p="firstname"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="lastname"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="lastname" name="lastname" value="{if isset($admin.lastname)}{text value=$admin.lastname allowEmpty=true}{/if}" placeholder="{lng p="lastname"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label" for="email">{lng p="admin_email"}</label>
					<div class="col-sm-8">
						<input type="email" class="form-control" id="email" name="email" value="{if isset($admin.email)}{text value=$admin.email allowEmpty=true}{/if}" placeholder="{lng p="admin_email"}">
						<small class="form-hint">{lng p="admin_email_hint"}</small>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="type"}</label>
					<div class="col-sm-8">
						<select class="form-select" name="type"{if $admin.adminid==1} disabled="disabled"{/if} onclick="EBID('perms').style.display=this.value==0?'none':'';">
							<option value="1"{if $admin.type==1} selected="selected"{/if}>{lng p="admin"}</option>
							<option value="0"{if $admin.type==0} selected="selected"{/if}>{lng p="superadmin"}</option>
						</select>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="password"}</legend>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="newpassword"}</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="newpw1" name="newpw1" placeholder="{lng p="newpassword"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="newpassword"} ({lng p="repeat"})</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="newpw2" name="newpw2" placeholder="{lng p="newpassword"} ({lng p="repeat"})">
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<fieldset id="perms" style="display:{if $admin.type==0}none{/if};">
		<legend>{lng p="permissions"}</legend>

		<div class="row">
			<div class="col-md-6">
				<div class="mb-3">
					<label class="form-label">{lng p="areas"}</label>
					<div>
						{foreach from=$permsTable item=permTitle key=permName}
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="perms[{$permName}]" value="1" id="perm_{$permName}"{if isset($admin.perms.$permName) &&  $admin.perms.$permName} checked="checked"{/if}>
								<span class="form-check-label">{$permTitle}</span>
							</label>
						{/foreach}
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3">
					<label class="form-label">{lng p="plugins"}</label>
					<div>
						{foreach from=$pluginList item=pluginTitle key=pluginName}
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="perms[plugins][{$pluginName}]" value="1" id="plugin_{$pluginName}"{if isset($admin.perms.plugins.$pluginName) && $admin.perms.plugins.$pluginName} checked="checked"{/if}>
								<span class="form-check-label">{text value=$pluginTitle}</span>
							</label>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
	</fieldset>

	{if $mfaAdminEnabled}
	<fieldset>
		<legend>{lng p="mfa"}</legend>
		<div class="mb-3 row">
			<label class="col-sm-4 col-form-label">{lng p="mfa"}</label>
			<div class="col-sm-8">
				<div class="form-control-plaintext">
					{if $mfaEnabled}
						{lng p="mfa_status_on"}
						{if $mfaActiveMethod == 'email'} ({lng p="mfa_method_email_active"}){else} ({lng p="mfa_method_totp_active"}){/if}
						{if $mfaEnabledAtFormatted != ''}<br /><small class="text-secondary">{lng p="mfa_active_since_label"} {$mfaEnabledAtFormatted}</small>{/if}
					{else}
						{lng p="mfa_status_off"}
						{if $mfaSetupRequired} <span class="text-warning">({lng p="mfa_setup_pending"})</span>{/if}
					{/if}
				</div>
				<button type="button" class="btn btn-outline-danger btn-sm mt-2" onclick="if(confirm('{lng p="mfa_reset_admin_confirm"}')) document.getElementById('adminResetMfaForm').submit();">{lng p="mfa_reset_btn"}</button>
			</div>
		</div>
	</fieldset>
	{/if}

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
<form id="adminResetMfaForm" method="post" action="{sessionurl file='admins.php' params="action=admins&do=edit&id={$admin.adminid}&resetMfa=1"}" class="d-none">
	{csrffield}
	<input type="hidden" name="resetMfa" value="1" />
</form>
