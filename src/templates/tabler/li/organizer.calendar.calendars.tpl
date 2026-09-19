<div class="bm-organizer-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-calendar icon icon-sm" aria-hidden="true"></i>
			{lng p="calendars"}
		</div>
		<div class="right">
			<button type="button" class="btn btn-sm btn-primary" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params='action=calendars&do=addForm'}', '{lng p="addcalendar"|escape:'javascript'}', 440, 260);">
				<i class="ti ti-plus icon icon-sm me-1"></i>{lng p="add"}
			</button>
		</div>
	</div>
	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter">
				<thead>
					<tr>
						<th>{lng p="title"}</th>
						<th style="width:80px;">{lng p="color"}</th>
						<th style="width:80px;">{lng p="default"}</th>
						<th style="width:80px;"></th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$calendars key=calID item=cal}
				<tr>
					<td>{text value=$cal.title}</td>
					<td><div class="calendarDate_{$cal.color}" style="width:12px;height:12px;"></div></td>
					<td>{if $cal.is_default}{lng p="yes"}{/if}</td>
					<td class="text-nowrap">
						<a href="#" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params="action=calendars&do=edit&id={$calID}"}', '{lng p="editcalendar"|escape:'javascript'}', 440, 260);"><i class="ti ti-pencil"></i></a>
						{if $cal.is_default}
						<span class="bm-organizer-action-disabled" title="{lng p="nodeletdefault"}" aria-label="{lng p="nodeletdefault"}" aria-disabled="true"><i class="ti ti-trash"></i></span>
						{else}
						<a href="#" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params="action=calendars&do=deleteForm&id={$calID}"}', '{lng p="delete"|escape:'javascript'}', 400, 150);"><i class="ti ti-trash"></i></a>
						{/if}
					</td>
				</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>
