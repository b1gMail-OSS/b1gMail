{hook id="email.sidebar.tpl:head"}

<div class="sidebarHeading">{lng p="email"}</div>
<div class="contentMenuIcons">
	<a href="email.compose.php?sid={$sid}"><i class="fa fa-envelope-o" aria-hidden="true"></i> {lng p="sendmail"}</a><br />
	<a href="email.folders.php?sid={$sid}"><i class="fa fa-folder-open-o" aria-hidden="true"></i> {lng p="folderadmin"}</a><br />
	{hook id="email.sidebar.tpl:email"}
</div>

<div class="sidebarHeading">{lng p="folders"}</div>
<div class="contentMenuIcons" id="folderList">
</div>
<script>
<!--
	{include file="li/email.folderlist.tpl"}
	EBID('folderList').innerHTML = d;
	enableFolderDragTargets();
//-->
</script>

{hook id="email.sidebar.tpl:foot"}
