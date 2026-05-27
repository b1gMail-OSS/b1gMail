<form name="f1" method="post" action="email.compose.php?action=sendMail&sid={$sid}" autocomplete="off" class="bm-compose-form" onreset="if(!askReset()) return(false);editor.reset();">

<div id="contentHeader" class="bm-compose-header">
	<div class="left">
		<span class="bm-compose-header-title">{lng p="sendmail"}</span>
	</div>
	<div class="right bm-compose-header-tools">
		<select name="newTextMode" id="textMode" class="form-select form-select-sm bm-compose-control" onchange="return editor.switchMode(this.value)">
			<option value="text"{if !$mail || $mail.textMode=='text'} selected="selected"{/if}>{lng p="plaintext"}</option>
			<option value="html"{if $mail.textMode=='html'} selected="selected"{/if}>{lng p="htmltext"}</option>
		</select>
		<select name="priority" id="priority" class="form-select form-select-sm bm-compose-control">
			<option value="1"{if $mail.priority==1} selected="selected"{/if}>{lng p="prio_1"}</option>
			<option value="0"{if !$mail || $mail.priority==0} selected="selected"{/if}>{lng p="prio_0"}</option>
			<option value="-1"{if $mail.priority==-1} selected="selected"{/if}>{lng p="prio_-1"}</option>
		</select>
	</div>
</div>

<div class="bigForm withBottomBar bm-compose-body">
	<input type="hidden" name="actionToken" value="{$actionToken}" />
	<input type="hidden" name="do" id="do" value="" />
	<input type="hidden" name="reference" id="reference" value="{$reference}" />
	<input type="hidden" name="baseDraftID" id="baseDraftID" value="{if array_key_exists('isAutoSavedDraft', $mail)}{$mail.baseDraftID}{/if}" />

	{if isset($latestDraft)}
	<div class="alert alert-info bm-compose-draft-note" id="draftNote" role="alert">
		<div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
			<div>
				<strong>{lng p="drafttext"}</strong>
				<ul class="mb-0 mt-1">
					{if $latestDraft.subject}<li><span class="text-secondary">{lng p="subject"}:</span> {text value=$latestDraft.subject cut=100}</li>{/if}
					{if $latestDraft.to}<li><span class="text-secondary">{lng p="to"}:</span> {text value=$latestDraft.to cut=100}</li>{/if}
				</ul>
			</div>
			<div class="d-flex flex-wrap align-items-center gap-2">
				<button type="button" class="btn btn-sm btn-primary" onclick="loadDraft({$latestDraft.id})">{lng p="loaddraft"}</button>
				<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="hideDraftNote(true,{$latestDraft.id})">{lng p="nothanks"}</button>
				<label class="form-check mb-0" for="deleteDraft">
					<input class="form-check-input" type="checkbox" id="deleteDraft" />
					<span class="form-check-label">{lng p="deletedraft"}</span>
				</label>
			</div>
		</div>
	</div>
	{/if}

	<div class="bm-compose-fields" id="composeHeader">
		<div class="bm-compose-row">
			<label class="bm-compose-label required" for="from">{lng p="from"}</label>
			<div class="bm-compose-field">
				<select name="from" id="from" class="form-select form-select-sm bm-compose-control">
					{foreach from=$possibleSenders key=senderID item=sender}
					<option value="{$senderID}"{if $senderID==$mail.from} selected="selected"{/if}>{email value=$sender}</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="bm-compose-row">
			<label class="bm-compose-label required" for="to">{lng p="to"}</label>
			<div class="bm-compose-field" id="addrDiv_to">
				<div class="input-group input-group-sm">
					<input type="text" class="form-control form-control-sm bm-compose-control" name="to" id="to" value="{if isset($mail.to)}{text allowEmpty=true value=$mail.to}{/if}" />
					<button type="button" class="btn btn-outline-secondary" onclick="openAddressbook('{$sid}','email')">
						<i class="ti ti-address-book icon" aria-hidden="true"></i>
						<span class="d-none d-md-inline">{lng p="fromaddr"}</span>
					</button>
				</div>
			</div>
		</div>

		<div class="bm-compose-row">
			<label class="bm-compose-label" for="cc">
				<a class="bm-compose-advanced-toggle" href="javascript:advancedOptions('fields', 'right', 'bottom', '{$tpldir}');composeSizer(true);">
					{if empty($mail.replyto) && empty($mail.bcc)}<i class="ti ti-chevron-right" id="advanced_fields_arrow" aria-hidden="true"></i>{else}<i class="ti ti-chevron-down" id="advanced_fields_arrow" aria-hidden="true"></i>{/if}
				</a>
				{lng p="cc"}
			</label>
			<div class="bm-compose-field">
				<input type="text" class="form-control form-control-sm bm-compose-control" name="cc" id="cc" value="{if isset($mail.cc)}{text allowEmpty=true value=$mail.cc}{/if}" />
			</div>
		</div>

		<div id="advanced_fields_body" style="display:{if empty($mail.replyto) && empty($mail.bcc)}none{/if};">
			<div class="bm-compose-row">
				<label class="bm-compose-label" for="bcc">{lng p="bcc"}</label>
				<div class="bm-compose-field">
					<input type="text" class="form-control form-control-sm bm-compose-control" name="bcc" id="bcc" value="{if isset($mail.bcc)}{text allowEmpty=true value=$mail.bcc}{/if}" />
				</div>
			</div>
			<div class="bm-compose-row">
				<label class="bm-compose-label" for="replyto">{lng p="replyto"}</label>
				<div class="bm-compose-field">
					<input type="text" class="form-control form-control-sm bm-compose-control" name="replyto" id="replyto" value="{if isset($mail.replyto)}{text allowEmpty=true value=$mail.replyto}{/if}" />
				</div>
			</div>
		</div>

		<div class="bm-compose-row">
			<label class="bm-compose-label required" for="subject">{lng p="subject"}</label>
			<div class="bm-compose-field">
				<input type="text" class="form-control form-control-sm bm-compose-control" name="subject" id="subject" value="{if isset($mail.subject)}{text allowEmpty=true value=$mail.subject}{/if}" onchange="beginDraftAutoSave()" />
			</div>
		</div>

		<div class="bm-compose-row bm-compose-row-attachments">
			<span class="bm-compose-label">{lng p="attachments"}</span>
			<div class="bm-compose-field d-flex align-items-start gap-2" id="bmComposeAttachments">
				<input type="hidden" name="attachments" value="{if isset($mail.attachments)}{text value=$mail.attachments allowEmpty=true}{/if}" id="attachments" />
				<div class="flex-fill">
					<div id="attachmentList"></div>
				</div>
				<button class="btn btn-sm btn-outline-secondary flex-shrink-0" onclick="javascript:addAttachment('{$sid}')" type="button">
					<i class="ti ti-paperclip icon" aria-hidden="true"></i>
					{lng p="add"}
				</button>
			</div>
		</div>

		{hook id="email.compose.tpl:beforemailSendOptions"}
		<div class="bm-compose-options">
			<label class="form-check form-check-inline mb-0">
				<input class="form-check-input" type="checkbox" name="attachVCard" id="attachVCard"{if $mail.attachVCard} checked="checked"{/if} />
				<span class="form-check-label">{lng p="attachvc"}</span>
			</label>
			<label class="form-check form-check-inline mb-0">
				<input class="form-check-input" type="checkbox" name="certMail" id="certMail"{if $mail.certMail} checked="checked"{/if} onchange="EBID('smimeEncrypt').disabled=this.checked;if(this.checked)EBID('smimeEncrypt').checked=false;" />
				<span class="form-check-label">{lng p="certmail"}</span>
			</label>
			<label class="form-check form-check-inline mb-0">
				<input class="form-check-input" type="checkbox" name="mailConfirmation" id="mailConfirmation"{if $mail.mailConfirmation} checked="checked"{/if} />
				<span class="form-check-label">{lng p="mailconfirmation"}</span>
			</label>
			{if $smime}
			<label class="form-check form-check-inline mb-0">
				<input class="form-check-input" type="checkbox" name="smimeSign" id="smimeSign"{if $mail.smimeSign} checked="checked"{/if} />
				<span class="form-check-label">{lng p="sign"}</span>
			</label>
			<label class="form-check form-check-inline mb-0">
				<input class="form-check-input" type="checkbox" name="smimeEncrypt" id="smimeEncrypt"{if $mail.smimeEncrypt} checked="checked"{/if} onchange="EBID('certMail').disabled=this.checked;if(this.checked)EBID('certMail').checked=false;" />
				<span class="form-check-label">{lng p="encrypt"}</span>
			</label>
			{/if}
		</div>
	</div>

	<div id="composeText" class="bm-compose-editor">
		<textarea class="composeTextarea{if $lineSep} lineSep{/if}" name="emailText" id="emailText" style="width:100%;height:100%;{if $useCourier}font-family:courier;{/if}">{text allowEmpty=true value=$mail.text}</textarea>
		{if !$mail || $mail.textMode=='text'}
		<input type="hidden" name="textMode" value="text" />
		{else}
		<input type="hidden" name="textMode" value="html" />
		{/if}
		<script src="./clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
		<script type="text/javascript" src="./clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
		<script>
		<!--
			var autoSaveDrafts = {if $autoSaveDrafts}true{else}false{/if};
			var autoSaveDraftsInterval = {if $autoSaveDraftsInterval}{$autoSaveDraftsInterval}{else}0{/if};

			var editor = new htmlEditor('emailText', '{$tpldir}/images/editor/');
			editor.ckEditorPrefs.uiColor = bmGetCkEditorUiColor();
			editor.ckEditorPrefs.contentsCss = [
				'./clientlib/ckeditor/contents.css?{fileDateSig file="../../clientlib/ckeditor/contents.css"}',
				'{$tpldir}style/ckeditor-tabler-contents.css?{fileDateSig file="style/ckeditor-tabler-contents.css"}'
			];
			editor.modeField = 'textMode';
			editor.onReady = function()
			{literal}{{/literal}
				editor.start();
				editor.switchMode("{if !$mail||$mail.textMode=='text'}text{else}html{/if}", true);
			{literal}}{/literal}
			{if $autoSaveDrafts}editor.onChange = beginDraftAutoSave;{/if}
			editor.init();
		//-->
		</script>
	</div>
</div>

{hook id="email.compose.tpl:foot"}

{if isset($captchaInfo)}
<div id="safecodeFooter" class="bm-compose-captcha"{if $captchaInfo.heightHint} style="min-height:{$captchaInfo.heightHint};"{/if}>
	<div class="d-flex flex-wrap align-items-center justify-content-center gap-3">
		<label class="form-label mb-0" for="safecode">{lng p="safecode"}</label>
		<div id="captchaContainer">{$captchaHTML}</div>
		{if !$captchaInfo.hasOwnInput}
		<input type="text" class="form-control form-control-sm bm-compose-safecode-input" name="safecode" id="safecode" />
		{/if}
		{if $captchaInfo.showNotReadable}<small class="text-secondary">{lng p="notreadable"}</small>{/if}
	</div>
</div>
{/if}

<div id="contentFooter" class="bm-compose-footer">
	<div class="left d-flex flex-wrap align-items-center gap-2">
		<div class="bm-compose-save-row d-inline-flex align-items-center gap-2">
			<label class="form-label mb-0 text-secondary text-nowrap" for="savecopy">{lng p="savecopy"}</label>
			<select name="savecopy" id="savecopy" class="form-select form-select-sm bm-compose-savecopy">
				<option value="-128">-</option>
				{foreach from=$dropdownFolderList key=dFolderID item=dFolderTitle}
				<option value="{$dFolderID}"{if (!$composeDefaults.savecopy&&$composeDefaults.savecopy!=='0'&&$dFolderID==-2)||$composeDefaults.savecopy==$dFolderID} selected="selected"{/if}>{$dFolderTitle}</option>
				{/foreach}
			</select>
		</div>

		{if $signatures}
		<select name="signature" id="signature" class="form-select form-select-sm bm-compose-signature">
			{foreach from=$signatures item=signature}
			<option value="{$signature.id}">{text value=$signature.titel cut=15}</option>
			{/foreach}
		</select>
		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="placeSignature(EBID('signature').value)" title="{lng p="signature"}">
			<i class="ti ti-writing icon" aria-hidden="true"></i>
		</button>
		{/if}
	</div>
	<div class="center text-secondary small" id="autoSaveNote"></div>
	<div class="right d-flex flex-wrap align-items-center gap-2">
		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="EBID('do').value='saveDraft';editor.submit();document.forms.f1.submit();">
			<i class="ti ti-device-floppy icon" aria-hidden="true"></i>
			{lng p="savedraft"}
		</button>
		<button class="btn btn-sm btn-primary" type="button" id="sendButton" onclick="if(!checkComposeForm(document.forms.f1, {if $attCheck}true{else}false{/if}, '{lng p="att_keywords"}')) return(false); EBID('do').value='sendMail';editor.submit();checkSMIME('{if isset($captchaInfo)&&!$captchaInfo.hasOwnAJAXCheck}checkSafeCode(\'{$captchaInfo.failAction}\',\'submitComposeForm();\');{else}submitComposeForm();{/if}');">
			<i class="ti ti-send icon" aria-hidden="true"></i>
			{lng p="sendmail2"}
		</button>
	</div>
</div>

</form>

<div id="composeLoading" class="bm-compose-loading" style="display:none"><i class="ti ti-loader-2 icon icon-lg bm-spin" aria-hidden="true"></i></div>

<script src="./clientlib/dndupload.js?{fileDateSig file="../../clientlib/dndupload.js"}" type="text/javascript"></script>

<script>
<!--
	registerLoadAction(initComposeAutoComplete);
	registerLoadAction(generateAttachmentList);
	registerLoadAction(composeSizer);
	initDnDUpload(EBID('mainContent'), 'email.compose.php?action=uploadDnDAttachment&sid=' + currentSID, false, dndAttachmentUploaded, dndAttachmentURLAddition);
//-->
</script>
