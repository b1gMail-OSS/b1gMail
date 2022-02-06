<fieldset>
	<legend>{lng p="admins"}</legend>
	
	<form action="admins.php?action=admins&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'admin_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="name"}</th>
			<th width="140">{lng p="status"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$admins item=admin}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/user_active.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center">{if $admin.adminid!=1}<input type="checkbox" name="admin_{$admin.adminid}" />{/if}</td>
			<td>{text value=$admin.username}<br />
				<small>{text value=$admin.firstname} {text value=$admin.lastname}</small></td>
			<td>
				{if $admin.type==0}{lng p="superadmin"}{else}{lng p="admin"}{/if}
			</td>
			<td>
				<a href="admins.php?action=admins&do=edit&id={$admin.adminid}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				{if $admin.adminid!=1}<a href="admins.php?action=admins&delete={$admin.adminid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>{/if}
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
	<legend>{lng p="addadmin"}</legend>
	
	<form action="admins.php?action=admins&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/ico_users.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="username"}:</td>
				<td class="td2"><input type="text" size="28" id="username" name="username" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="firstname"}:</td>
				<td class="td2"><input type="text" size="36" id="firstname" name="firstname" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="lastname"}:</td>
				<td class="td2"><input type="text" size="36" id="lastname" name="lastname" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="password"}:</td>
				<td class="td2"><input type="password" size="28" id="pw1" name="pw1" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="password"} ({lng p="repeat"}):</td>
				<td class="td2"><input type="password" size="28" id="pw2" name="pw2" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="type"}:</td>
				<td class="td2"><select name="type">
					<option value="1">{lng p="admin"}</option>
					<option value="0">{lng p="superadmin"}</option>
				</select></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>
