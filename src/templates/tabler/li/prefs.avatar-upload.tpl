<div class="bm-prefs-avatar-block">
	<table class="listTable">
		<tr>
			<td class="listTableLeftDesc"><i class="ti ti-photo" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="avatar_upload"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label>{lng p="avatar_upload"}:</label></td>
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
				<form method="post" action="{sessionurl file='prefs.php' params='action=avatar&do=upload'}" enctype="multipart/form-data" class="mb-2">
					{csrffield}
					<input type="hidden" name="action" value="avatar" />
					<input type="hidden" name="do" value="upload" />
					<div class="bm-prefs-avatar-upload-row">
						<input type="file" class="form-control" name="avatar_file" accept="image/jpeg,image/png,image/gif,image/webp" required="required" />
						<button type="submit" class="btn btn-primary">{lng p="avatar_upload_btn"}</button>
					</div>
					<small class="form-hint d-block mt-1">{lng p="avatar_upload_hint"}</small>
				</form>
				{if $avatarHasCustom}
				<form method="post" action="{sessionurl file='prefs.php' params='action=avatar&do=delete'}" class="d-inline">
					{csrffield}
					<input type="hidden" name="action" value="avatar" />
					<input type="hidden" name="do" value="delete" />
					<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{lng p="avatar_delete_confirm"}');">{lng p="avatar_delete_btn"}</button>
				</form>
				{/if}
			</td>
		</tr>
	</table>
</div>
