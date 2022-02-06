<div id="contentHeader">
	<div class="left">
		<i class="fa fa-external-link" aria-hidden="true"></i>
		{lng p="extpop3"}
	</div>
	<div class="right">
		{$accountUsage}
	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=extpop3&do=action&sid={$sid}">

<div class="scrollContainer withBottomBar">
<table class="bigTable">
	<tr>
		<th width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'pop3');" /></th>
		<th>
			<a href="prefs.php?sid={$sid}&action=extpop3&sort=p_user&order={$sortOrderInv}">{lng p="username"}</a>
			{if $sortColumn=='p_user'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
		</th>
		<th>
			<a href="prefs.php?sid={$sid}&action=extpop3&sort=p_host&order={$sortOrderInv}">{lng p="host"}</a>
			{if $sortColumn=='p_host'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
		</th>
		<th width="60">
			{lng p="paused"}?
		</th>
		<th width="220">
			<a href="prefs.php?sid={$sid}&action=extpop3&sort=last_fetch&order={$sortOrderInv}">{lng p="lastfetch"}</a>
			{if $sortColumn=='last_fetch'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
		</th>
		<th width="55">&nbsp;</th>
	</tr>
	
	{if $accountList}
	<tbody class="listTBody">
	{foreach from=$accountList key=accountID item=account}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}" nowrap="nowrap"><input type="checkbox" id="pop3_{$accountID}" name="pop3_{$accountID}" /></td>
		<td nowrap="nowrap" class="{if $sortColumn=='p_user'}listTableTDActive{else}{$class}{/if}">&nbsp;<i class="fa fa-external-link" aria-hidden="true"></i> {text value=$account.p_user}</td>
		<td nowrap="nowrap" class="{if $sortColumn=='p_host'}listTableTDActive{else}{$class}{/if}">&nbsp;{text value=$account.p_host}:{$account.p_port}</td>
		<td nowrap="nowrap" class="{$class}">&nbsp;{if $account.paused}{lng p="yes"}{else}{lng p="no"}{/if}</td>
		<td nowrap="nowrap" class="{if $sortColumn=='last_fetch'}listTableTDActive{else}{$class}{/if}" style="text-align:center;">{if $account.last_fetch<=0}({lng p="never"}){else}{date timestamp=$account.last_fetch nice=true}
			({if $account.last_success==0}{lng p="error"}{elseif $account.last_success==1}{lng p="success"}{else}{lng p="fetching"}{/if}){/if}</td>
		<td class="{$class}" nowrap="nowrap">
			<a href="prefs.php?action=extpop3&do=edit&id={$accountID}&sid={$sid}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			<a onclick="return confirm('{lng p="realdel"}');" href="prefs.php?action=extpop3&do=delete&id={$accountID}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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
		{if $allowAdd}<button type="button" class="primary" onclick="document.location.href='prefs.php?action=extpop3&do=add&sid={$sid}';">
			<i class="fa fa-plus-circle"></i>
			{lng p="addpop3"}
		</button>{/if}
	</div>
</div>

</form>
