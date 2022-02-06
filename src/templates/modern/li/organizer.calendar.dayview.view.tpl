<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Calendar Day View</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}style/loggedin.css" rel="stylesheet" type="text/css" />
	<link href="{$tpldir}style/dtree.css" rel="stylesheet" type="text/css" />
	
	<!-- client scripts -->
	<script>
	<!--
		var currentSID = '{$sid}', tplDir = '{$tpldir}', serverTZ = {$serverTZ};
	//-->
	</script>
	<script src="clientlang.php?sid={$sid}" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/organizer.js" type="text/javascript"></script>
	<script src="clientlib/dtree.js" type="text/javascript"></script>
	<script src="clientlib/overlay.js" type="text/javascript"></script>
	<script src="clientlib/autocomplete.js" type="text/javascript"></script>
</head>

<body onload="initCalendar()" style="background-color:#FFF;background-image:none;">
	<div id="calendarDayBody">
		<table class="calendarDayBody">
		{section name=halfHours start=0 loop=48}
		<tr>
		{if $smarty.section.halfHours.index%2==0}
			<td class="calendarDayTimeCell" rowspan="2">
				<div class="calendarDayTimeCellText"><a href="organizer.calendar.php?action=addDate&date={$dateStart}&time={halfHourToTime value=$smarty.section.halfHours.index dateStart=$dateStart}&sid={$sid}" target="_top">{halfHourToTime value=$smarty.section.halfHours.index}</a></div>
			</td>
		{/if}
		{if $smarty.section.halfHours.index==0}
			<td class="calendarDaySepCell" rowspan="48"></td>
			<td class="calendarDaySepCell2" rowspan="48"></td>
		{/if}
			<td class="calendarDayCell{if $smarty.section.halfHours.index%2}2{/if}{if $smarty.section.halfHours.index>=$dayStart && $smarty.section.halfHours.index<$dayEnd}_day{/if}" id="timeRow_{$smarty.section.halfHours.index}" style="{if $smarty.section.halfHours.index==0}border-top:0;{/if}">
				&nbsp;
			</td>
		</tr>
		{/section}
		</table>
	</div>

	<script>
	<!--
		var calendarDayStart = {$dayStart},
			calendarDayEnd = {$dayEnd},
			calendarDates = [];
		
		{foreach from=$dates item=date}
		{if ($date.flags&1)==0}
		calendarDates.push([
			{$date.id},
			{$date.startdate},
			{$date.enddate},
			"{text escape=true noentities=true value=$date.title}",
			{$groups[$date.group].color}
		]);
		{/if}
		{/foreach}
	//-->
	</script>
</body>

</html>
