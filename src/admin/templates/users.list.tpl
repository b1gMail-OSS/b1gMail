<form action="users.php?filter=true&sid={$sid}" method="post" onsubmit="return userMassActionFormSubmit(this);" name="f1">
	<input type="hidden" name="page" id="page" value="{$pageNo}" />
	<input type="hidden" name="sortBy" id="sortBy" value="{$sortBy}" />
	<input type="hidden" name="sortOrder" id="sortOrder" value="{$sortOrder}" />
	<input type="hidden" name="singleAction" id="singleAction" value="" />
	<input type="hidden" name="singleID" id="singleID" value="" />
	{if !empty($queryString)}<input type="hidden" name="query" id="query" value="{text value=$queryString}" />{/if}

	{if isset($searchQuery)}
		<fieldset>
			<legend>{lng p="search"}</legend>

			{lng p="searchingfor"}: <b>{text value=$searchQuery}</b>

			<a href="users.php?action=search&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
		</fieldset>
	{/if}

	<fieldset>
		<legend>{lng p="users"}</legend>

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

	<fieldset>
		<legend>{lng p="show"}</legend>

		<div class="row">
			<div class="col-md-4">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="status"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="statusRegistered" id="statusRegistered"{if $statusRegistered} checked="checked"{/if}>
							<span class="form-check-label">{lng p="registered"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="statusActive" id="statusActive"{if $statusActive} checked="checked"{/if}>
							<span class="form-check-label">{lng p="active"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="statusLocked" id="statusLocked"{if $statusLocked} checked="checked"{/if}>
							<span class="form-check-label">{lng p="locked"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="statusNotActivated" id="statusNotActivated"{if $statusNotActivated} checked="checked"{/if}>
							<span class="form-check-label">{lng p="notactivated"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="statusDeleted" id="statusDeleted"{if $statusDeleted} checked="checked"{/if}>
							<span class="form-check-label">{lng p="deleted"}</span>
						</label>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="profilefields"}</label>
					<div class="col-sm-8">
						{foreach from=$fields item=field key=fieldID}
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="field_{$fieldID}" id="field_{$fieldID}"{if !empty($field.checked)} checked="checked"{/if}>
								<span class="form-check-label">{text value=$field.feld}</span>
							</label>
						{/foreach}
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="groups"}</label>
					<div class="col-sm-8">
						{foreach from=$groups item=group key=groupID}
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="group_{$groupID}" id="group_{$groupID}"{if $group.checked} checked="checked"{/if}>
								<span class="form-check-label">{text value=$group.title}</span>
							</label>
						{/foreach}
					</div>
				</div>
			</div>
		</div>


		<div class="text-end">
			{lng p="perpage"}:
			<input type="text" name="perPage" value="{$perPage}" size="5" />
			<input class="btn btn-sm btn-primary" type="submit" value=" {lng p="apply"} " />
		</div>
	</fieldset>
</form>
