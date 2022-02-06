<!DOCTYPE html>
<html lang="{lng p="langCode"}">

<head>
    <title>{if $pageTitle}{text value=$pageTitle} - {/if}{$service_title}</title>

	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />

	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}style/loggedin.css?{fileDateSig file="style/loggedin.css"}" rel="stylesheet" type="text/css" />
	<link href="{$tpldir}style/dtree.css?{fileDateSig file="style/dtree.css"}" rel="stylesheet" type="text/css" />
	<link href="clientlib/fontawesome/css/font-awesome.min.css?{fileDateSig file="../../clientlib/fontawesome/css/font-awesome.min.css"}" rel="stylesheet" type="text/css" />
	<link href="clientlib/fontawesome/css/font-awesome-animation.min.css?{fileDateSig file="../../clientlib/fontawesome/css/font-awesome-animation.min.css"}" rel="stylesheet" type="text/css" />
{foreach from=$_cssFiles.li item=_file}	<link rel="stylesheet" type="text/css" href="{$_file}" />
{/foreach}

	<!-- client scripts -->
	<script type="text/javascript">
	<!--
		var currentSID = '{$sid}', tplDir = '{$tpldir}', serverTZ = {$serverTZ}, ftsBGIndexing = {if $ftsBGIndexing}true{else}false{/if}{if $bmNotifyInterval},
			notifyInterval = {$bmNotifyInterval}, notifySound = {if $bmNotifySound}true{else}false{/if}{/if};
	//-->
	</script>
	<script src="clientlang.php?sid={$sid}" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js?{fileDateSig file="js/common.js"}" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js?{fileDateSig file="js/loggedin.js"}" type="text/javascript"></script>
	<script src="clientlib/dtree.js?{fileDateSig file="../../clientlib/dtree.js"}" type="text/javascript"></script>
	<script src="clientlib/overlay.js?{fileDateSig file="../../clientlib/overlay.js"}" type="text/javascript"></script>
	<script src="clientlib/autocomplete.js?{fileDateSig file="../../clientlib/autocomplete.js"}" type="text/javascript"></script>
	<!--[if lt IE 9]>
	<script defer type="text/javascript" src="clientlib/IE9.js"></script>
	<![endif]-->
	<!--[if IE]>
	<meta http-equiv="Page-Enter" content="blendTrans(duration=0)" />
	<meta http-equiv="Page-Exit" content="blendTrans(duration=0)" />
	<![endif]-->
{foreach from=$_jsFiles.li item=_file}	<script type="text/javascript" src="{$_file}"></script>
{/foreach}
	{hook id="li:index.tpl:head"}
</head>

<body onload="documentLoader()">
	{hook id="li:index.tpl:beforeContent"}

	<div id="main">
		<div class="dropdownNavbar">
			<a class="logo" href="#"{if $templatePrefs.navPos=='top'} onclick="toggleDropdownNavMenu()"{/if}>
				{if $activeTab=='_search'}<i class="fa fa-search"></i>{else}{foreach from=$pageTabs key=tabID item=tab}{if $activeTab==$tabID}
				<i class="fa {$tab.faIcon}"></i>
				{/if}{/foreach}{/if}
				{$service_title}
				{if $templatePrefs.navPos=='top'}<span style="">| <i class="fa fa-angle-down"></i></span>{/if}
			</a>

			<div class="toolbar right">
				{if $bmNotifyInterval>0}<a href="#" onclick="showNotifications(this)" title="{lng p="notifications"}" style="position:relative;"><i id="notifyIcon" class="fa fa-bell faa-ring"></i><div class="noBadge" id="notifyCount"{if $bmUnreadNotifications==0} style="display:none;"{/if}>{number value=$bmUnreadNotifications min=0 max=99}</div></a>{/if}
				<a href="#" onclick="showNewMenu(this)" title="{lng p="new"}"><i class="fa fa-plus-square fa-lg"></i> {lng p="new"}
							| <i class="fa fa-angle-down"></i></a>
				<a href="#" onclick="showSearchPopup(this)" title="{lng p="search"}"><i class="fa fa-search"></i></a>
				<a href="prefs.php?action=faq&sid={$sid}" title="{lng p="faq"}"><i class="fa fa-question fa-lg"></i></a>
				<a href="start.php?sid={$sid}&action=logout" onclick="return confirm('{lng p="logoutquestion"}');" title="{lng p="logout"}"><i class="fa fa-sign-out fa-lg"></i></a>
			</div>

			<div class="toolbar">
				{if $pageToolbarFile}
				{comment text="including $pageToolbarFile"}
				{include file="$pageToolbarFile"}
				{elseif $pageToolbar}
				{$pageToolbar}
				{else}
				&nbsp;
				{/if}
			</div>

			<div class="menu fade" id="dropdownNavMenu" style="display:none;">
				<div class="arrow"></div>
				{foreach from=$pageTabs key=tabID item=tab}
				{comment text="tab $tabID"}
				<a href="{$tab.link}{$sid}" title="{$tab.text}"{if $activeTab==$tabID} class="active"{/if}>
					<i class="fa {$tab.faIcon}"></i>
					{$tab.text}
				</a>
				{/foreach}
			</div>
		</div>

		<div id="mainMenu" class="up">
			<div id="mainMenuContainer"{if $templatePrefs.navPos=='left'} style="bottom:{math equation="x*29" x=$pageTabsCount}px;"{/if}>
	            {if $pageMenuFile}
	            {comment text="including $pageMenuFile"}
	            {include file="$pageMenuFile"}
	            {else}
	            {foreach from=$pageMenu key=menuID item=menu}
	            {comment text="menuitem $menuID"}
	           	<a href="{$menu.link}">
		            <img src="{$tpldir}images/li/menu_ico_{$menu.icon}.png" width="16" height="16" border="0" alt="" align="absmiddle" />
		            {$menu.text}
	            </a>
	            {if $menu.addText}
	            <span class="menuAddText">{$menu.addText}</span>
	            {/if}
	            <br />
	        	{/foreach}
	            {/if}
            </div>

			{if $templatePrefs.navPos=='left'}
			<ul id="menuTabItems">
	            {foreach from=$pageTabs key=tabID item=tab}
	            {comment text="tab $tabID"}
	            <li{if $activeTab==$tabID} class="active"{/if}>
	            	<a href="{$tab.link}{$sid}">
	            		<i class="fa {$tab.faIcon}"></i>
	                    {if $tab.text}&nbsp;{$tab.text}{/if}
	                </a>
	            </li>
	            {/foreach}
			</ul>
			{/if}
		</div>

		<div id="mainBanner" style="display:none;">
			{banner}
		</div>

		<div id="mainContent" class="up">
			{include file="$pageContent"}
		</div>

		<div id="mainStatusBar">
			powered by <a target="_blank" href="deref.php?https://www.b1gmail.eu/" rel="noreferrer">b1gMail.eu</a>
		</div>

	    {comment text="search popup"}
	    <div class="headerBox" id="searchPopup" style="display:none">
			<div class="arrow"></div>
			<div class="inner">
				<table width="100%" cellspacing="0" cellpadding="0" class="up" onmouseover="disableHide=true;" onmouseout="disableHide=false;">
					<tr>
						<td>
							{if $templatePrefs.navPos=='top'}<div class="arrow"></div>{/if}
							<table cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td width="22" height="26" align="right"><i id="searchSpinner" style="display:none;" class="fa fa-spinner fa-pulse fa-fw"></i></td>
									<td align="right" width="70">{lng p="search"}: &nbsp;</td>
									<td align="center">
										<input id="searchField" name="searchField" style="width:90%" onkeypress="searchFieldKeyPress(event,{if $searchDetailsDefault}true{else}false{/if})" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tbody id="searchResultBody" style="display:none">
					<tr>
						<td id="searchResults"></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>

	    {comment text="new menu"}
		<div class="headerBox" id="newMenu" style="display:none;">
			<div class="arrow"></div>
			<div class="inner">
			{foreach from=$newMenu item=item}
				{if $item.sep}
				<div class="mailMenuSep"></div>
				{else}
				<a class="mailMenuItem" href="{$item.link}{$sid}"><i class="fa {$item.faIcon}" aria-hidden="true"></i> {$item.text}...</a>
				{/if}
			{/foreach}
			</div>
		</div>

		{comment text="notifications"}
		<div class="headerBox" id="notifyBox" style="display:none;">
			<div class="arrow"></div>
			<div class="inner" id="notifyInner"></div>
		</div>

	</div>

	{hook id="li:index.tpl:afterContent"}
</body>

</html>
