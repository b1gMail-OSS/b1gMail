<form action="{$pageURL}&sid={$sid}&action=msgqueue&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="bms_queue_prefs"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="10" valign="top" width="40"><img src="../plugins/templates/images/bms_queue.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_queue_interval"}:</td>
				<td class="td2"><input type="text" name="queue_interval" value="{$bms_prefs.queue_interval}" size="6" />
								{lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_queue_retry"}:</td>
				<td class="td2"><input type="text" name="queue_retry" value="{$bms_prefs.queue_retry/60}" size="6" />
								{lng p="bms_minutes"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_queue_lifetime"}:</td>
				<td class="td2"><input type="text" name="queue_lifetime" value="{$bms_prefs.queue_lifetime/3600}" size="6" />
								{lng p="bms_hours"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_queue_timeout"}:</td>
				<td class="td2"><input type="text" name="queue_timeout" value="{$bms_prefs.queue_timeout}" size="6" />
								{lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_queue_threads"}:</td>
				<td class="td2"><input type="text" name="queue_threads" value="{$bms_prefs.queue_threads}" size="6" />
								/ <input type="text" name="queue_maxthreads" value="{$bms_prefs.queue_maxthreads}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_control_addr"}:</td>
				<td class="td2">
					<input type="text" name="control_addr" value="{text value=$bms_prefs.control_addr allowEmpty=true}" size="16" />
					<a href="#" onclick="alert('{lng p="bms_control_addr_help"}');"><img src="{$tpldir}images/info.png" border="0" alt="{lng p="help"}" /></a>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_mysqlconnection"}:</td>
				<td class="td2"><input type="checkbox" name="queue_mysqlclose"{if $bms_prefs.queue_mysqlclose==1} checked="checked"{/if} id="queue_mysqlclose" />
					<label for="queue_mysqlclose"><b>{lng p="bms_closewhenidle"}</b></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_random_queue_id"}?</td>
				<td class="td2"><input type="checkbox" name="random_queue_id"{if $bms_prefs.random_queue_id==1} checked="checked"{/if} id="random_queue_id" />
					<label for="random_queue_id"><b>{lng p="activate"}</b></label></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_received_header"}:</td>
				<td class="td2"><input type="checkbox" name="received_header_no_expose"{if $bms_prefs.received_header_no_expose==1} checked="checked"{/if} id="received_header_no_expose" />
					<label for="received_header_no_expose"><b>{lng p="bms_dont_expose"}</b></label></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="bms_inbound"}</legend>
		
		<table width="100%">
			<tr>
				<td align="left" valign="top" width="40" rowspan="2"><img src="../plugins/templates/images/bms_inbound.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_php_path"}:</td>
				<td class="td2"><input type="text" name="php_path" value="{text value=$bms_prefs.php_path allowEmpty=true}" size="32" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_reuseprocess"}?</td>
				<td class="td2"><input id="inbound_reuse_process" name="inbound_reuse_process"{if $bms_prefs.inbound_reuse_process&&$minV72} checked="checked"{/if} type="checkbox"{if !$minV72} disabled="disabled"{/if} />
								<label for="inbound_reuse_process"><b>{lng p="activate"}</b></label></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="bms_outbound"}</legend>
		
		<table width="100%">
			<tr>
				<td align="left" valign="top" width="40"><img src="../plugins/templates/images/bms_outbound.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_processing"}:</td>
				<td class="td2"><select name="outbound_target" onchange="EBID('outbound_sendmail_prefs').style.display=this.value==0?'':'none';EBID('outbound_smtp_prefs').style.display=this.value==1?'':'none';EBID('outbound_smtpself_prefs').style.display=this.value!=0?'':'none';">
					<option value="0"{if $bms_prefs.outbound_target==0} selected="selected"{/if}>{lng p="bms_redirecttosendmail"}</option>
					<option value="1"{if $bms_prefs.outbound_target==1} selected="selected"{/if}>{lng p="bms_redirecttosmtprelay"}</option>
					<option value="2"{if $bms_prefs.outbound_target==2} selected="selected"{/if}>{lng p="bms_deliverself"}</option>
				</select></td>
			</tr>
			<tbody id="outbound_sendmail_prefs" style="display:{if $bms_prefs.outbound_target!=0}none{/if};">
			<tr>
				<td>&nbsp;</td>
				<td class="td1">{lng p="sendmailpath"}:</td>
				<td class="td2"><input type="text" name="outbound_sendmail_path" value="{text value=$bms_prefs.outbound_sendmail_path allowEmpty=true}" size="32" /></td>
			</tr>
			</tbody>
			<tbody id="outbound_smtp_prefs" style="display:{if $bms_prefs.outbound_target!=1}none{/if};">
			<tr>
				<td rowspan="5">&nbsp;</td>
				<td class="td1">{lng p="smtphost"}:</td>
				<td class="td2"><input type="text" name="outbound_smtp_relay_host" value="{text value=$bms_prefs.outbound_smtp_relay_host allowEmpty=true}" size="32" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smtpport"}:</td>
				<td class="td2"><input type="text" name="outbound_smtp_relay_port" value="{$bms_prefs.outbound_smtp_relay_port}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smtpauth"}?</td>
				<td class="td2"><input name="outbound_smtp_relay_auth"{if $bms_prefs.outbound_smtp_relay_auth} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smtpuser"}:</td>
				<td class="td2"><input type="text" name="outbound_smtp_relay_user" value="{text allowEmpty=true value=$bms_prefs.outbound_smtp_relay_user}" size="36" /></td>
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
				<td class="td2"><input name="outbound_smtp_usetls"{if $bms_prefs.outbound_smtp_usetls} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="td1">{lng p="bms_usednssecdane"}?</td>
				<td class="td2"><input name="outbound_smtp_usedane"{if $bms_prefs.outbound_smtp_usedane} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			</tbody>
		</table>
		{if $haveSignatureSupport}
		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_signature.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_add_signature"}?</td>
				<td class="td2"><input type="checkbox" name="outbound_add_signature"{if $bms_prefs.outbound_add_signature==1} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_signature_sep"}:</td>
				<td class="td2"><input type="text" name="outbound_signature_sep" value="{text value=$bms_prefs.outbound_signature_sep allowEmpty=true}" size="54" /></td>
			</tr>
		</table>
		{/if}
	</fieldset>
	
	<fieldset>
		<legend>{lng p="bms_queue"}</legend>
		
		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_queue.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_queue"}:</td>
				<td class="td2">{$queueCount} {lng p="entries"} ({size bytes=$queueSize})
					<input class="button" type="button"{if $queueCount==0} disabled="disabled"{/if} value=" {lng p="show"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=msgqueue&do=queue';" />
					<input class="button" type="button"{if $queueCount==0} disabled="disabled"{/if} value=" {lng p="bms_clearqueue"} " onclick="if(confirm('{lng p="bms_clearquestion"}')) document.location.href='{$pageURL}&sid={$sid}&action=msgqueue&clearQueue=true';" />
					<input class="button" type="button"{if $queueCount==0||!$allowFlush} disabled="disabled"{/if} value=" {lng p="bms_flushqueue"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=msgqueue&flushQueue=true';" />
					<input class="button" type="button"{if !$enableRestart} disabled="disabled"{/if} value=" {lng p="bms_restartqueue"} " onclick="if(confirm('{lng p="bms_reallyrestartqueue"}')) document.location.href='{$pageURL}&sid={$sid}&action=msgqueue&restartQueue=true';" />
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

	<fieldset>
		<legend>{lng p="bms_advanced"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="2" valign="top" width="40"><img src="../plugins/templates/images/bms_common.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="200">{lng p="bms_ownheaders"}:</td>
				<td class="td2">
					<input class="button" type="button" value=" {lng p="edit"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=msgqueue&do=headers';" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="bms_deliveryrules"}:</td>
				<td class="td2">
					<input class="button" type="button" value=" {lng p="edit"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=msgqueue&do=deliveryRules';" />
				</td>
			</tr>
		</table>
	</fieldset>
	
	<p>
		<div style="float:left" class="buttons">
			<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
			{lng p="bms_queuerestartnote"}
		</div>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
