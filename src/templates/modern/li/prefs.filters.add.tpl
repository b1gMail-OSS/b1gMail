<div id="contentHeader">
	<div class="left">
		<i class="fa fa-filter" aria-hidden="true"></i>
		{lng p="addfilter"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f1" method="post" action="prefs.php?action=filters&do=createFilter&sid={$sid}" onsubmit="return(checkFilterForm(this));">
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
				<input type="checkbox" id="active" name="active" checked="checked" />
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
