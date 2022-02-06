{hook id="webdisk.sidebar.tpl:head"}
<div class="sidebarHeading">{lng p="iteminfo"}</div>
<div id="webdiskDetailInfoNote" class="webdiskDetailInfo" style="display:block;">{lng p="pleaseselectitem"}</div>
<div id="webdiskDetailInfo" class="webdiskDetailInfo" style="display:none;">
	<table style="border-collapse:collapse;">
		<tr>
			<td rowspan="2" valign="top" width="35"><img src="" border="0" alt="" id="wdExt" /></td>
			<td><b><span id="wdTitle">&nbsp;</span></b>
				<br /><br />
				<small>
					<b>{lng p="size"}:</b><br />
					<span id="wdSize">&nbsp;</span><br /><br />
					<b>{lng p="created"}:</b><br />
					<span id="wdDate">&nbsp;</span><br />
					<span id="wdShared" style="display:none;"><br /><b>{lng p="shared"}</b></span>
					{hook id="webdisk.sidebar.tpl:itemInfo"}
				</small>
			</td>
		</tr>
	</table>
</div>

<div class="sidebarHeading">{lng p="actions"}</div>
<div id="webdiskDetailActionsNote" class="webdiskDetailInfo">{if !$clipboard}{lng p="pleaseselectitem"}{/if}</div>
<div class="contentMenuIcons">
	<div id="webdiskDetailFolderActions" style="display:none;">
		&nbsp;<a href="javascript:void(0);" onclick="switchWebdiskFolder(currentID);"><i class="fa fa-eye" aria-hidden="true"></i> {lng p="view"}</a><br />
		{if $allowShare}&nbsp;<a href="javascript:void(0);" onclick="document.location.href='webdisk.php?action=shareFolder&folder='+currentWebdiskFolderID+'&id=' + currentID + '&sid={$sid}';"><i class="fa fa-share-square-o" aria-hidden="true"></i> {lng p="sharing"}</a><br />{/if}
	</div>
	<div id="webdiskDetailFileActionsView" style="display:none;">
		&nbsp;<a href="javascript:void(0);" onclick="window.open('webdisk.php?action=downloadFile&id='+currentID+'&view=true&sid={$sid}');"><i class="fa fa-eye" aria-hidden="true"></i> {lng p="view"}</a><br />
	</div>
	<div id="webdiskDetailFileActions" style="display:none;">
		&nbsp;<a href="javascript:void(0);" onclick="document.location.href='webdisk.php?action=downloadFile&id='+currentID+'&sid={$sid}';"><i class="fa fa-download" aria-hidden="true"></i> {lng p="download"}</a><br />
	</div>
	<div id="webdiskDetailZIPActions" style="display:none;">
		&nbsp;<a href="javascript:void(0);" onclick="document.location.href='webdisk.php?action=extractFile&id='+currentID+'&folder='+currentWebdiskFolderID+'&sid={$sid}';"><i class="fa fa-file-archive-o" aria-hidden="true"></i> {lng p="extract"}</a><br />
	</div>
	<div id="webdiskDetailActions" style="display:none;">
		&nbsp;<a href="javascript:webdiskRename(currentWebdiskFolderID, currentID, currentType, currentTitle);"><i class="fa fa-i-cursor" aria-hidden="true"></i> {lng p="rename"}</a><br />
		&nbsp;<a href="javascript:void(0);" onclick="if(confirm('{lng p="realdel"}')) document.location.href='webdisk.php?action=deleteItem&type=' + currentType + '&folder='+currentWebdiskFolderID+'&id=' + currentID + '&sid={$sid}';"><i class="fa fa-trash-o" aria-hidden="true"></i> {lng p="delete"}</a><br />
		&nbsp;<a href="javascript:webdiskClipboardAction('copy');" id="wdCopyLink"><i class="fa fa-clipboard" aria-hidden="true"></i> {lng p="copy"}</a><br />
		&nbsp;<a href="javascript:webdiskClipboardAction('cut');" id="wdCutLink"><i class="fa fa-scissors" aria-hidden="true"></i> {lng p="cut"}</a><br />
		{hook id="webdisk.sidebar.tpl:actions.details"}
	</div>
	<div id="webdiskMultiActions" style="display:none;">
		&nbsp;<a href="javascript:void(0);" onclick="EBID('wdMassAction').value='download';transferSelectedWebdiskItems();document.forms.f1.submit();"><i class="fa fa-download" aria-hidden="true"></i> {lng p="download"}</a><br />
		&nbsp;<a href="javascript:void(0);" onclick="if(confirm('{lng p="realdel"}')) {literal}{  EBID('wdMassAction').value='delete';transferSelectedWebdiskItems();document.forms.f1.submit(); }{/literal}"><i class="fa fa-trash-o" aria-hidden="true"></i> {lng p="delete"}</a><br />
		&nbsp;<a href="javascript:webdiskClipboardAction('copy');" id="wdCopyLink2"><i class="fa fa-clipboard" aria-hidden="true"></i> {lng p="copy"}</a><br />
		&nbsp;<a href="javascript:webdiskClipboardAction('cut');" id="wdCutLink2"><i class="fa fa-scissors" aria-hidden="true"></i> {lng p="cut"}</a><br />
		{hook id="webdisk.sidebar.tpl:actions.details"}
	</div>
	{if $clipboard}
		&nbsp;<a id="pasteLink" href="webdisk.php?action=pasteHere&folder={$folderID}&sid={$sid}"><i class="fa fa-clipboard" aria-hidden="true"></i> {lng p="paste"}</a><br />
	{/if}
	{hook id="webdisk.sidebar.tpl:actions"}
</div>

<div class="sidebarHeading">{lng p="uploadfiles"}</div>
<div class="contentMenuIcons">
	<form action="webdisk.php?do=uploadFilesForm&folder={$folderID}&sid={$sid}" method="post" id="fileCountForm" onsubmit="return webdiskShowUploadForm();">
	{lng p="count"}: <input type="text" size="4" value="5" name="fileCount" id="fileCount" />
	<input type="submit" value="{lng p="ok"}" />
	</form>
	
	{hook id="webdisk.sidebar.tpl:upload"}
</div>

{hook id="webdisk.sidebar.tpl:foot"}
