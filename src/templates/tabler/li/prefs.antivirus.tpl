<div class="bm-prefs-page bm-prefs-page-antivirus">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-bug icon icon-sm" aria-hidden="true"></i>
		{lng p="antivirus"}
	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=antivirus&do=save&sid={$sid}">
<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="antivirus"}</th>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="virusfilter">{lng p="virusfilter"}:</label></td>
			<td class="listTableRight">
				<label class="form-check mb-0"><input type="checkbox" class="form-check-input" name="virusfilter" id="virusfilter"{if $virusFilter} checked="checked"{/if} /><span class="form-check-label"><b>{lng p="enable"}</b></span></label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="virusaction">{lng p="virusaction"}:</label></td>
			<td class="listTableRight">
				<select name="virusaction" id="virusaction">
					<option value="-256"{if $virusAction==-256} selected="selected"{/if}>------------</option>
					<option value="-1"{if $virusAction==-1} selected="selected"{/if}>{lng p="block"}</option>
					
					<optgroup label="{lng p="move"}">
					{foreach from=$dropdownFolderList key=dFolderID item=dFolderTitle}
					<option value="{$dFolderID}" style="font-family:courier;"{if $virusAction==$dFolderID} selected="selected"{/if}>{$dFolderTitle}</option>
					{/foreach}
					</optgroup>
				</select>
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
