<div class="mb-3 row">
	<div class="col-sm-12">
		<div id="modsigTextWrap"{if $modsigHtmlMode} class="border rounded bg-white"{/if}>
			<textarea name="text" id="modsigText" class="plainTextArea" style="width:100%;height:180px;{if !$modsigHtmlMode}font-family:courier;{/if}">{if isset($modsigTextValue)}{text value=$modsigTextValue allowEmpty=true}{/if}</textarea>
		</div>
	</div>
</div>
<div class="mb-3 row">
	<label class="col-sm-2 col-form-check-label">{lng p="modsig_html"}</label>
	<div class="col-sm-10">
		<label class="form-check">
			<input class="form-check-input" type="checkbox" name="html" id="modsigHtmlCb" value="1"{if $modsigHtmlMode} checked="checked"{/if} onchange="modsigHtmlCheckboxChanged();">
			<span class="form-check-label">{lng p="htmltext"}</span>
		</label>
	</div>
</div>

<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
<script src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
<script>
var modsigEditor = null;

function modsigStartHtmlEditor()
{
	var wrap = EBID('modsigTextWrap');
	if(wrap)
		wrap.className = 'border rounded bg-white';
	if(EBID('modsigText'))
		EBID('modsigText').style.fontFamily = '';

	if(!modsigEditor)
	{
		modsigEditor = new htmlEditor('modsigText');
		if(typeof bmGetCkEditorUiColor === 'function')
			modsigEditor.ckEditorPrefs.uiColor = bmGetCkEditorUiColor();
	}

	if(modsigEditor.mode === 'html' && modsigEditor.ckEditor)
		return;

	modsigEditor.init();
	registerLoadAction(function() { modsigEditor.start(); });
}

function modsigStopHtmlEditor()
{
	var wrap = EBID('modsigTextWrap');
	if(wrap)
		wrap.className = '';
	if(EBID('modsigText'))
		EBID('modsigText').style.fontFamily = 'courier';

	if(modsigEditor && modsigEditor.mode === 'html' && modsigEditor.ckEditor)
		modsigEditor.switchMode('text', false);
}

function modsigHtmlCheckboxChanged()
{
	var cb = EBID('modsigHtmlCb');
	if(!cb)
		return;

	if(cb.checked)
		modsigStartHtmlEditor();
	else
		modsigStopHtmlEditor();
}

function modsigFormSubmit(form)
{
	var cb = EBID('modsigHtmlCb');
	if(cb && cb.checked && modsigEditor && modsigEditor.mode === 'html')
		modsigEditor.submit();
	spin(form);
	return true;
}

{if $modsigHtmlMode}
modsigStartHtmlEditor();
{else}
registerLoadAction(function()
{
	if(EBID('modsigHtmlCb') && EBID('modsigHtmlCb').checked)
		modsigStartHtmlEditor();
});
{/if}
</script>
