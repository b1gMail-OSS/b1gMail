<?php
/* Smarty version 5.8.0, created on 2026-05-25 15:46:39
  from 'file:nli/tos.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a146edf415492_34757034',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ec9b37cbbab75f940ead83fee4909a6d5f47696d' => 
    array (
      0 => 'nli/tos.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/page.open.tpl' => 1,
    'file:nli/page.close.tpl' => 1,
  ),
))) {
function content_6a146edf415492_34757034 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
$_smarty_tpl->renderSubTemplate("file:nli/page.open.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
<h1 class="mb-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tos"), $_smarty_tpl);?>
</h1>
<div class="text-secondary">
	<?php echo $_smarty_tpl->getValue('tos_html');?>

</div>
<?php $_smarty_tpl->renderSubTemplate("file:nli/page.close.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
