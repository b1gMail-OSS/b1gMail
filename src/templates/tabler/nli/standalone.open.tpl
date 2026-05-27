<!doctype html>
<html lang="{lng p="langCode_editor"|default:'en'}">

<head>
	<meta charset="{$charset}" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<meta name="robots" content="noindex" />
	<title>{if isset($standalonePageTitle)}{$standalonePageTitle}{else}{$service_title}{/if}</title>

	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link rel="stylesheet" href="{$tpldir}css/tabler.min.css?{fileDateSig file="css/tabler.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/tabler-icons.min.css?{fileDateSig file="css/tabler-icons.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/inter.css?{fileDateSig file="css/inter.css"}" />
	<link rel="stylesheet" href="{$tpldir}style/tabler-custom.css?{fileDateSig file="style/tabler-custom.css"}" />
</head>

<body class="border-top-wide border-primary nli-standalone d-flex flex-column">
