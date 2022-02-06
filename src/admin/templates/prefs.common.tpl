<form action="prefs.common.php?save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="common"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="14"><img src="{$tpldir}images/ico_prefs_common.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="projecttitle"}:</td>
				<td class="td2"><input type="text" name="titel" value="{text allowEmpty=true value=$bm_prefs.titel}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="hostname"}:</td>
				<td class="td2"><input type="text" name="b1gmta_host" value="{text allowEmpty=true value=$bm_prefs.b1gmta_host}" size="28" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="selffolder"}:</td>
				<td class="td2"><input type="text" name="selffolder" value="{text allowEmpty=true value=$bm_prefs.selffolder}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="selfurl"}:</td>
				<td class="td2"><input type="text" name="selfurl" value="{text allowEmpty=true value=$bm_prefs.selfurl}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="mobile_url"}:</td>
				<td class="td2"><input type="text" name="mobile_url" value="{text allowEmpty=true value=$bm_prefs.mobile_url}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="searchengine"}:</td>
				<td class="td2"><input type="text" name="search_engine" value="{text allowEmpty=true value=$bm_prefs.search_engine}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="croninterval"}:</td>
				<td class="td2"><input type="number" min="1" step="1" name="cron_interval" value="{text allowEmpty=true value=$bm_prefs.cron_interval}" size="6" /> {lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="auto_tz"}?</td>
				<td class="td2"><input name="auto_tz"{if $bm_prefs.auto_tz=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="sessioniplock"}?</td>
				<td class="td2"><input name="ip_lock"{if $bm_prefs.ip_lock=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="sessioncookielock"}?</td>
				<td class="td2"><input name="cookie_lock"{if $bm_prefs.cookie_lock=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="compresspages"}?</td>
				<td class="td2"><input name="compress_pages"{if $bm_prefs.compress_pages=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="maintmode"}?</td>
				<td class="td2"><input name="wartung"{if $bm_prefs.wartung=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="nliarea"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/template32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="domain_combobox"}?</td>
				<td class="td2"><input name="domain_combobox"{if $bm_prefs.domain_combobox=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="redirectmobile"}?</td>
				<td class="td2"><input name="redirect_mobile"{if $bm_prefs.redirect_mobile=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="contactform"}?</td>
				<td class="td2"><input name="contactform" id="contactform"{if $bm_prefs.contactform=='yes'} checked="checked"{/if} type="checkbox" />
					<label for="contactform"> {lng p="to2"}: </label><input type="text" name="contactform_to" value="{email value=$bm_prefs.contactform_to}" size="24" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="contactform_name"}?</td>
				<td class="td2"><input name="contactform_name"{if $bm_prefs.contactform_name=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="contactform_subject"}?</td>
				<td class="td2"><input name="contactform_subject"{if $bm_prefs.contactform_subject=='yes'} checked="checked"{/if} type="checkbox" />
					&nbsp;
					<small>{lng p="cfs_note"}
						<a href="prefs.languages.php?action=texts&sid={$sid}#contact_subjects">&raquo; {lng p="customtexts"}</a></small></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="ssl"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="4"><img src="{$tpldir}images/ico_prefs_ssl.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="ssl_url"}:</td>
				<td class="td2"><input type="text" name="ssl_url" value="{text allowEmpty=true value=$bm_prefs.ssl_url}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="ssl_login_option"}?</td>
				<td class="td2"><input name="ssl_login_option"{if $bm_prefs.ssl_login_option=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="ssl_login_enable"}?</td>
				<td class="td2"><input name="ssl_login_enable"{if $bm_prefs.ssl_login_enable=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="ssl_signup_enable"}?</td>
				<td class="td2"><input name="ssl_signup_enable"{if $bm_prefs.ssl_signup_enable=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="datastorage"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/ico_prefs_storage.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="datafolder"}:</td>
				<td class="td2"><input type="text" name="datafolder" value="{text allowEmpty=true value=$bm_prefs.datafolder}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="structstorage"}:</td>
				<td class="td2"><input type="checkbox" name="structstorage"{if $bm_prefs.structstorage=='yes'&&!$safemode} checked="checked"{/if}{if $safemode} disabled="disabled"{/if} /></td>
			</tr>
		</table>

		{if $safemode}
		<p>
			<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
			{lng p="structsafewarn"}
		</p>
		{/if}
	</fieldset>

	<fieldset>
		<legend>{lng p="logs"}</legend>
		<table>
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/filter.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="log_autodelete"}:</td>
				<td class="td2">
					<input type="checkbox" id="logs_autodelete" name="logs_autodelete"{if $bm_prefs.logs_autodelete=='yes'} checked="checked"{/if} />
					<label for="logs_autodelete">{lng p="enableolder"}</label>
					<input type="number" name="logs_autodelete_days" value="{text value=$bm_prefs.logs_autodelete_days}" size="4" min="1" step="1" />
					{lng p="days"}<br />
					<input type="checkbox" id="logs_autodelete_archive" name="logs_autodelete_archive"{if $bm_prefs.logs_autodelete_archive=='yes'} checked="checked"{/if} />
					<label for="logs_autodelete_archive">{lng p="savearc"}</label>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="users"}</legend>
		<table>
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/ico_prefs_users.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="logouturl"}:</td>
				<td class="td2"><input type="text" name="logouturl" value="{text allowEmpty=true value=$bm_prefs.logouturl}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="savehistory"}?</td>
				<td class="td2"><input name="contact_history"{if $bm_prefs.contact_history=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="gutregged"}?</td>
				<td class="td2"><input name="gut_regged"{if $bm_prefs.gut_regged=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="autocancel"}?</td>
				<td class="td2"><input name="autocancel"{if $bm_prefs.autocancel=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="notifications"}</legend>
		<table>
			<tr>
				<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/ico_notify.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="notifyinterval"}:</td>
				<td class="td2"><input type="number" name="notify_interval" value="{$bm_prefs.notify_interval}" size="6" min="1" step="1" /> {lng p="seconds"}</td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="notifylifetime"}:</td>
				<td class="td2"><input type="number" name="notify_lifetime" value="{$bm_prefs.notify_lifetime}" size="6" min="1" step="1" /> {lng p="days2"}</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="defaults"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="7"><img src="{$tpldir}images/ico_prefs_defaults.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="language"}:</td>
				<td class="td2"><select name="language">
				{foreach from=$languages item=lang key=langID}
					<option value="{$langID}"{if $langID==$bm_prefs.language} selected="selected"{/if}>{text value=$lang.title}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="country"}:</td>
				<td class="td2"><select name="std_land">
				{foreach from=$countries item=country key=countryID}
					<option value="{$countryID}"{if $countryID==$bm_prefs.std_land} selected="selected"{/if}>{text value=$country}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="dateformat"}:</td>
				<td class="td2"><input type="text" name="datumsformat" value="{$bm_prefs.datumsformat}" size="16" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="itemsperpage"}:</td>
				<td class="td2"><input type="number" min="1" step="1" name="ordner_proseite" value="{$bm_prefs.ordner_proseite}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="mail_groupmode"}:</td>
				<td class="td2"><select name="mail_groupmode">
					<option value="-"{if $bm_prefs.mail_groupmode=='-'} selected="selected"{/if}>------------</option>

					<optgroup label="{lng p="props"}">
						<option value="fetched"{if $bm_prefs.mail_groupmode=='fetched'} selected="selected"{/if}>{lng p="date"}</option>
						<option value="von"{if $bm_prefs.mail_groupmode=='von'} selected="selected"{/if}>{lng p="from"}</option>
					</optgroup>

					<optgroup label="{lng p="flags"}">
						<option value="gelesen"{if $bm_prefs.mail_groupmode=='gelesen'} selected="selected"{/if}>{lng p="read"}</option>
						<option value="beantwortet"{if $bm_prefs.mail_groupmode=='beantwortet'} selected="selected"{/if}>{lng p="answered"}</option>
						<option value="weitergeleitet"{if $bm_prefs.mail_groupmode=='weitergeleitet'} selected="selected"{/if}>{lng p="forwarded"}</option>
						<option value="flagged"{if $bm_prefs.mail_groupmode=='flagged'} selected="selected"{/if}>{lng p="flagged"}</option>
						<option value="done"{if $bm_prefs.mail_groupmode=='done'} selected="selected"{/if}>{lng p="done"}</option>
						<option value="attach"{if $bm_prefs.mail_groupmode=='attach'} selected="selected"{/if}>{lng p="attachment"}</option>
						<option value="color"{if $bm_prefs.mail_groupmode=='color'} selected="selected"{/if}>{lng p="color"}</option>
					</optgroup>
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="calendarviewmode"}:</td>
				<td class="td2"><select name="calendar_defaultviewmode">
					<option value="day"{if $bm_prefs.calendar_defaultviewmode=='day'} selected="selected"{/if}>{lng p="day"}</option>
					<option value="week"{if $bm_prefs.calendar_defaultviewmode=='week'} selected="selected"{/if}>{lng p="week"}</option>
					<option value="month"{if $bm_prefs.calendar_defaultviewmode=='month'} selected="selected"{/if}>{lng p="month"}</option>
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="hotkeys"}?</td>
				<td class="td2"><input name="hotkeys_default"{if $bm_prefs.hotkeys_default=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
		</table>
	</fieldset>

	<p>
		<div style="float:right;" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
