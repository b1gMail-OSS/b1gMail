<form action="{$pageURL}&sid={$sid}&action=msgqueue&do=headers&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="bms_ownheaders"}</legend>

		<div class="alert alert-warning">{lng p="bms_headersnote"}</div>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="bms_inbound"}</label>
			<div class="col-sm-10">
				<textarea name="inbound_headers" class="form-control">{text value=$bms_prefs.inbound_headers allowEmpty=true}</textarea>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="bms_outbound"}</label>
			<div class="col-sm-10">
				<textarea name="outbound_headers" class="form-control">{text value=$bms_prefs.outbound_headers allowEmpty=true}</textarea>
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6">
			<input class="btn btn-primary" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=msgqueue&sid={$sid}';" />
		</div>
		<div class="col-md-6 text-end">
			<input class="btn btn-primary" type="submit" value=" {lng p="save"} " />
		</div>
	</div>
</form>