<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:08:59
  from 'file:li/navbar-user.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14822b77c961_40442797',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3108c612f2a049ebcd22cb00d26e9fff45c35e1a' => 
    array (
      0 => 'li/navbar-user.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a14822b77c961_40442797 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="nav-item me-3">
	<a href="#" class="nav-link d-flex lh-1 p-0 px-2" onclick="showUserMenu(this); return false;" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prefs"), $_smarty_tpl);?>
">
		<span class="avatar avatar-sm"><?php echo htmlspecialchars((string)$_smarty_tpl->getValue('_userInitials'), ENT_QUOTES, 'UTF-8', true);?>
</span>
		<div class="d-none d-xl-block ps-2">
			<div>
				<?php if ((($tmp = $_smarty_tpl->getValue('_userDisplayName') ?? null)===null||$tmp==='' ? '' ?? null : $tmp) != '') {?>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('_userDisplayName'),'allowEmpty'=>true), $_smarty_tpl);?>

				<?php } else { ?>
				<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('_userEmail'), ENT_QUOTES, 'UTF-8', true);?>

				<?php }?>
			</div>
			<div class="mt-1 small text-secondary"><?php echo htmlspecialchars((string)$_smarty_tpl->getValue('_userEmail'), ENT_QUOTES, 'UTF-8', true);?>
</div>
		</div>
	</a>
</div>
<?php }
}
