{include file=$sched_nav_tpl}

<form method="post" action="{sessionurl file=$sched_admin_script params="action={$sched_admin_action}&do=task"}" onsubmit="spin(this)">
	{csrffield}
	<input type="hidden" name="id" value="{if !empty($sched_data.taskid)}{$sched_data.taskid}{elseif isset($smarty.post.id)}{$smarty.post.id|default:''}{/if}" />
	<input type="hidden" name="next" value="1" id="sched_next_submit" disabled="disabled" />

	<fieldset class="mb-4">
		<legend class="h5 text-secondary mb-3">{lng p="sched.task_label"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-3 col-form-label">{lng p="sched.enabled"}</label>
			<div class="col-sm-9">
				<label class="form-check form-switch">
					<input class="form-check-input" type="checkbox" name="active" value="1"{if empty($sched_data) || !empty($sched_data.active)} checked="checked"{/if} />
				</label>
			</div>
		</div>

		<div class="mb-3 row">
			<label class="col-sm-3 col-form-label">{lng p="sched.logging"}</label>
			<div class="col-sm-9">
				<label class="form-check form-switch">
					<input class="form-check-input" type="checkbox" name="log" value="1"{if empty($sched_data) || !empty($sched_data.log)} checked="checked"{/if} />
				</label>
			</div>
		</div>

		<div class="mb-3 row">
			<label class="col-sm-3 col-form-label" for="sched_task">{lng p="sched.task_label"}</label>
			<div class="col-sm-9">
				{if !empty($sched_data.task)}<input type="hidden" name="task" value="{$sched_data.task}" />{/if}
				<select class="form-select" id="sched_task" name="task" data-sched-task-select="1"{if !empty($sched_data.task)} disabled="disabled"{/if}>
					<option value="">--</option>
					{foreach from=$sched_tasks item=taskLabel key=key}
						<option value="{$key}"{if !empty($sched_data.task) && $sched_data.task == $key} selected="selected"{/if}>{$taskLabel}</option>
					{/foreach}
				</select>
			</div>
		</div>
	</fieldset>

	{if empty($sched_data.task)}
		<div class="text-end mb-4">
			<button type="submit" class="btn btn-primary" id="sched_button_next" name="next" value="1">
				{lng p="next"} <i class="ti ti-arrow-right ms-1"></i>
			</button>
		</div>
	{else}
		{if $sched_task_data}
			<fieldset class="mb-4">
				<legend class="h5 text-secondary mb-3">{lng p="sched.task_params"}</legend>
				{include file=$sched_task_data}
			</fieldset>
		{/if}

		{include file=$sched_schedule_tpl}

		<div class="text-end">
			<button type="submit" class="btn btn-primary" name="save" value="1">
				<i class="ti ti-device-floppy me-1"></i> {lng p="save"}
			</button>
		</div>
	{/if}
</form>
