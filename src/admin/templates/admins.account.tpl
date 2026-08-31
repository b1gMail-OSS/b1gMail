<div class="row">
	<div class="col-md-6">
		<fieldset>
			<legend>{lng p="loggedinas"}</legend>

			<div class="mb-3 row">
				<label class="col-sm-4 col-form-label">{lng p="username"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{text value=$adminRow.username}</div>
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-4 col-form-label">{lng p="name"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{text value=$adminRow.firstname} {text value=$adminRow.lastname}</div>
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-4 col-form-label">{lng p="status"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{if $adminRow.type==0}{lng p="superadmin"}{else}{lng p="admin"}{/if}</div>
				</div>
			</div>
		</fieldset>

		<fieldset class="mt-3">
			<legend>{lng p="admin_email"}</legend>
			{if isset($emailSaved) && $emailSaved}
				<div class="alert alert-success" role="alert">{lng p="saveok"}</div>
			{/if}
			{if isset($emailError)}
				<div class="alert alert-danger" role="alert">{$emailError}</div>
			{/if}
			<form action="{sessionurl file='admins.php' params="saveEmail=true"}" method="post" onsubmit="spin(this)">
				{csrffield}
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label" for="admin_email">{lng p="admin_email"}</label>
					<div class="col-sm-8">
						<input type="email" class="form-control" id="admin_email" name="email" value="{text allowEmpty=true value=$adminRow.email}" placeholder="{lng p="admin_email"}">
						<small class="form-hint">{lng p="admin_email_hint"}</small>
					</div>
				</div>
				<div class="text-end"><input class="btn btn-primary" type="submit" value="{lng p="save"}" /></div>
			</form>
		</fieldset>
	</div>
	<div class="col-md-6">
		{include file="admins.avatar.tpl"}
		<fieldset class="mt-3">
			<legend>{lng p="password"}</legend>

			<form action="{sessionurl file='admins.php' params="changePassword=true"}" method="post" onsubmit="spin(this)" autocomplete="off">
				{csrffield}
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="newpassword"}</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" name="newpw1" placeholder="{lng p="newpassword"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="newpassword"} ({lng p="repeat"})</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" name="newpw2" placeholder="{lng p="newpassword"} ({lng p="repeat"})">
					</div>
				</div>
				<div class="text-end"><input class="btn btn-primary" type="submit" value="{lng p="save"}" /></div>
			</form>
		</fieldset>
	</div>
</div>

{if $mfaAdminMayManage}
{include file="admins.mfa.tpl"}
{/if}