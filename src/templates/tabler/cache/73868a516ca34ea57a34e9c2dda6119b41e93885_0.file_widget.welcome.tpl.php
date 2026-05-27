<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:50:22
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.welcome.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a5ee41747_50702206',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '73868a516ca34ea57a34e9c2dda6119b41e93885' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.welcome.tpl',
      1 => 1779525290,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133a5ee41747_50702206 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div class="innerWidget">
	<?php echo $_smarty_tpl->getValue('bmwidget_welcome_welcomeText');?>
<br /><br />
	
	<a href="email.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-inbox" aria-hidden="true"></i>
	<?php echo $_smarty_tpl->getValue('bmwidget_welcome_mails');?>
</a><br />
	
	<a href="organizer.calendar.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-calendar" aria-hidden="true"></i>
	<?php echo $_smarty_tpl->getValue('bmwidget_welcome_dates');?>
</a><br />
	
	<a href="organizer.todo.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-tasks" aria-hidden="true"></i>
	<?php echo $_smarty_tpl->getValue('bmwidget_welcome_tasks');?>
</a><br />
	
	
</div><?php }
}
