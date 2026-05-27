<?php
/* Smarty version 5.8.0, created on 2026-05-24 21:05:30
  from 'file:li/organizer.calendar.groups.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a134bfa8d0944_50094131',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1de7677475f36d90046ac9ded76afbc5e741718c' => 
    array (
      0 => 'li/organizer.calendar.groups.tpl',
      1 => 1779525291,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a134bfa8d0944_50094131 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div id="contentHeader">
	<div class="left">
		<i class="fa fa-calendar-o" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"groups"), $_smarty_tpl);?>

	</div>
</div>

<form name="f1" method="post" action="organizer.calendar.php?action=groups&do=action&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">

<div class="scrollContainer withBottomBar">
<table class="bigTable">
	<tr>
		<th class="listTableHead" width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'group');" /></th>
		<th class="listTableHead">
			<a href="organizer.calendar.php?action=groups&sid=<?php echo $_smarty_tpl->getValue('sid');?>
&sort=title&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"title"), $_smarty_tpl);?>
</a>
			<?php if ($_smarty_tpl->getValue('sortColumn') == 'title') {?><i class="fa <?php echo $_smarty_tpl->getValue('sortOrder');?>
" aria-hidden="true"></i><?php }?>
		</th>
		<th class="listTableHead" width="120">
			<a href="organizer.calendar.php?action=groups&sid=<?php echo $_smarty_tpl->getValue('sid');?>
&sort=color&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"color"), $_smarty_tpl);?>
</a>
			<?php if ($_smarty_tpl->getValue('sortColumn') == 'color') {?><i class="fa <?php echo $_smarty_tpl->getValue('sortOrder');?>
" aria-hidden="true"></i><?php }?>
		</th>
		<th class="listTableHead" width="55">&nbsp;</th>
	</tr>
	
	<?php if ($_smarty_tpl->getValue('haveGroups')) {?>
	<tbody class="listTBody">
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('groups'), 'group', false, 'groupID');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('groupID')->value => $_smarty_tpl->getVariable('group')->value) {
$foreach0DoElse = false;
?>
	<?php if ($_smarty_tpl->getValue('groupID') != -1) {?>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('cycle')->handle(array('values'=>"listTableTD,listTableTD2",'assign'=>"class"), $_smarty_tpl);?>

	<tr>
		<td class="<?php echo $_smarty_tpl->getValue('class');?>
" nowrap="nowrap"><input type="checkbox" id="group_<?php echo $_smarty_tpl->getValue('groupID');?>
" name="group_<?php echo $_smarty_tpl->getValue('groupID');?>
" /></td>
		<td nowrap="nowrap" class="<?php if ($_smarty_tpl->getValue('sortColumn') == 'title') {?>listTableTDActive<?php } else {
echo $_smarty_tpl->getValue('class');
}?>">&nbsp;<a href="organizer.calendar.php?switchGroup=<?php echo $_smarty_tpl->getValue('groupID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-calendar-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('group')['title']), $_smarty_tpl);?>
</a></td>
		<td class="<?php if ($_smarty_tpl->getValue('sortColumn') == 'color') {?>listTableTDActive<?php } else {
echo $_smarty_tpl->getValue('class');
}?>"><div class="calendarDate_<?php echo $_smarty_tpl->getValue('group')['color'];?>
" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
		<td class="<?php echo $_smarty_tpl->getValue('class');?>
" nowrap="nowrap">
			<a href="organizer.calendar.php?action=groups&do=edit&id=<?php echo $_smarty_tpl->getValue('groupID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			<a onclick="return confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
');" href="organizer.calendar.php?action=groups&do=delete&id=<?php echo $_smarty_tpl->getValue('groupID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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

<div id="contentFooter">
	<div class="left">
		<select class="smallInput" name="do2">
			<option value="-">------ <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
 ------</option>
			<option value="delete"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
</option>
		</select>
		<input class="smallInput" type="submit" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
" />
	</div>
	<div class="right">
		<button type="button" class="primary" onclick="document.location.href='organizer.calendar.php?action=groups&do=addForm&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
			<i class="fa fa-plus-circle"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"add"), $_smarty_tpl);?>

		</button>
	</div>
</div>

</form>
<?php }
}
