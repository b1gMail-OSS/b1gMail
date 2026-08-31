<fieldset>
	<legend>{lng p="workgroups"}</legend>

	<form name="f1" action="{sessionurl file='workgroups.php'}" method="post">
		{csrffield}
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px; text-align: center;"><a href="javascript:invertSelection(document.forms.f1,'group_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="title"}</th>
						<th>{lng p="email"}</th>
						<th style="width: 70px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$groups item=group}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><input type="checkbox" name="group_{$group.id}" /></td>
							<td><a href="{sessionurl file='workgroups.php' params="do=edit&id={$group.id}"}">{text value=$group.title}</a><br /><small>{$group.members} {lng p="members"}</small></td>
							<td>{email value=$group.email}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="{sessionurl file='workgroups.php' params="do=edit&id={$group.id}"}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="{sessionurl file='workgroups.php' params="delete={$group.id}"}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
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
	<legend>{lng p="add"}</legend>

	<form method="post" action="{sessionurl file='workgroups.php' params="create=true"}" onsubmit="spin(this)">
		{csrffield}
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="title"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="title" value="" placeholder="{lng p="title"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="email"}</label>
			<div class="col-sm-10">
				<input type="email" class="form-control" name="email" value="" placeholder="{lng p="email"}">
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>
