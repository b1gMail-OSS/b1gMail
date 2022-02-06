<form action="admins.php?action=admins&do=edit&id={$admin.adminid}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	
	<fieldset>
		<legend>{lng p="editadmin"}: {text value=$admin.username}</legend>
		
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/ico_users.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="username"}:</td>
				<td class="td2"><input type="text" size="28" id="username" name="username" value="{text value=$admin.username}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="firstname"}:</td>
				<td class="td2"><input type="text" size="36" id="firstname" name="firstname" value="{text value=$admin.firstname allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="lastname"}:</td>
				<td class="td2"><input type="text" size="36" id="lastname" name="lastname" value="{text value=$admin.lastname allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="type"}:</td>
				<td class="td2"><select name="type"{if $admin.adminid==1} disabled="disabled"{/if} onclick="EBID('perms').style.display=this.value==0?'none':'';">
					<option value="1"{if $admin.type==1} selected="selected"{/if}>{lng p="admin"}</option>
					<option value="0"{if $admin.type==0} selected="selected"{/if}>{lng p="superadmin"}</option>
				</select></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset id="perms" style="display:{if $admin.type==0}none{/if};">
		<legend>{lng p="permissions"}</legend>
		
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/ico_prefs_validation.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="areas"}:</td>
				<td class="td2">
					{foreach from=$permsTable item=permTitle key=permName}
					<input type="checkbox" name="perms[{$permName}]" value="1" id="perm_{$permName}"{if $admin.perms.$permName} checked="checked"{/if} />
					<label for="perm_{$permName}" style="font-weight:bold;">{$permTitle}</label><br />
					{/foreach}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="plugins"}:</td>
				<td class="td2">
					{foreach from=$pluginList item=pluginTitle key=pluginName}
					<input type="checkbox" name="perms[plugins][{$pluginName}]" value="1" id="plugin_{$pluginName}"{if $admin.perms.plugins.$pluginName} checked="checked"{/if} />
					<label for="plugin_{$pluginName}" style="font-weight:bold;">{text value=$pluginTitle}</label><br />
					{/foreach}
				</td>
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
