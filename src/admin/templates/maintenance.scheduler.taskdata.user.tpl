{if $sched_data.task == 'sched.us_delete' || $sched_data.task == 'sched.us_na_delete' || $sched_data.task == 'sched.us_nl_delete'}
<div class="mb-3">
	<label class="form-check">
		<input class="form-check-input" type="checkbox" name="taskdata[realdel]" value="1" id="sched_realdel"{if !empty($sched_data.taskdata.realdel)} checked="checked"{/if} />
		<span class="form-check-label">{lng p="sched.user_delete_confirm"}</span>
	</label>
</div>
{/if}

<p class="text-secondary small">{lng p="sched.user_filter_hint"}</p>

<div class="mb-3 row">
	<label class="col-sm-3 col-form-label" for="sched_user_days">
		{if $sched_data.task == 'sched.us_na_delete' || $sched_data.task == 'sched.us_nl_delete'}{lng p="trash_daysonly"}{else}{lng p="notloggedinsince"}{/if}
	</label>
	<div class="col-sm-9">
		<div class="input-group" style="max-width:12rem;">
			<input class="form-control" type="number" min="1" id="sched_user_days" name="taskdata[days]" value="{if !isset($sched_data.taskdata.days)}90{else}{$sched_data.taskdata.days}{/if}" />
			<span class="input-group-text">{lng p="days"}</span>
		</div>
	</div>
</div>

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
