<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:40:56
  from 'file:li/prefs.aliases.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15bf08d48803_92815479',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '528c3541debdcfe43c0c3110a9d1d187be19d5c4' => 
    array (
      0 => 'li/prefs.aliases.tpl',
      1 => 1779809656,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a15bf08d48803_92815479 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-prefs-page bm-prefs-page-aliases">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-at icon icon-sm" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"aliases"), $_smarty_tpl);?>

	</div>
	<div class="right">
		<?php echo $_smarty_tpl->getValue('aliasUsage');?>

	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=aliases&do=action&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">

<div class="scrollContainer withBottomBar bm-prefs-body bm-prefs-list-body">
<div class="card bm-prefs-table-card">
<div class="table-responsive bm-prefs-table-wrap">
<table class="bigTable table table-vcenter table-hover bm-prefs-table">
	<tr>
		<th class="bm-prefs-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'alias');" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
" /></label></th>
		<th>
			<a href="prefs.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&action=aliases&sort=email&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"alias"), $_smarty_tpl);?>
</a>
			<?php if ($_smarty_tpl->getValue('sortColumn') == 'email') {?><i class="fa <?php echo $_smarty_tpl->getValue('sortOrder');?>
" aria-hidden="true"></i><?php }?>
		</th>
		<th width="220">
			<a href="prefs.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&action=aliases&sort=type&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"type"), $_smarty_tpl);?>
</a>
			<?php if ($_smarty_tpl->getValue('sortColumn') == 'type') {?><i class="fa <?php echo $_smarty_tpl->getValue('sortOrder');?>
" aria-hidden="true"></i><?php }?>
		</th>
		<th class="bm-prefs-col-actions">&nbsp;</th>
	</tr>
	
	<?php if ($_smarty_tpl->getValue('aliasList')) {?>
	<tbody class="listTBody">
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('aliasList'), 'alias', false, 'aliasID');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('aliasID')->value => $_smarty_tpl->getVariable('alias')->value) {
$foreach0DoElse = false;
?>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('cycle')->handle(array('values'=>"listTableTD,listTableTD2",'assign'=>"class"), $_smarty_tpl);?>

	<tr>
		<td class="<?php echo $_smarty_tpl->getValue('class');?>
 bm-prefs-col-check" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="alias_<?php echo $_smarty_tpl->getValue('aliasID');?>
" name="alias_<?php echo $_smarty_tpl->getValue('aliasID');?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('email')->handle(array('value'=>$_smarty_tpl->getValue('alias')['email']), $_smarty_tpl);?>
" /></label></td>
		<td class="<?php if ($_smarty_tpl->getValue('sortColumn') == 'email') {?>listTableTDActive<?php } else {
echo $_smarty_tpl->getValue('class');
}?>" nowrap="nowrap"><i class="ti ti-user icon icon-sm text-secondary me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('email')->handle(array('value'=>$_smarty_tpl->getValue('alias')['email']), $_smarty_tpl);?>
</td>
		<td class="<?php if ($_smarty_tpl->getValue('sortColumn') == 'type') {?>listTableTDActive<?php } else {
echo $_smarty_tpl->getValue('class');
}?>"><?php echo $_smarty_tpl->getValue('alias')['typeText'];?>
</td>
		<td class="<?php echo $_smarty_tpl->getValue('class');?>
 bm-prefs-col-actions text-end" nowrap="nowrap">
			<div class="btn-group btn-group-sm bm-prefs-row-actions" role="group" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"actions"), $_smarty_tpl);?>
">
				<a href="prefs.php?action=aliases&do=edit&id=<?php echo $_smarty_tpl->getValue('aliasID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-outline-secondary btn-icon" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
				<a onclick="return confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
');" href="prefs.php?action=aliases&do=delete&id=<?php echo $_smarty_tpl->getValue('aliasID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-outline-secondary btn-icon text-danger" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
			</div>
		</td>
	</tr>
	<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	</tbody>
	<?php }?>
</table>
</div>
</div>
</div>

<div id="contentFooter" class="contentFooter bm-organizer-footer bm-prefs-footer">
	<div class="left">
		<div class="input-group input-group-sm bm-prefs-action-group">
			<select class="form-select bm-prefs-action-select" name="do2">
				<option value="-">------ <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
 ------</option>
				<option value="delete"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
</option>
			</select>
			<input class="btn btn-primary" type="submit" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
" />
		</div>
	</div>
	<div class="right">
		<?php if ($_smarty_tpl->getValue('allowAdd')) {?><button class="btn btn-sm btn-primary" type="button" onclick="document.location.href='prefs.php?action=aliases&do=add&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
			<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addalias"), $_smarty_tpl);?>

		</button><?php }?>
	</div>
</div>

</form>
</div>
<?php }
}
