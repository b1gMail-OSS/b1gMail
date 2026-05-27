<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:43:48
  from 'file:li/prefs.filters.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15bfb4918148_19343877',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3aab04628a72406e4a949b08db6fb6010981e75e' => 
    array (
      0 => 'li/prefs.filters.tpl',
      1 => 1779809656,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a15bfb4918148_19343877 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-prefs-page bm-prefs-page-filters">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-filter icon icon-sm" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"filters"), $_smarty_tpl);?>

	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=filters&do=action&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">

<div class="scrollContainer withBottomBar bm-prefs-body bm-prefs-list-body">
<div class="card bm-prefs-table-card">
<div class="table-responsive bm-prefs-table-wrap">
<table class="bigTable table table-vcenter table-hover bm-prefs-table">
	<tr>
		<th class="bm-prefs-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'filter');" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
" /></label></th>
		<th>
			<a href="prefs.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&action=filters&sort=title&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"title"), $_smarty_tpl);?>
</a>
			<?php if ($_smarty_tpl->getValue('sortColumn') == 'title') {?><i class="fa <?php echo $_smarty_tpl->getValue('sortOrder');?>
" aria-hidden="true"></i><?php }?>
		</th>
		<th class="bm-prefs-col-applied">
			<a href="prefs.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&action=filters&sort=applied&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"applied"), $_smarty_tpl);?>
</a>
			<?php if ($_smarty_tpl->getValue('sortColumn') == 'applied') {?><i class="fa <?php echo $_smarty_tpl->getValue('sortOrder');?>
" aria-hidden="true"></i><?php }?>
		</th>
		<th class="bm-prefs-col-orderpos">
			<a href="prefs.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&action=filters&sort=orderpos&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"orderpos"), $_smarty_tpl);?>
</a>
			<?php if ($_smarty_tpl->getValue('sortColumn') == 'orderpos') {?><i class="fa <?php echo $_smarty_tpl->getValue('sortOrder');?>
" aria-hidden="true"></i><?php }?>
		</th>
		<th class="bm-prefs-col-active">
			<a href="prefs.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&action=filters&sort=active&order=<?php echo $_smarty_tpl->getValue('sortOrderInv');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"active"), $_smarty_tpl);?>
?</a>
			<?php if ($_smarty_tpl->getValue('sortColumn') == 'active') {?><i class="fa <?php echo $_smarty_tpl->getValue('sortOrder');?>
" aria-hidden="true"></i><?php }?>
		</th>
		<th class="bm-prefs-col-actions">&nbsp;</th>
	</tr>
	
	<?php if ($_smarty_tpl->getValue('filterList')) {?>
	<tbody class="listTBody">
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('filterList'), 'filter', false, 'filterID');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('filterID')->value => $_smarty_tpl->getVariable('filter')->value) {
$foreach0DoElse = false;
?>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('cycle')->handle(array('values'=>"listTableTD,listTableTD2",'assign'=>"class"), $_smarty_tpl);?>

	<tr>
		<td class="<?php echo $_smarty_tpl->getValue('class');?>
 bm-prefs-col-check" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="filter_<?php echo $_smarty_tpl->getValue('filterID');?>
" name="filter_<?php echo $_smarty_tpl->getValue('filterID');?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('filter')['title']), $_smarty_tpl);?>
" /></label></td>
		<td class="<?php if ($_smarty_tpl->getValue('sortColumn') == 'title') {?>listTableTDActive<?php } else {
echo $_smarty_tpl->getValue('class');
}?>" nowrap="nowrap"><a href="prefs.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&action=filters&do=edit&id=<?php echo $_smarty_tpl->getValue('filterID');?>
"><i class="ti ti-filter icon icon-sm text-secondary me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('filter')['title']), $_smarty_tpl);?>
</a></td>
		<td class="<?php if ($_smarty_tpl->getValue('sortColumn') == 'applied') {?>listTableTDActive<?php } else {
echo $_smarty_tpl->getValue('class');
}?> bm-prefs-col-applied" nowrap="nowrap"><?php echo $_smarty_tpl->getValue('filter')['applied'];?>
</td>
		<td class="<?php if ($_smarty_tpl->getValue('sortColumn') == 'orderpos') {?>listTableTDActive<?php } else {
echo $_smarty_tpl->getValue('class');
}?> bm-prefs-col-orderpos" nowrap="nowrap">
			<span class="bm-prefs-orderpos-value"><?php echo $_smarty_tpl->getValue('filter')['orderpos'];?>
</span>
			<div class="btn-group btn-group-sm bm-prefs-row-actions bm-prefs-orderpos-actions ms-1" role="group" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"orderpos"), $_smarty_tpl);?>
">
				<a href="prefs.php?action=filters&down=<?php echo $_smarty_tpl->getValue('filterID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-outline-secondary btn-icon"><i class="ti ti-arrow-down icon" aria-hidden="true"></i></a>
				<a href="prefs.php?action=filters&up=<?php echo $_smarty_tpl->getValue('filterID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-outline-secondary btn-icon"><i class="ti ti-arrow-up icon" aria-hidden="true"></i></a>
			</div>
		</td>
		<td class="<?php if ($_smarty_tpl->getValue('sortColumn') == 'active') {?>listTableTDActive<?php } else {
echo $_smarty_tpl->getValue('class');
}?> bm-prefs-col-active" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" disabled="disabled"<?php if ($_smarty_tpl->getValue('filter')['active']) {?> checked="checked"<?php }?> aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"active"), $_smarty_tpl);?>
" /></label></td>
		<td class="<?php echo $_smarty_tpl->getValue('class');?>
 bm-prefs-col-actions text-end" nowrap="nowrap">
			<div class="btn-group btn-group-sm bm-prefs-row-actions" role="group" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"actions"), $_smarty_tpl);?>
">
				<a href="prefs.php?action=filters&do=edit&id=<?php echo $_smarty_tpl->getValue('filterID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-outline-secondary btn-icon" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>
"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
				<a onclick="return confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
');" href="prefs.php?action=filters&do=delete&id=<?php echo $_smarty_tpl->getValue('filterID');?>
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
		<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='prefs.php?action=filters&do=add&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
			<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addfilter"), $_smarty_tpl);?>

		</button>
	</div>
</div>

</form>
</div>
<?php }
}
