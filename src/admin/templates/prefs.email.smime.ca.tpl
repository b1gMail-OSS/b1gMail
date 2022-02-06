<form action="prefs.email.php?action=smime&do=editca&set=true&sid={$sid}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
<fieldset>
	<legend>{lng p="info"}</legend>
	
	<table>
		<tr>
			<td width="36" valign="top"><img src="{$tpldir}images/info32.png" border="0" alt="" width="32" height="32" /></td>
			<td valign="top">{lng p="cert_ca_info"}</td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="cert_ca_current"}</legend>
	
	{if $certInfo}
	<table width="90%">
		<tr>
			<td align="left" rowspan="2" valign="top" width="40"><img src="{$tpldir}images/cert32.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="220">{lng p="cert_ca"}:</td>
			<td class="td2">{text value=$certInfo.subject.CN}</td>
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
		<center><i>({lng p="cert_noca"})</i></center>
	{/if}
</fieldset>

<fieldset>
	<legend>{lng p="cert_ca_import"}</legend>
		
	<table width="90%">
		<tr>
			<td align="left" rowspan="2" valign="top" width="40"><img src="{$tpldir}images/certadd32.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="220">{lng p="cert_ca_cert"}:</td>
			<td class="td2">
				<div style="float:left;">{lng p="cert_ca_file_pem"}:<br />
				<input type="file" name="cert_ca_pem" style="width:280px;" /><br />
				{lng p="cert_ca_file_key"}:<br />
				<input type="file" name="cert_ca_key" style="width:280px;" />
			</td>
		</tr>
		<tr>
			<td class="td1">{lng p="cert_ca_pass"}:</td>
			<td class="td2"><input type="password" name="cert_ca_pass" value="" size="36" autocomplete="off" /></td>
		</tr>
	</table>
</fieldset>

<p>
	<div style="float:left;" class="buttons">
		<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='prefs.email.php?action=smime&sid={$sid}';" />
	</div>
	
	<div style="float:right;" class="buttons">
		<input class="button" type="submit" value=" {lng p="import"} " />
	</div>
</p>

</form>
