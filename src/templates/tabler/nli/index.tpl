<!doctype html>
<html lang="{lng p="langCode"}">

<head>
	<meta charset="{$charset}" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
	{if isset($robotsNoIndex)}<meta name="robots" content="noindex" />{/if}

	<title>{$service_title}{if isset($pageTitle)} - {text value=$pageTitle}{/if}</title>

	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />

	<link rel="stylesheet" href="{$tpldir}css/tabler.min.css?{fileDateSig file="css/tabler.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/tabler-icons.min.css?{fileDateSig file="css/tabler-icons.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/inter.css?{fileDateSig file="css/inter.css"}" />
	<link rel="stylesheet" href="{$tpldir}style/bs3-compat.css?{fileDateSig file="style/bs3-compat.css"}" />
	<link rel="stylesheet" href="{$tpldir}style/tabler-custom.css?{fileDateSig file="style/tabler-custom.css"}" />
	<link rel="stylesheet" href="{$tpldir}style/notloggedin.css?{fileDateSig file="style/notloggedin.css"}" />

	<script type="text/javascript">
	<!--
		var tplDir = '{$tpldir}', sslURL = '{$ssl_url}', serverTZ = {$serverTZ};
	//-->
	</script>

	<script src="clientlang.php" type="text/javascript"></script>
	<script src="clientlib/jquery/jquery-3.7.1.min.js"></script>
	<script src="{$tpldir}js/tabler.min.js?{fileDateSig file="js/tabler.min.js"}"></script>
	<script type="text/javascript">
	<!--
		if(typeof bootstrap === 'undefined' && typeof tabler !== 'undefined' && tabler.bootstrap)
			window.bootstrap = tabler.bootstrap;
	//-->
	</script>
	<script src="{$tpldir}js/nli.main.js?{fileDateSig file="js/nli.main.js"}"></script>
	{hook id="nli:index.tpl:head"}
</head>

{include file="nli/layout.vars.tpl" scope=parent}
<body class="nli-body{if $nliCompactLayout && $page!='nli/login.tpl'} nli-msp-layout{/if}{if $page=='nli/login.tpl' && $nliStyle!='msp'} nli-login-layout nli-login-{$nliStyle|default:'cover'}{if $nliStyle=='cover' || $nliStyle=='minimal'} d-flex flex-column bg-white{/if}{/if}">
	{hook id="nli:index.tpl:beforeContent"}

	{if !$nliCompactLayout && $page!='nli/login.tpl'}
	<header class="navbar navbar-expand-md navbar-dark bg-dark d-print-none sticky-top">
		<div class="container-xl">
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nli-navbar-menu" aria-controls="nli-navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<a class="navbar-brand" href="index.php">
				<img src="{$tpldir}images/logo.png" border="0" alt="" class="navbar-brand-image me-2" style="height:24px;" />
				{$service_title}
			</a>
			<div class="collapse navbar-collapse" id="nli-navbar-menu">
				<ul class="navbar-nav">
					<li class="nav-item{if isset($smarty.request.action) && $smarty.request.action=='login'} active{/if}">
						<a class="nav-link" href="index.php">{lng p="home"}</a>
					</li>
				{foreach from=$pluginUserPages item=item}{if isset($item.top)&&$item.after=='login'}
					<li class="nav-item{if $item.active} active{/if}"><a class="nav-link" href="{$item.link}">{$item.text}</a></li>
				{/if}{/foreach}
					{if $_regEnabled||(!$templatePrefs.hideSignup)}
					<li class="nav-item{if isset($smarty.request.action) && $smarty.request.action=='signup'} active{/if}">
						<a class="nav-link" href="{if $ssl_signup_enable}{$ssl_url}{/if}index.php?action=signup">{lng p="signup"}</a>
					</li>
					{/if}
				{foreach from=$pluginUserPages item=item}{if isset($item.top)&&$item.after=='signup'}
					<li class="nav-item{if $item.active} active{/if}"><a class="nav-link" href="{$item.link}">{$item.text}</a></li>
				{/if}{/foreach}
					<li class="nav-item{if isset($smarty.request.action) && $smarty.request.action=='faq'} active{/if}">
						<a class="nav-link" href="index.php?action=faq">{lng p="faq"}</a>
					</li>
				{foreach from=$pluginUserPages item=item}{if isset($item.top)&&$item.after=='faq'}
					<li class="nav-item{if $item.active} active{/if}"><a class="nav-link" href="{$item.link}">{$item.text}</a></li>
				{/if}{/foreach}
					<li class="nav-item{if isset($smarty.request.action) && $smarty.request.action=='tos'} active{/if}">
						<a class="nav-link" href="index.php?action=tos">{lng p="tos"}</a>
					</li>
				{foreach from=$pluginUserPages item=item}{if isset($item.top)&&(!$item.after||$item.after=='tos')}
					<li class="nav-item{if $item.active} active{/if}"><a class="nav-link" href="{$item.link}">{$item.text}</a></li>
				{/if}{/foreach}
					<li class="nav-item{if isset($smarty.request.action) && $smarty.request.action=='imprint'} active{/if}">
						<a class="nav-link" href="index.php?action=imprint">{lng p="contact"}</a>
					</li>
				</ul>
				<form action="{if $ssl_login_enable||($welcomeBack&&$smarty.cookies.bm_savedSSL)}{$ssl_url}{/if}index.php?action=login" method="post" id="loginFormPopover" class="ms-md-auto d-flex align-items-center">
					<input type="hidden" name="do" value="login" />
					<input type="hidden" name="timezone" value="{$timezone}" />

					<ul class="navbar-nav flex-row gap-2">
						{if (isset($smarty.request.action) && $smarty.request.action!='login')||$welcomeBack}
						<li class="nav-item login-li{if !$welcomeBack} d-none d-md-block{/if}">
							{if $welcomeBack}
							<input type="hidden" name="email_full" value="{$smarty.cookies.bm_savedUser}" />
							<input type="hidden" name="password" value="" />
							<input type="hidden" name="savelogin" value="true" />
							{if $smarty.cookies.bm_savedSSL}<input type="hidden" name="ssl" value="true" />{/if}

							<div class="btn-group">
								<button type="submit" class="btn btn-primary">
									<i class="ti ti-user me-1"></i>
									{text value=$smarty.cookies.bm_savedUser cut=18}
								</button>
								<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
									<span class="visually-hidden">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu dropdown-menu-end">
									<li><a class="dropdown-item" href="index.php?action=forgetCookie">{lng p="logout"}</a></li>
								</ul>
							</div>
							{else}
							<div class="dropdown">
								<button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
									{lng p="login"}
								</button>
								<div class="dropdown-menu dropdown-menu-end p-3 login-li" id="loginPopoverDropdown">
									<div id="loginPopover">
										<div class="alert alert-danger" style="display:none;"></div>

										<div class="mb-3">
											<div class="input-group">
												<span class="input-group-text"><i class="ti ti-user"></i></span>
											{if $domain_combobox}
												<label class="visually-hidden" for="email_local_p">{lng p="email"}</label>
												<input type="text" name="email_local" id="email_local_p" class="form-control" placeholder="{lng p="email"}" required="true" />
												<input type="hidden" name="email_domain" data-bind="email-domain" value="{domain value=$domainList[0]}" />
												<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"><span data-bind="label">@{domain value=$domainList[0]}</span></button>
												<ul class="dropdown-menu dropdown-menu-end domainMenu">
													{foreach from=$domainList item=domain key=_key}<li{if $_key==0} class="active"{/if}><a class="dropdown-item" href="#">@{domain value=$domain}</a></li>{/foreach}
												</ul>
											{else}
												<label class="visually-hidden" for="email_full_p">{lng p="email"}</label>
												<input type="email" name="email_full" id="email_full_p" class="form-control" placeholder="{lng p="email"}" required="true" />
											{/if}
											</div>
										</div>
										<div class="mb-3">
											<div class="input-group">
												<span class="input-group-text"><i class="ti ti-lock"></i></span>
												<label class="visually-hidden" for="password_p">{lng p="password"}</label>
												<input type="password" name="password" id="password_p" class="form-control" placeholder="{lng p="password"}" required="true" />
											</div>
										</div>
										<label class="form-check mb-2">
											<input type="checkbox" class="form-check-input" name="savelogin" id="savelogin_p" />
											<span class="form-check-label">{lng p="savelogin"}</span>
										</label>
										{if $ssl_login_option}
										<label class="form-check mb-3">
											<input type="checkbox" class="form-check-input" id="ssl_p"{if $ssl_login_enable} checked="checked"{/if} onchange="updateFormSSL(this)" onclick="updateFormSSL(this)" />
											<span class="form-check-label">{lng p="ssl"}</span>
										</label>
										{/if}
										<button type="submit" class="btn btn-success w-100">{lng p="login"}</button>
										<div class="login-lostpw mt-2">
											<a href="#" data-bs-toggle="modal" data-bs-target="#lostPW">{lng p="lostpw"}?</a>
										</div>
									</div>
								</div>
							</div>
							{/if}
						</li>
						{/if}

						<li class="nav-item dropdown">
							<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">{foreach from=$languageList key=langKey item=langInfo}{if $langInfo.active}{$langInfo.title}{/if}{/foreach}</a>
							<ul class="dropdown-menu dropdown-menu-end">
								{foreach from=$languageList key=langKey item=langInfo}
								<li{if $langInfo.active} class="active"{/if}><a class="dropdown-item" href="index.php?action=switchLanguage&amp;lang={$langKey}{if !empty($smarty.get.action)}&amp;target={text value=$smarty.get.action}{/if}">{$langInfo.title}</a></li>
								{/foreach}
							</ul>
						</li>
					</ul>
				</form>
			</div>
		</div>
	</header>
	{/if}

	<div class="modal modal-blur fade" id="lostPW" tabindex="-1" aria-labelledby="lostPWLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
			<form action="index.php?action=lostPassword" method="post">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="lostPWLabel">{lng p="lostpw"}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{lng p="cancel"}"></button>
				</div>
				<div class="modal-body">
					<p class="text-muted mb-3">{lng p="lostpw"}</p>
					{if $domain_combobox}
					<div class="mb-3">
						<label class="form-label" for="email_local_lpw">{lng p="email"}</label>
						<div class="input-group nli-domain-group">
							<span class="input-group-text text-muted"><i class="ti ti-mail"></i></span>
							<input type="text" name="email_local" id="email_local_lpw" class="form-control" placeholder="{lng p="email"}" required="true" />
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
						<div class="input-group">
							<span class="input-group-text text-muted"><i class="ti ti-mail"></i></span>
							<input type="email" name="email_full" id="email_full_lpw" class="form-control" placeholder="{lng p="email"}" required="true" />
						</div>
					</div>
					{/if}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">{lng p="cancel"}</button>
					<button type="submit" class="btn btn-primary ms-auto">{lng p="requestpw"}</button>
				</div>
			</div>
			</form>
		</div>
	</div>

	{assign var="_nliPagePath" value=$page|default:''}
	{assign var="_nliPageIsPlugin" value=($_nliPagePath|replace:'plugins/templates':'' != $_nliPagePath)}
	{if $nliCompactLayout && ($nliWrapPlugin|default:true) && $_nliPageIsPlugin}
		{* Plugin-NLI: MSP-Rahmen (Tabs, Footer) – Inhalt nur im Plugin-Template *}
		{include file="nli/page.open.tpl"}
		{include file="$page"}
		{include file="nli/page.close.tpl"}
	{else}
		{include file="$page"}
	{/if}

	{if !$nliCompactLayout && $page!='nli/login.tpl'}
	<footer class="footer footer-transparent d-print-none border-top">
		<div class="container-xl">
			<div class="row text-secondary small py-3">
				<div class="col-md-4">&copy; {$year} {$service_title}</div>
				<div class="col-md-4 text-center">
					<a href="{$mobileURL}">{lng p="mobilepda"}</a>
					{foreach from=$pluginUserPages item=item}{if !$item.top}
					| <a href="{$item.link}">{$item.text}</a>
					{/if}{/foreach}
				</div>
				<div class="col-md-4 text-md-end">
					powered by <a href="https://www.b1gmail.eu/" target="_blank" rel="noreferrer">b1gMail.eu</a>
				</div>
			</div>
		</div>
	</footer>
	{/if}

	{hook id="nli:index.tpl:afterContent"}
</body>

</html>
