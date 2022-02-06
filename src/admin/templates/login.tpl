<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>b1gMail - {lng p="acp"}</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
	<!-- links -->
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link href="{$tpldir}style/common.css?{fileDateSig file="style/common.css"}" rel="stylesheet" type="text/css" />
	
	<!-- client scripts -->
	<script src="../clientlang.php?sid={$sid}" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js?{fileDateSig file="js/common.js"}" type="text/javascript"></script>
</head>

<body onload="EBID('username').focus();" id="loginBody">
	
	<form action="index.php?action=login" method="post" autocomplete="off">
		{if $jump}<input type="hidden" id="jump" name="jump" value="{text value=$jump allowEmpty=true}" />{/if}
		<input type="hidden" name="timezone" id="timezone" value="{$timezone}" />
		
		<div id="loginBox1">
			<div id="loginBox2">
				<div id="loginBox3">
					{if $error}<div class="loginError">{$error}</div>{/if}
				
					<div id="loginLogo">
						<img src="templates/images/logo_letter.png" style="width:90px;height:53px;" border="0" alt="" />
					</div>
					
					<div id="loginForm">
						{lng p="username"}:<br />
						<input id="username" type="text" name="username" value="" style="width:200px;" />
						<br /><br />
						
						{lng p="password"}:<br />
						<input id="pw" type="password" name="password" value="" style="width:200px;" />
						<br /><br />
						
						<div style="float:right;">
						<input class="button" type="submit" value=" {lng p="login"} &raquo; " />
						</div>
					</div>
					
					<br class="clear" />
				</div>
			</div>
		</div>
	</form>
	
	<script>
	<!--
		EBID('timezone').value = (new Date()).getTimezoneOffset() * (-60);
	//-->
	</script>

</body>

</html>
