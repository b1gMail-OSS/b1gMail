<fieldset>
	<legend>{lng p="prefs"}</legend>

	<form action="{$pageURL}&sid={$sid}" name="save" id="save" method="post" onsubmit="spin(this)">
	{if $erfolg}<div class="alert alert-success">{$erfolg}</div>{/if}

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="enable"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="openfire_enableAuth"{if $openfire_prefs.enableAuth} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="openfire_domain"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="openfire_domain" value="{if isset($openfire_prefs.domain)}{text value=$openfire_prefs.domain}{/if}" placeholder="{lng p="openfire_domain"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="openfire_port"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="openfire_domain" value="{if isset($openfire_prefs.domain)}{text value=$openfire_prefs.domain}{/if}" placeholder="{lng p="openfire_port"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="openfire_https"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="openfire_https"{if !empty($openfire_prefs.https)} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="openfire_secretkey"}</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="openfire_userservice_secretkey" value="{if isset($openfire_prefs.secretkey)}{text value=$openfire_prefs.secretkey}{/if}" placeholder="{lng p="openfire_secretkey"}">
			</div>
		</div>

		<div class="text-end">
			<input type="submit" name="save" value="{lng p="save"}" class="btn btn-primary" />
		</div>
	</form>
</fieldset>

<div class="text-center"><small>b1gMail Openfire-Integration &copy; <a href="http://www.sebijk.com" target="_blank" rel="noreferrer">Home of the Sebijk.com</a></small></div>