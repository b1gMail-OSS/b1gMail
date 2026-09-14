{if $pageContent|substr:0:22 == 'li/organizer.calendar.'}
<div class="bm-organizer-minical bm-organizer-minical-plain">
	{miniCalendar}
</div>
{/if}

{if $pageContent|substr:0:22 == 'li/organizer.calendar.'}
<div class="sidebarHeading bm-organizer-sidebar-heading">
	<span>{lng p="mycalendars"}</span>
	<a href="#" class="bm-organizer-sidebar-heading-action" title="{lng p="addcalendar"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params='action=calendars&do=addForm'}', '{lng p="addcalendar"|escape:'javascript'}', 440, 260);"><i class="fa fa-plus" aria-hidden="true"></i></a>
</div>
<div class="contentMenuIcons bm-organizer-sidebar-cals">
{foreach from=$calendars key=calID item=cal}
	<div class="bm-organizer-sidebar-cal bm-organizer-cal-{$cal.color}{if $currentCalendarID==$calID} current{/if}">
		<input type="checkbox" class="visibleCalBox" value="{$calID}"{if !isset($visibleCalendarIDs) || in_array($calID, $visibleCalendarIDs)} checked="checked"{/if} onclick="updateVisibleCalendars('{if isset($theDate)}{$theDate}{/if}')" />
		<span class="bm-organizer-cal-swatch"></span>
		<a href="{sessionurl file='organizer.calendar.php' params="calendar={$calID}"}">{text value=$cal.title cut=22}</a>
		<div class="btn-group btn-group-sm bm-organizer-sidebar-cal-actions" role="group" aria-label="{lng p="actions"}">
			<a href="#" class="btn btn-icon" title="{lng p="edit"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params="action=calendars&do=edit&id={$calID}"}', '{lng p="editcalendar"|escape:'javascript'}', 440, 260);"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			{if $canShareCalendar}
			<a href="#" class="btn btn-icon" title="{lng p="calendarshare"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params="action=share&id={$calID}"}', '{lng p="calendarshare"|escape:'javascript'}', 640, 480);"><i class="fa fa-share" aria-hidden="true"></i></a>
			{/if}
			{if $cal.is_default}
			<span class="btn btn-icon disabled" title="{lng p="nodeletdefault"}"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
			{else}
			<a href="#" class="btn btn-icon" title="{lng p="delete"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params="action=calendars&do=deleteForm&id={$calID}"}', '{lng p="delete"|escape:'javascript'}', 400, 150);"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
			{/if}
		</div>
	</div>
{/foreach}
</div>
{if isset($sharedCalendars) && $sharedCalendars}
<div class="sidebarHeading bm-organizer-sidebar-heading">
	<span>{lng p="sharedcalendars"}</span>
</div>
<div class="contentMenuIcons bm-organizer-sidebar-cals">
{foreach from=$sharedCalendars key=calID item=cal}
	<div class="bm-organizer-sidebar-cal bm-organizer-cal-{$cal.color}{if $currentCalendarID==$calID} current{/if}">
		<input type="checkbox" class="visibleCalBox" value="{$calID}"{if !isset($visibleCalendarIDs) || in_array($calID, $visibleCalendarIDs)} checked="checked"{/if} onclick="updateVisibleCalendars('{if isset($theDate)}{$theDate}{/if}')" />
		<span class="bm-organizer-cal-swatch"></span>
		<a href="{sessionurl file='organizer.calendar.php' params="calendar={$calID}"}" title="{text value=$cal.owner_email}">{text value=$cal.title cut=18}</a>
		<div class="btn-group btn-group-sm bm-organizer-sidebar-cal-actions" role="group" aria-label="{lng p="actions"}">
			<span class="btn btn-icon disabled" title="{lng p="edit"}"><i class="fa fa-pencil" aria-hidden="true"></i></span>
			{if $canShareCalendar}
			<span class="btn btn-icon disabled" title="{lng p="calendarshare"}"><i class="fa fa-share" aria-hidden="true"></i></span>
			{/if}
			<a href="#" class="btn btn-icon" title="{lng p="shareleave"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params="action=leaveshare&id={$calID}"}', '{lng p="shareleave"|escape:'javascript'}', 480, 260);"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		</div>
	</div>
{/foreach}
</div>
{/if}

{elseif $pageContent|substr:0:25 == 'li/organizer.addressbook.'}
<div class="sidebarHeading bm-organizer-sidebar-heading">
	<span>{lng p="myaddressbooks"}</span>
	<a href="#" class="bm-organizer-sidebar-heading-action" title="{lng p="addaddressbook"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params='action=books&do=addForm'}', '{lng p="addaddressbook"|escape:'javascript'}', 420, 190);"><i class="fa fa-plus" aria-hidden="true"></i></a>
</div>
<div class="contentMenuIcons bm-organizer-sidebar-books">
{foreach from=$addressbooks key=bookID item=abook}
	<div class="bm-organizer-sidebar-book{if $currentAddressbookID==$bookID} current{/if}">
		<a href="{sessionurl file='organizer.addressbook.php' params="addressbook={$bookID}"}"><i class="fa fa-book" aria-hidden="true"></i> {text value=$abook.title cut=22}</a>
		<div class="btn-group btn-group-sm bm-organizer-sidebar-cal-actions" role="group" aria-label="{lng p="actions"}">
			<a href="#" class="btn btn-icon" title="{lng p="edit"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params="action=books&do=edit&id={$bookID}"}', '{lng p="editaddressbook"|escape:'javascript'}', 420, 190);"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			{if $canShareAddressbook}
			<a href="#" class="btn btn-icon" title="{lng p="addressbookshare"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params="action=share&id={$bookID}"}', '{lng p="addressbookshare"|escape:'javascript'}', 520, 360);"><i class="fa fa-share" aria-hidden="true"></i></a>
			{/if}
			{if $abook.is_default}
			<span class="btn btn-icon disabled" title="{lng p="nodeletdefault"}"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
			{else}
			<a href="#" class="btn btn-icon" title="{lng p="delete"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params="action=books&do=deleteForm&id={$bookID}"}', '{lng p="delete"|escape:'javascript'}', 400, 150);"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
			{/if}
		</div>
	</div>
{/foreach}
</div>
{if isset($sharedAddressbooks) && $sharedAddressbooks}
<div class="sidebarHeading bm-organizer-sidebar-heading">
	<span>{lng p="sharedaddressbooks"}</span>
</div>
<div class="contentMenuIcons bm-organizer-sidebar-books">
{foreach from=$sharedAddressbooks key=bookID item=abook}
	<div class="bm-organizer-sidebar-book{if $currentAddressbookID==$bookID} current{/if}">
		<a href="{sessionurl file='organizer.addressbook.php' params="addressbook={$bookID}"}" title="{text value=$abook.owner_email}"><i class="fa fa-book" aria-hidden="true"></i> {text value=$abook.title cut=18}</a>
		<div class="btn-group btn-group-sm bm-organizer-sidebar-cal-actions" role="group" aria-label="{lng p="actions"}">
			<span class="btn btn-icon disabled" title="{lng p="edit"}"><i class="fa fa-pencil" aria-hidden="true"></i></span>
			{if $canShareAddressbook}
			<span class="btn btn-icon disabled" title="{lng p="addressbookshare"}"><i class="fa fa-share" aria-hidden="true"></i></span>
			{/if}
			<a href="#" class="btn btn-icon" title="{lng p="shareleave"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params="action=leaveshare&id={$bookID}"}', '{lng p="shareleave"|escape:'javascript'}', 480, 260);"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		</div>
	</div>
{/foreach}
</div>
{/if}

{elseif $pageContent|substr:0:18 == 'li/organizer.todo.'}
<div class="sidebarHeading">{lng p="todolist"}</div>
<div class="contentMenuIcons">
	<a href="{sessionurl file='organizer.todo.php' params='action=addTask'}"><i class="fa fa-plus" aria-hidden="true"></i> {lng p="addtask"}</a><br />
</div>
{if $tasks}
<div class="sidebarHeading">{lng p="tasks"}</div>
<div class="contentMenuIcons">
{foreach from=$tasks key=taskID item=task}
	<input type="checkbox" id="sbTask_{$taskID}" onclick="setTaskDone('{$sid}', {$taskID}, this.checked);"{if $task.akt_status==64} checked="checked"{/if} />
	<a href="{sessionurl file='organizer.todo.php' params="action=editTask&id={$taskID}"}">{text value=$task.titel cut=20}</a><br />
{/foreach}
{if $tasks_haveMore}
	<small><a href="{sessionurl file='organizer.todo.php'}">{lng p="more"}...</a></small><br />
{/if}
</div>
{/if}

{else}
<div class="sidebarHeading">{lng p="notes"}</div>
<div class="contentMenuIcons">
	<a href="{sessionurl file='organizer.notes.php' params='action=addNote'}"><i class="fa fa-plus" aria-hidden="true"></i> {lng p="addnote"}</a><br />
</div>
{/if}
