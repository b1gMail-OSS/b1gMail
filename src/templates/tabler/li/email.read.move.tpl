<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="move"}</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	<link href="{$tpldir}style/dtree.css" rel="stylesheet" type="text/css" />
	
	<!-- client scripts -->
	<script>
	<!--
		var tplDir = '{$tpldir}';
	//-->
	</script>
	<script src="clientlang.php" type="text/javascript"></script>
	<script src="{$tpldir}clientlib/dtree.js" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
</head>

<body onload="documentLoader()">

	<table width="100%">
		<tr>
			<td>{lng p="movemailto"}:</td>
		</tr>
		<tr>
			<td align="center">
				<div class="foldersDiv"><div style="padding:5px;">
					<script>
					<!--
						var d = new dTree('d');
					{foreach from=$folderList item=folder}
						d.add({$folder.i}, {$folder.parent}, '{text value=$folder.text escape=true noentities=true}', 'email.read.php?action=move&id={$mailID}&dest={$folder.id}&sid={$sid}', '{text value=$folder.text escape=true noentities=true}', '', 'fa {if $folder.icon == 'inbox'}fa-inbox{elseif $folder.icon == 'outbox'}fa-inbox{elseif $folder.icon == 'drafts'}fa-envelope{elseif $folder.icon == 'spam'}fa-ban{elseif $folder.icon == 'trash'}fa-trash-o{elseif $folder.icon == 'intellifolder'}fa-folder{else}fa-folder-o{/if}', 'fa {if $folder.icon == 'inbox'}fa-inbox{elseif $folder.icon == 'outbox'}fa-inbox{elseif $folder.icon == 'drafts'}fa-envelope{elseif $folder.icon == 'spam'}fa-ban{elseif $folder.icon == 'trash'}fa-trash-o{elseif $folder.icon == 'intellifolder'}fa-folder{else}fa-folder-o{/if}', 'fa {if $folder.icon == 'inbox'}fa-inbox{elseif $folder.icon == 'outbox'}fa-inbox{elseif $folder.icon == 'drafts'}fa-envelope{elseif $folder.icon == 'spam'}fa-ban{elseif $folder.icon == 'trash'}fa-trash-o{elseif $folder.icon == 'intellifolder'}fa-folder{else}fa-folder-o{/if}', 'fa {if $folder.icon == 'inbox'}fa-inbox{elseif $folder.icon == 'outbox'}fa-inbox{elseif $folder.icon == 'drafts'}fa-envelope{elseif $folder.icon == 'spam'}fa-ban{elseif $folder.icon == 'trash'}fa-trash-o{elseif $folder.icon == 'intellifolder'}fa-folder{else}fa-folder-o{/if}');
					{/foreach}
						document.write(d);
					//-->
					</script>
				</div></div>
			</td>
		</tr>
		<tr>
			<td align="right">
				<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
			</td>
		</tr>
	</table>
	
</body>

</html>
