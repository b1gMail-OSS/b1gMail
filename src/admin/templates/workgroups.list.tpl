<fieldset>
	<legend>{lng p="workgroups"}</legend>

	<form name="f1" action="workgroups.php?sid={$sid}" method="post">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'group_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="title"}</th>
			<th>{lng p="email"}</th>
			<th width="70">&nbsp;</th>
		</tr>

		{foreach from=$groups item=group}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/ico_workgroup.png" border="0" width="16" height="16" alt="" /></td>
			<td align="center"><input type="checkbox" name="group_{$group.id}" /></td>
			<td><a href="workgroups.php?do=edit&id={$group.id}&sid={$sid}">{text value=$group.title}</a><br /><small>{$group.members} {lng p="members"}</small></td>
			<td>{email value=$group.email}</td>
			<td>
				<a href="workgroups.php?do=edit&id={$group.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="workgroups.php?delete={$group.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}

		<tr>
			<td class="footer" colspan="5">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>

						<optgroup label="{lng p="actions"}">
							<option value="delete">{lng p="delete"}</option>
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
			</td>
		</tr>
	</table>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="add"}</legend>

	<form method="post" action="workgroups.php?create=true&sid={$sid}" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/workgroup_add32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="130">{lng p="title"}:</td>
				<td class="td2"><input type="text" name="title" value="" style="width:85%;" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="email"}:</td>
				<td class="td2"><input type="text" name="email" value="" style="width:85%;" /></td>
			</tr>
		</table>

		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>
