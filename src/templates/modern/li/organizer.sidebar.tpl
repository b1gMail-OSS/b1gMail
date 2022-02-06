<div class="sidebarHeading">{lng p="organizer"}</div>
<div class="contentMenuIcons">
	<a href="organizer.php?sid={$sid}"><i class="fa fa-tachometer" aria-hidden="true"></i> {lng p="overview"}</a><br />
	<a href="organizer.calendar.php?sid={$sid}"><i class="fa fa-calendar" aria-hidden="true"></i> {lng p="calendar"}</a><br />
	<a href="organizer.todo.php?sid={$sid}"><i class="fa fa-tasks" aria-hidden="true"></i> {lng p="todolist"}</a><br />
	<a href="organizer.addressbook.php?sid={$sid}"><i class="fa fa-address-book-o" aria-hidden="true"></i> {lng p="addressbook"}</a><br />
	<a href="organizer.notes.php?sid={$sid}"><i class="fa fa-sticky-note-o" aria-hidden="true"></i> {lng p="notes"}</a><br />
</div>

<div class="sidebarHeading">{lng p="tasks"}</div>
<div class="contentMenuIcons">
{foreach from=$tasks key=taskID item=task}
	<input type="checkbox" id="sbTask_{$taskID}" onclick="setTaskDone('{$sid}', {$taskID}, this.checked);"{if $task.akt_status==64} checked="checked"{/if} />
	<a href="organizer.todo.php?action=editTask&id={$taskID}&sid={$sid}">{text value=$task.titel cut=20}</a><br />
{/foreach}
{if $tasks_haveMore}
	<small><a href="organizer.todo.php?sid={$sid}">{lng p="more"}...</a></small><br />
{/if}
</div>

<div class="sidebarHeading">{lng p="calendar"}</div>
<center>
	<br />{miniCalendar}
</center>
