{foreach from=$folders item=folder}
{$treeName}.add({$folder.i}, {$folder.parent}, '{text value=$folder.text escape=true noentities=true}', 'javascript:switchWebdiskFolder({$folder.id});', '{text value=$folder.text escape=true noentities=true}', '', 'fa fa-folder-o', 'fa fa-folder-open-o');
{* Alte Share-Only-Action nur für Ordner, die keine kombinierten Row-Actions bekommen (Root / fremde Freigaben). *}
{if !empty($folder.canShareWebdisk)}webdiskFolderShareActions[{$folder.id}] = 'webdisk';
{elseif !empty($folder.canShare) && !($folder.parent != -1 && empty($folder.shareOwner))}webdiskFolderShareActions[{$folder.id}] = 'folder';
{/if}
{if $folder.parent != -1 && empty($folder.shareOwner)}
webdiskFolderRowActions[{$folder.id}] = {
	share: {if !empty($folder.canShare)}true{else}false{/if},
	rename: true,
	del: true,
	parent: {if isset($folder.parentFolder) && $folder.parentFolder != -1}{$folder.parentFolder}{else}0{/if},
	title: '{text value=$folder.text escape=javascript noentities=true}'
};
{/if}
{/foreach}
