<div class="taskContainer withBottomBar taskList" style="overflow-y:scroll;overflow-x:auto;">
		
	<table class="bigTable" id="tasksTable">
	<tr style="height: auto;">
		<th width="32">&nbsp;</th>
		<th width="16">&nbsp;</th>
		<th>
			{lng p="title"}				
		</th>
		<th width="120">
			{lng p="due"}
		</th>
		<th width="100">
			{lng p="done"}
		</th>
		<th class="listTableHead" width="55">&nbsp;</th>
	</tr>
	
	<tr style="height:auto;">
		<td colspan="6" class="folderGroup">
			<a style="display:block;cursor:pointer;" onclick="toggleGroup(0,'todo0');">&nbsp;<img id="groupImage_0" src="{$tpldir}images/{if $smarty.cookies.toggleGroup.todo0=='closed'}expand{else}contract{/if}.png" width="11" height="11" border="0" align="absmiddle" alt="" />
			&nbsp;{lng p="undonetasks"}</a>
		</td>
	</tr>
	
	<tbody id="group_0" style="display:{if $smarty.cookies.toggleGroup.todo0=='closed'}none{/if};">

	{foreach from=$todoList key=taskID item=task}{if $task.akt_status!=64}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr id="task_{$taskID}">
		<td class="{$class} taskCheckBox" nowrap="nowrap">
			<input type="checkbox" name="task_{$taskID}"
			 	onchange="setTaskDone('', {$taskID}, this.checked);" />
		</td>
		<td class="{$class}" nowrap="nowrap">
			{if $task.priority==1}<i class="fa fa-exclamation" aria-hidden="true"></i>{/if}
		</td>
		<td class="{$class}" nowrap="nowrap">
			{text value=$task.titel}
		</td>
		<td class="{$class}" nowrap="nowrap">
			{date timestamp=$task.faellig nice=true}
		</td>
		<td class="{$class}" nowrap="nowrap" align="center"><center>{progressBar width=80 value=$task.erledigt max=100}</center></td>
		<td class="{$class}" nowrap="nowrap">
			<a href="organizer.todo.php?action=editTask&id={$taskID}&sid={$sid}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			<a onclick="return confirm('{lng p="realdel"}');" href="organizer.todo.php?action=deleteTask&taskListID={$taskListID}&id={$taskID}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		</td>
	</tr>
	{else}{assign value=true var=haveDoneTasks}{/if}{/foreach}
	
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr id="newTask">
		<td class="{$class} taskCheckBox">
			<i class="fa fa-plus" aria-hidden="true"></i>
		</td>
		<td class="{$class}">&nbsp;</td>
		<td class="{$class}">
			<input type="text" id="newTaskText" onkeypress="return newTaskKeyPress(event);" onfocus="_tasksSel.unselectAll();" />
		</td>
		<td class="{$class}">&nbsp;</td>
		<td class="{$class}">&nbsp;</td>
		<td class="{$class}">
			<input type="button" class="smallInput" value=" {lng p="ok"} " onclick="addTask()" />
		</td>
	</tr>
	
	</tbody>
	
	{if $haveDoneTasks}
	<tr style="height:auto;">
		<td colspan="6" class="folderGroup">
			<a style="display:block;cursor:pointer;" onclick="toggleGroup(1,'todo1');">&nbsp;<img id="groupImage_1" src="{$tpldir}images/{if $smarty.cookies.toggleGroup.todo1=='closed'}expand{else}contract{/if}.png" width="11" height="11" border="0" align="absmiddle" alt="" />
			&nbsp;{lng p="donetasks"}</a>
		</td>
	</tr>
	
	<tbody id="group_1" style="display:{if $smarty.cookies.toggleGroup.todo1=='closed'}none{/if};">
		
	{foreach from=$todoList key=taskID item=task}
	{if $task.akt_status==64}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr id="task_{$taskID}" class="done">
		<td class="{$class} taskCheckBox" nowrap="nowrap">
			<input type="checkbox" name="task_{$taskID}" checked="checked"
			 	onchange="setTaskDone('', {$taskID}, this.checked);" />
		</td>
		<td class="{$class}" nowrap="nowrap">
			{if $task.priority==1}<i class="fa fa-exclamation" aria-hidden="true"></i>{/if}
		</td>
		<td class="{$class}" nowrap="nowrap">
			{text value=$task.titel}
		</td>
		<td class="{$class}" nowrap="nowrap">
			{date timestamp=$task.faellig nice=true}
		</td>
		<td class="{$class}" nowrap="nowrap" align="center"><center>{progressBar width=80 value=$task.erledigt max=100}</center></td>
		<td class="{$class}" nowrap="nowrap">
			<a href="organizer.todo.php?action=editTask&id={$taskID}&sid={$sid}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			<a onclick="return confirm('{lng p="realdel"}');" href="organizer.todo.php?action=deleteTask&taskListID={$taskListID}&id={$taskID}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		</td>
	</tr>
	{/if}
	{/foreach}
	</tbody>
	
	{/if}

	</table>
	
</div>

<div class="contentFooter">
<div class="left">
	<form name="f1" method="post" action="organizer.todo.php?action=action&sid={$sid}" onsubmit="transferSelectedTasks()">
	<input type="hidden" name="taskListID" value="{$taskListID}" />
	<input type="hidden" name="taskIDs" id="taskIDs" value="" />
	
	<select class="smallInput" name="do">
		<option value="-">------ {lng p="selaction"} ------</option>
		<option value="markasdone">{lng p="markasdone"}</option>
		<option value="delete">{lng p="delete"}</option>
	</select>
	<input class="smallInput" type="submit" value="{lng p="ok"}" />
	
	</form>
</div>
<div class="right">
	<button type="button" class="primary" onclick="document.location.href='organizer.todo.php?action=addTask&taskListID={$taskListID}&sid={$sid}';">
		<i class="fa fa-plus-circle"></i>
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
