<div id="contentHeader">
	<div class="left">
		<i class="fa fa-calendar" aria-hidden="true"></i>
		{lng p="calendars"}
	</div>
</div>

<div class="scrollContainer withBottomBar">
<table class="bigTable">
	<tr>
		<th class="listTableHead">{lng p="title"}</th>
		<th class="listTableHead" width="120">{lng p="color"}</th>
		<th class="listTableHead" width="80">{lng p="default"}</th>
		<th class="listTableHead" width="55">&nbsp;</th>
	</tr>
	<tbody class="listTBody">
	{foreach from=$calendars key=calID item=cal}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}">&nbsp;<i class="fa fa-calendar" aria-hidden="true"></i> {text value=$cal.title}</td>
		<td class="{$class}"><div class="calendarDate_{$cal.color}" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
		<td class="{$class}">{if $cal.is_default}{lng p="yes"}{/if}</td>
		<td class="{$class}" nowrap="nowrap">
			<a href="{sessionurl file='organizer.calendar.php' params="action=calendars&do=edit&id={$calID}"}" onclick="return organizerOpenOverlay(this.href, '{lng p="editcalendar"|escape:'javascript'}', 440, 260);"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			{if $cal.is_default}
			<span class="bm-organizer-action-disabled" title="{lng p="nodeletdefault"}"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
			{else}
			<a href="{sessionurl file='organizer.calendar.php' params="action=calendars&do=deleteForm&id={$calID}"}" onclick="return organizerOpenOverlay(this.href, '{lng p="delete"|escape:'javascript'}', 400, 150);"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
			{/if}
		</td>
	</tr>
	{/foreach}
	</tbody>
</table>
</div>

<div id="contentFooter">
	<div class="right">
		<button type="button" class="primary" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params='action=calendars&do=addForm'}', '{lng p="addcalendar"|escape:'javascript'}', 440, 260);">
			<i class="fa fa-plus-circle"></i>
			{lng p="add"}
		</button>
	</div>
</div>
