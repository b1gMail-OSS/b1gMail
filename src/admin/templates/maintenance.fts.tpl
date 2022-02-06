<fieldset>
	<legend>{lng p="buildindex"}</legend>
	
	<div id="buildForm">
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/searchindex32.png" border="0" alt="" width="32" height="32" /></td>
				<td valign="top">
					<p>
						{lng p="buildindex_desc"}
					</p>
				</td>
			</tr>
		</table>

		<p>
			<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
			{lng p="heavyop"}
		</p>

		<p align="right">
			{lng p="opsperpage"}:
			<input type="text" id="buildPerPage" value="50" size="5" />
			<input class="button" type="button" onclick="buildIndex()" value=" {lng p="execute"} " />
		</p>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="optimizeindex"}</legend>
	
	<div id="optimizeForm">
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/optimizeindex32.png" border="0" alt="" width="32" height="32" /></td>
				<td valign="top">
					<p>
						{lng p="optimizeindex_desc"}
					</p>
				</td>
			</tr>
		</table>

		<p>
			<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
			{lng p="heavyop"}
		</p>
		
		<p align="right">
			{lng p="opsperpage"}:
			<input type="text" id="optimizePerPage" value="5" size="5" />
			<input class="button" type="button" onclick="optimizeIndex()" value=" {lng p="execute"} " />
		</p>
	</div>
</fieldset>
