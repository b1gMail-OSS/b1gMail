<fieldset>
	<legend>{lng p="license"}</legend>

	<div class="mb-3 row">
		<label class="col-sm-2 col-form-label">{lng p="version"}</label>
		<div class="col-sm-10">
			<div class="form-control-plaintext">{$version}</div>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="overview"}</legend>

	<div class="row">
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="pacc_outstandingpayments"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$outstandingPayments}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="pacc_banktransfer"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$advancePayments}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="pacc_sofortueberweisung"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$suPayments}</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="pacc_packages"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$packageCount}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="pacc_subscribers"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$subscriberCount}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="pacc_paypal"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$paypalPayments}</div>
				</div>
			</div>
		</div>
	</div>
	<p>&nbsp;</p>
	<div class="row">
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="pacc_revenue"} ({lng p="overall"})</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$overallRevenue}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="pacc_revenue"} ({lng p="pacc_thismonth"})</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$monthRevenue}</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">

		</div>
	</div>
</fieldset>

{if $notices}
	</div>
	<div class="card-footer" style="padding: 10px 0px 0px 0px;">
		<h3 style="margin: 10px 30px 10px 30px;">{lng p="notices"}</h3>
		<table class="table" style="margin-bottom: 0px;">
			{foreach from=$notices item=notice}
				<tr>
					<td class="align-top text-end" style="width: 60px;">
						{if $notice.type == 'debug'}
							<i class="fa-solid fa-bug text-danger"></i>
						{elseif $notice.type == 'info'}
							<i class="fa-solid fa-circle-info text-info"></i>
						{elseif $notice.type == 'warning'}
							<i class="fa-solid fa-triangle-exclamation text-warning"></i>
						{elseif $notice.type == 'error'}
							<i class="fa-regular fa-circle-xmark text-red"></i>
						{else}
							<i class="fa-solid fa-puzzle-piece text-cyan"></i>
						{/if}
					</td>
					<td class="align-top">{$notice.text}</td>
					<td class="align-top" style="width: 60px;">{if isset($notice.link)}<a href="{$notice.link}sid={$sid}"><i class="fa-solid fa-square-arrow-up-right"></i></a>{else}&nbsp;{/if}</td>
				</tr>
			{/foreach}
		</table>
	</div>
{/if}

<!--<script src="https://service.b1gmail.org/premiumaccount/updates/?do=noticeJS&version={$version}&lang={$lang}"></script>-->