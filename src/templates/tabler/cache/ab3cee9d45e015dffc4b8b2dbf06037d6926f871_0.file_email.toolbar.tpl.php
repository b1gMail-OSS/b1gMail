<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:19:32
  from 'file:li/email.toolbar.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a158fd4e52434_97763223',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ab3cee9d45e015dffc4b8b2dbf06037d6926f871' => 
    array (
      0 => 'li/email.toolbar.tpl',
      1 => 1779797815,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a158fd4e52434_97763223 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="col-12 bm-li-email-toolbar py-0">
	<div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 w-100">
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.toolbar.tpl:firstColumn"), $_smarty_tpl);?>


		<div class="bm-li-toolbar-item d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
			<span class="bm-li-toolbar-label" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"space"), $_smarty_tpl);?>
">
				<i class="icon ti ti-database icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"space"), $_smarty_tpl);?>
</span>
			</span>
			<div class="bm-li-toolbar-progress">
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('progressBar')->handle(array('value'=>$_smarty_tpl->getValue('spaceUsed'),'max'=>$_smarty_tpl->getValue('spaceLimit'),'width'=>120), $_smarty_tpl);?>

			</div>
			<span class="bm-li-toolbar-meta"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('spaceUsed')), $_smarty_tpl);?>
 / <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('spaceLimit')), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"used"), $_smarty_tpl);?>
</span>
		</div>

		<?php if (!( !$_smarty_tpl->hasVariable('enablePreview') || empty($_smarty_tpl->getValue('enablePreview')))) {?>
		<div class="bm-li-toolbar-divider d-none d-md-block" aria-hidden="true"></div>
		<div class="bm-li-toolbar-item d-flex align-items-center gap-2">
			<span class="bm-li-toolbar-label" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"preview"), $_smarty_tpl);?>
">
				<i class="icon ti ti-layout-columns icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"preview"), $_smarty_tpl);?>
</span>
			</span>
			<select class="form-select form-select-sm bm-li-preview-select" onchange="updatePreviewPosition(this)">
				<option value="bottom"<?php if (!$_smarty_tpl->getValue('narrow')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"bottom"), $_smarty_tpl);?>
</option>
				<option value="right"<?php if ($_smarty_tpl->getValue('narrow')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"right"), $_smarty_tpl);?>
</option>
			</select>
		</div>
		<?php }?>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.toolbar.tpl:lastColumn"), $_smarty_tpl);?>

	</div>
</div>
<?php }
}
