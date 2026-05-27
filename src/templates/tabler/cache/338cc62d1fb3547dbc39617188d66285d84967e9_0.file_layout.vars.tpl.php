<?php
/* Smarty version 5.8.0, created on 2026-05-25 12:39:43
  from 'file:nli/layout.vars.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14430fac1954_56396746',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '338cc62d1fb3547dbc39617188d66285d84967e9' => 
    array (
      0 => 'nli/layout.vars.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a14430fac1954_56396746 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
$_smarty_tpl->assign('nliStyle', (($tmp = $_smarty_tpl->getValue('templatePrefs')['loginStyle'] ?? null)===null||$tmp==='' ? 'cover' ?? null : $tmp), false, NULL);
$_smarty_tpl->assign('nliCompactLayout', true, false, NULL);
}
}
