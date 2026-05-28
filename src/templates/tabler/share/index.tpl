{capture assign="standalonePageTitle"}{$service_title} - {lng p="sharing"}{if $userMail} - {$userMail}{/if}{/capture}
<!doctype html>
<html lang="{lng p="langCode_editor"|default:'en'}">

<head>
	<meta charset="{$charset}" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<meta name="robots" content="noindex" />
	<title>{$standalonePageTitle}</title>

	<link rel="shortcut icon" type="image/png" href="{$selfurl}res/favicon.png" />
	<link rel="icon" href="{$selfurl}favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="{$tpldir}css/tabler.min.css?{fileDateSig file="css/tabler.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/tabler-icons.min.css?{fileDateSig file="css/tabler-icons.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/inter.css?{fileDateSig file="css/inter.css"}" />
	<link rel="stylesheet" href="{$tpldir}style/tabler-custom.css?{fileDateSig file="style/tabler-custom.css"}" />
	<link rel="stylesheet" href="{$tpldir}style/share.css?{fileDateSig file="style/share.css"}" />

	<script src="{$selfurl}clientlang.php"></script>
	<script src="{$tpldir}js/common.js?{fileDateSig file="js/common.js"}"></script>
	<script src="{$selfurl}clientlib/overlay.js"></script>
	<script src="{$selfurl}clientlib/share.js?{fileDateSig file="../../clientlib/share.js"}"></script>
</head>

<body class="border-top-wide border-primary nli-standalone bm-share-page d-flex flex-column"{if !$error} onload="shareInit('{$user|escape:'javascript'}','{$selfurl}{$_tpldir}', true, '{$fileToken|default:''|escape:'javascript'}')"{/if}>

	<div class="page page-center flex-fill">
		<div class="container-xl bm-share-container py-4">

			{if $banner}
				<div class="text-center mb-3 bm-share-banner">
					{banner}
				</div>
			{/if}

			<div class="text-center mb-4">
				<a href="{$selfurl}" class="bm-share-brand text-reset text-decoration-none">
					<span class="avatar avatar-lg bg-primary-lt text-primary mb-3">
						<i class="ti ti-share icon icon-lg" aria-hidden="true"></i>
					</span>
					<div class="h2 mb-1">{$service_title}</div>
					<div class="fs-4 text-secondary">{lng p="sharing"}{if $userMail} &middot; {$userMail|escape}{/if}</div>
				</a>
			</div>

		{if $error}
			<div class="card card-md">
				<div class="card-body">
					<div class="alert alert-danger mb-0" role="alert">
						<div class="d-flex">
							<div>
								<i class="ti ti-alert-circle icon alert-icon" aria-hidden="true"></i>
							</div>
							<div>
								<h4 class="alert-title">{$title|escape}</h4>
								<div class="text-secondary">{$msg}</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		{else}
			<div class="card bm-share-card shadow-sm" id="mainLayer">
				<div id="locationBar" class="bm-share-breadcrumb border-bottom px-4 py-3" aria-label="Pfad"></div>

				<div id="contentLayer" class="table-responsive">
					<table class="table table-lg table-vcenter table-hover table-striped mb-0" cellspacing="0" cellpadding="0" id="contentTable">
						<thead>
							<tr>
								<th>{lng p="title"}</th>
								<th class="bm-share-col-date">{lng p="modified"}</th>
								<th class="bm-share-col-size text-end">{lng p="size"}</th>
								<th class="bm-share-col-actions text-end">&nbsp;</th>
							</tr>
						</thead>
						<tbody id="shareContentBody"></tbody>
					</table>
				</div>
			</div>
		{/if}

			<div class="text-center text-secondary mt-4 small">
				{if isset($year)}&copy; {$year} {/if}{$service_title}
			</div>
		</div>
	</div>

	<script src="{$tpldir}js/tabler.min.js?{fileDateSig file="js/tabler.min.js"}" defer></script>
</body>
</html>
