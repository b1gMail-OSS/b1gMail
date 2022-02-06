<div id="contentHeader">
	<div class="left">
		<i class="fa fa-user" aria-hidden="true"></i>
		{lng p="aliases"}
	</div>
	<div class="right">
		{$aliasUsage}
	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=aliases&do=action&sid={$sid}">

<div class="scrollContainer withBottomBar">
<table class="bigTable">
	<tr>
		<th width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'alias');" /></th>
		<th>
			<a href="prefs.php?sid={$sid}&action=aliases&sort=email&order={$sortOrderInv}">{lng p="alias"}</a>
			{if $sortColumn=='email'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
		</th>
		<th width="220">
			<a href="prefs.php?sid={$sid}&action=aliases&sort=type&order={$sortOrderInv}">{lng p="type"}</a>
			{if $sortColumn=='type'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
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
		<td class="{$class}" nowrap="nowrap">
			<a onclick="return confirm('{lng p="realdel"}');" href="prefs.php?action=aliases&do=delete&id={$aliasID}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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
		{if $allowAdd}<button class="primary" type="button" onclick="document.location.href='prefs.php?action=aliases&do=add&sid={$sid}';">
			<i class="fa fa-plus-circle"></i>
			{lng p="addalias"}
		</button>{/if}
	</div>
</div>

</form>
