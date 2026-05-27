<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:21:37
  from 'file:li/organizer.calendar.toolbar.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a159051e348c0_44235527',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3b8e9fbe58f97bcfc7f751031551924d3bc38c09' => 
    array (
      0 => 'li/organizer.calendar.toolbar.tpl',
      1 => 1779798079,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a159051e348c0_44235527 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="col-12 bm-li-email-toolbar py-0">
	<form action="organizer.calendar.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
" method="post" class="d-flex flex-wrap align-items-center gap-2 gap-md-3 w-100">
		<div class="bm-li-toolbar-item d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
			<span class="bm-li-toolbar-label" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"viewmode"), $_smarty_tpl);?>
">
				<i class="icon ti ti-layout-list icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"viewmode"), $_smarty_tpl);?>
</span>
			</span>
			<select class="form-select form-select-sm bm-li-toolbar-select" onchange="updateCalendarViewMode(this, '<?php echo $_smarty_tpl->getValue('theDate');?>
', '<?php echo $_smarty_tpl->getValue('sid');?>
')">
				<option value="day"<?php if ($_smarty_tpl->getValue('viewMode') == "day") {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"day"), $_smarty_tpl);?>
</option>
				<option value="week"<?php if ($_smarty_tpl->getValue('viewMode') == "week") {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"week"), $_smarty_tpl);?>
</option>
				<option value="month"<?php if ($_smarty_tpl->getValue('viewMode') == "month") {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"month"), $_smarty_tpl);?>
</option>
			</select>
		</div>

		<div class="bm-li-toolbar-divider d-none d-md-block" aria-hidden="true"></div>

		<div class="bm-li-toolbar-item d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
			<span class="bm-li-toolbar-label" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"group"), $_smarty_tpl);?>
">
				<i class="icon ti ti-users-group icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"group"), $_smarty_tpl);?>
</span>
			</span>
			<select class="form-select form-select-sm bm-li-toolbar-select bm-li-toolbar-select-wide" onchange="updateCalendarGroup(this, '<?php echo $_smarty_tpl->getValue('theDate');?>
', '<?php echo $_smarty_tpl->getValue('sid');?>
')">
				<option value="-2"<?php if ($_smarty_tpl->getValue('theGroup') == -2) {?> selected="selected"<?php }?>>------------</option>
				<option value="-1"<?php if ($_smarty_tpl->getValue('theGroup') == -1) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"nocalcat"), $_smarty_tpl);?>
</option>
				<optgroup label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"groups"), $_smarty_tpl);?>
">
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('groups'), 'group');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('group')->value) {
$foreach0DoElse = false;
?>
				<?php if ($_smarty_tpl->getValue('group')['id'] > 0) {?>
					<option value="<?php echo $_smarty_tpl->getValue('group')['id'];?>
"<?php if ($_smarty_tpl->getValue('theGroup') == $_smarty_tpl->getValue('group')['id']) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('group')['title']), $_smarty_tpl);?>
</option>
				<?php }?>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</optgroup>
			</select>
		</div>

		<div class="bm-li-toolbar-divider d-none d-md-block" aria-hidden="true"></div>

		<div class="bm-li-toolbar-item d-flex flex-wrap align-items-center gap-2 flex-grow-1">
			<span class="bm-li-toolbar-label" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"date"), $_smarty_tpl);?>
">
				<i class="icon ti ti-calendar icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"date"), $_smarty_tpl);?>
</span>
			</span>

			<div class="bm-li-toolbar-nav d-flex flex-wrap align-items-center gap-2">
				<?php if ($_smarty_tpl->getValue('viewMode') == 'day') {?>
				<a href="organizer.calendar.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&date=<?php echo $_smarty_tpl->getValue('date')-86400;?>
" class="btn btn-sm btn-ghost-secondary btn-icon bm-li-toolbar-btn" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"back"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"back"), $_smarty_tpl);?>
"><i class="ti ti-chevron-left icon" aria-hidden="true"></i></a>
				<span class="bm-organizer-date-select"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('html_select_date')->handle(array('prefix'=>"date_",'time'=>$_smarty_tpl->getValue('date'),'start_year'=>"-5",'end_year'=>"+5",'field_order'=>"DMY"), $_smarty_tpl);?>
</span>
				<button type="submit" class="btn btn-sm btn-ghost-secondary bm-li-toolbar-btn" name="jumpToday"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"today"), $_smarty_tpl);?>
</button>
				<button type="submit" class="btn btn-sm btn-ghost-primary bm-li-toolbar-btn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
				<a href="organizer.calendar.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&date=<?php echo $_smarty_tpl->getValue('date')+86400;?>
" class="btn btn-sm btn-ghost-secondary btn-icon bm-li-toolbar-btn" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"forward"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"forward"), $_smarty_tpl);?>
"><i class="ti ti-chevron-right icon" aria-hidden="true"></i></a>

				<?php } elseif ($_smarty_tpl->getValue('viewMode') == 'week') {?>
				<a href="organizer.calendar.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&date=<?php echo $_smarty_tpl->getValue('prevWeek');?>
" class="btn btn-sm btn-ghost-secondary btn-icon bm-li-toolbar-btn" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"back"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"back"), $_smarty_tpl);?>
"><i class="ti ti-chevron-left icon" aria-hidden="true"></i></a>
				<select class="form-select form-select-sm bm-li-toolbar-select" name="date_Week">
					<?php
$_smarty_tpl->tpl_vars['__smarty_section_w'] = new \Smarty\Variable(array());
if (true) {
for ($__section_w_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_w']->value['index'] = 1; $__section_w_0_iteration <= 52; $__section_w_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_w']->value['index']++){
?>
					<option value="<?php echo ($_smarty_tpl->getValue('__smarty_section_w')['index'] ?? null);?>
"<?php if ((true && ($_smarty_tpl->hasVariable('calWeekNo') && null !== ($_smarty_tpl->getValue('calWeekNo') ?? null))) && ($_smarty_tpl->getValue('__smarty_section_w')['index'] ?? null) == $_smarty_tpl->getValue('calWeekNo')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cw"), $_smarty_tpl);?>
 <?php echo ($_smarty_tpl->getValue('__smarty_section_w')['index'] ?? null);?>
</option>
					<?php
}
}
?>
				</select>
				<span class="bm-organizer-date-select"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('html_select_date')->handle(array('prefix'=>"date_",'time'=>$_smarty_tpl->getValue('date'),'start_year'=>"-5",'end_year'=>"+5",'field_order'=>"Y"), $_smarty_tpl);?>
</span>
				<button type="submit" class="btn btn-sm btn-ghost-secondary bm-li-toolbar-btn" name="jumpToday"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"today"), $_smarty_tpl);?>
</button>
				<button type="submit" class="btn btn-sm btn-ghost-primary bm-li-toolbar-btn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
				<a href="organizer.calendar.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&date=<?php echo $_smarty_tpl->getValue('nextWeek');?>
" class="btn btn-sm btn-ghost-secondary btn-icon bm-li-toolbar-btn" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"forward"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"forward"), $_smarty_tpl);?>
"><i class="ti ti-chevron-right icon" aria-hidden="true"></i></a>

				<?php } elseif ($_smarty_tpl->getValue('viewMode') == 'month') {?>
				<a href="organizer.calendar.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&date=<?php echo $_smarty_tpl->getValue('prevMonth');?>
" class="btn btn-sm btn-ghost-secondary btn-icon bm-li-toolbar-btn" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"back"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"back"), $_smarty_tpl);?>
"><i class="ti ti-chevron-left icon" aria-hidden="true"></i></a>
				<span class="bm-organizer-date-select"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('html_select_date')->handle(array('prefix'=>"date_",'time'=>$_smarty_tpl->getValue('date'),'display_days'=>false,'start_year'=>"-5",'end_year'=>"+5",'field_order'=>"MY"), $_smarty_tpl);?>
</span>
				<button type="submit" class="btn btn-sm btn-ghost-secondary bm-li-toolbar-btn" name="jumpToday"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"today"), $_smarty_tpl);?>
</button>
				<button type="submit" class="btn btn-sm btn-ghost-primary bm-li-toolbar-btn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
				<a href="organizer.calendar.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&date=<?php echo $_smarty_tpl->getValue('nextMonth');?>
" class="btn btn-sm btn-ghost-secondary btn-icon bm-li-toolbar-btn" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"forward"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"forward"), $_smarty_tpl);?>
"><i class="ti ti-chevron-right icon" aria-hidden="true"></i></a>
				<?php }?>
			</div>
		</div>
	</form>
</div>
<?php }
}
