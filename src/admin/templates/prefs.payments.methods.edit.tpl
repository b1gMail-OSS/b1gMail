<form action="prefs.payments.php?action=paymethods&do=edit&methodid={$row.methodid}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">

	<fieldset>
		<legend>{lng p="paymentmethod"}: {text value=$row.title}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="title"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="title" value="{if isset($row.title)}{text value=$row.title allowEmpty=true}{/if}" placeholder="{lng p="title"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="enable"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="enabled"{if $row.enabled} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="invoice"}</label>
			<div class="col-sm-10">
				<select name="invoice" class="form-select">
					<option value="at_order"{if $row.invoice=='at_order'} selected="selected"{/if}>{lng p="at_order"}</option>
					<option value="at_activation"{if $row.invoice=='at_activation'} selected="selected"{/if}>{lng p="at_activation"}</option>
				</select>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="fields"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th>{lng p="title"}</th>
						<th style="width: 120px;">{lng p="type"}</th>
						<th>{lng p="options"}</th>
						<th style="width: 70px;">{lng p="oblig"}?</th>
						<th>{lng p="validityrule"}</th>
						<th style="width: 70px;">{lng p="pos"}</th>
						<th style="width: 70px;">{lng p="delete"}?</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$fields item=field key=fieldID}
						{cycle name=class values="td1,td2" assign=class}
						{assign var=lastPos value=$field.pos}
						<tr class="{$class}">
							<td><input type="text" class="form-control" name="fields[{$fieldID}][title]" value="{if isset($field.title)}{text value=$field.title allowEmpty=true}{/if}" /></td>
							<td><select name="fields[{$fieldID}][type]" class="form-select">
									{foreach from=$fieldTypeTable key=id item=text}
										<option value="{$id}"{if $id==$field.type} selected="selected"{/if}>{$text}</option>
									{/foreach}
								</select></td>
							<td><input type="text" class="form-control" name="fields[{$fieldID}][options]" value="{if isset($field.options)}{text value=$field.options allowEmpty=true}{/if}" /></td>
							<td class="text-center"><input type="checkbox" class="form-check-input" name="fields[{$fieldID}][oblig]"{if $field.oblig} checked="checked"{/if} /></td>
							<td><input type="text" class="form-control" name="fields[{$fieldID}][rule]" value="{if isset($field.rule)}{text value=$field.rule allowEmpty=true}{/if}" /></td>
							<td><input type="text" class="form-control" name="fields[{$fieldID}][pos]" value="{if isset($field.pos)}{text value=$field.pos allowEmpty=true}{/if}" size="5" /></td>
							<td class="text-center"><input type="checkbox" class="form-check-input" name="fields[{$fieldID}][delete]" /></td>
						</tr>
					{/foreach}

					{cycle name=class values="td1,td2" assign=class}
					<tr class="{$class}">
						<td><input type="text" class="form-control" name="fields[new][title]" /></td>
						<td><select name="fields[new][type]" class="form-select">
								{foreach from=$fieldTypeTable key=id item=text}
									<option value="{$id}"{if $id==1} selected="selected"{/if}>{$text}</option>
								{/foreach}
							</select></td>
						<td><input type="text" class="form-control" name="fields[new][options]" /></td>
						<td class="text-center"><input type="checkbox" class="form-check-input" name="fields[new][oblig]" /></td>
						<td><input type="text" class="form-control" name="fields[new][rule]" /></td>
						<td><input type="text" class="form-control" name="fields[new][pos]" value="{$lastPos+10}" size="5" /></td>
						<td>&nbsp;</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6"><input class="btn btn-muted" type="button" onclick="document.location.href='prefs.payments.php?action=paymethods&sid={$sid}';" value=" &laquo; {lng p="back"}" /></div>
		<div class="col-md-6 text-end"><input class="btn btn-primary" type="submit" value="{lng p="save"}" /></div>
	</div>
</form>
