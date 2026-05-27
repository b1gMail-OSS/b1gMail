<?php
/* Smarty version 5.8.0, created on 2026-05-24 21:18:00
  from 'file:li/dialog.openfile.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a134ee8b86e95_07181116',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f71015f0d25bb2a682c7517f158591f3b2754c75' => 
    array (
      0 => 'li/dialog.openfile.tpl',
      1 => 1779633343,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/dialog.head.tpl' => 1,
    'file:li/file.selector.tpl' => 1,
    'file:li/dialog.foot.tpl' => 1,
  ),
))) {
function content_6a134ee8b86e95_07181116 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
$_smarty_tpl->renderSubTemplate("file:li/dialog.head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_smarty_tpl->getValue('title'),'dialogBodyClass'=>"bm-dialog-openfile"), (int) 0, $_smarty_current_dir);
?>

<div class="bm-dialog-page">
	<p class="text-secondary bm-dialog-intro mb-3"><?php echo $_smarty_tpl->getValue('text');?>
</p>

	<form action="<?php echo $_smarty_tpl->getValue('formAction');?>
" enctype="multipart/form-data" method="post" class="bm-dialog-form">
		<?php $_smarty_tpl->renderSubTemplate("file:li/file.selector.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('name'=>$_smarty_tpl->getValue('fieldName'),'multiple'=>$_smarty_tpl->getValue('multiple'),'sid'=>$_smarty_tpl->getValue('sid'),'hasWebdisk'=>$_smarty_tpl->getValue('hasWebdisk')), (int) 0, $_smarty_current_dir);
?>

		<?php if ($_smarty_tpl->getValue('bar')) {?>
		<div class="mt-3">
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('progressBar')->handle(array('value'=>$_smarty_tpl->getValue('bar')['value'],'max'=>$_smarty_tpl->getValue('bar')['max'],'width'=>100), $_smarty_tpl);?>

		</div>
		<?php }?>

		<div class="bm-dialog-actions">
			<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cancel"), $_smarty_tpl);?>
</button>
			<button type="submit" class="btn btn-primary">
				<i class="ti ti-check icon" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>

			</button>
		</div>
	</form>
</div>

<?php if ((true && ($_smarty_tpl->hasVariable('fileSource') && null !== ($_smarty_tpl->getValue('fileSource') ?? null))) && $_smarty_tpl->getValue('fileSource') == 'webdisk') {
echo '<script'; ?>
>
<!--
	registerLoadAction(function()
	{
		var sel = document.querySelector('.bm-file-selector[data-name="<?php echo $_smarty_tpl->getValue('fieldName');?>
"] .bm-file-selector-source');
		if(sel)
		{
			sel.value = 'webdisk';
			changeFileSelectorSource(sel, '<?php echo $_smarty_tpl->getValue('fieldName');?>
');
		}
	});
//-->
<?php echo '</script'; ?>
>
<?php }?>

<?php $_smarty_tpl->renderSubTemplate("file:li/dialog.foot.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
