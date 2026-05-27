<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:08:59
  from 'file:li/navbar-tools.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14822b779a84_70969568',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '965143fd9d2b813ac3fe7b9b57c0d71ba054c995' => 
    array (
      0 => 'li/navbar-tools.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/navbar-theme.tpl' => 1,
  ),
))) {
function content_6a14822b779a84_70969568 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
$_smarty_tpl->renderSubTemplate("file:li/navbar-theme.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
if ($_smarty_tpl->getValue('bmNotifyInterval') > 0) {?>
<div class="nav-item d-none d-md-flex me-2">
	<a href="#" class="nav-link px-0 position-relative" onclick="showNotifications(this); return false;" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notifications"), $_smarty_tpl);?>
">
		<i id="notifyIcon" class="icon ti ti-bell icon-1"></i>
		<span class="badge bg-red text-red-fg position-absolute top-0 start-100 translate-middle" id="notifyCount"<?php if ($_smarty_tpl->getValue('bmUnreadNotifications') == 0) {?> style="display:none;"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('number')->handle(array('value'=>$_smarty_tpl->getValue('bmUnreadNotifications'),'min'=>0,'max'=>99), $_smarty_tpl);?>
</span>
	</a>
</div>
<?php }?>
<div class="nav-item d-none d-md-flex me-2">
	<a href="#" class="nav-link px-0" onclick="showNewMenu(this); return false;" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"new"), $_smarty_tpl);?>
">
		<i class="icon ti ti-square-plus icon-1"></i>
		<span class="d-none d-lg-inline ms-1"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"new"), $_smarty_tpl);?>
</span>
	</a>
</div>
<div class="nav-item d-none d-md-flex me-2">
	<a href="#" class="nav-link px-0" onclick="showSearchPopup(this); return false;" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"search"), $_smarty_tpl);?>
">
		<i class="icon ti ti-search icon-1"></i>
	</a>
</div>
<?php }
}
