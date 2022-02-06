<form action="maintenance.php?sid={$sid}" method="post" onsubmit="return userMassActionFormSubmit(this);" name="f1">
<input type="hidden" name="page" id="page" value="{$pageNo}" />
<input type="hidden" name="sortBy" id="sortBy" value="{$sortBy}" />
<input type="hidden" name="sortOrder" id="sortOrder" value="{$sortOrder}" />
<input type="hidden" name="singleAction" id="singleAction" value="" />
<input type="hidden" name="singleID" id="singleID" value="" />
<input type="hidden" name="do" value="exec" />
<input type="hidden" name="queryAction" value="show" />
{if $smarty.post.queryTypeLogin}<input type="hidden" name="queryTypeLogin" value="on" />{/if}
{if $smarty.post.queryTypeGroups}<input type="hidden" name="queryTypeGroups" value="on" />{/if}
{if $smarty.post.loginDays}<input type="hidden" name="loginDays" value="{text value=$smarty.post.loginDays allowEmpty=true}" />{/if}
{foreach from=$smarty.post.groups item=item key=key}<input type="hidden" name="groups[{$key}]" value="{text value=$item allowEmpty=true}" />{/foreach}

<fieldset>
	<legend>{lng p="inactiveusers"}</legend>

	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'user_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th><a href="javascript:updateSort('id');">{lng p="id"}
				{if $sortBy=='id'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th><a href="javascript:updateSort('email');">{lng p="email"}
				{if $sortBy=='email'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th><a href="javascript:updateSort('nachname');">{lng p="name"}
				{if $sortBy=='nachname'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th><a href="javascript:updateSort('gesperrt');">{lng p="status"}
				{if $sortBy=='gesperrt'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th><a href="javascript:updateSort('lastactivity');">{lng p="lastactivity"}
				{if $sortBy=='lastactivity'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="95">&nbsp;</th>
		</tr>

		{foreach from=$users item=user}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/user_{$user.statusImg}.png" border="0" width="16" height="16" alt="" /></td>
			<td align="center"><input type="checkbox" name="user_{$user.id}" /></td>
			<td>{$user.id}</td>
			<td><a href="users.php?do=edit&id={$user.id}&sid={$sid}">{email value=$user.email}</a><br /><small>{text value=$user.aliases cut=45 allowEmpty=true}</small></td>
			<td>{text value=$user.nachname cut=20}, {text value=$user.vorname cut=20}<br /><small>{text value=$user.strasse cut=20} {text value=$user.hnr cut=5}, {text value=$user.plz cut=8} {text value=$user.ort cut=20}</small></td>
			<td>{$user.status}<br /><small>{lng p="group"}: {text value=$user.groupName cut=25}</small></td>
			<td>
				{if $user.lastActivity==0}
					({lng p="never"})
				{else}
					{date timestamp=$user.lastActivity nice=true}<br />
					<small>
					{if $user.lastlogin==$user.lastActivity}{lng p="login"}
					{elseif $user.last_pop3==$user.lastActivity}{lng p="pop3"}
					{elseif $user.last_imap==$user.lastActivity}{lng p="imap"}
					{elseif $user.last_smtp==$user.lastActivity}{lng p="smtp"}
					{elseif $user.reg_date==$user.lastActivity}{lng p="signup"}{/if}
					</small>
				{/if}
			</td>
			<td>
				<a href="users.php?do=edit&id={$user.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="javascript:singleAction('{if $user.gesperrt=='no'}lock{elseif $user.gesperrt=='yes'}unlock{elseif $user.gesperrt=='locked'}activate{elseif $user.gesperrt=='delete'}recover{/if}', '{$user.id}');"><img src="{$tpldir}images/{if $user.gesperrt=='no'}lock{elseif $user.gesperrt=='yes'}unlock{elseif $user.gesperrt=='locked'}unlock{elseif $user.gesperrt=='delete'}recover{/if}.png" border="0" alt="{if $user.gesperrt=='no'}{lng p="lock"}{elseif $user.gesperrt=='yes'}{lng p="unlock"}{elseif $user.gesperrt=='locked'}{lng p="activate"}{elseif $user.gesperrt=='delete'}{lng p="recover"}{/if}" width="16" height="16" /></a>
				<a href="javascript:singleAction('delete', '{$user.id}');"><img src="{$tpldir}images/{if $user.gesperrt=='delete'}delete{else}trash{/if}.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
				<a href="users.php?do=login&id={$user.id}&sid={$sid}" target="_blank" onclick="return confirm('{lng p="loginwarning"}');"><img src="{$tpldir}images/login.png" border="0" alt="{lng p="login"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}

		<tr>
			<td class="footer" colspan="8">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" id="massAction" class="smallInput">
						<option value="-">------------</option>

						<optgroup label="{lng p="actions"}">
							<option value="delete">{lng p="delete"}</option>
							<option value="lock">{lng p="lock"}</option>
							<option value="unlock">{lng p="unlock"}</option>
							<option value="restore">{lng p="restore"}</option>
						</optgroup>

						<optgroup label="{lng p="move"}">
						{foreach from=$groups item=group key=groupID}
							<option value="moveto_{$groupID}">{lng p="moveto"} &quot;{text value=$group.title cut=25}&quot;</option>
						{/foreach}
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
				<div style="float:right;padding-top:3px;">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
				</div>
			</td>
		</tr>
	</table>
</fieldset>

</form>
