<form action="{$pageURL}&sid={$sid}&action=pop3&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="common"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="4" valign="top" width="40"><img src="../plugins/templates/images/bms_common.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_greeting"}:</td>
				<td class="td2"><input type="text" name="pop3greeting" value="{text value=$bms_prefs.pop3greeting allowEmpty=true}" size="32" style="width:95%;" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_timeout"}:</td>
				<td class="td2"><input type="text" name="pop3_timeout" value="{$bms_prefs.pop3_timeout}" size="6" />
								{lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_altpop3"}:</td>
				<td class="td2"><input type="checkbox" name="altpop3_enable"{if $bms_prefs.altpop3!=0} checked="checked"{/if} />
								{lng p="bms_toport"}
								<input type="text" name="altpop3_port" value="{$bms_prefs.altpop3}" size="6" /></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="bms_folderstofetch"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_folders.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_user_chosepop3folders"}?</td>
				<td class="td2"><input type="checkbox" name="user_chosepop3folders"{if $bms_prefs.user_chosepop3folders} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="folders"}:</td>
				<td class="td2">
					<input type="checkbox" name="pop3_folders[]" value="0" id="pop3_folders_0"{if $pop3Folders.0} checked="checked"{/if} />
					<label for="pop3_folders_0">{lng p="bms_folder_inbox"}</label><br />

					<input type="checkbox" name="pop3_folders[]" value="-4" id="pop3_folders_-4"{if $pop3Folders.m4} checked="checked"{/if} />
					<label for="pop3_folders_-4">{lng p="bms_folder_spam"}</label><br />

					<input type="checkbox" name="pop3_folders[]" value="-5" id="pop3_folders_-5"{if $pop3Folders.m5} checked="checked"{/if} />
					<label for="pop3_folders_-5">{lng p="bms_folder_trash"}</label><br />

					<input type="checkbox" name="pop3_folders[]" value="-128" id="pop3_folders_-128"{if $pop3Folders.m128} checked="checked"{/if} />
					<label for="pop3_folders_-128">{lng p="bms_userfolders"}</label><br />
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
