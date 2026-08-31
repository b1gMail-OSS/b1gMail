{include file=$tccrn_task_data_user}

<div class="mb-3 row">
	<label class="col-sm-3 col-form-label" for="tccrn_move_group">{lng p="movetogroup"}</label>
	<div class="col-sm-9">
		<select class="form-select" name="taskdata[moveGroup]" id="tccrn_move_group">
			{foreach from=$groups item=groupItem}
				<option value="{$groupItem.id}"{if isset($tccrn_data.taskdata.moveGroup) && $groupItem.id == $tccrn_data.taskdata.moveGroup} selected="selected"{/if}>{text value=$groupItem.title}</option>
			{/foreach}
		</select>
	</div>
</div>
