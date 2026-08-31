<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=common&save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_userarea"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="4" valign="top" width="40"><img src="{$tpldir}images/ico_users.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_usershowlogin"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="user_showlogin"{if $bms_prefs.user_showlogin} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_pop3server"}:</td>
				<td class="td2">
					<div class="input-group input-group-wide">
						<input class="form-control" type="text" name="user_pop3server" value="{text value=$bms_prefs.user_pop3server allowEmpty=true}" placeholder="mail.example.com" />
						<span class="input-group-text">:</span>
						<input class="form-control" type="text" name="user_pop3port" value="{text value=$bms_prefs.user_pop3port}" style="max-width:6rem;" placeholder="110" />
						<span class="input-group-text">
							<label class="form-check mb-0">
								<input class="form-check-input" type="checkbox" name="user_pop3ssl" id="user_pop3ssl"{if $bms_prefs.user_pop3ssl} checked="checked"{/if} />
								<span class="form-check-label" for="user_pop3ssl">SSL</span>
							</label>
						</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_smtpserver"}:</td>
				<td class="td2">
					<div class="input-group input-group-wide">
						<input class="form-control" type="text" name="user_smtpserver" value="{text value=$bms_prefs.user_smtpserver allowEmpty=true}" placeholder="mail.example.com" />
						<span class="input-group-text">:</span>
						<input class="form-control" type="text" name="user_smtpport" value="{text value=$bms_prefs.user_smtpport}" style="max-width:6rem;" placeholder="25" />
						<span class="input-group-text">
							<label class="form-check mb-0">
								<input class="form-check-input" type="checkbox" name="user_smtpssl" id="user_smtpssl"{if $bms_prefs.user_smtpssl} checked="checked"{/if} />
								<span class="form-check-label" for="user_smtpssl">SSL</span>
							</label>
						</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_imapserver"}:</td>
				<td class="td2">
					<div class="input-group input-group-wide">
						<input class="form-control" type="text" name="user_imapserver" value="{text value=$bms_prefs.user_imapserver allowEmpty=true}" placeholder="mail.example.com" />
						<span class="input-group-text">:</span>
						<input class="form-control" type="text" name="user_imapport" value="{text value=$bms_prefs.user_imapport}" style="max-width:6rem;" placeholder="143" />
						<span class="input-group-text">
							<label class="form-check mb-0">
								<input class="form-check-input" type="checkbox" name="user_imapssl" id="user_imapssl"{if $bms_prefs.user_imapssl} checked="checked"{/if} />
								<span class="form-check-label" for="user_imapssl">SSL</span>
							</label>
						</span>
					</div>
				</td>
			</tr>
	</table>
	</fieldset>

	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_logging"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="5" valign="top" width="40"><img src="../plugins/templates/images/bms_logging.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_logging_debug"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="loglevel[8]"{if ($bms_prefs.loglevel&8)!=0} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_logging_notices"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="loglevel[1]"{if ($bms_prefs.loglevel&1)!=0} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_logging_warnings"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="loglevel[2]"{if ($bms_prefs.loglevel&2)!=0} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_logging_errors"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="loglevel[4]"{if ($bms_prefs.loglevel&4)!=0} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_logging_autodelete"}:</td>
				<td class="td2">
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" id="logs_autodelete" name="logs_autodelete"{if $bms_prefs.logs_autodelete} checked="checked"{/if} /><span class="form-check-label" for="logs_autodelete">{lng p="bms_enableolder"}</span></label>
					<input class="form-control" type="text" name="logs_autodelete_days" value="{text value=$bms_prefs.logs_autodelete_days}" size="4" />
					{lng p="days"}<br />
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" id="logs_autodelete_archive" name="logs_autodelete_archive"{if $bms_prefs.logs_autodelete_archive} checked="checked"{/if} /><span class="form-check-label" for="logs_autodelete_archive">{lng p="savearc"}</span></label>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_failban"}</legend>

		<table width="100%">
			<tr>
				<td align="left" rowspan="5" valign="top" width="40"><img src="../plugins/templates/images/bms_untrusted.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_fb_activatefor"}:</td>
				<td class="td2">
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="failban_types[1]" id="failban_types_1"{if ($bms_prefs.failban_types&1)!=0} checked="checked"{/if} /><span class="form-check-label" for="failban_types_1">{lng p="bms_fb_1"}</span></label><br />
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="failban_types[2]" id="failban_types_2"{if ($bms_prefs.failban_types&2)!=0} checked="checked"{/if} /><span class="form-check-label" for="failban_types_2">{lng p="bms_fb_2"}</span></label><br />
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="failban_types[4]" id="failban_types_4"{if ($bms_prefs.failban_types&4)!=0} checked="checked"{/if} /><span class="form-check-label" for="failban_types_4">{lng p="bms_fb_4"}</span></label><br />
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="failban_types[8]" id="failban_types_8"{if ($bms_prefs.failban_types&8)!=0} checked="checked"{/if} /><span class="form-check-label" for="failban_types_8">{lng p="bms_fb_8"}</span></label>{*<br />
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="failban_types[16]" id="failban_types_16"{if ($bms_prefs.failban_types&16)!=0} checked="checked"{/if} /><span class="form-check-label" for="failban_types_16">{lng p="bms_fb_16"}</span></label>*}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_fb_attempts"}:</td>
				<td class="td2"><input class="form-control" type="text" size="6" name="failban_attempts" value="{text value=$bms_prefs.failban_attempts}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_fb_time"}:</td>
				<td class="td2"><input class="form-control" type="text" size="6" name="failban_time" value="{text value=$bms_prefs.failban_time}" /> {lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_fb_bantime"}:</td>
				<td class="td2"><input class="form-control" type="text" size="6" name="failban_bantime" value="{text value=$bms_prefs.failban_bantime}" /> {lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_list"}:</td>
				<td class="td2">
					{$banCount} {lng p="entries"}
					<button type="button" class="btn btn-outline-secondary btn-sm"{if $banCount==0} disabled="disabled"{/if} onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=common&action=banlist"}';">{lng p="show"}</button>
					{if $banCount>0}
					<button type="submit" class="btn btn-outline-secondary btn-sm" form="bmsResetBanForm">{lng p="reset"}</button>
					{else}
					<button type="button" class="btn btn-outline-secondary btn-sm" disabled="disabled">{lng p="reset"}</button>
					{/if}
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_tls_ssl"}</legend>

		<table width="100%">
			<tr>
				<td align="left" valign="top" width="40" rowspan="4"><img src="{$tpldir}images/ico_prefs_ssl.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_ssl_cipher_list"}:</td>
				<td class="td2"><input class="form-control" type="text" name="ssl_cipher_list" value="{text value=$bms_prefs.ssl_cipher_list allowEmpty=true}" style="width:95%;" /></td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_ssl_ciphersuites"}:</td>
				<td class="td2"><input class="form-control" type="text" name="ssl_ciphersuites" value="{text value=$bms_prefs.ssl_ciphersuites allowEmpty=true}" style="width:95%;" /></td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_ssl_minmaxversion"}:</td>
				<td class="td2">{include file=$bmsSslMinMaxTpl}</td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_tlsarecord"}:</td>
				<td class="td2"><div id="tlsaRecord"><button{if !$queueRunning||($bms_prefs.core_features&1)==0} disabled="disabled"{/if} type="button" class="btn btn-outline-secondary btn-sm" onclick="bms_showTLSARecord()">{lng p="show"}</button></div></td>
			</tr>
		</table>
	</fieldset>
	{include file=$bmsSaveFooterTpl}
</form>

<form id="bmsResetBanForm" method="post" action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=common"}" style="display:none;" aria-hidden="true">
	{csrffield}
	<input type="hidden" name="resetBanList" value="1" />
</form>

<script>
{literal}<!--
	function bms_showTLSARecord()
	{
		MakeXMLRequest('{/literal}{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=common&action=tlsaRecord"}{literal}',
			function(e)
			{
				if(e.readyState == 4)
				{
					var text = e.responseText;
					if(text.length > 0)
					{
						var div = EBID('tlsaRecord');
						while(div.firstChild) div.removeChild(div.firstChild);

						var field = document.createElement('input');
						field.style.width 	= '95%';
						field.readOnly 		= true;
						field.value 		= text;
						field.onclick 		= function() { field.select(); };
						EBID('tlsaRecord').appendChild(field);

						field.select();
					}
				}
			});
	}
//-->{/literal}
</script>
