<div class="bm-folder-admin">
	<div id="contentHeader" class="contentHeader bm-folder-admin-header">
		<div class="left">
			<i class="ti ti-folders icon icon-sm" aria-hidden="true"></i>
			{lng p="editfolder"}
		</div>
	</div>

	<form name="f1" method="post" action="email.folders.php?action=saveFolder&id={$folderID}&sid={$sid}" class="card bm-folder-admin-card bm-folder-edit-form">
		<div class="card-body bm-folder-edit-body">
			<div class="bm-folder-edit-fields">
				<div class="bm-folder-edit-row">
					<label class="bm-folder-edit-label required" for="titel">{lng p="title"}</label>
					<div class="bm-folder-edit-field">
						<input type="text" class="form-control form-control-sm" id="titel" value="{if isset($folderTitle)}{text value=$folderTitle allowEmpty=true}{/if}" disabled="disabled" />
					</div>
				</div>

				<div class="bm-folder-edit-row">
					<label class="bm-folder-edit-label" for="storetime">{lng p="storetime"}</label>
					<div class="bm-folder-edit-field">
						<select class="form-select form-select-sm" name="storetime" id="storetime">
							<option value="-1">------------</option>
							<option value="86400"{if $storeTime==86400} selected="selected"{/if}>1 {lng p="days"}</option>
							<option value="172800"{if $storeTime==172800} selected="selected"{/if}>2 {lng p="days"}</option>
							<option value="432000"{if $storeTime==432000} selected="selected"{/if}>5 {lng p="days"}</option>
							<option value="604800"{if $storeTime==604800} selected="selected"{/if}>7 {lng p="days"}</option>
							<option value="1209600"{if $storeTime==1209600} selected="selected"{/if}>2 {lng p="weeks"}</option>
							<option value="2419200"{if $storeTime==2419200} selected="selected"{/if}>4 {lng p="weeks"}</option>
							<option value="4838400"{if $storeTime==4838400} selected="selected"{/if}>2 {lng p="months"}</option>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="card-footer bm-folder-edit-footer">
			<button type="submit" class="btn btn-sm btn-primary">{lng p="ok"}</button>
			<button type="reset" class="btn btn-sm btn-outline-secondary">{lng p="reset"}</button>
		</div>
	</form>
</div>
