<?php
/* Smarty version 5.8.0, created on 2026-05-25 17:08:27
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/pacc.nli.packages.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14820b0f2e32_00983622',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cbea18c3df56fc849e8f72c74f9fb6990ec42a9a' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/pacc.nli.packages.tpl',
      1 => 1779525290,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a14820b0f2e32_00983622 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
if (!$_smarty_tpl->getValue('nliPackages')) {?><form action="index.php?action=paccOrder" method="post" onsubmit="submitSignupForm()">
<input type="hidden" name="userID" value="<?php echo $_smarty_tpl->getValue('userID');?>
" />
<input type="hidden" name="userToken" value="<?php echo $_smarty_tpl->getValue('userToken');?>
" />
<?php if ($_smarty_tpl->getValue('signUp')) {?><input type="hidden" name="signUp" value="true" /><?php }
}?>

<style type="text/css">

	.pacc-col { background-color: auto; }
	.pacc-col:nth-child(4n+4) { background-color: #FAFAFA; }

	COL.accent-1
	{
		border: 2px solid #D6E9C6;
	}
	TH.accent-1, BUTTON.accent-1
	{
		background-color: #DFF0D8;
		color: #3C763D;
	}

	COL.accent-2
	{
		border: 2px solid #BCE8F1;
	}
	TH.accent-2, BUTTON.accent-2
	{
		background-color: #D9EDF7;
		color: #31708F;
	}

	COL.accent-3
	{
		border: 2px solid #FAEBCC;
	}
	TH.accent-3, BUTTON.accent-3
	{
		background-color: #FCF8E3;
		color: #8A6D3B;
	}

	COL.pacc-spacer
	{
		width: 1px;
	}

	COL.pacc-spacer:last-of-type
	{
		visibility: collapse;
	}

</style>

<div class="container">
	<div class="page-header"><h1><?php if ($_smarty_tpl->getValue('signUp')) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signup"), $_smarty_tpl);
} elseif ($_smarty_tpl->getValue('nliPackages')) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_packages"), $_smarty_tpl);
} else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"login"), $_smarty_tpl);
}?></h1></div>

	<p>
		<?php echo $_smarty_tpl->getValue('orderText');?>

	</p>

	<table class="table">
		<colgroup>
			<col />
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('matrix')['packages'], 'package');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('package')->value) {
$foreach0DoElse = false;
?>
			<col id="col_<?php echo $_smarty_tpl->getValue('package')['id'];?>
" class="pacc-col<?php if ($_smarty_tpl->getValue('package')['accentuation']) {?> accent-<?php echo $_smarty_tpl->getValue('package')['accentuation'];
}?>" />
			<col class="pacc-spacer" />
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</colgroup>

		<thead>
		<tr>
			<th>&nbsp;</th>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('matrix')['packages'], 'package');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('package')->value) {
$foreach1DoElse = false;
?>
			<th style="text-align:center;"<?php if ($_smarty_tpl->getValue('package')['accentuation']) {?> class="accent-<?php echo $_smarty_tpl->getValue('package')['accentuation'];?>
"<?php }?>>
				<h3 class="panel-title"><?php if ($_smarty_tpl->getValue('package')['accentuation'] == 1) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_accent_1"), $_smarty_tpl);?>

				<?php } elseif ($_smarty_tpl->getValue('package')['accentuation'] == 2) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_accent_2"), $_smarty_tpl);?>

				<?php } elseif ($_smarty_tpl->getValue('package')['accentuation'] == 3) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_accent_3"), $_smarty_tpl);?>

				<?php } else { ?>&nbsp;
				<?php }?></h3>
			</th>
			<th></th>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</tr>
		</thead>

		<thead>
		<tr>
			<th>&nbsp;</th>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('matrix')['packages'], 'package');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('package')->value) {
$foreach2DoElse = false;
?>
			<th style="text-align:center;"><label for="package_<?php echo $_smarty_tpl->getValue('package')['id'];?>
"><strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('package')['title'],'cut'=>25), $_smarty_tpl);?>
</strong></label></th>
			<th></th>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</tr>
		</thead>
		
		<tbody>
		<tr>
			<td><h3><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_infos"), $_smarty_tpl);?>
</h3></td>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('matrix')['packages'], 'package');
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('package')->value) {
$foreach3DoElse = false;
?>
			<td></td><td></td>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</tr>
		<tr>
			<th scope="row"><b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_price"), $_smarty_tpl);?>
</b></th>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('matrix')['packages'], 'package');
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('package')->value) {
$foreach4DoElse = false;
?>
			<td align="center">
				<?php if ($_smarty_tpl->getValue('package')['isFree']) {?>
					<small>&nbsp;</small><br />
					<span style="line-height:20px;"><b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_free"), $_smarty_tpl);?>
</b></span>
				<?php } else { ?>
					<small><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('package')['priceInterval']), $_smarty_tpl);?>
</small><br />
					<span style="line-height:20px;"><b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('package')['price']), $_smarty_tpl);?>
</b></span>
					<br /><small><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('package')['priceTax']), $_smarty_tpl);?>
</small>
				<?php }?>
			</td>
			<td></td>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</tr>
		
		<tr>
			<td><h3><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_features"), $_smarty_tpl);?>
</h3></td>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('matrix')['packages'], 'package');
$foreach5DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('package')->value) {
$foreach5DoElse = false;
?>
			<td></td><td></td>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</tr>
		<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('matrix')['fields'], 'fieldTitle', false, 'fieldKey');
$foreach6DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('fieldKey')->value => $_smarty_tpl->getVariable('fieldTitle')->value) {
$foreach6DoElse = false;
?>
		<tr>
			<th scope="row"><?php echo $_smarty_tpl->getValue('fieldTitle');?>
</th>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('matrix')['packages'], 'package');
$foreach7DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('package')->value) {
$foreach7DoElse = false;
?>
			<td align="center"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('paccFormatField')->handle(array('value'=>$_smarty_tpl->getValue('package')['fields'][$_smarty_tpl->getValue('fieldKey')],'key'=>$_smarty_tpl->getValue('fieldKey'),'cut'=>25), $_smarty_tpl);?>
</td>
			<td></td>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</tr>
		<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		
		<?php if (!$_smarty_tpl->getValue('nliPackages')) {?>
		<tr>
			<td><h3><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_selection"), $_smarty_tpl);?>
</h3></td>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('matrix')['packages'], 'package');
$foreach8DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('package')->value) {
$foreach8DoElse = false;
?>
			<td></td><td></td>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('matrix')['packages'], 'package');
$foreach9DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('package')->value) {
$foreach9DoElse = false;
?>
			<td align="center"><input type="radio" onclick="$('#orderButton').prop('disabled',false);" name="package" id="package_<?php echo $_smarty_tpl->getValue('package')['id'];?>
" value="<?php echo $_smarty_tpl->getValue('package')['id'];?>
" /></td>
			<td></td>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</tr>
		<?php } elseif ($_smarty_tpl->getValue('regEnabled')) {?>
		<tr>
			<th scope="row">&nbsp;</th>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('matrix')['packages'], 'package');
$foreach10DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('package')->value) {
$foreach10DoElse = false;
?>
			<td align="center"><button type="button" class="btn accent-<?php echo $_smarty_tpl->getValue('package')['accentuation'];?>
" onclick="document.location.href='index.php?action=signup&paccPackage=<?php echo $_smarty_tpl->getValue('package')['id'];?>
';">
				<?php if ($_smarty_tpl->getValue('package')['isFree']) {?>
					<span class="glyphicon glyphicon-user"></span> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signup"), $_smarty_tpl);?>

				<?php } else { ?>
					<span class="glyphicon glyphicon-shopping-cart"></span> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_order"), $_smarty_tpl);?>

				<?php }?>
			</button></td>
			<td></td>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</tr>
		<?php }?>
		</tbody>
	</table>

	<?php if (!$_smarty_tpl->getValue('nliPackages')) {?>
	<div class="alert alert-info">
		<span class="glyphicon glyphicon-info-sign"></span>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"iprecord"), $_smarty_tpl);?>

	</div>

	<div class="form-group">
		<?php if ($_smarty_tpl->getValue('signUp') && !$_smarty_tpl->getValue('force')) {?><button type="submit" name="dontOrder" class="btn">
			<span class="glyphicon glyphicon-remove"></span> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_dontorder"), $_smarty_tpl);?>

		</button>
		<?php } elseif ($_smarty_tpl->getValue('signUp')) {?><button type="submit" name="dontOrder" class="btn btn-warning">
			<span class="glyphicon glyphicon-remove"></span> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_abort"), $_smarty_tpl);?>

		</button><?php }?>

		<button type="submit" name="doOrder" id="orderButton" class="btn btn-success pull-right" data-loading-text="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pleasewait"), $_smarty_tpl);?>
">
			<span class="glyphicon glyphicon-ok"></span> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pacc_doorder"), $_smarty_tpl);?>

		</button>
	</div>
	<?php }?>
</div>

<?php if (!$_smarty_tpl->getValue('nliPackages')) {?></form><?php }
}
}
