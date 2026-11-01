<fieldset>
	<legend>{lng p="workgroups"}</legend>

	<form name="f1" action="{sessionurl file='workgroups.php'}" method="post">
		{csrffield}
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th class="text-center" style="width:3rem;"><a href="javascript:invertSelection(document.forms.f1,'group_');">{acpicon file="dot.png" size="sm"}</a></th>
						<th>{lng p="title"}</th>
						<th>{lng p="email"}</th>
						<th style="width: 70px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$groups item=group}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><label class="form-check d-inline-block mb-0"><input class="form-check-input" type="checkbox" name="group_{$group.id}" /></label></td>
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
