<div class="mb-3 row">
	<label class="col-sm-3 col-form-label" for="tccrn_stats_days">{lng p="sched.keep_last"}</label>
	<div class="col-sm-9">
		<div class="input-group" style="max-width:12rem;">
			<input class="form-control" type="number" min="0" id="tccrn_stats_days" name="taskdata[days]" value="{if !isset($tccrn_data.taskdata.days)}0{else}{$tccrn_data.taskdata.days}{/if}" />
			<span class="input-group-text">{lng p="days"}</span>
		</div>
	</div>
</div>
