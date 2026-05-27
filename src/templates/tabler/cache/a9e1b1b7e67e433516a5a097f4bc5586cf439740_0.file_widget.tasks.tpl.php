<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:50:22
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.tasks.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a5ee399a9_73768702',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a9e1b1b7e67e433516a5a097f4bc5586cf439740' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.tasks.tpl',
      1 => 1779525290,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133a5ee399a9_73768702 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div class="innerWidget" style="max-height: 150px; overflow-y: auto;">
<table width="100%" cellspacing="0" cellpadding="0">
<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('bmwidget_tasks_items'), 'task', false, 'taskID');
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('taskID')->value => $_smarty_tpl->getVariable('task')->value) {
$foreach4DoElse = false;
?>
	<tr>
		<td><input type="checkbox" onclick="setTaskDone('<?php echo $_smarty_tpl->getValue('sid');?>
', <?php echo $_smarty_tpl->getValue('taskID');?>
, this.checked);"<?php if ($_smarty_tpl->getValue('task')['akt_status'] == 64) {?> checked="checked"<?php }?> />
		<a href="organizer.todo.php?action=editTask&id=<?php echo $_smarty_tpl->getValue('taskID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('task')['titel'],'cut'=>30), $_smarty_tpl);?>
</a></td>
		<td align="right">
		<?php if ($_smarty_tpl->getValue('task')['priority'] == 1) {?><i class="fa fa-exclamation" aria-hidden="true"></i><?php }?>
		</td>
	</tr>
<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
</table>
</div><?php }
}
