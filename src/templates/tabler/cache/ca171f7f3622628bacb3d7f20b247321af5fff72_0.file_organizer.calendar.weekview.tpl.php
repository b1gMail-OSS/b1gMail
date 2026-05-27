<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:19:48
  from 'file:li/organizer.calendar.weekview.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a158fe40dbe56_61018993',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ca171f7f3622628bacb3d7f20b247321af5fff72' => 
    array (
      0 => 'li/organizer.calendar.weekview.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a158fe40dbe56_61018993 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-organizer-page bm-organizer-calendar">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-calendar icon icon-sm" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"calendar"), $_smarty_tpl);?>
: <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cw"), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('calWeek');?>
,
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('weekStartDate'),'dayonly'=>true), $_smarty_tpl);?>

			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"dateto"), $_smarty_tpl);?>

			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('weekEndDate'),'dayonly'=>true), $_smarty_tpl);?>

		</div>
		<div class="right">
			<button type="button" class="btn btn-sm btn-outline-primary" onclick="document.location.href='organizer.calendar.php?action=groups&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
				<i class="ti ti-users-group icon icon-sm me-1" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"editgroups"), $_smarty_tpl);?>

			</button>
		</div>
	</div>

	<div class="scrollContainer withBottomBar bm-organizer-calendar-body bm-organizer-calendar-week" id="calendarContainer">
		<div class="bm-organizer-week-allday-wrap" id="calendarWholeDayBody">
			<table class="calendarWholeDayBody bm-organizer-calendar-allday" id="weekWholeDayTable" style="border-bottom:3px double var(--tblr-border-color, #B3B8BD);">
			<tr style="border-bottom:1px solid var(--tblr-border-color, #B3B8BD);">
				<td class="calendarDayTimeCell"></td>
				<td class="calendarDaySepCell"></td>
				<td></td>
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('dates'), 'dontCare', false, 'dayName');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('dayName')->value => $_smarty_tpl->getVariable('dontCare')->value) {
$foreach1DoElse = false;
?>
				<td class="calendarWeekDayCaption bm-organizer-week-caption"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('dayName')), $_smarty_tpl);?>
</td>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
			</tr>
			<tr>
				<td class="calendarDayTimeCell">&nbsp;</td>
				<td class="calendarDaySepCell"></td>
				<td class="calendarDaySepCell2"></td>
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('dates'), 'dayDates', false, 'dayName');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('dayName')->value => $_smarty_tpl->getVariable('dayDates')->value) {
$foreach2DoElse = false;
?>
				<td class="calendarWholeDayCell bm-organizer-week-allday" style="border-right:1px solid var(--tblr-border-color, #B3B8BD);">
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('dayDates'), 'date');
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('date')->value) {
$foreach3DoElse = false;
?>
					<?php if ($_smarty_tpl->getValue('date')['flags']&1) {?>
						<div style="overflow:hidden;text-overflow:ellipsis;" class="calendarDate_<?php echo $_smarty_tpl->getValue('groups')[$_smarty_tpl->getValue('date')['group']]['color'];?>
 bm-organizer-calendar-event" onclick="showCalendarDate(<?php echo $_smarty_tpl->getValue('date')['id'];?>
, <?php echo $_smarty_tpl->getValue('date')['startdate'];?>
, <?php echo $_smarty_tpl->getValue('date')['enddate'];?>
)">
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('date')['title']), $_smarty_tpl);?>

						</div>
					<?php }?>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</td>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
			</tr>
			</table>
		</div>

		<div id="calendarDayBody" class="calendarWeekBody bm-organizer-week-grid">
		<table class="calendarDayBody">
		<?php
$_smarty_tpl->tpl_vars['__smarty_section_halfHours'] = new \Smarty\Variable(array());
if (true) {
for ($__section_halfHours_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_halfHours']->value['index'] = 0; $__section_halfHours_0_iteration <= 48; $__section_halfHours_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_halfHours']->value['index']++){
?>
		<tr>
		<?php if (($_smarty_tpl->getValue('__smarty_section_halfHours')['index'] ?? null)%2 == 0) {?>
			<td class="calendarDayTimeCell" rowspan="2">
				<div class="calendarDayTimeCellText"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('halfHourToTime')->handle(array('value'=>($_smarty_tpl->getValue('__smarty_section_halfHours')['index'] ?? null)), $_smarty_tpl);?>
</div>
			</td>
		<?php }?>
		<?php if (($_smarty_tpl->getValue('__smarty_section_halfHours')['index'] ?? null) == 0) {?>
			<td class="calendarDaySepCell" rowspan="48"></td>
			<td class="calendarDaySepCell2" rowspan="48"></td>
		<?php }?>
		<?php $_smarty_tpl->assign('d', 0, false, NULL);?>
		<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('dates'), 'dontCare', false, 'dayName');
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('dayName')->value => $_smarty_tpl->getVariable('dontCare')->value) {
$foreach4DoElse = false;
?>
			<td class="calendarDayCell<?php if (($_smarty_tpl->getValue('__smarty_section_halfHours')['index'] ?? null)%2) {?>2<?php }
if (($_smarty_tpl->getValue('__smarty_section_halfHours')['index'] ?? null) >= $_smarty_tpl->getValue('dayStart') && ($_smarty_tpl->getValue('__smarty_section_halfHours')['index'] ?? null) < $_smarty_tpl->getValue('dayEnd')) {?>_day<?php }?> calendarWeekCell bm-organizer-week-cell" id="timeRow_<?php echo $_smarty_tpl->getValue('d');?>
_<?php echo ($_smarty_tpl->getValue('__smarty_section_halfHours')['index'] ?? null);?>
" style="<?php if (($_smarty_tpl->getValue('__smarty_section_halfHours')['index'] ?? null) == 0) {?>border-top:0;<?php }?>">
				&nbsp;
			</td>
		<?php $_smarty_tpl->assign('d', $_smarty_tpl->getValue('d')+1, false, NULL);?>
		<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</tr>
		<?php
}
}
?>
		</table>
		</div>

		<?php echo '<script'; ?>
>
		<!--
			var calendarDayStart = <?php echo $_smarty_tpl->getValue('dayStart');?>
,
				calendarDayEnd = <?php echo $_smarty_tpl->getValue('dayEnd');?>
,
				calendarDates = [];

			<?php $_smarty_tpl->assign('d', 0, false, NULL);?>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('dates'), 'dayDates');
$foreach5DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('dayDates')->value) {
$foreach5DoElse = false;
?>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('dayDates'), 'date');
$foreach6DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('date')->value) {
$foreach6DoElse = false;
?>
			<?php if (($_smarty_tpl->getValue('date')['flags']&1) == 0) {?>
			calendarDates.push([
				<?php echo $_smarty_tpl->getValue('date')['id'];?>
,
				<?php echo $_smarty_tpl->getValue('date')['startdate'];?>
,
				<?php echo $_smarty_tpl->getValue('date')['enddate'];?>
,
				"<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('escape'=>true,'noentities'=>true,'value'=>$_smarty_tpl->getValue('date')['title']), $_smarty_tpl);?>
",
				<?php echo $_smarty_tpl->getValue('groups')[$_smarty_tpl->getValue('date')['group']]['color'];?>
,
				<?php echo $_smarty_tpl->getValue('d');?>

			]);
			<?php }?>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
			<?php $_smarty_tpl->assign('d', $_smarty_tpl->getValue('d')+1, false, NULL);?>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

			registerLoadAction('calendarDaySizer()');
			registerLoadAction('initCalendar()');
		//-->
		<?php echo '</script'; ?>
>
	</div>

	<div id="contentFooter" class="contentFooter bm-organizer-footer">
		<div class="right">
			<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='organizer.calendar.php?action=addDate&date=<?php echo $_smarty_tpl->getValue('theDate');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
				<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"adddate"), $_smarty_tpl);?>

			</button>
		</div>
	</div>
</div>
<?php }
}
