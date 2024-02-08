<fieldset>
	<legend>{lng p="admins"}</legend>

	<form action="admins.php?action=admins&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px; text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'admin_');"><img src="{$tpldir}images/dot.png" alt="" width="16" /></a></th>
						<th>{lng p="name"}</th>
						<th style="width: 200px;">{lng p="status"}</th>
						<th style="width: 80px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$admins item=admin}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center">{if $admin.adminid!=1}<input type="checkbox" name="admin_{$admin.adminid}" />{/if}</td>
							<td>{text value=$admin.firstname} {text value=$admin.lastname} ({text value=$admin.username})</td>
							<td>
								{if $admin.type==0}{lng p="superadmin"}{else}{lng p="admin"}{/if}
							</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="admins.php?action=admins&do=edit&id={$admin.adminid}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									{if $admin.adminid!=1}<a href="admins.php?action=admins&delete={$admin.adminid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>{/if}
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
	<legend>{lng p="addadmin"}</legend>

	<form action="admins.php?action=admins&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="row">
			<div class="col-md-6">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="username"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="username" name="username" placeholder="{lng p="username"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="firstname"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="firstname" name="firstname" placeholder="{lng p="firstname"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="lastname"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="lastname" name="lastname" placeholder="{lng p="lastname"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="type"}</label>
					<div class="col-sm-8">
						<select class="form-select" name="type">
							<option value="1">{lng p="admin"}</option>
							<option value="0">{lng p="superadmin"}</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="password"}</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="pw1" name="pw1" placeholder="{lng p="password"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="password"} ({lng p="repeat"})</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="pw2" name="pw2" placeholder="{lng p="password"} ({lng p="repeat"})">
					</div>
				</div>
			</div>
		</div>
		<div class="text-end">
			<input class="btn btn-primary" type="submit" value=" {lng p="add"} " />
		</div>
	</form>
</fieldset>
