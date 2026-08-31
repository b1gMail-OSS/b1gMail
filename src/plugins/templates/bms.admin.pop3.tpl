<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=pop3&save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="common"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="4" valign="top" width="40"><img src="../plugins/templates/images/bms_common.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_greeting"}:</td>
				<td class="td2"><input class="form-control" type="text" name="pop3greeting" value="{if isset($bms_prefs.pop3greeting)}{text value=$bms_prefs.pop3greeting allowEmpty=true}{/if}" size="32" style="width:95%;" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_timeout"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="pop3_timeout" value="{$bms_prefs.pop3_timeout}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="seconds"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_altpop3"}:</td>
				<td class="td2">
					<div class="input-group">
						<span class="input-group-text">
							<label class="form-check mb-0">
								<input class="form-check-input" type="checkbox" name="altpop3_enable" id="altpop3_enable"{if $bms_prefs.altpop3!=0} checked="checked"{/if} />
								<span class="form-check-label" for="altpop3_enable">{lng p="activate"}</span>
							</label>
						</span>
						<span class="input-group-text">{lng p="bms_toport"}</span>
						<input class="form-control" type="text" name="altpop3_port" value="{$bms_prefs.altpop3}" style="max-width:6rem;" />
					</div>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_folderstofetch"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_folders.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_user_chosepop3folders"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="user_chosepop3folders"{if $bms_prefs.user_chosepop3folders} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="folders"}:</td>
				<td class="td2">
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="pop3_folders[]" value="0" id="pop3_folders_0"{if isset($pop3Folders.0) && $pop3Folders.0} checked="checked"{/if} /><span class="form-check-label" for="pop3_folders_0">{lng p="bms_folder_inbox"}</span></label><br />

					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="pop3_folders[]" value="-4" id="pop3_folders_-4"{if isset($pop3Folders.m4) && $pop3Folders.m4} checked="checked"{/if} /><span class="form-check-label" for="pop3_folders_-4">{lng p="bms_folder_spam"}</span></label><br />

					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="pop3_folders[]" value="-5" id="pop3_folders_-5"{if isset($pop3Folders.m5) && $pop3Folders.m5} checked="checked"{/if} /><span class="form-check-label" for="pop3_folders_-5">{lng p="bms_folder_trash"}</span></label><br />

					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="pop3_folders[]" value="-128" id="pop3_folders_-128"{if isset($pop3Folders.m128) && $pop3Folders.m128} checked="checked"{/if} /><span class="form-check-label" for="pop3_folders_-128">{lng p="bms_userfolders"}</span></label><br />
				</td>
			</tr>
		</table>
	</fieldset>
	{include file=$bmsSaveFooterTpl}
</form>
