<div id="contentHeader">
	<div class="left">
		<i class="fa fa-calendar-o" aria-hidden="true"></i>
		{lng p="groups"}
	</div>
</div>

<form name="f1" method="post" action="{sessionurl file='organizer.calendar.php' params='action=groups&do=action'}">
	{csrffield}

<div class="scrollContainer withBottomBar">
<table class="bigTable">
	<tr>
		<th class="listTableHead" width="20"><label class="form-check mb-0"><input type="checkbox" class="form-check-input" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'group');" /></label></th>
		<th class="listTableHead">
			<a href="organizer.calendar.php?action=groups&sort=title&order={$sortOrderInv}{$sessionUrlSuffix}">{lng p="title"}</a>
			{if $sortColumn=='title'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="listTableHead" width="120">
			<a href="organizer.calendar.php?action=groups&sort=color&order={$sortOrderInv}{$sessionUrlSuffix}">{lng p="color"}</a>
			{if $sortColumn=='color'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="listTableHead" width="55">&nbsp;</th>
	</tr>
	
	{if $haveGroups}
	<tbody class="listTBody">
	{foreach from=$groups key=groupID item=group}
	{if $groupID!=-1}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input" id="group_{$groupID}" name="group_{$groupID}" /></label></td>
		<td nowrap="nowrap" class="{if $sortColumn=='title'}listTableTDActive{else}{$class}{/if}">&nbsp;<a href="organizer.calendar.php?switchGroup={$groupID}{$sessionUrlSuffix}"><i class="fa fa-calendar-o" aria-hidden="true"></i> {text value=$group.title}</a></td>
		<td class="{if $sortColumn=='color'}listTableTDActive{else}{$class}{/if}"><div class="calendarDate_{$group.color}" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
		<td class="{$class}" nowrap="nowrap">
			<a href="organizer.calendar.php?action=groups&do=edit&id={$groupID}{$sessionUrlSuffix}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			<a onclick="return confirm('{lng p="realdel"}');" href="organizer.calendar.php?action=groups&do=delete&id={$groupID}{$sessionUrlSuffix}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		</td>
	</tr>
	{/if}
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
		<button type="button" class="primary" onclick="document.location.href='{sessionurl file='organizer.calendar.php' params='action=groups&do=addForm'}';">
			<i class="fa fa-plus-circle"></i>
			{lng p="add"}
		</button>
	</div>
</div>

</form>
