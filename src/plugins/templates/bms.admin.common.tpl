<form action="{$pageURL}&sid={$sid}&action=common&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="bms_userarea"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="4" valign="top" width="40"><img src="{$tpldir}images/ico_users.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_usershowlogin"}?</td>
				<td class="td2">
					<input type="checkbox" name="user_showlogin"{if $bms_prefs.user_showlogin} checked="checked"{/if} />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_pop3server"}:</td>
				<td class="td2"><input type="text" name="user_pop3server" value="{text value=$bms_prefs.user_pop3server allowEmpty=true}" size="32" />
					:
					<input type="text" name="user_pop3port" value="{text value=$bms_prefs.user_pop3port}" size="5" />
					<input type="checkbox" name="user_pop3ssl" id="user_pop3ssl"{if $bms_prefs.user_pop3ssl} checked="checked"{/if} />
					<label for="user_pop3ssl">SSL</label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_smtpserver"}:</td>
				<td class="td2"><input type="text" name="user_smtpserver" value="{text value=$bms_prefs.user_smtpserver allowEmpty=true}" size="32" />
					:
					<input type="text" name="user_smtpport" value="{text value=$bms_prefs.user_smtpport}" size="5" />
					<input type="checkbox" name="user_smtpssl" id="user_smtpssl"{if $bms_prefs.user_smtpssl} checked="checked"{/if} />
					<label for="user_smtpssl">SSL</label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_imapserver"}:</td>
				<td class="td2"><input type="text" name="user_imapserver" value="{text value=$bms_prefs.user_imapserver allowEmpty=true}" size="32" />
					:
					<input type="text" name="user_imapport" value="{text value=$bms_prefs.user_imapport}" size="5" />
					<input type="checkbox" name="user_imapssl" id="user_imapssl"{if $bms_prefs.user_imapssl} checked="checked"{/if} />
					<label for="user_imapssl">SSL</label></td>
			</tr>
	</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="bms_logging"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="5" valign="top" width="40"><img src="../plugins/templates/images/bms_logging.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_logging_debug"}?</td>
				<td class="td2"><input type="checkbox" name="loglevel[8]"{if ($bms_prefs.loglevel&8)!=0} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_logging_notices"}?</td>
				<td class="td2"><input type="checkbox" name="loglevel[1]"{if ($bms_prefs.loglevel&1)!=0} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_logging_warnings"}?</td>
				<td class="td2"><input type="checkbox" name="loglevel[2]"{if ($bms_prefs.loglevel&2)!=0} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_logging_errors"}?</td>
				<td class="td2"><input type="checkbox" name="loglevel[4]"{if ($bms_prefs.loglevel&4)!=0} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_logging_autodelete"}:</td>
				<td class="td2">
					<input type="checkbox" id="logs_autodelete" name="logs_autodelete"{if $bms_prefs.logs_autodelete} checked="checked"{/if} />
					<label for="logs_autodelete">{lng p="bms_enableolder"}</label>
					<input type="text" name="logs_autodelete_days" value="{text value=$bms_prefs.logs_autodelete_days}" size="4" />
					{lng p="days"}<br />
					<input type="checkbox" id="logs_autodelete_archive" name="logs_autodelete_archive"{if $bms_prefs.logs_autodelete_archive} checked="checked"{/if} />
					<label for="logs_autodelete_archive">{lng p="savearc"}</label>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="bms_failban"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="5" valign="top" width="40"><img src="../plugins/templates/images/bms_untrusted.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_fb_activatefor"}:</td>
				<td class="td2">
					<input type="checkbox" name="failban_types[1]" id="failban_types_1"{if ($bms_prefs.failban_types&1)!=0} checked="checked"{/if} />
						<label for="failban_types_1">{lng p="bms_fb_1"}</label><br />
					<input type="checkbox" name="failban_types[2]" id="failban_types_2"{if ($bms_prefs.failban_types&2)!=0} checked="checked"{/if} />
						<label for="failban_types_2">{lng p="bms_fb_2"}</label><br />
					<input type="checkbox" name="failban_types[4]" id="failban_types_4"{if ($bms_prefs.failban_types&4)!=0} checked="checked"{/if} />
						<label for="failban_types_4">{lng p="bms_fb_4"}</label><br />
					<input type="checkbox" name="failban_types[8]" id="failban_types_8"{if ($bms_prefs.failban_types&8)!=0} checked="checked"{/if} />
						<label for="failban_types_8">{lng p="bms_fb_8"}</label>{*<br />
					<input type="checkbox" name="failban_types[16]" id="failban_types_16"{if ($bms_prefs.failban_types&16)!=0} checked="checked"{/if} />
						<label for="failban_types_16">{lng p="bms_fb_16"}</label>*}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_fb_attempts"}:</td>
				<td class="td2"><input type="text" size="6" name="failban_attempts" value="{text value=$bms_prefs.failban_attempts}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_fb_time"}:</td>
				<td class="td2"><input type="text" size="6" name="failban_time" value="{text value=$bms_prefs.failban_time}" /> {lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_fb_bantime"}:</td>
				<td class="td2"><input type="text" size="6" name="failban_bantime" value="{text value=$bms_prefs.failban_bantime}" /> {lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_list"}:</td>
				<td class="td2">{$banCount} {lng p="entries"} <input class="button" type="button"{if $banCount==0} disabled="disabled"{/if} value=" {lng p="show"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=common&do=banlist';" /> <input{if $banCount==0} disabled="disabled"{/if} class="button" type="button" value=" {lng p="reset"} " onclick="document.location.href='{$pageURL}&action=common&resetBanList=true&sid={$sid}';" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="bms_tls_ssl"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" valign="top" width="40" rowspan="4"><img src="{$tpldir}images/ico_prefs_ssl.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_ssl_cipher_list"}:</td>
				<td class="td2"><input type="text" name="ssl_cipher_list" value="{text value=$bms_prefs.ssl_cipher_list allowEmpty=true}" style="width:95%;" /></td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_ssl_ciphersuites"}:</td>
				<td class="td2"><input type="text" name="ssl_ciphersuites" value="{text value=$bms_prefs.ssl_ciphersuites allowEmpty=true}" style="width:95%;" /></td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_ssl_minmaxversion"}:</td>
				<td class="td2">
					<select name="ssl_min_version">
						<option value="0"{if $bms_prefs.ssl_min_version==0} selected="selected"{/if}>-</option>
						<option value="768"{if $bms_prefs.ssl_min_version==768} selected="selected"{/if}>SSL3</option>
						<option value="769"{if $bms_prefs.ssl_min_version==769} selected="selected"{/if}>TLSv1</option>
						<option value="770"{if $bms_prefs.ssl_min_version==770} selected="selected"{/if}>TLSv1.1</option>
						<option value="771"{if $bms_prefs.ssl_min_version==771} selected="selected"{/if}>TLSv1.2</option>
						<option value="772"{if $bms_prefs.ssl_min_version==772} selected="selected"{/if}>TLSv1.3</option>
					</select>
					/
					<select name="ssl_max_version">
						<option value="0"{if $bms_prefs.ssl_max_version==0} selected="selected"{/if}>-</option>
						<option value="768"{if $bms_prefs.ssl_max_version==768} selected="selected"{/if}>SSL3</option>
						<option value="769"{if $bms_prefs.ssl_max_version==769} selected="selected"{/if}>TLSv1</option>
						<option value="770"{if $bms_prefs.ssl_max_version==770} selected="selected"{/if}>TLSv1.1</option>
						<option value="771"{if $bms_prefs.ssl_max_version==771} selected="selected"{/if}>TLSv1.2</option>
						<option value="772"{if $bms_prefs.ssl_max_version==772} selected="selected"{/if}>TLSv1.3</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="td1" width="200">{lng p="bms_tlsarecord"}:</td>
				<td class="td2"><div id="tlsaRecord"><input{if !$queueRunning||($bms_prefs.core_features&1)==0} disabled="disabled"{/if} type="button" class="button" value="{lng p="show"}" onclick="bms_showTLSARecord()" /></div></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="license"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="5" valign="top" width="40"><img src="{$tpldir}images/ico_license.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="status"}:</td>
				<td class="td2">
					{if $bms_prefs.licstate==2}
					<font color="red">{lng p="bms_expired"}</font>
					{elseif $bms_prefs.licstate==0}
					<font color="red">{lng p="bms_invalid"}</font>
					{elseif $bms_prefs.licstate==1}
					<font color="darkgreen">{lng p="bms_valid"}
						{if $bms_prefs.lic_valid_until<=0}
						({lng p="unlimited"})
						{else}
						({lng p="bms_until"} {date timestamp=$bms_prefs.lic_valid_until dayonly=true})
						{/if}
					</font>
					{else}
					{lng p="bms_validating"}
					{/if}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="features"}:</td>
				<td class="td2">{text value=$features}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="version"}:</td>
				<td class="td2">{text value=$bms_prefs.core_version}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="licensekey"}:</td>
				<td class="td2"><input type="text" name="license" value="{text value=$bms_prefs.license allowEmpty=true}" size="32" style="width:95%;" /></td>
			</tr>
		</table>
	</fieldset>
	
	<p>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>

<script>
{literal}<!--
	function bms_showTLSARecord()
	{
		MakeXMLRequest('{/literal}{$pageURL}{literal}&sid=' + currentSID
							+ '&action=common&do=tlsaRecord',
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
