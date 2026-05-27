<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:01:59
  from 'file:li/email.folders.edit.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a158bb79c6848_77220932',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '97140c4a7c4da24112accec665c30b08f1dbbc9e' => 
    array (
      0 => 'li/email.folders.edit.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a158bb79c6848_77220932 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-folder-admin">
	<div id="contentHeader" class="contentHeader bm-folder-admin-header">
		<div class="left">
			<i class="ti ti-folders icon icon-sm" aria-hidden="true"></i>
			<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"editfolder"), $_smarty_tpl);
} else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addfolder"), $_smarty_tpl);
}?>
		</div>
	</div>

	<form name="f1" method="post" action="email.folders.php?action=<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null)))) {?>saveFolder&id=<?php echo $_smarty_tpl->getValue('folder')['id'];
} else { ?>createFolder<?php }?>&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="card bm-folder-admin-card bm-folder-edit-form" onsubmit="<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) && $_smarty_tpl->getValue('folder')['intelligent'] == 1) {?>if(!formSubmitOK) { parent.frames.condition_frame.document.forms.saveForm.elements.submitParent.value='1';parent.frames.condition_frame.document.forms.saveForm.submit();return(false); }<?php }?>return(checkFolderForm(this));">
		<div class="card-body bm-folder-edit-body">
			<div class="bm-folder-edit-fields">
				<div class="bm-folder-edit-row">
					<label class="bm-folder-edit-label required" for="titel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"title"), $_smarty_tpl);?>
</label>
					<div class="bm-folder-edit-field">
						<input type="text" class="form-control form-control-sm" name="titel" id="titel" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>(($tmp = $_smarty_tpl->getValue('folder')['titel'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp),'allowEmpty'=>true), $_smarty_tpl);?>
" />
					</div>
				</div>

				<div class="bm-folder-edit-row">
					<label class="bm-folder-edit-label" for="parentfolder"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"parentfolder"), $_smarty_tpl);?>
</label>
					<div class="bm-folder-edit-field">
						<select class="form-select form-select-sm" name="parentfolder" id="parentfolder">
							<option value="-1">------------</option>
						<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('dropdownFolderList'), 'dFolderTitle', false, 'dFolderID');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('dFolderID')->value => $_smarty_tpl->getVariable('dFolderTitle')->value) {
$foreach0DoElse = false;
if ($_smarty_tpl->getValue('dFolderID') > 0 && (!(true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) || $_smarty_tpl->getValue('dFolderID') != $_smarty_tpl->getValue('folder')['id'])) {?>
							<option value="<?php echo $_smarty_tpl->getValue('dFolderID');?>
" style="font-family:courier;"<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) && $_smarty_tpl->getValue('folder')['parent'] == $_smarty_tpl->getValue('dFolderID')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getValue('dFolderTitle');?>
</option>
						<?php }
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
						</select>
					</div>
				</div>

				<div class="bm-folder-edit-row">
					<label class="bm-folder-edit-label" for="storetime"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"storetime"), $_smarty_tpl);?>
</label>
					<div class="bm-folder-edit-field">
						<select class="form-select form-select-sm" name="storetime" id="storetime"<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) && $_smarty_tpl->getValue('folder')['intelligent'] == 1) {?> disabled="disabled"<?php }?>>
							<option value="-1">------------</option>
							<option value="86400"<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) && $_smarty_tpl->getValue('folder')['storetime'] == 86400) {?> selected="selected"<?php }?>>1 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"days"), $_smarty_tpl);?>
</option>
							<option value="172800"<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) && $_smarty_tpl->getValue('folder')['storetime'] == 172800) {?> selected="selected"<?php }?>>2 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"days"), $_smarty_tpl);?>
</option>
							<option value="432000"<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) && $_smarty_tpl->getValue('folder')['storetime'] == 432000) {?> selected="selected"<?php }?>>5 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"days"), $_smarty_tpl);?>
</option>
							<option value="604800"<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) && $_smarty_tpl->getValue('folder')['storetime'] == 604800) {?> selected="selected"<?php }?>>7 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"days"), $_smarty_tpl);?>
</option>
							<option value="1209600"<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) && $_smarty_tpl->getValue('folder')['storetime'] == 1209600) {?> selected="selected"<?php }?>>2 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"weeks"), $_smarty_tpl);?>
</option>
							<option value="2419200"<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) && $_smarty_tpl->getValue('folder')['storetime'] == 2419200) {?> selected="selected"<?php }?>>4 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"weeks"), $_smarty_tpl);?>
</option>
							<option value="4838400"<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) && $_smarty_tpl->getValue('folder')['storetime'] == 4838400) {?> selected="selected"<?php }?>>2 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"months"), $_smarty_tpl);?>
</option>
						</select>
					</div>
				</div>

				<div class="bm-folder-edit-row">
					<label class="bm-folder-edit-label" for="subscribed"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"subscribed"), $_smarty_tpl);?>
</label>
					<div class="bm-folder-edit-field">
						<label class="form-check mb-0">
							<input type="checkbox" class="form-check-input" id="subscribed" name="subscribed"<?php if (!(true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) || $_smarty_tpl->getValue('folder')['subscribed'] == 1) {?> checked="checked"<?php }?> />
						</label>
					</div>
				</div>

				<div class="bm-folder-edit-row">
					<label class="bm-folder-edit-label" for="intelligent"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"intelligent"), $_smarty_tpl);?>
</label>
					<div class="bm-folder-edit-field">
						<label class="form-check mb-0">
							<input type="checkbox" class="form-check-input" id="intelligent" name="intelligent"<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null)))) {?> readonly="readonly" disabled="disabled"<?php if ($_smarty_tpl->getValue('folder')['intelligent'] == 1) {?> checked="checked"<?php }
}?> />
						</label>
					</div>
				</div>

				<?php if ((true && ($_smarty_tpl->hasVariable('folder') && null !== ($_smarty_tpl->getValue('folder') ?? null))) && $_smarty_tpl->getValue('folder')['intelligent']) {?>
				<div class="bm-folder-edit-row bm-folder-edit-row-conditions">
					<label class="bm-folder-edit-label required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"conditions"), $_smarty_tpl);?>
</label>
					<div class="bm-folder-edit-field">
						<iframe id="condition_frame" name="condition_frame" class="conditionIFrame bm-folder-condition-frame" width="100%" height="30" scrolling="no" frameborder="0" border="0" src="email.folders.php?action=editConditions&id=<?php echo $_smarty_tpl->getValue('folder')['id'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"></iframe>
						<div class="bm-folder-link-box linkBox">
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"requiredis"), $_smarty_tpl);?>

							<select class="form-select form-select-sm d-inline-block w-auto" name="intelligent_link">
								<option value="1"<?php if ($_smarty_tpl->getValue('folder')['intelligent_link'] == 1) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ofevery"), $_smarty_tpl);?>
</option>
								<option value="2"<?php if ($_smarty_tpl->getValue('folder')['intelligent_link'] == 2) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ofatleastone"), $_smarty_tpl);?>
</option>
							</select>
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"oftheseconditions"), $_smarty_tpl);?>

						</div>
					</div>
				</div>
				<?php }?>
			</div>
		</div>

		<div class="card-footer bm-folder-edit-footer">
			<button type="submit" class="btn btn-sm btn-primary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
			<button type="reset" class="btn btn-sm btn-outline-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"reset"), $_smarty_tpl);?>
</button>
		</div>
	</form>
</div>
<?php }
}
