<form action="prefs.common.php?action=caching&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="caching"}</legend>
		
		<table>
			<tr>
				<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/ico_prefs_cache.png" border="0" alt="" width="32" height="32" /></td>
				<td>{lng p="cachemanager"}:</td>
				<td>{lng p="prefs"}:</td>
			</tr>
			<tr>
				<td valign="top" width="35%">
					<table>
						<tr>
							<td valign="top" width="20" align="center"><input type="radio" id="cache_disable" name="cache_type" value="0"{if $bm_prefs.cache_type==0} checked="checked"{/if} onchange="cachePrefs()" /></td>
							<td><label for="cache_disable"><b>{lng p="ce_disable"}</b></label><br />
								{lng p="ce_disable_desc"}</td>
						</tr>
						<tr>
							<td colspan="3">
								&nbsp;
							</td>
						</tr>
						<tr>
							<td valign="top" width="20" align="center"><input type="radio" id="cache_b1gmail" name="cache_type" value="1"{if $bm_prefs.cache_type==1} checked="checked"{/if} onchange="cachePrefs()" /></td>
							<td><label for="cache_b1gmail"><b>{lng p="ce_b1gmail"}</b></label><br />
								{lng p="ce_b1gmail_desc"}</td>
						</tr>
						<tr>
							<td colspan="3">
								&nbsp;
							</td>
						</tr>
						<tr>
							<td valign="top" width="20" align="center"><input type="radio" id="cache_memcache" name="cache_type" value="2"{if $bm_prefs.cache_type==2} checked="checked"{/if}{if !$memcache} disabled="disabled"{/if} onchange="cachePrefs()" /></td>
							<td><label for="cache_memcache"><b>{lng p="ce_memcache"}</b></label><br />
								{lng p="ce_memcache_desc"}</td>
						</tr>
					</table>
				</td>
				<td valign="top">
					<div id="prefs_0" style="display:{if $bm_prefs.cache_type!=0}none{/if};">
						<i>({lng p="none"})</i>
					</div>
					
					<div id="prefs_3" style="display:{if $bm_prefs.cache_type==0}none{/if};">
						<table>
							<tr>
								<td class="td1" width="180">{lng p="parseonly"}?</td>
								<td class="td2"><input type="checkbox" name="cache_parseonly"{if $bm_prefs.cache_parseonly=='yes'} checked="checked"{/if} /></td>
							</tr>
						</table>
					</div>
					
					<div id="prefs_1" style="display:{if $bm_prefs.cache_type!=1}none{/if};">
						<table>
							<tr>
								<td class="td1" width="180">{lng p="cachesize"}:</td>
								<td class="td2"><input type="text" name="filecache_size" value="{$bm_prefs.filecache_size/1024/1024}" size="6" />
												MB <!--<small>({lng p="inactiveonly"})</small>--></td>
							</tr>
						</table>
					</div>
					
					<div id="prefs_2" style="display:{if $bm_prefs.cache_type!=2}none{/if};">
						<table>
							<tr>
								<td class="td1" width="180">{lng p="persistent"}?</td>
								<td class="td2"><input type="checkbox" name="memcache_persistent"{if $bm_prefs.memcache_persistent=='yes'} checked="checked"{/if} /></td>
							</tr>
							<tr>
								<td class="td1" width="180">{lng p="servers"}:</td>
								<td class="td2">
									<textarea style="width:100%;height:80px;" name="memcache_servers">{text value=$bm_prefs.memcache_servers allowEmpty=true}</textarea>
									<small>{lng p="memcachesepby"}</small>
								</td>
							</tr>
						</table>
					</div>
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
	