<?php
/* Smarty version 5.8.0, created on 2026-05-25 13:16:23
  from 'file:nli/login.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a144ba77bb922_93383045',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '09254216f5fa6154dca9ebb2877e4f04a598b381' => 
    array (
      0 => 'nli/login.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/login.msp.tpl' => 1,
    'file:nli/login.center.tpl' => 1,
    'file:nli/login.cover.tpl' => 1,
  ),
))) {
function content_6a144ba77bb922_93383045 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
if ($_smarty_tpl->getValue('templatePrefs')['loginStyle'] == 'msp') {?>
	<?php $_smarty_tpl->renderSubTemplate("file:nli/login.msp.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
} elseif ($_smarty_tpl->getValue('templatePrefs')['loginStyle'] == 'center' || $_smarty_tpl->getValue('templatePrefs')['loginStyle'] == 'minimal') {?>
	<?php $_smarty_tpl->renderSubTemplate("file:nli/login.center.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
} else { ?>
	<?php $_smarty_tpl->renderSubTemplate("file:nli/login.cover.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
}
