<form action="prefs.payments.php?action=invoices&save=true&sid={$sid}" method="post" onsubmit="editor.submit();spin(this);" id="prefsForm">
	<fieldset>
		<legend>{lng p="invoices"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="sendrg"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="sendrg"{if $bm_prefs.sendrg=='yes'} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="rgnrfmt"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="rgnrfmt" value="{text allowEmpty=true value=$bm_prefs.rgnrfmt}" placeholder="{lng p="rgnrfmt"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="kdnrfmt"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="kdnrfmt" value="{text allowEmpty=true value=$bm_prefs.kdnrfmt}" placeholder="{lng p="kdnrfmt"}">
			</div>
		</div>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="rgtemplate"}</legend>

		<div class="mb-3 row">
			<div class="col-sm-12">
				<textarea name="rgtemplate" id="rgtemplate" class="plainTextArea" style="width:100%;height:500px;">{text value=$bm_prefs.rgtemplate allowEmpty=true}</textarea>
				<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
				<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
				<script>
					<!--
					var editor = new htmlEditor('rgtemplate');
					editor.height = 500;
					editor.disableIntro = true;
					editor.init();
					registerLoadAction('editor.start()');
					//-->
				</script>
			</div>
		</div>
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
