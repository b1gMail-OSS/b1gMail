<form action="users.php?filter=true&sid={$sid}" method="post" onsubmit="return userMassActionFormSubmit(this);" name="f1">
<input type="hidden" name="page" id="page" value="{$pageNo}" />
<input type="hidden" name="sortBy" id="sortBy" value="{$sortBy}" />
<input type="hidden" name="sortOrder" id="sortOrder" value="{$sortOrder}" />
<input type="hidden" name="singleAction" id="singleAction" value="" />
<input type="hidden" name="singleID" id="singleID" value="" />
{if $queryString}<input type="hidden" name="query" id="query" value="{text value=$queryString}" />{/if}

{if isset($searchQuery)}
<fieldset>
	<legend>{lng p="search"}</legend>

	<img src="{$tpldir}images/user_searchingfor.png" align="absmiddle" border="0" width="16" height="16" />
		{lng p="searchingfor"}: <b>{text value=$searchQuery}</b>

	<a href="users.php?action=search&sid={$sid}"><img src="{$tpldir}images/delete.png" align="absmiddle" border="0" width="16" height="16" /></a>
</fieldset>
{/if}

<fieldset>
	<legend>{lng p="users"}</legend>

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
			{foreach from=$fields item=field key=fieldID}{if $field.checked}
			<th{if $field.typ==2} style="text-align:center;"{/if}>{text value=$field.feld}</th>
			{/if}{/foreach}
			<th><a href="javascript:updateSort('gesperrt');">{lng p="status"}
				{if $sortBy=='gesperrt'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
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
			{foreach from=$fields item=field key=fieldID}{if $field.checked}
			<td{if $field.typ==2} style="text-align:center;"{/if}>
				{if $field.typ==2}<img src="{$tpldir}images/{if $user.profileData.$fieldID}yes{else}no{/if}.png" border="0" alt="" width="16" height="16" />
				{elseif $field.typ==32}{fieldDate value=$user.profileData.$fieldID}
				{else}{text value=$user.profileData.$fieldID}{/if}
			</td>
			{/if}{/foreach}
			<td>{$user.status}<br /><small>{lng p="group"}: {text value=$user.groupName cut=25}</small></td>
			<td>
				<a href="users.php?do=edit&id={$user.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="javascript:singleAction('{if $user.gesperrt=='no'}lock{elseif $user.gesperrt=='yes'}unlock{elseif $user.gesperrt=='locked'}activate{elseif $user.gesperrt=='delete'}recover{/if}', '{$user.id}');"><img src="{$tpldir}images/{if $user.gesperrt=='no'}lock{elseif $user.gesperrt=='yes'}unlock{elseif $user.gesperrt=='locked'}unlock{elseif $user.gesperrt=='delete'}recover{/if}.png" border="0" alt="{if $user.gesperrt=='no'}{lng p="lock"}{elseif $user.gesperrt=='yes'}{lng p="unlock"}{elseif $user.gesperrt=='locked'}{lng p="activate"}{elseif $user.gesperrt=='delete'}{lng p="recover"}{/if}" width="16" height="16" /></a>
				<a href="javascript:singleAction('delete', '{$user.id}');"><img src="{$tpldir}images/{if $user.gesperrt=='delete'}delete{else}trash{/if}.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
				<a href="users.php?do=login&id={$user.id}&sid={$sid}" target="_blank" onclick="return confirm('{lng p="loginwarning"}');"><img src="{$tpldir}images/login.png" border="0" alt="{lng p="login"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}

		<tr>
			<td class="footer" colspan="999">
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

<fieldset>
	<legend>{lng p="show"}</legend>

	<table width="100%">
		<tr>
			<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/filter.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="80">{lng p="status"}:</td>
			<td class="td2">
				<input type="checkbox" name="statusRegistered" id="statusRegistered"{if $statusRegistered} checked="checked"{/if} />
					<label for="statusRegistered"><b>{lng p="registered"}</b></label><br />
				<input type="checkbox" name="statusActive" id="statusActive"{if $statusActive} checked="checked"{/if} />
					<label for="statusActive"><b>{lng p="active"}</b></label><br />
				<input type="checkbox" name="statusLocked" id="statusLocked"{if $statusLocked} checked="checked"{/if} />
					<label for="statusLocked"><b>{lng p="locked"}</b></label><br />
				<input type="checkbox" name="statusNotActivated" id="statusNotActivated"{if $statusNotActivated} checked="checked"{/if} />
					<label for="statusNotActivated"><b>{lng p="notactivated"}</b></label><br />
				<input type="checkbox" name="statusDeleted" id="statusDeleted"{if $statusDeleted} checked="checked"{/if} />
					<label for="statusDeleted"><b>{lng p="deleted"}</b></label><br />
			</td>
			<td class="td1" width="80">{lng p="groups"}:</td>
			<td class="td2">
				{foreach from=$groups item=group key=groupID}
					<input type="checkbox" name="group_{$groupID}" id="group_{$groupID}"{if $group.checked} checked="checked"{/if} />
						<label for="group_{$groupID}"><b>{text value=$group.title}</b></label><br />
				{/foreach}
			</td>
		</tr>
		{if $fields}
		<tr>
			<td class="td1" widt="80">
				{lng p="profilefields"}:
			</td>
			<td colspan="3" class="td2">
				{foreach from=$fields item=field key=fieldID}
					<input type="checkbox" name="field_{$fieldID}" id="field_{$fieldID}"{if $field.checked} checked="checked"{/if} />
						<label for="field_{$fieldID}"><b>{text value=$field.feld}</b></label><br />
				{/foreach}
			</td>
		</tr>
		{/if}
	</table>

	<p align="right">
		{lng p="perpage"}:
		<input type="text" name="perPage" value="{$perPage}" size="5" />
		<input class="button" type="submit" value=" {lng p="apply"} " />
	</p>
</fieldset>
</form>
