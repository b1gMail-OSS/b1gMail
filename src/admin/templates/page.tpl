<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge"/>
	<title>b1gMail - {lng p="acp"}</title>
	{if $adminAbsoluteUrls}
	<base href="{$adminBaseHref|escape:'html'}" />
	{/if}

	<meta name="description" content="{lng p="acp"}"/>

	<meta name="msapplication-TileColor" content="#066fd1" />
	<meta name="theme-color" content="#066fd1" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="mobile-web-app-capable" content="yes" />
	<meta name="HandheldFriendly" content="True" />
	<meta name="MobileOptimized" content="320" />

	<link rel="icon" type="image/png" href="{$tpldir}images/favicon-256x256.png" />
	{if $bmPushEnabled}
	<link rel="manifest" href="{if $adminAbsoluteUrls}{$adminManifestUrl|escape:'html'}{else}manifest.php{/if}" />
	<meta name="mobile-web-app-capable" content="yes" />
	{/if}
	<link rel="stylesheet" href="{$tpldir}css/tabler.min.css?{fileDateSig file="css/tabler.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/tabler-icons.min.css?{fileDateSig file="css/tabler-icons.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/inter.css?{fileDateSig file="css/inter.css"}" />
	<link rel="stylesheet" href="{$tpldir}libs/fontawesome/css/all.min.css?{fileDateSig file="libs/fontawesome/css/all.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/common.css?{fileDateSig file="css/common.css"}" />
	{foreach from=$_cssFiles.admin item=_file}
	<link rel="stylesheet" type="text/css" href="{$_file}" />
	{/foreach}
	<link rel="stylesheet" href="{$tpldir}css/tabler-custom.css?{fileDateSig file="css/tabler-custom.css"}" />

	<script>
		<!--
		var currentSID = '{$sid}';
		var bmCsrfToken = '{$csrfToken|escape:'javascript'}';
		var bmSessionConfig = {
			sid: '{$sid}',
			csrfToken: '{$csrfToken|escape:'javascript'}',
			adminRouting: {if $urlRoutingEnabled|default:false}true{else}false{/if},
			urlCompat: {if $sessionUrlCompat|default:false}true{else}false{/if},
			cookieMode: {if $sessionCookieMode|default:false}true{else}false{/if},
			apiBase: '{if $adminAbsoluteUrls}{$adminApiBase|escape:'javascript'}{/if}',
			apiUrl: 'welcome.php',
			idleTimeout: {$sessionIdleTimeout|default:30},
			warnBefore: {$sessionWarnBefore|default:2},
			lifetime: {$sessionLifetime|default:480},
			unlockError: '{lng p="session_unlock_error" js=true}',
			csrfErrorTitle: '{lng p="csrf_error_title" js=true}',
			csrfErrorText: '{lng p="csrf_error_text" js=true}',
			csrfErrorReload: '{lng p="csrf_error_reload" js=true}',
			csrfError: '{lng p="csrf_error_text" js=true}'
		};
		//-->
	</script>

	<script src="{if $adminClientLangUrl}{$adminClientLangUrl|escape:'html'}{else}{sessionurl file='../clientlang.php'}{/if}" type="text/javascript"></script>
	<script src="{$tpldir}js/tabler.min.js?{fileDateSig file="js/tabler.min.js"}"></script>
	<script src="{$tpldir}js/tabler-custom.js?{fileDateSig file="js/tabler-custom.js"}"></script>
	<script src="{$tpldir}js/common.js?{fileDateSig file="js/common.js"}"></script>
	<script src="{$selfurl}clientlib/bm-errors.js?{fileDateSig file="../../clientlib/bm-errors.js"}" type="text/javascript"></script>
	<script src="{$tpldir}js/avatar.js?{fileDateSig file="js/avatar.js"}"></script>
	<script src="{$tpldir}js/session-monitor.js?{fileDateSig file="js/session-monitor.js"}"></script>
	{if $bmPushEnabled}
	<script type="text/javascript">
	<!--
		var bmPushEnabled = true;
	//-->
	</script>
	<script src="{$tpldir}js/push.js?{fileDateSig file="js/push.js"}" type="text/javascript"></script>
	<script type="text/javascript">
	{literal}
	(function () {
		function syncAdminPush() {
			if (typeof bmPush === 'undefined' || !bmPush.isSupported()) {
				return;
			}
			navigator.serviceWorker.ready
				.then(function (reg) { return reg.pushManager.getSubscription(); })
				.then(function (sub) {
					if (!sub) {
						return bmPush.subscribe();
					}
					var url = '{/literal}{if $adminAbsoluteUrls}{$adminPushSyncUrl|escape:'javascript'}{else}push-api.php?action=sync{/if}{literal}';
					if(typeof bmSessionAppendUrl === 'function')
						url = bmSessionAppendUrl(url);
					else if(typeof currentSID !== 'undefined' && currentSID)
						url += (url.indexOf('?') !== -1 ? '&' : '?') + 'sid=' + encodeURIComponent(currentSID);
					return fetch(url, {
						method: 'POST',
						credentials: 'same-origin',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
						body: (typeof bmCsrfToken !== 'undefined' && bmCsrfToken)
							? 'csrf_token=' + encodeURIComponent(bmCsrfToken)
							: ''
					});
				})
				.catch(function () {});
		}
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', syncAdminPush);
		} else {
			syncAdminPush();
		}
	})();
	{/literal}
	</script>
	{/if}
	{foreach from=$_jsFiles.admin item=_file}
	<script type="text/javascript" src="{$_file}"></script>
	{/foreach}
</head>
<body>
<div class="page">
	<!-- BEGIN NAVBAR  -->
	<header class="navbar navbar-expand-md navbar-light d-print-none bg-dark text-white">
		<div class="container-xl">
			<!-- BEGIN NAVBAR TOGGLER -->
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<!-- END NAVBAR TOGGLER -->
			<!-- BEGIN NAVBAR LOGO -->
			<div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
				<a href="{sessionurl file='welcome.php'}">
					<img src="{$tpldir}images/logo_letter.png" width="110" height="32" alt="b1gMail" class="navbar-brand-image me-4"><img src="{$tpldir}images/logo_text.png"  class="navbar-brand-image" alt="b1gMail" />
				</a>
			</div>
			<!-- END NAVBAR LOGO -->
			<div class="navbar-nav flex-row order-md-last">
				<div class="nav-item dropdown">
					<a href="#" class="nav-link dropdown-toggle d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
						{include file="user-avatar.tpl" avatarSize="sm"}
						<div class="d-none d-xl-block ps-2">
							<div>{text value=$adminRow.firstname} {text value=$adminRow.lastname}</div>
							<div class="mt-1 small text-muted">{text value=$adminRow.username}</div>
						</div>
					</a>
					<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
						<a href="{sessionurl file='admins.php'}" class="dropdown-item">{lng p="profile"}</a>
						<a href="../index.php" class="dropdown-item" target="_blank" rel="noreferrer">{lng p="to_startpage"}</a>
						<!--<a href="javascript:showHelp();" class="dropdown-item">{lng p="help"}</a>-->
						{if $adminRow.type==0}<a href="{sessionurl file='welcome.php' params='action=phpinfo'}" class="dropdown-item">{lng p="phpinfo"}</a>{/if}
						<div class="dropdown-divider"></div>
						<a href="javascript:void(0)" class="dropdown-item" id="bmSessionLockMenu" role="button">{lng p="session_lock_now"}</a>
						<a href="{sessionurl file='index.php' params='action=logout'}" class="dropdown-item" onclick="return confirm('{lng p="logoutquestion"}');">{lng p="logout"}</a>
					</div>
				</div>
			</div>
		</div>
	</header>
	<header class="navbar-expand-md">
		<div class="collapse navbar-collapse" id="navbar-menu">
			<div class="navbar">
				<div class="container-xl">
					<div class="row flex-column flex-md-row flex-fill align-items-center">
						<div class="col">
							<!-- BEGIN NAVBAR MENU -->
							<ul class="navbar-nav">
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
										<span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ico_license.png" height="16" /></span>
										<span class="nav-link-title">{lng p="welcome"}</span>
									</a>
									<div class="dropdown-menu">
										<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/welcome.php'} active{/if}" href="{sessionurl file='welcome.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ico_license.png" height="16" /></span><span class="nav-link-title"> {lng p="welcome"} </span></a>
										<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/admins.php' && $smarty.request.action|default:''=='admins'} active{/if}" href="{sessionurl file='admins.php' params='action=admins'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ico_users.png" height="16" /></span><span class="nav-link-title"> {lng p="admins"} </span></a>
									</div>
								</li>
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
										<span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ico_prefs_misc.png" height="16" /></span>
										<span class="nav-link-title">{lng p="prefs"}</span>
									</a>
									<div class="dropdown-menu">
										<div class="dropdown-menu-columns">
											<div class="dropdown-menu-column">
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.common.php'} active{/if}" href="{sessionurl file='prefs.common.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ico_prefs_common.png" height="16" /></span><span class="nav-link-title"> {lng p="common"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.email.php'} active{/if}" href="{sessionurl file='prefs.email.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ico_prefs_email.png" height="16" /></span><span class="nav-link-title"> {lng p="email"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.recvrules.php'} active{/if}" href="{sessionurl file='prefs.recvrules.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/rule32.png" height="16" /></span><span class="nav-link-title"> {lng p="recvrules"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.webdisk.php'} active{/if}" href="{sessionurl file='prefs.webdisk.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ico_disk.png" height="16" /></span><span class="nav-link-title"> {lng p="webdisk"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.sms.php'} active{/if}" href="{sessionurl file='prefs.sms.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/gateway32.png" height="16" /></span><span class="nav-link-title"> {lng p="sms"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.abuse.php'} active{/if}" href="{sessionurl file='prefs.abuse.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/abuse.png" height="16" /></span><span class="nav-link-title"> {lng p="abuseprotect"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.coupons.php'} active{/if}" href="{sessionurl file='prefs.coupons.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/coupon.png" height="16" /></span><span class="nav-link-title"> {lng p="coupons"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.profilefields.php'} active{/if}" href="{sessionurl file='prefs.profilefields.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/field32.png" height="16" /></span><span class="nav-link-title"> {lng p="profilefields"} </span></a>
											</div>
											<div class="dropdown-menu-column">
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.languages.php'} active{/if}" href="{sessionurl file='prefs.languages.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/lang32.png" height="16" /></span><span class="nav-link-title"> {lng p="languages"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.templates.php'} active{/if}" href="{sessionurl file='prefs.templates.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/template.png" height="16" /></span><span class="nav-link-title"> {lng p="templates"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.ads.php'} active{/if}" href="{sessionurl file='prefs.ads.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ad32.png" height="16" /></span><span class="nav-link-title"> {lng p="ads"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.faq.php'} active{/if}" href="{sessionurl file='prefs.faq.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/faq32.png" height="16" /></span><span class="nav-link-title"> {lng p="faq"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.countries.php'} active{/if}" href="{sessionurl file='prefs.countries.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/country.png" height="16" /></span><span class="nav-link-title"> {lng p="countries"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.widgetlayouts.php'} active{/if}" href="{sessionurl file='prefs.widgetlayouts.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/wlayout_add.png" height="16" /></span><span class="nav-link-title"> {lng p="widgetlayouts"} </span></a>
												<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/prefs.payments.php'} active{/if}" href="{sessionurl file='prefs.payments.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ico_prefs_payments.png" height="16" /></span><span class="nav-link-title"> {lng p="payments"} </span></a>
												{if !empty($smarty.const.TOOLBOX_SERVER)}<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/toolbox.php'} active{/if}" href="{sessionurl file='toolbox.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/toolbox.png" height="16" /></span><span class="nav-link-title"> {lng p="toolbox"} </span></a>{/if}
											</div>
										</div>
									</div>
								</li>
								{if $adminRow.type==0||$adminRow.privileges.users||$adminRow.privileges.groups||$adminRow.privileges.workgroups||$adminRow.privileges.activity||$adminRow.privileges.newsletter||$adminRow.privileges.payments}
									<li class="nav-item dropdown">
										<a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
											<span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ico_users.png" height="16" /></span>
											<span class="nav-link-title">{lng p="usersgroups"}</span>
										</a>
										<div class="dropdown-menu">
											{if $adminRow.type==0||$adminRow.privileges.users}<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/users.php'} active{/if}" href="{sessionurl file='users.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/user_action.png" height="16" /></span><span class="nav-link-title"> {lng p="users"} </span></a>{/if}
											{if $adminRow.type==0||$adminRow.privileges.groups}<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/groups.php'} active{/if}" href="{sessionurl file='groups.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ico_group.png" height="16" /></span><span class="nav-link-title"> {lng p="groups"} </span></a>{/if}
											{if $adminRow.type==0||$adminRow.privileges.workgroups}<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/workgroups.php'} active{/if}" href="{sessionurl file='workgroups.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ico_workgroup.png" height="16" /></span><span class="nav-link-title"> {lng p="workgroups"} </span></a>{/if}
											{if $adminRow.type==0||$adminRow.privileges.activity}<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/activity.php'} active{/if}" href="{sessionurl file='activity.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/activity.png" height="16" /></span><span class="nav-link-title"> {lng p="activity"} </span></a>{/if}
											{if $adminRow.type==0||$adminRow.privileges.abuse}<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/abuse.php'} active{/if}" href="{sessionurl file='abuse.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/abuse.png" height="16" /></span><span class="nav-link-title"> {lng p="abuseprotect"} </span></a>{/if}
											{if $adminRow.type==0||$adminRow.privileges.newsletter}<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/newsletter.php'} active{/if}" href="{sessionurl file='newsletter.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/newsletter.png" height="16" /></span><span class="nav-link-title"> {lng p="newsletter"} </span></a>{/if}
											{if $adminRow.type==0||$adminRow.privileges.payments}<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/payments.php'} active{/if}" href="{sessionurl file='payments.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/ico_prefs_payments.png" height="16" /></span><span class="nav-link-title"> {lng p="payments"} </span></a>{/if}
										</div>
									</li>
								{/if}
								{if $adminRow.type==0||$adminRow.privileges.optimize||$adminRow.privileges.maintenance||$adminRow.privileges.stats||$adminRow.privileges.logs}
									<li class="nav-item dropdown">
										<a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
											<span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/toolbox.png" height="16" /></span>
											<span class="nav-link-title">{lng p="tools"}</span>
										</a>
										<div class="dropdown-menu">
											{if $adminRow.type==0||$adminRow.privileges.optimize}<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/optimize.php'} active{/if}" href="{sessionurl file='optimize.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/db_optimize.png" height="16" /></span><span class="nav-link-title"> {lng p="optimize"} </span></a>{/if}
											{if $adminRow.type==0||$adminRow.privileges.maintenance}<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/maintenance.php'} active{/if}" href="{adminurl script='maintenance.php' trailingAmp='false'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/orphans32.png" height="16" /></span><span class="nav-link-title"> {lng p="maintenance"} </span></a>{/if}
											{if $adminRow.type==0||$adminRow.privileges.stats}<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/stats.php'} active{/if}" href="{sessionurl file='stats.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/stats.png" height="16" /></span><span class="nav-link-title"> {lng p="stats"} </span></a>{/if}
											{if $adminRow.type==0||$adminRow.privileges.logs}<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/logs.php'} active{/if}" href="{sessionurl file='logs.php'}"><span class="nav-link-icon d-md-none d-lg-inline-block"><img src="{$tpldir}images/logs.png" height="16" /></span><span class="nav-link-title"> {lng p="logs"} </span></a>{/if}
										</div>
									</li>
								{/if}
								{if $adminRow.type==0||$adminRow.privileges.plugins}
									<li class="nav-item dropdown">
										<a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
    <span class="nav-link-icon d-md-none d-lg-inline-block">
      <img src="{$tpldir}images/plugin.png" height="16" />
    </span>
											<span class="nav-link-title">{lng p="plugins"}</span>
										</a>

										<div class="dropdown-menu" data-bs-popper="static">
											<div class="dropdown-menu-columns">
												{assign var="count" value=0}
												{assign var="perColumn" value=8}
												<div class="dropdown-menu-column">
													{* if pluginmenuitems is empty show the first entry *}
													{if empty($pluginMenuItems)}
														{if $adminRow.type == 0 || $adminRow.privileges.plugins.$plugin}
														<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/plugins.php'} active{/if}" href="{sessionurl file='plugins.php'}">
															<span class="nav-link-icon d-md-none d-lg-inline-block">
																<img src="{$tpldir}images/plugin.png" height="16" />
															</span>
																<span class="nav-link-title">{lng p="plugins"}</span>
															</a>
														{/if}

													{/if}
													
													{foreach from=$pluginMenuItems key=plugin item=pluginInfo name=pluginLoop}

														{* Nur wenn sichtbar für Admin oder nach Berechtigung *}
														{if $adminRow.type == 0 || $adminRow.privileges.plugins.$plugin}

														{* Link zur Plugin-Übersicht als erstes Element *}
														{if $count == 0 && $adminRow.type == 0}
															<a class="dropdown-item{if $smarty.server.SCRIPT_NAME=='/admin/plugins.php'} active{/if}" href="{sessionurl file='plugins.php'}">
															<span class="nav-link-icon d-md-none d-lg-inline-block">
																<img src="{$tpldir}images/plugin.png" height="16" />
															</span>
																<span class="nav-link-title">{lng p="plugins"}</span>
															</a>
															{assign var="count" value=$count+1}
															{if $count % $perColumn == 0}
																</div>
																<div class="dropdown-menu-column">
															{/if}
														{/if}

														{* Plugin-Link selbst *}
														<a class="dropdown-item{if isset($smarty.get.plugin) && $smarty.get.plugin == $plugin && (!isset($smarty.get.do) || $smarty.get.do != "activatePlugin")} active{/if}" href="{sessionurl file='plugin.page.php' params="plugin={$plugin|escape:url}"}">
															<span class="nav-link-icon d-md-none d-lg-inline-block">
																	<img src="{if isset($pluginInfo.icon) && $pluginInfo.icon}{$selfurl|escape:'html'}plugins/templates/images/{$pluginInfo.icon|escape:'url'}{else}{$tpldir}images/wlayout_add.png{/if}" height="16" />
															</span>
															<span class="nav-link-title">{text value=$pluginInfo.title cut=20}</span>
														</a>

															{* Sichtbare Einträge zählen und ggf. neue Spalte beginnen *}
															{assign var="count" value=$count+1}
															{if $count % $perColumn == 0 && !$smarty.foreach.pluginLoop.last}
																</div>
																<div class="dropdown-menu-column">
															{/if}
														{/if}
													{/foreach}
												</div>
											</div>
										</div>
									</li>
								{/if}
							</ul>
							<!-- END NAVBAR MENU -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<!-- END NAVBAR  -->
	<div class="page-wrapper">
		<!-- BEGIN PAGE HEADER -->
		<div class="page-header d-print-none">
			<div class="container-xl">
				<div class="row g-2 align-items-center">
					<div class="col">
						<h2 class="page-title">
							{if $title}<div id="breadcrumb">{$title}</div>{/if}
						</h2>
					</div>
				</div>
			</div>
		</div>
		<!-- END PAGE HEADER -->
		<!-- BEGIN PAGE BODY -->
		<div class="page-body">
			<div class="container-xl flex-fill d-flex flex-column">
				<div class="card">
					<div class="card-header bg-muted-lt">
						<ul class="nav nav-pills card-header-pills">
							{foreach from=$tabs item=tab}
							<li class="nav-item">
								<a class="nav-link{if $tab.active} active{/if}" href="{sessionurl file=$tab.link}">
									<span class="nav-link-icon d-md-none d-lg-inline-block">
											<img src="{if isset($tab.relIcon) && $tab.relIcon}{$tpldir}images/{$tab.relIcon|escape:'url'}{elseif isset($tab.icon) && $tab.icon}{$tab.icon}{else}{$tpldir}images/ico_prefs_misc.png{/if}" height="16" alt="{$tab.title}" />
										</span>
									<span class="nav-link-title"> {$tab.title} </span>
								</a>
							</li>
							{/foreach}
						</ul>
					</div>
					<div class="card-body">
						{include file="$page"}
					</div>
				</div>
			</div>
		</div>
		<!-- END PAGE BODY -->
		<!--  BEGIN FOOTER  -->
		<footer class="footer footer-transparent d-print-none">
			<div class="container-xl">
				<div class="row text-center align-items-center flex-row-reverse">
					<div class="col-lg-auto ms-lg-auto">
						<ul class="list-inline list-inline-dots mb-0">
							<li class="list-inline-item"><a href="https://www.b1gmail.eu/" target="_blank" class="link-secondary"><i class="ti ti-users-group"></i> Community</a></li>
							<li class="list-inline-item">Source code: <a href="https://github.com/b1gMail-OSS/b1gMail" target="_blank" class="link-secondary" rel="noopener noreferrer"><i class="ti ti-brand-github"></i> GitHub</a> | <a href="https://codeberg.org/b1gMail/b1gMail" target="_blank" class="link-secondary" rel="noopener noreferrer">Codeberg</a></li>
							<li class="list-inline-item"><a href="https://github.com/b1gMail-OSS/b1gMail/wiki" target="_blank" class="link-secondary" rel="noopener noreferrerr"><i class="ti ti-brand-github"></i> Wiki</a></li>
							<li class="list-inline-item"><a href="https://fosstodon.org/@b1gmail" target="_blank" class="link-secondary" rel="noopener noreferrerr"><i class="ti ti-brand-mastodon"></i> {lng p="follow_mastodon"}</a></li>
						</ul>
					</div>
					<div class="col-12 col-lg-auto mt-3 mt-lg-0">
						<ul class="list-inline list-inline-dots mb-0">
							<li class="list-inline-item">Copyright &copy; {$smarty.now|date_format:"%Y"} b1gMail</li>
							<li class="list-inline-item"><a href="{sessionurl file='welcome.php' params='action=about'}" class="link-secondary" rel="noopener">{$bmver}</a></li>
						</ul>
					</div>
				</div>
			</div>
		</footer>
		<!--  END FOOTER  -->
	</div>
</div>
{include file="session-lock.tpl"}
</body>
</html>