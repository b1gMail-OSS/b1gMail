<form action="prefs.webdisk.php?action=limits&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="limits"}</legend>
		
		<table>
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="{$tpldir}images/filetype.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="limitedextensions"}:</td>
				<td class="td2" width="300">
					<textarea style="width:100%;height:120px;" name="forbidden_extensions">{text value=$bm_prefs.forbidden_extensions allowEmpty=true}</textarea>
					<small>{lng p="sepby"}</small>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="limitedmimetypes"}:</td>
				<td class="td2">
					<textarea style="width:100%;height:120px;" name="forbidden_mimetypes">{text value=$bm_prefs.forbidden_mimetypes allowEmpty=true}</textarea>
					<small>{lng p="sepby"}</small>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<p>
		<div style="float:right;" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
