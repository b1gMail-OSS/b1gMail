<fieldset>
	<legend>{lng p="groups"}</legend>

	<form name="f1" action="groups.php?sid={$sid}" method="post">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th width="20">&nbsp;</th>
						<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'group_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="title"}</th>
						<th width="70">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$groups item=group}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td align="center"><i class="fa-solid fa-user-group{if $group.default} text-green{/if}"></i></td>
							<td align="center"><input type="checkbox" name="group_{$group.id}" /></td>
							<td><a href="groups.php?do=edit&id={$group.id}&sid={$sid}">{text value=$group.titel}</a><br /><small><a href="users.php?onlyGroup={$group.id}&sid={$sid}">{$group.members} {lng p="members"}</a></small></td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
								<a href="groups.php?do=edit&id={$group.id}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
								{if !$group.default}<a href="groups.php?do=delete&id={$group.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>{/if}
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