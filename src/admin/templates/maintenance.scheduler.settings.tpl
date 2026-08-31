{include file=$tccrn_nav_tpl}

<form method="post" action="{sessionurl file=$tccrn_admin_script params="action={$tccrn_admin_action}&do=settings"}" onsubmit="spin(this)">
	{csrffield}

	<fieldset class="mb-4">
		<legend class="h5 text-secondary mb-3">{lng p="sched.logging"}</legend>

		<div class="mb-2">
			<label class="form-check">
				<input class="form-check-input" id="id_loglevel_8" type="checkbox" name="loglevel[]" value="8"{if ($tccrn_prefs.loglevel&8)!=0} checked="checked"{/if} />
				<span class="form-check-label">{lng p="sched.logging_debug"}</span>
			</label>
		</div>
		<div class="mb-2">
			<label class="form-check">
				<input class="form-check-input" id="id_loglevel_1" type="checkbox" name="loglevel[]" value="1"{if ($tccrn_prefs.loglevel&1)!=0} checked="checked"{/if} />
				<span class="form-check-label">{lng p="sched.logging_notices"}</span>
			</label>
		</div>
		<div class="mb-2">
			<label class="form-check">
				<input class="form-check-input" id="id_loglevel_2" type="checkbox" name="loglevel[]" value="2"{if ($tccrn_prefs.loglevel&2)!=0} checked="checked"{/if} />
				<span class="form-check-label">{lng p="sched.logging_warnings"}</span>
			</label>
		</div>
		<div class="mb-2">
			<label class="form-check">
				<input class="form-check-input" id="id_loglevel_4" type="checkbox" name="loglevel[]" value="4"{if ($tccrn_prefs.loglevel&4)!=0} checked="checked"{/if} />
				<span class="form-check-label">{lng p="sched.logging_errors"}</span>
			</label>
		</div>
	</fieldset>

	<div class="text-end">
		<button type="submit" class="btn btn-primary">
			<i class="ti ti-device-floppy me-1"></i> {lng p="save"}
		</button>
	</div>
</form>
