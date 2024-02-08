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
	{if $smarty.post.loginDays}<input type="hidden" name="loginDays" value="{if isset($smarty.post.loginDays)}{text value=$smarty.post.loginDays allowEmpty=true}{/if}" />{/if}
	{foreach from=$smarty.post.groups item=item key=key}<input type="hidden" name="groups[{$key}]" value="{if isset($item)}{text value=$item allowEmpty=true}{/if}" />{/foreach}

	<fieldset>
		<legend>{lng p="inactiveusers"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 30px;">&nbsp;</th>
						<th style="width: 25px; text-align: center;"><a href="javascript:invertSelection(document.forms.f1,'user_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
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
						<th><a href="javascript:updateSort('lastactivity');">{lng p="lastactivity"}
								{if $sortBy=='lastactivity'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th width="95">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$users item=user}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center">
								{if $user.statusImg == 'active'}
									<i class="fa-solid fa-user"></i>
								{elseif $user.statusImg == 'nologin'}
									<i class="fa-regular fa-user"></i>
								{elseif $user.statusImg == 'deleted'}
									<i class="fa-solid fa-user-xmark"></i>
								{elseif $user.statusImg == 'locked'}
									<i class="fa-solid fa-user-lock"></i>
								{/if}
							</td>
							<td class="text-center"><input type="checkbox" name="user_{$user.id}" /></td>
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
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="users.php?do=edit&id={$user.id}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="javascript:singleAction('{if $user.gesperrt=='no'}lock{elseif $user.gesperrt=='yes'}unlock{elseif $user.gesperrt=='locked'}activate{elseif $user.gesperrt=='delete'}recover{/if}', '{$user.id}');" class="btn btn-sm">{if $user.gesperrt=='no'}<i class="fa-solid fa-lock"></i>{elseif $user.gesperrt=='yes'}<i class="fa-solid fa-lock-open"></i>{elseif $user.gesperrt=='locked'}<i class="fa-solid fa-lock-open"></i>{elseif $user.gesperrt=='unlock'}<i class="fa-solid fa-lock-open"></i>{elseif $user.gesperrt=='delete'}<i class="fa-solid fa-hammer"></i>{/if}</a>
									<a href="javascript:singleAction('delete', '{$user.id}');" class="btn btn-sm">{if $user.gesperrt=='delete'}<i class="fa-regular fa-trash-can text-danger"></i>{else}<i class="fa-regular fa-trash-can"></i>{/if}</a>
									<a href="users.php?do=login&id={$user.id}&sid={$sid}" target="_blank" onclick="return confirm('{lng p="loginwarning"}');" class="btn btn-sm"><i class="fa-solid fa-house-chimney-user"></i></a>
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
								<option value="lock">{lng p="lock"}</option>
								<option value="unlock">{lng p="unlock"}</option>
								<option value="restore">{lng p="restore"}</option>
							</optgroup>
							<optgroup label="{lng p="move"}">
								{foreach from=$groups item=group key=groupID}
									<option value="moveto_{$groupID}">{lng p="moveto"} &quot;{text value=$group.title cut=25}&quot;</option>
								{/foreach}
							</optgroup>
						</select>
						<input type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" />
					</div>
				</div>
				<div class="text-end">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
				</div>
			</div>
		</div>
	</fieldset>
</form>
