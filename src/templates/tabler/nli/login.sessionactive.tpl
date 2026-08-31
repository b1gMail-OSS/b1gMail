<div class="text-center mb-4">
	<span class="avatar avatar-lg bg-success-lt mb-3">
		<i class="ti ti-check" aria-hidden="true"></i>
	</span>
	<h2 class="h3 mb-2">{lng p="session_active_title"}</h2>
	{if $sessionActiveUser}<p class="text-secondary mb-0">{email value=$sessionActiveUser cut=50}</p>{/if}
</div>
<div class="form-footer">
	<a href="{$sessionActiveUrl}" class="btn btn-primary w-100">
		<i class="ti ti-arrow-right me-1" aria-hidden="true"></i>
		{lng p="session_active_continue"}
	</a>
</div>
<div class="text-center mt-3">
	<a href="{$sessionActiveLogoutUrl}" class="text-secondary">{lng p="logout"}</a>
</div>
