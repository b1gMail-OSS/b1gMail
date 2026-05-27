<?php
/* Smarty version 5.8.0, created on 2026-05-26 15:56:11
  from 'file:nli/login.cover-footer.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15c29bcf6345_60521819',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9ab2899e5a17a65483d2e751e7360310a7604b7e' => 
    array (
      0 => 'nli/login.cover-footer.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/lang.dropdown.tpl' => 1,
  ),
))) {
function content_6a15c29bcf6345_60521819 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><div class="d-flex align-items-center justify-content-between gap-3 mt-4 pt-3 nli-cover-footer">
	<?php $_smarty_tpl->assign('langDropdownClass', "link-secondary", false, NULL);?>
	<?php $_smarty_tpl->renderSubTemplate("file:nli/lang.dropdown.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
	<?php if ($_smarty_tpl->getValue('_regEnabled') || (!$_smarty_tpl->getValue('templatePrefs')['hideSignup'])) {?>
	<a href="<?php if ($_smarty_tpl->getValue('ssl_signup_enable')) {
echo $_smarty_tpl->getValue('ssl_url');
}?>index.php?action=signup" class="btn btn-primary">
		<i class="ti ti-user-plus me-1" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signup"), $_smarty_tpl);?>

	</a>
	<?php }?>
</div>
<?php }
}
