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
<table class="bigTable table table-vcenter table-hover bm-prefs-table">
	<tr>
		<th width="20"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'sms');" /></label></th>
		<th>
			<a href="sms.php?action=outbox&sort=from&order={$sortOrderInv}{$sessionUrlSuffix}">{lng p="from"}</a>
			{if $sortColumn=='from'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th>
			<a href="sms.php?action=outbox&sort=to&order={$sortOrderInv}{$sessionUrlSuffix}">{lng p="to"}</a>
			{if $sortColumn=='to'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="160">
			<a href="sms.php?action=outbox&sort=date&order={$sortOrderInv}{$sessionUrlSuffix}">{lng p="date"}</a>
			{if $sortColumn=='date'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="5.5rem">&nbsp;</th>
	</tr>

	{if $outbox}
	<tbody class="listTBody">
	{foreach from=$outbox key=smsID item=sms}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="sms_{$sms.id}" name="sms_{$sms.id}" /></label></td>
		<td class="{if $sortColumn=='from'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">
			<button type="button" class="btn btn-ghost-secondary btn-icon btn-sm py-0 px-1 me-1 align-middle" onclick="toggleGroup({$sms.id});" aria-expanded="{if $smarty.request.show==$sms.id}true{else}false{/if}" aria-controls="group_{$sms.id}">
				<i class="ti ti-chevron-{if $smarty.request.show==$sms.id}down{else}right{/if} icon" id="groupImage_{$sms.id}" aria-hidden="true"></i>
			</button>
			{text value=$sms.from}
		</td>
		<td class="{if $sortColumn=='to'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;<a href="sms.php?to={text value=$sms.to}{$sessionUrlSuffix}">{text value=$sms.to}</a></td>
		<td class="{if $sortColumn=='date'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;{date timestamp=$sms.date nice=true}</td>
		<td class="{$class} bm-prefs-col-actions text-end" nowrap="nowrap">
			<a onclick="return confirm('{lng p="realdel"}');" href="sms.php?action=outbox&do=delete&id={$sms.id}{$sessionUrlSuffix}" class="btn btn-outline-secondary btn-icon btn-sm text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
		</td>
	</tr>
	<tbody id="group_{$sms.id}" style="display:{if $smarty.request.show!=$sms.id}none{/if}">
	<tr>
		<td colspan="5" class="listTableTDText bm-sms-outbox-text">{text value=$sms.text}</td>
	</tr>
	</tbody>
	{/foreach}
	</tbody>
	{/if}
</table>
</div>
</div>
</div>

<div id="contentFooter" class="contentFooter bm-organizer-footer bm-prefs-footer">
	<div class="left">
		<div class="input-group input-group-sm bm-prefs-action-group">
			<select class="form-select bm-prefs-action-select" name="do2">
				<option value="-">------ {lng p="selaction"} ------</option>
				<option value="delete">{lng p="delete"}</option>
			</select>
			<input class="btn btn-primary" type="submit" value="{lng p="ok"}" />
		</div>
	</div>
</div>

</form>
</div>
