<div class="d-flex align-items-center justify-content-between gap-3 mt-4 pt-3 nli-cover-footer">
	{assign var="langDropdownClass" value="link-secondary"}
	{include file="nli/lang.dropdown.tpl"}
	{if $_regEnabled||(!$templatePrefs.hideSignup)}
	<a href="{if $ssl_signup_enable}{$nliUrlSignupSsl}{else}{$nliUrlSignup}{/if}" class="btn btn-primary">
		<i class="ti ti-user-plus me-1" aria-hidden="true"></i>
		{lng p="signup"}
	</a>
	{/if}
</div>
