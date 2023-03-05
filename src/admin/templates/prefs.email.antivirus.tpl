<form action="prefs.email.php?action=antivirus&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="clamintegration"}</legend>

		<div class="alert alert-warning">{lng p="clamwarning"}</div>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="enable"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="use_clamd"{if $bm_prefs.use_clamd=='yes'} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="host"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="clamd_host" value="{$bm_prefs.clamd_host}" placeholder="{lng p="host"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="port"}</label>
			<div class="col-sm-10">
				<input type="number" class="form-control" name="clamd_port" value="{$bm_prefs.clamd_port}" placeholder="{lng p="port"}">
			</div>
		</div>
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
