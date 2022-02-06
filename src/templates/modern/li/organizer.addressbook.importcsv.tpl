<div id="contentHeader">
	<div class="left">
		<i class="fa fa-address-book-o" aria-hidden="true"></i>
		{lng p="import"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f1" method="post" action="organizer.addressbook.php?action=importCSV&sid={$sid}" onsubmit="return(checkNoteForm(this));">
<input type="hidden" name="encoding" value="{text value=$encoding allowEmpty=true}" />
<input type="hidden" name="tempID" value="{$tempID}" />
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="import"}</th>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="priority">{lng p="association"}:</label></td>
			<td class="listTableRight">
				<table width="100%" cellspacing="0">
					<tr>
						<td width="45%" class="listTableTHLeft">{lng p="file"}</td>
						<td class="listTableDB">&lt;-&gt;</td>
						<td width="45%" class="listTableTHRight">{lng p="addressbook"}</td>
					</tr>
					{foreach from=$fileFields item=field}
					{cycle values="#FFFFFF,#F9F9F9" assign="color"}
					<tr>
						<td width="45%" class="listTableLeft2" style="background-color: {$color};">{text value=$field}</td>
						<td class="listTableDB">&lt;-&gt;</td>
						<td width="45%" class="listTableRight2" style="background-color: {$color};"><select name="fields[{$field}]">
							<option value="-">-</option>
							{foreach from=$bookFields item=bookField key=bookFieldKey}
							<option value="{$bookFieldKey}"{if $autoDetect[$field]==$bookFieldKey} selected="selected"{/if}>{$bookField}</option>
							{/foreach}
						</select></td>
					</tr>
					{/foreach}
				</table>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="datasets"}:</td>
			<td class="listTableRight">
				{$datasetCount}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">* {lng p="existingdatasets"}:</td>
			<td class="listTableRight">
				<input type="radio" name="existing" value="update" id="updateExisting" checked="checked" />
					<label for="updateExisting">{lng p="update"}</label>
				<input type="radio" name="existing" value="ignore" id="ignoreExisting" />
					<label for="ignoreExisting">{lng p="ignore"}</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="putingroups"}:</td>
			<td class="listTableRight">
				{foreach from=$groups item=group key=groupID}
					<input type="checkbox" id="group_{$groupID}" name="group_{$groupID}" />
					<label for="group_{$groupID}">{text value=$group.title cut=18}</label><br />
				{/foreach}

				<input type="checkbox" id="group_new" name="group_new" />
				<input type="text" name="group_new_name" placeholder="{lng p="newgroup"}" value="" class="smallInput" style="width:120px;" onchange="this.onkeypress();" onkeypress="EBID('group_new').checked = this.value.length > 0;" /><br />
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
