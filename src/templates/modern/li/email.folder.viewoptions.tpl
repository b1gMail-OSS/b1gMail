<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="viewoptions"}</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	
	<!-- client scripts -->
	<script src="clientlang.php?sid={$sid}" type="text/javascript"></script>
	<script src="clientlib/overlay.js" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
</head>

<body>

		<form action="email.php?folder={$folderID}&do=setViewOptions&overlay=true&sid={$sid}" method="post">
			<fieldset>
				<legend>{lng p="viewoptions"}</legend>
				
				<table>
					<tr>
						<td><label for="group_mode">{lng p="group_mode"}</label>:</td>
						<td><select name="group_mode" id="group_mode">
								<option value="-"{if $groupMode=='-'} selected="selected"{/if}>------------</option>
								
								<optgroup label="{lng p="props"}">
									<option value="fetched"{if $groupMode=='fetched'} selected="selected"{/if}>{lng p="date"}</option>
									<option value="von"{if $groupMode=='von'} selected="selected"{/if}>{lng p="from"}</option>
								</optgroup>
								
								<optgroup label="{lng p="flags"}">
									<option value="gelesen"{if $groupMode=='gelesen'} selected="selected"{/if}>{lng p="read"}</option>
									<option value="beantwortet"{if $groupMode=='beantwortet'} selected="selected"{/if}>{lng p="answered"}</option>
									<option value="weitergeleitet"{if $groupMode=='weitergeleitet'} selected="selected"{/if}>{lng p="forwarded"}</option>
									<option value="flagged"{if $groupMode=='flagged'} selected="selected"{/if}>{lng p="flagged"}</option>
									<option value="done"{if $groupMode=='done'} selected="selected"{/if}>{lng p="done"}</option>
									<option value="attach"{if $groupMode=='attach'} selected="selected"{/if}>{lng p="attachment"}</option>
									<option value="color"{if $groupMode=='color'} selected="selected"{/if}>{lng p="color"}</option>
								</optgroup>
							</select></td>
					</tr>
					<tr>
						<td><label for="perpage">{lng p="mails_per_page"}</label>:</td>
						<td><select name="perpage" id="perpage">
							{section start=5 step=5 loop=55 name=num}
							<option value="{$smarty.section.num.index}"{if $perPage==$smarty.section.num.index} selected="selected"{/if}>{$smarty.section.num.index}</option>
							{/section}
							{section start=75 step=25 loop=175 name=num}
							<option value="{$smarty.section.num.index}"{if $perPage==$smarty.section.num.index} selected="selected"{/if}>{$smarty.section.num.index}</option>
							{/section}
						</select></td>
					</tr>
				</table>
			</fieldset>
	
			<p align="right">
				<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
				<input type="submit" value="{lng p="ok"}" />
			</p>
		</form>
	
</body>

</html>
