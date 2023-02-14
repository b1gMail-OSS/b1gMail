<fieldset>
	<legend>{lng p="edittemplate"}</legend>

	<form name="f1" action="newsletter.php?action=templates&do=edit&templateID={$tpl.templateid}&save=true&sid={$sid}" method="post" onsubmit="editor.submit();spin(this);">

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="title"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="subject" name="title" value="{if isset($tpl.title)}{text value=$tpl.title allowEmpty=true}{/if}" placeholder="{lng p="title"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="mode"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="mode" value="html" id="mode_html"{if $tpl.mode=='html'} checked="checked"{/if} checked="checked" onclick="if(this.checked) return editor.switchMode('html');">
					<span class="form-check-label">{lng p="htmltext"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" name="mode" value="text" id="mode_text"{if $tpl.mode=='text'} checked="checked"{/if} onclick="if(this.checked) return editor.switchMode('text');">
					<span class="form-check-label">{lng p="plaintext"}</span>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="from"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="from" name="from" value="{if isset($tpl.from)}{text value=$tpl.from allowEmpty=true}{/if}" placeholder="{lng p="from"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="subject"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="subject" name="subject" value="{if isset($tpl.subject)}{text value=$tpl.subject allowEmpty=true}{/if}" placeholder="{lng p="subject"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="priority"}</label>
			<div class="col-sm-10">
				<select name="priority" id="priority" class="form-select">
					<option value="1"{if $tpl.priority==1} selected="selected"{/if}>{lng p="prio_1"}</option>
					<option value="0"{if $tpl.priority==0} selected="selected"{/if}>{lng p="prio_0"}</option>
					<option value="-1"{if $tpl.priority==-1} selected="selected"{/if}>{lng p="prio_-1"}</option>
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<textarea name="emailText" id="emailText" class="plainTextArea" style="width:100%;height:400px;">{text allowEmpty=true value=$tpl.body}</textarea>
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

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>