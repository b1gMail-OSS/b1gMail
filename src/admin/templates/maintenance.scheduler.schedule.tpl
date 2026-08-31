<fieldset class="mb-0">
	<legend class="h5 text-secondary mb-2">{lng p="sched.schedule"}</legend>
	<p class="text-secondary small mb-4">{lng p="sched.schedule_hint"}</p>

	<div class="card mb-3">
		<div class="card-header">
			<h3 class="card-title">{lng p="sched.weekdays"}</h3>
			<div class="card-actions btn-list">
				<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="all" data-tccrn-target="weekday">{lng p="sched.select_all"}</button>
				<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="none" data-tccrn-target="weekday">{lng p="sched.select_none"}</button>
			</div>
		</div>
		<div class="card-body">
			<div class="tccrn-btn-grid" data-tccrn-group="weekday">
				{section name=weekday start=0 loop=7 step=1}
					<input type="checkbox" class="btn-check" name="crondata[weekday][]" value="{$smarty.section.weekday.index}" id="tccrn-wd-{$smarty.section.weekday.index}"{if isset($tccrn_data.crondata.weekday) && ($smarty.section.weekday.index|in_array:$tccrn_data.crondata.weekday)} checked="checked"{/if} />
					<label class="btn btn-outline-primary btn-sm" for="tccrn-wd-{$smarty.section.weekday.index}">{$sched_weekdays_short[$smarty.section.weekday.index]}</label>
				{/section}
			</div>
		</div>
	</div>

	<div class="card mb-3">
		<div class="card-header">
			<h3 class="card-title">{lng p="sched.month_days"}</h3>
			<div class="card-actions btn-list">
				<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="all" data-tccrn-target="day">{lng p="sched.select_all"}</button>
				<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="none" data-tccrn-target="day">{lng p="sched.select_none"}</button>
			</div>
		</div>
		<div class="card-body">
			<div class="tccrn-btn-grid tccrn-btn-grid--compact" data-tccrn-group="day">
				{section name=day start=1 loop=32 step=1}
					<input type="checkbox" class="btn-check" name="crondata[day][]" value="{$smarty.section.day.index}" id="tccrn-day-{$smarty.section.day.index}"{if isset($tccrn_data.crondata.day) && ($smarty.section.day.index|in_array:$tccrn_data.crondata.day)} checked="checked"{/if} />
					<label class="btn btn-outline-primary btn-sm" for="tccrn-day-{$smarty.section.day.index}">{$smarty.section.day.index}</label>
				{/section}
			</div>
		</div>
	</div>

	<div class="card mb-3">
		<div class="card-header">
			<h3 class="card-title">{lng p="sched.months"}</h3>
			<div class="card-actions btn-list">
				<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="all" data-tccrn-target="month">{lng p="sched.select_all"}</button>
				<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="none" data-tccrn-target="month">{lng p="sched.select_none"}</button>
			</div>
		</div>
		<div class="card-body">
			<div class="tccrn-btn-grid tccrn-btn-grid--compact" data-tccrn-group="month">
				{section name=month start=1 loop=13 step=1}
					<input type="checkbox" class="btn-check" name="crondata[month][]" value="{$smarty.section.month.index}" id="tccrn-month-{$smarty.section.month.index}"{if isset($tccrn_data.crondata.month) && ($smarty.section.month.index|in_array:$tccrn_data.crondata.month)} checked="checked"{/if} />
					<label class="btn btn-outline-primary btn-sm" for="tccrn-month-{$smarty.section.month.index}">{$smarty.section.month.index}</label>
				{/section}
			</div>
		</div>
	</div>

	<div class="card mb-3">
		<div class="card-header">
			<h3 class="card-title">{lng p="sched.hours_label"}</h3>
			<div class="card-actions btn-list">
				<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="all" data-tccrn-target="hour">{lng p="sched.select_all"}</button>
				<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="none" data-tccrn-target="hour">{lng p="sched.select_none"}</button>
			</div>
		</div>
		<div class="card-body">
			<div class="tccrn-btn-grid tccrn-btn-grid--compact" data-tccrn-group="hour">
				{section name=hour start=0 loop=24 step=1}
					<input type="checkbox" class="btn-check" name="crondata[hour][]" value="{$smarty.section.hour.index}" id="tccrn-hour-{$smarty.section.hour.index}"{if isset($tccrn_data.crondata.hour) && ($smarty.section.hour.index|in_array:$tccrn_data.crondata.hour)} checked="checked"{/if} />
					<label class="btn btn-outline-primary btn-sm" for="tccrn-hour-{$smarty.section.hour.index}">{$smarty.section.hour.index}</label>
				{/section}
			</div>
		</div>
	</div>

	<div class="card mb-3">
		<div class="card-header">
			<h3 class="card-title">{lng p="sched.minutes_label"}</h3>
			<div class="card-actions btn-list">
				<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="all" data-tccrn-target="minute">{lng p="sched.select_all"}</button>
				<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="none" data-tccrn-target="minute">{lng p="sched.select_none"}</button>
			</div>
		</div>
		<div class="card-body">
			<div class="tccrn-btn-grid tccrn-btn-grid--minute" data-tccrn-group="minute">
				{section name=minute start=0 loop=60 step=1}
					<input type="checkbox" class="btn-check" name="crondata[minute][]" value="{$smarty.section.minute.index}" id="tccrn-minute-{$smarty.section.minute.index}"{if isset($tccrn_data.crondata.minute) && ($smarty.section.minute.index|in_array:$tccrn_data.crondata.minute)} checked="checked"{/if} />
					<label class="btn btn-outline-primary btn-sm" for="tccrn-minute-{$smarty.section.minute.index}">{$smarty.section.minute.index}</label>
				{/section}
			</div>
		</div>
	</div>
</fieldset>
