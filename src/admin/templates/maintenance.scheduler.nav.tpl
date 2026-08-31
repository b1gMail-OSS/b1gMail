{assign var="sched_do" value=$scheduler_do|default:'start'}

<div class="scheduler-toolbar d-flex flex-wrap align-items-center gap-2 mb-4">
	{if $sched_do == 'start'}
		<div class="text-secondary small flex-fill">{lng p="sched.toolbar_hint"}</div>
		<div class="btn-list flex-nowrap">
			<a href="{sessionurl file=$tccrn_admin_script params="action={$tccrn_admin_action}&do=task"}" class="btn btn-primary">
				<i class="ti ti-plus icon me-1"></i>{lng p="sched.new_task"}
			</a>
			<a href="{sessionurl file=$tccrn_admin_script params="action={$tccrn_admin_action}&do=settings"}" class="btn btn-ghost-secondary" title="{lng p="prefs"}">
				<i class="ti ti-settings icon"></i>
			</a>
		</div>
	{else}
		<a href="{sessionurl file=$tccrn_admin_script params="action={$tccrn_admin_action}&do=start"}" class="btn btn-ghost-secondary">
			<i class="ti ti-arrow-left icon me-1"></i>{lng p="overview"}
		</a>
		<div class="h4 mb-0 flex-fill">
			{if $sched_do == 'settings'}
				{lng p="prefs"}
			{elseif !empty($tccrn_data.taskid)}
				{lng p="edit"}: {lng p="sched.task_label"}
			{else}
				{lng p="sched.new_task"}
			{/if}
		</div>
	{/if}
</div>
