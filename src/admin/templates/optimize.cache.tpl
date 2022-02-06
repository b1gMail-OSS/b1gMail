{if $fileCache}<fieldset>
	<legend>{lng p="filecache"}</legend>
	
	<form action="optimize.php?action=cache&do=cleanupFileCache&sid={$sid}" method="post" onsubmit="spin(this)">
		<p>{lng p="filecachedesc"}</p>
		
		<table>
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="{$tpldir}images/cache.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="count"}:</td>
				<td class="td2">{$cacheFileCount}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="size"}:</td>
				<td class="td2">{size bytes=$cacheFileSize}</td>
			</tr>
		</table>
		
		<p align="right">
			<input class="button" type="submit" value=" {lng p="emptycache"} " />
		</p>
	</form>
</fieldset>{/if}

<fieldset>
	<legend>{lng p="rebuildcaches"}</legend>
	
	<div id="form">
		<p>
			{lng p="rebuild_desc"}
		</p>
		
		<blockquote>
			<table>
				<tr>
					<td width="20" valign="top"><input type="radio" id="rebuild_usersizes" name="rebuild" value="usersizes" checked="checked" /></td>
					<td><label for="rebuild_usersizes"><b>{lng p="usersizes_cache"}</b></label><br />
						{lng p="usersizes_desc"}<br /><br /></td>
				</tr>
				<tr>
					<td width="20" valign="top"><input type="radio" id="rebuild_disksizes" name="rebuild" value="disksizes" /></td>
					<td><label for="rebuild_disksizes"><b>{lng p="disksizes_cache"}</b></label><br />
						{lng p="disksizes_desc"}<br /><br /></td>
				</tr>
				<tr>
					<td width="20" valign="top"><input type="radio" id="rebuild_mailsizes" name="rebuild" value="mailsizes" /></td>
					<td><label for="rebuild_mailsizes"><b>{lng p="emailsizes_cache"}</b></label><br />
						{lng p="emailsizes_desc"}<br /><br /></td>
				</tr>
			</table>
		</blockquote>
		
		<p>
			<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
			{lng p="heavyop"}
		</p>
		
		<p align="right">
			{lng p="opsperpage"}:
			<input type="text" name="perpage" id="perpage" value="50" size="5" />
			<input class="button" type="button" onclick="rebuildCaches()" value=" {lng p="execute"} " />
		</p>
	</div>
</fieldset>