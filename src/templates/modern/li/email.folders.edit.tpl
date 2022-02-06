<div id="contentHeader">
	<div class="left">
		<i class="fa fa-folder-o" aria-hidden="true"></i>
		{if $folder}{lng p="editfolder"}{else}{lng p="addfolder"}{/if}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
<form name="f1" method="post" action="email.folders.php?action={if $folder}saveFolder&id={$folder.id}{else}createFolder{/if}&sid={$sid}" onsubmit="{if $folder && $folder.intelligent==1}if(!formSubmitOK) {literal}{ parent.frames.condition_frame.document.forms.saveForm.elements.submitParent.value='1';parent.frames.condition_frame.document.forms.saveForm.submit();return(false); }{/literal}{/if}return(checkFolderForm(this));">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {if $folder}{lng p="editfolder"}{else}{lng p="addfolder"}{/if}</th>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="titel">{lng p="title"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="titel" id="titel" value="{text value=$folder.titel allowEmpty=true}" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="parentfolder">{lng p="parentfolder"}:</label></td>
			<td class="listTableRight">
				<select name="parentfolder" id="parentfolder">
					<option value="-1">------------</option>
				{foreach from=$dropdownFolderList key=dFolderID item=dFolderTitle}{if $dFolderID>0&&$dFolderID!=$folder.id}
					<option value="{$dFolderID}" style="font-family:courier;"{if $folder.parent==$dFolderID} selected="selected"{/if}>{$dFolderTitle}</option>
				{/if}{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="storetime">{lng p="storetime"}:</label></td>
			<td class="listTableRight">
				<select name="storetime" id="storetime"{if $folder&&$folder.intelligent==1} disabled="disabled"{/if}>
					<option value="-1">------------</option>
					<option value="86400"{if $folder.storetime==86400} selected="selected"{/if}>1 {lng p="days"}</option>
					<option value="172800"{if $folder.storetime==172800} selected="selected"{/if}>2 {lng p="days"}</option>
					<option value="432000"{if $folder.storetime==432000} selected="selected"{/if}>5 {lng p="days"}</option>
					<option value="604800"{if $folder.storetime==604800} selected="selected"{/if}>7 {lng p="days"}</option>
					<option value="1209600"{if $folder.storetime==1209600} selected="selected"{/if}>2 {lng p="weeks"}</option>
					<option value="2419200"{if $folder.storetime==2419200} selected="selected"{/if}>4 {lng p="weeks"}</option>
					<option value="4838400"{if $folder.storetime==4838400} selected="selected"{/if}>2 {lng p="months"}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="subscribed">{lng p="subscribed"}?</label></td>
			<td class="listTableRight">
				<input type="checkbox" id="subscribed" name="subscribed" {if !$folder || $folder.subscribed==1}checked="checked" {/if}/>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="intelligent">{lng p="intelligent"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" id="intelligent" name="intelligent" {if $folder}readonly="readonly" disabled="disabled" {/if}{if $folder.intelligent==1}checked="checked" {/if}/>
			</td>
		</tr>
		
		{if $folder && $folder.intelligent}
		<tr>
			<td class="listTableLeft">* {lng p="conditions"}:</td>
			<td class="listTableRight">
				<iframe id="condition_frame" name="condition_frame" class="conditionIFrame" width="100%" height="30" scrolling="no" frameborder="0" border="0" src="email.folders.php?action=editConditions&id={$folder.id}&sid={$sid}"></iframe>
				<div class="linkBox">
					{lng p="requiredis"}
					<select name="intelligent_link">
						<option value="1"{if $folder.intelligent_link==1} selected="selected"{/if}>{lng p="ofevery"}</option>
						<option value="2"{if $folder.intelligent_link==2} selected="selected"{/if}>{lng p="ofatleastone"}</option>
					</select>
					{lng p="oftheseconditions"}
				</div>
			</td>
		</tr>		
		{/if}
	
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

