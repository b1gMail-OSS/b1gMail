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
	<thead>
	<tr>
		<th class="listTableHead">{lng p="orderno"}</th>
		<th class="listTableHead bm-prefs-col-date">{lng p="date"}</th>
		<th class="listTableHead bm-prefs-col-amount">{lng p="amount"}</th>
		<th class="listTableHead bm-prefs-col-invoice">{lng p="invoice"}</th>
		<th class="listTableHead bm-prefs-col-completed">{lng p="completed"}</th>
	</tr>
	</thead>

	{foreach from=$orders item=order}
	<tbody>
	<tr>
		<td>
			<button type="button" class="btn btn-ghost-secondary btn-icon btn-sm bm-order-toggle" onclick="toggleGroup({$order.orderid});" aria-label="{lng p="details"}">
				<i class="ti ti-chevron-right icon" id="groupImage_{$order.orderid}" aria-hidden="true"></i>
			</button>
			<span class="bm-order-no">{text value=$order.invoiceNo}</span>
		</td>
		<td class="bm-prefs-col-date">{date timestamp=$order.created dayonly=true}</td>
		<td class="bm-prefs-col-amount">{text value=$order.amountText}</td>
		<td class="bm-prefs-col-invoice">{if $order.invoiceAvailable}<a href="javascript:void(0);" onclick="openOverlay('{sessionurl file='prefs.php' params="action=orders&do=showInvoice&id={$order.orderid}"}','{lng p="invoice"}: {$order.invoiceNo}',760,640)" class="btn btn-outline-secondary btn-icon" title="{lng p="invoice"}" aria-label="{lng p="invoice"}"><i class="ti ti-file-text icon" aria-hidden="true"></i></a>{else}<span class="text-secondary">–</span>{/if}</td>
		<td class="bm-prefs-col-completed text-end">
			{if $order.status==0}
			<div class="btn-group btn-group-sm bm-prefs-row-actions" role="group" aria-label="{lng p="actions"}">
				<a href="{sessionurl file='prefs.php' params="action=orders&do=initiatePayment&id={$order.orderid}"}" class="btn btn-outline-secondary btn-icon" title="{lng p="pay"}" aria-label="{lng p="pay"}"><i class="ti ti-credit-card icon" aria-hidden="true"></i></a>
				<a href="{sessionurl file='prefs.php' params="action=orders&do=deleteOrder&id={$order.orderid}&csrf_token={$csrfToken}"}" class="btn btn-outline-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}" onclick="return(confirm('{lng p="realdel_order"}'));"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
			</div>
			{else}
			<span class="badge bg-green-lt text-green" title="{lng p="completed"}"><i class="ti ti-check icon" aria-hidden="true"></i></span>
			{/if}
		</td>
	</tr>
	</tbody>
	<tbody id="group_{$order.orderid}" class="bm-order-group" style="display:none;">
	<tr>
		<td colspan="5" class="bm-order-details">
			<table class="bm-order-cart">
				<thead>
				<tr>
					<th>{lng p="count"}</th>
					<th>{lng p="descr"}</th>
					<th>{lng p="ep"} ({$currency})</th>
					<th>{lng p="gp"} ({$currency})</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$order.cart item=pos}
				<tr>
					<td>{$pos.count}</td>
					<td>{text value=$pos.text}</td>
					<td>{$pos.amount}</td>
					<td>{$pos.total}</td>
				</tr>
				{/foreach}
				</tbody>
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
