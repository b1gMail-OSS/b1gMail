{if !isset($ownFolderList)}{assign var="ownFolderList" value=$folderList}{/if}
var d = new dTree('d');
{include file="li/email.foldertree.nodes.tpl" treeName="d" folders=$ownFolderList}
{if isset($sharedFolderMenus)}
{foreach from=$sharedFolderMenus key=ownerId item=group}
{capture assign="shareTreeName"}dshare{$ownerId}{/capture}
var {$shareTreeName} = new dTree('{$shareTreeName}');
{include file="li/email.foldertree.nodes.tpl" treeName=$shareTreeName folders=$group.folders}
{/foreach}
{/if}
function bmEmailFolderTreesHtml()
{
	var html = '' + d;
{if isset($sharedFolderMenus)}
{foreach from=$sharedFolderMenus key=ownerId item=group}
{capture assign="shareTreeName"}dshare{$ownerId}{/capture}
	html += '<div class="sidebarHeading bm-mailbox-heading" title="{text value=$group.email escape=true}">{text value=$group.email escape=true}</div>';
	html += '' + {$shareTreeName};
{/foreach}
{/if}
	return html;
}
