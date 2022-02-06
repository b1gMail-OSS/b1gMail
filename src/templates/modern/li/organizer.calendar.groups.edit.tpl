<div id="contentHeader">
	<div class="left">
		<i class="fa fa-calendar-o" aria-hidden="true"></i>
		{if $group}{lng p="editgroup"}{else}{lng p="addgroup"}{/if}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f2" method="post" action="organizer.calendar.php?action=groups&do={if $group}save&id={$group.id}{else}add{/if}&sid={$sid}" onsubmit="return(checkCalendarGroupForm(this));">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {if $group}{lng p="editgroup"}{else}{lng p="addgroup"}{/if}</th>
		</tr>
		
		<tr>
			<td class="listTableLeft">* <label for="title">{lng p="title"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="title" id="title" value="{text value=$group.title allowEmpty=true}" size="34" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="title">{lng p="color"}:</label></td>
			<td class="listTableRight">
				<table>
					<tr>
						<td><input type="radio"{if !$group||$group.color==0} checked="checked"{/if} name="color" value="0" /></td>
						<td><div class="calendarDate_0" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
					</tr>
					<tr>
						<td><input type="radio"{if $group.color==1} checked="checked"{/if} name="color" value="1" /></td>
						<td><div class="calendarDate_1" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
					</tr>
					<tr>
						<td><input type="radio"{if $group.color==2} checked="checked"{/if} name="color" value="2" /></td>
						<td><div class="calendarDate_2" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
					</tr>
					<tr>
						<td><input type="radio"{if $group.color==3} checked="checked"{/if} name="color" value="3" /></td>
						<td><div class="calendarDate_3" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
					</tr>
					<tr>
						<td><input type="radio"{if $group.color==4} checked="checked"{/if} name="color" value="4" /></td>
						<td><div class="calendarDate_4" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
					</tr>
					<tr>
						<td><input type="radio"{if $group.color==5} checked="checked"{/if} name="color" value="5" /></td>
						<td><div class="calendarDate_5" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
					</tr>
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
