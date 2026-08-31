<div class="row g-0 flex-fill nli-login-cover">
	<div class="col-12 col-lg-6 col-xl-4 border-top-wide border-primary d-flex flex-column justify-content-center">
		<div class="container container-tight my-5 px-lg-5">
			{include file="nli/login.brand.tpl"}

			{if $sessionActive}
			{include file="nli/login.sessionactive.tpl"}
			{include file="nli/login.cover-footer.tpl"}
			{elseif $welcomeBack}
			{include file="nli/login.welcomeback.tpl"}
			{include file="nli/login.cover-footer.tpl"}
			{else}
			<h2 class="h3 text-center mb-3">{lng p="welcome"}</h2>
			{include file="nli/login.form.tpl"}
			{include file="nli/login.cover-footer.tpl"}
			{/if}

			<div class="text-center text-secondary mt-4 small">
				<div>&copy; {$year} {$service_title}</div>
				<div class="mt-1">
					<a href="{$nliUrlFaq}" class="text-secondary">{lng p="faq"}</a>
					<span class="mx-1">·</span>
					<a href="{$nliUrlImprint}" class="text-secondary">{lng p="contact"}</a>
					<span class="mx-1">·</span>
					<a href="{$mobileURL}" class="text-secondary">{lng p="mobilepda"}</a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block p-0">
		<div class="bg-cover h-100 min-vh-100" style="background-image: url('{$tpldir}images/nli/{$templatePrefs.splashImage}');"></div>
	</div>
</div>
