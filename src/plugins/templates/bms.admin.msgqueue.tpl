<form action="{$pageURL}&sid={$sid}&action=msgqueue&save=true" method="post" onsubmit="spin(this)">

	<div class="alert alert-warning">{lng p="bms_queuerestartnote"}</div>

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="bms_queue_prefs"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_queue_interval"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="queue_interval" value="{$bms_prefs.queue_interval}" placeholder="{lng p="bms_queue_interval"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_queue_retry"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="queue_retry" value="{$bms_prefs.queue_retry/60}" placeholder="{lng p="bms_queue_retry"}">
							<span class="input-group-text">{lng p="bms_minutes"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_queue_lifetime"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="queue_lifetime" value="{$bms_prefs.queue_lifetime/3600}" placeholder="{lng p="bms_queue_lifetime"}">
							<span class="input-group-text">{lng p="bms_hours"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_queue_timeout"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="queue_timeout" value="{$bms_prefs.queue_timeout}" placeholder="{lng p="bms_queue_timeout"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="bms_queue_threads"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="queue_threads" value="{$bms_prefs.queue_threads}">
							<span class="input-group-text">/</span>
							<input type="text" class="form-control" name="queue_maxthreads" value="{$bms_prefs.queue_maxthreads}">
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="bms_control_addr"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="control_addr" value="{if isset($bms_prefs.control_addr)}{text value=$bms_prefs.control_addr allowEmpty=true}{/if}" placeholder="{lng p="bms_control_addr"}">
							<span class="input-group-text"><a href="#" onclick="alert('{lng p="bms_control_addr_help"}');"><i class="fa-solid fa-circle-info"></i></a></span>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_mysqlconnection"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="queue_mysqlclose"{if $bms_prefs.queue_mysqlclose==1} checked="checked"{/if} id="queue_mysqlclose">
							<span class="form-check-label">{lng p="bms_closewhenidle"}</span>
						</label>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_random_queue_id"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="random_queue_id"{if $bms_prefs.random_queue_id==1} checked="checked"{/if} id="random_queue_id">
							<span class="form-check-label">{lng p="activate"}</span>
						</label>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_received_header"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="received_header_no_expose"{if $bms_prefs.received_header_no_expose==1} checked="checked"{/if} id="received_header_no_expose">
							<span class="form-check-label">{lng p="bms_dont_expose"}</span>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="bms_inbound"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_php_path"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="php_path" value="{if isset($bms_prefs.php_path)}{text value=$bms_prefs.php_path allowEmpty=true}{/if}" placeholder="{lng p="bms_php_path"}">
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_reuseprocess"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="inbound_reuse_process"{if $bms_prefs.inbound_reuse_process&&$minV72} checked="checked"{/if} type="checkbox"{if !$minV72} disabled="disabled"{/if}>
							<span class="form-check-label">{lng p="activate"}</span>
						</label>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="bms_outbound"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_processing"}</label>
					<div class="col-sm-8">
						<select name="outbound_target" onchange="EBID('outbound_sendmail_prefs').style.display=this.value==0?'':'none';EBID('outbound_smtp_prefs').style.display=this.value==1?'':'none';EBID('outbound_smtpself_prefs').style.display=this.value!=0?'':'none';" class="form-select">
							<option value="0"{if $bms_prefs.outbound_target==0} selected="selected"{/if}>{lng p="bms_redirecttosendmail"}</option>
							<option value="1"{if $bms_prefs.outbound_target==1} selected="selected"{/if}>{lng p="bms_redirecttosmtprelay"}</option>
							<option value="2"{if $bms_prefs.outbound_target==2} selected="selected"{/if}>{lng p="bms_deliverself"}</option>
						</select>
					</div>
				</div>
				<div id="outbound_sendmail_prefs" style="display:{if $bms_prefs.outbound_target!=0}none{/if};">
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{lng p="sendmailpath"}</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="outbound_sendmail_path" value="{if isset($bms_prefs.outbound_sendmail_path)}{text value=$bms_prefs.outbound_sendmail_path allowEmpty=true}{/if}" placeholder="{lng p="sendmailpath"}">
						</div>
					</div>
				</div>
				<div id="outbound_smtp_prefs" style="display:{if $bms_prefs.outbound_target!=1}none{/if};">
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{lng p="smtphost"}</label>
						<div class="col-sm-8">
							<div class="input-group mb-2">
								<input type="text" class="form-control" name="outbound_smtp_relay_host" value="{if isset($bms_prefs.outbound_smtp_relay_host)}{text value=$bms_prefs.outbound_smtp_relay_host allowEmpty=true}{/if}"  placeholder="{lng p="smtphost"}">
								<span class="input-group-text">:</span>
								<input type="text" class="form-control" name="outbound_smtp_relay_port" value="{$bms_prefs.outbound_smtp_relay_port}" placeholder="{lng p="smtpport"}">
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-check-label">{lng p="smtpauth"}</label>
						<div class="col-sm-8">
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="outbound_smtp_relay_auth"{if $bms_prefs.outbound_smtp_relay_auth} checked="checked"{/if}>
							</label>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{lng p="smtpuser"}</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="outbound_smtp_relay_user" value="{text allowEmpty=true value=$bms_prefs.outbound_smtp_relay_user}"  placeholder="{lng p="smtpuser"}">
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{lng p="smtppass"}</label>
						<div class="col-sm-8">
							<input type="password" class="form-control" autocomplete="off" name="outbound_smtp_relay_pass" value="{text allowEmpty=true value=$bms_prefs.outbound_smtp_relay_pass}" placeholder="{lng p="smtppass"}">
						</div>
					</div>
				</div>
				<div id="outbound_smtpself_prefs" style="display:{if $bms_prefs.outbound_target==0}none{/if};">
					<div class="row">
						<label class="col-sm-4 col-form-check-label">{lng p="bms_usetls"}</label>
						<div class="col-sm-8">
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="outbound_smtp_usetls"{if $bms_prefs.outbound_smtp_usetls} checked="checked"{/if}>
							</label>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-check-label">{lng p="bms_usednssecdane"}</label>
						<div class="col-sm-8">
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="outbound_smtp_usedane"{if $bms_prefs.outbound_smtp_usedane} checked="checked"{/if}>
							</label>
						</div>
					</div>
				</div>

				{if $haveSignatureSupport}
					<div class="row">
						<label class="col-sm-4 col-form-check-label">{lng p="bms_add_signature"}</label>
						<div class="col-sm-8">
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="outbound_add_signature"{if $bms_prefs.outbound_add_signature==1} checked="checked"{/if} >
							</label>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{lng p="bms_signature_sep"}</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="outbound_signature_sep" value="{if isset($bms_prefs.outbound_signature_sep)}{text value=$bms_prefs.outbound_signature_sep allowEmpty=true}{/if}" placeholder="{lng p="bms_signature_sep"}">
						</div>
					</div>
				{/if}
			</fieldset>
		</div>
	</div>

	<fieldset>
		<legend>{lng p="bms_queue"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="bms_queue"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<span class="input-group-text">{$queueCount} {lng p="entries"} ({size bytes=$queueSize})</span>
					<input class="btn btn-primary" type="button"{if $queueCount==0} disabled="disabled"{/if} value=" {lng p="show"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=msgqueue&do=queue';" />
					<input class="btn btn-danger" type="button"{if $queueCount==0} disabled="disabled"{/if} value=" {lng p="bms_clearqueue"} " onclick="if(confirm('{lng p="bms_clearquestion"}')) document.location.href='{$pageURL}&sid={$sid}&action=msgqueue&clearQueue=true';" />
					<input class="btn btn-primary" type="button"{if $queueCount==0||!$allowFlush} disabled="disabled"{/if} value=" {lng p="bms_flushqueue"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=msgqueue&flushQueue=true';" />
					<input class="btn btn-warning" type="button"{if !$enableRestart} disabled="disabled"{/if} value=" {lng p="bms_restartqueue"} " onclick="if(confirm('{lng p="bms_reallyrestartqueue"}')) document.location.href='{$pageURL}&sid={$sid}&action=msgqueue&restartQueue=true';" />
				</div>
			</div>
		</div>
		{if $threadCount}
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="bms_threads"}</label>
			<div class="col-sm-10">
				<div class="form-control-plaintext">{$threadCount}</div>
			</div>
		</div>
		{/if}
	</fieldset>

	<fieldset>
		<legend>{lng p="bms_advanced"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="bms_ownheaders"}</label>
			<div class="col-sm-10">
				<input class="btn btn-primary" type="button" value=" {lng p="edit"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=msgqueue&do=headers';" />
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="bms_deliveryrules"}</label>
			<div class="col-sm-10">
				<input class="btn btn-primary" type="button" value=" {lng p="edit"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=msgqueue&do=deliveryRules';" />
			</div>
		</div>
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value=" {lng p="save"} " />
	</div>
</form>
