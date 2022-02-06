<div id="contentHeader">
	<div class="left">
		<i class="fa fa-filter" aria-hidden="true"></i>
		{lng p="editfilter"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
<form name="f1" method="post" action="prefs.php?action=filters&do=saveFilter&id={$filter.id}&sid={$sid}" onsubmit="if(checkFilterForm(this)) {literal}{ if(!formSubmitOK) { parent.frames.condition_frame.document.forms.saveForm.submitParent.value='1';parent.frames.condition_frame.document.forms.saveForm.submit(); return(false); } else { return(true); } }{/literal} return(false);">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="editfilter"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="titel">{lng p="title"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="title" id="titel" value="{text value=$filter.title}" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="active">{lng p="active"}?</label></td>
			<td class="listTableRight">
				<input type="checkbox" id="active" name="active"{if $filter.active} checked="checked"{/if} />
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeft">* {lng p="conditions"}:</td>
			<td class="listTableRight">
				<iframe id="condition_frame" name="condition_frame" class="conditionIFrame" width="100%" height="30" scrolling="no" frameborder="0" border="0" src="prefs.php?action=filters&do=editConditions&id={$filter.id}&sid={$sid}"></iframe>
				<div class="linkBox">
					{lng p="filterrequiredis"}
					<select name="link">
						<option value="1"{if $filter.link==1} selected="selected"{/if}>{lng p="ofevery"}</option>
						<option value="2"{if $filter.link==2} selected="selected"{/if}>{lng p="ofatleastone"}</option>
					</select>
					{lng p="oftheseconditions"}
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeft">* {lng p="actions"}:</td>
			<td class="listTableRight">
				<iframe id="action_frame" name="action_frame" class="conditionIFrame" width="100%" height="30" scrolling="no" frameborder="0" border="0" src="prefs.php?action=filters&do=editActions&id={$filter.id}&sid={$sid}"></iframe>
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeft">{lng p="options"}:</td>
			<td class="listTableRight">
				<input type="checkbox" name="flags[]" value="2"{if $filter.flags&2} checked="checked"{/if} id="flag2" />
				<label for="flag2">{lng p="nospamoverride"}</label>
			</td>
		</tr>
	
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>
</div></div>
