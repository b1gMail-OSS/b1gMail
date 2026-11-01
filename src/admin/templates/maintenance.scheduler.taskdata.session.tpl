<div class="mb-3 row">
	<label class="col-sm-2 col-form-label" for="sched_sess_days">{lng p="sched.keep_last"}</label>
	<div class="col-sm-10">
		<div class="input-group">
			<input class="form-control" type="number" min="0" id="sched_sess_days" name="taskdata[days]" value="{if !isset($sched_data.taskdata.days)}30{else}{$sched_data.taskdata.days}{/if}" />
			<span class="input-group-text">{lng p="days"}</span>
		</div>
	</div>
</div>
