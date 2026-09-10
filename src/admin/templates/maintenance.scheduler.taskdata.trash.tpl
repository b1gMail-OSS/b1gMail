<p class="text-secondary small mb-3">
	{if $sched_data.task == 'sched.tr_sp_delete'}
		{lng p="sched.spam_desc"}
	{else}
		{lng p="sched.trash_desc"}
	{/if}
</p>

<div class="mb-3">
	<label class="form-label">{lng p="whobelongtogrps"}</label>
	<div class="sched-group-grid" data-sched-group="groups">
		{foreach from=$groups item=group key=groupID}
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="taskdata[groups][]" value="{$groupID}" id="group_{$groupID}"{if !isset($sched_data.taskdata.groups) || !is_array($sched_data.taskdata.groups) || ($groupID|in_array:$sched_data.taskdata.groups)} checked="checked"{/if} />
				<span class="form-check-label">{text value=$group.title}</span>
			</label>
		{/foreach}
	</div>
	<div class="btn-list mt-2">
		<button type="button" class="btn btn-sm btn-ghost-secondary" data-sched-toggle="all" data-sched-target="groups">{lng p="sched.select_all"}</button>
		<button type="button" class="btn btn-sm btn-ghost-secondary" data-sched-toggle="none" data-sched-target="groups">{lng p="sched.select_none"}</button>
	</div>
</div>

<p class="text-secondary small mb-3">{lng p="trash_only"}</p>

<div class="mb-3 row">
	<label class="col-sm-3 col-form-label">{lng p="trash_daysonly"}</label>
	<div class="col-sm-9">
		<div class="d-flex flex-wrap align-items-center gap-2">
			<label class="form-check mb-0">
				<input class="form-check-input" type="checkbox" value="1" name="taskdata[daysOnly]" id="sched_trash_days"{if !is_array($sched_data.taskdata) || isset($sched_data.taskdata.daysOnly)} checked="checked"{/if} />
			</label>
			<div class="input-group" style="max-width:12rem;">
				<input class="form-control" type="number" min="0" name="taskdata[days]" value="{if !isset($sched_data.taskdata.days)}30{else}{$sched_data.taskdata.days}{/if}" />
				<span class="input-group-text">{lng p="days"}</span>
			</div>
		</div>
	</div>
</div>

<div class="mb-3 row">
	<label class="col-sm-3 col-form-label">{lng p="trash_sizesonly"}</label>
	<div class="col-sm-9">
		<div class="d-flex flex-wrap align-items-center gap-2">
			<label class="form-check mb-0">
				<input class="form-check-input" type="checkbox" value="1" name="taskdata[sizesOnly]" id="sched_trash_size"{if isset($sched_data.taskdata.sizesOnly)} checked="checked"{/if} />
			</label>
			<div class="input-group" style="max-width:12rem;">
				<input class="form-control" type="number" min="0" name="taskdata[size]" value="{if !isset($sched_data.taskdata.size)}512{else}{$sched_data.taskdata.size}{/if}" />
				<span class="input-group-text">KB</span>
			</div>
		</div>
	</div>
</div>
