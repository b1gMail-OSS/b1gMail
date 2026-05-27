<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:40:55
  from 'file:li/prefs.start.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15bf0777eb61_09610499',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd05dff34e771c1f40018da15120b311987887b9e' => 
    array (
      0 => 'li/prefs.start.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/prefs.item-icon.tpl' => 2,
  ),
))) {
function content_6a15bf0777eb61_09610499 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-prefs-page bm-prefs-overview">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
		<div class="left">
			<i class="ti ti-settings icon icon-sm" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prefs"), $_smarty_tpl);?>

		</div>
	</div>

	<div class="scrollContainer bm-prefs-body">
		<?php if ($_smarty_tpl->getValue('templatePrefs')['prefsLayout'] == 'onecolumn') {?>
		<div class="card bm-prefs-overview-card">
			<div class="list-group list-group-flush bm-prefs-overview-list">
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('prefsItems'), 'null', false, 'item');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value => $_smarty_tpl->getVariable('null')->value) {
$foreach0DoElse = false;
?>
				<a href="prefs.php?action=<?php echo $_smarty_tpl->getValue('item');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="list-group-item list-group-item-action bm-prefs-overview-item">
					<span class="avatar avatar-sm bg-primary-lt text-primary bm-prefs-overview-icon">
						<?php $_smarty_tpl->renderSubTemplate("file:li/prefs.item-icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
					</span>
					<span class="bm-prefs-overview-text">
						<span class="bm-prefs-overview-title"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>((string)$_smarty_tpl->getValue('item'))), $_smarty_tpl);?>
</span>
						<span class="bm-prefs-overview-desc"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prefs_d_".((string)$_smarty_tpl->getValue('item'))), $_smarty_tpl);?>
</span>
					</span>
					<i class="ti ti-chevron-right icon text-secondary bm-prefs-overview-chevron" aria-hidden="true"></i>
				</a>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
			</div>
		</div>
		<?php } else { ?>
		<div class="bm-prefs-overview-grid">
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('prefsItems'), 'null', false, 'item');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value => $_smarty_tpl->getVariable('null')->value) {
$foreach1DoElse = false;
?>
			<a href="prefs.php?action=<?php echo $_smarty_tpl->getValue('item');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="card bm-prefs-overview-tile">
				<div class="card-body bm-prefs-overview-tile-body">
					<span class="avatar avatar-sm bg-primary-lt text-primary bm-prefs-overview-icon">
						<?php $_smarty_tpl->renderSubTemplate("file:li/prefs.item-icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
					</span>
					<span class="bm-prefs-overview-text">
						<span class="bm-prefs-overview-title"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>((string)$_smarty_tpl->getValue('item'))), $_smarty_tpl);?>
</span>
						<span class="bm-prefs-overview-desc"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prefs_d_".((string)$_smarty_tpl->getValue('item'))), $_smarty_tpl);?>
</span>
					</span>
					<i class="ti ti-chevron-right icon text-secondary bm-prefs-overview-chevron" aria-hidden="true"></i>
				</div>
			</a>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</div>
		<?php }?>
	</div>
</div>
<?php }
}
