<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
	<title>b1gMail - {lng p="session_locked"}</title>
	<link rel="stylesheet" href="{$tpldir}css/tabler.min.css?{fileDateSig file="css/tabler.min.css"}" />
	<script src="{$tpldir}js/tabler.min.js?{fileDateSig file="js/tabler.min.js"}"></script>
	<script src="{$tpldir}js/avatar.js?{fileDateSig file="js/avatar.js"}"></script>
	<link rel="stylesheet" href="{$tpldir}css/inter.css?{fileDateSig file="css/inter.css"}" />
</head>
<body class="d-flex flex-column">
<div class="page page-center">
	<div class="container-tight py-4">
		<div class="text-center mb-4">
			<img src="{$tpldir}images/logo_text.png" height="36" alt="{lng p="acp"}">
		</div>
		<form class="card card-md" id="bmSessionLockForm" method="post" action="{sessionurl file='welcome.php' params='action=sessionUnlock'}" autocomplete="off">
			{csrffield}
			<div class="card-body text-center">
				<div class="mb-4">
					<h2 class="card-title">{lng p="session_locked"}</h2>
					<p class="text-secondary">{lng p="session_locked_desc"}</p>
				</div>
				{if $sessionLockName != ''}
				<div class="mb-4">
					{include file="user-avatar.tpl" avatarSize="xl" avatarClass="mb-3" avatarBgPrimary=true}
					<h3 class="mb-0">{text value=$sessionLockName}</h3>
					{if $sessionLockUsername|default:'' != '' && $sessionLockUsername != $sessionLockName}
					<div class="text-secondary">{text value=$sessionLockUsername}</div>
					{elseif $sessionLockEmail|default:'' != ''}
					<div class="text-secondary">{email value=$sessionLockEmail}</div>
					{/if}
				</div>
				{/if}
				{if $sessionUnlockError|default:'' != ''}
				<div class="alert alert-danger" role="alert">{text value=$sessionUnlockError}</div>
				{/if}
				<div class="mb-4">
					<input type="password" class="form-control" name="password" id="bmSessionLockPassword" placeholder="{lng p="password"}" autocomplete="current-password" required>
				</div>
				<div class="d-grid gap-2">
					<button type="submit" class="btn btn-primary">
						<i class="ti ti-lock-open icon icon-2" aria-hidden="true"></i>
						{lng p="session_unlock"}
					</button>
					<a href="{sessionurl file='index.php' params='action=logout'}" class="btn btn-ghost-secondary">{lng p="logout"}</a>
				</div>
			</div>
		</form>
	</div>
</div>
</body>
</html>
