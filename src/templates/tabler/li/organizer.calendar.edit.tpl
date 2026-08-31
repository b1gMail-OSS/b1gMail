<div class="bm-organizer-page bm-organizer-form-page bm-organizer-date-form">
	<form name="f2" method="post" action="organizer.calendar.php?action={if !empty($eDate)}saveDate&id={$eDate.id}{if !empty($smarty.get.jumpbackDate)}&jumpbackDate={text value=$smarty.get.jumpbackDate allowEmpty=true}{/if}{else}createDate{/if}{$sessionUrlSuffix}" class="bm-organizer-form" onsubmit="return(checkCalendarDateForm(this));">
		{csrffield}
		<div id="contentHeader" class="bm-compose-header">
			<div class="left">
				<span class="bm-compose-header-title">{if !empty($eDate)}{lng p="editdate"}{else}{lng p="adddate"}{/if}</span>
			</div>
		</div>

		<div class="bm-organizer-form-body">

			<div class="bm-organizer-form-section mb-4">
				<h4 class="bm-organizer-form-section-title">
					<i class="ti ti-info-circle icon icon-sm me-1" aria-hidden="true"></i>{lng p="common"}
				</h4>
				<div class="row g-3">
					<div class="col-md-8">
						<label class="form-label required" for="title">{lng p="title"}</label>
						<input type="text" class="form-control" name="title" id="title" value="{if isset($eDate.title)}{text value=$eDate.title allowEmpty=true}{/if}" />
					</div>
					<div class="col-md-4">
						<label class="form-label" for="location">{lng p="location"}</label>
						<input type="text" class="form-control" name="location" id="location" value="{if isset($eDate.location)}{text value=$eDate.location allowEmpty=true}{/if}" />
					</div>
					<div class="col-12">
						<label class="form-label" for="text">{lng p="text"}</label>
						<textarea class="form-control" name="text" id="text" rows="4">{if isset($eDate.text)}{text value=$eDate.text allowEmpty=true}{/if}</textarea>
					</div>
				</div>
			</div>

			<div class="bm-organizer-form-section mb-4">
				<h4 class="bm-organizer-form-section-title">
					<i class="ti ti-calendar-time icon icon-sm me-1" aria-hidden="true"></i>{lng p="date"}
				</h4>
				<div class="row g-3">
					<div class="col-md-6">
						<label class="form-label required">{lng p="begin"}</label>
						<div class="bm-organizer-datetime">
							{html_select_date prefix="startdate" time=$startDate field_order="DMY" start_year="-5" end_year="+5" field_separator="."},
							{html_select_time prefix="startdate" time=$startTime minute_interval=5 display_seconds=false}
						</div>
					</div>
					<div class="col-md-6">
						<label class="form-label required">{lng p="duration"}</label>
						<div class="bm-organizer-form-options">
							<label class="form-check mb-2">
								<input type="radio" class="form-check-input" id="wholeDay_0" name="wholeDay" value="0"{if empty($eDate) || !($eDate.flags&1)} checked="checked"{/if} />
								<span class="form-check-label d-inline-flex flex-wrap align-items-center gap-2">
									<input type="text" class="form-control form-control-sm bm-organizer-input-xs" onfocus="EBID('wholeDay_0').checked=true;" name="durationHours" id="durationHours" value="{if isset($durationHours)}{$durationHours}{/if}" />
									<span>{lng p="hours"},</span>
									<input type="text" class="form-control form-control-sm bm-organizer-input-xs" onfocus="EBID('wholeDay_0').checked=true;" name="durationMinutes" id="durationMinutes" value="{if isset($durationMinutes)}{$durationMinutes}{/if}" />
									<span>{lng p="minutes"}</span>
								</span>
							</label>
							<label class="form-check mb-0">
								<input type="radio" class="form-check-input" id="wholeDay_1" name="wholeDay" value="1"{if (isset($eDate.flags) && $eDate.flags&1)} checked="checked"{/if} />
								<span class="form-check-label">{lng p="wholeday"}</span>
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="bm-organizer-form-section mb-4">
				<h4 class="bm-organizer-form-section-title">
					<i class="ti ti-repeat icon icon-sm me-1" aria-hidden="true"></i>{lng p="repeatoptions"}
				</h4>
				<label class="form-check mb-3">
					<input type="checkbox" class="form-check-input" name="repeating" id="repeating"{if isset($eDate.repeating)} checked="checked"{/if} onclick="toggleRepeatingDiv(this)" />
					<span class="form-check-label">{lng p="repeating"}</span>
				</label>

				<div id="repeatingDiv" class="bm-organizer-form-subsection"{if empty($eDate.repeating)} style="display:none;"{/if}>
					<div class="row g-3 mb-3">
						<div class="col-12">
							<label class="form-label">{lng p="repeatcount"}</label>
							<div class="bm-organizer-form-options">
								<label class="form-check mb-2">
									<input type="radio" class="form-check-input" name="repeat_until" id="repeat_until_endless" value="endless"{if empty($eDate)||(isset($eDate.repeat_flags) && $eDate.repeat_flags&1)} checked="checked"{/if} />
									<span class="form-check-label">{lng p="endless"}</span>
								</label>
								<label class="form-check mb-2 d-flex flex-wrap align-items-center gap-2">
									<input type="radio" class="form-check-input" name="repeat_until" id="repeat_until_count" value="count"{if isset($eDate.repeat_flags) && $eDate.repeat_flags&2} checked="checked"{/if} />
									<input type="text" class="form-control form-control-sm bm-organizer-input-xs" name="repeat_until_count" value="{if isset($eDate)&&$eDate.repeat_flags&2}{$eDate.repeat_times}{else}5{/if}" />
									<span class="form-check-label">{lng p="times"}</span>
								</label>
								<label class="form-check mb-0 d-flex flex-wrap align-items-center gap-2">
									<input type="radio" class="form-check-input" name="repeat_until" id="repeat_until_date" value="date"{if isset($eDate.repeat_flags) && $eDate.repeat_flags&4} checked="checked"{/if} />
									<span class="form-check-label">{lng p="until"}</span>
									<span class="bm-organizer-datetime">
									{if isset($eDate)&&$eDate.repeat_flags&4}
										{html_select_date prefix="repeat_until_date" time=$eDate.repeat_times field_order="DMY" start_year="-5" end_year="+5" field_separator="."},
										{html_select_time prefix="repeat_until_date" time=$eDate.repeat_times minute_interval=5 display_seconds=false}
									{else}
										{html_select_date prefix="repeat_until_date" field_order="DMY" start_year="-5" end_year="+5" field_separator="."},
										{html_select_time prefix="repeat_until_date" minute_interval=5 display_seconds=false}
									{/if}
									</span>
								</label>
							</div>
						</div>
					</div>

					<div class="row g-3">
						<div class="col-12">
							<label class="form-label">{lng p="interval"}</label>
							<div class="bm-organizer-form-options">
								<label class="form-check mb-3 align-items-start">
									<input type="radio" class="form-check-input mt-1" name="repeat_interval" id="repeat_interval_daily" value="daily"{if empty($eDate)||(isset($eDate.repeat_flags) && $eDate.repeat_flags&8)} checked="checked"{/if} />
									<span class="form-check-label">
										{lng p="every"}
										<input type="text" class="form-control form-control-sm bm-organizer-input-xs d-inline-block" name="repeat_interval_daily" value="{if isset($eDate)&&$eDate.repeat_flags&8}{$eDate.repeat_value}{else}1{/if}" />
										{lng p="days"}<br />
										<span class="text-secondary small">{lng p="besides"}</span>
										<span class="d-inline-flex flex-wrap gap-2 mt-1">
										{foreach from=$weekDays item=weekDay key=weekDayID}
											<label class="form-check form-check-inline mb-0" for="rd_ex_{$weekDayID}">
												<input type="checkbox" class="form-check-input" name="repeat_daily_exceptions[]"{if isset($eDate)&&$eDate.repeat_flags&8&&$repeatExtraDays[$weekDayID]} checked="checked"{/if} value="{$weekDayID}" id="rd_ex_{$weekDayID}" />
												<span class="form-check-label">{$weekDay}</span>
											</label>
										{/foreach}
										</span>
									</span>
								</label>

								<label class="form-check mb-2 d-flex flex-wrap align-items-center gap-2">
									<input type="radio" class="form-check-input" name="repeat_interval" id="repeat_interval_weekly" value="weekly"{if isset($eDate.repeat_flags) && $eDate.repeat_flags&16} checked="checked"{/if} />
									<span class="form-check-label">{lng p="every"}</span>
									<input type="text" class="form-control form-control-sm bm-organizer-input-xs" name="repeat_interval_weekly" value="{if isset($eDate)&&$eDate.repeat_flags&16}{$eDate.repeat_value}{else}1{/if}" />
									<span class="form-check-label">{lng p="weeks"}</span>
								</label>

								<label class="form-check mb-2 d-flex flex-wrap align-items-center gap-2">
									<input type="radio" class="form-check-input" name="repeat_interval" id="repeat_interval_monthly_mday" value="monthly_mday"{if isset($eDate.repeat_flags) && $eDate.repeat_flags&32} checked="checked"{/if} />
									<span class="form-check-label">{lng p="every"}</span>
									<input type="text" class="form-control form-control-sm bm-organizer-input-xs" name="repeat_interval_monthly_mday" value="{if isset($eDate)&&$eDate.repeat_flags&32}{$eDate.repeat_value}{else}1{/if}" />
									<span class="form-check-label">{lng p="months"} {lng p="at"}</span>
									<input type="text" class="form-control form-control-sm bm-organizer-input-xs" name="repeat_interval_monthly_mday_extra1" value="{if isset($eDate)&&$eDate.repeat_flags&32}{$eDate.repeat_extra1}{else}1{/if}" />
									<span class="form-check-label">. {lng p="ofthemonth"}</span>
								</label>

								<label class="form-check mb-2 d-flex flex-wrap align-items-center gap-2">
									<input type="radio" class="form-check-input" name="repeat_interval" id="repeat_interval_monthly_wday" value="monthly_wday"{if isset($eDate.repeat_flags) && $eDate.repeat_flags&64} checked="checked"{/if} />
									<span class="form-check-label">{lng p="every"}</span>
									<input type="text" class="form-control form-control-sm bm-organizer-input-xs" name="repeat_interval_monthly_wday" value="{if isset($eDate)&&$eDate.repeat_flags&64}{$eDate.repeat_value}{else}1{/if}" />
									<span class="form-check-label">{lng p="months"} {lng p="at"}</span>
									<select class="form-select form-select-sm bm-organizer-input-sm" name="repeat_interval_monthly_wday_extra1">
										<option value="0"{if isset($eDate)&&$eDate.repeat_flags&64&&$eDate.repeat_extra1==0} selected="selected"{/if}>{lng p="first"}</option>
										<option value="1"{if isset($eDate)&&$eDate.repeat_flags&64&&$eDate.repeat_extra1==1} selected="selected"{/if}>{lng p="second"}</option>
										<option value="2"{if isset($eDate)&&$eDate.repeat_flags&64&&$eDate.repeat_extra1==2} selected="selected"{/if}>{lng p="third"}</option>
										<option value="3"{if isset($eDate)&&$eDate.repeat_flags&64&&$eDate.repeat_extra1==3} selected="selected"{/if}>{lng p="fourth"}</option>
										<option value="4"{if isset($eDate)&&$eDate.repeat_flags&64&&$eDate.repeat_extra1==4} selected="selected"{/if}>{lng p="last"}</option>
									</select>
									<select class="form-select form-select-sm bm-organizer-input-sm" name="repeat_interval_monthly_wday_extra2">
									{foreach from=$weekDays item=weekDay key=weekDayID}
										<option value="{$weekDayID}"{if isset($eDate)&&$eDate.repeat_flags&64&&$eDate.repeat_extra2==$weekDayID} selected="selected"{/if}>{$weekDay}</option>
									{/foreach}
									</select>
									<span class="form-check-label">{lng p="ofthemonth"}</span>
								</label>

								<label class="form-check mb-0 d-flex flex-wrap align-items-center gap-2">
									<input type="radio" class="form-check-input" name="repeat_interval" id="repeat_interval_yearly" value="yearly"{if isset($eDate.repeat_flags) &&$eDate.repeat_flags&128} checked="checked"{/if} />
									<span class="form-check-label">{lng p="every"}</span>
									<input type="text" class="form-control form-control-sm bm-organizer-input-xs" name="repeat_interval_yearly" value="{if isset($eDate)&&(isset($eDate.repeat_flags) && $eDate.repeat_flags&128)}{$eDate.repeat_value}{else}1{/if}" />
									<span class="form-check-label">{lng p="years"}</span>
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="bm-organizer-form-section">
				<h4 class="bm-organizer-form-section-title">
					<i class="ti ti-adjustments icon icon-sm me-1" aria-hidden="true"></i>{lng p="misc"}
				</h4>
				<div class="row g-3">
					<div class="col-md-4">
						<label class="form-label" for="group">{lng p="group"}</label>
						<select class="form-select" name="group" id="group">
						{foreach from=$groups item=group key=groupID}
							<option value="{$groupID}"{if (!$eDate&&$groupID==-1) || ($eDate.group==$groupID)} selected="selected"{/if}>{text value=$group.title}</option>
						{/foreach}
						</select>
					</div>

					<div class="col-md-8">
						<label class="form-label">{lng p="reminder"}</label>
						<div class="row g-3">
							<div class="col-sm-6">
								<label class="form-check mb-1">
									<input type="checkbox" class="form-check-input" name="reminder_notify" id="reminderNotify"{if !$eDate||($eDate.flags&8)} checked="checked"{/if} />
									<span class="form-check-label">{lng p="bynotify"}</span>
								</label>
								<label class="form-check mb-1">
									<input type="checkbox" class="form-check-input" name="reminder_email" id="reminderEMail"{if isset($eDate)&& $eDate.flags&2} checked="checked"{/if} />
									<span class="form-check-label">{lng p="byemail"}</span>
								</label>
								{if $smsEnabled}
								<label class="form-check mb-0">
									<input type="checkbox" class="form-check-input" name="reminder_sms" id="reminderSMS"{if isset($eDate)&& $eDate.flags&4} checked="checked"{/if} />
									<span class="form-check-label">{lng p="bysms"}</span>
								</label>
								{/if}
							</div>
							<div class="col-sm-6">
								<div class="bm-organizer-form-subsection h-100">
									<label class="form-label mb-2" for="reminder">{lng p="timeframe"}</label>
									<select class="form-select form-select-sm" name="reminder" id="reminder">
										<optgroup label="{lng p="minutes"}">
											<option value="5"{if !$eDate||$eDate.reminder/60==5} selected="selected"{/if}>5 {lng p="minutes"}</option>
											<option value="15"{if isset($eDate.reminder) && $eDate.reminder/60==15} selected="selected"{/if}>15 {lng p="minutes"}</option>
											<option value="30"{if isset($eDate.reminder) && $eDate.reminder/60==30} selected="selected"{/if}>30 {lng p="minutes"}</option>
											<option value="45"{if isset($eDate.reminder) && $eDate.reminder/60==45} selected="selected"{/if}>45 {lng p="minutes"}</option>
										</optgroup>
										<optgroup label="{lng p="hours"}">
											<option value="60"{if isset($eDate.reminder) && $eDate.reminder/60==60} selected="selected"{/if}>1 {lng p="hours"}</option>
											<option value="120"{if isset($eDate.reminder) && $eDate.reminder/60==120} selected="selected"{/if}>2 {lng p="hours"}</option>
											<option value="240"{if isset($eDate.reminder) && $eDate.reminder/60==240} selected="selected"{/if}>4 {lng p="hours"}</option>
											<option value="480"{if isset($eDate.reminder) && $eDate.reminder/60==480} selected="selected"{/if}>8 {lng p="hours"}</option>
											<option value="720"{if isset($eDate.reminder) && $eDate.reminder/60==720} selected="selected"{/if}>12 {lng p="hours"}</option>
										</optgroup>
										<optgroup label="{lng p="days"}">
											<option value="1440"{if isset($eDate.reminder) && $eDate.reminder/60==1440} selected="selected"{/if}>1 {lng p="days"}</option>
											<option value="2880"{if isset($eDate.reminder) && $eDate.reminder/60==2880} selected="selected"{/if}>2 {lng p="days"}</option>
											<option value="5760"{if isset($eDate.reminder) && $eDate.reminder/60==5760} selected="selected"{/if}>4 {lng p="days"}</option>
											<option value="8640"{if isset($eDate.reminder) && $eDate.reminder/60==8640} selected="selected"{/if}>6 {lng p="days"}</option>
										</optgroup>
										<optgroup label="{lng p="weeks"}">
											<option value="10080"{if isset($eDate.reminder) && $eDate.reminder/60==10080} selected="selected"{/if}>1 {lng p="weeks"}</option>
											<option value="20160"{if isset($eDate.reminder) && $eDate.reminder/60==20160} selected="selected"{/if}>2 {lng p="weeks"}</option>
											<option value="30240"{if isset($eDate.reminder) && $eDate.reminder/60==30240} selected="selected"{/if}>3 {lng p="weeks"}</option>
											<option value="40320"{if isset($eDate.reminder) && $eDate.reminder/60==40320} selected="selected"{/if}>4 {lng p="weeks"}</option>
										</optgroup>
									</select>
									<div class="text-secondary small mt-2">{lng p="timebefore"}</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-12">
						<label class="form-label">{lng p="attendees"}</label>
						<input type="hidden" name="attendees" value="{if isset($attendees)}{text value=$attendees allowEmpty=true}{/if}" id="attendees" />
						<div id="attendeeList" class="bm-organizer-attendee-list"></div>
						<button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="openAttendeePopup('{$sid}');">
							<i class="ti ti-user-plus icon icon-sm me-1" aria-hidden="true"></i>{lng p="add"}
						</button>
						<label class="form-check mt-3 mb-0">
							<input class="form-check-input" type="checkbox" name="sendInvites" id="sendInvites" value="1" checked="checked" />
							<span class="form-check-label">{lng p="mail_att_send_invites"}</span>
						</label>
						<div class="form-text">{lng p="mail_att_send_invites_d"}</div>
						{if $attendees}
						<script>
						<!--
							registerLoadAction('generateAttendeeList()');
						//-->
						</script>
						{/if}
					</div>
				</div>
			</div>

			<div class="bm-organizer-form-footer">
				<div class="btn-list">
					<button type="submit" class="btn btn-primary">{lng p="ok"}</button>
					<button type="reset" class="btn">{lng p="reset"}</button>
				</div>
			</div>
		</div>
	</form>
</div>
<script>
<!--
	var bmOrganizerActionUrls = {
		attendeePopup: '{sessionurl file='organizer.addressbook.php' params='action=attendeePopup'|escape:'javascript'}'
	};
//-->
</script>
