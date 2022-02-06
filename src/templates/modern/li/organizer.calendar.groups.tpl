<div id="contentHeader">
	<div class="left">
		<i class="fa fa-calendar-o" aria-hidden="true"></i>
		{lng p="groups"}
	</div>
</div>

<form name="f1" method="post" action="organizer.calendar.php?action=groups&do=action&sid={$sid}">

<div class="scrollContainer withBottomBar">
<table class="bigTable">
	<tr>
		<th class="listTableHead" width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'group');" /></th>
		<th class="listTableHead">
			<a href="organizer.calendar.php?action=groups&sid={$sid}&sort=title&order={$sortOrderInv}">{lng p="title"}</a>
			{if $sortColumn=='title'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
		</th>
		<th class="listTableHead" width="120">
			<a href="organizer.calendar.php?action=groups&sid={$sid}&sort=color&order={$sortOrderInv}">{lng p="color"}</a>
			{if $sortColumn=='color'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
		</th>
		<th class="listTableHead" width="55">&nbsp;</th>
	</tr>
	
	{if $haveGroups}
	<tbody class="listTBody">
	{foreach from=$groups key=groupID item=group}
	{if $groupID!=-1}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}" nowrap="nowrap"><input type="checkbox" id="group_{$groupID}" name="group_{$groupID}" /></td>
		<td nowrap="nowrap" class="{if $sortColumn=='title'}listTableTDActive{else}{$class}{/if}">&nbsp;<a href="organizer.calendar.php?switchGroup={$groupID}&sid={$sid}"><i class="fa fa-calendar-o" aria-hidden="true"></i> {text value=$group.title}</a></td>
		<td class="{if $sortColumn=='color'}listTableTDActive{else}{$class}{/if}"><div class="calendarDate_{$group.color}" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
		<td class="{$class}" nowrap="nowrap">
			<a href="organizer.calendar.php?action=groups&do=edit&id={$groupID}&sid={$sid}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			<a onclick="return confirm('{lng p="realdel"}');" href="organizer.calendar.php?action=groups&do=delete&id={$groupID}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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
		<button type="button" class="primary" onclick="document.location.href='organizer.calendar.php?action=groups&do=addForm&sid={$sid}';">
			<i class="fa fa-plus-circle"></i>
			{lng p="add"}
		</button>
	</div>
</div>

</form>
