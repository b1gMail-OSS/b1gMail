<div class="bm-folder-admin">
	<div id="contentHeader" class="contentHeader bm-folder-admin-header">
		<div class="left">
			<i class="ti ti-folders icon icon-sm" aria-hidden="true"></i>
			{if isset($folder)}{lng p="editfolder"}{else}{lng p="addfolder"}{/if}
		</div>
	</div>

	<form name="f1" method="post" action="{sessionurl file='email.folders.php' params="{if isset($folder)}action=saveFolder&id={$folder.id}{else}action=createFolder{/if}"}" class="card bm-folder-admin-card bm-folder-edit-form" onsubmit="{if isset($folder) && $folder.intelligent==1}if(!formSubmitOK) {literal}{ parent.frames.condition_frame.document.forms.saveForm.elements.submitParent.value='1';parent.frames.condition_frame.document.forms.saveForm.submit();return(false); }{/literal}{/if}return(checkFolderForm(this));">
		{csrffield}
		<div class="card-body bm-folder-edit-body">
			<div class="bm-folder-edit-fields">
				<div class="bm-folder-edit-row">
					<label class="bm-folder-edit-label required" for="titel">{lng p="title"}</label>
					<div class="bm-folder-edit-field">
						<input type="text" class="form-control form-control-sm" name="titel" id="titel" value="{text value=$folder.titel|default:'' allowEmpty=true}" />
					</div>
				</div>

				<div class="bm-folder-edit-row">
					<label class="bm-folder-edit-label" for="parentfolder">{lng p="parentfolder"}</label>
					<div class="bm-folder-edit-field">
						<select class="form-select form-select-sm" name="parentfolder" id="parentfolder">
							<option value="-1">------------</option>
						{foreach from=$dropdownFolderList key=dFolderID item=dFolderTitle}{if $dFolderID>0&&(!isset($folder)||$dFolderID!=$folder.id)}
							<option value="{$dFolderID}" style="font-family:courier;"{if isset($folder) && $folder.parent==$dFolderID} selected="selected"{/if}>{$dFolderTitle}</option>
						{/if}{/foreach}
						</select>
					</div>
				</div>

				<div class="bm-folder-edit-row">
					<label class="bm-folder-edit-label" for="storetime">{lng p="storetime"}</label>
					<div class="bm-folder-edit-field">
						<select class="form-select form-select-sm" name="storetime" id="storetime"{if isset($folder) && $folder.intelligent==1} disabled="disabled"{/if}>
							<option value="-1">------------</option>
							<option value="86400"{if isset($folder) && $folder.storetime==86400} selected="selected"{/if}>1 {lng p="days"}</option>
							<option value="172800"{if isset($folder) && $folder.storetime==172800} selected="selected"{/if}>2 {lng p="days"}</option>
							<option value="432000"{if isset($folder) && $folder.storetime==432000} selected="selected"{/if}>5 {lng p="days"}</option>
							<option value="604800"{if isset($folder) && $folder.storetime==604800} selected="selected"{/if}>7 {lng p="days"}</option>
							<option value="1209600"{if isset($folder) && $folder.storetime==1209600} selected="selected"{/if}>2 {lng p="weeks"}</option>
							<option value="2419200"{if isset($folder) && $folder.storetime==2419200} selected="selected"{/if}>4 {lng p="weeks"}</option>
							<option value="4838400"{if isset($folder) && $folder.storetime==4838400} selected="selected"{/if}>2 {lng p="months"}</option>
						</select>
					</div>
				</div>

				<div class="bm-folder-edit-row">
					<label class="bm-folder-edit-label" for="subscribed">{lng p="subscribed"}</label>
					<div class="bm-folder-edit-field">
						<label class="form-check mb-0">
							<input type="checkbox" class="form-check-input" id="subscribed" name="subscribed"{if !isset($folder) || $folder.subscribed==1} checked="checked"{/if} />
						</label>
					</div>
				</div>

				<div class="bm-folder-edit-row">
					<label class="bm-folder-edit-label" for="intelligent">{lng p="intelligent"}</label>
					<div class="bm-folder-edit-field">
						<label class="form-check mb-0">
							<input type="checkbox" class="form-check-input" id="intelligent" name="intelligent"{if isset($folder)} readonly="readonly" disabled="disabled"{if $folder.intelligent==1} checked="checked"{/if}{/if} />
						</label>
					</div>
				</div>

				{if isset($folder) && $folder.intelligent}
				<div class="bm-folder-edit-row bm-folder-edit-row-conditions">
					<label class="bm-folder-edit-label required">{lng p="conditions"}</label>
					<div class="bm-folder-edit-field">
						<iframe id="condition_frame" name="condition_frame" class="conditionIFrame bm-folder-condition-frame" width="100%" height="30" scrolling="no" frameborder="0" border="0" src="{sessionurl file='email.folders.php' params="action=editConditions&id={$folder.id}"}"></iframe>
						<div class="bm-folder-link-box linkBox">
							{lng p="requiredis"}
							<select class="form-select form-select-sm d-inline-block w-auto" name="intelligent_link">
								<option value="1"{if $folder.intelligent_link==1} selected="selected"{/if}>{lng p="ofevery"}</option>
								<option value="2"{if $folder.intelligent_link==2} selected="selected"{/if}>{lng p="ofatleastone"}</option>
							</select>
							{lng p="oftheseconditions"}
						</div>
					</div>
				</div>
				{/if}
			</div>
		</div>

		<div class="card-footer bm-folder-edit-footer">
			<button type="submit" class="btn btn-sm btn-primary">{lng p="ok"}</button>
			<button type="reset" class="btn btn-sm btn-outline-secondary">{lng p="reset"}</button>
		</div>
	</form>
</div>
