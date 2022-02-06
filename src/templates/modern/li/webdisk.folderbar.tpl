{hook id="webdisk.folderbar.tpl:head"}

<div class="sidebarHeading">{lng p="createfolder"}</div>
<center>
	<form action="webdisk.php?action=createFolder&folder={$folderID}&sid={$sid}" method="post" onsubmit="return webdiskCreateFolder();">
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

<div class="sidebarHeading">{lng p="folders"}</div>
<div class="contentMenuIcons" id="folderList">
</div>
<script>
<!--
	{include file="li/webdisk.folderlist.tpl"}
	EBID('folderList').innerHTML = webdisk_d;
	enableWebdiskDragTargets();
//-->
</script>

<img src="{$tpldir}images/li/drag_wdfile.png" style="display:none;" /><img src="{$tpldir}images/li/drag_wdfolder.png" style="display:none;" /><img src="{$tpldir}images/li/drag_wditems.png" style="display:none;" />

{hook id="webdisk.folderbar.tpl:foot"}
