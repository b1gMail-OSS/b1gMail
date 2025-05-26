<fieldset>
	<legend>{lng p="lockedusernames"}</legend>

	<form action="prefs.common.php?action=lockedusernames&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px; text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'locked_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="username"}</th>
						<th style="width: 55px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$lockedUsernames item=locked}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td><input type="checkbox" name="locked_{$locked.id}" /></td>
							<td>{$locked.type} &quot;{text value=$locked.username}&quot;</td>
							<td>
								<a href="prefs.common.php?action=lockedusernames&delete={$locked.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
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

<form action="prefs.common.php?action=lockedusernames&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="addlockedusername"}</legend>

		<div class="row">
			<div class="col-md-6">
				<div class="mb-3">
					<label class="form-label">{lng p="type"}</label>
					<select name="typ" class="form-select">
						{foreach from=$lockedTypeTable key=id item=text}
							<option value="{$id}">{$text}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3">
					<label class="form-label">{lng p="username"}</label>
					<input type="text" class="form-control" name="benutzername" value="" placeholder="{lng p="username"}">
				</div>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</fieldset>
</form>