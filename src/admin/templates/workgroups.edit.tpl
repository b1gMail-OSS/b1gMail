<form method="post" action="workgroups.php?{if !$create}do=edit&id={$group.id}&save=true{else}action=create&create=true{/if}&sid={$sid}" onsubmit="spin(this)">
<table width="100%" cellspacing="2" cellpadding="0">
	<tr>
		<td valign="top" width="50%">
			<fieldset>
				<legend>{lng p="common"}</legend>

				<table width="100%">
					<tr>
						<td class="td1" width="130">{lng p="title"}:</td>
						<td class="td2"><input type="text" name="title" value="{if !$create}{text value=$group.title}{/if}" style="width:85%;" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="email"}:</td>
						<td class="td2"><input type="text" name="email" value="{email value=$group.email}" style="width:85%;" /></td>
					</tr>
				</table>
			</fieldset>

			{*
			<fieldset>
				<legend>{lng p="collaboration"}</legend>

				<table width="100%">
					<tr>
						<td class="td1" width="160">{lng p="share_addr"}?</td>
						<td class="td2"><input type="checkbox" name="addressbook"{if $group.addressbook=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1" width="160">{lng p="share_calendar"}?</td>
						<td class="td2"><input type="checkbox" name="calendar"{if $group.calendar=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1" width="160">{lng p="share_todo"}?</td>
						<td class="td2"><input type="checkbox" name="todo"{if $group.todo=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1" width="160">{lng p="share_notes"}?</td>
						<td class="td2"><input type="checkbox" name="notes"{if $group.notes=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="share_webdisk"}:</td>
						<td class="td2"><input type="text" name="webdisk" value="{$group.webdisk/1024/1024}" size="8" /> MB</td>
					</tr>
				</table>
			</fieldset>
			*}
		</td>
		{if !$create}
		<td valign="top">
			<fieldset>
				<legend>{lng p="members"}</legend>

				<table class="list">
					<tr>
						<th width="20">&nbsp;</th>
						<th>{lng p="email"}</th>
						<th width="28">&nbsp;</th>
					</tr>

					{foreach from=$members item=member}
					{cycle values="td1,td2" name="class" assign="class"}
					<tr class="{$class}">
						<td><img src="{$tpldir}images/user_active.png" border="0" alt="" width="16" height="16" /></td>
						<td><a href="users.php?do=edit&id={$member.id}&sid={$sid}">{email value=$member.email}</a></td>
						<td><a href="workgroups.php?do=edit&id={$group.id}&deleteMember={$member.id}&sid={$sid}" onclick="return(confirm('{lng p="realdel"}'));"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a></td>
					</tr>
					{/foreach}
				</table>
			</fieldset>

			<fieldset>
				<legend>{lng p="addmember"}</legend>

				<table width="100%">
					<tr>
						<td class="td1" width="130">{lng p="email"}:</td>
						<td class="td2"><input type="text" name="userMail" value="" style="width:95%;" /></td>
						<td class="td2" nowrap="nowrap" width="1"><input type="submit" value=" {lng p="add"} " class="smallInput" /></td>
					</tr>
				</table>
			</fieldset>
		</td>
		{/if}
	</tr>
</table>
<p>
	{if !$create}<div style="float:left" class="buttons">
		&nbsp;{lng p="action"}:
		<select name="groupAction" id="groupAction">
			<optgroup label="{lng p="actions"}">
				<option value="mailto:{email value=$group.email}">{lng p="sendmail"}</option>
				<option value="workgroups.php?singleAction=delete&singleID={$group.id}&sid={$sid}">{lng p="delete"}</option>
			</optgroup>
		</select>
	</div>
	<div style="float:left">
		<input class="button" type="button" value=" {lng p="ok"} " onclick="executeAction('groupAction');" />
	</div>{/if}
	<div style="float:right" class="buttons">
		<input class="button" type="submit" value=" {lng p="save"} " />
	</div>
</p>
</form>