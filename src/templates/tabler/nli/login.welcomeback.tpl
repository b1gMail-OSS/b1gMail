<div class="text-center mb-4">
	<span class="avatar avatar-lg bg-primary-lt mb-3">
		<i class="ti ti-user" aria-hidden="true"></i>
	</span>
	<h2 class="h3 mb-2">{lng p="welcome"}</h2>
	<p class="text-secondary mb-0">{text value=$smarty.cookies.bm_savedUser}</p>
</div>
<form action="{$nliUrlLogin}" method="post">
	<input type="hidden" name="do" value="login" />
	<input type="hidden" name="timezone" value="{$timezone}" />
	{csrffield}
	<input type="hidden" name="email_full" value="{$smarty.cookies.bm_savedUser}" />
	<input type="hidden" name="password" value="" />
	{if $smarty.cookies.bm_savedSSL}<input type="hidden" name="ssl" value="true" />{/if}
	<div class="form-footer">
		<button type="submit" class="btn btn-primary w-100">
			<i class="ti ti-login me-1" aria-hidden="true"></i>
			{lng p="login"}
		</button>
	</div>
</form>
<div class="text-center mt-3">
	<a href="{$nliUrlForgetCookie}" class="text-secondary">{lng p="logout"}</a>
</div>
