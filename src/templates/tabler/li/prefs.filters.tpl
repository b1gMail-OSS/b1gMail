<div class="bm-prefs-page bm-prefs-page-filters">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-filter icon icon-sm" aria-hidden="true"></i>
		{lng p="filters"}
	</div>
</div>

<form name="f1" method="post" action="{sessionurl file='prefs.php' params='action=filters&do=action'}">
	{csrffield}

<div class="scrollContainer withBottomBar bm-prefs-body bm-prefs-list-body">
<div class="card bm-prefs-table-card">
<div class="table-responsive bm-prefs-table-wrap">
<table class="bigTable table table-vcenter table-hover bm-prefs-table">
	<tr>
		<th class="bm-prefs-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'filter');" aria-label="{lng p="selaction"}" /></label></th>
		<th>
			<a href="{sessionurl file='prefs.php' params="action=filters&sort=title&order={$sortOrderInv}"}">{lng p="title"}</a>
			{if $sortColumn=='title'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="bm-prefs-col-applied">
			<a href="{sessionurl file='prefs.php' params="action=filters&sort=applied&order={$sortOrderInv}"}">{lng p="applied"}</a>
			{if $sortColumn=='applied'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="bm-prefs-col-orderpos">
			<a href="{sessionurl file='prefs.php' params="action=filters&sort=orderpos&order={$sortOrderInv}"}">{lng p="orderpos"}</a>
			{if $sortColumn=='orderpos'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="bm-prefs-col-active">
			<a href="{sessionurl file='prefs.php' params="action=filters&sort=active&order={$sortOrderInv}"}">{lng p="active"}?</a>
			{if $sortColumn=='active'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="bm-prefs-col-actions">&nbsp;</th>
	</tr>
	
	{if $filterList}
	<tbody class="listTBody">
	{foreach from=$filterList key=filterID item=filter}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class} bm-prefs-col-check" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="filter_{$filterID}" name="filter_{$filterID}" aria-label="{text value=$filter.title}" /></label></td>
		<td class="{if $sortColumn=='title'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap"><a href="{sessionurl file='prefs.php' params="action=filters&do=edit&id={$filterID}"}"><i class="ti ti-filter icon icon-sm text-secondary me-1" aria-hidden="true"></i>{text value=$filter.title}</a></td>
		<td class="{if $sortColumn=='applied'}listTableTDActive{else}{$class}{/if} bm-prefs-col-applied" nowrap="nowrap">{$filter.applied}</td>
		<td class="{if $sortColumn=='orderpos'}listTableTDActive{else}{$class}{/if} bm-prefs-col-orderpos" nowrap="nowrap">
			<span class="bm-prefs-orderpos-value">{$filter.orderpos}</span>
			<div class="btn-group btn-group-sm bm-prefs-row-actions bm-prefs-orderpos-actions ms-1" role="group" aria-label="{lng p="orderpos"}">
				<a href="{sessionurl file='prefs.php' params="action=filters&down={$filterID}"}" class="btn btn-outline-secondary btn-icon"><i class="ti ti-arrow-down icon" aria-hidden="true"></i></a>
				<a href="{sessionurl file='prefs.php' params="action=filters&up={$filterID}"}" class="btn btn-outline-secondary btn-icon"><i class="ti ti-arrow-up icon" aria-hidden="true"></i></a>
			</div>
		</td>
		<td class="{if $sortColumn=='active'}listTableTDActive{else}{$class}{/if} bm-prefs-col-active" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" disabled="disabled"{if $filter.active} checked="checked"{/if} aria-label="{lng p="active"}" /></label></td>
		<td class="{$class} bm-prefs-col-actions text-end" nowrap="nowrap">
			<div class="btn-group btn-group-sm bm-prefs-row-actions" role="group" aria-label="{lng p="actions"}">
				<a href="{sessionurl file='prefs.php' params="action=filters&do=edit&id={$filterID}"}" class="btn btn-outline-secondary btn-icon" title="{lng p="edit"}" aria-label="{lng p="edit"}"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
				<a onclick="return confirm('{lng p="realdel"}');" href="{sessionurl file='prefs.php' params="action=filters&do=delete&id={$filterID}"}" class="btn btn-outline-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
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
		<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='{sessionurl file='prefs.php' params='action=filters&do=add'}';">
			<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
			{lng p="addfilter"}
		</button>
	</div>
</div>

</form>
</div>
