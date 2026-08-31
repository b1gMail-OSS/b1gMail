<div class="page page-center">
	<div class="container container-tight py-4">
		{include file="nli/login.brand.tpl"}

		{if $sessionActive}
		<div class="card card-md">
			<div class="card-body">
				{include file="nli/login.sessionactive.tpl"}
			</div>
			{include file="nli/login.card-footer.tpl"}
		</div>
		{elseif $welcomeBack}
		<div class="card card-md">
			<div class="card-body">
				{include file="nli/login.welcomeback.tpl"}
			</div>
			{include file="nli/login.card-footer.tpl"}
		</div>
		{else}
		<div class="card card-md">
			<div class="card-body">
				<h2 class="h2 text-center mb-4">{lng p="welcome"}</h2>
				{include file="nli/login.form.tpl"}
			</div>
			{include file="nli/login.card-footer.tpl"}
		</div>
		{/if}

		<div class="text-center text-secondary mt-3 small">
			<div>&copy; {$year} {$service_title}</div>
			<div class="mt-1">
				<a href="{$nliUrlFaq}" class="text-secondary">{lng p="faq"}</a>
				<span class="mx-1">·</span>
				<a href="{$nliUrlTos}" class="text-secondary">{lng p="tos"}</a>
				<span class="mx-1">·</span>
				<a href="{$nliUrlImprint}" class="text-secondary">{lng p="contact"}</a>
			</div>
			<div class="mt-1">
				powered by <a href="https://www.b1gmail.eu/" target="_blank" rel="noreferrer" class="text-secondary">b1gMail.eu</a>
			</div>
		</div>
	</div>
</div>
