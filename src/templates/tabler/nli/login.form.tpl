<form action="{if $ssl_login_enable}{$ssl_url}{/if}index.php?action=login" method="post" id="loginFormMain" autocomplete="on">
	<input type="hidden" name="do" value="login" />
	<input type="hidden" name="timezone" value="{$timezone}" />

	<div class="alert alert-danger" style="display:none;" role="alert"></div>

	{if $domain_combobox}
	<div class="mb-3">
		<label class="form-label" for="email_local">{lng p="email"}</label>
		<div class="input-group nli-domain-group">
			<input type="text" name="email_local" id="email_local" class="form-control" placeholder="{lng p="email"}" required="true" autocomplete="username" />
			<input type="hidden" name="email_domain" data-bind="email-domain" value="{domain value=$domainList[0]}" />
			<button type="button" class="btn btn-outline-secondary dropdown-toggle nli-domain-btn" data-bs-toggle="dropdown" aria-expanded="false">
				<span data-bind="label">@{domain value=$domainList[0]}</span>
			</button>
			<ul class="dropdown-menu dropdown-menu-end domainMenu">
				{foreach from=$domainList item=domain key=key}
				<li{if $key==0} class="active"{/if}><a class="dropdown-item" href="#">@{domain value=$domain}</a></li>
				{/foreach}
			</ul>
		</div>
	</div>
	{else}
	<div class="mb-3">
		<label class="form-label" for="email_full">{lng p="email"}</label>
		<input type="email" name="email_full" id="email_full" class="form-control" placeholder="{lng p="email"}" required="true" autocomplete="username" />
	</div>
	{/if}

	<div class="mb-2">
		<label class="form-label" for="password">
			{lng p="password"}
			<span class="form-label-description">
				<a href="#" data-bs-toggle="modal" data-bs-target="#lostPW">{lng p="lostpw"}?</a>
			</span>
		</label>
		<div class="input-group input-group-flat">
			<input type="password" name="password" id="password" class="form-control" placeholder="{lng p="password"}" required="true" autocomplete="current-password" />
			<span class="input-group-text">
				<a href="#" class="link-secondary" title="{lng p="password"}" data-nli-toggle-password="password" aria-label="{lng p="password"}">
					<i class="ti ti-eye" aria-hidden="true"></i>
				</a>
			</span>
		</div>
	</div>

	<div class="mb-2">
		<label class="form-check">
			<input type="checkbox" class="form-check-input" name="savelogin" id="savelogin" />
			<span class="form-check-label">{lng p="savelogin"}</span>
		</label>
	</div>

	{if $ssl_login_option}
	<div class="mb-3">
		<label class="form-check">
			<input type="checkbox" class="form-check-input" id="ssl"{if $ssl_login_enable} checked="checked"{/if} onchange="updateFormSSL(this)" onclick="updateFormSSL(this)" />
			<span class="form-check-label">{lng p="ssl"}</span>
		</label>
	</div>
	{/if}

	<div class="form-footer">
		<button type="submit" class="btn btn-primary w-100">{lng p="login"}</button>
	</div>
</form>
