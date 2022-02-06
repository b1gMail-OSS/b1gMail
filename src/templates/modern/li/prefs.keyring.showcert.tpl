<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="certificate"}</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
	
	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	
	<!-- client scripts -->
	<script>
	<!--
		var tplDir = '{$tpldir}';
	//-->
	</script>
	<script src="clientlang.php" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
</head>

<body onload="documentLoader()">

	<h1><i class="fa fa-certificate" aria-hidden="true"></i>
		{text value=$certInfo.subject.CN}</h1>
	
	<div class="certContainer">
	<fieldset style="margin-bottom:12px;">
		<legend>{lng p="cert_subject"}</legend>
		
		<table>
			{if $certInfo.subject.C}<tr>
				<td><b>{lng p="country"}:</b></td>
				<td>{text value=$certInfo.subject.C}</td>
			</tr>{/if}
			{if $certInfo.subject.L}<tr>
				<td><b>{lng p="city"}:</b></td>
				<td>{text value=$certInfo.subject.L}</td>
			</tr>{/if}
			{if $certInfo.subject.ST}<tr>
				<td><b>{lng p="state"}:</b></td>
				<td>{text value=$certInfo.subject.ST}</td>
			</tr>{/if}
			{if $certInfo.subject.O}<tr>
				<td><b>{lng p="organization"}:</b></td>
				<td>{text value=$certInfo.subject.O}</td>
			</tr>{/if}
			{if $certInfo.subject.OU}<tr>
				<td><b>{lng p="organizationunit"}:</b></td>
				<td>{text value=$certInfo.subject.OU}</td>
			</tr>{/if}
			{if $certInfo.subject.CN}<tr>
				<td width="110"><b>{lng p="commonname"}:</b></td>
				<td>{text value=$certInfo.subject.CN}</td>
			</tr>{/if}
			{if $certInfo.subject.emailAddress}<tr>
				<td><b>{lng p="email"}:</b></td>
				<td>{text value=$certInfo.subject.emailAddress}</td>
			</tr>{/if}
		</table>
	</fieldset>
		
	<fieldset style="margin-bottom:12px;">
		<legend>{lng p="cert_issuer"}</legend>
		
		<table>
			{if $certInfo.issuer.C}<tr>
				<td width="110"><b>{lng p="country"}:</b></td>
				<td>{text value=$certInfo.issuer.C}</td>
			</tr>{/if}
			{if $certInfo.issuer.ST}<tr>
				<td width="110"><b>{lng p="state"}:</b></td>
				<td>{text value=$certInfo.issuer.ST}</td>
			</tr>{/if}
			{if $certInfo.issuer.L}<tr>
				<td><b>{lng p="city"}:</b></td>
				<td>{text value=$certInfo.issuer.L}</td>
			</tr>{/if}
			{if $certInfo.issuer.O}<tr>
				<td><b>{lng p="organization"}:</b></td>
				<td>{text value=$certInfo.issuer.O}</td>
			</tr>{/if}
			{if $certInfo.issuer.OU}<tr>
				<td><b>{lng p="organizationunit"}:</b></td>
				<td>{text value=$certInfo.issuer.OU}</td>
			</tr>{/if}
			{if $certInfo.issuer.CN}<tr>
				<td><b>{lng p="commonname"}:</b></td>
				<td>{text value=$certInfo.issuer.CN}</td>
			</tr>{/if}
			{if $certInfo.issuer.emailAddress}<tr>
				<td><b>{lng p="email"}:</b></td>
				<td>{text value=$certInfo.issuer.emailAddress}</td>
			</tr>{/if}
		</table>
	</fieldset>
	
	<fieldset style="margin-bottom:12px;">
		<legend>{lng p="common"}</legend>
		
		<table>
			<tr>
				<td><b>{lng p="validity"}:</b></td>
				<td>{date timestamp=$certInfo.validFrom_time_t dayonly=true}
					{lng p="until"}
					{date timestamp=$certInfo.validTo_time_t dayonly=true}</td>
			</tr>
			{if $certInfo.serialNumber}<tr>
				<td><b>{lng p="serial"}:</b></td>
				<td>{text value=$certInfo.serialNumber}</td>
			</tr>{/if}
			{if $certInfo.version}<tr>
				<td width="110"><b>{lng p="version"}:</b></td>
				<td>{text value=$certInfo.version}</td>
			</tr>{/if}
		</table>
	</fieldset>
	
	{if $publicKeyInfo}
	<fieldset style="margin-bottom:12px;">
		<legend>{lng p="publickey"}</legend>
		
		<table>
			<tr>
				<td width="110"><b>{lng p="type"}:</b></td>
				<td>{text value=$publicKeyInfo.typeText}</td>
			</tr>
			<tr>
				<td><b>{lng p="bits"}:</b></td>
				<td>{text value=$publicKeyInfo.bits}</td>
			</tr>
		</table>
	</fieldset>
	{/if}
	<br />
	</div>
	
	<div>
		<div style="float:left">
			<input type="button" value=" {lng p="download"} " onclick="document.location.href='prefs.php?action=keyring&do=downloadCertificate&hash={$certInfo.hash}&sid={$sid}';" />
		</div>
		<div style="float:right">
			<input type="button" value=" {lng p="close"} " onclick="parent.hideOverlay();" />
		</div>
	</div>
	
</body>

</html>
