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
	<script type="text/javascript">
	<!--
		var tplDir = '{$tpldir}';
	//-->
	</script>
	<script src="clientlang.php" type="text/javascript" ></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
</head>

<body onload="documentLoader()">

	<table width="100%">
		<tr>
			<td colspan="2" height="127">
				<div class="addressDiv" style="height: 330px;" id="addresses"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
				<input type="button" onclick="submitNumberDialog()" value="{lng p="ok"}" />
			</td>
		</tr>
	</table>
	<script type="text/javascript">
	<!--
		registerLoadAction(initNumberDialog);
	
		var Addr = [];
				
		{literal}function initNumberDialog()
		{
			{/literal}{foreach from=$addresses item=address}
			Addr.push(["{text noentities=true escape=true value=$address.lastname}, {text noentities=true escape=true value=$address.firstname}",
										"{text noentities=true escape=true value=$address.fax}",
									  	"{text noentities=true escape=true value=$address.work_fax}"]);
			{/foreach}
			
			initNumbers(Addr);
			{literal}
		}{/literal}
	//-->
	</script>
	
</body>

</html>