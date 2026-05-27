<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:43:47
  from 'file:li/prefs.faq.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15bfb3d613d4_54861252',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f793ea89ed472236329f36e6189666597a171455' => 
    array (
      0 => 'li/prefs.faq.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a15bfb3d613d4_54861252 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-prefs-page bm-prefs-page-faq">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-help icon icon-sm" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"faq"), $_smarty_tpl);?>

	</div>
</div>

<div class="scrollContainer bm-prefs-body bm-prefs-list-body">
<div class="card bm-prefs-table-card">
<div class="table-responsive bm-prefs-table-wrap">
<table class="bigTable table table-vcenter table-hover bm-prefs-table">
	<tr>
		<th>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"question"), $_smarty_tpl);?>

		</th>
	</tr>
	
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('faq'), 'item');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach0DoElse = false;
?>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('cycle')->handle(array('values'=>"listTableTD,listTableTD2",'assign'=>"class"), $_smarty_tpl);?>

	<tr>
		<td class="<?php echo $_smarty_tpl->getValue('class');?>
">&nbsp;<a href="javascript:toggleGroup(<?php echo $_smarty_tpl->getValue('item')['id'];?>
);"><img id="groupImage_<?php echo $_smarty_tpl->getValue('item')['id'];?>
" src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/expand.png" width="11" height="11" border="0" alt="" align="absmiddle" /> <i class="fa fa-question-circle-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getValue('item')['frage'];?>
</a></td>
	</tr>
	<tbody id="group_<?php echo $_smarty_tpl->getValue('item')['id'];?>
" style="display:none;">
		<tr>
			<td class="listTableTDText"><?php echo $_smarty_tpl->getValue('item')['antwort'];?>
</td>
		</tr>
	</tbody>
	<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
</table>
</div>
</div>
</div>
</div>
<?php }
}
