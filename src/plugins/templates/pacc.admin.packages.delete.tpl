<form action="{sessionurl file='plugin.page.php' params="plugin={$paccPlugin}&do=packages&delete={$id}"}" method="post">
	{csrffield}
<fieldset>
	<legend>{lng p="pacc_deletepackage"}</legend>
	
	<div class="alert alert-warning">{lng p="pacc_deletepackagedesc"}</div>

	<div class="mb-3 row">
		<label class="col-sm-2 col-form-label">{text value=$packageTitle}</label>
		<div class="col-sm-10">
			<select name="subscriptionAction" class="form-select">
				<option value="continue">{lng p="pacc_delcontinue"}</option>
				<option value="delete">{lng p="pacc_delfallback"}</option>
			</select>
		</div>
	</div>
	<div class="d-flex justify-content-between">
		<a href="{sessionurl file='plugin.page.php' params="plugin={$paccPlugin}&do=packages"}" class="btn btn-outline-secondary">
			<i class="ti ti-arrow-left me-1"></i>
			{lng p="back"}
		</a>
		<button type="submit" class="btn btn-danger">
			<i class="ti ti-trash me-1"></i>
			{lng p="delete"}
		</button>
	</div>
</fieldset>
</form>
