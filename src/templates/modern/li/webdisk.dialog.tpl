<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="$type"}</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	
	<!-- client scripts -->
	<script src="clientlang.php?sid={$sid}" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
</head>

<body onload="dialogInit('{$sid}');documentLoader()">

	<table cellpadding="0" cellspacing="0" width="100%">
	
		{if $type=='save'}
		<tr>
			<td colspan="2" class="saveAs">
				{lng p="saveas"}: <input type="text" name="filename" id="filename" value="{text value=$filename allowEmpty=true}" size="30" />
			</td>
		</tr>
		{else}
			<input type="hidden" name="filename" id="filename" />
		{/if}
		<input type="hidden" name="fileid" id="fileid" />
		
		<tr>
			<td colspan="2">
				<div class="fileList" id="fileList" style="width:635px;">
					<br>
					<center><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></center>
				</div>
			</td>
		</tr>
		
		<tr>
			<td height="32">
				{if $type=='save'}<input type="button" value="{lng p="createfolder"}" onclick="createFolder()" />{/if}
				&nbsp;
			</td>
			<td align="right">
				<input type="button" value="{lng p="cancel"}" onclick="parent.hideOverlay()" />
				<input type="button" value="{lng p="ok"}" onclick="close{if $type=='save'}Save{else}Open{/if}Dialog('{$smarty.request.field}'{if $params}, '{$params}'{/if})" />
			</td>
		</tr>
	
	</table>
	
</body>

</html>
