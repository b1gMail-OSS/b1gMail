<div class="bm-prefs-page bm-prefs-page-bms-userarea">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-arrows-exchange icon icon-sm" aria-hidden="true"></i>
		{lng p="bms_userarea"}
	</div>
</div>

<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">

{if $bms_prefs.user_showlogin}
<form action="#" onsubmit="return false;" method="post">
	{csrffield}
<h2>{lng p="bms_userlogin"}</h2>
<table class="listTable">
	<tr>
		<th class="listTableHead" colspan="2">{lng p="bms_userlogin"}</th>
	</tr>
	<tr>
		<td class="listTableLeft">&nbsp;</td>
		<td class="listTableRight text-secondary">
			{lng p="bms_userloginnote"}
		</td>
	</tr>
	{if $havePOP3}<tr>
		<td class="listTableLeft">{lng p="bms_pop3server"}:</td>
		<td class="listTableRight">
			{text value=$bms_prefs.user_pop3server}
			<span class="text-secondary">({lng p="port"}: {$bms_prefs.user_pop3port}{if $bms_prefs.user_pop3ssl}, {lng p="bms_sslport"}{/if})</span>
		</td>
	</tr>{/if}
	{if $haveIMAP}<tr>
		<td class="listTableLeft">{lng p="bms_imapserver"}:</td>
		<td class="listTableRight">
			{text value=$bms_prefs.user_imapserver}
			<span class="text-secondary">({lng p="port"}: {$bms_prefs.user_imapport}{if $bms_prefs.user_imapssl}, {lng p="bms_sslport"}{/if})</span>
		</td>
	</tr>{/if}
	{if $haveSMTP}<tr>
		<td class="listTableLeft">{lng p="bms_smtpserver"}:</td>
		<td class="listTableRight">
			{text value=$bms_prefs.user_smtpserver}
			<span class="text-secondary">({lng p="port"}: {$bms_prefs.user_smtpport}{if $bms_prefs.user_smtpssl}, {lng p="bms_sslport"}{/if})</span>
		</td>
	</tr>{/if}
	<tr>
		<td class="listTableLeft">{lng p="username"}:</td>
		<td class="listTableRight">{$username}</td>
	</tr>
	<tr>
		<td class="listTableLeft">{lng p="password"}:</td>
		<td class="listTableRight"><em class="text-secondary">{lng p="bms_pwnote"}</em></td>
	</tr>
</table>
</form>
{/if}

{if $havePOP3&&$bms_prefs.user_chosepop3folders}
<form action="{sessionurl file='prefs.php' params='action=bms_userarea'}" method="post">
	{csrffield}
	<input type="hidden" name="do" value="savePOP3Folders" />
	<h2>{lng p="bms_folderstofetch"}</h2>
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2">{lng p="bms_folderstofetch"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight text-secondary">{lng p="bms_folderstofetchnote"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label>{lng p="folders"}:</label></td>
			<td class="listTableRight">
				<div class="border rounded p-3 bg-body-tertiary bm-bms-folder-list" style="display:inline-block; min-width:16rem; max-height:9rem; overflow-y:auto;">
					<div class="mb-1">
						<label class="form-check mb-0">
							<input type="checkbox" class="form-check-input" name="pop3_folders[]" value="0" id="pop3_folders_0"{if $pop3Folders.0} checked="checked"{/if} />
							<span class="form-check-label"><i class="ti ti-inbox icon icon-sm text-secondary me-1" aria-hidden="true"></i>{lng p="inbox"}</span>
						</label>
					</div>
					<div class="mb-1">
						<label class="form-check mb-0">
							<input type="checkbox" class="form-check-input" name="pop3_folders[]" value="-4" id="pop3_folders_-4"{if $pop3Folders.m4} checked="checked"{/if} />
							<span class="form-check-label"><i class="ti ti-ban icon icon-sm text-secondary me-1" aria-hidden="true"></i>{lng p="spam"}</span>
						</label>
					</div>
					<div class="mb-1">
						<label class="form-check mb-0">
							<input type="checkbox" class="form-check-input" name="pop3_folders[]" value="-5" id="pop3_folders_-5"{if $pop3Folders.m5} checked="checked"{/if} />
							<span class="form-check-label"><i class="ti ti-trash icon icon-sm text-secondary me-1" aria-hidden="true"></i>{lng p="trash"}</span>
						</label>
					</div>
					<div class="mb-1">
						<label class="form-check mb-0">
							<input type="checkbox" class="form-check-input" name="pop3_folders[]" value="-128" id="pop3_folders_-128"{if $pop3Folders.m128} checked="checked"{/if} onchange="EBID('userFolders').style.display=this.checked?'none':'';" />
							<span class="form-check-label"><i class="ti ti-folders icon icon-sm text-secondary me-1" aria-hidden="true"></i>{lng p="bms_userfolders"}</span>
						</label>
					</div>
					<div style="display:{if $pop3Folders.m128}none{/if};" id="userFolders">
						{foreach from=$folderList key=folderID item=folderTitle}{if $folderID>0}
							<div class="mb-1">
								<label class="form-check mb-0">
									<input type="checkbox" class="form-check-input" name="pop3_folders[]" value="{$folderID}" id="pop3_folders_{$folderID}"{if $pop3Folders.$folderID} checked="checked"{/if} />
									<span class="form-check-label font-monospace"><i class="ti ti-folder icon icon-sm text-secondary me-1" aria-hidden="true"></i>{$folderTitle}</span>
								</label>
							</div>
						{/if}{/foreach}
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="btn btn-primary" value="{lng p="save"}" />
			</td>
		</tr>
	</table>
</form>
{/if}

{if $haveIMAP&&$bms_prefs.user_choseimaplimit}
<form action="{sessionurl file='prefs.php' params='action=bms_userarea'}" method="post">
	{csrffield}
	<input type="hidden" name="do" value="saveIMAPLimit" />
	<h2>{lng p="bms_imaplimit"}</h2>
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2">{lng p="bms_imaplimit"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight text-secondary">{lng p="bms_imaplimitnote"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="imapLimit">{lng p="bms_limit"}:</label></td>
			<td class="listTableRight">
				<select name="imapLimit" id="imapLimit" class="form-select form-select-sm" style="width:auto; min-width:12rem;">
					{if $imapLimit!=0&&$imapLimit!=100&&$imapLimit!=500&&$imapLimit!=1000&&$imapLimit!=2000&&$imapLimit!=5000&&$imapLimit!=10000}<option value="{$bms_prefs.imap_limit}"{if $imapLimit==$bms_prefs.imap_limit} selected="selected"{/if}>{lng p="default"} ({$bms_prefs.imap_limit})</option>{/if}
					<option value="0"{if $imapLimit==0} selected="selected"{/if}>{lng p="bms_nolimit"}</option>
					<option value="100"{if $imapLimit==100} selected="selected"{/if}>100 {lng p="bms_emails"}</option>
					<option value="500"{if $imapLimit==500} selected="selected"{/if}>500 {lng p="bms_emails"}</option>
					<option value="1000"{if $imapLimit==1000} selected="selected"{/if}>1.000 {lng p="bms_emails"}</option>
					<option value="2000"{if $imapLimit==2000} selected="selected"{/if}>2.000 {lng p="bms_emails"}</option>
					<option value="5000"{if $imapLimit==5000} selected="selected"{/if}>5.000 {lng p="bms_emails"}</option>
					<option value="10000"{if $imapLimit==10000} selected="selected"{/if}>10.000 {lng p="bms_emails"}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="btn btn-primary" value="{lng p="save"}" />
			</td>
		</tr>
	</table>
</form>
{/if}

</div></div>
</div>
