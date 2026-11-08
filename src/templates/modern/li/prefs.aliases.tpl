<div id="contentHeader">
	<div class="left">
		<i class="fa fa-user" aria-hidden="true"></i>
		{lng p="aliases"}
	</div>
	<div class="right">
		{$aliasUsage}
	</div>
</div>

<form name="f1" method="post" action="{sessionurl file='prefs.php' params='action=aliases&do=action'}" data-alias-login-url="{sessionurl file='prefs.php' params='action=aliases&do=toggleLogin'}">
	{csrffield}

<div class="scrollContainer withBottomBar">
<table class="bigTable">
	<tr>
		<th width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'alias');" /></th>
		<th>
			<a href="{sessionurl file='prefs.php' params="action=aliases&sort=email&order={$sortOrderInv}"}">{lng p="alias"}</a>
			{if $sortColumn=='email'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="220">
			<a href="{sessionurl file='prefs.php' params="action=aliases&sort=type&order={$sortOrderInv}"}">{lng p="type"}</a>
			{if $sortColumn=='type'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="90" title="{lng p="login_with_alias"}">
			<a href="{sessionurl file='prefs.php' params="action=aliases&sort=login&order={$sortOrderInv}"}" title="{lng p="login_with_alias"}">{lng p="login"}</a>
			{if $sortColumn=='login'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="55">&nbsp;</th>
	</tr>
	
	{if $aliasList}
	<tbody class="listTBody">
	{foreach from=$aliasList key=aliasID item=alias}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}" nowrap="nowrap"><input type="checkbox" id="alias_{$aliasID}" name="alias_{$aliasID}" /></td>
		<td class="{if $sortColumn=='email'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;<i class="fa fa-user-o" aria-hidden="true"></i> {email value=$alias.email}</td>
		<td class="{if $sortColumn=='type'}listTableTDActive{else}{$class}{/if}">&nbsp;{$alias.typeText}</td>
		<td class="{if $sortColumn=='login'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">
			{if $alias.canLogin}
			<input type="checkbox"{if $alias.login=='yes'} checked="checked"{/if} title="{lng p="login_with_alias"}" aria-label="{lng p="login_with_alias"}" onchange="bmToggleAliasLogin({$aliasID}, this.checked)" />
			{else}
			—
			{/if}
		</td>
		<td class="{$class}" nowrap="nowrap">
			<a href="{sessionurl file='prefs.php' params="action=aliases&do=edit&id={$aliasID}"}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			<a onclick="return confirm('{lng p="realdel"}');" href="{sessionurl file='prefs.php' params="action=aliases&do=delete&id={$aliasID}&csrf_token={$csrfToken}"}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		</td>
	</tr>
	{/foreach}
	</tbody>
	{/if}
</table>
</div>

<div id="contentFooter">
	<div class="left">
		<select class="smallInput" name="do2">
			<option value="-">------ {lng p="selaction"} ------</option>
			<option value="delete">{lng p="delete"}</option>
		</select>
		<input class="smallInput" type="submit" value="{lng p="ok"}" />
	</div>
	<div class="right">
		{if $allowAdd}<button class="primary" type="button" onclick="document.location.href='{sessionurl file='prefs.php' params='action=aliases&do=add'}';">
			<i class="fa fa-plus-circle"></i>
			{lng p="addalias"}
		</button>{/if}
	</div>
</div>

</form>
<script>
function bmToggleAliasLogin(id, enabled) {
	var source = document.forms.f1;
	var form = document.createElement('form');
	form.method = 'post';
	form.action = source.getAttribute('data-alias-login-url') || source.action;
	function field(name, value) {
		var input = document.createElement('input');
		input.type = 'hidden';
		input.name = name;
		input.value = value;
		form.appendChild(input);
	}
	if(source.csrf_token)
		field('csrf_token', source.csrf_token.value);
	field('id', String(id));
	field('login', enabled ? 'yes' : 'no');
	document.body.appendChild(form);
	form.submit();
}
</script>
