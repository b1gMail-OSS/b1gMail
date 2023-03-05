<form action="{$pageURL}&sid={$sid}&action=imap&save=true" method="post" onsubmit="spin(this)">
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="common"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_greeting"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="imapgreeting" value="{if isset($bms_prefs.imapgreeting)}{text value=$bms_prefs.imapgreeting allowEmpty=true}{/if}" placeholder="{lng p="bms_greeting"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_timeout"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="pop3_timeout" value="{$bms_prefs.pop3_timeout}" placeholder="{lng p="bms_timeout"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_idle_poll"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="imap_idle_poll" value="{$bms_prefs.imap_idle_poll}" placeholder="{lng p="bms_idle_poll"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_mysqlconnection"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="imap_mysqlclose"{if $bms_prefs.imap_mysqlclose==1} checked="checked"{/if} id="imap_mysqlclose">
							<span class="form-check-label">{lng p="bms_closewhenidle"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="imap_idle_mysqlclose"{if $bms_prefs.imap_idle_mysqlclose==1} checked="checked"{/if} id="imap_idle_mysqlclose">
							<span class="form-check-label">{lng p="bms_closeduringidle"}</span>
						</label>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_intfolders"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="imap_intelligentfolders"{if $bms_prefs.imap_intelligentfolders==1} checked="checked"{/if} id="imap_intelligentfolders">
							<span class="form-check-label">{lng p="show"}</span>
						</label>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_autoexpunge"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="imap_autoexpunge"{if $bms_prefs.imap_autoexpunge==1} checked="checked"{/if} id="imap_autoexpunge">
							<span class="form-check-label">{lng p="enable"}</span>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="bms_foldernames"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_folder_inbox"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" value="INBOX" disabled="disabled">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_folder_sent"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="imap_folder_sent" value="{if isset($bms_prefs.imap_folder_sent)}{text value=$bms_prefs.imap_folder_sent allowEmpty=true}{/if}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_folder_spam"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="imap_folder_spam" value="{if isset($bms_prefs.imap_folder_spam)}{text value=$bms_prefs.imap_folder_spam allowEmpty=true}{/if}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_folder_drafts"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="imap_folder_drafts" value="{if isset($bms_prefs.imap_folder_drafts)}{text value=$bms_prefs.imap_folder_drafts allowEmpty=true}{/if}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_folder_trash"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="imap_folder_trash" value="{if isset($bms_prefs.imap_folder_trash)}{text value=$bms_prefs.imap_folder_trash allowEmpty=true}{/if}">
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="bms_imaplimit"}</legend>

				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_user_choseimaplimit"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="user_choseimaplimit"{if $bms_prefs.user_choseimaplimit} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_imaplimit"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="imap_limit" value="{$bms_prefs.imap_limit}" placeholder="{lng p="bms_imaplimit"}">
							<span class="input-group-text">{lng p="emails"} <small>({lng p="bms_zerolimit"})</small></span>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="bms_apns"}</legend>

				<div class="alert alert-warning">{lng p="bms_apnsqueuerestartnote"}</div>

				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_apns"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="apns_enable"{if $bms_prefs.apns_enable==1} checked="checked"{/if} id="apns_enable"{if !$apnsSet} disabled="disabled"{/if}>
							<span class="form-check-label">{lng p="enable"}</span>
						</label>
						{if !$apnsSet}
							<div class="input-group">
								<i class="fa-solid fa-triangle-exclamation"></i> {lng p="bms_apnsnote"}
							</div>
						{/if}
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_serverport"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="apns_host" value="{if isset($bms_prefs.apns_host)}{text value=$bms_prefs.apns_host allowEmpty=true}{/if}">
							<span class="input-group-text">:</span>
							<input type="text" class="form-control" name="apns_port" value="{if isset($bms_prefs.apns_port)}{text value=$bms_prefs.apns_port allowEmpty=true}{/if}">
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_pushcertificate"}</label>
					<div class="col-sm-8">
						<div class="input-group input-group-sm mb-2">
							<span class="input-group-text">
								{if !$apnsSet||!$apnsValid}<i class="fa-solid fa-circle-xmark text-red"></i>{else}<i class="fa-solid fa-circle-check text-green"></i>{/if}&nbsp;
								{if $apnsSet}
									{lng p="bms_setvaliduntil"}
									{date timestamp=$apnsValidUntil dayonly=true}
								{else}
									{lng p="bms_notset"}
								{/if}
							</span>
							<input class="btn btn-sm btn-primary" type="button" value=" {lng p="setedit"} " onclick="document.location.href='{$pageURL}&sid={$sid}&action=imap&do=apns';" />
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
