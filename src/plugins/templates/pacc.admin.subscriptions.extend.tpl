<form action="{$pageURL}&action=subscriptions&extend={$ids}&sid={$sid}" method="post">
<fieldset>
	<legend>{lng p="pacc_extendsubscr"}</legend>
	
	{lng p="pacc_extendsubscrdesc"}
		
	<p>
		<div>
			<table>
			<tr>
				<td><input type="radio" id="modeDynamic" name="mode" value="dynamic" checked="checked" /></td>
				<td><label for="modeDynamic">{lng p="pacc_extenddynamic"}</label></td>
				<td>
					<input type="text" name="dynamicValue" value="30" size="4" />
					<select name="dynamicFactor">
						<option value="1">{lng p="pacc_period_tage"}</option>
						<option value="7">{lng p="pacc_period_wochen"}</option>
						<option value="31">{lng p="pacc_period_monate"}</option>
						<option value="365">{lng p="pacc_period_jahre"}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><input type="radio" id="modeStatic" name="mode" value="static" /></td>
				<td><label for="modeStatic">{lng p="pacc_extendstatic"} &nbsp;</label></td>
				<td>
					{html_select_date prefix="staticValue" end_year="+5" field_order="DMY" field_separator="."}
				</td>
			</tr>
			</table>
		</div>
	</p>
</fieldset>

<p>
	<div style="float:right">
		<input class="button" type="submit" value=" {lng p="pacc_extendsubscr"} " />
	</div>
</p>
</form>
