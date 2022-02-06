<form action="prefs.email.php?save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="common"}</legend>
	
		<table>
			<tr>
				<td width="40" valign="top" rowspan="3"><img src="{$tpldir}images/ico_prefs_email.png" border="0" alt="" width="32" height="32" /></td>				
				<td class="td1" width="220">{lng p="storein"}:</td>
				<td class="td2"><select name="blobstorage_provider">
					<option value="0"{if $bm_prefs.blobstorage_provider==0} selected="selected"{/if}>{lng p="filesystem"} ({lng p="separatefiles"})</option>
					<option value="1"{if $bm_prefs.blobstorage_provider==1} selected="selected"{/if}{if !$bsUserDBAvailable} disabled="disabled"{/if}>{lng p="filesystem"} ({lng p="userdb"})</option>
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="blobcompress"}?</td>
				<td class="td2">
					<label>
						<input name="blobstorage_compress"{if $bm_prefs.blobstorage_compress=='yes'} checked="checked"{/if} type="checkbox" />
						{lng p="enable"}
					</label>
					<small style="margin-left:1em;color:#666;">{lng p="onlyfor"} &quot;{lng p="filesystem"} ({lng p="userdb"})&quot;</small>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="ftsearch"}:</td>
				<td class="td2">
					<label>
						<input name="fts_bg_indexing"{if $bm_prefs.fts_bg_indexing=='yes'} checked="checked"{/if} type="checkbox" />
						{lng p="fts_bg_indexing"}
					</label>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<p>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
