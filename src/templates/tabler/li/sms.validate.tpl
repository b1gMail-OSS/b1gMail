<div class="bm-prefs-page bm-sms-page bm-sms-page-validate">

<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-message icon icon-sm" aria-hidden="true"></i>
		{lng p="sendsms"}
	</div>
</div>

<div class="scrollContainer bm-prefs-body">
<div class="pad bm-prefs-form-pad">

{if $enterCode}
{if $error}
<div class="alert alert-danger" role="alert">{lng p="invalidsmscode"}</div>
{/if}

<form name="f1" method="post" action="{sessionurl file='sms.php' params='do=validate'}">
	{csrffield}
<div class="card bm-sms-validate-card">
	<div class="card-header">
		<h3 class="card-title mb-0">{lng p="smsvalidation2"}</h3>
	</div>
	<div class="card-body">
		<p class="text-secondary mb-3">{lng p="smsvalidation2_text"}</p>
		<div class="mb-3">
			<label class="form-label" for="sms_validation_code">{lng p="validationcode"}</label>
			<input type="text" class="form-control" name="sms_validation_code" id="sms_validation_code" value="" autocomplete="one-time-code" />
		</div>
		<div class="d-flex flex-wrap gap-2">
			<button type="submit" class="btn btn-primary">{lng p="ok"}</button>
			<button type="reset" class="btn btn-ghost-secondary">{lng p="reset"}</button>
		</div>
	</div>
</div>
</form>

{else}

<div class="card bm-sms-validate-card">
	<div class="card-body">
		<div class="d-flex flex-wrap gap-3 align-items-start">
			<div class="text-primary flex-shrink-0">
				<i class="ti ti-info-circle icon icon-lg" aria-hidden="true"></i>
			</div>
			<div class="flex-fill min-w-0">
				<h3 class="mb-2">{lng p="smsvalidation2"}</h3>
				<p class="text-secondary mb-3">{lng p="pleasevalidate"}</p>
				<button type="button" class="btn btn-primary" onclick="document.location.href='{sessionurl file='prefs.php' params='action=contact'}';">
					{lng p="ok"} &raquo;
				</button>
			</div>
		</div>
	</div>
</div>

{/if}

</div>
</div>
</div>
