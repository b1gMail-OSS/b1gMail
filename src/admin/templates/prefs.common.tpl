<form action="{sessionurl file='prefs.common.php' params="save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="common"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="projecttitle"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="titel" value="{text allowEmpty=true value=$bm_prefs.titel}" placeholder="{lng p="projecttitle"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="hostname"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="b1gmta_host" value="{text allowEmpty=true value=$bm_prefs.b1gmta_host}" placeholder="{lng p="hostname"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="selffolder"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="selffolder" value="{text allowEmpty=true value=$bm_prefs.selffolder}" placeholder="{lng p="selffolder"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="selfurl"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="selfurl" value="{text allowEmpty=true value=$bm_prefs.selfurl}" placeholder="{lng p="selfurl"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="mobile_url"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="mobile_url" value="{text allowEmpty=true value=$bm_prefs.mobile_url}" placeholder="{lng p="mobile_url"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="searchengine"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="search_engine" value="{text allowEmpty=true value=$bm_prefs.search_engine}" placeholder="{lng p="searchengine"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="croninterval"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="cron_interval" value="{text allowEmpty=true value=$bm_prefs.cron_interval}" placeholder="{lng p="croninterval"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="cronauth"}</label>
					<div class="col-sm-8">
						<p class="form-text text-secondary mb-2">{lng p="cronauthdesc"}</p>
						<label class="form-label">{lng p="croncli"}</label>
						<input type="text" class="form-control form-control-sm mb-2" readonly="readonly" value="{text allowEmpty=true value=$cronCliCmd}">
						<label class="form-label">{lng p="cronhttp"}</label>
						<input type="text" class="form-control form-control-sm mb-2" readonly="readonly" value="{text allowEmpty=true value=$cronHttpUrl}">
						<button type="submit" class="btn btn-outline-secondary btn-sm" name="regen_cron_secret" value="1">{lng p="cronregen"}</button>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="clientapi"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="clientapi_enable"{if $bm_prefs.clientapi_enable=='yes'} checked="checked"{/if}>
							<span class="form-check-label">{lng p="clientapidesc"}</span>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="auto_tz"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="auto_tz"{if $bm_prefs.auto_tz=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="compresspages"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="compress_pages"{if $bm_prefs.compress_pages=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="nliarea"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="contactform"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
                        <span class="input-group-text">
                        	<input class="form-check-input m-0" type="checkbox" name="contactform"{if $bm_prefs.contactform=='yes'} checked="checked"{/if}>
                        </span>
							<span class="input-group-text">{lng p="to2"}:</span>
							<input type="text" class="form-control" name="contactform_to" value="{email value=$bm_prefs.contactform_to}" placeholder="{lng p="contactform"}">
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="contactform_name"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="contactform_name"{if $bm_prefs.contactform_name=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="contactform_subject"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="contactform_subject"{if $bm_prefs.contactform_subject=='yes'} checked="checked"{/if}>
							<span class="form-check-label">{lng p="cfs_note"}<a href="{sessionurl file='prefs.languages.php' params='action=texts'}#contact_subjects">&raquo; {lng p="customtexts"}</a></span>
						</label>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="ssl"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="ssl_signup_enable"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="ssl_signup_enable"{if $bm_prefs.ssl_signup_enable=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="datastorage"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="datafolder"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="datafolder" value="{text allowEmpty=true value=$bm_prefs.datafolder}" placeholder="{lng p="datafolder"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="structstorage"}</label>
					<div class="col-sm-8">
						<input class="form-check-input" type="checkbox" name="structstorage"{if $bm_prefs.structstorage=='yes'&&!$safemode} checked="checked"{/if}>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="logs"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="log_autodelete"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
                        <span class="input-group-text">
                        	<input class="form-check-input m-0" type="checkbox" name="logs_autodelete"{if $bm_prefs.logs_autodelete=='yes'} checked="checked"{/if}>
                        </span>
							<span class="input-group-text">{lng p="enableolder"}:</span>
							<input type="text" class="form-control" name="logs_autodelete_days" value="{if isset($bm_prefs.logs_autodelete_days)}{text value=$bm_prefs.logs_autodelete_days}{/if}" placeholder="{lng p="contactform"}">
							<span class="input-group-text">{lng p="days"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="savearc"}</label>
					<div class="col-sm-8">
						<input class="form-check-input" type="checkbox" name="logs_autodelete_archive"{if $bm_prefs.logs_autodelete_archive=='yes'&&!$safemode} checked="checked"{/if}>
					</div>
					</label>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="users"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="gutregged"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="gut_regged"{if $bm_prefs.gut_regged=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="autocancel"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="autocancel"{if $bm_prefs.autocancel=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="defaults"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="language"}</label>
					<div class="col-sm-8">
						<select name="language" class="form-select">
							{foreach from=$languages item=lang key=langID}
								<option value="{$langID}"{if $langID==$bm_prefs.language} selected="selected"{/if}>{text value=$lang.title}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="country"}</label>
					<div class="col-sm-8">
						<select name="std_land" class="form-select">
							{foreach from=$countries item=country key=countryID}
								<option value="{$countryID}"{if $countryID==$bm_prefs.std_land} selected="selected"{/if}>{text value=$country}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="dateformat"}</label><div class="col-sm-8">
						<input type="text" class="form-control" name="datumsformat" value="{$bm_prefs.datumsformat}" placeholder="{lng p="dateformat"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="itemsperpage"}</label>
					<div class="col-sm-8">
						<input type="number" min="1" step="1" class="form-control" name="ordner_proseite" value="{$bm_prefs.ordner_proseite}" placeholder="{lng p="itemsperpage"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="mail_groupmode"}</label>
					<div class="col-sm-8">
						<select name="mail_groupmode" class="form-select">
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
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="calendarviewmode"}</label>
					<div class="col-sm-8">
						<select name="calendar_defaultviewmode" class="form-select">
							<option value="day"{if $bm_prefs.calendar_defaultviewmode=='day'} selected="selected"{/if}>{lng p="day"}</option>
							<option value="week"{if $bm_prefs.calendar_defaultviewmode=='week'} selected="selected"{/if}>{lng p="week"}</option>
							<option value="month"{if $bm_prefs.calendar_defaultviewmode=='month'} selected="selected"{/if}>{lng p="month"}</option>
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="hotkeys"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="hotkeys_default"{if $bm_prefs.hotkeys_default=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="maintmode"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="maintmode"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="wartung"{if $bm_prefs.wartung=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="whitelist"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="wartung_whitelist" value="{text allowEmpty=true value=$wartungwhitelist}" placeholder="{lng p="whitelist"}">
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="push_settings"}</legend>

				{if isset($pushVapidResult)}
				<div class="alert alert-{if $pushVapidResult=='ok'}success{elseif $pushVapidResult=='openssl'}warning{else}danger{/if} mb-3" role="alert">
					{if $pushVapidResult=='ok'}{lng p="push_vapid_generated_ok"}
					{elseif $pushVapidResult=='openssl'}{lng p="push_vapid_openssl_missing"}
					{else}{lng p="push_vapid_generated_fail"}{/if}
				</div>
				{/if}

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="push_enable_global"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="push_enabled"{if $bm_prefs.push_enabled|default:'no'=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="push_vapid_subject"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="push_vapid_subject" value="{text allowEmpty=true value=$bm_prefs.push_vapid_subject|default:''}" placeholder="mailto:admin@example.com">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="push_vapid_keys"}</label>
					<div class="col-sm-8">
						{if $bm_prefs.push_vapid_public|default:''}
						<p class="text-muted small mb-2">{lng p="push_vapid_configured"}</p>
						{else}
						<p class="text-muted small mb-2">{lng p="push_vapid_missing"}</p>
						{/if}
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="notifications"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="notifyinterval"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="number" class="form-control" name="notify_interval" value="{$bm_prefs.notify_interval}" placeholder="{lng p="notifyinterval"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="notifylifetime"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="number" class="form-control" name="notify_lifetime" value="{$bm_prefs.notify_lifetime}" placeholder="{lng p="notifylifetime"}">
							<span class="input-group-text">{lng p="days2"}</span>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="text-end">
		<a class="btn btn-outline-secondary me-2" href="{sessionurl file='prefs.common.php' params='action=generateVapid'}">{lng p="push_generate_vapid"}</a>
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
