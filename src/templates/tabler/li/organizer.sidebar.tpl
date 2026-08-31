{hook id="organizer.sidebar.tpl:head"}

<div class="sidebarHeading">{lng p="organizer"}</div>
<div class="contentMenuIcons">
	<a href="{sessionurl file='organizer.php'}"{if $pageContent=='li/organizer.start.tpl' || $pageContent=='li/organizer.customize.tpl'} class="active"{/if}>{include file="li/icon.tpl" faIcon="fa-tachometer"} {lng p="overview"}</a>
	<a href="{sessionurl file='organizer.calendar.php'}"{if $pageContent|substr:0:22 == 'li/organizer.calendar.'} class="active"{/if}>{include file="li/icon.tpl" faIcon="fa-calendar"} {lng p="calendar"}</a>
	<a href="{sessionurl file='organizer.todo.php'}"{if $pageContent|substr:0:18 == 'li/organizer.todo.'} class="active"{/if}>{include file="li/icon.tpl" faIcon="fa-tasks"} {lng p="todolist"}</a>
	<a href="{sessionurl file='organizer.addressbook.php'}"{if $pageContent|substr:0:25 == 'li/organizer.addressbook.'} class="active"{/if}>{include file="li/icon.tpl" faIcon="fa-address-book-o"} {lng p="addressbook"}</a>
	<a href="{sessionurl file='organizer.notes.php'}"{if $pageContent|substr:0:19 == 'li/organizer.notes.'} class="active"{/if}>{include file="li/icon.tpl" faIcon="fa-sticky-note-o"} {lng p="notes"}</a>
</div>

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

<div class="sidebarHeading">{lng p="calendar"}</div>
<div class="bm-organizer-minical">
	{miniCalendar}
</div>

{hook id="organizer.sidebar.tpl:foot"}
