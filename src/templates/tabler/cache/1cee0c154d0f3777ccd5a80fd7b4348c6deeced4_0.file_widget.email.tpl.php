<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:50:22
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.email.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a5ee29f46_82868344',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1cee0c154d0f3777ccd5a80fd7b4348c6deeced4' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.email.tpl',
      1 => 1779525290,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133a5ee29f46_82868344 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div class="innerWidget">
<table cellspacing="0" width="100%" style="table-layout:fixed;">
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('bmwidget_email_items'), 'folder', false, 'folderID');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('folderID')->value => $_smarty_tpl->getVariable('folder')->value) {
$foreach2DoElse = false;
?>
<tr>
	<td width="20" align="center">
	<i class="fa <?php if ($_smarty_tpl->getValue('folder')['icon'] == 'inbox') {?>fa-inbox<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'outbox') {?>fa-inbox<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'drafts') {?>fa-envelope<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'spam') {?>fa-ban<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'trash') {?>fa-trash-o<?php } elseif ($_smarty_tpl->getValue('folder')['icon'] == 'intellifolder') {?>fa-folder<?php } else { ?>fa-folder-o<?php }?>" aria-hidden="true"></i>
	</td>
	<td style="text-overflow:ellipsis;overflow:hidden;"><a href="email.php?folder=<?php echo $_smarty_tpl->getValue('folderID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php echo $_smarty_tpl->getValue('folder')['text'];?>
</a></td>
	<td align="left" width="50"><i class="fa fa-envelope-o"></i> <?php echo $_smarty_tpl->getValue('folder')['allMails'];?>
</td>
	<td align="left" width="45"><i class="fa fa-flag-o"></i> <?php if ($_smarty_tpl->getValue('folder')['flaggedMails'] > 0) {?><b><?php echo $_smarty_tpl->getValue('folder')['flaggedMails'];?>
</b><?php } else { ?>-<?php }?></td>
	<td align="left" width="45"><i class="fa fa-envelope"></i> <?php if ($_smarty_tpl->getValue('folder')['unreadMails'] > 0) {?><b><?php echo $_smarty_tpl->getValue('folder')['unreadMails'];?>
</b><?php } else { ?>-<?php }?></td>
</tr>
	<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
</table>
</div><?php }
}
