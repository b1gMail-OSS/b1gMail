<?php
/* Smarty version 5.8.0, created on 2026-05-25 17:08:47
  from 'file:nli/regdone.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14821f10fc15_99139148',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9a265526161e853317085f81b5e7e2c3876a062f' => 
    array (
      0 => 'nli/regdone.tpl',
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
function content_6a14821f10fc15_99139148 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
$_smarty_tpl->renderSubTemplate("file:nli/page.open.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
<h1 class="mb-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signup"), $_smarty_tpl);?>
</h1>
<div class="text-secondary">
	<?php echo $_smarty_tpl->getValue('msg');?>

</div>
<?php $_smarty_tpl->renderSubTemplate("file:nli/page.close.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
