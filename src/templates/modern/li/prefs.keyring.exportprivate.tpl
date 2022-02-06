<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{$title}</title>
    
	<!-- meta -->
	{*<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" /> eclipse sucks *}
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	
	<!-- client scripts -->
	<script src="clientlang.php?sid={$sid}" type="text/javascript"></script>
	<script src="clientlib/overlay.js" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
</head>

<body>

		{lng p="exportprivcerttext"}
		
		<form action="prefs.php?action=keyring&do=downloadPrivateCertificate&hash={text value=$hash}&sid={$sid}" method="post" autocomplete="off">
			<br /><br />
			<table width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td>* {lng p="password"}:</td>
					<td>&nbsp;</td>
					<td><input type="password" name="pw1" value="" size="28" style="width:86%;" /></td>
				</tr>
				<tr>
					<td>* {lng p="repeat"}:</td>
					<td>&nbsp;</td>
					<td><input type="password" name="pw2" value="" size="28" style="width:86%;" /></td>
				</tr>
			</table>
			
			<p align="right">
				<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
				<input type="submit" value="{lng p="ok"}" />
			</p>
		</form>
	
</body>

</html>
