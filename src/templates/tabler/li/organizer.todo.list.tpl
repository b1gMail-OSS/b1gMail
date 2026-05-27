<div class="taskContainer withBottomBar taskList bm-organizer-task-table-wrap">
	<table class="table table-vcenter table-hover card-table bm-organizer-table" id="tasksTable">
	<thead>
	<tr>
		<th class="taskCheckBox bm-organizer-task-gutter">&nbsp;</th>
		<th class="bm-organizer-task-priority">&nbsp;</th>
		<th>{lng p="title"}</th>
		<th style="width:8.75rem;">{lng p="due"}</th>
		<th style="width:6.25rem;">{lng p="done"}</th>
		<th class="bm-organizer-task-col-actions" style="width:5.5rem;">&nbsp;</th>
	</tr>
	</thead>

	<tbody>
	<tr class="bm-organizer-section-row">
		<td class="taskCheckBox bm-organizer-task-gutter">
			<button type="button" class="bm-organizer-section-toggle bm-organizer-section-toggle-icon" onclick="toggleGroup(0,'todo0');" aria-label="{lng p="undonetasks"}">
				<i class="ti ti-chevron-{if isset($smarty.cookies.toggleGroup.todo0) && $smarty.cookies.toggleGroup.todo0=='closed'}right{else}down{/if} icon icon-sm" id="groupImage_0" aria-hidden="true"></i>
			</button>
		</td>
		<td class="bm-organizer-task-priority">&nbsp;</td>
		<td colspan="4">
			<button type="button" class="bm-organizer-section-toggle" onclick="toggleGroup(0,'todo0');">
				{lng p="undonetasks"}
			</button>
		</td>
	</tr>
	</tbody>

	<tbody id="group_0" style="display:{if isset($smarty.cookies.toggleGroup.todo0) && $smarty.cookies.toggleGroup.todo0=='closed'}none{/if};">

	{foreach from=$todoList key=taskID item=task}{if $task.akt_status!=64}
	<tr id="task_{$taskID}">
		<td class="taskCheckBox bm-organizer-task-gutter" nowrap="nowrap">
			<label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" name="task_{$taskID}" onchange="setTaskDone('', {$taskID}, this.checked);" /></label>
		</td>
		<td nowrap="nowrap" class="bm-organizer-task-priority">
			{if $task.priority==1}<i class="ti ti-alert-triangle icon icon-sm text-danger" aria-hidden="true"></i>{/if}
		</td>
		<td nowrap="nowrap">{text value=$task.titel}</td>
		<td nowrap="nowrap">{date timestamp=$task.faellig nice=true}</td>
		<td nowrap="nowrap" class="text-center">{progressBar width=80 value=$task.erledigt max=100}</td>
		<td nowrap="nowrap" class="text-end bm-organizer-task-col-actions">
			<div class="btn-group btn-group-sm bm-organizer-task-actions" role="group" aria-label="{lng p="actions"}">
				<a href="organizer.todo.php?action=editTask&id={$taskID}&sid={$sid}" class="btn btn-outline-secondary btn-icon" title="{lng p="edit"}" aria-label="{lng p="edit"}"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
				<a onclick="return confirm('{lng p="realdel"}');" href="organizer.todo.php?action=deleteTask&taskListID={$taskListID}&id={$taskID}&sid={$sid}" class="btn btn-outline-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
			</div>
		</td>
	</tr>
	{else}{assign value=true var=haveDoneTasks}{/if}{/foreach}

	<tr id="newTask" class="bm-organizer-new-row">
		<td class="taskCheckBox bm-organizer-task-gutter">
			<i class="ti ti-plus icon icon-sm text-secondary" aria-hidden="true"></i>
		</td>
		<td>&nbsp;</td>
		<td colspan="3">
			<input type="text" class="form-control form-control-sm" id="newTaskText" onkeypress="return newTaskKeyPress(event);" onfocus="_tasksSel.unselectAll();" placeholder="{lng p="addtask"}" />
		</td>
		<td class="text-end">
			<button type="button" class="btn btn-sm btn-primary" onclick="addTask();">{lng p="ok"}</button>
		</td>
	</tr>

	</tbody>

	{if isset($haveDoneTasks)}
	<tbody>
	<tr class="bm-organizer-section-row">
		<td class="taskCheckBox bm-organizer-task-gutter">
			<button type="button" class="bm-organizer-section-toggle bm-organizer-section-toggle-icon" onclick="toggleGroup(1,'todo1');" aria-label="{lng p="donetasks"}">
				<i class="ti ti-chevron-{if isset($smarty.cookies.toggleGroup.todo1) && $smarty.cookies.toggleGroup.todo1=='closed'}right{else}down{/if} icon icon-sm" id="groupImage_1" aria-hidden="true"></i>
			</button>
		</td>
		<td class="bm-organizer-task-priority">&nbsp;</td>
		<td colspan="4">
			<button type="button" class="bm-organizer-section-toggle" onclick="toggleGroup(1,'todo1');">
				{lng p="donetasks"}
			</button>
		</td>
	</tr>
	</tbody>

	<tbody id="group_1" style="display:{if isset($smarty.cookies.toggleGroup.todo1) && $smarty.cookies.toggleGroup.todo1=='closed'}none{/if};">

	{foreach from=$todoList key=taskID item=task}
	{if $task.akt_status==64}
	<tr id="task_{$taskID}" class="done">
		<td class="taskCheckBox bm-organizer-task-gutter" nowrap="nowrap">
			<label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" name="task_{$taskID}" checked="checked" onchange="setTaskDone('', {$taskID}, this.checked);" /></label>
		</td>
		<td nowrap="nowrap" class="bm-organizer-task-priority">
			{if $task.priority==1}<i class="ti ti-alert-triangle icon icon-sm text-danger" aria-hidden="true"></i>{/if}
		</td>
		<td nowrap="nowrap">{text value=$task.titel}</td>
		<td nowrap="nowrap">{date timestamp=$task.faellig nice=true}</td>
		<td nowrap="nowrap" class="text-center">{progressBar width=80 value=$task.erledigt max=100}</td>
		<td nowrap="nowrap" class="text-end bm-organizer-task-col-actions">
			<div class="btn-group btn-group-sm bm-organizer-task-actions" role="group" aria-label="{lng p="actions"}">
				<a href="organizer.todo.php?action=editTask&id={$taskID}&sid={$sid}" class="btn btn-outline-secondary btn-icon" title="{lng p="edit"}" aria-label="{lng p="edit"}"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
				<a onclick="return confirm('{lng p="realdel"}');" href="organizer.todo.php?action=deleteTask&taskListID={$taskListID}&id={$taskID}&sid={$sid}" class="btn btn-outline-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
			</div>
		</td>
	</tr>
	{/if}
	{/foreach}
	</tbody>

	{/if}

	</table>
</div>

<div id="contentFooter" class="contentFooter bm-organizer-footer">
	<div class="left bm-organizer-footer-actions">
		<form name="f1" method="post" action="organizer.todo.php?action=action&sid={$sid}" onsubmit="transferSelectedTasks()">
			<input type="hidden" name="taskListID" value="{$taskListID}" />
			<input type="hidden" name="taskIDs" id="taskIDs" value="" />

			<div class="input-group input-group-sm bm-organizer-action-group">
				<select class="form-select" name="do" aria-label="{lng p="selaction"}">
					<option value="-">{lng p="selaction"}</option>
					<option value="markasdone">{lng p="markasdone"}</option>
					<option value="delete">{lng p="delete"}</option>
				</select>
				<button class="btn btn-primary" type="submit">{lng p="ok"}</button>
			</div>
		</form>
	</div>
	<div class="right bm-organizer-footer-tools">
		<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='organizer.todo.php?action=addTask&taskListID={$taskListID}&sid={$sid}';">
			<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
			{lng p="addtask"}
		</button>
	</div>
</div>

<script>
<!--
	currentTaskListID = {$taskListID};
	initTasksSel();
	enableTodoDragTargets();
	EBID('newTaskText').focus();
//-->
</script>
