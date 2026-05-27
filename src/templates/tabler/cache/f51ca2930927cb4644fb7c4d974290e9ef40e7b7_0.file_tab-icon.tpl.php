<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:08:59
  from 'file:li/tab-icon.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14822b785287_17489355',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f51ca2930927cb4644fb7c4d974290e9ef40e7b7' => 
    array (
      0 => 'li/tab-icon.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/icon.tpl' => 2,
  ),
))) {
function content_6a14822b785287_17489355 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
if ((true && (true && null !== ($_smarty_tpl->getValue('tab')['iconDir'] ?? null))) && (true && (true && null !== ($_smarty_tpl->getValue('tab')['icon'] ?? null)))) {?>
<img src="<?php echo $_smarty_tpl->getValue('tab')['iconDir'];
echo $_smarty_tpl->getValue('tab')['icon'];?>
.png" width="16" height="16" alt="" class="bm-tab-icon-img" />
<?php } elseif ((true && (true && null !== ($_smarty_tpl->getValue('tab')['faIcon'] ?? null)))) {
$_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>$_smarty_tpl->getValue('tab')['faIcon']), (int) 0, $_smarty_current_dir);
} else {
$_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>"fa-puzzle-piece"), (int) 0, $_smarty_current_dir);
}
}
}
