<!DOCTYPE html>
<html>
<head>
	<title>{if $bookItem}{lng p="editaddressbook"}{else}{lng p="addaddressbook"}{/if}</title>
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	<link rel="shortcut icon" type="image/png" href="{$selfurl}res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	<script src="{sessionurl file='clientlang.php'}"></script>
	<script src="{$selfurl}clientlib/overlay.js"></script>
	<script src="{$tpldir}js/common.js"></script>
	<script src="{$tpldir}js/loggedin.js"></script>
	<script src="{$tpldir}js/dialog.js"></script>
	<script>
	function organizerCollectionCheck(f) {
		var t = f.elements['title'];
		if(!t || String(t.value).replace(/^\s+|\s+$/g, '').length === 0) { alert(lang['fillin']); return false; }
		return true;
	}
	</script>
</head>
<body>
	<form method="post" target="_top" action="{sessionurl file='organizer.addressbook.php' params="action=books&do={if $bookItem}save&id={$bookItem.id}{else}add{/if}"}" onsubmit="return organizerCollectionCheck(this);">
		{csrffield}
		<fieldset>
			<legend>{if $bookItem}{lng p="editaddressbook"}{else}{lng p="addaddressbook"}{/if}</legend>
			<table>
				<tr>
					<td><label for="title">{lng p="title"}:</label></td>
					<td><input type="text" name="title" id="title" value="{if isset($bookItem.title)}{text value=$bookItem.title allowEmpty=true}{/if}" size="34" style="width:100%;" /></td>
				</tr>
			</table>
		</fieldset>
		<p align="right">
			<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
			<input type="submit" value="{lng p="ok"}" />
		</p>
	</form>
</body>
</html>
