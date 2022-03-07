{if $pref_exturl_warning=='no'}
<meta http-equiv="refresh" content="0;URL={$url}" />
<a href="{$url}" rel="noreferrer">{$url}</a>
{else}
<html>
<head>
	<title>{lng p="redirect_note"}</title>
	<style>
	<!--
    {literal}
		*			{ font-family: tahoma, arial, verdana; font-size: 12px; }
		H1			{ font-size: 16px; font-weight: bold; border-bottom: 1px solid #DDDDDD; }
		H2, a		{ font-size: 14px; font-weight: normal; }
		.addInfo	{ font-family: courier, courier new; font-size: 10px; height: 100px; overflow: auto;
						border: 1px solid #DDDDDD; padding: 5px; }
		.box		{ width: 50%; border: 1px solid #CCC; border-radius: 10px; background-color: #FFF;
						padding: 30px 15px; margin-top: 3em; margin-left: auto; margin-right: auto; }
    {/literal}
	//-->
	</style>
	<link href="{$selfurl}clientlib/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#F1F2F6">
	
	<div class="box">
		<table width="100%">
			<tr>
				<td align="center" width="80" valign="top"><i class="fa fa-exclamation-triangle fa-5x" aria-hidden="true"></i></td>
				<td valign="top" align="left">
				
					<h1>{lng p="redirect_note"}</h1>
					<h2>{$exturlwarningurl}</h2>
					
					<hr size="1" color="#DDDDDD" width="100%" noshade="noshade" />
					<input type="button" value="&nbsp; {lng p="close"} &nbsp;" onclick="javascript:window.close()" style="padding: 1px;" />
					
				</td>
			</tr>
		</table>
	</div>

</body>
</html>
{/if}