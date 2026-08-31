<div class="bm-organizer-page bm-organizer-calendar">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-calendar icon icon-sm" aria-hidden="true"></i>
			{lng p="calendar"}: {$weekDay}, {date timestamp=$date dayonly=true} ({lng p="cw"} {$calWeek})
		</div>
		<div class="right">
			<button type="button" class="btn btn-sm btn-outline-primary" onclick="document.location.href='{sessionurl file='organizer.calendar.php' params='action=groups'}';">
				<i class="ti ti-users-group icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="editgroups"}
			</button>
		</div>
	</div>

	<div class="scrollContainer withBottomBar bm-organizer-calendar-body" id="calendarContainer">
		<div style="overflow-y:scroll;" id="calendarWholeDayBody">
			<table class="calendarWholeDayBody bm-organizer-calendar-allday" style="border-bottom:3px double var(--tblr-border-color, #B3B8BD);">
			<tr>
				<td class="calendarDayTimeCell">&nbsp;</td>
				<td class="calendarDaySepCell"></td>
				<td class="calendarDaySepCell2"></td>
				<td class="calendarWholeDayCell">
					{foreach from=$dates item=date}
					{if $date.flags&1}
						<div class="calendarDate_{$groups[$date.group].color} bm-organizer-calendar-event" onclick="showCalendarDate({$date.id}, {$date.startdate}, {$date.enddate})">
							{text value=$date.title}
						</div>
					{/if}
					{/foreach}
				</td>
			</tr>
			</table>
		</div>
		<iframe class="calendarDayBody bm-organizer-calendar-frame" id="calendarDayBody" src="organizer.calendar.php?action=dayView&date={$theDate}{$sessionUrlSuffix}" frameborder="0" border="0"></iframe>
	</div>

	<script>
	<!--
		registerLoadAction('calendarDaySizer()');
	//-->
	</script>

	<div id="contentFooter" class="contentFooter bm-organizer-footer">
		<div class="right">
			<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='{sessionurl file='organizer.calendar.php' params="action=addDate&date={$theDate}"|escape:'javascript'}';">
				<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="adddate"}
			</button>
		</div>
	</div>
</div>
