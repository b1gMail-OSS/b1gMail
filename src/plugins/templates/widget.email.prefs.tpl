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
	<script src="{sessionurl file='clientlang.php'}" type="text/javascript"></script>
	<script src="clientlib/overlay.js" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
</head>

<body>

		<form action="{$widgetPrefsURL}" method="post">
			{csrffield}
			<input type="hidden" name="save" value="true" />
			
			<fieldset>
				<legend>{lng p="prefs"}</legend>
				
				<label class="form-check mb-2">
					<input type="checkbox" class="form-check-input" name="hideSystemFolders" id="hideSystemFolders"{if $hideSystemFolders} checked="checked"{/if} />
					<span class="form-check-label">{lng p="hidesystemfolders"}</span>
				</label>
				
				<label class="form-check mb-2">
					<input type="checkbox" class="form-check-input" name="hideCustomFolders" id="hideCustomFolders"{if $hideCustomFolders} checked="checked"{/if} />
					<span class="form-check-label">{lng p="hidecustomfolders"}</span>
				</label>
				
				<label class="form-check mb-0">
					<input type="checkbox" class="form-check-input" name="hideIntelliFolders" id="hideIntelliFolders"{if $hideIntelliFolders} checked="checked"{/if} />
					<span class="form-check-label">{lng p="hideintellifolders"}</span>
				</label>
			</fieldset>
	
			<p align="right">
				<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
				<input type="submit" value="{lng p="ok"}" />
			</p>
		</form>
	
</body>

</html>
