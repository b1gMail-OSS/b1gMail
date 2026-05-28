{if $webdiskForbiddenExtensions|@count > 0 || $webdiskForbiddenMimetypes|@count > 0}
<div class="alert alert-info bm-webdisk-upload-limits-alert py-2 px-3 mb-3" role="note">
	<div class="small mb-0">
		<strong>{lng p="wd_upload_restrictions"}</strong>
		{if $webdiskForbiddenExtensions|@count > 0}
		<div class="mt-1">{lng p="wd_upload_forbidden_ext"}: {text value=$webdiskForbiddenExtensionsList allowEmpty=true}</div>
		{/if}
		{if $webdiskForbiddenMimetypes|@count > 0}
		<div class="mt-1">{lng p="wd_upload_forbidden_mime"}: {text value=$webdiskForbiddenMimetypesList allowEmpty=true}</div>
		{/if}
	</div>
</div>
{/if}
