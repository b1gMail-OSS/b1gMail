<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="addressbook"}</title>
    
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

<body onload="documentLoader()">

	<table width="100%">
		<tr>
			<td colspan="2" height="340">
				<div class="addressDiv" style="height:330px;" id="groups">
				{foreach from=$groupList key=groupID item=group}
					<div class="addressItem" style="min-height:20px;cursor:default;">
						<div style="float:left;">
							<i class="fa fa-users" aria-hidden="true"></i>
							{text value=$group.title}
						</div>
						
						<div style="float:right;padding-right:18px;">
							<small>
								{$group.members}
								{lng p="members"}
							</small>
							
							&nbsp;
							&nbsp;
							
							<a title="{lng p="sendmail"}" target="_top" href="email.compose.php?sid={$sid}&toGroup={$groupID}"><i class="fa fa-envelope-open-o" aria-hidden="true"></i></a>
							<a title="{lng p="export"}" target="_top" href="organizer.addressbook.php?sid={$sid}&action=groups&do=export&id={$groupID}"><i class="fa fa-address-card-o" aria-hidden="true"></i></a>
							<a title="{lng p="delete"}" onclick="return(confirm('{lng p="realdel"}'))" href="organizer.addressbook.php?sid={$sid}&action=groups&do=delete&id={$groupID}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
						</div>
					</div>
				{/foreach}
				</div>
			</td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td align="left">
				<form action="organizer.addressbook.php?action=groups&do=add&sid={$sid}" method="post">
					<input type="text" name="title" value="" style="width:180px;" />
					<input type="submit" value=" {lng p="add"} " />
				</form>
			</td>
			<td align="right">
				<input type="button" onclick="parent.document.location.reload();" value="{lng p="close"}" />
			</td>
		</tr>
	</table>
	
</body>

</html>
