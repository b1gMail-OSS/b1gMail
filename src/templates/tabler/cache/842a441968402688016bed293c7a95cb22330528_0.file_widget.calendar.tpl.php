<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:50:22
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.calendar.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a5ee257a2_87366098',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '842a441968402688016bed293c7a95cb22330528' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.calendar.tpl',
      1 => 1779525290,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133a5ee257a2_87366098 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div class="innerWidget">
	<center>
		<?php echo $_smarty_tpl->getValue('bmwidget_calendar_html');?>

	</center>
	
	<div class="clndrWdgtDates">
	<?php if ($_smarty_tpl->getValue('bmwidget_calendar_nextDates')) {?>
		<ul>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('bmwidget_calendar_nextDates'), '_date');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('_date')->value) {
$foreach1DoElse = false;
?>
			<li>
				<span class="date"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('_date')['startdate'],'format'=>"%a., %d.%m."), $_smarty_tpl);?>
</span>
				<a href="organizer.calendar.php?date=<?php echo $_smarty_tpl->getValue('_date')['startdate'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('_date')['title'],'cut'=>35), $_smarty_tpl);?>
</a>
			</li>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</ul>
	<?php } else { ?>
		<div style="text-align:center;font-size:10px;font-style:italic;"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"nodatesin31d"), $_smarty_tpl);?>
</div>
	<?php }?>
	</div>
</div><?php }
}
