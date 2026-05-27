<!doctype html>
<html lang="{lng p="langCode"}">
<script>
(function(){
	try {
		var t = null;
		if(window.parent && window.parent !== window && window.parent.document)
			t = window.parent.document.documentElement.getAttribute('data-bs-theme');
		if(!t)
			t = localStorage.getItem('bm-tabler-theme');
		if(t === 'dark' || t === 'light')
			document.documentElement.setAttribute('data-bs-theme', t);
	} catch(e) {}
})();
</script>

<head>
	<title>{if isset($title)}{$title}{elseif isset($dialogTitle)}{$dialogTitle}{else}{$service_title}{/if}</title>

	<meta charset="{$charset}" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />

	<link rel="shortcut icon" href="{$selfurl}favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="{$tpldir}css/tabler.min.css?{fileDateSig file="css/tabler.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/tabler-icons.min.css?{fileDateSig file="css/tabler-icons.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/inter.css?{fileDateSig file="css/inter.css"}" />
	<link href="{$tpldir}style/dialog-tabler.css?{fileDateSig file="style/dialog-tabler.css"}" rel="stylesheet" type="text/css" />
	<link href="{$tpldir}style/tabler-custom.css?{fileDateSig file="style/tabler-custom.css"}" rel="stylesheet" type="text/css" />
	{foreach from=$_cssFiles.li item=_file}<link rel="stylesheet" type="text/css" href="{$_file}" />{/foreach}

	<script type="text/javascript">
	<!--
		var tplDir = '{$tpldir}';
	//-->
	</script>
	<script src="{$selfurl}clientlang.php?sid={$sid}" type="text/javascript"></script>
	<script src="{$tpldir}clientlib/overlay.js" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js?{fileDateSig file="js/common.js"}" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js?{fileDateSig file="js/loggedin.js"}" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js?{fileDateSig file="js/dialog.js"}" type="text/javascript"></script>
	{foreach from=$_jsFiles.li item=_file}<script type="text/javascript" src="{$_file}"></script>{/foreach}
</head>

<body class="bm-dialog-body{if isset($dialogBodyClass)} {$dialogBodyClass}{/if}"{if isset($dialogOnLoad)} onload="{$dialogOnLoad}"{/if}>
