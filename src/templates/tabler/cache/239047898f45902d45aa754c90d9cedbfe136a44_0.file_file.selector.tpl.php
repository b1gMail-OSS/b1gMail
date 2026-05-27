<?php
/* Smarty version 5.8.0, created on 2026-05-24 21:18:00
  from 'file:li/file.selector.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a134ee8b8e182_58041361',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '239047898f45902d45aa754c90d9cedbfe136a44' => 
    array (
      0 => 'li/file.selector.tpl',
      1 => 1779633343,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a134ee8b8e182_58041361 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-file-selector" data-name="<?php echo $_smarty_tpl->getValue('name');?>
">
	<div class="input-group bm-file-selector-source-group">
		<span class="input-group-text text-secondary">
			<i class="ti ti-source-code icon" aria-hidden="true"></i>
		</span>
		<select class="form-select bm-file-selector-source" onchange="changeFileSelectorSource(this, '<?php echo $_smarty_tpl->getValue('name');?>
')">
			<option value="local"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"localfile"), $_smarty_tpl);?>
</option>
			<?php if ($_smarty_tpl->getValue('hasWebdisk')) {?>
			<option value="webdisk"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"webdiskfile"), $_smarty_tpl);?>
</option>
			<?php }?>
		</select>
	</div>

	<div id="fileSelector_local_<?php echo $_smarty_tpl->getValue('name');?>
" class="bm-file-selector-panel mt-2">
		<div class="input-group">
			<span class="input-group-text text-secondary">
				<i class="ti ti-upload icon" aria-hidden="true"></i>
			</span>
			<input type="file" class="form-control" id="localFile_<?php echo $_smarty_tpl->getValue('name');?>
" name="localFile_<?php echo $_smarty_tpl->getValue('name');
if ((true && ($_smarty_tpl->hasVariable('multiple') && null !== ($_smarty_tpl->getValue('multiple') ?? null))) && $_smarty_tpl->getValue('multiple')) {?>[]<?php }?>"<?php if ((true && ($_smarty_tpl->hasVariable('multiple') && null !== ($_smarty_tpl->getValue('multiple') ?? null))) && $_smarty_tpl->getValue('multiple')) {?> multiple="multiple"<?php }?> />
		</div>
	</div>

	<div id="fileSelector_webdisk_<?php echo $_smarty_tpl->getValue('name');?>
" class="bm-file-selector-panel mt-2" style="display:none;">
		<input type="hidden" name="webdiskFile_<?php echo $_smarty_tpl->getValue('name');?>
_id" id="webdiskFile_<?php echo $_smarty_tpl->getValue('name');?>
_id" value="" />
		<div class="input-group">
			<span class="input-group-text text-secondary">
				<i class="ti ti-cloud icon" aria-hidden="true"></i>
			</span>
			<input type="text" class="form-control" id="webdiskFile_<?php echo $_smarty_tpl->getValue('name');?>
" name="webdiskFile_<?php echo $_smarty_tpl->getValue('name');?>
" readonly="readonly" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"webdiskfile"), $_smarty_tpl);?>
" />
			<button type="button" class="btn btn-outline-secondary" onclick="webdiskDialog('<?php echo $_smarty_tpl->getValue('sid');?>
', 'open', 'webdiskFile_<?php echo $_smarty_tpl->getValue('name');?>
')">
				<i class="ti ti-folder icon" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"browse"), $_smarty_tpl);?>

			</button>
		</div>
	</div>
</div>
<?php }
}
