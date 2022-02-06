<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="addressbook"}</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
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
	<script src="{$tpldir}js/common.js?{fileDateSig file="js/common.js"}" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js?{fileDateSig file="js/loggedin.js"}" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js?{fileDateSig file="js/dialog.js"}" type="text/javascript"></script>
</head>

<body onload="documentLoader()">

	<table width="100%">
		<tr>
			<td colspan="2" height="127">
				<div class="addressDiv" style="height: 120px;" id="addresses"></div>
			</td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td width="60" valign="top"><input type="button" class="addrButton" value=" &raquo; {lng p="to"} " onclick="addAddr('to');" /></td>
			<td>
				<div class="addressDiv" style="height: 63px;" id="to"></div>
			</td>
		</tr>
		<tr>
			<td valign="top"><input type="button" class="addrButton" value=" &raquo; CC " onclick="addAddr('cc');" /></td>
			<td>
				<div class="addressDiv" style="height: 63px;" id="cc"></div>
			</td>
		</tr>
		<tr>
			<td valign="top"><input type="button" class="addrButton" value=" &raquo; BCC " onclick="addAddr('bcc');" /></td>
			<td>
				<div class="addressDiv" style="height: 63px;" id="bcc"></div>
			</td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td colspan="2" align="right">
				<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
				<input type="button" onclick="submitAddressDialog('{$mode}')" value="{lng p="ok"}" />
			</td>
		</tr>
	</table>
	<script>
	<!--
		registerLoadAction(initAddressDialog);
	
		var toAddr = [],
			ccAddr = [],
			bccAddr = [],
			Addr = [];
		
		{literal}function initAddressDialog()
		{
			{/literal}{foreach from=$addresses item=address}
			{if ($mode=='handy'&&$address.handy) || ($mode!='handy'&&($address.email1||$address.email2))}
			{$address.type}Addr.push(["{text noentities=true escape=true value=$address.name}",
										"{text noentities=true escape=true value=$address.email1}",
									  	"{text noentities=true escape=true value=$address.email2}",
									  	"{text noentities=true escape=true value=$address.handy}"]);
			{/if}
			{/foreach}
			
			{if $mode!='handy'}
			initEMailAddresses(Addr, toAddr, ccAddr, bccAddr);
			{else}
			initMobileAddresses(Addr, toAddr);
			{/if}
			{literal}
		}{/literal}
	//-->
	</script>
	
</body>

</html>
