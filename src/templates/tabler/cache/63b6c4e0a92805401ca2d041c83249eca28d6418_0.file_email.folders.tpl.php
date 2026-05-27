<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:12:59
  from 'file:li/email.folders.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15b87b267682_48559689',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '63b6c4e0a92805401ca2d041c83249eca28d6418' => 
    array (
      0 => 'li/email.folders.tpl',
      1 => 1779808369,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a15b87b267682_48559689 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-folder-admin">
	<div id="contentHeader" class="contentHeader bm-folder-admin-header">
		<div class="left">
			<i class="ti ti-folders icon icon-sm" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"folderadmin"), $_smarty_tpl);?>

		</div>
		<div class="right">
			<button class="btn btn-sm btn-outline-primary" onclick="document.location.href='email.folders.php?action=addFolder&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';" type="button">
				<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addfolder"), $_smarty_tpl);?>

			</button>
		</div>
	</div>

	<form name="f1" method="post" action="email.folders.php?action=action&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="card bm-folder-admin-card">
		<div class="table-responsive">
			<table class="table table-vcenter table-hover card-table bm-folder-admin-table" id="folderAdminTable">
				<thead>
				<tr>
					<th class="bm-folder-col-check"><input type="checkbox" class="form-check-input m-0" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'folder');" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
" /></th>
					<th class="bm-folder-col-title">
						<a class="bm-folder-sort-link" href="email.folders.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&sort=titel&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"title"), $_smarty_tpl);?>
</a>
						<?php if ($_smarty_tpl->getValue('sortColumn') == 'titel') {?><i class="ti ti-arrow-<?php if ($_smarty_tpl->getValue('sortOrder') == 'fa-arrow-down') {?>down<?php } else { ?>up<?php }?> icon icon-sm ms-1 text-primary" aria-hidden="true"></i><?php }?>
					</th>
					<th class="bm-folder-col-parent d-none d-md-table-cell">
						<a class="bm-folder-sort-link" href="email.folders.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&sort=parent&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"parentfolder"), $_smarty_tpl);?>
</a>
						<?php if ($_smarty_tpl->getValue('sortColumn') == 'parent') {?><i class="ti ti-arrow-<?php if ($_smarty_tpl->getValue('sortOrder') == 'fa-arrow-down') {?>down<?php } else { ?>up<?php }?> icon icon-sm ms-1 text-primary" aria-hidden="true"></i><?php }?>
					</th>
					<th class="bm-folder-col-size d-none d-lg-table-cell"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"size"), $_smarty_tpl);?>
</th>
					<th class="bm-folder-col-status"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"status"), $_smarty_tpl);?>
</th>
					<th class="bm-folder-col-subscribed">
						<a class="bm-folder-sort-link" href="email.folders.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&sort=subscribed&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"subscribed"), $_smarty_tpl);?>
</a>
						<?php if ($_smarty_tpl->getValue('sortColumn') == 'subscribed') {?><i class="ti ti-arrow-<?php if ($_smarty_tpl->getValue('sortOrder') == 'fa-arrow-down') {?>down<?php } else { ?>up<?php }?> icon icon-sm ms-1 text-primary" aria-hidden="true"></i><?php }?>
					</th>
					<th class="bm-folder-col-actions"></th>
				</tr>
				</thead>

				<?php if (!( !$_smarty_tpl->hasVariable('sysFolderList') || empty($_smarty_tpl->getValue('sysFolderList')))) {?>
				<tr class="bm-folder-section-row">
					<td colspan="7">
						<button type="button" class="bm-folder-section-toggle" onclick="toggleGroup('sys');">
							<i class="ti ti-chevron-down icon icon-sm" id="groupImage_sys" aria-hidden="true"></i>
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sysfolders"), $_smarty_tpl);?>

						</button>
					</td>
				</tr>
				<tbody id="group_sys" class="bm-folder-section-body">
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('sysFolderList'), 'folder', false, 'folderID');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('folderID')->value => $_smarty_tpl->getVariable('folder')->value) {
$foreach0DoElse = false;
?>
				<tr class="bm-folder-row">
					<td class="bm-folder-col-check"><input type="checkbox" class="form-check-input m-0" disabled="disabled" aria-hidden="true" /></td>
					<td class="bm-folder-col-title<?php if ($_smarty_tpl->getValue('sortColumn') == 'titel') {?> bm-folder-col-sorted<?php }?>">
						<a class="bm-folder-title-link" href="email.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&folder=<?php echo $_smarty_tpl->getValue('folderID');?>
">
							<span class="bm-folder-title-icon"><i class="ti <?php if ($_smarty_tpl->getValue('folder')['type'] == 'inbox') {?>ti-inbox<?php } elseif ($_smarty_tpl->getValue('folder')['type'] == 'outbox') {?>ti-send<?php } elseif ($_smarty_tpl->getValue('folder')['type'] == 'drafts') {?>ti-file-pencil<?php } elseif ($_smarty_tpl->getValue('folder')['type'] == 'spam') {?>ti-ban<?php } elseif ($_smarty_tpl->getValue('folder')['type'] == 'trash') {?>ti-trash<?php } else { ?>ti-folder<?php }?> icon" aria-hidden="true"></i></span>
							<span class="bm-folder-title-text">
								<span class="bm-folder-name"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['titel'],'cut'=>40), $_smarty_tpl);?>
</span>
								<?php if ((true && (true && null !== ($_smarty_tpl->getValue('folder')['parent'] ?? null)))) {?><span class="bm-folder-parent d-md-none"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['parent'],'cut'=>20), $_smarty_tpl);?>
</span><?php }?>
							</span>
						</a>
					</td>
					<td class="bm-folder-col-parent d-none d-md-table-cell"><?php if ((true && (true && null !== ($_smarty_tpl->getValue('folder')['parent'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['parent'],'cut'=>20), $_smarty_tpl);
}?></td>
					<td class="bm-folder-col-size d-none d-lg-table-cell text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('folder')['size']), $_smarty_tpl);?>
</td>
					<td class="bm-folder-col-status">
						<div class="bm-folder-stats">
							<span class="badge bg-secondary-lt bm-folder-stat" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"all"), $_smarty_tpl);?>
"><i class="ti ti-mail icon icon-sm" aria-hidden="true"></i><?php echo $_smarty_tpl->getValue('folder')['allMails'];?>
</span>
							<span class="badge bg-azure-lt bm-folder-stat" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"unread"), $_smarty_tpl);?>
"><i class="ti ti-mail-opened icon icon-sm" aria-hidden="true"></i><?php echo $_smarty_tpl->getValue('folder')['unreadMails'];?>
</span>
							<span class="badge bg-yellow-lt bm-folder-stat" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"flagged"), $_smarty_tpl);?>
"><i class="ti ti-flag icon icon-sm" aria-hidden="true"></i><?php echo $_smarty_tpl->getValue('folder')['flaggedMails'];?>
</span>
						</div>
					</td>
					<td class="bm-folder-col-subscribed"><input type="checkbox" class="form-check-input m-0" checked="checked" disabled="disabled" aria-hidden="true" /></td>
					<td class="bm-folder-col-actions">
						<a href="email.folders.php?action=editFolder&id=<?php echo $_smarty_tpl->getValue('folderID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-sm btn-ghost-secondary btn-icon" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
					</td>
				</tr>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</tbody>
				<?php }?>

				<?php if ($_smarty_tpl->getValue('theFolderList')) {?>
				<tr class="bm-folder-section-row">
					<td colspan="7">
						<button type="button" class="bm-folder-section-toggle" onclick="toggleGroup('own');">
							<i class="ti ti-chevron-down icon icon-sm" id="groupImage_own" aria-hidden="true"></i>
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ownfolders"), $_smarty_tpl);?>

						</button>
					</td>
				</tr>
				<tbody id="group_own" class="bm-folder-section-body">
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('theFolderList'), 'folder', false, 'folderID');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('folderID')->value => $_smarty_tpl->getVariable('folder')->value) {
$foreach1DoElse = false;
?>
				<tr class="bm-folder-row">
					<td class="bm-folder-col-check"><input type="checkbox" class="form-check-input m-0" id="folder_<?php echo $_smarty_tpl->getValue('folderID');?>
" name="folder_<?php echo $_smarty_tpl->getValue('folderID');?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['titel'],'cut'=>40), $_smarty_tpl);?>
" /></td>
					<td class="bm-folder-col-title<?php if ($_smarty_tpl->getValue('sortColumn') == 'titel') {?> bm-folder-col-sorted<?php }?>">
						<a class="bm-folder-title-link" href="email.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&folder=<?php echo $_smarty_tpl->getValue('folderID');?>
">
							<span class="bm-folder-title-icon"><i class="ti <?php if ($_smarty_tpl->getValue('folder')['intelligent'] == 1) {?>ti-folder-cog<?php } else { ?>ti-folder<?php }?> icon" aria-hidden="true"></i></span>
							<span class="bm-folder-title-text">
								<span class="bm-folder-name"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['titel'],'cut'=>40), $_smarty_tpl);?>
</span>
								<?php if ((true && (true && null !== ($_smarty_tpl->getValue('folder')['parent'] ?? null)))) {?><span class="bm-folder-parent d-md-none"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['parent'],'cut'=>20), $_smarty_tpl);?>
</span><?php }?>
							</span>
						</a>
					</td>
					<td class="bm-folder-col-parent d-none d-md-table-cell<?php if ($_smarty_tpl->getValue('sortColumn') == 'parent') {?> bm-folder-col-sorted<?php }?>"><?php if ((true && (true && null !== ($_smarty_tpl->getValue('folder')['parent'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['parent'],'cut'=>20), $_smarty_tpl);
}?></td>
					<td class="bm-folder-col-size d-none d-lg-table-cell text-secondary"><?php if ($_smarty_tpl->getValue('folder')['intelligent']) {?>-<?php } else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('folder')['size']), $_smarty_tpl);
}?></td>
					<td class="bm-folder-col-status">
						<div class="bm-folder-stats">
							<span class="badge bg-secondary-lt bm-folder-stat" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"all"), $_smarty_tpl);?>
"><i class="ti ti-mail icon icon-sm" aria-hidden="true"></i><?php echo $_smarty_tpl->getValue('folder')['allMails'];?>
</span>
							<span class="badge bg-azure-lt bm-folder-stat" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"unread"), $_smarty_tpl);?>
"><i class="ti ti-mail-opened icon icon-sm" aria-hidden="true"></i><?php echo $_smarty_tpl->getValue('folder')['unreadMails'];?>
</span>
							<span class="badge bg-yellow-lt bm-folder-stat" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"flagged"), $_smarty_tpl);?>
"><i class="ti ti-flag icon icon-sm" aria-hidden="true"></i><?php echo $_smarty_tpl->getValue('folder')['flaggedMails'];?>
</span>
						</div>
					</td>
					<td class="bm-folder-col-subscribed<?php if ($_smarty_tpl->getValue('sortColumn') == 'subscribed') {?> bm-folder-col-sorted<?php }?>"><input type="checkbox" class="form-check-input m-0" <?php if ($_smarty_tpl->getValue('folder')['subscribed'] == 1) {?>checked="checked" <?php }?> onchange="updateFolderSubscription('<?php echo $_smarty_tpl->getValue('folderID');?>
', this, '<?php echo $_smarty_tpl->getValue('sid');?>
')" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"subscribed"), $_smarty_tpl);?>
" /></td>
					<td class="bm-folder-col-actions">
						<div class="btn-list flex-nowrap justify-content-end">
							<a href="email.folders.php?action=editFolder&id=<?php echo $_smarty_tpl->getValue('folderID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-sm btn-ghost-secondary btn-icon" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
							<a onclick="return confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
');" href="email.folders.php?action=deleteFolder&id=<?php echo $_smarty_tpl->getValue('folderID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-sm btn-ghost-secondary btn-icon text-danger" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
						</div>
					</td>
				</tr>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</tbody>
				<?php }?>

				<?php if ($_smarty_tpl->getValue('sharedFolderList')) {?>
				<tr class="bm-folder-section-row">
					<td colspan="7">
						<button type="button" class="bm-folder-section-toggle" onclick="toggleGroup('shared');">
							<i class="ti ti-chevron-down icon icon-sm" id="groupImage_shared" aria-hidden="true"></i>
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sharedfolders"), $_smarty_tpl);?>

						</button>
					</td>
				</tr>
				<tbody id="group_shared" class="bm-folder-section-body">
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('sharedFolderList'), 'folder', false, 'folderID');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('folderID')->value => $_smarty_tpl->getVariable('folder')->value) {
$foreach2DoElse = false;
?>
				<tr class="bm-folder-row">
					<td class="bm-folder-col-check"><input type="checkbox" class="form-check-input m-0" id="folder_<?php echo $_smarty_tpl->getValue('folderID');?>
" name="folder_<?php echo $_smarty_tpl->getValue('folderID');?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['titel'],'cut'=>40), $_smarty_tpl);?>
" /></td>
					<td class="bm-folder-col-title<?php if ($_smarty_tpl->getValue('sortColumn') == 'titel') {?> bm-folder-col-sorted<?php }?>">
						<a class="bm-folder-title-link" href="email.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&folder=<?php echo $_smarty_tpl->getValue('folderID');?>
">
							<span class="bm-folder-title-icon"><i class="ti ti-share-3 icon" aria-hidden="true"></i></span>
							<span class="bm-folder-title-text">
								<span class="bm-folder-name"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['titel'],'cut'=>40), $_smarty_tpl);
if ($_smarty_tpl->getValue('folder')['readonly']) {?> <span class="text-secondary fw-normal">(<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"readonly"), $_smarty_tpl);?>
)</span><?php }?></span>
								<?php if ($_smarty_tpl->getValue('folder')['parent']) {?><span class="bm-folder-parent d-md-none"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['parent'],'cut'=>20), $_smarty_tpl);?>
</span><?php }?>
							</span>
						</a>
					</td>
					<td class="bm-folder-col-parent d-none d-md-table-cell<?php if ($_smarty_tpl->getValue('sortColumn') == 'parent') {?> bm-folder-col-sorted<?php }?>"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['parent'],'cut'=>20), $_smarty_tpl);?>
</td>
					<td class="bm-folder-col-size d-none d-lg-table-cell text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('folder')['size']), $_smarty_tpl);?>
</td>
					<td class="bm-folder-col-status">
						<div class="bm-folder-stats">
							<span class="badge bg-secondary-lt bm-folder-stat" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"all"), $_smarty_tpl);?>
"><i class="ti ti-mail icon icon-sm" aria-hidden="true"></i><?php echo $_smarty_tpl->getValue('folder')['allMails'];?>
</span>
							<span class="badge bg-azure-lt bm-folder-stat" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"unread"), $_smarty_tpl);?>
"><i class="ti ti-mail-opened icon icon-sm" aria-hidden="true"></i><?php echo $_smarty_tpl->getValue('folder')['unreadMails'];?>
</span>
							<span class="badge bg-yellow-lt bm-folder-stat" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"flagged"), $_smarty_tpl);?>
"><i class="ti ti-flag icon icon-sm" aria-hidden="true"></i><?php echo $_smarty_tpl->getValue('folder')['flaggedMails'];?>
</span>
						</div>
					</td>
					<td class="bm-folder-col-subscribed<?php if ($_smarty_tpl->getValue('sortColumn') == 'subscribed') {?> bm-folder-col-sorted<?php }?>"><input type="checkbox" class="form-check-input m-0" <?php if ($_smarty_tpl->getValue('folder')['subscribed'] == 1) {?>checked="checked" <?php }?> disabled="disabled" aria-hidden="true" /></td>
					<td class="bm-folder-col-actions"></td>
				</tr>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</tbody>
				<?php }?>
			</table>
		</div>

		<div class="card-footer bm-folder-admin-footer">
			<div class="bm-folder-admin-footer-row">
				<div class="input-group input-group-sm bm-folder-action-group">
					<select class="form-select" name="do" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
">
						<option value="-"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
</option>
						<option value="delete"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
</option>
					</select>
					<button type="submit" class="btn btn-primary btn-sm bm-folder-footer-ok" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
">
						<i class="ti ti-check bm-folder-footer-ok-icon" aria-hidden="true"></i>
						<span class="bm-folder-footer-ok-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</span>
					</button>
				</div>
			</div>
		</div>
	</form>
</div>
<?php }
}
