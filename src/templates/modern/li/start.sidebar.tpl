{hook id="start.sidebar.tpl:head"}

<div class="sidebarHeading">{lng p="start"}</div>
<div class="contentMenuIcons">
	<a href="start.php?sid={$sid}"><i class="fa fa-home" aria-hidden="true"></i> {lng p="start"}</a><br />
	<a href="start.php?action=customize&sid={$sid}"><i class="fa fa-puzzle-piece" aria-hidden="true"></i> {lng p="customize"}</a><br />
	{hook id="start.sidebar.tpl:start"}
</div>

<div class="sidebarHeading">{lng p="misc"}</div>
<div class="contentMenuIcons">
	<a href="start.php?sid={$sid}&action=logout" onclick="return(confirm('{lng p="logoutquestion"}'));"><i class="fa fa-sign-out" aria-hidden="true"></i> {lng p="logout"}</a><br />
	{hook id="start.sidebar.tpl:misc"}
</div>

{hook id="start.sidebar.tpl:foot"}
