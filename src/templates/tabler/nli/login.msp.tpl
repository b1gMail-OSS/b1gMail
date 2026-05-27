<div class="page-body nli-msp-layout">
	<div class="container-xl py-4">
		<div class="text-center mb-4">
			{include file="nli/login.brand.tpl"}
		</div>

		{if $welcomeBack}
		<div class="row justify-content-center">
			<div class="col-md-6 col-lg-5">
				<div class="card card-md">
					{include file="nli/msp.tabs.tpl"}
					<div class="card-body">
						{include file="nli/login.welcomeback.tpl"}
					</div>
				</div>
			</div>
		</div>
		{else}
		<div class="row g-4 align-items-stretch">
			<div class="col-lg-7">
				<div class="card h-100">
					{include file="nli/msp.tabs.tpl"}
					<div class="card-body">
						<h1 class="h2 mb-3">{lng p="welcome"}</h1>
						<p class="text-secondary mb-4">{lng p="signuptxt"}</p>

						{if $_regEnabled||(!$templatePrefs.hideSignup)}
						<div class="card bg-primary-lt border-0 mb-4">
							<div class="card-body">
								<h3 class="h4 mb-2">
									<i class="ti ti-user-plus me-1 text-primary" aria-hidden="true"></i>
									{lng p="signup"}
								</h3>
								<div class="text-secondary mb-3">{lng p="notmembertxt"}</div>
								<a href="{if $ssl_signup_enable}{$ssl_url}{/if}index.php?action=signup" class="btn btn-primary">
									{lng p="signup"}
								</a>
							</div>
						</div>
						{/if}

						<div class="mt-4">{banner}</div>
					</div>
				</div>
			</div>

			<div class="col-lg-5">
				<div class="card card-md h-100">
					<div class="card-body">
						<h2 class="h2 text-center mb-4">{lng p="login"}</h2>
						{include file="nli/login.form.tpl"}
					</div>
					{if $_regEnabled||(!$templatePrefs.hideSignup)}
					<div class="card-footer text-center text-secondary">
						{lng p="notmember"}?
						<a href="{if $ssl_signup_enable}{$ssl_url}{/if}index.php?action=signup">{lng p="signup"}</a>
					</div>
					{/if}
				</div>
			</div>
		</div>
		{/if}

		{include file="nli/msp.footer.tpl"}
	</div>
</div>
