<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=msgqueue&action=headers&save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_ownheaders"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_queue.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_inbound"}:</td>
				<td class="td2">
					<textarea name="inbound_headers" style="width:80%;height:100px;">{text value=$bms_prefs.inbound_headers allowEmpty=true}</textarea>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_outbound"}:</td>
				<td class="td2">
					<textarea name="outbound_headers" style="width:80%;height:100px;">{text value=$bms_prefs.outbound_headers allowEmpty=true}</textarea>
				</td>
		</table>
	</fieldset>
	
	<p>
		<div style="float:left" class="buttons">
			<button type="button" class="btn btn-outline-secondary btn-sm"  onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=msgqueue"}';" >&laquo; {lng p="back"}</button>
		</div>
		<div style="float:right" class="buttons">
			<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
			{lng p="bms_headersnote"}
			&nbsp;
			<button type="submit" class="btn btn-primary"  >{lng p="save"}</button>
		</div>
	</p>
</form>
