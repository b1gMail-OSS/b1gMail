<div id="contentHeader">
	<div class="left">
		<i class="fa fa-tasks" aria-hidden="true"></i>
		{lng p="todolist"}
	</div>
</div>
	
<div class="scrollContainer" style="overflow:hidden;">
	<div class="taskLists">
		<div class="taskContainer withBottomBar" style="overflow:auto;" id="taskListsScrollContainer">
			<table class="bigTable">
				<tr>
					<th>{lng p="tasklists"}</th>
				</tr>
			</table>
		
			<div id="taskListsContainer">
				{foreach from=$taskLists item=taskList}
				<a href="#" class="taskList{if $taskList.tasklistid==$taskListID} selected{/if}" onclick="selectTaskList({$taskList.tasklistid})" id="taskList_{$taskList.tasklistid}">
					{text value=$taskList.title}
					{if $taskList.tasklistid!=0}<img src="{$tpldir}images/li/delcross.png" onclick="deleteTaskList({$taskList.tasklistid})" />{/if}
				</a>
				{/foreach}
			</div>
		</div>
		
		<div class="contentFooter">
			<div class="left">
				<i class="fa fa-plus-square"></i>
				<input type="text" id="addListTitle" class="smallInput" style="width:120px;" onkeypress="return todoListInputKeyPress(event);" />
			</div>
			<div class="right">
				<input type="button" class="smallInput" value=" {lng p="ok"} " onclick="addTodoList();" />
			</div>
		</div>
	</div>
	
	<div class="taskContents" id="taskListContainer">
		{include file="li/organizer.todo.list.tpl"}
	</div>
</div>

<img src="{$tpldir}images/li/drag_task.png" style="display:none;" /><img src="{$tpldir}images/li/drag_tasks.png" style="display:none;" />
