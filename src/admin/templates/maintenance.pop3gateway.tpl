<fieldset>
	<legend>{lng p="pop3gateway"}</legend>
	
	<div id="form">
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/fetch.png" border="0" alt="" width="32" height="32" /></td>
				<td valign="top">{lng p="pop3fetch_desc"}</td>
			</tr>
		</table>
		
		<p align="right">
			{lng p="opsperpage"}:
			<input type="text" name="perpage" id="perpage" value="5" size="5" />
			<input class="button" type="button" onclick="fetchPOP3()" value=" {lng p="execute"} " />
		</p>
	</div>
</fieldset>