<div class="page page-center">
	<div class="container container-tight py-4">
		<div class="card card-md">
			<div class="card-body">
				<h2 class="h2 text-center mb-4">{lng p="lostpw"}</h2>
				<p class="text-secondary">{lng p="lostpwhint"}</p>

				<form method="post" action="{if $nliUrlLostPassword}{$nliUrlLostPassword}{else}/lost-password{/if}" autocomplete="on">
					{csrffield}
					<input type="hidden" name="action" value="lostPassword" />

					{if $domain_combobox}
					<div class="mb-3">
						<label class="form-label" for="email_local_lpw">{lng p="email"}</label>
						<div class="input-group nli-domain-group">
							<input type="text" name="email_local" id="email_local_lpw" class="form-control" placeholder="{lng p="email"}" required="true" autocomplete="username" />
							<input type="hidden" name="email_domain" data-bind="email-domain" value="{domain value=$domainList[0]}" />
							<button type="button" class="btn btn-outline-secondary dropdown-toggle nli-domain-btn" data-bs-toggle="dropdown"><span data-bind="label">@{domain value=$domainList[0]}</span></button>
							<ul class="dropdown-menu dropdown-menu-end domainMenu">
								{foreach from=$domainList item=domain key=_key}<li{if $_key==0} class="active"{/if}><a class="dropdown-item" href="#">@{domain value=$domain}</a></li>{/foreach}
							</ul>
						</div>
					</div>
					{else}
					<div class="mb-3">
						<label class="form-label" for="email_full_lpw">{lng p="email"}</label>
						<input type="email" name="email_full" id="email_full_lpw" class="form-control" placeholder="{lng p="email"}" required="true" autocomplete="username" />
					</div>
					{/if}

					<div class="form-footer">
						<button type="submit" class="btn btn-primary w-100">{lng p="requestpw"}</button>
					</div>
				</form>

				<div class="text-center mt-3">
					<a href="{$nliUrlLogin}{$sessionUrlSuffix}">{lng p="back"}</a>
				</div>
			</div>
		</div>
	</div>
</div>
