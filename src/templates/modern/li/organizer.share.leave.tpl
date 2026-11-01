<!DOCTYPE html>
<html>
<head>
	<title>{lng p="shareleave"}</title>
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	<link rel="shortcut icon" type="image/png" href="{$selfurl}res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	<script src="{sessionurl file='clientlang.php'}"></script>
	<script src="{$selfurl}clientlib/overlay.js"></script>
	<script src="{$tpldir}js/common.js"></script>
	<script src="{$tpldir}js/loggedin.js"></script>
	<script src="{$tpldir}js/dialog.js"></script>
</head>
<body>
	<form method="post" target="_top" action="{sessionurl file=$leaveAction params="action=leaveshare&id={$leaveItem.id}{if isset($leaveKind) && $leaveKind != ''}&kind={$leaveKind}{/if}"}">
		{csrffield}
		<input type="hidden" name="id" value="{$leaveItem.id}" />
		{if isset($leaveKind) && $leaveKind != ''}<input type="hidden" name="kind" value="{$leaveKind}" />{/if}
		<input type="hidden" name="do" value="leave" />
		<p>{lng p="shareleaveq"}</p>
		<p><b>{text value=$leaveItem.title}</b></p>
		{if isset($leaveItem.owner_email) && $leaveItem.owner_email != ''}
		<p><small>{text value=$leaveItem.owner_email}</small></p>
		{/if}
		<p align="right">
			<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
			<input type="submit" value="{lng p="shareleave"}" />
		</p>
	</form>
</body>
</html>
