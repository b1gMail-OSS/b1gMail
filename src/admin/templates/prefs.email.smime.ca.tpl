<form action="prefs.email.php?action=smime&do=editca&set=true&sid={$sid}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="info"}</legend>

		<div class="alert alert-info">{lng p="cert_ca_info"}</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="cert_ca_current"}</legend>

		{if $certInfo}
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="cert_ca"}</label>
				<div class="col-sm-10">
					<div class="form-control-plaintext" style="font-weight: bold;">{text value=$certInfo.subject.CN}</div>
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="validity"}</label>
				<div class="col-sm-10">
					<div class="form-control-plaintext" style="font-weight: bold;">
						{if !$validCert}<p class="text-red">{/if}{lng p="from"} {date timestamp=$certInfo.validFrom_time_t dayonly=true}<br />
							{lng p="to"} {date timestamp=$certInfo.validTo_time_t dayonly=true}{if !$validCert}</p>{/if}
					</div>
				</div>
			</div>
		{else}
			<div class="alert alert-muted">({lng p="cert_noca"})</div>
		{/if}
	</fieldset>

	<fieldset>
		<legend>{lng p="cert_ca_import"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="cert_ca_cert"}</label>
			<div class="col-sm-10">
				<div class="input-group">
					<span class="input-group-text">{lng p="cert_ca_file_pem"}</span>
					<input type="file" name="cert_ca_pem" class="form-control" />
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">&nbsp;</label>
			<div class="col-sm-10">
				<div class="input-group">
					<span class="input-group-text">{lng p="cert_ca_file_key"}</span>
					<input type="file" name="cert_ca_key" class="form-control" />
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="cert_ca_pass"}</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="cert_ca_pass" value="" placeholder="{lng p="cert_ca_pass"}" autocomplete="off">
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6"><input class="btn btn-muted" type="button" value="&laquo; {lng p="back"}" onclick="document.location.href='prefs.email.php?action=smime&sid={$sid}';" /></div>
		<div class="col-md-6 text-end"><input class="btn btn-primary" type="submit" value="{lng p="import"}" /></div>
	</div>
</form>
