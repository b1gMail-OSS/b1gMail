{hook id="email.sidebar.tpl:head"}

<div class="sidebarHeading">{lng p="email"}</div>
<div class="contentMenuIcons">
	<a href="email.compose.php?sid={$sid}">{include file="li/icon.tpl" faIcon="fa-envelope-o"} {lng p="sendmail"}</a><br />
	<a href="email.folders.php?sid={$sid}">{include file="li/icon.tpl" faIcon="fa-folder-open-o"} {lng p="folderadmin"}</a><br />
	{hook id="email.sidebar.tpl:email"}
</div>

<div class="sidebarHeading">{lng p="folders"}</div>
<div class="bm-folder-tree" id="folderList">
</div>
<script>
<!--
	{include file="li/email.folderlist.tpl"}
	d.config.useLines = false;
	d.icon.nlPlus = 'ti ti-chevron-right';
	d.icon.nlMinus = 'ti ti-chevron-down';
	d.icon.plus = 'ti ti-chevron-right';
	d.icon.minus = 'ti ti-chevron-down';
	d.icon.plusBottom = 'ti ti-chevron-right';
	d.icon.minusBottom = 'ti ti-chevron-down';
	EBID('folderList').innerHTML = d;
	enableFolderDragTargets();
//-->
</script>

{hook id="email.sidebar.tpl:foot"}
