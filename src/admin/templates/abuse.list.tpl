<form action="abuse.php?sid={$sid}" method="post" onsubmit="spin(this)" name="f1">
	<input type="hidden" name="page" id="page" value="{$pageNo}" />
	<input type="hidden" name="sortBy" id="sortBy" value="{$sortBy}" />
	<input type="hidden" name="sortOrder" id="sortOrder" value="{$sortOrder}" />
	<input type="hidden" name="singleAction" id="singleAction" value="" />
	<input type="hidden" name="singleID" id="singleID" value="" />

	<fieldset>
		<legend>{lng p="users"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 20px;">&nbsp;</th>
						<th style="width: 25px; text-align: center;"><a href="javascript:invertSelection(document.forms.f1,'users[]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th style="width: 60px;"><a href="javascript:updateSort('id');">{lng p="id"}
								{if $sortBy=='id'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th><a href="javascript:updateSort('email');">{lng p="email"}
								{if $sortBy=='email'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th style="width: 80px;"><a href="javascript:updateSort('pointsum');">{lng p="points"}
								{if $sortBy=='pointsum'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th style="width: 120px;">&nbsp;</th>
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
							<td class="text-center"><input type="checkbox" name="users[]" value="{$user.id}" /></td>
							<td>{$user.id}</td>
							<td><a href="abuse.php?do=show&userid={$user.id}&sid={$sid}">{email value=$user.email cut=30}</a></td>
							<td><i class="fa-regular fa-circle text-{$user.indicator}"></i>&nbsp; <a href="abuse.php?do=show&userid={$user.id}&sid={$sid}">{$user.pointsum}</a></td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="abuse.php?do=show&userid={$user.id}&sid={$sid}" title="{lng p="show"}" class="btn btn-sm"><i class="fa-solid fa-magnifying-glass"></i></a>
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
								<!--<option value="mail">{lng p="sendmail"}</option>-->
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
