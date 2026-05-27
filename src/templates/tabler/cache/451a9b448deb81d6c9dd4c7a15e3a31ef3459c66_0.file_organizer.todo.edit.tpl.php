<?php
/* Smarty version 5.8.0, created on 2026-05-24 21:06:44
  from 'file:li/organizer.todo.edit.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a134c44d2e912_81842317',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '451a9b448deb81d6c9dd4c7a15e3a31ef3459c66' => 
    array (
      0 => 'li/organizer.todo.edit.tpl',
      1 => 1779633343,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a134c44d2e912_81842317 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-organizer-page bm-organizer-form-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-list-check icon icon-sm" aria-hidden="true"></i>
			<?php if ((true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edittask"), $_smarty_tpl);
} else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addtask"), $_smarty_tpl);
}?>
		</div>
	</div>

	<form name="f1" method="post" action="organizer.todo.php?action=<?php if ((true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null)))) {?>saveTask&id=<?php echo $_smarty_tpl->getValue('task')['id'];
} else { ?>createTask<?php }?>&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="card bm-organizer-form-card" onsubmit="return(checkTodoForm(this));">
		<div class="card-body">
			<h3 class="card-title mb-4"><?php if ((true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edittask"), $_smarty_tpl);
} else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addtask"), $_smarty_tpl);
}?></h3>

			<div class="row g-3">
				<div class="col-md-6">
					<label class="form-label required" for="taskListID"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tasklist"), $_smarty_tpl);?>
</label>
					<select class="form-select" name="taskListID" id="taskListID">
						<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('taskLists'), 'taskList');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('taskList')->value) {
$foreach0DoElse = false;
?>
						<option value="<?php echo $_smarty_tpl->getValue('taskList')['tasklistid'];?>
"<?php if ((!(true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null))) && $_smarty_tpl->getValue('taskListID') == $_smarty_tpl->getValue('taskList')['tasklistid']) || ((true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null))) && $_smarty_tpl->getValue('task')['tasklistid'] == $_smarty_tpl->getValue('taskList')['tasklistid'])) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('taskList')['title']), $_smarty_tpl);?>
</option>
						<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					</select>
				</div>

				<div class="col-md-6">
					<label class="form-label required" for="titel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"title"), $_smarty_tpl);?>
</label>
					<input type="text" class="form-control" name="titel" id="titel" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('task')['titel'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('task')['titel'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
				</div>

				<div class="col-md-6">
					<label class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"begin"), $_smarty_tpl);?>
</label>
					<div class="bm-organizer-datetime">
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('html_select_date')->handle(array('prefix'=>"beginn",'time'=>(($tmp = $_smarty_tpl->getValue('task')['beginn'] ?? null)===null||$tmp==='' ? 0 ?? null : $tmp),'end_year'=>"+5",'start_year'=>"-5",'field_order'=>"DMY",'field_separator'=>"."), $_smarty_tpl);?>
,
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('html_select_time')->handle(array('prefix'=>"beginn",'time'=>(($tmp = $_smarty_tpl->getValue('task')['beginn'] ?? null)===null||$tmp==='' ? 0 ?? null : $tmp),'display_seconds'=>false), $_smarty_tpl);?>

					</div>
				</div>

				<div class="col-md-6">
					<label class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"due"), $_smarty_tpl);?>
</label>
					<div class="bm-organizer-datetime">
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('html_select_date')->handle(array('prefix'=>"faellig",'time'=>(($tmp = $_smarty_tpl->getValue('task')['faellig'] ?? null)===null||$tmp==='' ? 0 ?? null : $tmp),'end_year'=>"+5",'start_year'=>"-5",'field_order'=>"DMY",'field_separator'=>"."), $_smarty_tpl);?>
,
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('html_select_time')->handle(array('prefix'=>"faellig",'time'=>(($tmp = $_smarty_tpl->getValue('task')['faellig'] ?? null)===null||$tmp==='' ? 0 ?? null : $tmp),'display_seconds'=>false), $_smarty_tpl);?>

					</div>
				</div>

				<div class="col-md-4">
					<label class="form-label required" for="erledigt"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"done"), $_smarty_tpl);?>
</label>
					<div class="input-group">
						<input type="text" class="form-control" name="erledigt" id="erledigt" value="<?php if ((true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null)))) {
echo $_smarty_tpl->getValue('task')['erledigt'];
} else { ?>0<?php }?>" />
						<span class="input-group-text">%</span>
					</div>
				</div>

				<div class="col-md-4">
					<label class="form-label" for="akt_status"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"status"), $_smarty_tpl);?>
</label>
					<select class="form-select" name="akt_status" id="akt_status">
						<option value="16"<?php if ((true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null))) && $_smarty_tpl->getValue('task')['akt_status'] == 16) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"taskst_16"), $_smarty_tpl);?>
</option>
						<option value="32"<?php if ((true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null))) && $_smarty_tpl->getValue('task')['akt_status'] == 32) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"taskst_32"), $_smarty_tpl);?>
</option>
						<option value="64"<?php if ((true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null))) && $_smarty_tpl->getValue('task')['akt_status'] == 64) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"taskst_64"), $_smarty_tpl);?>
</option>
						<option value="128"<?php if ((true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null))) && $_smarty_tpl->getValue('task')['akt_status'] == 128) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"taskst_128"), $_smarty_tpl);?>
</option>
					</select>
				</div>

				<div class="col-md-4">
					<label class="form-label" for="priority"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"priority"), $_smarty_tpl);?>
</label>
					<select class="form-select" name="priority" id="priority">
						<option value="1"<?php if ((true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null))) && $_smarty_tpl->getValue('task')['priority'] == 1) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_1"), $_smarty_tpl);?>
</option>
						<option value="0"<?php if (!(true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null))) || $_smarty_tpl->getValue('task')['priority'] == 0) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_0"), $_smarty_tpl);?>
</option>
						<option value="-1"<?php if ((true && ($_smarty_tpl->hasVariable('task') && null !== ($_smarty_tpl->getValue('task') ?? null))) && $_smarty_tpl->getValue('task')['priority'] == -1) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_-1"), $_smarty_tpl);?>
</option>
					</select>
				</div>

				<div class="col-12">
					<label class="form-label" for="comments"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"comment"), $_smarty_tpl);?>
</label>
					<textarea class="form-control" name="comments" id="comments" rows="5"><?php if ((true && (true && null !== ($_smarty_tpl->getValue('task')['comments'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('task')['comments'],'allowEmpty'=>true), $_smarty_tpl);
}?></textarea>
				</div>
			</div>

			<div class="btn-list mt-4">
				<button type="submit" class="btn btn-primary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
				<button type="reset" class="btn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"reset"), $_smarty_tpl);?>
</button>
			</div>
		</div>
	</form>
</div>
<?php }
}
