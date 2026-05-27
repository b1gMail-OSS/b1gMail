<?php
/* Smarty version 5.8.0, created on 2026-05-24 20:01:45
  from 'file:li/start.customize.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133d0962fc66_31564783',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6012393fb70c11003e40a0b60fa281ed7f468fcb' => 
    array (
      0 => 'li/start.customize.tpl',
      1 => 1779643027,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133d0962fc66_31564783 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-start-dashboard bm-dashboard-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-dashboard-header">
		<div class="left">
			<i class="ti ti-layout-grid-add icon icon-sm" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"customize"), $_smarty_tpl);?>

		</div>
	</div>

	<div class="bm-dashboard-body">
		<div class="card bm-dashboard-card">
			<form name="f1" method="post" action="start.php?action=saveCustomize&amp;sid=<?php echo $_smarty_tpl->getValue('sid');?>
">
				<div class="list-group list-group-flush">
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('possibleWidgets'), 'info', false, 'widget');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('widget')->value => $_smarty_tpl->getVariable('info')->value) {
$foreach0DoElse = false;
?>
					<label class="list-group-item list-group-item-action d-flex align-items-center gap-2" for="widget_<?php echo $_smarty_tpl->getValue('widget');?>
">
						<input class="form-check-input m-0" type="checkbox" id="widget_<?php echo $_smarty_tpl->getValue('widget');?>
" name="widget_<?php echo $_smarty_tpl->getValue('widget');?>
"<?php if (!( !true || empty($_smarty_tpl->getValue('info')['active']))) {?> checked="checked"<?php }?> />
						<span class="d-flex align-items-center gap-2">
							<?php if (!( !true || empty($_smarty_tpl->getValue('info')['icon']))) {?><img src="<?php echo $_smarty_tpl->getValue('info')['icon'];?>
" alt="" width="16" height="16" /><?php }?>
							<?php echo $_smarty_tpl->getValue('info')['title'];?>

						</span>
					</label>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</div>

				<div class="card-footer d-flex justify-content-end">
					<div class="btn-list">
						<button type="submit" class="btn btn-primary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
						<button type="reset" class="btn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"reset"), $_smarty_tpl);?>
</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php }
}
