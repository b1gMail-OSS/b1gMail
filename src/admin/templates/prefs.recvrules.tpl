<fieldset>
	<legend>{lng p="recvrules"}</legend>

	<form action="prefs.recvrules.php?sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px;" class="text-center"><a href="javascript:invertSelection(document.forms.f1,'rule_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="field"}</th>
						<th>{lng p="expression"}</th>
						<th style="width: 210px;">{lng p="action"}</th>
						<th style="width: 45px;">{lng p="value"}</th>
						<th style="width: 120px;">{lng p="type"}</th>
						<th style="width: 60px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$rules item=rule}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><input type="checkbox" name="rule_{$rule.id}" /></td>
							<td>{text value=$rule.field}</td>
							<td>{text value=$rule.expression}</td>
							<td>{$rule.action}</td>
							<td>{$rule.value}</td>
							<td>{$rule.type}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="prefs.recvrules.php?do=edit&id={$rule.id}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="prefs.recvrules.php?delete={$rule.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
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
								<option value="export">{lng p="export"}</option>
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

<div class="row">
	<div class="col-md-6">
		<fieldset>
			<legend>{lng p="addrecvrule"}</legend>

			<form action="prefs.recvrules.php?add=true&sid={$sid}" method="post" onsubmit="spin(this)">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="field"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="field" value="" placeholder="{lng p="field"} (from, subject, ...)">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="expression"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="expression" value="" placeholder="{lng p="expression"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="action"}</label>
					<div class="col-sm-8">
						<select name="ruleAction" class="form-select">
							{foreach from=$ruleActionTable key=id item=text}
								<option value="{$id}">{$text}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="value"}</label>
					<div class="col-sm-8">
						<input type="number" class="form-control" name="value" value="0" placeholder="{lng p="value"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="type"}</label>
					<div class="col-sm-8">
						<select name="type" class="form-select">
							{foreach from=$ruleTypeTable key=id item=text}
								<option value="{$id}">{$text}</option>
							{/foreach}
						</select>
					</div>
				</div>

				<div class="text-end">
					<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
				</div>
			</form>
		</fieldset>
	</div>
	<div class="col-md-6">
		<fieldset>
			<legend>{lng p="import"}</legend>

			<form action="prefs.recvrules.php?import=true&sid={$sid}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="rulefile"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input type="file" name="rulefile" class="form-control" accept=".bmrecvrules" />
						</label>
					</div>
				</div>

				<div class="text-end">
					<input class="btn btn-primary" type="submit" value="{lng p="import"}" />
				</div>
			</form>
		</fieldset>
	</div>
</div>