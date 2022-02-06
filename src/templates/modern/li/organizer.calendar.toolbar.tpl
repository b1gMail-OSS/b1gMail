<form action="organizer.calendar.php?sid={$sid}" method="post">
<table cellspacing="0" cellpadding="0">
	<tr>
		{comment text="viewmode"}
		<td><img align="absmiddle" src="{$tpldir}images/li/tb_sep.gif" border="0" alt="" /></td>
		<td><small>&nbsp; {lng p="viewmode"}: &nbsp;</small></td>
		<td><select class="smallInput" onchange="updateCalendarViewMode(this, '{$theDate}', '{$sid}')">
			<option value="day"{if $viewMode=="day"} selected="selected"{/if}>{lng p="day"}</option>
			<option value="week"{if $viewMode=="week"} selected="selected"{/if}>{lng p="week"}</option>
			<option value="month"{if $viewMode=="month"} selected="selected"{/if}>{lng p="month"}</option>
		</select></td>
		
		{comment text="groups"}
		<td width="15">&nbsp;</td>
		<td><img align="absmiddle" src="{$tpldir}images/li/tb_sep.gif" border="0" alt="" /></td>
		<td><small>&nbsp; {lng p="group"}: &nbsp;</small></td>
		<td><select class="smallInput" onchange="updateCalendarGroup(this, '{$theDate}', '{$sid}')">
			<option value="-2"{if $theGroup==-2} selected="selected"{/if}>------------</option>
			<option value="-1"{if $theGroup==-1} selected="selected"{/if}>{lng p="nocalcat"}</option>
			<optgroup label="{lng p="groups"}">
			{foreach from=$groups item=group}
			{if $group.id>0}
				<option value="{$group.id}"{if $theGroup==$group.id} selected="selected"{/if}>{text value=$group.title}</option>
			{/if}
			{/foreach}
			</optgroup>
		</select></td>
		
		{comment text="date navigation"}
		<td width="15">&nbsp;</td>
		<td><img align="absmiddle" src="{$tpldir}images/li/tb_sep.gif" border="0" alt="" /></td>
		<td><small>&nbsp; {lng p="date"}: &nbsp;</small></td>
		
		{if $viewMode=='day'}
		<td>&nbsp;<a href="organizer.calendar.php?sid={$sid}&date={$date-86400}"><i class="fa fa-backward"></i></a>&nbsp;</td>
		<td>{html_select_date prefix="date_" time=$date start_year="-5" end_year="+5" field_order="DMY"}</td>
		<td><input type="submit" class="smallInput" value=" {lng p="today"} " name="jumpToday" /></td>
		<td><input type="submit" class="smallInput" value=" {lng p="ok"} " /></td>
		<td>&nbsp;<a href="organizer.calendar.php?sid={$sid}&date={$date+86400}"><i class="fa fa-forward"></i></a>&nbsp;</td>
		
		{elseif $viewMode=='week'}
		<td>&nbsp;<a href="organizer.calendar.php?sid={$sid}&date={$prevWeek}"><i class="fa fa-backward"></i></a>&nbsp;</td>
		<td>
			<select name="date_Week">
				{section name=w start=1 loop=53 step=1}
				<option value="{$smarty.section.w.index}"{if $smarty.section.w.index==$calWeekNo} selected="selected"{/if}>{lng p="cw"} {$smarty.section.w.index}</option>
				{/section}
			</select>
			{html_select_date prefix="date_" time=$date start_year="-5" end_year="+5" field_order="Y"}
		</td>
		<td><input type="submit" class="smallInput" value=" {lng p="today"} " name="jumpToday" /></td>
		<td><input type="submit" class="smallInput" value=" {lng p="ok"} " /></td>
		<td>&nbsp;<a href="organizer.calendar.php?sid={$sid}&date={$nextWeek}"><i class="fa fa-forward"></i></a>&nbsp;</td>
		
		{elseif $viewMode=='month'}
		<td>&nbsp;<a href="organizer.calendar.php?sid={$sid}&date={$prevMonth}"><i class="fa fa-backward"></i></a>&nbsp;</td>
		<td>{html_select_date prefix="date_" time=$date display_days=false start_year="-5" end_year="+5" field_order="MY"}</td>
		<td><input type="submit" class="smallInput" value=" {lng p="today"} " name="jumpToday" /></td>
		<td><input type="submit" class="smallInput" value=" {lng p="ok"} " /></td>
		<td>&nbsp;<a href="organizer.calendar.php?sid={$sid}&date={$nextMonth}"><i class="fa fa-forward"></i></a>&nbsp;</td>

		{/if}
		
		</td>
	</tr>
</table>
</form>
