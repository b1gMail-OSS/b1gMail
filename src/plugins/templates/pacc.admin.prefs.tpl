<form action="{$pageURL}&action=prefs&save=true&sid={$sid}" method="post" id="prefsForm" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="common"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="pacc_delete_order"}</label>
			<div class="col-sm-10">
				<div class="input-group">
					<span class="input-group-text">
                    	<input class="form-check-input m-0" type="checkbox" id="delete_order" name="delete_order"{if $pacc_prefs.delete_order=='yes'} checked="checked"{/if}>
					</span>
					<span class="input-group-text">{lng p="pacc_after"}</span>
					<input type="text" class="form-control" name="delete_order_after" value="{$pacc_prefs.delete_order_after/86400}">
					<span class="input-group-text">{lng p="pacc_afterdays"}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="pacc_update_notification"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<span class="input-group-text">
                    	<input class="form-check-input m-0" type="checkbox" id="send_update_notification" name="send_update_notification"{if $pacc_prefs.send_update_notification=='yes'} checked="checked"{/if}>
					</span>
					<input type="text" class="form-control" name="update_notification_days" value="{if isset($pacc_prefs.update_notification_days)}{text value=$pacc_prefs.update_notification_days allowEmpty=true}{/if}">
					<span class="input-group-text">{lng p="pacc_before_expiration"}</span>
				</div>
				<div class="input-group mb-2">
					<span class="input-group-text">
                    	<input class="form-check-input m-0" type="checkbox" id="update_notification_altmail" name="update_notification_altmail"{if $pacc_prefs.update_notification_altmail=='yes'} checked="checked"{/if}>
					</span>
					<input type="text" class="form-control" value="{lng p="pacc_update_notification_altmail"}" disabled>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="pacc_fields"}</label>
			<div class="col-sm-10">
				<input class="btn btn-sm btn-muted" type="button" value=" {lng p="pacc_viewedit"} " onclick="document.location.href='{$pageURL}&action=prefs&do=featureFields&sid={$sid}';" />
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="signup"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="pacc_signup_order"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" id="signup_order_page" name="signup_order_page"{if $pacc_prefs.signup_order_page=='yes'} checked="checked"{/if} onclick="if(!this.checked) EBID('signup_order_force').checked=false;">
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="pacc_signup_order_force"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" id="signup_order_force" name="signup_order_force"{if $pacc_prefs.signup_order_force=='yes'} checked="checked"{/if} onclick="if(this.checked) EBID('signup_order_page').checked=true;">
				</label>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="nli"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="pacc_nlipackages"}</label>
			<div class="col-sm-10">
				<select name="nli_packages_page" id="nli_packages_page" class="form-select">
					<option value="yes"{if $pacc_prefs.nli_packages_page=='yes'} selected="selected"{/if}>{lng p="pacc_nlipack_yes"}</option>
					<option value="replace"{if $pacc_prefs.nli_packages_page=='replace'} selected="selected"{/if}>{lng p="pacc_nlipack_replace"}</option>
					<option value="no"{if $pacc_prefs.nli_packages_page=='no'} selected="selected"{/if}>{lng p="pacc_nlipack_no"}</option>
				</select>
			</div>
		</div>
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />&nbsp;
	</div>
</form>
