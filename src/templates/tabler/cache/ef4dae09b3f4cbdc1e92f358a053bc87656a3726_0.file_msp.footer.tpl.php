<?php
/* Smarty version 5.8.0, created on 2026-05-25 12:39:43
  from 'file:nli/msp.footer.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14430faf8f39_72902049',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ef4dae09b3f4cbdc1e92f358a053bc87656a3726' => 
    array (
      0 => 'nli/msp.footer.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/lang.dropdown.tpl' => 1,
  ),
))) {
function content_6a14430faf8f39_72902049 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><footer class="text-secondary mt-3 small nli-msp-footer">
	<div class="d-flex flex-wrap align-items-center justify-content-center gap-1 mb-1 nli-msp-footer-meta">
		<span>&copy; <?php echo $_smarty_tpl->getValue('year');?>
 <?php echo $_smarty_tpl->getValue('service_title');?>
</span>
		<span class="text-secondary" aria-hidden="true">·</span>
		<span>powered by <a href="https://www.b1gmail.eu/" target="_blank" rel="noreferrer" class="text-secondary">b1gMail.eu</a></span>
	</div>
	<div class="d-flex flex-wrap align-items-center justify-content-center gap-1 nli-msp-footer-actions">
		<?php $_smarty_tpl->assign('langDropdownClass', "link-secondary py-0", false, NULL);?>
		<?php $_smarty_tpl->assign('langDropdownDropup', true, false, NULL);?>
		<?php $_smarty_tpl->renderSubTemplate("file:nli/lang.dropdown.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
		<span class="text-secondary" aria-hidden="true">·</span>
		<a href="<?php echo $_smarty_tpl->getValue('mobileURL');?>
" class="text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mobilepda"), $_smarty_tpl);?>
</a>
		<span class="text-secondary" aria-hidden="true">·</span>
		<a href="index.php?action=imprint" class="text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"contact"), $_smarty_tpl);?>
</a>
		<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('pluginUserPages'), 'item');
$foreach16DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach16DoElse = false;
if (!(($tmp = $_smarty_tpl->getValue('item')['top'] ?? null)===null||$tmp==='' ? false ?? null : $tmp)) {?>
		<span class="text-secondary" aria-hidden="true">·</span>
		<a href="<?php echo $_smarty_tpl->getValue('item')['link'];?>
" class="text-secondary"><?php echo $_smarty_tpl->getValue('item')['text'];?>
</a>
		<?php }
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	</div>
</footer>
<?php }
}
