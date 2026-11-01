<div class="bm-prefs-page bm-prefs-page-paymentreturn">
	<div class="scrollContainer bm-prefs-body">
		<div class="card bm-payreturn-card">
			<div class="card-body text-center">
				<div class="bm-payreturn-icon">
					<span class="avatar avatar-lg bg-green-lt text-green">
						<i class="ti ti-circle-check" aria-hidden="true"></i>
					</span>
				</div>
				<h2 class="bm-payreturn-title">{lng p="thankyou"}</h2>
				<div class="bm-payreturn-text">{$msg}</div>
				<div class="bm-payreturn-action">
					{if $backLink}
					<a class="btn btn-primary" href="{$backLink}">{lng p="paymentreturn_orders"}</a>
					{else}
					<button type="button" class="btn btn-primary" onclick="history.back();">{lng p="back"}</button>
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>
