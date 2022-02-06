<div data-role="header" data-position="fixed">
	{if $parentFolderID>=0}<a href="webdisk.php?folder={$parentFolderID}&sid={$sid}" data-icon="arrow-l" data-direction="reverse" data-transition="slide">{text value=$parentFolderName}</a>{/if}
	<h1>{$pageTitle}</h1>
	<div data-role="controlgroup" data-type="horizontal" class="ui-btn-right">
		<a href="webdisk.php?action=createFolder&folder={$folderID}&sid={$sid}" data-rel="dialog" data-role="button" data-icon="plus">{lng p="folder"}</a>
		<a href="webdisk.php?action=uploadFiles&folder={$folderID}&sid={$sid}" data-rel="dialog" data-role="button" data-icon="plus">{lng p="file"}</a>
	</div>
</div>

<div data-role="content">
	<ul data-role="listview" data-filter="true" data-filter-placeholder="{lng p="search"}..." data-split-icon="gear" data-split-theme="d">	
	{foreach from=$folderContent item=item}
		<li{if $item.type==2} data-icon="false"{/if}>
			<a href="webdisk.php?{if $item.type==1}folder={$item.id}{else}action=downloadFile&id={$item.id}{if $item.viewable}&view=true{/if}{/if}&sid={$sid}"{if $item.type==2} rel="external" target="_blank"{/if} data-transition="slide">
				<img src="webdisk.php?action=displayExtension&ext={$item.ext}&sid={$sid}" class="ui-li-icon" />
				{text value=$item.title}
			</a>
			<a href="webdisk.php?action=itemDetails&type={$item.type}&id={$item.id}&sid={$sid}" data-transition="slide">{lng p="details"}</a>
		</li>
	{/foreach}
	</ul>
</div>

