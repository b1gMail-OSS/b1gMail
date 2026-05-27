{hook id="organizer.sidebar.tpl:head"}

<div class="sidebarHeading">{lng p="organizer"}</div>
<div class="contentMenuIcons">
	<a href="organizer.php?sid={$sid}"{if $pageContent=='li/organizer.start.tpl' || $pageContent=='li/organizer.customize.tpl'} class="active"{/if}>{include file="li/icon.tpl" faIcon="fa-tachometer"} {lng p="overview"}</a>
	<a href="organizer.calendar.php?sid={$sid}"{if $pageContent|substr:0:22 == 'li/organizer.calendar.'} class="active"{/if}>{include file="li/icon.tpl" faIcon="fa-calendar"} {lng p="calendar"}</a>
	<a href="organizer.todo.php?sid={$sid}"{if $pageContent|substr:0:18 == 'li/organizer.todo.'} class="active"{/if}>{include file="li/icon.tpl" faIcon="fa-tasks"} {lng p="todolist"}</a>
	<a href="organizer.addressbook.php?sid={$sid}"{if $pageContent|substr:0:25 == 'li/organizer.addressbook.'} class="active"{/if}>{include file="li/icon.tpl" faIcon="fa-address-book-o"} {lng p="addressbook"}</a>
	<a href="organizer.notes.php?sid={$sid}"{if $pageContent|substr:0:19 == 'li/organizer.notes.'} class="active"{/if}>{include file="li/icon.tpl" faIcon="fa-sticky-note-o"} {lng p="notes"}</a>
</div>

<div class="sidebarHeading">{lng p="tasks"}</div>
<div class="contentMenuIcons bm-organizer-sidebar-tasks">
{foreach from=$tasks key=taskID item=task}
	<div class="bm-organizer-sidebar-task">
		<label class="form-check mb-0">
			<input type="checkbox" class="form-check-input m-0" id="sbTask_{$taskID}" onclick="setTaskDone('{$sid}', {$taskID}, this.checked);"{if $task.akt_status==64} checked="checked"{/if} />
		</label>
		<a href="organizer.todo.php?action=editTask&id={$taskID}&sid={$sid}">{text value=$task.titel cut=20}</a>
	</div>
{/foreach}
{if $tasks_haveMore}
	<small><a href="organizer.todo.php?sid={$sid}">{lng p="more"}...</a></small>
{/if}
</div>

<div class="sidebarHeading">{lng p="calendar"}</div>
<div class="bm-organizer-minical">
	{miniCalendar}
</div>

{hook id="organizer.sidebar.tpl:foot"}
