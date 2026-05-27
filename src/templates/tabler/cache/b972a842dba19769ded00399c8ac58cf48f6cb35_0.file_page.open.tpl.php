<?php
/* Smarty version 5.8.0, created on 2026-05-25 12:39:43
  from 'file:nli/page.open.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14430fae98f5_60387695',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b972a842dba19769ded00399c8ac58cf48f6cb35' => 
    array (
      0 => 'nli/page.open.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/layout.vars.tpl' => 1,
    'file:nli/msp.page.open.tpl' => 1,
  ),
))) {
function content_6a14430fae98f5_60387695 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
if (!(true && ($_smarty_tpl->hasVariable('nliCompactLayout') && null !== ($_smarty_tpl->getValue('nliCompactLayout') ?? null)))) {
$_smarty_tpl->renderSubTemplate("file:nli/layout.vars.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 2, $_smarty_current_dir);
}
if ($_smarty_tpl->getValue('nliCompactLayout')) {
$_smarty_tpl->renderSubTemplate("file:nli/msp.page.open.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
} else { ?>
<div class="page-body">
	<div class="container-xl py-4">
		<div class="card">
			<div class="card-body">
<?php }
}
}
