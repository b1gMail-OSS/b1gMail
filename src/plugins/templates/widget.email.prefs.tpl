<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="prefs"}</title>
    
	<!-- meta -->
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

		<form action="{$widgetPrefsURL}" method="post">
			<input type="hidden" name="save" value="true" />
			
			<fieldset>
				<legend>{lng p="prefs"}</legend>
				
				<input type="checkbox" name="hideSystemFolders" id="hideSystemFolders"{if $hideSystemFolders} checked="checked"{/if} />
				<label for="hideSystemFolders">{lng p="hidesystemfolders"}</label><br />
				
				<input type="checkbox" name="hideCustomFolders" id="hideCustomFolders"{if $hideCustomFolders} checked="checked"{/if} />
				<label for="hideCustomFolders">{lng p="hidecustomfolders"}</label><br />
				
				<input type="checkbox" name="hideIntelliFolders" id="hideIntelliFolders"{if $hideIntelliFolders} checked="checked"{/if} />
				<label for="hideIntelliFolders">{lng p="hideintellifolders"}</label>
			</fieldset>
	
			<p align="right">
				<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
				<input type="submit" value="{lng p="ok"}" />
			</p>
		</form>
	
</body>

</html>
