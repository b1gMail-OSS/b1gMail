<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:09:04
  from 'file:li/email.folderlist.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1482302f3666_12742687',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6a97f1e34be635b2c41fd473816959cd9d3a5f2c' => 
    array (
      0 => 'li/email.folderlist.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a1482302f3666_12742687 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?>var d = new dTree('d');
<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('folderList'), 'folder');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('folder')->value) {
$foreach0DoElse = false;
?>
d.add(<?php echo $_smarty_tpl->getValue('folder')['i'];?>
, <?php echo $_smarty_tpl->getValue('folder')['parent'];?>
, '<span class="bm-folder-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['text'],'escape'=>true,'noentities'=>true), $_smarty_tpl);?>
</span><?php if ($_smarty_tpl->getValue('folder')['unread'] > 0) {?><span class="bm-folder-count"><?php echo $_smarty_tpl->getValue('folder')['unread'];?>
</span><?php }?>', 'javascript:switchFolder(<?php echo $_smarty_tpl->getValue('folder')['id'];?>
);', '<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('folder')['text'],'escape'=>true,'noentities'=>true), $_smarty_tpl);?>
', '', 'ti <?php if ($_smarty_tpl->getValue('folder')['icon'] == 'inbox') {?>ti-inbox<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'outbox') {?>ti-send<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'drafts') {?>ti-file-pencil<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'spam') {?>ti-ban<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'trash') {?>ti-trash<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'intellifolder') {?>ti-folder<?php } else { ?>ti-folder<?php }?>', 'ti <?php if ($_smarty_tpl->getValue('folder')['icon'] == 'inbox') {?>ti-inbox<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'outbox') {?>ti-send<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'drafts') {?>ti-file-pencil<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'spam') {?>ti-ban<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'trash') {?>ti-trash<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'intellifolder') {?>ti-folder<?php } else { ?>ti-folder<?php }?>');
<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);
}
}
