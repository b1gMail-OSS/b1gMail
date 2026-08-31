{include file="li/dialog.head.tpl" title="{lng p='statement'}" dialogBodyClass="bm-dialog-statement" dialogOnLoad="documentLoader()"}

<div class="bm-dialog-page bm-statement-dialog">
	<form action="{sessionurl file='prefs.php' params='action=membership&do=statement'}" method="post" class="bm-dialog-form">
		{csrffield}
		<div class="card bm-statement-card">
			<div class="table-responsive bm-statement-table-wrap">
				<table class="table table-vcenter card-table bm-statement-table">
					<thead>
						<tr>
							<th width="150">{lng p="date"}</th>
							<th>{lng p="description"}</th>
							<th width="120" class="text-end">{lng p="credits"}</th>
						</tr>
					</thead>
					<tbody>
						<tr class="bm-statement-balance">
							<td colspan="2" class="text-end">
								{lng p="balance"} ({date dayonly=true timestamp=$timeFrom})
							</td>
							<td class="text-end">
								{if $startBalance<0}<span class="text-danger">{$startBalance}</span>{else}{$startBalance}{/if}
							</td>
						</tr>
						{if !$transactions}
						<tr>
							<td colspan="3" class="text-center text-secondary"><em>({lng p="none"})</em></td>
						</tr>
						{/if}
						{foreach from=$transactions item=tx}
						<tr class="{if $tx.status==2}bm-statement-cancelled{/if}">
							<td>{date nice=true timestamp=$tx.date}</td>
							<td><span title="{text value=$tx.description}">{text value=$tx.description cut=60}</span></td>
							<td class="text-end">{if $tx.amount<0}<span class="text-danger">{$tx.amount}</span>{else}{$tx.amount}{/if}</td>
						</tr>
						{/foreach}
						{if $dynamicBalance}
						<tr>
							<td>-</td>
							<td>{lng p="dynamicbalance"}</td>
							<td class="text-end">{$dynamicBalance}</td>
						</tr>
						{/if}
						<tr class="bm-statement-balance">
							<td colspan="2" class="text-end">
								{lng p="balance"} ({if $timeToIsCurrent}{lng p="current"}{else}{date dayonly=true timestamp=$timeTo}{/if})
							</td>
							<td class="text-end">
								{if $endBalance<0}<span class="text-danger">{$endBalance}</span>{else}{$endBalance}{/if}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="bm-dialog-actions bm-statement-actions">
			<div class="bm-dialog-actions-left bm-statement-filter">
				{html_select_date prefix="date_" time=$date display_days=false start_year="-10" field_order="MY" time=$timeFrom}
				<button type="submit" class="btn btn-primary">
					<i class="ti ti-check icon" aria-hidden="true"></i>
					{lng p="ok"}
				</button>
			</div>
			<div class="bm-dialog-actions-right">
				<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">
					{lng p="close"}
				</button>
			</div>
		</div>
	</form>
</div>

{include file="li/dialog.foot.tpl"}
