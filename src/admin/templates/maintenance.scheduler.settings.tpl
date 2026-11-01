<form method="post" action="{sessionurl file=$sched_admin_script params="action={$sched_admin_action}&do=settings"}" onsubmit="spin(this)">
	{csrffield}

	<fieldset>
		<legend>{lng p="prefs"}</legend>

		<div class="mb-3">
			<label class="form-check">
				<input class="form-check-input" id="id_loglevel_8" type="checkbox" name="loglevel[]" value="8"{if ($sched_prefs.loglevel&8)!=0} checked="checked"{/if} />
				<span class="form-check-label">{lng p="sched.logging_debug"}</span>
			</label>
			<label class="form-check">
				<input class="form-check-input" id="id_loglevel_1" type="checkbox" name="loglevel[]" value="1"{if ($sched_prefs.loglevel&1)!=0} checked="checked"{/if} />
				<span class="form-check-label">{lng p="sched.logging_notices"}</span>
			</label>
			<label class="form-check">
				<input class="form-check-input" id="id_loglevel_2" type="checkbox" name="loglevel[]" value="2"{if ($sched_prefs.loglevel&2)!=0} checked="checked"{/if} />
				<span class="form-check-label">{lng p="sched.logging_warnings"}</span>
			</label>
			<label class="form-check">
				<input class="form-check-input" id="id_loglevel_4" type="checkbox" name="loglevel[]" value="4"{if ($sched_prefs.loglevel&4)!=0} checked="checked"{/if} />
				<span class="form-check-label">{lng p="sched.logging_errors"}</span>
			</label>
		</div>

		<div class="text-end">
			<button type="submit" class="btn btn-primary">{lng p="save"}</button>
		</div>
	</fieldset>
</form>
