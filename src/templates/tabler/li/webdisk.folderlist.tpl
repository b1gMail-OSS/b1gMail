var webdisk_d = new dTree('webdisk_d');
{foreach from=$folderList item=folder}
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
webdisk_d.add({$folder.i}, {$folder.parent}, '<span class="bm-folder-label">{text value=$folder.text escape=true noentities=true}</span>', 'javascript:switchWebdiskFolder({$folder.id});', '{text value=$folder.text escape=true noentities=true}', '', 'ti {$wdIcon}', 'ti {$wdIconOpen}');
{/foreach}
