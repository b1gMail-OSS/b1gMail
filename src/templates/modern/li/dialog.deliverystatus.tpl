<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="deliverystatus"}</title>

	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />

	<!-- links -->
	<link rel="shortcut icon" href="{$selfurl}favicon.ico" type="image/x-icon" />
	<link href="{$tpldir}style/dialog.css?{fileDateSig file="style/dialog.css"}" rel="stylesheet" type="text/css" />
	<link href="{$selfurl}clientlib/fontawesome/css/font-awesome.min.css?{fileDateSig file="../../clientlib/fontawesome/css/font-awesome.min.css"}" rel="stylesheet" type="text/css" />
	{foreach from=$_cssFiles.li item=_file}	<link rel="stylesheet" type="text/css" href="{$_file}" /> {/foreach}

	<!-- client scripts -->
	<script>
	<!--
		var tplDir = '{$tpldir}';
	//-->
	</script>
	<script src="{$selfurl}clientlang.php" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
	{foreach from=$_jsFiles.li item=_file}  <script type="text/javascript" src="{$_file}"></script>{/foreach}
</head>

<body onload="documentLoader()">

	<table width="100%">
		<tr>
			<td align="center" colspan="2">
				<div class="deliveryStatusDiv">
					<table class="listTable">
						<tr>
							<th>{lng p="recipient"}</th>
							<th>{lng p="status"}</th>
						</tr>
						{foreach from=$deliveryStatus.recipients item=recp}
						<tr>
							<td>
								{if $recp.status==3}<i class="fa fa-check" style="color:green;"></i>
								{elseif $recp.status==5}<i class="fa fa-exclamation-triangle" style="color:orange;"></i>
								{else}<i class="fa fa-refresh"></i>{/if}
								{text value=$recp.recipient}
							</td>
							<td>
								{if $recp.status==1||$recp.status==2}{lng p="mds_recp_processing"}
								{elseif $recp.status==3}{if $recp.delivered_to}<a href="#" class="hint" title="{lng p="to2"} {email value=$recp.delivered_to}, {date timestamp=$recp.updated}">{/if}{lng p="mds_recp_delivered"}{if $recp.delivered_to}</a>{/if}
								{elseif $recp.status==4}{lng p="mds_recp_deferred"}
								{elseif $recp.status==5}{lng p="mds_recp_failed"}
								{else}{lng p="unknown"}{/if}
							</td>
						</tr>
						{/foreach}
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td align="right">
				<input type="button" onclick="parent.hideOverlay()" value="{lng p="close"}" />
			</td>
		</tr>
	</table>

</body>

</html>
