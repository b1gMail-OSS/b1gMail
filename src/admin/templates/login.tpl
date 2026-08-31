<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge"/>
	<title>b1gMail - {lng p="acp"}</title>

	<meta name="theme-color" content=""/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="mobile-web-app-capable" content="yes"/>
	<meta name="HandheldFriendly" content="True"/>
	<meta name="MobileOptimized" content="320"/>
	<link rel="icon" type="image/png" href="{$tpldir}images/favicon-256x256.png" />

	<meta name="description" content="{lng p="acp"}"/>

	<link rel="stylesheet" href="{$tpldir}css/tabler.min.css?{fileDateSig file="css/tabler.min.css"}" />
	<script src="{$tpldir}js/tabler.min.js?{fileDateSig file="js/tabler.min.js"}"></script>
	<script src="{$tpldir}js/common.js?{fileDateSig file="js/common.js"}"></script>

	<!-- BEGIN CUSTOM FONT -->
	<link rel="stylesheet" href="{$tpldir}css/inter.css?{fileDateSig file="css/inter.css"}" />
	<!-- END CUSTOM FONT -->
</head>
<body{if !$sessionActive} onload="EBID('username').focus();"{/if} class="d-flex flex-column" id="loginBody" style="background-color: #333333;">
<div class="page page-center">
	<div class="container container-tight py-4">
		<div class="text-center mb-4">
			<img src="{$tpldir}images/logo_text.png" height="36" alt="{lng p="acp"}">
		</div>
		<div class="card card-md">
			<div class="card-body">
				<h2 class="h2 text-center mb-4">{lng p="acp"}</h2>
				{if $sessionActive}
				{include file="login.sessionactive.tpl"}
				{else}
				{if $error|default:''}
				<div class="alert alert-danger" role="alert">{text value=$error}</div>
				{elseif $sessionExpired|default:false}
				<div class="alert alert-warning" role="alert">{lng p="sess_expired"}</div>
				{/if}
				<form action="{sessionurl file='index.php' params='action=login'}" method="post" autocomplete="off">
					{csrffield}
					<div class="mb-3">
						<label class="form-label">{lng p="username"}</label>
						<input type="text" id="username" name="username" class="form-control" placeholder="{lng p="username"}" autocomplete="off">
					</div>
					<div class="mb-2">
						<label class="form-label">{lng p="password"}</label>
						<input type="password" id="pw" name="password" class="form-control" placeholder="{lng p="password"}" autocomplete="off">
					</div>
					<div class="form-footer">
						<button type="submit" class="btn btn-primary w-100">{lng p="login"}</button>
					</div>
				</form>
				{/if}
			</div>
		</div>
	</div>
</div>


</body>
</html>