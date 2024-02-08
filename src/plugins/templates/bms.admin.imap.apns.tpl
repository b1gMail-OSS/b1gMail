<form action="{$pageURL}&action=imap&do=apns&import=true&sid={$sid}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="bms_pushcertificate"}</legend>

		{if $certInfo}
			<div class="mb-3 row">
				<label class="col-sm-4 col-form-label">{lng p="bms_certuidcn"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">
						{text value=$certInfo.subject.UID}<br />
						{text value=$certInfo.subject.CN}
					</div>
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-4 col-form-label">{lng p="validity"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">
						{if !$validCert}<p class="text-red">
							{/if}{lng p="from"} {date timestamp=$certInfo.validFrom_time_t dayonly=true}<br />
							{lng p="to"} {date timestamp=$certInfo.validTo_time_t dayonly=true}
							{if !$validCert}</p>{/if}
					</div>
				</div>
			</div>
		{else}
			<div class="alert alert-warning">{lng p="bms_nocertset"}</div>
		{/if}
	</fieldset>

	<fieldset>
		<legend>{lng p="bms_certimport"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-4 col-form-label">{lng p="bms_certpk"}</label>
			<div class="col-sm-8">
				<div class="input-group mb-2">
					<span class="input-group-text">{lng p="bms_certificate"}</span>
					<input type="file" class="form-control" name="cert_pem" placeholder="{lng p="bms_certificate"}">
				</div>
				<div class="input-group mb-2">
					<span class="input-group-text">{lng p="bms_privatekey"}</span>
					<input type="file" class="form-control" name="cert_key" placeholder="{lng p="bms_privatekey"}">
				</div>
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6">
			<input class="btn btn-primary" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=imap&sid={$sid}';" />
		</div>
		<div class="col-md-6">
			<input class="btn btn-primary" type="submit" value=" {lng p="import"} " />
		</div>
	</div>
</form>