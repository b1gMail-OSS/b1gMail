<fieldset>
	<legend>{lng p="groups"}</legend>
	
	<form name="f1" action="groups.php?sid={$sid}" method="post">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'group_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="title"}</th>
			<th width="70">&nbsp;</th>
		</tr>
		
		{foreach from=$groups item=group}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/ico_group{if $group.default}_default{/if}.png" border="0" width="16" height="16" alt="" /></td>
			<td align="center"><input type="checkbox" name="group_{$group.id}" /></td>
			<td><a href="groups.php?do=edit&id={$group.id}&sid={$sid}">{text value=$group.titel}</a><br /><small><a href="users.php?onlyGroup={$group.id}&sid={$sid}">{$group.members} {lng p="members"}</a></small></td>
			<td>
				<a href="groups.php?do=edit&id={$group.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				{if !$group.default}<a href="groups.php?do=delete&id={$group.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>{/if}
			</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="4">
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