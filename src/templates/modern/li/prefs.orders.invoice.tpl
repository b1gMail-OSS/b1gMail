<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="invoice"}</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	
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

<body onload="documentLoader(){if $print};window.print();window.close();{/if}" style="background-color:#FFFFFF;">

	{if !$print}
	<div style="position:fixed;top:0px;left:0px;width:100%;background-image:url({$tpldir}images/li/bar.png);padding:6px;">
		<div style="float:left;">
			<a href="javascript:void(0);" onclick="openWindow('prefs.php?action=orders&do=showInvoice&id={$id}&print=true&sid={$sid}','printInvoice_{$id}',640,480);" style="color:#FFFFFF;text-decoration:none;">
				<i class="fa fa-print" aria-hidden="true"></i>
				<span style="text-decoration:underline;">{lng p="printinvoice"}</span>
			</a>
		</div>
		<div style="float:right;margin-right:18px;">
			<a href="javascript:parent.hideOverlay();" style="color:#FFFFFF;text-decoration:none;">
				<i class="fa fa-trash-o" aria-hidden="true"></i>
				<span style="text-decoration:underline;">{lng p="close"}</span>
			</a>
		</div>
	</div>

	<br /><br />
	{/if}
	
	{$invoice}
	
</body>

</html>
