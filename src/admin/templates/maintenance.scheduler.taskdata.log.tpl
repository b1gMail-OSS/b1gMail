<div class="mb-3">
	<label class="form-check">
		<input class="form-check-input" type="checkbox" name="taskdata[save]" value="1" id="tccrn_log_save"{if !isset($tccrn_data.taskdata.save) || $tccrn_data.taskdata.save} checked="checked"{/if} />
		<span class="form-check-label">{lng p="savearc"}</span>
	</label>
</div>

<div class="mb-3 row">
	<label class="col-sm-3 col-form-label" for="tccrn_log_days">{lng p="sched.keep_last"}</label>
	<div class="col-sm-9">
		<div class="d-flex flex-wrap align-items-center gap-2">
			<label class="form-check mb-0">
				<input class="form-check-input" type="checkbox" value="1" name="taskdata[keepDays]" id="tccrn_log_keep"{if !isset($tccrn_data.taskdata.keepDays) || $tccrn_data.taskdata.keepDays} checked="checked"{/if} />
			</label>
			<div class="input-group" style="max-width:12rem;">
				<input class="form-control" type="number" min="0" id="tccrn_log_days" name="taskdata[days]" value="{if !isset($tccrn_data.taskdata.days)}30{else}{$tccrn_data.taskdata.days}{/if}" />
				<span class="input-group-text">{lng p="days"}</span>
			</div>
		</div>
	</div>
</div>
