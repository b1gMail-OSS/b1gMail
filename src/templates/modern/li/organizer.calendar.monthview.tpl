<div id="contentHeader">
	<div class="left">
		<i class="fa fa-calendar" aria-hidden="true"></i>
		{lng p="calendar"}: {$thisMonthText}
	</div>
	<div class="right">
		<button type="button" onclick="document.location.href='organizer.calendar.php?action=groups&sid={$sid}';">
			<i class="fa fa-calendar-o" aria-hidden="true"></i>
			{lng p="editgroups"}
		</button>
	</div>
</div>

<div class="scrollContainer withBottomBar" id="calendarContainer">
<table class="bigTable">
	<tr>
		{foreach from=$columns item=column}
		<th width="14%">{$wdays[$column]}</th>
		{/foreach}
	</tr>
	
	<tr>
		{assign var=i value=0}{foreach from=$days item=day key=dayKey}{if !$day}<td></td>{else}
		<td valign="top" class="monthCell{if $day.today}Today{/if}">
			<div class="monthCellDay" style="{if $day.today}font-weight:bold;{/if}" onclick="document.location.href='organizer.calendar.php?view=day&date={$day.dayStart}&sid={$sid}';">{$day.day}</div>
			{foreach from=$day.dates item=date}
			<div class="monthDate_{$groups[$date.group].color}" onclick="showCalendarDate({$date.id}, {$date.startdate}, {$date.enddate}, false)">
				&nbsp;
				{if $date.startdate<$day.dayStart}&lt;{/if}
				{text value=$date.title cut=18}
				{if $date.enddate>$day.dayEnd}&gt;{/if}
			</div>
			{/foreach}
		</td>
		{/if}{assign var=i value=$i+1}{if $i>6&&$lastDayKey!=$dayKey}
	</tr>
	
	<tr>
		{assign var=i value=0}{/if}{/foreach}{if $i<7}{math equation="7 - i" i=$i assign=left}{section name=remainingCells loop=$left}<td></td>{/section}{/if}
	</tr>
</table>
</div>

<div id="contentFooter">
	<div class="right">
		<button type="button" class="primary" onclick="document.location.href='organizer.calendar.php?action=addDate&date={$theDate}&sid={$sid}';">
			<i class="fa fa-plus-circle"></i>
			{lng p="adddate"}
		</button>
	</div>
</div>
