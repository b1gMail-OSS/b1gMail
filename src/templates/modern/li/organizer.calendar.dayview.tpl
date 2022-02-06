<div id="contentHeader">
	<div class="left">
		<i class="fa fa-calendar" aria-hidden="true"></i>
		{lng p="calendar"}: {$weekDay}, {date timestamp=$date dayonly=true} ({lng p="cw"} {$calWeek})
	</div>
	<div class="right">
		<button type="button" onclick="document.location.href='organizer.calendar.php?action=groups&sid={$sid}';">
			<i class="fa fa-calendar-o" aria-hidden="true"></i>
			{lng p="editgroups"}
		</button>
	</div>
</div>

<div class="scrollContainer withBottomBar" style="overflow-y:hidden;" id="calendarContainer">
	<div style="overflow-y:scroll;" id="calendarWholeDayBody">
		<table class="calendarWholeDayBody" style="border-bottom:3px double #B3B8BD;">
		<tr>
			<td class="calendarDayTimeCell">&nbsp;</td>
			<td class="calendarDaySepCell"></td>
			<td class="calendarDaySepCell2"></td>
			<td class="calendarWholeDayCell">
				{foreach from=$dates item=date}
				{if $date.flags&1}
					<div class="calendarDate_{$groups[$date.group].color}" onclick="showCalendarDate({$date.id}, {$date.startdate}, {$date.enddate})">
						{text value=$date.title}
					</div>
				{/if}
				{/foreach}
			</td>
		</tr>
		</table>
	</div>
	<iframe class="calendarDayBody" id="calendarDayBody" src="organizer.calendar.php?action=dayView&date={$theDate}&sid={$sid}" frameborder="0" border="0"></iframe>
</div>

<script>
<!--
	registerLoadAction('calendarDaySizer()');
//-->
</script>

<div id="contentFooter">
	<div class="right">
		<button type="button" class="primary" onclick="document.location.href='organizer.calendar.php?action=addDate&date={$theDate}&sid={$sid}';">
			<i class="fa fa-plus-circle"></i>
			{lng p="adddate"}
		</button>
	</div>
</div>
