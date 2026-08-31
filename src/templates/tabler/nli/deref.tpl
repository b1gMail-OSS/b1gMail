<!doctype html>
<html lang="{lng p="langCode_editor"|default:'de'}">

<head>
	<meta charset="{$charset}" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<meta name="robots" content="noindex" />
	<title>{$service_title} - {lng p="dsDerefTitle"}</title>

	<link rel="shortcut icon" type="image/png" href="{$selfurl}res/favicon.png" />
	<link rel="stylesheet" href="{$tpldir}css/tabler.min.css?{fileDateSig file="css/tabler.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/tabler-icons.min.css?{fileDateSig file="css/tabler-icons.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/inter.css?{fileDateSig file="css/inter.css"}" />
	<link rel="stylesheet" href="{$tpldir}style/tabler-custom.css?{fileDateSig file="style/tabler-custom.css"}" />
	<link rel="stylesheet" href="{$selfurl}plugins/css/datenschutz.css?ver={$dsPluginVersion|default:'1.5.0'}" />
</head>

<body class="border-top-wide border-primary nli-standalone d-flex flex-column">

<div class="page page-center flex-fill">
	<div class="container-tight py-4">
		{include file="nli/login.brand.tpl"}

		<div class="card bm-ds-deref-card">
			<div class="card-body">
				{if $deref_url_status == 'unsafe'}
				<div class="alert alert-danger mb-4 bm-deref-webrisk" role="alert">
					<div class="alert-icon">
						<i class="ti ti-alert-triangle icon alert-icon icon-2" aria-hidden="true"></i>
					</div>
					<div>
						<h4 class="alert-heading">{lng p="dsDerefUnsafeTitle"}</h4>
						<div class="alert-description">{lng p="dsDerefUnsafeText"}</div>
						{if $deref_url_threat == 'MALWARE'}
						<div class="text-secondary small mt-2">{lng p="dsDerefThreatLabel"}: {lng p="dsDerefThreatMalware"}</div>
						{elseif $deref_url_threat == 'SOCIAL_ENGINEERING'}
						<div class="text-secondary small mt-2">{lng p="dsDerefThreatLabel"}: {lng p="dsDerefThreatPhishing"}</div>
						{elseif $deref_url_threat == 'UNWANTED_SOFTWARE'}
						<div class="text-secondary small mt-2">{lng p="dsDerefThreatLabel"}: {lng p="dsDerefThreatUnwanted"}</div>
						{/if}
					</div>
				</div>
				{elseif $deref_url_status == 'safe'}
				<div class="alert alert-success mb-4 bm-deref-webrisk" role="alert">
					<div class="alert-icon">
						<i class="ti ti-circle-check icon alert-icon icon-2" aria-hidden="true"></i>
					</div>
					<div>
						<h4 class="alert-heading">{lng p="dsDerefSafeTitle"}</h4>
						<div class="alert-description">{lng p="dsDerefSafeText"}</div>
					</div>
				</div>
				{elseif $deref_url_status == 'invalid'}
				<div class="alert alert-warning mb-4 bm-deref-webrisk" role="alert">
					<div class="alert-icon">
						<i class="ti ti-alert-circle icon alert-icon icon-2" aria-hidden="true"></i>
					</div>
					<div>
						<h4 class="alert-heading">{lng p="dsDerefInvalidTitle"}</h4>
						<div class="alert-description">{lng p="dsDerefInvalidText"}</div>
					</div>
				</div>
				{elseif $deref_url_status == 'unavailable'}
				<div class="alert alert-warning mb-4 bm-deref-webrisk" role="alert">
					<div class="alert-icon">
						<i class="ti ti-shield-off icon alert-icon icon-2" aria-hidden="true"></i>
					</div>
					<div>
						<h4 class="alert-heading">{lng p="dsDerefUnavailableTitle"}</h4>
						<div class="alert-description">{lng p="dsDerefUnavailableText"}</div>
					</div>
				</div>
				{else}
				<div class="alert alert-info mb-4" role="alert">
					<div class="alert-icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2" aria-hidden="true">
							<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
							<path d="M12 9h.01"></path>
							<path d="M11 12h1v4h1"></path>
						</svg>
					</div>
					<div>
						<h4 class="alert-heading">{lng p="dsDerefTitle"}</h4>
						<div class="alert-description">{lng p="dsDerefRedirect"}</div>
					</div>
				</div>
				{/if}

				<div class="mb-4">
					<label class="form-label text-secondary">{lng p="dsDerefLinkLabel"}</label>
					<div class="bm-deref-url-display{if $deref_url_status == 'unsafe'} bm-deref-url-display-danger{/if}">
						{if $deref_host}
						<div class="bm-deref-url-host">
							<i class="ti ti-world icon icon-2" aria-hidden="true"></i>
							<span class="bm-deref-url-hostname">{text value=$deref_host}</span>
						</div>
						{/if}
						<div class="bm-deref-url-body">
							{if $deref_url_status == 'unsafe'}
							<span class="bm-deref-url-link bm-deref-url-link-static" title="{text value=$url}">{text value=$url cut=512}</span>
							{else}
							<a href="{$url}" class="bm-deref-url-link" rel="noreferrer nofollow noopener" title="{text value=$url}">{text value=$url cut=512}</a>
							{/if}
						</div>
					</div>
				</div>

				<div class="d-flex flex-wrap gap-2">
					{if $deref_url_status == 'unsafe'}
					<a href="{$url}" class="btn btn-outline-danger" rel="noreferrer nofollow">
						<i class="ti ti-external-link icon icon-2" aria-hidden="true"></i>
						{lng p="dsDerefContinueAnyway"}
					</a>
					{else}
					<a href="{$url}" class="btn btn-primary" rel="noreferrer nofollow">
						<i class="ti ti-external-link icon icon-2" aria-hidden="true"></i>
						{lng p="dsDerefContinue"}
					</a>
					{/if}
					<button type="button" class="btn btn-outline-secondary" onclick="window.close()">
						<i class="ti ti-x icon icon-2" aria-hidden="true"></i>
						{lng p="dsDerefClose"}
					</button>
				</div>

				<p class="text-secondary small mt-4 mb-0">{lng p="dsDerefBack"}</p>
			</div>
		</div>

		<div class="text-center text-secondary mt-4 small">
			&copy; {$service_title}
		</div>
	</div>
</div>

{include file="nli/standalone.close.tpl"}
