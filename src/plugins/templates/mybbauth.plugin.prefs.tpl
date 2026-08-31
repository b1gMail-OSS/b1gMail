{if $mybbMsg}
<div class="alert alert-{$mybbMsg.type} mb-3" role="alert">{$mybbMsg.text}</div>
{/if}

<fieldset>
	<legend>{lng p="prefs"}</legend>

	<form action="{sessionurl file='plugin.page.php' params="plugin={$mybbPlugin}"}" method="post" onsubmit="spin(this)">
		{csrffield}
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="enable"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="enableAuth"{if $mybb_prefs.enableAuth} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="host"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mysqlHost" value="{if isset($mybb_prefs.mysqlHost)}{text value=$mybb_prefs.mysqlHost}{/if}" placeholder="{lng p="host"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="user"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mysqlUser" value="{if isset($mybb_prefs.mysqlUser)}{text value=$mybb_prefs.mysqlUser}{/if}" placeholder="{lng p="user"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="password"}</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="mysqlPass" value="{if isset($mybb_prefs.mysqlPass)}{text value=$mybb_prefs.mysqlPass}{/if}" placeholder="{lng p="password"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="db"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mysqlDB" value="{if isset($mybb_prefs.mysqlDB)}{text value=$mybb_prefs.mysqlDB}{/if}" placeholder="{lng p="db"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">MySQL Prefix</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="mysqlPrefix" value="{if isset($mybb_prefs.mysqlPrefix)}{text value=$mybb_prefs.mysqlPrefix allowEmpty=true}{/if}" placeholder="MySQL Prefix">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="user"}-{lng p="domain"}</label>
			<div class="col-sm-10">
				<select name="userDomain" class="form-select">
					{foreach from=$domains item=domain}
						<option value="{$domain}"{if $mybb_prefs.userDomain==$domain} selected="selected"{/if}>{$domain}</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="text-end">
			<button type="submit" name="save" value="1" class="btn btn-primary">{lng p="save"}</button>
		</div>
	</form>
</fieldset>
