<form action="index.php?action=login" method="post">
	{csrffield}
<input type="hidden" name="do" value="login" />
<input type="hidden" name="email_full" value="{if isset($email)}{text value=$email}{/if}" />

{include file="nli/page.open.tpl"}
<h1 class="mb-3">{lng p="smsvalidation"}</h1>

<p class="text-secondary mb-4">
	{lng p="smsvalidation_text"}
</p>

<div class="row">
	<div class="col-md-6">
		<div class="mb-3">
			<label class="form-label" for="sms_validation_code">{lng p="validationcode"}</label>
			<input type="text" name="sms_validation_code" id="sms_validation_code" class="form-control" required="true" value="" />
		</div>
	</div>
</div>

<button type="submit" class="btn btn-primary">
	<i class="ti ti-check me-1" aria-hidden="true"></i>
	{lng p="ok"}
</button>

{if $enableResend}
<div class="alert alert-warning mt-4">
	<h4 class="alert-title">{lng p="didnotgetcode"}</h4>
	<div class="text-secondary">{$resendText}</div>
	{if $allowResend}
	<div class="mt-3">
		<input type="submit" class="btn btn-warning" value="{lng p="resendcode"}" name="resendCode" onclick="$('sms_validation_code').val('');" />
	</div>
	{/if}
</div>
{/if}

{include file="nli/page.close.tpl"}

{if $smarty.post.sms_validation_code}
<script>
<!--
	markFieldAsInvalid('sms_validation_code');
//-->
</script>
{/if}
</form>
