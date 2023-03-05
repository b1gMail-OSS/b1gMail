<form action="{$pageURL}&sid={$sid}&action=common&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="bms_userarea"}</legend>

		<div class="row">
			<label class="col-sm-2 col-form-check-label">{lng p="bms_usershowlogin"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="user_showlogin"{if $bms_prefs.user_showlogin} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="bms_imapserver"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="user_imapserver" value="{text value=$bms_prefs.user_imapserver allowEmpty=true}">
					<span class="input-group-text">:</span>
					<input type="number" class="form-control" name="user_imapport" value="{text value=$bms_prefs.user_imapport}">
					<span class="input-group-text">
                       	<input class="form-check-input m-0" type="checkbox" name="user_imapssl" id="user_imapssl"{if $bms_prefs.user_imapssl} checked="checked"{/if}>
                    </span>
					<span class="input-group-text">SSL</span>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="bms_pop3server"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="user_pop3server" value="{text value=$bms_prefs.user_pop3server allowEmpty=true}">
					<span class="input-group-text">:</span>
					<input type="number" class="form-control" name="user_pop3port" value="{text value=$bms_prefs.user_pop3port}">
					<span class="input-group-text">
                       	<input class="form-check-input m-0" type="checkbox" name="user_pop3ssl" id="user_pop3ssl"{if $bms_prefs.user_pop3ssl} checked="checked"{/if}>
                    </span>
					<span class="input-group-text">SSL</span>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="bms_smtpserver"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="user_smtpserver" value="{text value=$bms_prefs.user_smtpserver allowEmpty=true}">
					<span class="input-group-text">:</span>
					<input type="number" class="form-control" name="user_smtpport" value="{text value=$bms_prefs.user_smtpport}">
					<span class="input-group-text">
                       	<input class="form-check-input m-0" type="checkbox" name="user_smtpssl" id="user_smtpssl"{if $bms_prefs.user_smtpssl} checked="checked"{/if}>
                    </span>
					<span class="input-group-text">SSL</span>
				</div>
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="bms_logging"}</legend>

				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_logging_debug"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="loglevel[8]"{if ($bms_prefs.loglevel&8)!=0} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_logging_notices"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="loglevel[1]"{if ($bms_prefs.loglevel&1)!=0} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_logging_warnings"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="loglevel[2]"{if ($bms_prefs.loglevel&2)!=0} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_logging_errors"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="loglevel[4]"{if ($bms_prefs.loglevel&4)!=0} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_logging_autodelete"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<span class="input-group-text">
								<input class="form-check-input m-0" type="checkbox" id="logs_autodelete" name="logs_autodelete"{if $bms_prefs.logs_autodelete} checked="checked"{/if}>
							</span>
							<span class="input-group-text">{lng p="bms_enableolder"}</span>
							<input type="text" class="form-control" name="logs_autodelete_days" value="{text value=$bms_prefs.logs_autodelete_days}">
							<span class="input-group-text">{lng p="days"}</span>
						</div>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" id="logs_autodelete_archive" name="logs_autodelete_archive"{if $bms_prefs.logs_autodelete_archive} checked="checked"{/if}>
							<span class="form-check-label">{lng p="savearc"}</span>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="bms_failban"}</legend>

				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_fb_activatefor"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="failban_types[1]" id="failban_types_1"{if ($bms_prefs.failban_types&1)!=0} checked="checked"{/if}>
							<span class="form-check-label">{lng p="bms_fb_1"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="failban_types[2]" id="failban_types_2"{if ($bms_prefs.failban_types&2)!=0} checked="checked"{/if}>
							<span class="form-check-label">{lng p="bms_fb_2"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="failban_types[4]" id="failban_types_4"{if ($bms_prefs.failban_types&4)!=0} checked="checked"{/if}>
							<span class="form-check-label">{lng p="bms_fb_4"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="failban_types[8]" id="failban_types_8"{if ($bms_prefs.failban_types&8)!=0} checked="checked"{/if}>
							<span class="form-check-label">{lng p="bms_fb_8"}</span>
						</label>
						<!--
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="failban_types[16]" id="failban_types_16"{if ($bms_prefs.failban_types&16)!=0} checked="checked"{/if}>
							<span class="form-check-label">{lng p="bms_fb_16"}</span>
						</label>
						-->
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_fb_attempts"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="failban_attempts" value="{text value=$bms_prefs.failban_attempts}" placeholder="{lng p="bms_fb_attempts"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_fb_time"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="failban_time" value="{text value=$bms_prefs.failban_time}" placeholder="{lng p="bms_fb_time"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_fb_bantime"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="failban_bantime" value="{text value=$bms_prefs.failban_bantime}" placeholder="{lng p="bms_fb_bantime"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_list"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<span class="input-group-text">{$banCount} {lng p="entries"}</span>
							<input class="btn btn-primary" type="button"{if $banCount==0} disabled="disabled"{/if} value=" {lng p="show"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=common&do=banlist';" />
							<input{if $banCount==0} disabled="disabled"{/if} class="btn btn-primary" type="button" value=" {lng p="reset"} " onclick="document.location.href='{$pageURL}&action=common&resetBanList=true&sid={$sid}';" />
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<fieldset>
		<legend>{lng p="bms_tls_ssl"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="bms_ssl_cipher_list"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="ssl_cipher_list" value="{text value=$bms_prefs.ssl_cipher_list allowEmpty=true}" placeholder="{lng p="bms_ssl_cipher_list"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="bms_ssl_ciphersuites"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="ssl_ciphersuites" value="{text value=$bms_prefs.ssl_ciphersuites allowEmpty=true}" placeholder="{lng p="bms_ssl_ciphersuites"}">
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="bms_ssl_minmaxversion"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<select name="ssl_min_version" class="form-select">
						<option value="0"{if $bms_prefs.ssl_min_version==0} selected="selected"{/if}>-</option>
						<option value="768"{if $bms_prefs.ssl_min_version==768} selected="selected"{/if}>SSL3</option>
						<option value="769"{if $bms_prefs.ssl_min_version==769} selected="selected"{/if}>TLSv1</option>
						<option value="770"{if $bms_prefs.ssl_min_version==770} selected="selected"{/if}>TLSv1.1</option>
						<option value="771"{if $bms_prefs.ssl_min_version==771} selected="selected"{/if}>TLSv1.2</option>
						<option value="772"{if $bms_prefs.ssl_min_version==772} selected="selected"{/if}>TLSv1.3</option>
					</select>
					<span class="input-group-text"><i class="fa-solid fa-arrows-left-right"></i></span>
					<select name="ssl_max_version" class="form-select">
						<option value="0"{if $bms_prefs.ssl_max_version==0} selected="selected"{/if}>-</option>
						<option value="768"{if $bms_prefs.ssl_max_version==768} selected="selected"{/if}>SSL3</option>
						<option value="769"{if $bms_prefs.ssl_max_version==769} selected="selected"{/if}>TLSv1</option>
						<option value="770"{if $bms_prefs.ssl_max_version==770} selected="selected"{/if}>TLSv1.1</option>
						<option value="771"{if $bms_prefs.ssl_max_version==771} selected="selected"{/if}>TLSv1.2</option>
						<option value="772"{if $bms_prefs.ssl_max_version==772} selected="selected"{/if}>TLSv1.3</option>
					</select>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="bms_tlsarecord"}</label>
			<div class="col-sm-10">
				<div id="tlsaRecord"><input{if !$queueRunning||($bms_prefs.core_features&1)==0} disabled="disabled"{/if} type="button" class="btn btn-sm btn-primary" value="{lng p="show"}" onclick="bms_showTLSARecord()" /></div>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="license"}</legend>

		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="status"}</label>
			<div class="col-sm-10">
				<div class="form-control-plaintext">
					{if $bms_prefs.licstate==2}
						<p class="text-red">{lng p="bms_expired"}</p>
					{elseif $bms_prefs.licstate==0}
						<p class="text-red">{lng p="bms_invalid"}</p>
					{elseif $bms_prefs.licstate==1}
						<p class="text-green">
							{lng p="bms_valid"}
							{if $bms_prefs.lic_valid_until<=0}
								({lng p="unlimited"})
							{else}
								({lng p="bms_until"} {date timestamp=$bms_prefs.lic_valid_until dayonly=true})
							{/if}
						</p>
					{else}
						{lng p="bms_validating"}
					{/if}
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="features"}</label>
			<div class="col-sm-10">
				<div class="form-control-plaintext">{text value=$features}</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="version"}</label>
			<div class="col-sm-10">
				<div class="form-control-plaintext">{text value=$bms_prefs.core_version}</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="licensekey"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="license" value="{text value=$bms_prefs.license allowEmpty=true}" placeholder="{lng p="licensekey"}">
			</div>
		</div>
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
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
