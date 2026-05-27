<?php
/* Smarty version 5.8.0, created on 2026-05-25 12:39:43
  from 'file:nli/login.brand.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14430faeb995_15421179',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e60c8e33312d84a5a8564d03aa2eaa6473a629f7' => 
    array (
      0 => 'nli/login.brand.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a14430faeb995_15421179 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><div class="text-center mb-4 nli-brand">
	<a href="index.php" class="navbar-brand d-inline-flex flex-column align-items-center text-decoration-none">
		<img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/logo.png" height="36" alt="<?php echo $_smarty_tpl->getValue('service_title');?>
" class="navbar-brand-image nli-brand-image mb-2" />
		<span class="navbar-brand-text text-body fw-semibold"><?php echo $_smarty_tpl->getValue('service_title');?>
</span>
	</a>
</div>
<?php }
}
