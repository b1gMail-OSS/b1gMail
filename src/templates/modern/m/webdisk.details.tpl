<div data-role="header" data-position="fixed">
	<a href="webdisk.php?folder={$folderID}&sid={$sid}" data-icon="arrow-l" data-direction="reverse" data-transition="slide">{text value=$folderName}</a>
	<h1>{$pageTitle}</h1>
</div>

<div data-role="content">
	<div>
		<img src="webdisk.php?action=displayExtension&ext={$item.ext}&sid={$sid}" style="vertical-align:middle;"/>
		<strong>{$pageTitle}</strong>
	</div>
	<p><small>
			<strong>{lng p="size"}:</strong><br />
			{if $itemType==1}-{else}{size bytes=$item.size}{/if}
		</small></p>
	<p><small>
			<strong>{lng p="created"}:</strong><br />
			{date nice=true timestamp=$item.created}
		</small></p>
	
	<div data-role="controlgroup">
		{if $itemType==1||$item.viewable}<a href="webdisk.php?{if $itemType==1}folder={$item.id}{else}action=downloadFile&id={$item.id}&view=true{/if}&sid={$sid}" {if $itemType==2}data-ajax="false" target="_blank" {/if}data-role="button" data-icon="search">{lng p="view"}</a>{/if}
		<a href="webdisk.php?action={if $itemType==2}downloadFile{else}downloadFolder{/if}&id={$item.id}&sid={$sid}" data-ajax="false" data-role="button" data-icon="arrow-d">{lng p="download"}</a>
		<a href="#delete" data-role="button" data-icon="delete" data-rel="popup" data-position-to="window" data-transition="pop">{lng p="delete"}</a>
	</div>
	
	<div data-role="popup" id="delete" class="ui-content" data-overlay-theme="b" style="max-width:340px;">
		<h3>{lng p="delete"}</h3>
		<p>{lng p="realdel"}</p>
		<a href="webdisk.php?folder={$folderID}&do=deleteItem&type={$itemType}&id={$item.id}&sid={$sid}" data-role="button" data-theme="b" data-icon="delete" data-inline="true" data-mini="true">{lng p="delete"}</a>
		<a href="#" data-role="button" data-rel="back" data-inline="true" data-mini="true">{lng p="cancel"}</a>
	</div>
</div>
