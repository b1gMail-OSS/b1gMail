<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:21:09
  from 'file:li/webdisk.folderlist.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a159035a64f30_94002492',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5e5d18899a5e9d0032abca158b5900cf9e551d7b' => 
    array (
      0 => 'li/webdisk.folderlist.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a159035a64f30_94002492 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?>var webdisk_d = new dTree('webdisk_d');
<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('folderList'), 'folder');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('folder')->value) {
$foreach0DoElse = false;
if ($_smarty_tpl->getValue('folder')['parent'] == -1) {
$_smarty_tpl->assign('wdIcon', "ti-cloud", false, NULL);
$_smarty_tpl->assign('wdIconOpen', "ti-cloud", false, NULL);
} elseif ($_smarty_tpl->getValue('folder')['icon'] == 'folder_shared') {
$_smarty_tpl->assign('wdIcon', "ti-folder-share", false, NULL);
$_smarty_tpl->assign('wdIconOpen', "ti-folder-share", false, NULL);
} else {
$_smarty_tpl->assign('wdIcon', "ti-folder", false, NULL);
$_smarty_tpl->assign('wdIconOpen', "ti-folder-open", false, NULL);
}?>
webdisk_d.add(<?php echo $_smarty_tpl->getValue('folder')['i'];?>
, <?php echo $_smarty_tpl->getValue('folder')['parent'];?>
, '<span class="bm-folder-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['text'],'escape'=>true,'noentities'=>true), $_smarty_tpl);?>
</span>', 'javascript:switchWebdiskFolder(<?php echo $_smarty_tpl->getValue('folder')['id'];?>
);', '<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['text'],'escape'=>true,'noentities'=>true), $_smarty_tpl);?>
', '', 'ti <?php echo $_smarty_tpl->getValue('wdIcon');?>
', 'ti <?php echo $_smarty_tpl->getValue('wdIconOpen');?>
');
<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);
}
}
