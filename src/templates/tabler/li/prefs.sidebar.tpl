{hook id="prefs.sidebar.tpl:head"}

<div class="sidebarHeading">{lng p="prefs"}</div>
<div class="contentMenuIcons bm-prefs-sidebar-nav">
	<a href="prefs.php?sid={$sid}"{if $pageContent=='li/prefs.start.tpl'} class="active"{/if}>
		{include file="li/icon.tpl" faIcon="fa-tachometer"} {lng p="overview"}
	</a>
	{foreach from=$prefsItems item=null key=item}
	{assign var="_prefsPrefix" value="li/prefs.$item."}
	<a href="prefs.php?action={$item}&sid={$sid}"{if $pageContent=="li/prefs.$item.tpl" || $pageContent|substr:0:($_prefsPrefix|count_characters)==$_prefsPrefix} class="active"{/if}>
		<span class="bm-prefs-sidebar-icon">{include file="li/prefs.item-icon.tpl"}</span>
		{lng p="$item"}
	</a>
	{/foreach}
</div>

{hook id="prefs.sidebar.tpl:foot"}
