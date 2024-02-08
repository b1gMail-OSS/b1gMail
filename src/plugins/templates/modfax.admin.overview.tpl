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
				<label class="col-sm-4 col-form-label">{lng p="modfax_faxtoday"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$faxToday}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="modfax_faxmonth"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$errMonth}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="modfax_faxall"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$faxAll}</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="modfax_errtoday"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$errToday}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="modfax_errmonth"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$faxToday}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="modfax_errall"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$errAll}</div>
				</div>
			</div>
		</div>
	</div>
	<p>&nbsp;</p>
	<div class="row">
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="modfax_creditstoday"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$creditsToday}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="modfax_creditsmonth"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$creditsMonth}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="modfax_creditsall"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$creditsAll}</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="modfax_refundstoday"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$refundsToday}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="modfax_refundsmonth"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$refundsMonth}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="modfax_refundsall"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$refundsAll}</div>
				</div>
			</div>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="notices"}</legend>

	{foreach from=$notices item=notice}
		<div class="alert{if $notice.type == 'error'} alert-danger{/if}">{$notice.text}{if $notice.link}<br /><small><a href="{$notice.link}sid={$sid}"><img src="{$tpldir}images/go.png" border="0" alt="" width="16" height="16" /></a></small>{else}&nbsp;{/if}</div>
	{/foreach}
</fieldset>

<!--<script src="https://service.b1gmail.org/fax/updates/?do=noticeJS&version={$version}&lang={$lang}"></script>-->
