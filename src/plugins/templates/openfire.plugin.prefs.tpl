{if $ofMsg}
<div class="alert alert-{$ofMsg.type} mb-3" role="alert">{$ofMsg.text}</div>
{/if}

<form action="{sessionurl file='plugin.page.php' params="plugin={$ofPlugin}"}" method="post" onsubmit="spin(this)">
	{csrffield}

	<div class="card">
		<div class="card-header">
			<h3 class="card-title">
				<img src="../plugins/templates/images/openfire_logo.png" alt="" width="24" height="24" class="me-2 align-text-bottom" />
				{lng p="prefs"}
			</h3>
		</div>
		<div class="card-body">
			<div class="mb-3">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="openfire_enableAuth" id="openfire_enableAuth"{if $openfire_prefs.enableAuth} checked="checked"{/if} />
					<span class="form-check-label">{lng p="enable"}</span>
				</label>
			</div>

			<div class="mb-3">
				<label class="form-label" for="openfire_domain">{lng p="openfire_domain"}</label>
				<input type="text" class="form-control" name="openfire_domain" id="openfire_domain" value="{if isset($openfire_prefs.domain)}{text value=$openfire_prefs.domain}{/if}" placeholder="{lng p="openfire_domain"}" required="required" />
			</div>

			<div class="mb-3">
				<label class="form-label" for="openfire_port">{lng p="openfire_port"}</label>
				<input type="number" class="form-control" name="openfire_port" id="openfire_port" value="{if isset($openfire_prefs.port)}{$openfire_prefs.port}{/if}" placeholder="{lng p="openfire_port"}" min="1" max="65535" step="1" required="required" />
			</div>

			<div class="mb-3">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="openfire_https" id="openfire_https"{if !empty($openfire_prefs.https)} checked="checked"{/if} />
					<span class="form-check-label">{lng p="openfire_https"}</span>
				</label>
			</div>

			<div class="mb-3">
				<label class="form-label" for="openfire_userservice_secretkey">{lng p="openfire_secretkey"}</label>
				<input type="password" class="form-control" name="openfire_userservice_secretkey" id="openfire_userservice_secretkey" value="{if isset($openfire_prefs.secretkey)}{text value=$openfire_prefs.secretkey}{/if}" placeholder="{lng p="openfire_secretkey"}" autocomplete="off" />
			</div>
		</div>
		<div class="card-footer text-end">
			<button type="submit" name="save" value="1" class="btn btn-primary">
				<i class="ti ti-device-floppy me-1"></i>
				{lng p="save"}
			</button>
		</div>
	</div>
</form>

<div class="text-center text-secondary mt-3"><small>b1gMail Openfire-Integration &copy; <a href="http://www.sebijk.com" target="_blank" rel="noreferrer">Home of the Sebijk.com</a></small></div>
