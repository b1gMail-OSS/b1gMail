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
		
			<div id="taskListsContainer" data-can-share="{if $canShareTodo}1{else}0{/if}" data-share-title="{lng p="sharetodolist"|escape:'html'}" data-leave-title="{lng p="shareleave"|escape:'html'}" data-share-icon="fa fa-share" data-trash-icon="fa fa-trash-o">
				{foreach from=$taskLists item=taskList}
				<div class="taskList{if $taskList.tasklistid==$taskListID} selected{/if}" id="taskList_{$taskList.tasklistid}" onclick="selectTaskList({$taskList.tasklistid});">
					<a href="#" class="bm-organizer-tasklist-title" onclick="selectTaskList({$taskList.tasklistid}); return false;">{text value=$taskList.title}</a>
					<span class="bm-organizer-tasklist-actions" onclick="event.stopPropagation();">
						{if $canShareTodo && $taskList.can_share}
						<a href="#" title="{lng p="sharetodolist"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.todo.php' params="action=share&id={$taskList.tasklistid}"}', '{lng p="sharetodolist"|escape:'javascript'}', 520, 360);"><i class="fa fa-share" aria-hidden="true"></i></a>
						{/if}
						{if $taskList.can_delete}
						<img src="{$tpldir}images/li/delcross.png" onclick="deleteTaskList({$taskList.tasklistid}); return false;" alt="" />
						{elseif !empty($taskList.can_leave)}
						<a href="#" title="{lng p="shareleave"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.todo.php' params="action=leaveshare&id={$taskList.tasklistid}"}', '{lng p="shareleave"|escape:'javascript'}', 480, 260);"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
						{/if}
					</span>
				</div>
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
