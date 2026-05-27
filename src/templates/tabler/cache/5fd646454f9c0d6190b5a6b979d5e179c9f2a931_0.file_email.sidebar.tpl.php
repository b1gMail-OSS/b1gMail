<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:09:04
  from 'file:li/email.sidebar.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1482302ee1b4_92092125',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5fd646454f9c0d6190b5a6b979d5e179c9f2a931' => 
    array (
      0 => 'li/email.sidebar.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/icon.tpl' => 2,
    'file:li/email.folderlist.tpl' => 1,
  ),
))) {
function content_6a1482302ee1b4_92092125 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.sidebar.tpl:head"), $_smarty_tpl);?>


<div class="sidebarHeading"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</div>
<div class="contentMenuIcons">
	<a href="email.compose.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php $_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>"fa-envelope-o"), (int) 0, $_smarty_current_dir);
?> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sendmail"), $_smarty_tpl);?>
</a><br />
	<a href="email.folders.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php $_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>"fa-folder-open-o"), (int) 0, $_smarty_current_dir);
?> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"folderadmin"), $_smarty_tpl);?>
</a><br />
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.sidebar.tpl:email"), $_smarty_tpl);?>

</div>

<div class="sidebarHeading"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"folders"), $_smarty_tpl);?>
</div>
<div class="bm-folder-tree" id="folderList">
</div>
<?php echo '<script'; ?>
>
<!--
	<?php $_smarty_tpl->renderSubTemplate("file:li/email.folderlist.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
	d.config.useLines = false;
	d.icon.nlPlus = 'ti ti-chevron-right';
	d.icon.nlMinus = 'ti ti-chevron-down';
	d.icon.plus = 'ti ti-chevron-right';
	d.icon.minus = 'ti ti-chevron-down';
	d.icon.plusBottom = 'ti ti-chevron-right';
	d.icon.minusBottom = 'ti ti-chevron-down';
	EBID('folderList').innerHTML = d;
	enableFolderDragTargets();
//-->
<?php echo '</script'; ?>
>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.sidebar.tpl:foot"), $_smarty_tpl);?>

<?php }
}
