<div class="bm-organizer-page bm-organizer-todo">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-list-check icon icon-sm" aria-hidden="true"></i>
			{lng p="todolist"}
		</div>
	</div>

	<div class="scrollContainer bm-organizer-split">
		<div class="taskLists bm-organizer-tasklists">
			<div class="taskContainer withBottomBar bm-organizer-tasklists-scroll" id="taskListsScrollContainer">
				<div class="bm-organizer-tasklists-head px-3 py-2 border-bottom">
					<strong class="small text-secondary text-uppercase">{lng p="tasklists"}</strong>
				</div>

				<div id="taskListsContainer" class="bm-organizer-tasklists-items" data-can-share="{if $canShareTodo}1{else}0{/if}" data-share-title="{lng p="sharetodolist"|escape:'html'}" data-edit-title="{lng p="edittasklist"|escape:'html'}" data-leave-title="{lng p="shareleave"|escape:'html'}" data-delete-title="{lng p="delete"|escape:'html'}" data-actions-label="{lng p="actions"|escape:'html'}" data-edit-icon="ti ti-pencil icon" data-share-icon="ti ti-share icon" data-trash-icon="ti ti-trash icon">
					{foreach from=$taskLists item=taskList}
					<div class="taskList{if $taskList.tasklistid==$taskListID} selected{/if}" id="taskList_{$taskList.tasklistid}" onclick="selectTaskList({$taskList.tasklistid});">
						<a href="#" class="bm-organizer-tasklist-title" onclick="selectTaskList({$taskList.tasklistid}); return false;">{text value=$taskList.title}</a>
						<div class="btn-group btn-group-sm bm-organizer-tasklist-actions" role="group" aria-label="{lng p="actions"}" onclick="event.stopPropagation();">
							{if !empty($taskList.can_edit)}
							<a href="#" class="btn btn-icon" title="{lng p="edittasklist"}" aria-label="{lng p="edittasklist"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.todo.php' params="action=editList&id={$taskList.tasklistid}"}', '{lng p="edittasklist"|escape:'javascript'}', 440, 220);"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
							{else}
							<span class="btn btn-icon disabled" aria-disabled="true" title="{lng p="edit"}"><i class="ti ti-pencil icon" aria-hidden="true"></i></span>
							{/if}
							{if $canShareTodo}
							{if $taskList.can_share}
							<a href="#" class="btn btn-icon" title="{lng p="sharetodolist"}" aria-label="{lng p="sharetodolist"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.todo.php' params="action=share&id={$taskList.tasklistid}"}', '{lng p="sharetodolist"|escape:'javascript'}', 520, 360);"><i class="ti ti-share icon" aria-hidden="true"></i></a>
							{else}
							<span class="btn btn-icon disabled" aria-disabled="true" title="{lng p="sharetodolist"}"><i class="ti ti-share icon" aria-hidden="true"></i></span>
							{/if}
							{/if}
							{if $taskList.can_delete}
							<a href="#" class="btn btn-icon" title="{lng p="delete"}" aria-label="{lng p="delete"}" onclick="deleteTaskList({$taskList.tasklistid}); return false;"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
							{elseif !empty($taskList.can_leave)}
							<a href="#" class="btn btn-icon" title="{lng p="shareleave"}" aria-label="{lng p="shareleave"}" onclick="return organizerOpenOverlay('{sessionurl file='organizer.todo.php' params="action=leaveshare&id={$taskList.tasklistid}"}', '{lng p="shareleave"|escape:'javascript'}', 480, 260);"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
							{else}
							<span class="btn btn-icon disabled" aria-disabled="true" title="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></span>
							{/if}
						</div>
					</div>
					{/foreach}
				</div>
			</div>

			<div class="contentFooter bm-organizer-footer bm-organizer-tasklists-footer">
				<div class="left bm-organizer-footer-actions">
					<div class="input-group input-group-sm bm-organizer-action-group">
						<span class="input-group-text"><i class="ti ti-plus icon icon-sm" aria-hidden="true"></i></span>
						<input type="text" id="addListTitle" class="form-control" onkeypress="return todoListInputKeyPress(event);" placeholder="{lng p="tasklists"}" aria-label="{lng p="tasklists"}" />
						<button type="button" class="btn btn-primary" onclick="addTodoList();">{lng p="ok"}</button>
					</div>
				</div>
			</div>
		</div>

		<div class="taskContents bm-organizer-taskcontents" id="taskListContainer">
			{include file="li/organizer.todo.list.tpl"}
		</div>
	</div>
</div>

<img src="{$tpldir}images/li/drag_task.png" style="display:none;" alt="" /><img src="{$tpldir}images/li/drag_tasks.png" style="display:none;" alt="" />
