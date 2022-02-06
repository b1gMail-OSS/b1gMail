{if $_tplname=='modern'}
<div id="contentHeader">
	<div class="left">
		<i class="fa fa-exchange" aria-hidden="true"></i>
		{lng p="bms_userarea"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
{else}
<h1><<i class="fa fa-exchange" aria-hidden="true"></i> {lng p="bms_userarea"}</h1>
{/if}

{if $bms_prefs.user_showlogin}
<table class="listTable">
	<tr>
		<th class="listTableHead" colspan="2"> {lng p="bms_userlogin"}</th>
	</tr>

	<tr>
		<td class="listTableLeft">&nbsp;</td>
		<td class="listTableRight">
			{lng p="bms_userloginnote"}
		</td>
	</tr>

	{if $havePOP3}<tr>
		<td class="listTableLeft">{lng p="bms_pop3server"}:</td>
		<td class="listTableRight">
			{text value=$bms_prefs.user_pop3server}
			({lng p="port"}: {$bms_prefs.user_pop3port}{if $bms_prefs.user_pop3ssl}, {lng p="bms_sslport"}{/if})
		</td>
	</tr>{/if}
	{if $haveIMAP}<tr>
		<td class="listTableLeft">{lng p="bms_imapserver"}:</td>
		<td class="listTableRight">
			{text value=$bms_prefs.user_imapserver}
			({lng p="port"}: {$bms_prefs.user_imapport}{if $bms_prefs.user_imapssl}, {lng p="bms_sslport"}{/if})
		</td>
	</tr>{/if}
	{if $haveSMTP}<tr>
		<td class="listTableLeft">{lng p="bms_smtpserver"}:</td>
		<td class="listTableRight">
			{text value=$bms_prefs.user_smtpserver}
			({lng p="port"}: {$bms_prefs.user_smtpport}{if $bms_prefs.user_smtpssl}, {lng p="bms_sslport"}{/if})
		</td>
	</tr>{/if}
	<tr>
		<td class="listTableLeft">{lng p="username"}:</td>
		<td class="listTableRight">
			{$username}
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">{lng p="password"}:</td>
		<td class="listTableRight">
			<i>{lng p="bms_pwnote"}</i>
		</td>
	</tr>
</table>
<br />
{/if}

{if $havePOP3&&$bms_prefs.user_chosepop3folders}
<form action="prefs.php?action=bms_userarea&sid={$sid}" method="post">
	<input type="hidden" name="do" value="savePOP3Folders" />
		
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="bms_folderstofetch"}</th>
		</tr>

		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				{lng p="bms_folderstofetchnote"}
			</td>
		</tr>

		<tr>
			<td class="listTableLeft">{lng p="folders"}:</td>
			<td class="listTableRight">
				<div style="border: 1px solid #ccc; display: inline-block; min-height: 80px; max-height: 120px; overflow-y: scroll; padding: 5px; margin-bottom: 5px; padding-right: 20px; background-color: #FAFAFA;">
					<div>
						<input type="checkbox" name="pop3_folders[]" value="0" id="pop3_folders_0"{if $pop3Folders.0} checked="checked"{/if} style="vertical-align:middle;" />
						<label for="pop3_folders_0">
							<i class="fa fa-inbox" aria-hidden="true"></i>
							{lng p="inbox"}
						</label>
					</div>
					<div>
						<input type="checkbox" name="pop3_folders[]" value="-4" id="pop3_folders_-4"{if $pop3Folders.m4} checked="checked"{/if} style="vertical-align:middle;" />
						<label for="pop3_folders_-4">
							<i class="fa fa-ban" aria-hidden="true"></i>
							{lng p="spam"}
						</label>
					</div>
					<div>
						<input type="checkbox" name="pop3_folders[]" value="-5" id="pop3_folders_-5"{if $pop3Folders.m5} checked="checked"{/if} style="vertical-align:middle;" />
						<label for="pop3_folders_-5">
							<i class="fa fa-trash-o" aria-hidden="true"></i>
							{lng p="trash"}
						</label>
					</div>
					<div>
						<input type="checkbox" name="pop3_folders[]" value="-128" id="pop3_folders_-128"{if $pop3Folders.m128} checked="checked"{/if} style="vertical-align:middle;" onchange="EBID('userFolders').style.display=this.checked?'none':'';" />
						<label for="pop3_folders_-128">
							<i class="fa fa-folder-open-o" aria-hidden="true"></i>
							{lng p="bms_userfolders"}
						</label>
					</div>
					<div style="display:{if $pop3Folders.m128}none{/if};" id="userFolders">
						{foreach from=$folderList key=folderID item=folderTitle}{if $folderID>0}
							<div>
								<input type="checkbox" name="pop3_folders[]" value="{$folderID}" id="pop3_folders_{$folderID}"{if $pop3Folders.$folderID} checked="checked"{/if} style="vertical-align:middle;" />
								<label for="pop3_folders_{$folderID}" style="font-family:courier;">
									<i class="fa fa-folder-open-o" aria-hidden="true"></i>
									{$folderTitle}
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
				<input type="submit" class="primary" value="{lng p="save"}" />
			</td>
		</tr>
	</table>
</form>
<br />
{/if}

{if $haveIMAP&&$bms_prefs.user_choseimaplimit}
<form action="prefs.php?action=bms_userarea&sid={$sid}" method="post">
	<input type="hidden" name="do" value="saveIMAPLimit" />
		
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="bms_imaplimit"}</th>
		</tr>

		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				{lng p="bms_imaplimitnote"}
			</td>
		</tr>

		<tr>
			<td class="listTableLeft">{lng p="bms_limit"}:</td>
			<td class="listTableRight">
				<select name="imapLimit">
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
				<input type="submit" class="primary" value="{lng p="save"}" />
			</td>
		</tr>
	</table>
</form>
<br />
{/if}

{if $_tplname=='modern'}
</div></div>
{/if}
