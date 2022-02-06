<fieldset>
	<legend>{lng p="archiving"}</legend>
		
	<form action="logs.php?action=archiving&do=archive&sid={$sid}" method="post" onsubmit="if(EBID('saveCopy').checked || confirm('{lng p="reallynotarc"}')) spin(this); else return(false);">
		<p>
			{lng p="logarc_desc"}
		</p>
		
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/archiving.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="80">{lng p="date"}:</td>
				<td class="td2">
					{html_select_date prefix="date" start_year="-5" field_order="DMY" field_separator="."}, 
					{html_select_time prefix="date" display_seconds=false}
				</td>
			</tr>
		</table>
		
		<p align="right">
			<input type="checkbox" name="saveCopy" id="saveCopy" checked="checked" />
			<label for="saveCopy"><b>{lng p="savearc"}</label>
			<input class="button" type="submit" value=" {lng p="execute"} " />
		</p>
	</form>
</fieldset>
