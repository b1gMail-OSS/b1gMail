{hook id="webdisk.sidebar.tpl:head"}

<div class="bm-webdisk-sidebar-section">
	<div class="sidebarHeading">{lng p="iteminfo"}</div>
	<div id="webdiskDetailInfoNote" class="webdiskDetailInfo bm-webdisk-sidebar-note">{lng p="pleaseselectitem"}</div>
	<div id="webdiskDetailInfo" class="webdiskDetailInfo bm-webdisk-sidebar-detail" style="display:none;">
		<div class="bm-webdisk-detail-title fw-semibold mb-2" id="wdTitle">&nbsp;</div>
		<dl class="bm-webdisk-detail-list mb-0">
			<dt>{lng p="size"}</dt>
			<dd id="wdSize">&nbsp;</dd>
			<dt>{lng p="created"}</dt>
			<dd id="wdDate">&nbsp;</dd>
			<dd id="wdShared" class="bm-webdisk-detail-shared" style="display:none;"><strong>{lng p="shared"}</strong></dd>
			{hook id="webdisk.sidebar.tpl:itemInfo"}
		</dl>
	</div>
</div>

<div class="bm-webdisk-sidebar-section">
	<div class="sidebarHeading">{lng p="actions"}</div>
	<div id="webdiskDetailActionsNote" class="webdiskDetailInfo bm-webdisk-sidebar-note">{if !$clipboard}{lng p="pleaseselectitem"}{/if}</div>
	<div class="contentMenuIcons bm-webdisk-sidebar-actions">
		<div id="webdiskDetailFolderActions" style="display:none;">
			<a href="javascript:void(0);" onclick="switchWebdiskFolder(currentID);"><i class="ti ti-eye icon icon-sm me-1" aria-hidden="true"></i>{lng p="view"}</a><br />
			<a href="javascript:void(0);" onclick="webdiskDownloadCurrent();"><i class="ti ti-download icon icon-sm me-1" aria-hidden="true"></i>{lng p="download"}</a><br />
			{if $allowShare}
			<a href="javascript:void(0);" id="wdShareLink" onclick="document.location.href='webdisk.php?action=shareFolder&folder='+currentWebdiskFolderID+'&id=' + currentID + '&sid={$sid}';"><i class="ti ti-share icon icon-sm me-1" aria-hidden="true"></i>{lng p="sharing"}</a><br />
			<a href="javascript:void(0);" id="wdStopShareLink" style="display:none;" onclick="webdiskStopShare();"><i class="ti ti-share-off icon icon-sm me-1" aria-hidden="true"></i>{lng p="stopsharing"}</a><br />
			{/if}
		</div>
		<div id="webdiskDetailFileActionsView" style="display:none;">
			<a href="javascript:void(0);" onclick="webdiskOpenPreview(currentID);"><i class="ti ti-eye icon icon-sm me-1" aria-hidden="true"></i>{lng p="view"}</a><br />
		</div>
		<div id="webdiskDetailFileActions" style="display:none;">
			<a href="javascript:void(0);" onclick="webdiskDownloadCurrent();"><i class="ti ti-download icon icon-sm me-1" aria-hidden="true"></i>{lng p="download"}</a><br />
			{if $allowShare}
			<a href="javascript:void(0);" id="wdShareFileLink" onclick="document.location.href='webdisk.php?action=shareFile&id=' + currentID + '&sid={$sid}';"><i class="ti ti-share icon icon-sm me-1" aria-hidden="true"></i>{lng p="sharing"}</a><br />
			<a href="javascript:void(0);" id="wdStopFileShareLink" style="display:none;" onclick="webdiskStopFileShare();"><i class="ti ti-share-off icon icon-sm me-1" aria-hidden="true"></i>{lng p="stopsharing"}</a><br />
			{/if}
		</div>
		<div id="webdiskDetailZIPActions" style="display:none;">
			<a href="javascript:void(0);" onclick="document.location.href='webdisk.php?action=extractFile&id='+currentID+'&folder='+currentWebdiskFolderID+'&sid={$sid}';"><i class="ti ti-file-zip icon icon-sm me-1" aria-hidden="true"></i>{lng p="extract"}</a><br />
		</div>
		<div id="webdiskDetailActions" style="display:none;">
			<a href="javascript:webdiskRename(currentWebdiskFolderID, currentID, currentType, currentTitle);"><i class="ti ti-pencil icon icon-sm me-1" aria-hidden="true"></i>{lng p="rename"}</a><br />
			<a href="javascript:void(0);" onclick="if(confirm('{lng p="realdel"}')) document.location.href='webdisk.php?action=deleteItem&type=' + currentType + '&folder='+currentWebdiskFolderID+'&id=' + currentID + '&sid={$sid}';"><i class="ti ti-trash icon icon-sm me-1" aria-hidden="true"></i>{lng p="delete"}</a><br />
			<a href="javascript:webdiskClipboardAction('copy');" id="wdCopyLink"><i class="ti ti-copy icon icon-sm me-1" aria-hidden="true"></i>{lng p="copy"}</a><br />
			<a href="javascript:webdiskClipboardAction('cut');" id="wdCutLink"><i class="ti ti-cut icon icon-sm me-1" aria-hidden="true"></i>{lng p="cut"}</a><br />
			{hook id="webdisk.sidebar.tpl:actions.details"}
		</div>
		<div id="webdiskMultiActions" style="display:none;">
			<a href="javascript:void(0);" onclick="webdiskMassDownload();"><i class="ti ti-download icon icon-sm me-1" aria-hidden="true"></i>{lng p="download"}</a><br />
			<a href="javascript:void(0);" onclick="if(confirm('{lng p="realdel"}')) {literal}{  EBID('wdMassAction').value='delete';transferSelectedWebdiskItems();document.forms.f1.submit(); }{/literal}"><i class="ti ti-trash icon icon-sm me-1" aria-hidden="true"></i>{lng p="delete"}</a><br />
			<a href="javascript:webdiskClipboardAction('copy');" id="wdCopyLink2"><i class="ti ti-copy icon icon-sm me-1" aria-hidden="true"></i>{lng p="copy"}</a><br />
			<a href="javascript:webdiskClipboardAction('cut');" id="wdCutLink2"><i class="ti ti-cut icon icon-sm me-1" aria-hidden="true"></i>{lng p="cut"}</a><br />
			{hook id="webdisk.sidebar.tpl:actions.details"}
		</div>
		{if $clipboard}
			<a id="pasteLink" href="webdisk.php?action=pasteHere&folder={$folderID}&sid={$sid}"><i class="ti ti-clipboard icon icon-sm me-1" aria-hidden="true"></i>{lng p="paste"}</a><br />
		{/if}
		{hook id="webdisk.sidebar.tpl:actions"}
	</div>
</div>

{hook id="webdisk.sidebar.tpl:upload"}
{hook id="webdisk.sidebar.tpl:foot"}
