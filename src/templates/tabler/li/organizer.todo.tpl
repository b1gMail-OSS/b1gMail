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

				<div id="taskListsContainer" class="bm-organizer-tasklists-items">
					{foreach from=$taskLists item=taskList}
					<a href="#" class="taskList{if $taskList.tasklistid==$taskListID} selected{/if}" onclick="selectTaskList({$taskList.tasklistid}); return false;" id="taskList_{$taskList.tasklistid}">
						<span class="bm-organizer-tasklist-title">{text value=$taskList.title}</span>
						{if $taskList.tasklistid!=0}<img src="{$tpldir}images/li/delcross.png" onclick="deleteTaskList({$taskList.tasklistid}); return false;" alt="" />{/if}
					</a>
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
