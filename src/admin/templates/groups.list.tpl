<fieldset>
	<legend>{lng p="groups"}</legend>

	<form name="f1" action="{sessionurl file='groups.php'}" method="post">
		{csrffield}
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th width="20">&nbsp;</th>
						<th class="text-center" style="width:3rem;"><a href="javascript:invertSelection(document.forms.f1,'group_');">{acpicon file="dot.png" size="sm"}</a></th>
						<th>{lng p="title"}</th>
						<th width="70">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$groups item=group}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td align="center"><i class="fa-solid fa-user-group{if $group.default} text-green{/if}"></i></td>
							<td class="text-center"><label class="form-check d-inline-block mb-0"><input class="form-check-input" type="checkbox" name="group_{$group.id}" /></label></td>
							<td><a href="{sessionurl file='groups.php' params="do=edit&id={$group.id}"}">{text value=$group.titel}</a><br /><small><a href="{sessionurl file='users.php' params="onlyGroup={$group.id}"}">{$group.members} {lng p="members"}</a></small></td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
								<a href="{sessionurl file='groups.php' params="do=edit&id={$group.id}"}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
								{if !$group.default}<a href="{sessionurl file='groups.php' params="do=delete&id={$group.id}"}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>{/if}
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