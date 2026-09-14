{hook id="webdisk.folderbar.tpl:head"}

{if !$webdiskReadonly|default:false}
<div class="sidebarHeading">{lng p="createfolder"}</div>
<form action="webdisk.php?action=createFolder&folder={$folderID}{$sessionUrlSuffix}&csrf_token={$csrfToken}" method="post" onsubmit="return webdiskCreateFolder();" class="bm-webdisk-create-folder-form">
	{csrffield}
	<div class="input-group input-group-sm">
		<span class="input-group-text"><i class="ti ti-folder-plus icon icon-sm" aria-hidden="true"></i></span>
		<input type="text" class="form-control" name="folderName" id="folderName" placeholder="{lng p="createfolder"}" aria-label="{lng p="createfolder"}" autocomplete="off" />
	</div>
	<button type="submit" class="btn btn-primary btn-sm w-100">{lng p="ok"}</button>
</form>
{hook id="webdisk.sidebar.tpl:createfolder"}
{/if}

<div class="sidebarHeading bm-webdisk-sidebar-heading bm-mailbox-heading" title="{text value=$webdiskEmail escape=true}">
	<span>{text value=$webdiskEmail}</span>
	{if $canShareWebdisk}
	<a href="#" class="bm-webdisk-sidebar-heading-action" title="{lng p="sharewebdisk"}" aria-label="{lng p="sharewebdisk"}" onclick="openOverlay('{sessionurl file='webdisk.php' params='action=shareWebdisk'}', '{lng p="sharewebdisk"|escape:'javascript'}', 520, 360, true); return false;">{include file="li/icon.tpl" faIcon="fa-share"}</a>
	{/if}
</div>
<div class="bm-folder-tree" id="folderList"></div>
<script>
<!--
	{include file="li/webdisk.folderlist.tpl"}
	webdiskShareTitleWebdisk = '{lng p="sharewebdisk"|escape:'javascript'}';
	webdiskShareTitleFolder = '{lng p="sharefolder"|escape:'javascript'}';
	initWebdiskFolderTree();
//-->
</script>

<img src="{$tpldir}images/li/drag_wdfile.png" style="display:none;" /><img src="{$tpldir}images/li/drag_wdfolder.png" style="display:none;" /><img src="{$tpldir}images/li/drag_wditems.png" style="display:none;" />

{hook id="webdisk.folderbar.tpl:foot"}
