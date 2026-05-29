<!doctype html>
<html lang="{lng p="langCode"}">
{if $templatePrefs.enableDarkMode|default:false}
<script>
(function(){
	try {
		var t = localStorage.getItem('bm-tabler-theme');
		if(t === 'dark' || t === 'light')
			document.documentElement.setAttribute('data-bs-theme', t);
	} catch(e) {}
})();
</script>
{/if}

<head>
	<title>{if isset($pageTitle)}{text value=$pageTitle} - {/if}{$service_title}</title>

	<meta charset="{$charset}" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />

	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	{if $bmPushEnabled}
	<link rel="manifest" href="manifest.php" />
	<meta name="theme-color" content="#066fd1" />
	<meta name="mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	{/if}
	<link rel="stylesheet" href="{$tpldir}css/tabler.min.css?{fileDateSig file="css/tabler.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/tabler-icons.min.css?{fileDateSig file="css/tabler-icons.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/inter.css?{fileDateSig file="css/inter.css"}" />
	<link href="{$tpldir}style/loggedin.css?{fileDateSig file="style/loggedin.css"}" rel="stylesheet" type="text/css" />
	<link href="{$tpldir}style/dtree.css?{fileDateSig file="style/dtree.css"}" rel="stylesheet" type="text/css" />
	<link href="{$tpldir}style/tabler-custom.css?{fileDateSig file="style/tabler-custom.css"}" rel="stylesheet" type="text/css" />
	<link href="{$tpldir}style/legacy-addon.css?{fileDateSig file="style/legacy-addon.css"}" rel="stylesheet" type="text/css" />
	<link href="{$tpldir}libs/fontawesome/css/all.min.css?{fileDateSig file="libs/fontawesome/css/all.min.css"}" rel="stylesheet" type="text/css" />
	<link href="{$tpldir}libs/fontawesome/css/v4-shims.min.css?{fileDateSig file="libs/fontawesome/css/v4-shims.min.css"}" rel="stylesheet" type="text/css" />
	<link href="clientlib/fontawesome/css/font-awesome-animation.min.css?{fileDateSig file="../../clientlib/fontawesome/css/font-awesome-animation.min.css"}" rel="stylesheet" type="text/css" />
{foreach from=$_cssFiles.li item=_file}	<link rel="stylesheet" type="text/css" href="{$_file}" />
{/foreach}

	<script type="text/javascript">
	<!--
		var currentSID = '{$sid}', tplDir = '{$tpldir}', serverTZ = {$serverTZ}, ftsBGIndexing = {if $ftsBGIndexing}true{else}false{/if}{if $bmNotifyInterval},
			notifyInterval = {$bmNotifyInterval}, notifySound = {if $bmNotifySound}true{else}false{/if}{/if},
			bmEnableDarkMode = {if $templatePrefs.enableDarkMode|default:false}true{else}false{/if};
	//-->
	</script>
	<script src="clientlang.php?sid={$sid}" type="text/javascript"></script>
	<script src="{$tpldir}js/tabler.min.js?{fileDateSig file="js/tabler.min.js"}" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js?{fileDateSig file="js/common.js"}" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js?{fileDateSig file="js/loggedin.js"}" type="text/javascript"></script>
	<script src="{$tpldir}clientlib/dtree.js?{fileDateSig file="clientlib/dtree.js"}" type="text/javascript"></script>
	<script src="{$tpldir}clientlib/overlay.js?{fileDateSig file="clientlib/overlay.js"}" type="text/javascript"></script>
	<script src="{$tpldir}js/modal.js?{fileDateSig file="js/modal.js"}" type="text/javascript"></script>
	<script src="clientlib/autocomplete.js?{fileDateSig file="../../clientlib/autocomplete.js"}" type="text/javascript"></script>
	<script src="clientlib/favico.min.js?{fileDateSig file="../../clientlib/favico.min.js"}" type="text/javascript"></script>
	{if $bmPushEnabled}
	<script type="text/javascript">
	<!--
		var bmPushEnabled = true, bmPushAutoSubscribe = {if $bmPushSubscribed}true{else}false{/if}, bmPushPromptDismissed = {if $bmPushPromptDismissed|default:false}true{else}false{/if};
	//-->
	</script>
	<script src="clientlib/push.js?{fileDateSig file="../../clientlib/push.js"}" type="text/javascript"></script>
	{/if}
	<script type="text/javascript">
	{literal}
	var favicon=new Favico({
			animation:'fade'
		});
	{/literal}
	{if $bmUnreadNotifications!=0}favicon.badge({number value=$bmUnreadNotifications min=0 max=99});{/if}
	</script>
{foreach from=$_jsFiles.li item=_file}	<script type="text/javascript" src="{$_file}"></script>
{/foreach}
	{hook id="li:index.tpl:head"}
</head>

<body class="layout-fluid bm-loggedin bm-layout-combo{if $activeTab=='start'} bm-li-start{/if}{if $activeTab=='email'} bm-li-email bm-mail-preview-lines-{$templatePrefs.mailListPreviewLines|default:2}{/if}{if $activeTab=='organizer'} bm-li-organizer{/if}{if $activeTab=='webdisk'} bm-li-webdisk{/if}{if $activeTab=='sms'} bm-li-sms{/if}{if $activeTab=='prefs'} bm-li-prefs{/if}{if $pageContent=='li/email.compose.tpl'||$pageContent=='li/sms.compose.tpl'} bm-li-compose{/if}{if $pageContent=='li/email.folders.tpl'||$pageContent=='li/email.folders.edit.tpl'||$pageContent=='li/email.folders.editsys.tpl'} bm-li-folders{/if}{if $pageContent|substr:0:22 == 'li/organizer.calendar.'} bm-li-organizer-calendar{/if}" onload="documentLoader();if(typeof bmPushInitClient==='function')bmPushInitClient();">
	{hook id="li:index.tpl:beforeContent"}

	{if $bmPushEnabled && !$bmPushPromptDismissed|default:false}
	{include file="li/push-prompt.tpl" promptId="bmPushPrompt" promptVariant="mobile"}
	{/if}

	<div class="page" id="main">
		<aside class="navbar navbar-vertical navbar-expand-lg bm-li-vertical-nav" data-bs-theme="dark" id="mainMenu">
			<div class="container-fluid">
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="{lng p="navigation"}">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="navbar-brand navbar-brand-autodark">
					<a href="start.php?sid={$sid}" aria-label="{$service_title|escape}">
						<img src="{$tpldir}images/logo.png" height="32" alt="{$service_title|escape}" class="navbar-brand-image" />
					</a>
				</div>

				<div class="navbar-nav flex-row d-lg-none">
					<div class="d-flex">
						{include file="li/navbar-tools.tpl"}
					</div>
					{include file="li/navbar-user.tpl"}
				</div>

				<div class="collapse navbar-collapse bm-li-vertical-nav-collapse" id="sidebar-menu">
					<div class="bm-li-vertical-nav-body">
						<div class="bm-li-sidebar-content pt-lg-3" id="mainMenuContainer">
							{if $pageMenuFile}
							{include file="$pageMenuFile"}
							{/if}
							{hook id="li:sidebar.tpl:plugins"}
						</div>
						{include file="li/sidebar.footer.tpl"}
					</div>
				</div>
			</div>
		</aside>

		<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
			<div class="container-xl">
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="{lng p="navigation"}">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="navbar-nav flex-row order-md-last">
					<div class="d-none d-md-flex">
						{include file="li/navbar-tools.tpl"}
					</div>
					{include file="li/navbar-user.tpl"}
				</div>

				<div class="collapse navbar-collapse" id="navbar-menu">
					<ul class="navbar-nav">
						{foreach from=$pageTabs key=tabID item=tab}
						{if $tabID != 'prefs'}
						<li class="nav-item{if $activeTab==$tabID} active{/if}">
							<a class="nav-link{if $activeTab==$tabID} active{/if}" href="{$tab.link}{$sid}" title="{$tab.text|escape}">
								<span class="nav-link-icon d-md-none d-lg-inline-block">{include file="li/tab-icon.tpl" tab=$tab}</span>
								<span class="nav-link-title">{$tab.text}</span>
							</a>
						</li>
						{/if}
						{/foreach}
					</ul>
				</div>
			</div>
		</header>

		<header class="navbar navbar-expand-md d-lg-none d-print-none bm-li-mobile-tabs">
			<div class="container-xl">
				<div class="collapse navbar-collapse show" id="navbar-menu-mobile">
					<ul class="navbar-nav flex-row flex-wrap">
						{foreach from=$pageTabs key=tabID item=tab}
						{if $tabID != 'prefs'}
						<li class="nav-item" data-tab="{$tabID|escape}">
							<a class="nav-link{if $activeTab==$tabID} active{/if}" href="{$tab.link}{$sid}" title="{$tab.text|escape}" aria-label="{$tab.text|escape}">
								<span class="nav-link-icon">{include file="li/tab-icon.tpl" tab=$tab}</span>
							</a>
						</li>
						{/if}
						{/foreach}
					</ul>
				</div>
			</div>
		</header>

		<div class="page-wrapper">
			{if isset($pageToolbarFile) || isset($pageToolbar)}
			<div class="page-header d-print-none">
				<div class="container-xl">
					<div class="row g-2 align-items-center bm-li-page-toolbar">
						{if isset($pageToolbarFile)}
						{include file="$pageToolbarFile"}
						{else}
						<div class="col">{$pageToolbar}</div>
						{/if}
					</div>
				</div>
			</div>
			{/if}

			<div class="page-body">
				<div class="container-xl" id="mainContent">
					{if $bmPushEnabled && !$bmPushPromptDismissed|default:false}
					{include file="li/push-prompt.tpl" promptId="bmPushPromptDesktop" promptVariant="desktop"}
					{/if}
					<div id="mainBanner" style="display:none;">{banner}</div>
					<div id="mainContentArea">
						{include file="$pageContent"}
					</div>
				</div>
			</div>
		</div>

		{comment text="search popup"}
		<div class="headerBox card shadow bm-li-header-popup" id="searchPopup" style="display:none">
			<div class="card-body p-2">
				<div class="input-group input-group-flat">
					<span class="input-group-text">
						<i id="searchSpinner" class="fa fa-spinner fa-pulse fa-fw" style="display:none;"></i>
						<i class="icon ti ti-search icon-1 search-icon-default"></i>
						<input type="search" class="form-control" id="searchField" name="searchField" placeholder="{lng p="search"}" onkeypress="searchFieldKeyPress(event,{if $searchDetailsDefault}true{else}false{/if})" />
					</span>
				</div>
				<div id="searchResultBody" class="mt-2" style="display:none">
					<div id="searchResults" class="list-group list-group-flush"></div>
				</div>
			</div>
		</div>

		{comment text="new menu"}
		<div class="headerBox bm-li-header-popup dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card" id="newMenu" style="display:none">
			<div class="card">
				<div class="card-body p-0">
				{foreach from=$newMenu item=item}
					{if array_key_exists('sep', $item)}
					<div class="dropdown-divider m-0"></div>
					{else}
					<a class="dropdown-item" href="{$item.link}{$sid}">
						<span class="me-2">{include file="li/icon.tpl" faIcon=$item.faIcon}</span>
						{$item.text}...
					</a>
					{/if}
				{/foreach}
				</div>
			</div>
		</div>

		{comment text="notifications"}
		<div class="headerBox bm-li-header-popup dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card" id="notifyBox" style="display:none">
			<div class="card">
				<div class="card-header d-flex">
					<h3 class="card-title mb-0">{lng p="notifications"}</h3>
					<button type="button" class="btn-close ms-auto" onclick="hideNotifications(true); return false;" aria-label="{lng p="close"}"></button>
				</div>
				<div class="card-body p-0" id="notifyInner" style="max-height: 320px; overflow-y: auto;"></div>
			</div>
		</div>

		{comment text="user menu"}
		<div class="headerBox bm-li-header-popup dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card" id="userMenu" style="display:none">
			<div class="card">
				<a href="prefs.php?sid={$sid}" class="dropdown-item">
					<i class="ti ti-settings icon dropdown-item-icon"></i>
					{lng p="prefs"}
				</a>
				<a href="prefs.php?action=faq&amp;sid={$sid}" class="dropdown-item">
					<i class="ti ti-help icon dropdown-item-icon"></i>
					{lng p="faq"}
				</a>
				<div class="dropdown-divider m-0"></div>
				<a href="start.php?sid={$sid}&amp;action=logout" class="dropdown-item" onclick="return confirm('{lng p="logoutquestion"}');">
					<i class="ti ti-logout icon dropdown-item-icon"></i>
					{lng p="logout"}
				</a>
			</div>
		</div>

		{* Legacy mobile tab grid (navPos top dropdown) *}
		<div class="menu fade d-md-none" id="dropdownNavMenu" style="display:none;">
			<div class="row g-2 p-2">
				{foreach from=$pageTabs key=tabID item=tab}
				{if $tabID != 'prefs'}
				<div class="col-4">
					<a href="{$tab.link}{$sid}" class="btn btn-ghost-secondary w-100 py-3{if $activeTab==$tabID} active{/if}" title="{$tab.text|escape}">
						{include file="li/tab-icon.tpl" tab=$tab}
						<span class="d-block small mt-1 text-truncate">{$tab.text}</span>
					</a>
				</div>
				{/if}
				{/foreach}
			</div>
		</div>
	</div>

	{hook id="li:index.tpl:afterContent"}
</body>

</html>
