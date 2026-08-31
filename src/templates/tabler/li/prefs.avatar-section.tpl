		<tr>
			<td class="listTableLeftDesc"><i class="ti ti-photo" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="avatar_upload"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="avatar_source">{lng p="avatar_source"}:</label></td>
			<td class="listTableRight">
				<select name="avatar_source" id="avatar_source">
					<option value="initials"{if $avatar_source=='initials'} selected="selected"{/if}>{lng p="avatar_source_initials"}</option>
					<option value="upload"{if $avatar_source=='upload'} selected="selected"{/if}>{lng p="avatar_source_upload"}</option>
					<option value="libravatar"{if $avatar_source=='libravatar'} selected="selected"{/if}>{lng p="avatar_source_libravatar"}</option>
					<option value="gravatar"{if $avatar_source=='gravatar'} selected="selected"{/if}>{lng p="avatar_source_gravatar"}</option>
					<option value="libravatar_gravatar_initials"{if $avatar_source=='libravatar_gravatar_initials'} selected="selected"{/if}>{lng p="avatar_source_libravatar_gravatar_initials"}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="bmAvatarFileInput">{lng p="avatar_upload"}:</label></td>
			<td class="listTableRight">
				<div class="d-flex flex-wrap align-items-center gap-3 mb-2">
					{include file="li/user-avatar.tpl" avatarSize="lg" avatarMode=$avatarDisplayMode}
				</div>
				{if isset($avatarMessage) && $avatarMessage != ''}
					<div class="alert alert-info mb-2" role="alert">{$avatarMessage}</div>
				{/if}
				{if isset($avatarError) && $avatarError != ''}
					<div class="alert alert-danger mb-2" role="alert">{$avatarError}</div>
				{/if}
				<div class="input-group bm-prefs-avatar-upload-row mb-2">
					<input type="file" class="form-control" id="bmAvatarFileInput" name="avatar_file" form="bmAvatarUploadForm" accept="image/jpeg,image/png,image/gif,image/webp"{if !$avatarHasCustom} required="required"{/if} />
					<button type="submit" class="btn btn-primary" form="bmAvatarUploadForm">{lng p="avatar_upload_btn"}</button>
					{if $avatarHasCustom}
					<button type="submit" class="btn btn-danger" form="bmAvatarDeleteForm" onclick="return confirm('{lng p="avatar_delete_confirm"}');">{lng p="avatar_delete_btn"}</button>
					{/if}
				</div>
				<small class="form-hint d-block">{lng p="avatar_upload_hint"}</small>
			</td>
		</tr>
