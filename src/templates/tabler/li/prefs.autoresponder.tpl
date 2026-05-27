<div class="bm-prefs-page bm-prefs-page-autoresponder">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-mail-forward icon icon-sm" aria-hidden="true"></i>
		{lng p="autoresponder"}
	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=autoresponder&do=save&sid={$sid}">
<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="autoresponder"}</th>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="active">{lng p="autoresponder"}:</label></td>
			<td class="listTableRight">
				<label class="form-check mb-0"><input type="checkbox" class="form-check-input" name="active" id="active"{if $active} checked="checked"{/if} /><span class="form-check-label"><b>{lng p="enable"}</b></span></label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="betreff">{lng p="subject"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="betreff" id="betreff" value="{text allowEmpty=true value=$betreff}" style="width:350px;">
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="mitteilung">{lng p="text"}:</label></td>
			<td class="listTableRight">
				<textarea name="mitteilung" id="mitteilung" style="width:400px;height:200px;">{text allowEmpty=true value=$mitteilung}</textarea>
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
