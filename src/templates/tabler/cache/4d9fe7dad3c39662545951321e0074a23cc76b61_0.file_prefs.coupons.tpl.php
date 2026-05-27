<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:43:49
  from 'file:li/prefs.coupons.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15bfb53fcd91_40582958',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4d9fe7dad3c39662545951321e0074a23cc76b61' => 
    array (
      0 => 'li/prefs.coupons.tpl',
      1 => 1779809763,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a15bfb53fcd91_40582958 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-prefs-page bm-prefs-page-coupons">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-ticket icon icon-sm" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"coupons"), $_smarty_tpl);?>

	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=coupons&do=redeem&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">
<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"redeemcoupon"), $_smarty_tpl);?>
</th>
		</tr>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prefs_d_coupons"), $_smarty_tpl);?>

			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="code"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"code"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<input type="text" name="code" id="code" value="" style="width:250px;" />
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="btn btn-primary" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
" />
				<input type="reset" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"reset"), $_smarty_tpl);?>
" />
			</td>
		</tr>
	</table>
</div></div>
</form>
</div>
<?php }
}
