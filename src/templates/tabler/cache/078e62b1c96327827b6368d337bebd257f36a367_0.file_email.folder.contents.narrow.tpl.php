<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:37:48
  from 'file:li/email.folder.contents.narrow.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15be4c5ea031_71734017',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '078e62b1c96327827b6368d337bebd257f36a367' => 
    array (
      0 => 'li/email.folder.contents.narrow.tpl',
      1 => 1779809710,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a15be4c5ea031_71734017 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
if (( !true || empty($_GET['tableOnly']))) {?><form name="f1" action="email.php?do=action&<?php echo $_smarty_tpl->getValue('folderString');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" onsubmit="transferSelectedMailIDs()" method="post">
<input type="hidden" name="selectedMailIDs" id="selectedMailIDs" value="" />

<div id="contentHeader">
	<div class="left"<?php if ($_smarty_tpl->getValue('templatePrefs')['showCheckboxes']) {?> style="padding-left:2px;"<?php }?>>
		<?php if ($_smarty_tpl->getValue('templatePrefs')['showCheckboxes']) {?><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="checkAllMails" onclick="if(this.checked) _mailSel.selectAll(); else _mailSel.unselectAll()||showMultiSelPreview(0);" /></label><?php }?>
		<i class="ti <?php if ($_smarty_tpl->getValue('folderInfo')['type'] == 'inbox') {?>ti-inbox<?php } elseif ($_smarty_tpl->getValue('folderInfo')['type'] == 'outbox') {?>ti-send<?php } elseif ($_smarty_tpl->getValue('folderInfo')['type'] == 'drafts') {?>ti-file-pencil<?php } elseif ($_smarty_tpl->getValue('folderInfo')['type'] == 'spam') {?>ti-ban<?php } elseif ($_smarty_tpl->getValue('folderInfo')['type'] == 'trash') {?>ti-trash<?php } elseif ($_smarty_tpl->getValue('folderInfo')['type'] == 'intellifolder') {?>ti-folder<?php } else { ?>ti-folder<?php }?> icon" aria-hidden="true"></i> <?php echo $_smarty_tpl->getValue('folderInfo')['title'];?>

	</div>

	<div class="right bm-mail-header-actions">
		<?php if ((true && (true && null !== ($_smarty_tpl->getValue('folderInfo')['type'] ?? null))) && $_smarty_tpl->getValue('folderInfo')['type'] != 'intellifolder' && ( !true || empty($_smarty_tpl->getValue('folderInfo')['readonly']))) {?>
		<button type="button" class="btn btn-icon btn-ghost-secondary" onclick="showFolderMenu(event);" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"folderactions"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"folderactions"), $_smarty_tpl);?>
">
			<i class="ti ti-settings icon" aria-hidden="true"></i>
		</button>
		<?php }?>

		<button type="button" class="btn btn-icon btn-ghost-secondary" onclick="switchPage(<?php echo $_smarty_tpl->getValue('pageNo');?>
)" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"refresh"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"refresh"), $_smarty_tpl);?>
">
			<i class="ti ti-refresh icon" aria-hidden="true"></i>
		</button>

		<?php if (( !true || empty($_smarty_tpl->getValue('folderInfo')['readonly']))) {?><button type="button" class="btn btn-icon btn-ghost-secondary" onclick="folderViewOptions(<?php echo $_smarty_tpl->getValue('folderID');?>
);" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"viewoptions"), $_smarty_tpl);?>
" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"viewoptions"), $_smarty_tpl);?>
">
			<i class="ti ti-layout-sidebar-right icon" aria-hidden="true"></i>
		</button><?php }?>
	</div>
</div>

<div class="scrollContainer withBottomBar">
<?php }?>

<table class="bigTable" id="mailTable">
	<colgroup>
		<?php if ($_smarty_tpl->getValue('templatePrefs')['showCheckboxes']) {?>
		<col style="width:24px;" />
		<?php }?>
		<col style="width:24px;" />
		<col />
	</colgroup>

	<?php if ($_smarty_tpl->getValue('mailList')) {?>
	<?php $_smarty_tpl->assign('first', true, false, NULL);?>
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('mailList'), 'mail', false, 'mailID');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('mailID')->value => $_smarty_tpl->getVariable('mail')->value) {
$foreach0DoElse = false;
?>
	<?php if ((true && (true && null !== ($_smarty_tpl->getValue('mail')['groupID'] ?? null)))) {
$_smarty_tpl->assign('mailGroupID', $_smarty_tpl->getValue('mail')['groupID'], false, NULL);?>
	<?php } else {
$_smarty_tpl->assign('mailGroupID', 0, false, NULL);
}?>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('cycle')->handle(array('values'=>"listTableTR,listTableTR2",'assign'=>"class"), $_smarty_tpl);?>


	<?php if ($_smarty_tpl->getValue('mailID') < 0) {?>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('cycle')->handle(array('values'=>"listTableTR,listTableTR2",'assign'=>"class"), $_smarty_tpl);?>

	<?php if (!$_smarty_tpl->getValue('first')) {?>
	</tbody>
	<?php }?>
	<tr>
		<td colspan="<?php if ($_smarty_tpl->getValue('templatePrefs')['showCheckboxes']) {?>3<?php } else { ?>2<?php }?>" class="folderGroup">
			<a style="display:block;cursor:pointer;" onclick="toggleGroup(<?php echo $_smarty_tpl->getValue('mailID');?>
,'<?php if ((true && (true && null !== ($_smarty_tpl->getValue('mail')['groupID'] ?? null)))) {
echo $_smarty_tpl->getValue('mail')['groupID'];
}?>');">&nbsp;<img id="groupImage_<?php echo $_smarty_tpl->getValue('mailID');?>
" src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/<?php if (!( !true || empty($_COOKIE['toggleGroup'][$_smarty_tpl->getValue('mailGroupID')])) && $_COOKIE['toggleGroup'][$_smarty_tpl->getValue('mailGroupID')] == 'closed') {?>expand<?php } else { ?>contract<?php }?>.png" width="11" height="11" border="0" align="absmiddle" alt="" />
			&nbsp;<?php echo $_smarty_tpl->getValue('mail')['text'];?>
 <?php if ((true && (true && null !== ($_smarty_tpl->getValue('mail')['date'] ?? null))) && $_smarty_tpl->getValue('mail')['date'] != -1) {?>(<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('mail')['date'],'dayonly'=>true), $_smarty_tpl);?>
)<?php }?></a>
		</td>
	</tr>
	<tbody id="group_<?php echo $_smarty_tpl->getValue('mailID');?>
" style="display:<?php if (!( !true || empty($_COOKIE['toggleGroup'][$_smarty_tpl->getValue('mailGroupID')])) && $_COOKIE['toggleGroup'][$_smarty_tpl->getValue('mailGroupID')] == 'closed') {?>none<?php }?>;">
	<?php $_smarty_tpl->assign('first', false, false, NULL);?>
	<?php } else { ?>
	<tr id="mail_<?php echo $_smarty_tpl->getValue('mailID');?>
_ntr" class="<?php echo $_smarty_tpl->getValue('class');
if ($_smarty_tpl->getValue('mail')['color'] > 0) {?> mailColor_<?php echo $_smarty_tpl->getValue('mail')['color'];
}?>">
		<?php if ($_smarty_tpl->getValue('templatePrefs')['showCheckboxes']) {?>
		<td class="narrowRow" style="text-align:center;width:24px;">
			<label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="selecTable_<?php echo $_smarty_tpl->getValue('mailID');?>
" /></label>
		</td>
		<?php }?>
		<td id="mail_<?php echo $_smarty_tpl->getValue('mailID');?>
_ncol1" class="narrowRow bm-mail-status-icon">
			<i id="mail_<?php echo $_smarty_tpl->getValue('mailID');?>
_nicon" class="ti <?php if ($_smarty_tpl->getValue('mail')['flags']&1) {?>ti-mail<?php } else { ?>ti-mail-opened<?php }?>" aria-hidden="true"></i>
		</td>
		<td draggable="false" id="mail_<?php echo $_smarty_tpl->getValue('mailID');?>
_ncol2" class="narrowRow bm-mail-card-cell">
			<a draggable="false" class="bm-mail-card-link" href="email.read.php?id=<?php echo $_smarty_tpl->getValue('mailID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" onclick="return(false)"<?php if ($_smarty_tpl->getValue('mail')['flags']&8) {?> style="text-decoration:line-through;"<?php }?>>
				<div class="bm-mail-card-top">
					<div id="mail_<?php echo $_smarty_tpl->getValue('mailID');?>
_nspan2" class="sender<?php if ($_smarty_tpl->getValue('mail')['flags']&1) {?> unread<?php }?>"><?php if ($_smarty_tpl->getValue('folderID') != -2) {
if ($_smarty_tpl->getValue('mail')['from_name']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('mail')['from_name']), $_smarty_tpl);
} else {
if ($_smarty_tpl->getValue('mail')['from_mail']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('email')->handle(array('value'=>$_smarty_tpl->getValue('mail')['from_mail']), $_smarty_tpl);
} else { ?>-<?php }
}
} else {
if ($_smarty_tpl->getValue('mail')['to_name']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('mail')['to_name']), $_smarty_tpl);
} else {
if ($_smarty_tpl->getValue('mail')['to_mail']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('email')->handle(array('value'=>$_smarty_tpl->getValue('mail')['to_mail']), $_smarty_tpl);
} else { ?>-<?php }
}
}?></div>
					<div id="mail_<?php echo $_smarty_tpl->getValue('mailID');?>
_nspan1" class="date<?php if ($_smarty_tpl->getValue('mail')['flags']&1) {?> unread<?php }?>"<?php if ($_smarty_tpl->getValue('mail')['flags']&8) {?> style="text-decoration:line-through;"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('mail')['timestamp'],'nice'=>true), $_smarty_tpl);?>
</div>
				</div>
				<div class="subject">
					<?php if ($_smarty_tpl->getValue('mail')['flags']&4096) {?><i id="maildone_<?php echo $_smarty_tpl->getValue('mailID');?>
" class="fa fa-check" aria-hidden="true"></i><?php }?>
					<?php if ($_smarty_tpl->getValue('mail')['flags']&16) {?><i id="mail_<?php echo $_smarty_tpl->getValue('mailID');?>
_flagimg" class="ti ti-flag-filled bm-mail-flag-icon" aria-hidden="true"></i><?php } elseif ($_smarty_tpl->getValue('mail')['priority'] == 1) {?><i id="mail_<?php echo $_smarty_tpl->getValue('mailID');?>
_flagimg" class="ti ti-alert-triangle bm-mail-flag-icon" aria-hidden="true"></i><?php } elseif ($_smarty_tpl->getValue('mail')['priority'] == -1) {?><i id="mail_<?php echo $_smarty_tpl->getValue('mailID');?>
_flagimg" class="ti ti-arrow-down bm-mail-flag-icon" aria-hidden="true"></i><?php } else { ?><i id="mail_<?php echo $_smarty_tpl->getValue('mailID');?>
_flagimg" class="bm-mail-flag-placeholder" aria-hidden="true"></i><?php }?>
					<?php if ($_smarty_tpl->getValue('mail')['flags']&4 || $_smarty_tpl->getValue('mail')['flags']&2) {?><i class="fa <?php if ($_smarty_tpl->getValue('mail')['flags']&4) {?>fa-mail-forward<?php } elseif ($_smarty_tpl->getValue('mail')['flags']&2) {?>fa-mail-reply<?php }?>" aria-hidden="true"></i><?php }?>
					<?php if ($_smarty_tpl->getValue('mail')['flags']&64) {?><i class="fa fa-paperclip" aria-hidden="true"></i><?php }?>
					<?php if ($_smarty_tpl->getValue('mail')['flags']&128) {?><i class="fa fa-bug" aria-hidden="true"></i><?php }?>
					<?php if ($_smarty_tpl->getValue('mail')['flags']&256) {?><i class="fa fa-ban" aria-hidden="true"></i><?php }?>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('mail')['subject']), $_smarty_tpl);?>

				</div>
				<?php if (((($tmp = $_smarty_tpl->getValue('templatePrefs')['mailListPreviewLines'] ?? null)===null||$tmp==='' ? 2 ?? null : $tmp)) > 0 && $_smarty_tpl->getValue('mail')['preview']) {?><div class="bm-mail-preview"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('mail')['preview']), $_smarty_tpl);?>
</div><?php }?>
			</a>
		</td>
	</tr>
	<?php }?>
	<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	<?php if (!$_smarty_tpl->getValue('first')) {?>
	</tbody>
	<?php }?>
	<?php }?>

</table>
<?php if (( !true || empty($_GET['tableOnly']))) {?>

</div>

<div id="contentFooter" class="contentFooter bm-mail-list-footer">
	<div class="bm-mail-list-footer-row">
		<div class="bm-mail-footer-actions">
			<div class="input-group input-group-sm bm-mail-action-group">
			<select class="form-select" name="massAction" id="massAction" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
">
				<option value="-"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
</option>

			<optgroup label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"actions"), $_smarty_tpl);?>
">
			<?php if (( !true || empty($_smarty_tpl->getValue('folderInfo')['readonly']))) {?><option value="delete"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>
</option><?php }?>
				<option value="forward"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"forward"), $_smarty_tpl);?>
</option>
				<option value="download"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"download"), $_smarty_tpl);?>
</option>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.folder.tpl:mailSelect.actions"), $_smarty_tpl);?>

			</optgroup>

			<?php if (( !true || empty($_smarty_tpl->getValue('folderInfo')['readonly']))) {?><optgroup label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"flags"), $_smarty_tpl);?>
">
				<option value="markread"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markread"), $_smarty_tpl);?>
</option>
				<option value="markunread"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markunread"), $_smarty_tpl);?>
</option>
				<option value="mark"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mark"), $_smarty_tpl);?>
</option>
				<option value="unmark"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"unmark"), $_smarty_tpl);?>
</option>
				<option value="done"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markdone"), $_smarty_tpl);?>
</option>
				<option value="undone"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"unmarkdone"), $_smarty_tpl);?>
</option>
				<option value="markspam"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markspam"), $_smarty_tpl);?>
</option>
				<option value="marknonspam"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"marknonspam"), $_smarty_tpl);?>
</option>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.folder.tpl:mailSelect.flags"), $_smarty_tpl);?>

			</optgroup>

			<optgroup label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"setmailcolor"), $_smarty_tpl);?>
">
				<option value="color_0" class="mailColor_0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"color_0"), $_smarty_tpl);?>
</option>
				<option value="color_1" class="mailColor_1"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"color_1"), $_smarty_tpl);?>
</option>
				<option value="color_2" class="mailColor_2"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"color_2"), $_smarty_tpl);?>
</option>
				<option value="color_3" class="mailColor_3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"color_3"), $_smarty_tpl);?>
</option>
				<option value="color_4" class="mailColor_4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"color_4"), $_smarty_tpl);?>
</option>
				<option value="color_5" class="mailColor_5"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"color_5"), $_smarty_tpl);?>
</option>
				<option value="color_6" class="mailColor_6"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"color_6"), $_smarty_tpl);?>
</option>
			</optgroup>

			<optgroup label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"move"), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"moveto"), $_smarty_tpl);?>
">
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('dropdownFolderList'), 'dFolderTitle', false, 'dFolderID');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('dFolderID')->value => $_smarty_tpl->getVariable('dFolderTitle')->value) {
$foreach1DoElse = false;
?>
			<option value="moveto_<?php echo $_smarty_tpl->getValue('dFolderID');?>
" style="font-family:courier;"><?php echo $_smarty_tpl->getValue('dFolderTitle');?>
</option>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
			</optgroup><?php }?>

			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.folder.tpl:mailSelect"), $_smarty_tpl);?>

			</select>
			<button type="submit" class="btn btn-primary btn-sm bm-mail-footer-ok" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
">
				<i class="ti ti-check bm-mail-footer-ok-icon" aria-hidden="true"></i>
				<span class="bm-mail-footer-ok-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</span>
			</button>
			</div>
		</div>

		<div class="bm-mail-footer-pagination">
			<div class="input-group input-group-sm bm-mail-page-group">
				<span class="input-group-text bm-mail-footer-page-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pages"), $_smarty_tpl);?>
</span>
				<select class="form-select" onchange="switchPage(this.value)" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pages"), $_smarty_tpl);?>
">
				<?php
$__section_page_0_loop = (is_array(@$_loop=$_smarty_tpl->getValue('pageCount')) ? count($_loop) : max(0, (int) $_loop));
$__section_page_0_start = min(0, $__section_page_0_loop);
$__section_page_0_total = min(($__section_page_0_loop - $__section_page_0_start), $__section_page_0_loop);
$_smarty_tpl->tpl_vars['__smarty_section_page'] = new \Smarty\Variable(array());
if ($__section_page_0_total !== 0) {
for ($__section_page_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_page']->value['index'] = $__section_page_0_start; $__section_page_0_iteration <= $__section_page_0_total; $__section_page_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_page']->value['index']++){
?>
					<option value="<?php echo ($_smarty_tpl->getValue('__smarty_section_page')['index'] ?? null)+1;?>
"<?php if ($_smarty_tpl->getValue('pageNo') == ($_smarty_tpl->getValue('__smarty_section_page')['index'] ?? null)+1) {?> selected="selected"<?php }?>><?php echo ($_smarty_tpl->getValue('__smarty_section_page')['index'] ?? null)+1;?>
</option>
				<?php
}
}
?>
				</select>
			</div>
		</div>
	</div>
</div>

</form>

<?php echo '<script'; ?>
>
<!--
	currentSortColumn = '<?php echo $_smarty_tpl->getValue('sortColumn');?>
';
	currentSortOrder = '<?php echo $_smarty_tpl->getValue('sortOrder');?>
';
	currentPageNo = <?php echo $_smarty_tpl->getValue('pageNo');?>
;
	currentPageCount = <?php echo $_smarty_tpl->getValue('pageCount');?>
;
	narrowMode = true;
	initMailSel();
//-->
<?php echo '</script'; ?>
>
<?php }
}
}
