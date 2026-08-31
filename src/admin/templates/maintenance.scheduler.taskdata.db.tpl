<div class="mb-3">
	<label class="form-label">{lng p="tables"}</label>
	<div class="tccrn-table-grid" data-tccrn-group="tables">
		{foreach from=$tccrn_tables item=table name=tccrn_tables}
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="taskdata[table][]" value="{$table}" id="tccrn-table-{$smarty.foreach.tccrn_tables.index}"{if !isset($tccrn_data.taskdata.table) || !is_array($tccrn_data.taskdata.table) || ($table|in_array:$tccrn_data.taskdata.table)} checked="checked"{/if} />
				<span class="form-check-label text-truncate" title="{$table}">{$table}</span>
			</label>
		{/foreach}
	</div>
	<div class="btn-list mt-2">
		<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="all" data-tccrn-target="tables">{lng p="sched.select_all"}</button>
		<button type="button" class="btn btn-sm btn-ghost-secondary" data-tccrn-toggle="none" data-tccrn-target="tables">{lng p="sched.select_none"}</button>
	</div>
</div>
