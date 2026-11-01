<div class="bm-prefs-page bm-prefs-page-membership">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-id icon icon-sm" aria-hidden="true"></i>
		{lng p="charge"}
	</div>
</div>

<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">
	<div class="row row-cards">

		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{lng p="charge"}</h3>
				</div>
				<form action="{sessionurl file='prefs.php' params='action=membership&do=chargeAccount'}" method="post">
					{csrffield}
					<div class="card-body">
						<p class="text-secondary mb-3">{lng p="charge_desc"}</p>
						{if $minAmount}<p class="text-secondary mb-3">{$minAmount}</p>{/if}
						{if $error}<div class="alert alert-danger" role="alert">{$error}</div>{/if}
						<div class="row">
							<label class="col-md-3 col-form-label" for="credits">{lng p="charge2"}</label>
							<div class="col-md-9 col-lg-5 d-flex flex-wrap align-items-center gap-2">
								<input type="text" class="form-control" name="credits" id="credits" value="{if $credits}{$credits}{else}{$minCredits}{/if}" style="max-width:8rem;" />
								<span class="text-secondary">{$priceText}</span>
							</div>
						</div>
					</div>
					<div class="card-footer">
						<button type="submit" class="btn btn-primary">{lng p="ok"} &raquo;</button>
					</div>
				</form>
			</div>
		</div>

		{if $credits}
		<div class="col-12">
			<form action="{sessionurl file='prefs.php' params='action=membership&do=chargeAccount'}" method="post">
				{csrffield}
				<input type="hidden" name="credits" value="{$credits}" />
				<input type="hidden" name="submitOrder" value="true" />
				{include file="li/payment.form.tpl"}
			</form>
		</div>
		{/if}

	</div>
</div></div>
</div>
