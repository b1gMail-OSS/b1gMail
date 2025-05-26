<fieldset>
	<legend>{lng p="profilefields"}</legend>

	<form action="prefs.profilefields.php?sid={$sid}" method="post" name="f1" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px; text-align: center;"><a href="javascript:invertSelection(document.forms.f1,'field_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="field"}</th>
						<th>{lng p="validityrule"}</th>
						<th style="width: 100px;">{lng p="type"}</th>
						<th style="width: 50px;">{lng p="oblig"}</th>
						<th style="width: 60px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$fields item=field}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><input type="checkbox" name="field_{$field.id}" /></td>
							<td>{text value=$field.feld}<br /><small>{text value=$field.extra}</small></td>
							<td>{text value=$field.rule}</td>
							<td>{$field.typ}</td>
							<td><input type="checkbox" disabled="disabled"{if $field.pflicht} checked="checked"{/if} /></td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="prefs.profilefields.php?do=edit&id={$field.id}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="prefs.profilefields.php?delete={$field.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
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
	<legend>{lng p="addprofilefield"}</legend>

	<form action="prefs.profilefields.php?add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="field"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="feld" value="" placeholder="{lng p="field"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="validityrule"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="rule" value="" placeholder="{lng p="validityrule"}">
				<small>{lng p="pfrulenote"}</small>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="type"}</label>
			<div class="col-sm-10">
				<select name="typ" class="form-select">
					{foreach from=$fieldTypeTable key=id item=text}
						<option value="{$id}">{$text}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="oblig"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="pflicht">
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="show"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="show_signup">
					<span class="form-check-label">{lng p="signup"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="show_li">
					<span class="form-check-label">{lng p="li"}</span>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="options"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="extra" value="" placeholder="{lng p="options"}">
				<small>{lng p="optionsdesc"}</small>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>