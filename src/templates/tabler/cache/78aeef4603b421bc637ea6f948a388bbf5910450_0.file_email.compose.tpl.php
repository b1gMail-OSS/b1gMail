<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:37:50
  from 'file:li/email.compose.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15be4e8d6098_90551756',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '78aeef4603b421bc637ea6f948a388bbf5910450' => 
    array (
      0 => 'li/email.compose.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a15be4e8d6098_90551756 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><form name="f1" method="post" action="email.compose.php?action=sendMail&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" autocomplete="off" class="bm-compose-form" onreset="if(!askReset()) return(false);editor.reset();">

<div id="contentHeader" class="bm-compose-header">
	<div class="left">
		<span class="bm-compose-header-title"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sendmail"), $_smarty_tpl);?>
</span>
	</div>
	<div class="right bm-compose-header-tools">
		<select name="newTextMode" id="textMode" class="form-select form-select-sm bm-compose-control" onchange="return editor.switchMode(this.value)">
			<option value="text"<?php if (!$_smarty_tpl->getValue('mail') || $_smarty_tpl->getValue('mail')['textMode'] == 'text') {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"plaintext"), $_smarty_tpl);?>
</option>
			<option value="html"<?php if ($_smarty_tpl->getValue('mail')['textMode'] == 'html') {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"htmltext"), $_smarty_tpl);?>
</option>
		</select>
		<select name="priority" id="priority" class="form-select form-select-sm bm-compose-control">
			<option value="1"<?php if ($_smarty_tpl->getValue('mail')['priority'] == 1) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_1"), $_smarty_tpl);?>
</option>
			<option value="0"<?php if (!$_smarty_tpl->getValue('mail') || $_smarty_tpl->getValue('mail')['priority'] == 0) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_0"), $_smarty_tpl);?>
</option>
			<option value="-1"<?php if ($_smarty_tpl->getValue('mail')['priority'] == -1) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_-1"), $_smarty_tpl);?>
</option>
		</select>
	</div>
</div>

<div class="bigForm withBottomBar bm-compose-body">
	<input type="hidden" name="actionToken" value="<?php echo $_smarty_tpl->getValue('actionToken');?>
" />
	<input type="hidden" name="do" id="do" value="" />
	<input type="hidden" name="reference" id="reference" value="<?php echo $_smarty_tpl->getValue('reference');?>
" />
	<input type="hidden" name="baseDraftID" id="baseDraftID" value="<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('array_key_exists')('isAutoSavedDraft',$_smarty_tpl->getValue('mail'))) {
echo $_smarty_tpl->getValue('mail')['baseDraftID'];
}?>" />

	<?php if ((true && ($_smarty_tpl->hasVariable('latestDraft') && null !== ($_smarty_tpl->getValue('latestDraft') ?? null)))) {?>
	<div class="alert alert-info bm-compose-draft-note" id="draftNote" role="alert">
		<div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
			<div>
				<strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"drafttext"), $_smarty_tpl);?>
</strong>
				<ul class="mb-0 mt-1">
					<?php if ($_smarty_tpl->getValue('latestDraft')['subject']) {?><li><span class="text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"subject"), $_smarty_tpl);?>
:</span> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('latestDraft')['subject'],'cut'=>100), $_smarty_tpl);?>
</li><?php }?>
					<?php if ($_smarty_tpl->getValue('latestDraft')['to']) {?><li><span class="text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"to"), $_smarty_tpl);?>
:</span> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('latestDraft')['to'],'cut'=>100), $_smarty_tpl);?>
</li><?php }?>
				</ul>
			</div>
			<div class="d-flex flex-wrap align-items-center gap-2">
				<button type="button" class="btn btn-sm btn-primary" onclick="loadDraft(<?php echo $_smarty_tpl->getValue('latestDraft')['id'];?>
)"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"loaddraft"), $_smarty_tpl);?>
</button>
				<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="hideDraftNote(true,<?php echo $_smarty_tpl->getValue('latestDraft')['id'];?>
)"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"nothanks"), $_smarty_tpl);?>
</button>
				<label class="form-check mb-0" for="deleteDraft">
					<input class="form-check-input" type="checkbox" id="deleteDraft" />
					<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"deletedraft"), $_smarty_tpl);?>
</span>
				</label>
			</div>
		</div>
	</div>
	<?php }?>

	<div class="bm-compose-fields" id="composeHeader">
		<div class="bm-compose-row">
			<label class="bm-compose-label required" for="from"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"from"), $_smarty_tpl);?>
</label>
			<div class="bm-compose-field">
				<select name="from" id="from" class="form-select form-select-sm bm-compose-control">
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('possibleSenders'), 'sender', false, 'senderID');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('senderID')->value => $_smarty_tpl->getVariable('sender')->value) {
$foreach0DoElse = false;
?>
					<option value="<?php echo $_smarty_tpl->getValue('senderID');?>
"<?php if ($_smarty_tpl->getValue('senderID') == $_smarty_tpl->getValue('mail')['from']) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('email')->handle(array('value'=>$_smarty_tpl->getValue('sender')), $_smarty_tpl);?>
</option>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</select>
			</div>
		</div>

		<div class="bm-compose-row">
			<label class="bm-compose-label required" for="to"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"to"), $_smarty_tpl);?>
</label>
			<div class="bm-compose-field" id="addrDiv_to">
				<div class="input-group input-group-sm">
					<input type="text" class="form-control form-control-sm bm-compose-control" name="to" id="to" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('mail')['to'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('allowEmpty'=>true,'value'=>$_smarty_tpl->getValue('mail')['to']), $_smarty_tpl);
}?>" />
					<button type="button" class="btn btn-outline-secondary" onclick="openAddressbook('<?php echo $_smarty_tpl->getValue('sid');?>
','email')">
						<i class="ti ti-address-book icon" aria-hidden="true"></i>
						<span class="d-none d-md-inline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"fromaddr"), $_smarty_tpl);?>
</span>
					</button>
				</div>
			</div>
		</div>

		<div class="bm-compose-row">
			<label class="bm-compose-label" for="cc">
				<a class="bm-compose-advanced-toggle" href="javascript:advancedOptions('fields', 'right', 'bottom', '<?php echo $_smarty_tpl->getValue('tpldir');?>
');composeSizer(true);">
					<?php if (( !true || empty($_smarty_tpl->getValue('mail')['replyto'])) && ( !true || empty($_smarty_tpl->getValue('mail')['bcc']))) {?><i class="ti ti-chevron-right" id="advanced_fields_arrow" aria-hidden="true"></i><?php } else { ?><i class="ti ti-chevron-down" id="advanced_fields_arrow" aria-hidden="true"></i><?php }?>
				</a>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cc"), $_smarty_tpl);?>

			</label>
			<div class="bm-compose-field">
				<input type="text" class="form-control form-control-sm bm-compose-control" name="cc" id="cc" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('mail')['cc'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('allowEmpty'=>true,'value'=>$_smarty_tpl->getValue('mail')['cc']), $_smarty_tpl);
}?>" />
			</div>
		</div>

		<div id="advanced_fields_body" style="display:<?php if (( !true || empty($_smarty_tpl->getValue('mail')['replyto'])) && ( !true || empty($_smarty_tpl->getValue('mail')['bcc']))) {?>none<?php }?>;">
			<div class="bm-compose-row">
				<label class="bm-compose-label" for="bcc"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"bcc"), $_smarty_tpl);?>
</label>
				<div class="bm-compose-field">
					<input type="text" class="form-control form-control-sm bm-compose-control" name="bcc" id="bcc" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('mail')['bcc'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('allowEmpty'=>true,'value'=>$_smarty_tpl->getValue('mail')['bcc']), $_smarty_tpl);
}?>" />
				</div>
			</div>
			<div class="bm-compose-row">
				<label class="bm-compose-label" for="replyto"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"replyto"), $_smarty_tpl);?>
</label>
				<div class="bm-compose-field">
					<input type="text" class="form-control form-control-sm bm-compose-control" name="replyto" id="replyto" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('mail')['replyto'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('allowEmpty'=>true,'value'=>$_smarty_tpl->getValue('mail')['replyto']), $_smarty_tpl);
}?>" />
				</div>
			</div>
		</div>

		<div class="bm-compose-row">
			<label class="bm-compose-label required" for="subject"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"subject"), $_smarty_tpl);?>
</label>
			<div class="bm-compose-field">
				<input type="text" class="form-control form-control-sm bm-compose-control" name="subject" id="subject" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('mail')['subject'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('allowEmpty'=>true,'value'=>$_smarty_tpl->getValue('mail')['subject']), $_smarty_tpl);
}?>" onchange="beginDraftAutoSave()" />
			</div>
		</div>

		<div class="bm-compose-row bm-compose-row-attachments">
			<span class="bm-compose-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"attachments"), $_smarty_tpl);?>
</span>
			<div class="bm-compose-field d-flex align-items-start gap-2" id="bmComposeAttachments">
				<input type="hidden" name="attachments" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('mail')['attachments'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('mail')['attachments'],'allowEmpty'=>true), $_smarty_tpl);
}?>" id="attachments" />
				<div class="flex-fill">
					<div id="attachmentList"></div>
				</div>
				<button class="btn btn-sm btn-outline-secondary flex-shrink-0" onclick="javascript:addAttachment('<?php echo $_smarty_tpl->getValue('sid');?>
')" type="button">
					<i class="ti ti-paperclip icon" aria-hidden="true"></i>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"add"), $_smarty_tpl);?>

				</button>
			</div>
		</div>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.compose.tpl:beforemailSendOptions"), $_smarty_tpl);?>

		<div class="bm-compose-options">
			<label class="form-check form-check-inline mb-0">
				<input class="form-check-input" type="checkbox" name="attachVCard" id="attachVCard"<?php if ($_smarty_tpl->getValue('mail')['attachVCard']) {?> checked="checked"<?php }?> />
				<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"attachvc"), $_smarty_tpl);?>
</span>
			</label>
			<label class="form-check form-check-inline mb-0">
				<input class="form-check-input" type="checkbox" name="certMail" id="certMail"<?php if ($_smarty_tpl->getValue('mail')['certMail']) {?> checked="checked"<?php }?> onchange="EBID('smimeEncrypt').disabled=this.checked;if(this.checked)EBID('smimeEncrypt').checked=false;" />
				<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"certmail"), $_smarty_tpl);?>
</span>
			</label>
			<label class="form-check form-check-inline mb-0">
				<input class="form-check-input" type="checkbox" name="mailConfirmation" id="mailConfirmation"<?php if ($_smarty_tpl->getValue('mail')['mailConfirmation']) {?> checked="checked"<?php }?> />
				<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mailconfirmation"), $_smarty_tpl);?>
</span>
			</label>
			<?php if ($_smarty_tpl->getValue('smime')) {?>
			<label class="form-check form-check-inline mb-0">
				<input class="form-check-input" type="checkbox" name="smimeSign" id="smimeSign"<?php if ($_smarty_tpl->getValue('mail')['smimeSign']) {?> checked="checked"<?php }?> />
				<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sign"), $_smarty_tpl);?>
</span>
			</label>
			<label class="form-check form-check-inline mb-0">
				<input class="form-check-input" type="checkbox" name="smimeEncrypt" id="smimeEncrypt"<?php if ($_smarty_tpl->getValue('mail')['smimeEncrypt']) {?> checked="checked"<?php }?> onchange="EBID('certMail').disabled=this.checked;if(this.checked)EBID('certMail').checked=false;" />
				<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"encrypt"), $_smarty_tpl);?>
</span>
			</label>
			<?php }?>
		</div>
	</div>

	<div id="composeText" class="bm-compose-editor">
		<textarea class="composeTextarea<?php if ($_smarty_tpl->getValue('lineSep')) {?> lineSep<?php }?>" name="emailText" id="emailText" style="width:100%;height:100%;<?php if ($_smarty_tpl->getValue('useCourier')) {?>font-family:courier;<?php }?>"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('allowEmpty'=>true,'value'=>$_smarty_tpl->getValue('mail')['text']), $_smarty_tpl);?>
</textarea>
		<?php if (!$_smarty_tpl->getValue('mail') || $_smarty_tpl->getValue('mail')['textMode'] == 'text') {?>
		<input type="hidden" name="textMode" value="text" />
		<?php } else { ?>
		<input type="hidden" name="textMode" value="html" />
		<?php }?>
		<?php echo '<script'; ?>
 src="./clientlib/wysiwyg.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"../../clientlib/wysiwyg.js"), $_smarty_tpl);?>
"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 type="text/javascript" src="./clientlib/ckeditor/ckeditor.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"../../clientlib/ckeditor/ckeditor.js"), $_smarty_tpl);?>
"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
>
		<!--
			var autoSaveDrafts = <?php if ($_smarty_tpl->getValue('autoSaveDrafts')) {?>true<?php } else { ?>false<?php }?>;
			var autoSaveDraftsInterval = <?php if ($_smarty_tpl->getValue('autoSaveDraftsInterval')) {
echo $_smarty_tpl->getValue('autoSaveDraftsInterval');
} else { ?>0<?php }?>;

			var editor = new htmlEditor('emailText', '<?php echo $_smarty_tpl->getValue('tpldir');?>
/images/editor/');
			editor.ckEditorPrefs.uiColor = bmGetCkEditorUiColor();
			editor.ckEditorPrefs.contentsCss = [
				'./clientlib/ckeditor/contents.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"../../clientlib/ckeditor/contents.css"), $_smarty_tpl);?>
',
				'<?php echo $_smarty_tpl->getValue('tpldir');?>
style/ckeditor-tabler-contents.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"style/ckeditor-tabler-contents.css"), $_smarty_tpl);?>
'
			];
			editor.modeField = 'textMode';
			editor.onReady = function()
			{
				editor.start();
				editor.switchMode("<?php if (!$_smarty_tpl->getValue('mail') || $_smarty_tpl->getValue('mail')['textMode'] == 'text') {?>text<?php } else { ?>html<?php }?>", true);
			}
			<?php if ($_smarty_tpl->getValue('autoSaveDrafts')) {?>editor.onChange = beginDraftAutoSave;<?php }?>
			editor.init();
		//-->
		<?php echo '</script'; ?>
>
	</div>
</div>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.compose.tpl:foot"), $_smarty_tpl);?>


<?php if ((true && ($_smarty_tpl->hasVariable('captchaInfo') && null !== ($_smarty_tpl->getValue('captchaInfo') ?? null)))) {?>
<div id="safecodeFooter" class="bm-compose-captcha"<?php if ($_smarty_tpl->getValue('captchaInfo')['heightHint']) {?> style="min-height:<?php echo $_smarty_tpl->getValue('captchaInfo')['heightHint'];?>
;"<?php }?>>
	<div class="d-flex flex-wrap align-items-center justify-content-center gap-3">
		<label class="form-label mb-0" for="safecode"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"safecode"), $_smarty_tpl);?>
</label>
		<div id="captchaContainer"><?php echo $_smarty_tpl->getValue('captchaHTML');?>
</div>
		<?php if (!$_smarty_tpl->getValue('captchaInfo')['hasOwnInput']) {?>
		<input type="text" class="form-control form-control-sm bm-compose-safecode-input" name="safecode" id="safecode" />
		<?php }?>
		<?php if ($_smarty_tpl->getValue('captchaInfo')['showNotReadable']) {?><small class="text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notreadable"), $_smarty_tpl);?>
</small><?php }?>
	</div>
</div>
<?php }?>

<div id="contentFooter" class="bm-compose-footer">
	<div class="left d-flex flex-wrap align-items-center gap-2">
		<div class="bm-compose-save-row d-inline-flex align-items-center gap-2">
			<label class="form-label mb-0 text-secondary text-nowrap" for="savecopy"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"savecopy"), $_smarty_tpl);?>
</label>
			<select name="savecopy" id="savecopy" class="form-select form-select-sm bm-compose-savecopy">
				<option value="-128">-</option>
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('dropdownFolderList'), 'dFolderTitle', false, 'dFolderID');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('dFolderID')->value => $_smarty_tpl->getVariable('dFolderTitle')->value) {
$foreach1DoElse = false;
?>
				<option value="<?php echo $_smarty_tpl->getValue('dFolderID');?>
"<?php if ((!$_smarty_tpl->getValue('composeDefaults')['savecopy'] && $_smarty_tpl->getValue('composeDefaults')['savecopy'] !== '0' && $_smarty_tpl->getValue('dFolderID') == -2) || $_smarty_tpl->getValue('composeDefaults')['savecopy'] == $_smarty_tpl->getValue('dFolderID')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getValue('dFolderTitle');?>
</option>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
			</select>
		</div>

		<?php if ($_smarty_tpl->getValue('signatures')) {?>
		<select name="signature" id="signature" class="form-select form-select-sm bm-compose-signature">
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('signatures'), 'signature');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('signature')->value) {
$foreach2DoElse = false;
?>
			<option value="<?php echo $_smarty_tpl->getValue('signature')['id'];?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('signature')['titel'],'cut'=>15), $_smarty_tpl);?>
</option>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</select>
		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="placeSignature(EBID('signature').value)" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signature"), $_smarty_tpl);?>
">
			<i class="ti ti-writing icon" aria-hidden="true"></i>
		</button>
		<?php }?>
	</div>
	<div class="center text-secondary small" id="autoSaveNote"></div>
	<div class="right d-flex flex-wrap align-items-center gap-2">
		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="EBID('do').value='saveDraft';editor.submit();document.forms.f1.submit();">
			<i class="ti ti-device-floppy icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"savedraft"), $_smarty_tpl);?>

		</button>
		<button class="btn btn-sm btn-primary" type="button" id="sendButton" onclick="if(!checkComposeForm(document.forms.f1, <?php if ($_smarty_tpl->getValue('attCheck')) {?>true<?php } else { ?>false<?php }?>, '<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"att_keywords"), $_smarty_tpl);?>
')) return(false); EBID('do').value='sendMail';editor.submit();checkSMIME('<?php if ((true && ($_smarty_tpl->hasVariable('captchaInfo') && null !== ($_smarty_tpl->getValue('captchaInfo') ?? null))) && !$_smarty_tpl->getValue('captchaInfo')['hasOwnAJAXCheck']) {?>checkSafeCode(\'<?php echo $_smarty_tpl->getValue('captchaInfo')['failAction'];?>
\',\'submitComposeForm();\');<?php } else { ?>submitComposeForm();<?php }?>');">
			<i class="ti ti-send icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sendmail2"), $_smarty_tpl);?>

		</button>
	</div>
</div>

</form>

<div id="composeLoading" class="bm-compose-loading" style="display:none"><i class="ti ti-loader-2 icon icon-lg bm-spin" aria-hidden="true"></i></div>

<?php echo '<script'; ?>
 src="./clientlib/dndupload.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"../../clientlib/dndupload.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>

<?php echo '<script'; ?>
>
<!--
	registerLoadAction(initComposeAutoComplete);
	registerLoadAction(generateAttachmentList);
	registerLoadAction(composeSizer);
	initDnDUpload(EBID('mainContent'), 'email.compose.php?action=uploadDnDAttachment&sid=' + currentSID, false, dndAttachmentUploaded, dndAttachmentURLAddition);
//-->
<?php echo '</script'; ?>
>
<?php }
}
