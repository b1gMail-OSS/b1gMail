{include file=$sched_task_data_user}

<div class="mb-3 row">
	<label class="col-sm-3 col-form-label" for="sched_move_group">{lng p="movetogroup"}</label>
	<div class="col-sm-9">
		<select class="form-select" name="taskdata[moveGroup]" id="sched_move_group">
			{foreach from=$groups item=groupItem}
				<option value="{$groupItem.id}"{if isset($sched_data.taskdata.moveGroup) && $groupItem.id == $sched_data.taskdata.moveGroup} selected="selected"{/if}>{text value=$groupItem.title}</option>
			{/foreach}
		</select>
	</div>
</div>
