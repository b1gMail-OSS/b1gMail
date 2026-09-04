<!DOCTYPE html>
<html lang="{lng p="langCode"}">
<head>
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>{$service_title}: {lng p="session_locked"}</title>
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="{$tpldir}style/loggedin.css?{fileDateSig file="style/loggedin.css"}" rel="stylesheet" type="text/css" />
</head>
<body class="bm-session-lock-page">
	<div class="container" style="max-width:420px;margin-top:10vh;">
		<div class="panel panel-default">
			<div class="panel-body text-center">
				<form id="bmSessionLockForm" method="post" action="{sessionurl file='start.php' params='action=sessionUnlock'}" autocomplete="off">
					{csrffield}
					<h3>{lng p="session_locked"}</h3>
					<p class="text-muted">{lng p="session_locked_desc"}</p>
					{if $sessionLockName != ''}
					<p><strong>{text value=$sessionLockName}</strong>
					{if $sessionLockEmail != ''}<br /><span class="text-muted">{email value=$sessionLockEmail}</span>{/if}</p>
					{/if}
					{if $sessionUnlockError|default:'' != ''}
					<div class="alert alert-danger" role="alert">{text value=$sessionUnlockError}</div>
					{/if}
					<div class="form-group">
						<input type="password" class="form-control" name="password" id="bmSessionLockPassword" placeholder="{lng p="password"}" autocomplete="current-password" required />
					</div>
					<button type="submit" class="btn btn-primary btn-block">{lng p="session_unlock"}</button>
					<a href="{sessionurl file='start.php' params='action=logout'}" class="btn btn-link btn-block">{lng p="logout"}</a>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
