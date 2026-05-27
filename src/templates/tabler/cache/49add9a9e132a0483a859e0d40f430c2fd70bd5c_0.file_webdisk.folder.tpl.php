<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:47:50
  from 'file:li/webdisk.folder.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15c0a605bd93_58706413',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '49add9a9e132a0483a859e0d40f430c2fd70bd5c' => 
    array (
      0 => 'li/webdisk.folder.tpl',
      1 => 1779810462,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/webdisk.icons.tpl' => 2,
    'file:li/webdisk.sidebar.tpl' => 1,
  ),
))) {
function content_6a15c0a605bd93_58706413 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-webdisk-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-webdisk-header">
		<div class="left">
			<i class="ti ti-cloud icon icon-sm" aria-hidden="true"></i>
			<?php if ($_smarty_tpl->getValue('currentPath')) {?>
			<a href="#" onclick="switchWebdiskFolder(0); return false;" class="bm-webdisk-breadcrumb-root"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"webdisk"), $_smarty_tpl);?>
</a><?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('currentPath'), 'folder', false, 'pathKey', 'pathLoop', array (
  'last' => true,
  'iteration' => true,
  'total' => true,
));
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('pathKey')->value => $_smarty_tpl->getVariable('folder')->value) {
$foreach0DoElse = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_pathLoop']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_pathLoop']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_pathLoop']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_pathLoop']->value['total'];
?> <span class="bm-webdisk-breadcrumb-sep" aria-hidden="true">&raquo;</span> <?php if (($_smarty_tpl->getValue('__smarty_foreach_pathLoop')['last'] ?? null)) {?><span class="bm-webdisk-breadcrumb-current"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['title']), $_smarty_tpl);?>
</span><?php } else { ?><a href="#" onclick="switchWebdiskFolder(<?php echo $_smarty_tpl->getValue('folder')['id'];?>
); return false;" class="bm-webdisk-breadcrumb-link"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['title']), $_smarty_tpl);?>
</a><?php }
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
			<?php } else { ?>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"webdisk"), $_smarty_tpl);?>

			<?php }?>
		</div>
	</div>

	<div class="bm-webdisk-split">
		<div class="bm-webdisk-main">
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"webdisk.folder.tpl:head"), $_smarty_tpl);?>


			<?php if ($_smarty_tpl->getValue('isShared')) {?>
			<form action="email.compose.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
" method="post" name="mailForm">
				<input type="hidden" name="subject" value="<?php if ((true && ($_smarty_tpl->hasVariable('shareMailSubject') && null !== ($_smarty_tpl->getValue('shareMailSubject') ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('shareMailSubject'),'allowEmpty'=>true), $_smarty_tpl);
}?>" />
				<textarea name="text" style="display:none"><?php if ((true && ($_smarty_tpl->hasVariable('shareMail') && null !== ($_smarty_tpl->getValue('shareMail') ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('shareMail'),'allowEmpty'=>true), $_smarty_tpl);
}?></textarea>
			</form>
			<?php }?>

			<form enctype="multipart/form-data" action="webdisk.php?folder=<?php echo $_smarty_tpl->getValue('folderID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" method="post" name="f1" onsubmit="transferSelectedWebdiskItems();" class="bm-webdisk-form">
				<input type="hidden" name="" value="" id="wdAction" />
				<input type="hidden" name="massAction" value="" id="wdMassAction" />
				<input type="hidden" name="selectedWebdiskItems" id="selectedWebdiskItems" value="" />

				<div class="scrollContainer withBottomBar noSelect bm-webdisk-content<?php if (( !$_smarty_tpl->hasVariable('folderContent') || empty($_smarty_tpl->getValue('folderContent'))) && !(true && ($_smarty_tpl->hasVariable('upload') && null !== ($_smarty_tpl->getValue('upload') ?? null)))) {?> bm-webdisk-empty<?php } else { ?> bm-webdisk-has-items<?php }?>" id="wdDnDArea">
					<div id="wdDnDNote" class="bm-webdisk-dnd-note">
						<i class="ti ti-upload icon icon-sm me-1" aria-hidden="true"></i>
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"dragfileshere"), $_smarty_tpl);?>

					</div>
					<?php if ((true && ($_smarty_tpl->hasVariable('upload') && null !== ($_smarty_tpl->getValue('upload') ?? null)))) {?>
					<div class="card bm-webdisk-upload-card">
						<div class="card-header">
							<h3 class="card-title mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"uploadfiles"), $_smarty_tpl);?>
</h3>
						</div>
						<div class="card-body">
							<?php $_smarty_tpl->assign('i', 0, false, NULL);?>
							<?php
$__section_file_0_loop = (is_array(@$_loop=$_smarty_tpl->getValue('upload')) ? count($_loop) : max(0, (int) $_loop));
$__section_file_0_total = $__section_file_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_file'] = new \Smarty\Variable(array());
if ($__section_file_0_total !== 0) {
for ($__section_file_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_file']->value['index'] = 0; $__section_file_0_iteration <= $__section_file_0_total; $__section_file_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_file']->value['index']++){
?>
							<div class="mb-2">
								<input type="file" class="form-control form-control-sm" name="file<?php echo $_smarty_tpl->getValue('i');?>
" />
							</div>
							<?php $_smarty_tpl->assign('i', $_smarty_tpl->getValue('i')+1, false, NULL);?>
							<?php
}
}
?>
							<div class="d-flex align-items-center gap-2 mt-3">
								<i class="ti ti-loader-2 icon icon-sm fa-spin" style="display:none;" id="progressBar" aria-hidden="true"></i>
								<button id="sbButton" class="btn btn-sm btn-primary" type="button" onclick="EBID('wdAction').name='action';EBID('wdAction').value='uploadFiles';EBID('progressBar').style.display='';this.disabled=true;document.forms.f1.submit();"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
							</div>
						</div>
					</div>
					<?php } elseif ($_smarty_tpl->getValue('isShared')) {?>
					<div class="alert alert-info bm-webdisk-share-note">
						<div class="small mb-2"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sharednote"), $_smarty_tpl);?>
</div>
						<div class="d-flex flex-wrap align-items-center gap-2">
							<i class="ti ti-share icon icon-sm" aria-hidden="true"></i>
							<a target="_blank" href="<?php echo $_smarty_tpl->getValue('shareURL');?>
" class="text-break"><?php echo $_smarty_tpl->getValue('shareURL');?>
</a>
							<button type="button" class="btn btn-sm btn-outline-primary ms-auto" onclick="document.forms.mailForm.submit();return(false);">
								<i class="ti ti-mail icon icon-sm me-1" aria-hidden="true"></i>
								<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sendmail2"), $_smarty_tpl);?>

							</button>
						</div>
					</div>
					<?php }?>

					<?php if ($_smarty_tpl->getValue('viewMode') == 'icons') {?>
					<div id="wdContentDiv" class="bm-webdisk-icons-grid">
						<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('folderContent'), 'item');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach1DoElse = false;
?>
						<div class="bm-webdisk-icon-item card<?php if ($_smarty_tpl->getValue('item')['type'] == 1) {?> bm-webdisk-icon-item-folder<?php }?>">
							<a id="wli_<?php echo $_smarty_tpl->getValue('item')['type'];?>
_<?php echo $_smarty_tpl->getValue('item')['id'];?>
"
								class="webdiskItem bm-webdisk-item card-body"
								title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('item')['title']), $_smarty_tpl);?>
">
								<span class="bm-webdisk-item-icon-wrap">
									<?php $_smarty_tpl->assign('wdicons_size_class', 'bm-webdisk-icon-lg', false, 32);?>
									<?php $_smarty_tpl->assign('wdicons_additionalparam', 'draggable="true"', false, 32);?>
									<?php $_smarty_tpl->assign('wdicons_imgattr', '', false, 32);?>
									<?php $_smarty_tpl->renderSubTemplate("file:li/webdisk.icons.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
								</span>
								<span id="wd_<?php echo $_smarty_tpl->getValue('item')['type'];?>
_<?php echo $_smarty_tpl->getValue('item')['id'];?>
" class="bm-webdisk-item-title" draggable="false"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('item')['title'],'cut'=>20), $_smarty_tpl);?>
</span>
								<small class="bm-webdisk-item-meta" draggable="false"><?php if ($_smarty_tpl->getValue('item')['type'] == 1) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"folder"), $_smarty_tpl);
} else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('item')['size']), $_smarty_tpl);
}?></small>
							</a>
						</div>
						<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					</div>
					<?php } else { ?>
					<div class="table-responsive">
						<table class="table table-vcenter table-hover card-table bm-organizer-table" id="wdContentTable">
							<thead>
							<tr>
								<th style="width:2.5rem;">&nbsp;</th>
								<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"filename"), $_smarty_tpl);?>
</th>
								<th style="width:9.375rem;"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"created"), $_smarty_tpl);?>
</th>
								<th style="width:6rem;"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"size"), $_smarty_tpl);?>
</th>
								<th style="width:7rem;"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"type"), $_smarty_tpl);?>
</th>
							</tr>
							</thead>
							<tbody>
							<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('folderContent'), 'item');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach2DoElse = false;
?>
							<tr id="wli_<?php echo $_smarty_tpl->getValue('item')['type'];?>
_<?php echo $_smarty_tpl->getValue('item')['id'];?>
">
								<td class="text-center">
									<?php $_smarty_tpl->assign('wdicons_size_class', 'bm-webdisk-icon-sm', false, 32);?>
									<?php $_smarty_tpl->assign('wdicons_additionalparam', 'draggable="true"', false, 32);?>
									<?php $_smarty_tpl->assign('wdicons_imgattr', '', false, 32);?>
									<?php $_smarty_tpl->renderSubTemplate("file:li/webdisk.icons.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
								</td>
								<td nowrap="nowrap" style="cursor:default;" id="wd_<?php echo $_smarty_tpl->getValue('item')['type'];?>
_<?php echo $_smarty_tpl->getValue('item')['id'];?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('item')['title']), $_smarty_tpl);?>
</td>
								<td nowrap="nowrap"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('item')['created'],'nice'=>true), $_smarty_tpl);?>
</td>
								<td nowrap="nowrap"><?php if ($_smarty_tpl->getValue('item')['type'] == 1) {?>-<?php } else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('item')['size']), $_smarty_tpl);
}?></td>
								<td nowrap="nowrap"><?php if ($_smarty_tpl->getValue('item')['type'] == 1) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"folder"), $_smarty_tpl);
} elseif ($_smarty_tpl->getValue('item')['ext'] == '?') {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"file"), $_smarty_tpl);
} else { ?>.<?php echo $_smarty_tpl->getValue('item')['ext'];?>
-<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"file"), $_smarty_tpl);
}?></td>
							</tr>
							<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
							</tbody>
						</table>
					</div>
					<?php }?>
				</div>

				<div id="contentFooter" class="contentFooter bm-organizer-footer bm-webdisk-footer">
					<div class="bm-webdisk-footer-row">
					<div class="left bm-organizer-footer-actions bm-webdisk-footer-actions">
						<div class="input-group input-group-sm bm-organizer-action-group">
							<select class="form-select" id="massAction" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
">
								<option value="-">------ <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
 ------</option>
								<option value="download"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"download"), $_smarty_tpl);?>
</option>
								<option value="delete"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
</option>
								<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"webdisk.folder.tpl:select"), $_smarty_tpl);?>

							</select>
							<button type="button" class="btn btn-primary" onclick="EBID('wdMassAction').value=EBID('massAction').value;transferSelectedWebdiskItems();document.forms.f1.submit();"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
						</div>
					</div>
					</div>
				</div>
			</form>

			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"webdisk.folder.tpl:foot"), $_smarty_tpl);?>


			<?php if (!(true && (true && null !== ($_POST['inline'] ?? null)))) {?>
			<?php echo '<script'; ?>
 src="./clientlib/dndupload.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"../../clientlib/dndupload.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
			<?php echo '<script'; ?>
>
			<?php if ($_smarty_tpl->getValue('hotkeys')) {?>
				registerLoadAction('registerWebdiskFolderHotkeyHandler()');
			<?php }?>
				initDnDUpload(EBID('mainContent'), 'webdisk.php?sid='+currentSID+'&folder=<?php echo $_smarty_tpl->getValue('folderID');?>
&action=dndUpload', function() { document.location.href='webdisk.php?sid='+currentSID+'&folder=<?php echo $_smarty_tpl->getValue('folderID');?>
'; });
				currentWebdiskFolderID = <?php echo $_smarty_tpl->getValue('folderID');?>
;
				var treeID = webdiskGetTreeIDbyFolderID(<?php echo $_smarty_tpl->getValue('folderID');?>
);
				if(treeID > 0) {
					webdisk_d.openTo(treeID, true);
				}
				initWDSel();
			<?php echo '</script'; ?>
>
			<?php }?>
		</div>

		<div id="rightSidebar" class="bm-webdisk-sidebar">
			<?php $_smarty_tpl->renderSubTemplate("file:li/webdisk.sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
		</div>

		<div id="folderLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>
	</div>
</div>
<?php }
}
