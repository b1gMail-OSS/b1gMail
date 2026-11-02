<div class="bm-prefs-page bm-sms-page bm-sms-page-outbox">

<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-inbox icon icon-sm" aria-hidden="true"></i>
		{lng p="smsoutbox"}
	</div>
</div>

<form name="f1" method="post" action="{sessionurl file='sms.php' params='action=outbox&do=action'}">
	{csrffield}

<div class="scrollContainer withBottomBar bm-prefs-body bm-prefs-list-body">
<div class="card bm-prefs-table-card">
<div class="table-responsive bm-prefs-table-wrap">
<table class="bigTable table table-vcenter table-hover bm-prefs-table bm-sms-outbox-table">
	<tr>
		<th class="bm-sms-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'sms');" aria-label="{lng p="selaction"}" /></label></th>
		<th>
			<a href="sms.php?action=outbox&sort=from&order={$sortOrderInv}{$sessionUrlSuffix}">{lng p="from"}</a>
			{if $sortColumn=='from'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
		</th>
		<th>
			<a href="sms.php?action=outbox&sort=to&order={$sortOrderInv}{$sessionUrlSuffix}">{lng p="to"}</a>
			{if $sortColumn=='to'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
		</th>
		<th class="bm-sms-col-date">
			<a href="sms.php?action=outbox&sort=date&order={$sortOrderInv}{$sessionUrlSuffix}">{lng p="date"}</a>
			{if $sortColumn=='date'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
		</th>
		<th class="bm-sms-col-actions">&nbsp;</th>
	</tr>

	{if $outbox}
	<tbody class="listTBody">
	{foreach from=$outbox key=smsID item=sms}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class} bm-sms-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="sms_{$sms.id}" name="sms_{$sms.id}" /></label></td>
		<td class="{if $sortColumn=='from'}listTableTDActive{else}{$class}{/if} bm-sms-col-party">
			<button type="button" class="btn btn-ghost-secondary btn-icon bm-sms-expand" onclick="toggleGroup({$sms.id});" aria-expanded="{if ($smarty.request.show|default:'')==$sms.id}true{else}false{/if}" aria-controls="group_{$sms.id}">
				<i class="ti ti-chevron-{if ($smarty.request.show|default:'')==$sms.id}down{else}right{/if} icon" id="groupImage_{$sms.id}" aria-hidden="true"></i>
			</button>
			<span class="bm-sms-party">{text value=$sms.from}</span>
		</td>
		<td class="{if $sortColumn=='to'}listTableTDActive{else}{$class}{/if} bm-sms-col-party"><a class="bm-sms-party" href="sms.php?to={text value=$sms.to}{$sessionUrlSuffix}">{text value=$sms.to}</a></td>
		<td class="{if $sortColumn=='date'}listTableTDActive{else}{$class}{/if} bm-sms-col-date">{date timestamp=$sms.date nice=true}</td>
		<td class="{$class} bm-sms-col-actions text-end">
			<div class="btn-group btn-group-sm bm-sms-row-actions" role="group" aria-label="{lng p="actions"}">
				<a onclick="return confirm('{lng p="realdel"}');" href="sms.php?action=outbox&do=delete&id={$sms.id}{$sessionUrlSuffix}&csrf_token={$csrfToken}" class="btn btn-outline-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
			</div>
		</td>
	</tr>
	<tr id="group_{$sms.id}" style="display:{if ($smarty.request.show|default:'')!=$sms.id}none{/if}">
		<td colspan="5" class="listTableTDText bm-sms-outbox-text">{text value=$sms.text}</td>
	</tr>
	{/foreach}
	</tbody>
	{/if}
</table>
</div>
</div>
</div>

<div id="contentFooter" class="contentFooter bm-organizer-footer bm-prefs-footer">
	<div class="left bm-organizer-footer-actions">
		<div class="input-group input-group-sm bm-organizer-action-group">
			<select class="form-select form-select-sm" name="do2" aria-label="{lng p="selaction"}">
				<option value="-">{lng p="selaction"}</option>
				<option value="delete">{lng p="delete"}</option>
			</select>
			<button class="btn btn-sm btn-primary" type="submit">{lng p="ok"}</button>
		</div>
	</div>
</div>

</form>
</div>
