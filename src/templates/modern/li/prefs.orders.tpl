<div id="contentHeader">
	<div class="left">
		<i class="fa fa-shopping-cart" aria-hidden="true"></i>
		{lng p="orders"}
	</div>
</div>

<div class="scrollContainer">

<table class="bigTable">
	<tr>
		<th class="listTableHead">{lng p="orderno"}</th>
		<th class="listTableHead" width="120">
			{lng p="date"}
			<i class="fa fa-arrow-down" aria-hidden="true"></i>
		</th>
		<th class="listTableHead" width="80">{lng p="amount"}</th>
		<th class="listTableHead" width="80">{lng p="invoice"}</th>
		<th class="listTableHead" width="110">{lng p="completed"}</th>
	</tr>
	
	{foreach from=$orders item=order}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}">
			&nbsp;<a href="javascript:toggleGroup({$order.orderid});"><img id="groupImage_{$order.orderid}" src="{$tpldir}images/expand.png" width="11" height="11" border="0" alt="" align="absmiddle" /></a>
			<i class="fa fa-shopping-cart" aria-hidden="true"></i>
			{text value=$order.invoiceNo}
		</td>
		<td class="listTableTDActive">&nbsp;{date timestamp=$order.created dayonly=true}</td>
		<td class="{$class}">&nbsp;{text value=$order.amountText}</td>
		<td class="{$class}" style="text-align:center;">{if $order.invoiceAvailable}<a href="javascript:void(0);" onclick="openOverlay('prefs.php?action=orders&do=showInvoice&id={$order.orderid}&sid={$sid}','{lng p="invoice"}: {$order.invoiceNo}',600,550)"><img src="{$tpldir}images/li/ico_view.png" width="16" height="16" border="0" alt="" /></a>{else}-{/if}</td>
		<td class="{$class}" style="text-align:center;">{if $order.status==0}<a href="prefs.php?action=orders&do=deleteOrder&id={$order.orderid}&sid={$sid}" title="{lng p="delete"}" onclick="return(confirm('{lng p="realdel_order"}'));">{/if}<img src="{$tpldir}images/li/{if $order.status==1}yes{else}no{/if}.png" width="16" height="16" border="0" alt="" />{if $order.status==0}</a>
														<a href="prefs.php?action=orders&do=initiatePayment&id={$order.orderid}&sid={$sid}" title="{lng p="pay"}"><img src="{$tpldir}images/li/ico_pay.png" width="16" height="16" border="0" alt="{lng p="pay"}" /></a>{/if}</td>
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
