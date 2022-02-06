<form name="f1" method="post" action="email.compose.php?action=sendMail&sid={$sid}" autocomplete="off" onreset="if(!askReset()) return(false);editor.reset();">

<div id="contentHeader">
	<div class="left">
		<i class="fa fa-envelope-o" aria-hidden="true"></i> {lng p="sendmail"}
	</div>
	
	<div class="right">
		<select name="newTextMode" id="textMode" onchange="return editor.switchMode(this.value)">
			<option value="text"{if !$mail || $mail.textMode=='text'} selected="selected"{/if}>{lng p="plaintext"}</option>
			<option value="html"{if $mail.textMode=='html'} selected="selected"{/if}>{lng p="htmltext"}</option>
		</select>
		
		&nbsp;
		
		<i class="fa fa-flag"></i> <select name="priority" id="priority">
			<option value="1"{if $mail.priority==1} selected="selected"{/if}>{lng p="prio_1"}</option>
			<option value="0"{if !$mail || $mail.priority==0} selected="selected"{/if}>{lng p="prio_0"}</option>
			<option value="-1"{if $mail.priority==-1} selected="selected"{/if}>{lng p="prio_-1"}</option>
		</select>
	</div>
</div>

<div class="bigForm withBottomBar" style="overflow-y:auto">
	<input type="hidden" name="actionToken" value="{$actionToken}" />
	<input type="hidden" name="do" id="do" value="" />
	<input type="hidden" name="reference" id="reference" value="{$reference}" />
	<input type="hidden" name="baseDraftID" id="baseDraftID" value="{if $mail.isAutoSavedDraft}{$mail.baseDraftID}{/if}" />
	
	{if $latestDraft}
	<div class="draftNote" id="draftNote">
		<div>
			{lng p="drafttext"}
			<ul>
				{if $latestDraft.subject}<li><label>{lng p="subject"}:</label> {text value=$latestDraft.subject cut=100}</li>{/if}
				{if $latestDraft.to}<li><label>{lng p="to"}:</label> {text value=$latestDraft.to cut=100}</li>{/if}
			</ul>
			<input type="button" class="primary" value=" {lng p="loaddraft"} " onclick="loadDraft({$latestDraft.id})" />
			<input type="button" value=" {lng p="nothanks"} " onclick="hideDraftNote(true,{$latestDraft.id})" />
			<label for="deleteDraft" style="color:#666;"><input type="checkbox" id="deleteDraft" /> {lng p="deletedraft"}</label>
		</div>
		<br class="clear" />
	</div>
	{/if}

	<div class="previewMailHeader" id="composeHeader">
		<table class="lightTable">
			<tr>
				<th width="120">* <label for="from">{lng p="from"}:</label></th>
				<td><select name="from" id="from" style="width:100%;">
					{foreach from=$possibleSenders key=senderID item=sender}
						<option value="{$senderID}"{if $senderID==$mail.from} selected="selected"{/if}>{email value=$sender}</option>
					{/foreach}
					</select></td>
				<td width="140">&nbsp;</td>
			</tr>
			<tr>
				<th>* <label for="to">{lng p="to"}:</label></th>
				<td><input type="text" name="to" id="to" value="{text allowEmpty=true value=$mail.to}" style="width:100%;" /></td>
				<td>
					<span id="addrDiv_to">
						<button onclick="javascript:openAddressbook('{$sid}','email')" type="button">
							<i class="fa fa-users"></i>
							{lng p="fromaddr"}
						</button>
					</span>
				</td>
			</tr>
			<tr>
				<th>
					<a href="javascript:advancedOptions('fields', 'right', 'bottom', '{$tpldir}');composeSizer(true);">{if !$mail.replyto && !$mail.bcc}<i class="fa fa-caret-right" id="advanced_fields_arrow" aria-hidden="true"></i>{else}<i class="fa fa-caret-down" id="advanced_fields_arrow" aria-hidden="true"></i>{/if}</a> &nbsp;
					<label for="cc">{lng p="cc"}:</label></th>
				<td><input type="text" name="cc" id="cc" value="{text allowEmpty=true value=$mail.cc}" style="width:100%;" /></td>
				<td>&nbsp;</td>
			</tr>
			
			<tbody id="advanced_fields_body" style="display:{if !$mail.replyto && !$mail.bcc}none{/if};">
			<tr>
				<th><label for="bcc">{lng p="bcc"}:</label></th>
				<td><input type="text" name="bcc" id="bcc" value="{text allowEmpty=true value=$mail.bcc}" style="width:100%;" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><label for="replyto">{lng p="replyto"}:</label></th>
				<td><input type="text" name="replyto" id="replyto" value="{text allowEmpty=true value=$mail.replyto}" style="width:100%;" /></td>
				<td>&nbsp;</td>
			</tr>
			</tbody>
			
			<tr>
				<th>* <label for="subject">{lng p="subject"}:</label></th>
				<td><input type="text" name="subject" id="subject" value="{text allowEmpty=true value=$mail.subject}" onchange="beginDraftAutoSave()" style="width:100%;" /></td>
				<td>&nbsp;</td>
			</tr>
			
			<tr>
				<th>{lng p="attachments"}:</th>
				<td>
					<input type="hidden" name="attachments" value="{text value=$mail.attachments allowEmpty=true}" id="attachments" />
					<div id="attachmentList"></div>
				</td>
				<td valign="top">
					<button onclick="javascript:addAttachment('{$sid}')" type="button">
						<i class="fa fa-plus-circle"></i>
						{lng p="add"}
					</button>
				</td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td class="mailSendOptions" colspan="2">
					<div><i class="fa fa-id-card-o" aria-hidden="true"></i><input type="checkbox" name="attachVCard" id="attachVCard"{if $mail.attachVCard} checked="checked"{/if} /><label for="attachVCard">{lng p="attachvc"}</label></div>
					
					<div><i class="fa fa-certificate" aria-hidden="true"></i><input type="checkbox" name="certMail" id="certMail"{if $mail.certMail} checked="checked"{/if} onchange="EBID('smimeEncrypt').disabled=this.checked;if(this.checked)EBID('smimeEncrypt').checked=false;" /><label for="certMail">{lng p="certmail"}</label></div>
					
					<div><i class="fa fa-bullhorn" aria-hidden="true"></i><input type="checkbox" name="mailConfirmation" id="mailConfirmation"{if $mail.mailConfirmation} checked="checked"{/if} /><label for="mailConfirmation">{lng p="mailconfirmation"}</label></div>
					
					{if $smime}
					<div><i class="fa fa-map-signs" aria-hidden="true"></i><input type="checkbox" name="smimeSign" id="smimeSign"{if $mail.smimeSign} checked="checked"{/if} /><label for="smimeSign">{lng p="sign"}</label></div>
					
					<div><i class="fa fa-key" aria-hidden="true"></i><input type="checkbox" name="smimeEncrypt" id="smimeEncrypt"{if $mail.smimeEncrypt} checked="checked"{/if} onchange="EBID('certMail').disabled=this.checked;if(this.checked)EBID('certMail').checked=false;" /><label for="smimeEncrypt">{lng p="encrypt"}</label></div>
					{/if}
				</td>
			</tr>
		</table>
	</div>

	<div id="composeText" style="width:100%;position:absolute;">
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

{if $captchaInfo}
<div id="safecodeFooter"{if $captchaInfo.heightHint} style="height:{$captchaInfo.heightHint};"{/if}>
	<table cellpadding="0" style="margin-left:auto;margin-right:auto;">
		<tr>
			<td>
				<label for="safecode">{lng p="safecode"}:</label>
			</td>
			<td style="padding-left:2em;" id="captchaContainer">{$captchaHTML}</td>
			{if !$captchaInfo.hasOwnInput}
			<td style="padding-left:2em;">
				<input type="text" size="20" style="text-align:center;width:212px;" name="safecode" id="safecode" />
			</td>
			{/if}
			{if $captchaInfo.showNotReadable}<td style="padding-left:2em;"><small>{lng p="notreadable"}</small></td>{/if}
		</tr>
	</table>
</div>
{/if}

<div id="contentFooter">
	<div class="left">
		<i class="fa fa-folder-o"></i> <label for="savecopy">{lng p="savecopy"}</label> <select name="savecopy" id="savecopy">
				<option value="-128">-</option>
			{foreach from=$dropdownFolderList key=dFolderID item=dFolderTitle}
				<option value="{$dFolderID}" style="font-family:courier;"{if (!$composeDefaults.savecopy&&$composeDefaults.savecopy!=='0'&&$dFolderID==-2)||$composeDefaults.savecopy==$dFolderID} selected="selected"{/if}>{$dFolderTitle}</option>
			{/foreach}
		</select>
		
		{if $signatures}
		&nbsp;
		
		<i class="fa fa-certificate" aria-hidden="true"></i> <select name="signature" id="signature">
		{foreach from=$signatures item=signature}
			<option value="{$signature.id}">{text value=$signature.titel cut=15}</option>
		{/foreach}
		</select> <button type="button" onclick="placeSignature(EBID('signature').value)">&raquo;</button>
		{/if}
	</div>
	<div class="center" style="line-height:2em;" id="autoSaveNote">
	</div>
	<div class="right">
		<button type="button" onclick="EBID('do').value='saveDraft';editor.submit();document.forms.f1.submit();" />
			<i class="fa fa-save"></i>
			{lng p="savedraft"}
		</button>
		<button class="primary" type="button" id="sendButton" onclick="if(!checkComposeForm(document.forms.f1, {if $attCheck}true{else}false{/if}, '{lng p="att_keywords"}')) return(false); EBID('do').value='sendMail';editor.submit();checkSMIME('{if $captchaInfo&&!$captchaInfo.hasOwnAJAXCheck}checkSafeCode(\'{$captchaInfo.failAction}\',\'submitComposeForm();\');{else}submitComposeForm();{/if}');">
			<i class="fa fa-send"></i>
			{lng p="sendmail2"}
		</button>
	</div>
</div>

</form>

<div id="composeLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>

<script src="./clientlib/dndupload.js?{fileDateSig file="../../clientlib/dndupload.js"}" type="text/javascript"></script>

<script>
<!--
	registerLoadAction(initComposeAutoComplete);
	registerLoadAction(generateAttachmentList);
	registerLoadAction(composeSizer);
	initDnDUpload(EBID('mainContent'), 'email.compose.php?action=uploadDnDAttachment&sid=' + currentSID, false, dndAttachmentUploaded, dndAttachmentURLAddition);
//-->
</script>
