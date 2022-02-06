var webdisk_d = new dTree('webdisk_d');
{foreach from=$folderList item=folder}
webdisk_d.add({$folder.i}, {$folder.parent}, '{text value=$folder.text escape=true noentities=true}', 'javascript:switchWebdiskFolder({$folder.id});', '{text value=$folder.text escape=true noentities=true}', '', 'fa fa-folder-o', 'fa fa-folder-open-o');
{/foreach}
