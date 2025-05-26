<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.faq.php?do=edit&id={$faq.id}&save=true&sid={$sid}" method="post" onsubmit="editor.submit();spin(this);">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="question"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="frage" value="{$faq.frage}" placeholder="{lng p="question"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="type"}</label>
			<div class="col-sm-10">
				<select name="typ" class="form-select">
					<option value="nli"{if $faq.typ=='nli'} selected="selected"{/if}>{lng p="nli"}</option>
					<option value="li"{if $faq.typ=='li'} selected="selected"{/if}>{lng p="li"}</option>
					<option value="both"{if $faq.typ=='both'} selected="selected"{/if}>{lng p="both"}</option>
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="language"}</label>
			<div class="col-sm-10">
				<select name="lang" class="form-select">
					<option value=":all:">{lng p="all"}</option>
					<optgroup label="{lng p="languages"}">
						{foreach from=$languages item=lang key=langID}
							<option value="{$langID}"{if $faq.lang==$langID} selected="selected"{/if}>{text value=$lang.title}</option>
						{/foreach}
					</optgroup>
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="requires"}</label>
			<div class="col-sm-10">
				<select name="required" class="form-select">
					<option value="">------------</option>
					<optgroup label="{lng p="services"}">
						{foreach from=$requirements item=req key=reqID}
							<option value="{$reqID}"{if $faq.required==$reqID} selected="selected"{/if}>{$req}</option>
						{/foreach}
					</optgroup>
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<textarea name="antwort" id="antwort" class="plainTextArea" style="width:100%;height:220px;">{$faq.antwort}</textarea>
				<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
				<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
				<script>
					<!--
					var editor = new htmlEditor('antwort');
					editor.init();
					registerLoadAction('editor.start()');
					//-->
				</script>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<select onchange="editor.insertText(this.value);" class="form-select">
					<option value="">-- {lng p="vars"} --</option>
					<option value="%%user%%">%%user%% ({lng p="email"})</option>
					<option value="%%wddomain%%">%%wddomain%% ({lng p="wddomain"})</option>
					<option value="%%selfurl%%">%%selfurl%% ({lng p="selfurl"})</option>
					<option value="%%hostname%%">%%hostname%% ({lng p="hostname"})</option>
				</select>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>