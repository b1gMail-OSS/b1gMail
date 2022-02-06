<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Actions</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}style/loggedin.css" rel="stylesheet" type="text/css" />
	
	<!-- client scripts -->
	<script src="clientlang.php" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
	<script src="{$tpldir}js/prefs.js" type="text/javascript"></script>
</head>

<body style="margin: 0px; background-color: #FFFFFF; background-image: none;">

<form action="prefs.php?action=filters&do=editActions&do2=save&id={$id}&sid={$sid}" method="post" id="saveForm">
<input type="hidden" name="submitParent" value="0" />
<table width="100%" cellspacing="0" cellpadding="0" id="table">
{foreach from=$actions item=action}
{cycle values="conditionBox1,conditionBox2" assign="class"}
<tr class="{$class}">
	<td>&nbsp;<select id="op_{$action.id}" name="op_{$action.id}" onchange="showAppropriateValueLayer(this, '{$action.id}')">
			<option value="1" {if $action.op==1}selected="selected" {/if}>{lng p="move"}</option>
			<option value="2" {if $action.op==2}selected="selected" {/if}>{lng p="block"}</option>
			<option value="3" {if $action.op==3}selected="selected" {/if}>{lng p="delete"}</option>
			<option value="4" {if $action.op==4}selected="selected" {/if}>{lng p="markread"}</option>
			<option value="5" {if $action.op==5}selected="selected" {/if}>{lng p="markspam"}</option>
			<option value="6" {if $action.op==6}selected="selected" {/if}>{lng p="mark"}</option>
			<option value="12" {if $action.op==12}selected="selected" {/if}>{lng p="markdone"}</option>
			<option value="8" {if $action.op==8}selected="selected" {/if}>{lng p="sendsmsnotify"}</option>
			<option value="13" {if $action.op==13}selected="selected" {/if}>{lng p="sendnotify"}</option>
			<option value="9" {if $action.op==9}selected="selected" {/if}>{lng p="autoresponder"}</option>
			{if $action.op==10||$forwardingAllowed}<option value="10" {if $action.op==10}selected="selected" {/if}>{lng p="forward"}</option>{/if}
			<option value="11" {if $action.op==11}selected="selected" {/if}>{lng p="setmailcolor"}</option>
			<option value="7" {if $action.op==7}selected="selected" {/if}>{lng p="stoprules"}</option>
		</select>
		
		<span id="folderValue_{$action.id}" style="display:none;">
			{lng p="moveto"}
			<select name="folder_val_{$action.id}">
			{foreach from=$dropdownFolderList key=dFolderID item=dFolderTitle}
				<option value="{$dFolderID}" style="font-family:courier;"{if $action.val==$dFolderID} selected="selected"{/if}>{$dFolderTitle}</option>
			{/foreach}
			</select>
		</span>
		
		<span id="mailValue_{$action.id}" style="display:none;">
			{lng p="to2"}
			<input type="text" name="mail_val_{$action.id}" value="{text value=$action.text_val allowEmpty=true}" size="24" />
		</span>
		
		<span id="draftValue_{$action.id}" style="display:none;">
			{lng p="with"}
			<select name="draft_val_{$action.id}">
				<option value="0">--- {lng p="selectdraft"} ---</option>
			{foreach from=$draftList key=draftID item=draft}
				<option value="{$draftID}"{if $action.val==$draftID} selected="selected"{/if}>{text value=$draft.subject cut=35}</option>
			{/foreach}
			</select>
		</span>
		
		<span id="colorValue_{$action.id}" style="display:none;">
			{lng p="to3"}
			
			<span class="mailColorButtons" style="padding:0px;text-align:left;">
				<input type="hidden" name="color_val_{$action.id}" id="color_val_{$action.id}" value="{if $action.val==0}1{else}{$action.val}{/if}" />
				
				<span id="mailColorButton_1_{$action.id}" class="mailColorButton_1{if $action.val==1||$action.val==0}_a{/if}" onclick="javascript:setActionColor({$action.id}, 1);"><img src="{$tpldir}images/pixel.gif" /></span>&nbsp;
				<span id="mailColorButton_2_{$action.id}" class="mailColorButton_2{if $action.val==2}_a{/if}" onclick="javascript:setActionColor({$action.id}, 2);"><img src="{$tpldir}images/pixel.gif" /></span>&nbsp;
				<span id="mailColorButton_3_{$action.id}" class="mailColorButton_3{if $action.val==3}_a{/if}" onclick="javascript:setActionColor({$action.id}, 3);"><img src="{$tpldir}images/pixel.gif" /></span>&nbsp;
				<span id="mailColorButton_4_{$action.id}" class="mailColorButton_4{if $action.val==4}_a{/if}" onclick="javascript:setActionColor({$action.id}, 4);"><img src="{$tpldir}images/pixel.gif" /></span>&nbsp;
				<span id="mailColorButton_5_{$action.id}" class="mailColorButton_5{if $action.val==5}_a{/if}" onclick="javascript:setActionColor({$action.id}, 5);"><img src="{$tpldir}images/pixel.gif" /></span>&nbsp;
				<span id="mailColorButton_6_{$action.id}" class="mailColorButton_6{if $action.val==6}_a{/if}" onclick="javascript:setActionColor({$action.id}, 6);"><img src="{$tpldir}images/pixel.gif" /></span>
			</span>
		</span>
		
		<script>
		<!--
			showAppropriateValueLayer(EBID('op_{$action.id}'), '{$action.id}');
		//-->
		</script>
	</td>
	<td align="right"><input type="submit" name="remove_{$action.id}" value=" - " {if $actionCount==1} disabled="disabled"{/if}/>
						<input type="submit" name="add" value=" + " />&nbsp;</td>
</tr>
{/foreach}
</table>
</form>

<script>
<!--
	parent.document.getElementById('action_frame').style.height = max(getElementMetrics(EBID('table'), 'h'), 30) + 'px';
	{if $smarty.request.submitParent}parent.document.formSubmitOK = true;
	parent.document.forms.f1.submit();{/if}
//-->
</script>

</body>

</html>
