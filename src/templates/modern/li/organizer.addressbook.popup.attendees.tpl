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
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
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
			<td width="60" valign="top"><input type="button" class="addrButton" value=" &raquo;&raquo; " onclick="addAttendee();" /></td>
			<td>
				<div class="addressDiv" style="height: 120px;" id="attendees"></div>
			</td>
		<tr>
	</table>
	<table width="100%">
		<tr>
			<td colspan="2" align="right">
				<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
				<input type="button" onclick="submitAttendeeDialog()" value="{lng p="ok"}" />
			</td>
		</tr>
	</table>
	<script>
	<!--
		registerLoadAction(initAttendeeDialog);
	
		var attAddr = [],
			Addr = [];
				
		{literal}function initAttendeeDialog()
		{
			{/literal}{foreach from=$addresses item=address}
			{$address.type}Addr.push(["{text noentities=true escape=true value=$address.id}",
										"{text noentities=true escape=true value=$address.firstname}",
									  	"{text noentities=true escape=true value=$address.lastname}"]);
			{/foreach}
			
			initAttendees(Addr, attAddr);
			{literal}
		}{/literal}
	//-->
	</script>
	
</body>

</html>
