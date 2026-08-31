<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=msgqueue&save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_queue_prefs"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="10" valign="top" width="40"><img src="../plugins/templates/images/bms_queue.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_queue_interval"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="queue_interval" value="{$bms_prefs.queue_interval}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="seconds"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_queue_retry"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="queue_retry" value="{$bms_prefs.queue_retry/60}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="bms_minutes"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_queue_lifetime"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="queue_lifetime" value="{$bms_prefs.queue_lifetime/3600}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="bms_hours"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_queue_timeout"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="queue_timeout" value="{$bms_prefs.queue_timeout}" style="max-width:6rem;" />
						<span class="input-group-text">{lng p="seconds"}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_queue_threads"}:</td>
				<td class="td2">
					<div class="input-group">
						<input class="form-control" type="text" name="queue_threads" value="{$bms_prefs.queue_threads}" style="max-width:6rem;" />
						<span class="input-group-text">/</span>
						<input class="form-control" type="text" name="queue_maxthreads" value="{$bms_prefs.queue_maxthreads}" style="max-width:6rem;" />
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_control_addr"}:</td>
				<td class="td2">
					<div class="input-group input-group-wide">
						<input class="form-control" type="text" name="control_addr" value="{if isset($bms_prefs.control_addr)}{text value=$bms_prefs.control_addr allowEmpty=true}{/if}" />
						<span class="input-group-text" role="button" tabindex="0" style="cursor:pointer;" onclick="alert('{lng p="bms_control_addr_help"}');" title="{lng p="help"}">
							<i class="ti ti-info-circle"></i>
						</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_mysqlconnection"}:</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="queue_mysqlclose"{if $bms_prefs.queue_mysqlclose==1} checked="checked"{/if} id="queue_mysqlclose" /><span class="form-check-label" for="queue_mysqlclose"><b>{lng p="bms_closewhenidle"}</b></span></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_random_queue_id"}?</td>
				<td class="td2"><label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="random_queue_id"{if $bms_prefs.random_queue_id==1} checked="checked"{/if} id="random_queue_id" /><span class="form-check-label" for="random_queue_id"><b>{lng p="activate"}</b></span></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_received_header"}:</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="received_header_no_expose"{if $bms_prefs.received_header_no_expose==1} checked="checked"{/if} id="received_header_no_expose" /><span class="form-check-label" for="received_header_no_expose"><b>{lng p="bms_dont_expose"}</b></span></label></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_inbound"}</legend>
		
		<table width="100%">
			<tr>
				<td align="left" valign="top" width="40" rowspan="2"><img src="../plugins/templates/images/bms_inbound.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_php_path"}:</td>
				<td class="td2"><input class="form-control" type="text" name="php_path" value="{if isset($bms_prefs.php_path)}{text value=$bms_prefs.php_path allowEmpty=true}{/if}" size="32" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_reuseprocess"}?</td>
				<td class="td2">
					<label class="form-check mb-0">
						<input class="form-check-input" type="checkbox" id="inbound_reuse_process" name="inbound_reuse_process"{if $bms_prefs.inbound_reuse_process&&$minV72} checked="checked"{/if}{if !$minV72} disabled="disabled"{/if} />
						<span class="form-check-label" for="inbound_reuse_process"><b>{lng p="activate"}</b></span>
					</label>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_outbound"}</legend>
		
		<table width="100%">
			<tr>
				<td align="left" valign="top" width="40"><img src="../plugins/templates/images/bms_outbound.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_processing"}:</td>
				<td class="td2"><select class="form-select" name="outbound_target" onchange="EBID('outbound_sendmail_prefs').style.display=this.value==0?'':'none';EBID('outbound_smtp_prefs').style.display=this.value==1?'':'none';EBID('outbound_smtpself_prefs').style.display=this.value!=0?'':'none';">
					<option value="0"{if $bms_prefs.outbound_target==0} selected="selected"{/if}>{lng p="bms_redirecttosendmail"}</option>
					<option value="1"{if $bms_prefs.outbound_target==1} selected="selected"{/if}>{lng p="bms_redirecttosmtprelay"}</option>
					<option value="2"{if $bms_prefs.outbound_target==2} selected="selected"{/if}>{lng p="bms_deliverself"}</option>
				</select></td>
			</tr>
			<tbody id="outbound_sendmail_prefs" style="display:{if $bms_prefs.outbound_target!=0}none{/if};">
			<tr>
				<td>&nbsp;</td>
				<td class="td1">{lng p="sendmailpath"}:</td>
				<td class="td2"><input class="form-control" type="text" name="outbound_sendmail_path" value="{if isset($bms_prefs.outbound_sendmail_path)}{text value=$bms_prefs.outbound_sendmail_path allowEmpty=true}{/if}" size="32" /></td>
			</tr>
			</tbody>
			<tbody id="outbound_smtp_prefs" style="display:{if $bms_prefs.outbound_target!=1}none{/if};">
			<tr>
				<td rowspan="5">&nbsp;</td>
				<td class="td1">{lng p="smtphost"}:</td>
				<td class="td2"><input class="form-control" type="text" name="outbound_smtp_relay_host" value="{if isset($bms_prefs.outbound_smtp_relay_host)}{text value=$bms_prefs.outbound_smtp_relay_host allowEmpty=true}{/if}" size="32" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smtpport"}:</td>
				<td class="td2"><input class="form-control" type="text" name="outbound_smtp_relay_port" value="{$bms_prefs.outbound_smtp_relay_port}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smtpauth"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="outbound_smtp_relay_auth"{if $bms_prefs.outbound_smtp_relay_auth} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smtpuser"}:</td>
				<td class="td2"><input class="form-control" type="text" name="outbound_smtp_relay_user" value="{text allowEmpty=true value=$bms_prefs.outbound_smtp_relay_user}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smtppass"}:</td>
				<td class="td2"><input type="password" autocomplete="off" name="outbound_smtp_relay_pass" value="{text allowEmpty=true value=$bms_prefs.outbound_smtp_relay_pass}" size="36" /></td>
			</tr>
			</tbody>
			<tbody id="outbound_smtpself_prefs" style="display:{if $bms_prefs.outbound_target==0}none{/if};">
			<tr>
				<td>&nbsp;</td>
				<td class="td1">{lng p="bms_usetls"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="outbound_smtp_usetls"{if $bms_prefs.outbound_smtp_usetls} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="td1">{lng p="bms_usednssecdane"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="outbound_smtp_usedane"{if $bms_prefs.outbound_smtp_usedane} checked="checked"{/if} /></label></td>
			</tr>
			</tbody>
		</table>
		{if $haveSignatureSupport}
		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_signature.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_add_signature"}?</td>
				<td class="td2"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" name="outbound_add_signature"{if $bms_prefs.outbound_add_signature==1} checked="checked"{/if} /></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_signature_sep"}:</td>
				<td class="td2"><input class="form-control" type="text" name="outbound_signature_sep" value="{if isset($bms_prefs.outbound_signature_sep)}{text value=$bms_prefs.outbound_signature_sep allowEmpty=true}{/if}" size="54" /></td>
			</tr>
		</table>
		{/if}
	</fieldset>
	
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_queue"}</legend>
		
		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_queue.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_queue"}:</td>
				<td class="td2">
					{$queueCount} {lng p="entries"} ({size bytes=$queueSize})
					<button type="button" class="btn btn-outline-secondary btn-sm"{if $queueCount==0} disabled="disabled"{/if} onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=msgqueue&action=queue"}';">{lng p="show"}</button>
					{if $queueCount>0}
					<button type="submit" class="btn btn-outline-secondary btn-sm" form="bmsQueueClearForm" onclick="return confirm('{lng p="bms_clearquestion"}');">{lng p="bms_clearqueue"}</button>
					{if $allowFlush}<button type="submit" class="btn btn-outline-secondary btn-sm" form="bmsQueueFlushForm">{lng p="bms_flushqueue"}</button>{else}<button type="button" class="btn btn-outline-secondary btn-sm" disabled="disabled">{lng p="bms_flushqueue"}</button>{/if}
					{else}
					<button type="button" class="btn btn-outline-secondary btn-sm" disabled="disabled">{lng p="bms_clearqueue"}</button>
					<button type="button" class="btn btn-outline-secondary btn-sm" disabled="disabled">{lng p="bms_flushqueue"}</button>
					{/if}
					{if $enableRestart}<button type="submit" class="btn btn-outline-secondary btn-sm" form="bmsQueueRestartForm" onclick="return confirm('{lng p="bms_reallyrestartqueue"}');">{lng p="bms_restartqueue"}</button>{else}<button type="button" class="btn btn-outline-secondary btn-sm" disabled="disabled">{lng p="bms_restartqueue"}</button>{/if}
				</td>
			</tr>
			{if $threadCount}
			<tr>
				<td class="td1" width="200">{lng p="bms_threads"}:</td>
				<td class="td2">{$threadCount}</td>
			</tr>
			{/if}
		</table>
	</fieldset>

	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_advanced"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_common.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_ownheaders"}:</td>
				<td class="td2">
					<button type="button" class="btn btn-outline-secondary btn-sm"  onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=msgqueue&action=headers"}';" >{lng p="edit"}</button>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_deliveryrules"}:</td>
				<td class="td2">
					<button type="button" class="btn btn-outline-secondary btn-sm"  onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=msgqueue&action=deliveryRules"}';" >{lng p="edit"}</button>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<div class="d-flex justify-content-between align-items-center mt-3 mb-2">
		<div class="text-secondary small">
			<i class="ti ti-alert-triangle me-1"></i> {lng p="bms_queuerestartnote"}
		</div>
		<button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> {lng p="save"}</button>
	</div>
</form>

<form id="bmsQueueClearForm" method="post" action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=msgqueue"}" style="display:none;" aria-hidden="true">
	{csrffield}
	<input type="hidden" name="clearQueue" value="1" />
</form>
<form id="bmsQueueFlushForm" method="post" action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=msgqueue"}" style="display:none;" aria-hidden="true">
	{csrffield}
	<input type="hidden" name="flushQueue" value="1" />
</form>
<form id="bmsQueueRestartForm" method="post" action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=msgqueue"}" style="display:none;" aria-hidden="true">
	{csrffield}
	<input type="hidden" name="restartQueue" value="1" />
</form>
