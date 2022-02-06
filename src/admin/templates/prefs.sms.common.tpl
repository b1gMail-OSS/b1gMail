<form action="prefs.sms.php?save=true&sid={$sid}" method="post" onsubmit="spin(this)" id="prefsForm">
	<fieldset>
		<legend>{lng p="gateway"}</legend>
	
		<table>
			<tr>
				<td align="left" valign="top" width="40"><img src="{$tpldir}images/gateway32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="defaultgateway"}:</td>
				<td class="td2"><select name="sms_gateway">
				{foreach from=$gateways item=gateway}
					<option value="{$gateway.id}"{if $gateway.id==$bm_prefs.sms_gateway} selected="selected"{/if}>{text value=$gateway.titel}</option>
				{/foreach}
				</select></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="charge"}</legend>
		
		<table>
			<tr>
				<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/ico_prefs_payments.png" border="0" alt="" width="32" height="32" /></td>				
				<td class="td1" width="220">{lng p="enablesmscharge"}?</td>
				<td class="td2"><input name="sms_enable_charge"{if $bm_prefs.sms_enable_charge=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="minamount"}:</td>
				<td class="td2"><input type="text" name="charge_min_amount" value="{$bm_prefs.charge_min_amount}" size="8" /> {text value=$bm_prefs.currency}</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="common"}</legend>
	
		<table>
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/ico_prefs_misc.png" border="0" alt="" width="32" height="32" /></td>				
				<td class="td1" width="220">{lng p="clndrsmsabs"}:</td>
				<td class="td2"><input type="text" name="clndr_sms_abs" value="{$bm_prefs.clndr_sms_abs}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="clndr_sms_type"}:</td>
				<td class="td2"><select name="clndr_sms_type">
					<option value="0">({lng p="defaulttype"})</option>
				{foreach from=$types item=type}
					<option value="{$type.id}"{if $type.id==$bm_prefs.clndr_sms_type} selected="selected"{/if}>{text value=$type.titel}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="mail2smsabs"}:</td>
				<td class="td2"><input type="text" name="mail2sms_abs" value="{$bm_prefs.mail2sms_abs}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="mail2sms_type"}:</td>
				<td class="td2"><select name="mail2sms_type">
					<option value="0">({lng p="defaulttype"})</option>
				{foreach from=$types item=type}
					<option value="{$type.id}"{if $type.id==$bm_prefs.mail2sms_type} selected="selected"{/if}>{text value=$type.titel}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smsreplyabs"}:</td>
				<td class="td2"><input type="text" name="smsreply_abs" value="{$bm_prefs.smsreply_abs}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="smsvalidation_type"}:</td>
				<td class="td2"><select name="smsvalidation_type">
					<option value="0">({lng p="defaulttype"})</option>
				{foreach from=$types item=type}
					<option value="{$type.id}"{if $type.id==$bm_prefs.smsvalidation_type} selected="selected"{/if}>{text value=$type.titel}</option>
				{/foreach}
				</select></td>
			</tr>
		</table>
	</fieldset>
	
	<p>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>

<form action="https://www.sofort-ueberweisung.de/payment/createNew/" method="post" target="suWindow" id="suForm">
	<input type="hidden" name="project_name" value="b1gMail" />
	<input type="hidden" name="project_homepage" value="{$bm_prefs.selfurl}" />
	<input type="hidden" name="project_shop_system_id" value="142" />
	<input type="hidden" name="projectssetting_currency_id" value="{$bm_prefs.currency}" />
	<input type="hidden" name="projectssetting_interface_success_link" value="http://-USER_VARIABLE_2-" />
	<input type="hidden" name="projectssetting_interface_success_link_redirect" value="1" />
	<input type="hidden" name="projectssetting_interface_cancel_link" value="http://-USER_VARIABLE_3-" />
	<input type="hidden" name="projectssetting_project_password" value="{$prjPass}" />
	<input type="hidden" name="projectssetting_locked_amount" value="1" />
	<input type="hidden" name="projectssetting_locked_reason_1" value="1" />
	<input type="hidden" name="projectssetting_locked_reason_2" value="1" />
	<input type="hidden" name="projectsnotification_http_activated" value="1" />
	<input type="hidden" name="projectsnotification_http_url" value="{$bm_prefs.selfurl}interface/su_callback.php" />
	<input type="hidden" name="projectsnotification_http_method" value="1" />
	<input type="hidden" name="backlink" value="{$bm_prefs.selfurl}admin/prefs.sms.php?do=suBack&sid={$sid}&prjPass={$prjPass}" />
</form>
