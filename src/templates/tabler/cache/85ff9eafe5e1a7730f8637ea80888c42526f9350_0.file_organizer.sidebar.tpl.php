<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:43:39
  from 'file:li/organizer.sidebar.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15bfabd6c828_84347797',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '85ff9eafe5e1a7730f8637ea80888c42526f9350' => 
    array (
      0 => 'li/organizer.sidebar.tpl',
      1 => 1779809782,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/icon.tpl' => 5,
  ),
))) {
function content_6a15bfabd6c828_84347797 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"organizer.sidebar.tpl:head"), $_smarty_tpl);?>


<div class="sidebarHeading"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"organizer"), $_smarty_tpl);?>
</div>
<div class="contentMenuIcons">
	<a href="organizer.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"<?php if ($_smarty_tpl->getValue('pageContent') == 'li/organizer.start.tpl' || $_smarty_tpl->getValue('pageContent') == 'li/organizer.customize.tpl') {?> class="active"<?php }?>><?php $_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>"fa-tachometer"), (int) 0, $_smarty_current_dir);
?> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"overview"), $_smarty_tpl);?>
</a>
	<a href="organizer.calendar.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"<?php if (substr((string) $_smarty_tpl->getValue('pageContent'), (int) 0, (int) 22) == 'li/organizer.calendar.') {?> class="active"<?php }?>><?php $_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>"fa-calendar"), (int) 0, $_smarty_current_dir);
?> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"calendar"), $_smarty_tpl);?>
</a>
	<a href="organizer.todo.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"<?php if (substr((string) $_smarty_tpl->getValue('pageContent'), (int) 0, (int) 18) == 'li/organizer.todo.') {?> class="active"<?php }?>><?php $_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>"fa-tasks"), (int) 0, $_smarty_current_dir);
?> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"todolist"), $_smarty_tpl);?>
</a>
	<a href="organizer.addressbook.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"<?php if (substr((string) $_smarty_tpl->getValue('pageContent'), (int) 0, (int) 25) == 'li/organizer.addressbook.') {?> class="active"<?php }?>><?php $_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>"fa-address-book-o"), (int) 0, $_smarty_current_dir);
?> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addressbook"), $_smarty_tpl);?>
</a>
	<a href="organizer.notes.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"<?php if (substr((string) $_smarty_tpl->getValue('pageContent'), (int) 0, (int) 19) == 'li/organizer.notes.') {?> class="active"<?php }?>><?php $_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>"fa-sticky-note-o"), (int) 0, $_smarty_current_dir);
?> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notes"), $_smarty_tpl);?>
</a>
</div>

<div class="sidebarHeading"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tasks"), $_smarty_tpl);?>
</div>
<div class="contentMenuIcons bm-organizer-sidebar-tasks">
<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('tasks'), 'task', false, 'taskID');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('taskID')->value => $_smarty_tpl->getVariable('task')->value) {
$foreach0DoElse = false;
?>
	<div class="bm-organizer-sidebar-task">
		<label class="form-check mb-0">
			<input type="checkbox" class="form-check-input m-0" id="sbTask_<?php echo $_smarty_tpl->getValue('taskID');?>
" onclick="setTaskDone('<?php echo $_smarty_tpl->getValue('sid');?>
', <?php echo $_smarty_tpl->getValue('taskID');?>
, this.checked);"<?php if ($_smarty_tpl->getValue('task')['akt_status'] == 64) {?> checked="checked"<?php }?> />
		</label>
		<a href="organizer.todo.php?action=editTask&id=<?php echo $_smarty_tpl->getValue('taskID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('task')['titel'],'cut'=>20), $_smarty_tpl);?>
</a>
	</div>
<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);
if ($_smarty_tpl->getValue('tasks_haveMore')) {?>
	<small><a href="organizer.todo.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"more"), $_smarty_tpl);?>
...</a></small>
<?php }?>
</div>

<div class="sidebarHeading"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"calendar"), $_smarty_tpl);?>
</div>
<div class="bm-organizer-minical">
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('miniCalendar')->handle(array(), $_smarty_tpl);?>

</div>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"organizer.sidebar.tpl:foot"), $_smarty_tpl);?>

<?php }
}
