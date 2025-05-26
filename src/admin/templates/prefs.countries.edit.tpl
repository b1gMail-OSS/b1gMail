<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.countries.php?do=edit&id={$country.id}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="country"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="land" value="{$country.land}" placeholder="{lng p="country"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="eucountry"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="is_eu"{if $country.is_eu=='yes'} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="vatrate"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="cron_interval" value="{$country.vat}" min="0" max="100" step="any" placeholder="{lng p="vatrate"}">
					<span class="input-group-text">%</span>
				</div>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>