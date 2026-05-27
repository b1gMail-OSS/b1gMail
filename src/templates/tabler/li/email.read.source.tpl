<!doctype html>
<html lang="{lng p="langCode"}" class="bm-dialog-root">

<head>
	<title>{lng p="showsource"}</title>
	<meta charset="{$charset}" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link rel="stylesheet" href="{$tpldir}css/tabler.min.css?{fileDateSig file="css/tabler.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}style/tabler-custom.css?{fileDateSig file="style/tabler-custom.css"}" />
	<script>
	<!--
		var tplDir = '{$tpldir}';
	//-->
	</script>
	<script src="clientlang.php" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
</head>

<body class="bm-dialog-body" onload="documentLoader()">

	<div class="bm-dialog-page bm-dialog-page--source">
		<div class="bm-dialog-source-wrap">
			<div class="bm-dialog-source-content">{$source}</div>
		</div>
		<div class="bm-dialog-footer">
			<button type="button" class="btn btn-primary" onclick="parent.hideOverlay(); return false;">
				{lng p="close"}
			</button>
		</div>
	</div>

</body>

</html>
