<fieldset>
	<legend>{lng p="countries"}</legend>

	<form action="prefs.countries.php?sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px; text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'country_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="country"}</th>
						<th style="width: 80px;" style="text-align:center;">{lng p="plzdb"}?</th>
						<th style="width: 80px;" style="text-align:center;">{lng p="eucountry"}?</th>
						<th style="width: 80px;" style="text-align:center;">{lng p="vatrate"}</th>
						<th style="width: 60px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$countries item=country}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><input type="checkbox" name="country_{$country.id}" /></td>
							<td>{$country.land}</td>
							<td class="text-center">{if $country.plzDB}<i class="fa-regular fa-square-check"></i>{/if}</td>
							<td class="text-center">{if $country.is_eu}<i class="fa-regular fa-square-check"></i>{/if}</td>
							<td class="text-center">{if $country.vat}{$country.vat} %{/if}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="prefs.countries.php?do=edit&id={$country.id}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="prefs.countries.php?delete={$country.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div style="float: left;">{lng p="action"}:&nbsp;</div>
				<div style="float: left;">
					<div class="btn-group btn-group-sm">
						<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>
						<input type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" />
					</div>
				</div>
			</div>
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="addcountry"}</legend>

	<form action="prefs.countries.php?add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="country"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="land" value="" placeholder="{lng p="country"}">
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>