<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:08:59
  from 'file:li/navbar-theme.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14822b77adf2_01688647',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '16d71e5fa86706abf0fb39ec499d5098c26557b5' => 
    array (
      0 => 'li/navbar-theme.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a14822b77adf2_01688647 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
if ((($tmp = $_smarty_tpl->getValue('templatePrefs')['enableDarkMode'] ?? null)===null||$tmp==='' ? false ?? null : $tmp)) {?>
<div class="nav-item d-none d-md-flex me-2">
	<a href="#" class="nav-link px-0 hide-theme-dark" onclick="bmSetTheme('dark'); return false;" title="Dunkelmodus" aria-label="Dunkelmodus aktivieren">
		<i class="icon ti ti-moon icon-1"></i>
	</a>
	<a href="#" class="nav-link px-0 hide-theme-light" onclick="bmSetTheme('light'); return false;" title="Hellmodus" aria-label="Hellmodus aktivieren">
		<i class="icon ti ti-sun icon-1"></i>
	</a>
</div>
<?php }
}
}
