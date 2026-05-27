<div class="bm-prefs-page bm-prefs-page-aliases">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-at icon icon-sm" aria-hidden="true"></i>
		{lng p="aliases"}
	</div>
	<div class="right">
		{$aliasUsage}
	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=aliases&do=action&sid={$sid}">

<div class="scrollContainer withBottomBar bm-prefs-body bm-prefs-list-body">
<div class="card bm-prefs-table-card">
<div class="table-responsive bm-prefs-table-wrap">
<table class="bigTable table table-vcenter table-hover bm-prefs-table">
	<tr>
		<th class="bm-prefs-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'alias');" aria-label="{lng p="selaction"}" /></label></th>
		<th>
			<a href="prefs.php?sid={$sid}&action=aliases&sort=email&order={$sortOrderInv}">{lng p="alias"}</a>
			{if $sortColumn=='email'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="220">
			<a href="prefs.php?sid={$sid}&action=aliases&sort=type&order={$sortOrderInv}">{lng p="type"}</a>
			{if $sortColumn=='type'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="bm-prefs-col-actions">&nbsp;</th>
	</tr>
	
	{if $aliasList}
	<tbody class="listTBody">
	{foreach from=$aliasList key=aliasID item=alias}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class} bm-prefs-col-check" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="alias_{$aliasID}" name="alias_{$aliasID}" aria-label="{email value=$alias.email}" /></label></td>
		<td class="{if $sortColumn=='email'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap"><i class="ti ti-user icon icon-sm text-secondary me-1" aria-hidden="true"></i>{email value=$alias.email}</td>
		<td class="{if $sortColumn=='type'}listTableTDActive{else}{$class}{/if}">{$alias.typeText}</td>
		<td class="{$class} bm-prefs-col-actions text-end" nowrap="nowrap">
			<div class="btn-group btn-group-sm bm-prefs-row-actions" role="group" aria-label="{lng p="actions"}">
				<a href="prefs.php?action=aliases&do=edit&id={$aliasID}&sid={$sid}" class="btn btn-outline-secondary btn-icon" title="{lng p="edit"}" aria-label="{lng p="edit"}"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
				<a onclick="return confirm('{lng p="realdel"}');" href="prefs.php?action=aliases&do=delete&id={$aliasID}&sid={$sid}" class="btn btn-outline-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
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
		{if $allowAdd}<button class="btn btn-sm btn-primary" type="button" onclick="document.location.href='prefs.php?action=aliases&do=add&sid={$sid}';">
			<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
			{lng p="addalias"}
		</button>{/if}
	</div>
</div>

</form>
</div>
