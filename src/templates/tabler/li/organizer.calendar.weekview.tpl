<div class="bm-organizer-page bm-organizer-calendar">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-calendar icon icon-sm" aria-hidden="true"></i>
			{lng p="calendar"}: {lng p="cw"} {$calWeek},
			{date timestamp=$weekStartDate dayonly=true}
			{lng p="dateto"}
			{date timestamp=$weekEndDate dayonly=true}
		</div>
		<div class="right">
			<button type="button" class="btn btn-sm btn-outline-primary" onclick="document.location.href='{sessionurl file='organizer.calendar.php' params='action=calendars'}';">
				<i class="ti ti-calendar icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="calendars"}
			</button>
			<button type="button" class="btn btn-sm btn-outline-primary" onclick="document.location.href='{sessionurl file='organizer.calendar.php' params='action=groups'}';">
				<i class="ti ti-users-group icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="editgroups"}
			</button>
		</div>
	</div>

	<div class="scrollContainer withBottomBar bm-organizer-calendar-body bm-organizer-calendar-week" id="calendarContainer">
		<div class="bm-organizer-week-allday-wrap" id="calendarWholeDayBody">
			<table class="calendarWholeDayBody bm-organizer-calendar-allday" id="weekWholeDayTable">
			<tr>
				<td class="calendarDayTimeCell"></td>
				<td class="calendarDaySepCell"></td>
				<td class="calendarDaySepCell2"></td>
				{foreach from=$weekCaptions item=cap}
				<td class="calendarWeekDayCaption bm-organizer-week-caption{if $cap.today} is-today{/if}">
					<span class="bm-organizer-week-dow">{$cap.dow}</span>
					<span class="bm-organizer-week-dom">{$cap.dom}</span>
				</td>
				{/foreach}
			</tr>
			<tr>
				<td class="calendarDayTimeCell">&nbsp;</td>
				<td class="calendarDaySepCell"></td>
				<td class="calendarDaySepCell2"></td>
				{foreach from=$dates key=dayIndex item=dayDates}
				<td class="calendarWholeDayCell bm-organizer-week-allday{if $weekCaptions[$dayIndex].today} is-today{/if}">
					{foreach from=$dayDates item=date}
					{if $date.flags&1}
						<div style="overflow:hidden;text-overflow:ellipsis;" class="calendarDate_{$date.displayColor} bm-organizer-calendar-event" onclick="showCalendarDate({$date.id}, {$date.startdate}, {$date.enddate})">
							{text value=$date.title}
						</div>
					{/if}
					{/foreach}
				</td>
				{/foreach}
			</tr>
			</table>
		</div>

		<div id="calendarDayBody" class="calendarWeekBody bm-organizer-week-grid">
		<table class="calendarDayBody">
		{section name=halfHours start=0 loop=48}
		<tr>
		{if $smarty.section.halfHours.index%2==0}
			<td class="calendarDayTimeCell" rowspan="2">
				<div class="calendarDayTimeCellText">{halfHourToTime value=$smarty.section.halfHours.index}</div>
			</td>
		{/if}
		{if $smarty.section.halfHours.index==0}
			<td class="calendarDaySepCell" rowspan="48"></td>
			<td class="calendarDaySepCell2" rowspan="48"></td>
		{/if}
		{assign var=d value=0}
		{foreach from=$dates key=dayIndex item=dontCare}
			<td class="calendarDayCell{if $smarty.section.halfHours.index%2}2{/if}{if $smarty.section.halfHours.index>=$dayStart && $smarty.section.halfHours.index<$dayEnd}_day{/if} calendarWeekCell bm-organizer-week-cell{if $weekCaptions[$dayIndex].today} is-today{/if}" id="timeRow_{$d}_{$smarty.section.halfHours.index}">
				&nbsp;
			</td>
		{assign var=d value=$d+1}
		{/foreach}
		</tr>
		{/section}
		</table>
		</div>

		<script>
		<!--
			var calendarDayStart = {$dayStart},
				calendarDayEnd = {$dayEnd},
				calendarDates = [],
				calendarWeekDays = [{foreach from=$weekDayTimestamps item=ts name=wdt}{$ts}{if !$smarty.foreach.wdt.last},{/if}{/foreach}];

			{assign var=d value=0}
			{foreach from=$dates item=dayDates}
			{foreach from=$dayDates item=date}
			{if ($date.flags&1)==0}
			calendarDates.push([
				{$date.id},
				{$date.startdate},
				{$date.enddate},
				"{text escape=true noentities=true value=$date.title}",
				{$date.displayColor},
				{$d}
			]);
			{/if}
			{/foreach}
			{assign var=d value=$d+1}
			{/foreach}

			registerLoadAction('calendarDaySizer()');
			registerLoadAction('initCalendar()');
			registerLoadAction('initCalendarRangeSelection()');
		//-->
		</script>
	</div>

	<div id="contentFooter" class="contentFooter bm-organizer-footer">
		<div class="right">
			<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='{sessionurl file='organizer.calendar.php' params="action=addDate&date={$theDate}"|escape:'javascript'}';">
				<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="adddate"}
			</button>
		</div>
	</div>
</div>
