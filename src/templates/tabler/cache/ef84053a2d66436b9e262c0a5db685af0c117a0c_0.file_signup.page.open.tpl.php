<?php
/* Smarty version 5.8.0, created on 2026-05-25 15:47:51
  from 'file:nli/signup.page.open.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a146f27b0b8f3_13018565',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ef84053a2d66436b9e262c0a5db685af0c117a0c' => 
    array (
      0 => 'nli/signup.page.open.tpl',
      1 => 1779724052,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/layout.vars.tpl' => 1,
    'file:nli/login.brand.tpl' => 1,
    'file:nli/msp.tabs.tpl' => 1,
    'file:nli/page.open.tpl' => 1,
  ),
))) {
function content_6a146f27b0b8f3_13018565 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
if (!(true && ($_smarty_tpl->hasVariable('nliCompactLayout') && null !== ($_smarty_tpl->getValue('nliCompactLayout') ?? null)))) {
$_smarty_tpl->renderSubTemplate("file:nli/layout.vars.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 2, $_smarty_current_dir);
}
if ($_smarty_tpl->getValue('nliCompactLayout')) {?>
<div class="page-body nli-msp-layout">
	<div class="container-xl py-4">
		<div class="text-center mb-4">
			<?php $_smarty_tpl->renderSubTemplate("file:nli/login.brand.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
		</div>
		<div class="card bm-signup-card">
			<?php $_smarty_tpl->renderSubTemplate("file:nli/msp.tabs.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
			<form action="<?php if ($_smarty_tpl->getValue('ssl_signup_enable')) {
echo $_smarty_tpl->getValue('ssl_url');
}?>index.php?action=signup" method="post" id="signupForm" class="bm-signup-form">
			<div class="card-body">
<?php } else {
$_smarty_tpl->renderSubTemplate("file:nli/page.open.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
}
