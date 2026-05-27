<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:20:56
  from 'file:li/organizer.todo.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a159028a51847_30897639',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '49ab3abf9b92c1b148f627f4716aa4e3887c5bfe' => 
    array (
      0 => 'li/organizer.todo.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/organizer.todo.list.tpl' => 1,
  ),
))) {
function content_6a159028a51847_30897639 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-organizer-page bm-organizer-todo">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-list-check icon icon-sm" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"todolist"), $_smarty_tpl);?>

		</div>
	</div>

	<div class="scrollContainer bm-organizer-split">
		<div class="taskLists bm-organizer-tasklists">
			<div class="taskContainer withBottomBar bm-organizer-tasklists-scroll" id="taskListsScrollContainer">
				<div class="bm-organizer-tasklists-head px-3 py-2 border-bottom">
					<strong class="small text-secondary text-uppercase"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tasklists"), $_smarty_tpl);?>
</strong>
				</div>

				<div id="taskListsContainer" class="bm-organizer-tasklists-items">
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('taskLists'), 'taskList');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('taskList')->value) {
$foreach0DoElse = false;
?>
					<a href="#" class="taskList<?php if ($_smarty_tpl->getValue('taskList')['tasklistid'] == $_smarty_tpl->getValue('taskListID')) {?> selected<?php }?>" onclick="selectTaskList(<?php echo $_smarty_tpl->getValue('taskList')['tasklistid'];?>
); return false;" id="taskList_<?php echo $_smarty_tpl->getValue('taskList')['tasklistid'];?>
">
						<span class="bm-organizer-tasklist-title"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('taskList')['title']), $_smarty_tpl);?>
</span>
						<?php if ($_smarty_tpl->getValue('taskList')['tasklistid'] != 0) {?><img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/delcross.png" onclick="deleteTaskList(<?php echo $_smarty_tpl->getValue('taskList')['tasklistid'];?>
); return false;" alt="" /><?php }?>
					</a>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</div>
			</div>

			<div class="contentFooter bm-organizer-footer bm-organizer-tasklists-footer">
				<div class="left bm-organizer-footer-actions">
					<div class="input-group input-group-sm bm-organizer-action-group">
						<span class="input-group-text"><i class="ti ti-plus icon icon-sm" aria-hidden="true"></i></span>
						<input type="text" id="addListTitle" class="form-control" onkeypress="return todoListInputKeyPress(event);" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tasklists"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tasklists"), $_smarty_tpl);?>
" />
						<button type="button" class="btn btn-primary" onclick="addTodoList();"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
					</div>
				</div>
			</div>
		</div>

		<div class="taskContents bm-organizer-taskcontents" id="taskListContainer">
			<?php $_smarty_tpl->renderSubTemplate("file:li/organizer.todo.list.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
		</div>
	</div>
</div>

<img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/drag_task.png" style="display:none;" alt="" /><img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/drag_tasks.png" style="display:none;" alt="" />
<?php }
}
