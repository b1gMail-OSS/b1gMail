<!DOCTYPE html>
<html lang="{lng p="langCode"}">
<head>
	<title>{lng p="invoice"}</title>
	<meta charset="{$charset}" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	{include file="nli/favicon.tpl"}
	<link rel="stylesheet" href="{$tpldir}css/tabler-icons.min.css?{fileDateSig file="css/tabler-icons.min.css"}" />
	<link rel="stylesheet" href="{$tpldir}css/inter.css?{fileDateSig file="css/inter.css"}" />
	<style>
		html, body {
			margin: 0;
			background: #fff;
			color: #1d273b;
			font-family: Inter, system-ui, sans-serif;
		}
		.bm-invoice-bar {
			display: flex;
			justify-content: flex-end;
			gap: 0.5rem;
			padding: 0.75rem 1rem;
			border-bottom: 1px solid #e6e8eb;
			background: #f6f8fb;
			position: sticky;
			top: 0;
		}
		.bm-invoice-bar button {
			display: inline-flex;
			align-items: center;
			gap: 0.35rem;
			border: 1px solid #dce1e7;
			background: #fff;
			color: #1d273b;
			border-radius: 6px;
			padding: 0.35rem 0.7rem;
			font: inherit;
			font-size: 0.875rem;
			cursor: pointer;
		}
		.bm-invoice-bar button.bm-invoice-print {
			background: #066fd1;
			border-color: #066fd1;
			color: #fff;
		}
		.bm-invoice-sheet {
			padding: 1.25rem 1.5rem 2rem;
		}
		@media print {
			.bm-invoice-bar { display: none; }
			.bm-invoice-sheet { padding: 0; }
		}
	</style>
</head>
<body{if $print} onload="window.print();window.close();"{/if}>
	{if !$print}
	<div class="bm-invoice-bar">
		<button type="button" onclick="if(parent&amp;&amp;parent.hideOverlay)parent.hideOverlay();">
			<i class="ti ti-x" aria-hidden="true"></i>
			{lng p="close"}
		</button>
		<button type="button" class="bm-invoice-print" onclick="window.open('{sessionurl file='prefs.php' params="action=orders&do=showInvoice&id={$id}&print=true"}','printInvoice_{$id}','scrollbars=yes,resizable=yes,width=760,height=640');">
			<i class="ti ti-printer" aria-hidden="true"></i>
			{lng p="printinvoice"}
		</button>
	</div>
	{/if}
	<div class="bm-invoice-sheet">{$invoice}</div>
</body>
</html>
