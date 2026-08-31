{if $tccrn_data.task == 'tccrn.us_delete' || $tccrn_data.task == 'tccrn.us_na_delete' || $tccrn_data.task == 'tccrn.us_nl_delete'}
<div class="mb-3">
	<label class="form-check">
		<input class="form-check-input" type="checkbox" name="taskdata[realdel]" value="1" id="tccrn_realdel"{if !empty($tccrn_data.taskdata.realdel)} checked="checked"{/if} />
		<span class="form-check-label">{lng p="sched.user_delete_confirm"}</span>
	</label>
</div>
{/if}

<p class="text-secondary small">{lng p="sched.user_filter_hint"}</p>

<div class="mb-3 row">
	<label class="col-sm-3 col-form-label" for="tccrn_user_days">
		{if $tccrn_data.task == 'tccrn.us_na_delete' || $tccrn_data.task == 'tccrn.us_nl_delete'}{lng p="trash_daysonly"}{else}{lng p="notloggedinsince"}{/if}
	</label>
	<div class="col-sm-9">
		<div class="input-group" style="max-width:12rem;">
			<input class="form-control" type="number" min="1" id="tccrn_user_days" name="taskdata[days]" value="{if !isset($tccrn_data.taskdata.days)}90{else}{$tccrn_data.taskdata.days}{/if}" />
			<span class="input-group-text">{lng p="days"}</span>
		</div>
	</div>
</div>

<div class="mb-3">
	<label class="form-label">{lng p="whobelongtogrps"}</label>
	<div class="tccrn-group-grid" data-tccrn-group="groups">
		{foreach from=$groups item=group key=groupID}
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="taskdata[groups][]" value="{$groupID}" id="group_{$groupID}"{if !isset($tccrn_data.taskdata.groups) || !is_array($tccrn_data.taskdata.groups) || ($groupID|in_array:$tccrn_data.taskdata.groups)} checked="checked"{/if} />
				<span class="form-check-label">{text value=$group.title}</span>
			</label>
		{/foreach}
	</div>
	<div class="btn-list mt-2">
		<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="all" data-tccrn-target="groups">{lng p="sched.select_all"}</button>
		<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="none" data-tccrn-target="groups">{lng p="sched.select_none"}</button>
	</div>
</div>
