<div class="container">
	<div class="page-header"><h1>{lng p="lostpw"}</h1></div>

	<p>{lng p="lostpwhint"}</p>

	<form method="post" action="{if $nliUrlLostPassword}{$nliUrlLostPassword}{else}/lost-password{/if}" autocomplete="on" style="max-width:420px;">
		{csrffield}
		<input type="hidden" name="action" value="lostPassword" />

		{if $domain_combobox}
		<div class="form-group">
			<label class="control-label" for="email_local_lpw">{lng p="email"}</label>
			<div class="input-group">
				<input type="text" name="email_local" id="email_local_lpw" class="form-control" placeholder="{lng p="email"}" required="true" />
				<div class="input-group-btn">
					<input type="hidden" name="email_domain" data-bind="email-domain" value="{domain value=$domainList[0]}" />
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span data-bind="label">@{domain value=$domainList[0]}</span> <span class="caret"></span></button>
					<ul class="dropdown-menu dropdown-menu-right domainMenu" role="menu">
						{foreach from=$domainList item=domain key=_key}<li{if $_key==0} class="active"{/if}><a href="#">@{domain value=$domain}</a></li>{/foreach}
					</ul>
				</div>
			</div>
		</div>
		{else}
		<div class="form-group">
			<label class="control-label" for="email_full_lpw">{lng p="email"}</label>
			<input type="email" name="email_full" id="email_full_lpw" class="form-control" placeholder="{lng p="email"}" required="true" />
		</div>
		{/if}

		<div class="form-group">
			<button type="submit" class="btn btn-success">{lng p="requestpw"}</button>
		</div>
	</form>

	<p><a href="{$nliUrlLogin}{$sessionUrlSuffix}">&larr; {lng p="back"}</a></p>
</div>
