<form action="{$pageURL}&action=imap&do=apns&import=true&sid={$sid}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
<fieldset>
	<legend>{lng p="bms_pushcertificate"}</legend>
	
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

<fieldset>
	<legend>{lng p="bms_certimport"}</legend>
		
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
	<div style="float:left;" class="buttons">
		<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=imap&sid={$sid}';" />
	</div>
	
	<div style="float:right;" class="buttons">
		<input class="button" type="submit" value=" {lng p="import"} " />
	</div>
</p>

</form>
