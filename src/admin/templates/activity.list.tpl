<form action="activity.php?sid={$sid}" method="post" onsubmit="spin(this)" name="f1">
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
						<th style="width: 25px;" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'user_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th><a href="javascript:updateSort('id');">{lng p="id"}
								{if $sortBy=='id'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th><a href="javascript:updateSort('email');">{lng p="email"}
								{if $sortBy=='email'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th style="width: 80px;"><a href="javascript:updateSort('mailspace_used');">{lng p="email"}
								{if $sortBy=='mailspace_used'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th style="width: 80px;"><a href="javascript:updateSort('diskspace_used');">{lng p="webdisk"}
								{if $sortBy=='diskspace_used'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th style="width: 80px;"><a href="javascript:updateSort('traffic');">{lng p="wdtrafficshort"}
								{if $sortBy=='traffic'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th style="width: 80px;"><a href="javascript:updateSort('received_mails');">{lng p="receivedmails"}
								{if $sortBy=='received_mails'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th style="width: 80px;"><a href="javascript:updateSort('sent_mails');">{lng p="sentmails"}
								{if $sortBy=='sent_mails'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th style="width: 110px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$users item=user}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td align="center">
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
							<td><a href="users.php?do=edit&id={$user.id}&sid={$sid}">{email value=$user.email cut=30}</a></td>
							<td>{progressBar value=$user.mailspace_used max=$user.mailspace_max width=75}</td>
							<td>{progressBar value=$user.diskspace_used max=$user.diskspace_max width=75}</td>
							<td>{progressBar value=$user.traffic max=$user.traffic_max width=75}</td>
							<td>{$user.received_mails}</td>
							<td>{$user.sent_mails}</td>
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
