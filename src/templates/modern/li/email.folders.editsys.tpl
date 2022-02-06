<div id="contentHeader">
	<div class="left">
		<i class="fa fa-folder-o" aria-hidden="true"></i> {lng p="editfolder"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
<form name="f1" method="post" action="email.folders.php?action=saveFolder&id={$folderID}&sid={$sid}">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="editfolder"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="titel">{lng p="title"}:</label></td>
			<td class="listTableRight">
				<input type="text" value="{text value=$folderTitle allowEmpty=true}" style="width:100%;" disabled="disabled" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="storetime">{lng p="storetime"}:</label></td>
			<td class="listTableRight">
				<select name="storetime" id="storetime">
					<option value="-1">------------</option>
					<option value="86400"{if $storeTime==86400} selected="selected"{/if}>1 {lng p="days"}</option>
					<option value="172800"{if $storeTime==172800} selected="selected"{/if}>2 {lng p="days"}</option>
					<option value="432000"{if $storeTime==432000} selected="selected"{/if}>5 {lng p="days"}</option>
					<option value="604800"{if $storeTime==604800} selected="selected"{/if}>7 {lng p="days"}</option>
					<option value="1209600"{if $storeTime==1209600} selected="selected"{/if}>2 {lng p="weeks"}</option>
					<option value="2419200"{if $storeTime==2419200} selected="selected"{/if}>4 {lng p="weeks"}</option>
					<option value="4838400"{if $storeTime==4838400} selected="selected"{/if}>2 {lng p="months"}</option>
				</select>
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
