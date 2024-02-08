<fieldset>
	<legend>{lng p="prefs"}</legend>
	
	<form action="{$pageURL}&sid={$sid}&do=save" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="enable"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="enableAuth"{if $vb_prefs.enableAuth} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">MySQL {lng p="host"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mysqlHost" value="{if isset($vb_prefs.mysqlHost)}{text value=$vb_prefs.mysqlHost}{/if}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">MySQL {lng p="user"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mysqlUser" value="{if isset($vb_prefs.mysqlUser)}{text value=$vb_prefs.mysqlUser}{/if}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">MySQL {lng p="password"}</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="mysqlPass" value="{if isset($vb_prefs.mysqlPass)}{text value=$vb_prefs.mysqlPass}{/if}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">MySQL {lng p="db"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mysqlDB" value="{if isset($vb_prefs.mysqlDB)}{text value=$vb_prefs.mysqlDB}{/if}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">MySQL Prefix</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mysqlPrefix" value="{if isset($vb_prefs.mysqlPrefix)}{text value=$vb_prefs.mysqlPrefix allowEmpty=true}{/if}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="user"}-{lng p="domain"}</label>
			<div class="col-sm-10">
				<select name="userDomain" class="form-select">
					{foreach from=$domains item=domain}
						<option value="{$domain}"{if $vb_prefs.userDomain==$domain} selected="selected"{/if}>{$domain}</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="text-end">
			<input type="submit" class="btn btn-primary" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>