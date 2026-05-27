<?php
/* Smarty version 5.8.0, created on 2026-05-25 12:39:43
  from 'file:nli/msp.page.open.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14430faeabf6_27473706',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '72129fac6b28a29a45b2d248930c8ac83cc7c825' => 
    array (
      0 => 'nli/msp.page.open.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/login.brand.tpl' => 1,
    'file:nli/msp.tabs.tpl' => 1,
  ),
))) {
function content_6a14430faeabf6_27473706 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><div class="page-body nli-msp-layout">
	<div class="container-xl py-4">
		<div class="text-center mb-4">
			<?php $_smarty_tpl->renderSubTemplate("file:nli/login.brand.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
		</div>
		<div class="card">
			<?php $_smarty_tpl->renderSubTemplate("file:nli/msp.tabs.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
			<div class="card-body">
<?php }
}
