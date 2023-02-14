<form action="prefs.webdisk.php?action=limits&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="limits"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="limitedextensions"}</label>
			<div class="col-sm-10">
				<textarea class="form-control" name="forbidden_extensions" placeholder="{lng p="limitedextensions"}">{text value=$bm_prefs.forbidden_extensions allowEmpty=true}</textarea>
				<small>{lng p="sepby"}</small>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="limitedmimetypes"}</label>
			<div class="col-sm-10">
				<textarea class="form-control" name="forbidden_mimetypes" placeholder="{lng p="limitedmimetypes"}">{text value=$bm_prefs.forbidden_mimetypes allowEmpty=true}</textarea>
				<small>{lng p="sepby"}</small>
			</div>
		</div>
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
