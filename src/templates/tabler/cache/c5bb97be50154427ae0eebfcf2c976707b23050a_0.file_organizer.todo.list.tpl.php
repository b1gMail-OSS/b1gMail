<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:20:56
  from 'file:li/organizer.todo.list.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a159028a5b5d1_27982961',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c5bb97be50154427ae0eebfcf2c976707b23050a' => 
    array (
      0 => 'li/organizer.todo.list.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a159028a5b5d1_27982961 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="taskContainer withBottomBar taskList bm-organizer-task-table-wrap">
	<table class="table table-vcenter table-hover card-table bm-organizer-table" id="tasksTable">
	<thead>
	<tr>
		<th class="taskCheckBox bm-organizer-task-gutter">&nbsp;</th>
		<th class="bm-organizer-task-priority">&nbsp;</th>
		<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"title"), $_smarty_tpl);?>
</th>
		<th style="width:8.75rem;"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"due"), $_smarty_tpl);?>
</th>
		<th style="width:6.25rem;"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"done"), $_smarty_tpl);?>
</th>
		<th class="bm-organizer-task-col-actions" style="width:5.5rem;">&nbsp;</th>
	</tr>
	</thead>

	<tbody>
	<tr class="bm-organizer-section-row">
		<td class="taskCheckBox bm-organizer-task-gutter">
			<button type="button" class="bm-organizer-section-toggle bm-organizer-section-toggle-icon" onclick="toggleGroup(0,'todo0');" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"undonetasks"), $_smarty_tpl);?>
">
				<i class="ti ti-chevron-<?php if ((true && (true && null !== ($_COOKIE['toggleGroup']['todo0'] ?? null))) && $_COOKIE['toggleGroup']['todo0'] == 'closed') {?>right<?php } else { ?>down<?php }?> icon icon-sm" id="groupImage_0" aria-hidden="true"></i>
			</button>
		</td>
		<td class="bm-organizer-task-priority">&nbsp;</td>
		<td colspan="4">
			<button type="button" class="bm-organizer-section-toggle" onclick="toggleGroup(0,'todo0');">
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"undonetasks"), $_smarty_tpl);?>

			</button>
		</td>
	</tr>
	</tbody>

	<tbody id="group_0" style="display:<?php if ((true && (true && null !== ($_COOKIE['toggleGroup']['todo0'] ?? null))) && $_COOKIE['toggleGroup']['todo0'] == 'closed') {?>none<?php }?>;">

	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('todoList'), 'task', false, 'taskID');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('taskID')->value => $_smarty_tpl->getVariable('task')->value) {
$foreach1DoElse = false;
if ($_smarty_tpl->getValue('task')['akt_status'] != 64) {?>
	<tr id="task_<?php echo $_smarty_tpl->getValue('taskID');?>
">
		<td class="taskCheckBox bm-organizer-task-gutter" nowrap="nowrap">
			<input type="checkbox" class="form-check-input m-0" name="task_<?php echo $_smarty_tpl->getValue('taskID');?>
" onchange="setTaskDone('', <?php echo $_smarty_tpl->getValue('taskID');?>
, this.checked);" />
		</td>
		<td nowrap="nowrap" class="bm-organizer-task-priority">
			<?php if ($_smarty_tpl->getValue('task')['priority'] == 1) {?><i class="ti ti-alert-triangle icon icon-sm text-danger" aria-hidden="true"></i><?php }?>
		</td>
		<td nowrap="nowrap"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('task')['titel']), $_smarty_tpl);?>
</td>
		<td nowrap="nowrap"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('task')['faellig'],'nice'=>true), $_smarty_tpl);?>
</td>
		<td nowrap="nowrap" class="text-center"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('progressBar')->handle(array('width'=>80,'value'=>$_smarty_tpl->getValue('task')['erledigt'],'max'=>100), $_smarty_tpl);?>
</td>
		<td nowrap="nowrap" class="text-end bm-organizer-task-col-actions">
			<div class="btn-group btn-group-sm bm-organizer-task-actions" role="group" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"actions"), $_smarty_tpl);?>
">
				<a href="organizer.todo.php?action=editTask&id=<?php echo $_smarty_tpl->getValue('taskID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-outline-secondary btn-icon" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
				<a onclick="return confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
');" href="organizer.todo.php?action=deleteTask&taskListID=<?php echo $_smarty_tpl->getValue('taskListID');?>
&id=<?php echo $_smarty_tpl->getValue('taskID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-outline-secondary btn-icon text-danger" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
			</div>
		</td>
	</tr>
	<?php } else {
$_smarty_tpl->assign('haveDoneTasks', true, false, NULL);
}
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

	<tr id="newTask" class="bm-organizer-new-row">
		<td class="taskCheckBox bm-organizer-task-gutter">
			<i class="ti ti-plus icon icon-sm text-secondary" aria-hidden="true"></i>
		</td>
		<td>&nbsp;</td>
		<td colspan="3">
			<input type="text" class="form-control form-control-sm" id="newTaskText" onkeypress="return newTaskKeyPress(event);" onfocus="_tasksSel.unselectAll();" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addtask"), $_smarty_tpl);?>
" />
		</td>
		<td class="text-end">
			<button type="button" class="btn btn-sm btn-primary" onclick="addTask();"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
		</td>
	</tr>

	</tbody>

	<?php if ((true && ($_smarty_tpl->hasVariable('haveDoneTasks') && null !== ($_smarty_tpl->getValue('haveDoneTasks') ?? null)))) {?>
	<tbody>
	<tr class="bm-organizer-section-row">
		<td class="taskCheckBox bm-organizer-task-gutter">
			<button type="button" class="bm-organizer-section-toggle bm-organizer-section-toggle-icon" onclick="toggleGroup(1,'todo1');" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"donetasks"), $_smarty_tpl);?>
">
				<i class="ti ti-chevron-<?php if ((true && (true && null !== ($_COOKIE['toggleGroup']['todo1'] ?? null))) && $_COOKIE['toggleGroup']['todo1'] == 'closed') {?>right<?php } else { ?>down<?php }?> icon icon-sm" id="groupImage_1" aria-hidden="true"></i>
			</button>
		</td>
		<td class="bm-organizer-task-priority">&nbsp;</td>
		<td colspan="4">
			<button type="button" class="bm-organizer-section-toggle" onclick="toggleGroup(1,'todo1');">
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"donetasks"), $_smarty_tpl);?>

			</button>
		</td>
	</tr>
	</tbody>

	<tbody id="group_1" style="display:<?php if ((true && (true && null !== ($_COOKIE['toggleGroup']['todo1'] ?? null))) && $_COOKIE['toggleGroup']['todo1'] == 'closed') {?>none<?php }?>;">

	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('todoList'), 'task', false, 'taskID');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('taskID')->value => $_smarty_tpl->getVariable('task')->value) {
$foreach2DoElse = false;
?>
	<?php if ($_smarty_tpl->getValue('task')['akt_status'] == 64) {?>
	<tr id="task_<?php echo $_smarty_tpl->getValue('taskID');?>
" class="done">
		<td class="taskCheckBox bm-organizer-task-gutter" nowrap="nowrap">
			<input type="checkbox" class="form-check-input m-0" name="task_<?php echo $_smarty_tpl->getValue('taskID');?>
" checked="checked" onchange="setTaskDone('', <?php echo $_smarty_tpl->getValue('taskID');?>
, this.checked);" />
		</td>
		<td nowrap="nowrap" class="bm-organizer-task-priority">
			<?php if ($_smarty_tpl->getValue('task')['priority'] == 1) {?><i class="ti ti-alert-triangle icon icon-sm text-danger" aria-hidden="true"></i><?php }?>
		</td>
		<td nowrap="nowrap"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('task')['titel']), $_smarty_tpl);?>
</td>
		<td nowrap="nowrap"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('task')['faellig'],'nice'=>true), $_smarty_tpl);?>
</td>
		<td nowrap="nowrap" class="text-center"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('progressBar')->handle(array('width'=>80,'value'=>$_smarty_tpl->getValue('task')['erledigt'],'max'=>100), $_smarty_tpl);?>
</td>
		<td nowrap="nowrap" class="text-end bm-organizer-task-col-actions">
			<div class="btn-group btn-group-sm bm-organizer-task-actions" role="group" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"actions"), $_smarty_tpl);?>
">
				<a href="organizer.todo.php?action=editTask&id=<?php echo $_smarty_tpl->getValue('taskID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-outline-secondary btn-icon" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
				<a onclick="return confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
');" href="organizer.todo.php?action=deleteTask&taskListID=<?php echo $_smarty_tpl->getValue('taskListID');?>
&id=<?php echo $_smarty_tpl->getValue('taskID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-outline-secondary btn-icon text-danger" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
			</div>
		</td>
	</tr>
	<?php }?>
	<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	</tbody>

	<?php }?>

	</table>
</div>

<div id="contentFooter" class="contentFooter bm-organizer-footer">
	<div class="left bm-organizer-footer-actions">
		<form name="f1" method="post" action="organizer.todo.php?action=action&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" onsubmit="transferSelectedTasks()">
			<input type="hidden" name="taskListID" value="<?php echo $_smarty_tpl->getValue('taskListID');?>
" />
			<input type="hidden" name="taskIDs" id="taskIDs" value="" />

			<div class="input-group input-group-sm bm-organizer-action-group">
				<select class="form-select" name="do" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
">
					<option value="-"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
</option>
					<option value="markasdone"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markasdone"), $_smarty_tpl);?>
</option>
					<option value="delete"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
</option>
				</select>
				<button class="btn btn-primary" type="submit"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
			</div>
		</form>
	</div>
	<div class="right bm-organizer-footer-tools">
		<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='organizer.todo.php?action=addTask&taskListID=<?php echo $_smarty_tpl->getValue('taskListID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
			<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addtask"), $_smarty_tpl);?>

		</button>
	</div>
</div>

<?php echo '<script'; ?>
>
<!--
	currentTaskListID = <?php echo $_smarty_tpl->getValue('taskListID');?>
;
	initTasksSel();
	enableTodoDragTargets();
	EBID('newTaskText').focus();
//-->
<?php echo '</script'; ?>
>
<?php }
}
