<form action="newsletter.php?do=send&sid={$sid}" method="post" id="newsletterForm" onsubmit="{literal}if(newsletterMode=='export') { this.target='_blank'; } else { if(EBID('subject').value.length<3 && !confirm(window.lang['sendwosubject'])) return(false); this.target='';editor.submit();spin(this); }{/literal}">
	<fieldset>
		<legend>{lng p="recipients"}</legend>

		<div class="row">
			<div class="col-md-6">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="status"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="statusActive" id="statusActive" checked="checked" onclick="determineNewsletterRecipients()">
							<span class="form-check-label">{lng p="active"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="statusLocked" id="statusLocked" checked="checked" onclick="determineNewsletterRecipients()">
							<span class="form-check-label">{lng p="locked"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="statusNotActivated" id="statusNotActivated" checked="checked" onclick="determineNewsletterRecipients()">
							<span class="form-check-label">{lng p="notactivated"}</span>
						</label>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="groups"}</label>
					<div class="col-sm-8">
						{foreach from=$groups item=group key=groupID}
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="group_{$groupID}" id="group_{$groupID}"{if !isset($smarty.get.toGroup) || !$smarty.get.toGroup || $smarty.get.toGroup==$groupID}checked="checked"{/if} onclick="determineNewsletterRecipients()">
								<span class="form-check-label">{text value=$group.title}</span>
							</label>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="countries"}</label>
					<div class="col-sm-8" style="background-color:#FFF;border:1px solid #CCC;overflow-y:scroll;min-height:3em;max-height:80px;">
						{foreach from=$countries item=country key=countryID}
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="countries[]" value="{$countryID}" id="country_{$countryID}" checked="checked" onchange="determineNewsletterRecipients()">
								<span class="form-check-label">{$country}</span>
							</label>
						{/foreach}
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="sendto"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="radio" name="sendto" value="mailboxes" id="sendto_mailboxes" checked="checked" onclick="determineNewsletterRecipients()">
							<span class="form-check-label">{lng p="mailboxes"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="radio" name="sendto" value="altmails" id="sendto_altmails" onclick="determineNewsletterRecipients()">
							<span class="form-check-label">{lng p="altmails"}</span>
						</label>
					</div>
				</div>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="email"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="template"}</label>
			<div class="col-sm-10">
				<select name="template" id="template" onchange="loadNewsletterTemplate(this)" class="form-select">
					<option value="0" selected="selected">-</option>
					{foreach from=$templates item=tplTitle key=tplID}
						<option value="{$tplID}">{text value=$tplTitle}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="mode"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="mode" value="html" id="mode_html" checked="checked" onclick="if(this.checked) return editor.switchMode('html');">
					<span class="form-check-label">{lng p="htmltext"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" name="mode" value="text" id="mode_text" onclick="if(this.checked) return editor.switchMode('text');">
					<span class="form-check-label">{lng p="plaintext"}</span>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="from"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="from" name="from" value="{if isset($from)}{text value=$from}{/if}" placeholder="{lng p="from"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="subject"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="subject" name="subject" value="" placeholder="{lng p="subject"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="priority"}</label>
			<div class="col-sm-10">
				<select name="priority" id="priority" class="form-select">
					<option value="1">{lng p="prio_1"}</option>
					<option value="0" selected="selected">{lng p="prio_0"}</option>
					<option value="-1">{lng p="prio_-1"}</option>
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<textarea name="emailText" id="emailText" class="plainTextArea"></textarea>
				<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
				<script src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
				<script>
					var editor = new htmlEditor('emailText');
					editor.height = 400;
					editor.init();
					registerLoadAction('editor.start()');
				</script>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<select class="form-select" onchange="editor.insertText(this.value);">
					<option value="">-- {lng p="vars"} --</option>
					<option value="%%email%%">%%email%% ({lng p="email"})</option>
					<option value="%%greeting%%">%%greeting%% ({lng p="greeting"})</option>
					<option value="%%salutation%%">%%salutation%% ({lng p="salutation"})</option>
					<option value="%%firstname%%">%%firstname%% ({lng p="firstname"})</option>
					<option value="%%lastname%%">%%lastname%% ({lng p="lastname"})</option>
				</select>
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6">
			<div style="float: left;"><span id="recpCount">0</span> {lng p="recpdetermined"}&nbsp; </div>
			<div style="float: left;"><input class="btn btn-sm" type="submit" value=" {lng p="export"} " id="exportButton" name="exportRecipients" disabled="disabled" onclick="newsletterMode='export';return true;" /></div>
		</div>
		<div class="col-md-6">
			<div style="float: right;"><input class="btn btn-sm btn-primary" type="submit" value=" {lng p="sendletter"} " id="submitButton" disabled="disabled" onclick="newsletterMode='send';return true;" /></div>
			<div style="float: right;"><input type="text" class="form-control form-control-sm" size="5" name="perpage" id="perpage" value="25" size="5" />&nbsp; </div>
			<div style="float: right;">{lng p="opsperpage"}&nbsp; </div>
		</div>
	</div>
</form>

<script>
	var newsletterMode = 'export';
	registerLoadAction('determineNewsletterRecipients()');
</script>