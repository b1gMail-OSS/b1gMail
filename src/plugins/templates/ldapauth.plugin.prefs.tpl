{if $ldapMsg}
<div class="alert alert-{$ldapMsg.type} mb-3" role="alert">{$ldapMsg.text}</div>
{/if}

<fieldset>
	<legend>{lng p="prefs"}</legend>

	<form action="{$pageURL}&sid={$sid}&do=save" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="enable"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="enableAuth"{if $ldap_prefs.enableAuth} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="host"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="ldapserver" value="{if isset($ldap_prefs.ldapserver)}{text value=$ldap_prefs.ldapserver}{/if}" placeholder="{lng p="host"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="port"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="ldapport" value="{if isset($ldap_prefs.ldapport)}{text value=$ldap_prefs.ldapport}{/if}" placeholder="{lng p="port"}">
			</div>
		</div>
		<div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">{lng p="protocol"}</label>
                        <div class="col-sm-10">
                                <select name="ldapprotocol" class="form-select">
                                                <option value="2"{if $ldap_prefs.ldapprotocol=='2'} selected="selected"{/if}>2</option>
                                                <option value="3"{if $ldap_prefs.ldapprotocol=='3'} selected="selected"{/if}>3</option>
                                </select>
                        </div>
                </div>
		<div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">{lng p="encryption"}</label>
                        <div class="col-sm-10">
                                <select name="confidentiality" class="form-select">
						<option value="plain"{if $ldap_prefs.confidentiality=='plain'} selected="selected"{/if}>{lng p="no_encryption"}</option>
						<option value="ssl"{if $ldap_prefs.confidentiality=='ssl'} selected="selected"{/if}>SSL</option>
                                                <option value="tls"{if $ldap_prefs.confidentiality=='tls'} selected="selected"{/if}>TLS</option>
                                </select>
                        </div>
                </div>
		<div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">Proxy {lng p="username"}</label>
                        <div class="col-sm-10">
                                <input type="text" class="form-control" name="proxy_usr" value="{if isset($ldap_prefs.proxy_usr)}{text value=$ldap_prefs.proxy_usr}{/if}" placeholder="{lng p="username"}">
                        </div>
                </div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">Proxy {lng p="password"}</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="proxy_pwd" value="{if isset($ldap_prefs.proxy_pwd)}{text value=$ldap_prefs.proxy_pwd}{/if}" placeholder="{lng p="password"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="ou"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="ou" value="{if isset($ldap_prefs.ou)}{text value=$ldap_prefs.ou}{/if}" placeholder="{lng p="db"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">Base DN</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="basedn" value="{if isset($ldap_prefs.basedn)}{text value=$ldap_prefs.basedn allowEmpty=true}{/if}" placeholder="BaseDN">
			</div>
		</div>
		<div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">LDAP Naming Attribute</label>
                        <div class="col-sm-10">
                                <input type="text" class="form-control" name="ldapnaming" value="{if isset($ldap_prefs.ldapnaming)}{text value=$ldap_prefs.ldapnaming allowEmpty=true}{/if}" placeholder="">
                        </div>
                </div>
		<div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">LDAP Mail Attribute</label>
                        <div class="col-sm-10">
                                <input type="text" class="form-control" name="ldapmail" value="{if isset($ldap_prefs.ldapmail)}{text value=$ldap_prefs.ldapmail allowEmpty=true}{/if}" placeholder="">
                        </div>
                </div>
		<div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">LDAP Inclusion Attribute</label>
                        <div class="col-sm-10">
                                <input type="text" class="form-control" name="inclusionattribute" value="{if isset($ldap_prefs.inclusionattribute)}{text value=$ldap_prefs.inclusionattribute allowEmpty=true}{/if}" placeholder="">
                        </div>
                </div>
		 <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">LDAP Inclusion Attribute value</label>
                        <div class="col-sm-10">
                                <input type="text" class="form-control" name="inclusionvalue" value="{if isset($ldap_prefs.inclusionvalue)}{text value=$ldap_prefs.inclusionvalue allowEmpty=true}{/if}" placeholder="">
                        </div>
                </div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="user"}-{lng p="domain"}</label>
			<div class="col-sm-10">
				<select name="userDomain" class="form-select">
					{foreach from=$domains item=domain}
						<option value="{$domain}"{if $ldap_prefs.userDomain==$domain} selected="selected"{/if}>{$domain}</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="text-end">
			<button type="submit" name="save" value="1" class="btn btn-primary">{lng p="save"}</button>
		</div>
	</form>
</fieldset>
