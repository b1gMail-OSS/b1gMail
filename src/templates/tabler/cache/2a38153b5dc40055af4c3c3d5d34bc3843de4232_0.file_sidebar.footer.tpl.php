<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:08:59
  from 'file:li/sidebar.footer.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14822b783472_23766932',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2a38153b5dc40055af4c3c3d5d34bc3843de4232' => 
    array (
      0 => 'li/sidebar.footer.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a14822b783472_23766932 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-li-sidebar-footer">
	<div class="text-secondary small">&copy; <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('service_title'), ENT_QUOTES, 'UTF-8', true);?>
</div>
</div>
<?php }
}
