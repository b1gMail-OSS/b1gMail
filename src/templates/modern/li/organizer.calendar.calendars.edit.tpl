<div id="contentHeader">
	<div class="left">
		<i class="fa fa-calendar" aria-hidden="true"></i>
		{if $calendarItem}{lng p="editcalendar"}{else}{lng p="addcalendar"}{/if}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
<form name="f2" method="post" action="{sessionurl file='organizer.calendar.php' params="action=calendars&do={if $calendarItem}save&id={$calendarItem.id}{else}add{/if}"}" onsubmit="return(checkCalendarGroupForm(this));">
	{csrffield}
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2">{if $calendarItem}{lng p="editcalendar"}{else}{lng p="addcalendar"}{/if}</th>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="title">{lng p="title"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="title" id="title" value="{if isset($calendarItem.title)}{text value=$calendarItem.title allowEmpty=true}{/if}" size="34" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="color">{lng p="color"}:</label></td>
			<td class="listTableRight">
				<table>
					{section name=c loop=6}
					<tr>
						<td><input type="radio"{if (!$calendarItem && $smarty.section.c.index==0) || (isset($calendarItem.color) && $calendarItem.color==$smarty.section.c.index)} checked="checked"{/if} name="color" value="{$smarty.section.c.index}" /></td>
						<td><div class="calendarDate_{$smarty.section.c.index}" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
					</tr>
					{/section}
				</table>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>
</div></div>
