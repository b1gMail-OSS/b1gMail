<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:39:10
  from 'file:li/prefs.sidebar.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15be9ecac606_50979802',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cca54d6e1b8a9c98b7195084b421d300d8b60db8' => 
    array (
      0 => 'li/prefs.sidebar.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/icon.tpl' => 1,
    'file:li/prefs.item-icon.tpl' => 1,
  ),
))) {
function content_6a15be9ecac606_50979802 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"prefs.sidebar.tpl:head"), $_smarty_tpl);?>


<div class="sidebarHeading"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prefs"), $_smarty_tpl);?>
</div>
<div class="contentMenuIcons bm-prefs-sidebar-nav">
	<a href="prefs.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"<?php if ($_smarty_tpl->getValue('pageContent') == 'li/prefs.start.tpl') {?> class="active"<?php }?>>
		<?php $_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>"fa-tachometer"), (int) 0, $_smarty_current_dir);
?> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"overview"), $_smarty_tpl);?>

	</a>
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('prefsItems'), 'null', false, 'item');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value => $_smarty_tpl->getVariable('null')->value) {
$foreach0DoElse = false;
?>
	<?php $_smarty_tpl->assign('_prefsPrefix', "li/prefs.".((string)$_smarty_tpl->getValue('item')).".", false, NULL);?>
	<a href="prefs.php?action=<?php echo $_smarty_tpl->getValue('item');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"<?php if ($_smarty_tpl->getValue('pageContent') == "li/prefs.".((string)$_smarty_tpl->getValue('item')).".tpl" || substr((string) $_smarty_tpl->getValue('pageContent'), (int) 0, (int) (preg_match_all('/[^\s]/u',$_smarty_tpl->getValue('_prefsPrefix'), $tmp))) == $_smarty_tpl->getValue('_prefsPrefix')) {?> class="active"<?php }?>>
		<span class="bm-prefs-sidebar-icon"><?php $_smarty_tpl->renderSubTemplate("file:li/prefs.item-icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?></span>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>((string)$_smarty_tpl->getValue('item'))), $_smarty_tpl);?>

	</a>
	<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
</div>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"prefs.sidebar.tpl:foot"), $_smarty_tpl);?>

<?php }
}
