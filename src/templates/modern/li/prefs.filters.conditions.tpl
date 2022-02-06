<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Conditions</title>
    
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

<form action="prefs.php?action=filters&do=editConditions&do2=save&id={$id}&sid={$sid}" method="post" id="saveForm">
<input type="hidden" name="submitParent" value="0" />
<table width="100%" cellspacing="0" cellpadding="0" id="table">
{foreach from=$conditions item=condition}
{cycle values="conditionBox1,conditionBox2" assign="class"}
<tr class="{$class}">
	<td>&nbsp;<select id="field_{$condition.id}" name="field_{$condition.id}" onchange="showAppropriateFolderCondLayer(this, '{$condition.id}')">
			<option value="1" {if $condition.field==1}selected="selected" {/if}>{lng p="subject"}</option>
			<option value="2" {if $condition.field==2}selected="selected" {/if}>{lng p="from"}</option>
			<option value="3" {if $condition.field==3}selected="selected" {/if}>{lng p="to"}</option>
			<option value="4" {if $condition.field==4}selected="selected" {/if}>{lng p="cc"}</option>
			<option value="9" {if $condition.field==9}selected="selected" {/if}>{lng p="priority"}</option>
			<option value="10" {if $condition.field==10}selected="selected" {/if}>{lng p="attachment"}</option>
			<option value="13" {if $condition.field==13}selected="selected" {/if}>{lng p="attachmentlist"}</option>
		</select>
		
		<span id="defaultComparison_{$condition.id}" style="display:none;">
			<select name="op_{$condition.id}">
				<option value="1" {if $condition.op==1}selected="selected" {/if}>{lng p="isequal"}</option>
				<option value="2" {if $condition.op==2}selected="selected" {/if}>{lng p="isnotequal"}</option>
				<option value="3" {if $condition.op==3}selected="selected" {/if}>{lng p="contains"}</option>
				<option value="4" {if $condition.op==4}selected="selected" {/if}>{lng p="notcontains"}</option>
				<option value="5" {if $condition.op==5}selected="selected" {/if}>{lng p="startswith"}</option>
				<option value="6" {if $condition.op==6}selected="selected" {/if}>{lng p="endswith"}</option>
			</select>
			<input type="text" size="20" name="text_val_{$condition.id}" value="{text value=$condition.val allowEmpty=true}" />
		</span>
		
		<span id="attComparison_{$condition.id}" style="display:none;">
			<!-- op: 1 -->{lng p="contains"} 
			
			<input type="text" size="20" name="att_val_{$condition.id}" value="{text value=$condition.val allowEmpty=true}" />
		</span>
		
		<span id="boolComparison_{$condition.id}" style="display:none;">
			<!-- op: 1 -->{lng p="isequal"}
			<select name="bool_val_{$condition.id}">
				<option value="yes" {if $condition.val=='yes'}selected="selected" {/if}>{lng p="yes"}</option>
				<option value="no" {if $condition.val=='no'}selected="selected" {/if}>{lng p="no"}</option>
			</select>
		</span>
		
		<span id="priorityComparison_{$condition.id}" style="display:none;">
			<!-- op: 1 -->{lng p="isequal"}
			<select name="priority_val_{$condition.id}">
				<option value="high" {if $condition.val=='high'}selected="selected" {/if}>{lng p="prio_1"}</option>
				<option value="normal" {if $condition.val=='normal'}selected="selected" {/if}>{lng p="prio_0"}</option>
				<option value="low" {if $condition.val=='low'}selected="selected" {/if}>{lng p="prio_-1"}</option>
			</select>
		</span>
		
		<script>
		<!--
			showAppropriateFolderCondLayer(EBID('field_{$condition.id}'), '{$condition.id}');
		//-->
		</script>
	</td>
	<td align="right"><input type="submit" name="remove_{$condition.id}" value=" - " {if $conditionCount==1} disabled="disabled"{/if}/>
						<input type="submit" name="add" value=" + " />&nbsp;</td>
</tr>
{/foreach}
</table>
</form>

<script>
<!--
	parent.document.getElementById('condition_frame').style.height = max(getElementMetrics(EBID('table'), 'h'), 30) + 'px';
	{if $smarty.request.submitParent}parent.frames.action_frame.document.forms.saveForm.submitParent.value = '1'; 
	parent.frames.action_frame.document.forms.saveForm.submit(); {/if}
//-->
</script>

</body>

</html>
