		<fieldset class="mt-3">
			<legend>{lng p="avatar_upload"}</legend>
			{if isset($avatarSaved) && $avatarSaved}
				<div class="alert alert-success" role="alert">{lng p="avatar_upload_ok"}</div>
			{/if}
			{if isset($avatarDeleted) && $avatarDeleted}
				<div class="alert alert-success" role="alert">{lng p="avatar_delete_ok"}</div>
			{/if}
			{if isset($avatarError) && $avatarError != ''}
				<div class="alert alert-danger" role="alert">{$avatarError}</div>
			{/if}
			<div class="mb-3">
				<label class="form-label" for="admin_avatar_source">{lng p="avatar_source"}</label>
				<select class="form-select" id="admin_avatar_source" disabled="disabled">
					<option selected="selected">{lng p="avatar_source_upload"}</option>
				</select>
				<small class="form-hint">{lng p="avatar_upload_hint"}</small>
			</div>
			<div class="mb-3 d-flex align-items-center gap-3">
				{include file="user-avatar.tpl" avatarSize="xl" avatarMode=$adminAvatarDisplayMode avatarBgPrimary=true}
			</div>
			<form action="{sessionurl file='admins.php'}" method="post" enctype="multipart/form-data" class="mb-2">
				{csrffield}
				<input type="hidden" name="action" value="account" />
				<div class="input-group bm-admin-avatar-upload">
					<input type="file" class="form-control" id="admin_avatar_file" name="avatar_file" accept="image/jpeg,image/png,image/gif,image/webp"{if !$adminAvatarHasCustom} required="required"{/if} />
					<button type="submit" class="btn btn-primary" name="avatarUpload" value="1">{lng p="avatar_upload_btn"}</button>
					{if $adminAvatarHasCustom}
					<button type="submit" class="btn btn-danger" name="avatarDelete" value="1" onclick="return confirm('{lng p="avatar_delete_confirm"}');">{lng p="avatar_delete_btn"}</button>
					{/if}
				</div>
			</form>
		</fieldset>
