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
var bmFolderUnreadCounts = {};
{foreach from=$ownFolderList item=folder}{if empty($folder.virtual)}bmFolderUnreadCounts[{$folder.id}] = {if !empty($folder.unread)}{$folder.unread}{else}0{/if};
{/if}{/foreach}
{if isset($sharedFolderMenus)}{foreach from=$sharedFolderMenus item=group}{foreach from=$group.folders item=folder}{if empty($folder.virtual)}bmFolderUnreadCounts[{$folder.id}] = {if !empty($folder.unread)}{$folder.unread}{else}0{/if};
{/if}{/foreach}{/foreach}{/if}
function bmInitEmailFolderTree(tree)
{
	if(!tree || !tree.config)
		return;
	tree.config.useLines = false;
	tree.icon.nlPlus = 'ti ti-chevron-right';
	tree.icon.nlMinus = 'ti ti-chevron-down';
	tree.icon.plus = 'ti ti-chevron-right';
	tree.icon.minus = 'ti ti-chevron-down';
	tree.icon.plusBottom = 'ti ti-chevron-right';
	tree.icon.minusBottom = 'ti ti-chevron-down';
}
function bmEmailFolderTreesHtml()
{
	bmInitEmailFolderTree(d);
	var html = '' + d;
{if isset($sharedFolderMenus)}
{foreach from=$sharedFolderMenus key=ownerId item=group}
{capture assign="shareTreeName"}dshare{$ownerId}{/capture}
	bmInitEmailFolderTree({$shareTreeName});
	html += '<div class="sidebarHeading bm-mailbox-heading" title="{text value=$group.email escape=true}">{text value=$group.email escape=true}</div>';
	html += '' + {$shareTreeName};
{/foreach}
{/if}
	return html;
}
