<!doctype html>
<html lang="{lng p="langCode_editor"|default:'en'}">

<head>
	<meta charset="{$charset}" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>{lng p="protectedfolder"}</title>

	<link rel="shortcut icon" type="image/png" href="{$selfurl}res/favicon.png" />
	<link rel="icon" href="{$selfurl}favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="{$tpldir}css/tabler.min.css?{fileDateSig file="css/tabler.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/tabler-icons.min.css?{fileDateSig file="css/tabler-icons.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/inter.css?{fileDateSig file="css/inter.css"}" />
	<link rel="stylesheet" href="{$tpldir}style/share.css?{fileDateSig file="style/share.css"}" />

	<script src="{$selfurl}clientlang.php"></script>
</head>

<body class="bm-share-password-dialog p-3" onload="document.getElementById('pw').focus()">

	<div class="d-flex gap-3">
		<span class="avatar avatar-lg bg-primary-lt text-primary flex-shrink-0">
			<i class="ti ti-lock icon" aria-hidden="true"></i>
		</span>
		<div class="flex-fill">
			<p class="mb-3 text-secondary">{lng p="protected_desc"}</p>

			<form action="index.php?action=passwordSubmitFile&amp;user={$user|escape:'url'}&amp;file={$fileShareToken|escape:'url'}&amp;id={$fileShareID}" method="post" class="bm-share-password-form">
				<div class="mb-3">
					<label class="form-label" for="pw">{lng p="password"}</label>
					<input type="password" class="form-control" name="pw" id="pw" autocomplete="current-password" required="required" />
				</div>

				<div class="d-flex justify-content-end gap-2">
					<button type="button" class="btn" onclick="parent.hideOverlay()">{lng p="cancel"}</button>
					<button type="submit" class="btn btn-primary">{lng p="ok"}</button>
				</div>
			</form>
		</div>
	</div>

</body>
</html>
