<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="statement"}</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
	<!-- links -->
	<link rel="shortcut icon" href="{$selfurl}favicon.ico" type="image/x-icon" />
	<link href="{$tpldir}style/dialog.css?{fileDateSig file="style/dialog.css"}" rel="stylesheet" type="text/css" />
	<link href="clientlib/fontawesome/css/font-awesome.min.css?{fileDateSig file="../../clientlib/fontawesome/css/font-awesome.min.css"}" rel="stylesheet" type="text/css" />

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
</head>

<body onload="documentLoader()">

	<form action="{$selfurl}prefs.php?action=membership&do=statement&sid={$sid}" method="post">
	<table width="100%">
		<tr>
			<td align="center" colspan="2">
				<div class="statementDiv">
					<table class="listTable">
						<tr>
							<th width="150">{lng p="date"}</th>
							<th>{lng p="description"}</th>
							<th width="120">{lng p="credits"}</th>
						</tr>
						<tr class="balance">
							<td colspan="2" style="text-align:right;">
								{lng p="balance"} ({date dayonly=true timestamp=$timeFrom})
							</td>
							<td style="text-align:right;">
								{if $startBalance<0}<span style="color:red;">{$startBalance}</span>{else}{$startBalance}{/if}
							</td>
						</tr>
						{if !$transactions}
						<tr>
							<td colspan="3" style="text-align:center;"><em>({lng p="none"})</em></td>
						</tr>
						{/if}
						{foreach from=$transactions item=tx}
						<tr style="{if $tx.status==2}text-decoration:line-through;color:#666;{/if}">
							<td>{date nice=true timestamp=$tx.date}</td>
							<td><a title="{text value=$tx.description}">{text value=$tx.description cut=60}</a></td>
							<td style="text-align:right;">{if $tx.amount<0}<span style="color:red;">{$tx.amount}</span>{else}{$tx.amount}{/if}</td>
						</tr>
						{/foreach}
						{if $dynamicBalance}
						<tr>
							<td>-</td>
							<td>{lng p="dynamicbalance"}</td>
							<td style="text-align:right;">{$dynamicBalance}</td>
						</tr>
						{/if}
						<tr class="balance">
							<td colspan="2" style="text-align:right;">
								{lng p="balance"} ({if $timeToIsCurrent}{lng p="current"}{else}{date dayonly=true timestamp=$timeTo}{/if})
							</td>
							<td style="text-align:right;">
								{if $endBalance<0}<span style="color:red;">{$endBalance}</span>{else}{$endBalance}{/if}
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td align="left">
				{html_select_date prefix="date_" time=$date display_days=false start_year="-10" field_order="MY" time=$timeFrom}
				<input type="submit" value=" {lng p="ok"} &raquo; " />
			</td>
			<td align="right">
				<input type="button" onclick="parent.hideOverlay()" value="{lng p="close"}" />
			</td>
		</tr>
	</table>
	</form>
	
</body>

</html>
