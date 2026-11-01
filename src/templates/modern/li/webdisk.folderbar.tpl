{hook id="webdisk.folderbar.tpl:head"}

<div class="sidebarHeading">{lng p="createfolder"}</div>
<center>
	<form action="{sessionurl file='webdisk.php' params="action=createFolder&folder={$folderID}&csrf_token={$csrfToken}"}" method="post" onsubmit="return webdiskCreateFolder();">
		{csrffield}
	<table>
		<tr>
			<td width="16"><i class="fa fa-folder-open-o" aria-hidden="true"></i></td>
			<td><input type="text" name="folderName" id="folderName" style="width: 100px;" /></td>
			<td><input type="submit" value="{lng p="ok"}" /></td>
		</tr>
	</table>
	</form>
	{hook id="webdisk.sidebar.tpl:createfolder"}
</center>

<div class="sidebarHeading bm-webdisk-sidebar-heading bm-mailbox-heading" title="{text value=$webdiskEmail escape=true}">
	<span>{text value=$webdiskEmail}</span>
	{if $canShareWebdisk}
	<a href="#" title="{lng p="sharewebdisk"}" onclick="openOverlay('{sessionurl file='webdisk.php' params='action=shareWebdisk'}', '{lng p="sharewebdisk"|escape:'javascript'}', 520, 360, true); return false;"><i class="fa fa-share" aria-hidden="true"></i></a>
	{/if}
</div>
<div class="contentMenuIcons" id="folderList">
</div>
<script>
<!--
	{include file="li/webdisk.folderlist.tpl"}
	EBID('folderList').innerHTML = (typeof bmWebdiskFolderTreesHtml == 'function') ? bmWebdiskFolderTreesHtml() : webdisk_d;
	enableWebdiskDragTargets();
	// Beide Aufrufe: zuerst die alte Share-only Logik für den Root/canShareWebdisk,
	// dann die neue Row-Actions-Logik für eigene Unterordner (Edit / Share / Delete).
	if(typeof attachWebdiskFolderShareActions === 'function')
		attachWebdiskFolderShareActions(typeof webdiskFolderShareActions !== 'undefined' ? webdiskFolderShareActions : {}, 'fa fa-share', '{lng p="sharewebdisk"|escape:'javascript'}', '{lng p="sharefolder"|escape:'javascript'}');
	if(typeof attachWebdiskFolderRowActions === 'function' && typeof webdiskFolderRowActions !== 'undefined')
		attachWebdiskFolderRowActions(webdiskFolderRowActions);
//-->
</script>

<img src="{$tpldir}images/li/drag_wdfile.png" style="display:none;" /><img src="{$tpldir}images/li/drag_wdfolder.png" style="display:none;" /><img src="{$tpldir}images/li/drag_wditems.png" style="display:none;" />

{hook id="webdisk.folderbar.tpl:foot"}
