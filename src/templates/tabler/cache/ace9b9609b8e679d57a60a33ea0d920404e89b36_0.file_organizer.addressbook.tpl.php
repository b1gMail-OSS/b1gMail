<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:21:01
  from 'file:li/organizer.addressbook.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15902d4baa51_26569743',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ace9b9609b8e679d57a60a33ea0d920404e89b36' => 
    array (
      0 => 'li/organizer.addressbook.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a15902d4baa51_26569743 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-organizer-page bm-organizer-addressbook">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-address-book icon icon-sm" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addressbook"), $_smarty_tpl);?>

		</div>
		<div class="right bm-organizer-header-actions">
			<div class="d-flex flex-wrap align-items-center gap-2">
				<label class="small text-secondary mb-0" for="abLetterFilter"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"view"), $_smarty_tpl);?>
:</label>
				<select class="form-select form-select-sm" id="abLetterFilter" style="width:auto;min-width:4rem;" onchange="document.location.href='organizer.addressbook.php?sid='+currentSID+'&group=<?php echo $_smarty_tpl->getValue('currentGroup');?>
&letter='+this.value;">
					<option value=""><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"all"), $_smarty_tpl);?>
</option>
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('alpha'), 'letter', false, 'key');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('letter')->value) {
$foreach0DoElse = false;
?>
					<option value="<?php echo $_smarty_tpl->getValue('key');?>
"<?php if ($_REQUEST['letter'] == $_smarty_tpl->getValue('key')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getValue('letter');?>
</option>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</select>

				<label class="small text-secondary mb-0" for="abGroupFilter"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"group"), $_smarty_tpl);?>
:</label>
				<select class="form-select form-select-sm" id="abGroupFilter" style="width:auto;min-width:8rem;" onchange="updateCurrentGroup(this.value,'<?php echo $_smarty_tpl->getValue('sid');?>
')">
					<option value="-1"<?php if ($_smarty_tpl->getValue('currentGroup') == -1) {?> selected="selected"<?php }?>>------------</option>
					<optgroup label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"groups"), $_smarty_tpl);?>
">
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('groupList'), 'group', false, 'groupID');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('groupID')->value => $_smarty_tpl->getVariable('group')->value) {
$foreach1DoElse = false;
?>
						<option value="<?php echo $_smarty_tpl->getValue('groupID');?>
"<?php if ($_smarty_tpl->getValue('currentGroup') == $_smarty_tpl->getValue('groupID')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('group')['title'],'cut'=>25), $_smarty_tpl);?>
</option>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					</optgroup>
				</select>

				<button type="button" class="btn btn-sm btn-outline-primary" onclick="abGroups();">
					<i class="ti ti-users icon icon-sm me-1" aria-hidden="true"></i>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"editgroups"), $_smarty_tpl);?>

				</button>
				<button type="button" class="btn btn-sm btn-outline-primary" onclick="abImport();">
					<i class="ti ti-upload icon icon-sm me-1" aria-hidden="true"></i>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"import"), $_smarty_tpl);?>

				</button>
				<button type="button" class="btn btn-sm btn-outline-primary" onclick="abExport();">
					<i class="ti ti-download icon icon-sm me-1" aria-hidden="true"></i>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"export"), $_smarty_tpl);?>

				</button>
			</div>
		</div>
	</div>

	<div class="scrollContainer bm-organizer-split">
		<div class="addressContents bm-organizer-address-list" id="hSep1">
			<div class="addressContainer withBottomBar bm-organizer-address-table-wrap">
				<table class="table table-vcenter table-hover card-table bm-organizer-table" id="addressTable">
					<thead>
					<tr>
						<th class="bm-organizer-task-gutter">&nbsp;</th>
						<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"name"), $_smarty_tpl);?>
</th>
					</tr>
					</thead>

					<?php if ($_smarty_tpl->getValue('addressList')) {?>
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('addressList'), 'addresses', false, 'letter');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('letter')->value => $_smarty_tpl->getVariable('addresses')->value) {
$foreach2DoElse = false;
?>
					<?php $_smarty_tpl->assign('groupID', "addr".((string)$_smarty_tpl->getValue('letter')), false, NULL);?>

					<tbody>
					<tr class="bm-organizer-section-row">
						<td class="bm-organizer-task-gutter">
							<button type="button" class="bm-organizer-section-toggle bm-organizer-section-toggle-icon" onclick="toggleGroup('<?php echo $_smarty_tpl->getValue('letter');?>
','addr<?php echo $_smarty_tpl->getValue('letter');?>
');" aria-label="<?php echo $_smarty_tpl->getValue('letter');?>
">
								<i class="ti ti-chevron-<?php if ($_COOKIE['toggleGroup'][$_smarty_tpl->getValue('groupID')] == 'closed') {?>right<?php } else { ?>down<?php }?> icon icon-sm" id="groupImage_<?php echo $_smarty_tpl->getValue('letter');?>
" aria-hidden="true"></i>
							</button>
						</td>
						<td>
							<button type="button" class="bm-organizer-section-toggle" onclick="toggleGroup('<?php echo $_smarty_tpl->getValue('letter');?>
','addr<?php echo $_smarty_tpl->getValue('letter');?>
');">
								<?php echo $_smarty_tpl->getValue('letter');?>

							</button>
						</td>
					</tr>
					</tbody>

					<tbody id="group_<?php echo $_smarty_tpl->getValue('letter');?>
" style="display:<?php if ($_COOKIE['toggleGroup'][$_smarty_tpl->getValue('groupID')] == 'closed') {?>none<?php }?>;">

					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('addresses'), 'address', false, 'addressID');
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('addressID')->value => $_smarty_tpl->getVariable('address')->value) {
$foreach3DoElse = false;
?>
					<tr id="addr_<?php echo $_smarty_tpl->getValue('addressID');?>
">
						<td class="bm-organizer-task-gutter">
							<?php if ($_smarty_tpl->getValue('templatePrefs')['showCheckboxes']) {?>
							<input type="checkbox" class="form-check-input m-0" id="selecTable_<?php echo $_smarty_tpl->getValue('addressID');?>
" />
							<?php }?>
						</td>
						<td>
							<?php if (!$_smarty_tpl->getValue('address')['vorname'] && !$_smarty_tpl->getValue('address')['nachname'] && $_smarty_tpl->getValue('address')['firma']) {?>
							<strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('address')['firma']), $_smarty_tpl);?>
</strong>
							<?php } else { ?>
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('address')['vorname']), $_smarty_tpl);?>

							<strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('address')['nachname']), $_smarty_tpl);?>
</strong>
							<?php }?>
						</td>
					</tr>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

					</tbody>

					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					<?php }?>
				</table>
			</div>

			<form name="f1" method="post" action="organizer.addressbook.php?action=action&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" onsubmit="transferSelectedAddresses();">
			<input name="addrIDs" id="addrIDs" value="" type="hidden" />

			<div id="contentFooter" class="contentFooter bm-organizer-footer">
				<div class="left bm-organizer-footer-actions">
					<div class="input-group input-group-sm bm-organizer-action-group">
						<select class="form-select" name="do" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
">
							<option value="-"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
</option>

							<optgroup label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"actions"), $_smarty_tpl);?>
">
								<option value="export"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"export_csv"), $_smarty_tpl);?>
</option>
								<option value="sendmail"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sendmail"), $_smarty_tpl);?>
</option>
								<option value="delete"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
</option>
							</optgroup>

							<?php if ($_smarty_tpl->getValue('groupList')) {?><optgroup label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"associatewith"), $_smarty_tpl);?>
">
							<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('groupList'), 'group', false, 'groupID');
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('groupID')->value => $_smarty_tpl->getVariable('group')->value) {
$foreach4DoElse = false;
?>
								<option value="addtogroup_<?php echo $_smarty_tpl->getValue('groupID');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('group')['title'],'cut'=>32), $_smarty_tpl);?>
</option>
							<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
							</optgroup><?php }?>
						</select>
						<button class="btn btn-primary" type="submit"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
					</div>
				</div>

				<div class="right bm-organizer-footer-tools">
					<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='organizer.addressbook.php?action=addContact&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
						<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"add"), $_smarty_tpl);?>

					</button>
				</div>
			</div>
			</form>
		</div>

		<div id="hSepSep"></div>

		<div class="addressPreview bm-organizer-address-preview" id="hSep2">
			<div id="previewArea" style="display:none;"></div>
			<div id="multiSelPreview" class="bm-organizer-address-preview-empty">
				<div id="multiSelPreview_vCenter">
					<div id="multiSelPreview_inner">
						<div id="multiSelPreview_count"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"nocontactselected"), $_smarty_tpl);?>
</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo '<script'; ?>
>
	<!--
		registerLoadAction('initHSep(\'addr\')');
		initAddrSel();
	//-->
	<?php echo '</script'; ?>
>
</div>
<?php }
}
