<form action="prefs.abuse.php?save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="common"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="ap_medium_limit"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<span class="input-group-text"><img src="templates/images/indicator_yellow.png" border="0" alt="" align="absmiddle" /></span>
					<input type="text" class="form-control" name="ap_medium_limit" value="{text allowEmpty=true value=$bm_prefs.ap_medium_limit}" placeholder="{lng p="ap_medium_limit"}">
					<span class="input-group-text">{lng p="points"}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="ap_medium_limit"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<span class="input-group-text"><img src="templates/images/indicator_red.png" border="0" alt="" align="absmiddle" /></span>
					<input type="text" class="form-control" name="ap_hard_limit" value="{text allowEmpty=true value=$bm_prefs.ap_hard_limit}" placeholder="{lng p="ap_medium_limit"}">
					<span class="input-group-text">{lng p="points"}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="ap_expire_time"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="ap_hard_limit" value="{text allowEmpty=true value=$bm_prefs.ap_expire_time/3600}" placeholder="{lng p="ap_expire_time"}">
					<span class="input-group-text">{lng p="hours"}</span>
				</div>
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
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="ap_autolock"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="ap_autolock"{if $bm_prefs.ap_autolock=='yes'} checked="checked"{/if}>
					<span class="form-check-label">{lng p="ap_athardlimit"}</span>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="ap_autolock_notify"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
                        <span class="input-group-text">
                        	<input class="form-check-input m-0" type="checkbox" name="ap_autolock_notify"{if $bm_prefs.ap_autolock_notify=='yes'} checked="checked"{/if}>
                        </span>
					<span class="input-group-text">{lng p="to2"}:</span>
					<input type="text" class="form-control" name="ap_autolock_notify_to" value="{email value=$bm_prefs.ap_autolock_notify_to}" placeholder="{lng p="ap_autolock_notify"}">
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
												<tr>
													<td style="width: 150px;">{$prefDetails.title}</td>
													<td>
														{if $prefDetails.type==1}
															<input type="text" name="types[{$apTypeID}][prefs][{$prefKey}]" value="{if isset($prefDetails.value)}{text value=$prefDetails.value}{/if}" style="width:100px;" class="smallInput" />
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
