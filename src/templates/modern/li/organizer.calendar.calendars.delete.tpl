<!DOCTYPE html>
<html>
<head>
	<title>{lng p="editcalendar"}</title>
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
	<form method="post" target="_top" action="{sessionurl file='organizer.calendar.php' params="action=calendars&do=delete&id={$calendarItem.id}&csrf_token={$csrfToken}"}">
		{csrffield}
		<p>{lng p="realdel"}</p>
		<p><b>{text value=$calendarItem.title}</b></p>
		<p align="right">
			<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
			<input type="submit" value="{lng p="delete"}" />
		</p>
	</form>
</body>
</html>
