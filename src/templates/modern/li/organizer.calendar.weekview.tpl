<div id="contentHeader">
	<div class="left">
		<i class="fa fa-calendar" aria-hidden="true"></i>
		{lng p="calendar"}: {lng p="cw"} {$calWeek},
		{date timestamp=$weekStartDate dayonly=true}
		{lng p="dateto"}
		{date timestamp=$weekEndDate dayonly=true}
	</div>
	<div class="right">
		<button type="button" onclick="document.location.href='organizer.calendar.php?action=groups&sid={$sid}';">
			<i class="fa fa-calendar-o" aria-hidden="true"></i>
			{lng p="editgroups"}
		</button>
	</div>
</div>

<div class="scrollContainer withBottomBar" style="overflow:hidden;" id="calendarContainer">
	<div style="overflow-y:scroll;" id="calendarWholeDayBody">
		<table class="calendarWholeDayBody" id="weekWholeDayTable" style="border-bottom:3px double #B3B8BD;">
		<tr style="border-bottom:1px solid #B3B8BD;">
			<td class="calendarDayTimeCell"></td>
			<td class="calendarDaySepCell"></td>
			<td></td>
			{foreach from=$dates key=dayName item=dontCare}
			<td class="calendarWeekDayCaption">{text value=$dayName}</td>
			{/foreach}
		</tr>
		<tr>
			<td class="calendarDayTimeCell">&nbsp;</td>
			<td class="calendarDaySepCell"></td>
			<td class="calendarDaySepCell2"></td>
			{foreach from=$dates key=dayName item=dayDates}
			<td class="calendarWholeDayCell" style="border-right:1px solid #B3B8BD;">
				{foreach from=$dayDates item=date}
				{if $date.flags&1}
					<div style="overflow:hidden;text-overflow:ellipsis;" class="calendarDate_{$groups[$date.group].color}" onclick="showCalendarDate({$date.id}, {$date.startdate}, {$date.enddate})">
						{text value=$date.title}
					</div>
				{/if}
				{/foreach}
			</td>
			{/foreach}
		</tr>
		</table>
	</div>
	
	<div id="calendarDayBody" class="calendarWeekBody">
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
		<td class="calendarDayCell{if $smarty.section.halfHours.index%2}2{/if}{if $smarty.section.halfHours.index>=$dayStart && $smarty.section.halfHours.index<$dayEnd}_day{/if} calendarWeekCell" id="timeRow_{$d}_{$smarty.section.halfHours.index}" style="{if $smarty.section.halfHours.index==0}border-top:0;{/if}">
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

<div id="contentFooter">
	<div class="right">
		<button type="button" class="primary" onclick="document.location.href='organizer.calendar.php?action=addDate&date={$theDate}&sid={$sid}';">
			<i class="fa fa-plus-circle"></i>
			{lng p="adddate"}
		</button>
	</div>
</div>
