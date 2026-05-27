<?php
/* Smarty version 5.8.0, created on 2026-05-24 21:26:47
  from 'file:li/organizer.addressbook.popup.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1350f740bec2_13130094',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2516e9ea0f378be5b48b77d005018e8bda6ac029' => 
    array (
      0 => 'li/organizer.addressbook.popup.tpl',
      1 => 1779650560,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/dialog.head.tpl' => 1,
    'file:li/dialog.foot.tpl' => 1,
  ),
))) {
function content_6a1350f740bec2_13130094 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
$_smarty_tpl->getSmarty()->getRuntime('Capture')->open($_smarty_tpl, 'default', "dialogTitleText", null);
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addressbook"), $_smarty_tpl);
$_smarty_tpl->getSmarty()->getRuntime('Capture')->close($_smarty_tpl);
$_smarty_tpl->renderSubTemplate("file:li/dialog.head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('dialogTitle'=>$_smarty_tpl->getValue('dialogTitleText'),'dialogBodyClass'=>"bm-dialog-addressbook",'dialogOnLoad'=>"documentLoader()"), (int) 0, $_smarty_current_dir);
?>

<div class="bm-dialog-page bm-dialog-page-fill bm-addressbook-dialog">
	<div class="bm-addressbook-picker mb-3">
		<label class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addressbook"), $_smarty_tpl);?>
</label>
		<div class="addressDiv bm-addressbook-list" id="addresses"></div>
	</div>

	<div class="bm-addressbook-targets">
		<div class="mb-2">
			<label class="form-label" for="addrTarget_to"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"to"), $_smarty_tpl);?>
</label>
			<div class="input-group">
				<div class="addressDiv bm-addressbook-target" id="to" role="textbox" aria-labelledby="addrTarget_to"></div>
				<button type="button" class="btn btn-primary" onclick="addAddr('to');" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"to"), $_smarty_tpl);?>
">
					<i class="ti ti-arrow-right icon" aria-hidden="true"></i>
				</button>
			</div>
		</div>
		<div class="mb-2">
			<label class="form-label" for="addrTarget_cc">CC</label>
			<div class="input-group">
				<div class="addressDiv bm-addressbook-target" id="cc" role="textbox" aria-labelledby="addrTarget_cc"></div>
				<button type="button" class="btn btn-primary" onclick="addAddr('cc');" title="CC">
					<i class="ti ti-arrow-right icon" aria-hidden="true"></i>
				</button>
			</div>
		</div>
		<div class="mb-2">
			<label class="form-label" for="addrTarget_bcc">BCC</label>
			<div class="input-group">
				<div class="addressDiv bm-addressbook-target" id="bcc" role="textbox" aria-labelledby="addrTarget_bcc"></div>
				<button type="button" class="btn btn-primary" onclick="addAddr('bcc');" title="BCC">
					<i class="ti ti-arrow-right icon" aria-hidden="true"></i>
				</button>
			</div>
		</div>
	</div>

	<div class="bm-dialog-actions">
		<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cancel"), $_smarty_tpl);?>
</button>
		<button type="button" class="btn btn-primary" onclick="submitAddressDialog('<?php echo $_smarty_tpl->getValue('mode');?>
')">
			<i class="ti ti-check icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>

		</button>
	</div>
</div>

<?php echo '<script'; ?>
>
<!--
	registerLoadAction(initAddressDialog);

	var toAddr = [],
		ccAddr = [],
		bccAddr = [],
		Addr = [];

	function initAddressDialog()
	{
		<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('addresses'), 'address');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('address')->value) {
$foreach0DoElse = false;
?>
		<?php if (($_smarty_tpl->getValue('mode') == 'handy' && $_smarty_tpl->getValue('address')['handy']) || ($_smarty_tpl->getValue('mode') != 'handy' && ($_smarty_tpl->getValue('address')['email1'] || $_smarty_tpl->getValue('address')['email2']))) {?>
		<?php echo $_smarty_tpl->getValue('address')['type'];?>
Addr.push(["<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('noentities'=>true,'escape'=>true,'value'=>$_smarty_tpl->getValue('address')['name']), $_smarty_tpl);?>
",
									"<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('noentities'=>true,'escape'=>true,'value'=>$_smarty_tpl->getValue('address')['email1']), $_smarty_tpl);?>
",
									"<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('noentities'=>true,'escape'=>true,'value'=>$_smarty_tpl->getValue('address')['email2']), $_smarty_tpl);?>
",
									"<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('noentities'=>true,'escape'=>true,'value'=>$_smarty_tpl->getValue('address')['handy']), $_smarty_tpl);?>
"]);
		<?php }?>
		<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

		<?php if ($_smarty_tpl->getValue('mode') != 'handy') {?>
		initEMailAddresses(Addr, toAddr, ccAddr, bccAddr);
		<?php } else { ?>
		initMobileAddresses(Addr, toAddr);
		<?php }?>
		
	}
//-->
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:li/dialog.foot.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
