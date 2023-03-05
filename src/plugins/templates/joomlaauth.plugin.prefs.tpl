<fieldset>
	<legend>{lng p="prefs"}</legend>
	
	<form action="{$pageURL}&sid={$sid}&do=save" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="enable"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="enableAuth"{if $joomla_prefs.enableAuth} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="host"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mysqlHost" value="{if isset($joomla_prefs.mysqlHost)}{text value=$joomla_prefs.mysqlHost}{/if}" placeholder="{lng p="host"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="user"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mysqlUser" value="{if isset($joomla_prefs.mysqlUser)}{text value=$joomla_prefs.mysqlUser}{/if}" placeholder="{lng p="user"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="password"}</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="mysqlPass" value="{if isset($joomla_prefs.mysqlPass)}{text value=$joomla_prefs.mysqlPass}{/if}" placeholder="{lng p="password"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="db"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mysqlDB" value="{if isset($joomla_prefs.mysqlDB)}{text value=$joomla_prefs.mysqlDB}{/if}" placeholder="{lng p="db"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">MySQL Prefix</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mysqlPrefix" value="{if isset($joomla_prefs.mysqlPrefix)}{text value=$joomla_prefs.mysqlPrefix allowEmpty=true}{/if}" placeholder="MySQL Prefix">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="user"}-{lng p="domain"}</label>
			<div class="col-sm-10">
				<select name="userDomain" class="form-select">
					{foreach from=$domains item=domain}
						<option value="{$domain}"{if $joomla_prefs.userDomain==$domain} selected="selected"{/if}>{$domain}</option>
					{/foreach}
				</select>
			</div>
		</div>
		
		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>