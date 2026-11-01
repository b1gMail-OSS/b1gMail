{hook id="organizer.sidebar.tpl:head"}

{if $pageContent|substr:0:22 == 'li/organizer.calendar.'}
<div class="bm-organizer-minical bm-organizer-minical-plain">
	{miniCalendar}
</div>
<div class="sidebarHeading bm-organizer-sidebar-heading">
	<span>{lng p="mycalendars"}</span>
	<a href="#" class="bm-organizer-sidebar-heading-action" title="{lng p="addcalendar"}" aria-label="{lng p="addcalendar"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params='action=calendars&do=addForm'}', '{lng p="addcalendar"|escape:'javascript'}', 440, 260);">{include file="li/icon.tpl" faIcon="fa-plus"}</a>
</div>
<div class="contentMenuIcons bm-organizer-sidebar-cals">
{foreach from=$calendars key=calID item=cal}
	<div class="bm-organizer-sidebar-cal bm-organizer-cal-{$cal.color}{if $currentCalendarID==$calID} current{/if}">
		<label class="form-check mb-0">
			<input type="checkbox" class="form-check-input m-0 visibleCalBox" value="{$calID}"{if !isset($visibleCalendarIDs) || in_array($calID, $visibleCalendarIDs)} checked="checked"{/if} onclick="updateVisibleCalendars('{if isset($theDate)}{$theDate}{/if}')" />
		</label>
		<span class="bm-organizer-cal-swatch" aria-hidden="true"></span>
		<a href="{sessionurl file='organizer.calendar.php' params="calendar={$calID}"}">{text value=$cal.title cut=22}</a>
		<div class="btn-group btn-group-sm bm-organizer-sidebar-cal-actions" role="group" aria-label="{lng p="actions"}">
			<a href="#" class="btn btn-icon" title="{lng p="edit"}" aria-label="{lng p="edit"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params="action=calendars&do=edit&id={$calID}"}', '{lng p="editcalendar"|escape:'javascript'}', 440, 260);">{include file="li/icon.tpl" faIcon="fa-pencil"}</a>
			{if $canShareCalendar}
			<a href="#" class="btn btn-icon" title="{lng p="calendarshare"}" aria-label="{lng p="calendarshare"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params="action=share&id={$calID}"}', '{lng p="calendarshare"|escape:'javascript'}', 640, 480);">{include file="li/icon.tpl" faIcon="fa-share"}</a>
			{/if}
			{if $cal.is_default}
			<span class="btn btn-icon disabled" title="{lng p="nodeletdefault"}" aria-label="{lng p="nodeletdefault"}" aria-disabled="true">{include file="li/icon.tpl" faIcon="fa-trash-o"}</span>
			{else}
			<a href="#" class="btn btn-icon" title="{lng p="delete"}" aria-label="{lng p="delete"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params="action=calendars&do=deleteForm&id={$calID}"}', '{lng p="delete"|escape:'javascript'}', 400, 150);">{include file="li/icon.tpl" faIcon="fa-trash-o"}</a>
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
		<label class="form-check mb-0">
			<input type="checkbox" class="form-check-input m-0 visibleCalBox" value="{$calID}"{if !isset($visibleCalendarIDs) || in_array($calID, $visibleCalendarIDs)} checked="checked"{/if} onclick="updateVisibleCalendars('{if isset($theDate)}{$theDate}{/if}')" />
		</label>
		<span class="bm-organizer-cal-swatch" aria-hidden="true"></span>
		<a href="{sessionurl file='organizer.calendar.php' params="calendar={$calID}"}" title="{text value=$cal.owner_email}">{text value=$cal.title cut=18}</a>
		<div class="btn-group btn-group-sm bm-organizer-sidebar-cal-actions" role="group" aria-label="{lng p="actions"}">
			<span class="btn btn-icon disabled" aria-disabled="true" title="{lng p="edit"}">{include file="li/icon.tpl" faIcon="fa-pencil"}</span>
			{if $canShareCalendar}
			<span class="btn btn-icon disabled" aria-disabled="true" title="{lng p="calendarshare"}">{include file="li/icon.tpl" faIcon="fa-share"}</span>
			{/if}
			<a href="#" class="btn btn-icon" title="{lng p="shareleave"}" aria-label="{lng p="shareleave"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.calendar.php' params="action=leaveshare&id={$calID}"}', '{lng p="shareleave"|escape:'javascript'}', 480, 260);">{include file="li/icon.tpl" faIcon="fa-trash-o"}</a>
		</div>
	</div>
{/foreach}
</div>
{/if}

{elseif $pageContent|substr:0:25 == 'li/organizer.addressbook.'}
<div class="sidebarHeading bm-organizer-sidebar-heading">
	<span>{lng p="myaddressbooks"}</span>
	<a href="#" class="bm-organizer-sidebar-heading-action" title="{lng p="addaddressbook"}" aria-label="{lng p="addaddressbook"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params='action=books&do=addForm'}', '{lng p="addaddressbook"|escape:'javascript'}', 420, 190);">{include file="li/icon.tpl" faIcon="fa-plus"}</a>
</div>
<div class="contentMenuIcons bm-organizer-sidebar-books">
{foreach from=$addressbooks key=bookID item=abook}
	<div class="bm-organizer-sidebar-book{if $currentAddressbookID==$bookID} current{/if}">
		<a href="{sessionurl file='organizer.addressbook.php' params="addressbook={$bookID}"}">{include file="li/icon.tpl" faIcon="fa-book"} {text value=$abook.title cut=22}</a>
		<div class="btn-group btn-group-sm bm-organizer-sidebar-cal-actions" role="group" aria-label="{lng p="actions"}">
			<a href="#" class="btn btn-icon" title="{lng p="edit"}" aria-label="{lng p="edit"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params="action=books&do=edit&id={$bookID}"}', '{lng p="editaddressbook"|escape:'javascript'}', 420, 190);">{include file="li/icon.tpl" faIcon="fa-pencil"}</a>
			{if $canShareAddressbook}
			<a href="#" class="btn btn-icon" title="{lng p="addressbookshare"}" aria-label="{lng p="addressbookshare"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params="action=share&id={$bookID}"}', '{lng p="addressbookshare"|escape:'javascript'}', 520, 360);">{include file="li/icon.tpl" faIcon="fa-share"}</a>
			{/if}
			{if $abook.is_default}
			<span class="btn btn-icon disabled" title="{lng p="nodeletdefault"}" aria-label="{lng p="nodeletdefault"}" aria-disabled="true">{include file="li/icon.tpl" faIcon="fa-trash-o"}</span>
			{else}
			<a href="#" class="btn btn-icon" title="{lng p="delete"}" aria-label="{lng p="delete"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params="action=books&do=deleteForm&id={$bookID}"}', '{lng p="delete"|escape:'javascript'}', 400, 150);">{include file="li/icon.tpl" faIcon="fa-trash-o"}</a>
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
		<a href="{sessionurl file='organizer.addressbook.php' params="addressbook={$bookID}"}" title="{text value=$abook.owner_email}">{include file="li/icon.tpl" faIcon="fa-book"} {text value=$abook.title cut=18}</a>
		<div class="btn-group btn-group-sm bm-organizer-sidebar-cal-actions" role="group" aria-label="{lng p="actions"}">
			<span class="btn btn-icon disabled" aria-disabled="true" title="{lng p="edit"}">{include file="li/icon.tpl" faIcon="fa-pencil"}</span>
			{if $canShareAddressbook}
			<span class="btn btn-icon disabled" aria-disabled="true" title="{lng p="addressbookshare"}">{include file="li/icon.tpl" faIcon="fa-share"}</span>
			{/if}
			<a href="#" class="btn btn-icon" title="{lng p="shareleave"}" aria-label="{lng p="shareleave"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params="action=leaveshare&id={$bookID}"}', '{lng p="shareleave"|escape:'javascript'}', 480, 260);">{include file="li/icon.tpl" faIcon="fa-trash-o"}</a>
		</div>
	</div>
{/foreach}
</div>
{/if}

{elseif $pageContent|substr:0:18 == 'li/organizer.todo.'}
<div class="sidebarHeading">{lng p="todolist"}</div>
<div class="contentMenuIcons">
	<a href="{sessionurl file='organizer.todo.php' params='action=addTask'}">{include file="li/icon.tpl" faIcon="fa-plus"} {lng p="addtask"}</a>
</div>
{if $tasks}
<div class="sidebarHeading">{lng p="tasks"}</div>
<div class="contentMenuIcons bm-organizer-sidebar-tasks">
{foreach from=$tasks key=taskID item=task}
	<div class="bm-organizer-sidebar-task">
		<label class="form-check mb-0">
			<input type="checkbox" class="form-check-input m-0" id="sbTask_{$taskID}" onclick="setTaskDone('{$sid}', {$taskID}, this.checked);"{if $task.akt_status==64} checked="checked"{/if} />
		</label>
		<a href="{sessionurl file='organizer.todo.php' params="action=editTask&id={$taskID}"}">{text value=$task.titel cut=20}</a>
	</div>
{/foreach}
{if $tasks_haveMore}
	<small><a href="{sessionurl file='organizer.todo.php'}">{lng p="more"}...</a></small>
{/if}
</div>
{/if}

{else}
<div class="sidebarHeading">{lng p="notes"}</div>
<div class="contentMenuIcons">
	<a href="{sessionurl file='organizer.notes.php' params='action=addNote'}">{include file="li/icon.tpl" faIcon="fa-plus"} {lng p="addnote"}</a>
</div>
{/if}

{hook id="organizer.sidebar.tpl:foot"}
