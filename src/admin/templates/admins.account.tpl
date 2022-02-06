<form action="admins.php?changePassword=true&sid={$sid}" method="post" onsubmit="spin(this)" autocomplete="off">
	<fieldset>
		<legend>{lng p="loggedinas"}</legend>
	
		<table>
			<tr>
				<td width="40" valign="top" rowspan="3"><img src="{$tpldir}images/ico_users.png" border="0" alt="" width="32" height="32" /></td>				
				<td class="td1" width="220">{lng p="username"}:</td>
				<td class="td2">{text value=$adminRow.username}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="name"}:</td>
				<td class="td2">{text value=$adminRow.firstname}
								{text value=$adminRow.lastname}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="status"}:</td>
				<td class="td2">{if $adminRow.type==0}{lng p="superadmin"}{else}{lng p="admin"}{/if}</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="password"}</legend>
	
		<table>
			<tr>
				<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/ico_prefs_login.png" border="0" alt="" width="32" height="32" /></td>				
				<td class="td1" width="220">{lng p="newpassword"}:</td>
				<td class="td2"><input type="password" name="newpw1" size="36" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="newpassword"} ({lng p="repeat"}):</td>
				<td class="td2"><input type="password" name="newpw2" size="36" /></td>
			</tr>
		</table>
	</fieldset>
	
	<p>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
