<fieldset>
	<legend>{lng p="types"}</legend>

	<form action="prefs.sms.php?action=types&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 20px;">&nbsp;</th>
						<th style="width: 25px; text-align: center;"><a href="javascript:invertSelection(document.forms.f1,'type_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="title"}</th>
						<th>{lng p="type"}</th>
						<th>{lng p="maxlength"}</th>
						<th>{lng p="price"} ({lng p="credits"})</th>
						<th style="width: 75px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$types item=type}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center">
								{if $type.std}
									<i class="fa-regular fa-square-check"></i>
								{else}
									<i class="fa-regular fa-square"></i>
								{/if}
							<td class="text-center">{if !$type.std}<input type="checkbox" name="type_{$type.id}" />{/if}</td>
							<td>{text value=$type.titel}</td>
							<td width="100">{text value=$type.typ}</td>
							<td width="100">{$type.maxlength}</td>
							<td width="100">{$type.price}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="prefs.sms.php?action=types&do=edit&id={$type.id}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									{if !$type.std}<a href="prefs.sms.php?action=types&setDefault={$type.id}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-square-check"></i></a>
									<a href="prefs.sms.php?action=types&delete={$type.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>{/if}
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
	<legend>{lng p="addtype"}</legend>

	<form action="prefs.sms.php?action=types&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="title"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="titel" value="" placeholder="{lng p="title"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="gateway"}</label>
			<div class="col-sm-10">
				<select name="gateway" class="form-select">
					<option value="0">({lng p="defaultgateway"})</option>
					{foreach from=$gateways item=gateway}
						<option value="{$gateway.id}">{text value=$gateway.titel}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="type"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="typ" value="" placeholder="{lng p="type"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="price"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="price" value="" placeholder="{lng p="price"}">
					<span class="input-group-text">{lng p="credits"}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="maxlength"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="maxlength" value="160" placeholder="{lng p="maxlength"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="prefs"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="flags[1]" value="true">
					<span class="form-check-label">{lng p="disablesender"}</span>
				</label>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value=" {lng p="add"} " />
		</div>
	</form>
</fieldset>