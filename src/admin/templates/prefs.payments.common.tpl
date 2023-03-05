<form action="prefs.payments.php?save=true&sid={$sid}" method="post" onsubmit="spin(this)" id="prefsForm">
	<fieldset>
		<legend>{lng p="payments"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="currency"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="currency" value="{$bm_prefs.currency}" placeholder="{lng p="currency"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="pay_notification"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
                        <span class="input-group-text">
                        	<input class="form-check-input m-0" type="checkbox" name="send_pay_notification"{if $bm_prefs.send_pay_notification=='yes'} checked="checked"{/if}>
                        </span>
					<span class="input-group-text">{lng p="to2"}:</span>
					<input type="text" class="form-control" name="pay_notification_to" value="{if isset($bm_prefs.pay_notification_to)}{text value=$bm_prefs.pay_notification_to allowEmpty=true}{/if}" placeholder="{lng p="pay_notification"}">
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="sysmailsender"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<span class="input-group-text">"</span>
					<input type="text" class="form-control" name="pay_emailfrom" value="{if isset($bm_prefs.pay_emailfrom)}{text value=$bm_prefs.pay_emailfrom allowEmpty=true}{/if}" placeholder="{lng p="name"}">
					<span class="input-group-text">" <</span>
					<input type="text" class="form-control" name="pay_emailfromemail" value="{email value=$bm_prefs.pay_emailfromemail}" placeholder="{lng p="email"}">
					<span class="input-group-text">></span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="vat"}</label>
			<div class="col-sm-10">
				<select name="mwst" class="form-select">
					<option value="add"{if $bm_prefs.mwst=='add'} selected="selected"{/if}>{lng p="vat_add"}</option>
					<option value="enthalten"{if $bm_prefs.mwst=='enthalten'} selected="selected"{/if}>{lng p="vat_enthalten"}</option>
					<option value="nomwst"{if $bm_prefs.mwst=='nomwst'} selected="selected"{/if}>{lng p="vat_nomwst"}</option>
				</select>
				<small>{lng p="vatratenotice"} <a href="prefs.countries.php?sid={$sid}">{lng p="countries"}</a>.</small>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>PayPal</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="enablechrgpaypal"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="enable_paypal"{if $bm_prefs.enable_paypal=='yes'} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="default"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="default_paymethod" value="1"{if $bm_prefs.default_paymethod==1} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="paypalacc"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="paypal_mail" value="{$bm_prefs.paypal_mail}" placeholder="{lng p="paypalacc"}">
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>sofort&uuml;berweisung.de</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="enablechrgsu"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="enable_su"{if $bm_prefs.enable_su=='yes'} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="default"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="default_paymethod" value="2"{if $bm_prefs.default_paymethod==2} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="sukdnr"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="su_kdnr" value="{$bm_prefs.su_kdnr}" placeholder="{lng p="sukdnr"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="suprjnr"}</label>
			<div class="col-sm-10">
				<div class="input-group">
					<input type="text" class="form-control" name="su_prjnr" value="{$bm_prefs.su_prjnr}" placeholder="{lng p="suprjnr"}">
					<input class="btn btn-muted" type="button" value=" {lng p="su_createnew"} " onclick="window.open('about:blank','suWindow','width=990,height=800,scrollbars=yes,location=yes,menubar=yes,resizable=yes,status=yes,toolbar=yes');EBID('suForm').submit();" />
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="suprjpass"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="su_prjpass" value="{$bm_prefs.su_prjpass}" placeholder="{lng p="suprjpass"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="sunotifypass"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="su_notifypass" value="{$bm_prefs.su_notifypass}" placeholder="{lng p="sunotifypass"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="suinputcheck"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="su_inputcheck"{if $bm_prefs.su_inputcheck=='yes'} checked="checked"{/if}>
				</label>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Skrill (Moneybookers)</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="enablechrgskrill"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="enable_skrill"{if $bm_prefs.enable_skrill=='yes'} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="default"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="default_paymethod" value="3"{if $bm_prefs.default_paymethod==3} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="skrillacc"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="skrill_mail" value="{$bm_prefs.skrill_mail}" placeholder="{lng p="skrillacc"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="skrillsecret"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="skrill_secret" value="{$bm_prefs.skrill_secret}" placeholder="{lng p="skrillsecret"}">
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="banktransfer"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="enablebanktransfer"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="enable_vk"{if $bm_prefs.enable_vk=='yes'} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="default"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="default_paymethod" value="0"{if $bm_prefs.default_paymethod==0} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="kto_inh"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="vk_kto_inh" value="{text allowEmpty=true value=$bm_prefs.vk_kto_inh}" placeholder="{lng p="kto_inh"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="kto_nr"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="vk_kto_nr" value="{text allowEmpty=true value=$bm_prefs.vk_kto_nr}" placeholder="{lng p="kto_nr"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="kto_blz"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="vk_kto_blz" value="{text allowEmpty=true value=$bm_prefs.vk_kto_blz}" placeholder="{lng p="kto_blz"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="kto_inst"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="vk_kto_inst" value="{text allowEmpty=true value=$bm_prefs.vk_kto_inst}" placeholder="{lng p="kto_inst"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="kto_iban"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="vk_kto_iban" value="{text allowEmpty=true value=$bm_prefs.vk_kto_iban}" placeholder="{lng p="kto_iban"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="kto_bic"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="vk_kto_bic" value="{text allowEmpty=true value=$bm_prefs.vk_kto_bic}" placeholder="{lng p="kto_bic"}">
			</div>
		</div>
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
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
