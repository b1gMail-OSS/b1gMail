<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge"/>
	<title>b1gMail - {text value=$pageTitle}</title>

	<link rel="icon" type="image/png" href="{$tpldir}images/favicon-256x256.png" />

	<link rel="stylesheet" href="{$tpldir}css/tabler.min.css?{fileDateSig file="css/tabler.min.css"}" />
	<script src="{$tpldir}js/tabler.min.js?{fileDateSig file="js/tabler.min.js"}"></script>
	<link rel="stylesheet" href="{$tpldir}css/inter.css?{fileDateSig file="css/inter.css"}" />
</head>
<body class="d-flex flex-column" style="background-color: #333333;">
<div class="page page-center">
	<div class="container container-tight py-4">
		<div class="text-center mb-4">
			<img src="{$tpldir}images/logo_text.png" height="36" alt="{lng p="acp"}">
		</div>
		<div class="card card-md">
			<div class="card-body">
				<h2 class="h2 text-center mb-4">{text value=$pageTitle}</h2>

				{if isset($mfaError)}
					<div class="alert alert-danger">{text value=$mfaError}</div>
				{/if}
				{if isset($mfaInfo)}
					<div class="alert alert-info">{text value=$mfaInfo}</div>
				{/if}

				<p class="text-secondary">{lng p="mfa_verify_hint"}</p>

				<form method="post" action="{sessionurl file='index.php' params="action=mfaVerify"}" autocomplete="off">
					{csrffield}
					<input type="hidden" name="do" value="mfaVerify" />
					<div class="mb-3">
						<label class="form-label">{lng p="mfa_code"}</label>
						<input type="text" class="form-control" name="mfa_code" inputmode="numeric" pattern="[0-9A-Za-z]*" maxlength="16" required="required" autofocus="autofocus" />
					</div>
					<div class="form-check mb-3">
						<input type="checkbox" class="form-check-input" name="mfa_use_backup" value="1" id="mfa_use_backup" />
						<label class="form-check-label" for="mfa_use_backup">{lng p="mfa_use_backup"}</label>
					</div>
					<div class="form-footer">
						<button type="submit" class="btn btn-primary w-100">{lng p="mfa_verify_submit"}</button>
					</div>
				</form>

				<form method="post" action="{sessionurl file='index.php' params="action=mfaVerify"}" class="mt-3 text-center">
					{csrffield}
					<input type="hidden" name="do" value="mfaResend" />
					<button type="submit" class="btn btn-link">{lng p="mfa_resend_code"}</button>
				</form>

				<div class="text-center mt-3">
					<a href="{sessionurl file='index.php'}">{lng p="back"}</a>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
