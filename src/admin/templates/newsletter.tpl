<form action="newsletter.php?do=send&sid={$sid}" method="post" id="newsletterForm" onsubmit="{literal}if(newsletterMode=='export') { this.target='_blank'; } else { if(EBID('subject').value.length<3 && !confirm(window.lang['sendwosubject'])) return(false); this.target='';editor.submit();spin(this); }{/literal}">
<fieldset>
	<legend>{lng p="recipients"}</legend>
	
	<table width="100%">
		<tr>
			<td width="40" valign="top" rowspan="3"><img src="{$tpldir}images/filter.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="120">{lng p="status"}:</td>
			<td class="td2">
				<input type="checkbox" name="statusActive" id="statusActive" checked="checked" onclick="determineNewsletterRecipients()" />
					<label for="statusActive"><b>{lng p="active"}</b></label><br />
				<input type="checkbox" name="statusLocked" id="statusLocked" checked="checked" onclick="determineNewsletterRecipients()" />
					<label for="statusLocked"><b>{lng p="locked"}</b></label><br />
				<input type="checkbox" name="statusNotActivated" id="statusNotActivated" checked="checked" onclick="determineNewsletterRecipients()" />
					<label for="statusNotActivated"><b>{lng p="notactivated"}</b></label><br />
			</td>
			<td class="td1" width="120">{lng p="groups"}:</td>
			<td class="td2">
				{foreach from=$groups item=group key=groupID}
					<input type="checkbox" name="group_{$groupID}" id="group_{$groupID}"{if !$smarty.get.toGroup||$smarty.get.toGroup==$groupID} checked="checked"{/if} onclick="determineNewsletterRecipients()" />
						<label for="group_{$groupID}"><b>{text value=$group.title}</b></label><br />
				{/foreach}
			</td>
		</tr>
		<tr>
		</tr>
		<tr>
			<td class="td1">{lng p="countries"}:</td>
			<td class="td2">
				<div id="countrySelectBox" style="background-color:#FFF;border:1px solid #CCC;overflow-y:scroll;min-height:3em;max-height:80px;">
					{foreach from=$countries item=country key=countryID}
					<div style="padding:1px;">
						<input type="checkbox" name="countries[]" value="{$countryID}" id="country_{$countryID}" checked="checked" onchange="determineNewsletterRecipients()" />
						<label for="country_{$countryID}">{$country}</label>
					</div>
					{/foreach}
				</div>
			</td>
			<td class="td1">{lng p="sendto"}:</td>
			<td class="td2">
				<input type="radio" name="sendto" value="mailboxes" id="sendto_mailboxes" checked="checked" onclick="determineNewsletterRecipients()" />
				<label for="sendto_mailboxes"><b>{lng p="mailboxes"}</b></label><br />
				<input type="radio" name="sendto" value="altmails" id="sendto_altmails" onclick="determineNewsletterRecipients()" />
				<label for="sendto_altmails"><b>{lng p="altmails"}</b></label>
			</td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="email"}</legend>
	
	<table width="100%">
		<tr>
			<td width="40" valign="top" rowspan="7"><img src="{$tpldir}images/newsletter.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="120">{lng p="template"}:</td>
			<td class="td2">
				<select name="template" id="template" onchange="loadNewsletterTemplate(this)">
					<option value="0" selected="selected">-</option>
					{foreach from=$templates item=tplTitle key=tplID}
					<option value="{$tplID}">{text value=$tplTitle}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="td1">{lng p="mode"}:</td>
			<td class="td2">
				<input type="radio" name="mode" value="html" id="mode_html" checked="checked" onclick="if(this.checked) return editor.switchMode('html');" />
				<label for="mode_html"><b>{lng p="htmltext"}</b></label>
				
				<input type="radio" name="mode" value="text" id="mode_text" onclick="if(this.checked) return editor.switchMode('text');" />
				<label for="mode_text"><b>{lng p="plaintext"}</b></label>
			</td>
		</tr>
		<tr>
			<td class="td1">{lng p="from"}:</td>
			<td class="td2"><input type="text" id="from" name="from" value="{text value=$from}" size="42" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="subject"}:</td>
			<td class="td2"><input type="text" id="subject" name="subject" value="" size="42" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="priority"}:</td>
			<td class="td2"><select name="priority" id="priority">
							<option value="1">{lng p="prio_1"}</option>
							<option value="0" selected="selected">{lng p="prio_0"}</option>
							<option value="-1">{lng p="prio_-1"}</option>
						</select></td>
		</tr>
		<tr>
			<td colspan="2" style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">
				<textarea name="emailText" id="emailText" class="plainTextArea" style="width:100%;height:400px;"></textarea>
				<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
				<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
				<script>
				<!--
					var editor = new htmlEditor('emailText');
					editor.height = 400;
					editor.init();
					registerLoadAction('editor.start()');
				//-->
				</script>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<select class="smallInput" onchange="editor.insertText(this.value);">
					<option value="">-- {lng p="vars"} --</option>
					<option value="%%email%%">%%email%% ({lng p="email"})</option>
					<option value="%%greeting%%">%%greeting%% ({lng p="greeting"})</option>
					<option value="%%salutation%%">%%salutation%% ({lng p="salutation"})</option>
					<option value="%%firstname%%">%%firstname%% ({lng p="firstname"})</option>
					<option value="%%lastname%%">%%lastname%% ({lng p="lastname"})</option>
				</select>
			</td>
		</tr>
	</table>
</fieldset>

<p>
	<div style="float:left;" class="buttons">
		<img src="{$tpldir}images/user_active.png" border="0" alt="" width="16" height="16" align="absmiddle" />
		<span id="recpCount">0</span> {lng p="recpdetermined"}
		<input class="button" type="submit" value=" {lng p="export"} " id="exportButton" name="exportRecipients" disabled="disabled"
			onclick="newsletterMode='export';return true;" />
	</div>
	<div style="float:right;" class="buttons">
		{lng p="opsperpage"}:
		<input type="text" name="perpage" id="perpage" value="25" size="5" />
		<input class="button" type="submit" value=" {lng p="sendletter"} " id="submitButton" disabled="disabled"
			onclick="newsletterMode='send';return true;" />
	</div>
</p>
</form>

<script>
<!--
	var newsletterMode = 'export';
	registerLoadAction('determineNewsletterRecipients()');
//-->
</script>
