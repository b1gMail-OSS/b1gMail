<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:50:22
  from 'file:li/start.sidebar.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a5ee15581_39317108',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8dbd63ed92802954e83e1e6810d217a8a2e791a2' => 
    array (
      0 => 'li/start.sidebar.tpl',
      1 => 1779644371,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/icon.tpl' => 2,
  ),
))) {
function content_6a133a5ee15581_39317108 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"start.sidebar.tpl:head"), $_smarty_tpl);?>


<div class="sidebarHeading"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"start"), $_smarty_tpl);?>
</div>
<div class="contentMenuIcons">
	<a href="start.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"<?php if ($_smarty_tpl->getValue('activeTab') == 'start') {?> class="active"<?php }?>><?php $_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>"fa-home"), (int) 0, $_smarty_current_dir);
?> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"start"), $_smarty_tpl);?>
</a>
	<a href="start.php?action=customize&amp;sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php $_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>"fa-puzzle-piece"), (int) 0, $_smarty_current_dir);
?> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"customize"), $_smarty_tpl);?>
</a>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"start.sidebar.tpl:start"), $_smarty_tpl);?>

</div>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"start.sidebar.tpl:foot"), $_smarty_tpl);?>

<?php }
}
