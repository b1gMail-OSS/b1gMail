<div class="bm-prefs-page bm-prefs-page-filters">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-filter icon icon-sm" aria-hidden="true"></i>
		{lng p="addfilter"}
	</div>
</div>

<form name="f1" method="post" action="{sessionurl file='prefs.php' params='action=filters&do=createFilter'}" onsubmit="return(checkFilterForm(this));">
	{csrffield}
<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="addfilter"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="titel">{lng p="title"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="title" id="titel" value="" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="active">{lng p="active"}?</label></td>
			<td class="listTableRight">
				<label class="form-check mb-0"><input type="checkbox" class="form-check-input" id="active" name="active" checked="checked" /></label>
			</td>
		</tr>
	
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="btn btn-primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</div></div>
</form>
</div>
