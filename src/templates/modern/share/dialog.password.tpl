<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Password</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="{$selfurl}{$_tpldir}images/li/webdisk_folder.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />

	<!-- font awesome -->
	<link href="{$selfurl}clientlib/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="{$selfurl}clientlib/fontawesome/css/font-awesome-animation.min.css" rel="stylesheet" type="text/css" />
	
	<!-- client scripts -->
	<script src="../clientlang.php" type="text/javascript"></script>
</head>

<body onload="document.getElementById('pw').focus()">

		<table width="100%" cellspacing="0">
			<tr>
				<td width="42" valign="top"><i class="fa fa-cloud-download fa-3x" aria-hidden="true"></i></td>
				<td>
					{lng p="protected_desc"}
					
					<form action="index.php?action=passwordSubmit&user={$user}&folder={$folder}" method="post">
						<p align="center">
							{lng p="password"}:
							<input type="password" name="pw" id="pw" size="26" />
						</p>
						
						<p align="right">
							<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
							<input type="submit" value="{lng p="ok"}" />
						</p>
					</form>
				</td>
			</tr>
		</table>
	
</body>

</html>
