<form action="{$pageURL}&action=subscriptions&extend={$ids}&sid={$sid}" method="post">
	<fieldset>
		<legend>{lng p="pacc_extendsubscr"}</legend>

		{lng p="pacc_extendsubscrdesc"}

		<div class="input-group mb-2">
		<span class="input-group-text">
        	<input class="form-check-input m-0" type="radio" id="modeDynamic" name="mode" value="dynamic" checked="checked">
		</span>
			<span class="input-group-text">{lng p="pacc_extenddynamic"}</span>
			<input type="text" class="form-control" name="dynamicValue" value="30">
			<select name="dynamicFactor" class="form-select">
				<option value="1">{lng p="pacc_period_tage"}</option>
				<option value="7">{lng p="pacc_period_wochen"}</option>
				<option value="31">{lng p="pacc_period_monate"}</option>
				<option value="365">{lng p="pacc_period_jahre"}</option>
			</select>
		</div>

		<div class="input-group mb-2">
		<span class="input-group-text">
        	<input class="form-check-input m-0" type="radio" id="modeStatic" name="mode" value="static">
		</span>
			<span class="input-group-text">{lng p="pacc_extendstatic"}</span>
			<input type="text" class="form-control" name="dynamicValue" value="30">
			{html_select_date prefix="staticValue" end_year="+5" field_order="DMY" field_separator="."}
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="pacc_extendsubscr"}" />
		</div>
	</fieldset>
</form>