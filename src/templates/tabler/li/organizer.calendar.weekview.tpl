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
			<button type="button" class="btn btn-sm btn-outline-primary" onclick="document.location.href='{sessionurl file='organizer.calendar.php' params='action=groups'}';">
				<i class="ti ti-users-group icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="editgroups"}
			</button>
		</div>
	</div>

	<div class="scrollContainer withBottomBar bm-organizer-calendar-body bm-organizer-calendar-week" id="calendarContainer">
		<div class="bm-organizer-week-allday-wrap" id="calendarWholeDayBody">
			<table class="calendarWholeDayBody bm-organizer-calendar-allday" id="weekWholeDayTable" style="border-bottom:3px double var(--tblr-border-color, #B3B8BD);">
			<tr style="border-bottom:1px solid var(--tblr-border-color, #B3B8BD);">
				<td class="calendarDayTimeCell"></td>
				<td class="calendarDaySepCell"></td>
				<td></td>
				{foreach from=$dates key=dayName item=dontCare}
				<td class="calendarWeekDayCaption bm-organizer-week-caption">{text value=$dayName}</td>
				{/foreach}
			</tr>
			<tr>
				<td class="calendarDayTimeCell">&nbsp;</td>
				<td class="calendarDaySepCell"></td>
				<td class="calendarDaySepCell2"></td>
				{foreach from=$dates key=dayName item=dayDates}
				<td class="calendarWholeDayCell bm-organizer-week-allday" style="border-right:1px solid var(--tblr-border-color, #B3B8BD);">
					{foreach from=$dayDates item=date}
					{if $date.flags&1}
						<div style="overflow:hidden;text-overflow:ellipsis;" class="calendarDate_{$groups[$date.group].color} bm-organizer-calendar-event" onclick="showCalendarDate({$date.id}, {$date.startdate}, {$date.enddate})">
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
		{foreach from=$dates key=dayName item=dontCare}
			<td class="calendarDayCell{if $smarty.section.halfHours.index%2}2{/if}{if $smarty.section.halfHours.index>=$dayStart && $smarty.section.halfHours.index<$dayEnd}_day{/if} calendarWeekCell bm-organizer-week-cell" id="timeRow_{$d}_{$smarty.section.halfHours.index}" style="{if $smarty.section.halfHours.index==0}border-top:0;{/if}">
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
				calendarDates = [];

			{assign var=d value=0}
			{foreach from=$dates item=dayDates}
			{foreach from=$dayDates item=date}
			{if ($date.flags&1)==0}
			calendarDates.push([
				{$date.id},
				{$date.startdate},
				{$date.enddate},
				"{text escape=true noentities=true value=$date.title}",
				{$groups[$date.group].color},
				{$d}
			]);
			{/if}
			{/foreach}
			{assign var=d value=$d+1}
			{/foreach}

			registerLoadAction('calendarDaySizer()');
			registerLoadAction('initCalendar()');
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
