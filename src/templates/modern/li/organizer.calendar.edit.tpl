<div id="contentHeader">
	<div class="left">
		<i class="fa fa-calendar" aria-hidden="true"></i>
		{if $eDate}{lng p="editdate"}{else}{lng p="adddate"}{/if}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f2" method="post" action="organizer.calendar.php?action={if $eDate}saveDate&id={$eDate.id}{if $smarty.get.jumpbackDate}&jumpbackDate={text value=$smarty.get.jumpbackDate allowEmpty=true}{/if}{else}createDate{/if}&sid={$sid}" onsubmit="return(checkCalendarDateForm(this));">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {if $eDate}{lng p="editdate"}{else}{lng p="adddate"}{/if}</th>
		</tr>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-calendar" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="common"}</td>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="title">{lng p="title"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="title" id="title" value="{text value=$eDate.title allowEmpty=true}" size="34" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="location">{lng p="location"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="location" id="location" value="{text value=$eDate.location allowEmpty=true}" size="34" style="width:60%;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="text">{lng p="text"}:</label></td>
			<td class="listTableRight">
				<textarea style="width:100%;height:100px;" name="text" id="text">{text value=$eDate.text allowEmpty=true}</textarea>
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-calendar-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="date"}</td>
		</tr>
		<tr>
			<td class="listTableLeft">* {lng p="begin"}:</td>
			<td class="listTableRight">
				{html_select_date prefix="startdate" time=$startDate field_order="DMY" start_year="-5" end_year="+5" field_separator="."},
				{html_select_time prefix="startdate" time=$startTime minute_interval=5 display_seconds=false}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">* {lng p="duration"}:</td>
			<td class="listTableRight">
				<table>
					<tr>
						<td><input type="radio" id="wholeDay_0" name="wholeDay" value="0"{if !$eDate || !($eDate.flags&1)} checked="checked"{/if} /></td>
						<td>
							<input type="text" onfocus="EBID('wholeDay_0').checked=true;" name="durationHours" id="durationHours" value="{$durationHours}" size="3" />
							{lng p="hours"},
							<input type="text" onfocus="EBID('wholeDay_0').checked=true;" name="durationMinutes" id="durationMinutes" value="{$durationMinutes}" size="3" />
							{lng p="minutes"}
						</td>
					</tr>
					<tr>
						<td><input type="radio" id="wholeDay_1" name="wholeDay" value="1"{if ($eDate.flags&1)} checked="checked"{/if} /></td>
						<td><label for="wholeDay_1">{lng p="wholeday"}</label></td>
					</tr>				
				</table>
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-calendar-plus-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="repeatoptions"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="repeating">{lng p="repeating"}?</td>
			<td class="listTableRight">
				<input type="checkbox" name="repeating" id="repeating"{if $eDate.repeating} checked="checked"{/if} onclick="toggleRepeatingDiv(this)" />
				<label for="repeating">{lng p="repeating"}</label>
			</td>
		</tr>
		<tbody id="repeatingDiv" style="display:{if !$eDate.repeating}none{/if};">
		<tr>
			<td class="listTableLeft"><label for="repeatCount">{lng p="repeatcount"}:</label></td>
			<td class="listTableRight">
				<table>
					<tr>
						<td><input type="radio" name="repeat_until" id="repeat_until_endless" value="endless"{if !$eDate||$eDate.repeat_flags&1} checked="checked"{/if} /></td>
						<td><label for="repeat_until_endless">{lng p="endless"}</label></td>
					</tr>
					<tr>
						<td><input type="radio" name="repeat_until" id="repeat_until_count" value="count"{if $eDate.repeat_flags&2} checked="checked"{/if} /></td>
						<td><input type="text" size="4" name="repeat_until_count" value="{if $eDate&&$eDate.repeat_flags&2}{$eDate.repeat_times}{else}5{/if}" /> <label for="repeat_until_count">{lng p="times"}</label></td>
					</tr>
					<tr>
						<td><input type="radio" name="repeat_until" id="repeat_until_date" value="date"{if $eDate.repeat_flags&4} checked="checked"{/if} /></td>
						<td><label for="repeat_until_date">{lng p="until"}</label>
						{if $eDate&&$eDate.repeat_flags&4}
							{html_select_date prefix="repeat_until_date" time=$eDate.repeat_times field_order="DMY" start_year="-5" end_year="+5" field_separator="."},
							{html_select_time prefix="repeat_until_date" time=$eDate.repeat_times minute_interval=5 display_seconds=false}
						{else}
							{html_select_date prefix="repeat_until_date" field_order="DMY" start_year="-5" end_year="+5" field_separator="."},
							{html_select_time prefix="repeat_until_date" minute_interval=5 display_seconds=false}
						{/if}</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="repeatCount">{lng p="interval"}:</label></td>
			<td class="listTableRight">
				<table>
					<tr>
						<td valign="top"><input type="radio" name="repeat_interval" id="repeat_interval_daily" value="daily"{if !$eDate||$eDate.repeat_flags&8} checked="checked"{/if} /></td>
						<td><label for="repeat_interval_daily">{lng p="every"}</label>
							<input type="text" name="repeat_interval_daily" value="{if $eDate&&$eDate.repeat_flags&8}{$eDate.repeat_value}{else}1{/if}" size="4" />
							{lng p="days"}<br />
							{lng p="besides"}
							{foreach from=$weekDays item=weekDay key=weekDayID}
							<input type="checkbox" name="repeat_daily_exceptions[]"{if $eDate&&$eDate.repeat_flags&8&&$repeatExtraDays[$weekDayID]} checked="checked"{/if} value="{$weekDayID}" id="rd_ex_{$weekDayID}" />
							<label for="rd_ex_{$weekDayID}">{$weekDay}</label>
							{/foreach}
						</td>
					</tr>
					<tr>
						<td valign="top"><input type="radio" name="repeat_interval" id="repeat_interval_weekly" value="weekly"{if $eDate.repeat_flags&16} checked="checked"{/if} /></td>
						<td><label for="repeat_interval_weekly">{lng p="every"}</label>
							<input type="text" name="repeat_interval_weekly" value="{if $eDate&&$eDate.repeat_flags&16}{$eDate.repeat_value}{else}1{/if}" size="4" />
							{lng p="weeks"}</td>
					</tr>
					<tr>
						<td valign="top"><input type="radio" name="repeat_interval" id="repeat_interval_monthly_mday" value="monthly_mday"{if $eDate.repeat_flags&32} checked="checked"{/if} /></td>
						<td><label for="repeat_interval_monthly_mday">{lng p="every"}</label>
							<input type="text" name="repeat_interval_monthly_mday" value="{if $eDate&&$eDate.repeat_flags&32}{$eDate.repeat_value}{else}1{/if}" size="4" />
							{lng p="months"} {lng p="at"}
							<input type="text" name="repeat_interval_monthly_mday_extra1" value="{if $eDate&&$eDate.repeat_flags&32}{$eDate.repeat_extra1}{else}1{/if}" size="4" />.
							{lng p="ofthemonth"}</td>
					</tr>
					<tr>
						<td valign="top"><input type="radio" name="repeat_interval" id="repeat_interval_monthly_wday" value="monthly_wday"{if $eDate.repeat_flags&64} checked="checked"{/if} /></td>
						<td><label for="repeat_interval_monthly_wday">{lng p="every"}</label>
							<input type="text" name="repeat_interval_monthly_wday" value="{if $eDate&&$eDate.repeat_flags&64}{$eDate.repeat_value}{else}1{/if}" size="4" />
							{lng p="months"} {lng p="at"}
							<select name="repeat_interval_monthly_wday_extra1">
								<option value="0"{if $eDate&&$eDate.repeat_flags&64&&$eDate.repeat_extra1==0} selected="selected"{/if}>{lng p="first"}</option>
								<option value="1"{if $eDate&&$eDate.repeat_flags&64&&$eDate.repeat_extra1==1} selected="selected"{/if}>{lng p="second"}</option>
								<option value="2"{if $eDate&&$eDate.repeat_flags&64&&$eDate.repeat_extra1==2} selected="selected"{/if}>{lng p="third"}</option>
								<option value="3"{if $eDate&&$eDate.repeat_flags&64&&$eDate.repeat_extra1==3} selected="selected"{/if}>{lng p="fourth"}</option>
								<option value="4"{if $eDate&&$eDate.repeat_flags&64&&$eDate.repeat_extra1==4} selected="selected"{/if}>{lng p="last"}</option>
							</select>
							<select name="repeat_interval_monthly_wday_extra2">
							{foreach from=$weekDays item=weekDay key=weekDayID}
								<option value="{$weekDayID}"{if $eDate&&$eDate.repeat_flags&64&&$eDate.repeat_extra2==$weekDayID} selected="selected"{/if}>{$weekDay}</option>
							{/foreach}
							</select>
							{lng p="ofthemonth"}</td>
					</tr>
					<tr>
						<td valign="top"><input type="radio" name="repeat_interval" id="repeat_interval_yearly" value="yearly"{if $eDate.repeat_flags&128} checked="checked"{/if} /></td>
						<td><label for="repeat_interval_yearly">{lng p="every"}</label>
							<input type="text" name="repeat_interval_yearly" value="{if $eDate&&$eDate.repeat_flags&128}{$eDate.repeat_value}{else}1{/if}" size="4" />
							{lng p="years"}</td>
					</tr>
				</table>
			</td>
		</tr>
		</tbody>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-calendar-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="misc"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="group">{lng p="group"}:</label></td>
			<td class="listTableRight">
				<select name="group" id="group">
				{foreach from=$groups item=group key=groupID}
					<option value="{$groupID}"{if (!$eDate&&$groupID==-1) || ($eDate.group==$groupID)} selected="selected"{/if}>{text value=$group.title}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="reminder"}:</td>
			<td class="listTableRight">
				<table>
					<tr>
						<td>
							<input type="checkbox" name="reminder_notify" id="reminderNotify"{if !$eDate||($eDate.flags&8)} checked="checked"{/if} /> 
								<label for="reminderNotify">{lng p="bynotify"}</label><br />
							<input type="checkbox" name="reminder_email" id="reminderEMail"{if $eDate.flags&2} checked="checked"{/if} /> 
								<label for="reminderEMail">{lng p="byemail"}</label><br />
							{if $smsEnabled}<input type="checkbox" name="reminder_sms" id="reminderSMS"{if $eDate.flags&4} checked="checked"{/if} />
								<label for="reminderSMS">{lng p="bysms"}</label>{/if}
						</td>
						<td width="20">
							&nbsp;
						</td>
						<td>
							<fieldset>
								<legend>{lng p="timeframe"}</legend>
								<select name="reminder">
									<optgroup label="{lng p="minutes"}">
										<option value="5"{if !$eDate||$eDate.reminder/60==5} selected="selected"{/if}>5 {lng p="minutes"}</option>
										<option value="15"{if $eDate.reminder/60==15} selected="selected"{/if}>15 {lng p="minutes"}</option>
										<option value="30"{if $eDate.reminder/60==30} selected="selected"{/if}>30 {lng p="minutes"}</option>
										<option value="45"{if $eDate.reminder/60==45} selected="selected"{/if}>45 {lng p="minutes"}</option>
									</optgroup>
									
									<optgroup label="{lng p="hours"}">
										<option value="60"{if $eDate.reminder/60==60} selected="selected"{/if}>1 {lng p="hours"}</option>
										<option value="120"{if $eDate.reminder/60==120} selected="selected"{/if}>2 {lng p="hours"}</option>
										<option value="240"{if $eDate.reminder/60==240} selected="selected"{/if}>4 {lng p="hours"}</option>
										<option value="480"{if $eDate.reminder/60==480} selected="selected"{/if}>8 {lng p="hours"}</option>
										<option value="720"{if $eDate.reminder/60==720} selected="selected"{/if}>12 {lng p="hours"}</option>
									</optgroup>
									
									<optgroup label="{lng p="days"}">
										<option value="1440"{if $eDate.reminder/60==1440} selected="selected"{/if}>1 {lng p="days"}</option>
										<option value="2880"{if $eDate.reminder/60==2880} selected="selected"{/if}>2 {lng p="days"}</option>
										<option value="5760"{if $eDate.reminder/60==5760} selected="selected"{/if}>4 {lng p="days"}</option>
										<option value="8640"{if $eDate.reminder/60==8640} selected="selected"{/if}>6 {lng p="days"}</option>
									</optgroup>
									
									<optgroup label="{lng p="weeks"}">
										<option value="10080"{if $eDate.reminder/60==10080} selected="selected"{/if}>1 {lng p="weeks"}</option>
										<option value="20160"{if $eDate.reminder/60==20160} selected="selected"{/if}>2 {lng p="weeks"}</option>
										<option value="30240"{if $eDate.reminder/60==30240} selected="selected"{/if}>3 {lng p="weeks"}</option>
										<option value="40320"{if $eDate.reminder/60==40320} selected="selected"{/if}>4 {lng p="weeks"}</option>
									</optgroup>
								</select>
								<label for="reminder">{lng p="timebefore"}</label>
							</fieldset>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="attendees"}:</td>
			<td class="listTableRight">
				<input type="hidden" name="attendees" value="{text value=$attendees allowEmpty=true}" id="attendees" />
				<div id="attendeeList"></div>
				<div>
					<a href="javascript:addAttendee('{$sid}')"><i class="fa fa-calendar-plus-o" aria-hidden="true"></i> {lng p="add"}</a>
				</div>
				{if $attendees}
				<script>
				<!--
					registerLoadAction('generateAttendeeList()');
				//-->
				</script>
				{/if}
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
