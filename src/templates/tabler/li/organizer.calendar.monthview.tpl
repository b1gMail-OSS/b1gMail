<div class="bm-organizer-page bm-organizer-calendar">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-calendar icon icon-sm" aria-hidden="true"></i>
			{lng p="calendar"}: {$thisMonthText}
		</div>
		<div class="right">
			<button type="button" class="btn btn-sm btn-outline-primary" onclick="document.location.href='organizer.calendar.php?action=groups&sid={$sid}';">
				<i class="ti ti-users-group icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="editgroups"}
			</button>
		</div>
	</div>

	<div class="scrollContainer withBottomBar bm-organizer-calendar-body bm-organizer-calendar-month" id="calendarContainer">
		<div class="bm-organizer-month-grid">
		<table class="bigTable bm-organizer-month-table">
			<thead>
			<tr>
				{foreach from=$columns item=column}
				<th width="14%">{$wdays[$column]}</th>
				{/foreach}
			</tr>
			</thead>
			<tbody>
			<tr class="bm-organizer-month-week">
				{assign var=i value=0}{foreach from=$days item=day key=dayKey}{if !$day}<td class="bm-organizer-month-cell bm-organizer-month-cell-empty"></td>{else}
				<td valign="top" class="monthCell{if $day.today}Today{/if} bm-organizer-month-cell">
					<div class="monthCellDay bm-organizer-month-day" style="{if $day.today}font-weight:bold;{/if}" onclick="document.location.href='organizer.calendar.php?view=day&date={$day.dayStart}&sid={$sid}';">{$day.day}</div>
					{foreach from=$day.dates item=date}
					<div class="monthDate_{$groups[$date.group].color} bm-organizer-month-event" onclick="showCalendarDate({$date.id}, {$date.startdate}, {$date.enddate}, false)">
						{if $date.startdate<$day.dayStart}&lt;{/if}
						{text value=$date.title cut=18}
						{if $date.enddate>$day.dayEnd}&gt;{/if}
					</div>
					{/foreach}
				</td>
				{/if}{assign var=i value=$i+1}{if $i>6&&$lastDayKey!=$dayKey}
			</tr>

			<tr class="bm-organizer-month-week">
				{assign var=i value=0}{/if}{/foreach}{if $i<7}{math equation="7 - i" i=$i assign=left}{section name=remainingCells loop=$left}<td class="bm-organizer-month-cell bm-organizer-month-cell-empty"></td>{/section}{/if}
			</tr>
			</tbody>
		</table>
		</div>
		<script>
		<!--
			registerLoadAction('calendarMonthSizer()');
		//-->
		</script>
	</div>

	<div id="contentFooter" class="contentFooter bm-organizer-footer">
		<div class="right">
			<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='organizer.calendar.php?action=addDate&date={$theDate}&sid={$sid}';">
				<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="adddate"}
			</button>
		</div>
	</div>
</div>
