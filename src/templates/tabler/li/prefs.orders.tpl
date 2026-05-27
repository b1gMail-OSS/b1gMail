<div class="bm-prefs-page bm-prefs-page-orders">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-shopping-cart icon icon-sm" aria-hidden="true"></i>
		{lng p="orders"}
	</div>
</div>

<div class="scrollContainer bm-prefs-body bm-prefs-list-body">

<div class="card bm-prefs-table-card">
<div class="table-responsive bm-prefs-table-wrap">
<table class="bigTable table table-vcenter table-hover bm-prefs-table">
	<tr>
		<th class="listTableHead">{lng p="orderno"}</th>
		<th class="listTableHead bm-prefs-col-date">
			{lng p="date"}
			<i class="fa fa-arrow-down" aria-hidden="true"></i>
		</th>
		<th class="listTableHead bm-prefs-col-amount">{lng p="amount"}</th>
		<th class="listTableHead bm-prefs-col-invoice">{lng p="invoice"}</th>
		<th class="listTableHead bm-prefs-col-completed">{lng p="completed"}</th>
	</tr>
	
	{foreach from=$orders item=order}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}">
			&nbsp;<a href="javascript:toggleGroup({$order.orderid});"><img id="groupImage_{$order.orderid}" src="{$tpldir}images/expand.png" width="11" height="11" border="0" alt="" align="absmiddle" /></a>
			<i class="fa fa-shopping-cart" aria-hidden="true"></i>
			{text value=$order.invoiceNo}
		</td>
		<td class="listTableTDActive bm-prefs-col-date">{date timestamp=$order.created dayonly=true}</td>
		<td class="{$class} bm-prefs-col-amount">{text value=$order.amountText}</td>
		<td class="{$class} bm-prefs-col-invoice">{if $order.invoiceAvailable}<a href="javascript:void(0);" onclick="openOverlay('prefs.php?action=orders&do=showInvoice&id={$order.orderid}&sid={$sid}','{lng p="invoice"}: {$order.invoiceNo}',600,550)" class="btn btn-outline-secondary btn-icon" title="{lng p="invoice"}" aria-label="{lng p="invoice"}"><i class="ti ti-file-text icon" aria-hidden="true"></i></a>{else}<span class="text-secondary">–</span>{/if}</td>
		<td class="{$class} bm-prefs-col-completed text-end">
			{if $order.status==0}
			<div class="btn-group btn-group-sm bm-prefs-row-actions" role="group" aria-label="{lng p="actions"}">
				<a href="prefs.php?action=orders&do=initiatePayment&id={$order.orderid}&sid={$sid}" class="btn btn-outline-secondary btn-icon" title="{lng p="pay"}" aria-label="{lng p="pay"}"><i class="ti ti-credit-card icon" aria-hidden="true"></i></a>
				<a href="prefs.php?action=orders&do=deleteOrder&id={$order.orderid}&sid={$sid}" class="btn btn-outline-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}" onclick="return(confirm('{lng p="realdel_order"}'));"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
			</div>
			{else}
			<i class="ti ti-circle-check icon text-success" aria-hidden="true" title="{lng p="completed"}"></i>
			{/if}
		</td>
	</tr>
	<tbody id="group_{$order.orderid}" style="display:none;">
	<tr>
		<td colspan="5" class="listTableTDText" style="padding:1em;">
			<table class="smallCart">
				<tr>
					<th>{lng p="count"}</td>
					<th>{lng p="descr"}</td>
					<th>{lng p="ep"} ({$currency})</td>
					<th>{lng p="gp"} ({$currency})</td>
				</tr>
				{foreach from=$order.cart item=pos}
				<tr>
					<td>{$pos.count}</td>
					<td>{text value=$pos.text}</td>
					<td>{$pos.amount}</td>
					<td>{$pos.total}</td>
				</tr>
				{/foreach}
			</table>
		</td>
	</tr>
	</tbody>
	{/foreach}
</table>
</div>
</div>
</div>

</div>
