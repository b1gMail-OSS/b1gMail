<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="import"}</title>
    
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
	<script src="{$tpldir}js/organizer.js" type="text/javascript"></script>
</head>

<body>

	<fieldset>
		<legend>{lng p="import"}</legend>
		
		<table>
			<tr>
				<td><label for="importType">{lng p="type"}:</label></td>
				<td>
					<select name="importType" id="importType">
						<option value="csv">{lng p="csvfile"}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="importEncoding">{lng p="encoding"}:</label></td>
				<td>
					<select name="importEncoding" id="importEncoding">
						<option value="UTF-8">UTF-8</option>
						<option value="ASCII">ASCII</option>
						<option value="ISO-8859-15" selected="selected">ISO-8859-15</option>
						<option value="ISO-8859-2">ISO-8859-2</option>
						<option value="ISO-8859-3">ISO-8859-3</option>
						<option value="ISO-8859-4">ISO-8859-4</option>
						<option value="ISO-8859-5">ISO-8859-5</option>
						<option value="ISO-8859-6">ISO-8859-6</option>
						<option value="ISO-8859-7">ISO-8859-7</option>
						<option value="ISO-8859-8">ISO-8859-8</option>
						<option value="ISO-8859-9">ISO-8859-9</option>
						<option value="ISO-8859-10">ISO-8859-10</option>
						<option value="Windows-1252">Windows-1252</option>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>

	<p align="right">
		<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
		<input type="button" onclick="addrImportDialog('{$sid}');" value="{lng p="ok"}" />
	</p>
	
</body>

</html>
