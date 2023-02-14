<form action="prefs.email.php?action=receive&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="recvmethod"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="recvmethod"}</label>
			<div class="col-sm-10">
				<select name="receive_method" class="form-select">
					<option value="pop3"{if $bm_prefs.receive_method=='pop3'} selected="selected"{/if}>{lng p="pop3gateway"}</option>
					<option value="pipe"{if $bm_prefs.receive_method=='pipe'} selected="selected"{/if}>{lng p="pipeetc"}</option>
				</select>
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="pop3gateway"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="pop3host"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="pop3_host" value="{text allowEmpty=true value=$bm_prefs.pop3_host}" placeholder="{lng p="pop3host"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="pop3port"}</label>
					<div class="col-sm-8">
						<input type="number" class="form-control" name="pop3_port" value="{$bm_prefs.pop3_port}" placeholder="{lng p="pop3port"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="pop3user"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="pop3_user" value="{text allowEmpty=true value=$bm_prefs.pop3_user}" placeholder="{lng p="pop3user"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="pop3pass"}</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" name="pop3_host" value="{text allowEmpty=true value=$bm_prefs.pop3_pass}" placeholder="{lng p="pop3pass"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="fetchcount"}</label>
					<div class="col-sm-8">
						<input type="number" class="form-control" name="fetchcount" value="{$bm_prefs.fetchcount}" placeholder="{lng p="fetchcount"}">
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="miscprefs"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="recpdetection"}</label>
					<div class="col-sm-8">
						<select name="recipient_detection" class="form-select">
							<option value="static"{if $bm_prefs.recipient_detection=='static'} selected="selected"{/if}>{lng p="rd_static"}</option>
							<option value="dynamic"{if $bm_prefs.recipient_detection=='dynamic'} selected="selected"{/if}>{lng p="rd_dynamic"}</option>
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="errormail"}</label>
					<div class="col-sm-8">
						<select name="errormail" class="form-select">
							<option value="yes"{if $bm_prefs.errormail=='yes'} selected="selected"{/if}>{lng p="yes"}</option>
							<option value="no"{if $bm_prefs.errormail=='no'} selected="selected"{/if}>{lng p="no"}</option>
							<option value="soft"{if $bm_prefs.errormail=='soft'} selected="selected"{/if}>{lng p="errormail_soft"}</option>
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="detectduplicates"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="detect_duplicates"{if $bm_prefs.detect_duplicates=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="returnpathcheck"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="returnpath_check"{if $bm_prefs.returnpath_check=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="failure_forward"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="failure_forward"{if $bm_prefs.failure_forward=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="mailmax"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="mailmax" value="{$bm_prefs.mailmax/1024}" placeholder="{lng p="mailmax"}">
							<span class="input-group-text">KB</span>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />&nbsp;
	</div>
</form>
