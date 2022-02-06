<fieldset>
	<legend>{lng p="tempfiles"}</legend>
	
	<form action="optimize.php?action=filesystem&do=cleanupTempFiles&sid={$sid}" method="post" onsubmit="spin(this)">
		<p>{lng p="tempdesc"}</p>
		
		<table>
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="{$tpldir}images/tempfiles.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="count"}:</td>
				<td class="td2">{$tempFileCount}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="size"}:</td>
				<td class="td2">{size bytes=$tempFileSize}</td>
			</tr>
		</table>
		
		<p align="right">
			<input class="button" type="submit" value=" {lng p="cleanup"} " />
		</p>
	</form>
</fieldset>

{if $haveSQLite3}<fieldset>
	<legend>{lng p="rebuildblobstor"}</legend>
	
	<div id="buildForm">
		<p>
			{lng p="rebuildblobstor_desc"}
		</p>

		<blockquote>
			<table>
				<tr>
					<td width="20" valign="top"><input type="radio" id="rebuild_email" name="rebuild" value="email" checked="checked" /></td>
					<td><label for="rebuild_email"><b>{lng p="rbbs_email"}</b></label></td>
				</tr>
				<tr>
					<td valign="top"><input type="radio" id="rebuild_webdisk" name="rebuild" value="webdisk" /></td>
					<td><label for="rebuild_webdisk"><b>{lng p="rbbs_webdisk"}</b></label></td>
				</tr>
			</table>
		</blockquote>

		<p>
			<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
			{lng p="heavyop"}
		</p>

		<p align="right">
			{lng p="opsperpage"}:
			<input type="text" id="buildPerPage" value="50" size="5" />
			<input class="button" type="button" onclick="rebuildBlobStor()" value=" {lng p="execute"} " />
		</p>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="userdbvacuum"}</legend>
	
	<div id="vacuumForm">
		<p>
			{lng p="userdbvacuum_desc"}<br /><br />
		</p>

		<p>
			<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
			{lng p="heavyop"}
		</p>

		<p align="right">
			{lng p="opsperpage"}:
			<input type="text" id="vacuumPerPage" value="5" size="5" />
			<input class="button" type="button" onclick="vacuumBlobStor()" value=" {lng p="execute"} " />
		</p>
	</div>
</fieldset>{/if}
