{if $rgTemplateReset|default:false}
<div class="alert alert-success">{lng p="rgtemplate_reset_ok"}</div>
{elseif $rgTemplateResetError|default:false}
<div class="alert alert-danger">{lng p="rgtemplate_reset_err"}</div>
{/if}
<form action="{sessionurl file='prefs.payments.php' params="action=invoices&save=true"}" method="post" onsubmit="editor.submit();spin(this);" id="prefsForm">
	{csrffield}
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

		<div class="mb-3 d-flex justify-content-end">
			<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#rgTemplateResetModal">{lng p="rgtemplate_reset"}</button>
		</div>

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

<div class="modal modal-blur fade" id="rgTemplateResetModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<form action="{sessionurl file='prefs.payments.php' params='action=invoices'}" method="post">
			{csrffield}
			<input type="hidden" name="resetTemplate" value="1" />
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{lng p="rgtemplate_reset_title"}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{lng p="rgtemplate_reset_abort"}"></button>
				</div>
				<div class="modal-body">
					<p class="mb-2">{lng p="rgtemplate_reset_info"}</p>
					<ul class="mb-0">
						<li>{lng p="rgtemplate_reset_keepinv"}</li>
						<li>{lng p="rgtemplate_reset_sender"}</li>
						<li>{lng p="rgtemplate_reset_keepfmt"}</li>
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn" data-bs-dismiss="modal">{lng p="rgtemplate_reset_abort"}</button>
					<button type="submit" class="btn btn-danger">{lng p="rgtemplate_reset"}</button>
				</div>
			</div>
		</form>
	</div>
</div>
