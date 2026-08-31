<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=imap&action=apns&import=true"}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
	{csrffield}
	<div class="alert alert-warning" role="alert">
		<i class="ti ti-alert-triangle me-1"></i> {lng p="bms_apnslegacy"}
	</div>
<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="bms_pushcertificate"} <span class="badge bg-secondary-lt align-middle">{lng p="bms_legacy"}</span></legend>
	
	{if $certInfo}
	<table width="90%">
		<tr>
			<td align="left" rowspan="2" valign="top" width="40"><img src="{$tpldir}images/cert32.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="220">{lng p="bms_certuidcn"}:</td>
			<td class="td2">{text value=$certInfo.subject.UID}<br />
							{text value=$certInfo.subject.CN}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="validity"}:</td>
			<td class="td2">
				{if !$validCert}<font color="red">{/if}{lng p="from"} {date timestamp=$certInfo.validFrom_time_t dayonly=true}<br />
				{lng p="to"} {date timestamp=$certInfo.validTo_time_t dayonly=true}{if !$validCert}</font>{/if}
			</td>
		</tr>
	</table>
	{else}
		<center><i>({lng p="bms_nocertset"})</i></center>
	{/if}
</fieldset>

<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="bms_certimport"}</legend>
		
	<table width="90%">
		<tr>
			<td align="left" valign="top" width="40"><img src="{$tpldir}images/certadd32.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="220">{lng p="bms_certpk"}:</td>
			<td class="td2">
				<div style="float:left;">{lng p="bms_certificate"}:<br />
				<input type="file" name="cert_pem" style="width:280px;" /><br />
				{lng p="bms_privatekey"}:<br />
				<input type="file" name="cert_key" style="width:280px;" />
			</td>
		</tr>
	</table>
</fieldset>

<p>
	<div class="d-flex justify-content-between mt-3 mb-2">
		<button type="button" class="btn btn-outline-secondary" onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=imap"}';"><i class="ti ti-chevron-left me-1"></i> {lng p="back"}</button>
		<button type="submit" class="btn btn-primary"><i class="ti ti-upload me-1"></i> {lng p="import"}</button>
	</div>
</p>

</form>
