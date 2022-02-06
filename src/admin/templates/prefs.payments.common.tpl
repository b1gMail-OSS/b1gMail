<form action="prefs.payments.php?save=true&sid={$sid}" method="post" onsubmit="spin(this)" id="prefsForm">
	<fieldset>
		<legend>{lng p="payments"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="4"><img src="{$tpldir}images/ico_prefs_payments.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="currency"}:</td>
				<td class="td2"><input type="text" name="currency" value="{$bm_prefs.currency}" size="8" /></td>
				<td class="td">&nbsp;</td>
			</tr>
			<tr>
				<td class="td1">{lng p="pay_notification"}:</td>
				<td class="td2"><input id="send_pay_notification" name="send_pay_notification"{if $bm_prefs.send_pay_notification=='yes'} checked="checked"{/if} type="checkbox" /><label for="send_pay_notification"> {lng p="to2"}: </label><input type="text" name="pay_notification_to" value="{text value=$bm_prefs.pay_notification_to allowEmpty=true}" size="24" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="sysmailsender"}:</td>
				<td class="td2">
					"<input type="text" name="pay_emailfrom" value="{text value=$bm_prefs.pay_emailfrom allowEmpty=true}" size="14" />"
					&lt;<input type="text" name="pay_emailfromemail" value="{email value=$bm_prefs.pay_emailfromemail}" size="22" />&gt;
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="vat"}:</td>
				<td class="td2">
					<select name="mwst">
						<option value="add"{if $bm_prefs.mwst=='add'} selected="selected"{/if}>{lng p="vat_add"}</option>
						<option value="enthalten"{if $bm_prefs.mwst=='enthalten'} selected="selected"{/if}>{lng p="vat_enthalten"}</option>
						<option value="nomwst"{if $bm_prefs.mwst=='nomwst'} selected="selected"{/if}>{lng p="vat_nomwst"}</option>
					</select>
					<small>
						&nbsp; {lng p="vatratenotice"} <a href="prefs.countries.php?sid={$sid}">{lng p="countries"}</a>.
					</small>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>PayPal</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="3"><img src="{$tpldir}images/ico_pay_paypal.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="enablechrgpaypal"}?</td>
				<td class="td2"><input name="enable_paypal"{if $bm_prefs.enable_paypal=='yes'} checked="checked"{/if} type="checkbox" /></td>
				<td class="td2">&nbsp;</td>
			</tr>
			<tr>
				<td class="td1">{lng p="default"}?</td>
				<td class="td2"><input type="radio" name="default_paymethod" value="1"{if $bm_prefs.default_paymethod==1} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="paypalacc"}:</td>
				<td class="td2"><input type="text" name="paypal_mail" value="{$bm_prefs.paypal_mail}" size="36" /></td>
				<td class="td2">&nbsp;</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>sofort&uuml;berweisung.de</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="7"><img src="{$tpldir}images/ico_pay_su.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="enablechrgsu"}?</td>
				<td class="td2"><input name="enable_su"{if $bm_prefs.enable_su=='yes'} checked="checked"{/if} id="su_enable" type="checkbox" /></td>
				<td class="td2">&nbsp;</td>
			</tr>
			<tr>
				<td class="td1">{lng p="default"}?</td>
				<td class="td2"><input type="radio" name="default_paymethod" value="2"{if $bm_prefs.default_paymethod==2} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="sukdnr"}:</td>
				<td class="td2"><input type="text" name="su_kdnr" value="{$bm_prefs.su_kdnr}" id="su_kdnr" size="24" /></td>
				<td class="td2">&nbsp;</td>
			</tr>
			<tr>
				<td class="td1">{lng p="suprjnr"}:</td>
				<td class="td2"><input type="text" name="su_prjnr" value="{$bm_prefs.su_prjnr}" id="su_prjnr" size="24" /></td>
				<td class="td2" style="padding-left:10px;">
					<input class="button" type="button" value=" {lng p="su_createnew"} " onclick="window.open('about:blank','suWindow','width=990,height=800,scrollbars=yes,location=yes,menubar=yes,resizable=yes,status=yes,toolbar=yes');EBID('suForm').submit();" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="suprjpass"}:</td>
				<td class="td2"><input type="text" name="su_prjpass" value="{$bm_prefs.su_prjpass}" id="su_prjpass" size="24" /></td>
				<td class="td2">&nbsp;</td>
			</tr>
			<tr>
				<td class="td1">{lng p="sunotifypass"}:</td>
				<td class="td2"><input type="text" name="su_notifypass" value="{$bm_prefs.su_notifypass}" id="su_notifypass" size="24" /></td>
				<td class="td2">&nbsp;</td>
			</tr>
			<tr>
				<td class="td1">{lng p="suinputcheck"}?</td>
				<td class="td2"><input name="su_inputcheck"{if $bm_prefs.su_inputcheck=='yes'} checked="checked"{/if} id="su_inputcheck" type="checkbox" /></td>
				<td class="td2">&nbsp;</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>Skrill (Moneybookers)</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="4"><img src="{$tpldir}images/ico_pay_skrill.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="enablechrgskrill"}?</td>
				<td class="td2"><input name="enable_skrill"{if $bm_prefs.enable_skrill=='yes'} checked="checked"{/if} type="checkbox" /></td>
				<td class="td2">&nbsp;</td>
			</tr>
			<tr>
				<td class="td1">{lng p="default"}?</td>
				<td class="td2"><input type="radio" name="default_paymethod" value="1"{if $bm_prefs.default_paymethod==3} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="skrillacc"}:</td>
				<td class="td2"><input type="text" name="skrill_mail" value="{$bm_prefs.skrill_mail}" size="36" /></td>
				<td class="td2">&nbsp;</td>
			</tr>
			<tr>
				<td class="td1">{lng p="skrillsecret"}:</td>
				<td class="td2"><input type="text" name="skrill_secret" value="{$bm_prefs.skrill_secret}" id="skrill_secret" size="24" /></td>
				<td class="td2">&nbsp;</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="banktransfer"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="8"><img src="{$tpldir}images/ico_pay_banktransfer.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="enablebanktransfer"}?</td>
				<td class="td2"><input name="enable_vk"{if $bm_prefs.enable_vk=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="default"}?</td>
				<td class="td2"><input type="radio" name="default_paymethod" value="0"{if $bm_prefs.default_paymethod==0} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="kto_inh"}:</td>
				<td class="td2"><input type="text" name="vk_kto_inh" value="{text allowEmpty=true value=$bm_prefs.vk_kto_inh}" size="24" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="kto_nr"}:</td>
				<td class="td2"><input type="text" name="vk_kto_nr" value="{text allowEmpty=true value=$bm_prefs.vk_kto_nr}" size="24" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="kto_blz"}:</td>
				<td class="td2"><input type="text" name="vk_kto_blz" value="{text allowEmpty=true value=$bm_prefs.vk_kto_blz}" size="24" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="kto_inst"}:</td>
				<td class="td2"><input type="text" name="vk_kto_inst" value="{text allowEmpty=true value=$bm_prefs.vk_kto_inst}" size="24" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="kto_iban"}:</td>
				<td class="td2"><input type="text" name="vk_kto_iban" value="{text allowEmpty=true value=$bm_prefs.vk_kto_iban}" size="24" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="kto_bic"}:</td>
				<td class="td2"><input type="text" name="vk_kto_bic" value="{text allowEmpty=true value=$bm_prefs.vk_kto_bic}" size="24" /></td>
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
	<input type="hidden" name="project_hash_algorithm" value="md5" />
	<input type="hidden" name="project_notification_password" value="{$notifyPass}" />
	<input type="hidden" name="projectssetting_currency_id" value="{$bm_prefs.currency}" />
	<input type="hidden" name="projectssetting_interface_success_link" value="http://-USER_VARIABLE_2-" />
	<input type="hidden" name="projectssetting_interface_success_link_redirect" value="1" />
	<input type="hidden" name="projectssetting_interface_cancel_link" value="http://-USER_VARIABLE_3-" />
	<input type="hidden" name="projectssetting_interface_input_hash_check_enabled" value="1" />
	<input type="hidden" name="projectssetting_project_password" value="{$prjPass}" />
	<input type="hidden" name="projectssetting_locked_amount" value="1" />
	<input type="hidden" name="projectssetting_locked_reason_1" value="1" />
	<input type="hidden" name="projectssetting_locked_reason_2" value="1" />
	<input type="hidden" name="projectsnotification_http_activated" value="1" />
	<input type="hidden" name="projectsnotification_http_url" value="{$bm_prefs.selfurl}interface/su_callback.php" />
	<input type="hidden" name="projectsnotification_http_method" value="1" />
	<input type="hidden" name="backlink" value="{$bm_prefs.selfurl}admin/prefs.payments.php?do=suBack&sid={$sid}&prjPass={$prjPass}&notifyPass={$notifyPass}" />
</form>
