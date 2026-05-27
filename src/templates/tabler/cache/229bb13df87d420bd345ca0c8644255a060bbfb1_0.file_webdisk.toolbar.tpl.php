<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:21:09
  from 'file:li/webdisk.toolbar.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a159035a696f0_15552611',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '229bb13df87d420bd345ca0c8644255a060bbfb1' => 
    array (
      0 => 'li/webdisk.toolbar.tpl',
      1 => 1779797831,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a159035a696f0_15552611 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="col-12 bm-li-email-toolbar py-0">
	<div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 w-100">
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"webdisk.toolbar.tpl:firstColumn"), $_smarty_tpl);?>


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
</span>
		</div>

		<?php if ($_smarty_tpl->getValue('trafficLimit') > 0) {?>
		<div class="bm-li-toolbar-divider d-none d-md-block" aria-hidden="true"></div>
		<div class="bm-li-toolbar-item d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
			<span class="bm-li-toolbar-label" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"traffic"), $_smarty_tpl);?>
">
				<i class="icon ti ti-arrows-exchange icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"traffic"), $_smarty_tpl);?>
</span>
			</span>
			<div class="bm-li-toolbar-progress">
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('progressBar')->handle(array('value'=>$_smarty_tpl->getValue('trafficUsed'),'max'=>$_smarty_tpl->getValue('trafficLimit'),'width'=>120), $_smarty_tpl);?>

			</div>
			<span class="bm-li-toolbar-meta"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('trafficUsed')), $_smarty_tpl);?>
 / <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('trafficLimit')), $_smarty_tpl);?>
</span>
		</div>
		<?php }?>

		<div class="bm-li-toolbar-divider d-none d-md-block" aria-hidden="true"></div>
		<div class="bm-li-toolbar-item d-flex align-items-center gap-2 ms-md-auto">
			<span class="bm-li-toolbar-label" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"viewmode"), $_smarty_tpl);?>
">
				<i class="icon ti ti-layout-grid icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"viewmode"), $_smarty_tpl);?>
</span>
			</span>
			<select class="form-select form-select-sm bm-li-toolbar-select" onchange="updateWebdiskViewMode(this, '<?php echo $_smarty_tpl->getValue('folderID');?>
', '<?php echo $_smarty_tpl->getValue('sid');?>
')">
				<option value="icons"<?php if ($_smarty_tpl->getValue('viewMode') == "icons") {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"icons"), $_smarty_tpl);?>
</option>
				<option value="list"<?php if ($_smarty_tpl->getValue('viewMode') == "list") {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"list"), $_smarty_tpl);?>
</option>
			</select>
		</div>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"webdisk.toolbar.tpl:lastColumn"), $_smarty_tpl);?>

	</div>
</div>
<?php }
}
