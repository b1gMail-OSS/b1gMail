<fieldset>
	<legend>{lng p="inactiveusers"}</legend>

	<div class="alert alert-warning">{lng p="undowarn"}</div>

	<form action="maintenance.php?do=exec&sid={$sid}" method="post" onsubmit="spin(this)">

		<p>{lng p="activity_desc1"}</p>

		<div class="mb-3 row">
			<div class="col-sm-12">
				<div class="input-group mb-2">
                        <span class="input-group-text">
                        	<input class="form-check-input m-0" type="checkbox" id="queryTypeLogin" name="queryTypeLogin" checked="checked">
                        </span>
					<span class="input-group-text">{lng p="notloggedinsince"}</span>
					<input type="text" class="form-control" name="loginDays" value="90">
					<span class="input-group-text">{lng p="days"}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12" style="padding-left: 22px;">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" id="queryTypeGroups" name="queryTypeGroups" checked="checked">
					<span class="form-check-label">{lng p="whobelongtogrps"}</span>
				</label>
			</div>
			<div class="col-sm-12" style="padding-left: 30px;">
				{foreach from=$groups item=group key=groupID}
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="contactform_name"{if isset($bm_prefs.contactform_name) && $bm_prefs.contactform_name=='yes'} checked="checked"{/if}>
					<span class="form-check-label">{text value=$group.title}</span>
				</label>
				{/foreach}
			</div>
		</div>

		<p>{lng p="activity_desc2"}</p>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<label class="form-check">
					<input class="form-check-input" type="radio" id="queryActionShow" name="queryAction" value="show" checked="checked">
					<span class="form-check-label">{lng p="showlist"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" id="queryActionLock" name="queryAction" value="lock">
					<span class="form-check-label">{lng p="lock"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" id="queryActionMove" name="queryAction" value="move">
					<span class="form-check-label">{lng p="movetogroup"}</span>
					<span class="form-check-label">
						<select name="moveGroup" class="form-select">
									{foreach from=$groups item=groupItem}
										<option value="{$groupItem.id}">{text value=$groupItem.title}</option>
									{/foreach}
									</select>
					</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" id="queryActionDelete" name="queryAction" value="delete">
					<span class="form-check-label">{lng p="delete"}</span>
				</label>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-warning" type="submit" value="{lng p="execute"}" />
		</div>
	</form>
</fieldset>
