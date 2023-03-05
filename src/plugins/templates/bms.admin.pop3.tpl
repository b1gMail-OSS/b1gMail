<form action="{$pageURL}&sid={$sid}&action=pop3&save=true" method="post" onsubmit="spin(this)">
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="common"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bms_greeting"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="pop3greeting" value="{if isset($bms_prefs.pop3greeting)}{text value=$bms_prefs.pop3greeting allowEmpty=true}{/if}" placeholder="{lng p="bms_greeting"}">
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
					<label class="col-sm-4 col-form-label">{lng p="bms_altpop3"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
                        <span class="input-group-text">
                        	<input class="form-check-input m-0" type="checkbox" name="altpop3_enable"{if $bms_prefs.altpop3!=0} checked="checked"{/if}>
                        </span>
							<span class="input-group-text">{lng p="bms_toport"}</span>
							<input type="text" class="form-control" name="altpop3_port" value="{$bms_prefs.altpop3}" placeholder="{lng p="bms_toport"}">
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="bms_folderstofetch"}</legend>

				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="bms_user_chosepop3folders"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="user_chosepop3folders"{if $bms_prefs.user_chosepop3folders} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-check-label">{lng p="folders"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="pop3_folders[]" value="0" id="pop3_folders_0"{if $pop3Folders.0} checked="checked"{/if}>
							<span class="form-check-label">{lng p="bms_folder_inbox"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="pop3_folders[]" value="-4" id="pop3_folders_-4"{if $pop3Folders.m4} checked="checked"{/if}>
							<span class="form-check-label">{lng p="bms_folder_spam"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="pop3_folders[]" value="-5" id="pop3_folders_-5"{if $pop3Folders.m5} checked="checked"{/if}>
							<span class="form-check-label">{lng p="bms_folder_trash"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="pop3_folders[]" value="-128" id="pop3_folders_-128"{if $pop3Folders.m128} checked="checked"{/if}>
							<span class="form-check-label">{lng p="bms_userfolders"}</span>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value=" {lng p="save"} " />
	</div>
</form>
