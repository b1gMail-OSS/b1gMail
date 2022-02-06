<form action="{$pageURL}&sid={$sid}&action=imap&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="common"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="6" valign="top" width="40"><img src="../plugins/templates/images/bms_common.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_greeting"}:</td>
				<td class="td2"><input type="text" name="imapgreeting" value="{text value=$bms_prefs.imapgreeting allowEmpty=true}" size="32" style="width:95%;" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_timeout"}:</td>
				<td class="td2"><input type="text" name="imap_timeout" value="{$bms_prefs.imap_timeout}" size="6" />
								{lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_idle_poll"}:</td>
				<td class="td2"><input type="text" name="imap_idle_poll" value="{$bms_prefs.imap_idle_poll}" size="6" />
								{lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_mysqlconnection"}:</td>
				<td class="td2">
					<input type="checkbox" name="imap_mysqlclose"{if $bms_prefs.imap_mysqlclose==1} checked="checked"{/if} id="imap_mysqlclose" />
					<label for="imap_mysqlclose"><b>{lng p="bms_closewhenidle"}</b></label><br />
					
					<input type="checkbox" name="imap_idle_mysqlclose"{if $bms_prefs.imap_idle_mysqlclose==1} checked="checked"{/if} id="imap_idle_mysqlclose" />
					<label for="imap_idle_mysqlclose"><b>{lng p="bms_closeduringidle"}</b></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_intfolders"}:</td>
				<td class="td2">
					<input type="checkbox" name="imap_intelligentfolders"{if $bms_prefs.imap_intelligentfolders==1} checked="checked"{/if} id="imap_intelligentfolders" />
					<label for="imap_intelligentfolders"><b>{lng p="show"}</b></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_autoexpunge"}:</td>
				<td class="td2">
					<input type="checkbox" name="imap_autoexpunge"{if $bms_prefs.imap_autoexpunge==1} checked="checked"{/if} id="imap_autoexpunge" />
					<label for="imap_autoexpunge"><b>{lng p="enable"}</b></label>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="bms_foldernames"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="5" valign="top" width="40"><img src="../plugins/templates/images/bms_folders.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_folder_inbox"}:</td>
				<td class="td2"><input type="text" value="INBOX" disabled="disabled" size="32" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_folder_sent"}:</td>
				<td class="td2"><input type="text" name="imap_folder_sent" value="{text value=$bms_prefs.imap_folder_sent allowEmpty=true}" size="32" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_folder_spam"}:</td>
				<td class="td2"><input type="text" name="imap_folder_spam" value="{text value=$bms_prefs.imap_folder_spam allowEmpty=true}" size="32" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_folder_drafts"}:</td>
				<td class="td2"><input type="text" name="imap_folder_drafts" value="{text value=$bms_prefs.imap_folder_drafts allowEmpty=true}" size="32" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_folder_trash"}:</td>
				<td class="td2"><input type="text" name="imap_folder_trash" value="{text value=$bms_prefs.imap_folder_trash allowEmpty=true}" size="32" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="bms_imaplimit"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_imaplimit.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_user_choseimaplimit"}?</td>
				<td class="td2"><input type="checkbox" name="user_choseimaplimit"{if $bms_prefs.user_choseimaplimit} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_imaplimit"}:</td>
				<td class="td2"><input type="text" name="imap_limit" value="{$bms_prefs.imap_limit}" size="6" />
								{lng p="emails"}
								<small>({lng p="bms_zerolimit"})</small></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="bms_apns"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="3" valign="top" width="40"><img src="../plugins/templates/images/bms_apns.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_apns"}:</td>
				<td class="td2">
					<input type="checkbox" name="apns_enable"{if $bms_prefs.apns_enable==1} checked="checked"{/if} id="apns_enable"{if !$apnsSet} disabled="disabled"{/if} />
					<label for="apns_enable"><b>{lng p="enable"}</b></label>
					{if !$apnsSet}
						<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
						{lng p="bms_apnsnote"}
					{/if}
				</td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_serverport"}:</td>
				<td class="td2">
					<input type="text" name="apns_host" value="{text value=$bms_prefs.apns_host allowEmpty=true}" size="32" />
					:
					<input type="text" name="apns_port" value="{text value=$bms_prefs.apns_port allowEmpty=true}" size="6" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_pushcertificate"}:</td>
				<td class="td2">
					<img src="{$tpldir}images/{if !$apnsSet||!$apnsValid}delete{else}yes{/if}.png" border="0" alt="" width="16" height="16" align="absmiddle" />
					{if $apnsSet}
						{lng p="bms_setvaliduntil"}
						{date timestamp=$apnsValidUntil dayonly=true}
					{else}
						{lng p="bms_notset"}
					{/if}
					<input class="button" type="button" value=" {lng p="setedit"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=imap&do=apns';" />
				</td>
			</tr>
		</table>
	</fieldset>

	<p>
		<div style="float:left" class="buttons">
			<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
			{lng p="bms_apnsqueuerestartnote"}
		</div>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
