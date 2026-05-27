<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:50:22
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.quicklinks.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a5ee34f10_09834857',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd05c2a0396b670c4e00f3595deccbc5d7c805684' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.quicklinks.tpl',
      1 => 1779525290,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133a5ee34f10_09834857 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div class="innerWidget">
	<fieldset>
		<legend><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</legend>
		<a href="email.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-inbox" aria-hidden="true"></i>
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"inbox"), $_smarty_tpl);?>
</a><br />
		<a href="email.compose.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-envelope-o" aria-hidden="true"></i>
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sendmail"), $_smarty_tpl);?>
</a><br />
		<a href="email.folders.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-folder-open-o" aria-hidden="true"></i>
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"folderadmin"), $_smarty_tpl);?>
</a><br />
	</fieldset>
	<fieldset>
		<legend><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"organizer"), $_smarty_tpl);?>
</legend>
		<a href="organizer.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-tachometer" aria-hidden="true"></i>
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"overview"), $_smarty_tpl);?>
</a><br />
		<a href="organizer.calendar.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-calendar" aria-hidden="true"></i>
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"calendar"), $_smarty_tpl);?>
</a><br />
		<a href="organizer.todo.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-tasks" aria-hidden="true"></i>
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tasks"), $_smarty_tpl);?>
</a><br />
		<a href="organizer.addressbook.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-address-book" aria-hidden="true"></i>
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addressbook"), $_smarty_tpl);?>
</a><br />
		<a href="organizer.notes.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-sticky-note-o" aria-hidden="true"></i>
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notes"), $_smarty_tpl);?>
</a><br />
	</fieldset>
	<?php if ($_smarty_tpl->getValue('pageTabs')['webdisk']) {?><fieldset>
		<legend><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"webdisk"), $_smarty_tpl);?>
</legend>
		<a href="webdisk.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-cloud" aria-hidden="true"></i>
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"webdisk"), $_smarty_tpl);?>
</a><br />
		<a href="webdisk.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&do=uploadFilesForm"><i class="fa fa-share-square-o" aria-hidden="true"></i>
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"uploadfiles"), $_smarty_tpl);?>
</a><br />
	</fieldset><?php }?>
	<fieldset>
		<legend><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"misc"), $_smarty_tpl);?>
</legend>
		<a href="prefs.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-cog" aria-hidden="true"></i>
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prefs"), $_smarty_tpl);?>
</a><br />
		<a href="start.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&action=logout"><i class="fa fa-sign-out" aria-hidden="true"></i>
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"logout"), $_smarty_tpl);?>
</a><br />
	</fieldset>
</div><?php }
}
