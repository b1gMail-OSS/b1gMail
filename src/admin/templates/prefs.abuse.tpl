<form action="{sessionurl file='prefs.abuse.php' params="save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<fieldset>
		<legend>{lng p="common"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="ap_medium_limit"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<span class="input-group-text">{acpicon file="indicator_yellow.png"}</span>
					<input type="text" class="form-control" name="ap_medium_limit" value="{text allowEmpty=true value=$bm_prefs.ap_medium_limit}" placeholder="{lng p="ap_medium_limit"}">
					<span class="input-group-text">{lng p="points"}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="ap_hard_limit"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<span class="input-group-text">{acpicon file="indicator_red.png"}</span>
					<input type="text" class="form-control" name="ap_hard_limit" value="{text allowEmpty=true value=$bm_prefs.ap_hard_limit}" placeholder="{lng p="ap_hard_limit"}">
					<span class="input-group-text">{lng p="points"}</span>
				</div>
				{if $apLimitHint|default:''}<div class="form-text text-warning">{$apLimitHint}</div>{/if}
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="ap_expire_time"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="ap_expire_time" value="{text allowEmpty=true value=$bm_prefs.ap_expire_time/3600}" placeholder="{lng p="ap_expire_time"}">
					<span class="input-group-text">{lng p="hours"}</span>
				</div>
				{if $bm_prefs.ap_expire_time == 0}<div class="form-text text-warning">{lng p="ap_expire_hint"}</div>{/if}
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="ap_expire_mode"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="ap_expire_mode" value="dynamic" id="ap_expire_mode_dynamic"{if $bm_prefs.ap_expire_mode=='dynamic'} checked="checked"{/if}>
					<span class="form-check-label">{lng p="ap_dynamic"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" name="ap_expire_mode" value="static" id="ap_expire_mode_static"{if $bm_prefs.ap_expire_mode=='static'} checked="checked"{/if}>
					<span class="form-check-label">{lng p="ap_static"}</span>
				</label>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="ap_reaction"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="ap_atlimit"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="ap_action" value="none" id="ap_action_none"{if $apAction=='none'} checked="checked"{/if} onclick="apSync();">
					<span class="form-check-label">{lng p="ap_action_none"} <span class="text-secondary">({lng p="ap_action_none_desc"})</span></span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" name="ap_action" value="lock" id="ap_action_lock"{if $apAction=='lock'} checked="checked"{/if} onclick="apSync();">
					<span class="form-check-label">{lng p="ap_action_lock"} <span class="text-secondary">({lng p="ap_action_lock_desc"})</span></span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" name="ap_action" value="move" id="ap_action_move"{if $apAction=='move'} checked="checked"{/if} onclick="apSync();">
					<span class="form-check-label">{lng p="ap_action_move"} <span class="text-secondary">({lng p="ap_action_move_desc"})</span></span>
				</label>
				<div class="input-group mt-2 mb-2">
					<span class="input-group-text">{lng p="ap_movegroup"}</span>
					<select class="form-select" name="ap_automove_group" id="ap_automove_group">
						<option value="0">{lng p="ap_movegroup_none"}</option>
						{foreach from=$apGroups item=group key=groupID}
						<option value="{$groupID}"{if ($bm_prefs.ap_automove_group|default:0) == $groupID} selected="selected"{/if}>{text value=$group.title}</option>
						{/foreach}
					</select>
				</div>
				{if $apQueueSupported}
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="ap_clearqueue" id="ap_clearqueue"{if ($bm_prefs.ap_clearqueue|default:'no')=='yes'} checked="checked"{/if}>
					<span class="form-check-label">{lng p="ap_clearqueue"} <span class="text-secondary">({lng p="ap_clearqueue_desc"})</span></span>
				</label>
				{/if}
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="ap_autolock_notify"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="ap_autolock_notify" id="ap_autolock_notify"{if $bm_prefs.ap_autolock_notify=='yes'} checked="checked"{/if} onclick="apSync();">
					<span class="form-check-label">{lng p="ap_notify_team"}</span>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label fw-normal">{lng p="ap_notify_address"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="ap_autolock_notify_to" id="ap_autolock_notify_to" value="{email value=$bm_prefs.ap_autolock_notify_to}" placeholder="{lng p="ap_notify_address"}">
				</div>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="pointtypes"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th>{lng p="title"}</th>
						<th style="width: 100px;">{lng p="points"}</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$apTypes item=apType key=apTypeID}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td>
								{$apType.title}
								{if $apType.prefs}
									<div>
										<table>
											{foreach from=$apType.prefs key=prefKey item=prefDetails}
												{* a title like "Period (minutes):" is shown as "Period:" with the unit right of the box *}
												{assign var=prefUnit value=$prefDetails.title|regex_replace:'/^.*\(([^)]*)\)\s*:?\s*$/s':'$1'}
												<tr>
													<td style="width: 190px; white-space: nowrap;">{if $prefUnit != $prefDetails.title}{$prefDetails.title|regex_replace:'/\s*\([^)]*\)\s*:?\s*$/':':'}{else}{$prefDetails.title}{/if}</td>
													<td>
														{if $prefDetails.type==1}
															<div class="input-group input-group-sm" style="width: 12rem;">
																<input type="text" class="form-control" name="types[{$apTypeID}][prefs][{$prefKey}]" value="{if isset($prefDetails.value)}{text value=$prefDetails.value}{/if}" />
																{if $prefUnit != $prefDetails.title}<span class="input-group-text">{$prefUnit}</span>{/if}
															</div>
														{/if}
													</td>
												</tr>
											{/foreach}
										</table>
									</div>
								{/if}
							</td>
							<td><input type="text" class="form-control" name="types[{$apTypeID}][points]" value="{$apType.points}" /></td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>

{literal}<script>
<!--
	function apSync()
	{
		document.getElementById('ap_automove_group').disabled = !document.getElementById('ap_action_move').checked;
		if(document.getElementById('ap_clearqueue'))
			document.getElementById('ap_clearqueue').disabled = !document.getElementById('ap_action_move').checked;
		document.getElementById('ap_autolock_notify_to').disabled = !document.getElementById('ap_autolock_notify').checked;
	}
	apSync();
//-->
</script>{/literal}
