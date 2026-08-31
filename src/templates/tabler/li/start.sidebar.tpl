{hook id="start.sidebar.tpl:head"}

<div class="sidebarHeading">{lng p="start"}</div>
<div class="contentMenuIcons">
	<a href="{sessionurl file='start.php'}"{if $activeTab=='start'} class="active"{/if}>{include file="li/icon.tpl" faIcon="fa-home"} {lng p="start"}</a>
	<a href="{sessionurl file='start.php' params='action=customize'}">{include file="li/icon.tpl" faIcon="fa-puzzle-piece"} {lng p="customize"}</a>
	{hook id="start.sidebar.tpl:start"}
</div>

{hook id="start.sidebar.tpl:foot"}
