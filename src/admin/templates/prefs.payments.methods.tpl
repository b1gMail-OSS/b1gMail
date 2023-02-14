<fieldset>
	<legend>{lng p="paymentmethods"}</legend>

	<form action="prefs.payments.php?action=paymethods&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px; text-align: center;"><a href="javascript:invertSelection(document.forms.f1,'method_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="title"}</th>
						<th style="width: 75px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$methods item=method}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td><input type="checkbox" name="method_{$method.methodid}" /></td>
							<td>{text value=$method.title}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="prefs.payments.php?action=paymethods&{if $method.enabled}dis{else}en{/if}able={$method.methodid}&sid={$sid}" title="{if $method.enabled}{lng p="disable"}{else}{lng p="enable"}{/if}" class="btn btn-sm">{if $method.enabled}<i class="fa-regular fa-square-check" title="{lng p="disable"}"></i>{else}<i class="fa-regular fa-square" title="{lng p="enable"}"></i>{/if}</a>
									<a href="prefs.payments.php?action=paymethods&do=edit&methodid={$method.methodid}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="prefs.payments.php?action=paymethods&delete={$method.methodid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
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
	<legend>{lng p="addpaymethod"}</legend>

	<form action="prefs.payments.php?action=paymethods&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="title"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="title" value="" placeholder="{lng p="title"}">
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>
