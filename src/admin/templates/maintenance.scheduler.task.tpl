{include file=$tccrn_nav_tpl}

<form method="post" action="{sessionurl file=$tccrn_admin_script params="action={$tccrn_admin_action}&do=task"}" onsubmit="spin(this)">
	{csrffield}
	<input type="hidden" name="id" value="{if !empty($tccrn_data.taskid)}{$tccrn_data.taskid}{elseif isset($smarty.post.id)}{$smarty.post.id|default:''}{/if}" />
	<input type="hidden" name="next" value="1" id="tccrn_next_submit" disabled="disabled" />

	<fieldset class="mb-4">
		<legend class="h5 text-secondary mb-3">{lng p="sched.task_label"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-3 col-form-label">{lng p="sched.enabled"}</label>
			<div class="col-sm-9">
				<label class="form-check form-switch">
					<input class="form-check-input" type="checkbox" name="active" value="1"{if empty($tccrn_data) || !empty($tccrn_data.active)} checked="checked"{/if} />
				</label>
			</div>
		</div>

		<div class="mb-3 row">
			<label class="col-sm-3 col-form-label">{lng p="sched.logging"}</label>
			<div class="col-sm-9">
				<label class="form-check form-switch">
					<input class="form-check-input" type="checkbox" name="log" value="1"{if empty($tccrn_data) || !empty($tccrn_data.log)} checked="checked"{/if} />
				</label>
			</div>
		</div>

		<div class="mb-3 row">
			<label class="col-sm-3 col-form-label" for="tccrn_task">{lng p="sched.task_label"}</label>
			<div class="col-sm-9">
				{if !empty($tccrn_data.task)}<input type="hidden" name="task" value="{$tccrn_data.task}" />{/if}
				<select class="form-select" id="tccrn_task" name="task" data-tccrn-task-select="1"{if !empty($tccrn_data.task)} disabled="disabled"{/if}>
					<option value="">--</option>
					{foreach from=$tccrn_tasks item=taskLabel key=key}
						<option value="{$key}"{if !empty($tccrn_data.task) && $tccrn_data.task == $key} selected="selected"{/if}>{$taskLabel}</option>
					{/foreach}
				</select>
			</div>
		</div>
	</fieldset>

	{if empty($tccrn_data.task)}
		<div class="text-end mb-4">
			<button type="submit" class="btn btn-primary" id="tccrn_button_next" name="next" value="1">
				{lng p="next"} <i class="ti ti-arrow-right ms-1"></i>
			</button>
		</div>
	{else}
		{if $tccrn_task_data}
			<fieldset class="mb-4">
				<legend class="h5 text-secondary mb-3">{lng p="sched.task_params"}</legend>
				{include file=$tccrn_task_data}
			</fieldset>
		{/if}

		{include file=$tccrn_schedule_tpl}

		<div class="text-end">
			<button type="submit" class="btn btn-primary" name="save" value="1">
				<i class="ti ti-device-floppy me-1"></i> {lng p="save"}
			</button>
		</div>
	{/if}
</form>
