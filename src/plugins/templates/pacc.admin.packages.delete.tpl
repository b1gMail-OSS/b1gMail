<form action="{$pageURL}&action=packages&delete={$id}&sid={$sid}" method="post">
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
	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="delete"}" />
	</div>
</fieldset>
</form>