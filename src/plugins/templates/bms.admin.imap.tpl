<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=imap&save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="common"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="6" valign="top" width="40"><img src="../plugins/templates/images/bms_common.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_greeting"}:</td>
				<td class="td2"><input class="form-control" type="text" name="imapgreeting" value="{if isset($bms_prefs.imapgreeting)}{text value=$bms_prefs.imapgreeting allowEmpty=true}{/if}" size="32" style="width:95%;" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_timeout"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="imap_timeout" value="{$bms_prefs.imap_timeout}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="seconds"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_idle_poll"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="imap_idle_poll" value="{$bms_prefs.imap_idle_poll}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="seconds"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_mysqlconnection"}:</td>
				<td class="td2">
					<label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="imap_mysqlclose"{if $bms_prefs.imap_mysqlclose==1} checked="checked"{/if} id="imap_mysqlclose" /><span class="form-check-label" for="imap_mysqlclose"><b>{lng p="bms_closewhenidle"}</b></span></label><br />
					
					<label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="imap_idle_mysqlclose"{if $bms_prefs.imap_idle_mysqlclose==1} checked="checked"{/if} id="imap_idle_mysqlclose" /><span class="form-check-label" for="imap_idle_mysqlclose"><b>{lng p="bms_closeduringidle"}</b></span></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_intfolders"}:</td>
				<td class="td2">
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="imap_intelligentfolders"{if $bms_prefs.imap_intelligentfolders==1} checked="checked"{/if} id="imap_intelligentfolders" /><span class="form-check-label" for="imap_intelligentfolders"><b>{lng p="show"}</b></span></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_autoexpunge"}:</td>
				<td class="td2">
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="imap_autoexpunge"{if $bms_prefs.imap_autoexpunge==1} checked="checked"{/if} id="imap_autoexpunge" /><span class="form-check-label" for="imap_autoexpunge"><b>{lng p="enable"}</b></span></label>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_foldernames"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="5" valign="top" width="40"><img src="../plugins/templates/images/bms_folders.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_folder_inbox"}:</td>
				<td class="td2"><input class="form-control" type="text" value="INBOX" disabled="disabled" size="32" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_folder_sent"}:</td>
				<td class="td2"><input class="form-control" type="text" name="imap_folder_sent" value="{if isset($bms_prefs.imap_folder_sent)}{text value=$bms_prefs.imap_folder_sent allowEmpty=true}{/if}" size="32" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_folder_spam"}:</td>
				<td class="td2"><input class="form-control" type="text" name="imap_folder_spam" value="{if isset($bms_prefs.imap_folder_spam)}{text value=$bms_prefs.imap_folder_spam allowEmpty=true}{/if}" size="32" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_folder_drafts"}:</td>
				<td class="td2"><input class="form-control" type="text" name="imap_folder_drafts" value="{if isset($bms_prefs.imap_folder_drafts)}{text value=$bms_prefs.imap_folder_drafts allowEmpty=true}{/if}" size="32" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_folder_trash"}:</td>
				<td class="td2"><input class="form-control" type="text" name="imap_folder_trash" value="{if isset($bms_prefs.imap_folder_trash)}{text value=$bms_prefs.imap_folder_trash allowEmpty=true}{/if}" size="32" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_imaplimit"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_imaplimit.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_user_choseimaplimit"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="user_choseimaplimit"{if $bms_prefs.user_choseimaplimit} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_imaplimit"}:</td>
				<td class="td2"><input class="form-control" type="text" name="imap_limit" value="{$bms_prefs.imap_limit}" size="6" />
								{lng p="emails"}
								<small>({lng p="bms_zerolimit"})</small></td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_apns"} <span class="badge bg-secondary-lt align-middle">{lng p="bms_legacy"}</span></legend>

		<div class="alert alert-warning" role="alert">
			<i class="ti ti-alert-triangle me-1"></i> {lng p="bms_apnslegacy"}
		</div>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="3" valign="top" width="40"><img src="../plugins/templates/images/bms_apns.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_apns"}:</td>
				<td class="td2">
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="apns_enable"{if $bms_prefs.apns_enable==1} checked="checked"{/if} id="apns_enable"{if !$apnsSet} disabled="disabled"{/if} /><span class="form-check-label" for="apns_enable"><b>{lng p="enable"}</b></span></label>
					{if !$apnsSet}
						<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
						{lng p="bms_apnsnote"}
					{/if}
				</td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_serverport"}:</td>
				<td class="td2">
					<div class="input-group input-group-wide">
						<input class="form-control" type="text" name="apns_host" value="{if isset($bms_prefs.apns_host)}{text value=$bms_prefs.apns_host allowEmpty=true}{/if}" placeholder="gateway.push.apple.com" />
						<span class="input-group-text">:</span>
						<input class="form-control" type="text" name="apns_port" value="{if isset($bms_prefs.apns_port)}{text value=$bms_prefs.apns_port allowEmpty=true}{/if}" style="max-width:6rem;" placeholder="2195" />
					</div>
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
					<button type="button" class="btn btn-outline-secondary btn-sm"  onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=imap&action=apns"}';" >{lng p="setedit"}</button>
				</td>
			</tr>
		</table>
	</fieldset>

	<div class="d-flex justify-content-between align-items-center mt-3 mb-2">
		<div class="text-secondary small"><i class="ti ti-alert-triangle me-1"></i> {lng p="bms_apnsqueuerestartnote"}</div>
		<button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> {lng p="save"}</button>
	</div>
</form>
