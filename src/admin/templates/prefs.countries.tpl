<fieldset>
	<legend>{lng p="countries"}</legend>

	<form action="{sessionurl file='prefs.countries.php'}" name="f1" method="post" onsubmit="spin(this)">
		{csrffield}
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th class="text-center" style="width:3rem;"><a href="javascript:invertSelection(document.forms.f1,'country_');">{acpicon file="dot.png" size="sm"}</a></th>
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
							<td class="text-center"><label class="form-check d-inline-block mb-0"><input class="form-check-input" type="checkbox" name="country_{$country.id}" /></label></td>
							<td>{$country.land}</td>
							<td class="text-center">{if $country.plzDB}<i class="fa-regular fa-square-check"></i>{/if}</td>
							<td class="text-center">{if $country.is_eu}<i class="fa-regular fa-square-check"></i>{/if}</td>
							<td class="text-center">{if $country.vat}{$country.vat} %{/if}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="{sessionurl file='prefs.countries.php' params="do=edit&id={$country.id}"}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="{sessionurl file='prefs.countries.php' params="delete={$country.id}"}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div class="input-group input-group-sm bm-mass-action">
					<span class="input-group-text">{lng p="action"}</span>
					<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>
					<button type="submit" name="executeMassAction" class="btn btn-dark-lt" value="1">{lng p="execute"}</button>
				</div>
			</div>
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="addcountry"}</legend>

	<form action="{sessionurl file='prefs.countries.php' params="add=true"}" method="post" onsubmit="spin(this)">
		{csrffield}
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