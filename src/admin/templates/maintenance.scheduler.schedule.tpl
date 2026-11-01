<fieldset>
	<legend>{lng p="sched.schedule"}</legend>
	<p>{lng p="sched.schedule_hint"}</p>

	<div class="sched-block">
		<div class="sched-block__head">
			<span class="fw-medium">{lng p="sched.weekdays"}</span>
			<div class="btn-group btn-group-sm">
				<button type="button" class="btn" data-sched-toggle="all" data-sched-target="weekday">{lng p="sched.select_all"}</button>
				<button type="button" class="btn" data-sched-toggle="none" data-sched-target="weekday">{lng p="sched.select_none"}</button>
			</div>
		</div>
		<div class="sched-grid sched-grid--week" data-sched-group="weekday">
			{section name=weekday start=0 loop=7 step=1}
				<input type="checkbox" class="btn-check" name="crondata[weekday][]" value="{$smarty.section.weekday.index}" id="sched-wd-{$smarty.section.weekday.index}"{if isset($sched_data.crondata.weekday) && ($smarty.section.weekday.index|in_array:$sched_data.crondata.weekday)} checked="checked"{/if} />
				<label class="btn btn-outline-primary" for="sched-wd-{$smarty.section.weekday.index}">{$sched_weekdays_short[$smarty.section.weekday.index]}</label>
			{/section}
		</div>
	</div>

	<div class="sched-block">
		<div class="sched-block__head">
			<span class="fw-medium">{lng p="sched.month_days"}</span>
			<div class="btn-group btn-group-sm">
				<button type="button" class="btn" data-sched-toggle="all" data-sched-target="day">{lng p="sched.select_all"}</button>
				<button type="button" class="btn" data-sched-toggle="none" data-sched-target="day">{lng p="sched.select_none"}</button>
			</div>
		</div>
		<div class="sched-grid sched-grid--days" data-sched-group="day">
			{section name=day start=1 loop=32 step=1}
				<input type="checkbox" class="btn-check" name="crondata[day][]" value="{$smarty.section.day.index}" id="sched-day-{$smarty.section.day.index}"{if isset($sched_data.crondata.day) && ($smarty.section.day.index|in_array:$sched_data.crondata.day)} checked="checked"{/if} />
				<label class="btn btn-outline-primary" for="sched-day-{$smarty.section.day.index}">{$smarty.section.day.index|string_format:"%02d"}</label>
			{/section}
		</div>
	</div>

	<div class="sched-block">
		<div class="sched-block__head">
			<span class="fw-medium">{lng p="sched.months"}</span>
			<div class="btn-group btn-group-sm">
				<button type="button" class="btn" data-sched-toggle="all" data-sched-target="month">{lng p="sched.select_all"}</button>
				<button type="button" class="btn" data-sched-toggle="none" data-sched-target="month">{lng p="sched.select_none"}</button>
			</div>
		</div>
		<div class="sched-grid sched-grid--months" data-sched-group="month">
			{section name=month start=1 loop=13 step=1}
				<input type="checkbox" class="btn-check" name="crondata[month][]" value="{$smarty.section.month.index}" id="sched-month-{$smarty.section.month.index}"{if isset($sched_data.crondata.month) && ($smarty.section.month.index|in_array:$sched_data.crondata.month)} checked="checked"{/if} />
				<label class="btn btn-outline-primary" for="sched-month-{$smarty.section.month.index}">{$smarty.section.month.index|string_format:"%02d"}</label>
			{/section}
		</div>
	</div>

	<div class="sched-block">
		<div class="sched-block__head">
			<span class="fw-medium">{lng p="sched.hours_label"}</span>
			<div class="btn-group btn-group-sm">
				<button type="button" class="btn" data-sched-toggle="all" data-sched-target="hour">{lng p="sched.select_all"}</button>
				<button type="button" class="btn" data-sched-toggle="none" data-sched-target="hour">{lng p="sched.select_none"}</button>
			</div>
		</div>
		<div class="sched-grid sched-grid--hours" data-sched-group="hour">
			{section name=hour start=0 loop=24 step=1}
				<input type="checkbox" class="btn-check" name="crondata[hour][]" value="{$smarty.section.hour.index}" id="sched-hour-{$smarty.section.hour.index}"{if isset($sched_data.crondata.hour) && ($smarty.section.hour.index|in_array:$sched_data.crondata.hour)} checked="checked"{/if} />
				<label class="btn btn-outline-primary" for="sched-hour-{$smarty.section.hour.index}">{$smarty.section.hour.index|string_format:"%02d"}</label>
			{/section}
		</div>
	</div>

	<div class="sched-block">
		<div class="sched-block__head">
			<span class="fw-medium">{lng p="sched.minutes_label"}</span>
			<div class="btn-group btn-group-sm">
				<button type="button" class="btn" data-sched-toggle="all" data-sched-target="minute">{lng p="sched.select_all"}</button>
				<button type="button" class="btn" data-sched-toggle="none" data-sched-target="minute">{lng p="sched.select_none"}</button>
			</div>
		</div>
		<div class="sched-grid sched-grid--minutes" data-sched-group="minute">
			{section name=minute start=0 loop=60 step=1}
				<input type="checkbox" class="btn-check" name="crondata[minute][]" value="{$smarty.section.minute.index}" id="sched-minute-{$smarty.section.minute.index}"{if isset($sched_data.crondata.minute) && ($smarty.section.minute.index|in_array:$sched_data.crondata.minute)} checked="checked"{/if} />
				<label class="btn btn-outline-primary" for="sched-minute-{$smarty.section.minute.index}">{$smarty.section.minute.index|string_format:"%02d"}</label>
			{/section}
		</div>
	</div>
</fieldset>
