<div class="col-12 bm-li-email-toolbar py-0">
	<div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 w-100">
		<div class="bm-li-toolbar-item d-flex align-items-center gap-2 flex-grow-0">
			<a href="prefs.php?action=membership&sid={$sid}" class="bm-li-toolbar-label text-reset text-decoration-none" aria-label="{lng p="accbalance"}">
				<i class="icon ti ti-coins icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text">{lng p="accbalance"}</span>
			</a>
			<div class="bm-li-toolbar-progress">
				{progressBar value=$accBalance max=$accBalanceMax width=120}
			</div>
			<span class="bm-li-toolbar-meta">{$accBalance} / {$accBalanceMax} {lng p="credits"}</span>
		</div>
	</div>
</div>
