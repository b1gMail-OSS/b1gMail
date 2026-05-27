<div class="card-footer">
	<div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
		{include file="nli/lang.dropdown.tpl"}
		{if $_regEnabled||(!$templatePrefs.hideSignup)}
		<div class="text-end">
			<a href="{if $ssl_signup_enable}{$ssl_url}{/if}index.php?action=signup" class="btn btn-sm btn-primary">
				<i class="ti ti-user-plus me-1" aria-hidden="true"></i>
				{lng p="signup"}
			</a>
		</div>
		{/if}
	</div>
</div>
