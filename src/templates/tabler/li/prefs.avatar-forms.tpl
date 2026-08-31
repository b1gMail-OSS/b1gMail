<form id="bmAvatarUploadForm" method="post" action="{sessionurl file='prefs.php' params='action=avatar&do=upload'}" enctype="multipart/form-data" class="d-none" aria-hidden="true">
	{csrffield}
	<input type="hidden" name="action" value="avatar" />
	<input type="hidden" name="do" value="upload" />
</form>
<form id="bmAvatarDeleteForm" method="post" action="{sessionurl file='prefs.php' params='action=avatar&do=delete'}" class="d-none" aria-hidden="true">
	{csrffield}
	<input type="hidden" name="action" value="avatar" />
	<input type="hidden" name="do" value="delete" />
</form>
