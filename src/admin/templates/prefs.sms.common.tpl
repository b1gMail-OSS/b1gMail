<form action="prefs.sms.php?save=true&sid={$sid}" method="post" onsubmit="spin(this)" id="prefsForm">
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="gateway"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="defaultgateway"}</label>
					<div class="col-sm-8">
						<select name="sms_gateway" class="form-select">
							{foreach from=$gateways item=gateway}
								<option value="{$gateway.id}"{if $gateway.id==$bm_prefs.sms_gateway} selected="selected"{/if}>{text value=$gateway.titel}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="charge"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="enablesmscharge"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="sms_enable_charge"{if $bm_prefs.sms_enable_charge=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="minamount"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="charge_min_amount" value="{$bm_prefs.charge_min_amount}" placeholder="{lng p="croninterval"}">
							<span class="input-group-text">{text value=$bm_prefs.currency}</span>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<fieldset>
		<legend>{lng p="common"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="clndrsmsabs"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="clndr_sms_abs" value="{$bm_prefs.clndr_sms_abs}" placeholder="{lng p="clndrsmsabs"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="clndr_sms_type"}</label>
			<div class="col-sm-10">
				<select name="clndr_sms_type" class="form-select">
					<option value="0">({lng p="defaulttype"})</option>
					{foreach from=$types item=type}
						<option value="{$type.id}"{if $type.id==$bm_prefs.clndr_sms_type} selected="selected"{/if}>{text value=$type.titel}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="mail2smsabs"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mail2sms_abs" value="{$bm_prefs.mail2sms_abs}" placeholder="{lng p="mail2smsabs"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="mail2sms_type"}</label>
			<div class="col-sm-10">
				<select name="mail2sms_type" class="form-select">
					<option value="0">({lng p="defaulttype"})</option>
					{foreach from=$types item=type}
						<option value="{$type.id}"{if $type.id==$bm_prefs.mail2sms_type} selected="selected"{/if}>{text value=$type.titel}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="smsreplyabs"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="smsreply_abs" value="{$bm_prefs.smsreply_abs}" placeholder="{lng p="smsreplyabs"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="smsvalidation_type"}</label>
			<div class="col-sm-10">
				<select name="smsvalidation_type" class="form-select">
					<option value="0">({lng p="defaulttype"})</option>
					{foreach from=$types item=type}
						<option value="{$type.id}"{if $type.id==$bm_prefs.smsvalidation_type} selected="selected"{/if}>{text value=$type.titel}</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
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
