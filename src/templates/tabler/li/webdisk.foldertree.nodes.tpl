{foreach from=$folders item=folder}
{if $folder.parent == -1}
{assign var=wdIcon value="ti-cloud"}
{assign var=wdIconOpen value="ti-cloud"}
{elseif $folder.icon == 'folder_shared'}
{assign var=wdIcon value="ti-folder-share"}
{assign var=wdIconOpen value="ti-folder-share"}
{else}
{assign var=wdIcon value="ti-folder"}
{assign var=wdIconOpen value="ti-folder-open"}
{/if}
{$treeName}.add({$folder.i}, {$folder.parent}, '<span class="bm-folder-label">{text value=$folder.text escape=true noentities=true}</span>', 'javascript:switchWebdiskFolder({$folder.id});', '{text value=$folder.text escape=true noentities=true}', '', 'ti {$wdIcon}', 'ti {$wdIconOpen}');
{* Alte Share-Only-Action nur für Ordner, die keine kombinierten Row-Actions bekommen (also nicht eigene Unterordner). *}
{if !empty($folder.canShare) && !($folder.parent != -1 && empty($folder.shareOwner))}webdiskFolderShareActions[{$folder.id}] = 'folder';
{/if}
{if $folder.parent != -1 && empty($folder.shareOwner)}
webdiskFolderRowActions[{$folder.id}] = {
	share: {if !empty($folder.canShare)}true{else}false{/if},
	rename: true,
	del: true,
	parent: {if isset($folder.parentFolder) && $folder.parentFolder != -1}{$folder.parentFolder}{else}0{/if},
	title: '{text value=$folder.text escape=javascript noentities=true}'
};
{elseif !empty($folder.canShare)}
webdiskFolderRowActions[{$folder.id}] = { share: true, rename: false, del: false, parent: 0, title: '{text value=$folder.text escape=javascript noentities=true}' };
{/if}
{/foreach}
