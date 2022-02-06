<form action="{$pageURL}&action=prefs&save=true&sid={$sid}" method="post" id="prefsForm" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="common"}</legend>
		
		<table>
			<tr>
				<td class="td1" width="220">{lng p="pacc_delete_order"}:</td>
				<td class="td2"><input id="delete_order" name="delete_order"{if $pacc_prefs.delete_order=='yes'} checked="checked"{/if} type="checkbox" /><label for="delete_order"> {lng p="pacc_after"} </label><input type="text" name="delete_order_after" value="{$pacc_prefs.delete_order_after/86400}" size="4" /> {lng p="pacc_afterdays"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_update_notification"}:</td>
				<td class="td2">
					<input id="send_update_notification" name="send_update_notification"{if $pacc_prefs.send_update_notification=='yes'} checked="checked"{/if} type="checkbox" /> <input type="text" name="update_notification_days" value="{text value=$pacc_prefs.update_notification_days allowEmpty=true}" size="4" /> {lng p="pacc_before_expiration"}<br />
					<input id="update_notification_altmail" name="update_notification_altmail"{if $pacc_prefs.update_notification_altmail=='yes'} checked="checked"{/if} type="checkbox" />
					<label for="update_notification_altmail">{lng p="pacc_update_notification_altmail"}</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_fields"}:</td>
				<td class="td2"><input class="button" type="button" value=" {lng p="pacc_viewedit"} " onclick="document.location.href='{$pageURL}&action=prefs&do=featureFields&sid={$sid}';" /></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="signup"}</legend>
		
		<table>
			<tr>
				<td class="td1" width="220">{lng p="pacc_signup_order"}?</td>
				<td class="td2"><input id="signup_order_page" name="signup_order_page"{if $pacc_prefs.signup_order_page=='yes'} checked="checked"{/if} type="checkbox" onclick="if(!this.checked) EBID('signup_order_force').checked=false;" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_signup_order_force"}?</td>
				<td class="td2"><input id="signup_order_force" name="signup_order_force"{if $pacc_prefs.signup_order_force=='yes'} checked="checked"{/if} type="checkbox" onclick="if(this.checked) EBID('signup_order_page').checked=true;" /></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="nli"}</legend>
		
		<table>
			<tr>
				<td class="td1" width="220">{lng p="pacc_nlipackages"}?</td>
				<td class="td2"><select name="nli_packages_page" id="nli_packages_page">
					<option value="yes"{if $pacc_prefs.nli_packages_page=='yes'} selected="selected"{/if}>{lng p="pacc_nlipack_yes"}</option>
					<option value="replace"{if $pacc_prefs.nli_packages_page=='replace'} selected="selected"{/if}>{lng p="pacc_nlipack_replace"}</option>
					<option value="no"{if $pacc_prefs.nli_packages_page=='no'} selected="selected"{/if}>{lng p="pacc_nlipack_no"}</option>
				</select></td>
			</tr>
		</table>
	</fieldset>
	
	<p>
		<div style="float:right;">
			<input class="button" type="submit" value=" {lng p="save"} " />&nbsp;
		</div>
	</p>
</form>
