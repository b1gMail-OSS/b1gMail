<div class="col-12 bm-li-email-toolbar py-0">
	<form action="organizer.calendar.php?sid={$sid}" method="post" class="d-flex flex-wrap align-items-center gap-2 gap-md-3 w-100">
		<div class="bm-li-toolbar-item d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
			<span class="bm-li-toolbar-label" aria-label="{lng p="viewmode"}">
				<i class="icon ti ti-layout-list icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text">{lng p="viewmode"}</span>
			</span>
			<select class="form-select form-select-sm bm-li-toolbar-select" onchange="updateCalendarViewMode(this, '{$theDate}', '{$sid}')">
				<option value="day"{if $viewMode=="day"} selected="selected"{/if}>{lng p="day"}</option>
				<option value="week"{if $viewMode=="week"} selected="selected"{/if}>{lng p="week"}</option>
				<option value="month"{if $viewMode=="month"} selected="selected"{/if}>{lng p="month"}</option>
			</select>
		</div>

		<div class="bm-li-toolbar-divider d-none d-md-block" aria-hidden="true"></div>

		<div class="bm-li-toolbar-item d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
			<span class="bm-li-toolbar-label" aria-label="{lng p="group"}">
				<i class="icon ti ti-users-group icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text">{lng p="group"}</span>
			</span>
			<select class="form-select form-select-sm bm-li-toolbar-select bm-li-toolbar-select-wide" onchange="updateCalendarGroup(this, '{$theDate}', '{$sid}')">
				<option value="-2"{if $theGroup==-2} selected="selected"{/if}>------------</option>
				<option value="-1"{if $theGroup==-1} selected="selected"{/if}>{lng p="nocalcat"}</option>
				<optgroup label="{lng p="groups"}">
				{foreach from=$groups item=group}
				{if $group.id>0}
					<option value="{$group.id}"{if $theGroup==$group.id} selected="selected"{/if}>{text value=$group.title}</option>
				{/if}
				{/foreach}
				</optgroup>
			</select>
		</div>

		<div class="bm-li-toolbar-divider d-none d-md-block" aria-hidden="true"></div>

		<div class="bm-li-toolbar-item d-flex flex-wrap align-items-center gap-2 flex-grow-1">
			<span class="bm-li-toolbar-label" aria-label="{lng p="date"}">
				<i class="icon ti ti-calendar icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text">{lng p="date"}</span>
			</span>

			<div class="bm-li-toolbar-nav d-flex flex-wrap align-items-center gap-2">
				{if $viewMode=='day'}
				<a href="organizer.calendar.php?sid={$sid}&date={$date-86400}" class="btn btn-sm btn-ghost-secondary btn-icon bm-li-toolbar-btn" title="{lng p="back"}" aria-label="{lng p="back"}"><i class="ti ti-chevron-left icon" aria-hidden="true"></i></a>
				<span class="bm-organizer-date-select">{html_select_date prefix="date_" time=$date start_year="-5" end_year="+5" field_order="DMY"}</span>
				<button type="submit" class="btn btn-sm btn-ghost-secondary bm-li-toolbar-btn" name="jumpToday">{lng p="today"}</button>
				<button type="submit" class="btn btn-sm btn-ghost-primary bm-li-toolbar-btn">{lng p="ok"}</button>
				<a href="organizer.calendar.php?sid={$sid}&date={$date+86400}" class="btn btn-sm btn-ghost-secondary btn-icon bm-li-toolbar-btn" title="{lng p="forward"}" aria-label="{lng p="forward"}"><i class="ti ti-chevron-right icon" aria-hidden="true"></i></a>

				{elseif $viewMode=='week'}
				<a href="organizer.calendar.php?sid={$sid}&date={$prevWeek}" class="btn btn-sm btn-ghost-secondary btn-icon bm-li-toolbar-btn" title="{lng p="back"}" aria-label="{lng p="back"}"><i class="ti ti-chevron-left icon" aria-hidden="true"></i></a>
				<select class="form-select form-select-sm bm-li-toolbar-select" name="date_Week">
					{section name=w start=1 loop=53 step=1}
					<option value="{$smarty.section.w.index}"{if isset($calWeekNo) && $smarty.section.w.index==$calWeekNo} selected="selected"{/if}>{lng p="cw"} {$smarty.section.w.index}</option>
					{/section}
				</select>
				<span class="bm-organizer-date-select">{html_select_date prefix="date_" time=$date start_year="-5" end_year="+5" field_order="Y"}</span>
				<button type="submit" class="btn btn-sm btn-ghost-secondary bm-li-toolbar-btn" name="jumpToday">{lng p="today"}</button>
				<button type="submit" class="btn btn-sm btn-ghost-primary bm-li-toolbar-btn">{lng p="ok"}</button>
				<a href="organizer.calendar.php?sid={$sid}&date={$nextWeek}" class="btn btn-sm btn-ghost-secondary btn-icon bm-li-toolbar-btn" title="{lng p="forward"}" aria-label="{lng p="forward"}"><i class="ti ti-chevron-right icon" aria-hidden="true"></i></a>

				{elseif $viewMode=='month'}
				<a href="organizer.calendar.php?sid={$sid}&date={$prevMonth}" class="btn btn-sm btn-ghost-secondary btn-icon bm-li-toolbar-btn" title="{lng p="back"}" aria-label="{lng p="back"}"><i class="ti ti-chevron-left icon" aria-hidden="true"></i></a>
				<span class="bm-organizer-date-select">{html_select_date prefix="date_" time=$date display_days=false start_year="-5" end_year="+5" field_order="MY"}</span>
				<button type="submit" class="btn btn-sm btn-ghost-secondary bm-li-toolbar-btn" name="jumpToday">{lng p="today"}</button>
				<button type="submit" class="btn btn-sm btn-ghost-primary bm-li-toolbar-btn">{lng p="ok"}</button>
				<a href="organizer.calendar.php?sid={$sid}&date={$nextMonth}" class="btn btn-sm btn-ghost-secondary btn-icon bm-li-toolbar-btn" title="{lng p="forward"}" aria-label="{lng p="forward"}"><i class="ti ti-chevron-right icon" aria-hidden="true"></i></a>
				{/if}
			</div>
		</div>
	</form>
</div>
