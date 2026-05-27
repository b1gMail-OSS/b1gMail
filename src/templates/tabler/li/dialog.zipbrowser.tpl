<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{text value=$filename}</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
	<!-- links -->
	<link rel="shortcut icon" href="{$selfurl}favicon.ico" type="image/x-icon" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	
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
	<script src="{$tpldir}clientlib/dtree.js" type="text/javascript"></script>
</head>

<body onload="documentLoader()">

	<table width="100%">
		<tr>
			<td align="center" colspan="2">
				<div class="fileListDiv">
					<div class="cycleBG">
						<script>
						<!--
							var zip = new dTree('zip');
							zip.config.useCookies = false;
							zip.config.useLines = false;
							zip.add(-2, -1, ' {text value=$filename cut=65 escape=true noentities=true}', '', '{text value=$filename escape=true noentities=true}', '', '{$tpldir}images/li/ico_zip.png', '{$tpldir}images/li/ico_zip.png'); 
							{foreach from=$files item=file}
							zip.add({$file.fileNo}, {$file.parentID}, ' {text value=$file.baseName cut=65 escape=true noentities=true}', '{if $file.type=='file'}email.read.php?action=attachedZIP&id={$id}&attachment={$attachment}&do=extract&fileNo={$file.fileNo}&sid={$sid}{/if}', '{text value=$file.baseName escape=true noentities=true}{if $file.type=='file'} ({size bytes=$file.uncompressedSize}){/if}', '', '{$tpldir}images/li/webdisk_{if $file.type=='folder'}folder{else}file{/if}.png', '{$tpldir}images/li/webdisk_{if $file.type=='folder'}folder{else}file{/if}.png'); 
							{/foreach}
							document.write(zip);
						//-->
						</script>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td align="left">
				<input type="button" onclick="document.location.href='email.read.php?id={$id}&action=downloadAttachment&attachment={$attachment}&sid={$sid}';" value="{lng p="download"}" />
			</td>
			<td align="right">
				<input type="button" onclick="parent.hideOverlay()" value="{lng p="close"}" />
			</td>
		</tr>
	</table>
	
</body>

</html>
