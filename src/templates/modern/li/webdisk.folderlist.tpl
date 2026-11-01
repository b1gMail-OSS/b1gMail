{if !isset($ownFolderList)}{assign var="ownFolderList" value=$folderList}{/if}
var webdisk_d = new dTree('webdisk_d');
var webdiskFolderShareActions = {};
var webdiskFolderRowActions = {};
var webdiskFolderTrees = [];
webdiskRenamePromptTitle = '{lng p="rename"|escape:'javascript'}';
webdiskDeleteConfirmMessage = '{lng p="realdel"|escape:'javascript'}';
webdiskEditTitle = '{lng p="edit"|escape:'javascript'}';
webdiskDeleteTitle = '{lng p="delete"|escape:'javascript'}';
{include file="li/webdisk.foldertree.nodes.tpl" treeName="webdisk_d" folders=$ownFolderList}
webdiskFolderTrees.push(webdisk_d);
{if isset($sharedFolderMenus)}
{foreach from=$sharedFolderMenus key=ownerId item=group}
{capture assign="shareTreeName"}webdisk_dshare{$ownerId}{/capture}
var {$shareTreeName} = new dTree('{$shareTreeName}');
{include file="li/webdisk.foldertree.nodes.tpl" treeName=$shareTreeName folders=$group.folders}
webdiskFolderTrees.push({$shareTreeName});
{/foreach}
{/if}
function bmWebdiskFolderTreesHtml()
{
	var html = '' + webdisk_d;
{if isset($sharedFolderMenus)}
{foreach from=$sharedFolderMenus key=ownerId item=group}
{capture assign="shareTreeName"}webdisk_dshare{$ownerId}{/capture}
	html += '<div class="sidebarHeading bm-mailbox-heading" title="{text value=$group.email escape=true}">{text value=$group.email escape=true}</div>';
	html += '' + {$shareTreeName};
{/foreach}
{/if}
	return html;
}
