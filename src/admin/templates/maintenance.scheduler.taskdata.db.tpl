<div class="mb-3">
	<label class="form-label">{lng p="tables"}</label>
	<div class="sched-table-grid" data-sched-group="tables">
		{foreach from=$sched_tables item=table name=sched_tables}
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="taskdata[table][]" value="{$table}" id="sched-table-{$smarty.foreach.sched_tables.index}"{if !isset($sched_data.taskdata.table) || !is_array($sched_data.taskdata.table) || ($table|in_array:$sched_data.taskdata.table)} checked="checked"{/if} />
				<span class="form-check-label text-truncate" title="{$table}">{$table}</span>
			</label>
		{/foreach}
	</div>
	<div class="btn-list mt-2">
		<button type="button" class="btn btn-sm btn-ghost-secondary" data-sched-toggle="all" data-sched-target="tables">{lng p="sched.select_all"}</button>
		<button type="button" class="btn btn-sm btn-ghost-secondary" data-sched-toggle="none" data-sched-target="tables">{lng p="sched.select_none"}</button>
	</div>
</div>
