<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:21:07
  from 'file:li/organizer.notes.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1590331b7923_41245676',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cbb48a9a28d928a9fe0391bc404a3eb92472e6be' => 
    array (
      0 => 'li/organizer.notes.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a1590331b7923_41245676 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-organizer-page bm-organizer-notes">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-notes icon icon-sm" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notes"), $_smarty_tpl);?>

		</div>
	</div>

	<div class="scrollContainer bm-organizer-split">
		<div id="hSep1" class="bm-organizer-notes-panel">
			<form name="f1" method="post" action="organizer.notes.php?action=action&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">
				<div class="scrollContainer withBottomBar bm-organizer-notes-list">
					<div class="table-responsive">
						<table class="table table-vcenter table-hover card-table bm-organizer-table" id="notesTable">
							<thead>
							<tr>
								<th style="width:1.25rem;"><input type="checkbox" class="form-check-input m-0" id="allChecker" onclick="checkAll(this.checked, document.forms.f1);" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
" /></th>
								<th style="width:5rem;">
									<a class="bm-organizer-sort-link" href="organizer.notes.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&sort=priority&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"priority"), $_smarty_tpl);?>
</a>
									<?php if ($_smarty_tpl->getValue('sortColumn') == 'priority') {?><i class="ti ti-arrow-<?php if ($_smarty_tpl->getValue('sortOrder') == 'fa-arrow-down') {?>down<?php } else { ?>up<?php }?> icon icon-sm ms-1 text-primary" aria-hidden="true"></i><?php }?>
								</th>
								<th style="width:9.375rem;">
									<a class="bm-organizer-sort-link" href="organizer.notes.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&sort=date&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"date"), $_smarty_tpl);?>
</a>
									<?php if ($_smarty_tpl->getValue('sortColumn') == 'date') {?><i class="ti ti-arrow-<?php if ($_smarty_tpl->getValue('sortOrder') == 'fa-arrow-down') {?>down<?php } else { ?>up<?php }?> icon icon-sm ms-1 text-primary" aria-hidden="true"></i><?php }?>
								</th>
								<th>
									<a class="bm-organizer-sort-link" href="organizer.notes.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&sort=text&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"text"), $_smarty_tpl);?>
</a>
									<?php if ($_smarty_tpl->getValue('sortColumn') == 'text') {?><i class="ti ti-arrow-<?php if ($_smarty_tpl->getValue('sortOrder') == 'fa-arrow-down') {?>down<?php } else { ?>up<?php }?> icon icon-sm ms-1 text-primary" aria-hidden="true"></i><?php }?>
								</th>
								<th class="bm-organizer-task-col-actions">&nbsp;</th>
							</tr>
							</thead>

							<?php if ($_smarty_tpl->getValue('noteList')) {?>
							<tbody>
							<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('noteList'), 'note', false, 'noteID');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('noteID')->value => $_smarty_tpl->getVariable('note')->value) {
$foreach0DoElse = false;
?>
							<?php $_smarty_tpl->assign('prio', $_smarty_tpl->getValue('note')['priority'], false, NULL);?>
							<tr>
								<td nowrap="nowrap"><input type="checkbox" class="form-check-input m-0" name="note_<?php echo $_smarty_tpl->getValue('noteID');?>
" /></td>
								<td nowrap="nowrap"<?php if ($_smarty_tpl->getValue('sortColumn') == 'priority') {?> class="text-primary fw-semibold"<?php }?>>
									<img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/prio_<?php if ($_smarty_tpl->getValue('note')['priority'] == -1) {?>low<?php } elseif ($_smarty_tpl->getValue('note')['priority'] == 0) {?>normal<?php } else { ?>high<?php }?>.gif" border="0" alt="" align="absmiddle" />
									<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_".((string)$_smarty_tpl->getValue('prio'))), $_smarty_tpl);?>

								</td>
								<td nowrap="nowrap"<?php if ($_smarty_tpl->getValue('sortColumn') == 'date') {?> class="text-primary fw-semibold"<?php }?>>&nbsp;<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('note')['date'],'nice'=>true), $_smarty_tpl);?>
&nbsp;</td>
								<td nowrap="nowrap"<?php if ($_smarty_tpl->getValue('sortColumn') == 'text') {?> class="text-primary fw-semibold"<?php }?>>&nbsp;<a href="javascript:previewNote('<?php echo $_smarty_tpl->getValue('sid');?>
', '<?php echo $_smarty_tpl->getValue('noteID');?>
');"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('note')['text']), $_smarty_tpl);?>
</a>&nbsp;</td>
								<td nowrap="nowrap" class="text-end bm-organizer-task-col-actions">
									<div class="btn-group btn-group-sm bm-organizer-task-actions" role="group" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"actions"), $_smarty_tpl);?>
">
										<a href="organizer.notes.php?action=editNote&id=<?php echo $_smarty_tpl->getValue('noteID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-outline-secondary btn-icon" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
										<a onclick="return confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
');" href="organizer.notes.php?action=deleteNote&id=<?php echo $_smarty_tpl->getValue('noteID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-outline-secondary btn-icon text-danger" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
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
						</table>
					</div>
				</div>

				<div id="contentFooter" class="contentFooter bm-organizer-footer">
					<div class="left d-flex flex-wrap align-items-center gap-2">
						<select class="form-select form-select-sm" name="do" style="width:auto;min-width:10rem;">
							<option value="-">------ <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
 ------</option>
							<option value="delete"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
</option>
						</select>
						<button class="btn btn-sm btn-primary" type="submit"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
					</div>
					<div class="right">
						<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='organizer.notes.php?action=addNote&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
							<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addnote"), $_smarty_tpl);?>

						</button>
					</div>
				</div>
			</form>
		</div>

		<div id="hSepSep"></div>

		<div id="hSep2" class="notePreview bm-organizer-note-preview">
			<div id="notePreview" class="bm-organizer-note-preview-inner bm-organizer-note-preview-empty"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"clicknote"), $_smarty_tpl);?>
</div>
		</div>
	</div>
</div>

<?php echo '<script'; ?>
>
<!--
	registerLoadAction('initHSep(\'notes\')');
<?php if ((true && ($_smarty_tpl->hasVariable('showID') && null !== ($_smarty_tpl->getValue('showID') ?? null)))) {?>
	registerLoadAction('previewNote(\'<?php echo $_smarty_tpl->getValue('sid');?>
\', \'<?php echo $_smarty_tpl->getValue('showID');?>
\')');
<?php }?>
//-->
<?php echo '</script'; ?>
>
<?php }
}
