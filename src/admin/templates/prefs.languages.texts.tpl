<fieldset>
	<legend>{lng p="language"}</legend>

	<form action="prefs.languages.php?action=texts&sid={$sid}" method="post">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="language"}</label>
			<div class="col-sm-10">
				<div class="btn-group">
					<select name="lang" class="form-select">
						{foreach from=$languages key=langID item=lang}
							<option value="{$langID}"{if $langID==$selectedLang} selected="selected"{/if}>{text value=$lang.title}</option>
						{/foreach}
					</select>
					<input type="submit" value="{lng p="ok"}" class="btn btn-dark-lt" />
				</div>
			</div>
		</div>
	</form>
</fieldset>

{if $selectedLang}
	<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
	<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>

	<script>
		<!--
		var editors = [];
		//-->
	</script>
	<fieldset>
		<legend>{lng p="customtexts"}</legend>

		<form action="prefs.languages.php?action=texts&lang={$selectedLang}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
			<div class="mb-3 text-end">
				<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
			</div>

			{foreach from=$texts item=text}
				<a name="{$text.key}" />
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{$text.title}<br /><small>{text value=$text.key}</small></label>
					<div class="col-sm-10">
						{if isset($customTextsHTML[$text.key])}<div style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">{/if}
							<textarea class="form-control" onfocus="this.style.height='240px';" onblur="this.style.height='100px';" style="height:{if isset($customTextsHTML[$text.key])}350{else}100{/if}px;" name="text-{$text.key}" id="text-{$text.key}">{text value=$text.text allowEmpty=true}</textarea>
							{if isset($customTextsHTML[$text.key])}
						</div>
						<script>
							<!--
							editors['{$text.key}'] = new htmlEditor('text-{$text.key}');
							editors['{$text.key}'].disableIntro = true;
							editors['{$text.key}'].init();
							registerLoadAction('editors[\'{$text.key}\'].start()');
							//-->
						</script>
						{/if}
					</div>
				</div>
			{/foreach}

			<div class="mb-3 text-end">
				<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
			</div>
		</form>
	</fieldset>
{/if}