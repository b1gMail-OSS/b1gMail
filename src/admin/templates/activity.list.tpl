<form action="{sessionurl file='activity.php'}" method="post" onsubmit="spin(this)" name="f1">
	{csrffield}
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
						<th class="text-center" style="width:3rem;"><a href="javascript:invertSelection(document.forms.f1,'user_');">{acpicon file="dot.png" size="sm"}</a></th>
						<th><a href="javascript:updateSort('id');">{lng p="id"}
								{if $sortBy=='id'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th><a href="javascript:updateSort('email');">{lng p="email"}
								{if $sortBy=='email'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th style="width: 80px;"><a href="javascript:updateSort('mailspace_used');">{lng p="email"}
								{if $sortBy=='mailspace_used'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th style="width: 80px;"><a href="javascript:updateSort('diskspace_used');">{lng p="webdisk"}
								{if $sortBy=='diskspace_used'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th style="width: 80px;"><a href="javascript:updateSort('traffic');">{lng p="wdtrafficshort"}
								{if $sortBy=='traffic'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th style="width: 80px;"><a href="javascript:updateSort('received_mails');">{lng p="receivedmails"}
								{if $sortBy=='received_mails'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th style="width: 80px;"><a href="javascript:updateSort('sent_mails');">{lng p="sentmails"}
								{if $sortBy=='sent_mails'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th style="width: 110px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$users item=user}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td align="center">
								{if $user.statusImg == 'active'}
									<i class="fa-solid fa-user text-green" title="{$user.status|escape}" aria-label="{$user.status|escape}"></i>
								{elseif $user.statusImg == 'nologin'}
									<i class="fa-regular fa-user text-azure" title="{$user.status|escape}" aria-label="{$user.status|escape}"></i>
								{elseif $user.statusImg == 'notactivated'}
									<i class="fa-solid fa-user-clock text-yellow" title="{$user.status|escape}" aria-label="{$user.status|escape}"></i>
								{elseif $user.statusImg == 'deleted'}
									<i class="fa-solid fa-user-xmark text-orange" title="{$user.status|escape}" aria-label="{$user.status|escape}"></i>
								{elseif $user.statusImg == 'locked'}
									<i class="fa-solid fa-user-lock text-red" title="{$user.status|escape}" aria-label="{$user.status|escape}"></i>
								{/if}
							</td>
							<td class="text-center"><label class="form-check d-inline-block mb-0"><input class="form-check-input" type="checkbox" name="user_{$user.id}" /></label></td>
							<td>{$user.id}</td>
							<td><a href="{sessionurl file='users.php' params="do=edit&id={$user.id}"}">{email value=$user.email cut=30}</a></td>
							<td>{progressBar value=$user.mailspace_used max=$user.mailspace_max width=75}</td>
							<td>{progressBar value=$user.diskspace_used max=$user.diskspace_max width=75}</td>
							<td>{progressBar value=$user.traffic max=$user.traffic_max width=75}</td>
							<td>{$user.received_mails}</td>
							<td>{$user.sent_mails}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="{sessionurl file='users.php' params="do=edit&id={$user.id}"}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="javascript:singleAction('{if $user.gesperrt=='no'}lock{elseif $user.gesperrt=='yes'}unlock{elseif $user.gesperrt=='locked'}activate{elseif $user.gesperrt=='delete'}recover{/if}', '{$user.id}');" class="btn btn-sm">{if $user.gesperrt=='no'}<i class="fa-solid fa-lock"></i>{elseif $user.gesperrt=='yes'}<i class="fa-solid fa-lock-open"></i>{elseif $user.gesperrt=='locked'}<i class="fa-solid fa-lock-open"></i>{elseif $user.gesperrt=='unlock'}<i class="fa-solid fa-lock-open"></i>{elseif $user.gesperrt=='delete'}<i class="fa-solid fa-hammer"></i>{/if}</a>
									<a href="javascript:singleAction('delete', '{$user.id}');" class="btn btn-sm">{if $user.gesperrt=='delete'}<i class="fa-regular fa-trash-can text-danger"></i>{else}<i class="fa-regular fa-trash-can"></i>{/if}</a>
									<a href="#" target="_blank" onclick="if(confirm('{lng p="loginwarning"}')) adminPostNavigate('{sessionurl file='users.php' params="do=login&id={$user.id}"}', true); return false;" class="btn btn-sm"><i class="fa-solid fa-house-chimney-user"></i></a>
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2">
				<div class="input-group input-group-sm bm-mass-action">
					<span class="input-group-text">{lng p="action"}</span>
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
					<button type="submit" name="executeMassAction" class="btn btn-dark-lt" value="1">{lng p="execute"}</button>
				</div>
				<div class="text-end">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
				</div>
			</div>
		</div>
	</fieldset>
</form>
