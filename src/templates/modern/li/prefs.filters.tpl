<div id="contentHeader">
	<div class="left">
		<i class="fa fa-filter" aria-hidden="true"></i>
		{lng p="filters"}
	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=filters&do=action&sid={$sid}">

<div class="scrollContainer withBottomBar">
<table class="bigTable">
	<tr>
		<th width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'filter');" /></th>
		<th>
			<a href="prefs.php?sid={$sid}&action=filters&sort=title&order={$sortOrderInv}">{lng p="title"}</a>
			{if $sortColumn=='title'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="150">
			<a href="prefs.php?sid={$sid}&action=filters&sort=applied&order={$sortOrderInv}">{lng p="applied"}</a>
			{if $sortColumn=='applied'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="65">
			<a href="prefs.php?sid={$sid}&action=filters&sort=orderpos&order={$sortOrderInv}">{lng p="orderpos"}</a>
			{if $sortColumn=='orderpos'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="50">
			<a href="prefs.php?sid={$sid}&action=filters&sort=active&order={$sortOrderInv}">{lng p="active"}?</a>
			{if $sortColumn=='active'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="55">&nbsp;</th>
	</tr>
	
	{if $filterList}
	<tbody class="listTBody">
	{foreach from=$filterList key=filterID item=filter}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}" nowrap="nowrap"><input type="checkbox" id="filter_{$filterID}" name="filter_{$filterID}" /></td>
		<td class="{if $sortColumn=='title'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;<a href="prefs.php?sid={$sid}&action=filters&do=edit&id={$filterID}"><i class="fa fa-filter" aria-hidden="true"></i> {text value=$filter.title}</a></td>
		<td class="{if $sortColumn=='applied'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;{$filter.applied}</td>
		<td class="{if $sortColumn=='orderpos'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;{$filter.orderpos}
			<a href="prefs.php?action=filters&down={$filterID}&sid={$sid}"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>
			<a href="prefs.php?action=filters&up={$filterID}&sid={$sid}"><i class="fa fa-arrow-up" aria-hidden="true"></i></td>
		<td class="{if $sortColumn=='active'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;<input type="checkbox" disabled="disabled"{if $filter.active} checked="checked"{/if} /></td>
		<td class="{$class}" nowrap="nowrap">
			<a href="prefs.php?action=filters&do=edit&id={$filterID}&sid={$sid}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			<a onclick="return confirm('{lng p="realdel"}');" href="prefs.php?action=filters&do=delete&id={$filterID}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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
		<button type="button" class="primary" onclick="document.location.href='prefs.php?action=filters&do=add&sid={$sid}';">
			<i class="fa fa-plus-circle"></i>
			{lng p="addfilter"}
		</button>
	</div>
</div>

</form>
