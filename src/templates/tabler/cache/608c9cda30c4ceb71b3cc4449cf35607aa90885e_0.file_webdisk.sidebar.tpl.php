<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:21:09
  from 'file:li/webdisk.sidebar.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a159035a89b80_35113712',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '608c9cda30c4ceb71b3cc4449cf35607aa90885e' => 
    array (
      0 => 'li/webdisk.sidebar.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a159035a89b80_35113712 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"webdisk.sidebar.tpl:head"), $_smarty_tpl);?>


<div class="bm-webdisk-sidebar-section">
	<div class="sidebarHeading"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"iteminfo"), $_smarty_tpl);?>
</div>
	<div id="webdiskDetailInfoNote" class="webdiskDetailInfo bm-webdisk-sidebar-note"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pleaseselectitem"), $_smarty_tpl);?>
</div>
	<div id="webdiskDetailInfo" class="webdiskDetailInfo bm-webdisk-sidebar-detail" style="display:none;">
		<div class="bm-webdisk-detail-title fw-semibold mb-2" id="wdTitle">&nbsp;</div>
		<dl class="bm-webdisk-detail-list mb-0">
			<dt><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"size"), $_smarty_tpl);?>
</dt>
			<dd id="wdSize">&nbsp;</dd>
			<dt><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"created"), $_smarty_tpl);?>
</dt>
			<dd id="wdDate">&nbsp;</dd>
			<dd id="wdShared" class="bm-webdisk-detail-shared" style="display:none;"><strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"shared"), $_smarty_tpl);?>
</strong></dd>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"webdisk.sidebar.tpl:itemInfo"), $_smarty_tpl);?>

		</dl>
	</div>
</div>

<div class="bm-webdisk-sidebar-section">
	<div class="sidebarHeading"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"actions"), $_smarty_tpl);?>
</div>
	<div id="webdiskDetailActionsNote" class="webdiskDetailInfo bm-webdisk-sidebar-note"><?php if (!$_smarty_tpl->getValue('clipboard')) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pleaseselectitem"), $_smarty_tpl);
}?></div>
	<div class="contentMenuIcons bm-webdisk-sidebar-actions">
		<div id="webdiskDetailFolderActions" style="display:none;">
			<a href="javascript:void(0);" onclick="switchWebdiskFolder(currentID);"><i class="ti ti-eye icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"view"), $_smarty_tpl);?>
</a><br />
			<?php if ($_smarty_tpl->getValue('allowShare')) {?><a href="javascript:void(0);" onclick="document.location.href='webdisk.php?action=shareFolder&folder='+currentWebdiskFolderID+'&id=' + currentID + '&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';"><i class="ti ti-share icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sharing"), $_smarty_tpl);?>
</a><br /><?php }?>
		</div>
		<div id="webdiskDetailFileActionsView" style="display:none;">
			<a href="javascript:void(0);" onclick="window.open('webdisk.php?action=downloadFile&id='+currentID+'&view=true&sid=<?php echo $_smarty_tpl->getValue('sid');?>
');"><i class="ti ti-eye icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"view"), $_smarty_tpl);?>
</a><br />
		</div>
		<div id="webdiskDetailFileActions" style="display:none;">
			<a href="javascript:void(0);" onclick="document.location.href='webdisk.php?action=downloadFile&id='+currentID+'&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';"><i class="ti ti-download icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"download"), $_smarty_tpl);?>
</a><br />
		</div>
		<div id="webdiskDetailZIPActions" style="display:none;">
			<a href="javascript:void(0);" onclick="document.location.href='webdisk.php?action=extractFile&id='+currentID+'&folder='+currentWebdiskFolderID+'&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';"><i class="ti ti-file-zip icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"extract"), $_smarty_tpl);?>
</a><br />
		</div>
		<div id="webdiskDetailActions" style="display:none;">
			<a href="javascript:webdiskRename(currentWebdiskFolderID, currentID, currentType, currentTitle);"><i class="ti ti-pencil icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"rename"), $_smarty_tpl);?>
</a><br />
			<a href="javascript:void(0);" onclick="if(confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
')) document.location.href='webdisk.php?action=deleteItem&type=' + currentType + '&folder='+currentWebdiskFolderID+'&id=' + currentID + '&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';"><i class="ti ti-trash icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
</a><br />
			<a href="javascript:webdiskClipboardAction('copy');" id="wdCopyLink"><i class="ti ti-copy icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"copy"), $_smarty_tpl);?>
</a><br />
			<a href="javascript:webdiskClipboardAction('cut');" id="wdCutLink"><i class="ti ti-cut icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cut"), $_smarty_tpl);?>
</a><br />
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"webdisk.sidebar.tpl:actions.details"), $_smarty_tpl);?>

		</div>
		<div id="webdiskMultiActions" style="display:none;">
			<a href="javascript:void(0);" onclick="EBID('wdMassAction').value='download';transferSelectedWebdiskItems();document.forms.f1.submit();"><i class="ti ti-download icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"download"), $_smarty_tpl);?>
</a><br />
			<a href="javascript:void(0);" onclick="if(confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
')) {  EBID('wdMassAction').value='delete';transferSelectedWebdiskItems();document.forms.f1.submit(); }"><i class="ti ti-trash icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
</a><br />
			<a href="javascript:webdiskClipboardAction('copy');" id="wdCopyLink2"><i class="ti ti-copy icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"copy"), $_smarty_tpl);?>
</a><br />
			<a href="javascript:webdiskClipboardAction('cut');" id="wdCutLink2"><i class="ti ti-cut icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cut"), $_smarty_tpl);?>
</a><br />
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"webdisk.sidebar.tpl:actions.details"), $_smarty_tpl);?>

		</div>
		<?php if ($_smarty_tpl->getValue('clipboard')) {?>
			<a id="pasteLink" href="webdisk.php?action=pasteHere&folder=<?php echo $_smarty_tpl->getValue('folderID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="ti ti-clipboard icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"paste"), $_smarty_tpl);?>
</a><br />
		<?php }?>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"webdisk.sidebar.tpl:actions"), $_smarty_tpl);?>

	</div>
</div>

<div class="bm-webdisk-sidebar-section">
	<div class="sidebarHeading"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"uploadfiles"), $_smarty_tpl);?>
</div>
	<div class="contentMenuIcons bm-webdisk-sidebar-upload">
		<form action="webdisk.php?do=uploadFilesForm&folder=<?php echo $_smarty_tpl->getValue('folderID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" method="post" id="fileCountForm" onsubmit="return webdiskShowUploadForm();" class="d-flex flex-wrap align-items-center gap-2">
			<label class="small text-secondary mb-0" for="fileCount"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"count"), $_smarty_tpl);?>
:</label>
			<input type="text" class="form-control form-control-sm" style="width:4rem;" value="5" name="fileCount" id="fileCount" />
			<button type="submit" class="btn btn-sm btn-primary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
		</form>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"webdisk.sidebar.tpl:upload"), $_smarty_tpl);?>

	</div>
</div>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"webdisk.sidebar.tpl:foot"), $_smarty_tpl);?>

<?php }
}
