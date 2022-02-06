<div id="contentHeader">
	<div class="left">
		<i class="fa fa-bug" aria-hidden="true"></i>
		{lng p="antivirus"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f1" method="post" action="prefs.php?action=antivirus&do=save&sid={$sid}">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="antivirus"}</th>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="virusfilter">{lng p="virusfilter"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="virusfilter" id="virusfilter"{if $virusFilter} checked="checked"{/if} />
				<label for="virusfilter"><b>{lng p="enable"}</b></label>
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
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>

</div></div>
