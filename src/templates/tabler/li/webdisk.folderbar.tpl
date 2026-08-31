{hook id="webdisk.folderbar.tpl:head"}

<div class="sidebarHeading">{lng p="createfolder"}</div>
<form action="webdisk.php?action=createFolder&folder={$folderID}{$sessionUrlSuffix}" method="post" onsubmit="return webdiskCreateFolder();" class="bm-webdisk-create-folder-form">
	{csrffield}
	<div class="input-group input-group-sm">
		<span class="input-group-text"><i class="ti ti-folder-plus icon icon-sm" aria-hidden="true"></i></span>
		<input type="text" class="form-control" name="folderName" id="folderName" placeholder="{lng p="createfolder"}" aria-label="{lng p="createfolder"}" autocomplete="off" />
	</div>
	<button type="submit" class="btn btn-primary btn-sm w-100">{lng p="ok"}</button>
</form>
{hook id="webdisk.sidebar.tpl:createfolder"}

<div class="sidebarHeading">{lng p="folders"}</div>
<div class="bm-folder-tree" id="folderList"></div>
<script>
<!--
	{include file="li/webdisk.folderlist.tpl"}
	initWebdiskFolderTree();
//-->
</script>

<img src="{$tpldir}images/li/drag_wdfile.png" style="display:none;" /><img src="{$tpldir}images/li/drag_wdfolder.png" style="display:none;" /><img src="{$tpldir}images/li/drag_wditems.png" style="display:none;" />

{hook id="webdisk.folderbar.tpl:foot"}
