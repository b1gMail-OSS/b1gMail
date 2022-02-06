<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="pacc_packagedetails"}</title>

	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />

	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	<link href="clientlib/fontawesome/css/font-awesome.min.css?{fileDateSig file="../../clientlib/fontawesome/css/font-awesome.min.css"}" rel="stylesheet" type="text/css" />
	<link href="clientlib/fontawesome/css/font-awesome-animation.min.css?{fileDateSig file="../../clientlib/fontawesome/css/font-awesome-animation.min.css"}" rel="stylesheet" type="text/css" />

	<!-- client scripts -->
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

<body onload="documentLoader()">

	<h1>
		<i class="fa fa-archive" aria-hidden="true"></i>
		{text value=$package.titel}
		{if $package.geloescht}<small><i>({lng p="pacc_deletedpackage"})</i></small>{/if}
	</h1>

	<fieldset style="margin-top:12px;margin-bottom:12px;">
		<legend>{lng p="pacc_description"}</legend>
		{$package.beschreibung}
	</fieldset>

	<fieldset style="margin-bottom:12px;">
		<table width="100%">
			<tr>
				<td nowrap="nowrap"><b>{lng p="pacc_price"}</b> &nbsp;&nbsp;&nbsp;</td>
				{foreach from=$matrix.packages item=package}
				<td>{if $package.isFree}{lng p="pacc_free"}{else}<b>{text value=$package.price}</b> <small>({text value=$package.priceInterval}{if $package.priceTax}, {text value=$package.priceTax}{/if})</small>{/if}</td>
				{/foreach}
			</tr>

			{foreach from=$matrix.fields item=fieldTitle key=fieldKey}
			<tr>
				<td nowrap="nowrap"><b>{$fieldTitle}</b> &nbsp;&nbsp;&nbsp;</td>
				{foreach from=$matrix.packages item=package}
				<td>{paccFormatField value=$package.fields.$fieldKey key=$fieldKey cut=25}</td>
				{/foreach}
			</tr>
			{/foreach}
		</table>
	</fieldset>

	<div>
		<div style="float:right">
			<input type="submit" value=" {lng p="close"} " onclick="parent.hideOverlay();" />
		</div>
	</div>
</body>

</html>
