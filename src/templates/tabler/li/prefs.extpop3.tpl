<div class="bm-prefs-page bm-prefs-page-extpop3">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-mail-down icon icon-sm" aria-hidden="true"></i>
		{lng p="extpop3"}
	</div>
	<div class="right">
		{$accountUsage}
	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=extpop3&do=action&sid={$sid}">

<div class="scrollContainer withBottomBar bm-prefs-body bm-prefs-list-body">
<div class="card bm-prefs-table-card">
<div class="table-responsive bm-prefs-table-wrap">
<table class="bigTable table table-vcenter table-hover bm-prefs-table">
	<tr>
		<th class="bm-prefs-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'pop3');" aria-label="{lng p="selaction"}" /></label></th>
		<th>
			<a href="prefs.php?sid={$sid}&action=extpop3&sort=p_user&order={$sortOrderInv}">{lng p="username"}</a>
			{if $sortColumn=='p_user'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="bm-prefs-col-host">
			<a href="prefs.php?sid={$sid}&action=extpop3&sort=p_host&order={$sortOrderInv}">{lng p="host"}</a>
			{if $sortColumn=='p_host'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="bm-prefs-col-paused">
			{lng p="paused"}?
		</th>
		<th class="bm-prefs-col-lastfetch">
			<a href="prefs.php?sid={$sid}&action=extpop3&sort=last_fetch&order={$sortOrderInv}">{lng p="lastfetch"}</a>
			{if $sortColumn=='last_fetch'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="bm-prefs-col-actions">&nbsp;</th>
	</tr>
	
	{if $accountList}
	<tbody class="listTBody">
	{foreach from=$accountList key=accountID item=account}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class} bm-prefs-col-check" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="pop3_{$accountID}" name="pop3_{$accountID}" aria-label="{text value=$account.p_user}" /></label></td>
		<td nowrap="nowrap" class="{if $sortColumn=='p_user'}listTableTDActive{else}{$class}{/if}"><i class="ti ti-external-link icon icon-sm text-secondary me-1" aria-hidden="true"></i>{text value=$account.p_user}</td>
		<td nowrap="nowrap" class="{if $sortColumn=='p_host'}listTableTDActive{else}{$class}{/if} bm-prefs-col-host">{text value=$account.p_host}:{$account.p_port}</td>
		<td nowrap="nowrap" class="{$class} bm-prefs-col-paused">{if $account.paused}{lng p="yes"}{else}{lng p="no"}{/if}</td>
		<td nowrap="nowrap" class="{if $sortColumn=='last_fetch'}listTableTDActive{else}{$class}{/if} bm-prefs-col-lastfetch">{if $account.last_fetch<=0}({lng p="never"}){else}{date timestamp=$account.last_fetch nice=true} ({if $account.last_success==0}{lng p="error"}{elseif $account.last_success==1}{lng p="success"}{else}{lng p="fetching"}{/if}){/if}</td>
		<td class="{$class} bm-prefs-col-actions text-end" nowrap="nowrap">
			<div class="btn-group btn-group-sm bm-prefs-row-actions" role="group" aria-label="{lng p="actions"}">
				<a href="prefs.php?action=extpop3&do=edit&id={$accountID}&sid={$sid}" class="btn btn-outline-secondary btn-icon" title="{lng p="edit"}" aria-label="{lng p="edit"}"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
				<a onclick="return confirm('{lng p="realdel"}');" href="prefs.php?action=extpop3&do=delete&id={$accountID}&sid={$sid}" class="btn btn-outline-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
			</div>
		</td>
	</tr>
	{/foreach}
	</tbody>
	{/if}
</table>
</div>
</div>
</div>

<div id="contentFooter" class="contentFooter bm-organizer-footer bm-prefs-footer">
	<div class="left">
		<div class="input-group input-group-sm bm-prefs-action-group">
			<select class="form-select bm-prefs-action-select" name="do2">
				<option value="-">------ {lng p="selaction"} ------</option>
				<option value="delete">{lng p="delete"}</option>
			</select>
			<input class="btn btn-primary" type="submit" value="{lng p="ok"}" />
		</div>
	</div>
	<div class="right">
		{if $allowAdd}<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='prefs.php?action=extpop3&do=add&sid={$sid}';">
			<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
			{lng p="addpop3"}
		</button>{/if}
	</div>
</div>

</form>
</div>
